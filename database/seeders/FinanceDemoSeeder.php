<?php

namespace Database\Seeders;

use App\Models\FinanceRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceDemoSeeder extends Seeder
{
    private function seedRecord(array $match, array $values): FinanceRecord
    {
        return FinanceRecord::updateOrCreate($match, $values);
    }

    public function run(): void
    {
        $actor = User::where('email', 'superadmin@jknc.com')->first() ?? User::query()->first();

        if (!$actor) {
            $this->command?->warn('FinanceDemoSeeder skipped: no user found to attach seed records.');
            return;
        }

        $baseMeta = [
            'status' => 'Active',
            'workflow_status' => 'Accepted',
            'approval_status' => 'Approved',
            'submitted_by' => $actor->id,
            'submitted_at' => now(),
            'approved_by' => $actor->id,
            'approved_at' => now(),
            'review_note' => 'Seeded demo data for finance workflow testing.',
            'user' => $actor->name,
        ];

        $supplier = $this->seedRecord(
            ['module_key' => 'supplier', 'record_number' => 'SUP-0001'],
            $baseMeta + [
                'record_title' => 'Lynxis Office Supplies Inc.',
                'record_date' => '2026-04-10',
                'data' => [
                    'completion_mode' => 'complete_internally',
                    'trade_name' => 'Lynxis Office Supplies',
                    'supplier_type' => 'Local Vendor',
                    'representative_full_name' => 'Maria Santos',
                    'designation' => 'Sales Manager',
                    'email_address' => 'sales@lynxis-office.test',
                    'phone_number' => '09171234567',
                    'alternate_contact_number' => '09179876543',
                    'business_address' => '123 Finance St., Makati City',
                    'billing_address' => '123 Billing St., Makati City',
                    'tin' => '123-456-789-000',
                    'vat_status' => 'VAT',
                    'payment_terms' => 'Net 30',
                    'accreditation_status' => 'Accredited',
                    'bank_name' => 'BPI',
                    'bank_account_name' => 'Lynxis Office Supplies Inc.',
                    'bank_account_number' => '1234567890',
                    'remarks' => 'Primary supplier for workflow testing.',
                ],
            ]
        );

        $service = $this->seedRecord(
            ['module_key' => 'service', 'record_number' => 'SRV-0001'],
            $baseMeta + [
                'record_title' => 'Office Cleaning Service',
                'record_date' => '2026-04-10',
                'amount' => 2500.00,
                'data' => [
                    'service_description' => 'Weekly office cleaning and maintenance.',
                    'supplier_id' => $supplier->id,
                    'coa_id' => null, // updated after chart account seed
                    'category' => 'Facilities',
                    'unit_of_measure' => 'per service/job',
                    'default_cost' => 2500,
                    'tax_type' => 'VAT',
                    'service_status' => 'Active',
                    'remarks' => 'Used by PR and PO workflow tests.',
                ],
            ]
        );

        $product = $this->seedRecord(
            ['module_key' => 'product', 'record_number' => 'PRD-0001'],
            $baseMeta + [
                'record_title' => 'A4 Bond Paper',
                'record_date' => '2026-04-10',
                'amount' => 450.00,
                'data' => [
                    'product_description' => 'White bond paper, 70gsm, 500 sheets.',
                    'supplier_id' => $supplier->id,
                    'coa_id' => null, // updated after chart account seed
                    'category' => 'Office Supplies',
                    'unit_of_measure' => 'per unit',
                    'default_cost' => 450,
                    'tax_type' => 'Non-VAT',
                    'product_status' => 'Active',
                    'remarks' => 'Used for product master lookup tests.',
                ],
            ]
        );

        $chartAccount = $this->seedRecord(
            ['module_key' => 'chart_account', 'record_number' => 'COA-1001'],
            $baseMeta + [
                'record_title' => 'Office Supplies Expense',
                'record_date' => '2026-04-10',
                'data' => [
                    'account_description' => 'Expense account for office consumables and services.',
                    'is_sub_account' => false,
                    'parent_account_id' => null,
                    'account_type' => 'Expense',
                    'account_group' => 'Operating Expenses',
                    'normal_balance' => 'Debit',
                    'account_status' => 'Active',
                    'remarks' => 'Used by finance workflow seed data.',
                ],
            ]
        );

        $bankAccount = $this->seedRecord(
            ['module_key' => 'bank_account', 'record_number' => 'BANK-0001'],
            $baseMeta + [
                'record_title' => 'Main Operating Account',
                'record_date' => '2026-04-10',
                'data' => [
                    'bank_name' => 'BPI',
                    'branch' => 'Makati Main',
                    'currency' => 'PHP',
                    'bank_status' => 'Active',
                    'account_type' => 'Checking',
                    'linked_coa_id' => $chartAccount->id,
                    'signatory_notes' => 'Any two signatories required.',
                    'remarks' => 'Primary bank account for testing.',
                ],
            ]
        );

        $destinationBankAccount = $this->seedRecord(
            ['module_key' => 'bank_account', 'record_number' => 'BANK-0002'],
            $baseMeta + [
                'record_title' => 'Payroll Clearing Account',
                'record_date' => '2026-04-10',
                'data' => [
                    'bank_name' => 'BDO',
                    'branch' => 'Ortigas Center',
                    'currency' => 'PHP',
                    'bank_status' => 'Active',
                    'account_type' => 'Savings',
                    'linked_coa_id' => $chartAccount->id,
                    'signatory_notes' => 'Primary payroll transfer destination.',
                    'remarks' => 'Second bank account for IBTF testing.',
                ],
            ]
        );

        $service->update([
            'data' => array_merge($service->data ?? [], ['coa_id' => $chartAccount->id]),
        ]);

        $product->update([
            'data' => array_merge($product->data ?? [], ['coa_id' => $chartAccount->id]),
        ]);

        $pr = $this->seedRecord(
            ['module_key' => 'pr', 'record_number' => 'PR-0001'],
            $baseMeta + [
                'record_title' => 'Cleaning Request',
                'record_date' => '2026-04-10',
                'amount' => 2500.00,
                'data' => [
                    'requesting_department' => 'Administration',
                    'requestor' => 'Juan Dela Cruz',
                    'request_type' => 'Service',
                    'supplier_id' => $supplier->id,
                    'master_item_type' => 'service',
                    'master_item_id' => $service->id,
                    'description_specification' => 'Quarterly office cleaning for the main floor.',
                    'quantity' => 1,
                    'unit_cost' => 2500,
                    'estimated_total_cost' => 2500,
                    'coa_id' => $chartAccount->id,
                    'purpose' => 'Maintain office cleanliness and safety.',
                    'needed_date' => '2026-04-12',
                    'remarks' => 'Seeded PR for workflow testing.',
                ],
            ]
        );

        $po = $this->seedRecord(
            ['module_key' => 'po', 'record_number' => 'PO-0001'],
            $baseMeta + [
                'record_title' => 'Office Cleaning PO',
                'record_date' => '2026-04-10',
                'amount' => 2500.00,
                'data' => [
                    'linked_pr_id' => $pr->id,
                    'supplier_id' => $supplier->id,
                    'delivery_address' => 'Main Office, Makati City',
                    'terms_and_conditions' => 'Service to be rendered within 7 days.',
                    'linked_item_type' => 'service',
                    'linked_item_id' => $service->id,
                    'quantity' => 1,
                    'unit_cost' => 2500,
                    'total_amount' => 2500,
                    'coa_id' => $chartAccount->id,
                    'expected_delivery_date' => '2026-04-15',
                    'remarks' => 'Seeded PO for workflow testing.',
                ],
            ]
        );

        $caShortage = $this->seedRecord(
            ['module_key' => 'ca', 'record_number' => 'CA-0001'],
            $baseMeta + [
                'record_title' => 'Petty Cash Advance - Shortage Flow',
                'record_date' => '2026-04-10',
                'amount' => 5000.00,
                'data' => [
                    'requestor' => 'Juan Dela Cruz',
                    'department' => 'Administration',
                    'purpose' => 'Field expense testing with shortage branch.',
                    'amount_requested' => 5000,
                    'needed_date' => '2026-04-11',
                    'mode_of_release' => 'Cash',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'remarks' => 'Linked to a shortage liquidation record.',
                ],
            ]
        );

        $dvFromCaShortage = $this->seedRecord(
            ['module_key' => 'dv', 'record_number' => 'DV-0001'],
            $baseMeta + [
                'record_title' => 'DV for Cash Advance CA-0001',
                'record_date' => '2026-04-10',
                'amount' => 5000.00,
                'data' => [
                    'source_document_type' => 'ca',
                    'source_document_id' => $caShortage->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 5000,
                    'payment_type' => 'Cash',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'reference_number' => 'PAY-CA-0001',
                    'purpose' => 'Release of cash advance.',
                    'payment_date' => '2026-04-10',
                    'remarks' => 'Seeded DV from CA.',
                ],
            ]
        );

        $lrShortage = $this->seedRecord(
            ['module_key' => 'lr', 'record_number' => 'LR-0001'],
            $baseMeta + [
                'record_title' => 'Liquidation Shortage',
                'record_date' => '2026-04-12',
                'amount' => 5500.00,
                'data' => [
                    'linked_dv_id' => $dvFromCaShortage->id,
                    'linked_ca_id' => $caShortage->id,
                    'total_cash_advance' => 5000,
                    'actual_expenses' => 5500,
                    'variance' => -500,
                    'variance_indicator' => 'Shortage',
                    'expense_line_items' => 'Office fare, snacks, supplies',
                    'supplier_id' => $supplier->id,
                    'coa_id' => $chartAccount->id,
                    'official_receipt' => 'OR-001',
                    'remarks' => 'Shortage scenario for ERR branch.',
                ],
            ]
        );

        $err = $this->seedRecord(
            ['module_key' => 'err', 'record_number' => 'ERR-0001'],
            $baseMeta + [
                'record_title' => 'Expense Reimbursement - Shortage',
                'record_date' => '2026-04-12',
                'amount' => 500.00,
                'data' => [
                    'linked_lr_id' => $lrShortage->id,
                    'expense_details' => 'Shortage reimbursement for unliquidated expenses.',
                    'amount' => 500,
                    'supplier_id' => $supplier->id,
                    'coa_id' => $chartAccount->id,
                    'reimbursement_mode' => 'Bank Transfer',
                    'bank_account_id' => $bankAccount->id,
                    'remarks' => 'Seeded ERR for workflow testing.',
                ],
            ]
        );

        $dvFromErr = $this->seedRecord(
            ['module_key' => 'dv', 'record_number' => 'DV-0002'],
            $baseMeta + [
                'record_title' => 'DV for ERR-0001',
                'record_date' => '2026-04-12',
                'amount' => 500.00,
                'data' => [
                    'source_document_type' => 'err',
                    'source_document_id' => $err->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 500,
                    'payment_type' => 'Bank Transfer',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'reference_number' => 'PAY-ERR-0001',
                    'purpose' => 'Reimbursement payment.',
                    'payment_date' => '2026-04-12',
                    'remarks' => 'Seeded DV from ERR.',
                ],
            ]
        );

        $caOverage = $this->seedRecord(
            ['module_key' => 'ca', 'record_number' => 'CA-0002'],
            $baseMeta + [
                'record_title' => 'Petty Cash Advance - Overage Flow',
                'record_date' => '2026-04-10',
                'amount' => 3000.00,
                'data' => [
                    'requestor' => 'Maria Reyes',
                    'department' => 'Operations',
                    'purpose' => 'Travel and meal testing with overage branch.',
                    'amount_requested' => 3000,
                    'needed_date' => '2026-04-11',
                    'mode_of_release' => 'Cash',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'remarks' => 'Linked to an overage liquidation record.',
                ],
            ]
        );

        $dvFromCaOverage = $this->seedRecord(
            ['module_key' => 'dv', 'record_number' => 'DV-0003'],
            $baseMeta + [
                'record_title' => 'DV for Cash Advance CA-0002',
                'record_date' => '2026-04-10',
                'amount' => 3000.00,
                'data' => [
                    'source_document_type' => 'ca',
                    'source_document_id' => $caOverage->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 3000,
                    'payment_type' => 'Cash',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'reference_number' => 'PAY-CA-0002',
                    'purpose' => 'Release of cash advance.',
                    'payment_date' => '2026-04-10',
                    'remarks' => 'Seeded DV for second CA.',
                ],
            ]
        );

        $lrOverage = $this->seedRecord(
            ['module_key' => 'lr', 'record_number' => 'LR-0002'],
            $baseMeta + [
                'record_title' => 'Liquidation Overage',
                'record_date' => '2026-04-12',
                'amount' => 2600.00,
                'data' => [
                    'linked_dv_id' => $dvFromCaOverage->id,
                    'linked_ca_id' => $caOverage->id,
                    'total_cash_advance' => 3000,
                    'actual_expenses' => 2600,
                    'variance' => 400,
                    'variance_indicator' => 'Overage',
                    'expense_line_items' => 'Transport, meals, incidentals',
                    'supplier_id' => $supplier->id,
                    'coa_id' => $chartAccount->id,
                    'official_receipt' => 'OR-002',
                    'remarks' => 'Overage scenario for CRF branch.',
                ],
            ]
        );

        $crf = $this->seedRecord(
            ['module_key' => 'crf', 'record_number' => 'CRF-0001'],
            $baseMeta + [
                'record_title' => 'Cash Return - Overage',
                'record_date' => '2026-04-12',
                'amount' => 400.00,
                'data' => [
                    'linked_lr_id' => $lrOverage->id,
                    'amount_returned' => 400,
                    'mode_of_return' => 'Cash',
                    'receiving_bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'reference_number' => 'CR-0001',
                    'remarks' => 'Seeded CRF for workflow testing.',
                ],
            ]
        );

        $pda = $this->seedRecord(
            ['module_key' => 'pda', 'record_number' => 'PDA-0001'],
            $baseMeta + [
                'record_title' => 'Payroll Release Authorization',
                'record_date' => '2026-04-15',
                'amount' => 85000.00,
                'data' => [
                    'total_payroll_amount' => 85000,
                    'department' => 'All Departments',
                    'funding_bank_account_id' => $bankAccount->id,
                    'payroll_expense_coa_id' => $chartAccount->id,
                    'supporting_payroll_summary' => 'Seed payroll summary for April 15 cycle.',
                    'remarks' => 'Used to test PDA to DV flow.',
                ],
            ]
        );

        $ibtf = $this->seedRecord(
            ['module_key' => 'ibtf', 'record_number' => 'IBTF-0001'],
            $baseMeta + [
                'record_title' => 'Interbank Transfer Test',
                'record_date' => '2026-04-14',
                'amount' => 15000.00,
                'data' => [
                    'source_bank_account_id' => $bankAccount->id,
                    'destination_bank_account_id' => $destinationBankAccount->id,
                    'amount' => 15000,
                    'reason' => 'Testing interbank transfer flow.',
                    'source_account_code' => 'BANK-0001',
                    'destination_account_code' => 'BANK-0002',
                    'transfer_reference_number' => 'TRF-0001',
                    'remarks' => 'This record is intentionally seeded for workflow tests.',
                ],
            ]
        );

        $dvFromPo = $this->seedRecord(
            ['module_key' => 'dv', 'record_number' => 'DV-0004'],
            $baseMeta + [
                'record_title' => 'DV for PO-0001',
                'record_date' => '2026-04-10',
                'amount' => 2500.00,
                'data' => [
                    'source_document_type' => 'po',
                    'source_document_id' => $po->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 2500,
                    'payment_type' => 'Bank Transfer',
                    'bank_account_id' => $bankAccount->id,
                    'coa_id' => $chartAccount->id,
                    'reference_number' => 'PAY-PO-0001',
                    'purpose' => 'Payment against approved purchase order.',
                    'payment_date' => '2026-04-13',
                    'remarks' => 'Seeded DV for PO-to-ARF testing.',
                ],
            ]
        );

        $arf = $this->seedRecord(
            ['module_key' => 'arf', 'record_number' => 'ARF-0001'],
            $baseMeta + [
                'record_title' => 'Asset Registration - Printer',
                'record_date' => '2026-04-13',
                'amount' => 2500.00,
                'data' => [
                    'linked_po_id' => $po->id,
                    'linked_dv_id' => $dvFromPo->id,
                    'asset_description' => 'Office multifunction printer for the admin team.',
                    'asset_category' => 'Office Equipment',
                    'serial_number' => 'SN-ARF-0001',
                    'model' => 'HP LaserJet Pro',
                    'supplier_id' => $supplier->id,
                    'acquisition_cost' => 2500,
                    'acquisition_date' => '2026-04-13',
                    'asset_coa_id' => $chartAccount->id,
                    'location' => 'Administration Office',
                    'custodian' => 'Juan Dela Cruz',
                    'useful_life' => '5 years',
                    'residual_value' => 0,
                    'remarks' => 'Asset seeded from PO / DV workflow.',
                ],
            ]
        );

        $this->command?->info(sprintf(
            'Seeded finance demo data: supplier #%d, service #%d, product #%d, chart account #%d, bank account #%d, PR #%d, PO #%d, CA/LR/ERR/DV/PDA/CRF/IBTF/ARF records.',
            $supplier->id,
            $service->id,
            $product->id,
            $chartAccount->id,
            $bankAccount->id,
            $pr->id,
            $po->id
        ));
    }
}
