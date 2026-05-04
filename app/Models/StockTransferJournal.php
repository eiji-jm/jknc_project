<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

        $sourceLedger = $this->resolveSourceLedger();

        $this->ledgers()->create([
            'family_name' => $sourceLedger?->family_name ?? $this->extractFamilyName(),
            'first_name' => $sourceLedger?->first_name ?? $this->extractFirstName(),
            'middle_name' => $sourceLedger?->middle_name,
            'nationality' => $sourceLedger?->nationality,
            'address' => $sourceLedger?->address,
            'tin' => $sourceLedger?->tin,
            'email' => $sourceLedger?->email,
            'phone' => $sourceLedger?->phone,
            'shares' => $this->no_shares,
            'certificate_no' => $this->certificate_no,
            'date_registered' => $this->entry_date,
        ]);
    }

    private function resolveSourceLedger(): ?StockTransferLedger
    {
        if ($this->certificate_no) {
            $ledger = StockTransferLedger::query()
                ->whereNull('journal_id')
                ->where('certificate_no', $this->certificate_no)
                ->first();

            if ($ledger) {
                return $ledger;
            }
        }

        $shareholder = trim((string) $this->shareholder);
        if ($shareholder === '') {
            return null;
        }

        return StockTransferLedger::query()
            ->whereNull('journal_id')
            ->get()
            ->first(function (StockTransferLedger $ledger) use ($shareholder) {
                $fullName = trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));

                return Str::lower($fullName) === Str::lower($shareholder);
            });
    }

    private function extractFirstName(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->shareholder)) ?: [];

        return $parts[0] ?? '';
    }

    private function extractFamilyName(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->shareholder)) ?: [];

        if (count($parts) <= 1) {
            return $parts[0] ?? 'Unknown';
        }

        return $parts[count($parts) - 1];
    }
}
