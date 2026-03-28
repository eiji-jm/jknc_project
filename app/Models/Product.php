<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'product_type',
        'linked_service_id',
        'deal_id',
        'product_area',
        'product_area_other',
        'product_description',
        'product_inclusions',
        'category',
        'pricing_type',
        'price',
        'cost',
        'is_discountable',
        'tax_type',
        'sku',
        'inventory_type',
        'stock_qty',
        'unit',
        'status',
        'owner_id',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'custom_field_values',
    ];

    protected $casts = [
        'product_area' => 'array',
        'is_discountable' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'custom_field_values' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'linked_service_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
}
