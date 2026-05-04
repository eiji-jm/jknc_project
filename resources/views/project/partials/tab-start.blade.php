@php
    $formDate = optional($start?->form_date ?? $start?->created_at)->format('m/d/Y');
    $dateStartedLabel = optional($start?->date_started)->format('m/d/Y');
    $dateCompletedLabel = optional($start?->date_completed)->format('m/d/Y');
    $rejectionReason = $start?->rejection_reason;
    $isSoleKyc = $startKycOrganization === 'sole_proprietorship'
        || ($startKycOrganization === 'unknown' && $startKycSole->isNotEmpty() && $startKycJuridical->isEmpty());
    $activeKycItems = $isSoleKyc ? $startKycSole : $startKycJuridical;
    $activeKycTitle = $isSoleKyc
        ? 'SOLE / NATURAL PERSON/INDIVIDUAL'
        : 'JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)';
    $startStatus = strtolower((string) ($start?->status ?? 'draft'));
    $startStatusLabel = match ($startStatus) {
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        default => 'Draft',
    };
    $serviceMemoPath = (string) data_get($start?->attachments, 'service_memo_pdf_path', '');
    $serviceMemoUrl = $serviceMemoPath !== '' ? route('uploads.show', ['path' => $serviceMemoPath]) : null;
    $serviceMemoDownloadUrl = $serviceMemoPath !== '' ? route('uploads.show', ['path' => $serviceMemoPath, 'download' => 1]) : null;
    $startDrawerId = ($startDrawerId ?? 'project-start-drawer').'-'.($project?->id ?? 'default');
@endphp

