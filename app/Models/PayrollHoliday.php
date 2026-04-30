<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHoliday extends Model
{
    protected $fillable = [
        'salary_grade_id',
        'payroll_level_id',
        'name',
        'holiday_date',
        'holiday_type',
        'holiday_category',
        'percentage',
        'holiday_value',
        'multiplier',
        'date_created',
        'policy_number',
        'basis_file_path',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'date_created' => 'date',
    ];

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    public function payrollLevel()
    {
        return $this->belongsTo(PayrollLevel::class);
    }
}
