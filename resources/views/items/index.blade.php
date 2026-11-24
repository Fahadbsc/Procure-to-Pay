
@extends('layouts.app')

@section('title', 'Item Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Item Management</h4>
        <a href="{{ route('items.create') }}" class="btn btn-primary">
            + Add Item
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchItem"
                    class="form-control"
                    placeholder="Search item code, name or category...">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Base Unit</th>
                            <th>GST (%)</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ str_replace('_', ' ', $item->category) }}</td>
                                <td>{{ $item->baseUom->code ?? '-' }}</td>
                                <td>{{ number_format((float) $item->gst_rate, 2) }}</td>
                                <td>
                                    <a href="{{ route('items.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('items.destroy', $item->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this item?')">
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
                                <td colspan="7" class="text-center">
                                    No items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchItem').addEventListener('keyup', function () {
        const search = this.value.toLowerCase();

        document.querySelectorAll('#itemsTable tbody tr').forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(search)
                ? ''
                : 'none';
        });
    });
</script>
@endpush
