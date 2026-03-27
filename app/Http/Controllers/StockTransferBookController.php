<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StockTransferBookIndex;
use App\Models\StockTransferBookLedger;
use App\Models\StockTransferBookJournal;
use App\Models\StockTransferBookCertificate;
use App\Models\StockTransferBookCertificateVoucher;
use App\Models\StockTransferBookIssuanceRequest;
use App\Models\StockTransferBookCertificateCancellation;
use App\Models\StockTransferBookInstallment;

class StockTransferBookController extends Controller
{
    public function index()
    {
        $indexes = StockTransferBookIndex::latest()->get();

        return view('corporate.stock-transfer-book.index', compact('indexes'));
    }

    public function storeIndex(Request $request)
    {
        $request->validate([
            'family_name'     => 'required|string|max:255',
            'first_name'      => 'nullable|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'nationality'     => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:1000',
            'tin'             => 'nullable|string|max:255',
        ]);

        StockTransferBookIndex::create([
            'family_name'     => $request->family_name,
            'first_name'      => $request->first_name,
            'middle_name'     => $request->middle_name,
            'nationality'     => $request->nationality,
            'current_address' => $request->current_address,
            'tin'             => $request->tin,
            'created_by'      => Auth::id(),
        ]);

        return redirect()->route('stock-transfer-book.index')
            ->with('success', 'Index entry saved successfully.');
    }

    public function lookupIndex(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        $records = StockTransferBookIndex::query();

        if ($query !== '') {
            $records->where(function ($q) use ($query) {
                $q->where('family_name', 'like', "%{$query}%")
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('middle_name', 'like', "%{$query}%");
            });
        }

        $matches = $records->limit(8)->get([
            'id',
            'family_name',
            'first_name',
            'middle_name',
            'nationality',
            'current_address',
            'tin',
        ]);

        return response()->json($matches);
    }

    public function ledger()
    {
        $indexes = StockTransferBookIndex::latest()->get();

        $ledgers = StockTransferBookLedger::with('indexRecord')
            ->latest()
            ->get()
            ->map(function ($ledger) {
                return (object) [
                    'id' => $ledger->id,
                    'family_name' => $ledger->indexRecord->family_name ?? '',
                    'first_name' => $ledger->indexRecord->first_name ?? '',
                    'middle_name' => $ledger->indexRecord->middle_name ?? '',
                    'nationality' => $ledger->indexRecord->nationality ?? '',
                    'current_address' => $ledger->indexRecord->current_address ?? '',
                    'tin' => $ledger->indexRecord->tin ?? '',
                    'certificate_no' => $ledger->certificate_no,
                    'number_of_shares' => $ledger->number_of_shares,
                    'date_registered' => $ledger->date_registered,
                    'status' => $ledger->status,
                ];
            });

        return view('corporate.stock-transfer-book.ledger', compact('ledgers', 'indexes'));
    }

