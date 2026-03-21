<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\StockTransferInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockTransferJournalController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;

    public function index()
    {
        $journals = StockTransferJournal::query()
            ->where(function ($query) {
                $query->whereNull('transaction_type')
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation', 'Payment']);
            })
            ->latest()
            ->get();

        $indexShareholders = StockTransferLedger::query()
            ->get(['first_name', 'middle_name', 'family_name'])
            ->map(function ($ledger) {
                return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
            })
            ->filter()
            ->unique()
            ->values();

        return view('corporate.stock-transfer-book.journal', compact('journals', 'indexShareholders'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Journal Transaction',
            'action' => route('stock-transfer-book.journal.store'),
            'method' => 'POST',
            'cancelRoute' => route('stock-transfer-book.journal'),
            'fields' => $this->fields(),
            'item' => new StockTransferJournal(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['status'] = $data['status'] ?? 'active';

        $ledger = $this->resolveLedger($data['certificate_no'] ?? null, $data['shareholder'] ?? null);
        if (!$ledger) {
            return back()->withErrors([
                'certificate_no' => 'Stockholder must exist in the Index before adding a journal entry.',
            ])->withInput();
        }

        if (empty($data['certificate_no'])) {
            $data['certificate_no'] = $ledger->certificate_no ?: $this->nextStockNumber();
            if (!$ledger->certificate_no) {
                $ledger->certificate_no = $data['certificate_no'];
                $ledger->save();
            }
        }

        if (empty($data['shareholder'])) {
            $data['shareholder'] = trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
        }

        if (empty($data['entry_date'])) {
            $data['entry_date'] = now()->toDateString();
        }

        if (empty($data['journal_no'])) {
            $data['journal_no'] = $this->nextJournalNo();
        }

        if (empty($data['ledger_folio'])) {
            $data['ledger_folio'] = $this->nextLedgerFolio();
        }

        $installment = null;
        if (!empty($data['certificate_no'])) {
            $installment = StockTransferInstallment::query()
                ->where('stock_number', $data['certificate_no'])
                ->latest()
                ->first();
        }

        $isPaymentEntry = ($data['transaction_type'] ?? '') === 'Payment'
            || Str::contains(Str::lower($data['particulars'] ?? ''), 'payment')
            || Str::contains(Str::lower($data['remarks'] ?? ''), 'payment');

        if ($installment && $installment->isCancelled() && $isPaymentEntry) {
            return back()->withErrors([
                'certificate_no' => 'Cannot record payment: installment has been cancelled.',
            ])->withInput();
        }

        StockTransferJournal::create($data);

        return redirect()->route('stock-transfer-book.journal')->with('success', 'Journal entry added.');
    }

    public function show(StockTransferJournal $stockTransferJournal)
    {
        $certificateNo = $stockTransferJournal->certificate_no;
        $shareholder = $stockTransferJournal->shareholder;

        $journalEntries = StockTransferJournal::query()
            ->where(function ($query) use ($certificateNo, $shareholder) {
                if ($certificateNo) {
                    $query->orWhere('certificate_no', $certificateNo);
                }
                $this->applyNameTokens($query, $shareholder, ['shareholder']);
            })
            ->where(function ($query) {
                $query->whereNull('transaction_type')
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation', 'Payment']);
            })
            ->latest()
            ->get();

        $relatedCertificates = StockTransferCertificate::query()
            ->where(function ($query) use ($certificateNo, $shareholder) {
                if ($certificateNo) {
                    $query->orWhere('stock_number', $certificateNo);
                }
                $this->applyNameTokens($query, $shareholder, ['stockholder_name']);
            })
            ->latest()
            ->get();

        $relatedLedgers = StockTransferLedger::query()
            ->where(function ($query) use ($certificateNo, $shareholder) {
                if ($certificateNo) {
                    $query->orWhere('certificate_no', $certificateNo);
                }
                $this->applyNameTokens($query, $shareholder, ['first_name', 'middle_name', 'family_name']);
            })
            ->latest()
            ->get();

        $relatedInstallments = StockTransferInstallment::query()
            ->where(function ($query) use ($certificateNo, $shareholder) {
                if ($certificateNo) {
                    $query->orWhere('stock_number', $certificateNo);
                }
                $this->applyNameTokens($query, $shareholder, ['subscriber']);
            })
            ->latest()
            ->get();

        return view('corporate.stock-transfer-book.journal-preview', [
            'journal' => $stockTransferJournal,
            'journalEntries' => $journalEntries,
            'relatedCertificates' => $relatedCertificates,
            'relatedLedgers' => $relatedLedgers,
            'relatedInstallments' => $relatedInstallments,
            'backRoute' => route('stock-transfer-book.journal'),
            'editRoute' => route('stock-transfer-book.journal.edit', $stockTransferJournal),
        ]);
    }

    public function edit(StockTransferJournal $stockTransferJournal)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Journal Transaction',
            'action' => route('stock-transfer-book.journal.update', $stockTransferJournal),
            'method' => 'PUT',
            'cancelRoute' => route('stock-transfer-book.journal'),
            'fields' => $this->fields(),
            'item' => $stockTransferJournal,
        ]);
    }

    public function update(Request $request, StockTransferJournal $stockTransferJournal)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferJournal->document_path);

        $stockTransferJournal->update($data);

        return redirect()->route('stock-transfer-book.journal')->with('success', 'Journal entry updated.');
    }

    public function destroy(StockTransferJournal $stockTransferJournal)
    {
        $stockTransferJournal->status = 'voided';
        $stockTransferJournal->save();

        return redirect()->route('stock-transfer-book.journal')->with('success', 'Journal entry voided.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'entry_date', 'label' => 'Date', 'type' => 'date'],
            ['name' => 'journal_no', 'label' => 'Journal No.', 'type' => 'text'],
            ['name' => 'ledger_folio', 'label' => 'Ledger Folio', 'type' => 'text'],
            ['name' => 'particulars', 'label' => 'Particulars', 'type' => 'textarea'],
            ['name' => 'no_shares', 'label' => 'No. Shares', 'type' => 'number'],
            ['name' => 'transaction_type', 'label' => 'Transaction Type', 'type' => 'select', 'options' => ['Issuance', 'Cancellation', 'Payment']],
            ['name' => 'certificate_no', 'label' => 'Certificate No.', 'type' => 'text'],
            ['name' => 'shareholder', 'label' => 'Shareholder', 'type' => 'text'],
            ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea'],
            ['name' => 'document_path', 'label' => 'Upload Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'entry_date' => ['nullable', 'date'],
            'journal_no' => ['nullable', 'string', 'max:255'],
            'ledger_folio' => ['nullable', 'string', 'max:255'],
            'particulars' => ['nullable', 'string'],
            'no_shares' => ['nullable', 'integer'],
            'transaction_type' => ['nullable', 'string', 'in:Issuance,Cancellation,Payment'],
            'certificate_no' => ['nullable', 'string', 'max:255'],
            'shareholder' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,cancelled,voided,completed'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveLedger(?string $certificateNo, ?string $shareholder): ?StockTransferLedger
    {
        $query = StockTransferLedger::query();
        $query->where(function ($sub) use ($certificateNo, $shareholder) {
            if ($certificateNo) {
                $sub->orWhere('certificate_no', $certificateNo);
            }
            $this->applyNameTokens($sub, $shareholder, ['first_name', 'middle_name', 'family_name']);
        });

        return $query->first();
    }
}
