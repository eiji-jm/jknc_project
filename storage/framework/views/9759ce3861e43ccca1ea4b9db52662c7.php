<?php
    $statusColorMap = [
        'Draft' => 'bg-gray-100 text-gray-700 border border-gray-200',
        'Submitted' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'Approved' => 'bg-green-100 text-green-700 border border-green-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];
?>

<div class="px-6 py-6 lg:px-8">
    <div class="mb-5">
        <h1 class="text-3xl font-semibold text-gray-900">SOW Reports</h1>
        <p class="mt-1 text-sm text-gray-500">Manage scope of work reports and project status updates</p>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input
                type="text"
                placeholder="Search Reports..."
                autocomplete="off"
                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>
        <a href="<?php echo e(route('project.show', ['project' => $project->id, 'tab' => 'report', 'action' => 'create'])); ?>" class="ml-auto h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
            + Add Report
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-3 py-3 text-left">Report No.</th>
                        <th class="px-3 py-3 text-left">Version No.</th>
                        <th class="px-3 py-3 text-left">Date Prepared</th>
                        <th class="px-3 py-3 text-left">Prepared By</th>
                        <th class="px-3 py-3 text-left">Status</th>
                        <th class="px-3 py-3 text-left">Last Modified</th>
                        <th class="px-3 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(isset($report) && $report): ?>
                        <?php
                            // If single report is passed, wrap in collection for consistent iteration
                            $reports = collect([$report]);
                        ?>
                    <?php else: ?>
                        <?php
                            $reports = $project->sowReports()->orderByDesc('created_at')->get() ?? collect([]);
                        ?>
                    <?php endif; ?>

                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="text-gray-700 hover:bg-gray-50 transition">
                            <td class="px-3 py-3">
                                <a href="#" class="font-medium text-blue-600 hover:text-blue-700">
                                    <?php echo e($item->report_number ?: 'Report-' . $item->id); ?>

                                </a>
                            </td>
                            <td class="px-3 py-3"><?php echo e($item->version_number ?: '-'); ?></td>
                            <td class="px-3 py-3"><?php echo e(optional($item->date_prepared)->format('M d, Y') ?: '-'); ?></td>
                            <td class="px-3 py-3"><?php echo e($item->internal_approval['prepared_by'] ?? '-'); ?></td>
                            <td class="px-3 py-3">
                                <?php
                                    $status = $item->internal_approval['date_signed'] ? 'Approved' : 'Draft';
                                    $colorClass = $statusColorMap[$status] ?? $statusColorMap['Draft'];
                                ?>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($colorClass); ?>">
                                    <?php echo e($status); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3"><?php echo e($item->updated_at?->diffForHumans() ?? '-'); ?></td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="#" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        View
                                    </a>
                                    <a href="#" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-3 py-10 text-center text-sm text-gray-500">
                                No SOW reports found. <a href="<?php echo e(route('project.show', ['project' => $project->id, 'tab' => 'report', 'action' => 'create'])); ?>" class="text-blue-600 hover:text-blue-700 font-medium">Create one now</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-700">
        <?php if(isset($reports) && $reports->count() > 0): ?>
            <span>Total Reports: <span class="font-semibold"><?php echo e($reports->count()); ?></span></span>
            <span>Latest Version: <span class="font-semibold"><?php echo e($reports->max('version_number') ?? '-'); ?></span></span>
            <span>Avg Completion: <span class="font-semibold"><?php echo e(round($reports->avg('project_completion_percentage') ?? 0)); ?>%</span></span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/project/partials/tab-report.blade.php ENDPATH**/ ?>