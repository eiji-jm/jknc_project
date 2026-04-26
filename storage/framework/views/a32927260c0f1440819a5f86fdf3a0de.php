<form method="POST" action="<?php echo e(route('project.sow.update', $project)); ?>" enctype="multipart/form-data" class="space-y-4">
    <?php echo csrf_field(); ?>
    <section class="project-doc-shell">
        <div class="project-doc-topbar"></div>
        <div class="project-doc-header">
            <div class="project-doc-brand">
                <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company">
                <div class="project-doc-title">
                    <h2>Scope of Work</h2>
                    <p>Project Annex</p>
                </div>
            </div>
            <div class="flex items-start justify-between gap-4">
                <div class="project-doc-grid flex-1">
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Condeal Reference No.</span><span class="project-doc-meta-value"><?php echo e($project->deal?->deal_code ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Business Name</span><span class="project-doc-meta-value"><?php echo e($project->business_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Client Name</span><span class="project-doc-meta-value"><?php echo e($project->client_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Version No.</span><span class="project-doc-meta-value"><?php echo e($sow?->version_number ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">SOW No.</span><span class="project-doc-meta-value"><?php echo e($sow?->sow_number ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Prepared</span><span class="project-doc-meta-value"><?php echo e(optional($sow?->date_prepared)->format('M d, Y') ?: '-'); ?></span></div>
                </div>
                <button type="submit" class="project-doc-primary whitespace-nowrap">Save SOW</button>
            </div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Form Details</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-4">
            <div><label class="project-doc-label">Version No.</label><input name="version_number" value="<?php echo e(old('version_number', $sow?->version_number)); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Prepared</label><input type="date" name="date_prepared" value="<?php echo e(old('date_prepared', optional($sow?->date_prepared)->format('Y-m-d'))); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Approval Status</label><select name="approval_status" class="project-doc-select"><?php $__currentLoopData = ['draft' => 'Draft', 'pending_review' => 'Pending Review', 'approved' => 'Approved']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if(old('approval_status', $sow?->approval_status ?? 'draft') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div><label class="project-doc-label">NTP Status</label><select name="ntp_status" class="project-doc-select"><?php $__currentLoopData = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if(old('ntp_status', $sow?->ntp_status ?? 'pending') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        </div>
    </section>
    <?php $__currentLoopData = ['within' => ['label' => 'Within Scope', 'rows' => $sowWithin], 'out' => ['label' => 'Out of Scope', 'rows' => $sowOut]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prefix => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="project-doc-section">
            <div class="flex items-center justify-between gap-3">
                <div class="project-doc-section-title w-full"><?php echo e($section['label']); ?></div>
                <button type="button" class="project-doc-action mr-4 mt-3" data-add-scope-row="<?php echo e($prefix); ?>-scope-table">Add Row</button>
            </div>
            <div class="project-doc-section-body overflow-x-auto">
                <table class="project-doc-table text-sm">
                    <thead><tr><th class="text-left">Main Task Description</th><th class="text-left">Sub Task Description</th><th class="text-left">Responsible</th><th class="text-left">Duration</th><th class="text-left">Start Date</th><th class="text-left">End Date</th><th class="text-left">Status</th><th class="text-left">Remarks</th></tr></thead>
                    <tbody id="<?php echo e($prefix); ?>-scope-table" class="divide-y divide-gray-100">
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
        <div class="project-doc-section-title">Schedule and Client Confirmation</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-3">
            <div><label class="project-doc-label">Project Start Date</label><input value="<?php echo e(optional($project->planned_start_date)->format('Y-m-d')); ?>" readonly class="project-doc-input"></div>
            <div><label class="project-doc-label">Target Completion Date</label><input value="<?php echo e(optional($project->target_completion_date)->format('Y-m-d')); ?>" readonly class="project-doc-input"></div>
            <div><label class="project-doc-label">Client Preferred Completion Date</label><input value="<?php echo e(optional($project->client_preferred_completion_date)->format('Y-m-d')); ?>" readonly class="project-doc-input"></div>
        </div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-2 pt-0">
            <div><label class="project-doc-label">Client Confirmation Name</label><input name="client_confirmation_name" value="<?php echo e(old('client_confirmation_name', $sow?->client_confirmation_name ?: $project->client_name)); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Signed Client File</label><input type="file" name="client_signed_attachment" class="project-doc-input !p-2"><?php if($sow?->client_signed_attachment_path): ?><a href="<?php echo e(route('uploads.show', ['path' => $sow->client_signed_attachment_path])); ?>" target="_blank" class="mt-2 inline-block text-sm font-medium text-blue-700 hover:text-blue-800">View uploaded signed file</a><?php endif; ?></div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Internal Approval</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php $__currentLoopData = ['prepared_by' => 'Prepared By','reviewed_by' => 'Reviewed By','referred_by_closed_by' => 'Referred By / Closed By','sales_marketing' => 'Sales & Marketing','lead_consultant' => 'Lead Consultant','lead_associate_assigned' => 'Lead Associate Assigned','finance' => 'Finance','president' => 'President','record_custodian' => 'Record Custodian']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><label class="project-doc-label"><?php echo e($label); ?></label><input name="<?php echo e($field); ?>" value="<?php echo e(old($field, $sowApproval[$field] ?? '')); ?>" class="project-doc-input"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div><label class="project-doc-label">Date Recorded</label><input type="date" name="date_recorded" value="<?php echo e(old('date_recorded', $sowApproval['date_recorded'] ?? '')); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Signed</label><input type="date" name="date_signed" value="<?php echo e(old('date_signed', $sowApproval['date_signed'] ?? '')); ?>" class="project-doc-input"></div>
        </div>
    </section>
</form>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/project/partials/tab-sow.blade.php ENDPATH**/ ?>