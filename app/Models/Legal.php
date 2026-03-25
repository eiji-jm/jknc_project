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
    ];

    protected $appends = ['status'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function getStatusAttribute(): string
    {
        return $this->document_path ? 'Completed' : 'Pending';
    }
}