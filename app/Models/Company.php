<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'code',
        'name',
        'state',
        'gstin',
        'is_active'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function vendors()
{
    return $this->hasMany(Vendor::class);
}

public function costCentres()
{
    return $this->hasMany(CostCentre::class);
}

}