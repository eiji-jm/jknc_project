<?php

namespace App\Http\Controllers;

use App\Mail\SupplierCompletionMail;
use App\Models\FinanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    private const MODULES = [
        'supplier' => 'Supplier',
        'service' => 'Service',
        'product' => 'Product',
        'chart_account' => 'Chart of Accounts',
        'bank_account' => 'Bank Accounts',
        'pr' => 'Purchase Request',
        'po' => 'Purchase Order',
        'ca' => 'Cash Advance',
        'lr' => 'Liquidation Report',
        'err' => 'Expense Reimbursement Request',
        'dv' => 'Disbursement Voucher',
        'pda' => 'Payroll Disbursement Authorization',
        'crf' => 'Cash Return Form',
        'ibtf' => 'Interbank Fund Transfer Form',
        'arf' => 'Asset Registration Form',
    ];

    private const WORKFLOW_STATUSES = [
        'Uploaded',
        'Submitted',
        'Shared',
        'Accepted',
        'Reverted',
        'Archived',
    ];

    private function canApproveFinance(): bool
    {
        return Auth::check() && Auth::user()->hasPermission('approve_corporate');
    }

    private function moduleKeys(): array
    {
        return array_keys(self::MODULES);
    }

    private function moduleLabel(string $moduleKey): string
    {
        return self::MODULES[$moduleKey] ?? Str::headline($moduleKey);
    }

    private function moduleRecordTitleLabel(string $moduleKey): string
    {
        return match ($moduleKey) {
            'supplier' => 'Supplier Name',
            'service' => 'Service Name',
            'product' => 'Product Name',
            'chart_account' => 'Account Name',
            'bank_account' => 'Bank Account Name',
            'pr' => 'Request Title',
            'po' => 'Order Title',
            'ca' => 'Requestor',
            'lr' => 'Liquidating Person',
            'err' => 'Requestor',
            'dv' => 'Payee',
            'pda' => 'Payroll Period',
            'crf' => 'Returnee',
            'ibtf' => 'Transfer Title',
            'arf' => 'Asset Name',
            default => 'Record Name',
        };
    }

    private function financePdfImageDataUri(string $relativePath): ?string
    {
        $absolutePath = public_path($relativePath);

        if (!is_file($absolutePath)) {
            return null;
        }

        $contents = file_get_contents($absolutePath);
        if ($contents === false) {
            return null;
        }

        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    private function financeAttachmentUrl(?array $attachment): ?string
    {
        $path = trim((string) data_get($attachment, 'path', ''));

        if ($path === '') {
            return null;
        }

        return route('uploads.show', ['path' => $path]);
    }

    private function financePdfLookupLabel(array $lookupOptions, string $moduleKey, mixed $id): ?string
    {
        if (blank($id) || !array_key_exists($moduleKey, $lookupOptions)) {
            return null;
        }

        foreach ($lookupOptions[$moduleKey] as $option) {
            if ((string) ($option['id'] ?? '') === (string) $id) {
                return (string) ($option['label'] ?? $option['record_number'] ?? $option['id']);
            }
        }

        return null;
    }

    private function financePdfValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $flattened = array_filter(array_map(function ($item) {
                if (is_array($item)) {
                    return json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                return blank($item) ? null : (string) $item;
            }, $value));

            return $flattened ? implode(', ', $flattened) : 'N/A';
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? 'N/A' : $stringValue;
    }

    private function financeBarcodeSvg(?string $value): string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return '';
        }

        $normalized = strtoupper(preg_replace('/\s+/', '', $text) ?: '');
        if ($normalized === '') {
            return '';
        }

        $seed = 0;
        foreach (str_split($normalized) as $index => $char) {
            $seed += ord($char) * ($index + 3);
        }

        $bits = '1010';
        foreach (str_split($normalized) as $char) {
            $code = ord($char) ^ ($seed & 0xff);
            $bits .= str_pad(decbin($code), 8, '0', STR_PAD_LEFT);
        }
        $bits .= '110101';

        $unit = 2;
        $quietZone = 10;
        $height = 56;
        $textY = 84;
        $cursor = $quietZone;
        $current = $bits[0] ?? '0';
        $runLength = 0;
        $rects = '';

        $flushRun = function () use (&$rects, &$cursor, &$runLength, &$current, $unit, $height) {
            if ($runLength > 0 && $current === '1') {
                $rects .= sprintf(
                    '<rect x="%d" y="8" width="%d" height="%d" fill="#111827" />',
                    $cursor,
                    $runLength * $unit,
                    $height
                );
            }

            $cursor += $runLength * $unit;
            $runLength = 0;
        };

        foreach (str_split($bits) as $bit) {
            if ($bit === $current) {
                $runLength++;
                continue;
            }

            $flushRun();
            $current = $bit;
            $runLength = 1;
        }

        $flushRun();

        $width = max(240, $cursor + $quietZone);
        $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $centerX = (int) round($width / 2);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} 96" role="img" aria-label="Barcode for {$safeText}">
    <rect x="0" y="0" width="{$width}" height="96" rx="10" fill="#ffffff" />
    <rect x="0" y="0" width="{$width}" height="96" rx="10" fill="none" stroke="#e5e7eb" />
    <g>{$rects}</g>
    <text x="{$centerX}" y="{$textY}" text-anchor="middle" font-family="monospace" font-size="11" fill="#111827">{$safeText}</text>
