<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'base_uom_id',
        'gst_rate',
    ];

    protected $casts = [
        'gst_rate' => 'decimal:2',
    ];

    public function baseUom()
    {
        return $this->belongsTo(
            UnitOfMeasure::class,
            'base_uom_id'
        );
    }
}
