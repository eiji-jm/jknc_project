<?php

namespace App\Models;

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
}
