<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\CostCentre;
use App\Models\UnitOfMeasure;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Services\ApprovalWorkflowService;
use App\Support\DecimalMoney;


class PurchaseRequisitionController extends Controller
{
    public function index()
    {
        $requisitions = PurchaseRequisition::with([
            'requester',
            'costCentre',
            'items.item'
        ])
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(10);

        return view('purchase_requisitions.index', compact('requisitions'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;

        $items = Item::orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('code')->get();

        $costCentres = CostCentre::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'purchase_requisitions.create',
            compact('items', 'costCentres', 'units')
        );
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
        'required_date' => ['required', 'date', 'after_or_equal:today'],
        'purpose' => ['nullable', 'string', 'max:1000'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.item_id' => ['required', 'exists:items,id'],
        'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        'items.*.uom_id' => ['required', 'integer', 'exists:unit_of_measures,id'],
        'items.*.estimated_price' => ['required', 'numeric', 'min:0'],
        'items.*.remarks' => ['nullable', 'string', 'max:500'],
    ]);

    $itemIds = collect($data['items'])->pluck('item_id')->unique();

    if ($itemIds->count() !== count($data['items'])) {
        throw ValidationException::withMessages([
            'items' => 'Please select each item only once.',
        ]);
    }

    foreach ($data['items'] as $row) {
        $item = Item::with('baseUom')->findOrFail($row['item_id']);
        $uom = UnitOfMeasure::findOrFail($row['uom_id']);
        if ($uom->base_unit !== $item->baseUom->base_unit) {
            throw ValidationException::withMessages(['items' => 'Selected unit is incompatible with the item base unit.']);
        }
    }

    $requisition = DB::transaction(function () use ($data, $companyId) {

        $prNumber = 'PR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

        $requisition = PurchaseRequisition::create([
            'company_id' => $companyId,
            'cost_centre_id' => $data['cost_centre_id'],
            'pr_number' => $prNumber,
            'requested_by' => Auth::id(),
            'required_date' => $data['required_date'],
            'purpose' => $data['purpose'] ?? null,
            'status' => 'DRAFT',
        ]);

        foreach ($data['items'] as $row) {
            $requisition->items()->create([
                'item_id' => $row['item_id'],
                'uom_id' => $row['uom_id'],
                'quantity' => $row['quantity'],
                'estimated_price' => $row['estimated_price'],
                'remarks' => $row['remarks'] ?? null,
            ]);
        }

        return $requisition;
    });

    return redirect()
        ->route('purchase-requisitions.show', $requisition->id)
        ->with('success', 'Purchase Requisition saved as draft.');
}

public function submit(
    PurchaseRequisition $purchaseRequisition,
    ApprovalWorkflowService $workflow
) {
    $this->authorizeCompany($purchaseRequisition);

    if ($purchaseRequisition->status !== 'DRAFT') {
        return back()->with('error', 'Only draft PRs can be submitted.');
    }

    if ((int) $purchaseRequisition->requested_by !== (int) Auth::id()) {
        abort(403, 'Only the requester can submit this PR.');
    }

    DB::transaction(function () use ($purchaseRequisition, $workflow) {

        $pr = PurchaseRequisition::with('items')
            ->whereKey($purchaseRequisition->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($pr->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'approval' => 'This PR has already been submitted.',
            ]);
        }

        $amount = $pr->items->reduce(fn($sum,$item)=>DecimalMoney::add($sum,DecimalMoney::multiply($item->quantity,$item->estimated_price)), '0.00');
        $category = $pr->items()->whereHas('item', fn($q) => $q->where('category', 'IT'))->exists() ? 'IT' : null;

        $workflow->startApproval(
            'PR',
            $pr->id,
            $pr->company_id,
            $pr->requested_by,
            $amount,
            $pr->cost_centre_id,
            $category
        );

        $pr->update([
            'status' => 'SUBMITTED',
        ]);
    });

    return redirect()
        ->route('purchase-requisitions.show', $purchaseRequisition->id)
        ->with('success', 'Purchase Requisition submitted for approval.');
}


    public function show(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorizeCompany($purchaseRequisition);

        $purchaseRequisition->load([
            'requester',
            'reviewer',
            'costCentre',
            'items.item.baseUom', 'items.uom'
        ]);

        return view(
            'purchase_requisitions.show',
            compact('purchaseRequisition')
        );
    }

    private function authorizeCompany(PurchaseRequisition $requisition): void
    {
        abort_unless(
            (int) $requisition->company_id === (int) Auth::user()->company_id,
            403,
            'Unauthorized access.'
        );
    }

    public function approve(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorizeCompany($purchaseRequisition);

        if ($purchaseRequisition->status !== 'PENDING') {
            return back()->with('error', 'Only pending PRs can be approved.');
        }

        $purchaseRequisition->update([
            'status' => 'APPROVED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('purchase-requisitions.show', $purchaseRequisition->id)
            ->with('success', 'Purchase Requisition approved successfully.');
    }

    public function reject(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $this->authorizeCompany($purchaseRequisition);

        if ($purchaseRequisition->status !== 'PENDING') {
            return back()->with('error', 'Only pending PRs can be rejected.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $purchaseRequisition->update([
            'status' => 'REJECTED',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()
            ->route('purchase-requisitions.show', $purchaseRequisition->id)
            ->with('success', 'Purchase Requisition rejected.');
    }
}
