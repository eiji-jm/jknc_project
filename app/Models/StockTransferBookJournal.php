<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookJournal extends Model
{
    protected $table = 'stock_transfer_book_journals';

    protected $fillable = [
        'stock_transfer_book_ledger_id',
        'entry_date',
        'journal_no',
        'ledger_folio',
        'particulars',
        'no_shares',
        'transaction_type',
        'certificate_no',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function ledgerRecord()
    {
        return $this->belongsTo(StockTransferBookLedger::class, 'stock_transfer_book_ledger_id');
    }
}