<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\SupplierInvoice;
use App\Models\Budget;
use App\Models\Vendor;
use App\Models\VendorPaymentAllocation;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Apply date filter to a query
        $applyDateFilter = function ($query) use ($fromDate, $toDate) {
            if ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            }

            return $query;
        };

        // Purchase Requisitions
        $prQuery = PurchaseRequisition::where('company_id', $companyId);
        $applyDateFilter($prQuery);

        $totalPR = $prQuery->count();

        $pendingPR = PurchaseRequisition::where('company_id', $companyId)
            ->where('status', 'PENDING');

        $applyDateFilter($pendingPR);
        $pendingPR = $pendingPR->count();

        $approvedPR = PurchaseRequisition::where('company_id', $companyId)
            ->where('status', 'APPROVED');

        $applyDateFilter($approvedPR);
        $approvedPR = $approvedPR->count();

        $rejectedPR = PurchaseRequisition::where('company_id', $companyId)
            ->where('status', 'REJECTED');

        $applyDateFilter($rejectedPR);
        $rejectedPR = $rejectedPR->count();

        // Purchase Orders
        $poQuery = PurchaseOrder::where('company_id', $companyId);
        $applyDateFilter($poQuery);

        $totalPO = $poQuery->count();

        $sentPO = PurchaseOrder::where('company_id', $companyId)
            ->where('status', 'SENT');

        $applyDateFilter($sentPO);
        $sentPO = $sentPO->count();

        // Goods Receipts
        $grQuery = GoodsReceipt::where('company_id', $companyId);
        $applyDateFilter($grQuery);

        $totalGR = $grQuery->count();

        $completedGR = GoodsReceipt::where('company_id', $companyId)
            ->where('status', 'COMPLETED');

        $applyDateFilter($completedGR);
        $completedGR = $completedGR->count();

        // Supplier Invoices
        $invoiceQuery = SupplierInvoice::where('company_id', $companyId);
        $applyDateFilter($invoiceQuery);

        $totalInvoices = $invoiceQuery->count();

        $invoiceAmount = SupplierInvoice::where('company_id', $companyId)
            ->whereIn('status', ['POSTED', 'PARTIALLY_PAID', 'PAID']);

        $applyDateFilter($invoiceAmount);
        $invoiceAmount = $invoiceAmount->sum('total_amount');

        $paymentTotals = VendorPaymentAllocation::whereHas('invoice', fn($q) => $q->where('company_id',$companyId)->when($fromDate,fn($x)=>$x->whereDate('created_at','>=',$fromDate))->when($toDate,fn($x)=>$x->whereDate('created_at','<=',$toDate)));
        $paidAmount = $paymentTotals->sum('gross_amount');
        $unpaidAmount = SupplierInvoice::where('company_id', $companyId)
            ->whereIn('status', ['POSTED','PARTIALLY_PAID'])
            ->when($fromDate, fn($q)=>$q->whereDate('created_at','>=',$fromDate))
            ->when($toDate, fn($q)=>$q->whereDate('created_at','<=',$toDate))
            ->get()->sum(fn($invoice) => max(0,(float)$invoice->total_amount-(float)VendorPaymentAllocation::where('supplier_invoice_id',$invoice->id)->sum('gross_amount')));

        // Recent invoices
        $recentInvoices = SupplierInvoice::where('company_id', $companyId)
            ->with('purchaseOrder.vendor')
            ->when($fromDate, function ($query) use ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            })
            ->latest()
            ->take(10)
            ->get();

        $budgetRows = Budget::with('costCentre')->where('company_id', $companyId)->orderBy('financial_year')->get();
        $openPurchaseOrders = PurchaseOrder::with(['vendor','items.item','goodsReceipts.items'])
            ->where('company_id',$companyId)->whereIn('status',['APPROVED','PARTIALLY_RECEIVED'])->latest()->get();
        $onHoldInvoices = SupplierInvoice::with(['vendor','items.item'])->where('company_id',$companyId)->where('status','ON_HOLD')->latest()->get();

        $vendorAgeing = [];
        $ledgerRows = [];
        foreach (Vendor::where('company_id',$companyId)->orderBy('name')->get() as $vendor) {
            $buckets = ['0-30'=>0,'31-60'=>0,'61-90'=>0,'90+'=>0];
            $invoices = SupplierInvoice::with('vendor')->where('company_id',$companyId)->where('vendor_id',$vendor->id)->whereIn('status',['POSTED','PARTIALLY_PAID','PAID'])->get();
            $entries = [];
            foreach ($invoices as $invoice) {
                $entries[] = ['date'=>$invoice->invoice_date,'document'=>$invoice->invoice_number,'debit'=>0,'credit'=>(float)$invoice->total_amount];
                $paid = (float) VendorPaymentAllocation::where('supplier_invoice_id',$invoice->id)->sum('gross_amount');
                $outstanding = max(0,(float)$invoice->total_amount-$paid);
                if ($outstanding > 0) {
                    $due = $invoice->due_date ?? Carbon::parse($invoice->invoice_date)->addDays($vendor->payment_terms_days);
                    $days = max(0, Carbon::today()->diffInDays(Carbon::parse($due), false) * -1);
                    $bucket = $days <= 30 ? '0-30' : ($days <= 60 ? '31-60' : ($days <= 90 ? '61-90' : '90+'));
                    $buckets[$bucket] += $outstanding;
                }
                foreach (VendorPaymentAllocation::with('payment')->where('supplier_invoice_id',$invoice->id)->get() as $allocation) {
                    $entries[] = ['date'=>$allocation->payment->payment_date,'document'=>$allocation->payment->payment_number,'debit'=>(float)$allocation->gross_amount,'credit'=>0];
                }
            }
            $balance = 0;
            usort($entries, fn($a,$b) => $a['date'] <=> $b['date']);
            foreach ($entries as $entry) { $balance += $entry['credit'] - $entry['debit']; $ledgerRows[] = ['vendor'=>$vendor->name] + $entry + ['balance'=>$balance]; }
            $vendorAgeing[] = ['vendor'=>$vendor->name] + $buckets + ['total'=>array_sum($buckets)];
        }

        return view('reports.index', compact(
            'fromDate',
            'toDate',
            'totalPR',
            'pendingPR',
            'approvedPR',
            'rejectedPR',
            'totalPO',
            'sentPO',
            'totalGR',
            'completedGR',
            'totalInvoices',
            'invoiceAmount',
            'paidAmount',
            'unpaidAmount',
            'recentInvoices'
            , 'budgetRows','openPurchaseOrders','onHoldInvoices','vendorAgeing','ledgerRows'
        ));
    }
}
