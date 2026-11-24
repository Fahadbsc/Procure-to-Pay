<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'gstin',
        'state',
        'payment_terms_days',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrders()
{
    return $this->hasMany(PurchaseOrder::class);
}
}
