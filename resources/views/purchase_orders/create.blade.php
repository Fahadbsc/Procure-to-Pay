
@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Create Purchase Order</h3>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    @if($requisitions->isEmpty())
        <div class="alert alert-warning">
            No approved PR items with remaining quantities are available.
        </div>
    @else

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Purchase Order Details</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Approved Purchase Requisitions *</label>

                        <select name="purchase_requisition_ids[]"
                                id="purchase_requisition_ids"
                                class="form-select"
                                multiple
                                size="5"
                                required>
                            @foreach($requisitions as $pr)
                                <option value="{{ $pr->id }}"
                                    {{ in_array($pr->id, old('purchase_requisition_ids', [])) ? 'selected' : '' }}>
                                    {{ $pr->pr_number }}
                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Hold Ctrl to select multiple PRs.
                        </small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor *</label>

                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor</option>

                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }} ({{ $vendor->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Date *</label>
                        <input type="date"
                               name="order_date"
                               class="form-control"
                               value="{{ old('order_date', date('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date"
                               name="expected_delivery_date"
                               class="form-control"
                               value="{{ old('expected_delivery_date') }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Terms & Conditions</label>
                        <textarea name="terms_conditions"
                                  class="form-control"
                                  rows="3">{{ old('terms_conditions') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Purchase Requisition Items</h5>
            </div>

            <div class="card-body">

                <div id="noItemsMessage" class="text-muted">
                    Select one or more approved PRs to view their items.
                </div>

                <div class="table-responsive" id="itemsContainer" style="display:none;">

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>PR Number</th>
                                <th>Item</th>
                                <th>PR Quantity</th>
                                <th>Already Converted</th>
                                <th>Remaining</th>
                                <th>PO Quantity *</th>
                                <th>UOM</th>
                                <th>Unit Price</th>
                                <th>GST %</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody id="itemsTableBody"></tbody>

                        <tfoot>
                            <tr>
                                <th colspan="9" class="text-end">Grand Total</th>
                                <th id="grandTotal">0.00</th>
                            </tr>
                        </tfoot>
                    </table>

                    <small class="text-muted">
                        Enter the quantity to convert. It cannot exceed the remaining PR quantity.
                    </small>
                </div>

            </div>
        </div>

        <button type="submit" class="btn btn-success">
            Create Purchase Order
        </button>

        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
            Cancel
        </a>

    </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
const requisitions = {{ Illuminate\Support\Js::from(
    $requisitions->map(function ($pr) {
        return [
            'id' => $pr->id,
            'pr_number' => $pr->pr_number,
            'items' => $pr->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item->name ?? 'Unknown Item',
                    'pr_quantity' => (float) $item->quantity,
                    'converted_quantity' => (float) $item->converted_quantity,
                    'remaining_quantity' => (float) $item->remaining_quantity,
                    'uom' => $item->uom->code ?? $item->item->baseUom->code ?? '-',
                    'uom_id' => $item->uom_id ?? $item->item->base_uom_id,
                    'unit_price' => (float) $item->estimated_price,
                    'gst_rate' => (float) ($item->item->gst_rate ?? 0),
                ];
            })->values(),
        ];
    })->values()
) }};

const prSelect = document.getElementById('purchase_requisition_ids');
const itemsContainer = document.getElementById('itemsContainer');
const itemsTableBody = document.getElementById('itemsTableBody');
const noItemsMessage = document.getElementById('noItemsMessage');
const grandTotalElement = document.getElementById('grandTotal');

function calculateTotal(input) {
    const row = input.closest('tr');

    const quantity = parseFloat(input.value) || 0;
    const unitPrice = parseFloat(input.dataset.price);
    const gstRate = parseFloat(input.dataset.gst);

    const subtotal = quantity * unitPrice;
    const gstAmount = subtotal * gstRate / 100;
    const total = subtotal + gstAmount;

    row.querySelector('.line-total').textContent = total.toFixed(2);

    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;

    document.querySelectorAll('.line-total').forEach(element => {
        total += parseFloat(element.textContent) || 0;
    });

    grandTotalElement.textContent = total.toFixed(2);
}

function displayItems() {
    if (!prSelect) return;

    const selectedIds = Array.from(prSelect.selectedOptions)
        .map(option => Number(option.value));

    itemsTableBody.innerHTML = '';

    if (selectedIds.length === 0) {
        itemsContainer.style.display = 'none';
        noItemsMessage.style.display = 'block';
        noItemsMessage.textContent = 'Select one or more approved PRs to view their items.';
        grandTotalElement.textContent = '0.00';
        return;
    }

    let rowNumber = 0;

    selectedIds.forEach(prId => {

        const selectedPR = requisitions.find(pr => pr.id === prId);

        if (!selectedPR) return;

        selectedPR.items.forEach(item => {

            rowNumber++;

            const row = document.createElement('tr');

            const cells = [
                selectedPR.pr_number,
                item.name,
                item.pr_quantity.toFixed(2),
                item.converted_quantity.toFixed(2),
                item.remaining_quantity.toFixed(2)
            ];

            cells.forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            const quantityCell = document.createElement('td');

            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = `items[${item.id}][quantity]`;
            quantityInput.className = 'form-control po-quantity';
            quantityInput.min = '0.01';
            quantityInput.max = item.remaining_quantity;
            quantityInput.step = '0.01';
            quantityInput.value = item.remaining_quantity;
            quantityInput.required = true;
            const priceInput = document.createElement('input');
            priceInput.type = 'number';
            priceInput.name = `items[${item.id}][unit_price]`;
            priceInput.className = 'form-control po-unit-price';
            priceInput.min = '0';
            priceInput.step = '0.01';
            priceInput.value = item.unit_price.toFixed(2);
            priceInput.required = true;
            priceInput.dataset.gst = item.gst_rate;
            priceInput.addEventListener('input', function () {
                quantityInput.dataset.price = this.value;
                calculateTotal(quantityInput);
            });

            quantityInput.dataset.price = item.unit_price;
            quantityInput.dataset.gst = item.gst_rate;

            quantityInput.addEventListener('input', function () {
                if (parseFloat(this.value) > item.remaining_quantity) {
                    this.setCustomValidity(
                        'Quantity cannot exceed remaining PR quantity.'
                    );
                } else {
                    this.setCustomValidity('');
                }

                calculateTotal(this);
            });

            quantityCell.appendChild(quantityInput);
            row.appendChild(quantityCell);

            const remainingCells = [item.uom];
            remainingCells.forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            const priceCell = document.createElement('td');
            priceCell.appendChild(priceInput);
            row.appendChild(priceCell);

            const gstCell = document.createElement('td');
            gstCell.textContent = item.gst_rate.toFixed(2) + '%';
            row.appendChild(gstCell);

            const totalCell = document.createElement('td');
            totalCell.className = 'line-total';
            totalCell.textContent = '0.00';
            row.appendChild(totalCell);

            itemsTableBody.appendChild(row);

            calculateTotal(quantityInput);
        });
    });

    noItemsMessage.style.display = rowNumber ? 'none' : 'block';
    itemsContainer.style.display = rowNumber ? 'block' : 'none';

    if (!rowNumber) {
        noItemsMessage.textContent = 'No remaining items in selected PRs.';
    }
}

if (prSelect) {
    prSelect.addEventListener('change', displayItems);
    displayItems();
}
</script>
@endpush
