<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockTransferInstallmentController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;

    public function index()
    {
        $installments = StockTransferInstallment::latest()->get();

        $indexShareholders = StockTransferLedger::query()
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
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['status'] = $data['status'] ?? 'pending';

        $ledger = $this->resolveLedger($data['stock_number'] ?? null, $data['subscriber'] ?? null);
        if (!$ledger) {
            return back()->withErrors([
                'stock_number' => 'Stockholder must exist in the Index before adding an installment.',
            ])->withInput();
        }

        if (empty($data['stock_number'])) {
            $data['stock_number'] = $ledger->certificate_no ?: $this->nextStockNumber();
            if (!$ledger->certificate_no) {
                $ledger->certificate_no = $data['stock_number'];
                $ledger->save();
            }
        }

        if (empty($data['subscriber'])) {
            $data['subscriber'] = trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
        }

        if (empty($data['installment_date'])) {
            $data['installment_date'] = now()->toDateString();
        }

        DB::transaction(function () use ($data) {
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
                'status' => 'active',
            ]);

            StockTransferInstallment::create(array_merge($data, [
                'journal_id' => $journal->id,
            ]));
        });

        return redirect()->route('stock-transfer-book.installment')->with('success', 'Installment plan added.');
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
            ->where(function ($query) {
                $query->whereNull('transaction_type')
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation', 'Payment']);
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

        return view('corporate.stock-transfer-book.installment-preview', [
            'installment' => $stockTransferInstallment,
            'relatedCertificates' => $relatedCertificates,
            'relatedJournals' => $relatedJournals,
            'relatedLedgers' => $relatedLedgers,
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
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferInstallment->document_path);

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
            ['name' => 'stock_number', 'label' => 'Stock Number', 'type' => 'text'],
            ['name' => 'subscriber', 'label' => 'Subscriber', 'type' => 'text'],
            ['name' => 'installment_date', 'label' => 'Date', 'type' => 'date'],
            ['name' => 'no_shares', 'label' => 'No. Shares', 'type' => 'number'],
            ['name' => 'no_installments', 'label' => 'No. of Installments', 'type' => 'number'],
            ['name' => 'total_value', 'label' => 'Total Value (PhP)', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'installment_amount', 'label' => 'Per Installment', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
            ['name' => 'document_path', 'label' => 'Upload Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'stock_number' => ['nullable', 'string', 'max:255'],
            'subscriber' => ['nullable', 'string', 'max:255'],
            'installment_date' => ['nullable', 'date'],
            'no_shares' => ['nullable', 'integer'],
            'no_installments' => ['nullable', 'integer'],
            'total_value' => ['nullable', 'numeric'],
            'installment_amount' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'in:pending,on-going,active,cancelled,voided,completed'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveLedger(?string $stockNumber, ?string $subscriber): ?StockTransferLedger
    {
        $query = StockTransferLedger::query();
        $query->where(function ($sub) use ($stockNumber, $subscriber) {
            if ($stockNumber) {
                $sub->orWhere('certificate_no', $stockNumber);
            }
            $this->applyNameTokens($sub, $subscriber, ['first_name', 'middle_name', 'family_name']);
        });

        return $query->first();
    }
}
