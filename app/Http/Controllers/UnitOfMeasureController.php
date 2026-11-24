<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitOfMeasureController extends Controller
{
    public function index()
    {
        $units = UnitOfMeasure::orderBy('code')->paginate(10);

        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'unique:unit_of_measures,code'],
            'base_unit' => ['required', 'string'],
            'conversion_factor' => ['required', 'numeric', 'gt:0'],
        ]);

        UnitOfMeasure::create($data);

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(UnitOfMeasure $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, UnitOfMeasure $unit)
    {
        $data = $request->validate([
            'code' => [
                'required',
                Rule::unique('unit_of_measures', 'code')->ignore($unit->id)
            ],
            'base_unit' => ['required', 'string'],
            'conversion_factor' => ['required', 'numeric', 'gt:0'],
        ]);

        $unit->update($data);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(UnitOfMeasure $unit)
    {
        abort_if($unit->items()->exists(), 422,
            'This unit is assigned to an item.');

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
