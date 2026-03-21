<?php

namespace App\Http\Controllers\Concerns;

use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;

trait GeneratesStockTransferIds
{
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
}
