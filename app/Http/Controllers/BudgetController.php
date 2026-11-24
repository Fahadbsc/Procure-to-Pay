<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $budgets = Budget::with('costCentre')
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        $costCentres = CostCentre::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('budgets.index', compact(
            'budgets',
            'costCentres'
        ));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $data = $request->validate([
            'cost_centre_id' => [
                'required',
                Rule::exists('cost_centres', 'id')
                    ->where(function ($query) use ($companyId) {
                        $query->where('company_id', $companyId)
                              ->where('is_active', true);
                    }),
            ],

            'financial_year' => [
                'required',
                'regex:/^\d{4}-\d{2}$/',
            ],

            'budget_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
        ]);

        Budget::updateOrCreate(
            [
                'company_id' => $companyId,
                'cost_centre_id' => $data['cost_centre_id'],
                'financial_year' => $data['financial_year'],
            ],
            [
                'budget_amount' => $data['budget_amount'],
            ]
        );

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget saved successfully.');
    }
}
