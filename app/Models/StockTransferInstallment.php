<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class StockTransferInstallment extends Model
{
    protected $fillable = [
        'journal_id',
        'stock_number',
        'subscriber',
        'installment_date',
        'no_shares',
        'no_installments',
        'par_value',
        'total_value',
        'installment_amount',
        'payment_date',
        'payment_amount',
        'payment_remarks',
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
        'payment_date' => 'date',
        'cancellation_date' => 'date',
        'cancellation_effective_date' => 'date',
        'par_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'cancellation_types' => 'array',
        'schedule' => 'array',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(StockTransferJournal::class, 'journal_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(StockTransferCertificate::class, 'installment_id');
    }

    public function paymentJournals(): HasMany
    {
        return $this->hasMany(StockTransferJournal::class, 'certificate_no', 'stock_number')
            ->where('transaction_type', 'Issuance');
    }

    public function normalizedSchedule(): array
    {
        $schedule = $this->schedule;

        if (!is_array($schedule)) {
            return ['mode' => 'stock_subscribe', 'installments' => []];
        }

        if (array_is_list($schedule)) {
            return [
                'mode' => 'stock_subscribe',
                'installments' => $schedule,
            ];
        }

        return [
            'mode' => $schedule['mode'] ?? 'stock_subscribe',
            'installments' => is_array($schedule['installments'] ?? null) ? $schedule['installments'] : [],
        ];
    }

    public function installmentMode(): string
    {
        $mode = strtolower((string) ($this->normalizedSchedule()['mode'] ?? 'stock_subscribe'));
        $allowed = ['stock_subscribe', 'stock_payment', 'both'];

        return in_array($mode, $allowed, true) ? $mode : 'stock_subscribe';
    }

    public function installmentRows(): array
    {
        $rows = $this->normalizedSchedule()['installments'] ?? [];
        if (!empty($rows)) {
            return $rows;
        }

        $date = $this->installment_date;
        $count = max((int) ($this->no_installments ?? 0), 0);
        $amount = (float) ($this->installment_amount ?? 0);

        if (!$date instanceof Carbon || $count <= 0) {
            return [];
        }

        return collect(range(1, $count))
            ->map(function (int $index) use ($date, $amount) {
                return [
                    'no' => $index,
                    'dueDate' => $date->copy()->addMonths($index - 1)->format('Y-m-d'),
                    'amount' => number_format($amount, 2, '.', ''),
                    'status' => 'Pending',
                ];
            })
            ->all();
    }

    public function scheduledInstallmentRows(): array
    {
        return collect($this->installmentRows())
            ->values()
            ->map(function (array $row, int $index) {
                return [
                    'no' => (int) ($row['no'] ?? ($index + 1)),
                    'dueDate' => $this->normalizeScheduleDate($row['dueDate'] ?? $row['due_date'] ?? null),
                    'amount' => $this->normalizeScheduleAmount($row['amount'] ?? null),
                    'status' => $row['status'] ?? 'Pending',
                    'paymentDate' => $this->normalizeScheduleDate($row['paymentDate'] ?? $row['payment_date'] ?? null),
                ];
            })
            ->all();
    }

    public function paymentCount(): int
    {
        return collect($this->scheduledInstallmentRows())
            ->filter(fn (array $row) => !empty($row['paymentDate']))
            ->count();
    }

    public function paymentTotal(): float
    {
        return (float) collect($this->scheduledInstallmentRows())
            ->filter(fn (array $row) => !empty($row['paymentDate']))
            ->sum(fn (array $row) => (float) ($row['amount'] ?? 0));
    }

    public function issuedShareTotal(): int
    {
        return (int) ($this->paymentJournals()->latest('entry_date')->latest('id')->value('no_shares') ?? 0);
    }

    public function paymentStatus(): string
    {
        $status = strtolower((string) $this->status);

        if (in_array($status, ['cancelled', 'voided'], true)) {
            return $status;
        }

        $paidAmount = $this->paymentTotal();
        $expectedAmount = (float) ($this->total_value ?: $this->installment_amount ?: 0);

        if ($paidAmount > 0 && ($expectedAmount <= 0 || $paidAmount >= $expectedAmount)) {
            return 'paid';
        }

        if ($paidAmount > 0) {
            return 'partial';
        }

        return 'unpaid';
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
        return $this->paymentStatus() === 'paid';
    }

    private function normalizeScheduleDate($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toDateString();
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeScheduleAmount($value): string
    {
        return number_format((float) ($value ?? 0), 2, '.', '');
    }
}
