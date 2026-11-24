
@extends('layouts.app')

@section('title', 'Goods Receipt Details')

@push('styles')
<style>
    .gr-print-header {
        display: none;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body {
            background: #fff !important;
            font-size: 11px !important;
            color: #000 !important;
        }

        /* Hide application layout elements */
        .sidebar,
        nav,
        aside,
        header,
        footer,
        .no-print,
        .btn,
        .alert {
            display: none !important;
        }

        .main-content,
        .container,
        .container-fluid,
        .content,
        main {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .gr-print-header {
            display: block !important;
        }

        .card {
            border: 1px solid #999 !important;
            box-shadow: none !important;
            margin-bottom: 15px !important;
            page-break-inside: avoid;
        }

        .card-header {
            background: #eee !important;
            color: #000 !important;
            padding: 8px !important;
            border-bottom: 1px solid #999 !important;
        }

        .card-body {
            padding: 10px !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10px !important;
        }

        table th,
        table td {
            border: 1px solid #777 !important;
            padding: 6px !important;
            color: #000 !important;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .badge {
            background: transparent !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }

        .signature-section {
            margin-top: 65px !important;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 8px;
            text-align: center;
            width: 85%;
            margin: 0 auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3>Goods Receipt Details</h3>

        <div>
            <button onclick="window.print()" class="btn btn-primary">
                Print GR
            </button>

            <a href="{{ route('goods-receipts.index') }}"
               class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    {{-- PRINT HEADER --}}
    <div class="gr-print-header text-center mb-4">
        <h2 class="fw-bold mb-1">GOODS RECEIPT NOTE</h2>
        <p class="mb-1">Goods Receipt Document</p>
        <hr>
    </div>

    {{-- RECEIPT INFORMATION --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Receipt Information</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-6 mb-3">
                    <strong>GR Number:</strong>
                    <div>{{ $goodsReceipt->gr_number }}</div>
                </div>

                <div class="col-6 mb-3">
                    <strong>PO Number:</strong>
                    <div>{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}</div>
                </div>

                <div class="col-6 mb-3">
                    <strong>Vendor Name:</strong>
                    <div>{{ $goodsReceipt->purchaseOrder->vendor->name ?? '-' }}</div>
                </div>

                <div class="col-6 mb-3">
                    <strong>Received Date:</strong>
                    <div>{{ $goodsReceipt->received_date?->format('d-m-Y') }}</div>
                </div>

                <div class="col-6 mb-3">
                    <strong>Received By:</strong>
                    <div>{{ $goodsReceipt->receiver->name ?? '-' }}</div>
                </div>

                <div class="col-6 mb-3">
                    <strong>Status:</strong>
                    <div>{{ $goodsReceipt->status }}</div>
                </div>

                <div class="col-12">
                    <strong>Remarks:</strong>
                    <div>{{ $goodsReceipt->remarks ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ITEM DETAILS --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Received Item Details</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Ordered Qty</th>
                            <th>Received Qty</th>
                            <th>Accepted Qty</th>
                            <th>Rejected Qty</th>
                            <th>UOM</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalOrdered = 0;
                            $totalReceived = 0;
                            $totalAccepted = 0;
                            $totalRejected = 0;
                        @endphp

                        @forelse($goodsReceipt->items as $index => $grItem)
                            @php
                                $totalOrdered += (float) $grItem->purchaseOrderItem->quantity;
                                $poFactor = (float)($grItem->purchaseOrderItem->uom->conversion_factor ?? $grItem->purchaseOrderItem->item->baseUom->conversion_factor ?? 1);
                                $receiptFactor = (float)($grItem->receivedUom->conversion_factor ?? $poFactor);
                                $displayFactor = $poFactor / $receiptFactor;
                                $displayReceived = (float)$grItem->received_quantity * $displayFactor;
                                $displayAccepted = (float)$grItem->accepted_quantity * $displayFactor;
                                $displayRejected = (float)$grItem->rejected_quantity * $displayFactor;
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $grItem->purchaseOrderItem->item->code ?? '-' }}
                                </td>

                                <td>
                                    {{ $grItem->purchaseOrderItem->item->name ?? '-' }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $grItem->purchaseOrderItem->quantity, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($displayReceived, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($displayAccepted, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($displayRejected, 2) }}
                                </td>

                                <td>
                                    {{ $grItem->receivedUom->code ?? $grItem->purchaseOrderItem->uom->code ?? $grItem->purchaseOrderItem->item->baseUom->code ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    No items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="table-light fw-bold">
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ITEM REMARKS --}}
    @if($goodsReceipt->items->contains(fn($item) => !empty($item->remarks)))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Item Remarks</h5>
            </div>

            <div class="card-body">
                @foreach($goodsReceipt->items as $item)
                    @if($item->remarks)
                        <p class="mb-2">
                            <strong>
                                {{ $item->purchaseOrderItem->item->name ?? 'Item' }}:
                            </strong>
                            {{ $item->remarks }}
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- SIGNATURES --}}
    <div class="row signature-section">
        <div class="col-4">
            <div class="signature-line">
                Prepared By
            </div>
        </div>

        <div class="col-4">
            <div class="signature-line">
                Received By
            </div>
        </div>

        <div class="col-4">
            <div class="signature-line">
                Approved By
            </div>
        </div>
    </div>

    <div class="gr-print-header text-center mt-4">
        <hr>
        <small>Generated Goods Receipt Document</small>
    </div>

</div>
@endsection
