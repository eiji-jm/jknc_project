<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'employee_id',
        'department',
        'payroll_period',
        'pay_date',
        'basic_pay',
        'allowance',
        'deductions',
        'net_pay',
        'status',
        'prepared_by',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'basic_pay' => 'decimal:2',
        'allowance' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];
}