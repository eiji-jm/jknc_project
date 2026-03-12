<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompanyDealController extends Controller
{
    private const STAGES = ['Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Won', 'Lost'];

    public function index(Request $request, int $company): View
    {
        $companyData = $this->findCompany($request, $company);
        $search = trim((string) $request->query('search', ''));
        $stage = trim((string) $request->query('stage', 'all'));

        $deals = collect($request->session()->get($this->dealsKey(), $this->defaultDeals()))
            ->where('company_id', $company)
            ->when($search !== '', function (Collection $collection) use ($search) {
                $term = strtolower($search);

                return $collection->filter(function (array $deal) use ($term) {
                    return collect([
                        $deal['name'] ?? '',
                        $deal['stage'] ?? '',
                        $deal['owner'] ?? '',
                        $deal['deal_source'] ?? '',
                        $deal['priority'] ?? '',
                    ])->contains(fn (?string $value) => str_contains(strtolower((string) $value), $term));
                });
            })
            ->when($stage !== 'all', fn (Collection $collection) => $collection->where('stage', $stage))
            ->sortByDesc(fn (array $deal) => strtotime($deal['updated_at']))
            ->values();

        $summary = [
            'total' => $deals->count(),
            'open' => $deals->whereNotIn('stage', ['Won', 'Lost'])->count(),
            'won' => $deals->where('stage', 'Won')->count(),
            'pipeline_value' => $deals->whereNotIn('stage', ['Lost'])->sum('amount'),
        ];

        return view('company.deals', [
            'company' => (object) $companyData,
            'deals' => $deals,
            'search' => $search,
            'stage' => $stage,
            'stages' => self::STAGES,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $validated = $this->validateDeal($request);

        $deals = collect($request->session()->get($this->dealsKey(), $this->defaultDeals()));
        $nextId = (int) ($deals->max('id') ?? 800) + 1;

        $deals->push($this->makeDealPayload(
            $nextId,
            $company,
            $companyData['company_name'],
            $validated
        ));

        $request->session()->put($this->dealsKey(), $deals->values()->all());

        return redirect()
            ->route('company.deals', $company)
            ->with('deals_success', 'Deal added successfully.');
    }

    public function show(Request $request, int $company, int $deal): View
    {
        $companyData = $this->findCompany($request, $company);
        $dealData = collect($request->session()->get($this->dealsKey(), $this->defaultDeals()))
            ->firstWhere(fn (array $item) => (int) $item['id'] === $deal && (int) $item['company_id'] === $company);

        abort_unless($dealData, 404);

        return view('company.deal-show', [
            'company' => (object) $companyData,
            'deal' => (object) $dealData,
            'stages' => self::STAGES,
        ]);
    }

    public function update(Request $request, int $company, int $deal): RedirectResponse
    {
        $companyData = $this->findCompany($request, $company);
        $validated = $this->validateDeal($request);

        $deals = collect($request->session()->get($this->dealsKey(), $this->defaultDeals()));
        abort_unless($deals->contains(fn (array $item) => (int) $item['id'] === $deal && (int) $item['company_id'] === $company), 404);

        $deals = $deals->map(function (array $item) use ($deal, $company, $companyData, $validated) {
            if ((int) $item['id'] !== $deal || (int) $item['company_id'] !== $company) {
                return $item;
            }

            return $this->makeDealPayload($deal, $company, $companyData['company_name'], $validated, $item);
        })->values();

        $request->session()->put($this->dealsKey(), $deals->all());

        return redirect()
            ->route('company.deals', $company)
            ->with('deals_success', 'Deal updated successfully.');
    }

    public function destroy(Request $request, int $company, int $deal): RedirectResponse
    {
        $this->findCompany($request, $company);

        $deals = collect($request->session()->get($this->dealsKey(), $this->defaultDeals()));
        abort_unless($deals->contains(fn (array $item) => (int) $item['id'] === $deal && (int) $item['company_id'] === $company), 404);

        $request->session()->put(
            $this->dealsKey(),
            $deals->reject(fn (array $item) => (int) $item['id'] === $deal && (int) $item['company_id'] === $company)->values()->all()
        );

        return redirect()
            ->route('company.deals', $company)
            ->with('deals_success', 'Deal removed successfully.');
    }

    private function validateDeal(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stage' => ['required', 'in:' . implode(',', self::STAGES)],
            'amount' => ['required', 'numeric', 'min:0'],
            'expected_close_date' => ['nullable', 'date'],
            'owner' => ['required', 'string', 'max:255'],
            'deal_source' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function makeDealPayload(int $id, int $companyId, string $companyName, array $validated, array $existing = []): array
    {
        $owner = trim((string) $validated['owner']);
        $initials = collect(explode(' ', $owner))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return [
            'id' => $id,
            'company_id' => $companyId,
            'company_name' => $companyName,
            'name' => $validated['name'],
            'stage' => $validated['stage'],
            'amount' => (float) $validated['amount'],
            'expected_close_date' => $validated['expected_close_date'] ?? null,
            'owner' => $owner,
            'owner_initials' => $initials ?: 'NA',
            'deal_source' => $validated['deal_source'] ?? '',
            'priority' => $validated['priority'] ?? 'Normal',
            'notes' => $validated['notes'] ?? '',
            'created_at' => $existing['created_at'] ?? now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('M d, Y h:i A'),
        ];
    }

    private function dealsKey(): string
    {
        return 'mock_deals_catalog';
    }

    private function findCompany(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function defaultDeals(): array
    {
        return [
            [
                'id' => 801,
                'company_id' => 1,
                'company_name' => 'Company 1',
                'name' => 'Cloud Migration Services',
                'stage' => 'Qualification',
                'amount' => 800000,
                'expected_close_date' => '2026-04-30',
                'owner' => 'Maria Santos',
                'owner_initials' => 'MS',
                'deal_source' => 'Referral',
                'priority' => 'High',
                'notes' => 'Needs technical scoping session.',
                'created_at' => '2026-03-01 10:00:00',
                'updated_at' => 'Apr 10, 2026 05:33 PM',
            ],
            [
                'id' => 802,
                'company_id' => 1,
                'company_name' => 'Company 1',
                'name' => 'Security Audit Package',
                'stage' => 'Consultation',
                'amount' => 120000,
                'expected_close_date' => '2026-03-25',
                'owner' => 'Sarah Williams',
                'owner_initials' => 'SW',
                'deal_source' => 'Website Inquiry',
                'priority' => 'Normal',
                'notes' => 'Waiting on final scope approval.',
                'created_at' => '2026-03-02 11:00:00',
                'updated_at' => 'Mar 10, 2026 11:25 AM',
            ],
            [
                'id' => 803,
                'company_id' => 2,
                'company_name' => 'Company 2',
                'name' => 'Payroll System Integration',
                'stage' => 'Proposal',
                'amount' => 500000,
                'expected_close_date' => '2026-06-15',
                'owner' => 'David Lee',
                'owner_initials' => 'DL',
                'deal_source' => 'Upsell',
                'priority' => 'High',
                'notes' => 'Proposal shared with client CFO.',
                'created_at' => '2026-03-03 01:00:00',
                'updated_at' => 'Feb 26, 2026 02:23 PM',
            ],
            [
                'id' => 804,
                'company_id' => 2,
                'company_name' => 'Company 2',
                'name' => 'IT Infrastructure Setup',
                'stage' => 'Negotiation',
                'amount' => 1500000,
                'expected_close_date' => '2026-05-30',
                'owner' => 'John Admin',
                'owner_initials' => 'JA',
                'deal_source' => 'Partner Referral',
                'priority' => 'High',
                'notes' => 'Client negotiating payment schedule.',
                'created_at' => '2026-03-04 02:00:00',
                'updated_at' => 'Mar 06, 2026 02:50 PM',
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
