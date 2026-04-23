<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesMarketingIda extends Model
{
    protected $table = 'sales_marketing_idas';

    protected $fillable = [
        'deal_id',
        'condeal_ref_no',
        'client_name',
        'business_name',
        'service_area',
        'product_engagement_structure',
        'deal_value',
        'workflow_status',
        'created_by',
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
    ];

    public function allocations()
    {
        return $this->hasMany(SalesMarketingIdaAllocation::class, 'ida_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
}