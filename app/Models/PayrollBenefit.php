<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBenefit extends Model
{
    protected $fillable = [
        'salary_grade_id',
        'payroll_level_id',
        'name',
        'type',
        'rate',
        'value',
        'is_active',
        'date_created',
        'policy_number',
        'basis_file_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
