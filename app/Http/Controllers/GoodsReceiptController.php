<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoodsReceiptController extends Controller
{
    public function index()
    {
        $goodsReceipts = GoodsReceipt::with([
            'purchaseOrder.vendor',
            'receiver',
            'items',
        ])
        ->where('company_id', Auth::user()->company_id)
        ->latest()
        ->paginate(10);

        return view('goods_receipts.index', compact('goodsReceipts'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::with([
            'vendor',
            'items.item.baseUom',
            'goodsReceipts.items',
            'items.uom', 'items.item.baseUom',
        ])
        ->where('company_id', Auth::user()->company_id)
        ->whereIn('status', ['APPROVED', 'PARTIALLY_RECEIVED', 'RECEIVED'])
        ->latest()
        ->get();

        // Calculate remaining quantity for each PO item
        $purchaseOrders->each(function ($po) {
            foreach ($po->items as $poItem) {
                $received = $po->goodsReceipts
                    ->where('status', 'COMPLETED')
                    ->flatMap->items
                    ->where('purchase_order_item_id', $poItem->id)
                    ->sum('accepted_quantity');

                $poItem->remaining_quantity = max(
                    0,
                    ((float) $poItem->quantity * 1.05) - (float) $received
                );
                $poItem->previously_received_quantity = $po->goodsReceipts
                    ->where('status', 'COMPLETED')->flatMap->items
                    ->where('purchase_order_item_id', $poItem->id)->sum('received_quantity');
            }
        });

        $purchaseOrders = $purchaseOrders->filter(function ($po) {
            return $po->items->contains(
                fn ($item) => $item->remaining_quantity > 0
            );
        })->values();

        $units = UnitOfMeasure::orderBy('code')->get();
        return view('goods_receipts.create', compact('purchaseOrders', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|integer|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'remarks' => 'nullable|string|max:5000',

            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' =>
                'required|integer|exists:purchase_order_items,id',
            'items.*.received_uom_id' => 'required|integer|exists:unit_of_measures,id',
            'items.*.received_quantity' =>
                'required|numeric|gt:0',
            'items.*.accepted_quantity' =>
                'required|numeric|min:0',
            'items.*.rejected_quantity' =>
                'required|numeric|min:0',
            'items.*.remarks' =>
                'nullable|string|max:1000',
        ]);

        $companyId = Auth::user()->company_id;

        // Prevent duplicate PO item entries
        $itemIds = collect($validated['items'])
            ->pluck('purchase_order_item_id');

        if ($itemIds->count() !== $itemIds->unique()->count()) {
            throw ValidationException::withMessages([
                'items' => 'Duplicate PO items are not allowed.',
            ]);
        }

        DB::transaction(function () use ($validated, $companyId) {

            $purchaseOrder = PurchaseOrder::where('id', $validated['purchase_order_id'])
                ->where('company_id', $companyId)
                ->whereIn('status', ['APPROVED', 'PARTIALLY_RECEIVED', 'RECEIVED'])
                ->lockForUpdate()
                ->first();

            if (!$purchaseOrder) {
                throw ValidationException::withMessages([
                    'purchase_order_id' => 'Please select a valid SENT Purchase Order.',
                ]);
            }

            $receiptItems = [];

            foreach ($validated['items'] as $line) {

                $poItem = PurchaseOrderItem::where('id', $line['purchase_order_item_id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->lockForUpdate()
                    ->first();

                if (!$poItem) {
                    throw ValidationException::withMessages([
                        'items' => 'Invalid item selected for this Purchase Order.',
                    ]);
                }

                $received = (float) $line['received_quantity'];
                $accepted = (float) $line['accepted_quantity'];
                $rejected = (float) $line['rejected_quantity'];

                $receivedUom = UnitOfMeasure::findOrFail($line['received_uom_id']);
                $poUom = UnitOfMeasure::find($poItem->uom_id) ?? $poItem->item->baseUom;
                if (!$poUom || $receivedUom->base_unit !== $poUom->base_unit) {
                    throw ValidationException::withMessages(['items' => 'Receipt unit is incompatible with the PO line unit.']);
                }

                if (abs(($accepted + $rejected) - $received) > 0.001) {
                    throw ValidationException::withMessages([
                        'items' => 'Accepted quantity + Rejected quantity must equal Received quantity.',
                    ]);
                }

                $toPoUnit = (float) $receivedUom->conversion_factor / (float) $poUom->conversion_factor;
                $receivedInPoUnit = $received * $toPoUnit;
                $acceptedInPoUnit = $accepted * $toPoUnit;
                $rejectedInPoUnit = $rejected * $toPoUnit;

                $previouslyReceived = DB::table('goods_receipt_items')
                    ->join(
                        'goods_receipts',
                        'goods_receipts.id',
                        '=',
                        'goods_receipt_items.goods_receipt_id'
                    )
                    ->where(
                        'goods_receipt_items.purchase_order_item_id',
                        $poItem->id
                    )
                    ->where('goods_receipts.status', 'COMPLETED')
                    ->sum('goods_receipt_items.accepted_quantity');

                $remaining = ((float) $poItem->quantity * 1.05)
                    - (float) $previouslyReceived;

                if ($acceptedInPoUnit > $remaining + 0.001) {
                    throw ValidationException::withMessages([
                        'items' => 'Accepted quantity exceeds the 5% over-receipt tolerance for item: '
                            . ($poItem->item->name ?? $poItem->id),
                    ]);
                }

                $receiptItems[] = [
                    'purchase_order_item_id' => $poItem->id,
                    'received_uom_id' => $receivedUom->id,
                    'received_quantity' => $receivedInPoUnit,
                    'accepted_quantity' => $acceptedInPoUnit,
                    'rejected_quantity' => $rejectedInPoUnit,
                    'remarks' => $line['remarks'] ?? null,
                ];
            }

            $goodsReceipt = GoodsReceipt::create([
                'company_id' => $companyId,
                'purchase_order_id' => $purchaseOrder->id,
                'gr_number' => 'GR-' . now()->format('Ymd') . '-'
                    . Str::upper(Str::random(5)),
                'received_date' => $validated['received_date'],
                'received_by' => Auth::id(),
                'status' => 'COMPLETED',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $goodsReceipt->items()->createMany($receiptItems);

            $poItems = PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)->get();
            $fullyReceived = $poItems->every(function ($poItem) use ($purchaseOrder) {
                $accepted = DB::table('goods_receipt_items')
                    ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
                    ->where('goods_receipts.purchase_order_id', $purchaseOrder->id)
                    ->where('goods_receipts.status', 'COMPLETED')
                    ->where('goods_receipt_items.purchase_order_item_id', $poItem->id)
                    ->sum('goods_receipt_items.accepted_quantity');
                return (float) $accepted >= (float) $poItem->quantity;
            });
            $purchaseOrder->update(['status' => $fullyReceived ? 'RECEIVED' : 'PARTIALLY_RECEIVED']);
        });

        return redirect()
            ->route('goods-receipts.index')
            ->with('success', 'Goods Receipt recorded successfully.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        abort_unless(
            (int) $goodsReceipt->company_id === (int) Auth::user()->company_id,
            403
        );

        $goodsReceipt->load([
            'purchaseOrder.vendor',
            'receiver',
            'items.purchaseOrderItem.item.baseUom',
            'items.purchaseOrderItem.uom', 'items.receivedUom',
        ]);

        return view('goods_receipts.show', compact('goodsReceipt'));
    }
}
