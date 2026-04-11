<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookCertificateVoucher extends Model
{
    protected $table = 'stock_transfer_book_certificate_vouchers';

    protected $fillable = [
        'stock_transfer_book_certificate_id',
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
        'issued_to',
        'issued_to_type',
        'certificate_released_date',
        'created_by',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_issued' => 'date',
        'certificate_released_date' => 'date',
    ];

    public function certificate()
    {
        return $this->belongsTo(StockTransferBookCertificate::class, 'stock_transfer_book_certificate_id');
    }
}