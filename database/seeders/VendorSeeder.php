<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Company;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['V001', 'Shree Steel Traders', 'Maharashtra',
                '27AABCS1111A1Z1', 30, 'ACME-MUM'],

            ['V002', 'Bangalore Tech Supplies', 'Karnataka',
                '29AABCB2222B1Z2', 45, 'ACME-MUM'],

            ['V003', 'QuickClean Services', 'Maharashtra',
                '27AABCQ3333C1Z3', 15, 'ACME-MUM'],

            ['V004', 'Deccan Packaging', 'Karnataka',
                '29AABCD4444D1Z4', 30, 'ACME-BLR'],
        ];

        foreach ($vendors as $v) {

            $company = Company::where(
                'code', $v[5]
            )->firstOrFail();

            Vendor::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $v[0],
                ],
                [
                    'name' => $v[1],
                    'state' => $v[2],
                    'gstin' => $v[3],
                    'payment_terms_days' => $v[4],
                    'is_active' => true,
                ]
            );
        }
    }
}
