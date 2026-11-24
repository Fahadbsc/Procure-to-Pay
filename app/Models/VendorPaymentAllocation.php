<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VendorPaymentAllocation extends Model
{
    protected $fillable = ['vendor_payment_id','supplier_invoice_id','gross_amount','tds_amount'];
    protected $casts = ['gross_amount'=>'decimal:2','tds_amount'=>'decimal:2'];
    public function payment() { return $this->belongsTo(VendorPayment::class, 'vendor_payment_id'); }
    public function invoice() { return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id'); }
}
