<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    protected $fillable = [
        'code',
        'base_unit',
        'conversion_factor',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'base_uom_id');
    }
}
