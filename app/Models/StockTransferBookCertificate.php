<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookCertificate extends Model
{
    protected $table = 'stock_transfer_book_certificates';

    protected $fillable = [
        'stock_transfer_book_ledger_id',
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
        'status',
        'created_by',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_issued' => 'date',
    ];

    public function ledgerRecord()
    {
        return $this->belongsTo(StockTransferBookLedger::class, 'stock_transfer_book_ledger_id');
    }

    public function voucher()
    {
        return $this->hasOne(StockTransferBookCertificateVoucher::class, 'stock_transfer_book_certificate_id');
    }

    public function cancellations()
    {
        return $this->hasMany(StockTransferBookCertificateCancellation::class, 'stock_transfer_book_certificate_id');
    }
}