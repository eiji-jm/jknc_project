<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Secretary Certificate / Board Resolution</h1>
                <p class="text-sm text-gray-500">Review, edit, and export the secretary certificate as PDF with available company data autofilled first.</p>
            </div>
            <a href="<?php echo e($backUrl); ?>" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to KYC</a>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[minmax(0,1.45fr)_400px]">
            <div class="rounded-2xl border border-slate-200 bg-[#f8fafc] p-6">
                <div class="mx-auto max-w-[860px] rounded-sm bg-white px-14 py-12 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <?php echo $__env->make('company.requirements.partials.secretary-certificate-document', ['doc' => $doc], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'juridical_secretary_certificate'])); ?>" id="template-form" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Editable Fields</div>
                    <div class="mt-1 text-xs text-gray-500">Update the blanks and representatives here. The preview updates as you type.</div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <?php $__currentLoopData = [
                        ['affiant_name', 'Affiant Name'],
                        ['affiant_age', 'Affiant Age'],
                        ['affiant_address', 'Affiant Address'],
                        ['corporation_name', 'Corporation Name'],
                        ['sec_registration_no', 'SEC Registration No.'],
                        ['principal_office_address', 'Principal Office Address'],
                        ['board_resolution_no', 'Board Resolution No.'],
                        ['board_meeting_date', 'Board Meeting Date'],
                        ['witness_city', 'Witness City'],
                        ['witness_day', 'Witness Day'],
                        ['witness_month', 'Witness Month'],
                        ['witness_year', 'Witness Year'],
                        ['corporate_secretary_name', 'Corporate Secretary'],
                        ['corporate_secretary_tin', 'Corporate Secretary TIN'],
                        ['subscribed_day', 'Subscribed Day'],
                        ['subscribed_month', 'Subscribed Month'],
                        ['subscribed_year', 'Subscribed Year'],
                        ['affiant_tin', 'Affiant TIN'],
                        ['notary_public', 'Notary Public'],
                        ['doc_no', 'Doc No.'],
                        ['page_no', 'Page No.'],
                        ['book_no', 'Book No.'],
                        ['series_year', 'Series Year'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="text-xs font-medium text-gray-600"><?php echo e($label); ?></label>
                            <input type="text" name="<?php echo e($name); ?>" value="<?php echo e($doc[$name]); ?>" data-sync="<?php echo e($name); ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $doc['representatives']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $representative): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Representative <?php echo e($index + 1); ?></div>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <label class="text-xs font-medium text-gray-600">Name</label>
                                    <input type="text" name="representatives[<?php echo e($index); ?>][name]" value="<?php echo e($representative['name']); ?>" data-sync="representatives_<?php echo e($index); ?>_name" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600">Position</label>
                                    <input type="text" name="representatives[<?php echo e($index); ?>][position]" value="<?php echo e($representative['position']); ?>" data-sync="representatives_<?php echo e($index); ?>_position" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Refresh Preview</button>
                    <button type="submit" formaction="<?php echo e($downloadUrl); ?>" formtarget="_blank" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Download PDF</button>
                    <a href="<?php echo e(route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'juridical_secretary_certificate'])); ?>" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset Autofill</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('#template-form [data-sync]').forEach(function (input) {
    input.addEventListener('input', function () {
        const value = input.value;
        document.querySelectorAll('[data-field="' + input.dataset.sync + '"]').forEach(function (target) {
            target.textContent = value;
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\requirements\secretary-certificate-editor.blade.php ENDPATH**/ ?>