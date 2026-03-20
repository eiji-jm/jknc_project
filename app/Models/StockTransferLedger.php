<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferLedger extends Model
{
    protected $fillable = [
        'family_name',
        'first_name',
        'middle_name',
        'nationality',
        'address',
        'tin',
        'email',
        'phone',
        'shares',
        'certificate_no',
        'date_registered',
        'status',
        'document_path',
    ];

    protected $casts = [
        'date_registered' => 'date',
    ];
}
