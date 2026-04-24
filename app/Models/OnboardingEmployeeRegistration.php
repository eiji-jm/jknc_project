<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingEmployeeRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'employee_id',
        'department',
        'start_date',
        'work_email',
        'manager',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];
}