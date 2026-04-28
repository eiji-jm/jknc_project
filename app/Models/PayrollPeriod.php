<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'name',
        'period_start',
        'period_end',
        'payroll_start',
        'payroll_end',
        'payroll_date',
        'pay_date',
        'dispute_start',
        'dispute_end',
        'date_created',
        'policy_number',
        'basis_file_path',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payroll_start' => 'date',
        'payroll_end' => 'date',
        'payroll_date' => 'date',
        'pay_date' => 'date',
        'dispute_start' => 'date',
        'dispute_end' => 'date',
        'date_created' => 'date',
    ];
}
