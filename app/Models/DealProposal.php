<?php

namespace App\Models;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProposal extends Model
{
    protected $fillable = [
        'deal_id',
        'reference_id',
        'crud_id',
        'proposal_date',
        'location',
        'service_type',
        'scope_of_service',
        'what_you_will_receive',
        'our_proposal_text',
        'requirements_sole',
        'requirements_juridical',
        'requirements_optional',
        'price_regular',
        'price_discount',
        'price_subtotal',
        'price_tax',
        'price_total',
        'price_down',
        'price_balance',
        'prepared_by_name',
        'prepared_by_id',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'price_regular' => 'decimal:2',
        'price_discount' => 'decimal:2',
        'price_subtotal' => 'decimal:2',
        'price_tax' => 'decimal:2',
        'price_total' => 'decimal:2',
        'price_down' => 'decimal:2',
        'price_balance' => 'decimal:2',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
