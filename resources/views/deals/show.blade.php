@extends('layouts.app')

@section('content')
@php
    $formatCurrency = static fn ($amount): string => 'P'.number_format((float) $amount, 2);
    $stageBadgeClasses = [
        'Inquiry' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'Qualification' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'Consultation' => 'bg-cyan-100 text-cyan-700 border border-cyan-200',
        'Proposal' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Negotiation' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'Payment' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'Activation' => 'bg-violet-100 text-violet-700 border border-violet-200',
        'Closed Lost' => 'bg-red-100 text-red-700 border border-red-200',
    ];
    $dealStatusClasses = [
        'Open' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'Won' => 'bg-green-100 text-green-700 border border-green-200',
        'Lost' => 'bg-red-100 text-red-700 border border-red-200',
        'Pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
    ];
    $initials = strtoupper(substr((string) ($deal['contact_name'] ?? 'C'), 0, 1).substr(strrchr(' '.($deal['contact_name'] ?? 'C'), ' '), 1, 1));
@endphp

<div class="bg-[#f7f6f2] p-6">
    <div class="mx-auto max-w-[1500px] space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
            <a href="{{ route('deals.index') }}" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Deals</a>
            <span class="mx-1">/</span>
            <span class="font-medium text-gray-900">{{ $deal['deal_code'] ?? 'DEAL' }}</span>
        </div>

        <div class="flex flex-wrap items-start justify-between gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
            <div>
                <p class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-2 py-0.5 text-[11px] font-semibold tracking-wide text-blue-700">{{ $deal['deal_code'] ?? 'DEAL' }}</p>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $deal['deal_name'] }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $stageBadgeClasses[$deal['stage']] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">{{ $deal['stage'] }}</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $formatCurrency($deal['amount'] ?? 0) }}</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button id="openCreateDealModalBtn" type="button" class="h-9 rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="far fa-pen-to-square mr-1"></i>Edit Deal
                </button>
                <button type="button" class="h-9 rounded-lg bg-blue-700 px-3 text-sm font-medium text-white hover:bg-blue-800">
                    <i class="fas fa-arrow-up-right-dots mr-1"></i>Update Stage
                </button>
                <a href="{{ route('deals.download', $deal['id']) }}" target="_blank" class="flex h-9 items-center rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-pdf mr-1"></i>Download PDF
                </a>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.85fr_0.85fr]">
            <div class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Information</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Deal Name</p><p class="font-medium text-gray-800">{{ $deal['deal_name'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Company Name</p><p class="font-medium text-gray-800">{{ $detail['related_company'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Contact Person Name</p><p class="font-medium text-gray-800">{{ $detail['contact_person_name'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Contact Person Position</p><p class="font-medium text-gray-800">{{ $detail['contact_person_position'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Email Address</p><p class="font-medium text-gray-800">{{ $detail['email_address'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Contact Number</p><p class="font-medium text-gray-800">{{ $detail['contact_number'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Client Type</p><p class="font-medium text-gray-800">{{ $detail['client_type'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Industry</p><p class="font-medium text-gray-800">{{ $detail['industry'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Deal Stage</p><p class="font-medium text-gray-800">{{ $detail['deal_stage'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Expected Close Date</p><p class="font-medium text-gray-800">{{ $detail['expected_close_date'] ?? '-' }}</p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Service and Engagement Details</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Service Type</p><p class="font-medium text-gray-800">{{ data_get($detail, 'service.service_type', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Product Type</p><p class="font-medium text-gray-800">{{ data_get($detail, 'service.product_type', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Engagement Type</p><p class="font-medium text-gray-800">{{ data_get($detail, 'service.engagement_type', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Project Name (if applicable)</p><p class="font-medium text-gray-800">{{ $dealFormData['deal_name'] ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Engagement Duration</p><p class="font-medium text-gray-800">{{ data_get($detail, 'service.engagement_duration', '-') }}</p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Financial Details</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Deal Value</p><p class="font-medium text-gray-800">{{ $formatCurrency(data_get($detail, 'financial.deal_value', 0)) }}</p></div>
                        <div><p class="text-xs text-gray-500">Pricing Model</p><p class="font-medium text-gray-800">{{ data_get($detail, 'financial.pricing_model', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Payment Terms</p><p class="font-medium text-gray-800">{{ data_get($detail, 'financial.payment_terms', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Commission Applicable</p><p class="font-medium text-gray-800">{{ data_get($detail, 'financial.commission_applicable', '-') }}</p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Referral and Lead Source</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Lead Source</p><p class="font-medium text-gray-800">{{ data_get($detail, 'referral.lead_source', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Referred By</p><p class="font-medium text-gray-800">{{ data_get($detail, 'referral.referred_by', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Referral Type</p><p class="font-medium text-gray-800">{{ data_get($detail, 'referral.referral_type', '-') }}</p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Ownership and Team Assignment</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Lead Consultant</p><p class="font-medium text-gray-800">{{ data_get($detail, 'ownership.lead_consultant', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Lead Associate</p><p class="font-medium text-gray-800">{{ data_get($detail, 'ownership.lead_associate', '-') }}</p></div>
                        <div><p class="text-xs text-gray-500">Handling Team</p><p class="font-medium text-gray-800">{{ data_get($detail, 'ownership.handling_team', '-') }}</p></div>
                        <div>
                            <p class="text-xs text-gray-500">Assigned Team Members</p>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                @forelse ((array) data_get($detail, 'ownership.assigned_members', []) as $member)
                                    <span class="rounded-full border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $member }}</span>
                                @empty
                                    <span class="text-sm text-gray-500">-</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Deal Form Preview</h2>
                    @include('deals.partials.deal-form-document', ['dealFormData' => $dealFormData])
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Stage Progress</h2>
                    <div class="grid grid-cols-2 gap-4 text-center sm:grid-cols-4 lg:grid-cols-8">
                        @foreach (['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'] as $progressStage)
                            @php
                                $isCurrent = $progressStage === data_get($detail, 'progress.current_stage');
                                $circleClass = $isCurrent ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 text-gray-500 border-gray-200';
                            @endphp
                            <div>
                                <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full border text-xs {{ $circleClass }}">
                                    @if ($isCurrent)
                                        <i class="fas fa-check"></i>
                                    @endif
                                </span>
                                <p class="mt-2 text-xs text-gray-600">{{ $progressStage }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100">
                        <div class="flex flex-wrap gap-1 p-2">
                            @foreach (['timeline' => 'Timeline', 'notes' => 'Notes', 'activities' => 'Activities', 'emails' => 'Emails', 'stage-history' => 'Stage History', 'files' => 'Files', 'products' => 'Products'] as $tabKey => $label)
                                <button type="button" data-tab-button="{{ $tabKey }}" class="deal-tab-btn rounded-lg px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 {{ $tabKey === 'timeline' ? 'bg-blue-50 text-blue-700' : '' }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-4 text-sm">
                        <div data-tab-panel="timeline" class="deal-tab-panel space-y-3">
                            @foreach ((array) data_get($detail, 'timeline', []) as $entry)
                                <div class="flex items-start gap-3 rounded-lg border border-gray-100 p-3">
                                    <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                        <i class="fas {{ $entry['icon'] ?? 'fa-clock' }} text-xs"></i>
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $entry['title'] ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $entry['timestamp'] ?? '-' }} - {{ $entry['user'] ?? '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div data-tab-panel="notes" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No notes added yet</div>
                        <div data-tab-panel="activities" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No activities added yet</div>
                        <div data-tab-panel="emails" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No emails found for this deal</div>

                        <div data-tab-panel="stage-history" class="deal-tab-panel hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Stage</th>
                                            <th class="px-3 py-2 text-left">Amount</th>
                                            <th class="px-3 py-2 text-left">Stage Duration</th>
                                            <th class="px-3 py-2 text-left">Modified By</th>
                                            <th class="px-3 py-2 text-left">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ((array) data_get($detail, 'stage_history', []) as $history)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-800">{{ $history['stage'] ?? '-' }}</td>
                                                <td class="px-3 py-2 font-medium text-blue-700">{{ $formatCurrency($history['amount'] ?? 0) }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $history['duration'] ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $history['modified_by'] ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $history['date'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div data-tab-panel="files" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No files uploaded yet.</div>
                        <div data-tab-panel="products" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No associated products yet.</div>
                    </div>
                </article>
            </div>

            <aside class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-3 text-base font-semibold text-gray-900">Related Contact</h3>
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">{{ $initials }}</span>
                        <div>
                            <p class="font-medium text-gray-900">{{ $deal['contact_name'] ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $deal['company_name'] ?? '-' }}</p>
                            <p class="mt-2 text-xs text-gray-600"><i class="far fa-envelope mr-1"></i>{{ $detail['email_address'] ?? '-' }}</p>
                            <p class="mt-1 text-xs text-gray-600"><i class="fas fa-phone mr-1"></i>{{ $detail['contact_number'] ?? '-' }}</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-2 text-base font-semibold text-gray-900">Tags</h3>
                    <button type="button" class="text-sm font-medium text-blue-600 hover:text-blue-700"><i class="fas fa-plus mr-1"></i>Add Tag</button>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-2 text-base font-semibold text-gray-900">Actions</h3>
                    <div class="space-y-2">
                        <button id="openCreateDealModalBtnSecondary" type="button" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Edit Deal</button>
                        <button type="button" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Update Stage</button>
                    </div>
                    <div class="mt-3 border-t border-gray-100 pt-3">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $dealStatusClasses[$detail['deal_status'] ?? 'Open'] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                            {{ $detail['deal_status'] ?? 'Open' }}
                        </span>
                    </div>
                </article>
            </aside>
        </div>
    </div>
</div>

@include('deals.partials.create-deal-modal', [
    'stageOptions' => $stageOptions,
    'companyOptions' => $companyOptions,
    'contactOptions' => $contactOptions,
    'contactRecords' => $contactRecords,
    'productOptions' => $productOptions,
    'ownerLabel' => $ownerLabel,
    'owners' => $owners,
    'defaultOwnerId' => $defaultOwnerId,
    'dealDraft' => $dealFormData,
    'openDealModal' => $openDealModal ?? false,
    'formAction' => route('deals.update', $deal['id']),
    'formMethod' => 'PUT',
    'submitLabel' => 'Update Deal',
])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const secondary = document.getElementById('openCreateDealModalBtnSecondary');
    const primary = document.getElementById('openCreateDealModalBtn');
    secondary?.addEventListener('click', () => primary?.click());

    const buttons = Array.from(document.querySelectorAll('[data-tab-button]'));
    const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));
    const activate = (tabKey) => {
        buttons.forEach((button) => {
            const isActive = button.dataset.tabButton === tabKey;
            button.classList.toggle('bg-blue-50', isActive);
            button.classList.toggle('text-blue-700', isActive);
            button.classList.toggle('text-gray-600', !isActive);
        });
        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.tabPanel !== tabKey);
        });
    };
    buttons.forEach((button) => button.addEventListener('click', () => activate(button.dataset.tabButton)));
});
</script>
@endsection
