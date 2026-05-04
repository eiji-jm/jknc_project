<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransferCertificate extends Model
{
    protected $fillable = [
        'installment_id',
        'source_certificate_id',
        'issuance_request_id',
        'date_uploaded',
        'uploaded_by',
        'corporation_name',
        'company_reg_no',
        'certificate_type',
        'stock_number',
        'stockholder_name',
        'issued_to',
        'issued_to_type',
        'par_value',
        'number',
        'amount',
        'amount_in_words',
        'date_issued',
        'released_at',
        'president',
        'corporate_secretary',
        'document_path',
        'status',
    ];

    protected $casts = [
        'date_uploaded' => 'date',
        'date_issued' => 'date',
        'released_at' => 'datetime',
        'par_value' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Certificate -> Installment (inverse).
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(StockTransferInstallment::class, 'installment_id');
    }

    public function sourceCertificate(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_certificate_id');
    }

    public function derivedCertificates(): HasMany
    {
        return $this->hasMany(self::class, 'source_certificate_id');
    }

    public function issuanceRequests(): HasMany
    {
        return $this->hasMany(StockTransferIssuanceRequest::class, 'certificate_id');
    }
}
