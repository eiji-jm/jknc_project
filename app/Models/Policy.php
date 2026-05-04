<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'code',
        'policy',
        'version',
        'effectivity_date',
        'prepared_by',
        'reviewed_by',
        'approved_by',
        'classification',
        'description',
        'attachment',
        'approval_status',
        'workflow_status',
        'is_archived',
        'archived_at',
        'submitted_by',
        'approved_by_user_id',
        'approved_at',
        'review_note',
    ];
}
