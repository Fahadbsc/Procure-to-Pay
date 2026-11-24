<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'received_uom_id',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'remarks',
    ];

    protected $casts = [
        'received_quantity' => 'decimal:4',
        'accepted_quantity' => 'decimal:4',
        'rejected_quantity' => 'decimal:4',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(
            PurchaseOrderItem::class,
            'purchase_order_item_id'
        );
    }

    public function supplierInvoiceItems()
{
    return $this->hasMany(SupplierInvoiceItem::class);
}

    public function receivedUom() { return $this->belongsTo(UnitOfMeasure::class, 'received_uom_id'); }
}
