<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryGrade extends Model
{
    protected $fillable = [
        'code',
        'name',
        'payment_type',
        'monthly_basic_pay',
        'applicable_daily_rate',
        'hourly_rate',
        'minute_rate',
        'yearly_rate',
        'date_created',
        'policy_number',
        'basis_file_path',
    ];

    protected $casts = [
        'date_created' => 'date',
    ];

    public function payrollLevels()
    {
        return $this->hasMany(PayrollLevel::class);
    }

    public function benefits()
    {
        return $this->hasMany(PayrollBenefit::class);
    }

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function deductions()
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    public function holidays()
    {
        return $this->hasMany(PayrollHoliday::class);
    }
}
