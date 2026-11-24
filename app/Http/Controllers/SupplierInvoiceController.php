<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Budget;
use App\Models\UnitOfMeasure;

class SupplierInvoiceController extends Controller
{
    // Invoice List
    public function index()
    {
        $invoices = SupplierInvoice::with([
            'purchaseOrder.vendor',
            'goodsReceipt',
            'items'
        ])
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(10);

        return view('supplier_invoices.index', compact('invoices'));
    }

    // Create Invoice Page
    public function create()
    {
        $goodsReceipts = GoodsReceipt::with([
            'purchaseOrder.vendor',
            'items.purchaseOrderItem.item.baseUom',
            'items.purchaseOrderItem.uom',
            'items.supplierInvoiceItems.supplierInvoice'
        ])
            ->where('company_id', Auth::user()->company_id)
            ->where('status', 'COMPLETED')
            ->latest()
            ->get();

        // Calculate remaining accepted quantity
        $goodsReceipts->each(function ($gr) {
            $gr->items->each(function ($grItem) {
                $alreadyInvoiced = $grItem->supplierInvoiceItems
                    ->filter(function ($invoiceItem) {
                        return $invoiceItem->supplierInvoice
                            && $invoiceItem->supplierInvoice->status !== 'REJECTED';
                    })
                    ->sum(fn($invoiceItem) => (float) $invoiceItem->quantity);

                $grItem->remaining_quantity = max(
                    0,
                    (float) $grItem->accepted_quantity - $alreadyInvoiced
                );
            });

            $gr->setRelation(
                'items',
                $gr->items->filter(fn($item) => $item->remaining_quantity > 0)->values()
            );
        });

        $goodsReceipts = $goodsReceipts
            ->filter(fn($gr) => $gr->items->isNotEmpty())
            ->values();

        $units = UnitOfMeasure::orderBy('code')->get();
        return view('supplier_invoices.create', compact('goodsReceipts', 'units'));
    }

