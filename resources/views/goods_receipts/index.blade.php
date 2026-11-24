
@extends('layouts.app')

@section('title', 'Goods Receipts')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Goods Receipts</h3>

        <a href="{{ route('goods-receipts.create') }}"
           class="btn btn-primary">
            + Create Goods Receipt
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>GR Number</th>
                            <th>PO Number</th>
                            <th>Vendor</th>
                            <th>Received Date</th>
                            <th>Received By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($goodsReceipts as $index => $gr)
                            <tr>
                                <td>{{ $goodsReceipts->firstItem() + $index }}</td>

                                <td>{{ $gr->gr_number }}</td>

                                <td>{{ $gr->purchaseOrder->po_number ?? '-' }}</td>

                                <td>{{ $gr->purchaseOrder->vendor->name ?? '-' }}</td>

                                <td>{{ $gr->received_date?->format('d-m-Y') }}</td>

                                <td>{{ $gr->receiver->name ?? '-' }}</td>

                                <td>
                                    @if($gr->status === 'COMPLETED')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('goods-receipts.show', $gr->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No Goods Receipts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{ $goodsReceipts->links() }}

        </div>
    </div>

</div>
@endsection
