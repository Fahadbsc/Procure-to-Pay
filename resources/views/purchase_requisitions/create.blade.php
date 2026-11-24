
@extends('layouts.app')

@section('title', 'Create Purchase Requisition')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Create Purchase Requisition</h4>

        <a href="{{ route('purchase-requisitions.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    <form action="{{ route('purchase-requisitions.store') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <strong>PR Details</strong>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost Centre *</label>

                        <select name="cost_centre_id"
                                class="form-select"
                                required>
                            <option value="">Select Cost Centre</option>

                            @foreach($costCentres as $costCentre)
                                <option value="{{ $costCentre->id }}"
                                    {{ old('cost_centre_id') == $costCentre->id ? 'selected' : '' }}>
                                    {{ $costCentre->code }} - {{ $costCentre->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('cost_centre_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Required Date *</label>

                        <input type="date"
                               name="required_date"
                               class="form-control"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('required_date') }}"
                               required>

                        @error('required_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Purpose</label>

                        <textarea name="purpose"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Enter purchase purpose">{{ old('purpose') }}</textarea>

                        @error('purpose')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>PR Items</strong>

                <button type="button"
                        class="btn btn-sm btn-primary"
                        id="addItem">
                    + Add Item
                </button>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">

                        <thead class="table-light">
                            <tr>
                                <th width="25%">Item *</th>
                                <th width="10%">Unit *</th>
                                <th width="15%">Quantity *</th>
                                <th width="20%">Estimated Price *</th>
                                <th width="25%">Remarks</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody id="itemRows">

                            <tr class="item-row">
                                <td>
                                    <select name="items[0][item_id]"
                                            class="form-select item-select"
                                            required>

                                        <option value="">Select Item</option>

                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-base="{{ $item->baseUom->base_unit }}">
                                                {{ $item->code }} - {{ $item->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </td>

                                <td>
                                    <select name="items[0][uom_id]" class="form-select uom-select" required>
                                        @foreach($units as $unit)<option value="{{ $unit->id }}" data-base="{{ $unit->base_unit }}" {{ $unit->code === 'PCS' ? 'selected' : '' }}>{{ $unit->code }}</option>@endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[0][quantity]"
                                           class="form-control"
                                           min="0.01"
                                           step="0.01"
                                           placeholder="Quantity"
                                           required>
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[0][estimated_price]"
                                           class="form-control"
                                           min="0"
                                           step="0.01"
                                           placeholder="Price"
                                           required>
                                </td>

                                <td>
                                    <input type="text"
                                           name="items[0][remarks]"
                                           class="form-control"
                                           placeholder="Remarks">
                                </td>

                                <td>
                                    <button type="button"
                                            class="btn btn-danger btn-sm removeItem"
                                            disabled>
                                        Remove
                                    </button>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>

                @error('items')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                @foreach($errors->getMessages() as $key => $messages)
                    @if(str_starts_with($key, 'items.'))
                        <div class="text-danger small">
                            {{ implode(', ', $messages) }}
                        </div>
                    @endif
                @endforeach

            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('purchase-requisitions.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-success">
                Submit PR
            </button>

        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
    let itemIndex = 1;

    function filterItemUnits(itemSelect) {
        const row = itemSelect.closest('tr');
        const base = itemSelect.selectedOptions[0]?.dataset.base;
        const unitSelect = row.querySelector('.uom-select');
        [...unitSelect.options].forEach(option => {
            option.hidden = option.dataset.base !== base;
        });
        const first = [...unitSelect.options].find(option => option.dataset.base === base);
        if (first) unitSelect.value = first.value;
    }
    document.getElementById('itemRows').addEventListener('change', event => {
        if (event.target.matches('.item-select')) filterItemUnits(event.target);
    });

    const addItemButton = document.getElementById('addItem');
    const itemRows = document.getElementById('itemRows');

    addItemButton.addEventListener('click', function () {

        const newRow = document.createElement('tr');

        newRow.classList.add('item-row');

        newRow.innerHTML = `
            <td>
                <select name="items[${itemIndex}][item_id]"
                        class="form-select item-select"
                        required>

                    <option value="">Select Item</option>

                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-base="{{ $item->baseUom->base_unit }}">
                            {{ $item->code }} - {{ $item->name }}
                        </option>
                    @endforeach

                </select>
            </td>

            <td><select name="items[${itemIndex}][uom_id]" class="form-select uom-select" required>
                @foreach($units as $unit)<option value="{{ $unit->id }}" data-base="{{ $unit->base_unit }}">{{ $unit->code }}</option>@endforeach
            </select></td>

            <td>
                <input type="number"
                       name="items[${itemIndex}][quantity]"
                       class="form-control"
                       min="0.01"
                       step="0.01"
                       placeholder="Quantity"
                       required>
            </td>

            <td>
                <input type="number"
                       name="items[${itemIndex}][estimated_price]"
                       class="form-control"
                       min="0"
                       step="0.01"
                       placeholder="Price"
                       required>
            </td>

            <td>
                <input type="text"
                       name="items[${itemIndex}][remarks]"
                       class="form-control"
                       placeholder="Remarks">
            </td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm removeItem">
                    Remove
                </button>
            </td>
        `;

        itemRows.appendChild(newRow);

        itemIndex++;

        updateRemoveButtons();
    });

    itemRows.addEventListener('click', function (event) {

        if (event.target.classList.contains('removeItem')) {

            event.target.closest('tr').remove();

            updateRemoveButtons();
        }

    });

    function updateRemoveButtons() {

        const rows = document.querySelectorAll('.item-row');

        rows.forEach(function (row) {

            const button = row.querySelector('.removeItem');

            button.disabled = rows.length === 1;

        });

    }
</script>
@endpush
