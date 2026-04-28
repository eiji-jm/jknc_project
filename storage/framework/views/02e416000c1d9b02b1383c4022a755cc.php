<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="bg-gray-50 p-4 min-h-[560px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">Deal Details</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900"><?php echo e($deal->name); ?></h1>
                    </div>

                    <a href="<?php echo e(route('company.deals', $company->id)); ?>" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Back to Deals
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Stage:</span> <?php echo e($deal->stage); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Amount:</span> P<?php echo e(number_format((float) $deal->amount, 2)); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Expected Close Date:</span> <?php echo e($deal->expected_close_date ? \Illuminate\Support\Carbon::parse($deal->expected_close_date)->format('M d, Y') : '-'); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Owner:</span> <?php echo e($deal->owner); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Deal Source:</span> <?php echo e($deal->deal_source ?: '-'); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Priority:</span> <?php echo e($deal->priority ?: '-'); ?></p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Last Updated:</span> <?php echo e($deal->updated_at); ?></p>
                </div>

                <?php if(! empty($deal->notes)): ?>
                    <div class="mt-6 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <h2 class="text-sm font-semibold text-gray-900">Notes</h2>
                        <p class="mt-2 text-sm text-gray-600"><?php echo e($deal->notes); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\deal-show.blade.php ENDPATH**/ ?>