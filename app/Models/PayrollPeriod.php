<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'name',
        'period_start',
        'period_end',
        'payroll_date',
        'pay_date',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payroll_date' => 'date',
        'pay_date' => 'date',
    ];
}