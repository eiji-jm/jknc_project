<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingChecklist extends Model
{
    protected $fillable = [
        'employee_name',
        'checked_documents',
        'docs_submitted',
        'total_docs',
        'created_by',
    ];

    protected $casts = [
        'checked_documents' => 'array',
    ];
}