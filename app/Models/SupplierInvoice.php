<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'purchase_order_id',
        'goods_receipt_id',
        'invoice_number',
        'supplier_invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'remarks',
        'submitted_by',
        'reviewed_by',
        'paid_by',
        'reviewed_at',
        'paid_at',
        'rejection_reason',
        'override_comment',
'overridden_by',
'overridden_at',
        'cgst_total', 'sgst_total', 'igst_total',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'submitted_by' => 'integer',
        'reviewed_by' => 'integer',
        'paid_by' => 'integer',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'overridden_at' => 'datetime',
        'vendor_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor() { return $this->belongsTo(Vendor::class); }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