    public function storeLedger(Request $request)
    {
        $request->validate([
            'stock_transfer_book_index_id' => 'required|exists:stock_transfer_book_indexes,id',
            'certificate_no' => 'nullable|string|max:255',
            'number_of_shares' => 'nullable|integer|min:0',
            'date_registered' => 'nullable|date',
            'status' => 'nullable|string|max:255',
        ]);

        StockTransferBookLedger::create([
            'stock_transfer_book_index_id' => $request->stock_transfer_book_index_id,
            'certificate_no' => $request->certificate_no,
            'number_of_shares' => $request->number_of_shares,
            'date_registered' => $request->date_registered,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('stock-transfer-book.ledger')
            ->with('success', 'Ledger record saved successfully.');
    }

    public function journal()
{
    $ledgerRecords = StockTransferBookLedger::with('indexRecord')
        ->latest()
        ->get()
        ->map(function ($ledger, $index) {
            $shareholderName = trim(
                ($ledger->indexRecord->family_name ?? '') . ', ' .
                ($ledger->indexRecord->first_name ?? '') . ' ' .
                ($ledger->indexRecord->middle_name ?? '')
            );

            return (object) [
                'id' => $ledger->id,
                'ledger_folio' => 'LED-' . str_pad($ledger->id, 4, '0', STR_PAD_LEFT),
                'certificate_no' => $ledger->certificate_no,
                'number_of_shares' => $ledger->number_of_shares,
                'shareholder' => $shareholderName,
                'family_name' => $ledger->indexRecord->family_name ?? '',
                'first_name' => $ledger->indexRecord->first_name ?? '',
                'middle_name' => $ledger->indexRecord->middle_name ?? '',
                'nationality' => $ledger->indexRecord->nationality ?? '',
                'current_address' => $ledger->indexRecord->current_address ?? '',
                'tin' => $ledger->indexRecord->tin ?? '',
            ];
        });

    $journals = StockTransferBookJournal::with('ledgerRecord.indexRecord')
        ->latest()
        ->get()
        ->map(function ($journal) {
            $ledger = $journal->ledgerRecord;
            $indexRecord = $ledger?->indexRecord;

            return (object) [
                'id' => $journal->id,
                'entry_date' => $journal->entry_date,
                'journal_no' => $journal->journal_no,
                'ledger_folio' => $journal->ledger_folio,
                'particulars' => $journal->particulars,
                'no_shares' => $journal->no_shares,
                'transaction_type' => $journal->transaction_type,
                'certificate_no' => $journal->certificate_no,
                'shareholder' => trim(
                    ($indexRecord->family_name ?? '') . ', ' .
                    ($indexRecord->first_name ?? '') . ' ' .
                    ($indexRecord->middle_name ?? '')
                ),
                'remarks' => $journal->remarks,
            ];
        });

    return view('corporate.stock-transfer-book.journal', compact('journals', 'ledgerRecords'));
}

public function storeJournal(Request $request)
{
    $request->validate([
        'stock_transfer_book_ledger_id' => 'required|exists:stock_transfer_book_ledgers,id',
        'entry_date' => 'nullable|date',
        'journal_no' => 'nullable|string|max:255',
        'ledger_folio' => 'nullable|string|max:255',
        'particulars' => 'nullable|string',
        'no_shares' => 'nullable|integer|min:0',
        'transaction_type' => 'nullable|string|max:255',
        'certificate_no' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
    ]);

    StockTransferBookJournal::create([
        'stock_transfer_book_ledger_id' => $request->stock_transfer_book_ledger_id,
        'entry_date' => $request->entry_date,
        'journal_no' => $request->journal_no,
        'ledger_folio' => $request->ledger_folio,
        'particulars' => $request->particulars,
        'no_shares' => $request->no_shares,
        'transaction_type' => $request->transaction_type,
        'certificate_no' => $request->certificate_no,
        'remarks' => $request->remarks,
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('stock-transfer-book.journal')
        ->with('success', 'Journal transaction saved successfully.');
}

public function installment()
{
    $ledgerRecords = StockTransferBookLedger::with('indexRecord')
        ->latest()
        ->get()
        ->map(function ($ledger) {
            $shareholderName = trim(
                ($ledger->indexRecord->family_name ?? '') . ', ' .
                ($ledger->indexRecord->first_name ?? '') . ' ' .
                ($ledger->indexRecord->middle_name ?? '')
            );

            return (object) [
                'id' => $ledger->id,
                'shareholder' => $shareholderName,
                'certificate_no' => $ledger->certificate_no,
                'number_of_shares' => $ledger->number_of_shares,
            ];
        });

    $installments = StockTransferBookInstallment::latest()->get();

    return view('corporate.stock-transfer-book.installment', compact('installments', 'ledgerRecords'));
}

public function storeInstallment(Request $request)
{
    $request->validate([
        'stock_transfer_book_ledger_id' => 'required|exists:stock_transfer_book_ledgers,id',
        'stock_number' => 'nullable|string|max:255',
        'subscriber' => 'nullable|string|max:255',
        'installment_date' => 'nullable|date',
        'no_shares' => 'nullable|integer|min:0',
        'no_installments' => 'nullable|integer|min:0',
        'total_value' => 'nullable|numeric|min:0',
        'installment_amount' => 'nullable|numeric|min:0',
        'status' => 'nullable|string|max:255',
    ]);

    StockTransferBookInstallment::create([
        'stock_transfer_book_ledger_id' => $request->stock_transfer_book_ledger_id,
        'stock_number' => $request->stock_number,
        'subscriber' => $request->subscriber,
        'installment_date' => $request->installment_date,
        'no_shares' => $request->no_shares,
        'no_installments' => $request->no_installments,
        'total_value' => $request->total_value,
        'installment_amount' => $request->installment_amount,
        'status' => $request->status,
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('stock-transfer-book.installment')
        ->with('success', 'Installment plan saved successfully.');
}

    public function certificates()
    {
        $ledgerRecords = StockTransferBookLedger::with('indexRecord')
            ->latest()
            ->get()
            ->map(function ($ledger) {
                $shareholderName = trim(
                    ($ledger->indexRecord->family_name ?? '') . ', ' .
                    ($ledger->indexRecord->first_name ?? '') . ' ' .
                    ($ledger->indexRecord->middle_name ?? '')
                );
    
                return (object) [
                    'id' => $ledger->id,
                    'shareholder' => $shareholderName,
                    'certificate_no' => $ledger->certificate_no,
                    'number_of_shares' => $ledger->number_of_shares,
                    'family_name' => $ledger->indexRecord->family_name ?? '',
                    'first_name' => $ledger->indexRecord->first_name ?? '',
                    'middle_name' => $ledger->indexRecord->middle_name ?? '',
                    'nationality' => $ledger->indexRecord->nationality ?? '',
                    'current_address' => $ledger->indexRecord->current_address ?? '',
                    'tin' => $ledger->indexRecord->tin ?? '',
                ];
            });
    
        $certificates = StockTransferBookCertificate::latest()->get();
        $vouchers = StockTransferBookCertificateVoucher::latest()->get();
        $issuanceRequests = StockTransferBookIssuanceRequest::latest()->get();
        $cancellations = StockTransferBookCertificateCancellation::with('certificate')->latest()->get();
    
        return view('corporate.stock-transfer-book.certificates', compact(
            'certificates',
            'vouchers',
            'ledgerRecords',
            'issuanceRequests',
            'cancellations'
        ));
    }

public function storeCertificate(Request $request)
{
    $request->validate([
        'stock_transfer_book_ledger_id' => 'required|exists:stock_transfer_book_ledgers,id',
        'date_uploaded' => 'nullable|date',
        'uploaded_by' => 'nullable|string|max:255',
        'corporation_name' => 'nullable|string|max:255',
        'company_reg_no' => 'nullable|string|max:255',
        'stock_number' => 'nullable|string|max:255|unique:stock_transfer_book_certificates,stock_number',
        'stockholder_name' => 'nullable|string|max:255',
        'par_value' => 'nullable|string|max:255',
        'number' => 'nullable|integer|min:0',
        'amount' => 'nullable|numeric|min:0',
        'amount_in_words' => 'nullable|string',
        'date_issued' => 'nullable|date',
        'president' => 'nullable|string|max:255',
        'corporate_secretary' => 'nullable|string|max:255',
    ]);

    $stockNumber = $request->stock_number ?: ('STK-' . str_pad((string) (StockTransferBookCertificate::count() + 1), 4, '0', STR_PAD_LEFT));

    $certificate = StockTransferBookCertificate::create([
        'stock_transfer_book_ledger_id' => $request->stock_transfer_book_ledger_id,
        'date_uploaded' => $request->date_uploaded,
        'uploaded_by' => $request->uploaded_by,
        'corporation_name' => $request->corporation_name,
        'company_reg_no' => $request->company_reg_no,
        'stock_number' => $stockNumber,
        'stockholder_name' => $request->stockholder_name,
        'par_value' => $request->par_value,
        'number' => $request->number,
        'amount' => $request->amount,
        'amount_in_words' => $request->amount_in_words,
        'date_issued' => $request->date_issued,
        'president' => $request->president,
        'corporate_secretary' => $request->corporate_secretary,
        'status' => 'Active',
        'created_by' => Auth::id(),
    ]);

    StockTransferBookCertificateVoucher::create([
        'stock_transfer_book_certificate_id' => $certificate->id,
        'date_uploaded' => $certificate->date_uploaded,
        'uploaded_by' => $certificate->uploaded_by,
        'corporation_name' => $certificate->corporation_name,
        'company_reg_no' => $certificate->company_reg_no,
        'stock_number' => $certificate->stock_number,
        'stockholder_name' => $certificate->stockholder_name,
        'par_value' => $certificate->par_value,
        'number' => $certificate->number,
        'amount' => $certificate->amount,
        'amount_in_words' => $certificate->amount_in_words,
        'date_issued' => $certificate->date_issued,
        'president' => $certificate->president,
        'corporate_secretary' => $certificate->corporate_secretary,
        'issued_to' => $certificate->stockholder_name,
        'issued_to_type' => 'Stockholder',
        'certificate_released_date' => $certificate->date_issued,
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('stock-transfer-book.certificates')
        ->with('success', 'Certificate saved successfully. Voucher auto-generated.');
}
public function updateCertificateVoucher(Request $request, $id)
{
    $request->validate([
        'issued_to' => 'nullable|string|max:255',
        'issued_to_type' => 'nullable|string|max:255',
        'certificate_released_date' => 'nullable|date',
    ]);

    $voucher = StockTransferBookCertificateVoucher::findOrFail($id);

    $voucher->update([
        'issued_to' => $request->issued_to,
        'issued_to_type' => $request->issued_to_type,
        'certificate_released_date' => $request->certificate_released_date,
    ]);

    return redirect()->route('stock-transfer-book.certificates')
        ->with('success', 'Certificate voucher updated successfully.');
}

public function storeIssuanceRequest(Request $request)
{
    $request->validate([
        'ref_no' => 'nullable|string|max:255|unique:stock_transfer_book_issuance_requests,ref_no',
        'date_requested' => 'nullable|date',
        'time_requested' => 'nullable',
        'type_of_request' => 'nullable|string|max:255',
        'requester' => 'nullable|string|max:255',
        'received_by' => 'nullable|string|max:255',
        'issued_by' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:255',
    ]);

    $refNo = $request->ref_no ?: ('REQ-' . str_pad((string) (StockTransferBookIssuanceRequest::count() + 1), 4, '0', STR_PAD_LEFT));

    StockTransferBookIssuanceRequest::create([
        'ref_no' => $refNo,
        'date_requested' => $request->date_requested,
        'time_requested' => $request->time_requested,
        'type_of_request' => $request->type_of_request,
        'requester' => $request->requester,
        'received_by' => $request->received_by,
        'issued_by' => $request->issued_by,
        'status' => $request->status,
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('stock-transfer-book.certificates')
        ->with('success', 'Issuance request saved successfully.');
}
public function storeCertificateCancellation(Request $request)
{
    $request->validate([
        'stock_transfer_book_certificate_id' => 'required|exists:stock_transfer_book_certificates,id',
        'date_of_cancellation' => 'nullable|date',
        'effective_date' => 'nullable|date',
        'reason' => 'nullable|string',
        'type_of_cancellation' => 'nullable|string|max:255',
        'others_specify' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:255',
    ]);

    $cancellation = StockTransferBookCertificateCancellation::create([
        'stock_transfer_book_certificate_id' => $request->stock_transfer_book_certificate_id,
        'date_of_cancellation' => $request->date_of_cancellation,
        'effective_date' => $request->effective_date,
        'reason' => $request->reason,
        'type_of_cancellation' => $request->type_of_cancellation,
        'others_specify' => $request->others_specify,
        'status' => $request->status ?: 'Cancelled',
        'created_by' => Auth::id(),
    ]);

    $certificate = StockTransferBookCertificate::findOrFail($request->stock_transfer_book_certificate_id);
    $certificate->update([
        'status' => 'Cancelled',
    ]);

    return redirect()->route('stock-transfer-book.certificates')
        ->with('success', 'Certificate cancellation saved successfully.');
}

public function cancelCertificateVoucher(Request $request, $id)
{
    $request->validate([
        'issued_to' => 'nullable|string|max:255',
        'issued_to_type' => 'nullable|string|max:255',
        'certificate_released_date' => 'nullable|date',
    ]);

    $voucher = StockTransferBookCertificateVoucher::findOrFail($id);

    $voucher->update([
        'issued_to' => $request->issued_to,
        'issued_to_type' => $request->issued_to_type,
        'certificate_released_date' => $request->certificate_released_date,
    ]);

    return redirect()->route('stock-transfer-book.certificates')
        ->with('success', 'Certificate voucher updated successfully.');
}
}