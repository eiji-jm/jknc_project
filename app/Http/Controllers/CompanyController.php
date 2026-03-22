<?php

namespace App\Http\Controllers;

use App\Models\CompanyActivity;
use App\Models\CompanyConsultationNote;
use App\Models\CompanyCif;
use App\Models\CompanyHistoryEntry;
use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $search = trim((string) $request->query('search', ''));
        $typeFilter = trim((string) $request->query('type', 'All'));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50], true) ? $perPage : 10;
        $customFields = $this->companyCustomFields($request);

        $allCompanies = collect($request->session()->get('mock_companies', $this->defaultCompanies()))
            ->map(fn (array $company): array => $this->applyCompanyCustomFieldDefaults($company, $customFields))
            ->sortByDesc('created_at')
            ->values();

        $companyTypes = $allCompanies
            ->pluck('company_type')
            ->filter(fn (?string $type) => filled($type))
            ->unique()
            ->sort()
            ->values()
            ->all();

        if ($typeFilter !== 'All' && ! in_array($typeFilter, $companyTypes, true)) {
            $typeFilter = 'All';
        }

        $filteredCompanies = $allCompanies
            ->when($search !== '', function ($collection) use ($search) {
                $term = Str::lower($search);

                return $collection->filter(function (array $company) use ($term) {
                    return collect([
                        $company['company_name'] ?? '',
                        $company['email'] ?? '',
                        $company['phone'] ?? '',
                        $company['website'] ?? '',
                        $company['address'] ?? '',
                        $company['owner_name'] ?? '',
                        $company['company_type'] ?? '',
                        ...collect($company['custom_fields'] ?? [])->values()->all(),
                    ])->contains(fn (?string $value) => Str::contains(Str::lower((string) $value), $term));
                });
            })
            ->when($typeFilter !== 'All', fn ($collection) => $collection->where('company_type', $typeFilter))
            ->values();

        $currentPage = max((int) $request->query('page', 1), 1);
        $paginatedCompanies = new LengthAwarePaginator(
            $filteredCompanies
                ->slice(($currentPage - 1) * $perPage, $perPage)
                ->map(fn (array $company) => (object) $company)
                ->values(),
            $filteredCompanies->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $typeCounts = $allCompanies
            ->groupBy(fn (array $company) => $company['company_type'] ?: 'Unspecified')
            ->map(fn ($items) => $items->count())
            ->all();

        return view('company.company', [
            'companies' => $paginatedCompanies,
            'search' => $search,
            'typeFilter' => $typeFilter,
            'companyTypes' => $companyTypes,
            'perPage' => $perPage,
            'typeCounts' => $typeCounts,
            'customFields' => $customFields,
            'fieldTypes' => collect($this->fieldTypes()),
            'lookupModules' => $this->lookupModules(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCompany($request);
        $customFields = $this->companyCustomFields($request);

        $companies = $request->session()->get('mock_companies', $this->defaultCompanies());
        $nextId = collect($companies)->max('id') + 1;

        $companies[] = [
            'id' => $nextId,
            ...$this->makeCompanyPayload($validated, $customFields),
            'owner_name' => $request->user()?->name ?? 'Owner 1',
            'created_at' => now()->toDateTimeString(),
        ];

        $request->session()->put('mock_companies', $companies);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company added successfully.');
    }

    public function update(Request $request, int $company): RedirectResponse
    {
        $validated = $this->validateCompany($request);
        $companies = collect($request->session()->get('mock_companies', $this->defaultCompanies()));
        $companyData = $companies->firstWhere('id', $company);
        $customFields = $this->companyCustomFields($request);

        abort_unless($companyData, 404);

        $updatedCompanies = $companies
            ->map(function (array $existingCompany) use ($company, $validated) {
                if ((int) $existingCompany['id'] !== $company) {
                    return $existingCompany;
                }

                return [
                    ...$existingCompany,
                    ...$this->makeCompanyPayload($validated, $customFields),
                ];
            })
            ->values()
            ->all();

        $request->session()->put('mock_companies', $updatedCompanies);

        return redirect()
            ->back()
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Request $request, int $company): RedirectResponse
    {
        $companies = collect($request->session()->get('mock_companies', $this->defaultCompanies()));
        $companyData = $companies->firstWhere('id', $company);

        abort_unless($companyData, 404);

        $request->session()->put(
            'mock_companies',
            $companies->reject(fn (array $existingCompany) => (int) $existingCompany['id'] === $company)->values()->all()
        );

        return redirect()
            ->route('company.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function storeCustomField(Request $request): RedirectResponse
    {
        $allowedTypes = collect($this->fieldTypes())->pluck('value')->all();

        $validated = $request->validate([
            'field_type' => ['required', 'string', 'in:'.implode(',', $allowedTypes)],
            'field_name' => ['required', 'string', 'max:80'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'required' => ['nullable', 'boolean'],
            'lookup_module' => ['nullable', 'string', 'in:'.implode(',', array_column($this->lookupModules(), 'value'))],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:100'],
        ]);

        $fieldName = trim((string) $validated['field_name']);
        $fieldType = (string) $validated['field_type'];
        $customFields = collect($request->session()->get('company.custom_fields', []))->values();

        $nameExists = $customFields->contains(function (array $field) use ($fieldName): bool {
            return Str::lower((string) ($field['name'] ?? '')) === Str::lower($fieldName);
        });

        if ($nameExists) {
            return back()->withErrors(['field_name' => 'Field name already exists for Company.'])->withInput();
        }

        $keyBase = Str::slug($fieldName, '_');
        if ($keyBase === '') {
            $keyBase = 'custom_field';
        }

        $key = 'custom_'.$keyBase;
        $suffix = 1;
        $usedKeys = $customFields->pluck('key')->all();
        while (in_array($key, $usedKeys, true)) {
            $suffix++;
            $key = 'custom_'.$keyBase.'_'.$suffix;
        }

        $options = collect($validated['options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter()
            ->values()
            ->all();

        if ($fieldType === 'picklist' && count($options) === 0) {
            return back()->withErrors(['options' => 'Picklist fields need at least one option.'])->withInput();
        }

        if ($fieldType !== 'lookup') {
            $validated['lookup_module'] = null;
        }

        $defaultValue = trim((string) ($validated['default_value'] ?? ''));
        if ($fieldType === 'checkbox') {
            $defaultValue = in_array(Str::lower($defaultValue), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }
        if ($fieldType === 'picklist' && $defaultValue !== '' && ! in_array($defaultValue, $options, true)) {
            return back()->withErrors(['default_value' => 'Default value must match one of the options.'])->withInput();
        }

        $customFields->push([
            'id' => 'fld-'.now()->format('YmdHisv').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'type' => $fieldType,
            'name' => $fieldName,
            'key' => $key,
            'required' => (bool) ($validated['required'] ?? false),
            'options' => $fieldType === 'picklist' ? $options : [],
            'lookup_module' => $fieldType === 'lookup' ? (string) ($validated['lookup_module'] ?? '') : null,
            'default_value' => $this->normalizedDefaultValue($fieldType, $defaultValue),
        ]);

        $request->session()->put('company.custom_fields', $customFields->values()->all());
        $request->session()->put(
            'mock_companies',
            collect($request->session()->get('mock_companies', $this->defaultCompanies()))
                ->map(fn (array $company): array => $this->applyCompanyCustomFieldDefaults($company, $customFields->all()))
                ->values()
                ->all()
        );

        return redirect()
            ->route('company.index')
            ->with('success', 'Company custom field created successfully.');
    }

    public function show(Request $request, int $company): RedirectResponse
    {
        $this->findCompanyOrAbort($request, $company);

        return redirect()->route('company.kyc', [
            'company' => $company,
            'tab' => 'client-intake',
        ]);
    }

    public function history(Request $request, int $company): View
    {
        $companyData = $this->findCompanyOrAbort($request, $company);

        return view('company.history', [
            'company' => (object) $companyData,
            'historyItems' => $this->companyHistoryItems($companyData),
        ]);
    }

    public function consultationNotes(Request $request, int $company): View
    {
        $companyData = $this->findCompanyOrAbort($request, $company);

        return view('company.consultation-notes', [
            'company' => (object) $companyData,
            'consultationNotes' => $this->companyConsultationNotes($companyData),
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
            'availableContacts' => Contact::query()
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email', 'phone', 'owner_name', 'company_name'])
                ->map(fn (Contact $contact) => [
                    'id' => $contact->id,
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'full_name' => trim($contact->first_name . ' ' . $contact->last_name),
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'mobile' => $contact->phone,
                    'owner_name' => $contact->owner_name,
                    'company_name' => $contact->company_name,
                ])
                ->values(),
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

        $validated = $validator->validated();
        $linkedContactId = (int) ($validated['linked_contact_id'] ?? 0);
        $linkedContact = $linkedContactId > 0 ? Contact::query()->find($linkedContactId) : null;

        if ($linkedContact) {
            $validated = [
                ...$validated,
                'first_name' => $validated['first_name'] ?: $linkedContact->first_name,
                'last_name' => $validated['last_name'] ?: $linkedContact->last_name,
                'full_name' => $validated['full_name'] ?: trim($linkedContact->first_name . ' ' . $linkedContact->last_name),
                'email' => $validated['email'] ?: ($linkedContact->email ?? ''),
                'phone' => $validated['phone'] ?: ($linkedContact->phone ?? ''),
                'mobile' => $validated['mobile'] ?: ($linkedContact->phone ?? ''),
                'owner_name' => $validated['owner_name'] ?: ($linkedContact->owner_name ?? ''),
            ];
        }

        $contacts[$company][] = $this->makeContactPayload(
            $validated,
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
        $companyData = $this->findCompanyOrAbort($request, $company);

        return view('company.activities', [
            'company' => (object) $companyData,
            'activities' => $this->companyActivities($companyData),
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

    private function companyHistoryItems(array $companyData): array
    {
        $companyId = (int) $companyData['id'];

        if (Schema::hasTable('company_history_entries')) {
            return CompanyHistoryEntry::query()
                ->where('company_id', $companyId)
                ->latest('occurred_at')
                ->get()
                ->map(fn (CompanyHistoryEntry $entry) => [
                    'id' => $entry->id,
                    'type' => $entry->type,
                    'title' => $entry->title,
                    'description' => $entry->description,
                    'extraLabel' => $entry->extra_label,
                    'extraValue' => $entry->extra_value,
                    'user' => $entry->user_name,
                    'initials' => $entry->user_initials,
                    'datetime' => optional($entry->occurred_at)->format('M j, Y, h:i A'),
                ])
                ->all();
        }

        return [
            [
                'id' => 1,
                'type' => 'deals',
                'title' => 'Deal linked to company',
                'description' => "Deal 'Cloud Migration Services' linked to company",
                'extraLabel' => 'Deal',
                'extraValue' => 'Cloud Migration Services',
                'user' => 'John Admin',
                'initials' => 'JA',
                'datetime' => 'Mar 10, 2026, 02:30 PM',
            ],
            [
                'id' => 2,
                'type' => 'notes',
                'title' => 'Note added to company',
                'description' => 'Added consultation note regarding implementation scope',
                'extraLabel' => 'Note',
                'extraValue' => 'Implementation Scope Review',
                'user' => 'Maria Santos',
                'initials' => 'MS',
                'datetime' => 'Mar 08, 2026, 11:15 AM',
            ],
            [
                'id' => 3,
                'type' => 'profile',
                'title' => 'Company profile updated',
                'description' => 'Registered address updated on company profile',
                'extraLabel' => 'Address',
                'extraValue' => $companyData['address'] ?? 'Makati City',
                'user' => 'John Admin',
                'initials' => 'JA',
                'datetime' => 'Mar 05, 2026, 04:20 PM',
            ],
            [
                'id' => 4,
                'type' => 'files',
                'title' => 'File uploaded',
                'description' => 'Uploaded signed corporate requirements checklist',
                'extraLabel' => 'File',
                'extraValue' => 'Corporate_Requirements.pdf',
                'user' => 'Maria Santos',
                'initials' => 'MS',
                'datetime' => 'Mar 03, 2026, 09:00 AM',
            ],
        ];
    }

    private function companyConsultationNotes(array $companyData): array
    {
        $companyId = (int) $companyData['id'];

        if (Schema::hasTable('company_consultation_notes')) {
            return CompanyConsultationNote::query()
                ->where('company_id', $companyId)
                ->latest('consultation_date')
                ->get()
                ->map(fn (CompanyConsultationNote $note) => [
                    'id' => $note->id,
                    'title' => $note->title,
                    'consultationDate' => optional($note->consultation_date)->format('Y-m-d'),
                    'author' => $note->author,
                    'summary' => $note->summary,
                    'details' => $note->details,
                    'category' => $note->category,
                    'linkedDeal' => $note->linked_deal,
                    'linkedActivity' => $note->linked_activity,
                    'attachments' => $note->attachments ?? [],
                    'createdAt' => optional($note->created_at)->toIso8601String(),
                    'updatedAt' => optional($note->updated_at)->toIso8601String(),
                ])
                ->all();
        }

        return [
            [
                'id' => 1,
                'title' => 'Initial Consultation - Software Requirements',
                'consultationDate' => '2026-03-02',
                'author' => 'Maria Santos',
                'summary' => 'Discussed enterprise software licensing options, support packages, and implementation timeline.',
                'details' => 'Client requested a phased rollout, training bundle, and SLA-based support package.',
                'category' => 'Software Consultation',
                'linkedDeal' => 'Cloud Migration Services',
                'linkedActivity' => 'Follow-up call',
                'attachments' => [
                    ['id' => 101, 'name' => 'Requirements_Checklist.pdf', 'type' => 'PDF', 'size' => 182344, 'url' => '#'],
                ],
                'createdAt' => '2026-03-02T10:20:00',
                'updatedAt' => '2026-03-02T10:20:00',
            ],
            [
                'id' => 2,
                'title' => 'Follow-up Meeting - Budget Planning',
                'consultationDate' => '2026-02-26',
                'author' => 'John Admin',
                'summary' => 'Reviewed budget allocation for Q2 software implementation and training requirements.',
                'details' => 'Budget approved for initial deployment and milestone-based invoicing.',
                'category' => 'Budget Review',
                'linkedDeal' => 'Security Audit Package',
                'linkedActivity' => 'Quarterly review meeting',
                'attachments' => [
                    ['id' => 201, 'name' => 'Budget_Allocation_Q2.xlsx', 'type' => 'XLSX', 'size' => 120304, 'url' => '#'],
                ],
                'createdAt' => '2026-02-26T14:30:00',
                'updatedAt' => '2026-02-26T14:30:00',
            ],
        ];
    }

    private function companyActivities(array $companyData): array
    {
        $companyId = (int) $companyData['id'];

        if (Schema::hasTable('company_activities')) {
            return CompanyActivity::query()
                ->where('company_id', $companyId)
                ->latest('due_at')
                ->get()
                ->map(fn (CompanyActivity $activity) => [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'icon' => match (strtolower($activity->type)) {
                        'call' => 'fa-phone',
                        'meeting' => 'fa-video',
                        'email' => 'fa-envelope',
                        default => 'fa-square-check',
                    },
                    'description' => $activity->description,
                    'when' => optional($activity->due_at)->format('M d, Y h:i A') ?: '-',
                    'owner' => $activity->assigned_user,
                    'status' => $activity->status,
                    'notes' => $activity->notes,
                    'dueAt' => optional($activity->due_at)->format('Y-m-d\TH:i'),
                ])
                ->all();
        }

        $owner = $companyData['owner_name'] ?? 'John Admin';

        return [
            [
                'id' => 1,
                'type' => 'Call',
                'icon' => 'fa-phone',
                'description' => 'Follow-up call regarding implementation timeline',
                'when' => 'Mar 03, 2026 02:30 PM',
                'owner' => $owner,
                'status' => 'Completed',
                'notes' => 'Discussed deployment dependencies.',
                'dueAt' => '2026-03-03T14:30',
            ],
            [
                'id' => 2,
                'type' => 'Meeting',
                'icon' => 'fa-video',
                'description' => 'Quarterly review meeting with stakeholders',
                'when' => 'Mar 01, 2026 10:00 AM',
                'owner' => 'Maria Santos',
                'status' => 'Completed',
                'notes' => 'Budget and rollout approved.',
                'dueAt' => '2026-03-01T10:00',
            ],
            [
                'id' => 3,
                'type' => 'Task',
                'icon' => 'fa-square-check',
                'description' => 'Prepare contract documents for review',
                'when' => 'Feb 27, 2026 11:00 AM',
                'owner' => $owner,
                'status' => 'Pending',
                'notes' => 'Waiting on legal feedback.',
                'dueAt' => '2026-02-27T11:00',
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

    private function validateCompany(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_type' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);
    }

    private function makeCompanyPayload(array $validated, array $customFields = []): array
    {
        return [
            'company_name' => $validated['company_name'],
            'company_type' => $validated['company_type'] ?? 'Corporation',
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'custom_fields' => collect($customFields)
                ->mapWithKeys(fn (array $field) => [$field['key'] => $field['default_value'] ?? ''])
                ->all(),
        ];
    }

    private function companyCustomFields(Request $request): array
    {
        return collect($request->session()->get('company.custom_fields', []))
            ->values()
            ->all();
    }

    private function applyCompanyCustomFieldDefaults(array $company, array $customFields): array
    {
        $company['custom_fields'] = $company['custom_fields'] ?? [];

        foreach ($customFields as $field) {
            $key = $field['key'] ?? null;

            if (! $key) {
                continue;
            }

            $company['custom_fields'][$key] = $company['custom_fields'][$key] ?? ($field['default_value'] ?? '');
        }

        return $company;
    }

    private function fieldTypes(): array
    {
        return [
            ['value' => 'picklist', 'label' => 'Picklist', 'icon' => 'fa-list'],
            ['value' => 'text', 'label' => 'Text', 'icon' => 'fa-font'],
            ['value' => 'numerical', 'label' => 'Numerical', 'icon' => 'fa-hashtag'],
            ['value' => 'lookup', 'label' => 'Lookup', 'icon' => 'fa-link'],
            ['value' => 'date', 'label' => 'Date', 'icon' => 'fa-calendar-days'],
            ['value' => 'checkbox', 'label' => 'Checkbox', 'icon' => 'fa-square-check'],
            ['value' => 'email', 'label' => 'Email', 'icon' => 'fa-envelope'],
            ['value' => 'phone', 'label' => 'Phone', 'icon' => 'fa-phone'],
            ['value' => 'url', 'label' => 'URL', 'icon' => 'fa-globe'],
            ['value' => 'user', 'label' => 'User', 'icon' => 'fa-user'],
            ['value' => 'currency', 'label' => 'Currency', 'icon' => 'fa-peso-sign'],
        ];
    }

    private function lookupModules(): array
    {
        return [
            ['value' => 'contacts', 'label' => 'Contacts'],
            ['value' => 'deals', 'label' => 'Deals'],
            ['value' => 'services', 'label' => 'Services'],
        ];
    }

    private function normalizedDefaultValue(string $fieldType, ?string $defaultValue): string
    {
        $value = trim((string) ($defaultValue ?? ''));

        if ($fieldType === 'checkbox') {
            return in_array(Str::lower($value), ['1', 'yes', 'true', 'checked'], true) ? '1' : '0';
        }

        return $value;
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
            'linked_contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
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
