<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCif extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'cif_no',
        'cif_date',
        'client_type',
        'first_name',
        'last_name',
        'middle_name',
        'name_extension',
        'no_middle_name',
        'first_name_only',
        'address',
        'zip_code',
        'email',
        'phone_no',
        'mobile_no',
        'date_of_birth',
        'place_of_birth',
        'citizenship_status',
        'nationality',
        'gender',
        'marital_status',
        'spouse_name',
        'nature_of_work_business',
        'tin',
        'other_government_id',
        'id_number',
        'mothers_maiden_name',
        'source_of_funds_salary',
        'source_of_funds_remittance',
        'source_of_funds_business',
        'source_of_funds_others',
        'source_of_funds_other_text',
        'source_of_funds_commission_fees',
        'source_of_funds_retirement_pension',
        'passport_no',
        'passport_expiry_date',
        'passport_place_of_issue',
        'acr_id_no',
        'acr_expiry_date',
        'acr_place_of_issue',
        'visa_status',
        'signature_printed_name',
        'signature_position',
        'review_signature_printed_name',
        'review_signature_position',
        'referred_by',
        'referred_by_date',
        'sales_marketing_name',
        'finance_name',
        'president_name',
        'onboarding_two_valid_government_ids',
        'onboarding_tin_id',
        'onboarding_specimen_signature',
        'remarks',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by_name',
        'rejected_at',
        'rejected_by_name',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cif_date' => 'date',
        'no_middle_name' => 'boolean',
        'first_name_only' => 'boolean',
        'date_of_birth' => 'date',
        'source_of_funds_salary' => 'boolean',
        'source_of_funds_remittance' => 'boolean',
        'source_of_funds_business' => 'boolean',
        'source_of_funds_others' => 'boolean',
        'source_of_funds_commission_fees' => 'boolean',
        'source_of_funds_retirement_pension' => 'boolean',
        'passport_expiry_date' => 'date',
        'acr_expiry_date' => 'date',
        'referred_by_date' => 'date',
        'onboarding_two_valid_government_ids' => 'boolean',
        'onboarding_tin_id' => 'boolean',
        'onboarding_specimen_signature' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
