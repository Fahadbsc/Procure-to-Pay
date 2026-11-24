<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'item_id',
        'uom_id',
        'quantity',
        'estimated_price',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'estimated_price' => 'decimal:2',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(
            PurchaseRequisition::class,
            'purchase_requisition_id'
        );
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function uom() { return $this->belongsTo(UnitOfMeasure::class, 'uom_id'); }
}
