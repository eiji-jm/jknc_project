<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransferJournal extends Model
{
    protected $fillable = [
        'entry_date',
        'journal_no',
        'ledger_folio',
        'particulars',
        'no_shares',
        'transaction_type',
        'certificate_no',
        'shareholder',
        'remarks',
        'document_path',
        'status',
        'reversal_of_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::created(function (StockTransferJournal $journal) {
            // Automatically generate a ledger entry for every journal transaction.
            $journal->createLedgerEntryIfNeeded();
        });
    }

    /**
     * Journal -> Ledger (one-to-many).
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(StockTransferLedger::class, 'journal_id');
    }

    /**
     * Journal -> Installments (one-to-many).
     */
    public function installments(): HasMany
    {
        return $this->hasMany(StockTransferInstallment::class, 'journal_id');
    }

    /**
     * Reversal linkage (journal self-reference).
     */
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(StockTransferJournal::class, 'reversal_of_id');
    }

    /**
     * Journal -> Reversal entries (one-to-many).
     */
    public function reversalEntries(): HasMany
    {
        return $this->hasMany(StockTransferJournal::class, 'reversal_of_id');
    }

    public function createLedgerEntryIfNeeded(): void
    {
        if ($this->ledgers()->exists()) {
            return;
        }

        if (!in_array($this->transaction_type, ['Issuance', 'Cancellation'], true)) {
            return;
        }

        if (!$this->shareholder && !$this->certificate_no) {
            return;
        }

        $ledgerStatus = match ($this->transaction_type) {
            'Cancellation' => 'cancelled',
            'Payment' => 'completed',
            default => $this->status ?? 'active',
        };

        $this->ledgers()->create([
            // Ledger schema stores names by parts. We only have the full name here,
            // so we keep it in family_name as a best-effort fallback.
            'family_name' => $this->shareholder ?? 'Unknown',
            'first_name' => '',
            'middle_name' => null,
            'shares' => $this->no_shares,
            'certificate_no' => $this->certificate_no,
            'date_registered' => $this->entry_date,
            'status' => $ledgerStatus,
        ]);
    }
}
