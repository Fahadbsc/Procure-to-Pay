
@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Reports</h3>
                <p class="text-muted mb-0">
                Purchase, budget, payables, and supplier invoice reports
            </p>
        </div>

        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    {{-- Date Filter --}}
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">

            <form method="GET" action="{{ route('reports.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date"
                               name="from_date"
                               class="form-control"
                               value="{{ $fromDate }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date"
                               name="to_date"
                               class="form-control"
                               value="{{ $toDate }}">
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>

                        <a href="{{ route('reports.index') }}"
                           class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Purchase Requisition Report --}}
    <h5 class="fw-bold mb-3">Purchase Requisitions</h5>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total PR</p>
                    <h3 class="fw-bold">{{ $totalPR }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Pending PR</p>
                    <h3 class="fw-bold text-warning">{{ $pendingPR }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Approved PR</p>
                    <h3 class="fw-bold text-success">{{ $approvedPR }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Rejected PR</p>
                    <h3 class="fw-bold text-danger">{{ $rejectedPR }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Purchase Order Report --}}
    <h5 class="fw-bold mb-3">Purchase Orders</h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Purchase Orders</p>
                    <h3 class="fw-bold">{{ $totalPO }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Sent Purchase Orders</p>
                    <h3 class="fw-bold text-success">{{ $sentPO }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Goods Receipt Report --}}
    <h5 class="fw-bold mb-3">Goods Receipts</h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Goods Receipts</p>
                    <h3 class="fw-bold">{{ $totalGR }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Completed Goods Receipts</p>
                    <h3 class="fw-bold text-success">{{ $completedGR }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Supplier Invoice Report --}}
    <h5 class="fw-bold mb-3">Supplier Invoice Summary</h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Invoices</p>
                    <h3 class="fw-bold">{{ $totalInvoices }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                <p class="text-muted mb-1">Posted Invoice Amount</p>
                    <h3 class="fw-bold text-primary">
                        ₹{{ number_format((float) $invoiceAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Paid Amount</p>
                    <h3 class="fw-bold text-success">
                        ₹{{ number_format((float) $paidAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                <p class="text-muted mb-1">Outstanding Posted Amount</p>
                    <h3 class="fw-bold text-warning">
                        ₹{{ number_format((float) $unpaidAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <h5 class="fw-bold mb-3">Budget vs Committed vs Spent</h5>
    <div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>FY</th><th>Cost centre</th><th>Budget</th><th>Committed</th><th>Spent</th><th>Available</th></tr></thead><tbody>
    @forelse($budgetRows as $budget)<tr><td>{{ $budget->financial_year }}</td><td>{{ $budget->costCentre->code }}</td><td>{{ number_format($budget->budget_amount,2) }}</td><td>{{ number_format($budget->committed_amount,2) }}</td><td>{{ number_format($budget->spent_amount,2) }}</td><td>{{ number_format($budget->available_amount,2) }}</td></tr>@empty<tr><td colspan="6">No budgets recorded.</td></tr>@endforelse
    </tbody></table></div>

    <h5 class="fw-bold mb-3">Open Purchase Orders and Pending Quantity</h5>
    <div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>PO</th><th>Vendor</th><th>Item</th><th>Ordered</th><th>Accepted</th><th>Pending</th><th>Unit</th></tr></thead><tbody>
    @forelse($openPurchaseOrders as $po)@foreach($po->items as $line)@php($accepted=$po->goodsReceipts->flatMap->items->where('purchase_order_item_id',$line->id)->sum('accepted_quantity'))<tr><td>{{ $po->po_number }}</td><td>{{ $po->vendor->name }}</td><td>{{ $line->item->name }}</td><td>{{ number_format($line->quantity,2) }}</td><td>{{ number_format($accepted,2) }}</td><td>{{ number_format(max(0,(float)$line->quantity-(float)$accepted),2) }}</td><td>{{ $line->uom->code ?? $line->item->baseUom->code ?? '-' }}</td></tr>@endforeach @empty<tr><td colspan="7">No open POs.</td></tr>@endforelse
    </tbody></table></div>

    <h5 class="fw-bold mb-3">Invoices on Hold</h5>
    <div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>Invoice</th><th>Vendor</th><th>Amount</th><th>Line</th><th>Reason</th></tr></thead><tbody>
    @forelse($onHoldInvoices as $invoice)@foreach($invoice->items->where('match_status','ON_HOLD') as $line)<tr><td>{{ $invoice->supplier_invoice_number }}</td><td>{{ $invoice->vendor->name }}</td><td>{{ number_format($invoice->total_amount,2) }}</td><td>{{ $line->item->name }}</td><td>{{ $line->match_reason }}</td></tr>@endforeach @empty<tr><td colspan="5">No held invoices.</td></tr>@endforelse
    </tbody></table></div>

    <h5 class="fw-bold mb-3">Vendor Ageing (past due)</h5>
    <div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>Vendor</th><th>0–30</th><th>31–60</th><th>61–90</th><th>90+</th><th>Total due</th></tr></thead><tbody>
    @forelse($vendorAgeing as $row)<tr><td>{{ $row['vendor'] }}</td>@foreach(['0-30','31-60','61-90','90+'] as $bucket)<td>{{ number_format($row[$bucket],2) }}</td>@endforeach<td>{{ number_format($row['total'],2) }}</td></tr>@empty<tr><td colspan="6">No vendor balances.</td></tr>@endforelse
    </tbody></table></div>

    <h5 class="fw-bold mb-3">Vendor Ledger</h5>
    <div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>Date</th><th>Vendor</th><th>Document</th><th>Debit (payment)</th><th>Credit (invoice)</th><th>Running balance</th></tr></thead><tbody>
    @forelse($ledgerRows as $entry)<tr><td>{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}</td><td>{{ $entry['vendor'] }}</td><td>{{ $entry['document'] }}</td><td>{{ number_format($entry['debit'],2) }}</td><td>{{ number_format($entry['credit'],2) }}</td><td>{{ number_format($entry['balance'],2) }}</td></tr>@empty<tr><td colspan="6">No posted invoices or payments.</td></tr>@endforelse
    </tbody></table></div>

    {{-- Recent Invoice Details --}}
    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Supplier Invoice Details</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Invoice No</th>
                            <th>Supplier Invoice No</th>
                            <th>Vendor</th>
                            <th>Invoice Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentInvoices as $invoice)

                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>

                                <td>{{ $invoice->supplier_invoice_number }}</td>

                                <td>
                                    {{ $invoice->purchaseOrder->vendor->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $invoice->invoice_date
                                        ? $invoice->invoice_date->format('d-m-Y')
                                        : '-' }}
                                </td>

                                <td>
                                    ₹{{ number_format((float) $invoice->total_amount, 2) }}
                                </td>

                                <td>
                                    <span class="badge
                                        @if($invoice->status === 'PAID') bg-success
                                        @elseif($invoice->status === 'APPROVED') bg-info
                                        @elseif($invoice->status === 'REJECTED') bg-danger
                                        @elseif($invoice->status === 'SUBMITTED') bg-warning text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No invoices found for the selected period.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    @media print {
        .no-print,
        .sidebar,
        nav,
        .navbar,
        button {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            break-inside: avoid;
        }

        .container-fluid {
            width: 100% !important;
        }
    }
</style>
@endpush
