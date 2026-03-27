<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    protected $fillable = [
        'contact_id',
        'deal_code',
        'stage_id',
        'created_by',
        'deal_name',
        'stage',
        'service_area',
        'services',
        'products',
        'scope_of_work',
        'engagement_type',
        'requirements_status',
        'required_actions',
        'estimated_professional_fee',
        'estimated_government_fees',
        'estimated_service_support_fee',
        'total_estimated_engagement_value',
        'payment_terms',
        'payment_terms_other',
        'planned_start_date',
        'estimated_duration',
        'estimated_completion_date',
        'client_preferred_completion_date',
        'confirmed_delivery_date',
        'timeline_notes',
        'service_complexity',
        'support_required',
        'complexity_notes',
        'proposal_decision',
        'decline_reason',
        'assigned_consultant',
        'assigned_associate',
        'service_department_unit',
        'consultant_notes',
        'associate_notes',
        'customer_type',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'address',
        'company_name',
        'company_address',
        'position',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'estimated_completion_date' => 'date',
        'client_preferred_completion_date' => 'date',
        'confirmed_delivery_date' => 'date',
        'estimated_professional_fee' => 'decimal:2',
        'estimated_government_fees' => 'decimal:2',
        'estimated_service_support_fee' => 'decimal:2',
        'total_estimated_engagement_value' => 'decimal:2',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DealStage::class, 'stage_id');
    }
}
