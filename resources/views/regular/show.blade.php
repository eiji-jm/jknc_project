@extends('layouts.app')

@section('content')
@php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '-';
    $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' ')) ?: '-';
    $rsatRequirements = collect($rsat?->engagement_requirements ?? [])->whenEmpty(fn () => collect([['number' => 1, 'requirement' => '', 'notes' => '', 'purpose' => '', 'provided_by' => '', 'submitted_to' => '', 'assigned_to' => '', 'timeline' => '']]));
    $rsatApprovalSteps = collect($rsat?->approval_steps ?? [])->whenEmpty(fn () => collect([
        ['requirement' => 'RSAT Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
        ['requirement' => 'Client Confirmation', 'responsible_person' => 'Sales & Marketing / Consultant', 'name_and_signature' => '', 'date_time_done' => ''],
        ['requirement' => 'Execution Readiness', 'responsible_person' => 'Assigned Team', 'name_and_signature' => '', 'date_time_done' => ''],
    ]));
    $rsatClearance = (array) ($rsat?->clearance ?? []);
    $formDate = old('form_date', optional($rsat?->form_date ?? $rsat?->created_at)->format('Y-m-d'));
    $dateStarted = old('date_started', optional($rsat?->date_started)->format('Y-m-d'));
    $dateCompleted = old('date_completed', optional($rsat?->date_completed)->format('Y-m-d'));
@endphp

<style>
    .project-workspace { background: linear-gradient(180deg, #edf3fb 0%, #f7f6f2 24%, #f7f6f2 100%); }
    .project-top-card { border: 1px solid #d9e2ef; background: rgba(255,255,255,.92); box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05); }
    .project-tab-link { display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d4deeb; background: #102d79; padding: 10px 16px; font-size: .85rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #fff; box-shadow: 0 10px 20px rgba(16, 45, 121, 0.16); }
    .project-linked-card { border: 1px solid #dbe3f0; background: #fff; }
    .project-doc-shell { border: 1px solid #cbd5e1; background: #fff; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06); }
</style>

<div class="project-workspace p-6">
    <div class="mx-auto max-w-[1320px] space-y-4">
        <div class="project-top-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <a href="{{ route('regular.index') }}" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Regular</a>
            <span class="mx-1">/</span><span class="font-medium text-gray-900">{{ $regular->project_code }}</span>
        </div>

        <div class="project-top-card rounded-2xl px-5 py-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Regular Annex Forms</p>
                    <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ $regular->name }}</h1>
                    <p class="mt-2 text-sm text-gray-500">{{ $regular->project_code }} - {{ $regular->deal?->deal_code ?? 'No linked deal code' }}</p>
                </div>
                <div class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                    <div><span class="text-gray-400">Business:</span> {{ $regular->business_name ?: '-' }}</div>
                    <div><span class="text-gray-400">Client:</span> {{ $contactName }}</div>
                    <div><span class="text-gray-400">Planned Start:</span> {{ $fmt($regular->planned_start_date) }}</div>
                    <div><span class="text-gray-400">Target Completion:</span> {{ $fmt($regular->target_completion_date) }}</div>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-2">
                <span class="project-tab-link">RSAT Form</span>
                <a href="{{ route('regular.rsat.download', $regular) }}" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Download RSAT PDF</a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="project-linked-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <div class="flex flex-wrap gap-x-8 gap-y-2">
                <p>Deal: <a href="{{ route('deals.show', $regular->deal_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $regular->deal?->deal_code ?? 'View linked deal' }}</a></p>
                @if ($regular->company_id)
                    <p>Company: <a href="{{ route('company.show', $regular->company_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $regular->company?->company_name ?? 'View company' }}</a></p>
                @endif
                @if ($regular->contact_id)
                    <p>Contact: <a href="{{ route('contacts.show', $regular->contact_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $contactName }}</a></p>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('regular.rsat.update', $regular) }}" class="project-doc-shell overflow-hidden bg-white p-6">
            @csrf
            <input type="hidden" name="status" value="{{ old('status', $rsat?->status ?? 'pending') }}">
            <input type="hidden" name="form_date" value="{{ $formDate }}">

            <div class="border-[3px] border-[#163b7a] bg-white">
                <div class="grid gap-6 border-b border-slate-300 p-6 lg:grid-cols-[180px_1fr]">
                    <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly and Company" class="h-20 w-auto object-contain">
                    <div class="text-right font-['Times_New_Roman',Georgia,serif]">
                        <div class="text-3xl font-bold leading-tight text-slate-900">REGULAR SERVICE</div>
                        <div class="text-3xl font-bold leading-tight text-slate-900">ACTIVITY TRACKER (RSAT)</div>
                        <div class="mt-2 text-xs text-slate-500">[ Form Code ]</div>
                    </div>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-2 xl:grid-cols-3">
                    <label class="text-sm">Client Name<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $contactName }}" readonly></label>
                    <label class="text-sm">Date Created<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $formDate ? \Illuminate\Support\Carbon::parse($formDate)->format('m/d/Y') : '' }}" readonly></label>
                    <label class="text-sm">Condeal Ref No.<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->deal?->deal_code }}" readonly></label>
                    <label class="text-sm">Engagement Type<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->engagement_type }}" readonly></label>
                    <label class="text-sm">Business Name<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->business_name }}" readonly></label>
                    <label class="text-sm">Services<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->services }}" readonly></label>
                    <label class="text-sm">Service Area<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->service_area }}" readonly></label>
                    <label class="text-sm">Product<input class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2" value="{{ $regular->products }}" readonly></label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-sm">Date Started<input type="date" name="date_started" value="{{ $dateStarted }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2"></label>
                        <label class="text-sm">Date Completed<input type="date" name="date_completed" value="{{ $dateCompleted }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-2"></label>
                    </div>
                </div>

                <div class="overflow-x-auto border-t border-slate-900">
                    <table class="min-w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#163b7a] text-white">
                                <th class="border border-slate-900 px-2 py-2">ITEM #</th>
                                <th class="border border-slate-900 px-2 py-2">SERVICE</th>
                                <th class="border border-slate-900 px-2 py-2">ACTIVITY / OUTPUT</th>
                                <th class="border border-slate-900 px-2 py-2">FREQUENCY</th>
                                <th class="border border-slate-900 px-2 py-2">REMINDER LEAD TIME</th>
                                <th class="border border-slate-900 px-2 py-2">DEADLINE</th>
                            </tr>
                        </thead>
                        <tbody id="regular-requirements">
                            @foreach ($rsatRequirements as $index => $item)
                                <tr>
                                    <td class="border border-slate-900 px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-900 px-1"><input name="engagement_purpose[]" value="{{ old('engagement_purpose.'.$index, $item['purpose'] ?? '') }}" class="w-full border-0 px-1 py-1"></td>
                                    <td class="border border-slate-900 px-1"><input name="engagement_requirement[]" value="{{ old('engagement_requirement.'.$index, $item['requirement'] ?? '') }}" class="w-full border-0 px-1 py-1"></td>
                                    <td class="border border-slate-900 px-1"><input name="engagement_notes[]" value="{{ old('engagement_notes.'.$index, $item['notes'] ?? '') }}" class="w-full border-0 px-1 py-1"></td>
                                    <td class="border border-slate-900 px-1"><input name="engagement_timeline[]" value="{{ old('engagement_timeline.'.$index, $item['timeline'] ?? '') }}" class="w-full border-0 px-1 py-1"></td>
                                    <td class="border border-slate-900 px-1"><input name="engagement_submitted_to[]" value="{{ old('engagement_submitted_to.'.$index, $item['submitted_to'] ?? '') }}" class="w-full border-0 px-1 py-1"></td>
                                </tr>
                                <input type="hidden" name="engagement_provided_by[]" value="{{ old('engagement_provided_by.'.$index, $item['provided_by'] ?? '') }}">
                                <input type="hidden" name="engagement_assigned_to[]" value="{{ old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '') }}">
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-900">
                    <div class="bg-[#163b7a] px-4 py-2 text-center font-['Times_New_Roman',Georgia,serif] text-xl font-bold text-white">INTERNAL APPROVAL</div>
                    <div class="grid gap-4 p-6 lg:grid-cols-2">
                        @foreach ($rsatApprovalSteps as $index => $item)
                            <div class="grid gap-3 md:grid-cols-2">
                                <input name="approval_requirement[]" value="{{ old('approval_requirement.'.$index, $item['requirement'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Requirement">
                                <input name="approval_responsible_person[]" value="{{ old('approval_responsible_person.'.$index, $item['responsible_person'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Responsible Person">
                                <input name="approval_name_and_signature[]" value="{{ old('approval_name_and_signature.'.$index, $item['name_and_signature'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Name / Signature">
                                <input name="approval_date_time_done[]" value="{{ old('approval_date_time_done.'.$index, $item['date_time_done'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Date">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-4 border-t border-slate-900 p-6 md:grid-cols-2 xl:grid-cols-4">
                    <input name="clearance_assigned_team_lead" value="{{ old('clearance_assigned_team_lead', $rsatClearance['assigned_team_lead'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Prepared By">
                    <input name="clearance_lead_consultant_confirmed" value="{{ old('clearance_lead_consultant_confirmed', $rsatClearance['lead_consultant_confirmed'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Reviewed By">
                    <input name="clearance_lead_associate_assigned" value="{{ old('clearance_lead_associate_assigned', $rsatClearance['lead_associate_assigned'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Lead Associate Assigned">
                    <input name="clearance_sales_marketing" value="{{ old('clearance_sales_marketing', $rsatClearance['sales_marketing'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Sales & Marketing">
                    <input name="clearance_record_custodian_name" value="{{ old('clearance_record_custodian_name', $rsatClearance['record_custodian_name'] ?? '') }}" class="border-b border-slate-900 px-1 py-2" placeholder="Record Custodian">
                    <input type="date" name="clearance_date_recorded" value="{{ old('clearance_date_recorded', $rsatClearance['date_recorded'] ?? '') }}" class="border-b border-slate-900 px-1 py-2">
                    <input type="date" name="clearance_date_signed" value="{{ old('clearance_date_signed', $rsatClearance['date_signed'] ?? '') }}" class="border-b border-slate-900 px-1 py-2">
                    <input name="rejection_reason" value="{{ old('rejection_reason', $rsat?->rejection_reason) }}" class="border-b border-slate-900 px-1 py-2" placeholder="Notes / Reason">
                    <input type="hidden" name="clearance_assigned_team_lead_signature" value="{{ old('clearance_assigned_team_lead_signature', $rsatClearance['assigned_team_lead_signature'] ?? '') }}">
                    <input type="hidden" name="clearance_lead_consultant_signature" value="{{ old('clearance_lead_consultant_signature', $rsatClearance['lead_consultant_signature'] ?? '') }}">
                    <input type="hidden" name="clearance_lead_associate_signature" value="{{ old('clearance_lead_associate_signature', $rsatClearance['lead_associate_signature'] ?? '') }}">
                    <input type="hidden" name="clearance_sales_marketing_signature" value="{{ old('clearance_sales_marketing_signature', $rsatClearance['sales_marketing_signature'] ?? '') }}">
                    <input type="hidden" name="clearance_record_custodian_signature" value="{{ old('clearance_record_custodian_signature', $rsatClearance['record_custodian_signature'] ?? '') }}">
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <button type="button" class="inline-flex items-center border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700" data-add-row="regular-requirements">Add RSAT Row</button>
                <button type="submit" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Save RSAT</button>
            </div>
        </form>
    </div>
</div>

<template id="regular-requirement-row-template"><tr><td class="border border-slate-900 px-2 py-1 text-center"></td><td class="border border-slate-900 px-1"><input name="engagement_purpose[]" class="w-full border-0 px-1 py-1"></td><td class="border border-slate-900 px-1"><input name="engagement_requirement[]" class="w-full border-0 px-1 py-1"></td><td class="border border-slate-900 px-1"><input name="engagement_notes[]" class="w-full border-0 px-1 py-1"></td><td class="border border-slate-900 px-1"><input name="engagement_timeline[]" class="w-full border-0 px-1 py-1"></td><td class="border border-slate-900 px-1"><input name="engagement_submitted_to[]" class="w-full border-0 px-1 py-1"></td></tr></template>
<script>
document.querySelectorAll('[data-add-row]').forEach((button) => button.addEventListener('click', () => {
    document.getElementById(button.dataset.addRow).insertAdjacentHTML('beforeend', document.getElementById('regular-requirement-row-template').innerHTML);
}));
</script>
@endsection