    // Store Invoice

public function store(Request $request)
{
    $validated = $request->validate([
        'goods_receipt_id' => [
            'required',
            'exists:goods_receipts,id'
        ],
        'supplier_invoice_number' => [
            'required',
            'string',
            'max:100'
        ],
        'invoice_date' => [
            'required',
            'date'
        ],
        'due_date' => [
            'nullable',
            'date',
            'after_or_equal:invoice_date'
        ],
        'remarks' => [
            'nullable',
            'string',
            'max:1000'
        ],
        'items' => [
            'required',
            'array',
            'min:1'
        ],
        'items.*.goods_receipt_item_id' => [
            'required',
            'distinct',
            'exists:goods_receipt_items,id'
        ],
        'items.*.quantity' => [
            'required',
            'numeric',
            'gt:0'
        ],
        'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        'items.*.gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
        'items.*.uom_id' => ['required', 'integer', 'exists:unit_of_measures,id'],
    ]);

    $invoice = DB::transaction(function () use ($validated) {

        // Lock Goods Receipt
        $goodsReceipt = GoodsReceipt::where('id', $validated['goods_receipt_id'])
            ->where('company_id', Auth::user()->company_id)
            ->where('status', 'COMPLETED')
            ->lockForUpdate()
            ->firstOrFail();

        $purchaseOrder = $goodsReceipt->purchaseOrder;

        if (
            !$purchaseOrder ||
            $purchaseOrder->company_id != Auth::user()->company_id
        ) {
            throw ValidationException::withMessages([
                'goods_receipt_id' => 'Invalid Purchase Order for this company.'
            ]);
        }

        $vendorId = $purchaseOrder->vendor_id;

        // Check duplicate vendor invoice number BEFORE creating invoice
        $duplicateInvoice = SupplierInvoice::where('vendor_id', $vendorId)
            ->where(
                'supplier_invoice_number',
                $validated['supplier_invoice_number']
            )
            ->exists();

        if ($duplicateInvoice) {
            throw ValidationException::withMessages([
                'supplier_invoice_number' =>
                    'This invoice number already exists for this vendor.'
            ]);
        }

        $goodsReceiptItemIds = collect($validated['items'])
            ->pluck('goods_receipt_item_id')
            ->unique()
            ->values();

        // Lock selected GR items
        $goodsReceiptItems = GoodsReceiptItem::whereIn('id', $goodsReceiptItemIds)
            ->where('goods_receipt_id', $goodsReceipt->id)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($goodsReceiptItems->count() !== $goodsReceiptItemIds->count()) {
            throw ValidationException::withMessages([
                'items' => 'One or more selected items do not belong to this Goods Receipt.'
            ]);
        }

        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

        $invoice = SupplierInvoice::create([
            'company_id' => Auth::user()->company_id,
            'vendor_id' => $vendorId,
            'purchase_order_id' => $purchaseOrder->id,
            'goods_receipt_id' => $goodsReceipt->id,
            'invoice_number' => $invoiceNumber,
            'supplier_invoice_number' => $validated['supplier_invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => \Carbon\Carbon::parse($validated['invoice_date'])->addDays($purchaseOrder->vendor->payment_terms_days)->toDateString(),
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'status' => 'DRAFT',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $subtotal = 0;
        $taxTotal = 0;
        $cgstTotal = $sgstTotal = $igstTotal = 0;
        $company = \App\Models\Company::findOrFail($purchaseOrder->company_id);
        $vendor = \App\Models\Vendor::findOrFail($vendorId);
        $intraState = $company->state === $vendor->state;

        foreach ($validated['items'] as $row) {

            $grItem = $goodsReceiptItems->get($row['goods_receipt_item_id']);

            $alreadyInvoiced = SupplierInvoiceItem::where(
                'goods_receipt_item_id',
                $grItem->id
            )
                ->whereHas('supplierInvoice', function ($query) {
                    $query->where('status', '!=', 'REJECTED');
                })
                ->sum('quantity');

            $remainingQuantity = (float) $grItem->accepted_quantity
                - (float) $alreadyInvoiced;

            $enteredUom = UnitOfMeasure::findOrFail($row['uom_id']);
            $poItem = $grItem->purchaseOrderItem;
            if (!$poItem) throw ValidationException::withMessages(['items' => 'Purchase Order item not found.']);
            $poUom = UnitOfMeasure::find($poItem->uom_id) ?? $poItem->item->baseUom;
            if (!$poUom || $enteredUom->base_unit !== $poUom->base_unit) {
                throw ValidationException::withMessages(['items' => 'Invoice unit is incompatible with the PO line.']);
            }

            $conversion = (float) $enteredUom->conversion_factor / (float) $poUom->conversion_factor;
            $quantity = (float) $row['quantity'] * $conversion;

            if ($quantity > $remainingQuantity) {
                throw ValidationException::withMessages([
                    'items' => 'Invoice quantity cannot exceed the remaining accepted quantity for item: '
                        . ($grItem->purchaseOrderItem->item->name ?? 'Unknown Item')
                        . '. Remaining quantity: '
                        . number_format(max(0, $remainingQuantity), 2)
                ]);
            }

            $unitPrice = (float) $row['unit_price'] / $conversion;
            $gstRate = (float) $row['gst_rate'];

            $lineSubtotal = round($quantity * $unitPrice, 2);
            $cgst = $intraState ? round($lineSubtotal * $gstRate / 200, 2, PHP_ROUND_HALF_UP) : 0;
            $sgst = $intraState ? round($lineSubtotal * $gstRate / 200, 2, PHP_ROUND_HALF_UP) : 0;
            $igst = $intraState ? 0 : round($lineSubtotal * $gstRate / 100, 2, PHP_ROUND_HALF_UP);
            $lineTax = $cgst + $sgst + $igst;
            $lineTotal = $lineSubtotal + $lineTax;

            SupplierInvoiceItem::create([
                'supplier_invoice_id' => $invoice->id,
                'goods_receipt_item_id' => $grItem->id,
                'item_id' => $poItem->item_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'gst_rate' => $gstRate,
                'uom_id' => $poUom->id,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_amount' => $lineTax,
                'line_total' => $lineTotal,
            ]);

            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;
            $cgstTotal += $cgst; $sgstTotal += $sgst; $igstTotal += $igst;
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxTotal,
            'total_amount' => $subtotal + $taxTotal,
            'cgst_total' => $cgstTotal,
            'sgst_total' => $sgstTotal,
            'igst_total' => $igstTotal,
        ]);

        return $invoice;
    });

    return redirect()
        ->route('supplier-invoices.show', $invoice->id)
        ->with('success', 'Supplier Invoice created successfully.');
}

    // View Invoice
    public function show(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);

        $supplierInvoice->load([
            'purchaseOrder.vendor',
            'goodsReceipt',
            'items.item.baseUom',
            'items.uom',
            'items.goodsReceiptItem',
            'submitter',
            'reviewer',
            'payer',
        ]);

        return view('supplier_invoices.show', compact('supplierInvoice'));
    }

    public function submit(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);

        if ($supplierInvoice->status !== 'DRAFT') {
            return back()->with('error', 'Only draft invoices can be submitted.');
        }

        $supplierInvoice->update([
            'status' => 'SUBMITTED',
            'submitted_by' => Auth::id(),
        ]);

        return back()->with('success', 'Invoice submitted successfully.');
    }

