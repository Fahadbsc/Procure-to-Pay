<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRuleStep extends Model
{
    protected $fillable = [
        'approval_rule_id',
        'step_order',
        'approver_user_id',
    ];

    public function approvalRule()
    {
        return $this->belongsTo(ApprovalRule::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
