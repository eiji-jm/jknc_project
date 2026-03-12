<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class CompanyServiceController extends Controller
{
    public function index(Request $request, int $company): View
    {
        $companyData = $this->findCompany($request, $company);

        $linkedServices = collect($request->session()->get($this->linkedKey($company), $this->defaultLinkedServices()))
            ->sortByDesc(fn (array $service) => strtotime($service['updated_at']))
            ->values()
            ->all();

        $serviceCatalog = $request->session()->get($this->catalogKey(), $this->defaultServiceCatalog());

        return view('company.services', [
            'company' => (object) $companyData,
            'services' => $linkedServices,
            'serviceCatalog' => $serviceCatalog,
            'successMessage' => $request->session()->get('services_success'),
        ]);
    }

    public function link(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);

        $validated = $request->validate([
            'service_ids' => ['required', 'array'],
            'service_ids.*' => ['integer'],
        ]);

        $catalogById = collect($request->session()->get($this->catalogKey(), $this->defaultServiceCatalog()))
            ->keyBy('id');
        $linked = collect($request->session()->get($this->linkedKey($company), $this->defaultLinkedServices()));
        $linkedIds = $linked->pluck('id')->all();

        $requestedIds = collect($validated['service_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $newIds = $requestedIds->reject(fn (int $id) => in_array($id, $linkedIds, true));

        $newServices = $newIds
            ->map(fn (int $id) => $catalogById->get($id))
            ->filter()
            ->map(function (array $service): array {
                $service['status'] = 'Active';
                $service['updated_at'] = now()->format('M d, Y h:i A');

                return $service;
            })
            ->values();

        $updatedLinked = $linked->concat($newServices)->values()->all();
        $request->session()->put($this->linkedKey($company), $updatedLinked);

        $count = $newServices->count();
        $companyName = Arr::get($companyData, 'company_name', 'company');
        $message = $count > 0
            ? "Successfully linked {$count} service(s) to {$companyName}."
            : 'No new services were linked. Selected items may already be linked.';

        return redirect()
            ->route('company.services.index', $company)
            ->with('services_success', $message);
    }

    public function show(Request $request, int $company, int $service): View
    {
        $companyData = $this->findCompany($request, $company);
        $linked = collect($request->session()->get($this->linkedKey($company), $this->defaultLinkedServices()));
        $catalog = collect($request->session()->get($this->catalogKey(), $this->defaultServiceCatalog()));

        $serviceData = $linked->firstWhere('id', $service) ?? $catalog->firstWhere('id', $service);
        abort_unless($serviceData, 404);

        return view('company.service-show', [
            'company' => (object) $companyData,
            'service' => (object) $serviceData,
        ]);
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function linkedKey(int $company): string
    {
        return "mock_company_services_{$company}";
    }

    private function catalogKey(): string
    {
        return 'mock_service_catalog';
    }

    private function defaultLinkedServices(): array
    {
        return [
            [
                'id' => 501,
                'name' => 'IT Security Assessment',
                'service_type' => 'Consultant/Retainer',
                'category' => 'Cybersecurity',
                'pricing_model' => 'Retainer',
                'base_price' => 100000,
                'status' => 'Active',
                'updated_at' => 'Apr 10, 2024 05:46 PM',
            ],
            [
                'id' => 502,
                'name' => 'Cloud Management',
                'service_type' => 'Consultant/Recurring',
                'category' => 'Cloud Services',
                'pricing_model' => 'Recurring',
                'base_price' => 20000,
                'status' => 'Active',
                'updated_at' => 'Mar 06, 2024 02:32 PM',
            ],
            [
                'id' => 503,
                'name' => 'Tax Compliance Review',
                'service_type' => 'Consultant/One-Time',
                'category' => 'Compliance',
                'pricing_model' => 'One-Time',
                'base_price' => 50000,
                'status' => 'Active',
                'updated_at' => 'Jan 20, 2024 11:10 AM',
            ],
            [
                'id' => 504,
                'name' => 'Systems Audit',
                'service_type' => 'Consultant/Milestone',
                'category' => 'IT Consulting',
                'pricing_model' => 'Milestone',
                'base_price' => 200000,
                'status' => 'Active',
                'updated_at' => 'Jan 10, 2024 09:25 AM',
            ],
        ];
    }

    private function defaultServiceCatalog(): array
    {
        return [
            [
                'id' => 601,
                'name' => 'IT Support & Maintenance',
                'service_type' => 'Managed Service/Recurring',
                'category' => 'IT Operations',
                'pricing_model' => 'Recurring',
                'base_price' => 35000,
                'status' => 'Draft',
                'updated_at' => 'Mar 05, 2026 10:30 AM',
            ],
            [
                'id' => 602,
                'name' => 'Cybersecurity Consultation',
                'service_type' => 'Consulting/Project',
                'category' => 'Cybersecurity',
                'pricing_model' => 'One-Time',
                'base_price' => 80000,
                'status' => 'Draft',
                'updated_at' => 'Feb 26, 2026 03:15 PM',
            ],
            [
                'id' => 603,
                'name' => 'Cloud Migration & Management',
                'service_type' => 'Consulting/Project',
                'category' => 'Cloud Services',
                'pricing_model' => 'Milestone',
                'base_price' => 150000,
                'status' => 'Draft',
                'updated_at' => 'Feb 12, 2026 09:40 AM',
            ],
            [
                'id' => 604,
                'name' => 'Network Security Management',
                'service_type' => 'Managed Service/Retainer',
                'category' => 'Cybersecurity',
                'pricing_model' => 'Retainer',
                'base_price' => 90000,
                'status' => 'Draft',
                'updated_at' => 'Jan 29, 2026 01:10 PM',
            ],
            [
                'id' => 605,
                'name' => 'Data Backup & Recovery',
                'service_type' => 'Managed Service/Recurring',
                'category' => 'Business Continuity',
                'pricing_model' => 'Recurring',
                'base_price' => 45000,
                'status' => 'Draft',
                'updated_at' => 'Jan 14, 2026 11:55 AM',
            ],
        ];
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
