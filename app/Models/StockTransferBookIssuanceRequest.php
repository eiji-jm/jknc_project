<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookIssuanceRequest extends Model
{
    protected $table = 'stock_transfer_book_issuance_requests';

    protected $fillable = [
        'ref_no',
        'date_requested',
        'time_requested',
        'type_of_request',
        'requester',
        'received_by',
        'issued_by',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date_requested' => 'date',
    ];
}