<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesStockTransferIds;
use App\Http\Controllers\Concerns\MatchesShareholder;
use App\Models\AuthorizedCapitalStock;
use App\Models\GisRecord;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferIssuanceRequest;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\Stockholder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

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
                'stockholder_record' => null,
                'company' => null,
                'issuance_request' => null,
            ]);
        }

        $hasCompanyTables = Schema::hasTable('gis_records');
        $hasAuthorizedCapitalTable = Schema::hasTable('authorized_capital_stocks');
        $hasStockholdersTable = Schema::hasTable('stockholders');

        $latestCompany = $hasCompanyTables ? GisRecord::query()->latest()->first() : null;

        $parValue = $hasAuthorizedCapitalTable
            ? AuthorizedCapitalStock::query()->latest()->value('par_value')
            : null;

        if ($parValue === null && $latestCompany) {
            $parValue = optional($latestCompany?->authorizedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->subscribedCapital()->latest()->first())->par_value
                ?? optional($latestCompany?->paidUpCapital()->latest()->first())->par_value;
        }

        $ledger = StockTransferLedger::query()
            ->whereNull('journal_id')
            ->where(function ($query) use ($key) {
                if ($key !== '') {
                    $query->orWhere('certificate_no', $key);
                    $this->applyNameTokens($query, $key, ['first_name', 'middle_name', 'family_name']);
                }
            })
            ->latest()
            ->first();

        $installment = StockTransferInstallment::query()
            ->where(function ($query) use ($key, $ledger) {
                if ($key !== '') {
                    $query->orWhere('stock_number', $key);
                }

                if ($ledger) {
                    $fullName = trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '));
                    $this->applyNameTokens($query, $fullName, ['subscriber']);
                } else {
                    $this->applyNameTokens($query, $key, ['subscriber']);
                }
            })
            ->latest()
            ->first();

        $certificate = StockTransferCertificate::query()
            ->where(function ($query) use ($key, $ledger) {
                if ($key !== '') {
                    $query->orWhere('stock_number', $key)
                        ->orWhere('issued_to', 'like', '%' . $key . '%')
                        ->orWhere('stockholder_name', 'like', '%' . $key . '%');
                }

                if ($ledger && $ledger->certificate_no) {
                    $query->orWhere('stock_number', $ledger->certificate_no);
                }
            })
            ->latest()
            ->first();

        $journal = StockTransferJournal::query()
            ->where(function ($query) use ($key, $ledger) {
                if ($key !== '') {
                    $query->orWhere('certificate_no', $key);
                    $this->applyNameTokens($query, $key, ['shareholder']);
                }

                if ($ledger && $ledger->certificate_no) {
                    $query->orWhere('certificate_no', $ledger->certificate_no);
                }
            })
            ->latest()
            ->first();

        $stockholderName = $ledger
            ? trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' '))
            : ($installment?->subscriber ?: $certificate?->stockholder_name);

        $stockholderRecord = null;
        if ($hasStockholdersTable && $stockholderName) {
            $stockholderQuery = Stockholder::query();
            $this->applyNameTokens($stockholderQuery, $stockholderName, ['stockholder_name']);
            $stockholderRecord = $stockholderQuery->latest()->first();
        }

        $amount = $installment?->total_value
            ?? $certificate?->amount
            ?? (($ledger?->shares && $parValue) ? ((float) $ledger->shares * (float) $parValue) : null);

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
                'holder_name' => $installment->subscriber ?: $stockholderName,
                'installment_date' => optional($installment->installment_date)->toDateString(),
                'no_shares' => $installment->no_shares,
                'no_installments' => $installment->no_installments,
                'par_value' => $installment->par_value ?: $parValue,
                'total_value' => $installment->total_value,
                'installment_amount' => $installment->installment_amount,
                'status' => $installment->payment_status,
                'payment_total' => $installment->paymentTotal(),
                'payment_count' => $installment->paymentCount(),
                'mode' => $installment->installmentMode(),
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

    public function defaults()
    {
        $manilaNow = Carbon::now('Asia/Manila');

        return response()->json([
            'today' => $manilaNow->toDateString(),
            'now' => $manilaNow->format('Y-m-d\TH:i'),
            'journal_no' => $this->nextJournalNo(),
            'ledger_folio' => $this->nextLedgerFolio(),
            'stock_number' => $this->nextStockNumber(),
            'reference_no' => Schema::hasTable('stock_transfer_issuance_requests')
                ? $this->nextSequenceFor(StockTransferIssuanceRequest::class, 'reference_no', 'REQ-')
                : 'REQ-0001',
        ]);
    }
}