<?php $__env->startSection('content'); ?>
<div class="<?php echo e($downloadMode ? 'bg-white p-0' : 'bg-[#f7f6f2] p-6'); ?>">
    <div class="<?php echo e($downloadMode ? '' : 'mx-auto max-w-6xl space-y-4'); ?>">
        <?php if(! $downloadMode): ?>
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
                Deal form preview uses structured Consulting & Deal Form data.
            </div>
        <?php endif; ?>

        <?php echo $__env->make('deals.partials.deal-form-document', ['dealFormData' => $dealFormData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php if($downloadMode): ?>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\deals\pdf.blade.php ENDPATH**/ ?>