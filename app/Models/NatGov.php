<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NatGov extends Model
{
    protected $fillable = [
        'client',
        'tin',
        'agency',
        'registration_status',
        'registration_date',
        'deadline_date',
        'registration_no',
        'status',
        'uploaded_by',
        'date_uploaded',
        'document_path',
        'approved_document_path',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'deadline_date' => 'date',
        'date_uploaded' => 'date',
    ];
}
