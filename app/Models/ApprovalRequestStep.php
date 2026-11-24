<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequestStep extends Model
{
    protected $fillable = [
        'approval_request_id',
        'step_order',
        'approver_user_id',
        'status',
        'acted_by',
        'acted_at',
        'comments',
        'last_reminded_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function approvalRequest()
    {
        return $this->belongsTo(ApprovalRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

}
