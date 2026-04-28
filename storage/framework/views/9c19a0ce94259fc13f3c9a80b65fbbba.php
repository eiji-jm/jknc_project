<?php $__env->startSection('content'); ?>
<?php
    $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('M d, Y') : '-';
    $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' ')) ?: '-';
    $rsatRequirements = collect($rsat?->engagement_requirements ?? [])->whenEmpty(fn () => collect([['number' => 1, 'requirement' => '', 'notes' => '', 'purpose' => '', 'provided_by' => '', 'submitted_to' => '', 'assigned_to' => '', 'timeline' => '', 'status' => 'open']]));
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
    $generatedReports = $generatedReports ?? collect();
    $rsatAttachments = collect($rsat?->attachments ?? []);
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
    .rsat-table-wrap {
        margin-top: 18px;
        overflow-x: auto;
    }
    .rsat-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
        table-layout: fixed;
        font-family: Georgia, "Times New Roman", serif;
    }
    .rsat-table th,
    .rsat-table td {
        border: 1px solid #111827;
        padding: 0;
        vertical-align: middle;
    }
    .rsat-table th {
        background: #1c4587;
        color: #fff;
        padding: 6px 4px;
        text-align: center;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }
    .rsat-table .rsat-index {
        min-height: 32px;
        padding: 6px 4px;
        text-align: center;
        color: #111827;
        font-size: 0.82rem;
    }
    .rsat-row-delete {
        width: 100%;
        min-height: 32px;
        border: 0;
        background: transparent;
        color: #dc2626;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        line-height: 1;
    }
    .rsat-row-input {
        width: 100%;
        min-height: 32px;
        border: 0;
        background: transparent;
        padding: 6px 8px;
        color: #111827;
        font-size: 0.82rem;
    }
    .rsat-row-input:focus {
        outline: none;
        box-shadow: inset 0 0 0 1px #1c4587;
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
        display: flex;
        align-items: flex-end;
        justify-content: center;
        text-align: center;
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

        <form method="POST" action="<?php echo e(route('regular.rsat.update', $regular)); ?>" enctype="multipart/form-data" class="rsat-sheet overflow-hidden p-6 <?php echo e($tab !== 'rsat' ? 'hidden' : ''); ?>" data-tab-panel="rsat">
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

                <div class="rsat-table-wrap">
                    <table class="rsat-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Item #</th>
                                <th style="width: 18%;">Service</th>
                                <th style="width: 26%;">Activity / Output</th>
                                <th style="width: 12%;">Frequency</th>
                                <th style="width: 14%;">Reminder Lead Time</th>
                                <th style="width: 10%;">Deadline</th>
                                <th style="width: 9%;">Status</th>
                                <th style="width: 6%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="regular-requirements">
                            <?php $__currentLoopData = $rsatRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="rsat-matrix-row">
                                    <td><div class="rsat-index"><?php echo e($index + 1); ?></div></td>
                                    <td>
                                        <input name="engagement_purpose[]" value="<?php echo e(old('engagement_purpose.'.$index, $item['purpose'] ?? '')); ?>" class="rsat-row-input">
                                    </td>
                                    <td>
                                        <input name="engagement_requirement[]" value="<?php echo e(old('engagement_requirement.'.$index, $item['requirement'] ?? '')); ?>" class="rsat-row-input">
                                    </td>
                                    <td>
                                        <input name="engagement_notes[]" value="<?php echo e(old('engagement_notes.'.$index, $item['notes'] ?? '')); ?>" class="rsat-row-input">
                                    </td>
                                    <td>
                                        <input name="engagement_timeline[]" value="<?php echo e(old('engagement_timeline.'.$index, $item['timeline'] ?? '')); ?>" class="rsat-row-input">
                                    </td>
                                    <td>
                                        <input name="engagement_submitted_to[]" value="<?php echo e(old('engagement_submitted_to.'.$index, $item['submitted_to'] ?? '')); ?>" class="rsat-row-input">
                                    </td>
                                    <td>
                                        <select name="engagement_status[]" class="rsat-row-input" style="appearance: none;">
                                            <?php $__currentLoopData = ['open' => 'Open', 'in_progress' => 'In Progress', 'delayed' => 'Delayed', 'completed' => 'Completed', 'on_hold' => 'On Hold']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusValue => $statusLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($statusValue); ?>" <?php if(old('engagement_status.'.$index, $item['status'] ?? 'open') === $statusValue): echo 'selected'; endif; ?>><?php echo e($statusLabel); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="rsat-row-delete" data-delete-row>&times;</button>
                                    </td>
                                    <input type="hidden" name="engagement_provided_by[]" value="<?php echo e(old('engagement_provided_by.'.$index, $item['provided_by'] ?? '')); ?>">
                                    <input type="hidden" name="engagement_assigned_to[]" value="<?php echo e(old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '')); ?>">
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" class="inline-flex items-center border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700" data-add-row="regular-requirements">Add RSAT Row</button>
                </div>

                <div class="rsat-section-title">ATTACHMENTS</div>
                <div class="mt-5 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Upload Supporting Files</label>
                        <div id="rsat-attachments-inputs" class="space-y-3">
                            <div class="flex items-center gap-3" data-attachment-input-row>
                                <input
                                    type="file"
                                    name="attachments[]"
                                    accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700"
                                >
                                <button type="button" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-100" data-remove-attachment-input>
                                    Remove
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Attach images, PDFs, Office files, or text files up to 10MB each.</p>
                        <div class="mt-3">
                            <button type="button" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" id="add-rsat-attachment-input">
                                Add More
                            </button>
                        </div>
                    </div>

                    <?php if($rsatAttachments->isNotEmpty()): ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-700">Attached Files</p>
                            <div class="mt-3 space-y-2">
                                <?php $__currentLoopData = $rsatAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-800"><?php echo e($attachment['name'] ?? 'Attachment'); ?></p>
                                            <p class="text-xs text-slate-500">
                                                <?php echo e(strtoupper(pathinfo((string) ($attachment['name'] ?? ''), PATHINFO_EXTENSION) ?: 'FILE')); ?>

                                                <?php if(filled($attachment['size'] ?? null)): ?>
                                                    • <?php echo e(number_format(((int) $attachment['size']) / 1024, 1)); ?> KB
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php if(filled($attachment['path'] ?? null)): ?>
                                            <a href="<?php echo e(route('uploads.show', ['path' => $attachment['path'], 'download' => 1])); ?>" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                                Download
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="rsat-signature">
                    <div class="rsat-signature-line"><?php echo e($regular->client_name ?: $contactName); ?></div>
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

            <div class="rsat-actions">
                <div></div>
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center bg-[#21409a] px-4 py-2 text-sm font-medium text-white">Save RSAT</button>
                    <button type="submit" formaction="<?php echo e(route('regular.report.generate', $regular)); ?>" class="inline-flex items-center border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700">Generate RSAT Report</button>
                </div>
            </div>
        </form>

        <div class="space-y-5 <?php echo e($tab !== 'report' ? 'hidden' : ''); ?>" data-tab-panel="report">
            <section class="rsat-top-card rounded-2xl px-6 py-5">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Report Registry</p>
                        <h2 class="mt-2 text-3xl font-semibold text-gray-900">RSAT Reports</h2>
                        <p class="mt-2 text-sm text-slate-500">Generated RSAT reports are recorded here from the RSAT form tab.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="rsat-pill"><span class="text-slate-400">Total Reports</span> <?php echo e($generatedReports->count()); ?></span>
                        <span class="rsat-pill"><span class="text-slate-400">Latest Report</span> <?php echo e($generatedReports->first()?->report_number ?: '-'); ?></span>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-4">
                    <div class="relative max-w-md">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input
                            id="regularReportSearch"
                            type="text"
                            placeholder="Search report number or status..."
                            autocomplete="off"
                            class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4 text-left">Report No.</th>
                                <th class="px-6 py-4 text-left">Date of Reporting</th>
                                <th class="px-6 py-4 text-left">Date Sent to Client</th>
                                <th class="px-6 py-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="regularReportTableBody" class="divide-y divide-slate-100 bg-white">
                            <?php $__empty_1 = true; $__currentLoopData = $generatedReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $statusLabel = 'Sent to Client';
                                    $previewUrl = route('regular.report.preview', ['regular' => $regular->id, 'report' => $item->id]);
                                ?>
                                <tr
                                    class="cursor-pointer text-slate-700 transition hover:bg-slate-50"
                                    data-report-search="<?php echo e(\Illuminate\Support\Str::lower(implode(' ', array_filter([$item->report_number, $statusLabel, optional($item->date_prepared)->format('M d, Y')])) )); ?>"
                                    onclick="window.location='<?php echo e($previewUrl); ?>'"
                                >
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-blue-700 hover:text-blue-800"><?php echo e($item->report_number ?: 'Report-'.$item->id); ?></span>
                                    </td>
                                    <td class="px-6 py-4"><?php echo e(optional($item->date_prepared)->format('M d, Y') ?: '-'); ?></td>
                                    <td class="px-6 py-4"><?php echo e(optional($item->created_at)->format('M d, Y') ?: '-'); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <?php echo e($statusLabel); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                        No generated RSAT reports yet. Use <span class="font-semibold text-slate-700">Generate RSAT Report</span> in the RSAT Form tab.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<template id="regular-requirement-row-template">
    <tr class="rsat-matrix-row">
        <td><div class="rsat-index"></div></td>
        <td><input name="engagement_purpose[]" class="rsat-row-input"></td>
        <td><input name="engagement_requirement[]" class="rsat-row-input"></td>
        <td><input name="engagement_notes[]" class="rsat-row-input"></td>
        <td><input name="engagement_timeline[]" class="rsat-row-input"></td>
        <td><input name="engagement_submitted_to[]" class="rsat-row-input"></td>
        <td>
            <select name="engagement_status[]" class="rsat-row-input" style="appearance: none;">
                <option value="open" selected>Open</option>
                <option value="in_progress">In Progress</option>
                <option value="delayed">Delayed</option>
                <option value="completed">Completed</option>
                <option value="on_hold">On Hold</option>
            </select>
        </td>
        <td style="text-align: center;"><button type="button" class="rsat-row-delete" data-delete-row>&times;</button></td>
        <input type="hidden" name="engagement_provided_by[]" value="">
        <input type="hidden" name="engagement_assigned_to[]" value="">
    </tr>
</template>

<template id="rsat-attachment-input-template">
    <div class="flex items-center gap-3" data-attachment-input-row>
        <input
            type="file"
            name="attachments[]"
            accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700"
        >
        <button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-100" data-remove-attachment-input>
            Remove
        </button>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const requirementsContainer = document.getElementById('regular-requirements');
    const rowTemplate = document.getElementById('regular-requirement-row-template');
    const attachmentInputsContainer = document.getElementById('rsat-attachments-inputs');
    const attachmentInputTemplate = document.getElementById('rsat-attachment-input-template');
    const addAttachmentInputButton = document.getElementById('add-rsat-attachment-input');
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

        });
    });

    requirementsContainer?.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-delete-row]');
        if (!trigger) {
            return;
        }

        const row = trigger.closest('tr');
        if (!row) {
            return;
        }

        row.remove();
        syncRowNumbers(requirementsContainer);
    });

    addAttachmentInputButton?.addEventListener('click', () => {
        attachmentInputsContainer?.insertAdjacentHTML('beforeend', attachmentInputTemplate.innerHTML);
    });

    attachmentInputsContainer?.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-remove-attachment-input]');
        if (!trigger) {
            return;
        }

        const row = trigger.closest('[data-attachment-input-row]');
        if (!row) {
            return;
        }

        const rows = attachmentInputsContainer.querySelectorAll('[data-attachment-input-row]');
        if (rows.length <= 1) {
            const input = row.querySelector('input[type="file"]');
            if (input) {
                input.value = '';
            }
            return;
        }

        row.remove();
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

    (() => {
        const searchInput = document.getElementById('regularReportSearch');
        const rows = Array.from(document.querySelectorAll('#regularReportTableBody tr[data-report-search]'));

        if (!searchInput || rows.length === 0) {
            return;
        }

        searchInput.addEventListener('input', () => {
            const keyword = String(searchInput.value || '').trim().toLowerCase();

            rows.forEach((row) => {
                const blob = String(row.dataset.reportSearch || '').toLowerCase();
                row.classList.toggle('hidden', keyword !== '' && !blob.includes(keyword));
            });
        });
    })();

    syncRowNumbers(requirementsContainer);
    activateTab(<?php echo json_encode($tab, 15, 512) ?>);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/regular/show.blade.php ENDPATH**/ ?>