    public function approve(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);

        if ($supplierInvoice->status !== 'SUBMITTED') {
            return back()->with('error', 'Only submitted invoices can be approved.');
        }

        $supplierInvoice->update([
            'status' => 'APPROVED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Invoice approved successfully.');
    }

    public function reject(Request $request, SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);

        if ($supplierInvoice->status !== 'SUBMITTED') {
            return back()->with('error', 'Only submitted invoices can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $supplierInvoice->update([
            'status' => 'REJECTED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Invoice rejected successfully.');
    }

    public function post(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);
        DB::transaction(function () use ($supplierInvoice) {

            // Lock invoice
            $invoice = SupplierInvoice::with(['items.goodsReceiptItem.purchaseOrderItem'])
                ->where('id', $supplierInvoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($invoice->status !== 'MATCHED') {
                throw ValidationException::withMessages([
                    'status' => 'Only approved invoices can be posted.',
                ]);
            }

            // Lock Purchase Order
            $po = \App\Models\PurchaseOrder::where(
                'id',
                $invoice->purchase_order_id
            )->lockForUpdate()->firstOrFail();

            if (!in_array($po->status, ['APPROVED', 'PARTIALLY_RECEIVED', 'RECEIVED', 'CLOSED'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Purchase Order must be approved.',
                ]);
            }
            $pr = $po->purchaseRequisition;

            if (!$pr || !$pr->cost_centre_id) {
                throw ValidationException::withMessages([
                    'cost_centre_id' => 'Cost Centre is missing in the Purchase Requisition.',
                ]);
            }

            // Financial Year: April to March
            $orderDate = \Carbon\Carbon::parse($po->order_date);

            $year = $orderDate->month >= 4
                ? $orderDate->year
                : $orderDate->year - 1;

            $financialYear = $year . '-' . substr((string) ($year + 1), -2);

            $amount = (string)$invoice->total_amount;
            $poCommitted = (string)$po->budget_committed_amount;
            if (bccomp($amount,$poCommitted,2) === 1) {
                throw ValidationException::withMessages([
                    'amount' => 'Invoice amount exceeds the remaining committed budget.',
                ]);
            }

            $centreAmounts=[]; $poLineAmounts=[]; $lineTotal='0.00';
            foreach($invoice->items as $invoiceLine){
                $poLine=$invoiceLine->goodsReceiptItem?->purchaseOrderItem;
                if(!$poLine) throw ValidationException::withMessages(['items'=>'Invoice line is missing its PO line.']);
                $centreId=$poLine->cost_centre_id ?: $pr->cost_centre_id;
                $lineAmount=(string)$invoiceLine->line_total;
                $centreAmounts[$centreId]=bcadd($centreAmounts[$centreId]??'0.00',$lineAmount,2);
                $poLineAmounts[$poLine->id]=bcadd($poLineAmounts[$poLine->id]??'0.00',$lineAmount,2);
                $lineTotal=bcadd($lineTotal,$lineAmount,2);
                if($poLine->budget_committed_amount !== null && bccomp($poLineAmounts[$poLine->id],(string)$poLine->budget_committed_amount,2)===1){
                    throw ValidationException::withMessages(['amount'=>'Invoice line exceeds the remaining commitment for its PO line.']);
                }
            }
            if(bccomp($lineTotal,$amount,2)!==0) throw ValidationException::withMessages(['amount'=>'Invoice header total does not equal its line totals.']);
            $budgets=Budget::where('company_id',$po->company_id)->where('financial_year',$financialYear)
                ->whereIn('cost_centre_id',array_keys($centreAmounts))->orderBy('cost_centre_id')->lockForUpdate()->get()->keyBy('cost_centre_id');
            if($budgets->count()!==count($centreAmounts)) throw ValidationException::withMessages(['budget'=>'Budget record missing for an invoice cost centre.']);
            foreach($centreAmounts as $centreId=>$lineAmount){
                $budget=$budgets->get($centreId);
                if(bccomp($lineAmount,(string)$budget->committed_amount,2)===1) throw ValidationException::withMessages(['amount'=>'Invoice exceeds the remaining committed budget for '.$budget->costCentre->code.'.']);
            }
            foreach($centreAmounts as $centreId=>$lineAmount){
                $budget=$budgets->get($centreId);
                $budget->committed_amount=bcsub((string)$budget->committed_amount,$lineAmount,2);
                $budget->spent_amount=bcadd((string)$budget->spent_amount,$lineAmount,2);
                $budget->save();
            }
            foreach($po->items as $poLine){
                if($poLine->budget_committed_amount !== null && isset($poLineAmounts[$poLine->id]))
                    $poLine->update(['budget_committed_amount'=>bcsub((string)$poLine->budget_committed_amount,$poLineAmounts[$poLine->id],2)]);
            }

            // Reduce remaining PO commitment
            $po->budget_committed_amount = bcsub($poCommitted,$amount,2);
            $po->save();

            // Mark invoice as POSTED
            $invoice->status = 'POSTED';
            $invoice->save();
        });

        return redirect()
            ->route('supplier-invoices.show', $supplierInvoice->id)
            ->with('success', 'Supplier Invoice posted successfully.');
    }

