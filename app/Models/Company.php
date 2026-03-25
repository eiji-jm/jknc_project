<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'website',
        'description',
        'address',
        'owner_name',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function cifs(): HasMany
    {
        return $this->hasMany(CompanyCif::class);
    }

    public function bifs(): HasMany
    {
        return $this->hasMany(CompanyBif::class);
    }

    public function latestBif(): HasOne
    {
        return $this->hasOne(CompanyBif::class)->latestOfMany();
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class)->withTimestamps();
    }
}
