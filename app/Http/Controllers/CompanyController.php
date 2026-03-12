<?php

namespace App\Http\Controllers;

use App\Models\CompanyCif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $companies = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (array $company) => (object) $company);

        return view('company.company', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);

        $companies = $request->session()->get('mock_companies', $this->defaultCompanies());
        $nextId = collect($companies)->max('id') + 1;

        $companies[] = [
            'id' => $nextId,
            'company_name' => $validated['company_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'owner_name' => $request->user()?->name ?? 'Owner 1',
            'created_at' => now()->toDateTimeString(),
        ];

        $request->session()->put('mock_companies', $companies);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company added successfully.');
    }

    public function show(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $cifDocuments = collect();

        if (Schema::hasTable('company_cifs')) {
            $cifDocuments = CompanyCif::query()
                ->where('company_id', $company)
                ->latest('updated_at')
                ->get();
        }

        return view('company.overview', [
            'company' => (object) $companyData,
            'cifDocuments' => $cifDocuments,
        ]);
    }

    public function history(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $historyGroups = [
            [
                'label' => 'TODAY',
                'items' => [
                    [
                        'actor' => 'John Admin',
                        'time' => '10:30 AM',
                        'message' => 'updated the ABC Corporation Ave, Suite 202, San Francisco, CA 94103 to a newer address.',
                        'date' => 'April 24, 2024',
                    ],
                    [
                        'actor' => 'Maria Santos',
                        'time' => null,
                        'message' => 'added a note: Monthly meeting scheduled for the 3rd Friday of each month.',
                        'date' => 'April 23, 2024',
                    ],
                ],
            ],
            [
                'label' => 'APRIL 22, 2024',
                'items' => [
                    [
                        'actor' => 'John Admin',
                        'time' => '06:15 PM',
                        'message' => 'updated the company website field from http://oldsiteabc.com to https://abc.com',
                        'date' => 'April 22, 2024',
                    ],
                    [
                        'actor' => 'Maria Admin',
                        'time' => null,
                        'message' => 'assigned as new contact owner of ABC Corporation.',
                        'date' => null,
                    ],
                ],
            ],
        ];

        return view('company.history', [
            'company' => (object) $companyData,
            'historyGroups' => $historyGroups,
        ]);
    }

    public function consultationNotes(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $consultationNotes = [
            [
                'title' => 'Initial Consultation - Software Requirements',
                'summary' => 'Discussed enterprise software licensing options, support packages, and implementation timeline.',
                'date' => 'Mar 02, 2026',
                'author' => 'Maria Santos',
                'attachments' => 2,
            ],
            [
                'title' => 'Follow-up Meeting - Budget Planning',
                'summary' => 'Reviewed budget allocation for Q2 software implementation and training requirements.',
                'date' => 'Feb 26, 2026',
                'author' => 'John Admin',
                'attachments' => 1,
            ],
        ];

        return view('company.consultation-notes', [
            'company' => (object) $companyData,
            'consultationNotes' => $consultationNotes,
        ]);
    }

    public function contacts(Request $request, int $company): View
    {
        $companyData = $this->findCompanyOrAbort($request, $company);
        $search = trim((string) $request->query('search', ''));

        $contacts = collect($this->companyContacts($request, $company))
            ->when($search !== '', function ($collection) use ($search) {
                $term = Str::lower($search);

                return $collection->filter(function (array $contact) use ($term) {
                    return collect([
                        $contact['full_name'] ?? '',
                        $contact['email'] ?? '',
                        $contact['phone'] ?? '',
                        $contact['mobile'] ?? '',
                        $contact['owner_name'] ?? '',
                        ...collect($contact['custom_fields'] ?? [])->values()->all(),
                    ])->contains(fn (?string $value) => Str::contains(Str::lower((string) $value), $term));
                });
            })
            ->sortBy('full_name')
            ->values();

        return view('company.contacts', [
            'company' => (object) $companyData,
            'contacts' => $contacts,
            'search' => $search,
            'customFields' => $this->companyContactCustomFields($request, $company),
        ]);
    }

    public function storeContact(Request $request, int $company): RedirectResponse
    {
        $companyData = $this->findCompanyOrAbort($request, $company);
        $validator = $this->contactValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->to(route('company.contacts', $company) . '#contact-form')
                ->withErrors($validator)
                ->withInput($request->except(['_token']) + [
                    '_contact_form' => 'create',
                ]);
        }

        $contacts = $this->allMockContacts($request);
        $companyContacts = collect($contacts[$company] ?? []);
        $nextId = (int) ($companyContacts->max('id') ?? 0) + 1;

        $contacts[$company][] = $this->makeContactPayload(
            $validator->validated(),
            $nextId,
            $company,
            $companyData['owner_name'] ?? 'Owner 1',
            $this->companyContactCustomFields($request, $company)
        );

        $request->session()->put('mock_company_contacts', $contacts);

        return redirect()
            ->route('company.contacts', $company)
            ->with('success', 'Contact added successfully.');
    }

    public function updateContact(Request $request, int $company, int $contact): RedirectResponse
    {
        $companyData = $this->findCompanyOrAbort($request, $company);
        $contacts = $this->allMockContacts($request);
        $companyContacts = collect($contacts[$company] ?? []);
        $existingContact = $companyContacts->firstWhere('id', $contact);

        abort_unless($existingContact, 404);

        $validator = $this->contactValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->to(route('company.contacts', $company) . '#contact-form')
                ->withErrors($validator)
                ->withInput($request->except(['_token', '_method']) + [
                    '_contact_form' => 'edit',
                    'contact_id' => $contact,
                ]);
        }

        $contacts[$company] = $companyContacts
            ->map(function (array $contactItem) use ($validator, $contact, $company, $companyData) {
                if ((int) $contactItem['id'] !== $contact) {
                    return $contactItem;
                }

                return $this->makeContactPayload(
                    $validator->validated(),
                    $contact,
                    $company,
                    $companyData['owner_name'] ?? 'Owner 1',
                    $this->companyContactCustomFields($request, $company),
                    $contactItem
                );
            })
            ->values()
            ->all();

        $request->session()->put('mock_company_contacts', $contacts);

        return redirect()
            ->route('company.contacts', $company)
            ->with('success', 'Contact updated successfully.');
    }

    public function storeContactCustomField(Request $request, int $company): RedirectResponse
    {
        $this->findCompanyOrAbort($request, $company);
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('company.contacts', $company) . '#custom-field-form')
                ->withErrors($validator)
                ->withInput($request->except(['_token']) + ['_custom_field_form' => 'create']);
        }

        $label = trim($validator->validated()['label']);
        $key = Str::slug($label, '_');

        if ($key === '') {
            return redirect()
                ->to(route('company.contacts', $company) . '#custom-field-form')
                ->withErrors(['label' => 'Please provide a valid field name.'])
                ->withInput($request->except(['_token']) + ['_custom_field_form' => 'create']);
        }

        $customFields = $this->allMockContactCustomFields($request);
        $companyFields = collect($customFields[$company] ?? []);

        if ($companyFields->contains(fn (array $field) => $field['key'] === $key)) {
            return redirect()
                ->to(route('company.contacts', $company) . '#custom-field-form')
                ->withErrors(['label' => 'That custom field already exists for this company.'])
                ->withInput($request->except(['_token']) + ['_custom_field_form' => 'create']);
        }

        $customFields[$company][] = [
            'key' => $key,
            'label' => $label,
        ];

        $contacts = $this->allMockContacts($request);
        $contacts[$company] = collect($contacts[$company] ?? [])
            ->map(function (array $contact) use ($key) {
                $contact['custom_fields'] = $contact['custom_fields'] ?? [];
                $contact['custom_fields'][$key] = $contact['custom_fields'][$key] ?? '';

                return $contact;
            })
            ->all();

        $request->session()->put('mock_company_contact_custom_fields', $customFields);
        $request->session()->put('mock_company_contacts', $contacts);

        return redirect()
            ->route('company.contacts', $company)
            ->with('success', 'Custom field added successfully.');
    }

    public function destroyContact(Request $request, int $company, int $contact): RedirectResponse
    {
        $this->findCompanyOrAbort($request, $company);

        $contacts = $this->allMockContacts($request);
        $companyContacts = collect($contacts[$company] ?? []);

        abort_unless($companyContacts->contains('id', $contact), 404);

        $contacts[$company] = $companyContacts
            ->reject(fn (array $contactItem) => (int) $contactItem['id'] === $contact)
            ->values()
            ->all();

        $request->session()->put('mock_company_contacts', $contacts);

        return redirect()
            ->route('company.contacts', $company)
            ->with('success', 'Contact deleted successfully.');
    }

    public function deals(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $deals = [
            [
                'name' => 'Cloud Migration Services',
                'stage' => 'Qualification',
                'amount' => 'P800,000',
                'expected_close_date' => 'Apr 30, 2024',
                'owner' => 'Maria Santos',
                'owner_initials' => 'MS',
                'last_updated' => 'Apr 10, 2024 05:33 PM',
            ],
            [
                'name' => 'Security Audit Package',
                'stage' => 'Consultation',
                'amount' => 'P120,000',
                'expected_close_date' => 'Mar 25, 2024',
                'owner' => 'Sarah Williams',
                'owner_initials' => 'SW',
                'last_updated' => 'Mar 10, 2024 11:25 AM',
            ],
            [
                'name' => 'IT Infrastructure Setup',
                'stage' => 'Negotiation',
                'amount' => 'P1,500,000',
                'expected_close_date' => 'May 30, 2024',
                'owner' => 'John Admin',
                'owner_initials' => 'JA',
                'last_updated' => 'Mar 6, 2024 02:50 PM',
            ],
            [
                'name' => 'Payroll System Integration',
                'stage' => 'Proposal',
                'amount' => 'P500,000',
                'expected_close_date' => 'Jun 15, 2024',
                'owner' => 'David Lee',
                'owner_initials' => 'DL',
                'last_updated' => 'Feb 26, 2024 02:23 PM',
            ],
        ];

        return view('company.deals', [
            'company' => (object) $companyData,
            'deals' => $deals,
        ]);
    }

    public function activities(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $activities = [
            [
                'task_name' => 'Prepare a presentation for demo',
                'due_date' => 'Feb 28, 2026',
                'status' => 'Not Started',
                'owner' => 'John Boe',
                'owner_initials' => 'JB',
            ],
            [
                'task_name' => 'Prepare a presentation for demo',
                'due_date' => 'Feb 28, 2026',
                'status' => 'In Progress',
                'owner' => 'John Boe',
                'owner_initials' => 'JB',
            ],
            [
                'task_name' => 'Prepare a presentation for demo',
                'due_date' => 'Feb 28, 2026',
                'status' => 'Completed',
                'owner' => 'John Boe',
                'owner_initials' => 'JB',
            ],
            [
                'task_name' => 'Prepare a presentation for demo',
                'due_date' => 'Feb 28, 2026',
                'status' => 'Not Started',
                'owner' => 'John Boe',
                'owner_initials' => 'JB',
            ],
            [
                'task_name' => 'Prepare a presentation for demo',
                'due_date' => 'Feb 28, 2026',
                'status' => 'In Progress',
                'owner' => 'John Boe',
                'owner_initials' => 'JB',
            ],
        ];

        return view('company.activities', [
            'company' => (object) $companyData,
            'activities' => $activities,
        ]);
    }

    public function projects(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $projects = [
            [
                'name' => 'Website Redesign',
                'status' => 'In Progress',
                'start_date' => 'Feb 01, 2024',
                'end_date' => 'Jun 30, 2024',
                'owner' => 'James Tan',
                'owner_initials' => 'JT',
            ],
            [
                'name' => 'Cloud Migration',
                'status' => 'In Progress',
                'start_date' => 'Mar 15, 2024',
                'end_date' => 'Dec 31, 2024',
                'owner' => 'Maria Santos',
                'owner_initials' => 'MS',
            ],
            [
                'name' => 'Network Security Enhancement',
                'status' => 'Completed',
                'start_date' => 'Jan 10, 2023',
                'end_date' => 'Sep 01, 2023',
                'owner' => 'Sarah Williams',
                'owner_initials' => 'SW',
            ],
            [
                'name' => 'CRM Integration',
                'status' => 'In Progress',
                'start_date' => 'Jun 01, 2023',
                'end_date' => 'May 31, 2024',
                'owner' => 'John Admin',
                'owner_initials' => 'JA',
            ],
        ];

        return view('company.projects', [
            'company' => (object) $companyData,
            'projects' => $projects,
        ]);
    }

    public function regular(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $regularEngagements = [
            [
                'name' => 'Monthly Accounting Services',
                'frequency' => 'Monthly',
                'status' => 'Active',
                'start_date' => 'Jan 01, 2024',
                'next_billing_date' => 'May 01, 2024',
                'owner' => 'John Admin',
                'owner_initials' => 'JA',
            ],
            [
                'name' => 'Quarterly Tax Filing',
                'frequency' => 'Quarterly',
                'status' => 'Active',
                'start_date' => 'Jan 01, 2024',
                'next_billing_date' => 'Jul 01, 2024',
                'owner' => 'Maria Santos',
                'owner_initials' => 'MS',
            ],
            [
                'name' => 'Annual Corporate Compliance',
                'frequency' => 'Annual',
                'status' => 'Active',
                'start_date' => 'Feb 01, 2024',
                'next_billing_date' => 'Feb 01, 2025',
                'owner' => 'David Lee',
                'owner_initials' => 'DL',
            ],
            [
                'name' => 'Business Consulting Retainer',
                'frequency' => 'Monthly',
                'status' => 'Active',
                'start_date' => 'Mar 01, 2024',
                'next_billing_date' => 'May 01, 2024',
                'owner' => 'David Lee',
                'owner_initials' => 'DL',
            ],
        ];

        return view('company.regular', [
            'company' => (object) $companyData,
            'regularEngagements' => $regularEngagements,
        ]);
    }

    public function products(Request $request, int $company): View
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $products = [
            [
                'name' => 'Managed IT Services',
                'sku' => '00101',
                'category' => 'Consulting',
                'price' => 'P20,000',
                'status' => 'Active',
            ],
            [
                'name' => 'Cloud Backup Solution',
                'sku' => '00102',
                'category' => 'Software',
                'price' => 'P15,000',
                'status' => 'Active',
            ],
            [
                'name' => 'Accounting Software',
                'sku' => '00103',
                'category' => 'Software',
                'price' => 'P30,000',
                'status' => 'Active',
            ],
            [
                'name' => 'Office Firewall Appliance',
                'sku' => '00104',
                'category' => 'Hardware',
                'price' => 'P25,500',
                'status' => 'Active',
            ],
        ];

        return view('company.products', [
            'company' => (object) $companyData,
            'products' => $products,
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

    private function findCompanyOrAbort(Request $request, int $company): array
    {
        $companyData = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->firstWhere('id', $company);

        abort_unless($companyData, 404);

        return $companyData;
    }

    private function contactValidator(Request $request)
    {
        $rules = [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
        ];

        foreach ($this->companyContactCustomFields($request, (int) $request->route('company')) as $field) {
            $rules['custom_fields.' . $field['key']] = ['nullable', 'string', 'max:255'];
        }

        return Validator::make($request->all(), $rules)->after(function ($validator) use ($request) {
            $fullName = trim((string) $request->input('full_name'));
            $firstName = trim((string) $request->input('first_name'));
            $lastName = trim((string) $request->input('last_name'));

            if ($fullName === '' && trim($firstName . ' ' . $lastName) === '') {
                $validator->errors()->add('full_name', 'Please provide a contact name.');
            }
        });
    }

    private function allMockContacts(Request $request): array
    {
        return $request->session()->get('mock_company_contacts', $this->defaultCompanyContacts());
    }

    private function companyContacts(Request $request, int $companyId): array
    {
        return $this->allMockContacts($request)[$companyId] ?? [];
    }

    private function makeContactPayload(
        array $validated,
        int $id,
        int $companyId,
        string $defaultOwner,
        array $customFields,
        array $existingContact = []
    ): array {
        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));
        $providedFullName = trim((string) ($validated['full_name'] ?? ''));
        $fullName = $providedFullName !== '' ? $providedFullName : trim($firstName . ' ' . $lastName);

        return [
            'id' => $id,
            'company_id' => $companyId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $fullName !== '' ? $fullName : 'Unnamed Contact',
            'email' => trim((string) ($validated['email'] ?? '')),
            'phone' => trim((string) ($validated['phone'] ?? '')),
            'mobile' => trim((string) ($validated['mobile'] ?? '')),
            'owner_name' => trim((string) ($validated['owner_name'] ?? '')) ?: ($existingContact['owner_name'] ?? $defaultOwner),
            'custom_fields' => $this->normalizeCustomFieldValues($validated['custom_fields'] ?? [], $existingContact['custom_fields'] ?? [], $customFields),
            'created_at' => $existingContact['created_at'] ?? now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    private function allMockContactCustomFields(Request $request): array
    {
        return $request->session()->get('mock_company_contact_custom_fields', $this->defaultCompanyContactCustomFields());
    }

    private function companyContactCustomFields(Request $request, int $companyId): array
    {
        return $this->allMockContactCustomFields($request)[$companyId] ?? [];
    }

    private function normalizeCustomFieldValues(array $submitted, array $existing, array $customFields): array
    {
        $fields = collect($customFields)
            ->mapWithKeys(function (array $field) use ($submitted, $existing) {
                $value = $submitted[$field['key']] ?? $existing[$field['key']] ?? '';

                return [$field['key'] => trim((string) $value)];
            })
            ->all();

        return $fields;
    }

    private function defaultCompanyContacts(): array
    {
        return [
            1 => [
                [
                    'id' => 1,
                    'company_id' => 1,
                    'first_name' => 'Ted',
                    'last_name' => 'Watson',
                    'full_name' => 'Ted Watson',
                    'email' => 'support@bigin.com',
                    'phone' => '',
                    'mobile' => '609-884-0686',
                    'owner_name' => 'Owner 1',
                    'custom_fields' => [
                        'linkedin' => 'linkedin.com/in/tedwatson',
                    ],
                    'created_at' => '2026-03-01 10:00:00',
                    'updated_at' => '2026-03-01 10:00:00',
                ],
            ],
            2 => [
                [
                    'id' => 1,
                    'company_id' => 2,
                    'first_name' => 'Maria',
                    'last_name' => 'Santos',
                    'full_name' => 'Maria Santos',
                    'email' => 'maria.santos@company2.example.com',
                    'phone' => '02-8123-4500',
                    'mobile' => '0917-123-4567',
                    'owner_name' => 'Owner 2',
                    'custom_fields' => [
                        'branch' => 'BGC',
                    ],
                    'created_at' => '2026-03-02 10:00:00',
                    'updated_at' => '2026-03-02 10:00:00',
                ],
            ],
            3 => [],
        ];
    }

    private function defaultCompanyContactCustomFields(): array
    {
        return [
            1 => [
                ['key' => 'linkedin', 'label' => 'LinkedIn'],
            ],
            2 => [
                ['key' => 'branch', 'label' => 'Branch'],
            ],
            3 => [],
        ];
    }
}
