
@extends('layouts.app')

@section('title', 'Purchase Requisitions')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Purchase Requisitions</h4>

        <a href="{{ route('purchase-requisitions.create') }}"
           class="btn btn-primary">
            + Create PR
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PR Number</th>
                            <th>Requested By</th>
                            <th>Required Date</th>
                            <th>Total Items</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requisitions as $pr)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($requisitions->currentPage() - 1) * $requisitions->perPage() }}
                                </td>

                                <td>{{ $pr->pr_number }}</td>

                                <td>{{ $pr->requester->name ?? '-' }}</td>

                                <td>
                                    {{ $pr->required_date->format('d-m-Y') }}
                                </td>

                                <td>{{ $pr->items->count() }}</td>

                                <td>
                                    @if($pr->status === 'APPROVED')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($pr->status === 'REJECTED')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($pr->status === 'PENDING')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $pr->status }}</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('purchase-requisitions.show', $pr->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No Purchase Requisitions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $requisitions->links('pagination::bootstrap-5') }}

        </div>
    </div>
</div>
@endsection
