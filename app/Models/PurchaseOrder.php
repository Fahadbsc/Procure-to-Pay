<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'company_id',
        'purchase_requisition_id',
        'vendor_id',
        'po_number',
        'order_date',
        'expected_delivery_date',
        'status',
        'terms_conditions',
        'budget_committed_amount',
        'rejection_reason',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'budget_committed_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(
            PurchaseRequisition::class,
            'purchase_requisition_id'
        );
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    public function goodsReceipts()
{
    return $this->hasMany(GoodsReceipt::class);
}

public function supplierInvoices()
{
    return $this->hasMany(SupplierInvoice::class);
}
}
