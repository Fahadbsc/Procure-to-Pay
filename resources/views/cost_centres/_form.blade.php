<div class="row">
    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">Code *</label>
        <input id="code" name="code" class="form-control" maxlength="255" value="{{ old('code', $centre?->code) }}" required>
        @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Name *</label>
        <input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $centre?->name) }}" required>
        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $centre?->is_active ?? true))>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
<a href="{{ route('cost-centres.index') }}" class="btn btn-light">Cancel</a>
