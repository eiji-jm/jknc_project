<?php

namespace App\Http\Controllers;

use App\Models\CompanyBif;
use App\Support\CompanyHistoryLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyBifController extends Controller
{
    private const DEMO_AUTO_APPROVE_ON_SUBMIT = true;

    private const CLIENT_TYPES = [
        'new_client' => 'New Client',
        'existing_client' => 'Existing Client',
        'change_information' => 'Change Information',
    ];

    private const ORGANIZATION_TYPES = [
        'sole_proprietorship' => 'Sole Proprietorship',
        'corporation' => 'Corporation',
        'ngo' => 'NGO',
        'partnership' => 'Partnership',
        'cooperative' => 'Cooperative',
        'other' => 'Other (Please Specify)',
    ];

    private const NATIONALITY_TYPES = [
        'filipino' => 'Filipino',
        'foreign' => 'Foreign',
    ];

    private const OFFICE_TYPES = [
        'head_office' => 'Head Office',
        'regional_headquarter' => 'Regional Headquarter',
        'branch' => 'Branch',
        'other' => 'Other (Please Specify)',
    ];

    private const STATUSES = [
        'draft' => 'Draft',
        'pending_approval' => 'Waiting for Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public function create(Request $request, int $company): View
    {
        return view('company.bif.create', $this->buildViewData($request, $company, null));
    }

    public function store(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';
        $status = $this->resolveSubmittedStatus($isSubmit);

        $bif = CompanyBif::create([
            ...$payload,
            'company_id' => $company,
            'title' => $this->resolveTitle($payload),
            'status' => $status,
            'submitted_at' => $isSubmit ? now() : null,
            'approved_at' => $this->resolveApprovedAt($isSubmit),
            'approved_by_name' => $this->resolveApprovedByName($request, $isSubmit),
            'rejected_at' => null,
            'rejected_by_name' => null,
            'rejection_reason' => null,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        if (blank($bif->bif_no)) {
            $bif->updateQuietly([
                'bif_no' => $this->generateBifNumber($bif),
            ]);
        }

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $isSubmit ? 'Business Client Information Form submitted' : 'Business Client Information Form draft saved',
            'description' => $bif->title,
            'extra_label' => 'Status',
            'extra_value' => self::STATUSES[$status] ?? ucfirst($status),
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        $message = $isSubmit
            ? "Business Client Information Form saved for {$companyData['company_name']} and marked as approved for demo."
            : "Business Client Information Form draft saved for {$companyData['company_name']}.";

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', $message);
    }

    public function show(Request $request, int $company, int $bif): View
    {
        return view('company.bif.show', $this->buildViewData(
            $request,
            $company,
            $this->findBif($company, $bif)
        ));
    }

    public function edit(Request $request, int $company, int $bif): View
    {
        return view('company.bif.edit', $this->buildViewData(
            $request,
            $company,
            $this->findBif($company, $bif)
        ));
    }

    public function update(Request $request, int $company, int $bif): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $bifRecord = $this->findBif($company, $bif);
        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';
        $status = $this->resolveSubmittedStatus($isSubmit);

        $bifRecord->update([
            ...$payload,
            'title' => $this->resolveTitle($payload),
            'status' => $status,
            'submitted_at' => $isSubmit ? ($bifRecord->submitted_at ?? now()) : null,
            'approved_at' => $this->resolveApprovedAt($isSubmit),
            'approved_by_name' => $this->resolveApprovedByName($request, $isSubmit),
            'rejected_at' => null,
            'rejected_by_name' => null,
            'rejection_reason' => null,
            'updated_by' => $request->user()?->id,
        ]);

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $isSubmit ? 'Business Client Information Form updated' : 'Business Client Information Form draft updated',
            'description' => $bifRecord->title,
            'extra_label' => 'Status',
            'extra_value' => self::STATUSES[$status] ?? ucfirst($status),
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        $message = $isSubmit
            ? "Business Client Information Form updated for {$companyData['company_name']} and kept as an approved demo record."
            : "Business Client Information Form draft updated for {$companyData['company_name']}.";

        return redirect()
            ->route('company.bif.show', ['company' => $company, 'bif' => $bifRecord->id])
            ->with('bif_success', $message);
    }

    public function print(Request $request, int $company, int $bif): View
    {
        return view('company.bif.print', [
            ...$this->buildViewData($request, $company, $this->findBif($company, $bif)),
            'autoPrint' => $request->boolean('autoprint'),
        ]);
    }

    private function buildViewData(Request $request, int $company, ?CompanyBif $bif): array
    {
        return [
            'company' => (object) $this->findCompany($request, $company),
            'bif' => $bif,
            'clientTypeOptions' => self::CLIENT_TYPES,
            'organizationOptions' => self::ORGANIZATION_TYPES,
            'nationalityOptions' => self::NATIONALITY_TYPES,
            'officeTypeOptions' => self::OFFICE_TYPES,
            'statusLabels' => self::STATUSES,
        ];
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return [
            'company_type' => 'Corporation',
            'email' => null,
            'phone' => null,
            'website' => null,
            'description' => null,
            'address' => null,
            'owner_name' => null,
            ...$companyData,
        ];
    }

    private function findBif(int $company, int $bif): CompanyBif
    {
        abort_unless(Schema::hasTable('company_bifs'), 404);

        return CompanyBif::query()
            ->where('company_id', $company)
            ->findOrFail($bif);
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'bif_no' => ['nullable', 'string', 'max:255'],
            'bif_date' => ['required', 'date'],
            'client_type' => ['required', Rule::in(array_keys(self::CLIENT_TYPES))],
            'business_organization' => ['nullable', Rule::in(array_keys(self::ORGANIZATION_TYPES))],
            'business_organization_other' => ['nullable', 'string', 'max:255'],
            'nationality_status' => ['nullable', Rule::in(array_keys(self::NATIONALITY_TYPES))],
            'office_type' => ['nullable', Rule::in(array_keys(self::OFFICE_TYPES))],
            'office_type_other' => ['nullable', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'alternative_business_name' => ['nullable', 'string', 'max:255'],
            'business_address' => ['nullable', 'string'],
            'zip_code' => ['nullable', 'string', 'max:50'],
            'business_phone' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['nullable', 'string', 'max:255'],
            'tin_no' => ['nullable', 'string', 'max:255'],
            'place_of_incorporation' => ['nullable', 'string', 'max:255'],
            'date_of_incorporation' => ['nullable', 'date'],
            'industry_other_text' => ['nullable', 'string', 'max:255'],
            'employee_male' => ['nullable', 'integer', 'min:0'],
            'employee_female' => ['nullable', 'integer', 'min:0'],
            'employee_pwd' => ['nullable', 'integer', 'min:0'],
            'employee_total' => ['nullable', 'integer', 'min:0'],
            'source_other_text' => ['nullable', 'string', 'max:255'],
            'president_name' => ['nullable', 'string', 'max:255'],
            'treasurer_name' => ['nullable', 'string', 'max:255'],
            'authorized_signatory_name' => ['nullable', 'string', 'max:255'],
            'authorized_signatory_address' => ['nullable', 'string'],
            'authorized_signatory_nationality' => ['nullable', 'string', 'max:255'],
            'authorized_signatory_date_of_birth' => ['nullable', 'date'],
            'authorized_signatory_tin' => ['nullable', 'string', 'max:255'],
            'authorized_signatory_position' => ['nullable', 'string', 'max:255'],
            'ubo_name' => ['nullable', 'string', 'max:255'],
            'ubo_address' => ['nullable', 'string'],
            'ubo_nationality' => ['nullable', 'string', 'max:255'],
            'ubo_date_of_birth' => ['nullable', 'date'],
            'ubo_tin' => ['nullable', 'string', 'max:255'],
            'ubo_position' => ['nullable', 'string', 'max:255'],
            'authorized_contact_person_name' => ['nullable', 'string', 'max:255'],
            'authorized_contact_person_position' => ['nullable', 'string', 'max:255'],
            'authorized_contact_person_email' => ['nullable', 'email', 'max:255'],
            'authorized_contact_person_phone' => ['nullable', 'string', 'max:255'],
            'signature_printed_name' => ['nullable', 'string', 'max:255'],
            'signature_position' => ['nullable', 'string', 'max:255'],
            'review_signature_printed_name' => ['nullable', 'string', 'max:255'],
            'review_signature_position' => ['nullable', 'string', 'max:255'],
            'sales_marketing_name' => ['nullable', 'string', 'max:255'],
            'sales_marketing_date_signature' => ['nullable', 'string', 'max:255'],
            'finance_name' => ['nullable', 'string', 'max:255'],
            'finance_date_signature' => ['nullable', 'string', 'max:255'],
            'referred_by' => ['nullable', 'string', 'max:255'],
            'consultant_lead' => ['nullable', 'string', 'max:255'],
            'lead_associate' => ['nullable', 'string', 'max:255'],
            'president_use_only_name' => ['nullable', 'string', 'max:255'],
        ]);

        $booleanFields = [
            'industry_services',
            'industry_export_import',
            'industry_education',
            'industry_financial_services',
            'industry_transportation',
            'industry_distribution',
            'industry_manufacturing',
            'industry_government',
            'industry_wholesale_retail_trade',
            'industry_other',
            'capital_micro',
            'capital_small',
            'capital_medium',
            'capital_large',
            'source_revenue_income',
            'source_investments',
            'source_remittance',
            'source_other',
            'source_fees',
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        if (($validated['business_organization'] ?? null) !== 'other') {
            $validated['business_organization_other'] = null;
        }

        if (($validated['office_type'] ?? null) !== 'other') {
            $validated['office_type_other'] = null;
        }

        if (!($validated['industry_other'] ?? false)) {
            $validated['industry_other_text'] = null;
        }

        if (!($validated['source_other'] ?? false)) {
            $validated['source_other_text'] = null;
        }

        return $validated;
    }

    private function resolveTitle(array $payload): string
    {
        $name = trim((string) ($payload['business_name'] ?? ''));

        return $name !== '' ? "Business Client Information Form - {$name}" : 'Business Client Information Form';
    }

    private function generateBifNumber(CompanyBif $bif): string
    {
        return 'BIF-' . now()->format('Ymd') . '-' . str_pad((string) $bif->id, 4, '0', STR_PAD_LEFT);
    }

    private function resolveSubmittedStatus(bool $isSubmit): string
    {
        if (! $isSubmit) {
            return 'draft';
        }

        return self::DEMO_AUTO_APPROVE_ON_SUBMIT ? 'approved' : 'pending_approval';
    }

    private function resolveApprovedAt(bool $isSubmit)
    {
        if (! $isSubmit || ! self::DEMO_AUTO_APPROVE_ON_SUBMIT) {
            return null;
        }

        return now();
    }

    private function resolveApprovedByName(Request $request, bool $isSubmit): ?string
    {
        if (! $isSubmit || ! self::DEMO_AUTO_APPROVE_ON_SUBMIT) {
            return null;
        }

        return $request->user()?->name ?? 'Demo Approval';
    }

    private function defaultCompanies(): array
    {
        return [
            [
                'id' => 1,
                'company_name' => 'Company 1',
                'company_type' => 'Corporation',
                'email' => 'company1@example.com',
                'phone' => '09012345678',
                'website' => 'https://bigin.example',
                'description' => 'Sample company record',
                'address' => 'Makati City',
                'owner_name' => 'Owner 1',
                'created_at' => '2026-03-01 10:00:00',
            ],
            [
                'id' => 2,
                'company_name' => 'Company 2',
                'company_type' => 'Corporation',
                'email' => 'company2@example.com',
                'phone' => '09000345678',
                'website' => 'https://bigin.example',
                'description' => 'Sample company record',
                'address' => 'Taguig City',
                'owner_name' => 'Owner 2',
                'created_at' => '2026-03-02 10:00:00',
            ],
            [
                'id' => 3,
                'company_name' => 'Company 3',
                'company_type' => 'Corporation',
                'email' => 'company3@example.com',
                'phone' => '09777345678',
                'website' => 'https://bigin.example',
                'description' => 'Sample company record',
                'address' => 'Pasig City',
                'owner_name' => 'Owner 3',
                'created_at' => '2026-03-03 10:00:00',
            ],
        ];
    }

    private function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }
}
