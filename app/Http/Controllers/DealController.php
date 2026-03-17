<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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
        $contactRecords = [$this->mockContactRecord()];
        $companyOptions = ['Consulting Group'];
        $contactOptions = ['David Lee'];

        try {
            if (Schema::hasTable('contacts')) {
                $contacts = Contact::query()
                    ->select([
                        'id',
                        'customer_type',
                        'salutation',
                        'first_name',
                        'middle_name',
                        'last_name',
                        'email',
                        'phone',
                        'contact_address',
                        'company_name',
                        'company_address',
                        'position',
                    ])
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
                                $contact->middle_name,
                                $contact->last_name,
                                $contact->company_name,
                                $contact->email,
                                $contact->phone,
                            ]))),
                            'customer_type' => $contact->customer_type,
                            'salutation' => $contact->salutation,
                            'first_name' => $contact->first_name,
                            'middle_name' => $contact->middle_name,
                            'last_name' => $contact->last_name,
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
            'ownerLabel' => 'Shine Florence Padillo',
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
        return $request->validate([
            'contact_id' => ['required', 'integer'],
            'deal_name' => ['required', 'string', 'max:255'],
            'stage' => ['required', 'string', 'max:100'],
            'service_area' => ['nullable', 'string', 'max:150'],
            'services' => ['nullable', 'string', 'max:150'],
            'products' => ['nullable', 'string', 'max:150'],
            'scope_of_work' => ['nullable', 'string', 'max:3000'],
            'engagement_type' => ['nullable', 'string', 'max:150'],
            'requirements_status' => ['nullable', 'string', 'max:150'],
            'required_actions' => ['nullable', 'string', 'max:2000'],
            'estimated_professional_fee' => ['nullable', 'numeric', 'min:0'],
            'estimated_government_fees' => ['nullable', 'numeric', 'min:0'],
            'estimated_service_support_fee' => ['nullable', 'numeric', 'min:0'],
            'total_estimated_engagement_value' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'string', 'max:150'],
            'payment_terms_other' => ['nullable', 'string', 'max:255'],
            'planned_start_date' => ['nullable', 'date'],
            'estimated_duration' => ['nullable', 'string', 'max:150'],
            'estimated_completion_date' => ['nullable', 'date'],
            'client_preferred_completion_date' => ['nullable', 'date'],
            'confirmed_delivery_date' => ['nullable', 'date'],
            'timeline_notes' => ['nullable', 'string', 'max:2000'],
            'service_complexity' => ['nullable', 'string', 'max:150'],
            'support_required' => ['nullable', 'string', 'max:150'],
            'complexity_notes' => ['nullable', 'string', 'max:2000'],
            'proposal_decision' => ['nullable', 'string', 'max:150'],
            'decline_reason' => ['nullable', 'string', 'max:2000'],
            'assigned_consultant' => ['nullable', 'string', 'max:150'],
            'assigned_associate' => ['nullable', 'string', 'max:150'],
            'service_department_unit' => ['nullable', 'string', 'max:150'],
            'consultant_notes' => ['nullable', 'string', 'max:2000'],
            'associate_notes' => ['nullable', 'string', 'max:2000'],
            'customer_type' => ['nullable', 'string', 'max:100'],
            'salutation' => ['nullable', 'string', 'max:30'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:150'],
        ]);
    }

    private function buildPreviewPayload(array $validated, Contact $contact): array
    {
        if (blank($validated['total_estimated_engagement_value'] ?? null)) {
            $validated['total_estimated_engagement_value'] = collect([
                $validated['estimated_professional_fee'] ?? 0,
                $validated['estimated_government_fees'] ?? 0,
                $validated['estimated_service_support_fee'] ?? 0,
            ])->sum();
        }

        $preparedBy = $validated['assigned_consultant'] ?? 'Shine Florence Padillo';
        $reference = 'DL-'.now()->format('Ymd-His');

        return [
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
            'lead_source' => $contact->lead_source,
            'referred_by' => $contact->referred_by,
            'referral_type' => $contact->service_inquiry_type,
            'selected_owner' => 'Shine Florence Padillo',
            'prepared_by' => $preparedBy,
            'created_date' => now()->format('F j, Y'),
            'status' => 'Draft Preview',
            'deal_reference_number' => $reference,
            'optional_remarks' => Arr::first(array_filter([
                $validated['consultant_notes'] ?? null,
                $validated['timeline_notes'] ?? null,
                $validated['associate_notes'] ?? null,
            ])),
            'contact_selector_label' => trim(collect([
                $contact->salutation,
                $contact->first_name,
                $contact->middle_name,
                $contact->last_name,
            ])->filter()->implode(' ')),
        ];
    }

    private function hiddenDraftFields(array $draft): array
    {
        return collect($draft)
            ->except([
                'lead_source',
                'referred_by',
                'referral_type',
                'selected_owner',
                'prepared_by',
                'created_date',
                'status',
                'deal_reference_number',
                'optional_remarks',
                'contact_selector_label',
            ])
            ->map(fn ($value) => is_scalar($value) || $value === null ? (string) $value : '')
            ->all();
    }

    private function resolveContact(int $contactId): ?Contact
    {
        $contact = Contact::query()->find($contactId);
        if ($contact) {
            return $contact;
        }

        if ($contactId === 101) {
            return $this->mockContact();
        }

        return null;
    }

    private function mockContact(): Contact
    {
        $contact = new Contact([
            'customer_type' => 'Corporation',
            'salutation' => 'Mr.',
            'first_name' => 'David',
            'middle_name' => 'S.',
            'last_name' => 'Lee',
            'email' => 'david.lee@consulting.com',
            'phone' => '09331234567',
            'position' => 'CEO',
            'contact_address' => 'Cebu City, Philippines',
            'company_name' => 'Consulting Group',
            'owner_name' => 'John Admin',
            'kyc_status' => 'Not Submitted',
            'lead_source' => 'Website',
            'referred_by' => 'John Smith',
            'service_inquiry_type' => 'Partner Referral',
        ]);
        $contact->id = 101;

        return $contact;
    }

    private function mockContactRecord(): array
    {
        return [
            'id' => 101,
            'label' => 'Mr. David S. Lee',
            'search_blob' => 'mr david s lee consulting group david.lee@consulting.com 09331234567',
            'customer_type' => 'Corporation',
            'salutation' => 'Mr.',
            'first_name' => 'David',
            'middle_name' => 'S.',
            'last_name' => 'Lee',
            'email' => 'david.lee@consulting.com',
            'mobile' => '09331234567',
            'address' => 'Cebu City, Philippines',
            'company_name' => 'Consulting Group',
            'company_address' => 'Cebu City, Philippines',
            'position' => 'CEO',
        ];
    }

    private function buildMockSavedDeal(array $validated, Contact $contact): array
    {
        return [
            'id' => 501,
            'deal_name' => $validated['deal_name'],
            'contact_name' => trim(collect([
                $validated['first_name'] ?? $contact->first_name,
                $validated['last_name'] ?? $contact->last_name,
            ])->filter()->implode(' ')),
            'company_name' => $validated['company_name'] ?? $contact->company_name ?? '-',
            'amount' => (int) round((float) ($validated['total_estimated_engagement_value'] ?? 0)),
            'expected_close' => filled($validated['estimated_completion_date'] ?? null)
                ? date('M d, Y', strtotime((string) $validated['estimated_completion_date']))
                : 'Jun 10, 2026',
            'owner_name' => $validated['assigned_consultant'] ?? 'John Admin',
            'stage' => $validated['stage'] ?? 'Inquiry',
        ];
    }
}
