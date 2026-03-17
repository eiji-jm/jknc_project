<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'customer_type',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'company_name',
        'company_address',
        'contact_address',
        'position',
        'service_inquiry_type',
        'referred_by',
        'lead_stage',
        'recommendation',
        'email',
        'phone',
        'kyc_status',
        'owner_name',
        'last_activity_at',
        'lead_source',
        'description',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
