<?php $__env->startSection('content'); ?>

<?php
    $draftUrl = !empty($record->file_path) ? asset(ltrim($record->file_path, '/')) : null;
    $notaryUrl = !empty($record->notary_file_path) ? asset(ltrim($record->notary_file_path, '/')) : null;
    $canEditRecord = in_array($record->workflow_status, ['Uploaded', 'Reverted']);
?>

<div class="w-full px-6 py-6"
     x-data="{
        fileTab: '<?php echo e($draftUrl ? 'draft' : ($notaryUrl ? 'notary' : 'draft')); ?>',
        editDraft: false,
        editNotary: false,
        editDetails: false
     }">

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <h1 class="text-2xl font-semibold mb-6">
        SEC – Certificate Of Incorporation
    </h1>

    <div class="grid grid-cols-3 gap-6">

        <!-- FILE VIEWER -->
        <div class="col-span-2 bg-white border rounded-lg p-4">

            <div class="flex items-center gap-6 border-b border-gray-200 mb-4">
                <button
                    @click="fileTab = 'draft'"
                    :class="fileTab === 'draft' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Draft
                </button>

                <button
                    @click="fileTab = 'notary'"
                    :class="fileTab === 'notary' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Notary
                </button>
            </div>

            <div x-show="fileTab === 'draft'">
                <?php if($draftUrl): ?>
                    <iframe
                        src="<?php echo e($draftUrl); ?>"
                        class="w-full h-[700px] border rounded">
                    </iframe>
                <?php else: ?>
                    <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                        No draft file attached for this SEC-COI record.
                    </div>
                <?php endif; ?>
            </div>

            <div x-show="fileTab === 'notary'" x-cloak>
                <?php if($notaryUrl): ?>
                    <iframe
                        src="<?php echo e($notaryUrl); ?>"
                        class="w-full h-[700px] border rounded">
                    </iframe>
                <?php else: ?>
                    <div class="w-full h-[700px] border rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400 text-sm px-6 text-center">
                        <i class="far fa-file-alt text-4xl mb-4"></i>
                        <p class="font-medium text-gray-500 mb-1">No notarized file attached yet.</p>
                        <p class="text-gray-400">This section is reserved for the final notarized document.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- INFORMATION PANEL -->
        <div class="bg-white border rounded-lg p-6 space-y-4">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">
                    Certificate Information
                </h2>

                <?php if($canEditRecord): ?>
                    <button
                        type="button"
                        @click="editDetails = !editDetails"
                        class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        <span x-text="editDetails ? 'Cancel' : 'Edit Details'"></span>
                    </button>
                <?php endif; ?>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Corporation</span>
                <span class="font-medium text-right"><?php echo e($record->corporate_name); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Company Reg No.</span>
                <span class="text-right"><?php echo e($record->company_reg_no); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Date Upload</span>
                <span class="text-right"><?php echo e($record->date_upload); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Date Created</span>
                <span class="text-right"><?php echo e($record->created_at ? $record->created_at->format('M d, Y') : ''); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Issued On</span>
                <span class="text-right"><?php echo e($record->issued_on); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Issued By</span>
                <span class="text-right"><?php echo e($record->issued_by); ?></span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Approval Status</span>
                <span class="text-right">
                    <?php
                        $status = $record->approval_status ?? 'Pending';
                        $badgeClass = match($status) {
                            'Approved' => 'bg-green-50 text-green-700',
                            'Needs Revision' => 'bg-yellow-50 text-yellow-700',
                            'Rejected' => 'bg-red-50 text-red-700',
                            default => 'bg-blue-50 text-blue-700',
                        };
                    ?>

                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo e($badgeClass); ?>">
                        <?php echo e($status); ?>

                    </span>
                </span>
            </div>

            <?php if($canEditRecord): ?>
                <form x-show="editDetails" x-cloak
                      action="<?php echo e(route('corporate.formation.update', $record->id)); ?>"
                      method="POST"
                      class="space-y-3 pt-4 border-t border-gray-100">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <input type="text"
                           name="corporate_name"
                           value="<?php echo e($record->corporate_name); ?>"
                           placeholder="Corporate Name"
                           class="w-full border rounded p-2"
                           required>

                    <input type="text"
                           name="company_reg_no"
                           value="<?php echo e($record->company_reg_no); ?>"
                           placeholder="Company Reg No."
                           class="w-full border rounded p-2"
                           required>

                    <input type="text"
                           name="issued_by"
                           value="<?php echo e($record->issued_by); ?>"
                           placeholder="Issued By"
                           class="w-full border rounded p-2"
                           required>

                    <input type="date"
                           name="issued_on"
                           value="<?php echo e($record->issued_on); ?>"
                           class="w-full border rounded p-2"
                           required>

                    <input type="date"
                           name="date_upload"
                           value="<?php echo e($record->date_upload); ?>"
                           class="w-full border rounded p-2"
                           required>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                        Save Details
                    </button>
                </form>
            <?php endif; ?>

            <!-- DRAFT FILE -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Draft File
                    </h3>

                    <?php if($canEditRecord && $draftUrl): ?>
                        <button
                            type="button"
                            @click="editDraft = !editDraft"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editDraft ? 'Cancel' : 'Edit'"></span>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if($draftUrl): ?>
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Draft file attached</p>
                            <p class="text-xs text-gray-400"><?php echo e(basename($record->file_path)); ?></p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'draft'"
                            class="text-xs px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            View
                        </button>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No draft file attached yet.
                    </div>
                <?php endif; ?>

                <?php if($canEditRecord): ?>
                    <form x-show="editDraft || !<?php echo e($draftUrl ? 'true' : 'false'); ?>" x-cloak
                          action="<?php echo e(route('corporate.formation.upload.draft', $record->id)); ?>"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        <?php echo csrf_field(); ?>

                        <input type="file" name="draft_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                            Save Draft File
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- NOTARY FILE -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Notary File
                    </h3>

                    <?php if($canEditRecord && $notaryUrl): ?>
                        <button
                            type="button"
                            @click="editNotary = !editNotary"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editNotary ? 'Cancel' : 'Edit'"></span>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if($notaryUrl): ?>
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Notary file attached</p>
                            <p class="text-xs text-gray-400"><?php echo e(basename($record->notary_file_path)); ?></p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'notary'"
                            class="text-xs px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700">
                            View
                        </button>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No notary file attached yet.
                    </div>
                <?php endif; ?>

                <?php if($canEditRecord): ?>
                    <form x-show="editNotary || !<?php echo e($notaryUrl ? 'true' : 'false'); ?>" x-cloak
                          action="<?php echo e(route('corporate.formation.upload.notary', $record->id)); ?>"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        <?php echo csrf_field(); ?>

                        <input type="file" name="notary_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                            Save Notary File
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\sec-coi-preview.blade.php ENDPATH**/ ?>