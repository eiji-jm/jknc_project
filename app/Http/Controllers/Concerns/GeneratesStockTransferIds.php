<?php

namespace App\Http\Controllers\Concerns;

use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;

trait GeneratesStockTransferIds
{
    protected function stockNumberExists(?string $stockNumber): bool
    {
        if (!is_string($stockNumber) || trim($stockNumber) === '') {
            return false;
        }

        $stockNumber = trim($stockNumber);
        $sources = [
            [StockTransferInstallment::class, 'stock_number'],
            [StockTransferCertificate::class, 'stock_number'],
            [StockTransferLedger::class, 'certificate_no'],
            [StockTransferJournal::class, 'certificate_no'],
        ];

        foreach ($sources as [$model, $column]) {
            if ($model::query()->where($column, $stockNumber)->exists()) {
                return true;
            }
        }

        return false;
    }

    protected function nextSequenceFor(string $modelClass, string $column, string $prefix, int $pad = 4): string
    {
        $values = $modelClass::query()
            ->where($column, 'like', $prefix . '%')
            ->pluck($column)
            ->all();

        $max = 0;
        foreach ($values as $value) {
            if (!is_string($value)) {
                continue;
            }
            if (preg_match('/(\d+)$/', $value, $matches)) {
                $num = (int) $matches[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        $next = $max + 1;

        return $prefix . str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
    }

    protected function nextJournalNo(): string
    {
        return $this->nextSequenceFor(StockTransferJournal::class, 'journal_no', 'JNL-');
    }

    protected function nextLedgerFolio(): string
    {
        return $this->nextSequenceFor(StockTransferJournal::class, 'ledger_folio', 'LED-');
    }

    protected function nextStockNumber(): string
    {
        $prefix = 'STK-';
        $sources = [
            [StockTransferInstallment::class, 'stock_number'],
            [StockTransferCertificate::class, 'stock_number'],
            [StockTransferLedger::class, 'certificate_no'],
            [StockTransferJournal::class, 'certificate_no'],
        ];

        $max = 0;
        foreach ($sources as [$model, $column]) {
            $values = $model::query()
                ->where($column, 'like', $prefix . '%')
                ->pluck($column)
                ->all();
            foreach ($values as $value) {
                if (!is_string($value)) {
                    continue;
                }
                if (preg_match('/(\d+)$/', $value, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $max) {
                        $max = $num;
                    }
                }
            }
        }

        $next = $max + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    protected function nextIssuanceRequestReference(): string
    {
        $modelClass = \App\Models\StockTransferIssuanceRequest::class;
        $column = 'reference_no';
        $prefix = 'REQ-';

        do {
            $next = $this->nextSequenceFor($modelClass, $column, $prefix);
        } while ($modelClass::query()->where($column, $next)->exists());

        return $next;
    }

    protected function nextAvailableStockNumber(?string $preferred = null): string
    {
        $preferred = is_string($preferred) ? trim($preferred) : '';
        if ($preferred !== '' && !$this->stockNumberExists($preferred)) {
            return $preferred;
        }

        do {
            $next = $this->nextStockNumber();
        } while ($this->stockNumberExists($next));

        return $next;
    }
}
