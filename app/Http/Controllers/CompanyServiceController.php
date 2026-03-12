<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompanyServiceController extends Controller
{
    private const STATUSES = ['Active', 'Pending', 'Completed', 'On Hold', 'Cancelled'];
    private const FREQUENCIES = ['One-time', 'Monthly', 'Quarterly', 'Annual'];

    public function globalIndex(Request $request): View
    {
        $filters = $this->serviceFilters($request);
        $services = $this->filteredServices($request, null, $filters);

        return view('services.index', [
            'services' => $services,
            'companies' => collect($request->session()->get('mock_companies', $this->defaultCompanies()))->map(fn (array $company) => (object) $company),
            'categories' => $this->allServices($request)->pluck('category')->filter()->unique()->sort()->values(),
            'staffOptions' => $this->allServices($request)->pluck('assigned_staff')->filter()->unique()->sort()->values(),
            'statusOptions' => collect(self::STATUSES),
            'frequencyOptions' => collect(self::FREQUENCIES),
            'filters' => $filters,
            'summary' => $this->serviceSummary($services),
        ]);
    }

    public function companyIndex(Request $request, int $company): View
    {
        $companyData = $this->findCompany($request, $company);
        $filters = $this->serviceFilters($request);
        $services = $this->filteredServices($request, $company, $filters);

        return view('company.services', [
            'company' => (object) $companyData,
            'services' => $services,
            'categories' => $this->allServices($request)->pluck('category')->filter()->unique()->sort()->values(),
            'staffOptions' => $this->allServices($request)->pluck('assigned_staff')->filter()->unique()->sort()->values(),
            'statusOptions' => collect(self::STATUSES),
            'frequencyOptions' => collect(self::FREQUENCIES),
            'filters' => $filters,
            'summary' => $this->serviceSummary($services),
        ]);
    }

    public function storeGlobal(Request $request): RedirectResponse
    {
        $validated = $this->validateService($request, false);
        $services = $this->allServices($request);
        $companyData = $this->findCompany($request, (int) $validated['company_id']);
        $nextId = (int) ($services->max('id') ?? 900) + 1;

        $services->push($this->makeServicePayload($nextId, (int) $validated['company_id'], $companyData['company_name'], $validated));
        $request->session()->put($this->servicesKey(), $services->values()->all());

        return redirect()
            ->route('services.index')
            ->with('services_success', 'Service engagement created successfully.');
    }

    public function storeForCompany(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $validated = $this->validateService($request, true);
        $services = $this->allServices($request);
        $nextId = (int) ($services->max('id') ?? 900) + 1;

        $services->push($this->makeServicePayload($nextId, $company, $companyData['company_name'], $validated));
        $request->session()->put($this->servicesKey(), $services->values()->all());

        return redirect()
            ->route('company.services.index', $company)
            ->with('services_success', 'Service assigned to this company successfully.');
    }

    public function showGlobal(Request $request, int $service): View
    {
        $serviceData = $this->findServiceOrAbort($request, $service);
        $companyData = $this->findCompany($request, (int) $serviceData['company_id']);

        return view('services.show', [
            'service' => (object) $serviceData,
            'company' => (object) $companyData,
        ]);
    }

    public function showForCompany(Request $request, int $company, int $service): View
    {
        $companyData = $this->findCompany($request, $company);
        $serviceData = $this->findServiceOrAbort($request, $service, $company);

        return view('company.service-show', [
            'company' => (object) $companyData,
            'service' => (object) $serviceData,
        ]);
    }

    public function updateGlobal(Request $request, int $service): RedirectResponse
    {
        $existingService = $this->findServiceOrAbort($request, $service);
        $validated = $this->validateService($request, false);
        $companyData = $this->findCompany($request, (int) $validated['company_id']);
        $services = $this->allServices($request)
            ->map(function (array $serviceItem) use ($service, $validated, $companyData, $existingService) {
                if ((int) $serviceItem['id'] !== $service) {
                    return $serviceItem;
                }

                return $this->makeServicePayload(
                    $service,
                    (int) $validated['company_id'],
                    $companyData['company_name'],
                    $validated,
                    $existingService
                );
            })
            ->values()
            ->all();

        $request->session()->put($this->servicesKey(), $services);

        return redirect()
            ->route('services.index')
            ->with('services_success', 'Service engagement updated successfully.');
    }

    public function updateForCompany(Request $request, int $company, int $service): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $existingService = $this->findServiceOrAbort($request, $service, $company);
        $validated = $this->validateService($request, true);

        $services = $this->allServices($request)
            ->map(function (array $serviceItem) use ($service, $company, $validated, $companyData, $existingService) {
                if ((int) $serviceItem['id'] !== $service || (int) $serviceItem['company_id'] !== $company) {
                    return $serviceItem;
                }

                return $this->makeServicePayload($service, $company, $companyData['company_name'], $validated, $existingService);
            })
            ->values()
            ->all();

        $request->session()->put($this->servicesKey(), $services);

        return redirect()
            ->route('company.services.index', $company)
            ->with('services_success', 'Service engagement updated successfully.');
    }

    public function destroyGlobal(Request $request, int $service): RedirectResponse
    {
        $this->findServiceOrAbort($request, $service);

        $request->session()->put(
            $this->servicesKey(),
            $this->allServices($request)
                ->reject(fn (array $serviceItem) => (int) $serviceItem['id'] === $service)
                ->values()
                ->all()
        );

        return redirect()
            ->route('services.index')
            ->with('services_success', 'Service engagement removed successfully.');
    }

    public function destroyForCompany(Request $request, int $company, int $service): RedirectResponse
    {
        $this->findServiceOrAbort($request, $service, $company);

        $request->session()->put(
            $this->servicesKey(),
            $this->allServices($request)
                ->reject(fn (array $serviceItem) => (int) $serviceItem['id'] === $service && (int) $serviceItem['company_id'] === $company)
                ->values()
                ->all()
        );

        return redirect()
            ->route('company.services.index', $company)
            ->with('services_success', 'Service removed from this company successfully.');
    }

    private function serviceFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', 'all')),
            'staff' => trim((string) $request->query('staff', 'all')),
            'category' => trim((string) $request->query('category', 'all')),
        ];
    }

    private function filteredServices(Request $request, ?int $companyId, array $filters): Collection
    {
        return $this->allServices($request)
            ->when($companyId !== null, fn (Collection $collection) => $collection->where('company_id', $companyId))
            ->when($filters['search'] !== '', function (Collection $collection) use ($filters) {
                $term = strtolower($filters['search']);

                return $collection->filter(function (array $service) use ($term) {
                    return collect([
                        $service['name'] ?? '',
                        $service['company_name'] ?? '',
                        $service['category'] ?? '',
                        $service['assigned_staff'] ?? '',
                        $service['status'] ?? '',
                        $service['frequency'] ?? '',
                    ])->contains(fn (?string $value) => str_contains(strtolower((string) $value), $term));
                });
            })
            ->when($filters['status'] !== 'all', fn (Collection $collection) => $collection->where('status', $filters['status']))
            ->when($filters['staff'] !== 'all', fn (Collection $collection) => $collection->where('assigned_staff', $filters['staff']))
            ->when($filters['category'] !== 'all', fn (Collection $collection) => $collection->where('category', $filters['category']))
            ->sortByDesc(fn (array $service) => strtotime((string) ($service['updated_at_raw'] ?? $service['updated_at'])))
            ->values();
    }

    private function serviceSummary(Collection $services): array
    {
        return [
            'active' => $services->where('status', 'Active')->count(),
            'upcoming' => $services->filter(function (array $service) {
                if (empty($service['end_date'])) {
                    return false;
                }

                $end = strtotime($service['end_date']);
                $today = strtotime(date('Y-m-d'));
                $nextThirtyDays = strtotime('+30 days', $today);

                return $end >= $today && $end <= $nextThirtyDays;
            })->count(),
            'completed' => $services->where('status', 'Completed')->count(),
        ];
    }

    private function validateService(Request $request, bool $lockCompany): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'assigned_staff' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
            'frequency' => ['required', 'in:' . implode(',', self::FREQUENCIES)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:255'],
            'service_package' => ['nullable', 'string', 'max:255'],
            'service_level' => ['nullable', 'string', 'max:255'],
        ];

        if (! $lockCompany) {
            $rules['company_id'] = ['required', 'integer'];
        }

        return $request->validate($rules);
    }

    private function makeServicePayload(int $id, int $companyId, string $companyName, array $validated, array $existing = []): array
    {
        return [
            'id' => $id,
            'company_id' => $companyId,
            'company_name' => $companyName,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'assigned_staff' => $validated['assigned_staff'],
            'status' => $validated['status'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'description' => trim((string) ($validated['description'] ?? '')),
            'priority' => trim((string) ($validated['priority'] ?? 'Normal')) ?: 'Normal',
            'service_package' => trim((string) ($validated['service_package'] ?? '')),
            'service_level' => trim((string) ($validated['service_level'] ?? '')),
            'created_at' => $existing['created_at'] ?? now()->format('Y-m-d H:i:s'),
            'updated_at_raw' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('M d, Y h:i A'),
        ];
    }

    private function allServices(Request $request): Collection
    {
        return collect($request->session()->get($this->servicesKey(), $this->defaultServices()));
    }

    private function findServiceOrAbort(Request $request, int $service, ?int $companyId = null): array
    {
        $serviceData = $this->allServices($request)->first(function (array $serviceItem) use ($service, $companyId) {
            if ((int) $serviceItem['id'] !== $service) {
                return false;
            }

            return $companyId === null || (int) $serviceItem['company_id'] === $companyId;
        });

        abort_unless($serviceData, 404);

        return $serviceData;
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function servicesKey(): string
    {
        return 'mock_services_catalog';
    }

    private function defaultServices(): array
    {
        return [
            [
                'id' => 901,
                'company_id' => 1,
                'company_name' => 'Company 1',
                'name' => 'Bookkeeping',
                'category' => 'Accounting',
                'assigned_staff' => 'Maria Santos',
                'status' => 'Active',
                'frequency' => 'Monthly',
                'start_date' => '2026-01-10',
                'end_date' => null,
                'description' => 'Monthly bookkeeping and reconciliation services.',
                'priority' => 'High',
                'service_package' => 'Accounting Core',
                'service_level' => 'Standard',
                'created_at' => '2026-01-10 09:00:00',
                'updated_at_raw' => '2026-03-10 10:30:00',
                'updated_at' => 'Mar 10, 2026 10:30 AM',
            ],
            [
                'id' => 902,
                'company_id' => 1,
                'company_name' => 'Company 1',
                'name' => 'BIR Filing',
                'category' => 'Tax',
                'assigned_staff' => 'John Admin',
                'status' => 'Pending',
                'frequency' => 'Quarterly',
                'start_date' => '2026-02-01',
                'end_date' => '2026-12-31',
                'description' => 'Quarterly filing support and submissions.',
                'priority' => 'Normal',
                'service_package' => '',
                'service_level' => 'Standard',
                'created_at' => '2026-02-01 08:30:00',
                'updated_at_raw' => '2026-03-05 14:00:00',
                'updated_at' => 'Mar 05, 2026 02:00 PM',
            ],
            [
                'id' => 903,
                'company_id' => 2,
                'company_name' => 'Company 2',
                'name' => 'Payroll Processing',
                'category' => 'HR & Payroll',
                'assigned_staff' => 'Sarah Williams',
                'status' => 'Active',
                'frequency' => 'Monthly',
                'start_date' => '2026-01-15',
                'end_date' => null,
                'description' => 'Monthly payroll generation and filing.',
                'priority' => 'High',
                'service_package' => 'Operations Plus',
                'service_level' => 'Premium',
                'created_at' => '2026-01-15 09:00:00',
                'updated_at_raw' => '2026-03-08 11:20:00',
                'updated_at' => 'Mar 08, 2026 11:20 AM',
            ],
            [
                'id' => 904,
                'company_id' => 3,
                'company_name' => 'Company 3',
                'name' => 'Tax Advisory',
                'category' => 'Advisory',
                'assigned_staff' => 'David Lee',
                'status' => 'On Hold',
                'frequency' => 'One-time',
                'start_date' => '2026-03-01',
                'end_date' => '2026-04-15',
                'description' => 'Special advisory engagement for tax planning.',
                'priority' => 'Critical',
                'service_package' => '',
                'service_level' => 'Advisory',
                'created_at' => '2026-03-01 13:15:00',
                'updated_at_raw' => '2026-03-09 15:45:00',
                'updated_at' => 'Mar 09, 2026 03:45 PM',
            ],
        ];
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
}
