<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correspondence extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'uploaded_date',
        'user',
        'client',
        'tin',
        'subject',
        'from',
        'to',
        'department',
        'details',
        'date',
        'time',
        'deadline',
        'sent_via',
        'status',
    ];
}