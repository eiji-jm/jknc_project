<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ activeVersion: '<?php echo e($approvedUrl ? 'approved' : 'draft'); ?>', selectedDraftUrl: <?php echo \Illuminate\Support\Js::from($selectedDraftUrl)->toHtml() ?> }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">NatGov Preview</div>
                <div class="text-xs text-gray-500">Company <?php echo e($natgov->client ?? '-'); ?></div>
            </div>
            <div class="flex-1"></div>
            <div class="inline-flex rounded-full bg-gray-100 p-1">
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'draft' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'draft'">Draft</button>
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'approved' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'approved'">Approved</button>
            </div>
            <a href="<?php echo e($editRoute); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <form method="POST" action="<?php echo e($deleteRoute); ?>" onsubmit="return confirm('Delete this NatGov entry?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Delete</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div x-show="activeVersion === 'draft'" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Draft NatGov File</div>
                            <div class="text-xs text-slate-500">Choose any saved draft revision to review it here.</div>
                        </div>
                    </div>
                    <?php if(!empty($draftOptions) && count($draftOptions) > 1): ?>
                        <div class="mt-3">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Draft Revision Selector</label>
                            <select x-model="selectedDraftUrl" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                                <?php $__currentLoopData = $draftOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option['url']); ?>">
                                        <?php echo e($option['label']); ?><?php if($option['uploaded_at']): ?> • <?php echo e($option['uploaded_at']); ?><?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if($latestDraft): ?>
                        <div class="mt-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600">
                            Latest draft: <span class="font-semibold text-slate-900"><?php echo e($latestDraft['name'] ?? basename($latestDraft['path'])); ?></span>
                            <?php if(!empty($latestDraft['uploaded_at'])): ?>
                                <span class="text-slate-400">• <?php echo e($latestDraft['uploaded_at']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($draftUrl): ?>
                        <iframe :src="selectedDraftUrl" class="mt-4 w-full h-[700px] border rounded bg-white"></iframe>
                    <?php else: ?>
                        <div class="mt-4 w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">No draft file available yet.</div>
                    <?php endif; ?>
                </div>

                <div x-show="activeVersion === 'approved'" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Approved NatGov File</div>
                            <div class="text-xs text-slate-500">Only the latest uploaded approved file appears here even if multiple approved PDFs were saved.</div>
                        </div>
                    </div>
                    <?php if($latestApproved): ?>
                        <div class="mt-3 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs text-emerald-700">
                            Latest approved: <span class="font-semibold text-slate-900"><?php echo e($latestApproved['name'] ?? basename($latestApproved['path'])); ?></span>
                            <?php if(!empty($latestApproved['uploaded_at'])): ?>
                                <span class="text-slate-400">• <?php echo e($latestApproved['uploaded_at']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($approvedUrl): ?>
                        <iframe src="<?php echo e($approvedUrl); ?>" class="mt-4 w-full h-[700px] border rounded bg-white"></iframe>
                    <?php else: ?>
                        <div class="mt-4 w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">No approved file uploaded yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">NatGov Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Company</span><div class="font-medium text-gray-900"><?php echo e($natgov->client ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900"><?php echo e($natgov->tin ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Agency</span><div class="font-medium text-gray-900"><?php echo e($natgov->agency ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Status</span><div class="font-medium text-gray-900"><?php echo e($natgov->registration_status ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Date</span><div class="font-medium text-gray-900"><?php echo e(optional($natgov->registration_date)->format('M d, Y') ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Deadline</span><div class="font-medium text-gray-900"><?php echo e(optional($natgov->deadline_date)->format('M d, Y') ?? '-'); ?></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="font-medium text-gray-900"><?php echo e($natgov->display_status); ?></div></div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900">Add More Draft Files</div>
                    <div class="mt-1 text-xs text-gray-500">You can keep uploading more draft revisions. Users can switch between them from the selector above.</div>
                    <form method="POST" action="<?php echo e($updateRoute); ?>" enctype="multipart/form-data" class="mt-4 space-y-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="file" name="document_paths[]" accept="application/pdf" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-700 file:text-white hover:file:bg-slate-800">
                        <button type="submit" class="w-full rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Upload Draft Revision<?php echo e($draftDocuments->count() === 1 ? '' : 's'); ?>

                        </button>
                    </form>
                    <?php if($draftDocuments->isNotEmpty()): ?>
                        <div class="mt-3 text-xs text-gray-500">Draft files saved: <?php echo e($draftDocuments->count()); ?></div>
                    <?php endif; ?>
                </div>

                <div class="bg-white border border-emerald-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900">Upload Approved File Later</div>
                    <div class="mt-1 text-xs text-gray-500">Use this when approved PDFs become available. The newest approved PDF becomes the visible approved preview.</div>
                    <form method="POST" action="<?php echo e($updateRoute); ?>" enctype="multipart/form-data" class="mt-4 space-y-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="file" name="approved_document_paths[]" accept="application/pdf" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            <?php echo e($natgov->approved_document_path ? 'Upload Approved Revision' : 'Upload Approved File'); ?>

                        </button>
                    </form>
                    <?php if($approvedDocuments->isNotEmpty()): ?>
                        <div class="mt-3 text-xs text-gray-500">Approved files saved: <?php echo e($approvedDocuments->count()); ?></div>
                    <?php endif; ?>
                </div>

                <div class="bg-white border border-amber-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900">Authority Notes</div>
                    <div class="mt-1 text-xs text-gray-500">Each note is saved separately with its own visibility permission. Users will only see notes allowed for their role.</div>
                    <form method="POST" action="<?php echo e(route('natgov.notes.store', $natgov)); ?>" class="mt-4 space-y-3">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="text-xs text-gray-600">Visible To Role</label>
                            <select name="visible_to_role" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                <option value="Admin">Admin</option>
                                <option value="Employee">Employee</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">New Note</label>
                            <textarea name="body" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Write registration notes, follow-ups, or authority-specific remarks here..."></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Send Note</button>
                    </form>

                    <div class="mt-4 space-y-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Visible Note List</div>
                        <?php $__empty_1 = true; $__currentLoopData = ($visibleAuthorityNotes ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-lg border border-amber-100 bg-amber-50 px-3 py-3">
                                <div class="flex items-center justify-between gap-3 text-[11px] text-gray-500">
                                    <div>
                                        <span class="font-semibold text-gray-800"><?php echo e($note->user?->name ?? 'Unknown User'); ?></span>
                                        <span>(<?php echo e($note->user?->role ?? 'No Role'); ?>)</span>
                                    </div>
                                    <div><?php echo e(optional($note->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A')); ?></div>
                                </div>
                                <div class="mt-1 text-[11px] uppercase tracking-wide text-amber-700">Visible to <?php echo e($note->visible_to_role); ?></div>
                                <div class="mt-2 whitespace-pre-line text-sm text-gray-900"><?php echo e($note->body); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-lg border border-dashed border-gray-300 px-3 py-4 text-sm text-gray-500">
                                No visible authority notes yet for your role.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\natgov\preview.blade.php ENDPATH**/ ?>