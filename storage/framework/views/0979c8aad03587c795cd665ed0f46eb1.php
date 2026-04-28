<?php
    $dropdownId = $dropdownId ?? 'createFieldDropdownMenu';
?>

<div id="<?php echo e($dropdownId); ?>" class="fixed z-[90] hidden max-h-80 w-64 overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-xl">
    <?php $__currentLoopData = $fieldTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button
            type="button"
            class="create-field-type-option flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-gray-700 hover:bg-blue-50"
            data-field-type="<?php echo e($type['value']); ?>"
            data-field-label="<?php echo e($type['label']); ?>"
        >
            <i class="fas <?php echo e($type['icon']); ?> w-4 text-center text-gray-500"></i>
            <span class="font-medium"><?php echo e($type['label']); ?></span>
        </button>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/products/partials/create-field-dropdown.blade.php ENDPATH**/ ?>