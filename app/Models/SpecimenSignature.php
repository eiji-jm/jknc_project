<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecimenSignature extends Model
{
    protected $fillable = [
        'contact_id',
        'bif_no',
        'date',
        'client_type',
        'business_name_left',
        'business_name_right',
        'account_number_left',
        'account_number_right',
        'signatories',
        'authentication_data',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'signatories' => 'array',
        'authentication_data' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
