<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class ContactsController extends Controller
{
    private const KYC_STATUSES = [
        'Verified',
        'Pending Verification',
        'Not Submitted',
        'Rejected',
    ];

    private const TABS = [
        'kyc' => 'KYC',
        'history' => 'History',
        'consultation-notes' => 'Consultation Notes',
        'activities' => 'Activities',
        'deals' => 'Deals',
        'company' => 'Company',
        'projects' => 'Projects',
        'regular' => 'Regular',
        'products' => 'Products',
        'services' => 'Services',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $kycFilter = (string) $request->query('kyc', 'All');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50], true) ? $perPage : 10;

        $query = Contact::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if (in_array($kycFilter, self::KYC_STATUSES, true)) {
            $query->where('kyc_status', $kycFilter);
        } else {
            $kycFilter = 'All';
        }

        $contacts = $query
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate($perPage)
            ->withQueryString();
        $customFields = collect($request->session()->get('contacts.custom_fields', []))
            ->values()
            ->all();

        $owners = $this->ownerOptions();
        $defaultOwner = $request->user();
        $defaultOwnerId = old('owner_id', $defaultOwner?->id ?? Arr::first($owners)['id']);

        return view('contacts.index', [
            'contacts' => $contacts,
            'search' => $search,
            'kycFilter' => $kycFilter,
            'perPage' => $perPage,
            'statusCounts' => [
                'Verified' => Contact::query()->where('kyc_status', 'Verified')->count(),
                'Pending Verification' => Contact::query()->where('kyc_status', 'Pending Verification')->count(),
                'Not Submitted' => Contact::query()->where('kyc_status', 'Not Submitted')->count(),
                'Rejected' => Contact::query()->where('kyc_status', 'Rejected')->count(),
            ],
            'kycStatuses' => self::KYC_STATUSES,
            'owners' => $owners,
            'defaultOwnerId' => (int) $defaultOwnerId,
            'customFields' => $customFields,
            'fieldTypes' => collect($this->fieldTypes()),
            'lookupModules' => $this->lookupModules(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $owners = collect($this->ownerOptions())->keyBy('id');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'lead_source' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:2000'],
            'owner_id' => ['required', 'integer'],
        ]);

        validator($validated, [])->after(function (Validator $validator) use ($validated) {
            if (blank($validated['last_name'] ?? null) && blank($validated['email'] ?? null)) {
                $validator->errors()->add('last_name', 'Last name or email is required.');
            }
        })->validate();

        $owner = $owners->get((int) $validated['owner_id']);
        if (! $owner) {
            return back()->withErrors(['owner_id' => 'Please select a valid owner.'])->withInput();
        }

        Contact::query()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? '',
            'lead_source' => $validated['lead_source'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['mobile'] ?? null,
            'description' => $validated['description'] ?? null,
            'kyc_status' => 'Not Submitted',
            'owner_name' => $owner['name'],
        ]);

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
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
        $customFields = collect($request->session()->get('contacts.custom_fields', []))->values();

        $nameExists = $customFields->contains(function (array $field) use ($fieldName): bool {
            return Str::lower((string) ($field['name'] ?? '')) === Str::lower($fieldName);
        });
        if ($nameExists) {
            return back()->withErrors(['field_name' => 'Field name already exists for Contacts.'])->withInput();
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

        $request->session()->put('contacts.custom_fields', $customFields->values()->all());

        return redirect()->route('contacts.index')->with('success', 'Contact custom field created successfully.');
    }

    public function show(Request $request, Contact $contact): View
    {
        $tab = strtolower((string) $request->query('tab', 'kyc'));
        if (! array_key_exists($tab, self::TABS)) {
            $tab = 'kyc';
        }

        return view('contacts.show', [
            'contact' => $contact,
            'tab' => $tab,
            'tabs' => self::TABS,
            'tabData' => $this->tabData($contact),
        ]);
    }

    private function ownerOptions(): array
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => ['id' => (int) $user->id, 'name' => $user->name])
            ->all();

        if (! empty($users)) {
            return $users;
        }

        return [
            ['id' => 1001, 'name' => 'John Admin'],
            ['id' => 1002, 'name' => 'AdminUser'],
            ['id' => 1003, 'name' => 'Shine Florence Padillo'],
        ];
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
            ['value' => 'deals', 'label' => 'Deals'],
            ['value' => 'company', 'label' => 'Company'],
            ['value' => 'products', 'label' => 'Products'],
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

    private function tabData(Contact $contact): array
    {
        $owner = $contact->owner_name ?: 'John Admin';

        return [
            'history' => [
                'filters' => ['All Activities', 'Profile Changes', 'KYC Updates', 'Deals', 'Files', 'Notes'],
                'items' => [
                    [
                        'id' => 1,
                        'type' => 'deals',
                        'icon' => 'fa-arrow-trend-up',
                        'title' => 'Deal linked to contact',
                        'description' => "Deal 'Corporate Software License' linked to contact",
                        'extraLabel' => 'Deal',
                        'extraValue' => 'Corporate Software License',
                        'user' => 'John Admin',
                        'initials' => 'JA',
                        'datetime' => 'Mar 1, 2026, 02:30 PM',
                    ],
                    [
                        'id' => 2,
                        'type' => 'deals',
                        'icon' => 'fa-arrow-trend-up',
                        'title' => 'Deal stage changed',
                        'description' => "Deal 'Corporate Software License' stage changed from Proposal to Negotiation",
                        'extraLabel' => 'Deal',
                        'extraValue' => 'Corporate Software License',
                        'user' => 'John Admin',
                        'initials' => 'JA',
                        'datetime' => 'Feb 28, 2026, 11:15 AM',
                    ],
                    [
                        'id' => 3,
                        'type' => 'notes',
                        'icon' => 'fa-note-sticky',
                        'title' => 'Note added to contact',
                        'description' => 'Added consultation note regarding software requirements',
                        'extraLabel' => 'Note',
                        'extraValue' => '"Discussed enterprise software licensing options and support packages"',
                        'user' => 'Maria Santos',
                        'initials' => 'MS',
                        'datetime' => 'Feb 26, 2026, 04:45 PM',
                    ],
                    [
                        'id' => 4,
                        'type' => 'profile',
                        'icon' => 'fa-pen',
                        'title' => 'Phone number updated',
                        'description' => 'Phone number changed',
                        'extraLabel' => 'Phone',
                        'extraValue' => '+63 917 123 4567',
                        'user' => 'Juan Dela Cruz',
                        'initials' => 'JD',
                        'datetime' => 'Feb 15, 2026, 04:20 PM',
                    ],
                    [
                        'id' => 5,
                        'type' => 'kyc',
                        'icon' => 'fa-shield-check',
                        'title' => 'KYC status updated',
                        'description' => 'KYC moved from Not Submitted to Pending Verification',
                        'extraLabel' => 'KYC',
                        'extraValue' => 'Pending Verification',
                        'user' => $owner,
                        'initials' => 'JA',
                        'datetime' => 'Feb 13, 2026, 10:20 AM',
                    ],
                    [
                        'id' => 6,
                        'type' => 'files',
                        'icon' => 'fa-file-arrow-up',
                        'title' => 'File attached',
                        'description' => 'Uploaded signed requirements document',
                        'extraLabel' => 'File',
                        'extraValue' => 'Requirements_Signed.pdf',
                        'user' => 'Maria Santos',
                        'initials' => 'MS',
                        'datetime' => 'Feb 12, 2026, 03:12 PM',
                    ],
                    [
                        'id' => 7,
                        'type' => 'profile',
                        'icon' => 'fa-user-plus',
                        'title' => 'Contact created',
                        'description' => 'Contact record created in the system',
                        'extraLabel' => 'Profile',
                        'extraValue' => 'New contact record added',
                        'user' => $owner,
                        'initials' => 'JA',
                        'datetime' => 'Feb 10, 2026, 09:00 AM',
                    ],
                ],
            ],
            'consultation-notes' => [
                [
                    'id' => 1,
                    'title' => 'Initial Consultation - Software Requirements',
                    'consultationDate' => '2026-03-02',
                    'author' => 'Maria Santos',
                    'summary' => 'Discussed enterprise software licensing options, support packages, and implementation timeline.',
                    'details' => 'Client requested a phased rollout, training bundle, and SLA-based support package. They prefer annual billing with quarterly review sessions and requested revised pricing by next week.',
                    'category' => 'Software Consultation',
                    'attachments' => [
                        [
                            'id' => 101,
                            'name' => 'Requirements_Checklist.pdf',
                            'type' => 'PDF',
                            'size' => 182344,
                            'url' => '#',
                        ],
                        [
                            'id' => 102,
                            'name' => 'Discovery_Notes.docx',
                            'type' => 'DOCX',
                            'size' => 93211,
                            'url' => '#',
                        ],
                    ],
                    'createdAt' => '2026-03-02T10:20:00',
                    'updatedAt' => '2026-03-02T10:20:00',
                ],
                [
                    'id' => 2,
                    'title' => 'Follow-up Meeting - Budget Planning',
                    'consultationDate' => '2026-02-26',
                    'author' => $owner,
                    'summary' => 'Reviewed budget allocation for Q2 software implementation and training requirements.',
                    'details' => 'Budget approved for initial deployment and user onboarding. Security audit to be scoped in a separate proposal. Finance team requested milestone-based invoicing.',
                    'category' => 'Budget Review',
                    'attachments' => [
                        [
                            'id' => 201,
                            'name' => 'Budget_Allocation_Q2.xlsx',
                            'type' => 'XLSX',
                            'size' => 120304,
                            'url' => '#',
                        ],
                    ],
                    'createdAt' => '2026-02-26T14:30:00',
                    'updatedAt' => '2026-02-26T14:30:00',
                ],
            ],
            'activities' => [
                [
                    'type' => 'Call',
                    'icon' => 'fa-phone',
                    'description' => 'Follow-up call regarding software implementation timeline',
                    'when' => 'Mar 03, 2026 02:30 PM',
                    'owner' => $owner,
                    'status' => 'Completed',
                ],
                [
                    'type' => 'Meeting',
                    'icon' => 'fa-video',
                    'description' => 'Quarterly review meeting with stakeholders',
                    'when' => 'Mar 01, 2026 10:00 AM',
                    'owner' => 'Maria Santos',
                    'status' => 'Completed',
                ],
                [
                    'type' => 'Email',
                    'icon' => 'fa-envelope',
                    'description' => 'Sent proposal document and pricing breakdown',
                    'when' => 'Feb 28, 2026 04:15 PM',
                    'owner' => $owner,
                    'status' => 'Sent',
                ],
                [
                    'type' => 'Task',
                    'icon' => 'fa-square-check',
                    'description' => 'Prepare contract documents for review',
                    'when' => 'Feb 27, 2026 11:00 AM',
                    'owner' => $owner,
                    'status' => 'Pending',
                ],
            ],
            'deals' => [
                [
                    'name' => 'Corporate Software License',
                    'stage' => 'Negotiation',
                    'amount' => '₱250,000',
                    'closing_date' => 'Mar 15, 2026',
                    'owner' => 'John Admin',
                    'status' => 'Open',
                ],
                [
                    'name' => 'Security Audit Package',
                    'stage' => 'Proposal',
                    'amount' => '₱120,000',
                    'closing_date' => 'Apr 02, 2026',
                    'owner' => 'Maria Santos',
                    'status' => 'Pending',
                ],
                [
                    'name' => 'Cloud Migration Retainer',
                    'stage' => 'Qualification',
                    'amount' => '₱340,000',
                    'closing_date' => 'Apr 20, 2026',
                    'owner' => 'AdminUser',
                    'status' => 'Open',
                ],
                [
                    'name' => 'Website Redesign Project',
                    'stage' => 'Closed Lost',
                    'amount' => '₱180,000',
                    'closing_date' => 'Feb 20, 2026',
                    'owner' => 'Admin User',
                    'status' => 'Lost',
                ],
            ],
            'projects' => [
                [
                    'name' => 'Software Implementation Phase 1',
                    'type' => 'Implementation',
                    'status' => 'In Progress',
                    'start_date' => 'Mar 01, 2026',
                    'team' => 'Tech Team A',
                ],
                [
                    'name' => 'Security Audit 2026',
                    'type' => 'Audit',
                    'status' => 'Planning',
                    'start_date' => 'Apr 15, 2026',
                    'team' => 'Security Team',
                ],
            ],
            'regular' => [
                'items' => [
                    [
                        'service' => 'Monthly IT Support & Maintenance',
                        'frequency' => 'Monthly',
                        'fee' => 'P25,000',
                        'start_date' => 'Jan 01, 2026',
                        'status' => 'Active',
                    ],
                    [
                        'service' => 'Quarterly Security Review',
                        'frequency' => 'Quarterly',
                        'fee' => 'P50,000',
                        'start_date' => 'Jan 01, 2026',
                        'status' => 'Active',
                    ],
                ],
                'revenue' => 'P25,000',
            ],
            'products' => [
                'items' => [
                    [
                        'name' => 'Enterprise Software License (Annual)',
                        'price' => 'P150,000',
                        'quantity' => '1',
                        'total' => 'P150,000',
                        'date' => 'Feb 24, 2026',
                    ],
                    [
                        'name' => 'Cloud Storage Package (500GB)',
                        'price' => 'P5,000',
                        'quantity' => '2',
                        'total' => 'P10,000',
                        'date' => 'Feb 24, 2026',
                    ],
                ],
                'grand_total' => 'P160,000',
                'total_products' => 2,
                'total_quantity' => 3,
                'total_revenue' => 'P160,000',
            ],
            'services' => [
                'items' => [
                    [
                        'name' => 'Software Implementation & Training',
                        'description' => 'Full implementation of enterprise software with on-site training for all users',
                        'fee' => 'P180,000',
                        'staff' => 'Tech Team A',
                        'status' => 'In Progress',
                    ],
                    [
                        'name' => 'IT Infrastructure Assessment',
                        'description' => 'Comprehensive assessment of current IT infrastructure and recommendations',
                        'fee' => 'P85,000',
                        'staff' => $owner,
                        'status' => 'Completed',
                    ],
                ],
                'total_services' => 2,
                'completed' => 1,
                'total_value' => 'P265,000',
            ],
        ];
    }
}
