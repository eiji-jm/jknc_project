<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferIssuanceRequest;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class StockTransferLedgerController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;
    use GeneratesStockTransferIds;
    use GeneratesPdfPreview;

    public function indexPage()
    {
        $ledgers = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->latest()
            ->get();
        $contacts = Contact::query()->orderBy('name')->get(['name', 'email', 'nationality', 'address', 'tax_id']);

        return view('corporate.stock-transfer-book.stb-index', compact('ledgers', 'contacts'));
    }

    public function index()
    {
        $ledgers = StockTransferLedger::query()
            ->whereNotNull('journal_id')
            ->with('journal')
            ->latest()
            ->get();
        $contacts = Contact::query()->orderBy('name')->get(['name', 'email', 'nationality', 'address', 'tax_id']);

        return view('corporate.stock-transfer-book.ledger', compact('ledgers', 'contacts'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Shareholder',
            'action' => route('stock-transfer-book.ledger.store'),
            'method' => 'POST',
            'cancelRoute' => route('stock-transfer-book.ledger'),
            'fields' => $this->fields(),
            'item' => new StockTransferLedger(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $contact = $this->resolveContact($data['first_name'] ?? null, $data['middle_name'] ?? null, $data['family_name'] ?? null);
        if (!$contact) {
            return back()->withErrors([
                'first_name' => 'Stockholder must exist in Contacts before adding to Index.',
            ])->withInput();
        }

        if (empty($data['certificate_no'])) {
            $data['certificate_no'] = $this->nextStockNumber();
        }

        // Auto-fill contact details when missing.
        $data['email'] = $data['email'] ?: $contact->email;
        $data['nationality'] = $data['nationality'] ?: $contact->nationality;
        $data['address'] = $data['address'] ?: $contact->address;
        $data['tin'] = $data['tin'] ?: $contact->tax_id;

        if (empty($data['date_registered'])) {
            $data['date_registered'] = now()->toDateString();
        }

        DB::transaction(function () use ($data) {
            StockTransferLedger::create($data);
        });

        return redirect()->route('stock-transfer-book.ledger')->with('success', 'Shareholder added.');
    }

    public function show(StockTransferLedger $stockTransferLedger)
    {
        $certificateNo = $stockTransferLedger->certificate_no;
        $fullName = trim(collect([$stockTransferLedger->first_name, $stockTransferLedger->middle_name, $stockTransferLedger->family_name])
            ->filter()
            ->implode(' '));

        $journalEntries = StockTransferJournal::query()
            ->where(function ($query) use ($certificateNo, $fullName) {
                if ($certificateNo) {
                    $query->orWhere('certificate_no', $certificateNo);
                }
                $this->applyNameTokens($query, $fullName, ['shareholder']);
            })
            ->latest()
            ->get();

        $relatedCertificates = StockTransferCertificate::query()
            ->where(function ($query) use ($certificateNo, $fullName) {
                if ($certificateNo) {
                    $query->orWhere('stock_number', $certificateNo);
                }
                $this->applyNameTokens($query, $fullName, ['stockholder_name']);
            })
            ->latest()
            ->get();

        $relatedInstallments = StockTransferInstallment::query()
            ->where(function ($query) use ($certificateNo, $fullName) {
                if ($certificateNo) {
                    $query->orWhere('stock_number', $certificateNo);
                }
                $this->applyNameTokens($query, $fullName, ['subscriber']);
            })
            ->latest()
            ->get();

        $relatedRequests = Schema::hasTable('stock_transfer_issuance_requests')
            ? StockTransferIssuanceRequest::query()
                ->where(function ($query) use ($certificateNo, $fullName) {
                    $this->applyNameTokens($query, $fullName, ['requester']);
                    if ($certificateNo) {
                        $query->orWhereHas('certificate', function ($certificateQuery) use ($certificateNo) {
                            $certificateQuery->where('stock_number', $certificateNo);
                        });
                    }
                })
                ->latest()
                ->get()
            : collect();

        $generatedPreviewPath = $this->generatePdfPreview(
            'corporate.stock-transfer-book.ledger-pdf',
            [
                'ledger' => $stockTransferLedger,
                'journalEntries' => $journalEntries,
            ],
            'generated-previews/stock-transfer-book/ledger/' . ($stockTransferLedger->certificate_no ?: $stockTransferLedger->id) . '.pdf'
        );

        return view('corporate.stock-transfer-book.ledger-preview', [
            'ledger' => $stockTransferLedger,
            'generatedPreviewUrl' => $generatedPreviewPath ? route('uploads.show', ['path' => $generatedPreviewPath]) : null,
            'journalEntries' => $journalEntries,
            'relatedCertificates' => $relatedCertificates,
            'relatedInstallments' => $relatedInstallments,
            'relatedRequests' => $relatedRequests,
            'backRoute' => route('stock-transfer-book.ledger'),
            'editRoute' => route('stock-transfer-book.ledger.edit', $stockTransferLedger),
        ]);
    }

    public function edit(StockTransferLedger $stockTransferLedger)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Shareholder',
            'action' => route('stock-transfer-book.ledger.update', $stockTransferLedger),
            'method' => 'PUT',
            'cancelRoute' => route('stock-transfer-book.ledger'),
            'fields' => $this->fields(),
            'item' => $stockTransferLedger,
        ]);
    }

    public function update(Request $request, StockTransferLedger $stockTransferLedger)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $stockTransferLedger->document_path);

        $stockTransferLedger->update($data);

        return redirect()->route('stock-transfer-book.ledger')->with('success', 'Shareholder updated.');
    }

    public function destroy(StockTransferLedger $stockTransferLedger)
    {
        $stockTransferLedger->delete();

        return redirect()->route('stock-transfer-book.ledger')->with('success', 'Shareholder deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'family_name', 'label' => 'Family Name', 'type' => 'text', 'required' => true],
            ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
            ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text'],
            ['name' => 'nationality', 'label' => 'Nationality', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Current Residential Address', 'type' => 'text'],
            ['name' => 'tin', 'label' => 'TIN', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'shares', 'label' => 'Number of Shares', 'type' => 'number'],
            ['name' => 'certificate_no', 'label' => 'Certificate No.', 'type' => 'text'],
            ['name' => 'date_registered', 'label' => 'Date Registered', 'type' => 'date'],
            ['name' => 'document_path', 'label' => 'Upload Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'family_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'shares' => ['nullable', 'integer'],
            'certificate_no' => ['nullable', 'string', 'max:255'],
            'date_registered' => ['nullable', 'date'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function resolveContact(?string $firstName, ?string $middleName, ?string $familyName): ?Contact
    {
        $name = trim(collect([$firstName, $middleName, $familyName])->filter()->implode(' '));
        if ($name === '') {
            return null;
        }

        $query = Contact::query();
        $this->applyNameTokens($query, $name, ['name']);

        return $query->first();
    }
}
