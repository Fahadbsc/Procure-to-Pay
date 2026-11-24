
@extends('layouts.app')

@section('title', 'Supplier Invoices')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Supplier Invoices</h3>

        <a href="{{ route('supplier-invoices.create') }}"
           class="btn btn-primary">
            + Create Invoice
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Supplier Invoice No.</th>
                            <th>PO Number</th>
                            <th>GR Number</th>
                            <th>Vendor</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $invoices->firstItem() + $index }}</td>

                                <td>{{ $invoice->invoice_number }}</td>

                                <td>{{ $invoice->supplier_invoice_number }}</td>

                                <td>{{ $invoice->purchaseOrder->po_number ?? '-' }}</td>

                                <td>{{ $invoice->goodsReceipt->gr_number ?? '-' }}</td>

                                <td>{{ $invoice->purchaseOrder->vendor->name ?? '-' }}</td>

                                <td>
                                    {{ $invoice->invoice_date?->format('d-m-Y') }}
                                </td>

                                <td class="text-end">
                                    ₹{{ number_format((float) $invoice->total_amount, 2) }}
                                </td>

                                <td>
                                    @php
                                        $statusClass = match($invoice->status) {
                                            'APPROVED' => 'success',
                                            'PAID' => 'primary',
                                            'REJECTED' => 'danger',
                                            'SUBMITTED' => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $invoice->status }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('supplier-invoices.show', $invoice->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    No Supplier Invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $invoices->links() }}

        </div>
    </div>
</div>
@endsection
