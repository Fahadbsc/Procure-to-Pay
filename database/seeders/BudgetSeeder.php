<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Company;
use App\Models\CostCentre;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $budgets = [
            ['ACME-MUM', 'PROD', 5000000], ['ACME-MUM', 'IT', 1000000],
            ['ACME-MUM', 'ADMIN', 200000], ['ACME-MUM', 'SALES', 100000],
            ['ACME-BLR', 'PROD', 3000000], ['ACME-BLR', 'IT', 500000],
        ];

        foreach ($budgets as [$companyCode, $centreCode, $amount]) {
            $company = Company::where('code', $companyCode)->firstOrFail();
            $centre = CostCentre::where('company_id', $company->id)->where('code', $centreCode)->firstOrFail();
            Budget::updateOrCreate(
                ['company_id' => $company->id, 'cost_centre_id' => $centre->id, 'financial_year' => '2026-27'],
                ['budget_amount' => $amount]
            );
        }
    }
}
