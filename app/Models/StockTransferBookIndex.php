<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferBookIndex extends Model
{
    protected $table = 'stock_transfer_book_indexes';

    protected $fillable = [
        'family_name',
        'first_name',
        'middle_name',
        'nationality',
        'current_address',
        'tin',
        'created_by',
    ];
}