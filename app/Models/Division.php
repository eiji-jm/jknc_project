<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'office_id',
        'branch_id',
        'department_id',
        'division_name',
        'division_address',
        'division_head',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}