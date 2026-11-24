<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('baseUom')
            ->latest()
            ->paginate(10);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $units = UnitOfMeasure::orderBy('code')->get();

        return view('items.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:items,code',
            ],
            'name' => ['required', 'string', 'max:255'],
            'category' => [
                'required',
                Rule::in([
                    'RAW_MATERIAL',
                    'IT',
                    'SERVICES',
                    'CONSUMABLES',
                ]),
            ],
            'base_uom_id' => [
                'required',
                'exists:unit_of_measures,id',
            ],
            'gst_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        Item::create($data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $units = UnitOfMeasure::orderBy('code')->get();

        return view('items.edit', compact('item', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')->ignore($item->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category' => [
                'required',
                Rule::in([
                    'RAW_MATERIAL',
                    'IT',
                    'SERVICES',
                    'CONSUMABLES',
                ]),
            ],
            'base_uom_id' => [
                'required',
                'exists:unit_of_measures,id',
            ],
            'gst_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $item->update($data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }
}
