<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banking extends Model
{
    protected $fillable = [
        'date_uploaded',
        'user',
        'client',
        'tin',
        'bank',
        'bank_doc',
        'status',
        'document_name',
        'document_path',
    ];
}