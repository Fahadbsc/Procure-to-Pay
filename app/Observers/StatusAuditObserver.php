<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalRequest;
use App\Models\User;
use App\Notifications\P2PNotice;

class StatusAuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, null, (string) $model->status);
    }

    public function updated(Model $model): void
    {
        if ($model->wasChanged('status')) {
            $this->record($model, (string) $model->getOriginal('status'), (string) $model->status);
        }
    }

    private function record(Model $model, ?string $from, string $to): void
    {
        if (!$model->getAttribute('company_id')) return;
        $comment = $model->getAttribute('rejection_reason')
            ?? $model->getAttribute('override_comment')
            ?? $model->getAttribute('remarks');
        DB::table('audit_logs')->insert([
            'company_id' => $model->company_id,
            'user_id' => Auth::id(),
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'from_status' => $from,
            'to_status' => $to,
            'comment' => $comment,
            'created_at' => now(),
        ]);

        $type = $model instanceof \App\Models\PurchaseRequisition ? 'PR' : ($model instanceof \App\Models\PurchaseOrder ? 'PO' : null);
        $approval = $type ? ApprovalRequest::with('steps')->where('document_type',$type)->where('document_id',$model->getKey())->first() : null;
        $url = $type === 'PR' ? route('purchase-requisitions.show',$model->getKey()) : ($type === 'PO' ? route('purchase-orders.show',$model->getKey()) : route('supplier-invoices.show',$model->getKey()));
        if ($to === 'APPROVED' && $approval) {
            User::find($approval->requested_by)?->notify(new P2PNotice('Your '.$type.' was approved.',$url));
        } elseif (($to === 'REJECTED' || $approval?->status === 'REJECTED') && $approval) {
            User::find($approval->requested_by)?->notify(new P2PNotice('Your '.$type.' was rejected: '.$comment,$url));
        } elseif ($approval && $approval->status === 'PENDING') {
            $step = $approval->steps->firstWhere('step_order',$approval->current_step);
            User::find($step?->approver_user_id)?->notify(new P2PNotice($type.' requires your approval.',$url));
        } elseif (in_array($to,['APPROVED','MATCHED','POSTED','ON_HOLD'],true)) {
            if ($approval) User::find($approval->requested_by)?->notify(new P2PNotice($type.' status changed to '.$to.'.',$url));
            if ($to === 'ON_HOLD' || $model instanceof \App\Models\SupplierInvoice) {
                User::where('company_id',$model->company_id)->whereHas('roles',fn($q)=>$q->where('name','Accounts'))->get()->each(fn($user)=>$user->notify(new P2PNotice('Invoice status changed to '.$to.'.',$url)));
            }
        }
    }
}
