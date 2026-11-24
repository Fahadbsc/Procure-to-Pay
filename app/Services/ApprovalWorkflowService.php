<?php

namespace App\Services;

use App\Models\ApprovalRule;
use App\Models\ApprovalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalWorkflowService
{
    public function startApproval(
        string $documentType,
        int $documentId,
        int $companyId,
        int $requesterId,
        string|float $amount,
        ?int $costCentreId = null,
        ?string $category = null
    ): ApprovalRequest {

        return DB::transaction(function () use (
            $documentType,
            $documentId,
            $companyId,
            $requesterId,
            $amount,
            $costCentreId,
            $category
        ) {

            // Prevent duplicate approval requests
            $existingRequest = ApprovalRequest::where(
                'document_type',
                $documentType
            )->where('document_id', $documentId)
             ->lockForUpdate()
             ->first();

            if ($existingRequest) {
                throw ValidationException::withMessages([
                    'approval' => 'Approval workflow already exists for this document.',
                ]);
            }

            // Find applicable approval rule
            $rule = ApprovalRule::where('company_id', $companyId)
                ->where('document_type', $documentType)
                ->where('is_active', true)
                ->where('min_amount', '<=', $amount)
                ->where(function ($query) use ($amount) {
                    $query->whereNull('max_amount')
                        ->orWhere('max_amount', '>=', $amount);
                })
                ->where(function ($query) use ($costCentreId) {
                    $query->whereNull('cost_centre_id');

                    if ($costCentreId !== null) {
                        $query->orWhere('cost_centre_id', $costCentreId);
                    }
                })
                ->where(function ($query) use ($category) {
                    $query->whereNull('category');
                    if ($category !== null) $query->orWhere('category', $category);
                })
                ->with('steps')
                ->orderByRaw(
                    'CASE WHEN cost_centre_id IS NULL THEN 1 ELSE 0 END'
                )
                ->orderByRaw('CASE WHEN category IS NULL THEN 1 ELSE 0 END')
                ->orderByDesc('min_amount')
                ->first();

            if (!$rule || $rule->steps->isEmpty()) {
                throw ValidationException::withMessages([
                    'approval' => 'No active approval rule found for this document amount and cost centre.',
                ]);
            }

            // Snapshot an eligible colleague when the configured approver raised the document.
            foreach ($rule->steps as $step) {
                $configuredApprover = $step->approver()->with('roles')->first();
                if (!$configuredApprover || (int) $configuredApprover->company_id !== $companyId) {
                    throw ValidationException::withMessages(['approval' => 'Approval rule contains an approver outside this company.']);
                }
                if ((int) $step->approver_user_id === $requesterId) {
                    $roleIds = $configuredApprover->roles
                        ->reject(fn($role) => $role->name === 'Requester')
                        ->pluck('id');
                    $alternate = \App\Models\User::where('company_id', $companyId)
                        ->where('id', '!=', $requesterId)
                        ->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $roleIds))
                        ->orderBy('id')->value('id');
                    if (!$alternate) {
                        throw ValidationException::withMessages(['approval' => 'No alternate approver with the required role is available; ask your Admin to assign one.']);
                    }
                    $step->approver_user_id = $alternate;
                    $step->save();
                }
            }

            // Create approval request
            $approvalRequest = ApprovalRequest::create([
                'company_id' => $companyId,
                'document_type' => $documentType,
                'document_id' => $documentId,
                'requested_by' => $requesterId,
                'current_step' => 1,
                'status' => 'PENDING',
            ]);

            // Snapshot the approver sequence
            foreach ($rule->steps as $step) {
                $approvalRequest->steps()->create([
                    'step_order' => $step->step_order,
                    'approver_user_id' => $step->approver_user_id,
                    'status' => 'PENDING',
                ]);
            }

            return $approvalRequest->load('steps');
        });
    }
}
