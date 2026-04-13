<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'division_name',
        'department_id',
        'address_id',
        'division_head',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'division_id');
    }
}