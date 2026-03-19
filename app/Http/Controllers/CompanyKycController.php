<?php

namespace App\Http\Controllers;

use App\Models\CompanyCif;
use App\Models\CompanyBif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CompanyKycController extends Controller
{
    private const GOVERNMENT_TYPES = [
        'NatGov',
        'Local',
    ];

    private const GOVERNMENT_BODIES = [
        'NatGov' => [
            'SEC / CDA Certificate of Registration',
            'BIR Certificate of Registration (COR)',
            'Secretary Certificate OR Board Resolution',
            'Articles of Incorporation / Partnership',
            'By-Laws',
            'Latest General Information Sheet (GIS)',
            'Ultimate Beneficial Owner (UBO) Declaration',
            'Appointment of Officers / Directors / Trustees',
            'Other national government requirement',
        ],
        'Local' => [
            'Business Permit / Mayor’s Permit',
            'Proof of Billing (Company Address)',
            'Barangay Clearance',
            'Occupancy Permit',
            'Sanitary Permit',
            'Fire Safety Inspection Certificate',
            'Other local government requirement',
        ],
    ];

    private const REGISTRATION_STATUSES = [
        'Pending',
        'In Review',
        'Registered',
        'Expired',
    ];

    public function index(Request $request, int $company): View
    {
        $companyData = $this->findCompany($request, $company);
        $activeTab = (string) $request->query('tab', 'client-intake');
        $search = trim((string) $request->query('search', ''));
        $governmentType = trim((string) $request->query('government_type', ''));
        $registrationStatus = trim((string) $request->query('registration_status', ''));

        $records = collect($request->session()->get($this->sessionKey(), $this->defaultRecords()))
            ->where('company_id', $company)
            ->when($search !== '', function (Collection $items) use ($search) {
                $term = strtolower($search);

                return $items->filter(function (array $item) use ($term) {
                    return collect([
                        $item['tin'] ?? '',
                        $item['uploaded_by'] ?? '',
                        $item['government_type'] ?? '',
                        $item['government_body'] ?? '',
                        $item['registration_no'] ?? '',
                    ])->contains(fn (?string $value) => str_contains(strtolower((string) $value), $term));
                });
            })
            ->when($governmentType !== '', fn (Collection $items) => $items->where('government_type', $governmentType))
            ->when($registrationStatus !== '', fn (Collection $items) => $items->where('registration_status', $registrationStatus))
            ->values();

        $cifDocuments = collect();
        $bifDocuments = collect();

        if (Schema::hasTable('company_cifs')) {
            $cifDocuments = CompanyCif::query()
                ->where('company_id', $company)
                ->latest('updated_at')
                ->get();
        }

        if (Schema::hasTable('company_bifs')) {
            $bifDocuments = CompanyBif::query()
                ->where('company_id', $company)
                ->latest('updated_at')
                ->get();
        }

        return view('company.kyc', [
            'company' => (object) $companyData,
            'activeTab' => $activeTab,
            'records' => $records,
            'search' => $search,
            'governmentType' => $governmentType,
            'registrationStatus' => $registrationStatus,
            'governmentTypes' => self::GOVERNMENT_TYPES,
            'governmentBodies' => self::GOVERNMENT_BODIES,
            'registrationStatuses' => self::REGISTRATION_STATUSES,
            'cifDocuments' => $cifDocuments,
            'bifDocuments' => $bifDocuments,
            'businessClientInformation' => $this->businessClientInformation($companyData),
        ]);
    }

    public function store(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $validated = $this->validateRecord($request);
        $records = collect($request->session()->get($this->sessionKey(), $this->defaultRecords()));
        $nextId = (int) ($records->max('id') ?? 0) + 1;

        $records->push([
            'id' => $nextId,
            'company_id' => $company,
            'date_uploaded' => $validated['date_uploaded'],
            'uploaded_by' => $validated['uploaded_by'],
            'client' => $companyData['company_name'],
            'tin' => $validated['tin'],
            'government_type' => $validated['government_type'],
            'government_body' => $validated['government_body'],
            'registration_status' => $validated['registration_status'],
            'registration_date' => $validated['registration_date'],
            'registration_no' => $validated['registration_no'],
            'document_file' => $validated['document_file'] ?? '',
            'remarks' => $validated['remarks'] ?? '',
        ]);

        $request->session()->put($this->sessionKey(), $records->values()->all());

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'doc-requirement'])
            ->with('kyc_success', 'KYC document record added successfully.');
    }

    public function update(Request $request, int $company, int $record): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $validated = $this->validateRecord($request);
        $records = collect($request->session()->get($this->sessionKey(), $this->defaultRecords()));
        $existing = $records->firstWhere('id', $record);

        abort_unless($existing && (int) $existing['company_id'] === $company, 404);

        $updated = $records->map(function (array $item) use ($record, $company, $companyData, $validated) {
            if ((int) $item['id'] !== $record) {
                return $item;
            }

            return [
                ...$item,
                'company_id' => $company,
                'date_uploaded' => $validated['date_uploaded'],
                'uploaded_by' => $validated['uploaded_by'],
                'client' => $companyData['company_name'],
                'tin' => $validated['tin'],
                'government_type' => $validated['government_type'],
                'government_body' => $validated['government_body'],
                'registration_status' => $validated['registration_status'],
                'registration_date' => $validated['registration_date'],
                'registration_no' => $validated['registration_no'],
                'document_file' => $validated['document_file'] ?? ($item['document_file'] ?? ''),
                'remarks' => $validated['remarks'] ?? '',
            ];
        });

        $request->session()->put($this->sessionKey(), $updated->values()->all());

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'doc-requirement'])
            ->with('kyc_success', 'KYC document record updated successfully.');
    }

    public function destroy(Request $request, int $company, int $record): RedirectResponse
    {
        $this->findCompany($request, $company);
        $records = collect($request->session()->get($this->sessionKey(), $this->defaultRecords()));
        $existing = $records->firstWhere('id', $record);

        abort_unless($existing && (int) $existing['company_id'] === $company, 404);

        $request->session()->put(
            $this->sessionKey(),
            $records->reject(fn (array $item) => (int) $item['id'] === $record)->values()->all()
        );

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'doc-requirement'])
            ->with('kyc_success', 'KYC document record removed successfully.');
    }

    private function validateRecord(Request $request): array
    {
        return $request->validate([
            'date_uploaded' => ['required', 'date'],
            'uploaded_by' => ['required', 'string', 'max:255'],
            'tin' => ['required', 'string', 'max:255'],
            'government_type' => ['required', 'string', 'max:255'],
            'government_body' => ['required', 'string', 'max:255'],
            'registration_status' => ['required', 'string', 'max:255'],
            'registration_date' => ['required', 'date'],
            'registration_no' => ['required', 'string', 'max:255'],
            'document_file' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function sessionKey(): string
    {
        return 'mock_company_kyc_records';
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function businessClientInformation(array $companyData): array
    {
        return [
            ['label' => 'Company Name', 'value' => $companyData['company_name']],
            ['label' => 'Company Type', 'value' => $companyData['company_type'] ?? 'Corporation'],
            ['label' => 'Email', 'value' => $companyData['email'] ?? '-'],
            ['label' => 'Phone', 'value' => $companyData['phone'] ?? '-'],
            ['label' => 'Address', 'value' => $companyData['address'] ?? '-'],
            ['label' => 'Website', 'value' => $companyData['website'] ?? '-'],
        ];
    }

    private function defaultCompanies(): array
    {
        return [
            ['id' => 1, 'company_name' => 'Company 1', 'company_type' => 'Corporation', 'email' => 'company1@example.com', 'phone' => '09012345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Makati City', 'owner_name' => 'Owner 1', 'created_at' => '2026-03-01 10:00:00'],
            ['id' => 2, 'company_name' => 'Company 2', 'company_type' => 'Corporation', 'email' => 'company2@example.com', 'phone' => '09000345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Taguig City', 'owner_name' => 'Owner 2', 'created_at' => '2026-03-02 10:00:00'],
            ['id' => 3, 'company_name' => 'Company 3', 'company_type' => 'Corporation', 'email' => 'company3@example.com', 'phone' => '09777345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Pasig City', 'owner_name' => 'Owner 3', 'created_at' => '2026-03-03 10:00:00'],
        ];
    }

    private function defaultRecords(): array
    {
        return [
            [
                'id' => 1,
                'company_id' => 1,
                'date_uploaded' => '2026-03-02',
                'uploaded_by' => 'John Admin',
                'client' => 'Company 1',
                'tin' => '123-456-789-000',
                'government_type' => 'NatGov',
                'government_body' => 'SEC / CDA Certificate of Registration',
                'registration_status' => 'Registered',
                'registration_date' => '2026-02-28',
                'registration_no' => 'SEC-2026-001',
                'document_file' => 'sec-certificate.pdf',
                'remarks' => 'Primary registration complete.',
            ],
            [
                'id' => 2,
                'company_id' => 1,
                'date_uploaded' => '2026-03-04',
                'uploaded_by' => 'Maria Santos',
                'client' => 'Company 1',
                'tin' => '123-456-789-000',
                'government_type' => 'Local',
                'government_body' => 'Business Permit / Mayor’s Permit',
                'registration_status' => 'In Review',
                'registration_date' => '2026-03-01',
                'registration_no' => 'LGU-MKT-2241',
                'document_file' => 'mayors-permit.pdf',
                'remarks' => 'Pending final permit release.',
            ],
            [
                'id' => 3,
                'company_id' => 2,
                'date_uploaded' => '2026-03-03',
                'uploaded_by' => 'Owner 2',
                'client' => 'Company 2',
                'tin' => '987-654-321-000',
                'government_type' => 'NatGov',
                'government_body' => 'BIR Certificate of Registration (COR)',
                'registration_status' => 'Pending',
                'registration_date' => '2026-03-02',
                'registration_no' => 'BIR-2026-044',
                'document_file' => 'bir-cor.pdf',
                'remarks' => '',
            ],
        ];
    }
}
