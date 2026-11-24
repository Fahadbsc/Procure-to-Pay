<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitOfMeasure;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['PCS', 'PCS', 1],
            ['BOX', 'PCS', 12],
            ['KG', 'KG', 1],
            ['TON', 'KG', 1000],
            ['HRS', 'HRS', 1],
        ];

        foreach ($units as $unit) {
            UnitOfMeasure::updateOrCreate(
                ['code' => $unit[0]],
                [
                    'base_unit' => $unit[1],
                    'conversion_factor' => $unit[2],
                ]
            );
        }
    }
}
