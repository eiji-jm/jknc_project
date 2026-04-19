<?php $__env->startSection('content'); ?>
<?php
    $phaseBadgeClasses = [
        'RSAT' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
        'Planning' => 'bg-blue-50 text-blue-700 border border-blue-200',
        'For NTP Approval' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Execution' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Reporting' => 'bg-cyan-50 text-cyan-700 border border-cyan-200',
        'Delivery' => 'bg-violet-50 text-violet-700 border border-violet-200',
        'Completed' => 'bg-green-50 text-green-700 border border-green-200',
    ];
?>

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Regular</h1>
            <p class="mt-1 max-w-3xl text-sm text-gray-500">
                Regular and retainer deals automatically open here, with RSAT, approvals, execution, reporting, delivery, and continuation tracked inside one record.
            </p>
        </div>

        <div class="mb-6 grid gap-3 xl:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">All Regular</p>
                <p class="mt-2 text-3xl font-bold text-gray-900"><?php echo e($stats['all']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">RSAT</p>
                <p class="mt-2 text-3xl font-bold text-indigo-700"><?php echo e($stats['rsat']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Planning</p>
                <p class="mt-2 text-3xl font-bold text-blue-700"><?php echo e($stats['planning']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active</p>
                <p class="mt-2 text-3xl font-bold text-amber-700"><?php echo e($stats['active']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Completed</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700"><?php echo e($stats['completed']); ?></p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-semibold text-gray-900">Regular Registry</h2>
                <p class="mt-1 text-sm text-gray-500">This list is now backed by saved regular engagements instead of placeholder data.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Regular</th>
                            <th class="px-4 py-3 text-left font-medium">Deal</th>
                            <th class="px-4 py-3 text-left font-medium">Company</th>
                            <th class="px-4 py-3 text-left font-medium">Phase</th>
                            <th class="px-4 py-3 text-left font-medium">Owner</th>
                            <th class="px-4 py-3 text-left font-medium">Target</th>
                            <th class="px-4 py-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $regulars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regular): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900"><?php echo e($regular->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($regular->project_code); ?></p>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->deal?->deal_code ?? '-'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->company?->company_name ?: ($regular->business_name ?: '-')); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium <?php echo e($phaseBadgeClasses[$regular->status] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>"><?php echo e($regular->status); ?></span>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->assigned_project_manager ?: '-'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e(optional($regular->target_completion_date)->format('M d, Y') ?: '-'); ?></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e(route('regular.show', $regular)); ?>" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">View</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No regular engagements have created regular records yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/regular/index.blade.php ENDPATH**/ ?>