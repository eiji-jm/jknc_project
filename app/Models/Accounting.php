<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $fillable = [
        'statement_type',
        'client',
        'tin',
        'date',
        'user',
        'submitted_by',
        'status',
        'workflow_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'review_note',
        'document_name',
        'document_path',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];
}