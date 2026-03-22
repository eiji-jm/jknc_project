<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UltimateBeneficialOwner extends Model
{
    protected $fillable = [
        'gis_id',
        'complete_name',
        'specific_residential_address',
        'nationality',
        'date_of_birth',
        'tax_identification_no',
        'ownership_voting_rights',
        'beneficial_owner_type',
        'beneficial_ownership_category',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'ownership_voting_rights' => 'decimal:2',
    ];

    public function gis()
    {
        return $this->belongsTo(GisRecord::class, 'gis_id');
    }
}