<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    protected $fillable = [
        'resolution_no',
        'date_uploaded',
        'uploaded_by',
        'governing_body',
        'type_of_meeting',
        'notice_ref',
        'meeting_no',
        'date_of_meeting',
        'location',
        'board_resolution',
        'directors',
        'chairman',
        'secretary',
        'notary_doc_no',
        'notary_page_no',
        'notary_book_no',
        'notary_series_no',
        'notary_public',
        'draft_file_path',
        'notarized_file_path',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_of_meeting' => 'date',
    ];
}
