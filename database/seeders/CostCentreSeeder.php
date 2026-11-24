<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CostCentre;

class CostCentreSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ACME-MUM', 'PROD', 'Production'],
            ['ACME-MUM', 'IT', 'Information Technology'],
            ['ACME-MUM', 'ADMIN', 'Administration'],
            ['ACME-MUM', 'SALES', 'Sales'],
            ['ACME-BLR', 'PROD', 'Production'],
            ['ACME-BLR', 'IT', 'Information Technology'],
        ];

        foreach ($data as $row) {

            $company = Company::where(
                'code', $row[0]
            )->firstOrFail();

            CostCentre::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $row[1],
                ],
                [
                    'name' => $row[2],
                    'is_active' => true,
                ]
            );
        }
    }
}
