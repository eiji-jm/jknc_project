<?php

namespace App\Http\Controllers;

use App\Models\CompanyCif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyCifController extends Controller
{
    public function create(Request $request, int $company): View
    {
        return view('company.cif.create', [
            'company' => (object) $this->findCompany($request, $company),
        ]);
    }

    public function store(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';

        CompanyCif::create([
            ...$payload,
            'company_id' => $company,
            'title' => 'Client Intake Form',
            'status' => $isSubmit ? 'submitted' : 'draft',
            'submitted_at' => $isSubmit ? now() : null,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $message = $isSubmit
            ? "Client Intake Form submitted for {$companyData['company_name']}."
            : "Client Intake Form draft saved for {$companyData['company_name']}.";

        return redirect()
            ->route('company.kyc', $company)
            ->with('cif_success', $message);
    }

    public function show(Request $request, int $company, int $cif): View
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        return view('company.cif.show', [
            'company' => (object) $companyData,
            'cif' => $cifRecord,
        ]);
    }

    public function edit(Request $request, int $company, int $cif): View
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);

        return view('company.cif.edit', [
            'company' => (object) $companyData,
            'cif' => $cifRecord,
        ]);
    }

    public function update(Request $request, int $company, int $cif): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $cifRecord = $this->findCif($company, $cif);
        $payload = $this->validatedPayload($request);
        $action = $request->input('action', 'submit');
        $isSubmit = $action !== 'draft';

        $status = $isSubmit ? ($cifRecord->status === 'reviewed' ? 'reviewed' : 'submitted') : 'draft';
        $submittedAt = $isSubmit ? ($cifRecord->submitted_at ?? now()) : null;

        $cifRecord->update([
            ...$payload,
            'status' => $status,
            'submitted_at' => $submittedAt,
            'updated_by' => $request->user()?->id,
        ]);

        $message = $isSubmit
            ? "Client Intake Form updated for {$companyData['company_name']}."
            : "Client Intake Form draft updated for {$companyData['company_name']}.";

        return redirect()
            ->route('company.cif.show', ['company' => $company, 'cif' => $cifRecord->id])
            ->with('cif_success', $message);
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
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
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'patient_identifier' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:100'],
            'preferred_pronouns' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:150'],
            'state' => ['nullable', 'string', 'max:150'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'preferred_phone' => ['nullable', 'string', 'max:100'],

            'emergency_contact_1_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_1_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_contact_1_home_phone' => ['nullable', 'string', 'max:100'],
            'emergency_contact_1_cell_phone' => ['nullable', 'string', 'max:100'],
            'emergency_contact_1_work_phone' => ['nullable', 'string', 'max:100'],

            'emergency_contact_2_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_2_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_contact_2_home_phone' => ['nullable', 'string', 'max:100'],
            'emergency_contact_2_cell_phone' => ['nullable', 'string', 'max:100'],
            'emergency_contact_2_work_phone' => ['nullable', 'string', 'max:100'],

            'insurance_carrier' => ['nullable', 'string', 'max:255'],
            'insurance_plan' => ['nullable', 'string', 'max:255'],
            'insurance_contact_number' => ['nullable', 'string', 'max:100'],
            'policy_number' => ['nullable', 'string', 'max:255'],
            'group_number' => ['nullable', 'string', 'max:255'],
            'social_security_number' => ['nullable', 'string', 'max:255'],

            'under_medical_care' => ['required', Rule::in(['0', '1'])],
            'medical_care_for' => ['nullable', 'string'],
            'primary_care_physician' => ['nullable', 'string', 'max:255'],
            'physician_address' => ['nullable', 'string'],
            'physician_contact_number' => ['nullable', 'string', 'max:100'],

            'main_concerns' => ['nullable', 'string'],
            'illness_begin' => ['nullable', 'string'],
            'visit_goals' => ['nullable', 'string'],
        ]);
    }

    private function defaultCompanies(): array
    {
        return [
            [
                'id' => 1,
                'company_name' => 'Company 1',
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
}
