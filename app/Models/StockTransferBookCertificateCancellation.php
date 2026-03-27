<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookCertificateCancellation extends Model
{
    protected $table = 'stock_transfer_book_certificate_cancellations';

    protected $fillable = [
        'stock_transfer_book_certificate_id',
        'date_of_cancellation',
        'effective_date',
        'reason',
        'type_of_cancellation',
        'others_specify',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date_of_cancellation' => 'date',
        'effective_date' => 'date',
    ];

    public function certificate()
    {
        return $this->belongsTo(StockTransferBookCertificate::class, 'stock_transfer_book_certificate_id');
    }
}