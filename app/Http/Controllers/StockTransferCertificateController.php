<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Models\AuthorizedCapitalStock;
use App\Models\GisRecord;
use App\Models\Stockholder;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferIssuanceRequest;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\StockTransferInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class StockTransferCertificateController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;

    public function index()
    {
        if ($this->hasCertificateWorkflowColumns()) {
            $certificateStocks = StockTransferCertificate::query()
                ->whereNull('source_certificate_id')
                ->latest()
                ->get();

            $certificateVouchers = StockTransferCertificate::query()
                ->whereNotNull('source_certificate_id')
                ->with('sourceCertificate')
                ->latest()
                ->get();
        } else {
            $certificateStocks = StockTransferCertificate::query()
                ->latest()
                ->get();
            $certificateVouchers = collect();
        }

        $issuanceRequests = $this->hasIssuanceRequestsTable()
            ? StockTransferIssuanceRequest::query()->with('certificate')->latest()->get()
            : collect();

        $indexShareholders = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->get(['first_name', 'middle_name', 'family_name'])
            ->map(function ($ledger) {
                return trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
            })
            ->filter()
            ->unique()
            ->values();

        $availableStockNumbers = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->pluck('certificate_no')
            ->merge(
                StockTransferInstallment::query()->pluck('stock_number')
            )
            ->filter()
            ->unique()
            ->values();

        $availableInstallments = StockTransferInstallment::query()
            ->latest('installment_date')
            ->get([
                'stock_number',
                'subscriber',
                'installment_date',
                'no_shares',
                'total_value',
                'installment_amount',
            ]);

        $hasCompanyTables = Schema::hasTable('gis_records');
        $hasAuthorizedCapitalTable = Schema::hasTable('authorized_capital_stocks');
        $hasStockholdersTable = Schema::hasTable('stockholders');

        $latestCompany = $hasCompanyTables ? GisRecord::query()->latest()->first() : null;
        $companyParValue = $hasAuthorizedCapitalTable
            ? AuthorizedCapitalStock::query()->latest()->value('par_value')
            : null;

        if ($companyParValue === null && $latestCompany) {
            $companyParValue = optional($latestCompany?->authorizedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->subscribedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->paidUpCapital()->latest()->first())->par_value;
        }

        $availableInstallments = $availableInstallments->map(function ($installment) use ($latestCompany, $companyParValue, $hasStockholdersTable) {
            $stockholderRecord = null;
            if ($hasStockholdersTable && !empty($installment->subscriber)) {
                $query = Stockholder::query();
                $this->applyNameTokens($query, $installment->subscriber, ['stockholder_name']);
                $stockholderRecord = $query->latest()->first();
            }

            $amount = $installment->total_value;
            if (($amount === null || $amount === '') && $installment->no_shares && $companyParValue) {
                $amount = (float) $installment->no_shares * (float) $companyParValue;
            }

            return (object) [
                'stock_number' => $installment->stock_number,
                'subscriber' => $installment->subscriber,
                'stockholder_name' => $stockholderRecord?->stockholder_name ?: $installment->subscriber,
                'installment_date' => $installment->installment_date,
                'no_shares' => $installment->no_shares,
                'total_value' => $amount,
                'installment_amount' => $installment->installment_amount,
                'corporation_name' => $latestCompany?->corporation_name,
                'company_reg_no' => $latestCompany?->company_reg_no,
                'par_value' => $companyParValue,
                'amount_in_words' => $this->amountToWords($amount),
            ];
        });

        return view('corporate.stock-transfer-book.certificates', [
            'certificateStocks' => $certificateStocks,
            'certificateVouchers' => $certificateVouchers,
            'issuanceRequests' => $issuanceRequests,
            'indexShareholders' => $indexShareholders,
            'availableStockNumbers' => $availableStockNumbers,
            'availableInstallments' => $availableInstallments,
        ]);
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
        $data['certificate_type'] = $data['certificate_type'] ?? 'COS';

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

        DB::transaction(function () use ($data, $ledger) {
            $stockPayload = $data;
            if ($this->hasCertificateWorkflowColumns()) {
                $stockPayload = array_merge($stockPayload, [
                    'issued_to' => $data['stockholder_name'] ?? null,
                    'issued_to_type' => 'Stockholder',
                ]);
            }

            $stock = StockTransferCertificate::create($stockPayload);

            if ($this->hasCertificateWorkflowColumns()) {
                StockTransferCertificate::create([
                    'source_certificate_id' => $stock->id,
                    'date_uploaded' => $stock->date_uploaded,
                    'uploaded_by' => $stock->uploaded_by,
                    'corporation_name' => $stock->corporation_name,
                    'company_reg_no' => $stock->company_reg_no,
                    'certificate_type' => $stock->certificate_type,
                    'stock_number' => $stock->stock_number,
                    'stockholder_name' => $stock->stockholder_name,
                    'issued_to' => $stock->stockholder_name,
                    'issued_to_type' => 'Stockholder',
                    'par_value' => $stock->par_value,
                    'number' => $stock->number,
                    'amount' => $stock->amount,
                    'amount_in_words' => $stock->amount_in_words,
                    'date_issued' => $stock->date_issued,
                    'released_at' => $stock->date_issued,
                    'president' => $stock->president,
                    'corporate_secretary' => $stock->corporate_secretary,
                    'document_path' => $stock->document_path,
                    'status' => 'released',
                    'installment_id' => $stock->installment_id,
                ]);
            }

            StockTransferJournal::create([
                'entry_date' => $stock->date_issued ?? now()->toDateString(),
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => sprintf('%s certificate stock added', $stock->certificate_type),
                'no_shares' => $stock->number,
                'transaction_type' => 'Certificate Stock',
                'certificate_no' => $stock->stock_number,
                'shareholder' => $stock->stockholder_name,
                'remarks' => 'Auto-generated from certificate stock creation.',
                'status' => $stock->status,
            ]);

            $ledger->update([
                'shares' => $ledger->shares ?: $stock->number,
                'status' => $ledger->status ?: 'active',
            ]);
        });

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

        $relatedRequests = $this->hasIssuanceRequestsTable()
            ? StockTransferIssuanceRequest::query()
                ->where('certificate_id', $stockTransferCertificate->id)
                ->orWhereHas('certificate', function ($query) use ($stockNumber) {
                    $query->where('stock_number', $stockNumber);
                })
                ->latest()
                ->get()
            : collect();

        return view('corporate.stock-transfer-book.certificate-preview', [
            'certificate' => $stockTransferCertificate,
            'relatedJournals' => $relatedJournals,
            'relatedLedgers' => $relatedLedgers,
            'relatedInstallments' => $relatedInstallments,
            'relatedRequests' => $relatedRequests,
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
        DB::transaction(function () use ($stockTransferCertificate) {
            $stockTransferCertificate->status = 'voided';
            $stockTransferCertificate->save();

            StockTransferJournal::create([
                'entry_date' => now()->toDateString(),
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => sprintf('%s certificate voided', $stockTransferCertificate->certificate_type ?: 'Stock'),
                'no_shares' => $stockTransferCertificate->number,
                'transaction_type' => 'Cancellation',
                'certificate_no' => $stockTransferCertificate->stock_number,
                'shareholder' => $stockTransferCertificate->stockholder_name,
                'remarks' => 'Auto-generated from certificate void action.',
                'status' => 'voided',
            ]);
        });

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Certificate voided.');
    }

    public function storeRequest(Request $request)
    {
        if (!$this->hasCertificateWorkflowColumns() || !$this->hasIssuanceRequestsTable()) {
            return back()->withErrors([
                'request_type' => 'Request for issuance requires the latest stock-transfer migration. Please run php artisan migrate.',
            ])->withInput();
        }

        $data = $request->validate([
            'reference_no' => ['nullable', 'string', 'max:255'],
            'requested_at' => ['nullable', 'date'],
            'request_type' => ['required', 'string', 'max:255'],
            'issuance_type' => ['required', 'string', 'in:COS,CV'],
            'requester' => ['required', 'string', 'max:255'],
            'received_by' => ['nullable', 'string', 'max:255'],
            'issued_by' => ['nullable', 'string', 'max:255'],
            'certificate_id' => ['required', 'integer', 'exists:stock_transfer_certificates,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $ledger = $this->resolveLedger(null, $data['requester']);
        if (!$ledger) {
            return back()->withErrors([
                'requester' => 'Requester must already exist in the Index.',
            ])->withInput();
        }

        $certificate = StockTransferCertificate::query()
            ->whereKey($data['certificate_id'])
            ->whereNull('source_certificate_id')
            ->first();

        if (!$certificate) {
            return back()->withErrors([
                'certificate_id' => 'Please select a valid certificate stock item that matches the chosen COS/CV type.',
            ])->withInput();
        }

        if (($certificate->certificate_type ?? 'COS') !== $data['issuance_type']) {
            return back()->withErrors([
                'issuance_type' => 'The selected certificate stock does not match the requested issuance type.',
            ])->withInput();
        }

        StockTransferIssuanceRequest::create([
            'reference_no' => $data['reference_no'] ?: $this->nextSequenceFor(StockTransferIssuanceRequest::class, 'reference_no', 'REQ-'),
            'requested_at' => $data['requested_at'] ?: now(),
            'request_type' => $data['request_type'],
            'issuance_type' => $data['issuance_type'],
            'requester' => $data['requester'],
            'received_by' => $data['received_by'] ?: auth()->user()?->name,
            'issued_by' => $data['issued_by'],
            'certificate_id' => $certificate->id,
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Issuance request added.');
    }

    public function showRequest(StockTransferIssuanceRequest $stockTransferIssuanceRequest)
    {
        $stockTransferIssuanceRequest->load(['certificate', 'journal', 'ledger']);

        return view('corporate.stock-transfer-book.issuance-request-preview', [
            'requestRecord' => $stockTransferIssuanceRequest,
            'backRoute' => route('stock-transfer-book.certificates'),
        ]);
    }

    public function approveRequest(StockTransferIssuanceRequest $stockTransferIssuanceRequest)
    {
        if (!$this->hasCertificateWorkflowColumns() || !$this->hasIssuanceRequestsTable()) {
            return redirect()->route('stock-transfer-book.certificates')->withErrors([
                'request_type' => 'Approving issuance requests requires the latest stock-transfer migration. Please run php artisan migrate.',
            ]);
        }

        if ($stockTransferIssuanceRequest->status === 'approved') {
            return redirect()
                ->route('stock-transfer-book.certificates.requests.show', $stockTransferIssuanceRequest)
                ->with('success', 'Issuance request is already approved.');
        }

        $stockTransferIssuanceRequest->load('certificate');
        $certificate = $stockTransferIssuanceRequest->certificate;

        if (!$certificate) {
            return redirect()->route('stock-transfer-book.certificates')->withErrors([
                'certificate' => 'The selected certificate stock is missing.',
            ]);
        }

        DB::transaction(function () use ($stockTransferIssuanceRequest, $certificate) {
            $journal = StockTransferJournal::create([
                'entry_date' => now()->toDateString(),
                'journal_no' => $this->nextJournalNo(),
                'ledger_folio' => $this->nextLedgerFolio(),
                'particulars' => sprintf('%s approved for %s', $stockTransferIssuanceRequest->request_type, $stockTransferIssuanceRequest->requester),
                'no_shares' => $certificate->number,
                'transaction_type' => 'Issuance',
                'certificate_no' => $certificate->stock_number,
                'shareholder' => $stockTransferIssuanceRequest->requester,
                'remarks' => sprintf(
                    'Approved issuance request %s using %s %s.',
                    $stockTransferIssuanceRequest->reference_no,
                    $stockTransferIssuanceRequest->issuance_type,
                    $certificate->stock_number
                ),
                'status' => 'approved',
            ]);

            $ledger = $journal->ledgers()->first();

            $voucher = StockTransferCertificate::create([
                'source_certificate_id' => $certificate->id,
                'issuance_request_id' => $stockTransferIssuanceRequest->id,
                'date_uploaded' => $certificate->date_uploaded,
                'uploaded_by' => $certificate->uploaded_by,
                'corporation_name' => $certificate->corporation_name,
                'company_reg_no' => $certificate->company_reg_no,
                'certificate_type' => $stockTransferIssuanceRequest->issuance_type,
                'stock_number' => $certificate->stock_number,
                'stockholder_name' => $certificate->stockholder_name ?: $stockTransferIssuanceRequest->requester,
                'issued_to' => $stockTransferIssuanceRequest->requester,
                'issued_to_type' => 'Requester',
                'par_value' => $certificate->par_value,
                'number' => $certificate->number,
                'amount' => $certificate->amount,
                'amount_in_words' => $certificate->amount_in_words,
                'date_issued' => now()->toDateString(),
                'released_at' => now(),
                'president' => $certificate->president,
                'corporate_secretary' => $certificate->corporate_secretary,
                'document_path' => $certificate->document_path,
                'status' => 'released',
                'installment_id' => $certificate->installment_id,
            ]);

            $stockTransferIssuanceRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->user()?->name,
                'issued_by' => $stockTransferIssuanceRequest->issued_by ?: auth()->user()?->name,
                'journal_id' => $journal->id,
                'ledger_id' => $ledger?->id,
                'certificate_id' => $certificate->id,
                'notes' => trim(implode(' ', array_filter([
                    $stockTransferIssuanceRequest->notes,
                    'Voucher Ref: ' . $voucher->stock_number,
                ]))),
            ]);
        });

        return redirect()
            ->route('stock-transfer-book.certificates.requests.show', $stockTransferIssuanceRequest)
            ->with('success', 'Issuance request approved.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'corporation_name', 'label' => 'Corporation Name', 'type' => 'text'],
            ['name' => 'company_reg_no', 'label' => 'Company Reg. No.', 'type' => 'text'],
            ['name' => 'certificate_type', 'label' => 'Certificate Type', 'type' => 'select', 'options' => ['COS', 'CV']],
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
            'certificate_type' => ['nullable', 'string', 'in:COS,CV'],
            'stock_number' => ['nullable', 'string', 'max:255'],
            'stockholder_name' => ['nullable', 'string', 'max:255'],
            'issued_to' => ['nullable', 'string', 'max:255'],
            'issued_to_type' => ['nullable', 'string', 'max:255'],
            'par_value' => ['nullable', 'numeric'],
            'number' => ['nullable', 'integer'],
            'amount' => ['nullable', 'numeric'],
            'amount_in_words' => ['nullable', 'string', 'max:255'],
            'date_issued' => ['nullable', 'date'],
            'released_at' => ['nullable', 'date'],
            'president' => ['nullable', 'string', 'max:255'],
            'corporate_secretary' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveLedger(?string $stockNumber, ?string $stockholderName): ?StockTransferLedger
    {
        $query = StockTransferLedger::query();
        $query->whereNull('journal_id');
        $query->where(function ($sub) use ($stockNumber, $stockholderName) {
            if ($stockNumber) {
                $sub->orWhere('certificate_no', $stockNumber);
            }
            $this->applyNameTokens($sub, $stockholderName, ['first_name', 'middle_name', 'family_name']);
        });

        return $query->first();
    }

    private function hasCertificateWorkflowColumns(): bool
    {
        return Schema::hasColumn('stock_transfer_certificates', 'source_certificate_id')
            && Schema::hasColumn('stock_transfer_certificates', 'certificate_type');
    }

    private function hasIssuanceRequestsTable(): bool
    {
        return Schema::hasTable('stock_transfer_issuance_requests');
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
}
