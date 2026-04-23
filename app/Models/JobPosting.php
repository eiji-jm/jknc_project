<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'wage_compliance' => 'array',
        'benefits_package' => 'array',
        'work_schedule' => 'array',
        'recruitment_channels' => 'array',
        'screening_flow' => 'array',
        'human_capital_approval' => 'array',
        'hiring_manager_approval' => 'array',
        'finance_approval' => 'array',
        'president_approval' => 'array',
    ];
}
