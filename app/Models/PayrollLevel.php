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
        'hours_per_day',
    ];

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
    }
}