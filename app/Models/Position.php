<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'position_name',
        'unit_id',
        'address_id',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }
}