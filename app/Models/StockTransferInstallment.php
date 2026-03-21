<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockTransferInstallment extends Model
{
    protected $fillable = [
        'journal_id',
        'stock_number',
        'subscriber',
        'installment_date',
        'no_shares',
        'no_installments',
        'total_value',
        'installment_amount',
        'status',
        'cancellation_date',
        'cancellation_effective_date',
        'cancellation_reason',
        'cancellation_types',
        'cancellation_other_reason',
        'schedule',
        'document_path',
    ];

    protected $casts = [
        'installment_date' => 'date',
        'cancellation_date' => 'date',
        'cancellation_effective_date' => 'date',
        'total_value' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'cancellation_types' => 'array',
        'schedule' => 'array',
    ];

    /**
     * Installment -> Journal (inverse of Journal -> Installments).
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(StockTransferJournal::class, 'journal_id');
    }

    /**
     * Installment -> Certificate (one-to-one).
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(StockTransferCertificate::class, 'installment_id');
    }

    /**
     * Installment -> payment journals matched by stock number.
     */
    public function paymentJournals(): HasMany
    {
        return $this->hasMany(StockTransferJournal::class, 'certificate_no', 'stock_number')
            ->where(function ($query) {
                $query->where('transaction_type', 'Payment')
                    ->orWhere('particulars', 'like', '%payment%')
                    ->orWhere('remarks', 'like', '%payment%');
            });
    }

    public function paymentCount(): int
    {
        return $this->paymentJournals()->count();
    }

    public function paymentStatus(): string
    {
        $status = strtolower((string) $this->status);

        if (in_array($status, ['cancelled', 'voided'], true)) {
            return $status;
        }

        $paymentCount = $this->paymentCount();
        $expectedPayments = (int) ($this->no_installments ?? 0);

        if ($status === 'completed' || ($expectedPayments > 0 && $paymentCount >= $expectedPayments && $paymentCount > 0)) {
            return 'completed';
        }

        if ($paymentCount > 0) {
            return 'on-going';
        }

        return 'pending';
    }

    public function getPaymentStatusAttribute(): string
    {
        return $this->paymentStatus();
    }

    public function isCancelled(): bool
    {
        return in_array($this->paymentStatus(), ['cancelled', 'voided'], true);
    }

    public function isFullyPaid(): bool
    {
        return $this->paymentStatus() === 'completed';
    }
}
