<?php

namespace App\Http\Controllers;

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
        ['key' => 'sec_registration', 'label' => 'SEC / CDA Registration'],
        ['key' => 'specimen_signatures', 'label' => 'Specimen Signatures'],
        ['key' => 'tin', 'label' => 'TIN'],
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
            'company' => (object) $companyData,
            'activeTab' => 'business-client-information',
            'bif' => $latestBif,
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
            ->map(function (array $requirement) use ($bif) {
                $uploaded = match ($requirement['key']) {
                    'sec_registration' => filled($bif?->place_of_incorporation) || filled($bif?->business_organization),
                    'specimen_signatures' => filled($bif?->signature_printed_name) || filled($bif?->review_signature_printed_name),
                    'tin' => filled($bif?->tin_no),
                    default => false,
                };

                return [
                    'label' => $requirement['label'],
                    'uploaded' => $uploaded,
                    'helper' => $uploaded ? 'Available from saved BIF data' : 'No file uploaded yet',
                ];
            })
            ->all();
    }

    private function findCompany(Request $request, int $company): array
    {
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
