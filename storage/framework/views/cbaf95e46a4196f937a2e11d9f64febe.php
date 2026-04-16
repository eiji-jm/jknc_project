<?php $__env->startSection('content'); ?>
<div id="policy-page" class="w-full h-full bg-[#f8f8f8]" x-data="{
    showSlideOver: false,
    previewPolicy: '',
    previewVersion: '1.0',
    previewDate: '',
    previewPrepared: '<?php echo e(Auth::user()->name); ?>',
    previewReviewed: '',
    previewApproved: '',
    previewClassification: 'Internal Use',
    previewBody: '<p style=&quot;color:#9ca3af;&quot;>Define the policy scope and rules here...</p>'
}">

    <div class="w-full px-6 py-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">Policies</h1>
                <p class="text-xs text-gray-500">Create and manage corporate governance documents</p>
            </div>

            <button
                type="button"
                @click="showSlideOver = true; $nextTick(() => { initQuill(); updatePreview(); });"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i>
                <span>Add Policy</span>
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto no-scrollbar">
                <table class="min-w-[1400px] w-full border-collapse text-sm text-gray-700">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[140px]">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[140px]">Code</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[100px]">Version</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[150px]">Effectivity</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[150px]">Prepared by</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[150px]">Reviewed by</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[150px]">Approved by</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-r border-gray-100 w-[160px]">Classification</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 w-[120px]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = ($policies ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->status ?? 'Draft'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100 font-mono text-xs"><?php echo e($policy->code ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->version ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->effectivity_date ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->prepared_by ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->reviewed_by ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->approved_by ?? '-'); ?></td>
                                <td class="px-4 py-3 border-r border-gray-100"><?php echo e($policy->classification ?? '-'); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"><i class="far fa-eye"></i></button>
                                        <button class="p-1.5 text-gray-400 hover:bg-gray-100 rounded transition"><i class="far fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php for($i = 0; $i < 10; $i++): ?>
                                <tr class="border-b border-gray-50">
                                    <?php for($j = 0; $j < 9; $j++): ?>
                                        <td class="px-4 py-4 border-r border-gray-50">&nbsp;</td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-[60] overflow-hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showSlideOver = false"></div>

        <div class="absolute inset-0 flex">
            <div
                x-show="showSlideOver"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="w-[70%] h-full bg-[#f5f6f8] flex flex-col p-6 border-r border-gray-200"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        Live Policy Preview
                    </h3>
                    <div id="syncIndicator" class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold uppercase hidden">Synced</div>
                </div>

                <div class="flex-1 bg-white border border-gray-300 shadow-xl rounded-xl overflow-hidden relative">
                    <div id="pdfLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 hidden">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs text-gray-500 font-medium">Updating Document...</p>
                        </div>
                    </div>
                    <iframe id="pdfPreview" class="w-full h-full border-none"></iframe>
                </div>
            </div>

            <div
                x-show="showSlideOver"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-[30%] h-full bg-white shadow-2xl flex flex-col"
            >
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">New Policy Details</h2>
                    <button @click="showSlideOver = false" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="policyFormSubmit" method="POST" action="<?php echo e(route('policies.store')); ?>" class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" name="code" value="AUTO-GENERATED">

                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Policy Title</label>
                        <input type="text" name="policy" x-model="previewPolicy" placeholder="e.g. Employee Conduct Policy"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" oninput="debouncedPreview()">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Version</label>
                            <input type="text" name="version" x-model="previewVersion" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" oninput="debouncedPreview()">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Classification</label>
                            <select name="classification" x-model="previewClassification" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" onchange="debouncedPreview()">
                                <option value="Confidential">Confidential</option>
                                <option value="Internal Use">Internal Use</option>
                                <option value="Public">Public</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Effectivity Date</label>
                        <input type="date" name="effectivity_date" x-model="previewDate" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" oninput="debouncedPreview()">
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Prepared By</label>
                            <input type="text" name="prepared_by" x-model="previewPrepared" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Reviewed By</label>
                            <input type="text" name="reviewed_by" x-model="previewReviewed" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" oninput="debouncedPreview()">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Approved By</label>
                            <input type="text" name="approved_by" x-model="previewApproved" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" oninput="debouncedPreview()">
                        </div>
                    </div>

                    
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Policy Description</label>
                        <div class="rounded-xl border border-gray-300 overflow-hidden shadow-sm">
                            <div id="policy-editor"></div>
                        </div>
                        <input type="hidden" name="description" id="description-input">
                    </div>

                    <div class="pt-6 border-t border-gray-200 flex items-center gap-3 bg-white sticky bottom-0">
                        <button type="button" @click="showSlideOver = false" class="flex-1 border border-gray-300 text-gray-700 rounded-xl py-2.5 text-sm font-semibold hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-blue-600 text-white rounded-xl py-2.5 text-sm font-bold hover:bg-blue-700 shadow-md transition">
                            Save Policy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.css" rel="stylesheet">
<style>
    #policy-editor {
        min-height: 300px;
        font-size: 14px;
        background: white;
    }
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #e5e7eb !important;
        background: #f9fafb;
    }
    .ql-container.ql-snow {
        border: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.js"></script>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.js"></script>
<script>
    let quill;
    let previewTimeout = null;

    function initQuill() {
        if (quill) return;
        Quill.register({'modules/table-better': QuillTableBetter}, true);

        quill = new Quill('#policy-editor', {
            theme: 'snow',
            placeholder: 'Define policy...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['table-better'],
                    ['clean']
                ],
                table: false,
                'table-better': {
                    language: 'en_US',
                    menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'copy', 'delete'],
                    toolbarTable: true
                }
            }
        });

        quill.on('text-change', () => {
            document.getElementById('description-input').value = quill.root.innerHTML;
            debouncedPreview();
        });
    }

    function debouncedPreview() {
        clearTimeout(previewTimeout);
        document.getElementById('pdfLoading')?.classList.remove('hidden');
        previewTimeout = setTimeout(updatePreview, 800);
    }

    function updatePreview() {
        const form = document.getElementById('policyFormSubmit');
        const formData = new FormData(form);

        // Update the form data with the actual Quill content
        if (quill) {
            formData.set('description', quill.root.innerHTML);
        }

        const params = new URLSearchParams(formData);
        const pdfUrl = `<?php echo e(route('policies.preview')); ?>?${params.toString()}`;
        document.getElementById('pdfPreview').src = pdfUrl;

        document.getElementById('pdfPreview').onload = () => {
            document.getElementById('pdfLoading')?.classList.add('hidden');
        };
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/policies/policies.blade.php ENDPATH**/ ?>