<div class="space-y-4">
    <div class="project-doc-view-sheet">
        <div class="project-doc-view-paper p-0">
            <section class="project-doc-shell overflow-hidden bg-white p-2 text-slate-900">
                <div class="mx-auto max-w-[980px] border-[3px] border-[#163b7a] bg-white">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-slate-700">START Status:</span>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $startStatus === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($startStatus === 'rejected' ? 'bg-rose-100 text-rose-700' : ($startStatus === 'pending_approval' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700')) }}">{{ $startStatusLabel }}</span>
                                    @if ($startStatus === 'approved' && $start?->approved_at)
                                        <span class="text-xs text-slate-500">Approved {{ optional($start->approved_at)->format('M d, Y h:i A') }}{{ $start?->approved_by_name ? ' by '.$start->approved_by_name : '' }}</span>
                                    @elseif ($startStatus === 'pending_approval')
                                        <span class="text-xs text-slate-500">Awaiting admin approval from the dashboard.</span>
                                    @elseif ($startStatus === 'rejected' && $start?->rejected_at)
                                        <span class="text-xs text-slate-500">Rejected {{ optional($start->rejected_at)->format('M d, Y h:i A') }}{{ $start?->rejected_by_name ? ' by '.$start->rejected_by_name : '' }}</span>
                                    @endif
                                </div>
                                <div class="text-xs uppercase tracking-wide text-slate-500">Admin approval required</div>
                            </div>
                            <div class="p-3">
                                <div class="grid grid-cols-[170px_1fr] gap-4">
                                    <div>
                                        <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly and Company" class="h-16 w-auto object-contain">
                                    </div>
                                    <div class="text-center font-['Times_New_Roman',Georgia,serif]">
                                        <div class="text-[20px] font-bold leading-tight md:text-[28px]">SERVICE TASK ACTIVATION AND</div>
                                        <div class="text-[20px] font-bold leading-tight md:text-[28px]">ROUTING TRACKER (START)</div>
                                        <div class="mt-1 text-[9px]">CASA-F-010-v1.0-03.16.26</div>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-[1fr_1fr_0.9fr]">
                                    <div class="space-y-1">
                                        @foreach ([
                                            'CLIENT NAME:' => $project->client_name ?: '',
                                            'BUSINESS NAME:' => $project->business_name ?: '',
                                            'CONDEAL REF NO.' => $project->deal?->deal_code ?: '',
                                            'Service Area' => $project->service_area ?: '',
                                        ] as $label => $value)
                                            <div class="grid grid-cols-[95px_1fr] items-end gap-2">
                                                <div class="text-[9px] uppercase">{{ $label }}</div>
                                                <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $value }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="space-y-1">
                                        @foreach ([
                                            'PRODUCT:' => $project->products ?: '',
                                            'SERVICES :' => $project->services ?: '',
                                            'ENGAGEMENT TYPE:' => $project->engagement_type ?: '',
                                        ] as $label => $value)
                                            <div class="grid grid-cols-[95px_1fr] items-end gap-2">
                                                <div class="text-[9px] uppercase">{{ $label }}</div>
                                                <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $value }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="space-y-1">
                                        <div class="grid grid-cols-[78px_1fr] items-end gap-2">
                                            <div class="text-[9px] uppercase">Date:</div>
                                            <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $formDate }}</div>
                                        </div>
                                        <div class="grid grid-cols-[78px_1fr] items-end gap-2">
                                            <div class="text-[9px] uppercase">Date Started:</div>
                                            <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $dateStartedLabel }}</div>
                                        </div>
                                        <div class="grid grid-cols-[78px_1fr] items-end gap-2">
                                            <div class="text-[9px] uppercase">Date Completed:</div>
                                            <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $dateCompletedLabel }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-900">
                                <div class="border-y border-slate-900 bg-[#163b7a] px-3 py-1 text-center font-['Times_New_Roman',Georgia,serif] text-[16px] font-bold text-white md:text-[18px]">Client Due Diligence (KYC) Documents</div>
                                <div>
                                    <div class="border-b border-slate-900 bg-[#eef4ff] px-2 py-1 text-center text-[10px] font-bold uppercase">
                                        @if ($isSoleKyc)
                                            {{ $activeKycTitle }}
                                        @else
                                            JURIDICAL ENTITY <span class="normal-case italic">(Corporation / OPC / Partnership / Cooperative)</span>
                                        @endif
                                    </div>
                                    <div class="px-6 py-2">
                                        @forelse ($activeKycItems as $item)
                                            <div class="grid grid-cols-[14px_1fr] items-start gap-2 text-[11px] leading-[1.2]">
                                                <div class="pt-[1px]">
                                                    <input type="checkbox" class="h-3 w-3 border-slate-700" {{ ($item['status'] ?? 'pending') === 'provided' ? 'checked' : '' }} disabled>
                                                </div>
                                                <div>{{ $item['label'] ?? '' }}</div>
                                            </div>
                                        @empty
                                            <div class="py-2 text-[11px] italic text-slate-500">No KYC requirements available for this business organization yet.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="border-y border-slate-900 bg-[#163b7a] px-3 py-1 text-center font-['Times_New_Roman',Georgia,serif] text-[16px] font-bold text-white md:text-[18px]">Engagement-Specific Requirements</div>
                                <table class="w-full border-collapse text-[11px]">
                                    <thead>
                                        <tr class="bg-[#eef4ff] font-bold">
                                            <th class="border border-slate-900 px-1 py-1">No.</th>
                                            <th class="border border-slate-900 px-1 py-1">Requirement / Document</th>
                                            <th class="border border-slate-900 px-1 py-1">Notes</th>
                                            <th class="border border-slate-900 px-1 py-1">Purpose</th>
                                            <th class="border border-slate-900 px-1 py-1">Provided By</th>
                                            <th class="border border-slate-900 px-1 py-1">Submitted To</th>
                                            <th class="border border-slate-900 px-1 py-1">Assigned To</th>
                                            <th class="border border-slate-900 px-1 py-1">Timeline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($startReqs as $index => $item)
                                            <tr>
                                                <td class="border border-slate-900 px-2 py-1 text-center">{{ $index + 1 }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['requirement'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['notes'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['purpose'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['provided_by'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['submitted_to'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['assigned_to'] ?? '' }}</td>
                                                <td class="border border-slate-900 px-2 py-1">{{ $item['timeline'] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="border-y border-slate-900 px-3 py-1 text-center font-['Times_New_Roman',Georgia,serif] text-[20px] font-bold">CLEARANCE</div>
                                <div class="grid grid-cols-4 border-b border-slate-900 text-center text-[10px] uppercase">
                                    <div class="border-r border-slate-900 px-2 py-1">Assigned to Regular/Project Team Lead</div>
                                    <div class="border-r border-slate-900 px-2 py-1">Lead Consultant Confirmed</div>
                                    <div class="border-r border-slate-900 px-2 py-1">Lead Associate Assigned</div>
                                    <div class="px-2 py-1">Sales &amp; Marketing</div>
                                </div>
                                <div class="grid grid-cols-4 text-[11px]">
                                    <div class="border-r border-slate-900 p-2 text-center">
                                        <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['assigned_team_lead'] ?? '' }}</div>
                                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['assigned_team_lead_signature'] ?? '' }}</div>
                                    </div>
                                    <div class="border-r border-slate-900 p-2 text-center">
                                        <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['lead_consultant_confirmed'] ?? '' }}</div>
                                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['lead_consultant_signature'] ?? '' }}</div>
                                    </div>
                                    <div class="border-r border-slate-900 p-2 text-center">
                                        <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['lead_associate_assigned'] ?? '' }}</div>
                                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['lead_associate_signature'] ?? '' }}</div>
                                    </div>
                                    <div class="p-2 text-center">
                                        <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['sales_marketing'] ?? '' }}</div>
                                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['sales_marketing_signature'] ?? '' }}</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-[1fr_220px] border-t border-slate-900 text-[11px]">
                                    <div class="border-r border-slate-900 p-2 text-center">
                                        <div class="italic text-[10px]">Record Custodian (Name and Signature)</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['record_custodian_name'] ?? '' }}</div>
                                        <div class="mt-1 min-h-[28px] border-b border-slate-900 px-1 py-1">{{ $startClearance['record_custodian_signature'] ?? '' }}</div>
                                    </div>
                                    <div>
                                        <div class="border-b border-slate-900 px-2 py-2">
                                            <div class="grid grid-cols-[92px_1fr] items-center gap-2">
                                                <div>Date Recorded:</div>
                                                <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ !empty($startClearance['date_recorded']) ? \Illuminate\Support\Carbon::parse($startClearance['date_recorded'])->format('m/d/Y') : '' }}</div>
                                            </div>
                                        </div>
                                        <div class="px-2 py-2">
                                            <div class="grid grid-cols-[92px_1fr] items-center gap-2">
                                                <div>Date Signed:</div>
                                                <div class="min-h-[28px] border-b border-slate-900 px-1 py-1">{{ !empty($startClearance['date_signed']) ? \Illuminate\Support\Carbon::parse($startClearance['date_signed'])->format('m/d/Y') : '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-3 border-t border-slate-900 p-3">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-600">Rejection / Hold Reason</label>
                                        <div class="mt-1 min-h-[42px] rounded border border-slate-300 px-3 py-2 text-sm">{{ $rejectionReason }}</div>
                                    </div>
                                </div>
                        </div>
                </div>
            </section>
        </div>
    </div>

    <x-slide-over :id="$startDrawerId" width="sm:max-w-[760px] lg:max-w-[980px]">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Edit START Form</h2>
                <p class="mt-1 text-sm text-slate-500">Update the START document from this drawer, then save or submit it for approval.</p>
            </div>
            <button type="button" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50" data-close-start-drawer="{{ $startDrawerId }}">Close</button>
        </div>
        <div class="flex-1 overflow-y-auto px-4 py-4">
            @include('project.partials.tab-start-form', [
                'project' => $project,
                'start' => $start,
                'startChecklist' => $startChecklist,
                'startKycOrganization' => $startKycOrganization,
                'startKycSole' => $startKycSole,
                'startKycJuridical' => $startKycJuridical,
                'startReqs' => $startReqs,
                'startApprovalSteps' => $startApprovalSteps,
                'startClearance' => $startClearance,
                'routing' => $routing,
                'startRedirectUrl' => $startRedirectUrl ?? null,
                'startFormAction' => $startFormAction ?? route('project.start.update', $project),
                'startFormId' => 'drawer-start-form-'.$project->id,
            ])
        </div>
    </x-slide-over>
</div>

<template id="start-requirement-row-template">
    <tr class="start-matrix-row">
        <td class="border border-slate-900 px-2 py-1 text-center start-index"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_requirement[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_notes[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_purpose[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_provided_by[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_submitted_to[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_assigned_to[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
        <td class="border border-slate-900 px-1"><input name="engagement_timeline[]" class="w-full border-0 px-1 py-1 text-[11px]"></td>
    </tr>
</template>

<script>
    (() => {
        const drawerId = @json($startDrawerId);
        const drawer = document.getElementById(drawerId);
        const openButtons = document.querySelectorAll(`[data-open-start-drawer="${drawerId}"]`);
        const closeButton = document.querySelector(`[data-close-start-drawer="${drawerId}"]`);
        const rowTemplate = document.getElementById('start-requirement-row-template');
        const requirementsContainer = drawer?.querySelector('#start-requirements');
        const addRowButton = drawer?.querySelector('[data-add-row="start-requirements"]');

        const syncRowNumbers = () => {
            requirementsContainer?.querySelectorAll('.start-matrix-row').forEach((row, index) => {
                const cell = row.querySelector('.start-index');
                if (cell) {
                    cell.textContent = index + 1;
                }
            });
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', () => window.jkncSlideOver?.open(drawer));
        });
        closeButton?.addEventListener('click', () => window.jkncSlideOver?.close(drawer));
        drawer?.querySelector('[data-drawer-overlay]')?.addEventListener('click', () => window.jkncSlideOver?.close(drawer));

        addRowButton?.addEventListener('click', () => {
            if (!requirementsContainer || !rowTemplate) {
                return;
            }

            requirementsContainer.insertAdjacentHTML('beforeend', rowTemplate.innerHTML);
            syncRowNumbers();
        });

        syncRowNumbers();
    })();
</script>
