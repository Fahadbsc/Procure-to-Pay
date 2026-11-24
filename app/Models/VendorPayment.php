<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VendorPayment extends Model
{
    protected $fillable = ['company_id','vendor_id','paid_by','payment_number','payment_date','gross_amount','tds_amount','net_amount','reference','remarks'];
    protected $casts = ['payment_date'=>'date','gross_amount'=>'decimal:2','tds_amount'=>'decimal:2','net_amount'=>'decimal:2'];
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function allocations() { return $this->hasMany(VendorPaymentAllocation::class); }
}
