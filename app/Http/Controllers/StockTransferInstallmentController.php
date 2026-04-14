<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\AuthorizedCapitalStock;
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
    use GeneratesPdfPreview;

    public function index()
    {
        $installments = StockTransferInstallment::latest()->get();

        $defaultParValue = null;
        if (Schema::hasTable('authorized_capital_stocks')) {
            $defaultParValue = AuthorizedCapitalStock::query()->latest()->value('par_value');
        }

        $indexShareholders = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->orderBy('family_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($ledger) use ($defaultParValue) {
                $fullName = trim(collect([
                    $ledger->first_name,
                    $ledger->middle_name,
                    $ledger->family_name,
                ])->filter()->implode(' '));

                return [
                    'id' => $ledger->id,
                    'name' => $fullName,
                    // Keep showing current base reference in UI if needed,
                    // but new subscriptions will still get a NEW stock number on save.
                    'stock_number' => $ledger->certificate_no,
                    'shares' => $ledger->shares,
                    'par_value' => $defaultParValue,
                ];
            })
            ->filter(fn($row) => !empty($row['name']))
            ->values();

        return view('corporate.stock-transfer-book.installment', compact(
            'installments',
            'indexShareholders',
            'defaultParValue'
        ));
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
        if (!$request->has('installment_mode')) {
            $request->merge([
                'installment_mode' => 'stock_subscribe',
            ]);
        }

        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');

        $ledger = $this->resolveLedger(
            $data['stock_number'] ?? null,
            $data['subscriber'] ?? null
        );

        if (!$ledger) {
            return back()->withErrors([
                'subscriber' => 'Stockholder must exist in the Index before adding an installment.',
            ])->withInput();
        }

        $holderName = $this->resolveHolderName($ledger, $data['subscriber'] ?? null);

        // IMPORTANT:
        // Every NEW subscription gets a NEW stock number,
        // even if the same shareholder already bought before.
        $stockNumber = $this->nextStockNumber();

        $data['stock_number'] = $stockNumber;
        $data['subscriber'] = $holderName;
        $data['installment_date'] = !empty($data['installment_date'])
            ? $data['installment_date']
            : now()->toDateString();

        if (empty($data['no_shares']) && !empty($ledger->shares)) {
            $data['no_shares'] = (int) $ledger->shares;
        }

        if ((empty($data['par_value']) || (float) $data['par_value'] <= 0) && Schema::hasTable('authorized_capital_stocks')) {
            $latestPar = AuthorizedCapitalStock::query()->latest()->value('par_value');
            if ($latestPar !== null) {
                $data['par_value'] = (float) $latestPar;
            }
        }

        $data = $this->applyInstallmentFinancials($data);

        if (!$this->hasInstallmentParValueColumn()) {
            unset($data['par_value']);
        }

        if (!$this->hasInstallmentPaymentColumns()) {
            unset($data['payment_date'], $data['payment_amount'], $data['payment_remarks']);
        }

        $mode = $data['installment_mode'] ?? 'stock_subscribe';
        unset($data['installment_mode']);

        $data['schedule'] = [
            'mode' => $mode,
            'installments' => $this->buildInstallmentRows($data),
        ];

        $data['status'] = 'unpaid';

        $installment = null;

        DB::transaction(function () use (&$installment, $data) {
            $installment = StockTransferInstallment::create($data);

            $journal = StockTransferJournal::create([
                'entry_date' => $installment->installment_date ?? now()->toDateString(),
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => 'Stock subscription recorded',
                'no_shares' => (int) ($installment->no_shares ?? 0),
                'transaction_type' => 'Issuance',
                'certificate_no' => $installment->stock_number,
                'shareholder' => $installment->subscriber,
                'remarks' => 'Auto-generated from installment subscription.',
                'status' => 'unpaid',
            ]);

            $installment->journal_id = $journal->id;
            $installment->save();
        });

        return redirect()
            ->route('stock-transfer-book.installment.show', $installment)
            ->with('success', 'Installment plan added and journal entry recorded.');
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
        $paymentRows = $this->buildPreviewPaymentRows($stockTransferInstallment);
        [$installmentRows, $totalPaid, $remainingBalance, $nextPaymentRow] = $this->buildTrackedInstallmentRows($stockTransferInstallment, $paymentRows);
        $paidInstallmentCount = collect($installmentRows)->where('status', 'Paid')->count();
        $remainingInstallmentCount = max(collect($installmentRows)->count() - $paidInstallmentCount, 0);
        $canRecordPreviewPayment = !$stockTransferInstallment->isCancelled() && !empty($nextPaymentRow);
        $individualInstallmentSheetRows = $this->buildIndividualInstallmentSheetRows($stockTransferInstallment, $paymentRows);

        $generatedPreviewPath = $this->generatePdfPreview(
            'corporate.stock-transfer-book.installment-pdf',
            [
                'installment' => $stockTransferInstallment,
                'installmentRows' => $installmentRows,
                'paymentRows' => $paymentRows,
                'individualInstallmentSheetRows' => $individualInstallmentSheetRows,
            ],
            'generated-previews/stock-transfer-book/installments/' . ($stockTransferInstallment->stock_number ?: $stockTransferInstallment->id) . '.pdf'
        );

        return view('corporate.stock-transfer-book.installment-preview', [
            'installment' => $stockTransferInstallment,
            'generatedPreviewUrl' => $generatedPreviewPath ? route('uploads.show', ['path' => $generatedPreviewPath]) : null,
            'relatedCertificates' => $relatedCertificates,
            'relatedJournals' => $relatedJournals,
            'relatedLedgers' => $relatedLedgers,
            'holderName' => $holderName,
            'paymentRows' => $paymentRows,
            'installmentRows' => $installmentRows,
            'individualInstallmentSheetRows' => $individualInstallmentSheetRows,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
            'paidInstallmentCount' => $paidInstallmentCount,
            'remainingInstallmentCount' => $remainingInstallmentCount,
            'nextPaymentRow' => $nextPaymentRow,
            'canRecordPreviewPayment' => $canRecordPreviewPayment,
            'backRoute' => route('stock-transfer-book.installment'),
            'editRoute' => route('stock-transfer-book.installment.edit', $stockTransferInstallment),
        ]);
    }

    public function recordPreviewPayment(Request $request, StockTransferInstallment $stockTransferInstallment)
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'payment_scope' => ['required', 'string', 'in:next,all_remaining'],
            'payment_remarks' => ['nullable', 'string'],
        ]);

        if ($stockTransferInstallment->isCancelled()) {
            return redirect()
                ->route('stock-transfer-book.installment.show', $stockTransferInstallment)
                ->with('warning', 'Cancelled installments cannot accept additional payments.');
        }

        $paymentRows = $this->buildPreviewPaymentRows($stockTransferInstallment);
        [$installmentRows,,, $nextPaymentRow] = $this->buildTrackedInstallmentRows($stockTransferInstallment, $paymentRows);

        if (empty($nextPaymentRow)) {
            return redirect()
                ->route('stock-transfer-book.installment.show', $stockTransferInstallment)
                ->with('warning', 'All scheduled installments are already paid.');
        }

        $ledger = $this->resolvePaymentLedgerForInstallment($stockTransferInstallment);
        if (!$ledger) {
            return redirect()
                ->route('stock-transfer-book.installment.show', $stockTransferInstallment)
                ->with('warning', 'Unable to find the stockholder ledger needed to post this payment.');
        }

        $paymentTargets = $data['payment_scope'] === 'all_remaining'
            ? collect($installmentRows)->filter(fn(array $row) => $row['status'] !== 'Paid')->values()
            : collect([$nextPaymentRow]);

        DB::transaction(function () use ($stockTransferInstallment, $ledger, $data, $paymentTargets) {
            $paymentRemarks = trim((string) ($data['payment_remarks'] ?? ''));

            $paymentTargets->each(function (array $targetRow) use ($stockTransferInstallment, $ledger, $data, $paymentRemarks) {
                $scheduleNote = 'Installment ' . $targetRow['no'];

                if (!empty($targetRow['due_date_raw'])) {
                    $scheduleNote .= ' due ' . $targetRow['due_date_raw'];
                }

                $this->recordPayment($stockTransferInstallment, $ledger, [
                    'payment_date' => $data['payment_date'],
                    'payment_amount' => $targetRow['amount_value'],
                    'payment_remarks' => $paymentRemarks !== ''
                        ? $paymentRemarks . ' | ' . $scheduleNote
                        : $scheduleNote . ' payment recorded from installment preview.',
                ]);
            });
        });

        return redirect()
            ->route('stock-transfer-book.installment.show', $stockTransferInstallment)
            ->with('success', $data['payment_scope'] === 'all_remaining'
                ? 'All remaining installments were recorded as paid. Certificate stock and voucher were generated automatically.'
                : 'Installment payment recorded.');
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
        $data = $this->applyInstallmentFinancials($data, $stockTransferInstallment);
        unset($data['installment_mode']);

        if (!$this->hasInstallmentParValueColumn()) {
            unset($data['par_value']);
        }

        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferInstallment->document_path);
        if (!$this->hasInstallmentPaymentColumns()) {
            unset($data['payment_date'], $data['payment_amount'], $data['payment_remarks']);
        }

        if (isset($data['status'])) {
            unset($data['status']);
        }

        $stockTransferInstallment->update($data);

        return redirect()->route('stock-transfer-book.installment')->with('success', 'Installment plan updated.');
    }

    public function destroy(StockTransferInstallment $stockTransferInstallment)
    {
        return $this->cancelInstallment(request(), $stockTransferInstallment);
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
            'cancellation_other_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $cancellationRules = $this->installmentCancellationRules($stockTransferInstallment);
        $selectedReason = (string) ($data['cancellation_reason'] ?? '');
        if (!($cancellationRules[$selectedReason]['allowed'] ?? false)) {
            return back()->withErrors([
                'cancellation_reason' => $cancellationRules[$selectedReason]['message'] ?? 'This cancellation type is not allowed for the selected installment.',
            ])->withInput();
        }

        $hasPayment = $stockTransferInstallment->paymentCount() > 0;

        if (!$stockTransferInstallment->isFullyPaid() && !$hasPayment) {
            return redirect()->route('stock-transfer-book.installment')
                ->with('warning', 'Cannot cancel: installment has no recorded payments.');
        }

        $cancellationJournal = null;

        DB::transaction(function () use ($stockTransferInstallment, $data, &$cancellationJournal) {
            $detailParts = [
                'Cancellation Date: ' . $data['cancellation_date'],
                'Effective Date: ' . $data['cancellation_effective_date'],
                'Reason: ' . $data['cancellation_reason'],
            ];

            if (!empty($data['cancellation_other_reason'])) {
                $detailParts[] = 'Others: ' . $data['cancellation_other_reason'];
            }

            $stockTransferInstallment->status = 'cancelled';
            $stockTransferInstallment->cancellation_date = $data['cancellation_date'];
            $stockTransferInstallment->cancellation_effective_date = $data['cancellation_effective_date'];
            $stockTransferInstallment->cancellation_reason = $data['cancellation_reason'];
            $stockTransferInstallment->cancellation_types = null;
            $stockTransferInstallment->cancellation_other_reason = $data['cancellation_other_reason'] ?? null;
            $stockTransferInstallment->save();

            $originalJournal = $stockTransferInstallment->journal
                ?: StockTransferJournal::query()
                ->where(function ($query) use ($stockTransferInstallment) {
                    if ($stockTransferInstallment->stock_number) {
                        $query->orWhere('certificate_no', $stockTransferInstallment->stock_number);
                    }
                    $this->applyNameTokens($query, $stockTransferInstallment->subscriber, ['shareholder']);
                })
                ->where('transaction_type', '!=', 'Cancellation')
                ->latest('entry_date')
                ->latest('id')
                ->first();

            $cancelledShares = (int) (
                $originalJournal?->no_shares
                ?? $stockTransferInstallment->issuedShareTotal()
                ?? $stockTransferInstallment->no_shares
                ?? 0
            );

            $cancellationJournal = StockTransferJournal::create([
                'entry_date' => $data['cancellation_date'],
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => 'Installment cancelled',
                'no_shares' => $cancelledShares,
                'transaction_type' => 'Cancellation',
                'certificate_no' => $stockTransferInstallment->stock_number,
                'shareholder' => $stockTransferInstallment->subscriber,
                'remarks' => 'Auto-generated from installment cancellation. ' . implode(' | ', $detailParts),
                'status' => 'voided',
            ]);

            $stockTransferInstallment->journal_id = $cancellationJournal->id;
            $stockTransferInstallment->save();

            $certificate = $stockTransferInstallment->certificate;
            if ($certificate && $certificate->status !== 'voided') {
                $certificate->status = 'voided';
                $certificate->save();
            }
        });

        if ($cancellationJournal) {
            return redirect()
                ->route('stock-transfer-book.journal.show', $cancellationJournal)
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
            ['name' => 'par_value', 'label' => 'PAR', 'type' => 'number', 'step' => '0.01'],
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
            'installment_mode' => ['nullable', 'string', 'in:stock_subscribe,stock_payment,both'],
            'stock_number' => ['nullable', 'string', 'max:255'],
            'subscriber' => ['nullable', 'string', 'max:255'],
            'installment_date' => ['nullable', 'date'],
            'no_shares' => ['nullable', 'integer'],
            'no_installments' => ['nullable', 'integer', 'min:1'],
            'par_value' => ['nullable', 'numeric'],
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
        if (!empty($subscriber)) {
            $query = StockTransferLedger::query()->whereNull('journal_id');
            $query->where(function ($sub) use ($subscriber) {
                $this->applyNameTokens($sub, $subscriber, ['first_name', 'middle_name', 'family_name']);
            });

            $ledger = $query->latest()->first();
            if ($ledger) {
                return $ledger;
            }
        }

        if (!empty($stockNumber)) {
            return StockTransferLedger::query()
                ->whereNull('journal_id')
                ->where('certificate_no', $stockNumber)
                ->latest()
                ->first();
        }

        return null;
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

    private function resolvePaymentLedgerForInstallment(StockTransferInstallment $installment): ?StockTransferLedger
    {
        $ledger = $this->resolveLedger($installment->stock_number, $installment->subscriber);
        if ($ledger) {
            return $ledger;
        }

        return StockTransferLedger::query()
            ->where(function ($query) use ($installment) {
                if ($installment->stock_number) {
                    $query->orWhere('certificate_no', $installment->stock_number);
                }
                $this->applyNameTokens($query, $installment->subscriber, ['first_name', 'middle_name', 'family_name']);
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

        return 'unpaid';
    }

    private function applyInstallmentFinancials(array $data, ?StockTransferInstallment $existingInstallment = null): array
    {
        $mode = $data['installment_mode'] ?? $existingInstallment?->installmentMode() ?? 'stock_subscribe';
        if ($mode === 'stock_payment') {
            return $data;
        }

        $shares = (int) ($data['no_shares'] ?? $existingInstallment?->no_shares ?? 0);
        $installmentCount = max((int) ($data['no_installments'] ?? $existingInstallment?->no_installments ?? 0), 0);
        $parValue = $data['par_value'] ?? $existingInstallment?->par_value;

        $parValue = ($parValue === null || $parValue === '') ? null : (float) $parValue;
        if ($parValue !== null) {
            $data['par_value'] = $parValue;
        }

        if ($shares > 0 && $parValue !== null) {
            $data['total_value'] = round($shares * $parValue, 2);
        } else {
            $data['total_value'] = 0;
        }

        if ($installmentCount > 0 && !empty($data['total_value'])) {
            $data['installment_amount'] = round(((float) $data['total_value']) / $installmentCount, 2);
        } else {
            $data['installment_amount'] = 0;
        }

        return $data;
    }

    private function recordPayment(StockTransferInstallment $installment, StockTransferLedger $ledger, array $paymentData): void
    {
        $schedule = $this->markNextInstallmentAsPaid($installment, $paymentData);

        $totalPaid = (float) collect($schedule['installments'] ?? [])->sum(
            fn(array $row) => !empty($row['paymentDate']) ? (float) ($row['amount'] ?? 0) : 0
        );

        $nextStatus = $this->determineInstallmentStatus(
            $installment->installment_date?->toDateString(),
            $totalPaid,
            $installment->total_value,
        );

        StockTransferJournal::create([
            'entry_date' => $paymentData['payment_date'],
            'journal_no' => $this->nextJournalNo(),
            'ledger_folio' => $this->nextLedgerFolio(),
            'particulars' => 'Installment payment recorded',
            'no_shares' => 0,
            'transaction_type' => 'Issuance',
            'certificate_no' => $installment->stock_number,
            'shareholder' => $installment->subscriber,
            'remarks' => $paymentData['payment_remarks'] ?: 'Auto-generated from installment payment.',
            'status' => $nextStatus,
        ]);

        $schedule['mode'] = 'stock_subscribe';

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
        $installment->refresh();

        $this->autoCreateDraftCertificateAndVoucherIfFullyPaid($installment, $ledger);
    }

    private function autoCreateDraftCertificateAndVoucherIfFullyPaid(StockTransferInstallment $installment, ?StockTransferLedger $ledger = null): void
    {
        if (!$installment->isFullyPaid()) {
            return;
        }

        $ledger = $ledger ?: $this->resolvePaymentLedgerForInstallment($installment);

        $parValue = $installment->par_value;
        if (($parValue === null || $parValue === '') && Schema::hasTable('authorized_capital_stocks')) {
            $parValue = AuthorizedCapitalStock::query()->latest()->value('par_value');
        }

        $numberOfShares = (int) ($installment->no_shares ?? $ledger?->shares ?? 0);
        $amount = $installment->total_value;

        if (($amount === null || $amount === '') && $parValue !== null && $numberOfShares > 0) {
            $amount = (float) $parValue * $numberOfShares;
        }

        $stockCertificate = StockTransferCertificate::query()
            ->whereNull('source_certificate_id')
            ->where('stock_number', $installment->stock_number)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'voided');
            })
            ->latest()
            ->first();

        if (!$stockCertificate) {
            $stockCertificate = StockTransferCertificate::create([
                'installment_id' => $installment->id,
                'date_uploaded' => now()->toDateString(),
                'uploaded_by' => auth()->user()?->name,
                'corporation_name' => null,
                'company_reg_no' => null,
                'certificate_type' => 'COS',
                'stock_number' => $installment->stock_number,
                'stockholder_name' => $installment->subscriber,
                'issued_to' => $installment->subscriber,
                'issued_to_type' => 'Stockholder',
                'par_value' => $parValue,
                'number' => $numberOfShares,
                'amount' => $amount,
                'amount_in_words' => $this->amountToWords($amount),
                'date_issued' => now()->toDateString(),
                'president' => null,
                'corporate_secretary' => null,
                'status' => 'draft',
                'document_path' => null,
            ]);
        } elseif (!$stockCertificate->installment_id) {
            $stockCertificate->installment_id = $installment->id;
            $stockCertificate->save();
        }

        $existingVoucher = StockTransferCertificate::query()
            ->whereNotNull('source_certificate_id')
            ->where('source_certificate_id', $stockCertificate->id)
            ->where('stock_number', $installment->stock_number)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'voided');
            })
            ->latest()
            ->first();

        if (!$existingVoucher) {
            StockTransferCertificate::create([
                'source_certificate_id' => $stockCertificate->id,
                'issuance_request_id' => null,
                'installment_id' => null,
                'date_uploaded' => now()->toDateString(),
                'uploaded_by' => auth()->user()?->name,
                'corporation_name' => $stockCertificate->corporation_name,
                'company_reg_no' => $stockCertificate->company_reg_no,
                'certificate_type' => 'CV',
                'stock_number' => $stockCertificate->stock_number,
                'stockholder_name' => $stockCertificate->stockholder_name,
                'issued_to' => $stockCertificate->stockholder_name,
                'issued_to_type' => 'Stockholder',
                'par_value' => $stockCertificate->par_value,
                'number' => $stockCertificate->number,
                'amount' => $stockCertificate->amount,
                'amount_in_words' => $stockCertificate->amount_in_words,
                'date_issued' => now()->toDateString(),
                'released_at' => now(),
                'president' => $stockCertificate->president,
                'corporate_secretary' => $stockCertificate->corporate_secretary,
                'document_path' => null,
                'status' => 'released',
            ]);
        }
    }

    private function amountToWords($amount): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $amount = round((float) $amount, 2);
        $whole = (int) floor($amount);
        $cents = (int) round(($amount - $whole) * 100);

        $words = $this->integerToWords($whole) . ' Pesos';
        if ($cents > 0) {
            $words .= ' and ' . $this->integerToWords($cents) . ' Centavos';
        }

        return trim($words . ' Only');
    }

    private function integerToWords(int $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($number < 20) {
            return $number === 0 ? 'Zero' : $ones[$number];
        }

        if ($number < 100) {
            return trim($tens[intdiv($number, 10)] . ' ' . $ones[$number % 10]);
        }

        if ($number < 1000) {
            return trim($ones[intdiv($number, 100)] . ' Hundred ' . $this->integerToWords($number % 100));
        }

        if ($number < 1000000) {
            return trim($this->integerToWords(intdiv($number, 1000)) . ' Thousand ' . $this->integerToWords($number % 1000));
        }

        if ($number < 1000000000) {
            return trim($this->integerToWords(intdiv($number, 1000000)) . ' Million ' . $this->integerToWords($number % 1000000));
        }

        return trim($this->integerToWords(intdiv($number, 1000000000)) . ' Billion ' . $this->integerToWords($number % 1000000000));
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

        return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
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

    private function buildPreviewPaymentRows(StockTransferInstallment $installment)
    {
        return collect($installment->scheduledInstallmentRows())
            ->filter(fn(array $row) => !empty($row['paymentDate']))
            ->values()
            ->map(function (array $row, int $index) use ($installment) {
                $paymentDate = $row['paymentDate'] ?? null;
                $amountPaid = (float) ($row['amount'] ?? 0);

                return [
                    'payment_date' => $paymentDate,
                    'payment_date_display' => $this->formatPreviewDate($paymentDate),
                    'posted_date' => $this->formatPreviewDate($paymentDate),
                    'installment_no' => $row['no'] ?? ($index + 1),
                    'amount_paid' => $amountPaid,
                    'holder_name' => $installment->subscriber,
                ];
            });
    }

    private function buildTrackedInstallmentRows(StockTransferInstallment $installment, $paymentRows): array
    {
        $paymentRows = collect($paymentRows ?? [])->values();
        $scheduledRows = collect($installment->scheduledInstallmentRows())->values();

        $installmentRows = $scheduledRows
            ->map(function (array $row, int $index) use ($paymentRows) {
                $paymentRow = $paymentRows->get($index);
                $amountValue = (float) ($row['amount'] ?? 0);

                return [
                    'no' => $row['no'] ?? ($index + 1),
                    'due_date' => $this->formatPreviewDate($row['dueDate'] ?? null),
                    'due_date_raw' => $row['dueDate'] ?? null,
                    'scheduled_amount' => $this->formatPreviewAmount($amountValue),
                    'amount_value' => $amountValue,
                    'payment_date' => $paymentRow['payment_date_display'] ?? '',
                    'payment_date_raw' => $paymentRow['payment_date'] ?? null,
                    'status' => $paymentRow ? 'Paid' : 'Pending',
                ];
            })
            ->values();

        $totalPaid = (float) $installmentRows
            ->filter(fn(array $row) => $row['status'] === 'Paid')
            ->sum('amount_value');

        if ($paymentRows->count() > $installmentRows->count()) {
            $totalPaid += (float) $paymentRows->slice($installmentRows->count())->sum('amount_paid');
        }

        $expectedTotal = (float) ($installment->total_value ?? 0);
        if ($expectedTotal <= 0) {
            $expectedTotal = (float) $installmentRows->sum('amount_value');
        }

        $remainingBalance = max($expectedTotal - $totalPaid, 0);
        $nextPaymentRow = $installmentRows->first(fn(array $row) => $row['status'] !== 'Paid');

        return [$installmentRows, $totalPaid, $remainingBalance, $nextPaymentRow];
    }

    private function buildIndividualInstallmentSheetRows(StockTransferInstallment $installment, $paymentRows): array
    {
        $paymentRows = collect($paymentRows ?? [])->values();
        $firstPaymentRow = $paymentRows->first();
        $remainingPaymentRows = $paymentRows->slice(1)->values();
        $blankRows = max(26 - (1 + $remainingPaymentRows->count()), 0);

        $rows = [[
            'subscribed_date' => optional($installment->installment_date)->format('m/d/Y'),
            'subscribed_shares' => $installment->no_shares ?? '',
            'subscribed_installments' => $installment->no_installments ?? '',
            'subscribed_value' => number_format((float) ($installment->total_value ?? 0), 2, '.', ''),
            'payment_date' => $firstPaymentRow['payment_date_display'] ?? '',
            'posted_date' => $firstPaymentRow['posted_date'] ?? '',
            'installment_no' => $firstPaymentRow['installment_no'] ?? '',
            'amount_paid' => $firstPaymentRow ? number_format((float) ($firstPaymentRow['amount_paid'] ?? 0), 2, '.', '') : '',
        ]];

        foreach ($remainingPaymentRows as $paymentRow) {
            $rows[] = [
                'subscribed_date' => '',
                'subscribed_shares' => '',
                'subscribed_installments' => '',
                'subscribed_value' => '',
                'payment_date' => $paymentRow['payment_date_display'] ?? '',
                'posted_date' => $paymentRow['posted_date'] ?? '',
                'installment_no' => $paymentRow['installment_no'] ?? '',
                'amount_paid' => number_format((float) ($paymentRow['amount_paid'] ?? 0), 2, '.', ''),
            ];
        }

        for ($i = 0; $i < $blankRows; $i++) {
            $rows[] = [
                'subscribed_date' => '',
                'subscribed_shares' => '',
                'subscribed_installments' => '',
                'subscribed_value' => '',
                'payment_date' => '',
                'posted_date' => '',
                'installment_no' => '',
                'amount_paid' => '',
            ];
        }

        return $rows;
    }

    private function markNextInstallmentAsPaid(StockTransferInstallment $installment, array $paymentData): array
    {
        $schedule = $installment->normalizedSchedule();
        $paymentDate = !empty($paymentData['payment_date'])
            ? Carbon::parse($paymentData['payment_date'])->toDateString()
            : now()->toDateString();
        $markedPaid = false;

        $schedule['installments'] = collect($installment->scheduledInstallmentRows())
            ->values()
            ->map(function (array $row, int $index) use (&$markedPaid, $paymentDate) {
                $currentPaymentDate = $row['paymentDate'] ?? null;
                $isPaid = !empty($currentPaymentDate);

                if (!$isPaid && !$markedPaid) {
                    $markedPaid = true;
                    $currentPaymentDate = $paymentDate;
                    $isPaid = true;
                }

                return [
                    'no' => $row['no'] ?? ($index + 1),
                    'dueDate' => $row['dueDate'] ?? null,
                    'amount' => number_format((float) ($row['amount'] ?? 0), 2, '.', ''),
                    'status' => $isPaid ? 'Paid' : 'Pending',
                    'paymentDate' => $currentPaymentDate,
                ];
            })
            ->all();

        return $schedule;
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

    private function hasInstallmentParValueColumn(): bool
    {
        return Schema::hasColumn('stock_transfer_installments', 'par_value');
    }

    private function installmentCancellationRules(StockTransferInstallment $installment): array
    {
        $remainingBalance = max((float) ($installment->total_value ?? 0) - $installment->paymentTotal(), 0);
        $installmentsRemaining = max((int) ($installment->no_installments ?? 0) - $installment->paymentCount(), 0);
        $paidAmount = $installment->paymentTotal();

        return [
            'Delinquent' => [
                'allowed' => $remainingBalance > 0 && $installmentsRemaining > 0,
                'message' => 'Delinquent cancellation is only allowed when there is remaining balance and unpaid installments.',
            ],
            'Buy-back' => [
                'allowed' => $paidAmount > 0,
                'message' => 'Buy-back cancellation requires payment history.',
            ],
            'Redemption' => [
                'allowed' => false,
                'message' => 'Redemption is not enabled for this installment flow yet.',
            ],
            'Treasury Cancellation' => [
                'allowed' => false,
                'message' => 'Treasury Cancellation is not enabled for this installment flow yet.',
            ],
            'Capital Reduction' => [
                'allowed' => false,
                'message' => 'Capital Reduction is restricted and not enabled from installment preview.',
            ],
            'Others' => [
                'allowed' => true,
                'message' => null,
            ],
        ];
    }
}
