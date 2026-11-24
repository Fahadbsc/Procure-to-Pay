<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\UnitOfMeasure;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['I001', 'MS Steel Sheet 2mm', 'RAW_MATERIAL', 'KG', 18],
            ['I002', 'Laptop i5 16GB', 'IT', 'PCS', 18],
            ['I003', 'A4 Paper Ream', 'CONSUMABLES', 'PCS', 12],
            ['I004', 'Housekeeping Service', 'SERVICES', 'HRS', 18],
            ['I005', 'Corrugated Box', 'CONSUMABLES', 'PCS', 12],
            ['I006', 'Rice (canteen)', 'CONSUMABLES', 'KG', 0],
        ];

        foreach ($items as $item) {

            $unit = UnitOfMeasure::where(
                'code', $item[3]
            )->firstOrFail();

            Item::updateOrCreate(
                ['code' => $item[0]],
                [
                    'name' => $item[1],
                    'category' => $item[2],
                    'base_uom_id' => $unit->id,
                    'gst_rate' => $item[4],
                ]
            );
        }
    }
}
