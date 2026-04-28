<?php $__env->startSection('content'); ?>
<div id="townhall-edit-page" class="w-full h-full px-6 py-5" x-data="{
    previewRef: '<?php echo e($communication->ref_no); ?>',
    previewDate: <?php echo \Illuminate\Support\Js::from(old('communication_date', $communication->communication_date))->toHtml() ?>,
    previewFrom: <?php echo \Illuminate\Support\Js::from($communication->from_name)->toHtml() ?>,
    previewDepartment: <?php echo \Illuminate\Support\Js::from(old('department_stakeholder', $communication->department_stakeholder))->toHtml() ?>,
    previewRecipientLabel: <?php echo \Illuminate\Support\Js::from(old('recipient_label', $communication->recipient_label ?? 'To'))->toHtml() ?>,
    previewTo: <?php echo \Illuminate\Support\Js::from(old('to_for', $communication->to_for))->toHtml() ?>,
    previewPriority: <?php echo \Illuminate\Support\Js::from(old('priority', $communication->priority ?? 'Low'))->toHtml() ?>,
    previewSubject: <?php echo \Illuminate\Support\Js::from(old('subject', $communication->subject))->toHtml() ?>,
    previewBody: <?php echo \Illuminate\Support\Js::from(old('message', $communication->message ?: '<p style=&quot;color:#9ca3af;&quot;>Write the formal communication here...</p>'))->toHtml() ?>,
    previewCc: <?php echo \Illuminate\Support\Js::from(old('cc', $communication->cc))->toHtml() ?>,
    previewAdditional: <?php echo \Illuminate\Support\Js::from(old('additional', $communication->additional))->toHtml() ?>
}">

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($communication->approval_notes): ?>
        <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            <span class="font-semibold">Revision Note:</span> <?php echo e($communication->approval_notes); ?>

        </div>
    <?php endif; ?>

    <div class="flex gap-6 h-[calc(100vh-7rem)]">

        
        <div class="w-[70%] bg-[#f5f6f8] overflow-y-auto p-6 border border-gray-200 rounded-xl">
            <div class="max-w-[850px] mx-auto mb-4 flex justify-between items-center sticky top-0 z-10">
                <a href="<?php echo e(route('townhall.show', $communication->id)); ?>"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    ← Back to Memo
                </a>

                <button
                    type="button"
                    id="download-preview-pdf"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow transition"
                >
                    <i class="fas fa-file-pdf"></i>
                    Download Preview PDF
                </button>
            </div>

            <div class="max-w-[850px] mx-auto">
                <div id="memo-preview-pdf" class="memo-preview bg-white border border-gray-300 shadow min-h-[1100px] px-[72px] py-[72px]">

                    
                    <div class="flex items-start justify-between border-b border-gray-300 pb-6 mb-8">
                        <div>
                            <h1 class="text-[22px] font-bold tracking-wide text-gray-900">JOHN KELLY & COMPANY</h1>
                            <p class="text-[12px] text-gray-500 mt-1">Corporate Memorandum</p>
                        </div>

                        <div class="text-right text-[12px] text-gray-600 leading-5">
                            <p>Ref No: <span class="font-semibold" x-text="previewRef"></span></p>
                            <p>Date: <span class="font-semibold" x-text="previewDate || '________________'"></span></p>
                        </div>
                    </div>

                    
                    <div class="text-center mb-8">
                        <h2 class="text-[20px] font-bold tracking-[0.18em] text-gray-900">MEMORANDUM</h2>
                    </div>

                    
                    <div class="space-y-3 text-[14px] text-gray-800 mb-10">
                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <p class="font-semibold uppercase tracking-wide" x-text="previewRecipientLabel"></p>
                            <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewTo || '______________________________'"></p>
                        </div>

                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <p class="font-semibold uppercase tracking-wide">From</p>
                            <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewFrom || '______________________________'"></p>
                        </div>

                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <p class="font-semibold uppercase tracking-wide">Department</p>
                            <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewDepartment || '______________________________'"></p>
                        </div>

                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <p class="font-semibold uppercase tracking-wide">Priority</p>
                            <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewPriority || 'Low'"></p>
                        </div>

                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <p class="font-semibold uppercase tracking-wide">Subject</p>
                            <p class="border-b border-dotted border-gray-300 pb-1 font-semibold" x-text="previewSubject || '______________________________'"></p>
                        </div>
                    </div>

                    
                    <div class="text-[15px] leading-8 text-gray-900 min-h-[420px]">
                        <div class="prose prose-sm max-w-none [&_p]:my-4 [&_p]:leading-8 [&_ul]:my-4 [&_ol]:my-4" x-html="previewBody"></div>
                    </div>

                    
                    <div class="mt-16 space-y-10 text-[14px] text-gray-800">
                        <div>
                            <p>Respectfully,</p>
                            <div class="mt-12 border-b border-gray-400 w-[260px]"></div>
                            <p class="mt-2 font-semibold" x-text="previewFrom || '________________'"></p>
                        </div>

                        <div class="pt-6 border-t border-gray-200 space-y-2">
                            <p><span class="font-semibold">CC:</span> <span x-text="previewCc || '______________________________'"></span></p>
                            <p><span class="font-semibold">Additional:</span> <span x-text="previewAdditional || '______________________________'"></span></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        
        <div class="w-[30%] bg-white border border-gray-200 rounded-xl shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Edit Communication</h2>
                    <p class="text-xs text-gray-500 mt-1">Resubmit for approval after updating the memo.</p>
                </div>

                <a href="<?php echo e(route('townhall.show', $communication->id)); ?>"
                   class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="fas fa-times"></i>
                </a>
            </div>

            <form id="townhall-edit-form" action="<?php echo e(route('townhall.update', $communication->id)); ?>" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Ref #</label>
                        <input
                            type="text"
                            value="<?php echo e($communication->ref_no); ?>"
                            readonly
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
                        <input
                            type="date"
                            name="communication_date"
                            x-model="previewDate"
                            value="<?php echo e(old('communication_date', $communication->communication_date)); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
                        <input
                            type="text"
                            value="<?php echo e($communication->from_name); ?>"
                            x-model="previewFrom"
                            readonly
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                        >
                        <p class="mt-1 text-xs text-gray-400">Automatically set based on creator</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Department / Stakeholder</label>
                        <input
                            type="text"
                            name="department_stakeholder"
                            x-model="previewDepartment"
                            value="<?php echo e(old('department_stakeholder', $communication->department_stakeholder)); ?>"
                            placeholder="Enter department or stakeholder"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Recipient Label and Value</label>

                    <div class="grid grid-cols-[120px_1fr] gap-3">
                        <select
                            x-model="previewRecipientLabel"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                            <option value="To">To</option>
                            <option value="For">For</option>
                        </select>

                        <input
                            type="text"
                            name="to_for"
                            x-model="previewTo"
                            value="<?php echo e(old('to_for', $communication->to_for)); ?>"
                            placeholder="Enter recipient"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>

                    <input type="hidden" name="recipient_label" :value="previewRecipientLabel">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Priority</label>
                    <select
                        name="priority"
                        x-model="previewPriority"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                        <option value="Low">Low</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
                    <input
                        type="text"
                        name="subject"
                        x-model="previewSubject"
                        value="<?php echo e(old('subject', $communication->subject)); ?>"
                        placeholder="Enter subject"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Body</label>
                    <div id="editor"><?php echo old('message', $communication->message); ?></div>
                    <input type="hidden" name="message" id="message">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">CC</label>
                        <input
                            type="text"
                            name="cc"
                            x-model="previewCc"
                            value="<?php echo e(old('cc', $communication->cc)); ?>"
                            placeholder="Enter CC recipients"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Additional</label>
                        <input
                            type="text"
                            name="additional"
                            x-model="previewAdditional"
                            value="<?php echo e(old('additional', $communication->additional)); ?>"
                            placeholder="Optional"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Replace Attachment</label>
                    <input
                        type="file"
                        name="attachment"
                        accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-xs text-gray-400">
                        Leave blank to keep the current attachment.
                    </p>

                    <?php if($communication->attachment): ?>
                        <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                            Current attachment:
                            <a href="<?php echo e(asset('storage/' . $communication->attachment)); ?>" target="_blank" class="text-blue-600 hover:underline">
                                View current file
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="px-0 py-4 border-t border-gray-200 flex items-center gap-3">
                    <a
                        href="<?php echo e(route('townhall.show', $communication->id)); ?>"
                        class="flex-1 border border-gray-300 text-center text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                    >
                        Update & Resubmit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editorEl = document.getElementById('editor');
    const hiddenInput = document.getElementById('message');
    const form = document.getElementById('townhall-edit-form');

    if (editorEl && hiddenInput && form) {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write the formal communication here...',
            modules: {
                toolbar: [
                    [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        const existingMessage = <?php echo json_encode(old('message', $communication->message)); ?>;
        const rootEl = document.getElementById('townhall-edit-page');
        const alpineData = rootEl ? Alpine.$data(rootEl) : null;

        if (existingMessage) {
            quill.root.innerHTML = existingMessage;
            hiddenInput.value = existingMessage;

            if (alpineData) {
                alpineData.previewBody = existingMessage;
            }
        } else {
            const defaultHtml = '<p style="color:#9ca3af;">Write the formal communication here...</p>';
            quill.root.innerHTML = '';
            hiddenInput.value = '';

            if (alpineData) {
                alpineData.previewBody = defaultHtml;
            }
        }

        quill.on('text-change', function () {
            const html = quill.root.innerHTML;
            hiddenInput.value = html;

            if (alpineData) {
                alpineData.previewBody = quill.getText().trim()
                    ? html
                    : '<p style="color:#9ca3af;">Write the formal communication here...</p>';
            }
        });

        form.addEventListener('submit', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
    }

    const downloadBtn = document.getElementById('download-preview-pdf');

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            const element = document.getElementById('memo-preview-pdf');
            if (!element) return;

            const subject = document.querySelector('input[name="subject"]')?.value?.trim() || 'townhall-memo';
            const safeFileName = subject
                .replace(/[\\/:*?"<>|]+/g, '')
                .replace(/\s+/g, '-')
                .toLowerCase();

            html2pdf().set({
                margin: [0.3, 0.3, 0.3, 0.3],
                filename: `${safeFileName}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(element).save();
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\townhall\edit.blade.php ENDPATH**/ ?>