<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
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
}
