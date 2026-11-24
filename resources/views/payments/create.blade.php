@extends('layouts.app')
@section('title', 'Record Vendor Payment')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4"><h3>Record Vendor Payment</h3><a href="{{ route('payments.index') }}" class="btn btn-secondary">Payments</a></div>
    @if($invoices->isEmpty())<div class="alert alert-info">There are no posted invoices with an outstanding balance.</div>@else
    <form method="POST" action="{{ route('payments.store') }}">@csrf
        <div class="row mb-3"><div class="col-md-4"><label class="form-label">Payment date</label><input class="form-control" type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required></div><div class="col-md-4"><label class="form-label">Bank reference</label><input class="form-control" name="reference" maxlength="120"></div><div class="col-md-4"><label class="form-label">Remarks</label><input class="form-control" name="remarks" maxlength="1000"></div></div>
        <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Pay</th><th>Invoice</th><th>Vendor</th><th>Due</th><th>Outstanding</th><th>Amount to settle</th><th>TDS</th><th>Net transfer</th></tr></thead><tbody>
        @foreach($invoices as $i => $invoice)
            <tr data-taxable-ratio="{{ (float)$invoice->service_taxable / max((float)$invoice->total_amount, 0.01) }}" data-service="{{ $invoice->tds_eligible ? 1 : 0 }}">
                <td><input class="form-check-input payment-check" type="checkbox" aria-label="Pay invoice"></td>
                <td>{{ $invoice->invoice_number }}<input type="hidden" name="allocations[{{ $i }}][invoice_id]" value="{{ $invoice->id }}" disabled></td>
                <td>{{ $invoice->vendor->name ?? '-' }}</td><td>{{ $invoice->due_date?->format('d-m-Y') ?? '-' }}</td>
                <td class="outstanding">{{ number_format($invoice->outstanding_amount, 2) }}</td>
                <td><input class="form-control amount-input" type="number" step="0.01" min="0.01" max="{{ $invoice->outstanding_amount }}" name="allocations[{{ $i }}][amount]" disabled></td>
                <td class="tds">0.00</td><td class="net">0.00</td>
            </tr>
        @endforeach
        </tbody></table></div>
        <div class="text-end mb-3"><strong>Gross: ₹<span id="gross">0.00</span> · TDS: ₹<span id="tds">0.00</span> · Net transfer: ₹<span id="net">0.00</span></strong></div>
        <button class="btn btn-success">Save Payment</button>
    </form>@endif
</div>
@endsection
@push('scripts')<script>
document.querySelectorAll('tbody tr').forEach(row => {
 const check=row.querySelector('.payment-check'), input=row.querySelector('.amount-input'), hidden=row.querySelector('input[type=hidden]');
 check.addEventListener('change',()=>{input.disabled=!check.checked; hidden.disabled=!check.checked; if(check.checked&&!input.value) input.value=row.querySelector('.outstanding').textContent.replaceAll(',',''); calc();});
 input.addEventListener('input',calc);
});
function calc(){let gross=0,tds=0;document.querySelectorAll('tbody tr').forEach(row=>{let amount=Number(row.querySelector('.amount-input').value)||0;let tax=amount*Number(row.dataset.taxableRatio||0)*(Number(row.dataset.service)===1?.02:0);row.querySelector('.tds').textContent=tax.toFixed(2);row.querySelector('.net').textContent=(amount-tax).toFixed(2);gross+=amount;tds+=tax;});document.getElementById('gross').textContent=gross.toFixed(2);document.getElementById('tds').textContent=tds.toFixed(2);document.getElementById('net').textContent=(gross-tds).toFixed(2);}
</script>@endpush
