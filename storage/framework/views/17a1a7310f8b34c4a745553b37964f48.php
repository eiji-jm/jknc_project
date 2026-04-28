<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ activePreview: '<?php echo e($uploadedRequestUrl ? 'uploaded' : 'system'); ?>' }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Issuance Request Preview</div>
                <div class="text-xs text-gray-500"><?php echo e($requestRecord->reference_no); ?></div>
            </div>
            <div class="flex-1"></div>
            <div class="inline-flex rounded-full bg-gray-100 p-1">
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activePreview === 'uploaded' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activePreview = 'uploaded'">Uploaded Request</button>
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activePreview === 'system' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activePreview = 'system'">System Preview</button>
            </div>
            <?php if($requestRecord->status !== 'approved'): ?>
                <form method="POST" action="<?php echo e(route('stock-transfer-book.certificates.requests.approve', $requestRecord)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">Approve</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div x-show="activePreview === 'uploaded'" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Uploaded Request PDF</div>
                            <div class="text-xs text-slate-500">This window shows the actual request PDF uploaded from the issuance slider.</div>
                        </div>
                    </div>
                    <?php if($uploadedRequestUrl): ?>
                        <iframe src="<?php echo e($uploadedRequestUrl); ?>" class="mt-4 w-full h-[780px] border rounded bg-white"></iframe>
                    <?php else: ?>
                        <div class="mt-4 w-full h-[780px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                            No uploaded request PDF available yet.
                        </div>
                    <?php endif; ?>
                </div>

                <div x-show="activePreview === 'system'" class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">System Request Preview</div>
                            <div class="text-xs text-slate-500">This window shows the generated request sheet based on the saved issuance request details.</div>
                        </div>
                    </div>
                    <?php if($generatedPreviewUrl): ?>
                        <iframe src="<?php echo e($generatedPreviewUrl); ?>" class="mt-4 w-full h-[780px] border rounded bg-white"></iframe>
                    <?php else: ?>
                        <div class="mt-4 w-full h-[780px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                            No system-generated request preview available yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Request Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Reference</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->reference_no); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Requested At</span><div class="font-medium text-gray-900"><?php echo e(optional($requestRecord->requested_at)->format('M d, Y h:i A')); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Request Type</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->request_type); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">COS / CV</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->issuance_type); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Requester</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->requester); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Received By</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->received_by ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Issued By</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->issued_by ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="font-medium text-gray-900"><?php echo e(ucfirst($requestRecord->status)); ?></div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Selection</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Selected Stock</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->certificate?->stock_number ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Type</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->certificate?->certificate_type ?: $requestRecord->issuance_type); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stockholder</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->certificate?->stockholder_name ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Current Certificate Record</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->certificate?->certificate_type ?: '-'); ?> <?php echo e($requestRecord->certificate?->stock_number ?: ''); ?></div></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Approval Posting</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Approved At</span><div class="font-medium text-gray-900"><?php echo e(optional($requestRecord->approved_at)->format('M d, Y h:i A') ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Approved By</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->approved_by ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Journal</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->journal?->journal_no ?: '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Ledger</span><div class="font-medium text-gray-900">No ledger posting for issuance requests</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notes</span><div class="font-medium text-gray-900 whitespace-pre-line"><?php echo e($requestRecord->notes ?: '-'); ?></div></div>
                    </div>
                </div>

                <?php if($requestRecord->journal): ?>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Journal Reflection</div>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Particulars</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->journal->particulars); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Transaction Type</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->journal->transaction_type); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900"><?php echo e($requestRecord->journal->certificate_no); ?></div></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\issuance-request-preview.blade.php ENDPATH**/ ?>