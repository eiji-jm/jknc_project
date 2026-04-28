<?php $__env->startSection('content'); ?>
<?php
    $attachmentItems = $transmittal->items->filter(fn($item) => !empty($item->attachment_path))->values();

    $firstAttachment = $attachmentItems->first();

    $firstAttachmentUrl = $firstAttachment ? asset('storage/' . $firstAttachment->attachment_path) : null;
    $firstAttachmentName = $firstAttachment ? basename($firstAttachment->attachment_path) : null;
    $firstAttachmentExt = $firstAttachment ? strtolower(pathinfo($firstAttachment->attachment_path, PATHINFO_EXTENSION)) : null;
?>

<div
    class="w-full h-full px-6 py-5"
    x-data="{
        activeTab: 'transmittal',
        selectedAttachmentUrl: <?php echo \Illuminate\Support\Js::from($firstAttachmentUrl)->toHtml() ?>,
        selectedAttachmentName: <?php echo \Illuminate\Support\Js::from($firstAttachmentName)->toHtml() ?>,
        selectedAttachmentExt: <?php echo \Illuminate\Support\Js::from($firstAttachmentExt)->toHtml() ?>,
        setAttachment(url, name, ext) {
            this.selectedAttachmentUrl = url;
            this.selectedAttachmentName = name;
            this.selectedAttachmentExt = ext;
        },
        isImage(ext) {
            return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes((ext || '').toLowerCase());
        },
        isPdf(ext) {
            return (ext || '').toLowerCase() === 'pdf';
        }
    }"
