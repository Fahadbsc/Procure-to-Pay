
@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Purchase Orders</h3>

        <a href="{{ route('purchase-orders.create') }}"
           class="btn btn-primary">
            + Create Purchase Order
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>PR Number</th>
                            <th>Vendor</th>
                            <th>Order Date</th>
                            <th>Expected Delivery</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($purchaseOrders as $index => $po)
                            <tr>
                                <td>{{ $purchaseOrders->firstItem() + $index }}</td>

                                <td>{{ $po->po_number }}</td>

                                <td>
                                    {{ $po->purchaseRequisition->pr_number ?? '-' }}
                                </td>

                                <td>{{ $po->vendor->name ?? '-' }}</td>

                                <td>
                                    {{ $po->order_date?->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $po->expected_delivery_date?->format('d-m-Y') ?? '-' }}
                                </td>

                                <td>
                                    @php
                                        [$statusLabel, $statusClass] = match ($po->status) {
                                            'DRAFT' => ['Draft', 'text-bg-secondary'],
                                            'SENT' => ['Pending Approval', 'text-bg-warning'],
                                            'APPROVED' => ['Approved', 'text-bg-success'],
                                            'PARTIALLY_RECEIVED' => ['Partially Received', 'text-bg-info'],
                                            'RECEIVED' => ['Received', 'text-bg-primary'],
                                            'CLOSED' => ['Closed', 'text-bg-dark'],
                                            'CANCELLED' => ['Cancelled', 'text-bg-danger'],
                                            default => [ucwords(strtolower(str_replace('_', ' ', $po->status))), 'text-bg-secondary'],
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>

                                <td>
                                    <a href="{{ route('purchase-orders.show', $po->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No Purchase Orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $purchaseOrders->links() }}

        </div>
    </div>

</div>
@endsection
