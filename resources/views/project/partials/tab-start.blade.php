@php
    $formDate = old('form_date', optional($start?->form_date ?? $start?->created_at)->format('Y-m-d'));
    $dateStarted = old('date_started', optional($start?->date_started)->format('Y-m-d'));
    $dateCompleted = old('date_completed', optional($start?->date_completed)->format('Y-m-d'));
    $rejectionReason = old('rejection_reason', $start?->rejection_reason);
    $isSoleKyc = $startKycOrganization === 'sole_proprietorship'
        || ($startKycOrganization === 'unknown' && $startKycSole->isNotEmpty() && $startKycJuridical->isEmpty());
    $activeKycItems = $isSoleKyc ? $startKycSole : $startKycJuridical;
    $activeKycPrefix = $isSoleKyc ? 'sole' : 'juridical';
    $activeKycTitle = $isSoleKyc
        ? 'SOLE / NATURAL PERSON/INDIVIDUAL'
        : 'JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)';
@endphp

<form method="POST" action="{{ $startFormAction ?? route('project.start.update', $project) }}" class="space-y-4">
    @csrf
    @if (! empty($startRedirectUrl ?? null))
        <input type="hidden" name="redirect_url" value="{{ $startRedirectUrl }}">
    @endif
    <input type="hidden" name="status" value="{{ old('status', $start?->status ?? 'pending') }}">
    <input type="hidden" name="form_date" value="{{ $formDate }}">
    @foreach ($startChecklist as $index => $item)
        <input type="hidden" name="checklist_label[]" value="{{ old('checklist_label.'.$index, $item['label'] ?? '') }}">
        <input type="hidden" name="checklist_status[{{ $index }}]" value="{{ old('checklist_status.'.$index, $item['status'] ?? 'pending') }}">
    @endforeach
    @foreach ($routing as $index => $item)
        <input type="hidden" name="routing_role[]" value="{{ old('routing_role.'.$index, $item['role'] ?? '') }}">
        <input type="hidden" name="routing_status[]" value="{{ old('routing_status.'.$index, $item['status'] ?? 'pending') }}">
    @endforeach

    <section class="project-doc-shell overflow-hidden bg-white p-2 text-slate-900">
        <div class="mx-auto max-w-[980px] border-[3px] border-[#163b7a] bg-white">
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
                            <div class="min-h-[16px] border-b border-slate-900 px-1 text-[11px] leading-none">{{ $formDate ? \Illuminate\Support\Carbon::parse($formDate)->format('m/d/Y') : '' }}</div>
                        </div>
                        <div class="grid grid-cols-[78px_1fr] items-end gap-2">
                            <div class="text-[9px] uppercase">Date Started:</div>
                            <input type="date" name="date_started" value="{{ $dateStarted }}" class="min-h-[16px] border-b border-slate-900 bg-transparent px-1 py-0 text-[11px] leading-none">
                        </div>
                        <div class="grid grid-cols-[78px_1fr] items-end gap-2">
                            <div class="text-[9px] uppercase">Date Completed:</div>
                            <input type="date" name="date_completed" value="{{ $dateCompleted }}" class="min-h-[16px] border-b border-slate-900 bg-transparent px-1 py-0 text-[11px] leading-none">
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
                        @forelse ($activeKycItems as $index => $item)
                            @php $status = old('kyc_'.$activeKycPrefix.'_status.'.$index, $item['status'] ?? 'pending'); @endphp
                            <div class="grid grid-cols-[14px_1fr] items-start gap-2 text-[11px] leading-[1.2]">
                                <div class="pt-[1px]">
                                    <input type="hidden" name="kyc_{{ $activeKycPrefix }}_label[]" value="{{ old('kyc_'.$activeKycPrefix.'_label.'.$index, $item['label'] ?? '') }}">
                                    <input type="checkbox" class="h-3 w-3 border-slate-700" {{ $status === 'provided' ? 'checked' : '' }}>
                                    <input type="hidden" name="kyc_{{ $activeKycPrefix }}_status[]" value="{{ $status === 'provided' ? 'provided' : 'pending' }}">
                                </div>
                                <div>{{ old('kyc_'.$activeKycPrefix.'_label.'.$index, $item['label'] ?? '') }}</div>
                            </div>
                        @empty
                            <div class="py-2 text-[11px] italic text-slate-500">No KYC requirements available for this business organization yet.</div>
                        @endforelse
                    </div>
                </div>
                @if ($activeKycPrefix === 'sole')
                    @foreach ($startKycJuridical as $index => $item)
                        <input type="hidden" name="kyc_juridical_label[]" value="{{ old('kyc_juridical_label.'.$index, $item['label'] ?? '') }}">
                        <input type="hidden" name="kyc_juridical_status[]" value="{{ old('kyc_juridical_status.'.$index, $item['status'] ?? 'pending') }}">
                    @endforeach
                @else
                    @foreach ($startKycSole as $index => $item)
                        <input type="hidden" name="kyc_sole_label[]" value="{{ old('kyc_sole_label.'.$index, $item['label'] ?? '') }}">
                        <input type="hidden" name="kyc_sole_status[]" value="{{ old('kyc_sole_status.'.$index, $item['status'] ?? 'pending') }}">
                    @endforeach
                @endif

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
                    <tbody id="start-requirements">
                        @foreach ($startReqs as $index => $item)
                            <tr>
                                <td class="border border-slate-900 px-2 py-1 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-900 px-1"><input name="engagement_requirement[]" value="{{ old('engagement_requirement.'.$index, $item['requirement'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_notes[]" value="{{ old('engagement_notes.'.$index, $item['notes'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_purpose[]" value="{{ old('engagement_purpose.'.$index, $item['purpose'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_provided_by[]" value="{{ old('engagement_provided_by.'.$index, $item['provided_by'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_submitted_to[]" value="{{ old('engagement_submitted_to.'.$index, $item['submitted_to'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_assigned_to[]" value="{{ old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                                <td class="border border-slate-900 px-1"><input name="engagement_timeline[]" value="{{ old('engagement_timeline.'.$index, $item['timeline'] ?? '') }}" class="w-full border-0 px-1 py-1 text-[11px]"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table class="w-full border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-[#eef4ff] font-bold">
                            <th class="border border-slate-900 px-1 py-1">Requirement</th>
                            <th class="border border-slate-900 px-1 py-1">Responsible Person</th>
                            <th class="border border-slate-900 px-1 py-1">Name and Signature</th>
                            <th class="border border-slate-900 px-1 py-1">Date &amp; Time Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($startApprovalSteps as $index => $item)
                            <tr>
                                <td class="border border-slate-900 px-1 py-1"><input name="approval_requirement[]" value="{{ old('approval_requirement.'.$index, $item['requirement'] ?? '') }}" class="w-full border-0 px-1 py-0 text-[11px]"></td>
                                <td class="border border-slate-900 px-1 py-1"><input name="approval_responsible_person[]" value="{{ old('approval_responsible_person.'.$index, $item['responsible_person'] ?? '') }}" class="w-full border-0 px-1 py-0 text-[11px]"></td>
                                <td class="border border-slate-900 px-1 py-1"><input name="approval_name_and_signature[]" value="{{ old('approval_name_and_signature.'.$index, $item['name_and_signature'] ?? '') }}" class="w-full border-0 px-1 py-0 text-[11px]"></td>
                                <td class="border border-slate-900 px-1 py-1"><input name="approval_date_time_done[]" value="{{ old('approval_date_time_done.'.$index, $item['date_time_done'] ?? '') }}" class="w-full border-0 px-1 py-0 text-[11px]"></td>
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
                    <div class="border-r border-slate-900 p-2">
                        <input name="clearance_assigned_team_lead" value="{{ old('clearance_assigned_team_lead', $startClearance['assigned_team_lead'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                        <input name="clearance_assigned_team_lead_signature" value="{{ old('clearance_assigned_team_lead_signature', $startClearance['assigned_team_lead_signature'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                    </div>
                    <div class="border-r border-slate-900 p-2">
                        <input name="clearance_lead_consultant_confirmed" value="{{ old('clearance_lead_consultant_confirmed', $startClearance['lead_consultant_confirmed'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                        <input name="clearance_lead_consultant_signature" value="{{ old('clearance_lead_consultant_signature', $startClearance['lead_consultant_signature'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                    </div>
                    <div class="border-r border-slate-900 p-2">
                        <input name="clearance_lead_associate_assigned" value="{{ old('clearance_lead_associate_assigned', $startClearance['lead_associate_assigned'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                        <input name="clearance_lead_associate_signature" value="{{ old('clearance_lead_associate_signature', $startClearance['lead_associate_signature'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                    </div>
                    <div class="p-2">
                        <input name="clearance_sales_marketing" value="{{ old('clearance_sales_marketing', $startClearance['sales_marketing'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                        <div class="mt-2 text-center text-[10px]">Signature over Printed Name</div>
                        <input name="clearance_sales_marketing_signature" value="{{ old('clearance_sales_marketing_signature', $startClearance['sales_marketing_signature'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                    </div>
                </div>

                <div class="grid grid-cols-[1fr_220px] border-t border-slate-900 text-[11px]">
                    <div class="border-r border-slate-900 p-2 text-center">
                        <div class="italic text-[10px]">Record Custodian (Name and Signature)</div>
                        <input name="clearance_record_custodian_name" value="{{ old('clearance_record_custodian_name', $startClearance['record_custodian_name'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                        <input name="clearance_record_custodian_signature" value="{{ old('clearance_record_custodian_signature', $startClearance['record_custodian_signature'] ?? '') }}" class="mt-1 w-full border-b border-slate-900 bg-transparent px-1 py-1 text-center">
                    </div>
                    <div>
                        <div class="border-b border-slate-900 px-2 py-2">
                            <div class="grid grid-cols-[92px_1fr] items-center gap-2">
                                <div>Date Recorded:</div>
                                <input type="date" name="clearance_date_recorded" value="{{ old('clearance_date_recorded', $startClearance['date_recorded'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1">
                            </div>
                        </div>
                        <div class="px-2 py-2">
                            <div class="grid grid-cols-[92px_1fr] items-center gap-2">
                                <div>Date Signed:</div>
                                <input type="date" name="clearance_date_signed" value="{{ old('clearance_date_signed', $startClearance['date_signed'] ?? '') }}" class="w-full border-b border-slate-900 bg-transparent px-1 py-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 border-t border-slate-900 p-3 md:grid-cols-[1fr_auto]">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-600">Rejection / Hold Reason</label>
                    <input name="rejection_reason" value="{{ $rejectionReason }}" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="flex flex-wrap items-end justify-end gap-2">
                    <button type="button" class="project-doc-action" data-add-row="start-requirements">Add Engagement Row</button>
                    <button type="submit" class="project-doc-primary">Save START Form</button>
                </div>
            </div>
        </div>
    </section>
</form>
