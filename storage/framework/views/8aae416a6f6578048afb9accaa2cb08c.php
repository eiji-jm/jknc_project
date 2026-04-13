<form method="POST" action="<?php echo e(route('project.start.update', $project)); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <section class="project-doc-shell">
        <div class="project-doc-topbar"></div>
        <div class="project-doc-header">
            <div class="project-doc-brand">
                <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly and Company">
                <div class="project-doc-title">
                    <h2>START</h2>
                    <p>Service Task Activation and Routing Tracker</p>
                </div>
            </div>
            <div class="flex items-start justify-between gap-4">
                <div class="grid flex-1 gap-3 md:grid-cols-4">
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Client Name</span><span class="project-doc-meta-value"><?php echo e($project->client_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Business Name</span><span class="project-doc-meta-value"><?php echo e($project->business_name ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Condeal Ref. No.</span><span class="project-doc-meta-value"><?php echo e($project->deal?->deal_code ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Engagement Type</span><span class="project-doc-meta-value"><?php echo e($project->engagement_type ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Product</span><span class="project-doc-meta-value"><?php echo e($project->products ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Services</span><span class="project-doc-meta-value"><?php echo e($project->services ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Started</span><span class="project-doc-meta-value"><?php echo e(optional($start?->date_started)->format('M d, Y') ?: '-'); ?></span></div>
                    <div class="project-doc-meta"><span class="project-doc-meta-label">Date Completed</span><span class="project-doc-meta-value"><?php echo e(optional($start?->date_completed)->format('M d, Y') ?: '-'); ?></span></div>
                </div>
                <button type="submit" class="project-doc-primary whitespace-nowrap">Save START</button>
            </div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Tracking Details</div>
        <div class="project-doc-section-body grid gap-4 md:grid-cols-3 xl:grid-cols-4">
            <div><label class="project-doc-label">Date Started</label><input type="date" name="date_started" value="<?php echo e(old('date_started', optional($start?->date_started)->format('Y-m-d'))); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Date Completed</label><input type="date" name="date_completed" value="<?php echo e(old('date_completed', optional($start?->date_completed)->format('Y-m-d'))); ?>" class="project-doc-input"></div>
            <div><label class="project-doc-label">Status</label><select name="status" class="project-doc-select"><?php $__currentLoopData = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'completed' => 'Completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if(old('status', $start?->status ?? 'pending') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div><label class="project-doc-label">Rejection Reason</label><input name="rejection_reason" value="<?php echo e(old('rejection_reason', $start?->rejection_reason)); ?>" class="project-doc-input"></div>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="project-doc-section-title">Core Checklist</div>
        <div class="project-doc-section-body overflow-x-auto">
            <table class="project-doc-table text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Requirement</th>
                        <th class="text-left">Provided</th>
                        <th class="text-left">Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $startChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $status = old('checklist_status.'.$index, $item['status'] ?? 'pending'); ?>
                        <tr>
                            <td>
                                <input type="text" name="checklist_label[]" value="<?php echo e(old('checklist_label.'.$index, $item['label'] ?? '')); ?>" class="project-doc-input">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="checklist_status[<?php echo e($index); ?>]" value="provided" <?php echo e($status === 'provided' ? 'checked' : ''); ?> class="h-4 w-4 border-gray-300 text-blue-700 focus:ring-blue-600">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="checklist_status[<?php echo e($index); ?>]" value="pending" <?php echo e($status !== 'provided' ? 'checked' : ''); ?> class="h-4 w-4 border-gray-300 text-blue-700 focus:ring-blue-600">
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="flex items-center justify-between gap-3">
            <div class="project-doc-section-title w-full">Engagement-Specific Requirements</div>
            <button type="button" class="project-doc-action mr-4 mt-3" data-add-row="start-requirements">Add Row</button>
        </div>
        <div class="project-doc-section-body overflow-x-auto">
            <table class="project-doc-table text-sm">
                <thead><tr><th class="text-left">Requirement / Document</th><th class="text-left">Purpose</th><th class="text-left">Assigned To</th><th class="text-left">Timeline</th></tr></thead>
                <tbody id="start-requirements" class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $startReqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input name="engagement_requirement[]" value="<?php echo e(old('engagement_requirement.'.$index, $item['requirement'] ?? '')); ?>" class="project-doc-input"></td>
                            <td><input name="engagement_purpose[]" value="<?php echo e(old('engagement_purpose.'.$index, $item['purpose'] ?? '')); ?>" class="project-doc-input"></td>
                            <td><input name="engagement_assigned_to[]" value="<?php echo e(old('engagement_assigned_to.'.$index, $item['assigned_to'] ?? '')); ?>" class="project-doc-input"></td>
                            <td><input name="engagement_timeline[]" value="<?php echo e(old('engagement_timeline.'.$index, $item['timeline'] ?? '')); ?>" class="project-doc-input"></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="project-doc-section">
        <div class="flex items-center justify-between gap-3">
            <div class="project-doc-section-title w-full">Clearance Routing</div>
            <button type="button" class="project-doc-action mr-4 mt-3" data-add-row="start-routing">Add Signer</button>
        </div>
        <div class="project-doc-section-body grid gap-3">
            <div class="grid gap-3 md:grid-cols-2 text-xs uppercase tracking-wide text-gray-500"><div>Role</div><div>Status</div></div>
            <div id="start-routing" class="space-y-3">
                <?php $__currentLoopData = $routing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid gap-3 md:grid-cols-2">
                        <input name="routing_role[]" value="<?php echo e(old('routing_role.'.$index, $item['role'] ?? '')); ?>" class="project-doc-input">
                        <select name="routing_status[]" class="project-doc-select">
                            <?php $__currentLoopData = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(old('routing_status.'.$index, $item['status'] ?? 'pending') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
</form>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/project/partials/tab-start.blade.php ENDPATH**/ ?>