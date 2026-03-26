<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Correspondence extends Model
{
    protected $fillable = [
        'type',
        'uploaded_date',
        'user',
        'submitted_by',
        'tin',
        'subject',
        'sender_type',
        'sender',
        'department',
        'details',
        'date',
        'time',
        'deadline',
        'sent_via',
        'workflow_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'review_note',
    ];

    protected $appends = ['computed_status'];

    protected $casts = [
        'uploaded_date' => 'date:Y-m-d',
        'date' => 'date:Y-m-d',
        'deadline' => 'date:Y-m-d',
        'approved_at' => 'datetime',
    ];

    public function getComputedStatusAttribute()
    {
        if (!$this->deadline) {
            return 'Open';
        }

        return $this->deadline->lt(Carbon::today()) ? 'Closed' : 'Open';
    }
}