</svg>
SVG;
    }

    private function financePreviewFieldValue(FinanceRecord $record, string $fieldName, array $lookupOptions): string
    {
        $data = $record->data ?? [];
        $value = data_get($data, $fieldName);

        if ($fieldName === 'requester_mode') {
            return match ($value) {
                'own_request' => 'Own Request',
                'request_for_another' => 'Request for Another',
                default => $this->financePdfValue($value),
            };
        }

        if (in_array($fieldName, ['completion_mode', 'vat_status', 'accreditation_status', 'tax_type', 'payment_type', 'mode_of_release', 'mode_of_return', 'reimbursement_mode', 'bank_status', 'account_type', 'account_status', 'service_status', 'product_status', 'normal_balance', 'variance_indicator', 'purchase_type', 'priority', 'request_type'], true)) {
            return $this->financePdfValue($value);
        }

        return match ($fieldName) {
            'supplier_id' => $this->financePdfLookupLabel($lookupOptions, 'supplier', $value) ?: $this->financePdfValue($value),
            'coa_id', 'parent_account_id', 'payroll_expense_coa_id', 'asset_coa_id' => $this->financePdfLookupLabel($lookupOptions, 'chart_account', $value) ?: $this->financePdfValue($value),
            'bank_account_id', 'funding_bank_account_id', 'receiving_bank_account_id', 'source_bank_account_id', 'destination_bank_account_id' => $this->financePdfLookupLabel($lookupOptions, 'bank_account', $value) ?: $this->financePdfValue($value),
            'linked_pr_id' => $this->financePdfLookupLabel($lookupOptions, 'pr', $value) ?: $this->financePdfValue($value),
            'linked_po_id' => $this->financePdfLookupLabel($lookupOptions, 'po', $value) ?: $this->financePdfValue($value),
            'linked_ca_id' => $this->financePdfLookupLabel($lookupOptions, 'ca', $value) ?: $this->financePdfValue($value),
            'linked_lr_id' => $this->financePdfLookupLabel($lookupOptions, 'lr', $value) ?: $this->financePdfValue($value),
            'linked_dv_id' => $this->financePdfLookupLabel($lookupOptions, 'dv', $value) ?: $this->financePdfValue($value),
            'source_document_id' => $this->financePdfLookupLabel($lookupOptions, (string) data_get($data, 'source_document_type', ''), $value) ?: $this->financePdfValue($value),
            'master_item_id' => $this->financePdfLookupLabel($lookupOptions, (string) data_get($data, 'master_item_type', 'product'), $value) ?: $this->financePdfValue($value),
            'linked_item_id' => $this->financePdfLookupLabel($lookupOptions, (string) data_get($data, 'linked_item_type', 'product'), $value) ?: $this->financePdfValue($value),
            default => $this->financePdfValue($value),
        };
    }

    private function financePreviewRow(FinanceRecord $record, array $lookupOptions, string $fieldName, ?string $label = null): array
    {
        return [
            'label' => $label ?: Str::headline(str_replace('_id', ' id', $fieldName)),
            'value' => $this->financePreviewFieldValue($record, $fieldName, $lookupOptions),
        ];
    }

    private function financePreviewSections(FinanceRecord $record, array $lookupOptions): array
    {
        $moduleKey = $record->module_key;
        $data = $record->data ?? [];
        $notesSection = [
            'type' => 'notes',
            'title' => 'Review Notes',
        ];

        $section = function (string $title, array $fields) use ($record, $lookupOptions): array {
            return [
                'type' => 'fields',
                'title' => $title,
                'rows' => array_map(fn ($field) => is_array($field)
                    ? $this->financePreviewRow($record, $lookupOptions, $field['name'], $field['label'] ?? null)
                    : $this->financePreviewRow($record, $lookupOptions, $field), $fields),
            ];
        };

        return match ($moduleKey) {
            'supplier' => data_get($data, 'completion_mode') === 'send_to_supplier' && blank($record->supplier_completed_at)
                ? []
                : [
                    $section('Supplier Profile', [
                        ['name' => 'completion_mode', 'label' => 'Completion Mode'],
                        ['name' => 'trade_name', 'label' => 'Trade Name'],
                        ['name' => 'supplier_type', 'label' => 'Supplier Type'],
                        ['name' => 'representative_full_name', 'label' => 'Representative Full Name'],
                        ['name' => 'designation', 'label' => 'Designation'],
                        ['name' => 'email_address', 'label' => 'Email Address'],
                        ['name' => 'phone_number', 'label' => 'Phone Number'],
                        ['name' => 'alternate_contact_number', 'label' => 'Alternate Contact Number'],
                    ]),
                $section('Business & Billing', [
                    ['name' => 'business_address', 'label' => 'Business Address'],
                    ['name' => 'billing_address', 'label' => 'Billing Address'],
                    ['name' => 'tin', 'label' => 'TIN'],
                    ['name' => 'vat_status', 'label' => 'VAT / Non-VAT'],
                    ['name' => 'payment_terms', 'label' => 'Payment Terms'],
                    ['name' => 'accreditation_status', 'label' => 'Accreditation Status'],
                ]),
                $section('Banking & Notes', [
                    ['name' => 'bank_name', 'label' => 'Bank Name'],
                    ['name' => 'bank_account_name', 'label' => 'Bank Account Name'],
                    ['name' => 'bank_account_number', 'label' => 'Bank Account Number'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'service' => [
                $section('Service Profile', [
                    ['name' => 'service_description', 'label' => 'Service Description'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                    ['name' => 'category', 'label' => 'Category'],
                    ['name' => 'unit_of_measure', 'label' => 'Unit of Measure'],
                    ['name' => 'default_cost', 'label' => 'Default Cost'],
                ]),
                $section('Classification & Notes', [
                    ['name' => 'tax_type', 'label' => 'Tax Type'],
                    ['name' => 'service_status', 'label' => 'Service Status'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'product' => [
                $section('Product Profile', [
                    ['name' => 'product_description', 'label' => 'Product Description'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                    ['name' => 'category', 'label' => 'Category'],
                    ['name' => 'unit_of_measure', 'label' => 'Unit of Measure'],
                    ['name' => 'default_cost', 'label' => 'Default Cost'],
                ]),
                $section('Classification & Notes', [
                    ['name' => 'tax_type', 'label' => 'Tax Type'],
                    ['name' => 'product_status', 'label' => 'Product Status'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'chart_account' => [
                $section('Account Profile', [
                    ['name' => 'account_description', 'label' => 'Account Description'],
                    ['name' => 'is_sub_account', 'label' => 'Sub-Account'],
                    ['name' => 'parent_account_id', 'label' => 'Main Account'],
                    ['name' => 'account_type', 'label' => 'Account Type'],
                    ['name' => 'account_group', 'label' => 'Account Group'],
                ]),
                $section('Balance & Status', [
                    ['name' => 'normal_balance', 'label' => 'Normal Balance'],
                    ['name' => 'account_status', 'label' => 'Status'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'bank_account' => [
                $section('Bank Profile', [
                    ['name' => 'bank_name', 'label' => 'Bank Name'],
                    ['name' => 'branch', 'label' => 'Branch'],
                    ['name' => 'currency', 'label' => 'Currency'],
                    ['name' => 'account_type', 'label' => 'Account Type'],
                    ['name' => 'bank_status', 'label' => 'Status'],
                ]),
                $section('Accounting Link & Notes', [
                    ['name' => 'linked_coa_id', 'label' => 'Linked Chart of Account'],
                    ['name' => 'signatory_notes', 'label' => 'Signatory Notes'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'pr' => [
                $section('Request Details', [
                    ['name' => 'requesting_department', 'label' => 'Department'],
                    ['name' => 'requester_mode', 'label' => 'Requester Option'],
                    ['name' => 'request_type', 'label' => 'Type'],
                    ['name' => 'priority', 'label' => 'Priority'],
                    ['name' => 'purchase_type', 'label' => 'Purchase Type'],
                    ['name' => 'needed_date', 'label' => 'Needed Date'],
                ]),
                $section('Requester Details', [
                    ['name' => 'requestor', 'label' => 'Employee Name'],
                    ['name' => 'employee_id', 'label' => 'Employee ID'],
                    ['name' => 'employee_email', 'label' => 'Email'],
                    ['name' => 'contact_number', 'label' => 'Contact #'],
                    ['name' => 'position', 'label' => 'Position'],
                    ['name' => 'superior', 'label' => 'Superior'],
                    ['name' => 'superior_email', 'label' => 'Superior Email'],
                ]),
                $section('Vendor Details', [
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'new_vendor', 'label' => 'New Vendor?'],
                    ['name' => 'vendor_id_number', 'label' => 'Vendor ID Number'],
                    ['name' => 'vendors_tin', 'label' => 'Vendors TIN#'],
                    ['name' => 'company_name', 'label' => 'Company'],
                    ['name' => 'vendor_phone', 'label' => 'Phone Number'],
                    ['name' => 'vendor_email', 'label' => 'Email'],
                    ['name' => 'vendor_address', 'label' => 'Address'],
                    ['name' => 'city', 'label' => 'City'],
                    ['name' => 'province', 'label' => 'Province'],
                    ['name' => 'zip', 'label' => 'Zip'],
                ]),
                ['type' => 'line_items', 'title' => 'Items / Cost Details'],
                $section('Purpose & Notes', [
                    ['name' => 'purpose', 'label' => 'Purpose / Justification'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'po' => [
                $section('Order Details', [
                    ['name' => 'linked_pr_id', 'label' => 'Linked PR'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'expected_delivery_date', 'label' => 'Expected Delivery Date'],
                    ['name' => 'delivery_address', 'label' => 'Delivery Address'],
                    ['name' => 'terms_and_conditions', 'label' => 'Terms and Conditions'],
                ]),
                $section('Item & Cost Details', [
                    ['name' => 'linked_item_type', 'label' => 'Items / Services Type'],
                    ['name' => 'linked_item_id', 'label' => 'Items / Services'],
                    ['name' => 'quantity', 'label' => 'Quantity'],
                    ['name' => 'unit_cost', 'label' => 'Unit Cost'],
                    ['name' => 'total_amount', 'label' => 'Total Amount'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                ]),
                $section('Notes', [
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'ca' => [
                $section('Request Details', [
                    ['name' => 'requestor', 'label' => 'Requestor'],
                    ['name' => 'needed_date', 'label' => 'Needed Date'],
                    ['name' => 'priority', 'label' => 'Priority'],
                    ['name' => 'cash_advance_type', 'label' => 'Cash Advance Type'],
                    ['name' => 'for_client', 'label' => 'For Client?'],
                    ['name' => 'purpose', 'label' => 'Justification / Business Need'],
                    ['name' => 'usage_categories', 'label' => 'Cash Advance Usage / Expense Categories'],
                    ['name' => 'other_business_purpose_specify', 'label' => 'Other Business Purpose - Specify'],
                    ['name' => 'other_expense_specify', 'label' => 'Other Expense - Specify'],
                    ['name' => 'client_names', 'label' => 'Client Name(s)'],
                ]),
                $section('Requester Details', [
                    ['name' => 'employee_id', 'label' => 'Employee ID'],
                    ['name' => 'employee_name', 'label' => 'Employee Name'],
                    ['name' => 'employee_email', 'label' => 'Email'],
                    ['name' => 'contact_number', 'label' => 'Contact #'],
                    ['name' => 'position', 'label' => 'Position'],
                    ['name' => 'department', 'label' => 'Department'],
                    ['name' => 'superior', 'label' => 'Superior'],
                    ['name' => 'superior_email', 'label' => 'Superior Email'],
                ]),
                $section('Cash Advance Details', [
                    ['name' => 'amount_requested', 'label' => 'Amount Requested'],
                    ['name' => 'mode_of_release', 'label' => 'Mode of Release'],
                    ['name' => 'paid_through', 'label' => 'Paid Through'],
                ]),
                $section('Declarations & Authorizations', [
                    ['name' => 'official_business_cash_advance', 'label' => 'Official Business Cash Advance'],
                    ['name' => 'employee_cash_advance_personal', 'label' => 'Employee Cash Advance - Personal Purpose'],
                    ['name' => 'liquidation_non_compliance', 'label' => 'Liquidation Non-Compliance'],
                    ['name' => 'automatic_salary_deduction_authorization', 'label' => 'Automatic Salary Deduction Authorization'],
                    ['name' => 'final_pay_deduction_authorization', 'label' => 'Final Pay Deduction Authorization'],
                    ['name' => 'policy_acknowledgment', 'label' => 'Policy Acknowledgment'],
                ]),
                $section('Funding & Notes', [
                    ['name' => 'bank_account_id', 'label' => 'Bank Account / Cash Source'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'lr' => [
                $section('Liquidation Details', [
                    ['name' => 'linked_ca_id', 'label' => 'CA Reference No.'],
                    ['name' => 'total_cash_advance', 'label' => 'CA Amount'],
                    ['name' => 'purpose', 'label' => 'Justification / Business Need'],
                ]),
                $section('Requester Details', [
                    ['name' => 'employee_id', 'label' => 'Employee ID'],
                    ['name' => 'employee_name', 'label' => 'Employee Name'],
                    ['name' => 'employee_email', 'label' => 'Email'],
                    ['name' => 'contact_number', 'label' => 'Contact #'],
                    ['name' => 'position', 'label' => 'Position'],
                    ['name' => 'department', 'label' => 'Department'],
                    ['name' => 'superior', 'label' => 'Superior'],
                    ['name' => 'superior_email', 'label' => 'Superior Email'],
                ]),
                [
                    'type' => 'liquidation_report',
                    'title' => 'Liquidation Report',
                ],
                [
                    'type' => 'line_items',
                    'title' => 'Liquidation Cost Details',
                ],
                [
                    'type' => 'cost_summary',
                    'title' => 'Liquidation Summary',
                ],
                $notesSection,
            ],
            'err' => [
                $section('Reimbursement Details', [
                    ['name' => 'linked_lr_id', 'label' => 'Linked LR'],
                    ['name' => 'expense_details', 'label' => 'Expense Details'],
                    ['name' => 'amount', 'label' => 'Amount'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'reimbursement_mode', 'label' => 'Mode of Reimbursement'],
                ]),
                $section('Accounting & Funding', [
                    ['name' => 'coa_id', 'label' => 'Account from Chart of Accounts'],
                    ['name' => 'bank_account_id', 'label' => 'Bank Account'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'dv' => [
                $section('Voucher Details', [
                    ['name' => 'source_document_type', 'label' => 'Linked Source Document Type'],
                    ['name' => 'source_document_id', 'label' => 'Linked Source Document'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'amount', 'label' => 'Amount'],
                    ['name' => 'payment_type', 'label' => 'Payment Type'],
                    ['name' => 'payment_date', 'label' => 'Payment Date'],
                ]),
                $section('Accounting & Notes', [
                    ['name' => 'bank_account_id', 'label' => 'Bank Account'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                    ['name' => 'reference_number', 'label' => 'Reference Number'],
                    ['name' => 'purpose', 'label' => 'Purpose'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'pda' => [
                $section('Payroll Details', [
                    ['name' => 'total_payroll_amount', 'label' => 'Total Payroll Amount'],
                    ['name' => 'department', 'label' => 'Department / Coverage'],
                    ['name' => 'funding_bank_account_id', 'label' => 'Funding Bank Account'],
                    ['name' => 'payroll_expense_coa_id', 'label' => 'Payroll Expense Account'],
                ]),
                $section('Supporting Notes', [
                    ['name' => 'supporting_payroll_summary', 'label' => 'Supporting Payroll Summary'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'crf' => [
                $section('Return Details', [
                    ['name' => 'linked_lr_id', 'label' => 'Linked LR'],
                    ['name' => 'amount_returned', 'label' => 'Amount Returned'],
                    ['name' => 'mode_of_return', 'label' => 'Mode of Return'],
                    ['name' => 'receiving_bank_account_id', 'label' => 'Receiving Bank / Cash Account'],
                    ['name' => 'coa_id', 'label' => 'Account'],
                ]),
                $section('Reference & Notes', [
                    ['name' => 'reference_number', 'label' => 'Reference Number'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'ibtf' => [
                $section('Transfer Details', [
                    ['name' => 'source_bank_account_id', 'label' => 'Source Bank Account'],
                    ['name' => 'destination_bank_account_id', 'label' => 'Destination Bank Account'],
                    ['name' => 'amount', 'label' => 'Amount'],
                    ['name' => 'reason', 'label' => 'Reason / Purpose'],
                ]),
                $section('Reference & Notes', [
                    ['name' => 'source_account_code', 'label' => 'Source Account Code'],
                    ['name' => 'destination_account_code', 'label' => 'Destination Account Code'],
                    ['name' => 'transfer_reference_number', 'label' => 'Transfer Reference Number'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            'arf' => [
                $section('Asset Details', [
                    ['name' => 'linked_po_id', 'label' => 'Linked PO'],
                    ['name' => 'linked_dv_id', 'label' => 'Linked DV'],
                    ['name' => 'supplier_id', 'label' => 'Supplier'],
                    ['name' => 'asset_code', 'label' => 'Asset Code'],
                    ['name' => 'asset_description', 'label' => 'Asset Description'],
                    ['name' => 'asset_category', 'label' => 'Asset Category'],
                    ['name' => 'serial_number', 'label' => 'Serial Number'],
                    ['name' => 'model', 'label' => 'Model'],
                ]),
                [
                    'type' => 'asset_tag',
                    'title' => 'Asset Tag',
                    'asset_code' => data_get($data, 'asset_code') ?: $record->record_number ?: 'N/A',
                    'location' => data_get($data, 'location') ?: 'N/A',
                    'serial_number' => data_get($data, 'serial_number') ?: 'N/A',
                    'barcode_svg' => $this->financeBarcodeSvg(data_get($data, 'asset_code') ?: $record->record_number ?: ''),
                ],
                $section('Valuation & Custody', [
                    ['name' => 'acquisition_cost', 'label' => 'Acquisition Cost'],
                    ['name' => 'acquisition_date', 'label' => 'Acquisition Date'],
                    ['name' => 'asset_coa_id', 'label' => 'Asset Account from Chart of Accounts'],
                    ['name' => 'location', 'label' => 'Location'],
                    ['name' => 'custodian', 'label' => 'Custodian'],
                    ['name' => 'useful_life', 'label' => 'Useful Life'],
                    ['name' => 'residual_value', 'label' => 'Residual Value'],
                    ['name' => 'remarks', 'label' => 'Remarks'],
                ]),
                $notesSection,
            ],
            default => [],
        };
    }

    private function financePdfContext(FinanceRecord $record, bool $includeLogo = true): array
    {
        $data = $record->data ?? [];
        $lookupOptions = $this->resolveLookupOptions();
        $moduleLabel = $this->moduleLabel($record->module_key);
        $recordTitleLabel = $this->moduleRecordTitleLabel($record->module_key);
        $companyName = 'John Kelly & Company';
        $companyLegalName = 'JK&C INC.';
        $companyLogo = $includeLogo ? $this->financePdfImageDataUri('images/imaglogo.png') : null;
        $summaryCards = [
            ['label' => 'Module', 'value' => $moduleLabel],
            ['label' => 'Record Number', 'value' => $this->normalizeFinanceRecordNumber($record->module_key, $record->record_number)],
            ['label' => $recordTitleLabel, 'value' => $record->record_title ?: 'N/A'],
            ['label' => 'Record Date', 'value' => optional($record->record_date)->format('Y-m-d') ?: 'N/A'],
            ['label' => 'Amount', 'value' => $record->amount !== null ? number_format((float) $record->amount, 2) : 'N/A'],
            ['label' => 'Status', 'value' => $record->status ?: 'N/A'],
            ['label' => 'Workflow', 'value' => $record->workflow_status ?: 'N/A'],
            ['label' => 'Approval', 'value' => $record->approval_status ?: 'N/A'],
            ['label' => 'Created By', 'value' => $record->user ?: 'N/A'],
            ['label' => 'Submitted At', 'value' => optional($record->submitted_at)->format('Y-m-d H:i:s') ?: 'N/A'],
            ['label' => 'Approved At', 'value' => optional($record->approved_at)->format('Y-m-d H:i:s') ?: 'N/A'],
        ];

        $lineItems = [];
        foreach ((array) data_get($data, 'line_items', []) as $item) {
            $quantity = (float) data_get($item, 'quantity', 0);
            $amount = (float) data_get($item, 'amount', 0);
            $subtotal = (float) data_get($item, 'subtotal', $quantity * $amount);
            $discountAmount = (float) data_get($item, 'discount_amount', 0);
            $shippingAmount = (float) data_get($item, 'shipping_amount', 0);
            $taxAmount = (float) data_get($item, 'tax_amount', 0);
            $whtAmount = (float) data_get($item, 'wht_amount', 0);
            $total = (float) data_get($item, 'total', $subtotal - $discountAmount + $shippingAmount + $taxAmount - $whtAmount);
            $lineItems[] = [
                'item' => $this->financePdfLookupLabel($lookupOptions, 'product', data_get($item, 'item_id')) ?: $this->financePdfValue(data_get($item, 'item_id')),
                'description' => $this->financePdfValue(data_get($item, 'description')),
                'category' => $this->financePdfValue(data_get($item, 'category')),
                'quantity' => $this->financePdfValue(data_get($item, 'quantity')),
                'amount' => $this->financePdfValue(data_get($item, 'amount')),
                'subtotal' => number_format($subtotal, 2),
                'discount_amount' => number_format($discountAmount, 2),
                'shipping_amount' => number_format($shippingAmount, 2),
                'tax_amount' => number_format($taxAmount, 2),
                'wht_amount' => number_format($whtAmount, 2),
                'total' => number_format($total, 2),
            ];
        }
        $lineItemsTotal = array_reduce((array) data_get($data, 'line_items', []), function (float $carry, $item) {
            return $carry + (float) data_get($item, 'total', 0);
        }, 0.0);

        $costSummary = match ($record->module_key) {
            'lr' => [
                ['label' => 'Subtotal', 'value' => $data['subtotal'] ?? '0.00'],
                ['label' => 'Discount Total', 'value' => $data['discount_total'] ?? '0.00'],
                ['label' => 'Tax Total', 'value' => $data['tax_total'] ?? '0.00'],
                ['label' => 'Shipping Total', 'value' => $data['shipping_total'] ?? '0.00'],
                ['label' => 'WHT Total', 'value' => $data['wht_total'] ?? '0.00'],
                ['label' => 'Grand Total', 'value' => $data['grand_total'] ?? $record->amount ?? '0.00'],
            ],
            default => [
                ['label' => 'Subtotal', 'value' => $data['subtotal'] ?? $record->amount ?? '0.00'],
                ['label' => 'Discount', 'value' => $data['discount'] ?? '0%'],
                ['label' => 'Discount Amount', 'value' => $data['discount_amount'] ?? '0.00'],
                ['label' => 'Shipping', 'value' => $data['shipping_amount'] ?? '0.00'],
                ['label' => 'Tax (VAT/Non-VAT/N/A)', 'value' => $data['tax_type'] ?? 'N/A'],
                ['label' => 'Tax Amount', 'value' => $data['tax_amount'] ?? '0.00'],
                ['label' => 'WHT', 'value' => $data['wht_amount'] ?? '0.00'],
                ['label' => 'Grand Total', 'value' => $data['grand_total'] ?? $record->amount ?? '0.00'],
            ],
        };

        $attachments = array_values(array_map(function ($attachment) {
            $attachment = is_array($attachment) ? $attachment : [];
            $attachment['url'] = $this->financeAttachmentUrl($attachment);
            $attachment['download_url'] = $attachment['url'] ? $attachment['url'] . '?download=1' : null;

            return $attachment;
        }, array_filter((array) ($record->attachments ?? []), fn ($attachment) => !blank(data_get($attachment, 'name')) || !blank(data_get($attachment, 'path')))));

        $detailRows = $record->module_key === 'arf'
            ? [
                $this->financePreviewRow($record, $lookupOptions, 'asset_code', 'Asset Code'),
                $this->financePreviewRow($record, $lookupOptions, 'linked_po_id', 'Linked PO'),
                $this->financePreviewRow($record, $lookupOptions, 'linked_dv_id', 'Linked DV'),
                $this->financePreviewRow($record, $lookupOptions, 'supplier_id', 'Supplier'),
                $this->financePreviewRow($record, $lookupOptions, 'asset_description', 'Asset Description'),
                $this->financePreviewRow($record, $lookupOptions, 'asset_category', 'Asset Category'),
                $this->financePreviewRow($record, $lookupOptions, 'serial_number', 'Serial Number'),
                $this->financePreviewRow($record, $lookupOptions, 'model', 'Model'),
                $this->financePreviewRow($record, $lookupOptions, 'acquisition_cost', 'Acquisition Cost'),
                $this->financePreviewRow($record, $lookupOptions, 'acquisition_date', 'Acquisition Date'),
                $this->financePreviewRow($record, $lookupOptions, 'asset_coa_id', 'Asset Account from Chart of Accounts'),
                $this->financePreviewRow($record, $lookupOptions, 'location', 'Location'),
                $this->financePreviewRow($record, $lookupOptions, 'custodian', 'Custodian'),
                $this->financePreviewRow($record, $lookupOptions, 'useful_life', 'Useful Life'),
                $this->financePreviewRow($record, $lookupOptions, 'residual_value', 'Residual Value'),
                $this->financePreviewRow($record, $lookupOptions, 'remarks', 'Remarks'),
            ]
            : [];

        $liquidationReport = $record->module_key === 'lr'
            ? [
                'ca_reference_no' => $this->financePdfLookupLabel($lookupOptions, 'ca', data_get($data, 'linked_ca_id')) ?: data_get($data, 'linked_ca_id') ?: 'N/A',
                'ca_amount' => data_get($data, 'total_cash_advance') ?: '0.00',
                'line_items_total' => number_format($lineItemsTotal, 2),
                'actual_expenses' => data_get($data, 'actual_expenses') ?: '0.00',
                'variance' => data_get($data, 'variance') ?: '0.00',
                'variance_indicator' => data_get($data, 'variance_indicator') ?: 'Balanced',
                'purpose' => data_get($data, 'purpose') ?: 'N/A',
                'remarks' => data_get($data, 'remarks') ?: 'N/A',
                'employee_name' => data_get($data, 'employee_name') ?: data_get($data, 'employee_id') ?: 'N/A',
                'status_label' => $this->financePdfValue(data_get($data, 'variance_indicator') ?: 'Balanced'),
                'calculation_label' => 'Line Items Total',
                'calculation_formula' => 'Line Items Total = Sum of all line item totals',
            ]
            : null;

        return [
            'companyName' => $companyName,
            'companyLegalName' => $companyLegalName,
            'companyLogo' => $companyLogo,
            'moduleLabel' => $moduleLabel,
            'recordTitleLabel' => $recordTitleLabel,
            'record' => $record,
            'assetTag' => $record->module_key === 'arf' ? [
                'asset_code' => data_get($data, 'asset_code') ?: $record->record_number ?: 'N/A',
                'location' => data_get($data, 'location') ?: 'N/A',
                'serial_number' => data_get($data, 'serial_number') ?: 'N/A',
                'barcode_svg' => $this->financeBarcodeSvg(data_get($data, 'asset_code') ?: $record->record_number ?: ''),
            ] : null,
            'summaryCards' => $summaryCards,
            'detailRows' => $detailRows,
            'previewSections' => $this->financePreviewSections($record, $lookupOptions),
            'lineItems' => $lineItems,
            'costSummary' => $costSummary,
            'liquidationReport' => $liquidationReport,
            'attachments' => $attachments,
            'chartAccountLabel' => $this->financePdfLookupLabel($lookupOptions, 'chart_account', data_get($data, 'coa_id')) ?: data_get($data, 'coa_id') ?: 'N/A',
        ];
    }

    private function acceptedRecordQuery(string $moduleKey, array $dataConstraints = [])
    {
        $query = FinanceRecord::query()
            ->where('module_key', $moduleKey)
            ->where(function ($statusQuery) {
                $statusQuery->where('workflow_status', 'Accepted')
                    ->orWhere('approval_status', 'Approved');
            });

        foreach ($dataConstraints as $field => $value) {
            $query->where("data->{$field}", $value);
        }

        return $query;
    }

    private function canEditRecord(FinanceRecord $record): bool
    {
        if ($this->canApproveFinance()) {
            return true;
        }

        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function canSubmitRecord(FinanceRecord $record): bool
    {
        return (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true);
    }

    private function canShareSupplierRecord(FinanceRecord $record): bool
    {
        return $record->module_key === 'supplier'
            && (int) $record->submitted_by === (int) Auth::id()
            && in_array($record->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)
            && data_get($record->data, 'completion_mode') === 'send_to_supplier';
    }

    private function canManageSupplierCompletion(FinanceRecord $record): bool
    {
        return $record->module_key === 'supplier'
            && data_get($record->data, 'completion_mode') === 'send_to_supplier'
            && ($this->canApproveFinance() || (int) $record->submitted_by === (int) Auth::id());
    }

    private function supplierCompletionEmailAddress(FinanceRecord $record): ?string
    {
        $email = trim((string) data_get($record->data, 'email_address', ''));

        return blank($email) ? null : $email;
    }

    private function ensureSupplierCompletionLink(FinanceRecord $record): string
    {
        $record->update([
            'share_token' => $record->share_token ?: Str::random(64),
            'shared_at' => now(),
            'workflow_status' => 'Shared',
            'approval_status' => 'Pending Supplier Completion',
        ]);

        return route('finance.supplier.completion', $record->fresh()->share_token);
    }

    private function sendSupplierCompletionEmail(FinanceRecord $record): string
    {
        $email = $this->supplierCompletionEmailAddress($record);

        if (blank($email)) {
            throw ValidationException::withMessages([
                'data.email_address' => 'Supplier email address is required to send the completion form.',
            ]);
        }

        $link = $this->ensureSupplierCompletionLink($record);
        $freshRecord = $record->fresh();

        Mail::to($email)->send(
            new SupplierCompletionMail($freshRecord, $link)
        );

        return $link;
    }

    private function optionLabel(FinanceRecord $record): string
    {
        $parts = array_filter([
            $this->normalizeFinanceRecordNumber($record->module_key, $record->record_number),
            $record->record_title,
        ]);

        if ($parts) {
            return implode(' - ', $parts);
        }

        $dataTitle = data_get($record->data, 'title')
            ?: data_get($record->data, 'business_name')
            ?: data_get($record->data, 'account_name')
            ?: data_get($record->data, 'service_name')
            ?: data_get($record->data, 'product_name')
            ?: data_get($record->data, 'payee')
            ?: data_get($record->data, 'supplier_name');

        if ($dataTitle) {
            return (string) $dataTitle;
        }

        return $this->moduleLabel($record->module_key) . ' #' . $record->id;
    }

    private function recordPrefixForModule(string $moduleKey): string
    {
        return match ($moduleKey) {
            'supplier' => 'SUP',
            'service' => 'SRV',
            'product' => 'PRD',
            'chart_account' => 'COA',
            'bank_account' => 'BA',
            'pr' => 'PR',
            'po' => 'PO',
            'ca' => 'CA',
            'lr' => 'LR',
            'err' => 'ERR',
            'dv' => 'DV',
            'pda' => 'PDA',
            'crf' => 'CRF',
            'ibtf' => 'IBTF',
            'arf' => 'ARF',
            default => Str::upper(Str::substr($moduleKey ?: 'FIN', 0, 5)),
        };
    }

    private function normalizeFinanceRecordNumber(string $moduleKey, ?string $recordNumber): string
    {
        $value = trim((string) $recordNumber);
        $prefix = $this->recordPrefixForModule($moduleKey);

        if ($value === '') {
            return $prefix . '-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        }

        if (preg_match('/^([A-Z0-9]{2,10})-(\d+)$/i', $value, $matches)) {
            $normalizedPrefix = strtoupper($matches[1]);
            $numeric = substr(str_pad($matches[2], 5, '0', STR_PAD_LEFT), -5);

            return $normalizedPrefix . '-' . $numeric;
        }

        if (preg_match('/^\d+$/', $value)) {
            return $prefix . '-' . substr(str_pad($value, 5, '0', STR_PAD_LEFT), -5);
        }

        return $value;
    }

    private function resolveLookupOptions(): array
    {
        $options = [];

        foreach (self::MODULES as $moduleKey => $label) {
            $options[$moduleKey] = $this->acceptedRecordQuery($moduleKey)
                ->orderByDesc('record_date')
                ->orderByDesc('created_at')
                ->get()
                ->map(function (FinanceRecord $record) {
                    $option = [
                        'id' => $record->id,
                        'label' => $this->optionLabel($record),
                        'record_number' => $record->record_number,
                        'record_title' => $record->record_title,
                    ];

                    if ($record->module_key === 'chart_account') {
                        $option['account_type'] = data_get($record->data, 'account_type');
                        $option['account_group'] = data_get($record->data, 'account_group');
                        $option['account_description'] = data_get($record->data, 'account_description');
                    }

                    return $option;
                })
                ->values();
        }

        $options['dv_ca'] = $this->acceptedRecordQuery('dv', ['source_document_type' => 'ca'])
            ->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => [
                'id' => $record->id,
                'label' => $this->optionLabel($record),
                'record_number' => $record->record_number,
                'record_title' => $record->record_title,
            ])
            ->values();

        $options['lr_overage'] = $this->acceptedRecordQuery('lr', ['variance_indicator' => 'Overage'])
            ->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => [
                'id' => $record->id,
                'label' => $this->optionLabel($record),
                'record_number' => $record->record_number,
                'record_title' => $record->record_title,
            ])
            ->values();

        return $options;
    }

    private function transformRecord(FinanceRecord $record): array
    {
        $data = $record->data ?? [];
        $existingDvPayload = is_array(data_get($data, 'dv_payload')) ? data_get($data, 'dv_payload') : [];

        return [
            'id' => $record->id,
            'module_key' => $record->module_key,
            'module_label' => $this->moduleLabel($record->module_key),
            'record_number' => $this->normalizeFinanceRecordNumber($record->module_key, $record->record_number),
            'record_title' => $record->record_title,
            'display_label' => $this->optionLabel($record),
            'record_date' => optional($record->record_date)->format('Y-m-d'),
            'amount' => $record->amount,
            'status' => $record->status ?? 'Active',
            'workflow_status' => $record->workflow_status ?? 'Uploaded',
            'approval_status' => $record->approval_status ?? 'Pending',
            'submitted_by' => $record->submitted_by,
            'submitted_at' => optional($record->submitted_at)->format('Y-m-d H:i:s'),
            'approved_by' => $record->approved_by,
            'approved_at' => optional($record->approved_at)->format('Y-m-d H:i:s'),
            'review_note' => $record->review_note,
            'data' => array_merge($data, [
                'dv_payload' => array_merge(
                    $this->fallbackDvPayload($record),
                    $existingDvPayload
                ),
            ]),
            'attachments' => array_values(array_map(function ($attachment) {
                $attachment = is_array($attachment) ? $attachment : [];
                $attachment['url'] = $this->financeAttachmentUrl($attachment);
                $attachment['download_url'] = $attachment['url'] ? $attachment['url'] . '?download=1' : null;

                return $attachment;
            }, (array) ($record->attachments ?? []))),
            'share_token' => $record->share_token,
            'shared_at' => optional($record->shared_at)->format('Y-m-d H:i:s'),
            'supplier_completed_at' => optional($record->supplier_completed_at)->format('Y-m-d H:i:s'),
            'user' => $record->user,
            'can_edit' => $this->canEditRecord($record),
            'can_submit' => $this->canSubmitRecord($record),
            'can_share_supplier' => $this->canShareSupplierRecord($record),
            'can_review' => $this->canApproveFinance(),
            'supplier_completion_url' => $record->share_token
                ? route('finance.supplier.completion', $record->share_token)
                : null,
        ];
    }

    private function fallbackDvPayload(FinanceRecord $record): array
    {
        $data = $record->data ?? [];

        $firstFilled = function (array $values) {
            foreach ($values as $value) {
                if (!blank($value)) {
                    return $value;
                }
            }

            return null;
        };

        return [
            'supplier_id' => data_get($data, 'supplier_id') ?? '',
            'amount' => $firstFilled([
                data_get($data, 'amount'),
                data_get($data, 'grand_total'),
                data_get($data, 'amount_requested'),
                data_get($data, 'total_cash_advance'),
                data_get($data, 'amount_returned'),
                data_get($data, 'total_payroll_amount'),
                data_get($data, 'acquisition_cost'),
                $record->amount,
            ]) ?? '',
            'payment_type' => $firstFilled([
                data_get($data, 'payment_type'),
                data_get($data, 'mode_of_release'),
                data_get($data, 'reimbursement_mode'),
                data_get($data, 'mode_of_return'),
                data_get($data, 'paid_through'),
            ]) ?? '',
            'coa_id' => $firstFilled([
                data_get($data, 'coa_id'),
                data_get($data, 'payroll_expense_coa_id'),
                data_get($data, 'asset_coa_id'),
            ]) ?? '',
            'reference_number' => $firstFilled([
                data_get($data, 'reference_number'),
                data_get($data, 'transfer_reference_number'),
                $record->record_number ? $record->record_number . '-REF' : null,
            ]) ?? '',
            'purpose' => $firstFilled([
                data_get($data, 'purpose'),
                data_get($data, 'expense_details'),
                data_get($data, 'reason'),
                data_get($data, 'supporting_payroll_summary'),
                data_get($data, 'asset_description'),
                $record->record_title,
            ]) ?? '',
            'payment_date' => optional($record->record_date)->format('Y-m-d') ?: '',
            'remarks' => data_get($data, 'remarks') ?: 'Seeded DV dummy payload.',
        ];
    }

    private function persistAttachments(Request $request, array $existingAttachments = []): array
    {
        $attachments = $existingAttachments;

        if (!$request->hasFile('attachments')) {
            return $attachments;
        }

        foreach ((array) $request->file('attachments') as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('finance_documents', 'public');

            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => 'storage/' . $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return $attachments;
    }

    private function commonValidationRules(): array
    {
        return [
            'module_key' => 'required|in:' . implode(',', $this->moduleKeys()),
            'record_number' => ['required', 'string', 'max:255', 'regex:/^[A-Z0-9]{2,10}-\d{5}$/'],
            'record_title' => 'nullable|string|max:255',
            'record_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'data' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ];
    }

    private function acceptedLinkedRecordRule(string $moduleKey, array $dataConstraints = [])
    {
        return Rule::exists('finance_records', 'id')->where(function ($query) use ($moduleKey, $dataConstraints) {
            $query->where('module_key', $moduleKey)
                ->where(function ($statusQuery) {
                    $statusQuery->where('workflow_status', 'Accepted')
                        ->orWhere('approval_status', 'Approved');
                });

            foreach ($dataConstraints as $field => $value) {
                $query->where("data->{$field}", $value);
            }
        });
    }

    private function moduleSpecificRules(string $moduleKey): array
    {
        $rules = [
            'supplier' => [
                'data.completion_mode' => 'nullable|in:complete_internally,send_to_supplier',
                'data.email_address' => 'required|email|max:255',
                'data.representative_full_name' => 'required|string|max:255',
                'data.phone_number' => 'required|string|max:255',
            ],
            'service' => [
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'product' => [
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'chart_account' => [
                'data.is_sub_account' => 'nullable|boolean',
                'data.parent_account_id' => ['nullable', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'bank_account' => [
                'data.linked_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'pr' => [
                'data.requesting_department' => 'required|string|max:255',
                'data.requester_mode' => 'nullable|in:own_request,request_for_another',
                'data.requestor' => 'required|string|max:255',
                'data.request_type' => 'required|in:Service,Product',
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.master_item_type' => 'required|in:product',
                'data.master_item_id' => 'required|integer',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.quantity' => 'nullable|numeric|min:0',
                'data.unit_cost' => 'nullable|numeric|min:0',
                'data.estimated_total_cost' => 'nullable|numeric|min:0',
            ],
            'po' => [
                'data.linked_pr_id' => ['required', $this->acceptedLinkedRecordRule('pr')],
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.linked_item_type' => 'required|in:service,product',
                'data.linked_item_id' => 'required|integer',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.quantity' => 'nullable|numeric|min:0',
                'data.unit_cost' => 'nullable|numeric|min:0',
                'data.total_amount' => 'nullable|numeric|min:0',
            ],
            'ca' => [
                'data.requestor' => 'required|string|max:255',
                'data.purpose' => 'required|string|max:2000',
                'data.amount_requested' => 'required|numeric|min:0',
                'data.mode_of_release' => 'required|in:Cash,Bank Transfer,Check',
                'data.bank_account_id' => ['nullable', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['nullable', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'lr' => [
                'data.linked_ca_id' => ['required', $this->acceptedLinkedRecordRule('ca')],
                'data.linked_dv_id' => ['nullable', $this->acceptedLinkedRecordRule('dv', ['source_document_type' => 'ca'])],
                'data.total_cash_advance' => 'required|numeric|min:0',
                'data.purpose' => 'required|string|max:2000',
                'data.employee_id' => 'nullable|string|max:255',
                'data.employee_name' => 'nullable|string|max:255',
                'data.employee_email' => 'nullable|email|max:255',
                'data.contact_number' => 'nullable|string|max:255',
                'data.position' => 'nullable|string|max:255',
                'data.department' => 'nullable|string|max:255',
                'data.superior' => 'nullable|string|max:255',
                'data.superior_email' => 'nullable|email|max:255',
                'data.actual_expenses' => 'required|numeric|min:0',
                'data.variance' => 'required|numeric',
                'data.variance_indicator' => 'required|in:Shortage,Overage,Balanced',
                'data.coa_id' => ['nullable', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'err' => [
                'data.linked_lr_id' => ['required', $this->acceptedLinkedRecordRule('lr')],
                'data.amount' => 'required|numeric|min:0',
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.supplier_id' => ['nullable', $this->acceptedLinkedRecordRule('supplier')],
            ],
            'dv' => [
                'data.source_document_type' => 'required|in:pr,po,ca,lr,err,pda,crf,ibtf,arf',
                'data.source_document_id' => 'required',
                'data.amount' => 'required|numeric|min:0',
                'data.payment_type' => 'required|in:Cash,Check,Bank Transfer,E-Wallet',
                'data.bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
                'data.supplier_id' => ['nullable', $this->acceptedLinkedRecordRule('supplier')],
            ],
            'pda' => [
                'data.total_payroll_amount' => 'required|numeric|min:0',
                'data.funding_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.payroll_expense_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'crf' => [
                'data.linked_lr_id' => ['required', $this->acceptedLinkedRecordRule('lr', ['variance_indicator' => 'Overage'])],
                'data.amount_returned' => 'required|numeric|min:0',
                'data.receiving_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
            'ibtf' => [
                'data.source_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.destination_bank_account_id' => ['required', $this->acceptedLinkedRecordRule('bank_account')],
                'data.amount' => 'required|numeric|min:0',
                'data.reason' => 'required|string|max:2000',
            ],
            'arf' => [
                'data.linked_po_id' => ['nullable', $this->acceptedLinkedRecordRule('po')],
                'data.linked_dv_id' => ['nullable', $this->acceptedLinkedRecordRule('dv')],
                'data.asset_code' => 'required|string|max:255',
                'data.supplier_id' => ['required', $this->acceptedLinkedRecordRule('supplier')],
                'data.acquisition_cost' => 'required|numeric|min:0',
                'data.acquisition_date' => 'required|date',
                'data.asset_coa_id' => ['required', $this->acceptedLinkedRecordRule('chart_account')],
            ],
        ];

        if ($moduleKey === 'po') {
            $rules['data.linked_item_id'] = ['required', $this->acceptedLinkedRecordRule('service')];
        }

        if ($moduleKey === 'pr') {
            $rules['data.master_item_id'] = ['required', $this->acceptedLinkedRecordRule('product')];
        }

        if ($moduleKey === 'chart_account') {
            $rules['data.parent_account_id'] = ['nullable', $this->acceptedLinkedRecordRule('chart_account')];
        }

        if ($moduleKey === 'dv') {
            $rules['data.source_document_id'] = [
                'required',
                'integer',
            ];
        }

        if ($moduleKey === 'lr') {
            $rules['data.linked_dv_id'] = ['nullable', $this->acceptedLinkedRecordRule('dv', ['source_document_type' => 'ca'])];
        }

        if ($moduleKey === 'arf') {
            $rules['data.linked_po_id'] = ['nullable', $this->acceptedLinkedRecordRule('po')];
            $rules['data.linked_dv_id'] = ['nullable', $this->acceptedLinkedRecordRule('dv')];
            $rules['data.linked_po_id'][] = 'required_without:data.linked_dv_id';
            $rules['data.linked_dv_id'][] = 'required_without:data.linked_po_id';
        }

        return $rules[$moduleKey] ?? [];
    }

    private function validateModulePayload(Request $request, ?FinanceRecord $financeRecord = null): void
    {
        $moduleKey = (string) $request->input('module_key', $financeRecord?->module_key);
        $supplierSendMode = $moduleKey === 'supplier'
            && data_get($request->input('data', []), 'completion_mode') === 'send_to_supplier';
        $rules = array_merge($this->commonValidationRules(), $this->moduleSpecificRules($moduleKey));

        if ($supplierSendMode) {
            $rules['record_number'] = 'nullable|string|max:255';
            $rules['record_title'] = 'nullable|string|max:255';
            $rules['record_date'] = 'nullable|date';
            $rules['amount'] = 'nullable|numeric|min:0';
            $rules['status'] = 'nullable|in:Active,Inactive';
            $rules['data.representative_full_name'] = 'nullable|string|max:255';
            $rules['data.designation'] = 'nullable|string|max:255';
            $rules['data.phone_number'] = 'nullable|string|max:255';
            $rules['data.alternate_contact_number'] = 'nullable|string|max:255';
            $rules['data.business_address'] = 'nullable|string|max:1000';
            $rules['data.billing_address'] = 'nullable|string|max:1000';
            $rules['data.tin'] = 'nullable|string|max:255';
            $rules['data.vat_status'] = 'nullable|string|max:255';
            $rules['data.payment_terms'] = 'nullable|string|max:255';
            $rules['data.accreditation_status'] = 'nullable|string|max:255';
            $rules['data.bank_name'] = 'nullable|string|max:255';
            $rules['data.bank_account_name'] = 'nullable|string|max:255';
            $rules['data.bank_account_number'] = 'nullable|string|max:255';
            $rules['data.remarks'] = 'nullable|string|max:2000';
        }

        if ($financeRecord) {
            $rules['module_key'] = ['required', Rule::in([$financeRecord->module_key])];
        }

        $validated = $request->validate($rules);

        if ($moduleKey === 'chart_account' && !blank(data_get($validated, 'data.is_sub_account')) && blank(data_get($validated, 'data.parent_account_id'))) {
            throw ValidationException::withMessages([
                'data.parent_account_id' => 'Main Account is required when Sub-Account is enabled.',
            ]);
        }

        if ($moduleKey === 'pr') {
            $itemType = data_get($validated, 'data.master_item_type');
            $itemId = data_get($validated, 'data.master_item_id');

            $itemModule = $itemType === 'product' ? 'product' : 'service';
            if (!$this->recordExistsForWorkflow($itemModule, $itemId)) {
                throw ValidationException::withMessages([
                    'data.master_item_id' => 'The selected item must exist in the chosen master list and be approved.',
                ]);
            }
        }

        if ($moduleKey === 'po') {
            $itemType = data_get($validated, 'data.linked_item_type');
            $itemId = data_get($validated, 'data.linked_item_id');

            $itemModule = $itemType === 'product' ? 'product' : 'service';
            if (!$this->recordExistsForWorkflow($itemModule, $itemId)) {
                throw ValidationException::withMessages([
                    'data.linked_item_id' => 'The selected item must exist in the chosen master list and be approved.',
                ]);
            }
        }

        if ($moduleKey === 'dv') {
            $sourceDocumentType = data_get($validated, 'data.source_document_type');
            $sourceDocumentId = data_get($validated, 'data.source_document_id');

            $allowedModule = match ($sourceDocumentType) {
                'pr' => 'pr',
                'po' => 'po',
                'ca' => 'ca',
                'lr' => 'lr',
                'err' => 'err',
                'pda' => 'pda',
                'crf' => 'crf',
                'ibtf' => 'ibtf',
                'arf' => 'arf',
                default => null,
            };

            if (!$allowedModule || !$this->recordExistsForWorkflow($allowedModule, $sourceDocumentId)) {
                throw ValidationException::withMessages([
                    'data.source_document_id' => 'The linked source document is invalid or is not yet approved.',
                ]);
            }
        }

        if ($moduleKey === 'lr') {
            $linkedDvId = data_get($validated, 'data.linked_dv_id');
            $linkedCaId = data_get($validated, 'data.linked_ca_id');
            if (!blank($linkedDvId) && !$this->recordExistsForWorkflow('dv', $linkedDvId, ['source_document_type' => 'ca'])) {
                throw ValidationException::withMessages([
                    'data.linked_dv_id' => 'The selected DV must come from a cash advance.',
                ]);
            }

            if (!blank($linkedCaId) && !blank($linkedDvId)) {
                $linkedDv = FinanceRecord::query()->find($linkedDvId);
                $linkedCaFromDv = data_get($linkedDv?->data, 'source_document_id');

                if ((string) $linkedCaFromDv !== (string) $linkedCaId) {
                    throw ValidationException::withMessages([
                        'data.linked_ca_id' => 'The selected CA must match the CA used by the linked DV.',
                    ]);
                }
            }
        }

        if ($moduleKey === 'crf') {
            $linkedLrId = data_get($validated, 'data.linked_lr_id');

            if (!$this->recordExistsForWorkflow('lr', $linkedLrId, ['variance_indicator' => 'Overage'])) {
                throw ValidationException::withMessages([
                    'data.linked_lr_id' => 'The selected LR must be approved and marked as overage.',
                ]);
            }
        }

        if ($moduleKey === 'arf') {
            $linkedPoId = data_get($validated, 'data.linked_po_id');
            $linkedDvId = data_get($validated, 'data.linked_dv_id');

            if (blank($linkedPoId) && blank($linkedDvId)) {
                throw ValidationException::withMessages([
                    'data.linked_po_id' => 'ARF must be linked to either a PO or a DV.',
                    'data.linked_dv_id' => 'ARF must be linked to either a PO or a DV.',
                ]);
            }
        }

        if ($moduleKey === 'ibtf' && data_get($validated, 'data.source_bank_account_id') === data_get($validated, 'data.destination_bank_account_id')) {
            throw ValidationException::withMessages([
                'data.destination_bank_account_id' => 'Source and destination bank accounts must be different.',
            ]);
        }
    }

    private function normalizeModuleData(string $moduleKey, array $data): array
    {
        if ($moduleKey === 'supplier' && blank(data_get($data, 'completion_mode'))) {
            data_set($data, 'completion_mode', 'complete_internally');
        }

        return $data;
    }

    private function recordExistsForWorkflow(string $moduleKey, mixed $recordId, array $dataConstraints = []): bool
    {
        return $this->acceptedRecordQuery($moduleKey, $dataConstraints)
            ->where('id', $recordId)
            ->exists();
    }

    public function index(Request $request)
    {
        $moduleKey = $request->get('module', 'supplier');
        $workflowFilter = $request->get('workflow_status', 'all');

        if (!array_key_exists($moduleKey, self::MODULES)) {
            $moduleKey = 'supplier';
        }

        if (!in_array($workflowFilter, array_merge(['all'], self::WORKFLOW_STATUSES), true)) {
            $workflowFilter = 'all';
        }

        $query = FinanceRecord::query();

        if (!$this->canApproveFinance()) {
            $query->where('submitted_by', Auth::id());
        }

        $records = $query->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => $this->transformRecord($record))
            ->values();

        $sourceRecords = FinanceRecord::query()
            ->where(function ($statusQuery) {
                $statusQuery->where('workflow_status', 'Accepted')
                    ->orWhere('approval_status', 'Approved');
            })
            ->orderByDesc('record_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (FinanceRecord $record) => $this->transformRecord($record))
            ->values();

        return view('finance.index', [
            'records' => $records,
            'sourceRecords' => $sourceRecords,
            'moduleLabels' => self::MODULES,
            'lookupOptions' => $this->resolveLookupOptions(),
            'currentModule' => $moduleKey,
            'currentWorkflowFilter' => $workflowFilter,
            'canApproveFinance' => $this->canApproveFinance(),
            'currentUserName' => Auth::user()->name ?? 'Unknown User',
            'currentUserEmail' => Auth::user()->email ?? '',
        ]);
    }

    public function show(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance() && (int) $financeRecord->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($this->transformRecord($financeRecord));
    }

    public function previewHtml(FinanceRecord $financeRecord)
    {
        return view('finance.preview-html', $this->financePdfContext($financeRecord->fresh()));
    }

    public function previewPdf(FinanceRecord $financeRecord)
    {
        $context = $this->financePdfContext($financeRecord->fresh(), false);
        $pdf = Pdf::loadView('finance.pdf', $context)->setPaper('letter', 'portrait');

        $fileName = $this->normalizeFinanceRecordNumber($financeRecord->module_key, $financeRecord->record_number) ?: 'finance-record';

        return $pdf->stream($fileName . '.pdf');
    }

    public function store(Request $request)
    {
        $this->validateModulePayload($request);

        $isApprover = $this->canApproveFinance();
        $attachments = $this->persistAttachments($request);
        $data = $this->normalizeModuleData($request->module_key, $request->input('data', []));
        $supplierSendMode = $request->module_key === 'supplier' && data_get($data, 'completion_mode') === 'send_to_supplier';
        $recordNumber = trim((string) $request->input('record_number', ''));
        $recordTitle = trim((string) $request->input('record_title', ''));
        $recordDate = $request->input('record_date');

        if ($supplierSendMode) {
            $recordNumber = $recordNumber ?: $this->normalizeFinanceRecordNumber('supplier', '');
            $recordTitle = $recordTitle ?: 'Supplier Completion';
            $recordDate = $recordDate ?: now()->toDateString();
        } elseif ($recordTitle === '') {
            $recordTitle = self::MODULES[$request->module_key] ?? Str::headline($request->module_key);
        }

        $recordNumber = $this->normalizeFinanceRecordNumber($request->module_key, $recordNumber);

        $record = FinanceRecord::create([
            'module_key' => $request->module_key,
            'record_number' => $recordNumber,
            'record_title' => $recordTitle,
            'record_date' => $recordDate,
            'amount' => $request->amount,
            'status' => $request->status,
            'workflow_status' => $isApprover ? 'Accepted' : 'Uploaded',
            'approval_status' => $isApprover ? 'Approved' : 'Pending',
            'submitted_by' => Auth::id(),
            'submitted_at' => $isApprover ? now() : null,
            'approved_by' => $isApprover ? Auth::id() : null,
            'approved_at' => $isApprover ? now() : null,
            'review_note' => null,
            'data' => $data,
            'attachments' => $attachments,
            'share_token' => null,
            'shared_at' => null,
            'user' => Auth::user()->name ?? 'Unknown User',
        ]);

        if ($request->module_key === 'supplier' && data_get($data, 'completion_mode') === 'send_to_supplier') {
            $this->sendSupplierCompletionEmail($record);
            $record = $record->fresh();
        }

        return response()->json([
            'message' => $record->workflow_status === 'Shared'
                ? 'Finance record created and emailed to the supplier.'
                : ($isApprover ? 'Finance record saved and accepted.' : 'Finance record saved successfully.'),
            'data' => $this->transformRecord($record),
        ], 201);
    }

    public function update(Request $request, FinanceRecord $financeRecord)
    {
        if (!$this->canEditRecord($financeRecord)) {
            abort(403, 'This record can no longer be edited.');
        }

        $this->validateModulePayload($request, $financeRecord);

        $existingAttachments = json_decode((string) $request->input('existing_attachments_json', '[]'), true);
        $existingAttachments = is_array($existingAttachments) ? $existingAttachments : [];
        $attachments = $this->persistAttachments($request, $existingAttachments);
        $data = $this->normalizeModuleData($request->module_key, $request->input('data', []));
        $supplierSendMode = $request->module_key === 'supplier' && data_get($data, 'completion_mode') === 'send_to_supplier';
        $recordNumber = trim((string) $request->input('record_number', ''));
        $recordTitle = trim((string) $request->input('record_title', ''));
        $recordDate = $request->input('record_date');

        if ($supplierSendMode) {
            $recordNumber = $recordNumber ?: ($financeRecord->record_number ?: $this->normalizeFinanceRecordNumber('supplier', ''));
            $recordTitle = $recordTitle ?: ($financeRecord->record_title ?: 'Supplier Completion');
            $recordDate = $recordDate ?: optional($financeRecord->record_date)->format('Y-m-d') ?: now()->toDateString();
        } elseif ($recordTitle === '') {
            $recordTitle = $financeRecord->record_title ?: (self::MODULES[$request->module_key] ?? Str::headline($request->module_key));
        }

        $recordNumber = $this->normalizeFinanceRecordNumber($request->module_key, $recordNumber);

        $payload = [
            'module_key' => $request->module_key,
            'record_number' => $recordNumber,
            'record_title' => $recordTitle,
            'record_date' => $recordDate,
            'amount' => $request->amount,
            'status' => $request->status,
            'data' => $data,
            'attachments' => $attachments,
        ];

        if (($financeRecord->workflow_status ?? 'Uploaded') === 'Reverted') {
            $payload['approval_status'] = 'Pending';
            $payload['review_note'] = null;
        }

        $financeRecord->update($payload);

        if ($request->module_key === 'supplier' && data_get($data, 'completion_mode') === 'send_to_supplier' && blank($financeRecord->share_token)) {
            $this->sendSupplierCompletionEmail($financeRecord);
            $financeRecord = $financeRecord->fresh();
        }

        return response()->json([
            'message' => $financeRecord->workflow_status === 'Shared'
                ? 'Finance record updated and emailed to the supplier.'
                : 'Finance record updated successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function submit(FinanceRecord $financeRecord)
    {
        if ((int) $financeRecord->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($financeRecord->workflow_status ?? 'Uploaded', ['Uploaded', 'Reverted'], true)) {
            return response()->json([
                'message' => 'Only uploaded or reverted records can be submitted.'
            ], 422);
        }

        $financeRecord->update([
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'submitted_at' => now(),
            'review_note' => null,
        ]);

        return response()->json([
            'message' => 'Finance record submitted for review successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function approve(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $financeRecord->update([
            'workflow_status' => 'Accepted',
            'approval_status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record approved successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function revert(Request $request, FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review_note' => 'required|string|max:1000',
        ]);

        $financeRecord->update([
            'workflow_status' => 'Reverted',
            'approval_status' => 'Needs Revision',
            'review_note' => $request->review_note,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record reverted for revision.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function archive(FinanceRecord $financeRecord)
    {
        if (!$this->canApproveFinance()) {
            abort(403, 'Unauthorized');
        }

        $financeRecord->update([
            'workflow_status' => 'Archived',
            'approval_status' => 'Archived',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Finance record archived successfully.',
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function shareSupplierLink(FinanceRecord $financeRecord)
    {
        if (!$this->canManageSupplierCompletion($financeRecord)) {
            abort(403, 'Unauthorized');
        }

        $link = $this->sendSupplierCompletionEmail($financeRecord->fresh());
        $financeRecord = $financeRecord->fresh();

        return response()->json([
            'message' => 'Supplier completion link has been emailed.',
            'link' => $link,
            'data' => $this->transformRecord($financeRecord->fresh()),
        ]);
    }

    public function updateSupplierEmailAndResend(Request $request, FinanceRecord $financeRecord)
    {
        if (!$this->canManageSupplierCompletion($financeRecord)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'email_address' => 'required|email|max:255',
        ]);

        $data = $financeRecord->data ?? [];
        $data['email_address'] = $request->email_address;

        $financeRecord->update([
            'data' => $data,
        ]);

        $link = $this->sendSupplierCompletionEmail($financeRecord->fresh());
        $financeRecord = $financeRecord->fresh();

        return response()->json([
            'message' => 'Supplier email updated and completion form resent.',
            'link' => $link,
            'data' => $this->transformRecord($financeRecord),
        ]);
    }

    public function supplierCompletionForm(string $token)
    {
        $record = FinanceRecord::query()
            ->where('module_key', 'supplier')
            ->where('share_token', $token)
            ->firstOrFail();

        return view('finance.supplier-completion', [
            'record' => $this->transformRecord($record),
        ]);
    }

    public function submitSupplierCompletion(Request $request, string $token)
    {
        $record = FinanceRecord::query()
            ->where('module_key', 'supplier')
            ->where('share_token', $token)
            ->firstOrFail();

        $request->validate([
            'record_title' => 'required|string|max:255',
            'record_number' => 'required|string|max:255',
            'record_date' => 'required|date',
            'data.representative_full_name' => 'required|string|max:255',
            'data.email_address' => 'required|email|max:255',
            'data.phone_number' => 'required|string|max:255',
            'data.business_address' => 'nullable|string|max:1000',
            'data.billing_address' => 'nullable|string|max:1000',
        ]);

        $data = array_merge($record->data ?? [], $request->input('data', []));
        $data['business_name'] = $request->record_title;

        $record->update([
            'record_number' => $request->record_number,
            'record_title' => $request->record_title,
            'record_date' => $request->record_date,
            'data' => $data,
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'supplier_completed_at' => now(),
        ]);

        return redirect()
            ->route('finance.supplier.completion', $token)
            ->with('success', 'Supplier information submitted successfully. It is now ready for internal review.');
    }
}
