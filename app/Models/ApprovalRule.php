<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    protected $fillable = [
        'company_id',
        'document_type',
        'category',
        'cost_centre_id',
        'min_amount',
        'max_amount',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    public function steps()
    {
        return $this->hasMany(ApprovalRuleStep::class)
            ->orderBy('step_order');
    }
}
