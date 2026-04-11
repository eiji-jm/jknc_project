<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Minute;
use App\Models\Notice;
use App\Models\Resolution;
use App\Models\SecretaryCertificate;

trait GeneratesCorporateDocumentNumbers
{
    protected function nextNoticeNumber(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return $this->nextSequenceFor(Notice::class, 'notice_number', $year . '-', 3);
    }

    protected function nextMinutesRef(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return $this->nextSequenceFor(Minute::class, 'minutes_ref', 'MIN-' . $year . '-', 3);
    }

    protected function nextResolutionNumber(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return $this->nextSequenceFor(Resolution::class, 'resolution_no', 'RES-' . $year . '-', 3);
    }

    protected function nextSecretaryCertificateNumber(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return $this->nextSequenceFor(SecretaryCertificate::class, 'certificate_no', 'SEC-' . $year . '-', 3);
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

        return $prefix . str_pad((string) ($max + 1), $pad, '0', STR_PAD_LEFT);
    }
}
