<?php $__env->startSection('content'); ?>
<div class="px-6 py-5 h-full flex flex-col">

    
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-[26px] font-semibold text-gray-800">Department</h1>

        
        <form method="GET">
            <select name="department"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                <option value="">All Departments</option>

                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept); ?>"
                        <?php echo e(request('department') == $dept ? 'selected' : ''); ?>>
                        <?php echo e($dept); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl flex flex-col flex-1 overflow-hidden">

        
        <div class="overflow-auto flex-1">
            <table class="w-full text-sm text-left border-collapse">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 border-r font-semibold">Ref#</th>
                        <th class="px-4 py-3 border-r font-semibold">Subject</th>
                        <th class="px-4 py-3 border-r font-semibold">Department</th>
                        <th class="px-4 py-3 border-r font-semibold">From</th>
                        <th class="px-4 py-3 font-semibold">Date</th>
                    </tr>
                </thead>

                <tbody class="bg-white text-gray-700">

                    <?php $__empty_1 = true; $__currentLoopData = $communications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr
                            class="border-t hover:bg-gray-50 cursor-pointer transition"
                            onclick="window.location='<?php echo e(route('townhall.show', $item->id)); ?>'">

                            <td class="px-4 py-3 border-r">
                                <?php echo e($item->ref_no); ?>

                            </td>

                            <td class="px-4 py-3 border-r font-medium">
                                <?php echo e($item->subject); ?>

                            </td>

                            <td class="px-4 py-3 border-r">
                                <?php echo e($item->department_stakeholder); ?>

                            </td>

                            <td class="px-4 py-3 border-r">
                                <?php echo e($item->from_name); ?>

                            </td>

                            <td class="px-4 py-3">
                                <?php echo e($item->communication_date); ?>

                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                No communications found.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

        
        <div class="px-4 py-3 border-t flex items-center justify-between text-xs text-gray-500">

            <div>
                Total Records:
                <span class="font-semibold text-gray-800">
                    <?php echo e($communications->count()); ?>

                </span>
            </div>

            <div class="flex items-center gap-4">
                <span>Department Filter Active:
                    <span class="font-medium text-blue-600">
                        <?php echo e(request('department') ?? 'All'); ?>

                    </span>
                </span>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/townhall/department.blade.php ENDPATH**/ ?>