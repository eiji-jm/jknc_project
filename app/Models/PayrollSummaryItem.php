<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSummaryItem extends Model
{
    protected $fillable = [
        'payroll_summary_id',
        'item_type',
        'category',
        'name',
        'amount',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function summary()
    {
        return $this->belongsTo(PayrollSummary::class, 'payroll_summary_id');
    }
}