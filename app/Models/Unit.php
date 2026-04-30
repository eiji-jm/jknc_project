<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'unit_name',
        'division_id',
        'address_id',
        'unit_head',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function address()
    {
        return $this->belongsTo(OrganizationalAddress::class, 'address_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class, 'unit_id');
    }
}