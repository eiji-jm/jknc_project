<?php $__env->startSection('content'); ?>
<div class="bg-[#f7f6f2] p-6">
    <div class="mx-auto max-w-6xl space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-gray-200 bg-white px-5 py-4">
            <div>
                <p class="text-sm text-gray-500">Deals / Preview</p>
                <h1 class="text-2xl font-semibold text-gray-900">Consulting & Deal Form Preview</h1>
                <p class="text-sm text-gray-500">Review the structured form before saving.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('deals.index', ['open_deal_modal' => 1])); ?>" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to Edit</a>
                <form method="POST" action="<?php echo e(route('deals.draft')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php $__currentLoopData = $hiddenFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <button type="submit" class="h-10 rounded-lg border border-amber-200 bg-amber-50 px-4 text-sm font-medium text-amber-700 hover:bg-amber-100">Save Draft</button>
                </form>
                <form method="POST" action="<?php echo e(route('deals.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php $__currentLoopData = $hiddenFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Confirm and Save Deal</button>
                </form>
            </div>
        </div>

        <?php echo $__env->make('deals.partials.deal-form-document', ['dealFormData' => $dealFormData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\deals\preview.blade.php ENDPATH**/ ?>