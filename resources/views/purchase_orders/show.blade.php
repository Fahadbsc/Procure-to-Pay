
@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container-fluid">
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <div class="d-flex justify-content-between align-items-center mb-4">

    <h3>Purchase Order Details</h3>

    <div class="d-flex gap-2">

        @if($purchaseOrder->status === 'DRAFT')

            <form action="{{ route('purchase-orders.send', $purchaseOrder->id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to send this PO?')">
                @csrf

                <button type="submit" class="btn btn-success">
                    Send PO
                </button>
            </form>

            <form action="{{ route('purchase-orders.cancel', $purchaseOrder->id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to cancel this PO?')">
                @csrf

                <button type="submit" class="btn btn-danger">
                    Cancel PO
                </button>
            </form>

        @endif
@if(in_array($purchaseOrder->status, ['SENT','APPROVED']))
    <form action="{{ route('purchase-orders.cancel', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Cancel this PO? Any remaining budget commitment will be released.')">
        @csrf<button type="submit" class="btn btn-danger">Cancel PO</button>
    </form>
@endif
        <button onclick="window.print()" class="btn btn-primary">
            Print PO
        </button>

        <a href="{{ route('purchase-orders.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </div>
</div>

    {{-- PO Information --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Purchase Order Information</h5>
            
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-3 mb-3">
                    <strong>PO Number</strong>
                    <div>{{ $purchaseOrder->po_number }}</div>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>PR Number</strong>
                    <div>
                        {{ $purchaseOrder->purchaseRequisition->pr_number ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>Order Date</strong>
                    <div>{{ $purchaseOrder->order_date?->format('d-m-Y') }}</div>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>Expected Delivery</strong>
                    <div>
                        {{ $purchaseOrder->expected_delivery_date?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>Status</strong>
                    <div>
                     @if($purchaseOrder->status === 'DRAFT')
    <span class="badge bg-secondary">Draft</span>
@elseif($purchaseOrder->status === 'SENT')
    <span class="badge bg-info">Sent</span>
@elseif($purchaseOrder->status === 'APPROVED')
    <span class="badge bg-success">Approved</span>
@elseif($purchaseOrder->status === 'PARTIALLY_RECEIVED')
    <span class="badge bg-info">Partially received</span>
@elseif($purchaseOrder->status === 'RECEIVED')
    <span class="badge bg-success">Received</span>
@elseif($purchaseOrder->status === 'CLOSED')
    <span class="badge bg-secondary">Closed</span>
@elseif($purchaseOrder->status === 'CANCELLED')
    <span class="badge bg-danger">Cancelled</span>
@endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Vendor Information --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Vendor Information</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <strong>Vendor Name</strong>
                    <div>{{ $purchaseOrder->vendor->name ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Vendor Code</strong>
                    <div>{{ $purchaseOrder->vendor->code ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>GSTIN</strong>
                    <div>{{ $purchaseOrder->vendor->gstin ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>State</strong>
                    <div>{{ $purchaseOrder->vendor->state ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Payment Terms</strong>
                    <div>
                        {{ $purchaseOrder->vendor->payment_terms_days ?? 0 }} Days
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Purchase Order Items</h5>
        </div>

        <div class="card-body">

            @php
                $subtotal = 0;
                $totalGst = 0;
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Unit Price</th>
                            <th>GST %</th>
                            <th>Taxable Amount</th>
                            <th>GST Amount</th>
                            <th>GST Type</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($purchaseOrder->items as $index => $poItem)

                            @php
                                $lineSubtotal = (float) $poItem->quantity
                                    * (float) $poItem->unit_price;

                                $lineGst = (float)$poItem->cgst_amount + (float)$poItem->sgst_amount + (float)$poItem->igst_amount;
                                if ($lineGst == 0 && (float)$poItem->gst_rate > 0) $lineGst = round($lineSubtotal * (float)$poItem->gst_rate / 100, 2);

                                $lineTotal = $lineSubtotal + $lineGst;

                                $subtotal += $lineSubtotal;
                                $totalGst += $lineGst;
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>{{ $poItem->item->code ?? '-' }}</td>

                                <td>{{ $poItem->item->name ?? '-' }}</td>

                                <td>{{ number_format((float) $poItem->quantity, 2) }}</td>

                                <td>
                                    {{ $poItem->uom->code ?? $poItem->item->baseUom->code ?? '-' }}
                                </td>

                                <td>{{ number_format((float) $poItem->unit_price, 2) }}</td>

                                <td>{{ number_format((float) $poItem->gst_rate, 2) }}%</td>

                                <td>{{ number_format($lineSubtotal, 2) }}</td>

                                <td>{{ number_format($lineGst, 2) }}</td>
                                <td>@if((float)$poItem->igst_amount > 0) IGST ₹{{ number_format($poItem->igst_amount,2) }} @else CGST ₹{{ number_format($poItem->cgst_amount,2) }} + SGST ₹{{ number_format($poItem->sgst_amount,2) }} @endif</td>

                                <td>{{ number_format($lineTotal, 2) }}</td>
                            </tr>

                        @endforeach
                    </tbody>

                    <tfoot class="table-light">

                        <tr>
                            <th colspan="10" class="text-end">
                                Subtotal
                            </th>
                            <th>{{ number_format($subtotal, 2) }}</th>
                        </tr>

                        <tr>
                            <th colspan="10" class="text-end">
                                Total GST
                            </th>
                            <th>{{ number_format($totalGst, 2) }}</th>
                        </tr>

                        <tr>
                            <th colspan="10" class="text-end">
                                Grand Total
                            </th>
                            <th>{{ number_format($subtotal + $totalGst, 2) }}</th>
                        </tr>

                    </tfoot>

                </table>
            </div>

        </div>
    </div>

    {{-- Terms --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Terms & Conditions</h5>
        </div>

        <div class="card-body">
            {{ $purchaseOrder->terms_conditions ?: 'No terms and conditions specified.' }}
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    @media print {
        .sidebar,
        nav,
        .btn,
        button,
        .no-print {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .container-fluid {
            width: 100% !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endpush
