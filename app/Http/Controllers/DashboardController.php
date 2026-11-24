<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\SupplierInvoice;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Purchase Requisitions
        $totalPR = PurchaseRequisition::where('company_id', $companyId)
            ->count();

        $pendingPR = PurchaseRequisition::where('company_id', $companyId)
            ->where('status', 'PENDING')
            ->count();

        // Purchase Orders
        $totalPO = PurchaseOrder::where('company_id', $companyId)
            ->count();

        $sentPO = PurchaseOrder::where('company_id', $companyId)
            ->where('status', 'SENT')
            ->count();

        // Goods Receipts
        $totalGR = GoodsReceipt::where('company_id', $companyId)
            ->count();

        $completedGR = GoodsReceipt::where('company_id', $companyId)
            ->where('status', 'COMPLETED')
            ->count();

        // Supplier Invoices
        $totalInvoices = SupplierInvoice::where('company_id', $companyId)
            ->count();

        $pendingInvoices = SupplierInvoice::where('company_id', $companyId)
            ->where('status', 'SUBMITTED')
            ->count();

        $approvedInvoices = SupplierInvoice::where('company_id', $companyId)
            ->whereIn('status', ['POSTED','PARTIALLY_PAID'])
            ->count();

        $paidInvoices = SupplierInvoice::where('company_id', $companyId)
            ->where('status', 'PAID')
            ->count();

        // Invoice Amounts
        $totalInvoiceAmount = SupplierInvoice::where('company_id', $companyId)
            ->whereIn('status', ['POSTED', 'PARTIALLY_PAID', 'PAID'])
            ->sum('total_amount');

        $paidAmount = \App\Models\VendorPaymentAllocation::whereHas('invoice', fn($q) => $q->where('company_id',$companyId))->sum('gross_amount');

        $unpaidAmount = SupplierInvoice::where('company_id', $companyId)
            ->whereIn('status', ['POSTED','PARTIALLY_PAID'])
            ->get()->sum(fn($invoice) => max(0,(float)$invoice->total_amount-(float)\App\Models\VendorPaymentAllocation::where('supplier_invoice_id',$invoice->id)->sum('gross_amount')));

        // Recent Invoices
        $recentInvoices = SupplierInvoice::where('company_id', $companyId)
            ->with('purchaseOrder.vendor')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalPR',
            'pendingPR',
            'totalPO',
            'sentPO',
            'totalGR',
            'completedGR',
            'totalInvoices',
            'pendingInvoices',
            'approvedInvoices',
            'paidInvoices',
            'totalInvoiceAmount',
            'paidAmount',
            'unpaidAmount',
            'recentInvoices'
        ));
    }
}
