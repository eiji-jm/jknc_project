<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $fillable = [
        'type',
        'client',
        'tin',
        'date',
        'user',
        'status',
        'document_name',
        'document_path',
    ];
}