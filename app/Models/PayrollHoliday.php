<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHoliday extends Model
{
    protected $fillable = [
        'name',
        'holiday_date',
        'holiday_type',
        'multiplier',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];
}