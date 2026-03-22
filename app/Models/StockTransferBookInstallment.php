<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookInstallment extends Model
{
    protected $table = 'stock_transfer_book_installments';

    protected $fillable = [
        'stock_transfer_book_ledger_id',
        'stock_number',
        'subscriber',
        'installment_date',
        'no_shares',
        'no_installments',
        'total_value',
        'installment_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'installment_date' => 'date',
        'total_value' => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    public function ledgerRecord()
    {
        return $this->belongsTo(StockTransferBookLedger::class, 'stock_transfer_book_ledger_id');
    }
}