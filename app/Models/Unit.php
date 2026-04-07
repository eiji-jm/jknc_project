<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'office_id',
        'branch_id',
        'department_id',
        'division_id',
        'unit_name',
        'unit_address',
        'unit_head',
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

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}