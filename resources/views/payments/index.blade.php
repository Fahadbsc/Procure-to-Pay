@extends('layouts.app')
@section('title', 'Vendor Payments')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between mb-4"><h3>Vendor Payments</h3><a href="{{ route('payments.create') }}" class="btn btn-primary">Record Payment</a></div>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Payment</th><th>Date</th><th>Vendor</th><th>Gross settled</th><th>TDS</th><th>Net transferred</th><th>Invoices</th></tr></thead><tbody>
@forelse($payments as $payment)<tr><td>{{ $payment->payment_number }}</td><td>{{ $payment->payment_date->format('d-m-Y') }}</td><td>{{ $payment->vendor->name }}</td><td>₹{{ number_format((float)$payment->gross_amount,2) }}</td><td>₹{{ number_format((float)$payment->tds_amount,2) }}</td><td>₹{{ number_format((float)$payment->net_amount,2) }}</td><td>{{ $payment->allocations->pluck('invoice.invoice_number')->filter()->join(', ') }}</td></tr>@empty<tr><td colspan="7" class="text-center">No payment records.</td></tr>@endforelse
</tbody></table></div>{{ $payments->links() }}</div>
@endsection
