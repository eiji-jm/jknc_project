<?php $__env->startSection('content'); ?>
<?php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '-';
    $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' ')) ?: '-';
    $rsatRequirements = collect($rsat?->engagement_requirements ?? [])->whenEmpty(fn () => collect([['number' => 1, 'requirement' => '', 'notes' => '', 'purpose' => '', 'provided_by' => '', 'submitted_to' => '', 'assigned_to' => '', 'timeline' => '']]));
    $rsatClearance = (array) ($rsat?->clearance ?? []);
    $formDate = old('form_date', optional($rsat?->form_date ?? $rsat?->created_at)->format('Y-m-d'));
    $dateStarted = old('date_started', optional($rsat?->date_started)->format('Y-m-d'));
    $dateCompleted = old('date_completed', optional($rsat?->date_completed)->format('Y-m-d'));
    $approvalPreparedBy = old('clearance_assigned_team_lead', $rsatClearance['assigned_team_lead'] ?? '');
    $approvalReviewedBy = old('clearance_lead_consultant_confirmed', $rsatClearance['lead_consultant_confirmed'] ?? '');
    $approvalReferredBy = old('rejection_reason', $rsat?->rejection_reason);
    $approvalSalesMarketing = old('clearance_sales_marketing', $rsatClearance['sales_marketing'] ?? '');
    $approvalLeadAssociate = old('clearance_lead_associate_assigned', $rsatClearance['lead_associate_assigned'] ?? '');
    $approvalLeadConsultant = old('approval_responsible_person.0', $regular->assigned_consultant ?? '');
    $approvalFinance = old('approval_responsible_person.1', '');
    $approvalPresident = old('approval_name_and_signature.0', '');
    $recordCustodian = old('clearance_record_custodian_name', $rsatClearance['record_custodian_name'] ?? '');
    $recordedDate = old('clearance_date_recorded', $rsatClearance['date_recorded'] ?? '');
    $signedDate = old('clearance_date_signed', $rsatClearance['date_signed'] ?? '');
    $clientType = strtolower(trim((string) ($regular->deal?->customer_type ?? '')));
    $isNewClient = $clientType === 'new';
    $isExistingClient = in_array($clientType, ['existing', 'existing client'], true);
    $isChangeInformation = str_contains($clientType, 'change');
    $reportRows = collect($report?->within_scope_items ?? [])->whenEmpty(fn () => collect(array_fill(0, 10, [
        'service' => '',
        'activity_output' => '',
        'frequency' => '',
        'reminder_lead_time' => '',
        'deadline' => '',
    ])));
    $reportApproval = (array) ($report?->internal_approval ?? []);
    $reportNumber = old('report_number', $report?->report_number);
    $reportDatePrepared = old('date_prepared', optional($report?->date_prepared)->format('Y-m-d'));
    $reportPeriod = old('report_period', $reportApproval['report_period'] ?? '');
    $reportPreparedBy = old('prepared_by', $reportApproval['prepared_by'] ?? '');
    $reportPreparedByName = old('prepared_by_name', $reportApproval['prepared_by_name'] ?? '');
    $reportPreparedByDate = old('prepared_by_date', $reportApproval['prepared_by_date'] ?? '');
    $reportReviewedBy = old('reviewed_by', $reportApproval['reviewed_by'] ?? '');
    $reportReviewedByName = old('reviewed_by_name', $reportApproval['reviewed_by_name'] ?? '');
    $reportReviewedByDate = old('reviewed_by_date', $reportApproval['reviewed_by_date'] ?? '');
    $reportReferredBy = old('referred_by_closed_by', $reportApproval['referred_by_closed_by'] ?? '');
    $reportSalesMarketing = old('sales_marketing', $reportApproval['sales_marketing'] ?? '');
    $reportLeadConsultant = old('lead_consultant', $reportApproval['lead_consultant'] ?? '');
    $reportLeadAssociate = old('lead_associate_assigned', $reportApproval['lead_associate_assigned'] ?? '');
    $reportFinance = old('finance', $reportApproval['finance'] ?? '');
    $reportPresident = old('president', $reportApproval['president'] ?? '');
    $reportRecordCustodian = old('record_custodian', $reportApproval['record_custodian'] ?? '');
    $reportDateRecorded = old('date_recorded', $reportApproval['date_recorded'] ?? '');
    $reportDateSigned = old('date_signed', $reportApproval['date_signed'] ?? '');
    $reportTransmittalNo = old('transmittal_no', $reportApproval['transmittal_no'] ?? '');
    $reportDateSubmittedForTransmittal = old('date_submitted_for_transmittal', $reportApproval['date_submitted_for_transmittal'] ?? '');
    $reportClientSignature = old('client_confirmation_name', $report?->client_confirmation_name ?? '');
