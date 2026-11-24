<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApprovalWorkflowController extends Controller
{

    public function index()
    {
        $approvals = \App\Models\ApprovalRequestStep::with([
            'approvalRequest.requester',
        ])
            ->where('approver_user_id', Auth::id())
            ->where('status', 'PENDING')
            ->whereHas('approvalRequest', function ($query) {
                $query->where('status', 'PENDING')
                    ->whereColumn(
                        'approval_requests.current_step',
                        'approval_request_steps.step_order'
                    )
                    ->where('company_id', Auth::user()->company_id);
            })
            ->latest()
            ->get();

        return view('approvals.index', compact('approvals'));
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        abort_unless((int) $approvalRequest->company_id === (int) Auth::user()->company_id, 403);
        $request->validate([
            'comments' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($approvalRequest, $request) {

            $approval = ApprovalRequest::whereKey($approvalRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($approval->status !== 'PENDING') {
                throw ValidationException::withMessages([
                    'approval' => 'This approval request is no longer pending.',
                ]);
            }

            $step = $approval->steps()
                ->where('step_order', $approval->current_step)
                ->lockForUpdate()
                ->first();

            if (
                !$step ||
                (int) $step->approver_user_id !== (int) Auth::id() ||
                $step->status !== 'PENDING'
            ) {

                abort(403, 'You are not authorized to approve this step.');
            }

            // Approve current step
            $step->update([
                'status' => 'APPROVED',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
                'comments' => $request->comments,
            ]);

            $nextStep = $approval->steps()
                ->where('step_order', '>', $approval->current_step)
                ->orderBy('step_order')
                ->first();

            if ($nextStep) {
                // Move to next approver
                $approval->update([
                    'current_step' => $nextStep->step_order,
                ]);

                $nextStep->approver?->notify(new \App\Notifications\P2PNotice(
                    $approval->document_type.' step is ready for your approval.',
                    $approval->document_type === 'PR'
                        ? route('purchase-requisitions.show', $approval->document_id)
                        : route('purchase-orders.show', $approval->document_id)
                ));

                return;
            }

            // Final approval
            if ($approval->document_type === 'PR') {

                $pr = PurchaseRequisition::whereKey($approval->document_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pr->status !== 'SUBMITTED') {
                    throw ValidationException::withMessages([
                        'approval' => 'Purchase Requisition is not in submitted status.',
                    ]);
                }

                $pr->update([
                    'status' => 'APPROVED',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
            } elseif ($approval->document_type === 'PO') {

                $po = PurchaseOrder::with('items', 'purchaseRequisition')
                    ->whereKey($approval->document_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($po->status !== 'SENT') {
                    throw ValidationException::withMessages([
                        'approval' => 'Only SENT purchase orders can be approved.',
                    ]);
                }

                $requisition = $po->purchaseRequisition;
                if (!$requisition) throw ValidationException::withMessages(['cost_centre'=>'Linked requisition is missing.']);
                $requisition = $po->purchaseRequisition;
                $lineAmounts = []; $centreAmounts = [];
                foreach ($po->items as $item) {
                    $centreId = $item->cost_centre_id ?: $requisition->cost_centre_id;
                    if (!$centreId) throw ValidationException::withMessages(['cost_centre'=>'A PO line is missing its cost centre.']);
                    $lineAmount = round((float)$item->quantity * (float)$item->unit_price,2,PHP_ROUND_HALF_UP);
                    $gstAmount = (float)$item->cgst_amount + (float)$item->sgst_amount + (float)$item->igst_amount;
                    if ($gstAmount == 0 && (float)$item->gst_rate > 0) $gstAmount = round($lineAmount * (float)$item->gst_rate / 100,2,PHP_ROUND_HALF_UP);
                    $lineTotal = number_format($lineAmount + $gstAmount,2,'.','');
                    $lineAmounts[$item->id] = $lineTotal;
                    $centreAmounts[$centreId] = bcadd($centreAmounts[$centreId] ?? '0.00',$lineTotal,2);
                }
                $poAmount = array_reduce($centreAmounts, fn($sum,$amount)=>bcadd($sum,$amount,2), '0.00');

                // Financial year April to March
                $orderDate = $po->order_date;
                $year = (int) $orderDate->format('Y');

                if ((int) $orderDate->format('n') >= 4) {
                    $financialYear = $year . '-' .
                        substr((string) ($year + 1), -2);
                } else {
                    $financialYear = ($year - 1) . '-' .
                        substr((string) $year, -2);
                }

                if (count($centreAmounts) === 1) {
                // Lock budget row
                $budget = Budget::where('company_id', $po->company_id)
                    ->where('cost_centre_id', array_key_first($centreAmounts))
                    ->where('financial_year', $financialYear)
                    ->lockForUpdate()
                    ->first();

                if (!$budget) {
                    throw ValidationException::withMessages([
                        'budget' => "No budget found for financial year {$financialYear}.",
                    ]);
                }

                $available = bcsub(bcsub((string)$budget->budget_amount,(string)$budget->committed_amount,2),(string)$budget->spent_amount,2);

                if (bccomp(number_format($poAmount,2,'.',''), $available, 2) === 1) {
                    throw ValidationException::withMessages([
                        'budget' => 'Insufficient budget. Available: ₹'
                            . number_format($available, 2)
                            . ', Required: ₹'
                            . number_format($poAmount, 2),
                    ]);
                }

                $budget->update([
                    'committed_amount' => bcadd((string)$budget->committed_amount,number_format($poAmount,2,'.',''),2),
                ]);

                foreach ($po->items as $item) $item->update(['budget_committed_amount'=>$lineAmounts[$item->id]]);

                $po->update([
                    'status' => 'APPROVED',
                    'budget_committed_amount' => $poAmount,
                ]);
                } else {
                    $budgets = Budget::where('company_id',$po->company_id)->where('financial_year',$financialYear)
                        ->whereIn('cost_centre_id',array_keys($centreAmounts))->orderBy('cost_centre_id')->lockForUpdate()->get()->keyBy('cost_centre_id');
                    if ($budgets->count() !== count($centreAmounts)) throw ValidationException::withMessages(['budget'=>"A cost centre has no budget for financial year {$financialYear}."]);
                    foreach ($centreAmounts as $centreId=>$required) {
                        $budget=$budgets->get($centreId);
                        $available=bcsub(bcsub((string)$budget->budget_amount,(string)$budget->committed_amount,2),(string)$budget->spent_amount,2);
                        if (bccomp($required,$available,2)===1) throw ValidationException::withMessages(['budget'=>'Insufficient budget for '.$budget->costCentre->code.'. Available ₹'.number_format((float)$available,2).', required ₹'.number_format((float)$required,2).'.']);
                    }
                    foreach ($centreAmounts as $centreId=>$required) {
                        $budget=$budgets->get($centreId);
                        $budget->update(['committed_amount'=>bcadd((string)$budget->committed_amount,$required,2)]);
                    }
                    foreach ($po->items as $item) $item->update(['budget_committed_amount'=>$lineAmounts[$item->id]]);
                    $po->update(['status'=>'APPROVED','budget_committed_amount'=>$poAmount]);
                }
            } else {
                throw ValidationException::withMessages([
                    'approval' => 'Unsupported document type.',
                ]);
            }

            $approval->update([
                'status' => 'APPROVED',
            ]);
        });

        return back()->with(
            'success',
            'Approval completed successfully.'
        );
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        abort_unless((int) $approvalRequest->company_id === (int) Auth::user()->company_id, 403);
        $request->validate([
            'comments' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($approvalRequest, $request) {

            $approval = ApprovalRequest::whereKey($approvalRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($approval->status !== 'PENDING') {
                throw ValidationException::withMessages([
                    'approval' => 'This approval request is no longer pending.',
                ]);
            }

            $step = $approval->steps()
                ->where('step_order', $approval->current_step)
                ->lockForUpdate()
                ->first();

            if (
                !$step ||
                (int) $step->approver_user_id !== (int) Auth::id() ||
                $step->status !== 'PENDING'
            ) {

                abort(403, 'You are not authorized to reject this step.');
            }

            $step->update([
                'status' => 'REJECTED',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
                'comments' => $request->comments,
            ]);

            $approval->update([
                'status' => 'REJECTED',
            ]);

            if ($approval->document_type === 'PR') {
                PurchaseRequisition::whereKey($approval->document_id)->where('company_id',Auth::user()->company_id)
                    ->firstOrFail()->update([
                        'status' => 'REJECTED',
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                        'rejection_reason' => $request->comments,
                    ]);
            } elseif ($approval->document_type === 'PO') {
                PurchaseOrder::whereKey($approval->document_id)
                    ->where('company_id', Auth::user()->company_id)->firstOrFail()->update([
                        'status' => 'CANCELLED',
                        'rejection_reason' => $request->comments,
                    ]);
            }
        });

        return back()->with('success', 'Document rejected successfully.');
    }
}
