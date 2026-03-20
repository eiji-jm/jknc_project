<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirTax extends Model
{
    protected $fillable = [
        'tin',
        'tax_payer',
        'registering_office',
        'registered_address',
        'tax_types',
        'form_type',
        'filing_frequency',
        'due_date',
        'uploaded_by',
        'date_uploaded',
        'document_path',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_uploaded' => 'date',
    ];
}
