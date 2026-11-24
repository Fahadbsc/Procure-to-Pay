<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'company_id',
        'document_type',
        'document_id',
        'requested_by',
        'current_step',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function steps()
    {
        return $this->hasMany(ApprovalRequestStep::class)
            ->orderBy('step_order');
    }

    public function currentApprovalStep()
    {
        return $this->hasOne(ApprovalRequestStep::class)
            ->where('step_order', $this->current_step);
    }
}
