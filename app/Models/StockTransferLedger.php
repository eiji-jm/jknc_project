<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferLedger extends Model
{
    protected $fillable = [
        'journal_id',
        'family_name',
        'first_name',
        'middle_name',
        'nationality',
        'address',
        'tin',
        'email',
        'phone',
        'shares',
        'certificate_no',
        'date_registered',
        'status',
        'document_path',
    ];

    protected $casts = [
        'date_registered' => 'date',
    ];

    /**
     * Ledger -> Journal (inverse).
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(StockTransferJournal::class, 'journal_id');
    }
}
