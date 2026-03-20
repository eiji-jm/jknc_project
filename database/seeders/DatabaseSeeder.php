<?php

namespace Database\Seeders;

use App\Models\BirTax;
use App\Models\Contact;
use App\Models\Minute;
use App\Models\NatGov;
use App\Models\Notice;
use App\Models\Resolution;
use App\Models\SecretaryCertificate;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Models\UltimateBeneficialOwner;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        if (Contact::count() === 0) {
            Contact::insert([
                [
                    'name' => 'Kelly, John',
                    'email' => 'john.kelly@email.com',
                    'nationality' => 'Filipino',
                    'address' => '1234 Elm Street, Ayala',
                    'tax_id' => '123-45-6789',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Rodriguez, Carmen',
                    'email' => 'carmen.rodriguez@email.com',
                    'nationality' => 'Filipino',
                    'address' => '5678 Oak Avenue, Makati',
                    'tax_id' => '456-78-9012',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Thompson, Elizabeth',
                    'email' => 'elizabeth.thompson@email.com',
                    'nationality' => 'American',
                    'address' => '3456 Maple Drive, Ortigas',
                    'tax_id' => '567-89-0123',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (UltimateBeneficialOwner::count() === 0) {
            UltimateBeneficialOwner::insert([
                [
                    'complete_name' => 'Kelly, John',
                    'email' => 'john.kelly@email.com',
                    'residential_address' => '1234 Elm Street, Ayala',
                    'nationality' => 'Filipino',
                    'date_of_birth' => '1970-11-12',
                    'tax_identification_no' => '123-45-6789',
                    'ownership_percentage' => 100,
                    'ownership_type' => 'Direct (D)',
                    'ownership_category' => 'Primary (P)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'complete_name' => 'Rodriguez, Carmen',
                    'email' => 'carmen.rodriguez@email.com',
                    'residential_address' => '5678 Oak Avenue, Makati',
                    'nationality' => 'Filipino',
                    'date_of_birth' => '1985-03-22',
                    'tax_identification_no' => '456-78-9012',
                    'ownership_percentage' => 25,
                    'ownership_type' => 'Direct (D)',
                    'ownership_category' => 'Secondary (S)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'complete_name' => 'Thompson, Elizabeth',
                    'email' => 'elizabeth.thompson@email.com',
                    'residential_address' => '3456 Maple Drive, Ortigas',
                    'nationality' => 'American',
                    'date_of_birth' => '1992-09-08',
                    'tax_identification_no' => '567-89-0123',
                    'ownership_percentage' => 10,
                    'ownership_type' => 'Indirect (I)',
                    'ownership_category' => 'Secondary (S)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (StockTransferLedger::count() === 0) {
            StockTransferLedger::insert([
                [
                    'family_name' => 'Kelly',
                    'first_name' => 'John',
                    'middle_name' => 'Michael',
                    'nationality' => 'Filipino',
                    'address' => '1234 Elm Street, Ayala',
                    'tin' => '123-45-6789',
                    'email' => 'john.kelly@jkc.com',
                    'phone' => '+63 2 1234 5678',
                    'shares' => 1000,
                    'certificate_no' => 'CERT-001',
                    'date_registered' => '2026-01-15',
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'family_name' => 'Rodriguez',
                    'first_name' => 'Carmen',
                    'middle_name' => 'Maria',
                    'nationality' => 'Filipino',
                    'address' => '5678 Oak Avenue, Makati',
                    'tin' => '456-78-9012',
                    'email' => 'carmen.rodriguez@email.com',
                    'phone' => '+63 2 8765 4321',
                    'shares' => 500,
                    'certificate_no' => 'CERT-002',
                    'date_registered' => '2026-02-01',
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'family_name' => 'Santos',
                    'first_name' => 'Miguel',
                    'middle_name' => 'Antonio',
                    'nationality' => 'Filipino',
                    'address' => '9012 Cedar Road, BGC',
                    'tin' => '234-56-7890',
                    'email' => 'miguel.santos@email.com',
                    'phone' => '+63 2 5432 1098',
                    'shares' => 750,
                    'certificate_no' => 'CERT-003',
                    'date_registered' => '2026-02-10',
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (StockTransferJournal::count() === 0) {
            StockTransferJournal::insert([
                [
                    'entry_date' => '2026-01-15',
                    'journal_no' => 'JNL-001',
                    'ledger_folio' => 'LED-001',
                    'particulars' => 'Initial share issuance to John Kelly',
                    'no_shares' => 1000,
                    'transaction_type' => 'Issuance',
                    'certificate_no' => 'CERT-0001',
                    'shareholder' => 'John Kelly',
                    'remarks' => 'Original issuance of shares at par value',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'entry_date' => '2026-02-10',
                    'journal_no' => 'JNL-002',
                    'ledger_folio' => 'LED-002',
                    'particulars' => 'Share transfer from Carmen Rodriguez to Miguel Santos',
                    'no_shares' => 500,
                    'transaction_type' => 'Transfer',
                    'certificate_no' => 'CERT-0002',
                    'shareholder' => 'Carmen Rodriguez -> Miguel Santos',
                    'remarks' => 'Transfer of ownership documented',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'entry_date' => '2026-02-28',
                    'journal_no' => 'JNL-003',
                    'ledger_folio' => 'LED-001',
                    'particulars' => 'Share cancellation',
                    'no_shares' => 100,
                    'transaction_type' => 'Cancellation',
                    'certificate_no' => 'CERT-0001B',
                    'shareholder' => 'John Kelly',
                    'remarks' => 'Shares cancelled and removed from circulation',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (StockTransferInstallment::count() === 0) {
            StockTransferInstallment::insert([
                [
                    'stock_number' => 'STK-001',
                    'subscriber' => 'John Kelly',
                    'installment_date' => '2026-01-15',
                    'no_shares' => 1000,
                    'no_installments' => 4,
                    'total_value' => 100000,
                    'installment_amount' => 25000,
                    'status' => 'Ongoing',
                    'schedule' => json_encode([
                        ['no' => '1st', 'dueDate' => '2026-01-15', 'amount' => '25000.00', 'status' => 'Paid'],
                        ['no' => '2nd', 'dueDate' => '2026-02-15', 'amount' => '25000.00', 'status' => 'Pending'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'stock_number' => 'STK-002',
                    'subscriber' => 'Carmen Rodriguez',
                    'installment_date' => '2026-02-01',
                    'no_shares' => 500,
                    'no_installments' => 2,
                    'total_value' => 50000,
                    'installment_amount' => 25000,
                    'status' => 'Ongoing',
                    'schedule' => json_encode([
                        ['no' => '1st', 'dueDate' => '2026-02-01', 'amount' => '25000.00', 'status' => 'Pending'],
                        ['no' => '2nd', 'dueDate' => '2026-03-01', 'amount' => '25000.00', 'status' => 'Pending'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'stock_number' => 'STK-003',
                    'subscriber' => 'Miguel Santos',
                    'installment_date' => '2026-02-15',
                    'no_shares' => 750,
                    'no_installments' => 3,
                    'total_value' => 75000,
                    'installment_amount' => 25000,
                    'status' => 'Ongoing',
                    'schedule' => json_encode([
                        ['no' => '1st', 'dueDate' => '2026-02-15', 'amount' => '25000.00', 'status' => 'Paid'],
                        ['no' => '2nd', 'dueDate' => '2026-03-15', 'amount' => '25000.00', 'status' => 'Pending'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (StockTransferCertificate::count() === 0) {
            StockTransferCertificate::insert([
                [
                    'date_uploaded' => '2026-01-20',
                    'uploaded_by' => 'Admin',
                    'corporation_name' => 'John Kelly & Company',
                    'company_reg_no' => '12345-ABC',
                    'stock_number' => 'STK-001',
                    'stockholder_name' => 'John Kelly',
                    'par_value' => 100,
                    'number' => 1000,
                    'amount' => 100000,
                    'amount_in_words' => 'One Hundred Thousand Pesos',
                    'date_issued' => '2026-01-22',
                    'president' => 'John Kelly',
                    'corporate_secretary' => 'Maria Santos',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'date_uploaded' => '2026-02-01',
                    'uploaded_by' => 'Admin',
                    'corporation_name' => 'John Kelly & Company',
                    'company_reg_no' => '12345-ABC',
                    'stock_number' => 'STK-002',
                    'stockholder_name' => 'Carmen Rodriguez',
                    'par_value' => 100,
                    'number' => 500,
                    'amount' => 50000,
                    'amount_in_words' => 'Fifty Thousand Pesos',
                    'date_issued' => '2026-02-03',
                    'president' => 'John Kelly',
                    'corporate_secretary' => 'Maria Santos',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'date_uploaded' => '2026-02-10',
                    'uploaded_by' => 'Admin',
                    'corporation_name' => 'John Kelly & Company',
                    'company_reg_no' => '12345-ABC',
                    'stock_number' => 'STK-003',
                    'stockholder_name' => 'Miguel Santos',
                    'par_value' => 100,
                    'number' => 750,
                    'amount' => 75000,
                    'amount_in_words' => 'Seventy Five Thousand Pesos',
                    'date_issued' => '2026-02-12',
                    'president' => 'John Kelly',
                    'corporate_secretary' => 'Maria Santos',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Notice::count() === 0) {
            Notice::insert([
                [
                    'notice_number' => '2024-001',
                    'date_of_notice' => '2024-01-15',
                    'governing_body' => 'Board of Directors',
                    'type_of_meeting' => 'Regular',
                    'date_of_meeting' => '2024-02-01',
                    'time_started' => '10:00',
                    'location' => 'Conference Room A',
                    'meeting_no' => '1',
                    'chairman' => 'John Smith',
                    'secretary' => 'Jane Doe',
                    'uploaded_by' => 'Admin User',
                    'date_updated' => '2024-01-15',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'notice_number' => '2024-002',
                    'date_of_notice' => '2024-01-20',
                    'governing_body' => 'Shareholders',
                    'type_of_meeting' => 'Annual General',
                    'date_of_meeting' => '2024-02-15',
                    'time_started' => '14:00',
                    'location' => 'Main Hall',
                    'meeting_no' => '2',
                    'chairman' => 'Robert Brown',
                    'secretary' => 'Sarah Wilson',
                    'uploaded_by' => 'Compliance Officer',
                    'date_updated' => '2024-01-20',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'notice_number' => '2024-003',
                    'date_of_notice' => '2024-01-25',
                    'governing_body' => 'Audit Committee',
                    'type_of_meeting' => 'Special',
                    'date_of_meeting' => '2024-02-08',
                    'time_started' => '11:00',
                    'location' => 'Audit Office',
                    'meeting_no' => '3',
                    'chairman' => 'Michael Johnson',
                    'secretary' => 'Emily Davis',
                    'uploaded_by' => 'Finance Manager',
                    'date_updated' => '2024-01-25',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Minute::count() === 0) {
            Minute::insert([
                [
                    'minutes_ref' => 'MIN-2024-001',
                    'date_uploaded' => '2024-02-02',
                    'uploaded_by' => 'Admin User',
                    'governing_body' => 'Board of Directors',
                    'type_of_meeting' => 'Regular',
                    'meeting_mode' => 'In-Person',
                    'notice_ref' => '2024-001',
                    'date_of_meeting' => '2024-02-01',
                    'time_started' => '10:00',
                    'time_ended' => '11:30',
                    'location' => 'Conference Room A',
                    'call_link' => '',
                    'recording_notes' => '',
                    'meeting_no' => '1',
                    'chairman' => 'John Smith',
                    'secretary' => 'Jane Doe',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'minutes_ref' => 'MIN-2024-002',
                    'date_uploaded' => '2024-02-16',
                    'uploaded_by' => 'Compliance Officer',
                    'governing_body' => 'Shareholders',
                    'type_of_meeting' => 'Annual General',
                    'meeting_mode' => 'Hybrid',
                    'notice_ref' => '2024-002',
                    'date_of_meeting' => '2024-02-15',
                    'time_started' => '14:00',
                    'time_ended' => '16:15',
                    'location' => 'Main Hall',
                    'call_link' => 'https://meet.example.com/agm-2024',
                    'recording_notes' => '',
                    'meeting_no' => '2',
                    'chairman' => 'Robert Brown',
                    'secretary' => 'Sarah Wilson',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'minutes_ref' => 'MIN-2024-003',
                    'date_uploaded' => '2024-02-09',
                    'uploaded_by' => 'Finance Manager',
                    'governing_body' => 'Audit Committee',
                    'type_of_meeting' => 'Special',
                    'meeting_mode' => 'Virtual',
                    'notice_ref' => '2024-003',
                    'date_of_meeting' => '2024-02-08',
                    'time_started' => '11:00',
                    'time_ended' => '12:30',
                    'location' => 'Audit Office',
                    'call_link' => 'https://meet.example.com/audit-2024',
                    'recording_notes' => '',
                    'meeting_no' => '3',
                    'chairman' => 'Michael Johnson',
                    'secretary' => 'Emily Davis',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Resolution::count() === 0) {
            Resolution::insert([
                [
                    'resolution_no' => 'RES-2024-001',
                    'date_uploaded' => '2024-02-05',
                    'uploaded_by' => 'Admin User',
                    'governing_body' => 'Board of Directors',
                    'type_of_meeting' => 'Regular',
                    'notice_ref' => '2024-001',
                    'meeting_no' => '1',
                    'date_of_meeting' => '2024-02-01',
                    'location' => 'Conference Room A',
                    'board_resolution' => 'Approval of Budget',
                    'directors' => 'J. Smith, R. Brown',
                    'chairman' => 'John Smith',
                    'secretary' => 'Jane Doe',
                    'notary_doc_no' => '102',
                    'notary_page_no' => '21',
                    'notary_book_no' => 'III',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Mark Cruz',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'resolution_no' => 'RES-2024-002',
                    'date_uploaded' => '2024-02-18',
                    'uploaded_by' => 'Compliance Officer',
                    'governing_body' => 'Shareholders',
                    'type_of_meeting' => 'Annual General',
                    'notice_ref' => '2024-002',
                    'meeting_no' => '2',
                    'date_of_meeting' => '2024-02-15',
                    'location' => 'Main Hall',
                    'board_resolution' => 'Election of Directors',
                    'directors' => 'S. Wilson, M. Johnson',
                    'chairman' => 'Robert Brown',
                    'secretary' => 'Sarah Wilson',
                    'notary_doc_no' => '207',
                    'notary_page_no' => '33',
                    'notary_book_no' => 'IV',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Paula Reyes',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'resolution_no' => 'RES-2024-003',
                    'date_uploaded' => '2024-02-10',
                    'uploaded_by' => 'Finance Manager',
                    'governing_body' => 'Audit Committee',
                    'type_of_meeting' => 'Special',
                    'notice_ref' => '2024-003',
                    'meeting_no' => '3',
                    'date_of_meeting' => '2024-02-08',
                    'location' => 'Audit Office',
                    'board_resolution' => 'Internal Controls Update',
                    'directors' => 'E. Davis, M. Johnson',
                    'chairman' => 'Michael Johnson',
                    'secretary' => 'Emily Davis',
                    'notary_doc_no' => '255',
                    'notary_page_no' => '12',
                    'notary_book_no' => 'IV',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Joel Perez',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (SecretaryCertificate::count() === 0) {
            SecretaryCertificate::insert([
                [
                    'certificate_no' => 'SEC-2024-001',
                    'date_uploaded' => '2024-02-07',
                    'uploaded_by' => 'Admin User',
                    'governing_body' => 'Board of Directors',
                    'type_of_meeting' => 'Regular',
                    'notice_ref' => '2024-001',
                    'meeting_no' => '1',
                    'resolution_no' => 'RES-2024-001',
                    'date_issued' => '2024-02-03',
                    'purpose' => 'Bank Account Opening',
                    'date_of_meeting' => '2024-02-01',
                    'location' => 'Conference Room A',
                    'secretary' => 'Jane Doe',
                    'notary_doc_no' => '110',
                    'notary_page_no' => '28',
                    'notary_book_no' => 'III',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Mark Cruz',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'certificate_no' => 'SEC-2024-002',
                    'date_uploaded' => '2024-02-20',
                    'uploaded_by' => 'Compliance Officer',
                    'governing_body' => 'Shareholders',
                    'type_of_meeting' => 'Annual General',
                    'notice_ref' => '2024-002',
                    'meeting_no' => '2',
                    'resolution_no' => 'RES-2024-002',
                    'date_issued' => '2024-02-16',
                    'purpose' => 'Appointment of Officers',
                    'date_of_meeting' => '2024-02-15',
                    'location' => 'Main Hall',
                    'secretary' => 'Sarah Wilson',
                    'notary_doc_no' => '221',
                    'notary_page_no' => '34',
                    'notary_book_no' => 'IV',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Paula Reyes',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'certificate_no' => 'SEC-2024-003',
                    'date_uploaded' => '2024-02-12',
                    'uploaded_by' => 'Finance Manager',
                    'governing_body' => 'Audit Committee',
                    'type_of_meeting' => 'Special',
                    'notice_ref' => '2024-003',
                    'meeting_no' => '3',
                    'resolution_no' => 'RES-2024-003',
                    'date_issued' => '2024-02-09',
                    'purpose' => 'Tax Compliance Filing',
                    'date_of_meeting' => '2024-02-08',
                    'location' => 'Audit Office',
                    'secretary' => 'Emily Davis',
                    'notary_doc_no' => '259',
                    'notary_page_no' => '15',
                    'notary_book_no' => 'IV',
                    'notary_series_no' => '2024',
                    'notary_public' => 'Atty. Joel Perez',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (BirTax::count() === 0) {
            BirTax::insert([
                [
                    'tin' => '123-456-789-000',
                    'tax_payer' => 'John Kelly & Co.',
                    'registering_office' => 'BIR RDO 44',
                    'registered_address' => 'Makati City, PH',
                    'tax_types' => 'VAT, WHT',
                    'form_type' => '1701Q',
                    'filing_frequency' => 'Quarterly',
                    'due_date' => '2024-04-30',
                    'uploaded_by' => 'Admin User',
                    'date_uploaded' => '2024-02-06',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tin' => '987-654-321-000',
                    'tax_payer' => 'JKC Holdings',
                    'registering_office' => 'BIR RDO 51',
                    'registered_address' => 'Quezon City, PH',
                    'tax_types' => 'Income Tax',
                    'form_type' => '1702',
                    'filing_frequency' => 'Annual',
                    'due_date' => '2024-04-15',
                    'uploaded_by' => 'Compliance Officer',
                    'date_uploaded' => '2024-02-18',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tin' => '555-222-333-000',
                    'tax_payer' => 'JKC Services',
                    'registering_office' => 'BIR RDO 38',
                    'registered_address' => 'Cebu City, PH',
                    'tax_types' => 'Percentage Tax',
                    'form_type' => '2551M',
                    'filing_frequency' => 'Monthly',
                    'due_date' => '2024-03-20',
                    'uploaded_by' => 'Finance Manager',
                    'date_uploaded' => '2024-02-12',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (NatGov::count() === 0) {
            NatGov::insert([
                [
                    'client' => 'John Kelly & Co.',
                    'tin' => '123-456-789-000',
                    'agency' => 'SSS',
                    'registration_status' => 'Registered',
                    'registration_date' => '2021-06-15',
                    'registration_no' => 'SSS-001234',
                    'status' => 'Active',
                    'uploaded_by' => 'Admin User',
                    'date_uploaded' => '2024-02-06',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'client' => 'John Kelly & Co.',
                    'tin' => '123-456-789-000',
                    'agency' => 'Pag-IBIG',
                    'registration_status' => 'Registered',
                    'registration_date' => '2021-06-20',
                    'registration_no' => 'PAG-009876',
                    'status' => 'Active',
                    'uploaded_by' => 'Compliance Officer',
                    'date_uploaded' => '2024-02-18',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'client' => 'John Kelly & Co.',
                    'tin' => '123-456-789-000',
                    'agency' => 'PhilHealth',
                    'registration_status' => 'Registered',
                    'registration_date' => '2021-07-01',
                    'registration_no' => 'PH-004321',
                    'status' => 'Active',
                    'uploaded_by' => 'Finance Manager',
                    'date_uploaded' => '2024-02-12',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
