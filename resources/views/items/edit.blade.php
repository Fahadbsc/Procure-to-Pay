
@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Item</h4>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item Code *</label>
                        <input type="text" name="code"
                            class="form-control"
                            value="{{ old('code', $item->code) }}" required>
                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="name"
                            class="form-control"
                            value="{{ old('name', $item->name) }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach(['RAW_MATERIAL', 'IT', 'SERVICES', 'CONSUMABLES'] as $category)
                                <option value="{{ $category }}"
                                    {{ old('category', $item->category) == $category ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $category) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Base Unit *</label>
                        <select name="base_uom_id" class="form-select" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('base_uom_id', $item->base_uom_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} - {{ $unit->base_unit }}
                                </option>
                            @endforeach
                        </select>
                        @error('base_uom_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">GST Rate (%) *</label>
                        <input type="number" name="gst_rate"
                            class="form-control" step="0.01"
                            min="0" max="100"
                            value="{{ old('gst_rate', $item->gst_rate) }}" required>
                        @error('gst_rate')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Item
                </button>
                <a href="{{ route('items.index') }}" class="btn btn-light">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
