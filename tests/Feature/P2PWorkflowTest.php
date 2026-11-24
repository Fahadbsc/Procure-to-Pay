<?php

namespace Tests\Feature;

use App\Models\{ApprovalRequest, Budget, Company, CostCentre, GoodsReceipt, GoodsReceiptItem, Item, PurchaseOrder, PurchaseOrderItem, PurchaseRequisition, PurchaseRequisitionItem, SupplierInvoice, SupplierInvoiceItem, UnitOfMeasure, User, Vendor};
use App\Services\ApprovalWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P2PWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_seeded_admin_can_open_company_dashboard(): void
    {
        $this->actingAs(User::where('email','admin.mum@acme.test')->firstOrFail())
            ->get('/dashboard')->assertOk();
    }

    public function test_requester_can_open_requisitions(): void
    {
        $this->actingAs(User::where('email','req.mum@acme.test')->firstOrFail())
            ->get(route('purchase-requisitions.index'))->assertOk();
    }

    public function test_requester_cannot_manage_company_users(): void
    {
        $this->actingAs(User::where('email','req.mum@acme.test')->firstOrFail())
            ->get(route('users.index'))->assertForbidden();
    }

    public function test_bengaluru_procurement_cannot_open_mumbai_po(): void
    {
        $po = $this->makePo('ACME-MUM','PROD',1,100,0,'PCS','APPROVED');
        $this->actingAs(User::where('email','proc.blr@acme.test')->firstOrFail())
            ->get(route('purchase-orders.show',$po))->assertForbidden();
    }

    public function test_admin_can_create_a_pr_line_in_a_compatible_unit(): void
    {
        $user = User::where('email','req.mum@acme.test')->firstOrFail();
        $company = Company::where('code','ACME-MUM')->firstOrFail();
        $centre = CostCentre::where('company_id',$company->id)->where('code','PROD')->firstOrFail();
        $item = Item::where('code','I005')->firstOrFail();
        $box = UnitOfMeasure::where('code','BOX')->firstOrFail();
        $this->actingAs($user)->post(route('purchase-requisitions.store'),[
            'cost_centre_id'=>$centre->id,'required_date'=>now()->addDay()->toDateString(),'purpose'=>'Unit conversion test',
            'items'=>[['item_id'=>$item->id,'uom_id'=>$box->id,'quantity'=>10,'estimated_price'=>25]],
        ])->assertRedirect();
        $this->assertDatabaseHas('purchase_requisition_items',['item_id'=>$item->id,'uom_id'=>$box->id,'quantity'=>10]);
    }

    public function test_self_approver_is_replaced_by_same_company_peer(): void
    {
        $company=Company::where('code','ACME-MUM')->firstOrFail();
        $requester=User::where('email','depthead.mum@acme.test')->firstOrFail();
        $approval=app(ApprovalWorkflowService::class)->startApproval('PR',910001,$company->id,$requester->id,1000);
        $this->assertSame('depthead.backup.mum@acme.test',$approval->steps->first()->approver->email);
    }

    public function test_budget_lock_allows_only_one_of_two_overlapping_po_approvals(): void
    {
        $finance=User::where('email','finhead.mum@acme.test')->firstOrFail();
        $first=$this->makePo('ACME-MUM','SALES',1,60000,0,'PCS','SENT');
        $second=$this->makePo('ACME-MUM','SALES',1,60000,0,'PCS','SENT');
        $this->approvalForPo($first,$finance);
        $this->approvalForPo($second,$finance);
        $this->actingAs($finance)->post(route('my-approvals.approve',ApprovalRequest::where('document_type','PO')->where('document_id',$first->id)->firstOrFail()),['comments'=>'Within budget']);
        $this->actingAs($finance)->post(route('my-approvals.approve',ApprovalRequest::where('document_type','PO')->where('document_id',$second->id)->firstOrFail()),['comments'=>'Concurrent request']);
        $budget=Budget::where('company_id',$first->company_id)->where('cost_centre_id',$first->purchaseRequisition->cost_centre_id)->where('financial_year','2026-27')->firstOrFail();
        $this->assertSame('60000.00',$budget->committed_amount);
        $this->assertSame('SENT',$second->fresh()->status);
    }

    public function test_grn_converts_box_order_to_pieces_and_enforces_five_percent_acceptance(): void
    {
        $store=User::where('email','store.mum@acme.test')->firstOrFail();
        $po=$this->makePo('ACME-MUM','PROD',10,25,12,'BOX','APPROVED','I005');
        $line=$po->items->first(); $pcs=UnitOfMeasure::where('code','PCS')->firstOrFail();
        $this->recordGrn($store,$po,$line,$pcs,60,58,2)->assertRedirect();
        $this->assertEqualsWithDelta(58/12,(float)GoodsReceiptItem::where('purchase_order_item_id',$line->id)->firstOrFail()->accepted_quantity,0.0001);
        $this->recordGrn($store,$po,$line,$pcs,66,66,0)->assertRedirect();
        $this->assertSame('RECEIVED',$po->fresh()->status);
        $this->recordGrn($store,$po,$line,$pcs,3,3,0)->assertSessionHasErrors('items');
    }

    public function test_three_way_match_passes_when_quantity_price_and_tax_match(): void
    {
        [$invoice]=$this->invoiceCase(10,25,12);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('supplier-invoices.match',$invoice))->assertRedirect();
        $this->assertSame('MATCHED',$invoice->fresh()->status);
    }

    public function test_three_way_match_holds_when_invoice_quantity_exceeds_accepted_grn(): void
    {
        [$invoice]=$this->invoiceCase(11,25,12,10);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('supplier-invoices.match',$invoice))->assertRedirect();
        $this->assertSame('ON_HOLD',$invoice->fresh()->status);
        $this->assertStringContainsString('quantity',$invoice->items->first()->fresh()->match_reason);
    }

    public function test_three_way_match_holds_when_invoice_price_exceeds_two_percent(): void
    {
        [$invoice]=$this->invoiceCase(10,25.80,12);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('supplier-invoices.match',$invoice))->assertRedirect();
        $this->assertSame('ON_HOLD',$invoice->fresh()->status);
        $this->assertStringContainsString('price',$invoice->items->first()->fresh()->match_reason);
    }

    public function test_three_way_match_holds_when_gst_rate_differs_from_po(): void
    {
        [$invoice]=$this->invoiceCase(10,25,18);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('supplier-invoices.match',$invoice))->assertRedirect();
        $this->assertSame('ON_HOLD',$invoice->fresh()->status);
        $this->assertStringContainsString('GST rate',$invoice->items->first()->fresh()->match_reason);
    }

    public function test_accounts_can_open_payment_entry_for_posted_invoices(): void
    {
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->get(route('payments.create'))->assertOk();
    }

    public function test_services_payment_deducts_two_percent_tds_before_transfer(): void
    {
        [$invoice]=$this->invoiceCase(100,300,18,100,'I004','HRS');
        $invoice->update(['status'=>'POSTED']);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('payments.store'),['payment_date'=>'2026-10-12','allocations'=>[['invoice_id'=>$invoice->id,'amount'=>35400]]])
            ->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('vendor_payments',['gross_amount'=>'35400.00','tds_amount'=>'600.00','net_amount'=>'34800.00']);
        $this->assertSame('PAID',$invoice->fresh()->status);
    }

    public function test_partial_invoice_payment_preserves_outstanding_balance(): void
    {
        [$invoice]=$this->invoiceCase(100,300,18,100,'I004','HRS');
        $invoice->update(['status'=>'POSTED']);
        $this->actingAs(User::where('email','accounts.mum@acme.test')->firstOrFail())
            ->post(route('payments.store'),['payment_date'=>'2026-10-12','allocations'=>[['invoice_id'=>$invoice->id,'amount'=>10000]]])
            ->assertRedirect(route('payments.index'));
        $this->assertSame('PARTIALLY_PAID',$invoice->fresh()->status);
        $this->assertDatabaseHas('vendor_payments',['gross_amount'=>'10000.00','tds_amount'=>'169.49','net_amount'=>'9830.51']);
    }

    private function makePo(string $companyCode,string $centreCode,float $quantity,float $price,float $gst,string $uomCode,string $status,string $itemCode='I006'): PurchaseOrder
    {
        $company=Company::where('code',$companyCode)->firstOrFail();
        $requester=User::where('company_id',$company->id)->whereHas('roles',fn($q)=>$q->where('name','Requester'))->firstOrFail();
        $centre=CostCentre::where('company_id',$company->id)->where('code',$centreCode)->firstOrFail();
        $item=Item::where('code',$itemCode)->firstOrFail(); $uom=UnitOfMeasure::where('code',$uomCode)->firstOrFail();
        $vendor=Vendor::where('company_id',$company->id)->firstOrFail();
        $pr=PurchaseRequisition::create(['company_id'=>$company->id,'cost_centre_id'=>$centre->id,'pr_number'=>'T-PR-'.uniqid(),'requested_by'=>$requester->id,'required_date'=>now()->addDay(),'status'=>'APPROVED']);
        $pri=PurchaseRequisitionItem::create(['purchase_requisition_id'=>$pr->id,'item_id'=>$item->id,'uom_id'=>$uom->id,'quantity'=>$quantity,'estimated_price'=>$price]);
        $po=PurchaseOrder::create(['company_id'=>$company->id,'purchase_requisition_id'=>$pr->id,'vendor_id'=>$vendor->id,'po_number'=>'T-PO-'.uniqid(),'order_date'=>'2026-10-10','status'=>$status,'budget_committed_amount'=>0]);
        $taxable=$quantity*$price; $intra=$company->state===$vendor->state;
        $tax=$intra?round($taxable*$gst/200,2,PHP_ROUND_HALF_UP):0;
        $po->items()->create(['purchase_requisition_item_id'=>$pri->id,'item_id'=>$item->id,'uom_id'=>$uom->id,'quantity'=>$quantity,'unit_price'=>$price,'gst_rate'=>$gst,'cgst_amount'=>$tax,'sgst_amount'=>$tax,'igst_amount'=>$intra?0:round($taxable*$gst/100,2,PHP_ROUND_HALF_UP)]);
        return $po->load(['items','purchaseRequisition']);
    }

    private function approvalForPo(PurchaseOrder $po,User $approver): void
    {
        $request=ApprovalRequest::create(['company_id'=>$po->company_id,'document_type'=>'PO','document_id'=>$po->id,'requested_by'=>User::where('email','proc.mum@acme.test')->value('id'),'current_step'=>1,'status'=>'PENDING']);
        $request->steps()->create(['step_order'=>1,'approver_user_id'=>$approver->id,'status'=>'PENDING']);
    }

    private function recordGrn(User $store,PurchaseOrder $po,PurchaseOrderItem $line,UnitOfMeasure $uom,float $received,float $accepted,float $rejected)
    {
        return $this->actingAs($store)->post(route('goods-receipts.store'),['purchase_order_id'=>$po->id,'received_date'=>'2026-10-11','items'=>[['purchase_order_item_id'=>$line->id,'received_uom_id'=>$uom->id,'received_quantity'=>$received,'accepted_quantity'=>$accepted,'rejected_quantity'=>$rejected]]]);
    }

    private function invoiceCase(float $billedQuantity,float $price,float $gst,float $accepted=10,string $itemCode='I003',string $uomCode='PCS'): array
    {
        $orderQty=$itemCode==='I004'?100:10;
        $po=$this->makePo('ACME-MUM','PROD',$orderQty,($itemCode==='I004'?300:25),($itemCode==='I004'?18:12),$uomCode,'RECEIVED',$itemCode); $line=$po->items->first();
        $store=User::where('email','store.mum@acme.test')->firstOrFail();
        $gr=GoodsReceipt::create(['company_id'=>$po->company_id,'purchase_order_id'=>$po->id,'gr_number'=>'T-GR-'.uniqid(),'received_date'=>'2026-10-11','received_by'=>$store->id,'status'=>'COMPLETED']);
        $grItem=GoodsReceiptItem::create(['goods_receipt_id'=>$gr->id,'purchase_order_item_id'=>$line->id,'received_uom_id'=>$line->uom_id,'received_quantity'=>$accepted,'accepted_quantity'=>$accepted,'rejected_quantity'=>0]);
        $vendor=Vendor::findOrFail($po->vendor_id); $intra=$vendor->state===$po->company->state; $taxable=$billedQuantity*$price;
        $cgst=$intra?round($taxable*$gst/200,2,PHP_ROUND_HALF_UP):0; $sgst=$cgst; $igst=$intra?0:round($taxable*$gst/100,2,PHP_ROUND_HALF_UP); $tax=$cgst+$sgst+$igst;
        $invoice=SupplierInvoice::create(['company_id'=>$po->company_id,'vendor_id'=>$vendor->id,'purchase_order_id'=>$po->id,'goods_receipt_id'=>$gr->id,'invoice_number'=>'T-INV-'.uniqid(),'supplier_invoice_number'=>'SUP-'.uniqid(),'invoice_date'=>'2026-10-11','due_date'=>'2026-11-10','subtotal'=>$taxable,'tax_amount'=>$tax,'cgst_total'=>$cgst,'sgst_total'=>$sgst,'igst_total'=>$igst,'total_amount'=>$taxable+$tax,'status'=>'SUBMITTED']);
        $invoiceLine=SupplierInvoiceItem::create(['supplier_invoice_id'=>$invoice->id,'goods_receipt_item_id'=>$grItem->id,'item_id'=>$line->item_id,'uom_id'=>$line->uom_id,'quantity'=>$billedQuantity,'unit_price'=>$price,'gst_rate'=>$gst,'tax_amount'=>$tax,'cgst_amount'=>$cgst,'sgst_amount'=>$sgst,'igst_amount'=>$igst,'line_total'=>$taxable+$tax]);
        return [$invoice,$invoiceLine];
    }
}
