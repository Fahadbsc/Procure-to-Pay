<?php
namespace App\Console\Commands;
use App\Models\ApprovalRequestStep;
use App\Notifications\P2PNotice;
use Illuminate\Console\Command;
class RemindOverdueApprovals extends Command
{
    protected $signature = 'p2p:remind-overdue-approvals';
    protected $description = 'Remind approvers about approval steps pending for more than 48 hours';
    public function handle(): int
    {
        ApprovalRequestStep::with(['approver','approvalRequest.steps'])
            ->where('status','PENDING')->where('created_at','<=',now()->subHours(48))
            ->where(fn($q)=>$q->whereNull('last_reminded_at')->orWhere('last_reminded_at','<=',now()->subHours(24)))
            ->chunkById(100,function($steps){
                foreach($steps as $step){
                    $request=$step->approvalRequest;
                    if(!$request || $request->status!=='PENDING' || (int)$request->current_step!==(int)$step->step_order) continue;
                    $url=$request->document_type==='PR' ? route('purchase-requisitions.show',$request->document_id) : route('purchase-orders.show',$request->document_id);
                    $notice=new P2PNotice('Approval has been waiting for more than 48 hours. Please review it.',$url);
                    $step->approver?->notify($notice);
                    if($step->approver?->manager) $step->approver->manager->notify($notice);
                    $step->update(['last_reminded_at'=>now()]);
                }
            });
        $this->info('Overdue approval reminders processed.');
        return self::SUCCESS;
    }
}
