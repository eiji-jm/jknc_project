<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UltimateBeneficialOwner extends Model
{
    protected $fillable = [
        'complete_name',
        'email',
        'residential_address',
        'nationality',
        'date_of_birth',
        'tax_identification_no',
        'ownership_percentage',
        'ownership_type',
        'ownership_category',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'ownership_percentage' => 'decimal:2',
    ];
}
