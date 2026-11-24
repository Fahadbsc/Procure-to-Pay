<?php
namespace App\Http\Controllers;

use App\Models\{SupplierInvoice, Vendor, VendorPayment, VendorPaymentAllocation};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VendorPaymentController extends Controller
{
    public function index()
    {
        $payments = VendorPayment::with(['vendor','allocations.invoice'])->where('company_id', Auth::user()->company_id)->latest()->paginate(20);
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = SupplierInvoice::with(['vendor','items.item'])
            ->where('company_id', Auth::user()->company_id)
            ->whereIn('status', ['POSTED','PARTIALLY_PAID'])
            ->orderBy('due_date')->get()
            ->map(function ($invoice) {
                $invoice->paid_amount = VendorPaymentAllocation::where('supplier_invoice_id', $invoice->id)->sum('gross_amount');
                $invoice->outstanding_amount = max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount);
                $invoice->tds_eligible = $invoice->items->contains(fn($line) => $line->item?->category === 'SERVICES');
                $invoice->service_taxable = $invoice->items->filter(fn($line) => $line->item?->category === 'SERVICES')->sum(fn($line) => (float)$line->quantity * (float)$line->unit_price);
                return $invoice;
            })->filter(fn($invoice) => $invoice->outstanding_amount > 0)->values();
        return view('payments.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_date'=>'required|date', 'reference'=>'nullable|string|max:120', 'remarks'=>'nullable|string|max:1000',
            'allocations'=>'required|array|min:1', 'allocations.*.invoice_id'=>'required|integer|distinct|exists:supplier_invoices,id',
            'allocations.*.amount'=>'required|numeric|gt:0',
        ]);
        $companyId = Auth::user()->company_id;
        $payment = DB::transaction(function () use ($data, $companyId) {
            $ids = collect($data['allocations'])->pluck('invoice_id');
            $invoices = SupplierInvoice::with(['vendor','items.item'])->where('company_id',$companyId)->whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if ($invoices->count() !== $ids->count()) throw ValidationException::withMessages(['allocations'=>'Select invoices belonging to your company.']);
            $vendorIds = $invoices->pluck('vendor_id')->unique();
            if ($vendorIds->count() !== 1) throw ValidationException::withMessages(['allocations'=>'A single payment can only cover invoices from one vendor.']);
            $grossTotal = $tdsTotal = 0; $rows = [];
            foreach ($data['allocations'] as $row) {
                $invoice = $invoices->get($row['invoice_id']);
                if (!in_array($invoice->status, ['POSTED','PARTIALLY_PAID'])) throw ValidationException::withMessages(['allocations'=>'Only posted invoices can be paid.']);
                $paid = (float) VendorPaymentAllocation::where('supplier_invoice_id',$invoice->id)->lockForUpdate()->sum('gross_amount');
                $amount = round((float)$row['amount'],2);
                if ($amount > (float)$invoice->total_amount - $paid + 0.001) throw ValidationException::withMessages(['allocations'=>'Payment exceeds invoice outstanding amount.']);
                $service = $invoice->items->contains(fn($line) => $line->item?->category === 'SERVICES');
                $serviceTaxable = $invoice->items->filter(fn($line) => $line->item?->category === 'SERVICES')->sum(fn($line) => (float)$line->quantity * (float)$line->unit_price);
                $taxableRatio = (float)$invoice->total_amount > 0 ? $serviceTaxable / (float)$invoice->total_amount : 0;
                $tds = $service ? round($amount * $taxableRatio * 0.02, 2, PHP_ROUND_HALF_UP) : 0;
                $rows[] = ['invoice'=>$invoice,'gross'=>$amount,'tds'=>$tds];
                $grossTotal += $amount; $tdsTotal += $tds;
            }
            $payment = VendorPayment::create([
                'company_id'=>$companyId,'vendor_id'=>$vendorIds->first(),'paid_by'=>Auth::id(),
                'payment_number'=>'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'payment_date'=>$data['payment_date'],'gross_amount'=>$grossTotal,'tds_amount'=>$tdsTotal,
                'net_amount'=>$grossTotal-$tdsTotal,'reference'=>$data['reference']??null,'remarks'=>$data['remarks']??null,
            ]);
            foreach ($rows as $row) {
                VendorPaymentAllocation::create(['vendor_payment_id'=>$payment->id,'supplier_invoice_id'=>$row['invoice']->id,'gross_amount'=>$row['gross'],'tds_amount'=>$row['tds']]);
                $paid = (float) VendorPaymentAllocation::where('supplier_invoice_id',$row['invoice']->id)->sum('gross_amount');
                $isFullyPaid = $paid + 0.001 >= (float)$row['invoice']->total_amount;
                $row['invoice']->update([
                    'status' => $isFullyPaid ? 'PAID' : 'PARTIALLY_PAID',
                    'paid_by' => Auth::id(),
                    'paid_at' => $isFullyPaid ? now() : null,
                ]);
            }
            DB::table('audit_logs')->insert(['company_id'=>$companyId,'user_id'=>Auth::id(),'auditable_type'=>VendorPayment::class,'auditable_id'=>$payment->id,'from_status'=>null,'to_status'=>'RECORDED','comment'=>$data['remarks']??null,'created_at'=>now()]);
            return $payment;
        });
        return redirect()->route('payments.index')->with('success','Payment recorded; TDS and net transfer are calculated.');
    }
}
