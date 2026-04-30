<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationalAddress extends Model
{
    protected $fillable = [
        'country',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'province_type',
        'city_code',
        'city_name',
        'barangay_code',
        'barangay_name',
        'street_address',
        'subdivision_building',
        'unit_no',
        'postal_code',
        'full_address',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class, 'address_id');
    }

    public function offices()
    {
        return $this->hasMany(Office::class, 'address_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'address_id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'address_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'address_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class, 'address_id');
    }
}