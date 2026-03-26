<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    protected $fillable = [
        'legal_type',
        'client',
        'tin',
        'date',
        'document_type',
        'document_name',
        'document_path',
        'user',
        'submitted_by',
        'workflow_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'review_note',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute(): string
    {
        return $this->document_path ? 'Completed' : 'Pending';
    }
}