<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'gr_number',
        'received_date',
        'received_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
    public function supplierInvoices()
{
    return $this->hasMany(SupplierInvoice::class);
}
}
