<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use Illuminate\Http\Request;

class StockTransferInstallmentController extends Controller
{
    use HandlesUploads;
    use MatchesShareholder;

    public function index()
    {
        $installments = StockTransferInstallment::latest()->get();

        return view('corporate.stock-transfer-book.installment', compact('installments'));
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

        StockTransferInstallment::create($data);

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
                    ->orWhereIn('transaction_type', ['Issuance', 'Cancellation']);
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
        $stockTransferInstallment->delete();

        return redirect()->route('stock-transfer-book.installment')->with('success', 'Installment plan deleted.');
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
            'status' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }
}
