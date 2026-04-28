<?php $__env->startSection('content'); ?>
<?php
    $canReviewKyc = in_array((string) (auth()->user()->role ?? ''), ['Admin', 'SuperAdmin'], true);
    $requiresAdminApprovalRequest = ! $canReviewKyc && (string) ($bif->status ?? '') === 'approved';
?>
<style>
    .bif-edit-canvas {
        overflow-x: auto;
    }

    @media screen and (min-width: 1024px) {
        .bif-edit-canvas .bif-sheet {
            zoom: 1.14;
        }
    }
</style>
<div class="mt-4 w-full px-4 pb-8 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-md border border-gray-100 bg-white">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="<?php echo e(route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Business Client Information Form</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">Edit Business Client Information Form</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <div class="flex flex-wrap items-start justify-between gap-3 px-4 pt-4">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Business Client Information Form</h1>
                        <p class="mt-1 text-sm text-gray-500">Update the business client information details for <?php echo e($company->company_name); ?> before final approval.</p>
                    </div>
                    <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        <?php echo e($statusLabels[$bif->status] ?? ucfirst(str_replace('_', ' ', $bif->status))); ?>

                    </span>
                </div>

                <?php if($errors->any()): ?>
                    <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        Please review the BIF fields and correct the highlighted errors.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('company.bif.update', ['company' => $company->id, 'bif' => $bif->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="bg-gray-50 p-4 sm:p-6">
                        <div class="mx-auto max-w-[1120px]">
                            <div class="bif-edit-canvas">
                                <?php echo $__env->make('company.bif.partials.form-fields', ['bif' => $bif], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                            <?php if($requiresAdminApprovalRequest): ?>
                                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-4">
                                    <label for="change_request_note" class="mb-2 block text-sm font-semibold text-amber-900">Change Request Note</label>
                                    <textarea id="change_request_note" name="change_request_note" rows="3" class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100" placeholder="Explain what was changed and why this needs admin approval."><?php echo e(old('change_request_note', $bif->change_request_note ?? '')); ?></textarea>
                                    <p class="mt-2 text-xs text-amber-800">Your edits will be submitted as a request and will only apply after admin approval.</p>
                                    <?php $__errorArgs = ['change_request_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 px-4 py-4">
                        <a href="<?php echo e(route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="inline-flex h-10 min-w-[100px] items-center justify-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <?php if($canReviewKyc): ?>
                            <button type="submit" name="action" value="draft" class="h-10 min-w-[120px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Save Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="h-10 min-w-[140px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                Return to Approval
                            </button>
                        <?php elseif($requiresAdminApprovalRequest): ?>
                            <button type="submit" name="action" value="submit" class="h-10 min-w-[220px] rounded-full bg-amber-600 px-4 text-sm font-medium text-white hover:bg-amber-700">
                                Request Admin Approval
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action" value="draft" class="h-10 min-w-[120px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Save Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="h-10 min-w-[140px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                Save Changes
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\bif\edit.blade.php ENDPATH**/ ?>