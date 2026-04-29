@extends('layouts.app')

@section('content')
@php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '-';
    $contactName = trim(collect([$project->contact?->first_name, $project->contact?->last_name])->filter()->implode(' ')) ?: ($project->client_name ?: '-');
    $tabs = ['sow' => 'Scope of Work', 'report' => 'SOW Report'];
    $sowWithin = collect($sow?->within_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $sowOut = collect($sow?->out_of_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $repWithin = collect($report?->within_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $repOut = collect($report?->out_of_scope_items ?? [])->whenEmpty(fn () => collect([['main_task_description' => '', 'sub_task_description' => '', 'responsible' => '', 'duration' => '', 'start_date' => '', 'end_date' => '', 'status' => '', 'remarks' => '']]));
    $sowApproval = (array) ($sow?->internal_approval ?? []);
    $repApproval = (array) ($report?->internal_approval ?? []);
    $repSummary = (array) ($report?->status_summary ?? []);
    $logoPath = asset('images/imaglogo.png');
    $ntpApproved = $ntpRecord?->client_response_status === 'approved_to_proceed' && $ntpRecord?->client_approved_at;
    $ntpStatusLabel = $ntpApproved
        ? 'Client approved NTP'
        : (($ntpRecord?->client_form_sent_at) ? 'Waiting for client signed NTP upload' : 'NTP not generated');
@endphp

<style>
    .project-workspace {
        background:
            radial-gradient(circle at top left, rgba(13, 70, 140, 0.08), transparent 28%),
            linear-gradient(180deg, #f2f6fc 0%, #fbfcfe 26%, #fbfcfe 100%);
    }
    .project-top-card {
        border: 1px solid #d8e1ee;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.05);
    }
    .project-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        border: 1px solid #cfd9e7;
        background: #fff;
        padding: 10px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e3a5f;
    }
    .project-tab-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid #cfd9e7;
        background: #fff;
        padding: 10px 18px;
        font-size: 0.84rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #1e3a5f;
        transition: all 0.16s ease;
    }
    .project-tab-link.active {
        border-color: #1c4587;
        background: #1c4587;
        color: #fff;
        box-shadow: 0 10px 22px rgba(28, 69, 135, 0.18);
    }
    .project-tab-link:hover {
        border-color: #9eb2cf;
        color: #1c4587;
    }
    .project-linked-card {
        border: 1px solid #d8e1ee;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.04);
    }
    .project-linked-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
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
    .project-doc-action i { margin-right: 8px; }
    .project-doc-action-approved { border-color: #86efac; background: #dcfce7; color: #166534; }
    .project-doc-status-chip { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; border: 1px solid #dbe3f0; background: #fff; padding: 9px 14px; font-size: 0.78rem; font-weight: 700; color: #475569; }
    .project-doc-status-chip.approved { border-color: #86efac; background: #dcfce7; color: #166534; }
    .project-doc-primary { display: inline-flex; align-items: center; background: #21409a; color: #fff; padding: 10px 14px; font-size: 0.85rem; font-weight: 600; }
    .project-doc-summary-grid { display: grid; gap: 12px; grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .project-doc-summary-box { border: 1px solid #dbe3f0; background: #f8fbff; padding: 12px; }
    .project-doc-summary-box span { display: block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
    .project-doc-summary-box strong { display: block; margin-top: 8px; font-size: 1.4rem; color: #0f172a; }
    .project-ntp-modal { position: fixed; inset: 0; z-index: 70; display: none; }
    .project-ntp-modal.is-open { display: block; }
    .project-ntp-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.55); }
    .project-ntp-frame { position: absolute; inset: 0; overflow-y: auto; padding: 28px 16px; }
    .project-ntp-shell { position: relative; max-width: 1060px; margin: 0 auto; }
    .project-ntp-toolbar { display: flex; justify-content: flex-end; margin-bottom: 14px; }
    .project-ntp-close { display: inline-flex; align-items: center; justify-content: center; min-width: 120px; border: 1px solid #cbd5e1; background: #fff; padding: 10px 14px; font-size: .84rem; font-weight: 700; color: #0f172a; }
    .project-ntp-sheet { border: 1px solid #d8e1ee; background: #fff; box-shadow: 0 16px 34px rgba(15, 23, 42, 0.05); }
    .project-ntp-doc { padding: 32px 34px 36px; border: 2px solid #1c4587; }
    .project-ntp-title { font-family: Georgia, "Times New Roman", serif; font-size: 18pt; font-weight: 700; line-height: 1.05; }
    .project-ntp-code { margin-bottom: 24px; font-family: Georgia, "Times New Roman", serif; font-size: 8pt; font-weight: 700; }
    .project-ntp-issued { margin: 18px 0 12px; font-family: Georgia, "Times New Roman", serif; font-size: 12pt; font-weight: 700; }
    .project-ntp-light { font-weight: 400; }
    .project-ntp-meta, .project-ntp-signatures { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .project-ntp-meta td { border: 1px solid #000; padding: 8px 10px; vertical-align: top; font-family: Georgia, "Times New Roman", serif; font-size: 11pt; font-weight: 700; }
    .project-ntp-copy { margin-top: 22px; font-size: 11pt; line-height: 1.35; }
    .project-ntp-copy p { margin: 0 0 18px; text-align: justify; }
    .project-ntp-signatures { margin-top: 34px; }
    .project-ntp-signatures td { border: 1px solid #000; padding: 8px 10px; vertical-align: top; }
    .project-ntp-sign-head { font-family: Georgia, "Times New Roman", serif; font-size: 12pt; font-weight: 700; }
    .project-ntp-sign-box { height: 96px; text-align: center; vertical-align: middle; font-family: Georgia, "Times New Roman", serif; font-size: 11pt; font-weight: 700; }
    .project-ntp-panel { margin-top: 28px; border: 1px solid #dbe3f0; background: #f8fbff; padding: 18px; }
    .project-ntp-grid { display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .project-ntp-label { display: block; margin-bottom: 6px; font-size: .74rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #475569; }
    .project-ntp-value-box { min-height: 44px; border: 1px solid #cbd5e1; background: #fff; padding: 10px 12px; font-size: .95rem; box-sizing: border-box; }
    .project-ntp-attachment-link { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; border: 1px solid #1c4587; background: #1c4587; padding: 0 18px; font-size: .9rem; font-weight: 700; color: #fff; text-decoration: none; }
    @media (max-width: 1280px) {
        .project-doc-grid, .project-doc-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
        .project-doc-grid, .project-doc-summary-grid { grid-template-columns: minmax(0, 1fr); }
        .project-doc-brand { flex-direction: column; }
        .project-doc-title { text-align: left; }
        .project-ntp-doc { padding: 20px 18px 24px; }
        .project-ntp-grid { grid-template-columns: minmax(0, 1fr); }
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
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Project Workspace</p>
                    <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
                    <p class="mt-2 text-sm text-gray-500">{{ $project->project_code }} - {{ $project->deal?->deal_code ?? 'No linked deal code' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="project-pill"><span class="text-slate-400">Business</span> {{ $project->business_name ?: '-' }}</span>
                    <span class="project-pill"><span class="text-slate-400">Client</span> {{ $contactName }}</span>
                    <span class="project-pill"><span class="text-slate-400">Planned Start</span> {{ $fmt($project->planned_start_date) }}</span>
                    <span class="project-pill"><span class="text-slate-400">Target Completion</span> {{ $fmt($project->target_completion_date) }}</span>
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
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex flex-wrap gap-x-8 gap-y-2">
                    <p>Deal: <a href="{{ route('deals.show', $project->deal_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $project->deal?->deal_code ?? 'View linked deal' }}</a></p>
                    @if ($project->company_id)
                        <p>Company: <a href="{{ route('company.show', $project->company_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $project->company?->company_name ?? 'View company' }}</a></p>
                    @endif
                    @if ($project->contact_id)
                        <p>Contact: <a href="{{ route('contacts.show', $project->contact_id) }}" class="font-medium text-blue-700 hover:text-blue-800">{{ $contactName }}</a></p>
                    @endif
                </div>
                <div class="project-linked-actions">
                    @if ($tab === 'sow')
                        <span id="projectNtpStatusChip" class="project-doc-status-chip {{ $ntpApproved ? 'approved' : '' }}">
                            <i id="projectNtpStatusIcon" class="{{ $ntpApproved ? 'fas fa-check-circle' : 'fas fa-hourglass-half' }}"></i>
                            <span id="projectNtpStatusText">{{ $ntpStatusLabel }}</span>
                        </span>
                        <button type="submit" form="project-sow-form" class="project-doc-primary">Save Scope of Work</button>
                        <button type="submit" form="project-sow-form" formaction="{{ route('project.sow.generate', $project) }}" class="project-doc-action">Generate SOW Report</button>
                        <a
                            id="projectNtpAction"
                            href="{{ $ntpApproved ? route('project.ntp.submission', $project) : route('project.ntp.download', $project) }}"
                            data-approved-view="{{ $ntpApproved ? 'true' : 'false' }}"
                            data-status-url="{{ route('project.ntp.status', $project) }}"
                            class="{{ $ntpApproved ? 'project-doc-action project-doc-action-approved' : 'project-doc-action' }}"
                        ><i id="projectNtpActionIcon" class="{{ $ntpApproved ? 'fas fa-check-circle' : 'fas fa-file-signature' }}"></i><span id="projectNtpActionText">{{ $ntpApproved ? 'View Approved NTP' : 'Generate NTP' }}</span></a>
                        <a href="{{ route('project.sow.download', $project) }}" class="project-doc-action">Download PDF</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="space-y-4">
            @include('project.partials.tab-'.$tab, compact('project', 'sow', 'report', 'contactName', 'sowWithin', 'sowOut', 'repWithin', 'repOut', 'sowApproval', 'repApproval', 'repSummary'))
        </div>
    </div>
</div>
@if ($tab === 'sow' && $ntpApproved && $ntpRecord)
<div id="projectApprovedNtpModal" class="project-ntp-modal" aria-hidden="true">
    <button id="projectApprovedNtpOverlay" type="button" class="project-ntp-overlay" aria-label="Close approved NTP view"></button>
    <div class="project-ntp-frame">
        <div class="project-ntp-shell">
            <div class="project-ntp-toolbar">
                <button id="projectApprovedNtpClose" type="button" class="project-ntp-close">Close View</button>
            </div>
            @include('project.partials.approved-ntp-document', ['ntp' => $ntpRecord->payload ?? [], 'ntpRecord' => $ntpRecord, 'contactName' => $contactName])
        </div>
    </div>
</div>
@endif
@if ($tab === 'sow')
<script>
    (() => {
        const action = document.getElementById('projectNtpAction');
        const statusChip = document.getElementById('projectNtpStatusChip');
        const statusIcon = document.getElementById('projectNtpStatusIcon');
        const statusText = document.getElementById('projectNtpStatusText');
        const actionIcon = document.getElementById('projectNtpActionIcon');
        const actionText = document.getElementById('projectNtpActionText');
        const approvedNtpModal = document.getElementById('projectApprovedNtpModal');
        const approvedNtpOverlay = document.getElementById('projectApprovedNtpOverlay');
        const approvedNtpClose = document.getElementById('projectApprovedNtpClose');

        if (!action) {
            return;
        }

        const openApprovedNtpModal = () => {
            if (!approvedNtpModal) {
                return;
            }

            approvedNtpModal.classList.add('is-open');
            approvedNtpModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        };

        const closeApprovedNtpModal = () => {
            if (!approvedNtpModal) {
                return;
            }

            approvedNtpModal.classList.remove('is-open');
            approvedNtpModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        const applyState = (payload) => {
            if (!payload) {
                return;
            }

            action.href = payload.action_url || action.href;
            action.className = payload.button_class || 'project-doc-action';
            actionIcon.className = payload.button_icon || 'fas fa-file-signature';
            actionText.textContent = payload.button_label || 'Generate NTP';
            action.dataset.approvedView = payload.is_approved ? 'true' : 'false';

            statusText.textContent = payload.status_label || 'NTP not generated';
            statusIcon.className = payload.is_approved ? 'fas fa-check-circle' : 'fas fa-hourglass-half';
            statusChip.classList.toggle('approved', Boolean(payload.is_approved));
        };

        const pollStatus = async () => {
            try {
                const response = await fetch(action.dataset.statusUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                applyState(payload);

                if (payload.is_approved) {
                    window.clearInterval(intervalId);
                }
            } catch (error) {
                console.error('Unable to refresh NTP status.', error);
            }
        };

        action.addEventListener('click', (event) => {
            if (action.dataset.approvedView === 'true' && approvedNtpModal) {
                event.preventDefault();
                openApprovedNtpModal();
            }
        });

        approvedNtpOverlay?.addEventListener('click', closeApprovedNtpModal);
        approvedNtpClose?.addEventListener('click', closeApprovedNtpModal);

        const intervalId = window.setInterval(pollStatus, 15000);
    })();
</script>
@endif
@endsection
