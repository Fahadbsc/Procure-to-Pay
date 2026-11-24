@extends('layouts.app')

@section('title', 'Cost Centres')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Cost Centres</h4>
            <p class="text-muted mb-0">Manage your company departments and budget owners.</p>
        </div>
        <a href="{{ route('cost-centres.create') }}" class="btn btn-primary">+ Add Cost Centre</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>#</th><th>Code</th><th>Name</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse($centres as $centre)
                        <tr>
                            <td>{{ $centres->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $centre->code }}</td>
                            <td>{{ $centre->name }}</td>
                            <td><span class="badge {{ $centre->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $centre->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('cost-centres.edit', $centre) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('cost-centres.destroy', $centre) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this cost centre?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No cost centres found. Add one to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $centres->links('pagination::bootstrap-5') }}</div>
    </div></div>
</div>
@endsection
