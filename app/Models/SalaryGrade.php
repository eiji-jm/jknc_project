<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryGrade extends Model
{
    protected $fillable = [
        'code',
        'name',
        'applicable_daily_rate',
    ];

    public function payrollLevels()
    {
        return $this->hasMany(PayrollLevel::class);
    }
}