    public function markPaid(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);

        if ($supplierInvoice->status !== 'APPROVED') {
            return back()->with('error', 'Only approved invoices can be marked as paid.');
        }

        $supplierInvoice->update([
            'status' => 'PAID',
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid successfully.');
    }


    public function match(SupplierInvoice $supplierInvoice)
    {
        $this->authorizeCompany($supplierInvoice);
        DB::transaction(function () use ($supplierInvoice) {

            $invoice = SupplierInvoice::where('id', $supplierInvoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($invoice->status !== 'SUBMITTED') {
                throw ValidationException::withMessages([
                    'status' => 'Only submitted invoices can be matched.',
                ]);
            }

            $invoiceItems = SupplierInvoiceItem::where(
                'supplier_invoice_id',
                $invoice->id
            )->lockForUpdate()->get();

            if ($invoiceItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Invoice has no items to match.',
                ]);
            }

            $allMatched = true;

            foreach ($invoiceItems as $invoiceItem) {

                $reasons = [];

                // Get corresponding GRN item
                $grItem = \App\Models\GoodsReceiptItem::where(
                    'id',
                    $invoiceItem->goods_receipt_item_id
                )->lockForUpdate()->first();

                if (!$grItem) {
                    $reasons[] = 'Goods Receipt item not found.';
                } else {

                    // Get corresponding PO item
                    $poItem = \App\Models\PurchaseOrderItem::find(
                        $grItem->purchase_order_item_id
                    );

                    if (!$poItem) {
                        $reasons[] = 'Purchase Order item not found.';
                    } else {

                        // Check item identity
                        if ((int) $invoiceItem->item_id !== (int) $poItem->item_id) {
                            $reasons[] = 'Invoice item does not match PO item.';
                        }

                        // Calculate already invoiced quantity
                        $alreadyInvoiced = DB::table('supplier_invoice_items')
                            ->join(
                                'supplier_invoices',
                                'supplier_invoice_items.supplier_invoice_id',
                                '=',
                                'supplier_invoices.id'
                            )
                            ->where(
                                'supplier_invoice_items.goods_receipt_item_id',
                                $grItem->id
                            )
                            ->where(
                                'supplier_invoice_items.supplier_invoice_id',
                                '!=',
                                $invoice->id
                            )
                            ->whereNotIn('supplier_invoices.status', [
                                'DRAFT',
                                'REJECTED'
                            ])
                            ->sum('supplier_invoice_items.quantity');

                        $availableQuantity = max(
                            0,
                            (float) $grItem->accepted_quantity
                                - (float) $alreadyInvoiced
                        );

                        if (
                            (float) $invoiceItem->quantity <= 0 ||
                            (float) $invoiceItem->quantity > $availableQuantity
                        ) {

                            $reasons[] = 'Invoice quantity exceeds remaining accepted GRN quantity.';
                        }

                        // Check price tolerance: +/- 2%
                        $poPrice = (float) $poItem->unit_price;
                        $invoicePrice = (float) $invoiceItem->unit_price;

                        $priceDifference = abs($invoicePrice - $poPrice);
                        $allowedDifference = $poPrice * 0.02;

                        if ($priceDifference > $allowedDifference) {
                            $reasons[] = 'Invoice price differs from PO price by more than 2%.';
                        }

                        // Check GST rate
            if (
                            round((float) $invoiceItem->gst_rate, 2) !==
                            round((float) $poItem->gst_rate, 2)
                        ) {
                            $reasons[] = 'Invoice GST rate does not match PO GST rate.';
                        }
                        $intraState = $invoice->company->state === $invoice->purchaseOrder->vendor->state;
                        $taxableAmount = round((float)$invoiceItem->quantity * (float)$invoiceItem->unit_price, 2);
                        $expectedCgst = $intraState ? round($taxableAmount * (float)$poItem->gst_rate / 200, 2, PHP_ROUND_HALF_UP) : 0;
                        $expectedSgst = $expectedCgst;
                        $expectedIgst = $intraState ? 0 : round($taxableAmount * (float)$poItem->gst_rate / 100, 2, PHP_ROUND_HALF_UP);
                        if (abs((float)$invoiceItem->cgst_amount - $expectedCgst) > 0.001 || abs((float)$invoiceItem->sgst_amount - $expectedSgst) > 0.001 || abs((float)$invoiceItem->igst_amount - $expectedIgst) > 0.001) {
                            $reasons[] = 'Invoice tax component amounts do not match the expected GST calculation.';
                        }
                        if ($intraState && ((float)$invoiceItem->igst_amount !== 0.0 || abs((float)$invoiceItem->cgst_amount - (float)$invoiceItem->sgst_amount) > 0.01)) {
                            $reasons[] = 'Invoice tax type must be CGST + SGST for an intra-state supply.';
                        } elseif (!$intraState && ((float)$invoiceItem->cgst_amount !== 0.0 || (float)$invoiceItem->sgst_amount !== 0.0)) {
                            $reasons[] = 'Invoice tax type must be IGST for an inter-state supply.';
                        }
                    }
                }

                if (empty($reasons)) {
                    $invoiceItem->match_status = 'MATCHED';
                    $invoiceItem->match_reason = null;
                } else {
                    $invoiceItem->match_status = 'ON_HOLD';
                    $invoiceItem->match_reason = implode(' ', $reasons);

                    $allMatched = false;
                }

                $invoiceItem->save();
            }

            // Update invoice status
            $invoice->status = $allMatched ? 'MATCHED' : 'ON_HOLD';
            $invoice->reviewed_by = Auth::id();
            $invoice->reviewed_at = now();
            $invoice->save();
        });

        return redirect()
            ->route('supplier-invoices.show', $supplierInvoice->id)
            ->with(
                'success',
                'Invoice matching completed successfully.'
            );
    }


public function override(Request $request, SupplierInvoice $supplierInvoice)
{
    $this->authorizeCompany($supplierInvoice);
    $validated = $request->validate([
        'override_comment' => 'required|string|min:5|max:1000',
    ]);

    DB::transaction(function () use ($supplierInvoice, $validated) {

        $invoice = SupplierInvoice::where('id', $supplierInvoice->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($invoice->status !== 'ON_HOLD') {
            throw ValidationException::withMessages([
                'status' => 'Only ON_HOLD invoices can be overridden.',
            ]);
        }

        $invoiceItems = SupplierInvoiceItem::where(
            'supplier_invoice_id',
            $invoice->id
        )->lockForUpdate()->get();

        if ($invoiceItems->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Invoice has no items.',
            ]);
        }

        foreach ($invoiceItems as $item) {
            if ($item->match_status === 'ON_HOLD') {
                $item->match_status = 'OVERRIDDEN';
                $item->save();
            }
        }

        $invoice->status = 'MATCHED';
        $invoice->override_comment = $validated['override_comment'];
        $invoice->overridden_by = auth()->id();
        $invoice->overridden_at = now();
        $invoice->reviewed_by = auth()->id();
        $invoice->reviewed_at = now();
        $invoice->save();
    });

    return redirect()
        ->route('supplier-invoices.show', $supplierInvoice->id)
        ->with('success', 'Invoice mismatch overridden successfully.');
}


public function rejectOnHold(Request $request, SupplierInvoice $supplierInvoice)
{
    $this->authorizeCompany($supplierInvoice);
    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:5|max:1000',
    ]);

    DB::transaction(function () use ($supplierInvoice, $validated) {

        $invoice = SupplierInvoice::where('id', $supplierInvoice->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($invoice->status !== 'ON_HOLD') {
            throw ValidationException::withMessages([
                'status' => 'Only ON_HOLD invoices can be rejected.',
            ]);
        }

        $invoice->status = 'REJECTED';
        $invoice->rejection_reason = $validated['rejection_reason'];
        $invoice->reviewed_by = auth()->id();
        $invoice->reviewed_at = now();
        $invoice->save();
    });

    return redirect()
        ->route('supplier-invoices.show', $supplierInvoice->id)
        ->with('success', 'Invoice rejected successfully.');
}



    // Company Access Check
    private function authorizeCompany(SupplierInvoice $invoice)
    {
        abort_unless(
            $invoice->company_id == Auth::user()->company_id,
            403,
            'Unauthorized access.'
        );
    }
}
