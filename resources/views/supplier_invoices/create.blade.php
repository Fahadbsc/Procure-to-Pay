
@extends('layouts.app')

@section('title', 'Create Supplier Invoice')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Create Supplier Invoice</h3>

        <a href="{{ route('supplier-invoices.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    <form action="{{ route('supplier-invoices.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Invoice Information</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Goods Receipt <span class="text-danger">*</span></label>

                        <select name="goods_receipt_id" id="goods_receipt_id"
                                class="form-select @error('goods_receipt_id') is-invalid @enderror"
                                required>
                            <option value="">Select Goods Receipt</option>

                            @foreach($goodsReceipts as $gr)
                                <option value="{{ $gr->id }}"
                                    {{ old('goods_receipt_id') == $gr->id ? 'selected' : '' }}>
                                    {{ $gr->gr_number }} -
                                    {{ $gr->purchaseOrder->vendor->name ?? 'Unknown Vendor' }}
                                </option>
                            @endforeach
                        </select>

                        @error('goods_receipt_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor</label>
                        <input type="text" id="vendor_name" class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Order</label>
                        <input type="text" id="po_number" class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_invoice_number"
                               value="{{ old('supplier_invoice_number') }}"
                               class="form-control @error('supplier_invoice_number') is-invalid @enderror"
                               required maxlength="100">

                        @error('supplier_invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date"
                               value="{{ old('invoice_date', date('Y-m-d')) }}"
                               class="form-control @error('invoice_date') is-invalid @enderror"
                               required>

                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date"
                               value="{{ old('due_date') }}"
                               class="form-control @error('due_date') is-invalid @enderror">

                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Invoice Items</h5>
            </div>

            <div class="card-body">
                @error('items')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Accepted Qty</th>
                                <th>Already Invoiced</th>
                                <th>Remaining Qty</th>
                                <th>Unit</th>
                                <th>Invoice Qty</th>
                                <th>Unit Price</th>
                                <th>GST %</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>

                        <tbody id="invoice_items">
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Select a Goods Receipt to load items.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <th>Subtotal</th>
                                <td class="text-end" id="subtotal">0.00</td>
                            </tr>
                            <tr>
                                <th>GST Amount</th>
                                <td class="text-end" id="tax_total">0.00</td>
                            </tr>
                            <tr class="table-light">
                                <th>Grand Total</th>
                                <th class="text-end" id="grand_total">0.00</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-4">
            <a href="{{ route('supplier-invoices.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-primary">
                Create Invoice
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const goodsReceipts = {{ Illuminate\Support\Js::from(
                $goodsReceipts->map(function ($gr) use ($units) {
            return [
                'id' => $gr->id,
                'gr_number' => $gr->gr_number,
                'po_number' => $gr->purchaseOrder->po_number ?? '-',
                'vendor' => $gr->purchaseOrder->vendor->name ?? '-',
                'items' => $gr->items->map(function ($grItem) use ($units) {
                    $poItem = $grItem->purchaseOrderItem;
                    $item = $poItem?->item;

                    $alreadyInvoiced = $grItem->supplierInvoiceItems
                        ->filter(fn ($invoiceItem) =>
                            $invoiceItem->supplierInvoice &&
                            $invoiceItem->supplierInvoice->status !== 'REJECTED'
                        )
                        ->sum(fn ($invoiceItem) => (float) $invoiceItem->quantity);

                    return [
                        'gr_item_id' => $grItem->id,
                        'item_name' => $item->name ?? 'Unknown Item',
                        'item_code' => $item->code ?? '-',
                        'uom' => $poItem->uom->code ?? $item->baseUom->code ?? '-',
                        'accepted_qty' => (float) $grItem->accepted_quantity,
                        'already_invoiced' => (float) $alreadyInvoiced,
                        'remaining_qty' => (float) $grItem->remaining_quantity,
                        'unit_price' => (float) ($poItem->unit_price ?? 0),
                        'po_uom_id' => $poItem->uom_id ?? $item->base_uom_id,
                        'po_uom_code' => $poItem->uom->code ?? $item->baseUom->code ?? '-',
                        'po_uom_factor' => (float) ($poItem->uom->conversion_factor ?? $item->baseUom->conversion_factor ?? 1),
                        'units' => $units->where('base_unit', $poItem->uom->base_unit ?? $item->baseUom->base_unit)->map(fn($unit) => ['id' => $unit->id, 'code' => $unit->code, 'factor' => (float) $unit->conversion_factor])->values(),
                        'gst_rate' => (float) ($poItem->gst_rate ?? 0),
                    ];
                })->values(),
            ];
        })->values()
    ) }};

    const grSelect = document.getElementById('goods_receipt_id');
    const itemsBody = document.getElementById('invoice_items');

    function formatMoney(value) {
        return Number(value).toFixed(2);
    }

    function updateTotals() {
        let subtotal = 0;
        let taxTotal = 0;

        document.querySelectorAll('.invoice-quantity').forEach(input => {
            const row = input.closest('tr');
            const quantity = Number(input.value) || 0;
            const price = Number(row.querySelector('.invoice-price')?.value) || 0;
            const gst = Number(row.querySelector('.invoice-gst')?.value) || 0;

            const lineSubtotal = quantity * price;
            const lineTax = lineSubtotal * gst / 100;

            subtotal += lineSubtotal;
            taxTotal += lineTax;

            row.querySelector('.line-total').textContent =
                formatMoney(lineSubtotal + lineTax);
        });

        document.getElementById('subtotal').textContent = formatMoney(subtotal);
        document.getElementById('tax_total').textContent = formatMoney(taxTotal);
        document.getElementById('grand_total').textContent =
            formatMoney(subtotal + taxTotal);
    }

    function loadGoodsReceipt() {
        const selectedId = Number(grSelect.value);
        const gr = goodsReceipts.find(item => item.id === selectedId);

        itemsBody.replaceChildren();

        if (!gr) {
            document.getElementById('vendor_name').value = '';
            document.getElementById('po_number').value = '';

            const row = itemsBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 10;
            cell.className = 'text-center text-muted';
            cell.textContent = 'Select a Goods Receipt to load items.';

            updateTotals();
            return;
        }

        document.getElementById('vendor_name').value = gr.vendor;
        document.getElementById('po_number').value = gr.po_number;

        gr.items.forEach((item, index) => {
            const row = itemsBody.insertRow();

            row.dataset.price = item.unit_price;
            row.dataset.gst = item.gst_rate;

            const values = [
                index + 1,
                item.item_code + ' - ' + item.item_name,
                formatMoney(item.accepted_qty) + ' ' + item.uom,
                formatMoney(item.already_invoiced),
                formatMoney(item.remaining_qty) + ' ' + item.uom
            ];

            values.forEach(value => {
                const cell = row.insertCell();
                cell.textContent = value;
            });

            const unitCell = row.insertCell();
            const unitSelect = document.createElement('select');
            unitSelect.name = `items[${index}][uom_id]`;
            unitSelect.className = 'form-select invoice-uom';
            unitSelect.required = true;
            item.units.forEach(unit => {
                const option = new Option(unit.code, unit.id);
                option.dataset.factor = unit.factor;
                option.selected = unit.id === item.po_uom_id;
                unitSelect.add(option);
            });
            unitCell.appendChild(unitSelect);

            const quantityCell = row.insertCell();

            const quantityInput = document.createElement('input');

            quantityInput.type = 'number';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.className = 'form-control invoice-quantity';
            quantityInput.min = '0.01';
            quantityInput.step = '0.01';
            quantityInput.required = true;

            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = `items[${index}][goods_receipt_item_id]`;
            hiddenId.value = item.gr_item_id;

            quantityCell.appendChild(quantityInput);
            quantityCell.appendChild(hiddenId);

            const priceCell = row.insertCell();
            const priceInput = document.createElement('input');
            priceInput.type = 'number'; priceInput.name = `items[${index}][unit_price]`;
            priceInput.className = 'form-control invoice-price'; priceInput.min = '0'; priceInput.step = '0.01'; priceInput.required = true;
            priceCell.appendChild(priceInput);

            const gstCell = row.insertCell();
            const gstInput = document.createElement('select');
            gstInput.name = `items[${index}][gst_rate]`; gstInput.className = 'form-select invoice-gst'; gstInput.required = true;
            [0,5,12,18,28].forEach(rate => { const opt = new Option(rate + '%', rate); opt.selected = rate === item.gst_rate; gstInput.add(opt); });
            gstCell.appendChild(gstInput);

            const totalCell = row.insertCell();
            totalCell.className = 'text-end line-total';
            totalCell.textContent = '0.00';

            quantityInput.addEventListener('input', () => {
                if (Number(quantityInput.value) > Number(quantityInput.max)) {
                    quantityInput.setCustomValidity(
                        'Quantity cannot exceed remaining accepted quantity.'
                    );
                } else {
                    quantityInput.setCustomValidity('');
                }

                updateTotals();
            });
            const setUnitValues = () => {
                const factor = Number(unitSelect.selectedOptions[0].dataset.factor);
                const ratio = factor / item.po_uom_factor;
                quantityInput.max = item.remaining_qty / ratio;
                quantityInput.value = quantityInput.max;
                priceInput.value = formatMoney(item.unit_price * ratio);
                updateTotals();
            };
            unitSelect.addEventListener('change', setUnitValues);
            [priceInput, gstInput].forEach(input => input.addEventListener('input', updateTotals));
            setUnitValues();
        });

        updateTotals();
    }

    grSelect.addEventListener('change', loadGoodsReceipt);

    // Load selected receipt after validation redirect
    loadGoodsReceipt();
</script>
@endpush
