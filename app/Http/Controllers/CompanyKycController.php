<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBif;
use App\Support\CompanyHistoryLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyKycController extends Controller
{
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

    private const STATUS_LABELS = [
        'draft' => 'Draft',
        'pending_approval' => 'Waiting for Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    private const STATUS_PILL_CLASSES = [
        'approved' => 'bg-green-100 text-green-700 border border-green-200',
        'pending_approval' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'draft' => 'bg-gray-100 text-gray-700 border border-gray-200',
        'rejected' => 'bg-red-100 text-red-700 border border-red-200',
        'empty' => 'bg-gray-100 text-gray-600 border border-gray-200',
    ];

    private const REQUIREMENT_DEFINITIONS = [
        [
            'group' => 'IF Sole / Natural Person / Individual',
            'items' => [
                ['key' => 'dti', 'label' => 'DTI'],
                ['key' => 'bmbe', 'label' => 'BMBE'],
                ['key' => 'bir_cor', 'label' => 'BIR COR'],
                ['key' => 'business_permit', 'label' => 'BUSINESS PERMIT'],
                ['key' => 'proof_of_billing', 'label' => 'Proof of billing'],
                ['key' => 'spa_if_applicable', 'label' => 'SPA if applicable'],
            ],
        ],
        [
            'group' => 'IF Juridical Entity',
            'items' => [
                ['key' => 'articles', 'label' => 'ARTICLES'],
                ['key' => 'partnership', 'label' => 'PARTNERSHIP'],
                ['key' => 'bylaws', 'label' => 'BYLAWS'],
            ],
        ],
    ];

    public function index(Request $request, int $company): View
    {
        $companyData = $this->findCompany($request, $company);
        $latestBif = null;

        if (Schema::hasTable('company_bifs')) {
            $latestBif = CompanyBif::query()
                ->where('company_id', $company)
                ->latest('updated_at')
                ->latest('id')
                ->first();
        }

        $kycRequirements = $this->kycRequirements($company, $latestBif);
        $requirementsComplete = $this->areKycRequirementsComplete($kycRequirements);
        $missingRequirements = $this->missingRequirementLabels($kycRequirements);

        $statusKey = $latestBif?->status ?: 'empty';

        return view('company.kyc', [
            'company' => (object) array_merge($companyData, [
                'bif_no' => $latestBif?->bif_no,
            ]),
            'activeTab' => 'business-client-information',
            'bif' => $latestBif,
            'bifRecipientEmail' => $latestBif?->authorized_contact_person_email ?: ($companyData['email'] ?? ''),
            'clientTypeOptions' => self::CLIENT_TYPES,
            'organizationOptions' => self::ORGANIZATION_TYPES,
            'nationalityOptions' => self::NATIONALITY_TYPES,
            'officeTypeOptions' => self::OFFICE_TYPES,
            'statusLabel' => $latestBif ? (self::STATUS_LABELS[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey))) : 'Not Submitted',
            'statusPillClass' => self::STATUS_PILL_CLASSES[$statusKey] ?? self::STATUS_PILL_CLASSES['empty'],
            'companyKycStatus' => $latestBif ? (self::STATUS_LABELS[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey))) : 'Not Submitted',
            'companyKycStatusClass' => self::STATUS_PILL_CLASSES[$statusKey] ?? self::STATUS_PILL_CLASSES['empty'],
            'kycActivityLogs' => $this->kycActivityLogs($latestBif, $companyData),
            'kycRequirements' => $kycRequirements,
            'requirementsComplete' => $requirementsComplete,
            'missingRequirements' => $missingRequirements,
        ]);
    }

    public function viewRequirementDocument(Request $request, int $company, string $requirement)
    {
        $bif = $this->latestCompanyBif($company);
        abort_unless($bif, 404);

        $documentKey = $this->requirementDocumentKey($requirement);
        abort_unless($documentKey !== null, 404);

        $path = $this->normalizeStoredPath((string) data_get($bif->client_requirement_documents, $documentKey.'.path', ''));
        abort_unless($path !== '', 404);

        $originalName = (string) data_get($bif->client_requirement_documents, $documentKey.'.original_name', basename($path));
        $disk = Storage::disk('public')->exists($path) ? 'public' : (Storage::disk('local')->exists($path) ? 'local' : null);
        abort_unless($disk !== null, 404);

        $mimeType = Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream';
        $contents = Storage::disk($disk)->get($path);

        return response($contents, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$originalName.'"',
        ]);
    }

    public function submitKycForVerification(Request $request, int $company): RedirectResponse
    {
        abort_unless(in_array((string) ($request->user()?->role ?? ''), ['Admin', 'SuperAdmin'], true), 403);

        $bif = $this->latestCompanyBif($company);
        if (! $bif) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'No Business Information Form found to submit for verification.']);
        }

        if (($bif->change_request_status ?? null) === 'pending') {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'A BIF change request is still pending admin review.']);
        }

        $requirements = $this->kycRequirements($company, $bif);
        $missingLabels = $this->missingRequirementLabels($requirements);

        if ($missingLabels !== []) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'Complete all required onboarding documents before submitting for verification. Missing: '.implode(', ', $missingLabels)]);
        }

        $bif->update([
            'status' => 'pending_approval',
            'submitted_at' => $bif->submitted_at ?? now(),
            'approved_at' => null,
            'approved_by_name' => null,
            'rejected_at' => null,
            'rejected_by_name' => null,
            'rejection_reason' => null,
            'updated_by' => $request->user()?->id,
        ]);

        $userName = $request->user()?->name ?? 'System User';

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => 'Company KYC submitted for verification',
            'description' => 'Business Information Form submitted for admin review',
            'extra_label' => 'Status',
            'extra_value' => 'Waiting for Approval',
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', 'Company KYC submitted for verification.');
    }

    public function approveKyc(Request $request, int $company): RedirectResponse
    {
        abort_unless(in_array((string) ($request->user()?->role ?? ''), ['Admin', 'SuperAdmin'], true), 403);
        $companyData = $this->findCompany($request, $company);
        $bif = $this->latestCompanyBif($company);

        if (! $bif) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'No Business Information Form found to approve.']);
        }

        if ((string) $bif->status !== 'pending_approval') {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'Only records submitted for verification can be approved.']);
        }

        $userName = $request->user()?->name ?? 'System User';

        $bif->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by_name' => $userName,
            'rejected_at' => null,
            'rejected_by_name' => null,
            'rejection_reason' => null,
            'updated_by' => $request->user()?->id,
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => 'Company KYC approved',
            'description' => "Business KYC approved for {$companyData['company_name']}",
            'extra_label' => 'Status',
            'extra_value' => 'Approved',
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', 'Company KYC approved successfully.');
    }

    public function rejectKyc(Request $request, int $company): RedirectResponse
    {
        abort_unless(in_array((string) ($request->user()?->role ?? ''), ['Admin', 'SuperAdmin'], true), 403);
        $companyData = $this->findCompany($request, $company);
        $bif = $this->latestCompanyBif($company);

        if (! $bif) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'No Business Information Form found to reject.']);
        }

        if ((string) $bif->status !== 'pending_approval') {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'Only records submitted for verification can be rejected.']);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $reason = trim((string) ($validated['reason'] ?? ''));
        $userName = $request->user()?->name ?? 'System User';

        $bif->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by_name' => null,
            'rejected_at' => now(),
            'rejected_by_name' => $userName,
            'rejection_reason' => $reason !== '' ? $reason : null,
            'updated_by' => $request->user()?->id,
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => 'Company KYC rejected',
            'description' => "Business KYC rejected for {$companyData['company_name']}",
            'extra_label' => 'Status',
            'extra_value' => 'Rejected',
            'user_name' => $userName,
            'user_initials' => $this->initials($userName),
        ]);

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', 'Company KYC rejected successfully.');
    }

    public function uploadRequirementDocument(Request $request, int $company, string $requirement): RedirectResponse
    {
        abort_unless(in_array((string) ($request->user()?->role ?? ''), ['Admin', 'SuperAdmin'], true), 403);

        $bif = $this->latestCompanyBif($company);
        if (! $bif) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'No Business Information Form found for requirement upload.']);
        }

        $documentKey = $this->requirementDocumentKey($requirement);
        abort_unless($documentKey !== null, 404);

        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $documents = is_array($bif->client_requirement_documents) ? $bif->client_requirement_documents : [];
        $existingPath = data_get($documents, $documentKey.'.path');
        if (filled($existingPath) && Storage::disk('public')->exists((string) $existingPath)) {
            Storage::disk('public')->delete((string) $existingPath);
        }

        $file = $validated['document'];
        $path = $file->store("company-bifs/{$bif->id}/client-documents", 'public');

        $documents[$documentKey] = [
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_at' => now()->toIso8601String(),
        ];

        $bif->update([
            'client_requirement_documents' => $documents,
            'updated_by' => $request->user()?->id,
            'last_submission_source' => 'manual',
            'last_manual_updated_at' => now(),
            'last_manual_updated_by_name' => $request->user()?->name ?? 'System User',
        ]);

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', 'Requirement document uploaded successfully.');
    }

    public function removeRequirementDocument(Request $request, int $company, string $requirement): RedirectResponse
    {
        abort_unless(in_array((string) ($request->user()?->role ?? ''), ['Admin', 'SuperAdmin'], true), 403);

        $bif = $this->latestCompanyBif($company);
        if (! $bif) {
            return redirect()
                ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
                ->withErrors(['kyc' => 'No Business Information Form found for requirement removal.']);
        }

        $documentKey = $this->requirementDocumentKey($requirement);
        abort_unless($documentKey !== null, 404);

        $documents = is_array($bif->client_requirement_documents) ? $bif->client_requirement_documents : [];
        $existingPath = data_get($documents, $documentKey.'.path');
        if (filled($existingPath) && Storage::disk('public')->exists((string) $existingPath)) {
            Storage::disk('public')->delete((string) $existingPath);
        }
        unset($documents[$documentKey]);

        $bif->update([
            'client_requirement_documents' => $documents,
            'updated_by' => $request->user()?->id,
            'last_submission_source' => 'manual',
            'last_manual_updated_at' => now(),
            'last_manual_updated_by_name' => $request->user()?->name ?? 'System User',
        ]);

        return redirect()
            ->route('company.kyc', ['company' => $company, 'tab' => 'business-client-information'])
            ->with('bif_success', 'Requirement document removed successfully.');
    }

    private function kycActivityLogs(?CompanyBif $bif, array $companyData): array
    {
        if (! $bif) {
            return [
                "KYC profile ready for {$companyData['company_name']}",
            ];
        }

        $logs = [
            "BIF profile loaded for {$companyData['company_name']}",
        ];

        if ($bif->submitted_at) {
            $logs[] = 'Submitted for verification on '.$bif->submitted_at->format('M j, Y');
        }

        if ($bif->client_form_sent_at) {
            $logs[] = 'Client BIF link sent to '.($bif->client_form_sent_to_email ?: 'recipient').' on '.$bif->client_form_sent_at->format('M j, Y g:i A');
        }

        if ($bif->client_submitted_at) {
            $logs[] = 'Client completed the BIF on '.$bif->client_submitted_at->format('M j, Y g:i A');
        }

        if (($bif->change_request_status ?? null) === 'pending') {
            $logs[] = 'Pending BIF change request from '.($bif->change_requested_by_name ?: 'User');
        } elseif (($bif->change_request_status ?? null) === 'rejected') {
            $logs[] = 'Latest BIF change request was rejected by '.($bif->change_reviewed_by_name ?: 'Admin');
        }

        if ($bif->approved_at) {
            $logs[] = 'Approved by '.($bif->approved_by_name ?: 'System User');
        } elseif ($bif->rejected_at) {
            $logs[] = 'Rejected by '.($bif->rejected_by_name ?: 'System User');
        } elseif ($bif->updated_at) {
            $logs[] = 'Last updated on '.$bif->updated_at->format('M j, Y g:i A');
        }

        return $logs;
    }

    private function kycRequirements(int $company, ?CompanyBif $bif): array
    {
        return collect(self::REQUIREMENT_DEFINITIONS)
            ->map(function (array $group) use ($company, $bif) {
                return [
                    'group' => $group['group'],
                    'items' => collect($group['items'])
                        ->map(function (array $requirement) use ($company, $bif) {
                            $documentMeta = $this->requirementDocumentMeta($requirement['key'], $bif);
                            $uploaded = $documentMeta['uploaded'];

                            return [
                                'key' => $requirement['key'],
                                'label' => $requirement['label'],
                                'uploaded' => $uploaded,
                                'file_name' => $documentMeta['file_name'],
                                'mime_type' => $documentMeta['mime_type'],
                                'uploaded_at' => $documentMeta['uploaded_at'],
                                'file_url' => $uploaded
                                    ? route('company.kyc.requirements.view', ['company' => $company, 'requirement' => $requirement['key']])
                                    : null,
                                'helper' => $uploaded
                                    ? ($documentMeta['file_name'] ?: 'File uploaded')
                                    : 'No file uploaded yet',
                            ];
                        })
                        ->all(),
                ];
            })
            ->all();
    }

    private function areKycRequirementsComplete(array $requirements): bool
    {
        foreach ($requirements as $group) {
            foreach (($group['items'] ?? []) as $item) {
                if (! (bool) ($item['uploaded'] ?? false)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function missingRequirementLabels(array $requirements): array
    {
        return collect($requirements)
            ->flatMap(fn (array $group) => $group['items'] ?? [])
            ->filter(fn (array $item) => ! (bool) ($item['uploaded'] ?? false))
            ->pluck('label')
            ->values()
            ->all();
    }

    private function requirementDocumentMeta(string $key, ?CompanyBif $bif): array
    {
        $documentKey = $this->requirementDocumentKey($key);
        if ($documentKey === null) {
            return [
                'uploaded' => false,
                'file_name' => null,
                'file_url' => null,
                'mime_type' => null,
                'uploaded_at' => null,
            ];
        }

        $path = $this->normalizeStoredPath((string) data_get($bif?->client_requirement_documents, $documentKey.'.path', ''));
        $fileName = data_get($bif?->client_requirement_documents, $documentKey.'.original_name');
        $uploaded = $path !== '';
        $mimeType = null;
        if ($uploaded) {
            if (Storage::disk('public')->exists($path)) {
                $mimeType = Storage::disk('public')->mimeType($path) ?: null;
            } elseif (Storage::disk('local')->exists($path)) {
                $mimeType = Storage::disk('local')->mimeType($path) ?: null;
            }
        }

        return [
            'uploaded' => $uploaded,
            'file_name' => $uploaded ? ($fileName ?: basename($path)) : null,
            'file_url' => null,
            'mime_type' => $mimeType,
            'uploaded_at' => data_get($bif?->client_requirement_documents, $documentKey.'.uploaded_at'),
        ];
    }

    private function normalizeStoredPath(string $path): string
    {
        $value = trim(str_replace('\\', '/', $path));
        if ($value === '') {
            return '';
        }

        if (str_contains($value, '/storage/')) {
            $value = substr($value, strpos($value, '/storage/') + 9);
        } elseif (str_starts_with($value, 'storage/')) {
            $value = substr($value, 8);
        }

        return ltrim($value, '/');
    }

    private function requirementDocumentKey(string $requirement): ?string
    {
        $documentMap = [
            'dti' => 'sole_dti_document',
            'bmbe' => 'sole_bmbe_document',
            'bir_cor' => 'entity_bir_cor_document',
            'business_permit' => 'entity_business_permit_document',
            'proof_of_billing' => 'common_proof_of_billing_document',
            'spa_if_applicable' => 'common_spa_document',
            'articles' => 'entity_articles_document',
            'partnership' => 'entity_articles_document',
            'bylaws' => 'entity_bylaws_document',
        ];

        return $documentMap[$requirement] ?? null;
    }

    private function findCompany(Request $request, int $company): array
    {
        if (Schema::hasTable('companies')) {
            $record = Company::query()->find($company);

            if ($record) {
                return [
                    'id' => $record->id,
                    'company_name' => $record->company_name,
                    'company_type' => null,
                    'email' => $record->email,
                    'phone' => $record->phone,
                    'website' => $record->website,
                    'description' => $record->description,
                    'address' => $record->address,
                    'owner_name' => $record->owner_name,
                    'created_at' => optional($record->created_at)->toDateTimeString(),
                ];
            }
        }

        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function latestCompanyBif(int $company): ?CompanyBif
    {
        if (! Schema::hasTable('company_bifs')) {
            return null;
        }

        return CompanyBif::query()
            ->where('company_id', $company)
            ->latest('updated_at')
            ->latest('id')
            ->first();
    }

    private function initials(string $name): string
    {
        $segments = collect(explode(' ', trim($name)))->filter()->take(2);

        if ($segments->isEmpty()) {
            return 'SU';
        }

        return $segments
            ->map(fn (string $segment): string => strtoupper(mb_substr($segment, 0, 1)))
            ->implode('');
    }

    private function defaultCompanies(): array
    {
        return [
            ['id' => 1, 'company_name' => 'Company 1', 'company_type' => 'Corporation', 'email' => 'company1@example.com', 'phone' => '09012345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Makati City', 'owner_name' => 'Owner 1', 'created_at' => '2026-03-01 10:00:00'],
            ['id' => 2, 'company_name' => 'Company 2', 'company_type' => 'Corporation', 'email' => 'company2@example.com', 'phone' => '09000345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Taguig City', 'owner_name' => 'Owner 2', 'created_at' => '2026-03-02 10:00:00'],
            ['id' => 3, 'company_name' => 'Company 3', 'company_type' => 'Corporation', 'email' => 'company3@example.com', 'phone' => '09777345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Pasig City', 'owner_name' => 'Owner 3', 'created_at' => '2026-03-03 10:00:00'],
        ];
    }
}
