<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferJournal extends Model
{
    protected $fillable = [
        'entry_date',
        'journal_no',
        'ledger_folio',
        'particulars',
        'no_shares',
        'transaction_type',
        'certificate_no',
        'shareholder',
        'remarks',
        'document_path',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];
}
