
@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3>Vendor Management</h3>
        <p class="text-muted mb-0">
            Manage your company vendors
        </p>
    </div>

    <a href="{{ route('vendors.create') }}" class="btn btn-primary">
        + Add Vendor
    </a>

</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <input type="text"
               id="search"
               class="form-control mb-3"
               placeholder="Search vendor...">

        <div class="table-responsive">

            <table class="table table-hover align-middle" id="vendorTable">

                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Vendor Name</th>
                        <th>GSTIN</th>
                        <th>State</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($vendors as $vendor)

                    <tr>
                        <td>{{ $vendor->code }}</td>
                        <td>{{ $vendor->name }}</td>
                        <td>{{ $vendor->gstin ?? '-' }}</td>
                        <td>{{ $vendor->state ?? '-' }}</td>
                        <td>{{ $vendor->payment_terms_days }} days</td>

                        <td>
                            @if($vendor->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('vendors.edit', $vendor->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('vendors.destroy', $vendor->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this vendor?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No vendors found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{ $vendors->links('pagination::bootstrap-5') }}

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('search').addEventListener('keyup', function () {
        let value = this.value.toLowerCase();

        document.querySelectorAll('#vendorTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value)
                ? ''
                : 'none';
        });
    });
</script>
@endpush
