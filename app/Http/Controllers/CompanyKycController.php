<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
            'kycRequirements' => $this->kycRequirements($latestBif),
        ]);
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

        if ($bif->approved_at) {
            $logs[] = 'Approved by '.($bif->approved_by_name ?: 'System User');
        } elseif ($bif->rejected_at) {
            $logs[] = 'Rejected by '.($bif->rejected_by_name ?: 'System User');
        } elseif ($bif->updated_at) {
            $logs[] = 'Last updated on '.$bif->updated_at->format('M j, Y g:i A');
        }

        return $logs;
    }

    private function kycRequirements(?CompanyBif $bif): array
    {
        return collect(self::REQUIREMENT_DEFINITIONS)
            ->map(function (array $group) use ($bif) {
                return [
                    'group' => $group['group'],
                    'items' => collect($group['items'])
                        ->map(function (array $requirement) use ($bif) {
                            $uploaded = $this->isRequirementUploaded($requirement['key'], $bif);

                            return [
                                'label' => $requirement['label'],
                                'uploaded' => $uploaded,
                                'helper' => $uploaded ? 'Available from saved BIF data' : 'No file uploaded yet',
                            ];
                        })
                        ->all(),
                ];
            })
            ->all();
    }

    private function isRequirementUploaded(string $key, ?CompanyBif $bif): bool
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

        if (filled(data_get($bif?->client_requirement_documents, ($documentMap[$key] ?? '').'.path'))) {
            return true;
        }

        return match ($key) {
            'dti' => filled($bif?->business_name),
            'bmbe' => filled($bif?->capital_small) || filled($bif?->capital_micro),
            'bir_cor' => filled($bif?->tin_no),
            'business_permit' => filled($bif?->business_address) || filled($bif?->office_type),
            'proof_of_billing' => filled($bif?->business_address) || filled($bif?->zip_code),
            'spa_if_applicable' => filled($bif?->authorized_contact_person_name) || filled($bif?->authorized_contact_person_position),
            'articles' => filled($bif?->place_of_incorporation) || in_array($bif?->business_organization, ['corporation', 'cooperative', 'ngo'], true),
            'partnership' => $bif?->business_organization === 'partnership',
            'bylaws' => filled($bif?->review_signature_printed_name) || filled($bif?->signature_printed_name),
            default => false,
        };
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

    private function defaultCompanies(): array
    {
        return [
            ['id' => 1, 'company_name' => 'Company 1', 'company_type' => 'Corporation', 'email' => 'company1@example.com', 'phone' => '09012345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Makati City', 'owner_name' => 'Owner 1', 'created_at' => '2026-03-01 10:00:00'],
            ['id' => 2, 'company_name' => 'Company 2', 'company_type' => 'Corporation', 'email' => 'company2@example.com', 'phone' => '09000345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Taguig City', 'owner_name' => 'Owner 2', 'created_at' => '2026-03-02 10:00:00'],
            ['id' => 3, 'company_name' => 'Company 3', 'company_type' => 'Corporation', 'email' => 'company3@example.com', 'phone' => '09777345678', 'website' => 'https://bigin.example', 'description' => 'Sample company record', 'address' => 'Pasig City', 'owner_name' => 'Owner 3', 'created_at' => '2026-03-03 10:00:00'],
        ];
    }
}
