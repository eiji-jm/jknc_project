<?php $__env->startSection('content'); ?>
<div class="bg-[#f5f6f8] min-h-screen p-6">
    <div class="max-w-[1400px] mx-auto flex gap-6">

        <div class="w-[70%] h-[calc(100vh-80px)] overflow-y-auto pr-2">
            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('admin.policies.index')); ?>"
                   class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                    ← Back
                </a>
            </div>

            <div class="flex justify-center">
                <div class="policy-paper bg-white border border-gray-300 shadow mb-6">

                    <div class="letterhead">
                        <p class="company-name">John Kelly &amp; Company</p>
                        <p class="company-subtitle">Enterprise Operating System | Corporate Policy</p>
                    </div>

                    <div class="policy-title">
                        <?php echo e($policy->policy ?: 'NEW POLICY DOCUMENT'); ?>

                    </div>

                    <table class="info-table">
                        <tr>
                            <td class="label">Document Code</td>
                            <td><?php echo e($policy->code ?? 'AUTO-GENERATED'); ?></td>
                            <td class="label">Version</td>
                            <td><?php echo e($policy->version ?? '1.0'); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Effectivity Date</td>
                            <td><?php echo e($policy->effectivity_date ?: '-'); ?></td>
                            <td class="label">Classification</td>
                            <td><?php echo e($policy->classification ?? 'Internal Use'); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Prepared By</td>
                            <td colspan="3"><?php echo e($policy->prepared_by ?? 'System Admin'); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Reviewed By</td>
                            <td><?php echo e($policy->reviewed_by ?: '-'); ?></td>
                            <td class="label">Approved By</td>
                            <td><?php echo e($policy->approved_by ?: '-'); ?></td>
                        </tr>
                    </table>

                    <div class="description-content">
                        <?php echo $policy->description ?? '<p style="color:#cbd5e0;">No description provided.</p>'; ?>

                    </div>

                    <?php if($policy->attachment): ?>
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Attachment Preview</h3>
                            <div class="border rounded-lg overflow-hidden bg-white">
                                <?php $ext = strtolower(pathinfo($policy->attachment, PATHINFO_EXTENSION)); ?>

                                <?php if(in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                                    <img src="<?php echo e(asset('storage/'.$policy->attachment)); ?>" class="w-full">
                                <?php elseif($ext === 'pdf'): ?>
                                    <iframe src="<?php echo e(asset('storage/'.$policy->attachment)); ?>" class="w-full h-[500px]"></iframe>
                                <?php else: ?>
                                    <div class="p-4 text-center">
                                        <a href="<?php echo e(asset('storage/'.$policy->attachment)); ?>"
                                           target="_blank"
                                           class="text-blue-600 underline">
                                            Download Attachment
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="w-[30%]">
            <div class="bg-white border rounded-xl shadow p-5 sticky top-6 space-y-4">
                <h3 class="font-semibold text-lg">Policy Details</h3>

                <div class="text-sm space-y-3">
                    <div><p class="text-gray-500 text-xs">Code</p><p><?php echo e($policy->code ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Policy Title</p><p><?php echo e($policy->policy ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Version</p><p><?php echo e($policy->version ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Effectivity Date</p><p><?php echo e($policy->effectivity_date ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Prepared By</p><p><?php echo e($policy->prepared_by ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Reviewed By</p><p><?php echo e($policy->reviewed_by ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Approved By</p><p><?php echo e($policy->approved_by ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Classification</p><p><?php echo e($policy->classification ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Approval Status</p><p><?php echo e($policy->approval_status ?? '-'); ?></p></div>
                    <div><p class="text-gray-500 text-xs">Workflow Status</p><p><?php echo e($policy->workflow_status ?? '-'); ?></p></div>

                    <?php if(!empty($policy->review_note)): ?>
                        <div>
                            <p class="text-gray-500 text-xs">Review Note</p>
                            <p><?php echo e($policy->review_note); ?></p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <p class="text-gray-500 text-xs">Attachment</p>
                        <?php if($policy->attachment): ?>
                            <a href="<?php echo e(asset('storage/' . $policy->attachment)); ?>" target="_blank" class="text-blue-600 hover:underline">
                                View Attachment
                            </a>
                        <?php else: ?>
                            <p>-</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <?php if(($policy->workflow_status ?? null) === 'Submitted' && Auth::user()->hasPermission('approve_policies')): ?>
                        <form method="POST" action="<?php echo e(route('admin.policies.approve', $policy->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Approve</button>
                        </form>

                        <form method="POST" action="<?php echo e(route('admin.policies.reject', $policy->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="review_note" value="Rejected by admin">
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Reject</button>
                        </form>

                        <form method="POST" action="<?php echo e(route('admin.policies.revise', $policy->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="review_note" value="Needs revision">
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">Revise</button>
                        </form>
                    <?php endif; ?>
                </div>

                <a href="<?php echo e(route('policies.preview', [
                        'policy' => $policy->policy,
                        'code' => $policy->code,
                        'version' => $policy->version,
                        'effectivity_date' => $policy->effectivity_date,
                        'prepared_by' => $policy->prepared_by,
                        'reviewed_by' => $policy->reviewed_by,
                        'approved_by' => $policy->approved_by,
                        'classification' => $policy->classification,
                        'description' => $policy->description,
                    ])); ?>"
                   target="_blank"
                   class="block text-center bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    Download PDF
                </a>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .policy-paper {
        width: 210mm;
        min-height: 297mm;
        padding: 20px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        color: #2d3748;
        font-size: 12px;
        line-height: 1.5;
    }

    .letterhead { border-bottom: 2px solid #2b6cb0; padding-bottom: 10px; margin-bottom: 25px; text-align: right; }
    .company-name { font-size: 18px; font-weight: bold; color: #2b6cb0; margin: 0; }
    .company-subtitle { font-size: 10px; color: #718096; margin: 2px 0 0; }
    .policy-title { text-align: center; font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 20px 0; color: #2d3748; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; table-layout: fixed; }
    .info-table td { padding: 8px 12px; border: 1px solid #cbd5e0; font-size: 11px; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
    .label { background-color: #f8fafc; font-weight: bold; width: 140px; color: #4a5568; }
    .description-content { margin-top: 20px; width: 100%; font-size: 12px; }
    .description-content p { margin: 0 0 8px 0; }
    .description-content ul, .description-content ol { margin: 0 0 10px 20px; padding: 0; }
    .description-content li { margin-bottom: 4px; }
    .description-content h1, .description-content h2, .description-content h3, .description-content h4, .description-content h5, .description-content h6 { margin: 12px 0 8px 0; line-height: 1.3; }
    .description-content strong { font-weight: bold; }
    .description-content em { font-style: italic; }
    .description-content u { text-decoration: underline; }
    .description-content blockquote { border-left: 3px solid #cbd5e0; padding-left: 10px; margin: 10px 0; color: #4a5568; }
    .description-content hr { border: none; border-top: 1px solid #cbd5e0; margin: 12px 0; }
    .description-content img { max-width: 100% !important; height: auto !important; }
    .description-content table { width: 100% !important; max-width: 100% !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 12px 0 !important; border: 1px solid #94a3b8 !important; }
    .description-content th, .description-content td { border: 1px solid #94a3b8 !important; padding: 8px !important; vertical-align: top !important; text-align: left !important; white-space: normal !important; word-break: break-word !important; overflow-wrap: break-word !important; }
    .description-content th { background: #f8fafc !important; font-weight: bold !important; }
    .description-content colgroup, .description-content col { display: none !important; width: auto !important; }
    .description-content td p, .description-content th p, .description-content td div, .description-content th div, .description-content td span, .description-content th span { margin: 0 !important; padding: 0 !important; white-space: normal !important; word-break: break-word !important; overflow-wrap: break-word !important; }

    @media (max-width: 1400px) {
        .policy-paper {
            width: 100%;
            min-height: auto;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/admin/policy-show.blade.php ENDPATH**/ ?>