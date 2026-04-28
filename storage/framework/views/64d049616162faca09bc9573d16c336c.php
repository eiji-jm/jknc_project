<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{
        openPanel: false,
        statusTab: null
     }">

<div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
        <?php echo $__env->make('corporate.partials.section-ribbon', ['activeTab' => 'formation', 'topButtonLabel' => 'SEC-COI'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="px-4 pt-4 bg-white border-b border-gray-100">
        <div class="flex gap-8 text-[15px] text-gray-700 overflow-x-auto">

            <button
                @click="statusTab = 'uploaded'"
                :class="statusTab === 'uploaded' ? 'text-green-800 border-b-[3px] border-green-800 font-medium' : 'text-gray-700'"
                class="pb-3 whitespace-nowrap">
                Uploaded
            </button>

            <button
                @click="statusTab = 'submitted'"
                :class="statusTab === 'submitted' ? 'text-green-800 border-b-[3px] border-green-800 font-medium' : 'text-gray-700'"
                class="pb-3 whitespace-nowrap">
                Submitted
            </button>

            <button
                @click="statusTab = 'accepted'"
                :class="statusTab === 'accepted' ? 'text-green-800 border-b-[3px] border-green-800 font-medium' : 'text-gray-700'"
                class="pb-3 whitespace-nowrap">
                Accepted
            </button>

            <button
                @click="statusTab = 'reverted'"
                :class="statusTab === 'reverted' ? 'text-green-800 border-b-[3px] border-green-800 font-medium' : 'text-gray-700'"
                class="pb-3 whitespace-nowrap">
                Reverted
            </button>

            <button
                @click="statusTab = 'archived'"
                :class="statusTab === 'archived' ? 'text-green-800 border-b-[3px] border-green-800 font-medium' : 'text-gray-700'"
                class="pb-3 whitespace-nowrap">
                Archived
            </button>
        </div>
    </div>

    <div class="bg-gray-50 min-h-[680px]">

        <div class="px-4 pt-4">
            <div class="border border-green-200 bg-green-50 text-green-800 text-[14px] px-4 py-3 rounded-md"
                 x-show="statusTab === null || statusTab === 'accepted'">
                These records were already accepted and approved.
            </div>

            <div class="border border-green-200 bg-green-50 text-green-800 text-[14px] px-4 py-3 rounded-md"
                 x-show="statusTab === 'uploaded'">
                These records are uploaded drafts and not yet submitted for approval.
            </div>

            <div class="border border-blue-200 bg-blue-50 text-blue-800 text-[14px] px-4 py-3 rounded-md"
                 x-show="statusTab === 'submitted'">
                These records have already been submitted and are waiting for review.
            </div>

            <div class="border border-yellow-200 bg-yellow-50 text-yellow-800 text-[14px] px-4 py-3 rounded-md"
                 x-show="statusTab === 'reverted'">
                These records were reverted and need correction before resubmission.
            </div>

            <div class="border border-gray-200 bg-gray-50 text-gray-700 text-[14px] px-4 py-3 rounded-md"
                 x-show="statusTab === 'archived'">
                These records are archived for reference.
            </div>
        </div>

        <div class="p-3">
            <div class="overflow-x-auto border border-gray-200 rounded-md bg-white">

                <table class="min-w-full text-[11px] text-left text-gray-700">
                    <thead class="bg-white border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 font-semibold">Date Upload</th>
                            <th class="px-3 py-2 font-semibold">Date Created</th>
                            <th class="px-3 py-2 font-semibold">Company Reg No.</th>
                            <th class="px-3 py-2 font-semibold">Corporation Name</th>
                            <th class="px-3 py-2 font-semibold">Issued On</th>
                            <th class="px-3 py-2 font-semibold">Issued By</th>
                            <th class="px-3 py-2 font-semibold">Workflow Status</th>
                            <th class="px-3 py-2 font-semibold">Files</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $workflow = $row->workflow_status;

                                if (!$workflow) {
                                    if ($row->approval_status === 'Approved') {
                                        $workflow = 'Accepted';
                                    } elseif ($row->approval_status === 'Needs Revision' || $row->approval_status === 'Rejected') {
                                        $workflow = 'Reverted';
                                    } else {
                                        $workflow = 'Uploaded';
                                    }
                                }

                                $showInUploaded = $workflow === 'Uploaded';
                                $showInSubmitted = $workflow === 'Submitted';
                                $showInAccepted = $workflow === 'Accepted';
                                $showInReverted = $workflow === 'Reverted';
                                $showInArchived = $workflow === 'Archived';

                                $hasDraft = !empty($row->file_path);
                                $hasNotary = !empty($row->notary_file_path);
                                $canSubmit = $hasDraft && $hasNotary;

                                $fileLabel = match(true) {
                                    $hasDraft && $hasNotary => 'Draft + Notary',
                                    $hasDraft => 'Draft Only',
                                    $hasNotary => 'Notary Only',
                                    default => 'No File',
                                };

                                $badgeClass = match($workflow) {
                                    'Accepted' => 'bg-green-50 text-green-700',
                                    'Reverted' => 'bg-yellow-50 text-yellow-700',
                                    'Archived' => 'bg-gray-100 text-gray-700',
                                    'Submitted' => 'bg-blue-50 text-blue-700',
                                    default => 'bg-orange-50 text-orange-700',
                                };
                            ?>

                            <tr
                                x-show="
                                    (statusTab === null && <?php echo e($showInAccepted ? 'true' : 'false'); ?>) ||
                                    (statusTab === 'uploaded' && <?php echo e($showInUploaded ? 'true' : 'false'); ?>) ||
                                    (statusTab === 'submitted' && <?php echo e($showInSubmitted ? 'true' : 'false'); ?>) ||
                                    (statusTab === 'accepted' && <?php echo e($showInAccepted ? 'true' : 'false'); ?>) ||
                                    (statusTab === 'reverted' && <?php echo e($showInReverted ? 'true' : 'false'); ?>) ||
                                    (statusTab === 'archived' && <?php echo e($showInArchived ? 'true' : 'false'); ?>)
                                "
                                data-url="<?php echo e(route('corporate.formation.show', $row->id)); ?>"
                                onclick="window.location.href=this.dataset.url"
                                class="border-b border-gray-200 hover:bg-blue-50 transition cursor-pointer">

                                <td class="px-3 py-2"><?php echo e($row->date_upload); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->created_at->format('M d, Y')); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->company_reg_no); ?></td>
                                <td class="px-3 py-2 font-semibold text-gray-800"><?php echo e($row->corporate_name); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->issued_on); ?></td>
                                <td class="px-3 py-2"><?php echo e($row->issued_by); ?></td>

                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-medium <?php echo e($badgeClass); ?>">
                                        <?php echo e($workflow); ?>

                                    </span>
                                </td>

                                <td class="px-3 py-2 text-blue-600 font-medium">
                                    <div class="flex flex-col items-start gap-2">
                                        <span><?php echo e($fileLabel); ?></span>

                                        <?php if($workflow === 'Uploaded' || $workflow === 'Reverted'): ?>
                                            <?php if($canSubmit): ?>
                                                <form action="<?php echo e(route('corporate.formation.submit', $row->id)); ?>"
                                                      method="POST"
                                                      onclick="event.stopPropagation();">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit"
                                                            class="px-3 py-1.5 text-xs rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                                        Submit
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button"
                                                        onclick="event.stopPropagation();"
                                                        disabled
                                                        title="Both Draft and Notary files are required before submitting"
                                                        class="px-3 py-1.5 text-xs rounded-md bg-gray-200 text-gray-500 cursor-not-allowed">
                                                    Incomplete
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if(count($records) === 0): ?>
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-gray-400">
                                    No records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<div
    x-show="openPanel"
    x-transition.opacity
    class="fixed inset-0 z-[70] bg-black/35"
    style="display:none;"
    @click="openPanel = false">
