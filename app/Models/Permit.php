<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'permit_type',
        'date_of_registration',
        'approved_date_of_registration',
        'expiration_date_of_registration',
        'user',
        'client',
        'tin',
        'registration_status',
        'status',
    ];
}