<?php

namespace App\Http\Controllers;

use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use Illuminate\Http\Request;

class StockTransferLookupController extends Controller
{
    public function lookup(Request $request)
    {
        $key = trim((string) $request->query('key', ''));

        if ($key === '') {
            return response()->json([
                'certificate' => null,
                'ledger' => null,
                'journal' => null,
                'installment' => null,
            ]);
        }

        $certificate = StockTransferCertificate::query()
            ->where('stock_number', $key)
            ->latest()
            ->first();

        $ledger = StockTransferLedger::query()
            ->where('certificate_no', $key)
            ->latest()
            ->first();

        $journal = StockTransferJournal::query()
            ->where('certificate_no', $key)
            ->latest()
            ->first();

        $installment = StockTransferInstallment::query()
            ->where('stock_number', $key)
            ->latest()
            ->first();

        return response()->json([
            'certificate' => $certificate ? [
                'date_uploaded' => optional($certificate->date_uploaded)->toDateString(),
                'uploaded_by' => $certificate->uploaded_by,
                'stock_number' => $certificate->stock_number,
                'stockholder_name' => $certificate->stockholder_name,
                'corporation_name' => $certificate->corporation_name,
                'company_reg_no' => $certificate->company_reg_no,
                'par_value' => $certificate->par_value,
                'number' => $certificate->number,
                'amount' => $certificate->amount,
                'amount_in_words' => $certificate->amount_in_words,
                'date_issued' => optional($certificate->date_issued)->toDateString(),
                'president' => $certificate->president,
                'corporate_secretary' => $certificate->corporate_secretary,
            ] : null,
            'ledger' => $ledger ? [
                'family_name' => $ledger->family_name,
                'first_name' => $ledger->first_name,
                'middle_name' => $ledger->middle_name,
                'full_name' => trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' ')),
                'nationality' => $ledger->nationality,
                'address' => $ledger->address,
                'tin' => $ledger->tin,
                'email' => $ledger->email,
                'phone' => $ledger->phone,
                'shares' => $ledger->shares,
                'certificate_no' => $ledger->certificate_no,
                'date_registered' => optional($ledger->date_registered)->toDateString(),
                'status' => $ledger->status,
            ] : null,
            'journal' => $journal ? [
                'entry_date' => optional($journal->entry_date)->toDateString(),
                'journal_no' => $journal->journal_no,
                'ledger_folio' => $journal->ledger_folio,
                'particulars' => $journal->particulars,
                'no_shares' => $journal->no_shares,
                'transaction_type' => $journal->transaction_type,
                'certificate_no' => $journal->certificate_no,
                'shareholder' => $journal->shareholder,
                'remarks' => $journal->remarks,
            ] : null,
            'installment' => $installment ? [
                'stock_number' => $installment->stock_number,
                'subscriber' => $installment->subscriber,
                'installment_date' => optional($installment->installment_date)->toDateString(),
                'no_shares' => $installment->no_shares,
                'no_installments' => $installment->no_installments,
                'total_value' => $installment->total_value,
                'installment_amount' => $installment->installment_amount,
                'status' => $installment->status,
            ] : null,
        ]);
    }
}
