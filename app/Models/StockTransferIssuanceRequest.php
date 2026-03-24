<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferIssuanceRequest extends Model
{
    protected $fillable = [
        'reference_no',
        'requested_at',
        'request_type',
        'issuance_type',
        'requester',
        'received_by',
        'issued_by',
        'certificate_id',
        'status',
        'notes',
        'document_path',
        'approved_at',
        'approved_by',
        'journal_id',
        'ledger_id',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(StockTransferCertificate::class, 'certificate_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(StockTransferJournal::class, 'journal_id');
    }

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(StockTransferLedger::class, 'ledger_id');
    }
}
