<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyConsultationNote extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'consultation_date',
        'author',
        'summary',
        'details',
        'category',
        'linked_deal',
        'linked_activity',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'consultation_date' => 'date',
            'attachments' => 'array',
        ];
    }
}
