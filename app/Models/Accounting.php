<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $fillable = [
        'statement_type',
        'client',
        'tin',
        'date',
        'user',
        'status',
        'document_name',
        'document_path',
    ];
}