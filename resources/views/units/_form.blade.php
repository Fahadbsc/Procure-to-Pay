<div class="row">
    <div class="col-md-4 mb-3">
        <label for="code" class="form-label">Unit Code *</label>
        <input id="code" name="code" class="form-control" maxlength="255" value="{{ old('code', $unit?->code) }}" required>
        @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="base_unit" class="form-label">Base Unit *</label>
        <input id="base_unit" name="base_unit" class="form-control" maxlength="255" value="{{ old('base_unit', $unit?->base_unit) }}" required>
        @error('base_unit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="conversion_factor" class="form-label">Conversion Factor *</label>
        <input id="conversion_factor" type="number" name="conversion_factor" class="form-control" min="0.000001" step="0.000001" value="{{ old('conversion_factor', $unit?->conversion_factor ?? 1) }}" required>
        <div class="form-text">Quantity multiplier used to convert this unit to its base unit.</div>
        @error('conversion_factor')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>
<button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
<a href="{{ route('units.index') }}" class="btn btn-light">Cancel</a>
