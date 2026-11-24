
@extends('layouts.app')

@section('title', 'Purchase Requisition Details')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Purchase Requisition Details</h4>

        <div>
            <a href="{{ route('purchase-requisitions.index') }}"
               class="btn btn-secondary">
                Back
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                Print
            </button>

            @if($purchaseRequisition->status === 'DRAFT'
                && (int) $purchaseRequisition->requested_by === (int) auth()->id())

                <form action="{{ route('purchase-requisitions.submit', $purchaseRequisition->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Submit this Purchase Requisition for approval?')">
                    @csrf

                    <button type="submit" class="btn btn-success">
                        Submit for Approval
                    </button>
                </form>

            @endif
        </div>
    </div>

    @php
        $totalAmount = $purchaseRequisition->items->sum(function ($row) {
            return (float) $row->quantity * (float) $row->estimated_price;
        });
    @endphp

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <strong>PR Information</strong>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="text-muted">PR Number</label>
                    <h6>{{ $purchaseRequisition->pr_number }}</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Requested By</label>
                    <h6>{{ $purchaseRequisition->requester->name ?? '-' }}</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Required Date</label>
                    <h6>{{ $purchaseRequisition->required_date->format('d-m-Y') }}</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Cost Centre</label>
                    <h6>{{ $purchaseRequisition->costCentre->name ?? '-' }}</h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Status</label>
                    <h6>
                        @if($purchaseRequisition->status === 'APPROVED')
                            <span class="badge bg-success">Approved</span>
                        @elseif($purchaseRequisition->status === 'REJECTED')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($purchaseRequisition->status === 'SUBMITTED')
                            <span class="badge bg-warning text-dark">Submitted for Approval</span>
                        @elseif($purchaseRequisition->status === 'DRAFT')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($purchaseRequisition->status === 'CONVERTED')
                            <span class="badge bg-info text-dark">Converted</span>
                        @elseif($purchaseRequisition->status === 'PARTIALLY_CONVERTED')
                            <span class="badge bg-info text-dark">Partially converted</span>
                        @else
                            <span class="badge bg-secondary">
                                {{ $purchaseRequisition->status }}
                            </span>
                        @endif
                    </h6>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="text-muted">Purpose</label>
                    <h6>{{ $purchaseRequisition->purpose ?? '-' }}</h6>
                </div>

            </div>
        </div>
    </div>

    @if($purchaseRequisition->status === 'SUBMITTED')
        <div class="alert alert-info">
            This Purchase Requisition has been submitted and is waiting for approval.
            Check the My Approvals page for the current approval status.
        </div>
    @endif

    @if($purchaseRequisition->reviewed_at)
        <div class="card mb-4">
            <div class="card-body">

                <strong>Reviewed By:</strong>
                {{ $purchaseRequisition->reviewer->name ?? '-' }}

                <br>

                <strong>Reviewed At:</strong>
                {{ $purchaseRequisition->reviewed_at->format('d-m-Y H:i') }}

                @if($purchaseRequisition->status === 'REJECTED')
                    <br>
                    <strong>Rejection Reason:</strong>
                    {{ $purchaseRequisition->rejection_reason ?? '-' }}
                @endif

            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Requested Items</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Estimated Price</th>
                            <th>Total</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($purchaseRequisition->items as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $row->item->code ?? '-' }}</td>

                                <td>{{ $row->item->name ?? '-' }}</td>

                                <td>
                                    {{ number_format((float) $row->quantity, 2) }}
                                </td>

                                <td>
                                    {{ $row->uom->code ?? $row->item->baseUom->code ?? '-' }}
                                </td>

                                <td>
                                    ₹{{ number_format((float) $row->estimated_price, 2) }}
                                </td>

                                <td>
                                    ₹{{ number_format(
                                        (float) $row->quantity * (float) $row->estimated_price,
                                        2
                                    ) }}
                                </td>

                                <td>{{ $row->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">
                                Grand Total
                            </th>

                            <th colspan="2">
                                ₹{{ number_format($totalAmount, 2) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    @media print {
        .sidebar,
        .btn,
        nav,
        .alert {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
