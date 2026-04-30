<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'department_name',
        'office_id',
        'address_id',
        'department_head',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'department_id');
    }
}