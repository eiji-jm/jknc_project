<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomField extends Model
{
    protected $fillable = [
        'field_type',
        'field_name',
        'field_key',
        'is_required',
        'options',
        'lookup_module',
        'default_value',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];
}
