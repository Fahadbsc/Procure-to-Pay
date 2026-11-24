<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'company_id',
        'cost_centre_id',
        'financial_year',
        'budget_amount',
        'committed_amount',
        'spent_amount',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'committed_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    // Available = Budget - Committed - Spent
    public function getAvailableAmountAttribute()
    {
        return bcsub(
            bcsub(
                (string) $this->budget_amount,
                (string) $this->committed_amount,
                2
            ),
            (string) $this->spent_amount,
            2
        );
    }
}
