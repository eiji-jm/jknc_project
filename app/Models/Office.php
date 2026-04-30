<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'office_name',
        'branch_id',
        'address_id',
        'office_head',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'office_id');
    }
}