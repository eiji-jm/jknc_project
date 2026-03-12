<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCif extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'first_name',
        'last_name',
        'preferred_name',
        'patient_identifier',
        'gender',
        'preferred_pronouns',
        'date_of_birth',
        'marital_status',
        'address',
        'city',
        'state',
        'zip_code',
        'email',
        'preferred_phone',
        'emergency_contact_1_name',
        'emergency_contact_1_relationship',
        'emergency_contact_1_home_phone',
        'emergency_contact_1_cell_phone',
        'emergency_contact_1_work_phone',
        'emergency_contact_2_name',
        'emergency_contact_2_relationship',
        'emergency_contact_2_home_phone',
        'emergency_contact_2_cell_phone',
        'emergency_contact_2_work_phone',
        'insurance_carrier',
        'insurance_plan',
        'insurance_contact_number',
        'policy_number',
        'group_number',
        'social_security_number',
        'under_medical_care',
        'medical_care_for',
        'primary_care_physician',
        'physician_address',
        'physician_contact_number',
        'main_concerns',
        'illness_begin',
        'visit_goals',
        'status',
        'submitted_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'under_medical_care' => 'boolean',
        'submitted_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
