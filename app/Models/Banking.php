<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banking extends Model
{
    protected $fillable = [
        'date_uploaded',
        'user',
        'submitted_by',
        'client',
        'tin',
        'bank',
        'bank_doc',
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
        'date_uploaded' => 'date',
        'approved_at' => 'datetime',
    ];
}