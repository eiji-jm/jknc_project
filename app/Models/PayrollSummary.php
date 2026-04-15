<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSummary extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'payroll_level_id',
        'computation_type',
        'gross_pay',
        'total_benefits',
        'total_allowances',
        'total_deductions',
        'night_differential_amount',
        'holiday_pay_amount',
        'net_pay',
        'breakdown_json',
        'status',
    ];

    protected $casts = [
        'breakdown_json' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function payrollLevel()
    {
        return $this->belongsTo(PayrollLevel::class);
    }

    public function items()
    {
        return $this->hasMany(PayrollSummaryItem::class);
    }
}