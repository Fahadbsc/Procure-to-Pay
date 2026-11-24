<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Budget;
use App\Services\ApprovalWorkflowService;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisitionItem;
use App\Support\DecimalMoney;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'vendor',
            'purchaseRequisition',
            'items.item'
        ])
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(10);

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

public function create()
{
    $companyId = Auth::user()->company_id;

    // Get already converted quantities.
    // Cancelled PO quantities are not counted.
    $convertedQuantities = PurchaseOrderItem::whereHas(
        'purchaseOrder',
        function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('status', '!=', 'CANCELLED');
        }
    )
        ->selectRaw(
            'purchase_requisition_item_id, SUM(quantity) as converted_quantity'
        )
        ->groupBy('purchase_requisition_item_id')
        ->pluck('converted_quantity', 'purchase_requisition_item_id');

    // Get approved PRs with remaining quantities
    $requisitions = PurchaseRequisition::with([
        'items.item.baseUom', 'items.uom'
    ])
        ->where('company_id', $companyId)
        ->whereIn('status', ['APPROVED','PARTIALLY_CONVERTED'])
        ->latest()
        ->get();

    // Calculate remaining quantity for every PR item
    foreach ($requisitions as $pr) {

        foreach ($pr->items as $item) {

            $converted = (float) (
                $convertedQuantities[$item->id] ?? 0
            );

            $item->converted_quantity = $converted;

            $item->remaining_quantity = max(
                0,
                (float) $item->quantity - $converted
            );
        }

        // Hide PRs where all items are fully converted
        $pr->setRelation(
            'items',
            $pr->items->filter(function ($item) {
                return $item->remaining_quantity > 0;
            })->values()
        );
    }

    $requisitions = $requisitions
        ->filter(function ($pr) {
            return $pr->items->isNotEmpty();
        })
        ->values();

    $vendors = Vendor::where('company_id', $companyId)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('purchase_orders.create', compact(
        'requisitions',
        'vendors'
    ));
}

 public function store(Request $request)
{
    $validated = $request->validate([
        'purchase_requisition_ids' => [
            'required',
            'array',
            'min:1',
        ],
        'purchase_requisition_ids.*' => [
            'required',
            'integer',
            'distinct',
            'exists:purchase_requisitions,id',
        ],
        'vendor_id' => [
            'required',
            'integer',
            'exists:vendors,id',
        ],
        'order_date' => [
            'required',
            'date',
        ],
        'expected_delivery_date' => [
            'nullable',
            'date',
            'after_or_equal:order_date',
        ],
        'terms_conditions' => [
            'nullable',
            'string',
            'max:5000',
        ],
        'items' => [
            'required',
            'array',
            'min:1',
        ],
        'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        'items.*.unit_price' => ['required', 'numeric', 'min:0'],
    ]);

    $companyId = Auth::user()->company_id;

    // Verify vendor belongs to the company
    $vendor = Vendor::where('id', $validated['vendor_id'])
        ->where('company_id', $companyId)
        ->where('is_active', true)
        ->first();

    if (!$vendor) {
        throw ValidationException::withMessages([
            'vendor_id' => 'Please select a valid active vendor.',
        ]);
    }

    $purchaseOrder = DB::transaction(function () use (
        $validated,
        $companyId,
        $vendor
    ) {

        $prIds = $validated['purchase_requisition_ids'];

        // Lock approved PRs to prevent concurrent conversion
        $requisitions = PurchaseRequisition::whereIn('id', $prIds)
            ->where('company_id', $companyId)
            ->whereIn('status', ['APPROVED','PARTIALLY_CONVERTED'])
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($requisitions->count() !== count($prIds)) {
            throw ValidationException::withMessages([
                'purchase_requisition_ids' =>
                    'One or more selected PRs are invalid or not approved.',
            ]);
        }

        $submittedItems = $validated['items'];

        $submittedItemIds = array_map(
            'intval',
            array_keys($submittedItems)
        );

        // Get selected PR items
        $prItems = PurchaseRequisitionItem::with('item','purchaseRequisition')
            ->whereIn('purchase_requisition_id', $prIds)
            ->whereIn('id', $submittedItemIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($prItems->count() !== count($submittedItems)) {
            throw ValidationException::withMessages([
                'items' => 'Invalid item selected for the Purchase Requisition.',
            ]);
        }

        // Create PO using first PR as its primary reference.
        // Each PO item retains its own original PR item reference.
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $companyId,
            'purchase_requisition_id' => $prIds[0],
            'vendor_id' => $validated['vendor_id'],
            'po_number' => 'PO-' . now()->format('Ymd') . '-'
                . Str::upper(Str::random(5)),
            'order_date' => $validated['order_date'],
            'expected_delivery_date' =>
                $validated['expected_delivery_date'] ?? null,
            'status' => 'DRAFT',
            'terms_conditions' =>
                $validated['terms_conditions'] ?? null,
        ]);

        foreach ($prItems as $prItem) {

            $requestedQuantity = (float) $submittedItems[$prItem->id]['quantity'];
            $agreedUnitPrice = (float) $submittedItems[$prItem->id]['unit_price'];

            // Calculate previously converted quantity
            $convertedQuantity = (float) PurchaseOrderItem::where(
                'purchase_requisition_item_id',
                $prItem->id
            )
                ->whereHas('purchaseOrder', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->where('status', '!=', 'CANCELLED');
                })
                ->sum('quantity');

            $remainingQuantity = (float) $prItem->quantity
                - $convertedQuantity;

            // Prevent over-ordering
            if (
                $requestedQuantity > $remainingQuantity ||
                $requestedQuantity <= 0
            ) {
                throw ValidationException::withMessages([
                    'items.' . $prItem->id =>
                        'Requested quantity exceeds remaining PR quantity for '
                        . ($prItem->item->name ?? 'this item')
                        . '. Remaining quantity: '
                        . number_format(max(0, $remainingQuantity), 2),
                ]);
            }

            $company = \App\Models\Company::findOrFail($companyId);
            $taxable = DecimalMoney::multiply($requestedQuantity, $agreedUnitPrice);
            $intraState = $company->state === $vendor->state;
            $cgst = $intraState ? DecimalMoney::percent($taxable,$prItem->item->gst_rate ?? 0,200) : '0.00';
            $sgst = $cgst;

            $purchaseOrder->items()->create([
                'purchase_requisition_item_id' => $prItem->id,
                'item_id' => $prItem->item_id,
                'uom_id' => $prItem->uom_id ?? $prItem->item->base_uom_id,
                'cost_centre_id' => $prItem->purchaseRequisition->cost_centre_id,
                'quantity' => $requestedQuantity,
                'unit_price' => $agreedUnitPrice,
                'gst_rate' => $prItem->item->gst_rate ?? 0,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $intraState ? '0.00' : DecimalMoney::percent($taxable,$prItem->item->gst_rate ?? 0),
                'remarks' => $prItem->remarks,
            ]);
        }

        foreach ($requisitions as $requisition) {
            $lineIds = $requisition->items()->pluck('id');
            $ordered = PurchaseOrderItem::whereIn('purchase_requisition_item_id', $lineIds)
                ->whereHas('purchaseOrder', fn ($query) => $query->where('status', '!=', 'CANCELLED'))
                ->sum('quantity');
            $requested = $requisition->items()->sum('quantity');
            if ((float) $ordered >= (float) $requested) {
                $requisition->update(['status' => 'CONVERTED']);
            } elseif ((float)$ordered > 0) {
                $requisition->update(['status' => 'PARTIALLY_CONVERTED']);
            }
        }

        return $purchaseOrder;
    });

    return redirect()
        ->route('purchase-orders.show', $purchaseOrder->id)
        ->with('success', 'Purchase Order created successfully.');
}

    public function show(PurchaseOrder $purchaseOrder)
    {
        abort_unless(
            (int) $purchaseOrder->company_id === (int) Auth::user()->company_id,
            403
        );

        $purchaseOrder->load([
            'vendor',
            'purchaseRequisition',
            'items.item.baseUom', 'items.uom'
        ]);

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

 public function send(
    PurchaseOrder $purchaseOrder,
    ApprovalWorkflowService $workflow
) {
    abort_unless(
        (int) $purchaseOrder->company_id === (int) Auth::user()->company_id,
        403
    );

    DB::transaction(function () use ($purchaseOrder, $workflow) {

        $po = PurchaseOrder::with([
            'items',
            'purchaseRequisition'
        ])
            ->whereKey($purchaseOrder->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($po->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'purchase_order' => 'Only Draft PO can be submitted for approval.'
            ]);
        }

        $requisition = $po->purchaseRequisition;

        if (!$requisition || !$requisition->cost_centre_id) {
            throw ValidationException::withMessages([
                'cost_centre' => 'Cost Centre is missing in the linked Purchase Requisition.'
            ]);
        }

        // Calculate PO total including GST
        $poAmount = '0.00';

        foreach ($po->items as $item) {
            $lineAmount = DecimalMoney::multiply($item->quantity,$item->unit_price);

            $gstAmount = (float)$item->cgst_amount + (float)$item->sgst_amount + (float)$item->igst_amount;
            if (bccomp($gstAmount,'0.00',2)===0 && (float)$item->gst_rate > 0) $gstAmount = DecimalMoney::percent($lineAmount,$item->gst_rate);

            $poAmount = bcadd($poAmount,bcadd($lineAmount,$gstAmount,2),2);
        }

        if (bccomp($poAmount,'0.00',2)<=0) {
            throw ValidationException::withMessages([
                'purchase_order' => 'PO amount must be greater than zero.'
            ]);
        }

        // Start sequential approval workflow
        $workflow->startApproval(
            'PO',
            $po->id,
            $po->company_id,
            Auth::id(),
            $poAmount,
            $requisition->cost_centre_id,
            $po->items()->whereHas('item', fn($q) => $q->where('category', 'IT'))->exists() ? 'IT' : null
        );

        // Mark PO as SENT (pending approval)
        $po->update([
            'status' => 'SENT'
        ]);
    });

    return redirect()
        ->route('purchase-orders.show', $purchaseOrder->id)
        ->with('success', 'Purchase Order submitted for approval successfully.');
}

    public function approve(PurchaseOrder $purchaseOrder)
    {
        abort_unless(
            (int) $purchaseOrder->company_id === (int) Auth::user()->company_id,
            403
        );

        try {
            DB::transaction(function () use ($purchaseOrder) {

                // Lock PO to prevent duplicate approval
                $po = PurchaseOrder::with([
                    'items',
                    'purchaseRequisition'
                ])
                    ->whereKey($purchaseOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($po->status !== 'SENT') {
                    throw ValidationException::withMessages([
                        'purchase_order' => 'Only SENT purchase orders can be approved.',
                    ]);
                }

                $requisition = $po->purchaseRequisition;

                if (!$requisition || !$requisition->cost_centre_id) {
                    throw ValidationException::withMessages([
                        'cost_centre' => 'Cost Centre is missing in the linked Purchase Requisition.',
                    ]);
                }

                // Calculate PO total including GST
                $poAmount = '0.00';

                foreach ($po->items as $item) {
                    $lineAmount = DecimalMoney::multiply($item->quantity,$item->unit_price);

                    $gstAmount = (float)$item->cgst_amount + (float)$item->sgst_amount + (float)$item->igst_amount;
                    if (bccomp($gstAmount,'0.00',2)===0 && (float)$item->gst_rate > 0) $gstAmount = DecimalMoney::percent($lineAmount,$item->gst_rate);

                    $poAmount = bcadd($poAmount,bcadd($lineAmount,$gstAmount,2),2);
                }


                // Financial year: April to March
                $orderDate = $po->order_date;
                $year = (int) $orderDate->format('Y');

                if ((int) $orderDate->format('n') >= 4) {
                    $financialYear = $year . '-' . substr((string) ($year + 1), -2);
                } else {
                    $financialYear = ($year - 1) . '-' . substr((string) $year, -2);
                }

                // Lock matching budget row
                $budget = Budget::where('company_id', $po->company_id)
                    ->where('cost_centre_id', $requisition->cost_centre_id)
                    ->where('financial_year', $financialYear)
                    ->lockForUpdate()
                    ->first();

                if (!$budget) {
                    throw ValidationException::withMessages([
                        'budget' => "No budget found for financial year {$financialYear} and selected cost centre.",
                    ]);
                }

                $available = bcsub(bcsub((string)$budget->budget_amount,(string)$budget->committed_amount,2),(string)$budget->spent_amount,2);

                if (bccomp(number_format($poAmount,2,'.',''),$available,2) === 1) {
                    throw ValidationException::withMessages([
                        'budget' => 'Insufficient budget. Available: ₹'
                            . number_format($available, 2)
                            . ', Required: ₹'
                            . number_format($poAmount, 2),
                    ]);
                }

                // Commit PO amount to budget
                $budget->committed_amount = bcadd((string)$budget->committed_amount,number_format($poAmount,2,'.',''),2);

                $budget->save();

                // Approve PO
                $po->update([
                    'status' => 'APPROVED',
                    'budget_committed_amount' => $poAmount,
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'Purchase Order approved and budget committed successfully.');
    }

   
public function cancel(PurchaseOrder $purchaseOrder)
{
    abort_unless(
        (int) $purchaseOrder->company_id === (int) Auth::user()->company_id,
        403
    );

    try {
        DB::transaction(function () use ($purchaseOrder) {

            $po = PurchaseOrder::with(['purchaseRequisition','items'])
                ->whereKey($purchaseOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($po->status, ['DRAFT', 'SENT', 'APPROVED'])) {
                throw ValidationException::withMessages([
                    'purchase_order' => 'This PO cannot be cancelled.',
                ]);
            }

            // Release committed amount only for an approved PO
            $releaseAmount = (float) $po->budget_committed_amount;

            if ($po->status === 'APPROVED' && $releaseAmount > 0) {

                $requisition = $po->purchaseRequisition;

                if (!$requisition || !$requisition->cost_centre_id) {
                    throw ValidationException::withMessages([
                        'budget' => 'Cost Centre information is missing.',
                    ]);
                }

                $orderDate = $po->order_date;
                $year = (int) $orderDate->format('Y');

                if ((int) $orderDate->format('n') >= 4) {
                    $financialYear = $year . '-' .
                        substr((string) ($year + 1), -2);
                } else {
                    $financialYear = ($year - 1) . '-' .
                        substr((string) $year, -2);
                }

                $lineAllocations = $po->items->filter(fn($item)=>$item->cost_centre_id && (float)$item->budget_committed_amount>0)
                    ->groupBy('cost_centre_id')->map(fn($lines)=>$lines->sum('budget_committed_amount'));
                if ($lineAllocations->isNotEmpty()) {
                    $budgets = Budget::where('company_id',$po->company_id)->where('financial_year',$financialYear)
                        ->whereIn('cost_centre_id',$lineAllocations->keys())->orderBy('cost_centre_id')->lockForUpdate()->get()->keyBy('cost_centre_id');
                    foreach($lineAllocations as $centreId=>$amount){
                        $budget=$budgets->get($centreId);
                        if(!$budget) throw ValidationException::withMessages(['budget'=>'Budget row is missing for a PO line cost centre.']);
                        $budget->committed_amount=bccomp((string)$budget->committed_amount,number_format((float)$amount,2,'.',''),2)<0?'0.00':bcsub((string)$budget->committed_amount,number_format((float)$amount,2,'.',''),2);
                        $budget->save();
                    }
                    foreach($po->items as $line) $line->update(['budget_committed_amount'=>0]);
                } else {
                $budget = Budget::where('company_id', $po->company_id)
                    ->where('cost_centre_id', $requisition->cost_centre_id)
                    ->where('financial_year', $financialYear)
                    ->lockForUpdate()
                    ->firstOrFail();

                $budget->committed_amount = bccomp((string)$budget->committed_amount,number_format($releaseAmount,2,'.',''),2) < 0
                    ? '0.00'
                    : bcsub((string)$budget->committed_amount,number_format($releaseAmount,2,'.',''),2);

                $budget->save();
                }
            }

            $po->update([
                'status' => 'CANCELLED',
                'budget_committed_amount' => 0,
            ]);
        });

    } catch (ValidationException $e) {
        throw $e;
    }

    return redirect()
        ->route('purchase-orders.show', $purchaseOrder->id)
        ->with('success', 'Purchase Order cancelled and remaining committed budget released.');
}

}
