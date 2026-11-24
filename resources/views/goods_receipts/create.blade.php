
@extends('layouts.app')

@section('title', 'Create Goods Receipt')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Create Goods Receipt</h3>

        <a href="{{ route('goods-receipts.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    @if($purchaseOrders->isEmpty())
        <div class="alert alert-warning">
            No SENT Purchase Orders with pending quantities are available.
        </div>
    @else

    <form action="{{ route('goods-receipts.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Goods Receipt Details</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Order *</label>

                        <select name="purchase_order_id"
                                id="purchase_order_id"
                                class="form-select"
                                required>

                            <option value="">Select Purchase Order</option>

                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}"
                                    {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                    {{ $po->po_number }} - {{ $po->vendor->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Received Date *</label>

                        <input type="date"
                               name="received_date"
                               class="form-control"
                               value="{{ old('received_date', date('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Remarks</label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="2">{{ old('remarks') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Received Items</h5>
            </div>

            <div class="card-body">

                <div id="noItemsMessage" class="text-muted">
                    Select a Purchase Order to view its items.
                </div>

                <div class="table-responsive"
                     id="itemsContainer"
                     style="display:none;">

                    <table class="table table-bordered align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Ordered</th>
                                <th>Previously Received</th>
                                <th>Remaining</th>
                                <th>Receiving Unit</th>
                                <th>Receiving Now</th>
                                <th>Accepted</th>
                                <th>Rejected</th>
                            </tr>
                        </thead>

                        <tbody id="itemsTableBody"></tbody>

                    </table>

                    <small class="text-muted">
                        Accepted quantity + Rejected quantity must equal
                        Receiving Now quantity.
                    </small>

                </div>

            </div>
        </div>

        <button type="submit" class="btn btn-success">
            Save Goods Receipt
        </button>

        <a href="{{ route('goods-receipts.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

    @endif

</div>
@endsection

@push('scripts')
<script>
    const purchaseOrders = {{ Illuminate\Support\Js::from(
        $purchaseOrders->map(function ($po) use ($units) {
            return [
                'id' => $po->id,
                'items' => $po->items->map(function ($item) use ($units) {
                    return [
                        'id' => $item->id,
                        'name' => $item->item->name ?? 'Unknown Item',
                        'uom' => $item->uom->code ?? $item->item->baseUom->code ?? '-',
                        'uom_factor' => (float) ($item->uom->conversion_factor ?? $item->item->baseUom->conversion_factor ?? 1),
                        'units' => $units->where('base_unit', $item->uom->base_unit ?? $item->item->baseUom->base_unit)->map(fn($unit) => ['id' => $unit->id, 'code' => $unit->code, 'factor' => (float) $unit->conversion_factor])->values(),
                        'ordered' => (float) $item->quantity,
                        'remaining' => (float) $item->remaining_quantity,
                        'previously_received' => (float) $item->previously_received_quantity,
                    ];
                })->values(),
            ];
        })->values()
    ) }};

    const poSelect = document.getElementById('purchase_order_id');
    const itemsContainer = document.getElementById('itemsContainer');
    const itemsTableBody = document.getElementById('itemsTableBody');
    const noItemsMessage = document.getElementById('noItemsMessage');

    function displayItems() {
        if (!poSelect) return;

        const selectedId = Number(poSelect.value);

        const selectedPO = purchaseOrders.find(
            po => po.id === selectedId
        );

        itemsTableBody.innerHTML = '';

        if (!selectedPO || selectedPO.items.length === 0) {
            itemsContainer.style.display = 'none';
            noItemsMessage.style.display = 'block';
            return;
        }

        selectedPO.items.forEach(item => {

            if (item.remaining <= 0) return;

            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    ${item.name}
                    <input type="hidden"
                           name="items[${item.id}][purchase_order_item_id]"
                           value="${item.id}">
                </td>

                <td>${item.ordered.toFixed(2)} ${item.uom}</td>

                <td>${item.previously_received.toFixed(2)} ${item.uom}</td>

                <td>${item.remaining.toFixed(2)} ${item.uom}</td>

                <td>
                    <select class="form-select received-uom" name="items[${item.id}][received_uom_id]" required>
                        ${item.units.map(unit => `<option value="${unit.id}" data-factor="${unit.factor}" ${unit.code === item.uom ? 'selected' : ''}>${unit.code}</option>`).join('')}
                    </select>
                </td>

                <td>
                    <input type="number"
                           class="form-control received-input"
                           name="items[${item.id}][received_quantity]"
                           min="0.01"
                           step="0.01"
                           value="${Math.max(0,item.ordered-item.previously_received)}"
                           required>
                </td>

                <td>
                    <input type="number"
                           class="form-control accepted-input"
                           name="items[${item.id}][accepted_quantity]"
                           min="0"
                           step="0.01"
                           value="${item.remaining}"
                           required>
                </td>

                <td>
                    <input type="number"
                           class="form-control rejected-input"
                           name="items[${item.id}][rejected_quantity]"
                           min="0"
                           step="0.01"
                           value="0"
                           required>
                </td>
            `;

            itemsTableBody.appendChild(row);
        });

        itemsContainer.style.display = 'block';
        noItemsMessage.style.display = 'none';

        document.querySelectorAll('#itemsTableBody tr').forEach(row => {
            const unitSelect = row.querySelector('.received-uom');
            const received = row.querySelector('.received-input');
            const accepted = row.querySelector('.accepted-input');
            const rejected = row.querySelector('.rejected-input');
            const itemId = Number(received.name.match(/items\[(\d+)\]/)[1]);
            const item = selectedPO.items.find(line => line.id === itemId);

            const updateUnitLimit = () => {
                const factor = Number(unitSelect.selectedOptions[0].dataset.factor);
                const acceptedLimit = item.remaining * item.uom_factor / factor;
                row.dataset.acceptedLimit = acceptedLimit;
                const receiptNow = Math.max(0,item.ordered-item.previously_received) * item.uom_factor / factor;
                received.value = receiptNow;
                accepted.value = Math.min(receiptNow,acceptedLimit);
                rejected.value = Math.max(0,receiptNow-Number(accepted.value));
                accepted.max = acceptedLimit;
                rejected.max = receiptNow;
            };
            unitSelect.addEventListener('change', updateUnitLimit);
            updateUnitLimit();

            received.addEventListener('input', () => {
                const value = parseFloat(received.value) || 0;
                const maxAccepted = Math.min(value, Number(row.dataset.acceptedLimit));
                accepted.max = maxAccepted;
                rejected.max = value;
                accepted.value = maxAccepted;
                rejected.value = value - maxAccepted;
            });
        });
    }

    if (poSelect) {
        poSelect.addEventListener('change', displayItems);
        displayItems();
    }
</script>
@endpush
