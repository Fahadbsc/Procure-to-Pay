
@extends('layouts.app')

@section('title', 'Supplier Invoice Details')

@push('styles')
<style>
    .invoice-print-header {
        display: none;
    }

    .signature-line {
        border-top: 1px solid #000;
        padding-top: 5px;
        text-align: center;
        min-height: 25px;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 6mm;
        }

        html, body {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 9px !important;
        }

        .sidebar,
        .navbar,
        .no-print,
        button,
        .btn {
            display: none !important;
        }

        .invoice-print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 8px !important;
        }

        .invoice-print-header h2 {
            font-size: 18px !important;
            margin-bottom: 2px !important;
        }

        .invoice-print-header p {
            margin-bottom: 4px !important;
        }

        .container,
        .container-fluid,
        .main-content {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: 1px solid #999 !important;
            box-shadow: none !important;
            margin-bottom: 8px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .card-header {
            padding: 5px !important;
        }

        .card-header h5 {
            font-size: 11px !important;
            margin: 0 !important;
        }

        .card-body {
            padding: 6px !important;
        }

        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .mb-3 {
            margin-bottom: 5px !important;
        }

        .mb-4 {
            margin-bottom: 8px !important;
        }

        table {
            width: 100% !important;
            font-size: 8px !important;
            margin-bottom: 5px !important;
        }

        th, td {
            padding: 3px !important;
            vertical-align: middle !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .signature-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 8px !important;
        }

        .signature-section .row {
            display: flex !important;
        }

        .signature-section .col-4 {
            width: 33.333333% !important;
            flex: 0 0 33.333333% !important;
        }

        .signature-space {
            height: 35px !important;
        }

        .signature-line {
            min-height: 20px !important;
            padding-top: 4px !important;
        }

        .approval-information {
            margin-top: 5px !important;
            margin-bottom: 8px !important;
        }

        .approval-information .col-md-4 {
            width: 33.333333% !important;
            flex: 0 0 33.333333% !important;
        }

        .totals-section {
            margin-top: 5px !important;
        }

        .totals-section .col-md-4 {
            width: 40% !important;
            flex: 0 0 40% !important;
            margin-left: auto !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3>Supplier Invoice Details</h3>

        <div>
            <button onclick="window.print()" class="btn btn-primary">
                Print Invoice
            </button>

            <a href="{{ route('supplier-invoices.index') }}"
               class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="invoice-print-header">
        <h2 class="fw-bold">SUPPLIER INVOICE</h2>
        <p>Invoice Document</p>
        <hr>
    </div>

    {{-- Invoice Information --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">
            <h5 class="mb-0">Invoice Information</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-3">
                    <strong>Invoice Number:</strong>
                    <div>{{ $supplierInvoice->invoice_number }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Supplier Invoice Number:</strong>
                    <div>{{ $supplierInvoice->supplier_invoice_number }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Invoice Date:</strong>
                    <div>{{ $supplierInvoice->invoice_date?->format('d-m-Y') }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Due Date:</strong>
                    <div>{{ $supplierInvoice->due_date?->format('d-m-Y') ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>PO Number:</strong>
                    <div>{{ $supplierInvoice->purchaseOrder->po_number ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>GR Number:</strong>
                    <div>{{ $supplierInvoice->goodsReceipt->gr_number ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Vendor:</strong>
                    <div>{{ $supplierInvoice->purchaseOrder->vendor->name ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Status:</strong>
                    <div>
                        @php
                            $statusClass = match($supplierInvoice->status) {
                                'APPROVED', 'POSTED' => 'bg-success',
                                'SUBMITTED' => 'bg-warning text-dark',
                                'REJECTED', 'ON_HOLD' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $statusClass }}">
                            {{ $supplierInvoice->status }}
                        </span>
                    </div>
                </div>

                <div class="col-md-12">
                    <strong>Remarks:</strong>
                    <div>{{ $supplierInvoice->remarks ?? '-' }}</div>
                </div>

            </div>
        </div>
    </div>

    {{-- Approval Information --}}
    <div class="row approval-information mt-3 mb-3">

        <div class="col-md-4">
            <strong>Submitted By:</strong>
            {{ $supplierInvoice->submitter?->name ?? '-' }}
        </div>

        <div class="col-md-4">
            <strong>Reviewed By:</strong>
            {{ $supplierInvoice->reviewer?->name ?? '-' }}
        </div>

        <div class="col-md-4">
            <strong>Paid By:</strong>
            {{ $supplierInvoice->payer?->name ?? '-' }}
        </div>
@if($supplierInvoice->override_comment)

    <div class="col-12 mt-2">
        <strong>Override Comment:</strong>
        {{ $supplierInvoice->override_comment }}
    </div>

    <div class="col-md-4 mt-2">
        <strong>Overridden By:</strong>
        {{ \App\Models\User::find($supplierInvoice->overridden_by)?->name ?? '-' }}
    </div>

    <div class="col-md-4 mt-2">
        <strong>Overridden At:</strong>
        {{ $supplierInvoice->overridden_at?->format('d-m-Y H:i') ?? '-' }}
    </div>

@endif
        @if($supplierInvoice->reviewed_at)
            <div class="col-md-4 mt-2">
                <strong>Reviewed At:</strong>
                {{ $supplierInvoice->reviewed_at->format('d-m-Y H:i') }}
            </div>
        @endif

        @if($supplierInvoice->paid_at)
            <div class="col-md-4 mt-2">
                <strong>Paid At:</strong>
                {{ $supplierInvoice->paid_at->format('d-m-Y H:i') }}
            </div>
        @endif

        @if($supplierInvoice->rejection_reason)
            <div class="col-12 mt-2">
                <strong>Rejection Reason:</strong>
                {{ $supplierInvoice->rejection_reason }}
            </div>
        @endif

    </div>

    {{-- Invoice Workflow Actions --}}
    <div class="no-print mb-4">

        <div class="d-flex gap-2 flex-wrap">

{{-- ON HOLD Override and Reject --}}
@if($supplierInvoice->status === 'ON_HOLD')

    <form action="{{ route('supplier-invoices.override', $supplierInvoice->id) }}"
          method="POST"
          class="d-flex gap-2 align-items-center flex-wrap">
        @csrf

        <input type="text"
               name="override_comment"
               class="form-control"
               placeholder="Enter mandatory override comment"
               minlength="5"
               maxlength="1000"
               required>

        <button type="submit"
                class="btn btn-warning"
                onclick="return confirm('Override this invoice mismatch?')">
            Override Invoice
        </button>
    </form>

    <form action="{{ route('supplier-invoices.reject-on-hold', $supplierInvoice->id) }}"
          method="POST"
          class="d-flex gap-2 align-items-center flex-wrap">
        @csrf

        <input type="text"
               name="rejection_reason"
               class="form-control"
               placeholder="Enter rejection reason"
               minlength="5"
               maxlength="1000"
               required>

        <button type="submit"
                class="btn btn-danger"
                onclick="return confirm('Reject this invoice?')">
            Reject Invoice
        </button>
    </form>

@endif

            {{-- Submit --}}
            @if($supplierInvoice->status === 'DRAFT')
                <form action="{{ route('supplier-invoices.submit', $supplierInvoice->id) }}"
                      method="POST">
                    @csrf

                    <button type="submit"
                            class="btn btn-primary"
                            onclick="return confirm('Submit this invoice?')">
                        Submit Invoice
                    </button>
                </form>
            @endif

            {{-- Approve and Reject --}}
            @if($supplierInvoice->status === 'SUBMITTED')
<form action="{{ route('supplier-invoices.match', $supplierInvoice->id) }}"
      method="POST"
      onsubmit="return confirm('Run 3-way matching for this invoice?')">
    @csrf

    <button type="submit" class="btn btn-primary">
        Run 3-Way Match
    </button>
</form>
               

                <form action="{{ route('supplier-invoices.reject', $supplierInvoice->id) }}"
                      method="POST"
                      class="d-flex gap-2">
                    @csrf

                    <input type="text"
                           name="rejection_reason"
                           class="form-control"
                           placeholder="Enter rejection reason"
                           required
                           maxlength="1000">

                    <button type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Reject this invoice?')">
                        Reject Invoice
                    </button>
                </form>

            @endif

            {{-- Post Invoice --}}
@if($supplierInvoice->status === 'MATCHED')
                <form action="{{ route('supplier-invoices.post', $supplierInvoice->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to post this invoice?')">
                    @csrf

                    <button type="submit" class="btn btn-success">
                        Post Invoice
                    </button>
                </form>
            @endif

        </div>
    </div>

    {{-- Invoice Items --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">
            <h5 class="mb-0">Invoice Item Details</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th class="text-end">Quantity</th>
                            <th>UOM</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">GST %</th>
                            <th class="text-end">Tax Amount</th>
                            <th class="text-end">Line Total</th>
                            <th>Match Status</th>
                            <th>Match Reason</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($supplierInvoice->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>{{ $item->item->code ?? '-' }}</td>

                                <td>{{ $item->item->name ?? '-' }}</td>

                                <td class="text-end">
                                    {{ number_format((float) $item->quantity, 2) }}
                                </td>

                                <td>
                                    {{ $item->uom->code ?? $item->item->baseUom->code ?? '-' }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $item->unit_price, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $item->gst_rate, 2) }}%
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $item->tax_amount, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $item->line_total, 2) }}
                                </td>
                                <td>
    <span class="badge {{ $item->match_status === 'MATCHED' ? 'bg-success' : ($item->match_status === 'ON_HOLD' ? 'bg-danger' : 'bg-secondary') }}">
        {{ $item->match_status ?? 'PENDING' }}
    </span>
</td>

<td>
    {{ $item->match_reason ?? '-' }}
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    No invoice items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- Totals --}}
    <div class="row justify-content-end totals-section">

        <div class="col-md-4">
            <table class="table table-bordered">

                <tr>
                    <th>Subtotal</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $supplierInvoice->subtotal, 2) }}
                    </td>
                </tr>

                <tr>
                    <th>CGST</th><td class="text-end">₹{{ number_format((float)$supplierInvoice->cgst_total,2) }}</td>
                </tr><tr><th>SGST</th><td class="text-end">₹{{ number_format((float)$supplierInvoice->sgst_total,2) }}</td>
                </tr><tr><th>IGST</th><td class="text-end">₹{{ number_format((float)$supplierInvoice->igst_total,2) }}</td>
                </tr><tr><th>Total GST</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $supplierInvoice->tax_amount, 2) }}
                    </td>
                </tr>

                <tr class="table-light fw-bold">
                    <th>Grand Total</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $supplierInvoice->total_amount, 2) }}
                    </td>
                </tr>

            </table>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="signature-section">

        <div class="row mt-3">

            <div class="col-4 text-center">
                <div class="signature-space"></div>
                <div class="signature-line">Prepared By</div>
                <small>Name &amp; Signature</small>
            </div>

            <div class="col-4 text-center">
                <div class="signature-space"></div>
                <div class="signature-line">Checked By</div>
                <small>Name &amp; Signature</small>
            </div>

            <div class="col-4 text-center">
                <div class="signature-space"></div>
                <div class="signature-line">Approved By</div>
                <small>Name &amp; Signature</small>
            </div>

        </div>
    </div>

</div>

@endsection
