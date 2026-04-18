<?php $__env->startSection('content'); ?>
<?php
    $canReviewKyc = in_array((string) (auth()->user()->role ?? ''), ['Admin', 'SuperAdmin'], true);
    $canManageRequirementDocs = ! $canReviewKyc;
?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', [
            'company' => $company,
            'companyKycStatus' => $companyKycStatus,
            'companyKycStatusClass' => $companyKycStatusClass,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="bg-gray-50 p-4">
            <?php if(session('bif_success')): ?>
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    <?php echo e(session('bif_success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('bif_warning')): ?>
                <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                    <?php echo e(session('bif_warning')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('kyc')): ?>
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?php echo e($errors->first('kyc')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('document')): ?>
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?php echo e($errors->first('document')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->has('change_rejection_reason')): ?>
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?php echo e($errors->first('change_rejection_reason')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('bif_client_link')): ?>
                <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <p class="font-medium">Client BIF link generated</p>
                    <p class="mt-1 break-all"><?php echo e(session('bif_client_link')); ?></p>
                </div>
            <?php endif; ?>

            <div id="companyKycApp">
                <div class="mb-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Business Information Form (BIF)</h2>
                                <p class="mt-1 text-xs text-gray-500">Saved business information is the source of truth for this company record.</p>
                            </div>
                            <?php if($bif): ?>
                                <a href="<?php echo e(route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                            <?php endif; ?>
                        </div>

                        <div class="bg-[#eef4ff] p-4">
                            <?php if($bif): ?>
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-4">
                                    <?php echo $__env->make('company.bif.partials.document', ['wrapperClass' => 'bif-doc'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            <?php else: ?>
                                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-gray-900">No business information submitted yet</h3>
                                    <p class="mt-2 text-sm text-gray-500">Create a Business Information Form to preview company KYC details here.</p>
                                    <a href="<?php echo e(route('company.bif.create', $company->id)); ?>" class="mt-5 inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                        Create BIF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Client Outreach</h3>
                            </div>
                            <div class="px-4 py-4">
                                <?php if($bif): ?>
                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure BIF link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">Send a secure BIF link to the client so they can complete missing details and upload onboarding documents.</p>
                                        <button type="button" data-open-send-bif-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send BIF
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure BIF link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">You can send the client a secure BIF link even before the internal record is fully completed.</p>
                                        <button type="button" data-open-send-bif-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send BIF
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Preview / PDF</h3>
                            </div>
                            <div class="space-y-4 px-4 py-4">
                                <?php if($bif): ?>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="mb-3">
                                            <p class="text-sm font-semibold text-gray-900">PDF Tools</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-600">Preview the current BIF or export a print-friendly PDF copy for offline review.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <a href="<?php echo e(route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id])); ?>" target="_blank" class="flex h-12 w-full items-center justify-center rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition hover:bg-gray-100">Preview PDF</a>
                                            <a href="<?php echo e(route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id, 'autoprint' => 1])); ?>" target="_blank" class="flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Download PDF</a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="mb-3">
                                            <p class="text-sm font-semibold text-gray-900">PDF Tools</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-600">Create or save the BIF first to unlock the printable preview and downloadable PDF.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <button type="button" disabled class="flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-400">Preview PDF</button>
                                            <button type="button" disabled class="flex h-12 w-full items-center justify-center rounded-xl bg-gray-200 text-sm font-semibold text-gray-500">Download PDF</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">Business Information Summary</h2>
                                <?php if($bif): ?>
                                    <a href="<?php echo e(route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                <?php endif; ?>
                            </div>
                            <div class="space-y-4 px-4 py-4 text-sm">
                                <div>
                                    <p class="text-gray-500">BIF Number</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->bif_no ?: '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($statusPillClass); ?>"><?php echo e($statusLabel); ?></span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date Submitted</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->submitted_at ? $bif->submitted_at->format('F j, Y') : '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Authorized Contact Person</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->authorized_contact_person_name ?: '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TIN</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->tin_no ?: '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Updated</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->updated_at ? $bif->updated_at->format('F j, Y g:i A') : '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Data Source</p>
                                    <p class="font-medium text-gray-900">
                                        <?php if(($bif?->last_submission_source ?? null) === 'client'): ?>
                                            Client-submitted
                                        <?php elseif(($bif?->last_submission_source ?? null) === 'manual'): ?>
                                            Manually encoded
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Client Submission</p>
                                    <p class="font-medium text-gray-900"><?php echo e($bif?->client_submitted_at ? $bif->client_submitted_at->format('F j, Y g:i A') : '-'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                            </div>
                            <div class="space-y-2 px-4 py-4">
                                <?php if($bif && $bif->change_request_status === 'pending'): ?>
                                    <div class="mb-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-3 text-xs text-amber-900">
                                        <p class="font-semibold">Pending BIF Change Request</p>
                                        <p class="mt-1">Requested by: <?php echo e($bif->change_requested_by_name ?: 'User'); ?></p>
                                        <p>Requested at: <?php echo e($bif->change_requested_at ? $bif->change_requested_at->format('F j, Y g:i A') : '-'); ?></p>
                                        <?php if(filled($bif->change_request_note)): ?>
                                            <p class="mt-1">Note: <?php echo e($bif->change_request_note); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($bif && $bif->change_request_status === 'rejected'): ?>
                                    <div class="mb-2 rounded-lg border border-red-200 bg-red-50 px-3 py-3 text-xs text-red-800">
                                        <p class="font-semibold">Latest Change Request Rejected</p>
                                        <p class="mt-1">Reviewed by: <?php echo e($bif->change_reviewed_by_name ?: 'Admin'); ?></p>
                                        <p>Reviewed at: <?php echo e($bif->change_reviewed_at ? $bif->change_reviewed_at->format('F j, Y g:i A') : '-'); ?></p>
                                        <?php if(filled($bif->change_rejection_reason)): ?>
                                            <p class="mt-1">Reason: <?php echo e($bif->change_rejection_reason); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if($canReviewKyc): ?>
                                    <?php
                                        $canReviewDecision = $bif && $bif->status === 'pending_approval';
                                    ?>
                                    <?php if($bif && $bif->change_request_status === 'pending'): ?>
                                        <form method="POST" action="<?php echo e(route('company.bif.change-request.approve', ['company' => $company->id, 'bif' => $bif->id])); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="mb-2 h-10 w-full rounded-lg bg-amber-600 text-sm font-medium text-white hover:bg-amber-700">Approve Requested BIF Changes</button>
                                        </form>

                                        <form method="POST" action="<?php echo e(route('company.bif.change-request.reject', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="mb-2 space-y-2">
                                            <?php echo csrf_field(); ?>
                                            <textarea name="change_rejection_reason" rows="2" class="w-full rounded-lg border border-red-200 px-3 py-2 text-xs outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="Reason for rejecting requested changes" required><?php echo e(old('change_rejection_reason')); ?></textarea>
                                            <button type="submit" class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700">Reject Requested BIF Changes</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('company.kyc.approve', $company->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="h-10 w-full rounded-lg bg-green-600 text-sm font-medium text-white <?php echo e($canReviewDecision ? 'hover:bg-green-700' : 'cursor-not-allowed opacity-60'); ?>" <?php if(! $canReviewDecision): echo 'disabled'; endif; ?>>Approve</button>
                                    </form>

                                    <form method="POST" action="<?php echo e(route('company.kyc.reject', $company->id)); ?>" class="space-y-2">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white <?php echo e($canReviewDecision ? 'hover:bg-red-700' : 'cursor-not-allowed opacity-60'); ?>" <?php if(! $canReviewDecision): echo 'disabled'; endif; ?>>Reject</button>
                                    </form>
                                    <?php if(! $canReviewDecision): ?>
                                        <p class="text-xs text-gray-500">Approve/Reject is enabled only after user submits for verification.</p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if($bif && $bif->change_request_status === 'pending'): ?>
                                        <button type="button" class="h-10 w-full rounded-lg bg-amber-500 text-sm font-medium text-white cursor-not-allowed opacity-80" disabled>Waiting For Admin Decision</button>
                                    <?php elseif($bif && $bif->status === 'approved'): ?>
                                        <a href="<?php echo e(route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id])); ?>" class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Request BIF Changes</a>
                                    <?php elseif($bif && $bif->status === 'pending_approval'): ?>
                                        <button type="button" class="h-10 w-full rounded-lg bg-slate-500 text-sm font-medium text-white cursor-not-allowed opacity-80" disabled>Submitted For Approval</button>
                                    <?php else: ?>
                                        <form method="POST" action="<?php echo e(route('company.kyc.submit', $company->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white <?php echo e($bif ? 'hover:bg-blue-700' : 'cursor-not-allowed opacity-60'); ?>" <?php if(! $bif): echo 'disabled'; endif; ?>>Submit For Verification</button>
                                        </form>
                                        <?php if($bif && ! $requirementsComplete): ?>
                                            <p class="text-xs text-amber-600">Recommendation: upload the remaining onboarding documents, but you may already submit this BIF for verification.</p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="pt-2">
                                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">KYC Activity</p>
                                    <div class="space-y-1 text-xs text-gray-500">
                                        <?php $__currentLoopData = $kycActivityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p><?php echo e($log); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <h2 class="text-base font-semibold text-gray-900">Business Onboarding Requirements</h2>
                            <p class="mt-1 text-xs text-gray-500">Upload and manage the required onboarding documents based on the business organization.</p>
                        </div>
                        <div class="max-h-[520px] space-y-3 overflow-y-auto p-4">
                            <?php $__currentLoopData = $kycRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <section class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="border-b border-gray-100 pb-3">
                                        <h3 class="text-sm font-semibold text-gray-900"><?php echo e($group['group']); ?></h3>
                                    </div>
                                    <div class="space-y-3 pt-3">
                                        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <article class="rounded-xl border border-gray-200 bg-white p-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900"><?php echo e($requirement['label']); ?></p>
                                                        <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e($requirement['uploaded'] ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200'); ?>">
                                                            <?php echo e($requirement['uploaded'] ? 'Uploaded' : 'Missing'); ?>

                                                        </span>
                                                        <p class="mt-1 text-xs text-gray-500"><?php echo e($requirement['helper']); ?></p>
                                                    </div>
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <?php if($requirement['template_url'] ?? null): ?>
                                                            <a href="<?php echo e($requirement['template_url']); ?>" class="rounded-md border border-blue-200 px-2 py-1 text-blue-600 hover:bg-blue-50">
                                                                Preview Form
                                                            </a>
                                                            <a href="<?php echo e($requirement['template_download_url']); ?>" target="_blank" class="rounded-md bg-blue-600 px-2 py-1 text-white hover:bg-blue-700">
                                                                Download Form
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if($canManageRequirementDocs): ?>
                                                            <form method="POST" action="<?php echo e(route('company.kyc.requirements.upload', ['company' => $company->id, 'requirement' => $requirement['key']])); ?>" enctype="multipart/form-data" class="inline-flex">
                                                                <?php echo csrf_field(); ?>
                                                                <label class="cursor-pointer rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50">
                                                                    <?php echo e(($requirement['template_url'] ?? null) ? ($requirement['uploaded'] ? 'Replace Signed Copy' : 'Upload Signed Copy') : ($requirement['uploaded'] ? 'Replace' : 'Upload')); ?>

                                                                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" onchange="this.form.submit()">
                                                                </label>
                                                            </form>
                                                        <?php endif; ?>

                                                        <?php if($requirement['uploaded'] && $requirement['file_url']): ?>
                                                            <button
                                                                type="button"
                                                                class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50"
                                                                data-open-requirement-view
                                                                data-view-url="<?php echo e($requirement['file_url']); ?>"
                                                                data-view-title="<?php echo e($requirement['label']); ?>"
                                                                data-view-file-name="<?php echo e($requirement['file_name']); ?>"
                                                                data-view-mime-type="<?php echo e($requirement['mime_type']); ?>"
                                                                data-view-uploaded-at="<?php echo e($requirement['uploaded_at']); ?>"
                                                            >
                                                                View
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 opacity-40 pointer-events-none">View</button>
                                                        <?php endif; ?>

                                                        <?php if($canManageRequirementDocs): ?>
                                                            <form method="POST" action="<?php echo e(route('company.kyc.requirements.remove', ['company' => $company->id, 'requirement' => $requirement['key']])); ?>" class="inline-flex" onsubmit="return confirm('Remove this uploaded requirement file?');">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="rounded-md border border-red-200 px-2 py-1 text-red-600 <?php echo e($requirement['uploaded'] ? 'hover:bg-red-50' : 'opacity-40 pointer-events-none'); ?>" <?php if(! $requirement['uploaded']): echo 'disabled'; endif; ?>>Remove</button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </article>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </section>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'sendBifModal','width' => 'sm:max-w-[560px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'sendBifModal','width' => 'sm:max-w-[560px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Send Business Information Form</h2>
                <p class="mt-1 text-sm text-gray-500">Email a secure BIF link to the client so they can complete missing business details and upload onboarding documents.</p>
            </div>
            <button type="button" data-close-send-bif-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('company.bif.send', $company->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                The secure link opens a mobile-friendly client form. Submitted details populate the Company KYC BIF record and uploaded documents are linked back to this company profile.
            </div>

            <div>
                <label for="recipient_email" class="mb-1 block text-sm font-medium text-gray-700">Recipient Email</label>
                <input id="recipient_email" name="recipient_email" type="email" value="<?php echo e(old('recipient_email', $bifRecipientEmail)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">Defaults to the authorized contact person email from the BIF when available. You can change it before sending.</p>
                <?php $__errorArgs = ['recipient_email'];
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

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                <p class="font-semibold uppercase tracking-wide text-gray-500">What the client can do</p>
                <p class="mt-2">Complete business organization, address, ownership structure, signatories, UBOs, and upload supporting documents based on business organization.</p>
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-send-bif-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-9 min-w-[140px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Send BIF Link
                </button>
            </div>
        </div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $attributes = $__attributesOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $component = $__componentOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__componentOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'requirementViewModal','width' => 'sm:max-w-[980px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'requirementViewModal','width' => 'sm:max-w-[980px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">View Document</h2>
            </div>
            <button type="button" data-close-requirement-view-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <div class="flex min-h-0 flex-1 flex-col">
        <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 overflow-y-auto lg:grid-cols-[1.1fr_0.9fr]">
            <div class="border-b border-gray-100 p-5 lg:border-b-0 lg:border-r">
                <div id="requirementViewPanel" class="flex min-h-[520px] items-center justify-center rounded-xl border border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-500">No file selected.</p>
                </div>
            </div>
            <div class="space-y-3 p-5">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Document Title</p>
                    <p id="requirementViewTitle" class="mt-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-900">-</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Date Upload</p>
                    <p id="requirementViewUploadedAt" class="mt-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">-</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">MIME Type</p>
                    <p id="requirementViewMime" class="mt-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">-</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">File Name</p>
                    <p id="requirementViewFileName" class="mt-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">-</p>
                </div>
                <div>
                    <a id="requirementViewOpenLink" href="#" target="_blank" rel="noopener" class="inline-flex rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                        Open file
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-requirement-view-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $attributes = $__attributesOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $component = $__componentOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__componentOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sendBifModal = document.getElementById('sendBifModal');
        const requirementViewModal = document.getElementById('requirementViewModal');
        const openSendBifButtons = document.querySelectorAll('[data-open-send-bif-modal]');
        const closeSendBifButtons = document.querySelectorAll('[data-close-send-bif-modal]');
        const openRequirementViewButtons = document.querySelectorAll('[data-open-requirement-view]');
        const closeRequirementViewButtons = document.querySelectorAll('[data-close-requirement-view-modal]');
        const shouldOpenSendModal = <?php echo json_encode($errors->has('recipient_email'), 15, 512) ?>;

        const openModal = () => window.jkncSlideOver.open(sendBifModal);
        const closeModal = () => window.jkncSlideOver.close(sendBifModal);
        const openRequirementModal = () => window.jkncSlideOver.open(requirementViewModal);
        const closeRequirementModal = () => window.jkncSlideOver.close(requirementViewModal);
        const requirementViewTitle = document.getElementById('requirementViewTitle');
        const requirementViewPanel = document.getElementById('requirementViewPanel');
        const requirementViewFileName = document.getElementById('requirementViewFileName');
        const requirementViewMime = document.getElementById('requirementViewMime');
        const requirementViewUploadedAt = document.getElementById('requirementViewUploadedAt');
        const requirementViewOpenLink = document.getElementById('requirementViewOpenLink');

        const formatDate = (value) => {
            if (!value) {
                return '-';
            }
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }
            return date.toLocaleString();
        };

        const renderRequirementPreview = (url, mimeType, fileName, title) => {
            if (!requirementViewPanel) {
                return;
            }

            const safeTitle = title || 'Requirement Document';
            const normalizedMime = (mimeType || '').toLowerCase();
            const normalizedName = (fileName || '').toLowerCase();
            const isPdf = normalizedMime.includes('pdf') || normalizedName.endsWith('.pdf');
            const isImage = normalizedMime.startsWith('image/')
                || normalizedName.endsWith('.jpg')
                || normalizedName.endsWith('.jpeg')
                || normalizedName.endsWith('.png')
                || normalizedName.endsWith('.gif')
                || normalizedName.endsWith('.webp');

            if (isPdf) {
                requirementViewPanel.innerHTML = `<iframe src="${url}" class="h-[520px] w-full rounded-lg border border-gray-200 bg-white"></iframe>`;
                return;
            }

            if (isImage) {
                requirementViewPanel.innerHTML = `<img src="${url}" alt="${safeTitle}" class="h-[520px] w-full rounded-lg border border-gray-200 bg-white object-contain">`;
                return;
            }

            requirementViewPanel.innerHTML = `
                <div class="px-4 text-center">
                    <p class="text-sm text-gray-600">Preview is not available for this file type.</p>
                    <a href="${url}" target="_blank" rel="noopener" class="mt-3 inline-flex rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Open file</a>
                </div>
            `;
        };

        openSendBifButtons.forEach((button) => {
            button.addEventListener('click', openModal);
        });

        closeSendBifButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        openRequirementViewButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const url = button.getAttribute('data-view-url') || '';
                const title = button.getAttribute('data-view-title') || 'Requirement Document';
                const fileName = button.getAttribute('data-view-file-name') || '';
                const mimeType = button.getAttribute('data-view-mime-type') || '';
                const uploadedAt = button.getAttribute('data-view-uploaded-at') || '';

                if (requirementViewTitle) {
                    requirementViewTitle.textContent = title;
                }
                if (requirementViewFileName) {
                    requirementViewFileName.textContent = fileName || '-';
                }
                if (requirementViewMime) {
                    requirementViewMime.textContent = mimeType || '-';
                }
                if (requirementViewUploadedAt) {
                    requirementViewUploadedAt.textContent = formatDate(uploadedAt);
                }
                if (requirementViewOpenLink) {
                    requirementViewOpenLink.href = url || '#';
                }

                renderRequirementPreview(url, mimeType, fileName, title);
                openRequirementModal();
            });
        });

        closeRequirementViewButtons.forEach((button) => {
            button.addEventListener('click', closeRequirementModal);
        });

        sendBifModal?.addEventListener('click', function (event) {
            if (event.target === sendBifModal || event.target.hasAttribute('data-drawer-overlay')) {
                closeModal();
            }
        });

        requirementViewModal?.addEventListener('click', function (event) {
            if (event.target === requirementViewModal || event.target.hasAttribute('data-drawer-overlay')) {
                closeRequirementModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
                closeRequirementModal();
            }
        });

        if (shouldOpenSendModal) {
            openModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/company/kyc.blade.php ENDPATH**/ ?>