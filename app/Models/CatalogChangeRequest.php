<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogChangeRequest extends Model
{
    protected $fillable = [
        'module',
        'record_id',
        'record_public_id',
        'record_name',
        'action',
        'payload',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_notes',
    ];

    protected $casts = [
        'payload' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