>
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[28px] font-semibold text-gray-800 leading-none">Transmittal Preview</h1>
                <p class="text-sm text-gray-500 mt-1"><?php echo e($transmittal->transmittal_no ?? 'N/A'); ?></p>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?php echo e(url('/transmittal')); ?>"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Back
                </a>
            </div>
        </div>

        <div class="p-5 bg-[#f8fafc]">
            <div class="grid grid-cols-1 2xl:grid-cols-[1fr_420px] gap-5">
                <div class="space-y-5">
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <!-- Tabs -->
                        <div class="px-4 pt-3 border-b border-gray-200 bg-white">
                            <div class="flex items-end gap-6">
                                <button
                                    type="button"
                                    @click="activeTab = 'transmittal'"
                                    :class="activeTab === 'transmittal'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Transmittal
                                </button>

                                <button
                                    type="button"
                                    @click="activeTab = 'receipt'"
                                    :class="activeTab === 'receipt'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Receipt
                                </button>

                                <button
                                    type="button"
                                    @click="activeTab = 'attachments'"
                                    :class="activeTab === 'attachments'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Attachments
                                </button>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-white flex items-center justify-between">
                            <div x-show="activeTab === 'transmittal'">
                                <h3 class="text-sm font-semibold text-gray-900">Transmittal Form Preview</h3>
                                <p class="text-xs text-gray-500"><?php echo e(ucfirst($transmittal->workflow_status ?? 'N/A')); ?></p>
                            </div>

                            <div x-show="activeTab === 'receipt'" x-cloak>
                                <h3 class="text-sm font-semibold text-gray-900">Receipt Preview</h3>
                                <p class="text-xs text-gray-500"><?php echo e(optional($transmittal->receipt)->receipt_no ?? 'No receipt number yet'); ?></p>
                            </div>

                            <div x-show="activeTab === 'attachments'" x-cloak>
                                <h3 class="text-sm font-semibold text-gray-900">Attachment Preview</h3>
                                <p class="text-xs text-gray-500">Uploaded file preview</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <a x-show="activeTab === 'transmittal'"
                                   href="<?php echo e($transmittalPdfUrl); ?>"
                                   target="_blank"
                                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                    Open
                                </a>

                                <?php if(!empty($receiptPdfUrl)): ?>
                                    <a x-show="activeTab === 'receipt'" x-cloak
                                       href="<?php echo e($receiptPdfUrl); ?>"
                                       target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                        Open
                                    </a>
                                <?php endif; ?>

                                <a x-show="activeTab === 'attachments' && selectedAttachmentUrl" x-cloak
                                   :href="selectedAttachmentUrl"
                                   target="_blank"
                                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                    Open
                                </a>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-4 bg-[#f3f4f6]">
                            <!-- Transmittal -->
                            <div x-show="activeTab === 'transmittal'">
                                <div class="pdf-preview-frame">
                                    <iframe
                                        src="<?php echo e($transmittalPdfUrl); ?>"
                                        class="w-full h-[920px] bg-white"
                                        frameborder="0">
                                    </iframe>
                                </div>
                            </div>

                            <!-- Receipt -->
                            <div x-show="activeTab === 'receipt'" x-cloak>
                                <?php if(!empty($receiptPdfUrl)): ?>
                                    <div class="pdf-preview-frame">
                                        <iframe
                                            src="<?php echo e($receiptPdfUrl); ?>"
                                            class="w-full h-[920px] bg-white"
                                            frameborder="0">
                                        </iframe>
                                    </div>
                                <?php else: ?>
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center text-sm text-gray-500">
                                        Receipt will appear here once the transmittal is approved.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Attachments -->
                            <div x-show="activeTab === 'attachments'" x-cloak>
                                <?php if($attachmentItems->count()): ?>
                                    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4">
                                        <!-- Left list -->
                                        <div class="space-y-3">
                                            <?php $__currentLoopData = $attachmentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $fileUrl = asset('storage/' . $item->attachment_path);
                                                    $fileName = basename($item->attachment_path);
                                                    $fileExt = strtolower(pathinfo($item->attachment_path, PATHINFO_EXTENSION));
                                                ?>

                                                <button
                                                    type="button"
                                                    @click="setAttachment('<?php echo e($fileUrl); ?>', '<?php echo e(addslashes($fileName)); ?>', '<?php echo e($fileExt); ?>')"
                                                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-left hover:border-blue-400 hover:bg-blue-50 transition"
                                                >
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        <?php echo e($item->particular ?: 'Item ' . $item->item_no); ?>

                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-500 break-all">
                                                        <?php echo e($fileName); ?>

                                                    </p>

                                                    <?php if(!empty($item->remarks)): ?>
                                                        <p class="mt-1 text-xs text-gray-400">
                                                            Remarks: <?php echo e($item->remarks); ?>

                                                        </p>
                                                    <?php endif; ?>

                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-600">
                                                            <?php echo e(strtoupper($fileExt)); ?>

                                                        </span>
                                                    </div>
                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        <!-- Right preview -->
                                        <div class="border border-gray-200 rounded-xl overflow-hidden bg-white min-h-[760px]">
                                            <div class="px-4 py-3 border-b border-gray-200 bg-white">
                                                <h4 class="text-sm font-semibold text-gray-900" x-text="selectedAttachmentName || 'Attachment Preview'"></h4>
                                            </div>

                                            <template x-if="selectedAttachmentUrl && isPdf(selectedAttachmentExt)">
                                                <iframe
                                                    :src="selectedAttachmentUrl"
                                                    class="w-full h-[700px] bg-white"
                                                    frameborder="0">
                                                </iframe>
                                            </template>

                                            <template x-if="selectedAttachmentUrl && isImage(selectedAttachmentExt)">
                                                <div class="h-[700px] flex items-center justify-center p-4 bg-[#f3f4f6]">
                                                    <img :src="selectedAttachmentUrl" class="max-w-full max-h-full object-contain rounded-lg border border-gray-200 bg-white">
                                                </div>
                                            </template>

                                            <template x-if="selectedAttachmentUrl && !isPdf(selectedAttachmentExt) && !isImage(selectedAttachmentExt)">
                                                <div class="h-[700px] flex items-center justify-center p-6 bg-[#f9fafb]">
                                                    <div class="text-center">
                                                        <p class="text-sm font-medium text-gray-800">This file type cannot be previewed directly.</p>
                                                        <p class="mt-1 text-xs text-gray-500" x-text="selectedAttachmentName"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center text-sm text-gray-500">
                                        No item attachments uploaded yet.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side -->
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Transmittal Information</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Ref No</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->transmittal_no ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Date</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->transmittal_date ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Mode</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->mode ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">From</span>
                                <span class="font-medium text-gray-900">
                                    <?php echo e($transmittal->mode === 'SEND' ? ($transmittal->office_name ?? 'N/A') : ($transmittal->party_name ?? 'N/A')); ?>

                                </span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">To</span>
                                <span class="font-medium text-gray-900">
                                    <?php echo e($transmittal->mode === 'SEND' ? ($transmittal->party_name ?? 'N/A') : ($transmittal->office_name ?? 'N/A')); ?>

                                </span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Address</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->address ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Delivery Type</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->delivery_type ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Recipient Email</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->recipient_email ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Workflow</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->workflow_status ?? 'N/A'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Approval</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->approval_status ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Receipt Information</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Receipt No</span>
                                <span class="font-medium text-gray-900"><?php echo e(optional($transmittal->receipt)->receipt_no ?? 'Not yet generated'); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Receipt File</span>
                                <span class="font-medium text-gray-900"><?php echo e(!empty($receiptPdfUrl) ? 'Available' : 'Not available'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Attachment Summary</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">With Files</span>
                                <span class="font-medium text-gray-900"><?php echo e($attachmentItems->count()); ?></span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Total Items</span>
                                <span class="font-medium text-gray-900"><?php echo e($transmittal->items->count()); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End right side -->
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .pdf-preview-frame {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\transmittal\preview.blade.php ENDPATH**/ ?>