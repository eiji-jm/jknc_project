<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookLedger extends Model
{
    protected $table = 'stock_transfer_book_ledgers';

    protected $fillable = [
        'stock_transfer_book_index_id',
        'certificate_no',
        'number_of_shares',
        'date_registered',
        'status',
        'created_by',
    ];

    public function indexRecord()
    {
        return $this->belongsTo(StockTransferBookIndex::class, 'stock_transfer_book_index_id');
    }

    public function journals()
    {
        return $this->hasMany(StockTransferBookJournal::class, 'stock_transfer_book_ledger_id');
    }

    public function certificates()
    {
        return $this->hasMany(StockTransferBookCertificate::class, 'stock_transfer_book_ledger_id');
    }

    public function installments()
    {
        return $this->hasMany(StockTransferBookInstallment::class, 'stock_transfer_book_ledger_id');
    }
}