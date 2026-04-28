<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Special Power of Attorney</h1>
                <p class="text-sm text-gray-500">Review, edit, and export the SPA as PDF using the saved company information as a starting point.</p>
            </div>
            <a href="<?php echo e($backUrl); ?>" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to KYC</a>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[minmax(0,1.45fr)_380px]">
            <div class="rounded-2xl border border-slate-200 bg-[#f8fafc] p-6">
                <div class="mx-auto max-w-[860px] rounded-sm bg-white px-14 py-12 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <?php echo $__env->make('company.requirements.partials.spa-document', ['doc' => $doc], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'sole_spa'])); ?>" id="template-form" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Editable Fields</div>
                    <div class="mt-1 text-xs text-gray-500">Adjust any blank or autofilled field here. The preview updates as you type.</div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <?php $__currentLoopData = [
                        ['principal_name', 'Full Name'],
                        ['principal_nationality', 'Nationality'],
                        ['principal_civil_status', 'Civil Status'],
                        ['principal_address', 'Address'],
                        ['attorney_name', 'Attorney-in-Fact Name'],
                        ['attorney_nationality', 'Attorney-in-Fact Nationality'],
                        ['attorney_address', 'Attorney-in-Fact Address'],
                        ['principal_id_no', 'Guarantor ID No.'],
                        ['attorney_id_no', 'Attorney-in-Fact ID No.'],
                        ['signed_place', 'Place of Signing'],
                        ['signed_day', 'Signed Day'],
                        ['signed_month', 'Signed Month'],
                        ['signed_year', 'Signed Year'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="text-xs font-medium text-gray-600"><?php echo e($label); ?></label>
                            <input type="text" name="<?php echo e($name); ?>" value="<?php echo e($doc[$name]); ?>" data-sync="<?php echo e($name); ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="button" id="spa-download-button" data-download-url="<?php echo e($downloadUrl); ?>" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Download PDF</button>
                    <a href="<?php echo e(route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'sole_spa'])); ?>" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset Autofill</a>
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

const spaDownloadButton = document.getElementById('spa-download-button');
const templateForm = document.getElementById('template-form');

if (spaDownloadButton && templateForm) {
    spaDownloadButton.addEventListener('click', function () {
        const params = new URLSearchParams(new FormData(templateForm));
        params.set('autoprint', '1');
        window.location.href = spaDownloadButton.dataset.downloadUrl + '?' + params.toString();
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\requirements\spa-editor.blade.php ENDPATH**/ ?>