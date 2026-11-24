@extends('layouts.app')

@section('title', 'Units of Measure')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Units of Measure</h4>
            <p class="text-muted mb-0">Manage units and conversion factors used by items.</p>
        </div>
        <a href="{{ route('units.create') }}" class="btn btn-primary">+ Add Unit</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Unit Code</th><th>Base Unit</th><th>Conversion Factor</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $units->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $unit->code }}</td>
                                <td>{{ $unit->base_unit }}</td>
                                <td>{{ number_format((float) $unit->conversion_factor, 6) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No units found. Add a unit to get started.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $units->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
