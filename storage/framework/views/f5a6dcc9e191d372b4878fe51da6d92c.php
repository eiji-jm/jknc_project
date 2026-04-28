<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold"><?php echo e($title); ?></div>
            <div class="flex-1"></div>
            <?php if(!empty($editRoute)): ?>
                <a href="<?php echo e($editRoute); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Edit
                </a>
            <?php endif; ?>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $fieldName = $field['name'];
                    $fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
                    $value = data_get($item, $fieldName);
                    $isFile = ($field['type'] ?? '') === 'file';
                ?>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                    <div class="text-xs text-gray-500"><?php echo e($fieldLabel); ?></div>
                    <div class="text-sm font-medium text-gray-900 mt-1">
                        <?php if($isFile): ?>
                            <?php if($value): ?>
                                <span class="text-gray-700">File uploaded</span>
                            <?php else: ?>
                                <span class="text-gray-400">None</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo e($value ?? '—'); ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\common\show.blade.php ENDPATH**/ ?>