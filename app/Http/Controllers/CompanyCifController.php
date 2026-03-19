<?php

namespace App\Http\Controllers;

use App\Models\CompanyCif;
use App\Support\CompanyHistoryLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompanyCifController extends Controller
{
    private const DEMO_AUTO_APPROVE_ON_SUBMIT = true;

    private const CLIENT_TYPES = [
        'new_client' => 'New Client',
        'existing_client' => 'Existing Client',
        'change_information' => 'Change Information',
    ];

    private const CITIZENSHIP_TYPES = [
        'filipino' => 'Filipino',
        'foreigner' => 'Foreigner',
        'dual_citizen' => 'Dual Citizen',
    ];

    private const GENDERS = [
        'male' => 'Male',
        'female' => 'Female',
    ];

    private const CIVIL_STATUSES = [
        'single' => 'Single',
        'married' => 'Married',
        'separated' => 'Separated',
        'widowed' => 'Widowed',
    ];

    private const SOURCE_OF_FUNDS = [
        'salary' => 'Salary',
        'remittance' => 'Remittance',
        'business' => 'Business',
        'others' => 'Others',
        'commission_fees' => 'Commission / Fees',
        'retirement_pension' => 'Retirement / Pension',
    ];

    private const STATUSES = [
        'draft' => 'Draft',
        'pending_approval' => 'Waiting for Approval',
        'approved' => 'Approved / Completed',
        'rejected' => 'Rejected',
    ];

    public function create(Request $request, int $company): View
    {
        return view('company.cif.create', $this->buildViewData(
            $request,
            $company,
            null
        ));
    }

    public function store(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';
        $status = $this->resolveSubmittedStatus($isSubmit);

        $cif = CompanyCif::create([
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

        if (blank($cif->cif_no)) {
            $cif->updateQuietly([
                'cif_no' => $this->generateCifNumber($cif),
            ]);
        }

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $isSubmit ? 'Client Information Form submitted' : 'Client Information Form draft saved',
            'description' => $cif->title,
            'extra_label' => 'Status',
            'extra_value' => self::STATUSES[$status] ?? ucfirst($status),
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        $message = $isSubmit
            ? (self::DEMO_AUTO_APPROVE_ON_SUBMIT
                ? "Client Information Form saved for {$companyData['company_name']} and marked as approved for demo."
                : "Client Information Form submitted for {$companyData['company_name']} and is now waiting for approval.")
            : "Client Information Form draft saved for {$companyData['company_name']}.";

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'client-intake'])
            ->with('cif_success', $message);
    }

    public function show(Request $request, int $company, int $cif): View
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        return view('company.cif.show', $this->buildViewData(
            $request,
            $company,
            $cifRecord,
            ['company' => (object) $companyData]
        ));
    }

    public function edit(Request $request, int $company, int $cif): View
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        return view('company.cif.edit', $this->buildViewData(
            $request,
            $company,
            $cifRecord,
            ['company' => (object) $companyData]
        ));
    }

    public function update(Request $request, int $company, int $cif): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';
        $status = $this->resolveSubmittedStatus($isSubmit);

        $cifRecord->update([
            ...$payload,
            'title' => $this->resolveTitle($payload),
            'status' => $status,
            'submitted_at' => $isSubmit ? ($cifRecord->submitted_at ?? now()) : null,
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
            'title' => $isSubmit ? 'Client Information Form updated' : 'Client Information Form draft updated',
            'description' => $cifRecord->title,
            'extra_label' => 'Status',
            'extra_value' => self::STATUSES[$status] ?? ucfirst($status),
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        $message = $isSubmit
            ? (self::DEMO_AUTO_APPROVE_ON_SUBMIT
                ? "Client Information Form updated for {$companyData['company_name']} and kept as an approved demo record."
                : "Client Information Form updated for {$companyData['company_name']} and returned to the approval queue.")
            : "Client Information Form draft updated for {$companyData['company_name']}.";

        return redirect()
            ->route('company.cif.show', ['company' => $company, 'cif' => $cifRecord->id])
            ->with('cif_success', $message);
    }

    public function approve(Request $request, int $company, int $cif): RedirectResponse
    {
        $cifRecord = $this->findCif($company, $cif);

        $cifRecord->update([
            'status' => 'approved',
            'submitted_at' => $cifRecord->submitted_at ?? now(),
            'approved_at' => now(),
            'approved_by_name' => $request->user()?->name ?? 'System User',
            'rejected_at' => null,
            'rejected_by_name' => null,
            'rejection_reason' => null,
            'updated_by' => $request->user()?->id,
        ]);

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => 'Client Information Form approved',
            'description' => $cifRecord->title,
            'extra_label' => 'Status',
            'extra_value' => self::STATUSES['approved'],
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        return redirect()
            ->route('company.cif.show', ['company' => $company, 'cif' => $cifRecord->id])
            ->with('cif_success', 'Client Information Form approved successfully.');
    }

    public function reject(Request $request, int $company, int $cif): RedirectResponse
    {
        $cifRecord = $this->findCif($company, $cif);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $cifRecord->update([
            'status' => 'rejected',
            'submitted_at' => $cifRecord->submitted_at ?? now(),
            'approved_at' => null,
            'approved_by_name' => null,
            'rejected_at' => now(),
            'rejected_by_name' => $request->user()?->name ?? 'System User',
            'rejection_reason' => $validated['rejection_reason'],
            'updated_by' => $request->user()?->id,
        ]);

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => 'Client Information Form rejected',
            'description' => $cifRecord->title,
            'extra_label' => 'Reason',
            'extra_value' => $validated['rejection_reason'],
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        return redirect()
            ->route('company.cif.show', ['company' => $company, 'cif' => $cifRecord->id])
            ->with('cif_success', 'Client Information Form rejected and returned for revision.');
    }

    public function print(Request $request, int $company, int $cif): View
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        return view('company.cif.print', $this->buildViewData(
            $request,
            $company,
            $cifRecord,
            [
                'company' => (object) $companyData,
                'autoPrint' => $request->boolean('autoprint'),
            ]
        ));
    }

    private function buildViewData(Request $request, int $company, ?CompanyCif $cif, array $overrides = []): array
    {
        $companyData = $this->findCompany($request, $company);

        return [
            'company' => (object) $companyData,
            'cif' => $cif,
            'clientTypeOptions' => self::CLIENT_TYPES,
            'citizenshipOptions' => self::CITIZENSHIP_TYPES,
            'genderOptions' => self::GENDERS,
            'civilStatusOptions' => self::CIVIL_STATUSES,
            'sourceOfFundsOptions' => self::SOURCE_OF_FUNDS,
            'statusLabels' => self::STATUSES,
            ...$overrides,
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

    private function findCif(int $company, int $cif): CompanyCif
    {
        abort_unless(Schema::hasTable('company_cifs'), 404);

        return CompanyCif::query()
            ->where('company_id', $company)
            ->findOrFail($cif);
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'cif_no' => ['nullable', 'string', 'max:255'],
            'cif_date' => ['required', 'date'],
            'client_type' => ['required', Rule::in(array_keys(self::CLIENT_TYPES))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'name_extension' => ['nullable', 'string', 'max:50'],
            'no_middle_name' => ['nullable', 'boolean'],
            'first_name_only' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_no' => ['nullable', 'string', 'max:100'],
            'mobile_no' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'citizenship_status' => ['nullable', Rule::in(array_keys(self::CITIZENSHIP_TYPES))],
            'nationality' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(array_keys(self::GENDERS))],
            'marital_status' => ['nullable', Rule::in(array_keys(self::CIVIL_STATUSES))],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'nature_of_work_business' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'other_government_id' => ['nullable', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:255'],
            'mothers_maiden_name' => ['nullable', 'string', 'max:255'],
            'source_of_funds_other_text' => ['nullable', 'string', 'max:255'],
            'passport_no' => ['nullable', 'string', 'max:255'],
            'passport_expiry_date' => ['nullable', 'date'],
            'passport_place_of_issue' => ['nullable', 'string', 'max:255'],
            'acr_id_no' => ['nullable', 'string', 'max:255'],
            'acr_expiry_date' => ['nullable', 'date'],
            'acr_place_of_issue' => ['nullable', 'string', 'max:255'],
            'visa_status' => ['nullable', 'string', 'max:255'],
            'signature_printed_name' => ['nullable', 'string', 'max:255'],
            'signature_position' => ['nullable', 'string', 'max:255'],
            'review_signature_printed_name' => ['nullable', 'string', 'max:255'],
            'review_signature_position' => ['nullable', 'string', 'max:255'],
            'referred_by' => ['nullable', 'string', 'max:255'],
            'referred_by_date' => ['nullable', 'date'],
            'sales_marketing_name' => ['nullable', 'string', 'max:255'],
            'finance_name' => ['nullable', 'string', 'max:255'],
            'president_name' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $request->boolean('first_name_only') && blank($validated['last_name'] ?? null)) {
            throw ValidationException::withMessages([
                'last_name' => 'Last name is required unless the client only has a first name.',
            ]);
        }

        $booleanFields = [
            'no_middle_name',
            'first_name_only',
            'source_of_funds_salary',
            'source_of_funds_remittance',
            'source_of_funds_business',
            'source_of_funds_others',
            'source_of_funds_commission_fees',
            'source_of_funds_retirement_pension',
            'onboarding_two_valid_government_ids',
            'onboarding_tin_id',
            'onboarding_specimen_signature',
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $citizenshipStatus = $validated['citizenship_status'] ?? null;

        if ($citizenshipStatus === 'filipino') {
            $validated['nationality'] = 'Filipino';
            $validated['passport_no'] = null;
            $validated['passport_expiry_date'] = null;
            $validated['passport_place_of_issue'] = null;
            $validated['acr_id_no'] = null;
            $validated['acr_expiry_date'] = null;
            $validated['acr_place_of_issue'] = null;
            $validated['visa_status'] = null;
        }

        if (($validated['marital_status'] ?? null) !== 'married') {
            $validated['spouse_name'] = null;
        }

        if (! $validated['source_of_funds_others']) {
            $validated['source_of_funds_other_text'] = null;
        }

        $validated['onboarding_two_valid_government_ids'] = true;
        $validated['onboarding_tin_id'] = true;
        $validated['onboarding_specimen_signature'] = true;

        return $validated;
    }

    private function resolveTitle(array $payload): string
    {
        $name = trim(implode(' ', array_filter([
            $payload['first_name'] ?? null,
            $payload['middle_name'] ?? null,
            $payload['last_name'] ?? null,
            $payload['name_extension'] ?? null,
        ])));

        return $name !== '' ? "Client Information Form - {$name}" : 'Client Information Form';
    }

    private function generateCifNumber(CompanyCif $cif): string
    {
        return 'CIF-' . now()->format('Ymd') . '-' . str_pad((string) $cif->id, 4, '0', STR_PAD_LEFT);
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
