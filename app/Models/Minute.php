<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minute extends Model
{
    protected $fillable = [
        'minutes_ref',
        'date_uploaded',
        'uploaded_by',
        'governing_body',
        'type_of_meeting',
        'meeting_mode',
        'notice_ref',
        'date_of_meeting',
        'time_started',
        'time_ended',
        'location',
        'call_link',
        'recording_notes',
        'meeting_no',
        'chairman',
        'secretary',
        'document_path',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_of_meeting' => 'date',
    ];
}
