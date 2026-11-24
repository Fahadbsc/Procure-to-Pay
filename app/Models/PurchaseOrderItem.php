<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'purchase_requisition_item_id',
        'item_id',
        'uom_id',
        'cost_centre_id', 'budget_committed_amount',
        'quantity',
        'unit_price',
        'gst_rate',
        'cgst_amount', 'sgst_amount', 'igst_amount',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'gst_rate' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseRequisitionItem()
    {
        return $this->belongsTo(
            PurchaseRequisitionItem::class,
            'purchase_requisition_item_id'
        );
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function uom() { return $this->belongsTo(UnitOfMeasure::class, 'uom_id'); }
    public function costCentre() { return $this->belongsTo(CostCentre::class); }
}
