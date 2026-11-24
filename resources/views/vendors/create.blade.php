
@extends('layouts.app')

@section('title', 'Add Vendor')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3>Add New Vendor</h3>
        <p class="text-muted mb-0">Enter vendor details</p>
    </div>

    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
        Back to Vendors
    </a>

</div>

<div class="card shadow-sm border-0">

    <div class="card-body p-4">

        <form method="POST" action="{{ route('vendors.store') }}">

            @csrf

            <div class="row g-3">

                <!-- Vendor Code -->
                <div class="col-md-6">
                    <label class="form-label">
                        Vendor Code <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}"
                           maxlength="20"
                           placeholder="Example: VEN001"
                           required>

                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vendor Name -->
                <div class="col-md-6">
                    <label class="form-label">
                        Vendor Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Enter vendor name"
                           required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- GSTIN -->
                <div class="col-md-6">
                    <label class="form-label">GSTIN</label>

                    <input type="text"
                           name="gstin"
                           maxlength="15"
                           class="form-control @error('gstin') is-invalid @enderror"
                           value="{{ old('gstin') }}"
                           placeholder="Enter 15-character GSTIN">

                    @error('gstin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- State -->
                <div class="col-md-6">
                    <label class="form-label">
                        State <span class="text-danger">*</span>
                    </label>

                    <select name="state"
                            class="form-select @error('state') is-invalid @enderror"
                            required>

                        <option value="">Select State</option>

                        <option value="Maharashtra"
                            @selected(old('state') == 'Maharashtra')>
                            Maharashtra
                        </option>

                        <option value="Karnataka"
                            @selected(old('state') == 'Karnataka')>
                            Karnataka
                        </option>

                        <option value="Tamil Nadu"
                            @selected(old('state') == 'Tamil Nadu')>
                            Tamil Nadu
                        </option>

                        <option value="Kerala"
                            @selected(old('state') == 'Kerala')>
                            Kerala
                        </option>

                        <option value="Delhi"
                            @selected(old('state') == 'Delhi')>
                            Delhi
                        </option>

                        <option value="Telangana"
                            @selected(old('state') == 'Telangana')>
                            Telangana
                        </option>

                        <option value="Other"
                            @selected(old('state') == 'Other')>
                            Other
                        </option>

                    </select>

                    @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Terms -->
                <div class="col-md-6">
                    <label class="form-label">
                        Payment Terms (Days) <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="payment_terms_days"
                           class="form-control @error('payment_terms_days') is-invalid @enderror"
                           value="{{ old('payment_terms_days', 30) }}"
                           min="0"
                           required>

                    @error('payment_terms_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="col-md-6 d-flex align-items-center">

                    <div class="form-check mt-3">

                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               id="is_active"
                               class="form-check-input"
                               @checked(old('is_active', true))>

                        <label class="form-check-label" for="is_active">
                            Active Vendor
                        </label>

                    </div>

                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    Save Vendor
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
