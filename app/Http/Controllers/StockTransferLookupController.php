<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\AuthorizedCapitalStock;
use App\Models\GisRecord;
use App\Models\Stockholder;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferIssuanceRequest;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class StockTransferLookupController extends Controller
{
    use GeneratesStockTransferIds;
    use MatchesShareholder;

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
            ->where(function ($query) use ($key) {
                $query->where('stock_number', $key)
                    ->orWhere('issued_to', 'like', '%' . $key . '%')
                    ->orWhere('stockholder_name', 'like', '%' . $key . '%');
            })
            ->latest()
            ->first();

        $ledger = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->where(function ($query) use ($key) {
                $query->where('certificate_no', $key);
                $this->applyNameTokens($query, $key, ['first_name', 'middle_name', 'family_name']);
            })
            ->latest()
            ->first();

        $journal = StockTransferJournal::query()
            ->where(function ($query) use ($key) {
                $query->where('certificate_no', $key);
                $this->applyNameTokens($query, $key, ['shareholder']);
            })
            ->latest()
            ->first();

        $installment = StockTransferInstallment::query()
            ->where(function ($query) use ($key) {
                $query->where('stock_number', $key);
                $this->applyNameTokens($query, $key, ['subscriber']);
            })
            ->latest()
            ->first();

        $stockholderName = $installment?->subscriber
            ?: $certificate?->stockholder_name
            ?: ($ledger ? trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' ')) : null);

        $hasStockholdersTable = Schema::hasTable('stockholders');
        $hasCompanyTables = Schema::hasTable('gis_records');
        $hasAuthorizedCapitalTable = Schema::hasTable('authorized_capital_stocks');

        $stockholderRecord = null;
        if ($hasStockholdersTable && $stockholderName) {
            $stockholderQuery = Stockholder::query();
            $this->applyNameTokens($stockholderQuery, $stockholderName, ['stockholder_name']);
            $stockholderRecord = $stockholderQuery->latest()->first();
        }

        $latestCompany = $hasCompanyTables ? GisRecord::query()->latest()->first() : null;
        $parValue = $hasAuthorizedCapitalTable
            ? AuthorizedCapitalStock::query()->latest()->value('par_value')
            : null;

        if ($parValue === null && $latestCompany) {
            $parValue = optional($latestCompany?->authorizedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->subscribedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->paidUpCapital()->latest()->first())->par_value;
        }

        $amount = $installment?->total_value
            ?? $certificate?->amount
            ?? (($installment?->no_shares && $parValue) ? ((float) $installment->no_shares * (float) $parValue) : null);

        $requestRecord = Schema::hasTable('stock_transfer_issuance_requests')
            ? StockTransferIssuanceRequest::query()
                ->where('reference_no', $key)
                ->latest()
                ->first()
            : null;

        return response()->json([
            'certificate' => $certificate ? [
                'date_uploaded' => optional($certificate->date_uploaded)->toDateString(),
                'uploaded_by' => $certificate->uploaded_by,
                'stock_number' => $certificate->stock_number,
                'stockholder_name' => $certificate->stockholder_name,
                'corporation_name' => $certificate->corporation_name ?: $latestCompany?->corporation_name,
                'company_reg_no' => $certificate->company_reg_no ?: $latestCompany?->company_reg_no,
                'par_value' => $certificate->par_value ?: $parValue,
                'number' => $certificate->number,
                'amount' => $certificate->amount ?: $amount,
                'amount_in_words' => $certificate->amount_in_words ?: $this->amountToWords($amount),
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
                'holder_name' => $stockholderRecord?->stockholder_name
                    ?: $installment->subscriber
                    ?: ($ledger ? trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' ')) : null),
                'installment_date' => optional($installment->installment_date)->toDateString(),
                'no_shares' => $installment->no_shares,
                'no_installments' => $installment->no_installments,
                'total_value' => $installment->total_value,
                'installment_amount' => $installment->installment_amount,
                'status' => $installment->payment_status,
                'payment_total' => $installment->paymentTotal(),
                'payment_count' => $installment->paymentCount(),
                'mode' => $installment->installmentMode(),
                'amount_in_words' => $this->amountToWords($installment->total_value),
            ] : null,
            'stockholder_record' => $stockholderRecord ? [
                'stockholder_name' => $stockholderRecord->stockholder_name,
                'shares' => $stockholderRecord->shares,
                'amount' => $stockholderRecord->amount,
                'amount_paid' => $stockholderRecord->amount_paid,
                'tin' => $stockholderRecord->tin,
            ] : null,
            'company' => $latestCompany ? [
                'corporation_name' => $latestCompany->corporation_name,
                'company_reg_no' => $latestCompany->company_reg_no,
                'par_value' => $parValue,
                'computed_amount' => $amount,
                'computed_amount_in_words' => $this->amountToWords($amount),
            ] : null,
            'issuance_request' => $requestRecord ? [
                'reference_no' => $requestRecord->reference_no,
                'requested_at' => optional($requestRecord->requested_at)->format('Y-m-d\TH:i'),
                'request_type' => $requestRecord->request_type,
                'issuance_type' => $requestRecord->issuance_type,
                'requester' => $requestRecord->requester,
                'received_by' => $requestRecord->received_by,
                'issued_by' => $requestRecord->issued_by,
                'status' => $requestRecord->status,
            ] : null,
        ]);
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

        return $words . ' Only';
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

    public function defaults()
    {
        return response()->json([
            'today' => now()->toDateString(),
            'now' => now()->format('Y-m-d\TH:i'),
            'journal_no' => $this->nextJournalNo(),
            'ledger_folio' => $this->nextLedgerFolio(),
            'stock_number' => $this->nextStockNumber(),
            'reference_no' => Schema::hasTable('stock_transfer_issuance_requests')
                ? $this->nextSequenceFor(StockTransferIssuanceRequest::class, 'reference_no', 'REQ-')
                : 'REQ-0001',
        ]);
    }
}
