<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayrollProfile extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_level_id',
        'basic_salary_override',
        'night_differential_enabled',
    ];

    protected $casts = [
        'night_differential_enabled' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollLevel()
    {
        return $this->belongsTo(PayrollLevel::class);
    }
}