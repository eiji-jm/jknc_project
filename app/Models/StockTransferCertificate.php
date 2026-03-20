<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferCertificate extends Model
{
    protected $fillable = [
        'date_uploaded',
        'uploaded_by',
        'corporation_name',
        'company_reg_no',
        'stock_number',
        'stockholder_name',
        'par_value',
        'number',
        'amount',
        'amount_in_words',
        'date_issued',
        'president',
        'corporate_secretary',
        'document_path',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_issued' => 'date',
        'par_value' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
