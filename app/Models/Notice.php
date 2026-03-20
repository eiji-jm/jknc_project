<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'notice_number',
        'date_of_notice',
        'governing_body',
        'type_of_meeting',
        'date_of_meeting',
        'time_started',
        'location',
        'meeting_no',
        'chairman',
        'secretary',
        'uploaded_by',
        'date_updated',
        'document_path',
    ];

    protected $casts = [
        'date_of_notice' => 'date',
        'date_of_meeting' => 'date',
        'date_updated' => 'date',
    ];
}
