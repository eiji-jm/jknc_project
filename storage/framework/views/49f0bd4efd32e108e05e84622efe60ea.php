<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="bg-gray-50 p-4">
            <?php if(session('bif_success')): ?>
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    <?php echo e(session('bif_success')); ?>

                </div>
            <?php endif; ?>

            <div class="rounded-md border border-gray-200 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button class="h-9 rounded bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Business Client Information Form</button>
                    </div>

                    <a href="<?php echo e(route('company.bif.create', $company->id)); ?>" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center">
                        New Business Client Information Form
                    </a>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Document</th>
                                        <th class="px-4 py-3 text-left font-medium">Status</th>
                                        <th class="px-4 py-3 text-left font-medium">Date Submitted</th>
                                        <th class="px-4 py-3 text-left font-medium">Remarks</th>
                                        <th class="px-4 py-3 text-left font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    <?php $__empty_1 = true; $__currentLoopData = $bifDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <a href="<?php echo e(route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="font-medium text-blue-700 hover:underline">
                                                    <?php echo e($bif->title ?: 'Business Client Information Form'); ?>

                                                </a>
                                            </td>
                                            <td class="px-4 py-3"><?php echo e(ucfirst($bif->status)); ?></td>
                                            <td class="px-4 py-3"><?php echo e($bif->submitted_at ? $bif->submitted_at->format('F j, Y') : '-'); ?></td>
                                            <td class="px-4 py-3">
                                                <?php if($bif->status === 'draft'): ?>
                                                    Draft saved
                                                <?php elseif($bif->status === 'reviewed'): ?>
                                                    Reviewed by team
                                                <?php else: ?>
                                                    <?php echo e($bif->updated_at && $bif->updated_at->ne($bif->created_at) ? 'Updated after submission' : 'Submitted by user'); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="<?php echo e(route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\overview.blade.php ENDPATH**/ ?>