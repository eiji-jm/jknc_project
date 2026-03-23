<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TownHallCommunication extends Model
{
    protected $table = 'townhall_communications';

    protected $fillable = [
        'ref_no',
        'communication_date',
        'from_name',
        'department_stakeholder',
        'to_for',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'subject',
        'message',
        'cc',
        'additional',
        'attachment',
        'created_by',
        'source_type',
        'source_id',
        'deadline_date',
    ];

    protected $casts = [
        'communication_date' => 'date',
        'approved_at' => 'datetime',
        'deadline_date' => 'date',
    ];
}
