<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $stages = $this->dealStages();

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
                            'deal_code' => $deal->deal_code ?: $this->generateDealCode($deal->contact, $deal->id),
                            'deal_name' => $deal->deal_name,
                            'contact_name' => $contactName !== '' ? $contactName : 'Linked Contact',
                            'company_name' => $deal->company_name ?: (optional($deal->contact)->company_name ?: '-'),
                            'amount' => (int) round((float) ($deal->total_estimated_engagement_value ?? 0)),
                            'expected_close' => optional($deal->estimated_completion_date)->format('M d, Y') ?: 'TBD',
                            'owner_name' => $deal->assigned_consultant ?: 'Unassigned',
                            'stage' => $deal->stage,
                            'created_by' => $deal->created_by ?: optional(Auth::user())->name ?: 'System',
                            'created_at_label' => optional($deal->created_at)->format('F d, Y • h:i:s A') ?: now()->format('F d, Y • h:i:s A'),
                            'search_blob' => Str::lower(implode(' ', array_filter([
                                $deal->deal_code,
                                $deal->deal_name,
                                $contactName,
                            ]))),
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

        if ($search !== '') {
            $deals = array_values(array_filter($deals, function (array $deal) use ($search): bool {
                $blob = Str::lower(implode(' ', array_filter([
                    $deal['deal_code'] ?? null,
                    $deal['deal_name'] ?? null,
                    $deal['contact_name'] ?? null,
                    $deal['search_blob'] ?? null,
                ])));

                return Str::contains($blob, Str::lower($search));
            }));
        }

        $groupedByStage = [];
        foreach ($stages as $stage) {
            $stageDeals = array_values(array_filter($deals, fn (array $deal): bool => $deal['stage'] === $stage['name']));
            $stageTotal = array_sum(array_column($stageDeals, 'amount'));
            $groupedByStage[] = [
                'id' => $stage['id'],
                'stage' => $stage['name'],
                'color' => $stage['color'],
                'total_amount' => $stageTotal,
                'deals' => $stageDeals,
            ];
        }

        $draft = session('deals.preview_payload', []);

        return view('deals.index', [
            'stageColumns' => $groupedByStage,
            'totalDeals' => count($deals),
            'search' => $search,
            'stageOptions' => array_values(array_map(fn (array $stage): string => $stage['name'], $stages)),
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

    public function storeStage(Request $request): JsonResponse
    {
        abort_unless(Schema::hasTable('deal_stages'), 500, 'Deal stages table is not available.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'after_stage_id' => ['nullable', 'integer', 'exists:deal_stages,id'],
        ]);

        $insertAfterId = (int) ($validated['after_stage_id'] ?? 0);
        $stages = DealStage::query()->orderBy('order')->get();
        $insertIndex = $insertAfterId > 0 ? max(0, $stages->search(fn (DealStage $stage) => $stage->id === $insertAfterId) + 1) : $stages->count();

        $stages->splice($insertIndex, 0, [new DealStage([
            'name' => trim($validated['name']),
            'order' => 0,
            'color' => null,
        ])]);

        foreach ($stages->values() as $index => $stage) {
            if ($stage->exists) {
                $stage->update(['order' => $index + 1]);
            } else {
                $stage->order = $index + 1;
                $stage->save();
            }
        }

        $createdStage = DealStage::query()->where('name', trim($validated['name']))->firstOrFail();

        return response()->json([
            'stage' => [
                'id' => $createdStage->id,
                'name' => $createdStage->name,
                'color' => $createdStage->color,
            ],
        ]);
    }

    public function updateStage(Request $request, DealStage $stage): JsonResponse
    {
        abort_unless(Schema::hasTable('deal_stages'), 500, 'Deal stages table is not available.');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $originalName = $stage->name;
        $updates = [];

        if (array_key_exists('name', $validated) && filled($validated['name']) && $validated['name'] !== $stage->name) {
            $updates['name'] = trim($validated['name']);
        }

        if (array_key_exists('color', $validated)) {
            $updates['color'] = $validated['color'] ?: null;
        }

        if ($updates !== []) {
            $stage->update($updates);
        }

        if (isset($updates['name']) && Schema::hasTable('deals')) {
            Deal::query()->where('stage', $originalName)->update(['stage' => $updates['name']]);
        }

        return response()->json([
            'stage' => [
                'id' => $stage->id,
                'name' => $stage->fresh()->name,
                'color' => $stage->fresh()->color,
            ],
        ]);
    }

    public function moveStage(Request $request, DealStage $stage): JsonResponse
    {
        abort_unless(Schema::hasTable('deal_stages'), 500, 'Deal stages table is not available.');

        $validated = $request->validate([
            'direction' => ['required', 'in:left,right'],
        ]);

        $stages = DealStage::query()->orderBy('order')->get()->values();
        $currentIndex = $stages->search(fn (DealStage $item) => $item->id === $stage->id);
        $swapIndex = $validated['direction'] === 'left' ? $currentIndex - 1 : $currentIndex + 1;

        if ($currentIndex === false || ! isset($stages[$swapIndex])) {
            return response()->json(['ok' => true]);
        }

        $current = $stages[$currentIndex];
        $swap = $stages[$swapIndex];
        $currentOrder = $current->order;
        $current->update(['order' => $swap->order]);
        $swap->update(['order' => $currentOrder]);

        return response()->json(['ok' => true]);
    }

    public function destroyStage(DealStage $stage): JsonResponse
    {
        abort_unless(Schema::hasTable('deal_stages'), 500, 'Deal stages table is not available.');

        if (Schema::hasTable('deals') && Deal::query()->where('stage', $stage->name)->exists()) {
            return response()->json(['message' => 'Stage cannot be deleted while it has deals.'], 422);
        }

        $stage->delete();

        DealStage::query()->orderBy('order')->get()->values()->each(function (DealStage $item, int $index) {
            $item->update(['order' => $index + 1]);
        });

        return response()->json(['ok' => true]);
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
            'dealFormData' => $this->normalizeDealFormData($draft),
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
            $request->session()->put('deals.mock_saved_payload', [
                'id' => $mockDeal['id'],
                ...$validated,
            ]);
            $request->session()->forget('deals.preview_payload');

            return redirect()->route('deals.show', $mockDeal['id'])->with('success', 'Mock deal saved successfully.');
        }

        $createdDeal = Deal::query()->create([
            ...$validated,
            ...(Schema::hasColumn('deals', 'deal_code') ? ['deal_code' => $this->generateDealCode($contact)] : []),
            ...(Schema::hasColumn('deals', 'created_by') ? ['created_by' => optional(Auth::user())->name ?: 'System'] : []),
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

        return redirect()->route('deals.show', $createdDeal->id)->with('success', 'Deal created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateDealPayload($request);
        $contact = $this->resolveContact((int) $validated['contact_id']);
        abort_unless($contact, 404);

        if (! Schema::hasTable('deals')) {
            $mockSaved = $this->buildMockSavedDeal($validated, $contact) + ['id' => $id];
            $request->session()->put('deals.mock_saved', $mockSaved);
            $request->session()->put('deals.mock_saved_payload', [
                'id' => $id,
                ...$validated,
            ]);

            return redirect()->route('deals.show', $id)->with('success', 'Mock deal updated successfully.');
        }

        $deal = Deal::query()->findOrFail($id);
        $deal->fill([
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
        if (Schema::hasColumn('deals', 'deal_code') && blank($deal->deal_code)) {
            $deal->deal_code = $this->generateDealCode($contact, $deal->id);
        }
        $deal->save();

        return redirect()->route('deals.show', $deal->id)->with('success', 'Deal updated successfully.');
    }

    public function show(int $id): View
    {
        if (Schema::hasTable('deals')) {
            $storedDeal = Deal::query()->with('contact')->find($id);
            if ($storedDeal) {
                $deal = [
                    'id' => $storedDeal->id,
                    'deal_code' => $storedDeal->deal_code ?: $this->generateDealCode($storedDeal->contact, $storedDeal->id),
                    'deal_name' => $storedDeal->deal_name,
                    'contact_name' => trim(collect([$storedDeal->first_name, $storedDeal->last_name])->filter()->implode(' ')),
                    'company_name' => $storedDeal->company_name ?: (optional($storedDeal->contact)->company_name ?: '-'),
                    'amount' => (int) round((float) ($storedDeal->total_estimated_engagement_value ?? 0)),
                    'expected_close' => optional($storedDeal->estimated_completion_date)->format('M d, Y') ?: 'TBD',
                    'owner_name' => $storedDeal->assigned_consultant ?: 'Unassigned',
                    'stage' => $storedDeal->stage,
                    'created_by' => $storedDeal->created_by ?: 'System',
                    'created_at_label' => optional($storedDeal->created_at)->format('F d, Y • h:i:s A') ?: now()->format('F d, Y • h:i:s A'),
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
                    'dealFormData' => $this->normalizeDealFormData($storedDeal->toArray()),
                    ...$this->dealPanelContext($this->normalizeDealFormData($storedDeal->toArray())),
                    'openDealModal' => (bool) request()->boolean('edit_deal'),
                ]);
            }
        }

        $mockPayload = session('deals.mock_saved_payload');
        if (is_array($mockPayload) && (int) ($mockPayload['id'] ?? 0) === $id) {
            $mockDeal = session('deals.mock_saved', []);
            $deal = [
                'id' => $id,
                'deal_code' => $mockPayload['deal_code'] ?? ($mockDeal['deal_code'] ?? $this->generateDealCodeFromNames($mockPayload['first_name'] ?? '', $mockPayload['last_name'] ?? '', $id)),
                'deal_name' => $mockPayload['deal_name'] ?? ($mockDeal['deal_name'] ?? 'Mock Deal'),
                'contact_name' => trim(collect([$mockPayload['first_name'] ?? '', $mockPayload['last_name'] ?? ''])->filter()->implode(' ')) ?: ($mockDeal['contact_name'] ?? 'Linked Contact'),
                'company_name' => $mockPayload['company_name'] ?? ($mockDeal['company_name'] ?? '-'),
                'amount' => (int) round((float) ($mockPayload['total_estimated_engagement_value'] ?? 0)),
                'expected_close' => filled($mockPayload['estimated_completion_date'] ?? null)
                    ? Carbon::parse($mockPayload['estimated_completion_date'])->format('M d, Y')
                    : ($mockDeal['expected_close'] ?? 'TBD'),
                'owner_name' => $mockPayload['assigned_consultant'] ?? ($mockDeal['owner_name'] ?? 'Unassigned'),
                'stage' => $mockPayload['stage'] ?? ($mockDeal['stage'] ?? 'Inquiry'),
            ];

            $detail = [
                'related_contact' => $deal['contact_name'],
                'related_company' => $deal['company_name'],
                'deal_amount' => $deal['amount'],
                'expected_close_date' => $deal['expected_close'],
                'contact_person_name' => $deal['contact_name'],
                'contact_person_position' => $mockPayload['position'] ?? '-',
                'email_address' => $mockPayload['email'] ?? '-',
                'contact_number' => $mockPayload['mobile'] ?? '-',
                'client_type' => $mockPayload['customer_type'] ?? '-',
                'industry' => $mockPayload['service_area'] ?? '-',
                'deal_stage' => $deal['stage'],
                'deal_owner' => $deal['owner_name'],
                'created_date' => now()->format('n/j/Y'),
                'last_modified' => now()->format('Y-m-d h:i A'),
                'deal_status' => 'Open',
                'service' => [
                    'service_type' => $mockPayload['services'] ?? '-',
                    'product_type' => $mockPayload['products'] ?? '-',
                    'engagement_type' => $mockPayload['engagement_type'] ?? '-',
                    'engagement_duration' => $mockPayload['estimated_duration'] ?? '-',
                ],
                'financial' => [
                    'deal_value' => (int) round((float) ($mockPayload['total_estimated_engagement_value'] ?? 0)),
                    'pricing_model' => $mockPayload['engagement_type'] ?? '-',
                    'payment_terms' => $mockPayload['payment_terms'] ?? '-',
                    'commission_applicable' => $mockPayload['support_required'] ?? '-',
                ],
                'referral' => [
                    'lead_source' => $mockPayload['lead_source'] ?? '-',
                    'referred_by' => $mockPayload['referred_by'] ?? '-',
                    'referral_type' => $mockPayload['referral_type'] ?? '-',
                ],
                'ownership' => [
                    'lead_consultant' => $mockPayload['assigned_consultant'] ?? '-',
                    'lead_associate' => $mockPayload['assigned_associate'] ?? '-',
                    'handling_team' => $mockPayload['service_department_unit'] ?? '-',
                    'assigned_members' => array_values(array_filter([
                        $mockPayload['assigned_consultant'] ?? null,
                        $mockPayload['assigned_associate'] ?? null,
                    ])),
                ],
                'progress' => [
                    'stages' => ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'],
                    'current_stage' => $deal['stage'],
                ],
                'timeline' => [
                    [
                        'icon' => 'fa-file-circle-plus',
                        'title' => 'Mock deal created',
                        'timestamp' => now()->format('Y-m-d, h:i A'),
                        'user' => $deal['owner_name'],
                    ],
                ],
                'stage_history' => [
                    [
                        'stage' => $deal['stage'],
                        'amount' => $deal['amount'],
                        'duration' => $mockPayload['estimated_duration'] ?? '-',
                        'modified_by' => $deal['owner_name'],
                        'date' => now()->format('M d, Y h:i A'),
                    ],
                ],
            ];

            return view('deals.show', [
                'deal' => $deal,
                'detail' => $detail,
                'dealFormData' => $this->normalizeDealFormData($mockPayload),
                ...$this->dealPanelContext($this->normalizeDealFormData($mockPayload)),
                'openDealModal' => (bool) request()->boolean('edit_deal'),
            ]);
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
            'dealFormData' => $this->normalizeDealFormData([
                ...$deal,
                'contact_id' => 101,
                'email' => $detail['email_address'] ?? null,
                'mobile' => $detail['contact_number'] ?? null,
                'service_area' => $detail['industry'] ?? null,
                'services' => data_get($detail, 'service.service_type'),
                'products' => data_get($detail, 'service.product_type'),
                'engagement_type' => data_get($detail, 'service.engagement_type'),
                'estimated_duration' => data_get($detail, 'service.engagement_duration'),
                'payment_terms' => data_get($detail, 'financial.payment_terms'),
                'assigned_consultant' => data_get($detail, 'ownership.lead_consultant'),
                'assigned_associate' => data_get($detail, 'ownership.lead_associate'),
                'service_department_unit' => data_get($detail, 'ownership.handling_team'),
                'referred_by' => data_get($detail, 'referral.referred_by'),
            ]),
            ...$this->dealPanelContext($this->normalizeDealFormData([
                ...$deal,
                'contact_id' => 101,
                'email' => $detail['email_address'] ?? null,
                'mobile' => $detail['contact_number'] ?? null,
            ])),
            'openDealModal' => (bool) request()->boolean('edit_deal'),
        ]);
    }

    public function download(int $id): View
    {
        $deal = collect($this->mockDeals())->firstWhere('id', $id);
        $dealFormData = $this->normalizeDealFormData($deal ?: []);

        if (Schema::hasTable('deals')) {
            $storedDeal = Deal::query()->find($id);
            if ($storedDeal) {
                $deal = [
                    'id' => $storedDeal->id,
                    'deal_name' => $storedDeal->deal_name,
                ];
                $dealFormData = $this->normalizeDealFormData($storedDeal->toArray());
            }
        }

        return view('deals.pdf', [
            'deal' => $deal ?? ['id' => $id, 'deal_name' => 'Deal Form'],
            'dealFormData' => $dealFormData,
            'downloadMode' => true,
        ]);
    }

    private function dealStages(): array
    {
        $defaults = [
            ['id' => null, 'name' => 'Inquiry', 'order' => 1, 'color' => '#2563eb'],
            ['id' => null, 'name' => 'Qualification', 'order' => 2, 'color' => '#4f46e5'],
            ['id' => null, 'name' => 'Consultation', 'order' => 3, 'color' => '#0891b2'],
            ['id' => null, 'name' => 'Proposal', 'order' => 4, 'color' => '#d97706'],
            ['id' => null, 'name' => 'Negotiation', 'order' => 5, 'color' => '#ea580c'],
            ['id' => null, 'name' => 'Payment', 'order' => 6, 'color' => '#059669'],
            ['id' => null, 'name' => 'Activation', 'order' => 7, 'color' => '#7c3aed'],
            ['id' => null, 'name' => 'Closed Lost', 'order' => 8, 'color' => '#dc2626'],
        ];

        if (! Schema::hasTable('deal_stages')) {
            return $defaults;
        }

        $stages = DealStage::query()
            ->orderBy('order')
            ->get(['id', 'name', 'order', 'color'])
            ->map(fn (DealStage $stage): array => [
                'id' => $stage->id,
                'name' => $stage->name,
                'order' => (int) $stage->order,
                'color' => $stage->color,
            ])
            ->all();

        if ($stages !== []) {
            return $stages;
        }

        foreach ($defaults as $stage) {
            DealStage::query()->create([
                'name' => $stage['name'],
                'order' => $stage['order'],
                'color' => $stage['color'],
            ]);
        }

        return DealStage::query()
            ->orderBy('order')
            ->get(['id', 'name', 'order', 'color'])
            ->map(fn (DealStage $stage): array => [
                'id' => $stage->id,
                'name' => $stage->name,
                'order' => (int) $stage->order,
                'color' => $stage->color,
            ])
            ->all();
    }

    private function mockDeals(): array
    {
        return [
            [
                'id' => 501,
                'deal_code' => 'DL-2026-001',
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
                'deal_code' => 'DL-2026-002',
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
                'deal_code' => 'RJ-2026-001',
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
                'deal_code' => 'RJ-2026-002',
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
                'deal_code' => 'RJ-2026-003',
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
                'deal_code' => 'RJ-2026-004',
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
                'deal_code' => 'RJ-2026-005',
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
                'deal_code' => 'MB-2026-001',
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
                'deal_code' => 'SW-2026-001',
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

    private function dealPanelContext(array $draft = []): array
    {
        $owners = $this->ownerOptions();
        $defaultOwnerId = (int) ($owners[0]['id'] ?? 1001);
        $defaultOwner = collect($owners)->firstWhere('id', $defaultOwnerId) ?: collect($owners)->first();
        $contactRecords = [$this->mockContactRecord()];
        $contactOptions = ['David Lee'];
        $companyOptions = ['Consulting Group'];

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

            $contacts = Contact::query()->select($contactColumns)->orderBy('first_name')->orderBy('last_name')->get();
            $contactRecords = $contacts->map(function (Contact $contact): array {
                return [
                    'id' => $contact->id,
                    'label' => trim(collect([$contact->salutation, $contact->first_name, $contact->middle_name, $contact->last_name])->filter()->implode(' ')),
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
            })->filter(fn (array $record): bool => $record['label'] !== '' || filled($record['company_name']))->values()->all();

            if (! collect($contactRecords)->contains(fn (array $record) => (int) $record['id'] === 101)) {
                array_unshift($contactRecords, $this->mockContactRecord());
            }

            $contactOptions = $contacts->map(fn (Contact $contact): string => trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')))->filter()->unique()->values()->all();
            $companyOptions = array_values(array_unique(array_merge(['Consulting Group'], $contacts->pluck('company_name')->filter()->values()->all())));
        }

        return [
            'stageOptions' => ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'],
            'companyOptions' => $companyOptions,
            'contactOptions' => $contactOptions,
            'contactRecords' => $contactRecords,
            'productOptions' => [],
            'ownerLabel' => $defaultOwner['name'] ?? 'Shine Florence Padillo',
            'owners' => $owners,
            'defaultOwnerId' => $defaultOwnerId,
            'dealDraft' => $draft,
        ];
    }

    private function normalizeDealFormData(array $payload): array
    {
        $normalized = $payload;

        $normalized['service_area_options'] = $this->normalizeListValue($payload['service_area_options'] ?? ($payload['service_area'] ?? null));
        $normalized['service_options'] = $this->normalizeListValue($payload['service_options'] ?? ($payload['services'] ?? null));
        $normalized['product_options'] = $this->normalizeListValue($payload['product_options'] ?? ($payload['products'] ?? null));
        $normalized['required_actions_options'] = $this->normalizeListValue($payload['required_actions_options'] ?? ($payload['required_actions'] ?? null));
        $normalized['support_required_options'] = $this->normalizeListValue($payload['support_required_options'] ?? ($payload['support_required'] ?? null));
        $normalized['requirements_status_map'] = $this->normalizeRequirementStatusMap($payload['requirements_status_map'] ?? ($payload['requirements_status'] ?? null));

        foreach ([
            'estimated_professional_fee',
            'estimated_government_fees',
            'estimated_service_support_fee',
            'total_estimated_engagement_value',
        ] as $moneyField) {
            if (filled($normalized[$moneyField] ?? null) && is_numeric(str_replace(',', '', (string) $normalized[$moneyField]))) {
                $normalized[$moneyField] = 'P'.number_format((float) str_replace(',', '', (string) $normalized[$moneyField]), 2);
            }
        }

        return $normalized;
    }

    private function normalizeListValue(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)->map(fn ($item) => trim((string) $item))->filter()->values()->all();
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeRequirementStatusMap(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->mapWithKeys(fn ($status, $key) => [(string) $key => strtolower((string) $status)])
                ->filter(fn ($status) => in_array($status, ['provided', 'pending'], true))
                ->all();
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $parsed = [];
        foreach (explode(';', $value) as $pair) {
            $parts = explode(':', $pair, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $key = str_replace(' ', '_', trim(strtolower($parts[0])));
            $status = trim(strtolower($parts[1]));
            if (in_array($status, ['provided', 'pending'], true)) {
                $parsed[$key] = $status;
            }
        }

        return $parsed;
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
            'deal_code' => $this->generateDealCode($contact),
            'deal_name' => $validated['deal_name'],
            'contact_name' => $contactName !== '' ? $contactName : 'Linked Contact',
            'company_name' => $validated['company_name'] ?? $contact->company_name ?? '-',
            'amount' => (int) round((float) $amount),
            'expected_close' => filled($validated['estimated_completion_date'] ?? null)
                ? Carbon::parse($validated['estimated_completion_date'])->format('M d, Y')
                : 'TBD',
            'owner_name' => $validated['assigned_consultant'] ?? 'Unassigned',
            'stage' => $validated['stage'] ?? 'Inquiry',
            'created_by' => optional(Auth::user())->name ?: 'System',
            'created_at_label' => now()->format('F d, Y • h:i:s A'),
        ];
    }

    private function generateDealCode(?Contact $contact, ?int $existingDealId = null): string
    {
        $initials = $this->dealCodeInitials(
            (string) ($contact?->first_name ?? ''),
            (string) ($contact?->last_name ?? '')
        );
        $year = now()->format('Y');
        $prefix = "{$initials}-{$year}-";

        if (Schema::hasTable('deals') && Schema::hasColumn('deals', 'deal_code')) {
            $existingCodes = Deal::query()
                ->when($existingDealId, fn ($query) => $query->where('id', '!=', $existingDealId))
                ->where('deal_code', 'like', "{$prefix}%")
                ->pluck('deal_code');

            $maxSequence = $existingCodes
                ->map(fn (string $code): int => (int) Str::afterLast($code, '-'))
                ->max() ?? 0;

            return $prefix.str_pad((string) ($maxSequence + 1), 3, '0', STR_PAD_LEFT);
        }

        return $prefix.str_pad((string) ($existingDealId ?: random_int(1, 999)), 3, '0', STR_PAD_LEFT);
    }

    private function generateDealCodeFromNames(string $firstName, string $lastName, int $seed): string
    {
        $initials = $this->dealCodeInitials($firstName, $lastName);

        return "{$initials}-".now()->format('Y').'-'.str_pad((string) $seed, 3, '0', STR_PAD_LEFT);
    }

    private function dealCodeInitials(string $firstName, string $lastName): string
    {
        $letters = strtoupper(mb_substr(trim($firstName), 0, 1).mb_substr(trim($lastName), 0, 1));

        return str_pad($letters !== '' ? $letters : 'DL', 2, 'X');
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
