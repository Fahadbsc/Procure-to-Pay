<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'company_id',
        'pr_number',
        'requested_by',
        'required_date',
        'purpose',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'cost_centre_id',
    ];

    protected $casts = [
        'required_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(
            PurchaseRequisitionItem::class,
            'purchase_requisition_id'
        );
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(
            PurchaseOrder::class,
            'purchase_requisition_id'
        );
    }
    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }
}
