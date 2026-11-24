
@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Edit Vendor</h3>
        <p class="text-muted mb-0">Update vendor information</p>
    </div>

    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
        Back to Vendors
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <form method="POST" action="{{ route('vendors.update', $vendor->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Vendor Code *</label>
                    <input type="text"
                           name="code"
                           value="{{ old('code', $vendor->code) }}"
                           class="form-control @error('code') is-invalid @enderror"
                           required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Vendor Name *</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $vendor->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">GSTIN</label>
                    <input type="text"
                           name="gstin"
                           value="{{ old('gstin', $vendor->gstin) }}"
                           class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <input type="text"
                           name="state"
                           value="{{ old('state', $vendor->state) }}"
                           class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Payment Terms (Days) *</label>
                    <input type="number"
                           name="payment_terms_days"
                           value="{{ old('payment_terms_days', $vendor->payment_terms_days) }}"
                           class="form-control"
                           min="0"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label d-block">Status</label>

                    <div class="form-check mt-2">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               class="form-check-input"
                               id="is_active"
                               @checked(old('is_active', $vendor->is_active))>

                        <label class="form-check-label" for="is_active">
                            Active Vendor
                        </label>
                    </div>
                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Update Vendor
                </button>

                <a href="{{ route('vendors.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
