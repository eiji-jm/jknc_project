@extends('layouts.app')

@section('content')
@php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '-';
    $contactName = trim(collect([$project->contact?->first_name, $project->contact?->last_name])->filter()->implode(' ')) ?: ($project->client_name ?: '-');
    $tabs = ['start' => 'START Form', 'sow' => 'Scope of Work', 'report' => 'SOW Report'];
    $startChecklist = collect($start?->checklist ?? [])->whenEmpty(fn () => collect([
        ['label' => 'Client Contact Form', 'status' => 'pending'],
        ['label' => 'Deal Form', 'status' => 'pending'],
        ['label' => 'Business Information Form', 'status' => 'pending'],
        ['label' => 'Client Information Form', 'status' => 'pending'],
        ['label' => 'Service Task Activation & Routing Tracker (Start)', 'status' => 'pending'],
        ['label' => 'Others', 'status' => 'pending'],
    ]));
    $startReqs = collect($start?->engagement_requirements ?? [])->whenEmpty(fn () => collect([['requirement' => '', 'purpose' => '', 'assigned_to' => '', 'timeline' => '']]));
    $routing = collect($start?->routing ?? [])->whenEmpty(fn () => collect([
        ['role' => 'Admin', 'status' => 'pending'],
        ['role' => 'Lead Consultant', 'status' => 'pending'],
        ['role' => 'Lead Associate', 'status' => 'pending'],
        ['role' => 'Sales & Marketing', 'status' => 'pending'],
    ]));
    $sowWithin = collect($sow?->within_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $sowOut = collect($sow?->out_of_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $repWithin = collect($report?->within_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $repOut = collect($report?->out_of_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $sowApproval = (array) ($sow?->internal_approval ?? []);
    $repApproval = (array) ($report?->internal_approval ?? []);
    $repSummary = (array) ($report?->status_summary ?? []);
    $logoPath = asset('images/imaglogo.png');
@endphp

<style>
    .project-workspace { background: linear-gradient(180deg, #edf3fb 0%, #f7f6f2 24%, #f7f6f2 100%); }
    .project-top-card { border: 1px solid #d9e2ef; background: rgba(255,255,255,.92); box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05); }
    .project-tab-link { display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d4deeb; background: #fff; padding: 10px 16px; font-size: .85rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #475569; }
    .project-tab-link.active { border-color: #102d79; background: #102d79; color: #fff; box-shadow: 0 10px 20px rgba(16, 45, 121, 0.16); }
    .project-tab-link:hover { border-color: #93a4c4; color: #102d79; }
    .project-linked-card { border: 1px solid #dbe3f0; background: #fff; }
    .project-doc-shell { border: 1px solid #cbd5e1; background: #fff; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06); }
    .project-doc-topbar { height: 8px; background: #102d79; }
    .project-doc-header { display: grid; gap: 18px; padding: 18px 22px; border-bottom: 1px solid #dbe3f0; background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%); }
    .project-doc-brand { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
    .project-doc-brand img { height: 46px; width: auto; object-fit: contain; }
    .project-doc-title { text-align: right; font-family: "Times New Roman", Georgia, serif; }
    .project-doc-title h2 { font-size: 2rem; line-height: 1.05; font-weight: 700; color: #0f172a; text-transform: uppercase; }
    .project-doc-title p { margin-top: 4px; font-size: 0.74rem; letter-spacing: 0.14em; text-transform: uppercase; color: #64748b; font-family: Arial, sans-serif; }
    .project-doc-grid { display: grid; gap: 12px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .project-doc-meta { border: 1px solid #dbe3f0; background: #fff; padding: 8px 10px; min-height: 62px; }
    .project-doc-meta-label { display: block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
    .project-doc-meta-value { display: block; margin-top: 8px; font-size: .98rem; font-weight: 600; color: #0f172a; }
    .project-doc-section { margin: 18px 24px 0; border: 1px solid #dbe3f0; background: #fff; }
    .project-doc-section-title { background: #102d79; color: #fff; padding: 10px 14px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
    .project-doc-section-body { padding: 18px; background: #fff; }
    .project-doc-table { min-width: 1100px; border-collapse: collapse; }
    .project-doc-table thead th { background: #eef4ff; color: #334155; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 700; }
    .project-doc-table th, .project-doc-table td { border: 1px solid #dbe3f0; padding: 8px; vertical-align: top; }
    .project-doc-input, .project-doc-select, .project-doc-textarea { width: 100%; border: 1px solid #cbd5e1; background: #fff; padding: 9px 11px; font-size: 0.9rem; color: #0f172a; }
    .project-doc-input[readonly] { background: #f8fafc; color: #475569; }
    .project-doc-textarea { min-height: 96px; resize: vertical; }
    .project-doc-label { display: block; margin-bottom: 6px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #475569; }
    .project-doc-total { margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: #475569; text-transform: uppercase; }
    .project-doc-action { display: inline-flex; align-items: center; border: 1px solid #cbd5e1; background: #fff; padding: 9px 12px; font-size: 0.82rem; font-weight: 600; color: #334155; }
    .project-doc-primary { display: inline-flex; align-items: center; background: #21409a; color: #fff; padding: 10px 14px; font-size: 0.85rem; font-weight: 600; }
    .project-doc-summary-grid { display: grid; gap: 12px; grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .project-doc-summary-box { border: 1px solid #dbe3f0; background: #f8fbff; padding: 12px; }
    .project-doc-summary-box span { display: block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
    .project-doc-summary-box strong { display: block; margin-top: 8px; font-size: 1.4rem; color: #0f172a; }
    @media (max-width: 1280px) {
        .project-doc-grid, .project-doc-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
        .project-doc-grid, .project-doc-summary-grid { grid-template-columns: minmax(0, 1fr); }
        .project-doc-brand { flex-direction: column; }
        .project-doc-title { text-align: left; }
    }
</style>

<div class="project-workspace p-6">
    <div class="mx-auto max-w-[1320px] space-y-4">
        <div class="project-top-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <a href="{{ route('project.index') }}" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Project</a>
            <span class="mx-1">/</span><span class="font-medium text-gray-900">{{ $project->project_code }}</span>
        </div>
        <div class="project-top-card rounded-2xl px-5 py-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Project Annex Forms</p>
                <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ $project->project_code }} - {{ $project->deal?->deal_code ?? 'No linked deal code' }}</p>
            </div>
            <div class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                <div><span class="text-gray-400">Business:</span> {{ $project->business_name ?: '-' }}</div>
                <div><span class="text-gray-400">Client:</span> {{ $contactName }}</div>
                <div><span class="text-gray-400">Planned Start:</span> {{ $fmt($project->planned_start_date) }}</div>
                <div><span class="text-gray-400">Target Completion:</span> {{ $fmt($project->target_completion_date) }}</div>
            </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('project.show', ['project' => $project->id, 'tab' => $key]) }}" class="project-tab-link {{ $tab === $key ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        <div class="project-linked-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <div class="flex flex-wrap gap-x-8 gap-y-2">
                <p>Deal: <a href="{{ route('deals.show', $project->deal_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $project->deal?->deal_code ?? 'View linked deal' }}</a></p>
                @if ($project->company_id)
                    <p>Company: <a href="{{ route('company.show', $project->company_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $project->company?->company_name ?? 'View company' }}</a></p>
                @endif
                @if ($project->contact_id)
                    <p>Contact: <a href="{{ route('contacts.show', $project->contact_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $contactName }}</a></p>
                @endif
            </div>
        </div>
        <div class="space-y-4">
            @include('project.partials.tab-'.$tab, compact('project', 'start', 'sow', 'report', 'contactName', 'startChecklist', 'startReqs', 'routing', 'sowWithin', 'sowOut', 'repWithin', 'repOut', 'sowApproval', 'repApproval', 'repSummary'))
        </div>
    </div>
</div>

<template id="scope-row-template"><tr><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="main_task_description"></td><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="sub_task_description"></td><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="responsible"></td><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="duration"></td><td class="px-3 py-2"><input type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="start_date"></td><td class="px-3 py-2"><input type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="end_date"></td><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="status"></td><td class="px-3 py-2"><input class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-name="remarks"></td></tr></template>
<template id="start-requirement-row-template"><tr><td class="px-3 py-2"><input name="engagement_requirement[]" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></td><td class="px-3 py-2"><input name="engagement_purpose[]" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></td><td class="px-3 py-2"><input name="engagement_assigned_to[]" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></td><td class="px-3 py-2"><input name="engagement_timeline[]" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></td></tr></template>
<template id="start-routing-row-template"><div class="grid gap-3 md:grid-cols-2"><input name="routing_role[]" class="h-10 rounded-lg border border-gray-300 px-3 text-sm"><select name="routing_status[]" class="h-10 rounded-lg border border-gray-300 px-3 text-sm"><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></div></template>
<script>
document.querySelectorAll('[data-add-row]').forEach((b)=>b.addEventListener('click',()=>{const t=document.getElementById(b.dataset.addRow);const id=b.dataset.addRow==='start-routing'?'start-routing-row-template':'start-requirement-row-template';t.insertAdjacentHTML('beforeend',document.getElementById(id).innerHTML);}));
document.querySelectorAll('[data-add-scope-row]').forEach((b)=>b.addEventListener('click',()=>{const t=document.getElementById(b.dataset.addScopeRow),p=b.dataset.addScopeRow.startsWith('within')?'within':'out',f=document.getElementById('scope-row-template').content.cloneNode(true);f.querySelectorAll('[data-name]').forEach((i)=>i.name=p+'_'+i.dataset.name+'[]');t.appendChild(f);}));
</script>
@endsection
