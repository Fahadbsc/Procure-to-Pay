<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostCentreController extends Controller
{
    public function index()
    {
        $centres = CostCentre::where(
            'company_id',
            auth()->user()->company_id
        )->latest()->paginate(10);

        return view('cost_centres.index', compact('centres'));
    }

    public function create()
    {
        return view('cost_centres.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => [
                'required',
                Rule::unique('cost_centres', 'code')
                    ->where('company_id', auth()->user()->company_id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['company_id'] = auth()->user()->company_id;
        $data['is_active'] = $request->boolean('is_active');

        CostCentre::create($data);

        return redirect()->route('cost-centres.index')
            ->with('success', 'Cost centre created successfully.');
    }

    public function edit(CostCentre $cost_centre)
    {
        $this->authorizeCentre($cost_centre);

        return view('cost_centres.edit', compact('cost_centre'));
    }

    public function update(Request $request, CostCentre $cost_centre)
    {
        $this->authorizeCentre($cost_centre);

        $data = $request->validate([
            'code' => [
                'required',
                Rule::unique('cost_centres', 'code')
                    ->where('company_id', auth()->user()->company_id)
                    ->ignore($cost_centre->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $cost_centre->update($data);

        return redirect()->route('cost-centres.index')
            ->with('success', 'Cost centre updated successfully.');
    }

    public function destroy(CostCentre $cost_centre)
    {
        $this->authorizeCentre($cost_centre);

        $cost_centre->delete();

        return redirect()->route('cost-centres.index')
            ->with('success', 'Cost centre deleted successfully.');
    }

    private function authorizeCentre(CostCentre $centre)
    {
        abort_unless(
            (int) $centre->company_id === (int) auth()->user()->company_id,
            404
        );
    }
}
