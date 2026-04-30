<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLevel extends Model
{
    protected $fillable = [
        'salary_grade_id',
        'level_name',
        'computation_type',
        'work_schedule',
        'work_schedule_label',
        'hours_per_day',
        'date_created',
        'policy_number',
        'basis_file_path',
    ];

    protected $casts = [
        'date_created' => 'date',
    ];

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
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
