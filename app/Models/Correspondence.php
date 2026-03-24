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
        'status',
    ];

    protected $appends = ['computed_status'];

    protected $casts = [
        'uploaded_date' => 'date:Y-m-d',
        'date' => 'date:Y-m-d',
        'deadline' => 'date:Y-m-d',
    ];

    public function getComputedStatusAttribute()
    {
        if (!$this->deadline) {
            return 'Open';
        }

        return $this->deadline->lt(Carbon::today()) ? 'Closed' : 'Open';
    }

    public function getFromValueAttribute()
    {
        return $this->sender_type === 'From' ? $this->sender : null;
    }

    public function getToValueAttribute()
    {
        return $this->sender_type === 'To' ? $this->sender : null;
    }
}