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

class StockTransferCertificateController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;

    public function index()
    {
        $certificates = StockTransferCertificate::latest()->get();

        $indexShareholders = StockTransferLedger::query()
            ->get(['first_name', 'middle_name', 'family_name'])
            ->map(function ($ledger) {
                return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
            })
            ->filter()
            ->unique()
            ->values();

        return view('corporate.stock-transfer-book.certificates', compact('certificates', 'indexShareholders'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'New Certificate',
            'action' => route('stock-transfer-book.certificates.store'),
            'method' => 'POST',
            'cancelRoute' => route('stock-transfer-book.certificates'),
            'fields' => $this->fields(),
            'item' => new StockTransferCertificate(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['status'] = $data['status'] ?? 'active';

        $ledger = $this->resolveLedger($data['stock_number'] ?? null, $data['stockholder_name'] ?? null);
        if (!$ledger) {
            return back()->withErrors([
                'stock_number' => 'Stockholder must exist in the Index before issuing a certificate.',
            ])->withInput();
        }

        if (empty($data['stock_number'])) {
            $data['stock_number'] = $ledger->certificate_no ?: $this->nextStockNumber();
            if (!$ledger->certificate_no) {
                $ledger->certificate_no = $data['stock_number'];
                $ledger->save();
            }
        }

        if (empty($data['stockholder_name'])) {
            $data['stockholder_name'] = trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
        }

        if (empty($data['date_uploaded'])) {
            $data['date_uploaded'] = now()->toDateString();
        }

        if (empty($data['date_issued'])) {
            $data['date_issued'] = now()->toDateString();
        }

        if (empty($data['number']) && $ledger->shares) {
            $data['number'] = $ledger->shares;
        }

        $installment = null;
        if (!empty($data['stock_number'])) {
            $installment = StockTransferInstallment::query()
                ->where('stock_number', $data['stock_number'])
                ->latest()
                ->first();
        }

        if ($installment && !$installment->isFullyPaid()) {
            return back()->withErrors([
                'stock_number' => 'Cannot issue certificate: installment is not fully paid.',
            ])->withInput();
        }

        if ($installment && $installment->certificate && $installment->certificate->status !== 'voided') {
            return back()->withErrors([
                'stock_number' => 'Cannot issue certificate: an active certificate already exists for this installment.',
            ])->withInput();
        }

        if ($installment) {
            $data['installment_id'] = $installment->id;
        }

        StockTransferCertificate::create($data);

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Certificate created.');
    }

    public function show(StockTransferCertificate $stockTransferCertificate)
    {
        $stockNumber = $stockTransferCertificate->stock_number;
        $stockholderName = $stockTransferCertificate->stockholder_name;

        $relatedJournals = StockTransferJournal::query()
            ->where(function ($query) use ($stockNumber, $stockholderName) {
                if ($stockNumber) {
                    $query->orWhere('certificate_no', $stockNumber);
                }
                $this->applyNameTokens($query, $stockholderName, ['shareholder']);
            })
            ->where(function ($query) {
                $query->whereNull('transaction_type')
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation', 'Payment']);
            })
            ->latest()
            ->get();

        $relatedLedgers = StockTransferLedger::query()
            ->where(function ($query) use ($stockNumber, $stockholderName) {
                if ($stockNumber) {
                    $query->orWhere('certificate_no', $stockNumber);
                }
                $this->applyNameTokens($query, $stockholderName, ['first_name', 'middle_name', 'family_name']);
            })
            ->latest()
            ->get();

        $relatedInstallments = StockTransferInstallment::query()
            ->where(function ($query) use ($stockNumber, $stockholderName) {
                if ($stockNumber) {
                    $query->orWhere('stock_number', $stockNumber);
                }
                $this->applyNameTokens($query, $stockholderName, ['subscriber']);
            })
            ->latest()
            ->get();

        return view('corporate.stock-transfer-book.certificate-preview', [
            'certificate' => $stockTransferCertificate,
            'relatedJournals' => $relatedJournals,
            'relatedLedgers' => $relatedLedgers,
            'relatedInstallments' => $relatedInstallments,
            'backRoute' => route('stock-transfer-book.certificates'),
            'editRoute' => route('stock-transfer-book.certificates.edit', $stockTransferCertificate),
        ]);
    }

    public function edit(StockTransferCertificate $stockTransferCertificate)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Certificate',
            'action' => route('stock-transfer-book.certificates.update', $stockTransferCertificate),
            'method' => 'PUT',
            'cancelRoute' => route('stock-transfer-book.certificates'),
            'fields' => $this->fields(),
            'item' => $stockTransferCertificate,
        ]);
    }

    public function update(Request $request, StockTransferCertificate $stockTransferCertificate)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferCertificate->document_path);

        $stockTransferCertificate->update($data);

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Certificate updated.');
    }

    public function destroy(StockTransferCertificate $stockTransferCertificate)
    {
        $stockTransferCertificate->status = 'voided';
        $stockTransferCertificate->save();

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Certificate voided.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'corporation_name', 'label' => 'Corporation Name', 'type' => 'text'],
            ['name' => 'company_reg_no', 'label' => 'Company Reg. No.', 'type' => 'text'],
            ['name' => 'stock_number', 'label' => 'Stock Number', 'type' => 'text'],
            ['name' => 'stockholder_name', 'label' => 'Name of Stockholder', 'type' => 'text'],
            ['name' => 'par_value', 'label' => 'PAR', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'number', 'label' => 'Number', 'type' => 'number'],
            ['name' => 'amount', 'label' => 'Amount (PhP)', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'amount_in_words', 'label' => 'Amount in Words', 'type' => 'text'],
            ['name' => 'date_issued', 'label' => 'Date Issued', 'type' => 'date'],
            ['name' => 'president', 'label' => 'President', 'type' => 'text'],
            ['name' => 'corporate_secretary', 'label' => 'Corporate Secretary', 'type' => 'text'],
            ['name' => 'document_path', 'label' => 'Upload Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'date_uploaded' => ['nullable', 'date'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'corporation_name' => ['nullable', 'string', 'max:255'],
            'company_reg_no' => ['nullable', 'string', 'max:255'],
            'stock_number' => ['nullable', 'string', 'max:255'],
            'stockholder_name' => ['nullable', 'string', 'max:255'],
            'par_value' => ['nullable', 'numeric'],
            'number' => ['nullable', 'integer'],
            'amount' => ['nullable', 'numeric'],
            'amount_in_words' => ['nullable', 'string', 'max:255'],
            'date_issued' => ['nullable', 'date'],
            'president' => ['nullable', 'string', 'max:255'],
            'corporate_secretary' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,cancelled,voided,completed'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveLedger(?string $stockNumber, ?string $stockholderName): ?StockTransferLedger
    {
        $query = StockTransferLedger::query();
        $query->where(function ($sub) use ($stockNumber, $stockholderName) {
            if ($stockNumber) {
                $sub->orWhere('certificate_no', $stockNumber);
            }
            $this->applyNameTokens($sub, $stockholderName, ['first_name', 'middle_name', 'family_name']);
        });

        return $query->first();
    }
}
