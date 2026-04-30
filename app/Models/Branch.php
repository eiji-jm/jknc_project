<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'branch_name',
        'address_id',
        'branch_head',
    ];

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }

    public function offices()
    {
        return $this->hasMany(Office::class, 'branch_id');
    }
}