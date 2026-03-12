<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'company_service')
            ->withTimestamps();
    }

    public function cifs(): HasMany
    {
        return $this->hasMany(CompanyCif::class);
    }
}
