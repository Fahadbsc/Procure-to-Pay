
@extends('layouts.app')

@section('title', 'Add Approval Rule')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3>Add Approval Rule</h3>
        <p class="text-muted mb-0">Configure sequential approval steps</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('approval-rules.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Document Type *</label>

                        <select name="document_type" class="form-select" required>
                            <option value="">Select Document Type</option>
                            <option value="PR" {{ old('document_type') == 'PR' ? 'selected' : '' }}>
                                Purchase Requisition
                            </option>
                            <option value="PO" {{ old('document_type') == 'PO' ? 'selected' : '' }}>
                                Purchase Order
                            </option>
                        </select>

                        @error('document_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost Centre</label>

                        <select name="cost_centre_id" class="form-select">
                            <option value="">All Cost Centres</option>

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

                    <div class="col-md-6 mb-3"><label class="form-label">Optional item category</label><select name="category" class="form-select"><option value="">Any category</option>@foreach(['RAW_MATERIAL','IT','SERVICES','CONSUMABLES'] as $category)<option value="{{ $category }}" @selected(old('category')===$category)>{{ $category }}</option>@endforeach</select></div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Minimum Amount *</label>

                        <input type="number"
                               name="min_amount"
                               class="form-control"
                               step="0.01"
                               min="0"
                               value="{{ old('min_amount', 0) }}"
                               required>

                        @error('min_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maximum Amount</label>

                        <input type="number"
                               name="max_amount"
                               class="form-control"
                               step="0.01"
                               min="0"
                               value="{{ old('max_amount') }}"
                               placeholder="Leave empty for no limit">

                        @error('max_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Approval Steps</h5>
                        <small class="text-muted">
                            Approvers will act in the order listed below.
                        </small>
                    </div>

                    <button type="button" class="btn btn-sm btn-success" id="addApprover">
                        + Add Approver
                    </button>
                </div>

                <div id="approverContainer">

                    @php
                        $oldApprovers = old('approver_user_ids', ['']);
                    @endphp

                    @foreach($oldApprovers as $approverId)
                        <div class="row align-items-center mb-2 approver-row">
                            <div class="col-md-2">
                                <label class="form-label">Step {{ $loop->iteration }}</label>
                            </div>

                            <div class="col-md-8">
                                <select name="approver_user_ids[]"
                                        class="form-select"
                                        required>
                                    <option value="">Select Approver</option>

                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $approverId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="button"
                                        class="btn btn-sm btn-danger removeApprover">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>

                @error('approver_user_ids')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                @error('approver_user_ids.*')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('approval-rules.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Approval Rule
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('approverContainer');
    const addButton = document.getElementById('addApprover');

    function updateStepNumbers() {
        container.querySelectorAll('.approver-row').forEach(function (row, index) {
            row.querySelector('label').textContent = 'Step ' + (index + 1);
        });
    }

    addButton.addEventListener('click', function () {

        const firstSelect = container.querySelector('select[name="approver_user_ids[]"]');

        if (!firstSelect) {
            return;
        }

        const newRow = document.createElement('div');
        newRow.className = 'row align-items-center mb-2 approver-row';

        newRow.innerHTML = `
            <div class="col-md-2">
                <label class="form-label"></label>
            </div>

            <div class="col-md-8">
                <select name="approver_user_ids[]" class="form-select" required>
                    ${firstSelect.innerHTML}
                </select>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger removeApprover">
                    Remove
                </button>
            </div>
        `;

        container.appendChild(newRow);
        updateStepNumbers();
    });

    container.addEventListener('click', function (event) {

        if (event.target.classList.contains('removeApprover')) {

            const rows = container.querySelectorAll('.approver-row');

            if (rows.length > 1) {
                event.target.closest('.approver-row').remove();
                updateStepNumbers();
            } else {
                alert('At least one approver is required.');
            }
        }
    });

    updateStepNumbers();
});
</script>

@endsection
