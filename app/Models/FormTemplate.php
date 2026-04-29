<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $fillable = [
        'type',
        'name',
        'payload',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
