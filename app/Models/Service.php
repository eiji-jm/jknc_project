<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'company_id',
        'service_id',
        'service_name',
        'service_description',
        'service_activity_output',
        'service_area',
        'service_area_other',
        'category',
        'frequency',
        'schedule_rule',
        'deadline',
        'reminder_lead_time',
        'requirements',
        'requirement_category',
        'engagement_structure',
        'is_recurring',
        'unit',
        'rate_per_unit',
        'min_units',
        'max_cap',
        'price_fee',
        'cost_of_service',
        'assigned_unit',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'custom_field_values',
    ];

    protected $casts = [
        'service_area' => 'array',
        'requirements' => 'array',
        'engagement_structure' => 'array',
        'custom_field_values' => 'array',
        'is_recurring' => 'boolean',
        'rate_per_unit' => 'decimal:2',
        'max_cap' => 'decimal:2',
        'price_fee' => 'decimal:2',
        'cost_of_service' => 'decimal:2',
        'deadline' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
