<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DealController extends Controller
{
    public function index(): View
    {
        $stages = [
            'Inquiry',
            'Qualification',
            'Consultation',
            'Proposal',
            'Negotiation',
            'Payment',
            'Activation',
            'Closed Lost',
        ];

        $deals = $this->mockDeals();
        $owners = $this->ownerOptions();
        $defaultOwnerId = (int) ($owners[0]['id'] ?? 1001);
        $defaultOwner = collect($owners)->firstWhere('id', $defaultOwnerId) ?: collect($owners)->first();
        $companyOptions = [
            'ABC Company',
            'XYZ Company',
            'Global Enterprises',
            'Consulting Group',
            'Innovate Co',
            'Startup Hub',
        ];
        $contactOptions = [
            'David Lee',
            'Robert Johnson',
            'Sarah Williams',
            'Michael Brown',
        ];
        $contactRecords = [$this->mockContactRecord()];

        try {
            if (Schema::hasTable('contacts')) {
                $contactColumns = array_values(array_filter([
                    'id',
                    'customer_type',
                    'client_status',
                    'salutation',
                    'first_name',
                    'middle_initial',
                    'middle_name',
                    'last_name',
                    'name_extension',
                    'sex',
                    'date_of_birth',
                    'email',
                    'phone',
                    'contact_address',
                    'company_name',
                    'company_address',
                    'position',
                ], fn (string $column): bool => Schema::hasColumn('contacts', $column)));

                if (! in_array('id', $contactColumns, true)) {
                    $contactColumns[] = 'id';
                }

                $contacts = Contact::query()
                    ->select($contactColumns)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();

                $contactRecords = $contacts
                    ->map(function (Contact $contact): array {
                        return [
                            'id' => $contact->id,
                            'label' => trim(collect([
                                $contact->salutation,
                                $contact->first_name,
                                $contact->middle_name,
                                $contact->last_name,
                            ])->filter()->implode(' ')),
                            'search_blob' => strtolower(implode(' ', array_filter([
                                $contact->salutation,
                                $contact->first_name,
                                $contact->middle_initial,
                                $contact->middle_name,
                                $contact->last_name,
                                $contact->company_name,
                                $contact->email,
                                $contact->phone,
                            ]))),
                            'customer_type' => $contact->customer_type,
                            'client_status' => $contact->client_status,
                            'salutation' => $contact->salutation,
                            'first_name' => $contact->first_name,
                            'middle_initial' => $contact->middle_initial ?: (filled($contact->middle_name) ? mb_substr((string) $contact->middle_name, 0, 1) : null),
                            'middle_name' => $contact->middle_name,
                            'last_name' => $contact->last_name,
                            'name_extension' => $contact->name_extension,
                            'sex' => $contact->sex,
                            'date_of_birth' => optional($contact->date_of_birth)->format('Y-m-d'),
                            'email' => $contact->email,
                            'mobile' => $contact->phone,
                            'address' => $contact->contact_address,
                            'company_name' => $contact->company_name,
                            'company_address' => $contact->company_address,
                            'position' => $contact->position,
                        ];
                    })
                    ->filter(fn (array $record): bool => $record['label'] !== '' || filled($record['company_name']))
                    ->values()
                    ->all();

                if (! collect($contactRecords)->contains(fn (array $record) => (int) $record['id'] === 101)) {
                    array_unshift($contactRecords, $this->mockContactRecord());
                }

                $contactOptions = $contacts
                    ->map(fn (Contact $contact): string => trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $companyOptions = array_values(array_unique(array_merge(
                    ['Consulting Group'],
                    $contacts->pluck('company_name')->filter()->values()->all()
                )));
            }

            if (Schema::hasTable('deals')) {
                $storedDeals = Deal::query()
                    ->with('contact:id,first_name,last_name')
                    ->latest()
                    ->get()
                    ->map(function (Deal $deal): array {
                        $contactName = trim(collect([
                            $deal->first_name ?: $deal->contact?->first_name,
                            $deal->last_name ?: $deal->contact?->last_name,
                        ])->filter()->implode(' '));

                        return [
                            'id' => $deal->id,
                            'deal_name' => $deal->deal_name,
                            'contact_name' => $contactName !== '' ? $contactName : 'Linked Contact',
                            'company_name' => $deal->company_name ?: (optional($deal->contact)->company_name ?: '-'),
                            'amount' => (int) round((float) ($deal->total_estimated_engagement_value ?? 0)),
                            'expected_close' => optional($deal->estimated_completion_date)->format('M d, Y') ?: 'TBD',
                            'owner_name' => $deal->assigned_consultant ?: 'Unassigned',
                            'stage' => $deal->stage,
                        ];
                    })
                    ->all();

                if (! empty($storedDeals)) {
                    $deals = $storedDeals;
                }
            }
        } catch (Throwable) {
            // Fallback to mock options when DB is unavailable.
        }

        $savedMockDeal = session('deals.mock_saved');
        if (is_array($savedMockDeal) && ! empty($savedMockDeal)) {
            $deals = array_values(array_filter($deals, fn (array $deal): bool => (int) $deal['id'] !== (int) $savedMockDeal['id']));
            array_unshift($deals, $savedMockDeal);
        }

        $groupedByStage = [];
        foreach ($stages as $stage) {
            $stageDeals = array_values(array_filter($deals, fn (array $deal): bool => $deal['stage'] === $stage));
            $stageTotal = array_sum(array_column($stageDeals, 'amount'));
            $groupedByStage[] = [
                'stage' => $stage,
                'total_amount' => $stageTotal,
                'deals' => $stageDeals,
            ];
        }

        $draft = session('deals.preview_payload', []);

        return view('deals.index', [
            'stageColumns' => $groupedByStage,
            'totalDeals' => count($deals),
            'stageOptions' => $stages,
            'companyOptions' => $companyOptions,
            'contactOptions' => $contactOptions,
            'contactRecords' => $contactRecords,
            'dealDraft' => is_array($draft) ? $draft : [],
            'openDealModal' => (bool) request()->boolean('open_deal_modal'),
            'productOptions' => [
                'Compliance Audit Package',
                'Enterprise Tax Filing Service',
                'Cloud Storage Package (500GB)',
                'Corporate Software License',
                'Security Audit Toolkit',
            ],
            'ownerLabel' => $defaultOwner['name'] ?? 'Shine Florence Padillo',
            'owners' => $owners,
            'defaultOwnerId' => $defaultOwnerId,
        ]);
    }

    public function preview(Request $request): RedirectResponse
    {
        $validated = $this->validateDealPayload($request);
        $contact = $this->resolveContact((int) $validated['contact_id']);
        abort_unless($contact, 404);
        $draft = $this->buildPreviewPayload($validated, $contact);

        $request->session()->put('deals.preview_payload', $draft);

        return redirect()->route('deals.preview.show');
    }

    public function saveDraft(Request $request): RedirectResponse
    {
        $draft = $request->except('_token');
        $request->session()->put('deals.preview_payload', $draft);

        return redirect()->route('deals.index')->with('success', 'Deal draft saved to mock session.');
    }

    public function previewPage(Request $request): View|RedirectResponse
    {
        $draft = $request->session()->get('deals.preview_payload');

        if (! is_array($draft) || empty($draft)) {
            return redirect()->route('deals.index')->with('error', 'No deal draft found for preview.');
        }

        return view('deals.preview', [
            'draft' => $draft,
            'hiddenFields' => $this->hiddenDraftFields($draft),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDealPayload($request);

        $contact = $this->resolveContact((int) $validated['contact_id']);
        abort_unless($contact, 404);

        if (blank($validated['total_estimated_engagement_value'] ?? null)) {
            $validated['total_estimated_engagement_value'] = collect([
                $validated['estimated_professional_fee'] ?? 0,
                $validated['estimated_government_fees'] ?? 0,
                $validated['estimated_service_support_fee'] ?? 0,
            ])->sum();
        }

        if (! Schema::hasTable('deals') || ! Contact::query()->whereKey($validated['contact_id'])->exists()) {
            $mockDeal = $this->buildMockSavedDeal($validated, $contact);
            $request->session()->put('deals.mock_saved', $mockDeal);
            $request->session()->forget('deals.preview_payload');

            return redirect()->route('deals.index')->with('success', 'Mock deal saved successfully.');
        }

        Deal::query()->create([
            ...$validated,
            'customer_type' => $validated['customer_type'] ?? $contact->customer_type,
            'salutation' => $validated['salutation'] ?? $contact->salutation,
            'first_name' => $validated['first_name'] ?? $contact->first_name,
            'middle_name' => $validated['middle_name'] ?? $contact->middle_name,
            'last_name' => $validated['last_name'] ?? $contact->last_name,
            'email' => $validated['email'] ?? $contact->email,
            'mobile' => $validated['mobile'] ?? $contact->phone,
            'address' => $validated['address'] ?? $contact->contact_address,
            'company_name' => $validated['company_name'] ?? $contact->company_name,
            'company_address' => $validated['company_address'] ?? $contact->company_address,
            'position' => $validated['position'] ?? $contact->position,
        ]);

        $request->session()->forget('deals.preview_payload');

        return redirect()->route('deals.index')->with('success', 'Deal created successfully.');
    }

    public function show(int $id): View
    {
        if (Schema::hasTable('deals')) {
            $storedDeal = Deal::query()->with('contact')->find($id);
            if ($storedDeal) {
                $deal = [
                    'id' => $storedDeal->id,
                    'deal_name' => $storedDeal->deal_name,
                    'contact_name' => trim(collect([$storedDeal->first_name, $storedDeal->last_name])->filter()->implode(' ')),
                    'company_name' => $storedDeal->company_name ?: (optional($storedDeal->contact)->company_name ?: '-'),
                    'amount' => (int) round((float) ($storedDeal->total_estimated_engagement_value ?? 0)),
                    'expected_close' => optional($storedDeal->estimated_completion_date)->format('M d, Y') ?: 'TBD',
                    'owner_name' => $storedDeal->assigned_consultant ?: 'Unassigned',
                    'stage' => $storedDeal->stage,
                ];

                $detail = [
                    'related_contact' => $deal['contact_name'],
                    'related_company' => $deal['company_name'],
                    'deal_amount' => $deal['amount'],
                    'expected_close_date' => $deal['expected_close'],
                    'contact_person_name' => $deal['contact_name'],
                    'contact_person_position' => $storedDeal->position ?: '-',
                    'email_address' => $storedDeal->email ?: '-',
                    'contact_number' => $storedDeal->mobile ?: '-',
                    'client_type' => $storedDeal->customer_type ?: '-',
                    'industry' => $storedDeal->service_area ?: '-',
                    'deal_stage' => $storedDeal->stage,
                    'deal_owner' => $storedDeal->assigned_consultant ?: 'Unassigned',
                    'created_date' => optional($storedDeal->created_at)->format('n/j/Y') ?: '-',
                    'last_modified' => optional($storedDeal->updated_at)->format('Y-m-d h:i A') ?: '-',
                    'deal_status' => $storedDeal->proposal_decision === 'decline engagement' ? 'Lost' : 'Open',
                    'service' => [
                        'service_type' => $storedDeal->services ?: '-',
                        'product_type' => $storedDeal->products ?: '-',
                        'engagement_type' => $storedDeal->engagement_type ?: '-',
                        'engagement_duration' => $storedDeal->estimated_duration ?: '-',
                    ],
                    'financial' => [
                        'deal_value' => (int) round((float) ($storedDeal->total_estimated_engagement_value ?? 0)),
                        'pricing_model' => $storedDeal->engagement_type ?: '-',
                        'payment_terms' => $storedDeal->payment_terms ?: '-',
                        'commission_applicable' => $storedDeal->support_required ?: '-',
                    ],
                    'referral' => [
                        'lead_source' => optional($storedDeal->contact)->lead_source ?: '-',
                        'referred_by' => optional($storedDeal->contact)->referred_by ?: '-',
                        'referral_type' => optional($storedDeal->contact)->service_inquiry_type ?: '-',
                    ],
                    'ownership' => [
                        'lead_consultant' => $storedDeal->assigned_consultant ?: '-',
                        'lead_associate' => $storedDeal->assigned_associate ?: '-',
                        'handling_team' => $storedDeal->service_department_unit ?: '-',
                        'assigned_members' => array_values(array_filter([
                            $storedDeal->assigned_consultant,
                            $storedDeal->assigned_associate,
                        ])),
                    ],
                    'progress' => [
                        'stages' => ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'],
                        'current_stage' => $storedDeal->stage,
                    ],
                    'timeline' => [
                        [
                            'icon' => 'fa-file-circle-plus',
                            'title' => 'Deal created',
                            'timestamp' => optional($storedDeal->created_at)->format('Y-m-d, h:i A') ?: '-',
                            'user' => $storedDeal->assigned_consultant ?: 'System',
                        ],
                    ],
                    'stage_history' => [
                        [
                            'stage' => $storedDeal->stage,
                            'amount' => (int) round((float) ($storedDeal->total_estimated_engagement_value ?? 0)),
                            'duration' => $storedDeal->estimated_duration ?: '-',
                            'modified_by' => $storedDeal->assigned_consultant ?: 'System',
                            'date' => optional($storedDeal->updated_at)->format('M d, Y h:i A') ?: '-',
                        ],
                    ],
                ];

                return view('deals.show', [
                    'deal' => $deal,
                    'detail' => $detail,
                ]);
            }
        }

        $deal = collect($this->mockDeals())->firstWhere('id', $id);

        if (! $deal) {
            throw new NotFoundHttpException();
        }

        $detail = [
            'related_contact' => $deal['contact_name'],
            'related_company' => $deal['company_name'],
            'deal_amount' => $deal['amount'],
            'expected_close_date' => '6/10/2026',
            'contact_person_name' => $deal['contact_name'],
            'contact_person_position' => 'CEO',
            'email_address' => strtolower(str_replace(' ', '.', $deal['contact_name'])).'@consulting.com',
            'contact_number' => '+63 933 789 0123',
            'client_type' => 'Corporation',
            'industry' => 'Consulting',
            'deal_stage' => $deal['stage'],
            'deal_owner' => $deal['owner_name'],
            'created_date' => '2/22/2026',
            'last_modified' => '2026-02-24 01:49 PM',
            'deal_status' => $deal['stage'] === 'Closed Lost' ? 'Lost' : 'Open',
            'service' => [
                'service_type' => 'Tax Advisory',
                'product_type' => 'Compliance Audit',
                'engagement_type' => 'Regular Retainer',
                'engagement_duration' => 'Annual',
            ],
            'financial' => [
                'deal_value' => $deal['amount'],
                'pricing_model' => 'Retainer',
                'payment_terms' => 'Installment',
                'commission_applicable' => 'Yes',
            ],
            'referral' => [
                'lead_source' => 'Website',
                'referred_by' => 'John Smith',
                'referral_type' => 'Partner Referral',
            ],
            'ownership' => [
                'lead_consultant' => 'Admin User',
                'lead_associate' => 'Karen User',
                'handling_team' => 'Tax Team',
                'assigned_members' => ['Admin User', 'Karen User', 'John Adams'],
            ],
            'progress' => [
                'stages' => ['Qualification', 'Needs Analysis', 'Proposal', 'Negotiation', 'Closed Won', 'Closed Lost'],
                'current_stage' => 'Qualification',
            ],
            'timeline' => [
                [
                    'icon' => 'fa-file-circle-plus',
                    'title' => 'Deal created',
                    'timestamp' => '2026-02-22, 08:00 AM',
                    'user' => 'Admin User',
                ],
                [
                    'icon' => 'fa-circle-arrow-up',
                    'title' => 'Stage changed to Inquiry',
                    'timestamp' => '2026-02-24, 01:49 PM',
                    'user' => 'Admin User',
                ],
            ],
            'stage_history' => [
                [
                    'stage' => 'Inquiry',
                    'amount' => 920000,
                    'duration' => '2 days',
                    'modified_by' => 'Admin User',
                    'date' => 'Feb 24, 2026 01:49 PM',
                ],
                [
                    'stage' => 'Qualification',
                    'amount' => 920000,
                    'duration' => '3 days',
                    'modified_by' => 'Admin User',
                    'date' => 'Feb 27, 2026 10:15 AM',
                ],
            ],
        ];

        if ((int) $deal['id'] === 501) {
            $detail = [
                'related_contact' => 'David Lee',
                'related_company' => 'Consulting Group',
                'deal_amount' => 920000,
                'expected_close_date' => '2026-06-10',
                'contact_person_name' => 'David Lee',
                'contact_person_position' => 'CEO',
                'email_address' => 'david.lee@consulting.com',
                'contact_number' => '09331234567',
                'client_type' => 'Corporation',
                'industry' => 'Tax Advisory',
                'deal_stage' => 'Inquiry',
                'deal_owner' => 'John Admin',
                'created_date' => '2026-03-17',
                'last_modified' => '2026-03-17 10:00 AM',
                'deal_status' => 'Open',
                'service' => [
                    'service_type' => 'Tax Advisory',
                    'product_type' => 'Compliance Audit',
                    'engagement_type' => 'Regular Retainer',
                    'engagement_duration' => 'Ongoing',
                ],
                'financial' => [
                    'deal_value' => 920000,
                    'pricing_model' => 'Retainer',
                    'payment_terms' => 'Installment',
                    'commission_applicable' => 'Yes',
                ],
                'referral' => [
                    'lead_source' => 'Website',
                    'referred_by' => 'John Smith',
                    'referral_type' => 'Partner Referral',
                ],
                'ownership' => [
                    'lead_consultant' => 'Admin User',
                    'lead_associate' => 'Karen User',
                    'handling_team' => 'Tax Team',
                    'assigned_members' => ['Admin User', 'Karen User'],
                ],
                'progress' => [
                    'stages' => ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'],
                    'current_stage' => 'Inquiry',
                ],
                'timeline' => [
                    [
                        'icon' => 'fa-file-circle-plus',
                        'title' => 'Mock deal loaded',
                        'timestamp' => '2026-03-17, 10:00 AM',
                        'user' => 'John Admin',
                    ],
                ],
                'stage_history' => [
                    [
                        'stage' => 'Inquiry',
                        'amount' => 920000,
                        'duration' => 'Mock flow',
                        'modified_by' => 'John Admin',
                        'date' => 'Mar 17, 2026 10:00 AM',
                    ],
                ],
            ];
        }

        return view('deals.show', [
            'deal' => $deal,
            'detail' => $detail,
        ]);
    }

    private function mockDeals(): array
    {
        return [
            [
                'id' => 501,
                'deal_name' => 'Tax Advisory Compliance Audit Regular Retainer',
                'contact_name' => 'David Lee',
                'company_name' => 'Consulting Group',
                'amount' => 920000,
                'expected_close' => 'Jun 10, 2026',
                'owner_name' => 'John Admin',
                'stage' => 'Inquiry',
            ],
            [
                'id' => 2,
                'deal_name' => 'Data Analytics Platform',
                'contact_name' => 'David Lee',
                'company_name' => 'Consulting Group',
                'amount' => 920000,
                'expected_close' => 'Jun 10, 2026',
                'owner_name' => 'Admin User',
                'stage' => 'Qualification',
            ],
            [
                'id' => 3,
                'deal_name' => 'Cloud Migration Services',
                'contact_name' => 'Robert Johnson',
                'company_name' => 'Global Enterprises',
                'amount' => 800000,
                'expected_close' => 'May 30, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Consultation',
            ],
            [
                'id' => 4,
                'deal_name' => 'Cloud Migration Services',
                'contact_name' => 'Robert Johnson',
                'company_name' => 'Global Enterprises',
                'amount' => 800000,
                'expected_close' => 'May 30, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Proposal',
            ],
            [
                'id' => 5,
                'deal_name' => 'Cloud Migration Services',
                'contact_name' => 'Robert Johnson',
                'company_name' => 'Global Enterprises',
                'amount' => 800000,
                'expected_close' => 'May 30, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Negotiation',
            ],
            [
                'id' => 6,
                'deal_name' => 'Cloud Migration Services',
                'contact_name' => 'Robert Johnson',
                'company_name' => 'Global Enterprises',
                'amount' => 800000,
                'expected_close' => 'May 30, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Payment',
            ],
            [
                'id' => 7,
                'deal_name' => 'Cloud Migration Services',
                'contact_name' => 'Robert Johnson',
                'company_name' => 'Global Enterprises',
                'amount' => 800000,
                'expected_close' => 'May 30, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Activation',
            ],
            [
                'id' => 8,
                'deal_name' => 'Website Redesign Project',
                'contact_name' => 'Michael Brown',
                'company_name' => 'Startup Hub',
                'amount' => 180000,
                'expected_close' => 'Feb 20, 2026',
                'owner_name' => 'Admin User',
                'stage' => 'Closed Lost',
            ],
            [
                'id' => 9,
                'deal_name' => 'Security Audit Package',
                'contact_name' => 'Sarah Williams',
                'company_name' => 'Innovate Co.',
                'amount' => 120000,
                'expected_close' => 'Mar 25, 2026',
                'owner_name' => 'John Adams',
                'stage' => 'Inquiry',
            ],
        ];
    }

    private function validateDealPayload(Request $request): array
    {
        $validated = $request->validate([
            'contact_id' => ['required', 'integer', 'min:1'],
            'deal_name' => ['required', 'string', 'max:255'],
            'stage' => ['nullable', 'string', 'max:100'],
            'owner_id' => ['nullable', 'integer'],
            'service_area' => ['nullable', 'string', 'max:4000'],
            'services' => ['nullable', 'string', 'max:4000'],
            'products' => ['nullable', 'string', 'max:4000'],
            'service_area_options' => ['nullable', 'array'],
            'service_area_options.*' => ['string', 'max:255'],
            'service_area_other' => ['nullable', 'string', 'max:255'],
            'service_options' => ['nullable', 'array'],
            'service_options.*' => ['string', 'max:255'],
            'services_other' => ['nullable', 'string', 'max:255'],
            'product_options' => ['nullable', 'array'],
            'product_options.*' => ['string', 'max:255'],
            'products_other' => ['nullable', 'string', 'max:255'],
            'scope_of_work' => ['nullable', 'string'],
            'engagement_type' => ['nullable', 'string', 'max:255'],
            'requirements_status' => ['nullable'],
            'requirements_status.*' => ['nullable', 'in:provided,pending'],
            'requirements_status_map' => ['nullable', 'array'],
            'requirements_status_map.*' => ['nullable', 'in:provided,pending'],
            'required_actions' => ['nullable', 'string'],
            'required_actions_options' => ['nullable', 'array'],
            'required_actions_options.*' => ['string', 'max:255'],
            'required_actions_other' => ['nullable', 'string'],
            'estimated_professional_fee' => ['nullable'],
            'estimated_government_fees' => ['nullable'],
            'estimated_service_support_fee' => ['nullable'],
            'total_estimated_engagement_value' => ['nullable'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'payment_terms_other' => ['nullable', 'string', 'max:255'],
            'planned_start_date' => ['nullable', 'date'],
            'estimated_duration' => ['nullable', 'string', 'max:255'],
            'estimated_completion_date' => ['nullable', 'date'],
            'client_preferred_completion_date' => ['nullable', 'date'],
            'confirmed_delivery_date' => ['nullable', 'date'],
            'timeline_notes' => ['nullable', 'string'],
            'service_complexity' => ['nullable', 'string', 'max:255'],
            'support_required' => ['nullable', 'string', 'max:255'],
            'support_required_options' => ['nullable', 'array'],
            'support_required_options.*' => ['string', 'max:255'],
            'complexity_notes' => ['nullable', 'string'],
            'proposal_decision' => ['nullable', 'string', 'max:255'],
            'decline_reason' => ['nullable', 'string'],
            'assigned_consultant' => ['nullable', 'string', 'max:255'],
            'assigned_associate' => ['nullable', 'string', 'max:255'],
            'service_department_unit' => ['nullable', 'string', 'max:255'],
            'consultant_notes' => ['nullable', 'string'],
            'associate_notes' => ['nullable', 'string'],
            'customer_type' => ['nullable', 'string', 'max:255'],
            'client_status' => ['nullable', 'in:new,existing'],
            'salutation' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'name_extension' => ['nullable', 'string', 'max:50'],
            'sex' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'position' => ['nullable', 'string', 'max:255'],
            'optional_remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:100'],
            'deal_reference_number' => ['nullable', 'string', 'max:100'],
            'selected_owner' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'reviewed_by' => ['nullable', 'string', 'max:255'],
            'internal_name' => ['nullable', 'string', 'max:255'],
            'internal_date' => ['nullable', 'date'],
            'client_fullname_signature' => ['nullable', 'string', 'max:255'],
            'referred_closed_by' => ['nullable', 'string', 'max:255'],
            'internal_sales_marketing' => ['nullable', 'string', 'max:255'],
            'lead_consultant' => ['nullable', 'string', 'max:255'],
            'lead_associate_assigned' => ['nullable', 'string', 'max:255'],
            'internal_finance' => ['nullable', 'string', 'max:255'],
            'internal_president' => ['nullable', 'string', 'max:255'],
            'created_date' => ['nullable', 'string', 'max:100'],
            'lead_source' => ['nullable', 'string', 'max:255'],
            'referred_by' => ['nullable', 'string', 'max:255'],
            'referral_type' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ([
            'estimated_professional_fee',
            'estimated_government_fees',
            'estimated_service_support_fee',
            'total_estimated_engagement_value',
        ] as $moneyField) {
            if (blank($validated[$moneyField] ?? null)) {
                $validated[$moneyField] = null;
                continue;
            }

            $normalized = str_replace(',', '', (string) $validated[$moneyField]);
            $validated[$moneyField] = is_numeric($normalized) ? (float) $normalized : null;
        }

        $validated['service_area'] = $this->truncateStringForColumn($this->composeMultiSelectString(
            $validated['service_area_options'] ?? [],
            $validated['service_area_other'] ?? null,
            'Others: '
        ));
        $validated['services'] = $this->truncateStringForColumn($this->composeMultiSelectString(
            $validated['service_options'] ?? [],
            $validated['services_other'] ?? null,
            'Others: '
        ));
        $validated['products'] = $this->truncateStringForColumn($this->composeMultiSelectString(
            $validated['product_options'] ?? [],
            $validated['products_other'] ?? null,
            'Others: '
        ));
        $requirementsStatusInput = $validated['requirements_status_map'] ?? ($validated['requirements_status'] ?? []);
        if (! is_array($requirementsStatusInput)) {
            $requirementsStatusInput = [];
        }
        $validated['requirements_status_map'] = $requirementsStatusInput;
        $validated['requirements_status'] = $this->truncateStringForColumn(
            $this->stringifyRequirements($requirementsStatusInput)
        );
        $validated['required_actions'] = $this->composeMultiSelectString(
            $validated['required_actions_options'] ?? [],
            $validated['required_actions_other'] ?? null,
            'Other Internal Requirements: '
        );
        $validated['support_required'] = $this->truncateStringForColumn($this->composeMultiSelectString(
            $validated['support_required_options'] ?? [],
            null
        ));

        if (blank($validated['middle_name'] ?? null) && filled($validated['middle_initial'] ?? null)) {
            $validated['middle_name'] = $validated['middle_initial'];
        }

        if (($validated['payment_terms'] ?? null) !== 'Others') {
            $validated['payment_terms_other'] = null;
        }

        return $validated;
    }

    private function composeMultiSelectString(array $selected, ?string $other = null, string $otherPrefix = 'Others: '): ?string
    {
        $cleanSelected = collect($selected)
            ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->values();

        if (filled($other)) {
            $cleanSelected->push($otherPrefix.trim((string) $other));
        }

        return $cleanSelected->isEmpty() ? null : $cleanSelected->implode(', ');
    }

    private function stringifyRequirements(array $requirements): ?string
    {
        $pairs = collect($requirements)
            ->filter(fn ($status, $item): bool => filled($item) && in_array($status, ['provided', 'pending'], true))
            ->map(fn ($status, $item): string => str_replace('_', ' ', (string) $item).': '.$status)
            ->values();

        return $pairs->isEmpty() ? null : $pairs->implode('; ');
    }

    private function truncateStringForColumn(?string $value, int $limit = 255): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Str::limit((string) $value, $limit, '');
    }

    private function resolveContact(int $contactId): ?Contact
    {
        if (Schema::hasTable('contacts')) {
            $contact = Contact::query()->find($contactId);
            if ($contact) {
                return $contact;
            }
        }

        if ($contactId === 101) {
            return new Contact([
                'customer_type' => 'Corporation',
                'client_status' => 'existing',
                'salutation' => 'Mr.',
                'first_name' => 'David',
                'middle_initial' => 'S',
                'middle_name' => '',
                'last_name' => 'Lee',
                'name_extension' => null,
                'sex' => 'Male',
                'date_of_birth' => '1990-01-01',
                'email' => 'david.lee@consulting.com',
                'phone' => '09331234567',
                'contact_address' => 'Makati City, Philippines',
                'company_name' => 'Consulting Group',
                'company_address' => 'Ayala Avenue, Makati City',
                'position' => 'CEO',
                'lead_source' => 'Website',
                'referred_by' => 'John Smith',
                'service_inquiry_type' => 'Partner Referral',
            ]);
        }

        return null;
    }

    private function buildPreviewPayload(array $validated, Contact $contact): array
    {
        $contactData = [
            'customer_type' => $validated['customer_type'] ?? $contact->customer_type,
            'client_status' => $validated['client_status'] ?? $contact->client_status,
            'salutation' => $validated['salutation'] ?? $contact->salutation,
            'first_name' => $validated['first_name'] ?? $contact->first_name,
            'middle_initial' => $validated['middle_initial'] ?? $contact->middle_initial,
            'middle_name' => $validated['middle_name'] ?? $contact->middle_name,
            'last_name' => $validated['last_name'] ?? $contact->last_name,
            'name_extension' => $validated['name_extension'] ?? $contact->name_extension,
            'sex' => $validated['sex'] ?? $contact->sex,
            'date_of_birth' => $validated['date_of_birth'] ?? optional($contact->date_of_birth)->format('Y-m-d'),
            'email' => $validated['email'] ?? $contact->email,
            'mobile' => $validated['mobile'] ?? $contact->phone,
            'address' => $validated['address'] ?? $contact->contact_address,
            'company_name' => $validated['company_name'] ?? $contact->company_name,
            'company_address' => $validated['company_address'] ?? $contact->company_address,
            'position' => $validated['position'] ?? $contact->position,
        ];

        $estimatedTotal = collect([
            $validated['estimated_professional_fee'] ?? 0,
            $validated['estimated_government_fees'] ?? 0,
            $validated['estimated_service_support_fee'] ?? 0,
        ])->sum();

        $dealValue = $validated['total_estimated_engagement_value'] ?? null;
        if (blank($dealValue) && $estimatedTotal > 0) {
            $dealValue = $estimatedTotal;
        }

        return [
            ...$validated,
            ...$contactData,
            'total_estimated_engagement_value' => $dealValue,
            'lead_source' => $validated['lead_source'] ?? $contact->lead_source,
            'referred_by' => $validated['referred_by'] ?? $contact->referred_by,
            'referral_type' => $validated['referral_type'] ?? $contact->service_inquiry_type,
            'deal_reference_number' => $validated['deal_reference_number'] ?? ('DEAL-'.now()->format('Ymd-His')),
            'selected_owner' => $validated['selected_owner']
                ?? $this->resolveOwnerName((int) ($validated['owner_id'] ?? 0))
                ?? ($validated['assigned_consultant'] ?? 'Unassigned'),
            'prepared_by' => $validated['prepared_by'] ?? ($validated['assigned_consultant'] ?? 'System'),
            'created_date' => $validated['created_date'] ?? now()->format('Y-m-d'),
            'status' => $validated['status'] ?? 'Draft',
            'contact_id' => (int) ($validated['contact_id'] ?? 0),
        ];
    }

    private function hiddenDraftFields(array $draft): array
    {
        $hidden = [];
        $this->flattenHiddenDraftFields($draft, $hidden);

        return $hidden;
    }

    private function flattenHiddenDraftFields(array $source, array &$destination, ?string $prefix = null): void
    {
        foreach ($source as $name => $value) {
            $fieldName = $prefix === null ? (string) $name : $prefix.'['.$name.']';

            if (is_array($value)) {
                $this->flattenHiddenDraftFields($value, $destination, $fieldName);
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $destination[$fieldName] = is_bool($value) ? (int) $value : (string) ($value ?? '');
        }
    }

    private function buildMockSavedDeal(array $validated, Contact $contact): array
    {
        $contactName = trim(collect([
            $validated['first_name'] ?? $contact->first_name,
            $validated['last_name'] ?? $contact->last_name,
        ])->filter()->implode(' '));

        $amount = $validated['total_estimated_engagement_value'] ?? collect([
            $validated['estimated_professional_fee'] ?? 0,
            $validated['estimated_government_fees'] ?? 0,
            $validated['estimated_service_support_fee'] ?? 0,
        ])->sum();

        return [
            'id' => (int) now()->format('His'),
            'deal_name' => $validated['deal_name'],
            'contact_name' => $contactName !== '' ? $contactName : 'Linked Contact',
            'company_name' => $validated['company_name'] ?? $contact->company_name ?? '-',
            'amount' => (int) round((float) $amount),
            'expected_close' => filled($validated['estimated_completion_date'] ?? null)
                ? Carbon::parse($validated['estimated_completion_date'])->format('M d, Y')
                : 'TBD',
            'owner_name' => $validated['assigned_consultant'] ?? 'Unassigned',
            'stage' => $validated['stage'] ?? 'Inquiry',
        ];
    }

    private function mockContactRecord(): array
    {
        return [
            'id' => 101,
            'label' => 'David Lee',
            'search_blob' => 'david lee consulting group david.lee@consulting.com 09331234567',
            'customer_type' => 'Corporation',
            'client_status' => 'existing',
            'salutation' => 'Mr.',
            'first_name' => 'David',
            'middle_initial' => 'S',
            'middle_name' => '',
            'last_name' => 'Lee',
            'name_extension' => null,
            'sex' => 'Male',
            'date_of_birth' => '1990-01-01',
            'email' => 'david.lee@consulting.com',
            'mobile' => '09331234567',
            'address' => 'Makati City, Philippines',
            'company_name' => 'Consulting Group',
            'company_address' => 'Ayala Avenue, Makati City',
            'position' => 'CEO',
        ];
    }

    private function ownerOptions(): array
    {
        if (! Schema::hasTable('users')) {
            return [
                ['id' => 1001, 'name' => 'Shine Florence Padillo', 'email' => 'shinepadi@gmail.com'],
                ['id' => 1002, 'name' => 'John Admin', 'email' => 'john.admin@example.com'],
                ['id' => 1003, 'name' => 'Maria Santos', 'email' => 'maria.santos@example.com'],
                ['id' => 1004, 'name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@example.com'],
            ];
        }

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => (int) $user->id,
                'name' => $user->name,
                'email' => $user->email ?: strtolower(str_replace(' ', '.', $user->name)).'@example.com',
            ])
            ->values()
            ->all();

        if (! empty($users)) {
            return $users;
        }

        return [
            ['id' => 1001, 'name' => 'Shine Florence Padillo', 'email' => 'shinepadi@gmail.com'],
            ['id' => 1002, 'name' => 'John Admin', 'email' => 'john.admin@example.com'],
            ['id' => 1003, 'name' => 'Maria Santos', 'email' => 'maria.santos@example.com'],
            ['id' => 1004, 'name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@example.com'],
        ];
    }

    private function resolveOwnerName(int $ownerId): ?string
    {
        if ($ownerId <= 0) {
            return null;
        }

        $owner = collect($this->ownerOptions())->firstWhere('id', $ownerId);

        return is_array($owner) ? ($owner['name'] ?? null) : null;
    }
}
