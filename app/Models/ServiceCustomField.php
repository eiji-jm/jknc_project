<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCustomField extends Model
{
    protected $fillable = [
        'field_name',
        'field_key',
        'field_type',
        'is_required',
        'options',
        'default_value',
        'lookup_module',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];
}
