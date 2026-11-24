<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Display vendors belonging to the logged-in user's company.
     */
    public function index()
    {
        $vendors = Vendor::where(
            'company_id',
            auth()->user()->company_id
        )
        ->latest()
        ->paginate(10);

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show vendor creation form.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a new vendor.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vendors', 'code')
                    ->where('company_id', $companyId),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'gstin' => [
                'nullable',
                'string',
                'size:15',
            ],

            'state' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_terms_days' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $data['company_id'] = $companyId;

        // Checkbox handling
        $data['is_active'] = $request->boolean('is_active');

        Vendor::create($data);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Show vendor edit form.
     */
    public function edit(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update existing vendor.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vendors', 'code')
                    ->where('company_id', $companyId)
                    ->ignore($vendor->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'gstin' => [
                'nullable',
                'string',
                'size:15',
            ],

            'state' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_terms_days' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        // Checkbox unchecked = false
        $data['is_active'] = $request->boolean('is_active');

        $vendor->update($data);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Delete vendor.
     */
    public function destroy(Vendor $vendor)
    {
        $this->authorizeVendor($vendor);

        $vendor->delete();

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Verify vendor belongs to logged-in user's company.
     */
    private function authorizeVendor(Vendor $vendor)
    {
        abort_unless(
            (int) $vendor->company_id === (int) auth()->user()->company_id,
            404
        );
    }
}