?>

<style>
    .rsat-workspace {
        background:
            radial-gradient(circle at top left, rgba(13, 70, 140, 0.08), transparent 28%),
            linear-gradient(180deg, #f2f6fc 0%, #fbfcfe 26%, #fbfcfe 100%);
    }
    .rsat-top-card {
        border: 1px solid #d8e1ee;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.05);
    }
    .rsat-pill {
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
    .rsat-sheet {
        border: 1px solid #d7deea;
        background: #fff;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    }
    .rsat-form {
        border: 2px solid #1c4587;
        padding: 28px 30px 34px;
    }
    .rsat-title {
        font-family: Georgia, "Times New Roman", serif;
        font-weight: 700;
        font-size: 2rem;
        line-height: 1.05;
        letter-spacing: 0.02em;
        color: #111827;
        text-align: right;
    }
    .rsat-form-code {
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.8rem;
        color: #64748b;
        text-align: right;
    }
    .rsat-meta-grid {
        display: grid;
        gap: 12px 22px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .rsat-meta-item {
        display: grid;
        grid-template-columns: 160px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.88rem;
    }
    .rsat-meta-label {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #334155;
    }
    .rsat-line-value,
    .rsat-line-input {
        min-height: 34px;
        border: 0;
        border-bottom: 1px solid #111827;
        background: transparent;
        padding: 6px 0 5px;
        color: #111827;
    }
    .rsat-line-input:focus {
        outline: none;
        border-bottom-color: #1c4587;
        box-shadow: inset 0 -1px 0 #1c4587;
    }
    .rsat-client-row {
        display: grid;
        grid-template-columns: 220px repeat(3, minmax(0, 1fr));
        gap: 18px;
        align-items: end;
    }
    .rsat-check-group {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.88rem;
        color: #111827;
        white-space: nowrap;
    }
    .rsat-check {
        display: inline-flex;
        height: 16px;
        width: 16px;
        align-items: center;
        justify-content: center;
        border: 1px solid #111827;
        font-size: 11px;
        line-height: 1;
    }
    .rsat-matrix {
        margin-top: 18px;
    }
    .rsat-matrix-header,
    .rsat-matrix-row {
        display: grid;
        grid-template-columns: 70px 1.1fr 1.5fr 1fr 1fr 0.9fr;
        gap: 14px;
        align-items: end;
    }
    .rsat-matrix-header {
        border-bottom: 2px solid #1c4587;
        padding-bottom: 8px;
        font-family: Arial, sans-serif;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #1c4587;
        text-transform: uppercase;
    }
    .rsat-matrix-row {
        padding: 10px 0 0;
    }
    .rsat-index {
        border-bottom: 1px solid #111827;
        padding: 6px 0 5px;
        text-align: center;
        color: #111827;
    }
    .rsat-row-input {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #111827;
        background: transparent;
        padding: 6px 0 5px;
        color: #111827;
    }
    .rsat-row-input:focus {
        outline: none;
        border-bottom-color: #1c4587;
        box-shadow: inset 0 -1px 0 #1c4587;
    }
    .rsat-signature {
        margin-top: 28px;
        display: grid;
        gap: 8px;
        justify-items: center;
    }
    .rsat-signature-line {
        width: min(100%, 420px);
        border-bottom: 1px solid #111827;
        min-height: 36px;
    }
    .rsat-signature-label {
        font-family: Georgia, "Times New Roman", serif;
        font-style: italic;
        font-size: 0.92rem;
        color: #111827;
    }
    .rsat-section-title {
        margin-top: 30px;
        background: #1c4587;
        padding: 10px 16px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #fff;
        text-align: center;
    }
    .rsat-approval-grid {
        margin-top: 18px;
        display: grid;
        gap: 12px 26px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .rsat-approval-pair {
        display: grid;
        grid-template-columns: 145px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.88rem;
    }
    .rsat-approval-label {
        color: #334155;
        font-style: italic;
    }
    .rsat-footer-grid {
        margin-top: 12px;
        display: grid;
        gap: 12px 26px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .rsat-footer-pair {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.88rem;
    }
    .rsat-footer-note {
        display: grid;
        gap: 8px;
    }
    .rsat-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 22px;
    }
    .rsat-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid #cfd9e7;
        background: #fff;
        padding: 10px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s ease;
    }
    .rsat-tab.is-active {
        border-color: #1c4587;
        background: #1c4587;
        color: #fff;
        box-shadow: 0 10px 24px rgba(28, 69, 135, 0.18);
    }
    @media (max-width: 1024px) {
        .rsat-meta-grid,
        .rsat-approval-grid,
        .rsat-footer-grid {
            grid-template-columns: 1fr;
        }
        .rsat-client-row {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 820px) {
        .rsat-form {
            padding: 20px 18px 24px;
        }
        .rsat-matrix-header,
        .rsat-matrix-row {
            grid-template-columns: 56px repeat(5, minmax(140px, 1fr));
            min-width: 980px;
        }
        .rsat-matrix {
            overflow-x: auto;
        }
    }
</style>

<div class="rsat-workspace p-6">
    <div class="mx-auto max-w-[1380px] space-y-4">
        <div class="rsat-top-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <a href="<?php echo e(route('regular.index')); ?>" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Regular</a>
            <span class="mx-1">/</span><span class="font-medium text-gray-900"><?php echo e($regular->project_code); ?></span>
        </div>

        <div class="rsat-top-card rounded-2xl px-5 py-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Regular Annex Forms</p>
                    <h1 class="mt-2 text-2xl font-semibold text-gray-900"><?php echo e($regular->name); ?></h1>
                    <p class="mt-2 text-sm text-gray-500"><?php echo e($regular->project_code); ?> - <?php echo e($regular->deal?->deal_code ?? 'No linked deal code'); ?></p>
                </div>
                <div class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                    <div><span class="text-gray-400">Business:</span> <?php echo e($regular->business_name ?: '-'); ?></div>
                    <div><span class="text-gray-400">Client:</span> <?php echo e($contactName); ?></div>
                    <div><span class="text-gray-400">Planned Start:</span> <?php echo e($fmt($regular->planned_start_date)); ?></div>
                    <div><span class="text-gray-400">Target Completion:</span> <?php echo e($fmt($regular->target_completion_date)); ?></div>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-2">
                <button type="button" class="rsat-tab <?php echo e($tab === 'rsat' ? 'is-active' : ''); ?>" data-tab-button="rsat">RSAT Form</button>
                <button type="button" class="rsat-tab <?php echo e($tab === 'report' ? 'is-active' : ''); ?>" data-tab-button="report">RSAT Report</button>
                <a href="<?php echo e(route('regular.rsat.download', $regular)); ?>" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Download RSAT PDF</a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
            <div class="flex flex-wrap gap-x-8 gap-y-2">
                <p>Deal: <a href="<?php echo e(route('deals.show', $regular->deal_id)); ?>" class="font-medium text-blue-700 hover:text-blue-800"><?php echo e($regular->deal?->deal_code ?? 'View linked deal'); ?></a></p>
                <?php if($regular->company_id): ?>
                    <p>Company: <a href="<?php echo e(route('company.show', $regular->company_id)); ?>" class="font-medium text-blue-700 hover:text-blue-800"><?php echo e($regular->company?->company_name ?? 'View company'); ?></a></p>
                <?php endif; ?>
                <?php if($regular->contact_id): ?>
                    <p>Contact: <a href="<?php echo e(route('contacts.show', $regular->contact_id)); ?>" class="font-medium text-blue-700 hover:text-blue-800"><?php echo e($contactName); ?></a></p>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('regular.rsat.update', $regular)); ?>" class="rsat-sheet overflow-hidden p-6 <?php echo e($tab !== 'rsat' ? 'hidden' : ''); ?>" data-tab-panel="rsat">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="status" value="<?php echo e(old('status', $rsat?->status ?? 'pending')); ?>">
            <input type="hidden" name="form_date" value="<?php echo e($formDate); ?>">
            <input type="hidden" name="clearance_assigned_team_lead_signature" value="<?php echo e(old('clearance_assigned_team_lead_signature', $rsatClearance['assigned_team_lead_signature'] ?? '')); ?>">
            <input type="hidden" name="clearance_lead_consultant_signature" value="<?php echo e(old('clearance_lead_consultant_signature', $rsatClearance['lead_consultant_signature'] ?? '')); ?>">
            <input type="hidden" name="clearance_lead_associate_signature" value="<?php echo e(old('clearance_lead_associate_signature', $rsatClearance['lead_associate_signature'] ?? '')); ?>">
            <input type="hidden" name="clearance_sales_marketing_signature" value="<?php echo e(old('clearance_sales_marketing_signature', $rsatClearance['sales_marketing_signature'] ?? '')); ?>">
            <input type="hidden" name="clearance_record_custodian_signature" value="<?php echo e(old('clearance_record_custodian_signature', $rsatClearance['record_custodian_signature'] ?? '')); ?>">

            <div class="rsat-form">
                <div class="grid gap-6 lg:grid-cols-[220px_1fr]">
                    <div>
                        <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company" class="h-24 w-auto object-contain">
                    </div>
                    <div class="space-y-2">
                        <div class="rsat-title">REGULAR SERVICE<br>ACTIVITY TRACKER (RSAT)</div>
                        <div class="rsat-form-code">[ Form Code ]</div>
                    </div>
                </div>

                <div class="mt-8 rsat-meta-grid">
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Client Name:</div>
                        <div class="rsat-line-value"><?php echo e($contactName); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Date Created:</div>
                        <div class="rsat-line-value"><?php echo e($formDate ? \Illuminate\Support\Carbon::parse($formDate)->format('m/d/Y') : ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Business Name:</div>
                        <div class="rsat-line-value"><?php echo e($regular->business_name ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Engagement Type:</div>
                        <div class="rsat-line-value"><?php echo e($regular->engagement_type ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Condeal Ref No.:</div>
                        <div class="rsat-line-value"><?php echo e($regular->deal?->deal_code ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Services:</div>
                        <div class="rsat-line-value"><?php echo e($regular->services ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Service Area:</div>
                        <div class="rsat-line-value"><?php echo e($regular->service_area ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Product:</div>
                        <div class="rsat-line-value"><?php echo e($regular->products ?: ''); ?></div>
                    </div>
                </div>

                <div class="mt-5 rsat-client-row">
                    <div class="rsat-meta-item" style="grid-template-columns: 90px minmax(0, 1fr);">
                        <div class="rsat-meta-label">BIF No.</div>
                        <div class="rsat-line-value"><?php echo e($regular->company?->latestBif?->bif_no ?? ''); ?></div>
                    </div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isNewClient ? 'X' : ''); ?></span> NEW CLIENT</div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isExistingClient ? 'X' : ''); ?></span> EXISTING CLIENT</div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isChangeInformation ? 'X' : ''); ?></span> CHANGE INFORMATION</div>
                </div>

                <div class="mt-5 rsat-meta-grid">
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Date Started:</div>
                        <input type="date" name="date_started" value="<?php echo e($dateStarted); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Date Completed:</div>
                        <input type="date" name="date_completed" value="<?php echo e($dateCompleted); ?>" class="rsat-line-input">
                    </div>
                </div>

                <div class="rsat-matrix">
                    <div class="rsat-matrix-header">
                        <div>Item #</div>
                        <div>Service</div>
                        <div>Activity / Output</div>
                        <div>Frequency</div>
                        <div>Reminder Lead Time</div>
                        <div>Deadline</div>
                    </div>

                    <div id="regular-requirements">
                        <?php $__currentLoopData = $rsatRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rsat-matrix-row">
                                <div class="rsat-index"><?php echo e($index + 1); ?></div>
                                <div>
                                    <input name="engagement_purpose[]" value="<?php echo e(old('engagement_purpose.'.$index, $item['purpose'] ?? '')); ?>" class="rsat-row-input">
                                </div>
                                <div>
                                    <input name="engagement_requirement[]" value="<?php echo e(old('engagement_requirement.'.$index, $item['requirement'] ?? '')); ?>" class="rsat-row-input">
                                </div>
                                <div>
                                    <input name="engagement_notes[]" value="<?php echo e(old('engagement_notes.'.$index, $item['notes'] ?? '')); ?>" class="rsat-row-input">
                                </div>
                                <div>
                                    <input name="engagement_timeline[]" value="<?php echo e(old('engagement_timeline.'.$index, $item['timeline'] ?? '')); ?>" class="rsat-row-input">
                                </div>
                                <div>
                                    <input name="engagement_submitted_to[]" value="<?php echo e(old('engagement_submitted_to.'.$index, $item['submitted_to'] ?? '')); ?>" class="rsat-row-input">
                                </div>
                                <input type="hidden" name="engagement_provided_by[]" value="<?php echo e(old('engagement_provided_by.'.$index, $item['provided_by'] ?? '')); ?>">
                                <input type="hidden" name="engagement_assigned_to[]" value="<?php echo e(old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '')); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="rsat-signature">
                    <div class="rsat-signature-line"></div>
                    <div class="rsat-signature-label">Client Fullname &amp; Signature</div>
                </div>

                <div class="rsat-section-title">INTERNAL APPROVAL</div>

                <div class="rsat-approval-grid">
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Prepared By:</div>
                        <input name="clearance_assigned_team_lead" value="<?php echo e($approvalPreparedBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Reviewed By:</div>
                        <input name="clearance_lead_consultant_confirmed" value="<?php echo e($approvalReviewedBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Name:</div>
                        <div class="rsat-line-value"></div>
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Name:</div>
                        <div class="rsat-line-value"></div>
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Date:</div>
                        <div class="rsat-line-value"></div>
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Date:</div>
                        <div class="rsat-line-value"></div>
                    </div>
                </div>

                <div class="rsat-footer-grid">
                    <div class="rsat-footer-pair">
                        <div>Referred By/Closed By:</div>
                        <input name="rejection_reason" value="<?php echo e($approvalReferredBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Sales &amp; Marketing:</div>
                        <input name="clearance_sales_marketing" value="<?php echo e($approvalSalesMarketing); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Lead Consultant:</div>
                        <input name="approval_responsible_person[]" value="<?php echo e($approvalLeadConsultant); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Lead Associate Assigned:</div>
                        <input name="clearance_lead_associate_assigned" value="<?php echo e($approvalLeadAssociate); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Finance:</div>
                        <input name="approval_responsible_person[]" value="<?php echo e($approvalFinance); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>President:</div>
                        <input name="approval_name_and_signature[]" value="<?php echo e($approvalPresident); ?>" class="rsat-line-input">
                    </div>
                </div>

                <div class="mt-4 rsat-footer-grid">
                    <div class="rsat-footer-note">
                        <div class="rsat-footer-pair" style="grid-template-columns: 230px minmax(0, 1fr);">
                            <div>Record Custodian ( Name and Signature)</div>
                            <input name="clearance_record_custodian_name" value="<?php echo e($recordCustodian); ?>" class="rsat-line-input">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="rsat-footer-pair">
                            <div>Date Recorded:</div>
                            <input type="date" name="clearance_date_recorded" value="<?php echo e($recordedDate); ?>" class="rsat-line-input">
                        </div>
                        <div class="rsat-footer-pair">
                            <div>Date Signed:</div>
                            <input type="date" name="clearance_date_signed" value="<?php echo e($signedDate); ?>" class="rsat-line-input">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="approval_requirement[]" value="<?php echo e(old('approval_requirement.0', 'Lead Consultant')); ?>">
                <input type="hidden" name="approval_requirement[]" value="<?php echo e(old('approval_requirement.1', 'Finance')); ?>">
                <input type="hidden" name="approval_name_and_signature[]" value="<?php echo e(old('approval_name_and_signature.1', '')); ?>">
                <input type="hidden" name="approval_date_time_done[]" value="<?php echo e(old('approval_date_time_done.0', '')); ?>">
                <input type="hidden" name="approval_date_time_done[]" value="<?php echo e(old('approval_date_time_done.1', '')); ?>">
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" class="inline-flex items-center border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700" data-add-row="regular-requirements">Add RSAT Row</button>
            </div>
            <div class="rsat-actions">
                <div></div>
                <button type="submit" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Save RSAT</button>
            </div>
        </form>

        <form method="POST" action="<?php echo e(route('regular.report.update', $regular)); ?>" class="rsat-sheet overflow-hidden p-6 <?php echo e($tab !== 'report' ? 'hidden' : ''); ?>" data-tab-panel="report">
            <?php echo csrf_field(); ?>

            <div class="rsat-form">
                <div class="grid gap-6 lg:grid-cols-[220px_1fr]">
                    <div>
                        <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company" class="h-24 w-auto object-contain">
                    </div>
                    <div class="space-y-2">
                        <div class="rsat-title">REGULAR SERVICE ACTIVITY<br>TRACKER REPORT (RSAT REPORT)</div>
                        <div class="rsat-form-code">[ Form Code ]</div>
                    </div>
                </div>

                <div class="rsat-section-title">REPORT INFORMATION</div>

                <div class="mt-8 rsat-meta-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div class="rsat-meta-item" style="grid-template-columns: 120px minmax(0, 1fr);">
                        <div class="rsat-meta-label">Report No.:</div>
                        <input name="report_number" value="<?php echo e($reportNumber); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-meta-item" style="grid-template-columns: 135px minmax(0, 1fr);">
                        <div class="rsat-meta-label">Report Date:</div>
                        <input type="date" name="date_prepared" value="<?php echo e($reportDatePrepared); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-meta-item" style="grid-template-columns: 140px minmax(0, 1fr);">
                        <div class="rsat-meta-label">Report Period:</div>
                        <input name="report_period" value="<?php echo e($reportPeriod); ?>" class="rsat-line-input">
                    </div>
                </div>

                <div class="mt-8 rsat-meta-grid">
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Client Name:</div>
                        <div class="rsat-line-value"><?php echo e($contactName); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Date Created:</div>
                        <div class="rsat-line-value"><?php echo e($formDate ? \Illuminate\Support\Carbon::parse($formDate)->format('m/d/Y') : ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Business Name:</div>
                        <div class="rsat-line-value"><?php echo e($regular->business_name ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Engagement Type:</div>
                        <div class="rsat-line-value"><?php echo e($regular->engagement_type ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Condeal Ref No.:</div>
                        <div class="rsat-line-value"><?php echo e($regular->deal?->deal_code ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Services:</div>
                        <div class="rsat-line-value"><?php echo e($regular->services ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Service Area:</div>
                        <div class="rsat-line-value"><?php echo e($regular->service_area ?: ''); ?></div>
                    </div>
                    <div class="rsat-meta-item">
                        <div class="rsat-meta-label">Product:</div>
                        <div class="rsat-line-value"><?php echo e($regular->products ?: ''); ?></div>
                    </div>
                </div>

                <div class="mt-5 rsat-client-row">
                    <div class="rsat-meta-item" style="grid-template-columns: 90px minmax(0, 1fr);">
                        <div class="rsat-meta-label">BIF No.</div>
                        <div class="rsat-line-value"><?php echo e($regular->company?->latestBif?->bif_no ?? ''); ?></div>
                    </div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isNewClient ? 'X' : ''); ?></span> NEW CLIENT</div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isExistingClient ? 'X' : ''); ?></span> EXISTING CLIENT</div>
                    <div class="rsat-check-group"><span class="rsat-check"><?php echo e($isChangeInformation ? 'X' : ''); ?></span> CHANGE INFORMATION</div>
                </div>

                <div class="rsat-matrix">
                    <div class="rsat-matrix-header">
                        <div>Item #</div>
                        <div>Service</div>
                        <div>Activity / Output</div>
                        <div>Frequency</div>
                        <div>Reminder Lead Time</div>
                        <div>Deadline</div>
                    </div>

                    <div id="regular-report-rows">
                        <?php $__currentLoopData = $reportRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rsat-matrix-row">
                                <div class="rsat-index"><?php echo e($index + 1); ?></div>
                                <div><input name="report_service[]" value="<?php echo e(old('report_service.'.$index, $item['service'] ?? '')); ?>" class="rsat-row-input"></div>
                                <div><input name="report_activity_output[]" value="<?php echo e(old('report_activity_output.'.$index, $item['activity_output'] ?? '')); ?>" class="rsat-row-input"></div>
                                <div><input name="report_frequency[]" value="<?php echo e(old('report_frequency.'.$index, $item['frequency'] ?? '')); ?>" class="rsat-row-input"></div>
                                <div><input name="report_reminder_lead_time[]" value="<?php echo e(old('report_reminder_lead_time.'.$index, $item['reminder_lead_time'] ?? '')); ?>" class="rsat-row-input"></div>
                                <div><input name="report_deadline[]" value="<?php echo e(old('report_deadline.'.$index, $item['deadline'] ?? '')); ?>" class="rsat-row-input"></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="rsat-signature">
                    <input name="client_confirmation_name" value="<?php echo e($reportClientSignature); ?>" class="rsat-line-input w-full max-w-[420px] text-center">
                    <div class="rsat-signature-label">Client Fullname &amp; Signature</div>
                </div>

                <div class="rsat-section-title">INTERNAL APPROVAL</div>

                <div class="rsat-approval-grid">
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Prepared By:</div>
                        <input name="prepared_by" value="<?php echo e($reportPreparedBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Reviewed By:</div>
                        <input name="reviewed_by" value="<?php echo e($reportReviewedBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Name:</div>
                        <input name="prepared_by_name" value="<?php echo e($reportPreparedByName); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Name:</div>
                        <input name="reviewed_by_name" value="<?php echo e($reportReviewedByName); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Date:</div>
                        <input name="prepared_by_date" value="<?php echo e($reportPreparedByDate); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-approval-pair">
                        <div class="rsat-approval-label">Date:</div>
                        <input name="reviewed_by_date" value="<?php echo e($reportReviewedByDate); ?>" class="rsat-line-input">
                    </div>
                </div>

                <div class="rsat-footer-grid">
                    <div class="rsat-footer-pair">
                        <div>Referred By/Closed By:</div>
                        <input name="referred_by_closed_by" value="<?php echo e($reportReferredBy); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Sales &amp; Marketing:</div>
                        <input name="sales_marketing" value="<?php echo e($reportSalesMarketing); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Lead Consultant:</div>
                        <input name="lead_consultant" value="<?php echo e($reportLeadConsultant); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Lead Associate Assigned:</div>
                        <input name="lead_associate_assigned" value="<?php echo e($reportLeadAssociate); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>Finance:</div>
                        <input name="finance" value="<?php echo e($reportFinance); ?>" class="rsat-line-input">
                    </div>
                    <div class="rsat-footer-pair">
                        <div>President:</div>
                        <input name="president" value="<?php echo e($reportPresident); ?>" class="rsat-line-input">
                    </div>
                </div>

                <div class="mt-4 rsat-footer-grid">
                    <div class="rsat-footer-note">
                        <div class="rsat-footer-pair" style="grid-template-columns: 230px minmax(0, 1fr);">
                            <div>Record Custodian ( Name and Signature)</div>
                            <input name="record_custodian" value="<?php echo e($reportRecordCustodian); ?>" class="rsat-line-input">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="rsat-footer-pair">
                            <div>Date Recorded:</div>
                            <input type="date" name="date_recorded" value="<?php echo e($reportDateRecorded); ?>" class="rsat-line-input">
                        </div>
                        <div class="rsat-footer-pair">
                            <div>Date Signed:</div>
                            <input type="date" name="date_signed" value="<?php echo e($reportDateSigned); ?>" class="rsat-line-input">
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <div class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Transmittal Reference</div>
                    <div class="mt-4 rsat-meta-grid">
                        <div class="rsat-meta-item">
                            <div class="rsat-meta-label">Transmittal No.:</div>
                            <input name="transmittal_no" value="<?php echo e($reportTransmittalNo); ?>" class="rsat-line-input">
                        </div>
                        <div class="rsat-meta-item" style="grid-template-columns: 250px minmax(0, 1fr);">
                            <div class="rsat-meta-label">Date Submitted For Transmittal:</div>
                            <input type="date" name="date_submitted_for_transmittal" value="<?php echo e($reportDateSubmittedForTransmittal); ?>" class="rsat-line-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" class="inline-flex items-center border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700" data-add-row="regular-report-rows">Add Report Row</button>
            </div>
            <div class="rsat-actions">
                <div></div>
                <button type="submit" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Save RSAT Report</button>
            </div>
        </form>
    </div>
</div>

<template id="regular-requirement-row-template">
    <div class="rsat-matrix-row">
        <div class="rsat-index"></div>
        <div><input name="engagement_purpose[]" class="rsat-row-input"></div>
        <div><input name="engagement_requirement[]" class="rsat-row-input"></div>
        <div><input name="engagement_notes[]" class="rsat-row-input"></div>
        <div><input name="engagement_timeline[]" class="rsat-row-input"></div>
        <div><input name="engagement_submitted_to[]" class="rsat-row-input"></div>
        <input type="hidden" name="engagement_provided_by[]" value="">
        <input type="hidden" name="engagement_assigned_to[]" value="">
    </div>
</template>

<template id="regular-report-row-template">
    <div class="rsat-matrix-row">
        <div class="rsat-index"></div>
        <div><input name="report_service[]" class="rsat-row-input"></div>
        <div><input name="report_activity_output[]" class="rsat-row-input"></div>
        <div><input name="report_frequency[]" class="rsat-row-input"></div>
        <div><input name="report_reminder_lead_time[]" class="rsat-row-input"></div>
        <div><input name="report_deadline[]" class="rsat-row-input"></div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const requirementsContainer = document.getElementById('regular-requirements');
    const rowTemplate = document.getElementById('regular-requirement-row-template');
    const reportContainer = document.getElementById('regular-report-rows');
    const reportRowTemplate = document.getElementById('regular-report-row-template');
    const tabButtons = Array.from(document.querySelectorAll('[data-tab-button]'));
    const tabPanels = Array.from(document.querySelectorAll('[data-tab-panel]'));

    const syncRowNumbers = (container) => {
        if (!container) {
            return;
        }
        Array.from(container.querySelectorAll('.rsat-matrix-row')).forEach((row, index) => {
            const indexCell = row.querySelector('.rsat-index');
            if (indexCell) {
                indexCell.textContent = index + 1;
            }
        });
    };

    document.querySelectorAll('[data-add-row]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.addRow === 'regular-requirements') {
                requirementsContainer.insertAdjacentHTML('beforeend', rowTemplate.innerHTML);
                syncRowNumbers(requirementsContainer);
                return;
            }

            if (button.dataset.addRow === 'regular-report-rows') {
                reportContainer.insertAdjacentHTML('beforeend', reportRowTemplate.innerHTML);
                syncRowNumbers(reportContainer);
            }
        });
    });

    const activateTab = (tabKey) => {
        tabButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.tabButton === tabKey);
        });
        tabPanels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.tabPanel !== tabKey);
        });
    };

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => activateTab(button.dataset.tabButton));
    });

    syncRowNumbers(requirementsContainer);
    syncRowNumbers(reportContainer);
    activateTab(<?php echo json_encode($tab, 15, 512) ?>);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/regular/show.blade.php ENDPATH**/ ?>