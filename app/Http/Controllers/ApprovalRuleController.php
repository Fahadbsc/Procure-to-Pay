<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRule;
use App\Models\ApprovalRuleStep;
use App\Models\CostCentre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalRuleController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $rules = ApprovalRule::with([
            'costCentre',
            'steps.approver'
        ])
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        return view('approval_rules.index', compact('rules'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;

        $costCentres = CostCentre::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $users = User::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('approval_rules.create', compact(
            'costCentres',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:PR,PO',
            'category' => 'nullable|in:RAW_MATERIAL,IT,SERVICES,CONSUMABLES',
            'cost_centre_id' => 'nullable|integer',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'approver_user_ids' => 'required|array|min:1',
            'approver_user_ids.*' => 'required|integer|distinct',
        ]);

        $companyId = Auth::user()->company_id;

        if ($validated['cost_centre_id']) {
            $costCentreExists = CostCentre::where('company_id', $companyId)
                ->where('id', $validated['cost_centre_id'])
                ->exists();

            if (!$costCentreExists) {
                return back()
                    ->withErrors(['cost_centre_id' => 'Invalid cost centre.'])
                    ->withInput();
            }
        }

        $approverIds = $validated['approver_user_ids'];

        $validApproverCount = User::where('company_id', $companyId)
            ->whereIn('id', $approverIds)
            ->count();

        if ($validApproverCount !== count($approverIds)) {
            return back()
                ->withErrors(['approver_user_ids' => 'Invalid approver selected.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $approverIds, $companyId) {

            $rule = ApprovalRule::create([
                'company_id' => $companyId,
                'document_type' => $validated['document_type'],
                'category' => $validated['category'] ?? null,
                'cost_centre_id' => $validated['cost_centre_id'] ?? null,
                'min_amount' => $validated['min_amount'],
                'max_amount' => $validated['max_amount'] ?? null,
                'is_active' => true,
            ]);

            foreach ($approverIds as $index => $approverId) {
                ApprovalRuleStep::create([
                    'approval_rule_id' => $rule->id,
                    'step_order' => $index + 1,
                    'approver_user_id' => $approverId,
                ]);
            }
        });

        return redirect()
            ->route('approval-rules.index')
            ->with('success', 'Approval rule created successfully.');
    }

    public function edit($id)
    {
        $companyId = Auth::user()->company_id;

        $rule = ApprovalRule::with('steps')
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $costCentres = CostCentre::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $users = User::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $selectedApprovers = $rule->steps
            ->sortBy('step_order')
            ->pluck('approver_user_id')
            ->toArray();

        return view('approval_rules.edit', compact(
            'rule',
            'costCentres',
            'users',
            'selectedApprovers'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:PR,PO',
            'category' => 'nullable|in:RAW_MATERIAL,IT,SERVICES,CONSUMABLES',
            'cost_centre_id' => 'nullable|integer',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'approver_user_ids' => 'required|array|min:1',
            'approver_user_ids.*' => 'required|integer|distinct',
            'is_active' => 'nullable|boolean',
        ]);

        $companyId = Auth::user()->company_id;

        $rule = ApprovalRule::where('company_id', $companyId)
            ->findOrFail($id);

        if (!empty($validated['cost_centre_id'])) {
            $exists = CostCentre::where('company_id', $companyId)
                ->where('id', $validated['cost_centre_id'])
                ->exists();

            if (!$exists) {
                return back()
                    ->withErrors(['cost_centre_id' => 'Invalid cost centre.'])
                    ->withInput();
            }
        }

        $approverIds = $validated['approver_user_ids'];

        $validApproverCount = User::where('company_id', $companyId)
            ->whereIn('id', $approverIds)
            ->count();

        if ($validApproverCount !== count($approverIds)) {
            return back()
                ->withErrors(['approver_user_ids' => 'Invalid approver selected.'])
                ->withInput();
        }

        DB::transaction(function () use ($rule, $validated, $approverIds) {

            $rule->update([
                'document_type' => $validated['document_type'],
                'category' => $validated['category'] ?? null,
                'cost_centre_id' => $validated['cost_centre_id'] ?? null,
                'min_amount' => $validated['min_amount'],
                'max_amount' => $validated['max_amount'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $rule->steps()->delete();

            foreach ($approverIds as $index => $approverId) {
                ApprovalRuleStep::create([
                    'approval_rule_id' => $rule->id,
                    'step_order' => $index + 1,
                    'approver_user_id' => $approverId,
                ]);
            }
        });

        return redirect()
            ->route('approval-rules.index')
            ->with('success', 'Approval rule updated successfully.');
    }

    public function destroy($id)
    {
        $rule = ApprovalRule::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        DB::transaction(function () use ($rule) {
            $rule->steps()->delete();
            $rule->delete();
        });

        return redirect()
            ->route('approval-rules.index')
            ->with('success', 'Approval rule deleted successfully.');
    }
}
