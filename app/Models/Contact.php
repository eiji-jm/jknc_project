<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'intake_date',
        'customer_type',
        'client_status',
        'salutation',
        'first_name',
        'middle_initial',
        'middle_name',
        'last_name',
        'name_extension',
        'sex',
        'date_of_birth',
        'company_name',
        'company_address',
        'contact_address',
        'position',
        'business_type_organization',
        'organization_type',
        'organization_type_other',
        'nature_of_business',
        'capitalization_amount',
        'ownership_structure',
        'previous_year_revenue',
        'years_operating',
        'projected_current_year_revenue',
        'ownership_flag',
        'foreign_business_nature',
        'service_inquiry_types',
        'service_inquiry_other',
        'service_inquiry_type',
        'inquiry',
        'jknc_notes',
        'sales_marketing',
        'consultant_lead',
        'lead_associate',
        'recommendation_options',
        'recommendation_other',
        'lead_source_channels',
        'lead_source_other',
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
        'intake_date' => 'date',
        'date_of_birth' => 'date',
        'last_activity_at' => 'datetime',
        'capitalization_amount' => 'decimal:2',
        'previous_year_revenue' => 'decimal:2',
        'projected_current_year_revenue' => 'decimal:2',
        'service_inquiry_types' => 'array',
        'recommendation_options' => 'array',
        'lead_source_channels' => 'array',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