</div>

<div
    x-show="openPanel"
    x-transition:enter="transform transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 bottom-0 z-[80] w-[430px] bg-white border-l border-gray-300 shadow-2xl"
    style="display:none;">

    <form action="<?php echo e(route('corporate.formation.store')); ?>" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
        <?php echo csrf_field(); ?>

        <div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-[26px] font-semibold text-gray-900 leading-none">
                Add SEC-COI Record
            </h2>

            <button
                type="button"
                @click="openPanel = false"
                class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6">

            <div class="space-y-5">

                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-2">Corporate Name</label>
                    <input type="text" name="corporate_name" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                </div>

                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-2">Company Reg No.</label>
                    <input type="text" name="company_reg_no" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                </div>

                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-2">Issued By</label>
                    <input type="text" name="issued_by" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Issued On</label>
                        <input type="date" name="issued_on" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Date Upload</label>
                        <input type="date" name="date_upload" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-[13px] font-medium text-gray-700 mb-2">Draft File Upload</label>

                    <label class="w-full min-h-[84px] border border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center gap-2 px-4 cursor-pointer transition">
                        <i class="far fa-file-alt text-[26px] text-gray-500"></i>
                        <span class="text-[14px] text-blue-600 font-medium">Choose draft file</span>
                        <span class="text-[11px] text-gray-400">Optional • PDF, DOC, DOCX supported</span>
                        <input type="file" name="draft_file_upload" class="hidden">
                    </label>
                </div>

                <div class="pt-2">
                    <label class="block text-[13px] font-medium text-gray-700 mb-2">Notary File Upload</label>

                    <label class="w-full min-h-[84px] border border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center gap-2 px-4 cursor-pointer transition">
                        <i class="far fa-file-alt text-[26px] text-gray-500"></i>
                        <span class="text-[14px] text-blue-600 font-medium">Choose notary file</span>
                        <span class="text-[11px] text-gray-400">Optional • PDF, DOC, DOCX supported</span>
                        <input type="file" name="notary_file_upload" class="hidden">
                    </label>
                </div>

            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button
                type="button"
                @click="openPanel = false"
                class="min-w-[92px] px-6 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                Cancel
            </button>

            <button
                type="submit"
                class="min-w-[92px] px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                Save
            </button>
        </div>

    </form>

</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\corporate-formation.blade.php ENDPATH**/ ?>