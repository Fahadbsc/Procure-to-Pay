
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dashboard</h3>
            <p class="text-muted mb-0">
                Welcome, {{ auth()->user()->name }}
            </p>
        </div>

        <span class="badge bg-primary px-3 py-2">
            {{ now()->format('d M Y') }}
        </span>
    </div>

    {{-- Company Information --}}
    <div class="alert alert-primary border-0 shadow-sm">
        <strong>Company:</strong>
        {{ auth()->user()->company->name ?? '-' }}

        <br>

        <strong>Roles:</strong>
        @foreach(auth()->user()->roles as $role)
            <span class="badge bg-primary ms-1">
                {{ $role->name }}
            </span>
        @endforeach
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3">

        {{-- Purchase Requisitions --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Purchase Requests</p>
                            <h3 class="fw-bold mb-0">{{ $totalPR }}</h3>
                            <small class="text-warning">
                                Pending: {{ $pendingPR }}
                            </small>
                        </div>
                        <div class="fs-2 text-primary">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Purchase Orders --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Purchase Orders</p>
                            <h3 class="fw-bold mb-0">{{ $totalPO }}</h3>
                            <small class="text-success">
                                Sent Orders: {{ $sentPO }}
                            </small>
                        </div>
                        <div class="fs-2 text-success">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Goods Receipts --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Goods Receipts</p>
                            <h3 class="fw-bold mb-0">{{ $totalGR }}</h3>
                            <small class="text-success">
                                Completed: {{ $completedGR }}
                            </small>
                        </div>
                        <div class="fs-2 text-info">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pending Approvals</p>
                            <h3 class="fw-bold mb-0">
                                {{ $pendingPR + $pendingInvoices }}
                            </h3>
                            <small class="text-warning">
                                PRs: {{ $pendingPR }}
                                | Invoices: {{ $pendingInvoices }}
                            </small>
                        </div>
                        <div class="fs-2 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier Invoices --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Supplier Invoices</p>
                            <h3 class="fw-bold mb-0">{{ $totalInvoices }}</h3>
                            <small class="text-success">
                                Paid: {{ $paidInvoices }}
                                | Posted / partially paid: {{ $approvedInvoices }}
                            </small>
                        </div>
                        <div class="fs-2 text-danger">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Invoice Amount Summary --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Invoice Amount Summary</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <p class="text-muted mb-1">
                            Total Posted Invoice Amount
                        </p>
                        <h4 class="fw-bold text-primary mb-0">
                            ₹{{ number_format((float) $totalInvoiceAmount, 2) }}
                        </h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <p class="text-muted mb-1">Paid Amount</p>
                        <h4 class="fw-bold text-success mb-0">
                            ₹{{ number_format((float) $paidAmount, 2) }}
                        </h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <p class="text-muted mb-1">Outstanding Posted Amount</p>
                        <h4 class="fw-bold text-warning mb-0">
                            ₹{{ number_format((float) $unpaidAmount, 2) }}
                        </h4>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Welcome Section --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold">Welcome to Acme Procure-to-Pay</h5>

            <p class="text-muted mb-0">
                Manage your purchasing activities, approvals, goods receipts
                and vendor invoices from one place.
            </p>
        </div>
    </div>

    {{-- Recent Supplier Invoices --}}
    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recent Supplier Invoices</h5>

            <a href="{{ route('supplier-invoices.index') }}"
               class="btn btn-sm btn-outline-primary">
                View All
            </a>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Invoice No</th>
                            <th>Supplier Invoice</th>
                            <th>Vendor</th>
                            <th>Invoice Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentInvoices as $invoice)

                            <tr>
                                <td class="fw-semibold">
                                    {{ $invoice->invoice_number }}
                                </td>

                                <td>
                                    {{ $invoice->supplier_invoice_number }}
                                </td>

                                <td>
                                    {{ $invoice->purchaseOrder->vendor->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $invoice->invoice_date
                                        ? $invoice->invoice_date->format('d-m-Y')
                                        : '-' }}
                                </td>

                                <td class="fw-semibold">
                                    ₹{{ number_format((float) $invoice->total_amount, 2) }}
                                </td>

                                <td>
                                    @php
                                        $statusColors = [
                                            'DRAFT' => 'secondary',
                                            'SUBMITTED' => 'warning',
                                            'APPROVED' => 'info',
                                            'REJECTED' => 'danger',
                                            'PAID' => 'success',
                                        ];

                                        $statusColor = $statusColors[$invoice->status] ?? 'secondary';
                                    @endphp

                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ $invoice->status }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('supplier-invoices.show', $invoice->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No supplier invoices found.
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
