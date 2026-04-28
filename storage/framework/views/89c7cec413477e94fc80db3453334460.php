<form method="POST" action="<?php echo e(route('project.report.update', $project)); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <section class="project-doc-shell">
        <div class="project-doc-topbar"></div>
        <div class="project-doc-header">
            <div class="project-doc-brand">
                <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company">
                <div class="project-doc-title">
                    <h2>Scope of Work Report</h2>
                    <p>Project Annex</p>
                </div>
            </div>
            <div class="flex items-start justify-between gap-4">
                <div class="project-doc-grid flex-1">
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Condeal Reference No.</span><span class="project-doc-meta-value"><?php echo e($project->deal?->deal_code ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Business Name</span><span class="project-doc-meta-value"><?php echo e($project->business_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Client Name</span><span class="project-doc-meta-value"><?php echo e($project->client_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Version No.</span><span class="project-doc-meta-value"><?php echo e($report?->version_number ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Report No.</span><span class="project-doc-meta-value"><?php echo e($report?->report_number ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Prepared</span><span class="project-doc-meta-value"><?php echo e(optional($report?->date_prepared)->format('M d, Y') ?: '-'); ?></span></div>
                </div>
                <button type="submit" class="project-doc-primary whitespace-nowrap">Save SOW Report</button>
            </div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Form Details</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-4">
            <div><label class="project-doc-label">Version No.</label><input name="version_number" value="<?php echo e(old('version_number', $report?->version_number)); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Prepared</label><input type="date" name="date_prepared" value="<?php echo e(old('date_prepared', optional($report?->date_prepared)->format('Y-m-d'))); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Completion %</label><input type="number" step="0.01" min="0" max="100" name="project_completion_percentage" value="<?php echo e(old('project_completion_percentage', $report?->project_completion_percentage)); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Client Confirmation Name</label><input name="client_confirmation_name" value="<?php echo e(old('client_confirmation_name', $report?->client_confirmation_name ?: $project->client_name)); ?>" class="project-doc-input"></div>
        </div>
    </section>
    <?php $__currentLoopData = ['within' => ['label' => 'Within Scope', 'rows' => $repWithin], 'out' => ['label' => 'Out of Scope', 'rows' => $repOut]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prefix => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="project-doc-section">
            <div class="flex items-center justify-between gap-3">
                <div class="project-doc-section-title w-full"><?php echo e($section['label']); ?></div>
                <button type="button" class="project-doc-action mr-4 mt-3" data-add-scope-row="<?php echo e($prefix); ?>-report-table">Add Row</button>
            </div>
            <div class="project-doc-section-body overflow-x-auto">
                <table class="project-doc-table text-sm">
                    <thead><tr><th class="text-left">Main Task Description</th><th class="text-left">Sub Task Description</th><th class="text-left">Responsible</th><th class="text-left">Duration</th><th class="text-left">Start Date</th><th class="text-left">End Date</th><th class="text-left">Status</th><th class="text-left">Remarks</th></tr></thead>
                    <tbody id="<?php echo e($prefix); ?>-report-table" class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $section['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><input name="<?php echo e($prefix); ?>_main_task_description[]" value="<?php echo e(old($prefix.'_main_task_description.'.$index, $item['main_task_description'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input name="<?php echo e($prefix); ?>_sub_task_description[]" value="<?php echo e(old($prefix.'_sub_task_description.'.$index, $item['sub_task_description'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input name="<?php echo e($prefix); ?>_responsible[]" value="<?php echo e(old($prefix.'_responsible.'.$index, $item['responsible'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input name="<?php echo e($prefix); ?>_duration[]" value="<?php echo e(old($prefix.'_duration.'.$index, $item['duration'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input type="date" name="<?php echo e($prefix); ?>_start_date[]" value="<?php echo e(old($prefix.'_start_date.'.$index, $item['start_date'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input type="date" name="<?php echo e($prefix); ?>_end_date[]" value="<?php echo e(old($prefix.'_end_date.'.$index, $item['end_date'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input name="<?php echo e($prefix); ?>_status[]" value="<?php echo e(old($prefix.'_status.'.$index, $item['status'] ?? '')); ?>" class="project-doc-input"></td>
                                <td><input name="<?php echo e($prefix); ?>_remarks[]" value="<?php echo e(old($prefix.'_remarks.'.$index, $item['remarks'] ?? '')); ?>" class="project-doc-input"></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <p class="project-doc-total ml-4"><?php echo e($section['rows']->filter(fn ($row) => filled($row['main_task_description'] ?? null))->count()); ?> item(s)</p>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Project Status Summary</div>
        <div class="project-doc-section-body">
        <div class="project-doc-summary-grid">
            <?php $__currentLoopData = ['total_main_tasks' => 'Total Main Tasks','open' => 'Open','in_progress' => 'In Progress','delayed' => 'Delayed','completed' => 'Completed','on_hold' => 'On Hold']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="project-doc-summary-box"><span><?php echo e($label); ?></span><input type="number" min="0" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $repSummary[$field] ?? 0)); ?>" class="project-doc-input mt-2"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-6 grid gap-4">
            <div><label class="project-doc-label">Key Issues & Observations</label><textarea name="key_issues" rows="3" class="project-doc-textarea"><?php echo e(old('key_issues', $report?->key_issues)); ?></textarea></div>
            <div><label class="project-doc-label">Recommendations</label><textarea name="recommendations" rows="3" class="project-doc-textarea"><?php echo e(old('recommendations', $report?->recommendations)); ?></textarea></div>
            <div><label class="project-doc-label">Summary & Way Forward</label><textarea name="way_forward" rows="3" class="project-doc-textarea"><?php echo e(old('way_forward', $report?->way_forward)); ?></textarea></div>
        </div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Internal Approval</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php $__currentLoopData = ['prepared_by' => 'Prepared By','reviewed_by' => 'Reviewed By','referred_by_closed_by' => 'Referred By / Closed By','sales_marketing' => 'Sales & Marketing','lead_consultant' => 'Lead Consultant','lead_associate_assigned' => 'Lead Associate Assigned','finance' => 'Finance','president' => 'President','record_custodian' => 'Record Custodian']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><label class="project-doc-label"><?php echo e($label); ?></label><input name="<?php echo e($field); ?>" value="<?php echo e(old($field, $repApproval[$field] ?? '')); ?>" class="project-doc-input"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div><label class="project-doc-label">Date Recorded</label><input type="date" name="date_recorded" value="<?php echo e(old('date_recorded', $repApproval['date_recorded'] ?? '')); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Signed</label><input type="date" name="date_signed" value="<?php echo e(old('date_signed', $repApproval['date_signed'] ?? '')); ?>" class="project-doc-input"></div>
        </div>
    </section>
</form>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\project\partials\tab-report.blade.php ENDPATH**/ ?>