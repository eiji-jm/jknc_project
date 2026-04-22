<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalDataSheet extends Model
{
    protected $fillable = [
        'full_name',
        'position',
        'email',
        'phone',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
