<?php $__env->startSection('content'); ?>
<div class="<?php echo e(($downloadMode ?? false) ? 'bg-white p-0' : 'bg-[#f7f6f2] p-6'); ?>">
    <div class="<?php echo e(($downloadMode ?? false) ? '' : 'mx-auto max-w-6xl space-y-4'); ?>">
        <?php if(! ($downloadMode ?? false)): ?>
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
                Printable Specimen Signature Form preview based on saved KYC data.
            </div>
        <?php endif; ?>

        <?php echo $__env->make('contacts.partials.specimen-signature-card', [
            'form' => $specimenForm,
            'readonly' => true,
            'contact' => $contact,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php if($downloadMode ?? false): ?>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\contacts\specimen-signature-preview.blade.php ENDPATH**/ ?>