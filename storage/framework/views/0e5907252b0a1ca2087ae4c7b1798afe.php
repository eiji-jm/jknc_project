<?php $__env->startSection('content'); ?>
<?php
    $fmt = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('M d, Y') : '-';
    $within = collect($report->within_scope_items ?? [])->values();
    $out = collect($report->out_of_scope_items ?? [])->values();
    $withinCount = $within->filter(fn ($row) => filled($row['main_task_description'] ?? null))->count();
    $outCount = $out->filter(fn ($row) => filled($row['main_task_description'] ?? null))->count();
    $summary = (array) ($report->status_summary ?? []);
    $approval = (array) ($report->internal_approval ?? []);
    $clientConfirmationName = $report->client_confirmation_name ?: $project->client_name;
?>

<style>
    .project-sow-sheet {
        border: 1px solid #d7deea;
        background: #fff;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    }
    .project-sow-form {
        border: 2px solid #1c4587;
        padding: 28px 30px 34px;
        background: #fff;
    }
    .project-sow-head {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }
    .project-sow-logo {
        width: 170px;
        height: auto;
        object-fit: contain;
    }
    .project-sow-title {
        text-align: right;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-title h2 {
        font-size: 2rem;
        line-height: 1.04;
        font-weight: 700;
        color: #111827;
        letter-spacing: 0.02em;
    }
    .project-sow-code {
        margin-top: 4px;
        font-size: 0.8rem;
        color: #64748b;
    }
    .project-sow-meta {
        margin-top: 18px;
        display: grid;
        gap: 8px 28px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .project-sow-meta-row {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.9rem;
        color: #111827;
    }
    .project-sow-meta-label {
        color: #334155;
    }
    .project-sow-line {
        min-height: 32px;
        border: 0;
        border-bottom: 1px solid #111827;
        background: transparent;
        padding: 4px 0 5px;
        color: #111827;
        width: 100%;
        display: inline-flex;
        align-items: end;
    }
    .project-sow-section {
        margin-top: 18px;
    }
    .project-sow-section-title {
        background: #1c4587;
        border: 2px solid #1c4587;
        padding: 9px 16px;
        text-align: center;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #fff;
    }
    .project-sow-table-wrap {
        overflow-x: auto;
    }
    .project-sow-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
        table-layout: fixed;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-table th,
    .project-sow-table td {
        border: 1px solid #111827;
        padding: 0;
        vertical-align: middle;
    }
    .project-sow-table th {
        background: #fff;
        padding: 8px 6px;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 400;
        color: #111827;
    }
    .project-sow-cell {
        min-height: 34px;
        padding: 6px 8px;
        font-size: 0.82rem;
        color: #111827;
        display: flex;
        align-items: center;
        background: #fff;
    }
    .project-sow-cell.center {
        justify-content: center;
        text-align: center;
    }
    .project-sow-empty {
        padding: 14px 12px;
        text-align: center;
        color: #64748b;
        font-size: 0.82rem;
        font-family: Arial, sans-serif;
    }
    .project-sow-total {
        display: grid;
        grid-template-columns: 1fr 180px 180px;
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-total-spacer {
        min-height: 34px;
        border-right: 1px solid #111827;
    }
    .project-sow-total-label {
        min-height: 34px;
        border-right: 1px solid #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
    }
    .project-sow-total-value {
        min-height: 34px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        font-size: 0.95rem;
        color: #111827;
    }
    .project-sow-meta-schedule {
        margin-top: 16px;
        display: grid;
        gap: 8px 28px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .project-sow-signature-box {
        border: 1px solid #111827;
        border-top: 0;
        min-height: 66px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 10px 16px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.8rem;
        font-style: italic;
        color: #111827;
        text-align: center;
    }
    .project-sow-approval-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
    }
    .project-sow-approval-cell {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        padding: 8px 10px;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.82rem;
        color: #111827;
    }
    .project-sow-approval-cell:nth-child(2n) {
        border-right: 0;
    }
    .project-sow-approval-label {
        font-style: italic;
        color: #334155;
    }
    .project-sow-record-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(280px, 0.85fr);
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
    }
    .project-sow-record-box {
        border-right: 1px solid #111827;
        min-height: 78px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 10px 16px;
        text-align: center;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.8rem;
        font-style: italic;
        color: #111827;
    }
    .project-sow-record-dates {
        display: grid;
    }
    .project-sow-record-date {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        padding: 8px 10px;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.82rem;
    }
    .project-sow-record-date:last-child {
        border-bottom: 0;
    }
    @media (max-width: 900px) {
        .project-sow-head,
        .project-sow-meta,
        .project-sow-meta-schedule,
        .project-sow-approval-grid,
        .project-sow-record-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .project-sow-title {
            text-align: left;
        }
        .project-sow-approval-cell,
        .project-sow-record-box {
            border-right: 0;
        }
    }
</style>

<div class="project-workspace p-6">
    <div class="mx-auto max-w-[1320px] space-y-4">
        <div class="project-top-card rounded-2xl px-5 py-4 text-sm text-gray-600">
            <a href="<?php echo e(route('project.show', ['project' => $project->id, 'tab' => 'report'])); ?>" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>SOW Reports</a>
            <span class="mx-1">/</span><span class="font-medium text-gray-900"><?php echo e($report->report_number ?: 'Generated Report'); ?></span>
        </div>

        <section class="project-sow-sheet">
            <div class="project-sow-form">
                <div class="project-sow-head">
                    <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company" class="project-sow-logo">
                    <div class="project-sow-title">
                        <h2>SCOPE OF WORK REPORT</h2>
                        <div class="project-sow-code">Generated Project Report</div>
                    </div>
                </div>

                <div class="project-sow-meta">
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Condeal Reference No.:</span>
                        <span class="project-sow-line"><?php echo e($project->deal?->deal_code ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Version No.:</span>
                        <span class="project-sow-line"><?php echo e($report->version_number ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Business Name:</span>
                        <span class="project-sow-line"><?php echo e($project->business_name ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Report No.:</span>
                        <span class="project-sow-line"><?php echo e($report->report_number ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Client Name:</span>
                        <span class="project-sow-line"><?php echo e($project->client_name ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Date of Reporting:</span>
                        <span class="project-sow-line"><?php echo e($fmt($report->date_prepared)); ?></span>
                    </div>
                </div>

                <?php $__currentLoopData = [
                    'within' => ['label' => 'WITHIN SCOPE', 'rows' => $within, 'count' => $withinCount],
                    'out' => ['label' => 'OUT OF SCOPE', 'rows' => $out, 'count' => $outCount],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="project-sow-section">
                        <div class="project-sow-section-title"><?php echo e($section['label']); ?></div>
                        <div class="project-sow-table-wrap">
                            <table class="project-sow-table">
                                <thead>
                                    <tr>
                                        <th style="width: 23%;">MAIN TASK</th>
                                        <th style="width: 22%;">SUB TASK</th>
                                        <th style="width: 13%;">RESPONSIBLE</th>
                                        <th style="width: 8%;">DURATION</th>
                                        <th style="width: 10%;">START DATE</th>
                                        <th style="width: 10%;">END DATE</th>
                                        <th style="width: 7%;">STATUS</th>
                                        <th style="width: 7%;">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $section['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php if(filled($row['main_task_description'] ?? null) || filled($row['sub_task_description'] ?? null)): ?>
                                            <tr>
                                                <td><div class="project-sow-cell"><?php echo e($row['main_task_description'] ?? ''); ?></div></td>
                                                <td><div class="project-sow-cell"><?php echo e($row['sub_task_description'] ?? ''); ?></div></td>
                                                <td><div class="project-sow-cell"><?php echo e($row['responsible'] ?? ''); ?></div></td>
                                                <td><div class="project-sow-cell center"><?php echo e($row['duration'] ?? ''); ?></div></td>
                                                <td><div class="project-sow-cell center"><?php echo e($fmt($row['start_date'] ?? null)); ?></div></td>
                                                <td><div class="project-sow-cell center"><?php echo e($fmt($row['end_date'] ?? null)); ?></div></td>
                                                <td><div class="project-sow-cell center"><?php echo e(str_replace('_', ' ', (string) ($row['status'] ?? ''))); ?></div></td>
                                                <td><div class="project-sow-cell"><?php echo e($row['remarks'] ?? ''); ?></div></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="8"><div class="project-sow-empty">No <?php echo e(strtolower($section['label'])); ?> items recorded.</div></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($section['count'] === 0): ?>
                                        <tr>
                                            <td colspan="8"><div class="project-sow-empty">No <?php echo e(strtolower($section['label'])); ?> items recorded.</div></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="project-sow-total">
                                <div class="project-sow-total-spacer"></div>
                                <div class="project-sow-total-label">Total:</div>
                                <div class="project-sow-total-value"><?php echo e($section['count']); ?> item(s)</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="project-sow-section">
                    <div class="project-sow-section-title">PROJECT STATUS SUMMARY</div>
                    <div class="project-doc-section-body">
                        <div class="project-doc-summary-grid mt-4">
                            <?php $__currentLoopData = ['total_main_tasks' => 'Total Main Tasks','open' => 'Open','in_progress' => 'In Progress','delayed' => 'Delayed','completed' => 'Completed','on_hold' => 'On Hold']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="project-doc-summary-box">
                                    <span><?php echo e($label); ?></span>
                                    <div class="project-doc-input mt-2 bg-slate-50"><?php echo e((int) ($summary[$field] ?? 0)); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="mt-6 grid gap-4">
                            <div><label class="project-doc-label">Key Issues &amp; Observations</label><div class="project-doc-textarea bg-slate-50"><?php echo e($report->key_issues ?: '-'); ?></div></div>
                            <div><label class="project-doc-label">Recommendations</label><div class="project-doc-textarea bg-slate-50"><?php echo e($report->recommendations ?: '-'); ?></div></div>
                            <div><label class="project-doc-label">Summary &amp; Way Forward</label><div class="project-doc-textarea bg-slate-50"><?php echo e($report->way_forward ?: '-'); ?></div></div>
                        </div>
                    </div>
                </div>

                <div class="project-sow-meta-schedule">
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Project Start Date:</span>
                        <span class="project-sow-line"><?php echo e(optional($project->planned_start_date)->format('M d, Y') ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Client Preferred Completion Date:</span>
                        <span class="project-sow-line"><?php echo e(optional($project->client_preferred_completion_date)->format('M d, Y') ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Target Completion Date:</span>
                        <span class="project-sow-line"><?php echo e(optional($project->target_completion_date)->format('M d, Y') ?: '-'); ?></span>
                    </div>
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Date Sent to Client:</span>
                        <span class="project-sow-line"><?php echo e(optional($report->created_at)->format('M d, Y') ?: '-'); ?></span>
                    </div>
                </div>

                <div class="project-sow-section">
                    <div class="project-sow-section-title">CLIENT CONFIRMATION</div>
                    <div class="project-sow-signature-box">
                        Client Fullname &amp; Signature
                    </div>
                    <div class="mt-4 project-sow-meta-row">
                        <span class="project-sow-meta-label">Client Confirmation Name:</span>
                        <span class="project-sow-line"><?php echo e($clientConfirmationName ?: '-'); ?></span>
                    </div>
                </div>

                <div class="project-sow-section">
                    <div class="project-sow-section-title">INTERNAL APPROVAL</div>
                    <div class="project-sow-approval-grid">
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Prepared By:</span>
                            <span class="project-sow-line"><?php echo e($approval['prepared_by'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Reviewed By:</span>
                            <span class="project-sow-line"><?php echo e($approval['reviewed_by'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Referred By/Closed By:</span>
                            <span class="project-sow-line"><?php echo e($approval['referred_by_closed_by'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Sales &amp; Marketing:</span>
                            <span class="project-sow-line"><?php echo e($approval['sales_marketing'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Lead Consultant:</span>
                            <span class="project-sow-line"><?php echo e($approval['lead_consultant'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Lead Associate Assigned:</span>
                            <span class="project-sow-line"><?php echo e($approval['lead_associate_assigned'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">Finance:</span>
                            <span class="project-sow-line"><?php echo e($approval['finance'] ?? ''); ?></span>
                        </div>
                        <div class="project-sow-approval-cell">
                            <span class="project-sow-approval-label">President:</span>
                            <span class="project-sow-line"><?php echo e($approval['president'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="project-sow-record-grid">
                        <div class="project-sow-record-box">Record Custodian ( Name and Signature )</div>
                        <div class="project-sow-record-dates">
                            <div class="project-sow-record-date">
                                <span class="project-sow-approval-label">Date Recorded :</span>
                                <span class="project-sow-line"><?php echo e($fmt($approval['date_recorded'] ?? null)); ?></span>
                            </div>
                            <div class="project-sow-record-date">
                                <span class="project-sow-approval-label">Date Signed :</span>
                                <span class="project-sow-line"><?php echo e($fmt($approval['date_signed'] ?? null)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/project/report-preview.blade.php ENDPATH**/ ?>