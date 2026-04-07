<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'office_id',
        'branch_id',
        'department_name',
        'department_address',
        'department_head',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}