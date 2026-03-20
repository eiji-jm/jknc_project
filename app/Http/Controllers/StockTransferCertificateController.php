<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\StockTransferInstallment;
use Illuminate\Http\Request;

class StockTransferCertificateController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;

    public function index()
    {
        $certificates = StockTransferCertificate::latest()->get();

        return view('corporate.stock-transfer-book.certificates', compact('certificates'));
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
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation']);
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
        $stockTransferCertificate->delete();

        return redirect()->route('stock-transfer-book.certificates')->with('success', 'Certificate deleted.');
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
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }
}
