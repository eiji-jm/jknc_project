<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferInstallment extends Model
{
    protected $fillable = [
        'stock_number',
        'subscriber',
        'installment_date',
        'no_shares',
        'no_installments',
        'total_value',
        'installment_amount',
        'status',
        'schedule',
        'document_path',
    ];

    protected $casts = [
        'installment_date' => 'date',
        'total_value' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'schedule' => 'array',
    ];
}
