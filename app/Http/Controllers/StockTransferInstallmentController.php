<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\Stockholder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockTransferInstallmentController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;

    public function index()
    {
        $installments = StockTransferInstallment::latest()->get();

        $indexShareholders = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->get(['first_name', 'middle_name', 'family_name'])
            ->map(function ($ledger) {
                return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
            })
            ->filter()
            ->unique()
            ->values();

        return view('corporate.stock-transfer-book.installment', compact('installments', 'indexShareholders'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Installment Plan',
            'action' => route('stock-transfer-book.installment.store'),
            'method' => 'POST',
            'cancelRoute' => route('stock-transfer-book.installment'),
            'fields' => $this->fields(),
            'item' => new StockTransferInstallment(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $mode = $data['installment_mode'];
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $paymentData = [
            'payment_date' => $data['payment_date'] ?? null,
            'payment_amount' => $data['payment_amount'] ?? null,
            'payment_remarks' => $data['payment_remarks'] ?? null,
        ];

        unset($data['installment_mode']);

        if (!$this->hasInstallmentPaymentColumns()) {
            unset($data['payment_date'], $data['payment_amount'], $data['payment_remarks']);
        }

        if ($mode === 'stock_payment') {
            return $this->storePaymentOnly($data, $paymentData);
        }

        $ledger = $this->resolveLedger($data['stock_number'] ?? null, $data['subscriber'] ?? null);
        if (!$ledger) {
            return back()->withErrors([
                'stock_number' => 'Stockholder must exist in the Index before adding an installment.',
            ])->withInput();
        }

        $data['stock_number'] = $this->nextAvailableStockNumber($data['stock_number'] ?? null);
        if (!$ledger->certificate_no) {
            $ledger->certificate_no = $data['stock_number'];
            $ledger->save();
        }

        $data['subscriber'] = $this->resolveHolderName($ledger, $data['subscriber'] ?? null);
        $data['installment_date'] = $data['installment_date'] ?? now()->toDateString();
        $data['schedule'] = [
            'mode' => $mode,
            'installments' => $this->buildInstallmentRows($data),
        ];
        $data['status'] = $this->determineInstallmentStatus(
            $data['installment_date'] ?? null,
            $mode === 'both' ? (float) ($paymentData['payment_amount'] ?? 0) : 0.0,
            $data['total_value'] ?? null,
        );

        $installment = null;
        DB::transaction(function () use ($data, $paymentData, $ledger, &$installment) {
            $journal = StockTransferJournal::create([
                'entry_date' => $data['installment_date'] ?? now()->toDateString(),
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => 'Installment plan created',
                'no_shares' => $data['no_shares'] ?? null,
                'transaction_type' => 'Issuance',
                'certificate_no' => $data['stock_number'] ?? null,
                'shareholder' => $data['subscriber'] ?? null,
                'remarks' => 'Auto-generated from installment creation.',
                'status' => $data['status'],
            ]);

            $installment = StockTransferInstallment::create(array_merge($data, [
                'journal_id' => $journal->id,
            ]));

            if (!empty($paymentData['payment_date']) && !empty($paymentData['payment_amount'])) {
                $this->recordPayment($installment, $ledger, $paymentData);
                $installment->refresh();
            }
        });

        return redirect()
            ->route('stock-transfer-book.installment.show', $installment)
            ->with('success', 'Installment plan added.');
    }

    public function show(StockTransferInstallment $stockTransferInstallment)
    {
        $stockNumber = $stockTransferInstallment->stock_number;
        $subscriber = $stockTransferInstallment->subscriber;

        $relatedCertificates = StockTransferCertificate::query()
            ->where(function ($query) use ($stockNumber, $subscriber) {
                if ($stockNumber) {
                    $query->orWhere('stock_number', $stockNumber);
                }
                $this->applyNameTokens($query, $subscriber, ['stockholder_name']);
            })
            ->latest()
            ->get();

        $relatedJournals = StockTransferJournal::query()
            ->where(function ($query) use ($stockNumber, $subscriber) {
                if ($stockNumber) {
                    $query->orWhere('certificate_no', $stockNumber);
                }
                $this->applyNameTokens($query, $subscriber, ['shareholder']);
            })
            ->latest()
            ->get();

        $relatedLedgers = StockTransferLedger::query()
            ->where(function ($query) use ($stockNumber, $subscriber) {
                if ($stockNumber) {
                    $query->orWhere('certificate_no', $stockNumber);
                }
                $this->applyNameTokens($query, $subscriber, ['first_name', 'middle_name', 'family_name']);
            })
            ->latest()
            ->get();

        $holderName = $this->resolvePreviewHolderName($stockTransferInstallment, $relatedLedgers);
        $paymentRows = $this->buildPreviewPaymentRows($stockTransferInstallment, $relatedJournals);
        $installmentRows = collect($stockTransferInstallment->installmentRows())
            ->map(function (array $row, int $index) use ($paymentRows) {
                $paidRow = $paymentRows->get($index);

                return [
                    'no' => $row['no'] ?? ($index + 1),
                    'due_date' => $this->formatPreviewDate($row['dueDate'] ?? null),
                    'amount' => $this->formatPreviewAmount($row['amount'] ?? null),
                    'status' => $paidRow ? 'Paid' : ($row['status'] ?? 'Pending'),
                    'paid_date' => $paidRow['payment_date'] ?? null,
                ];
            })
            ->values();

        $totalPaid = (float) $paymentRows->sum('amount_paid');
        $remainingBalance = max((float) ($stockTransferInstallment->total_value ?? 0) - $totalPaid, 0);

        return view('corporate.stock-transfer-book.installment-preview', [
            'installment' => $stockTransferInstallment,
            'relatedCertificates' => $relatedCertificates,
            'relatedJournals' => $relatedJournals,
            'relatedLedgers' => $relatedLedgers,
            'holderName' => $holderName,
            'paymentRows' => $paymentRows,
            'installmentRows' => $installmentRows,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
            'backRoute' => route('stock-transfer-book.installment'),
            'editRoute' => route('stock-transfer-book.installment.edit', $stockTransferInstallment),
        ]);
    }

    public function edit(StockTransferInstallment $stockTransferInstallment)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Installment Plan',
            'action' => route('stock-transfer-book.installment.update', $stockTransferInstallment),
            'method' => 'PUT',
            'cancelRoute' => route('stock-transfer-book.installment'),
            'fields' => $this->fields(),
            'item' => $stockTransferInstallment,
        ]);
    }

    public function update(Request $request, StockTransferInstallment $stockTransferInstallment)
    {
        if (!$request->has('installment_mode')) {
            $request->merge([
                'installment_mode' => $stockTransferInstallment->installmentMode(),
            ]);
        }

        $data = $this->validateData($request);
        unset($data['installment_mode']);

        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferInstallment->document_path);
        if (!$this->hasInstallmentPaymentColumns()) {
            unset($data['payment_date'], $data['payment_amount'], $data['payment_remarks']);
        }

        $stockTransferInstallment->update($data);

        return redirect()->route('stock-transfer-book.installment')->with('success', 'Installment plan updated.');
    }

    public function destroy(StockTransferInstallment $stockTransferInstallment)
    {
        return $this->cancelInstallment($stockTransferInstallment);
    }

    public function cancelInstallment(Request $request, StockTransferInstallment $stockTransferInstallment)
    {
        if ($stockTransferInstallment->isCancelled()) {
            return redirect()->route('stock-transfer-book.installment')->with('warning', 'Installment is already cancelled.');
        }

        $data = $request->validate([
            'cancellation_date' => ['required', 'date'],
            'cancellation_effective_date' => ['required', 'date'],
            'cancellation_reason' => ['required', 'string', 'in:Delinquent,Buy-back,Redemption,Treasury Cancellation,Capital Reduction,Others'],
            'cancellation_types' => ['required', 'array', 'min:1'],
            'cancellation_types.*' => ['required', 'string', 'in:Delinquent,Buy-back,Redemption,Treasury Cancellation,Capital Reduction,Others'],
            'cancellation_other_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $hasPayment = StockTransferJournal::query()
            ->where('certificate_no', $stockTransferInstallment->stock_number)
            ->where(function ($query) {
                $query->where('transaction_type', 'Payment')
                    ->orWhere('particulars', 'like', '%payment%')
                    ->orWhere('remarks', 'like', '%payment%');
            })
            ->exists();

        if (!$stockTransferInstallment->isFullyPaid() && !$hasPayment) {
            return redirect()->route('stock-transfer-book.installment')
                ->with('warning', 'Cannot cancel: installment has no recorded payments.');
        }

        DB::transaction(function () use ($stockTransferInstallment, $data) {
            $typeList = implode(', ', $data['cancellation_types']);
            $detailParts = [
                'Cancellation Date: ' . $data['cancellation_date'],
                'Effective Date: ' . $data['cancellation_effective_date'],
                'Reason: ' . $data['cancellation_reason'],
                'Types: ' . $typeList,
            ];

            if (!empty($data['cancellation_other_reason'])) {
                $detailParts[] = 'Others: ' . $data['cancellation_other_reason'];
            }

            $stockTransferInstallment->status = 'cancelled';
            $stockTransferInstallment->cancellation_date = $data['cancellation_date'];
            $stockTransferInstallment->cancellation_effective_date = $data['cancellation_effective_date'];
            $stockTransferInstallment->cancellation_reason = $data['cancellation_reason'];
            $stockTransferInstallment->cancellation_types = $data['cancellation_types'];
            $stockTransferInstallment->cancellation_other_reason = $data['cancellation_other_reason'] ?? null;
            $stockTransferInstallment->save();

            $originalJournal = $stockTransferInstallment->journal;
            $reversalJournal = null;
            if ($originalJournal) {
                $originalJournal->status = 'voided';
                $originalJournal->save();

                $reversalJournal = StockTransferJournal::create([
                    'entry_date' => now()->toDateString(),
                    'journal_no' => $this->nextJournalNo(),
                    'ledger_folio' => $this->nextLedgerFolio(),
                    'particulars' => 'Reversal of journal #' . $originalJournal->id,
                    'no_shares' => $originalJournal->no_shares,
                    'transaction_type' => 'Cancellation',
                    'certificate_no' => $originalJournal->certificate_no,
                    'shareholder' => $originalJournal->shareholder,
                    'remarks' => 'Auto-generated reversal entry. ' . implode(' | ', $detailParts),
                    'status' => 'cancelled',
                    'reversal_of_id' => $originalJournal->id,
                ]);
            }

            $certificate = $stockTransferInstallment->certificate;
            if ($certificate && $certificate->status !== 'voided') {
                $certificate->status = 'voided';
                $certificate->save();
            }

            StockTransferLedger::query()
                ->where('certificate_no', $stockTransferInstallment->stock_number)
                ->update(['status' => 'cancelled']);

            if ($originalJournal) {
                StockTransferLedger::query()
                    ->where('journal_id', $originalJournal->id)
                    ->update(['status' => 'voided']);
            }

            if ($reversalJournal) {
                StockTransferLedger::query()
                    ->where('journal_id', $reversalJournal->id)
                    ->update(['status' => 'cancelled']);
            }
        });

        $latestJournal = StockTransferJournal::query()
            ->where('certificate_no', $stockTransferInstallment->stock_number)
            ->latest()
            ->first();

        if ($latestJournal) {
            return redirect()
                ->route('stock-transfer-book.journal.show', $latestJournal)
                ->with('success', 'Installment plan cancelled.');
        }

        return redirect()->route('stock-transfer-book.journal')->with('success', 'Installment plan cancelled.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'installment_mode', 'label' => 'Installment Mode', 'type' => 'text'],
            ['name' => 'stock_number', 'label' => 'Stock Number', 'type' => 'text'],
            ['name' => 'subscriber', 'label' => 'Subscriber', 'type' => 'text'],
            ['name' => 'installment_date', 'label' => 'Date', 'type' => 'date'],
            ['name' => 'no_shares', 'label' => 'No. Shares', 'type' => 'number'],
            ['name' => 'no_installments', 'label' => 'No. of Installments', 'type' => 'number'],
            ['name' => 'total_value', 'label' => 'Total Value (PhP)', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'installment_amount', 'label' => 'Per Installment', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'payment_date', 'label' => 'Payment Date', 'type' => 'date'],
            ['name' => 'payment_amount', 'label' => 'Payment Amount', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'payment_remarks', 'label' => 'Payment Remarks', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
            ['name' => 'document_path', 'label' => 'Upload Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'installment_mode' => ['required', 'string', 'in:stock_subscribe,stock_payment,both'],
            'stock_number' => ['nullable', 'string', 'max:255'],
            'subscriber' => ['nullable', 'string', 'max:255'],
            'installment_date' => ['nullable', 'date'],
            'no_shares' => ['nullable', 'integer'],
            'no_installments' => ['nullable', 'integer'],
            'total_value' => ['nullable', 'numeric'],
            'installment_amount' => ['nullable', 'numeric'],
            'payment_date' => ['nullable', 'date'],
            'payment_amount' => ['nullable', 'numeric'],
            'payment_remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveLedger(?string $stockNumber, ?string $subscriber): ?StockTransferLedger
    {
        $query = StockTransferLedger::query();
        $query->whereNull('journal_id');
        $query->where(function ($sub) use ($stockNumber, $subscriber) {
            if ($stockNumber) {
                $sub->orWhere('certificate_no', $stockNumber);
            }
            $this->applyNameTokens($sub, $subscriber, ['first_name', 'middle_name', 'family_name']);
        });

        return $query->first();
    }

    private function resolveInstallment(?string $stockNumber, ?string $subscriber): ?StockTransferInstallment
    {
        return StockTransferInstallment::query()
            ->where(function ($query) use ($stockNumber, $subscriber) {
                if ($stockNumber) {
                    $query->orWhere('stock_number', $stockNumber);
                }
                $this->applyNameTokens($query, $subscriber, ['subscriber']);
            })
            ->latest()
            ->first();
    }

    private function determineInstallmentStatus($installmentDate, $paymentAmount, $expectedAmount): string
    {
        $paid = (float) ($paymentAmount ?? 0);
        $expected = (float) ($expectedAmount ?? 0);

        if ($paid > 0 && ($expected <= 0 || $paid >= $expected)) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partial';
        }

        return 'overdue';
    }

    private function storePaymentOnly(array $data, array $paymentData)
    {
        $installment = $this->resolveInstallment($data['stock_number'] ?? null, $data['subscriber'] ?? null);
        if (!$installment) {
            return back()->withErrors([
                'stock_number' => 'Choose an existing stock subscription before recording a stock payment.',
            ])->withInput();
        }

        $ledger = $this->resolveLedger($installment->stock_number, $installment->subscriber);
        if (!$ledger) {
            return back()->withErrors([
                'stock_number' => 'The selected stock subscription must still exist in the Index.',
            ])->withInput();
        }

        if (empty($paymentData['payment_date']) || empty($paymentData['payment_amount'])) {
            return back()->withErrors([
                'payment_amount' => 'Payment date and amount are required for a stock payment.',
            ])->withInput();
        }

        DB::transaction(function () use ($installment, $ledger, $paymentData) {
            $this->recordPayment($installment, $ledger, $paymentData);
        });

        return redirect()
            ->route('stock-transfer-book.installment.show', $installment)
            ->with('success', 'Stock payment recorded.');
    }

    private function recordPayment(StockTransferInstallment $installment, StockTransferLedger $ledger, array $paymentData): void
    {
        $totalPaid = $installment->paymentTotal() + (float) ($paymentData['payment_amount'] ?? 0);
        $nextStatus = $this->determineInstallmentStatus(
            $installment->installment_date?->toDateString(),
            $totalPaid,
            $installment->total_value,
        );

        $paymentJournal = StockTransferJournal::create([
            'entry_date' => $paymentData['payment_date'],
            'journal_no' => $this->nextJournalNo(),
            'ledger_folio' => $this->nextLedgerFolio(),
            'particulars' => 'Stock payment received',
            'no_shares' => (float) $paymentData['payment_amount'],
            'transaction_type' => 'Payment',
            'certificate_no' => $installment->stock_number,
            'shareholder' => $installment->subscriber,
            'remarks' => $paymentData['payment_remarks'] ?: 'Auto-generated from installment stock payment section.',
            'status' => $nextStatus,
        ]);

        $paymentJournal->ledgers()->create([
            'family_name' => $ledger->family_name,
            'first_name' => $ledger->first_name,
            'middle_name' => $ledger->middle_name,
            'nationality' => $ledger->nationality,
            'address' => $ledger->address,
            'tin' => $ledger->tin,
            'email' => $ledger->email,
            'phone' => $ledger->phone,
            'shares' => $installment->no_shares,
            'certificate_no' => $installment->stock_number,
            'date_registered' => $paymentData['payment_date'],
            'status' => $nextStatus,
        ]);

        $schedule = $installment->normalizedSchedule();
        $schedule['mode'] = $schedule['mode'] === 'stock_subscribe' ? 'both' : ($schedule['mode'] ?? 'stock_payment');

        $updates = [
            'status' => $nextStatus,
            'schedule' => $schedule,
        ];

        if ($this->hasInstallmentPaymentColumns()) {
            $updates['payment_date'] = $paymentData['payment_date'];
            $updates['payment_amount'] = $paymentData['payment_amount'];
            $updates['payment_remarks'] = $paymentData['payment_remarks'];
        }

        $installment->update($updates);
    }

    private function buildInstallmentRows(array $data): array
    {
        $count = max((int) ($data['no_installments'] ?? 0), 0);
        $date = !empty($data['installment_date']) ? Carbon::parse($data['installment_date']) : now();
        $amount = (float) ($data['installment_amount'] ?? 0);

        if ($count <= 0) {
            return [];
        }

        return collect(range(1, $count))
            ->map(function (int $index) use ($date, $amount) {
                return [
                    'no' => $index,
                    'dueDate' => $date->copy()->addMonths($index - 1)->toDateString(),
                    'amount' => number_format($amount, 2, '.', ''),
                    'status' => 'Pending',
                ];
            })
            ->all();
    }

    private function resolveHolderName(StockTransferLedger $ledger, ?string $subscriber = null): string
    {
        $candidate = trim((string) $subscriber);
        if ($candidate !== '' && Schema::hasTable('stockholders')) {
            $query = Stockholder::query();
            $this->applyNameTokens($query, $candidate, ['stockholder_name']);
            $match = $query->latest()->first();
            if ($match?->stockholder_name) {
                return $match->stockholder_name;
            }
        }

        return $candidate !== ''
            ? $candidate
            : trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
    }

    private function resolvePreviewHolderName(StockTransferInstallment $installment, $relatedLedgers): string
    {
        if (Schema::hasTable('stockholders') && !empty($installment->subscriber)) {
            $query = Stockholder::query();
            $this->applyNameTokens($query, $installment->subscriber, ['stockholder_name']);
            $match = $query->latest()->first();
            if ($match?->stockholder_name) {
                return $match->stockholder_name;
            }
        }

        $ledger = $relatedLedgers->first();
        if ($ledger) {
            return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
        }

        return $installment->subscriber ?: '-';
    }

    private function buildPreviewPaymentRows(StockTransferInstallment $installment, $relatedJournals)
    {
        return collect($relatedJournals ?? [])
            ->filter(function ($journal) {
                $type = strtolower((string) ($journal->transaction_type ?? ''));
                $particulars = strtolower((string) ($journal->particulars ?? ''));
                $remarks = strtolower((string) ($journal->remarks ?? ''));

                return $type === 'payment'
                    || str_contains($particulars, 'payment')
                    || str_contains($remarks, 'payment');
            })
            ->sortBy('entry_date')
            ->values()
            ->map(function ($journal, int $index) use ($installment) {
                return [
                    'payment_date' => optional($journal->entry_date)->format('m/d/Y'),
                    'posted_date' => optional($journal->entry_date)->format('m/d/Y'),
                    'installment_no' => $index + 1,
                    'amount_paid' => (float) ($journal->no_shares ?? 0),
                    'holder_name' => $installment->subscriber,
                ];
            });
    }

    private function formatPreviewDate(?string $date): string
    {
        if (!$date) {
            return '';
        }

        try {
            return Carbon::parse($date)->format('m/d/Y');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    private function formatPreviewAmount($amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return number_format((float) $amount, 2, '.', '');
    }

    private function hasInstallmentPaymentColumns(): bool
    {
        return Schema::hasColumn('stock_transfer_installments', 'payment_date')
            && Schema::hasColumn('stock_transfer_installments', 'payment_amount')
            && Schema::hasColumn('stock_transfer_installments', 'payment_remarks');
    }
}
