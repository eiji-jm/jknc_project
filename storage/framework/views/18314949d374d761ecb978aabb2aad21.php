<?php $__env->startSection('content'); ?>
<div class="w-full h-full px-6 py-5">

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Review files for approval and rejection</p>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Export
                </button>
                <button class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + New Review
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 px-5 pt-5">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase">Pending Approval</p>
                        <h2 class="text-3xl font-bold text-blue-700 mt-2"><?php echo e($pendingCount); ?></h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase">Approved</p>
                        <h2 class="text-3xl font-bold text-green-700 mt-2"><?php echo e($approvedCount); ?></h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase">Rejected</p>
                        <h2 class="text-3xl font-bold text-red-700 mt-2"><?php echo e($rejectedCount); ?></h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase">Needs Revision</p>
                        <h2 class="text-3xl font-bold text-yellow-700 mt-2"><?php echo e($revisionCount); ?></h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Expired</p>
                        <h2 class="text-3xl font-bold text-gray-700 mt-2"><?php echo e($expiredCount ?? 0); ?></h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center">
                        <i class="fas fa-box-archive"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 pt-5">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col xl:flex-row xl:items-center gap-3 xl:justify-between">
                <div class="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="text"
                            placeholder="Search file, module, department, uploader..."
                            class="w-full md:w-80 border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Modules</option>
                        <option>Town Hall</option>
                        <option>Corporate</option>
                        <option>Accounting</option>
                    </select>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Departments</option>
                        <option>Town Hall</option>
                        <option>Corporate</option>
                        <option>Accounting</option>
                        <option>Operations</option>
                        <option>LGU</option>
                    </select>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Status</option>
                        <option>Pending Approval</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                        <option>Needs Revision</option>
                        <option>Expired</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-white transition">
                        Reset
                    </button>
                    <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Module</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">File Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Department</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Uploaded By</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Date Uploaded</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Approver</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Priority</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $communications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $communication): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $approval = $communication->approval_status ?? 'Pending';

                                $approvalClasses = match($approval) {
                                    'Approved' => 'bg-green-50 text-green-700',
                                    'Rejected' => 'bg-red-50 text-red-700',
                                    'Needs Revision' => 'bg-blue-50 text-blue-700',
                                    default => 'bg-yellow-50 text-yellow-700',
                                };

                                $moduleLabel = 'Town Hall';
                                $moduleClasses = 'bg-blue-50 text-blue-700';

                                $priority = $communication->priority ?? 'Low';
                                $priorityClasses = $priority === 'High'
                                    ? 'bg-red-50 text-red-700'
                                    : 'bg-yellow-50 text-yellow-700';
                            ?>

                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 border-r border-gray-200"><?php echo e($communication->ref_no); ?></td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo e($moduleClasses); ?> font-medium">
                                        <?php echo e($moduleLabel); ?>

                                    </span>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php echo e($communication->subject ?: 'No Subject'); ?>

                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php echo e($communication->department_stakeholder ?: 'Town Hall'); ?>

                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php echo e($communication->from_name ?: ($communication->uploader->name ?? '—')); ?>

                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php echo e($communication->communication_date
                                        ? \Carbon\Carbon::parse($communication->communication_date)->format('M d, Y')
                                        : '—'); ?>

                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php if($communication->approver): ?>
                                        <div class="font-medium text-gray-800">
                                            <?php echo e($communication->approver->name); ?>

                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Approver
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">—</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo e($priorityClasses); ?> font-medium">
                                        <?php echo e($priority); ?>

                                    </span>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php if($communication->is_archived): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 font-medium">
                                            Expired
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo e($approvalClasses); ?> font-medium">
                                            <?php echo e($approval); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <?php if(!$communication->is_archived): ?>
                                            <form action="<?php echo e(route('townhall.approve', $communication->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="<?php echo e(route('townhall.reject', $communication->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                    Reject
                                                </button>
                                            </form>

                                            <form action="<?php echo e(route('townhall.revise', $communication->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-white hover:bg-black transition">
                                                    Revise
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a
                                            href="<?php echo e(route('townhall.show', $communication->id)); ?>"
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                                        >
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                    No files found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div class="flex items-center gap-6">
                    <span>Total Files <span class="text-gray-800 font-semibold"><?php echo e($communications->total()); ?></span></span>
                    <span>Pending <span class="text-yellow-600 font-semibold"><?php echo e($pendingCount); ?></span></span>
                    <span>Approved <span class="text-green-600 font-semibold"><?php echo e($approvedCount); ?></span></span>
                    <span>Rejected <span class="text-red-600 font-semibold"><?php echo e($rejectedCount); ?></span></span>
                    <span>Expired <span class="text-gray-700 font-semibold"><?php echo e($expiredCount ?? 0); ?></span></span>
                </div>

                <div class="flex items-center gap-4">
                    <span>
                        Records per page
                        <select class="bg-transparent outline-none font-semibold text-gray-700">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                    </span>
                    <span><?php echo e($communications->firstItem() ?? 0); ?> to <?php echo e($communications->lastItem() ?? 0); ?></span>
                </div>
            </div>

            <?php if(method_exists($communications, 'links')): ?>
                <div class="mt-4">
                    <?php echo e($communications->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/admin/admin-dashboard.blade.php ENDPATH**/ ?>