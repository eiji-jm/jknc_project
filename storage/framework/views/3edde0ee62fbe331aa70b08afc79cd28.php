<?php $__env->startSection('content'); ?>
<?php
    $statusPillClasses = [
        'Verified' => 'bg-green-100 text-green-700 border border-green-200',
        'Approved' => 'bg-green-100 text-green-700 border border-green-200',
        'Pending Verification' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'For Review' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Not Submitted' => 'bg-gray-100 text-gray-600 border border-gray-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];

    $status = $contact->kyc_status ?: 'Not Submitted';
    $name = trim($contact->first_name.' '.$contact->last_name);
    $initials = strtoupper(mb_substr($contact->first_name ?? '', 0, 1).mb_substr($contact->last_name ?? '', 0, 1));
    $contactCifNo = $contact->cif_no ?: ($cifData['cif_no'] ?? '-');
    $kycRequirements = $kycRequirementState ?? [
        'cif_signed_document' => ['file' => null, 'complete' => false],
        'two_valid_ids' => ['count' => 0, 'files' => [], 'complete' => false],
        'specimen_signature_form' => ['form_exists' => false, 'file' => null, 'files' => [], 'complete' => false],
        'tin_proof' => ['file' => null, 'files' => [], 'complete' => false],
    ];
    $requiredKycRequirementKeys = $requiredKycRequirementKeys ?? ['cif_signed_document', 'two_valid_ids', 'specimen_signature_form', 'tin_proof'];
    $kycRequirementLabels = [
        'cif_signed_document' => 'CIF Document (Signed)',
        'two_valid_ids' => 'Two Valid IDs',
        'specimen_signature_form' => 'Specimen Signature Form',
        'tin_proof' => 'TIN Proof',
        'passport_proof' => 'Passport',
        'visa_proof' => 'Visa',
        'acr_card_proof' => 'ACR Card',
        'aaep_proof' => 'AEP',
    ];
    $foreignerDocumentRequirements = [
        ['key' => 'passport_proof', 'label' => 'Passport'],
        ['key' => 'visa_proof', 'label' => 'Visa'],
        ['key' => 'acr_card_proof', 'label' => 'ACR Card'],
        ['key' => 'aaep_proof', 'label' => 'AEP'],
    ];
    $requiresForeignerDocuments = collect($requiredKycRequirementKeys)->intersect(array_column($foreignerDocumentRequirements, 'key'))->isNotEmpty();
    $canReviewKyc = in_array((string) (auth()->user()->role ?? ''), ['Admin', 'SuperAdmin'], true);
    $cifStatus = strtolower((string) ($contact->cif_status ?? 'draft'));
    $changeRequestStatus = strtolower((string) ($cifData['change_request_status'] ?? ''));
    $changeRequestPending = $changeRequestStatus === 'pending';
    $changeRequestApproved = $changeRequestStatus === 'approved';
    $kycLockedForRequester = ! $canReviewKyc && $cifStatus === 'approved' && ! $changeRequestApproved;
    $canModifyKyc = ! $kycLockedForRequester;
?>

<div class="bg-white">
    <div class="border-b border-gray-200 px-6 py-3 text-sm text-gray-600">
        <a href="<?php echo e(route('contacts.index')); ?>" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Contacts</a>
        <span class="mx-1">/</span>
        <span class="font-medium text-gray-900"><?php echo e($name); ?></span>
    </div>

    <div class="border-b border-gray-200 px-6 py-4">
        <div class="flex flex-wrap items-center gap-5">
            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-blue-100 text-3xl font-semibold text-blue-700">
                <?php echo e($initials ?: 'C'); ?>

            </div>
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold text-gray-900"><?php echo e($name); ?></h1>
                <p class="text-xl text-gray-700"><?php echo e($contact->company_name ?: 'ABC Corporation'); ?></p>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                    <span>Email: <?php echo e($contact->email ?: 'juan@gmail.com'); ?></span>
                    <span>Phone number: <?php echo e($contact->phone ?: '09345234'); ?></span>
                    <span>Customer Type: <?php echo e($contact->customer_type ?: 'Corporation'); ?></span>
                    <span>Position: <?php echo e($contact->position ?: 'CEO'); ?></span>
                    <span>CIF No: <?php echo e($contactCifNo ?: '-'); ?></span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span id="contactKycHeaderBadge" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($statusPillClasses[$status] ?? $statusPillClasses['Not Submitted']); ?>"><?php echo e($status); ?></span>
                    <span class="text-sm text-gray-700">Contact Owner: <?php echo e($contact->owner_name ?: 'John Admin'); ?></span>
                </div>
                <p class="text-sm text-gray-600">Address: <?php echo e($contact->contact_address ?: 'Cebu City, Philippines'); ?></p>
            </div>
        </div>
    </div>

    <div class="flex">
        <aside class="w-48 border-r border-gray-200 p-3">
            <nav class="space-y-1">
                <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tabLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a
                        href="<?php echo e(route('contacts.show', $contact).'?tab='.$tabKey); ?>"
                        class="block rounded-lg px-3 py-1.5 text-sm <?php echo e($tab === $tabKey ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100'); ?>"
                    >
                        <?php echo e($tabLabel); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </aside>

        <section class="flex-1 bg-white p-6">
            <?php if(session('success')): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('contact_client_link')): ?>
                <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <p class="font-medium"><?php echo e(session('contact_client_link.label')); ?></p>
                    <p class="mt-1 break-all"><?php echo e(session('contact_client_link.url')); ?></p>
                </div>
            <?php endif; ?>

            <?php if($tab === 'kyc'): ?>
                <div id="kyc">
                <div id="kycTabApp">
                    <div class="mb-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <div>
                                    <h2 class="text-base font-semibold text-gray-900">Client Information Form (CIF)</h2>
                                    <p class="mt-1 text-xs text-gray-500">Manual CIF data is the source of truth for this contact record.</p>
                                </div>
                                <?php if($cifEditMode): ?>
                                    <a href="<?php echo e(route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc'])); ?>" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                                <?php elseif($canModifyKyc): ?>
                                    <a href="<?php echo e(route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc', 'edit_cif' => 1])); ?>" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                <?php else: ?>
                                    <span class="text-sm text-amber-700">Request change to edit</span>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <?php if($cifEditMode): ?>
                                    <?php echo $__env->make('contacts.partials.cif-document-edit', [
                                        'contact' => $contact,
                                        'cifData' => $cifData,
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php else: ?>
                                    <?php echo $__env->make('contacts.partials.cif-document', ['cifData' => $cifData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Client Outreach</h3>
                                </div>
                                <div class="space-y-4 px-4 py-4">
                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure CIF link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">Send a secure CIF link to the client so they can complete missing details and upload onboarding documents.</p>
                                        <button type="button" data-open-send-cif-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send CIF
                                        </button>
                                    </div>

                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure Specimen Signature link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">Send a secure link so the client can complete the specimen signature form.</p>
                                        <button type="button" data-open-send-specimen-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send Specimen Link
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Preview / PDF</h3>
                                </div>
                                <div class="px-4 py-4">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <h3 class="mb-2 text-sm font-semibold text-gray-900">PDF Tools</h3>
                                        <p class="mb-3 text-xs text-gray-500">
                                            Preview the current document or export a print-friendly PDF.
                                        </p>

                                        <button type="button"
                                            data-cif-pdf-preview
                                            data-preview-url="<?php echo e(route('contacts.cif.preview', $contact->id)); ?>"
                                            class="mb-2 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Preview PDF
                                        </button>

                                        <button type="button"
                                            data-cif-pdf-download
                                            data-download-url="<?php echo e(route('contacts.cif.download', $contact->id)); ?>?autoprint=1"
                                            class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                            Download PDF
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <h2 class="text-base font-semibold text-gray-900">KYC Information</h2>
                                    <button id="openContactIntakeModal" type="button" class="text-sm text-blue-600 hover:text-blue-700">View KYC Form</button>
                                </div>
                                <div class="space-y-4 px-4 py-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">CIF</p>
                                        <p id="kycCifValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">TIN</p>
                                        <p id="kycTinValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">KYC Status</p>
                                        <span id="kycCardStatusBadge" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"></span>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Date Verified</p>
                                        <p id="kycDateVerifiedValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Verified By</p>
                                        <p id="kycVerifiedByValue" class="font-medium text-gray-900"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                                </div>
                                <div class="space-y-2 px-4 py-4">
                                    <?php if(! $canReviewKyc): ?>
                                        <?php if($cifStatus === 'approved'): ?>
                                            <?php if($changeRequestPending): ?>
                                                <button type="button" disabled class="h-10 w-full cursor-not-allowed rounded-lg bg-amber-100 text-sm font-medium text-amber-700">Change Request Pending</button>
                                                <p class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">
                                                    Your request is waiting for admin approval before editing is unlocked.
                                                </p>
                                            <?php elseif($changeRequestApproved): ?>
                                                <button type="button" disabled class="h-10 w-full cursor-not-allowed rounded-lg bg-blue-100 text-sm font-medium text-blue-700">Editing Unlocked</button>
                                                <p class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">
                                                    Update the forms and requirements, then submit for verification again.
                                                </p>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.change-request', $contact->id)); ?>" class="space-y-2">
                                                    <?php echo csrf_field(); ?>
                                                    <textarea name="change_request_note" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Optional note for the admin reviewer"></textarea>
                                                    <button type="submit" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Request Change Information</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php elseif($cifStatus === 'pending'): ?>
                                            <button type="button" disabled class="h-10 w-full cursor-not-allowed rounded-lg bg-amber-100 text-sm font-medium text-amber-700">Pending Approval</button>
                                        <?php else: ?>
                                            <form id="submitKycForVerificationForm" method="POST" action="<?php echo e(route('contacts.kyc.submit', $contact->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button id="submitForVerificationBtn" type="submit" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Submit For Verification</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($canReviewKyc): ?>
                                        <form id="approveKycForm" method="POST" action="<?php echo e(route('contacts.kyc.approve', $contact->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button id="approveKycBtn" type="submit" class="h-10 w-full rounded-lg bg-green-600 text-sm font-medium text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-green-300">Approve</button>
                                        </form>
                                        <form id="rejectKycForm" method="POST" action="<?php echo e(route('contacts.kyc.reject', $contact->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <input id="rejectReasonField" type="hidden" name="reason" value="">
                                            <button id="rejectKycBtn" type="button" class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300">Reject</button>
                                        </form>
                                        <?php if($changeRequestPending && $cifStatus === 'approved'): ?>
                                            <form method="POST" action="<?php echo e(route('contacts.kyc.change-request.approve', $contact->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Approve Change Request</button>
                                            </form>
                                            <form method="POST" action="<?php echo e(route('contacts.kyc.change-request.reject', $contact->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700">Reject Change Request</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <p id="kycActionWarning" class="hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700"></p>
                                    <div class="pt-2">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">KYC Activity</p>
                                        <div id="kycActionLogs" class="space-y-1 text-xs text-gray-500"></div>
                                    </div>
                                    <p id="kycRejectionNote" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">KYC Requirements</h2>
                                <p class="mt-1 text-xs text-gray-500">Upload and manage required compliance items. Foreigner and dual-citizen contacts also require Passport, Visa, ACR Card, and AEP uploads.</p>
                            </div>
                            <?php if($errors->kycRequirementUpload->any()): ?>
                                <div class="border-b border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <?php echo e($errors->kycRequirementUpload->first()); ?>

                                </div>
                            <?php endif; ?>
                            <div class="max-h-[520px] space-y-3 overflow-y-auto p-4">
                                <?php
                                    $cifSignedRequirement = $kycRequirements['cif_signed_document'];
                                    $twoValidIds = $kycRequirements['two_valid_ids'];
                                    $specimenRequirement = $kycRequirements['specimen_signature_form'];
                                    $tinRequirement = $kycRequirements['tin_proof'];
                                    $actionBtn = 'rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50';
                                    $primaryBtn = 'rounded-md border border-blue-200 px-2 py-1 text-blue-700 hover:bg-blue-50';
                                    $dangerBtn = 'rounded-md border border-red-200 px-2 py-1 text-red-600 hover:bg-red-50';
                                    $disabledBtn = 'opacity-40 pointer-events-none';
                                ?>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">CIF Document (Signed)</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e($cifSignedRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600'); ?>">
                                                <?php echo e($cifSignedRequirement['complete'] ? 'Complete' : 'Missing'); ?>

                                            </span>
                                            <p class="mt-2 text-xs text-gray-500"><?php echo e($cifSignedRequirement['file']['file_name'] ?? 'No file uploaded'); ?></p>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="inline-flex">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="requirement" value="cif_signed_document">
                                                    <label class="<?php echo e($actionBtn); ?> cursor-pointer">
                                                        Upload
                                                        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                    </label>
                                                </form>
                                            <?php endif; ?>
                                            <button
                                                id="viewCifSignedDocumentBtn"
                                                type="button"
                                                <?php if($cifSignedRequirement['file']): ?>
                                                    onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($cifSignedRequirement['file']['file_path'] ?? $cifSignedRequirement['file']['path'] ?? '')->toHtml() ?>, 'cif_signed_document')"
                                                <?php endif; ?>
                                                class="<?php echo e($actionBtn); ?> <?php echo e($cifSignedRequirement['file'] ? '' : $disabledBtn); ?>"
                                            >
                                                View
                                            </button>
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'cif_signed_document'])); ?>" class="inline-flex" onsubmit="return confirm('Remove the uploaded signed CIF document?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="<?php echo e($dangerBtn); ?> <?php echo e($cifSignedRequirement['file'] ? '' : $disabledBtn); ?>">Remove</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Two Valid IDs</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e($twoValidIds['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600'); ?>">
                                                <?php echo e($twoValidIds['complete'] ? 'Complete' : 'Missing'); ?>

                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                <?php echo e($twoValidIds['count'] > 0 ? $twoValidIds['count'].' file'.($twoValidIds['count'] === 1 ? '' : 's').' uploaded' : 'No file uploaded'); ?>

                                            </p>
                                            <?php if($twoValidIds['count'] > 0): ?>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <?php $__currentLoopData = $twoValidIds['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileIndex => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($file['file_path'] ?? $file['path'] ?? '')->toHtml() ?>, 'two_valid_ids', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $twoValidIds['files'])))->toHtml() ?>, <?php echo e($fileIndex); ?>)"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File <?php echo e($loop->iteration); ?>

                                                        </button>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="inline-flex">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="requirement" value="two_valid_ids">
                                                    <label class="<?php echo e($actionBtn); ?> cursor-pointer">
                                                        Upload
                                                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                    </label>
                                                </form>
                                            <?php endif; ?>
                                            <?php $twoValidFirstFile = $twoValidIds['files'][0] ?? null; ?>
                                            <button
                                                type="button"
                                                <?php if($twoValidFirstFile): ?>
                                                    onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($twoValidFirstFile['file_path'] ?? $twoValidFirstFile['path'] ?? '')->toHtml() ?>, 'two_valid_ids', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $twoValidIds['files'])))->toHtml() ?>, 0)"
                                                <?php endif; ?>
                                                class="<?php echo e($actionBtn); ?> <?php echo e($twoValidFirstFile ? '' : $disabledBtn); ?>"
                                            >
                                                View
                                            </button>
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'two_valid_ids'])); ?>" class="inline-flex" onsubmit="return confirm('Remove all uploaded valid IDs for this requirement?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="<?php echo e($dangerBtn); ?> <?php echo e($twoValidIds['count'] > 0 ? '' : $disabledBtn); ?>">Remove</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Specimen Signature Form</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e($specimenRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600'); ?>">
                                                <?php echo e($specimenRequirement['complete'] ? 'Complete' : 'Missing'); ?>

                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                <?php if($specimenRequirement['file']): ?>
                                                    <?php echo e($specimenRequirement['file']['file_name'] ?? 'Specimen signature file uploaded'); ?>

                                                <?php elseif($specimenRequirement['form_exists']): ?>
                                                    System form created
                                                <?php else: ?>
                                                    No file uploaded
                                                <?php endif; ?>
                                            </p>
                                            <?php if(!empty($specimenRequirement['files'])): ?>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <?php $__currentLoopData = $specimenRequirement['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileIndex => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($file['file_path'] ?? $file['path'] ?? '')->toHtml() ?>, 'specimen_signature_upload', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $specimenRequirement['files'])))->toHtml() ?>, <?php echo e($fileIndex); ?>)"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File <?php echo e($loop->iteration); ?>

                                                        </button>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <?php if($specimenRequirement['form_exists']): ?>
                                                <a href="<?php echo e(route('contacts.specimen-signature', ['id' => $contact->id])); ?>" class="<?php echo e($actionBtn); ?>">View Form</a>
                                                <?php if($canModifyKyc): ?>
                                                    <a href="<?php echo e(route('contacts.specimen-signature', ['id' => $contact->id, 'edit' => 1])); ?>" class="<?php echo e($actionBtn); ?>">Edit Form</a>
                                                <?php endif; ?>
                                            <?php elseif($canModifyKyc): ?>
                                                <a href="<?php echo e(route('contacts.specimen-signature', ['id' => $contact->id])); ?>" class="<?php echo e($primaryBtn); ?>">Create Form</a>
                                            <?php endif; ?>
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="inline-flex">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="requirement" value="specimen_signature_upload">
                                                    <label class="<?php echo e($actionBtn); ?> cursor-pointer">
                                                        Upload
                                                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                    </label>
                                                </form>
                                            <?php endif; ?>
                                            <button
                                                type="button"
                                                <?php if($specimenRequirement['file']): ?>
                                                    onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($specimenRequirement['file']['file_path'] ?? $specimenRequirement['file']['path'] ?? '')->toHtml() ?>, 'specimen_signature_upload', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $specimenRequirement['files'] ?? [])))->toHtml() ?>, 0)"
                                                <?php endif; ?>
                                                class="<?php echo e($actionBtn); ?> <?php echo e($specimenRequirement['file'] ? '' : $disabledBtn); ?>"
                                            >
                                                View
                                            </button>
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'specimen_signature_upload'])); ?>" class="inline-flex" onsubmit="return confirm('Remove the uploaded specimen signature file?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="<?php echo e($dangerBtn); ?> <?php echo e($specimenRequirement['file'] ? '' : $disabledBtn); ?>">Remove</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">TIN</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e($tinRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600'); ?>">
                                                <?php echo e($tinRequirement['complete'] ? 'Complete' : 'Missing'); ?>

                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                <?php if(!empty($tinRequirement['files'])): ?>
                                                    <?php echo e(count($tinRequirement['files']) > 1 ? count($tinRequirement['files']).' files uploaded' : ($tinRequirement['file']['file_name'] ?? 'No file uploaded')); ?>

                                                <?php else: ?>
                                                    No file uploaded
                                                <?php endif; ?>
                                            </p>
                                            <?php if(!empty($tinRequirement['files'])): ?>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <?php $__currentLoopData = $tinRequirement['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileIndex => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($file['file_path'] ?? $file['path'] ?? '')->toHtml() ?>, 'tin_proof', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $tinRequirement['files'])))->toHtml() ?>, <?php echo e($fileIndex); ?>)"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File <?php echo e($loop->iteration); ?>

                                                        </button>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="inline-flex">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="requirement" value="tin_proof">
                                                    <label class="<?php echo e($actionBtn); ?> cursor-pointer">
                                                        Upload
                                                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                    </label>
                                                </form>
                                            <?php endif; ?>
                                            <button
                                                type="button"
                                                <?php if($tinRequirement['file']): ?>
                                                    onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($tinRequirement['file']['file_path'] ?? $tinRequirement['file']['path'] ?? '')->toHtml() ?>, 'tin_proof', <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $tinRequirement['files'] ?? [])))->toHtml() ?>, 0)"
                                                <?php endif; ?>
                                                class="<?php echo e($actionBtn); ?> <?php echo e($tinRequirement['file'] ? '' : $disabledBtn); ?>"
                                            >
                                                View
                                            </button>
                                            <?php if($canModifyKyc): ?>
                                                <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'tin_proof'])); ?>" class="inline-flex" onsubmit="return confirm('Remove the uploaded TIN document?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="<?php echo e($dangerBtn); ?> <?php echo e($tinRequirement['file'] ? '' : $disabledBtn); ?>">Remove</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>

                                <?php if($requiresForeignerDocuments): ?>
                                    <?php $__currentLoopData = $foreignerDocumentRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foreignerRequirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $foreignerRequirementKey = $foreignerRequirement['key'];
                                            $foreignerRequirementLabel = $foreignerRequirement['label'];
                                            $foreignerState = $kycRequirements[$foreignerRequirementKey] ?? ['file' => null, 'files' => [], 'complete' => false];
                                            $foreignerFiles = array_values(array_filter((array) ($foreignerState['files'] ?? []), fn ($item) => is_array($item)));
                                            $foreignerPrimaryFile = $foreignerState['file'] ?? ($foreignerFiles[0] ?? null);
                                        ?>
                                        <article class="rounded-xl border border-gray-200 bg-white p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($foreignerRequirementLabel); ?></p>
                                                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium <?php echo e(($foreignerState['complete'] ?? false) ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600'); ?>">
                                                        <?php echo e(($foreignerState['complete'] ?? false) ? 'Complete' : 'Missing'); ?>

                                                    </span>
                                                    <p class="mt-2 text-xs text-gray-500">
                                                        <?php if(!empty($foreignerFiles)): ?>
                                                            <?php echo e(count($foreignerFiles) > 1 ? count($foreignerFiles).' files uploaded' : ($foreignerPrimaryFile['file_name'] ?? 'No file uploaded')); ?>

                                                        <?php else: ?>
                                                            No file uploaded
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if(!empty($foreignerFiles)): ?>
                                                        <div class="mt-2 flex flex-wrap gap-2">
                                                            <?php $__currentLoopData = $foreignerFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileIndex => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <button
                                                                    type="button"
                                                                    onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($file['file_path'] ?? $file['path'] ?? '')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($foreignerRequirementKey)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $foreignerFiles)))->toHtml() ?>, <?php echo e($fileIndex); ?>)"
                                                                    class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                                >
                                                                    File <?php echo e($loop->iteration); ?>

                                                                </button>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                                    <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="inline-flex">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="requirement" value="<?php echo e($foreignerRequirementKey); ?>">
                                                        <label class="<?php echo e($actionBtn); ?> cursor-pointer">
                                                            Upload
                                                            <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                        </label>
                                                    </form>
                                                    <button
                                                        type="button"
                                                        <?php if($foreignerPrimaryFile): ?>
                                                            onclick="openDocumentModal(<?php echo \Illuminate\Support\Js::from($foreignerPrimaryFile['file_path'] ?? $foreignerPrimaryFile['path'] ?? '')->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($foreignerRequirementKey)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $foreignerFiles)))->toHtml() ?>, 0)"
                                                        <?php endif; ?>
                                                        class="<?php echo e($actionBtn); ?> <?php echo e($foreignerPrimaryFile ? '' : $disabledBtn); ?>"
                                                    >
                                                        View
                                                    </button>
                                                    <form method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => $foreignerRequirementKey])); ?>" class="inline-flex" onsubmit="return confirm('Remove uploaded <?php echo e($foreignerRequirementLabel); ?> document(s)?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="<?php echo e($dangerBtn); ?> <?php echo e(!empty($foreignerFiles) ? '' : $disabledBtn); ?>">Remove</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div id="kycEditModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[560px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 class="text-xl font-semibold text-gray-900">Edit KYC Information</h3>
                                <button id="closeKycEditModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="kycEditForm" class="flex min-h-0 flex-1 flex-col">
                                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-6 sm:px-8">
                                <div>
                                    <label for="kycEditCif" class="mb-1 block text-sm font-medium text-gray-700">CIF</label>
                                    <input id="kycEditCif" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorCif" class="mt-1 hidden text-xs text-red-600">CIF is required.</p>
                                </div>
                                <div>
                                    <label for="kycEditTin" class="mb-1 block text-sm font-medium text-gray-700">TIN</label>
                                    <input id="kycEditTin" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorTin" class="mt-1 hidden text-xs text-red-600">TIN is required.</p>
                                </div>
                                <div>
                                    <label for="kycEditStatus" class="mb-1 block text-sm font-medium text-gray-700">KYC Status</label>
                                    <select id="kycEditStatus" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option>Not Submitted</option>
                                        <option>Pending Verification</option>
                                        <option>For Review</option>
                                        <option>Approved</option>
                                        <option>Rejected</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="kycEditDateVerified" class="mb-1 block text-sm font-medium text-gray-700">Date Verified</label>
                                    <input id="kycEditDateVerified" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorDateVerified" class="mt-1 hidden text-xs text-red-600">Date Verified is required for Approved status.</p>
                                </div>
                                <div>
                                    <label for="kycEditVerifiedBy" class="mb-1 block text-sm font-medium text-gray-700">Verified By</label>
                                    <input id="kycEditVerifiedBy" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorVerifiedBy" class="mt-1 hidden text-xs text-red-600">Verified By is required for Approved status.</p>
                                </div>
                                </div>
                                <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="cancelKycEdit" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <div id="documentModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[960px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 id="documentModalTitle" class="text-xl font-semibold text-gray-900">Edit CIF Document</h3>
                                <button id="closeDocumentModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="documentForm" method="POST" action="<?php echo e(route('contacts.kyc.requirements.upload', $contact->id)); ?>" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="requirement" value="cif_signed_document">
                                <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 overflow-y-auto lg:grid-cols-[1.1fr_0.9fr]">
                                <div class="border-b border-gray-100 p-5 lg:border-b-0 lg:border-r">
                                    <div id="documentPreviewPanel" class="flex min-h-[620px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 text-center text-sm text-gray-500">
                                        <i class="far fa-file-pdf text-6xl text-gray-400"></i>
                                        <p class="mt-2">No CIF document selected</p>
                                        <p class="text-xs">Upload a PDF or image file to preview</p>
                                    </div>
                                </div>
                                <div class="space-y-3 p-5">
                                    <div><label for="docCertificateNo" class="mb-1 block text-sm font-medium text-gray-700">CIF No.</label><input id="docCertificateNo" name="cif_no" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label for="docUploadDate" class="mb-1 block text-sm font-medium text-gray-700">Date Upload</label><input id="docUploadDate" name="date_upload" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div><div><label for="docCreatedDate" class="mb-1 block text-sm font-medium text-gray-700">Date Created</label><input id="docCreatedDate" name="date_created" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label for="docIssuedOn" class="mb-1 block text-sm font-medium text-gray-700">Issued On</label><input id="docIssuedOn" name="issued_on" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div><div><label for="docIssuedBy" class="mb-1 block text-sm font-medium text-gray-700">Issued By</label><input id="docIssuedBy" name="issued_by" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div></div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Document</label>
                                        <label for="docFileInput" class="flex h-11 cursor-pointer items-center rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-600 hover:bg-gray-50"><i class="fas fa-folder-open mr-2 text-blue-600"></i><span id="docFileNameLabel">Replace file</span></label>
                                        <input id="docFileInput" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                        <div class="mt-2 flex items-center justify-between gap-3 text-xs">
                                            <span id="docCurrentFileMeta" class="text-gray-500"></span>
                                            <button id="clearDocFileBtn" type="button" class="hidden text-gray-500 hover:text-red-600">Clear replacement file</button>
                                        </div>
                                        <p id="docErrorFile" class="mt-1 hidden text-xs text-red-600">Please upload a CIF document file.</p>
                                    </div>
                                    <div><label for="docRemarks" class="mb-1 block text-sm font-medium text-gray-700">Remarks</label><textarea id="docRemarks" name="remarks" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea></div>
                                </div>
                                </div>
                                <div class="flex justify-between gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="removeCifSignedDocumentBtn" type="button" class="h-10 rounded-lg border border-red-200 bg-white px-4 text-sm text-red-600 hover:bg-red-50">Remove file</button>
                                    <div class="flex justify-end gap-3">
                                        <button id="cancelDocumentModal" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                        <button type="submit" id="saveDocumentBtn" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <form id="removeCifSignedDocumentForm" method="POST" action="<?php echo e(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'cif_signed_document'])); ?>" class="hidden">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    </form>
                    <form id="removeKycDocumentForm" method="POST" action="" class="hidden">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <input type="hidden" name="index" id="removeKycDocumentIndex" value="">
                    </form>

                    <div id="documentViewModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[900px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8"><h3 id="documentViewTitle" class="text-xl font-semibold text-gray-900">Document Details</h3><button id="closeDocumentViewModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button></div>
                            <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 overflow-y-auto lg:grid-cols-[1.25fr_0.75fr]"><div id="documentViewPreview" class="m-4 flex min-h-[620px] items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500"></div><div class="space-y-3 border-l border-gray-100 p-5 text-sm"><div><p class="text-gray-500">CIF No.</p><p id="viewDocCertificateNo" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Date Upload</p><p id="viewDocUploadDate" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Date Created</p><p id="viewDocCreatedDate" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Issued On</p><p id="viewDocIssuedOn" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Issued By</p><p id="viewDocIssuedBy" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Remarks</p><p id="viewDocRemarks" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">File Name</p><p id="viewDocFileName" class="font-medium text-gray-900"></p></div></div></div>
                            <div class="flex justify-end border-t border-gray-100 px-6 py-4"><button id="closeDocumentViewFooter" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button></div>
                        </div>
                        </div>
                    </div>

                    <div id="rejectKycModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[520px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="border-b border-gray-100 px-6 py-5 sm:px-8"><h3 class="text-xl font-semibold text-gray-900">Reject KYC</h3></div>
                            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto px-6 py-6 sm:px-8">
                                <p class="text-sm text-gray-600">Are you sure you want to reject this KYC submission?</p>
                                <div><label for="rejectReasonInput" class="mb-1 block text-sm font-medium text-gray-700">Rejection Reason (optional)</label><textarea id="rejectReasonInput" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea></div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8"><button id="cancelRejectKyc" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button><button id="confirmRejectKyc" type="button" class="h-10 rounded-lg bg-red-600 px-4 text-sm font-medium text-white hover:bg-red-700">Reject</button></div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $cifDocumentDefaultsJs = [
                            'document_title' => 'CIF Document (Signed)',
                            'cif_no' => $contact->cif_no ?: ($cifData['cif_no'] ?? ''),
                            'date_created' => $cifData['cif_date'] ?? '',
                            'issued_on' => ($cifData['cif_document_issued_on'] ?? null) ?: optional($contact->cif_form_sent_at)->toDateString(),
                            'issued_by' => $cifData['cif_document_issued_by'] ?? '',
                        ];
                    ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const app = document.getElementById('kycTabApp');
                            if (!app) return;
                            const q = (id) => document.getElementById(id);
                            const mockUser = <?php echo json_encode($contact->owner_name ?: 'John Admin', 15, 512) ?>;
                            const todayIso = new Date().toISOString().slice(0, 10);
                            const statusStyles = {'Not Submitted':'bg-gray-100 text-gray-600 border border-gray-200','Pending Verification':'bg-amber-100 text-amber-700 border border-amber-200','For Review':'bg-amber-100 text-amber-700 border border-amber-200','Approved':'bg-green-100 text-green-700 border border-green-200','Rejected':'bg-red-100 text-red-700 border border-red-200'};
                            const statusRaw = <?php echo json_encode($status, 15, 512) ?>;
                            const statusInit = statusRaw === 'Verified' ? 'Approved' : statusRaw;
                            const specimenSignatureExists = <?php echo json_encode((bool) $specimenSignature, 15, 512) ?>;
                            const kycRequirementState = <?php echo json_encode($kycRequirements, 15, 512) ?>;
                            const requiredKycRequirementKeys = <?php echo json_encode($requiredKycRequirementKeys, 15, 512) ?>;
                            const kycRequirementLabels = <?php echo json_encode($kycRequirementLabels, 15, 512) ?>;
                            const cifSignedDocument = <?php echo json_encode($cifSignedRequirement['file'] ?? null, 15, 512) ?>;
                            const cifDocumentDefaults = <?php echo json_encode($cifDocumentDefaultsJs, 15, 512) ?>;
                            const specimenSignatureRoutes = {
                                create: <?php echo json_encode(route('contacts.specimen-signature', ['id' => $contact->id]), 512) ?>,
                                view: <?php echo json_encode(route('contacts.specimen-signature', ['id' => $contact->id]), 512) ?>,
                                edit: <?php echo json_encode(route('contacts.specimen-signature', ['id' => $contact->id, 'edit' => 1])) ?>,
                                download: <?php echo json_encode(route('contacts.specimen-signature.download', ['id' => $contact->id]), 512) ?>,
                            };
                            let kyc = {
                                cif: <?php echo json_encode($contact->cif_no ?: ($cifData['cif_no'] ?? ''), 15, 512) ?>,
                                tin: <?php echo json_encode($contact->tin ?: ($cifData['tin'] ?? ''), 15, 512) ?>,
                                status: statusInit || 'Not Submitted',
                                dateVerified: <?php echo json_encode($cifData['date_verified'] ?? '', 15, 512) ?>,
                                verifiedBy: <?php echo json_encode($cifData['verified_by'] ?? '', 15, 512) ?>,
                                rejectionReason: '',
                                submitted: ['Pending Verification','For Review','Approved','Rejected', 'Verified'].includes(statusInit)
                            };
                            let logs = <?php echo json_encode($kycActivityLogs ?? [], 15, 512) ?>;
                            let activeDoc = null; let file = null; let fileUrl = '';
                            let currentFiles = [];
                            let currentIndex = 0;
                            let currentDocs = [];

                            const fmtDate = (s) => { if (!s) return '-'; const d = new Date(s + 'T00:00:00'); return Number.isNaN(d.getTime()) ? s : new Intl.DateTimeFormat('en-US',{month:'short',day:'2-digit',year:'numeric'}).format(d); };
                            const fmtBytes = (n) => !n ? '-' : (n < 1024 ? `${n} B` : (n < 1048576 ? `${(n/1024).toFixed(1)} KB` : `${(n/1048576).toFixed(1)} MB`));
                            const open = (m) => {
                                const panel = m.querySelector('[data-slideover-panel]');
                                const overlay = m.querySelector('[data-slideover-overlay]');
                                m.classList.remove('hidden');
                                m.setAttribute('aria-hidden', 'false');
                                document.body.classList.add('overflow-hidden');
                                requestAnimationFrame(() => {
                                    overlay?.classList.remove('opacity-0');
                                    panel?.classList.remove('translate-x-full');
                                });
                            };
                            const close = (m) => {
                                const panel = m.querySelector('[data-slideover-panel]');
                                const overlay = m.querySelector('[data-slideover-overlay]');
                                overlay?.classList.add('opacity-0');
                                panel?.classList.add('translate-x-full');
                                window.setTimeout(() => {
                                    m.classList.add('hidden');
                                    m.setAttribute('aria-hidden', 'true');
                                    if ([q('kycEditModal'),q('documentModal'),q('documentViewModal'),q('rejectKycModal')].every((x) => x.classList.contains('hidden'))) document.body.classList.remove('overflow-hidden');
                                }, 300);
                            };
                            const badge = (el, status) => { el.className = `inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${statusStyles[status] || statusStyles['Not Submitted']}`; el.textContent = status; };
                            const addLog = (msg) => logs.unshift({
                                message: msg,
                                timestamp: new Date().toLocaleString('en-US', {
                                    month: 'long',
                                    day: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }).replace(',', '').replace(' at', ' •'),
                            });
                            const allRequiredUploaded = () => requiredKycRequirementKeys.every((key) => kycRequirementState[key]?.complete === true);

                            const render = () => {
                                q('kycCifValue').textContent = kyc.cif || '-';
                                q('kycTinValue').textContent = kyc.tin || '-';
                                q('kycDateVerifiedValue').textContent = fmtDate(kyc.dateVerified);
                                q('kycVerifiedByValue').textContent = kyc.verifiedBy || '-';
                                badge(q('kycCardStatusBadge'), kyc.status);
                                if (q('contactKycHeaderBadge')) badge(q('contactKycHeaderBadge'), kyc.status);
                                q('kycActionLogs').innerHTML = logs.slice(0, 7).map((entry) => `
                                    <div class="space-y-0.5">
                                        <p class="text-xs text-gray-600">${entry.message ?? ''}</p>
                                        <p class="text-[11px] text-gray-400">${entry.timestamp ?? ''}</p>
                                    </div>
                                `).join('');
                                const canReview = kyc.submitted && ['Pending Verification','For Review'].includes(kyc.status);
                                const approveBtn = q('approveKycBtn');
                                const rejectBtn = q('rejectKycBtn');
                                if (approveBtn) approveBtn.disabled = !canReview;
                                if (rejectBtn) rejectBtn.disabled = !canReview;
                                q('kycRejectionNote').classList.toggle('hidden', !kyc.rejectionReason);
                                q('kycRejectionNote').textContent = kyc.rejectionReason ? `Rejection reason: ${kyc.rejectionReason}` : '';
                            };

                            const renderPreview = (name, url, mime) => {
                                const label = 'document';
                                if (!name) { q('documentPreviewPanel').innerHTML = `<i class="far fa-file-pdf text-6xl text-gray-400"></i><p class="mt-2">No ${label.toLowerCase()} selected</p><p class="text-xs">Upload a PDF or image file to preview</p>`; return; }
                                if ((mime || '').includes('pdf') && url && url !== '#') q('documentPreviewPanel').innerHTML = `<iframe src="${url}" class="h-[620px] w-full rounded-lg border border-gray-200 bg-white"></iframe>`;
                                else if ((mime || '').startsWith('image/') && url && url !== '#') q('documentPreviewPanel').innerHTML = `<img src="${url}" alt="Document preview" class="h-[620px] w-full rounded-lg border border-gray-200 bg-white object-contain">`;
                                else q('documentPreviewPanel').innerHTML = `<div class="text-center"><i class="far fa-file text-5xl text-blue-600"></i><p class="mt-2 font-medium text-gray-800">${name}</p><p class="text-xs text-gray-500">${mime || 'Document file'}</p></div>`;
                            };

                            const normalizeDateInput = (value) => {
                                if (!value) return '';
                                return String(value).slice(0, 10);
                            };

                            const renderPreviewSwitcher = () => {
                                let switcher = document.getElementById('documentPreviewSwitcher');
                                if (!switcher) {
                                    switcher = document.createElement('div');
                                    switcher.id = 'documentPreviewSwitcher';
                                    switcher.className = 'mt-3 flex flex-wrap gap-2';
                                    q('documentPreviewPanel').insertAdjacentElement('afterend', switcher);
                                }
                                if (currentFiles.length <= 1) {
                                    switcher.innerHTML = '';
                                    return;
                                }
                                switcher.innerHTML = currentFiles.map((_, index) => `
                                    <button type="button" data-preview-index="${index}" class="rounded-md border px-2 py-1 text-xs ${index === currentIndex ? 'border-blue-200 bg-blue-100 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'}">
                                        ${index + 1}
                                    </button>
                                `).join('');
                                switcher.querySelectorAll('[data-preview-index]').forEach((button) => {
                                    button.addEventListener('click', () => switchFile(Number(button.dataset.previewIndex)));
                                });
                            };

                            const renderActiveDocument = () => {
                                const doc = currentDocs[currentIndex] || {};
                                const filePath = currentFiles[currentIndex] || '';
                                const previewUrl = filePath ? `/storage/${filePath}` : (doc.url || '');
                                q('documentModalTitle').textContent = kycRequirementLabels[activeDoc] || 'Document';
                                q('docCertificateNo').value = doc.cif_no || cifDocumentDefaults.cif_no || kyc.cif || '';
                                q('docUploadDate').value = normalizeDateInput(doc.uploaded_at) || todayIso;
                                q('docCreatedDate').value = normalizeDateInput(doc.date_created) || normalizeDateInput(cifDocumentDefaults.date_created);
                                q('docIssuedOn').value = normalizeDateInput(doc.issued_on) || normalizeDateInput(cifDocumentDefaults.issued_on);
                                q('docIssuedBy').value = doc.issued_by || cifDocumentDefaults.issued_by || '';
                                q('docRemarks').value = doc.remarks || '';
                                q('docFileNameLabel').textContent = doc.file_name ? 'Replace file' : 'Upload File';
                                q('docCurrentFileMeta').textContent = doc.file_name ? `Current file: ${doc.file_name}` : 'No file uploaded yet';
                                q('removeCifSignedDocumentBtn').classList.toggle('hidden', !doc.file_name);
                                const showCifFields = activeDoc === 'cif_signed_document';
                                q('docCertificateNo').closest('div').classList.toggle('hidden', !showCifFields);
                                q('docCreatedDate').closest('div').classList.toggle('hidden', !showCifFields);
                                renderPreview(doc.file_name || '', previewUrl, doc.mime_type || '');
                                renderPreviewSwitcher();
                            };

                            const switchFile = (index) => {
                                currentIndex = index;
                                renderActiveDocument();
                            };
                            window.switchFile = switchFile;

                            const openDocumentModal = (filePath, docType, files = [], startIndex = 0) => {
                                activeDoc = docType;
                                const requirementState = docType === 'cif_signed_document'
                                    ? { files: cifSignedDocument ? [cifSignedDocument] : [] }
                                    : kycRequirementState[docType];
                                currentDocs = Array.isArray(requirementState?.files) && requirementState.files.length
                                    ? requirementState.files
                                    : (requirementState?.file ? [requirementState.file] : []);
                                currentFiles = files.length ? files : currentDocs.map((doc) => doc.file_path || doc.path || '');
                                currentIndex = Math.min(Math.max(startIndex, 0), Math.max(currentFiles.length - 1, 0));
                                if (!currentFiles[currentIndex] && !(currentDocs[currentIndex]?.file_name)) return;
                                file = null;
                                fileUrl = '';
                                q('documentForm').reset();
                                q('docErrorFile').classList.add('hidden');
                                q('clearDocFileBtn').classList.add('hidden');
                                renderActiveDocument();
                                open(q('documentModal'));
                            };
                            window.openDocumentModal = openDocumentModal;

                            const openKycEditButton = q('openKycEditModal');
                            if (openKycEditButton) {
                                openKycEditButton.addEventListener('click', () => { q('kycEditCif').value = kyc.cif; q('kycEditTin').value = kyc.tin; q('kycEditStatus').value = kyc.status; q('kycEditDateVerified').value = kyc.dateVerified; q('kycEditVerifiedBy').value = kyc.verifiedBy; ['kycErrorCif','kycErrorTin','kycErrorDateVerified','kycErrorVerifiedBy'].forEach((id) => q(id).classList.add('hidden')); open(q('kycEditModal')); });
                            }
                            [q('closeKycEditModal'), q('cancelKycEdit')].forEach((b) => b.addEventListener('click', () => close(q('kycEditModal'))));
                            [q('closeDocumentModal'), q('cancelDocumentModal')].forEach((b) => b.addEventListener('click', () => close(q('documentModal'))));
                            [q('closeDocumentViewModal'), q('closeDocumentViewFooter')].forEach((b) => b.addEventListener('click', () => close(q('documentViewModal'))));
                            q('cancelRejectKyc').addEventListener('click', () => close(q('rejectKycModal')));

                            q('kycEditForm').addEventListener('submit', (e) => {
                                e.preventDefault();
                                const s = q('kycEditStatus').value; const req = s === 'Approved';
                                const okCif = !!q('kycEditCif').value.trim(), okTin = !!q('kycEditTin').value.trim(), okDate = !req || !!q('kycEditDateVerified').value, okBy = !req || !!q('kycEditVerifiedBy').value.trim();
                                q('kycErrorCif').classList.toggle('hidden', okCif); q('kycErrorTin').classList.toggle('hidden', okTin); q('kycErrorDateVerified').classList.toggle('hidden', okDate); q('kycErrorVerifiedBy').classList.toggle('hidden', okBy);
                                if (!(okCif && okTin && okDate && okBy)) return;
                                kyc = { ...kyc, cif: q('kycEditCif').value.trim(), tin: q('kycEditTin').value.trim(), status: s, dateVerified: q('kycEditDateVerified').value, verifiedBy: q('kycEditVerifiedBy').value.trim() };
                                addLog(`KYC information updated by ${mockUser}`); render(); close(q('kycEditModal'));
                            });

                            q('docFileInput').addEventListener('change', () => {
                                const f = q('docFileInput').files?.[0];
                                if (!f) return;
                                file = f;
                                fileUrl = URL.createObjectURL(f);
                                q('docFileNameLabel').textContent = 'Replace file';
                                q('docCurrentFileMeta').textContent = `Replacement file: ${f.name}`;
                                q('clearDocFileBtn').classList.remove('hidden');
                                renderPreview(f.name, fileUrl, f.type || '');
                            });
                            q('clearDocFileBtn').addEventListener('click', () => {
                                file = null;
                                fileUrl = '';
                                q('docFileInput').value = '';
                                q('clearDocFileBtn').classList.add('hidden');
                                renderActiveDocument();
                            });
                            q('removeCifSignedDocumentBtn')?.addEventListener('click', () => {
                                const activeFile = currentDocs[currentIndex] || {};
                                if (!activeFile?.file_name || !window.confirm('Delete this file?')) return;
                                if (activeDoc === 'cif_signed_document') {
                                    q('removeCifSignedDocumentForm').submit();
                                    return;
                                }
                                q('removeKycDocumentForm').setAttribute('action', <?php echo \Illuminate\Support\Js::from(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => '__REQUIREMENT__']))->toHtml() ?>.replace('__REQUIREMENT__', activeDoc));
                                q('removeKycDocumentIndex').value = String(currentIndex);
                                q('removeKycDocumentForm').submit();
                            });
                            q('documentForm').addEventListener('submit', (event) => {
                                const activeFile = currentDocs[currentIndex] || {};
                                const hasExistingFile = !!activeFile?.file_name;
                                const hasReplacementFile = !!q('docFileInput').files?.[0];
                                q('docErrorFile').classList.toggle('hidden', hasExistingFile || hasReplacementFile);
                                if (!hasExistingFile && !hasReplacementFile) {
                                    event.preventDefault();
                                }
                            });
                            q('submitKycForVerificationForm')?.addEventListener('submit', (event) => {
                                if (!allRequiredUploaded()) {
                                    event.preventDefault();
                                    const missingLabels = requiredKycRequirementKeys
                                        .filter((key) => kycRequirementState[key]?.complete !== true)
                                        .map((key) => kycRequirementLabels[key] || key);
                                    q('kycActionWarning').textContent = `Please complete the following before submitting for verification: ${missingLabels.join(', ')}.`;
                                    q('kycActionWarning').classList.remove('hidden');
                                    setTimeout(() => q('kycActionWarning').classList.add('hidden'), 3400);
                                    return;
                                }
                            });
                            q('rejectKycBtn')?.addEventListener('click', () => {
                                if (!kyc.submitted) {
                                    q('kycActionWarning').textContent = 'Submit for verification first before rejecting.';
                                    q('kycActionWarning').classList.remove('hidden');
                                    setTimeout(() => q('kycActionWarning').classList.add('hidden'), 3200);
                                    return;
                                }
                                open(q('rejectKycModal'));
                            });
                            q('confirmRejectKyc').addEventListener('click', () => {
                                const reason = q('rejectReasonInput').value.trim();
                                const reasonField = q('rejectReasonField');
                                if (reasonField) {
                                    reasonField.value = reason;
                                }
                                q('rejectKycForm')?.submit();
                            });
                            [q('kycEditModal'), q('documentModal'), q('documentViewModal'), q('rejectKycModal')].forEach((m) => {
                                m.querySelector('[data-slideover-overlay]')?.addEventListener('click', () => close(m));
                            });
                            document.addEventListener('keydown', (event) => {
                                if (event.key !== 'Escape') return;
                                [q('kycEditModal'), q('documentModal'), q('documentViewModal'), q('rejectKycModal')].forEach((m) => {
                                    if (!m.classList.contains('hidden')) close(m);
                                });
                            });
                            render();
                        });

                        window.addEventListener('load', function () {
                            if (window.location.hash === '#kyc') {
                                document.getElementById('kyc')?.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    </script>
                </div>
                </div>
            <?php endif; ?>

            <?php if($tab === 'history'): ?>
                <?php
                    $historyChips = [
                        ['key' => 'all', 'label' => 'All Activities'],
                        ['key' => 'profile', 'label' => 'Profile Changes'],
                        ['key' => 'kyc', 'label' => 'KYC Updates'],
                        ['key' => 'deals', 'label' => 'Deals'],
                        ['key' => 'files', 'label' => 'Files'],
                        ['key' => 'notes', 'label' => 'Notes'],
                    ];

                    $typeStyles = [
                        'deals' => [
                            'badge' => 'bg-amber-100 text-amber-600',
                            'icon' => 'fa-arrow-trend-up',
                        ],
                        'notes' => [
                            'badge' => 'bg-yellow-100 text-yellow-700',
                            'icon' => 'fa-note-sticky',
                        ],
                        'profile' => [
                            'badge' => 'bg-blue-100 text-blue-600',
                            'icon' => 'fa-pen',
                        ],
                        'kyc' => [
                            'badge' => 'bg-green-100 text-green-600',
                            'icon' => 'fa-shield-halved',
                        ],
                        'files' => [
                            'badge' => 'bg-indigo-100 text-indigo-600',
                            'icon' => 'fa-file-arrow-up',
                        ],
                    ];
                ?>

                <div id="historyFeed" class="rounded-xl bg-white">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50" aria-label="Filter">
                            <i class="fas fa-filter text-sm"></i>
                        </button>
                        <?php $__currentLoopData = $historyChips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button
                                type="button"
                                data-history-chip="<?php echo e($chip['key']); ?>"
                                class="history-chip rounded-lg border px-3 py-1.5 text-sm <?php echo e($chip['key'] === 'all' ? 'border-blue-200 bg-blue-700 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'); ?>"
                            >
                                <?php echo e($chip['label']); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <span id="historyRecordCount" class="ml-auto text-sm text-gray-500"><?php echo e(count($tabData['history']['items'])); ?> records</span>
                    </div>

                    <div class="relative space-y-4 pl-12 before:absolute before:bottom-2 before:left-4 before:top-2 before:w-px before:bg-gray-200">
                        <?php $__currentLoopData = $tabData['history']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $type = $item['type'] ?? 'profile';
                                $style = $typeStyles[$type] ?? $typeStyles['profile'];
                            ?>
                            <article data-history-item data-history-type="<?php echo e($type); ?>" class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                                <span class="absolute -left-12 top-6 z-10 flex h-9 w-9 items-center justify-center rounded-full <?php echo e($style['badge']); ?>">
                                    <i class="fas <?php echo e($style['icon']); ?> text-xs"></i>
                                </span>

                                <h3 class="text-lg font-semibold leading-tight text-gray-900"><?php echo e($item['title']); ?></h3>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($item['description']); ?></p>

                                <?php if(!empty($item['extraLabel']) && !empty($item['extraValue'])): ?>
                                    <div class="mt-3 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        <span class="font-medium text-gray-700"><?php echo e($item['extraLabel']); ?>:</span> <?php echo e($item['extraValue']); ?>

                                    </div>
                                <?php endif; ?>

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700"><?php echo e($item['initials']); ?></span>
                                    <span><?php echo e($item['user']); ?></span>
                                    <span><i class="far fa-clock mr-1"></i><?php echo e($item['datetime']); ?></span>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const feed = document.getElementById('historyFeed');
                        if (!feed) {
                            return;
                        }

                        const chips = Array.from(feed.querySelectorAll('[data-history-chip]'));
                        const items = Array.from(feed.querySelectorAll('[data-history-item]'));
                        const countLabel = document.getElementById('historyRecordCount');

                        function setActiveChip(activeKey) {
                            chips.forEach((chip) => {
                                const isActive = chip.dataset.historyChip === activeKey;
                                chip.classList.toggle('bg-blue-700', isActive);
                                chip.classList.toggle('text-white', isActive);
                                chip.classList.toggle('border-blue-200', isActive);
                                chip.classList.toggle('bg-white', !isActive);
                                chip.classList.toggle('text-gray-700', !isActive);
                                chip.classList.toggle('border-gray-200', !isActive);
                            });
                        }

                        function applyFilter(filterKey) {
                            let visibleCount = 0;

                            items.forEach((item) => {
                                const itemType = item.dataset.historyType;
                                const visible = filterKey === 'all' || itemType === filterKey;
                                item.classList.toggle('hidden', !visible);
                                if (visible) {
                                    visibleCount += 1;
                                }
                            });

                            countLabel.textContent = `${visibleCount} records`;
                            setActiveChip(filterKey);
                        }

                        chips.forEach((chip) => {
                            chip.addEventListener('click', function () {
                                applyFilter(chip.dataset.historyChip);
                            });
                        });

                        applyFilter('all');
                    });
                </script>
            <?php endif; ?>

            <?php if($tab === 'consultation-notes'): ?>
                <div id="consultationNotesApp">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">Consultation Notes</h2>
                            <p class="text-sm text-gray-500">Record and track all consultation sessions</p>
                        </div>
                        <button id="openConsultationNoteModal" type="button" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                            + Add Consultation Note
                        </button>
                    </div>

                    <div id="consultationNotesList" class="space-y-3"></div>

                    <div id="consultationFormModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 id="consultationFormTitle" class="text-xl font-semibold text-gray-900">Add Consultation Note</h3>
                                <button id="closeConsultationFormModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="consultationForm" class="flex min-h-0 flex-1 flex-col">
                                <div class="min-h-0 flex-1 overflow-y-auto p-6 sm:px-8">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label for="noteTitle" class="mb-1 block text-sm font-medium text-gray-700">Note Title</label>
                                        <input id="noteTitle" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <p id="errorTitle" class="mt-1 hidden text-xs text-red-600">Note title is required.</p>
                                    </div>
                                    <div>
                                        <label for="consultationDate" class="mb-1 block text-sm font-medium text-gray-700">Consultation Date</label>
                                        <input id="consultationDate" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <p id="errorDate" class="mt-1 hidden text-xs text-red-600">Consultation date is required.</p>
                                    </div>
                                    <div>
                                        <label for="consultationAuthor" class="mb-1 block text-sm font-medium text-gray-700">Author / Created By</label>
                                        <input id="consultationAuthor" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationCategory" class="mb-1 block text-sm font-medium text-gray-700">Tags or Category</label>
                                        <input id="consultationCategory" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="e.g. Budget Review">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationSummary" class="mb-1 block text-sm font-medium text-gray-700">Consultation Summary</label>
                                        <textarea id="consultationSummary" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationDetails" class="mb-1 block text-sm font-medium text-gray-700">Detailed Notes</label>
                                        <textarea id="consultationDetails" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                        <p id="errorBody" class="mt-1 hidden text-xs text-red-600">Provide a summary or detailed notes.</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Attachments</label>
                                        <label for="consultationAttachments" class="flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-sm text-gray-600 hover:bg-gray-100">
                                            <span><i class="fas fa-paperclip mr-2"></i>Upload files</span>
                                        </label>
                                        <input id="consultationAttachments" type="file" multiple class="hidden">
                                        <div id="selectedAttachments" class="mt-2 space-y-2"></div>
                                    </div>
                                </div>
                                </div>
                                <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="cancelConsultationForm" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button id="saveConsultationNote" type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save Consultation Note</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <div id="consultationViewModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[620px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 class="text-xl font-semibold text-gray-900">Consultation Note Details</h3>
                                <button id="closeConsultationViewModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-5 text-sm sm:px-8">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Note Title</p>
                                    <p id="viewNoteTitle" class="mt-1 text-base font-semibold text-gray-900"></p>
                                </div>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Consultation Date</p>
                                        <p id="viewConsultationDate" class="mt-1 text-gray-800"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Author</p>
                                        <p id="viewConsultationAuthor" class="mt-1 text-gray-800"></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Summary</p>
                                    <p id="viewConsultationSummary" class="mt-1 text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Detailed Notes</p>
                                    <p id="viewConsultationDetails" class="mt-1 whitespace-pre-wrap text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attached Files</p>
                                    <div id="viewConsultationAttachments" class="mt-2 space-y-2"></div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 sm:px-8">
                                <button id="editFromView" type="button" class="h-10 rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm text-blue-700 hover:bg-blue-100">Edit</button>
                                <button id="closeConsultationViewFooter" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const app = document.getElementById('consultationNotesApp');
                        if (!app) {
                            return;
                        }

                        const notesList = document.getElementById('consultationNotesList');
                        const openButton = document.getElementById('openConsultationNoteModal');
                        const formModal = document.getElementById('consultationFormModal');
                        const viewModal = document.getElementById('consultationViewModal');
                        const formTitle = document.getElementById('consultationFormTitle');
                        const form = document.getElementById('consultationForm');
                        const saveButton = document.getElementById('saveConsultationNote');
                        const attachmentInput = document.getElementById('consultationAttachments');
                        const selectedAttachments = document.getElementById('selectedAttachments');

                        const fields = {
                            title: document.getElementById('noteTitle'),
                            consultationDate: document.getElementById('consultationDate'),
                            author: document.getElementById('consultationAuthor'),
                            summary: document.getElementById('consultationSummary'),
                            details: document.getElementById('consultationDetails'),
                            category: document.getElementById('consultationCategory'),
                        };

                        const errors = {
                            title: document.getElementById('errorTitle'),
                            consultationDate: document.getElementById('errorDate'),
                            body: document.getElementById('errorBody'),
                        };

                        const defaultAuthor = <?php echo json_encode($contact->owner_name ?: 'John Admin', 15, 512) ?>;
                        let notes = <?php echo json_encode($tabData['consultation-notes'], 15, 512) ?>;
                        let editNoteId = null;
                        let viewNoteId = null;
                        let formAttachments = [];

                        const escapeHtml = (value) => String(value || '')
                            .replaceAll('&', '&amp;')
                            .replaceAll('<', '&lt;')
                            .replaceAll('>', '&gt;')
                            .replaceAll('"', '&quot;')
                            .replaceAll("'", '&#039;');

                        const formatDate = (value) => {
                            if (!value) return '-';
                            const date = new Date(value + 'T00:00:00');
                            return Number.isNaN(date.getTime())
                                ? value
                                : new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(date);
                        };

                        const formatBytes = (bytes) => {
                            if (!bytes || Number.isNaN(Number(bytes))) return '-';
                            if (bytes < 1024) return `${bytes} B`;
                            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
                            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
                        };

                        const normalizeType = (name, rawType) => {
                            if (rawType) {
                                const parts = rawType.split('/');
                                if (parts.length > 1 && parts[1]) return parts[1].toUpperCase();
                            }
                            const ext = name.includes('.') ? name.split('.').pop() : 'FILE';
                            return String(ext).toUpperCase();
                        };

                        const sortNotes = () => {
                            notes.sort((a, b) => {
                                const left = new Date(b.consultationDate || 0).getTime();
                                const right = new Date(a.consultationDate || 0).getTime();
                                if (left !== right) return left - right;
                                return new Date(b.updatedAt || 0).getTime() - new Date(a.updatedAt || 0).getTime();
                            });
                        };

                        const renderAttachmentsForForm = () => {
                            if (!formAttachments.length) {
                                selectedAttachments.innerHTML = '<p class="text-xs text-gray-500">No files selected.</p>';
                                return;
                            }
                            selectedAttachments.innerHTML = formAttachments.map((file) => `
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                        <p class="text-xs text-gray-500">${escapeHtml(file.type || 'FILE')} | ${escapeHtml(formatBytes(file.size))}</p>
                                    </div>
                                    <button type="button" class="remove-attachment text-gray-500 hover:text-red-600" data-file-id="${file.id}">
                                        <i class="fas fa-xmark"></i>
                                    </button>
                                </div>
                            `).join('');
                        };

                        const renderNotes = () => {
                            sortNotes();
                            if (!notes.length) {
                                notesList.innerHTML = '<div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-500">No consultation notes yet.</div>';
                                return;
                            }
                            notesList.innerHTML = notes.map((note) => {
                                const attachmentCount = (note.attachments || []).length;
                                const attachmentLabel = `${attachmentCount} attachment${attachmentCount === 1 ? '' : 's'}`;
                                return `
                                    <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="text-xl font-semibold text-gray-900">${escapeHtml(note.title)}</h3>
                                                <p class="mt-1 text-sm text-gray-600">${escapeHtml(note.summary || note.details || '')}</p>
                                                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                                    <span><i class="far fa-calendar mr-1"></i>${escapeHtml(formatDate(note.consultationDate))}</span>
                                                    <span><i class="far fa-user mr-1"></i>${escapeHtml(note.author || defaultAuthor)}</span>
                                                    <span><i class="fas fa-paperclip mr-1"></i>${attachmentLabel}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 text-gray-500">
                                                <button type="button" class="note-view hover:text-blue-600" data-note-id="${note.id}" aria-label="View note"><i class="far fa-eye"></i></button>
                                                <button type="button" class="note-edit hover:text-blue-600" data-note-id="${note.id}" aria-label="Edit note"><i class="far fa-pen-to-square"></i></button>
                                            </div>
                                        </div>
                                    </article>
                                `;
                            }).join('');
                        };

                        const showModal = (modal) => {
                            const panel = modal.querySelector('[data-slideover-panel]');
                            const overlay = modal.querySelector('[data-slideover-overlay]');
                            modal.classList.remove('hidden');
                            modal.setAttribute('aria-hidden', 'false');
                            document.body.classList.add('overflow-hidden');
                            requestAnimationFrame(() => {
                                overlay?.classList.remove('opacity-0');
                                panel?.classList.remove('translate-x-full');
                            });
                        };

                        const hideModal = (modal) => {
                            const panel = modal.querySelector('[data-slideover-panel]');
                            const overlay = modal.querySelector('[data-slideover-overlay]');
                            overlay?.classList.add('opacity-0');
                            panel?.classList.add('translate-x-full');
                            window.setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.setAttribute('aria-hidden', 'true');
                                if (formModal.classList.contains('hidden') && viewModal.classList.contains('hidden')) {
                                    document.body.classList.remove('overflow-hidden');
                                }
                            }, 300);
                        };

                        const resetValidation = () => {
                            Object.values(errors).forEach((el) => el.classList.add('hidden'));
                        };

                        const resetForm = () => {
                            editNoteId = null;
                            fields.title.value = '';
                            fields.consultationDate.value = '';
                            fields.author.value = defaultAuthor;
                            fields.summary.value = '';
                            fields.details.value = '';
                            fields.category.value = '';
                            formAttachments = [];
                            attachmentInput.value = '';
                            resetValidation();
                            renderAttachmentsForForm();
                        };

                        const openAddModal = () => {
                            resetForm();
                            formTitle.textContent = 'Add Consultation Note';
                            saveButton.textContent = 'Save Consultation Note';
                            showModal(formModal);
                        };

                        const openEditModal = (noteId) => {
                            const note = notes.find((item) => Number(item.id) === Number(noteId));
                            if (!note) return;

                            editNoteId = Number(note.id);
                            fields.title.value = note.title || '';
                            fields.consultationDate.value = note.consultationDate || '';
                            fields.author.value = note.author || defaultAuthor;
                            fields.summary.value = note.summary || '';
                            fields.details.value = note.details || '';
                            fields.category.value = note.category || '';
                            formAttachments = (note.attachments || []).map((file) => ({ ...file }));
                            attachmentInput.value = '';
                            resetValidation();
                            renderAttachmentsForForm();

                            formTitle.textContent = 'Edit Consultation Note';
                            saveButton.textContent = 'Update Consultation Note';
                            showModal(formModal);
                        };

                        const openViewModal = (noteId) => {
                            const note = notes.find((item) => Number(item.id) === Number(noteId));
                            if (!note) return;

                            viewNoteId = Number(note.id);
                            document.getElementById('viewNoteTitle').textContent = note.title || '-';
                            document.getElementById('viewConsultationDate').textContent = formatDate(note.consultationDate);
                            document.getElementById('viewConsultationAuthor').textContent = note.author || '-';
                            document.getElementById('viewConsultationSummary').textContent = note.summary || '-';
                            document.getElementById('viewConsultationDetails').textContent = note.details || '-';

                            const viewAttachmentList = document.getElementById('viewConsultationAttachments');
                            const attachments = note.attachments || [];
                            if (!attachments.length) {
                                viewAttachmentList.innerHTML = '<p class="text-xs text-gray-500">No attachments</p>';
                            } else {
                                viewAttachmentList.innerHTML = attachments.map((file) => `
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                            <p class="text-xs text-gray-500">${escapeHtml(file.type || 'FILE')} | ${escapeHtml(formatBytes(file.size))}</p>
                                        </div>
                                        <a href="${escapeHtml(file.url || '#')}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:text-blue-700">View</a>
                                    </div>
                                `).join('');
                            }

                            showModal(viewModal);
                        };

                        const validateForm = () => {
                            resetValidation();
                            let valid = true;
                            if (!fields.title.value.trim()) {
                                errors.title.classList.remove('hidden');
                                valid = false;
                            }
                            if (!fields.consultationDate.value) {
                                errors.consultationDate.classList.remove('hidden');
                                valid = false;
                            }
                            if (!fields.summary.value.trim() && !fields.details.value.trim()) {
                                errors.body.classList.remove('hidden');
                                valid = false;
                            }
                            return valid;
                        };

                        openButton.addEventListener('click', openAddModal);

                        document.getElementById('closeConsultationFormModal').addEventListener('click', () => hideModal(formModal));
                        document.getElementById('cancelConsultationForm').addEventListener('click', () => hideModal(formModal));
                        document.getElementById('closeConsultationViewModal').addEventListener('click', () => hideModal(viewModal));
                        document.getElementById('closeConsultationViewFooter').addEventListener('click', () => hideModal(viewModal));
                        document.getElementById('editFromView').addEventListener('click', () => {
                            hideModal(viewModal);
                            if (viewNoteId !== null) openEditModal(viewNoteId);
                        });

                        [formModal, viewModal].forEach((modal) => {
                            modal.querySelector('[data-slideover-overlay]')?.addEventListener('click', () => hideModal(modal));
                        });
                        document.addEventListener('keydown', function (event) {
                            if (event.key !== 'Escape') return;
                            [formModal, viewModal].forEach((modal) => {
                                if (!modal.classList.contains('hidden')) hideModal(modal);
                            });
                        });

                        attachmentInput.addEventListener('change', function () {
                            const files = Array.from(attachmentInput.files || []);
                            if (!files.length) return;

                            files.forEach((file, index) => {
                                formAttachments.push({
                                    id: Date.now() + index + Math.floor(Math.random() * 1000),
                                    name: file.name,
                                    type: normalizeType(file.name, file.type),
                                    size: file.size || 0,
                                    url: URL.createObjectURL(file),
                                });
                            });

                            attachmentInput.value = '';
                            renderAttachmentsForForm();
                        });

                        selectedAttachments.addEventListener('click', function (event) {
                            const button = event.target.closest('.remove-attachment');
                            if (!button) return;
                            const targetId = Number(button.dataset.fileId);
                            formAttachments = formAttachments.filter((file) => Number(file.id) !== targetId);
                            renderAttachmentsForForm();
                        });

                        notesList.addEventListener('click', function (event) {
                            const viewBtn = event.target.closest('.note-view');
                            const editBtn = event.target.closest('.note-edit');

                            if (viewBtn) {
                                openViewModal(viewBtn.dataset.noteId);
                            }
                            if (editBtn) {
                                openEditModal(editBtn.dataset.noteId);
                            }
                        });

                        form.addEventListener('submit', function (event) {
                            event.preventDefault();
                            if (!validateForm()) return;

                            const now = new Date().toISOString();
                            const payload = {
                                id: editNoteId ?? Date.now(),
                                title: fields.title.value.trim(),
                                consultationDate: fields.consultationDate.value,
                                author: fields.author.value.trim() || defaultAuthor,
                                summary: fields.summary.value.trim(),
                                details: fields.details.value.trim(),
                                category: fields.category.value.trim(),
                                attachments: formAttachments.map((item) => ({ ...item })),
                                createdAt: now,
                                updatedAt: now,
                            };

                            if (editNoteId !== null) {
                                notes = notes.map((item) => Number(item.id) === editNoteId
                                    ? { ...item, ...payload, createdAt: item.createdAt || now, updatedAt: now }
                                    : item);
                            } else {
                                notes.push(payload);
                            }

                            renderNotes();
                            hideModal(formModal);
                        });

                        renderNotes();
                    });
                </script>
            <?php endif; ?>

            <?php if($tab === 'activities'): ?>
                <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-phone mr-1"></i>Log Call</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-video mr-1"></i>Schedule Meeting</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-envelope mr-1"></i>Send Email</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-square-check mr-1"></i>Add Task</button>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-2xl font-semibold text-gray-900">Activity Timeline</h2>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $tabData['activities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                            <i class="fas <?php echo e($activity['icon']); ?> text-xs"></i>
                                        </span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo e($activity['type']); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo e($activity['description']); ?></p>
                                            <p class="mt-2 text-xs text-gray-500"><?php echo e($activity['when']); ?> | <?php echo e($activity['owner']); ?></p>
                                        </div>
                                    </div>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($activity['status'] === 'Completed' ? 'bg-green-100 text-green-700' : ($activity['status'] === 'Sent' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')); ?>">
                                        <?php echo e($activity['status']); ?>

                                    </span>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($tab === 'deals'): ?>
                <?php
                    $dealStageClasses = [
                        'Inquiry' => 'bg-slate-100 text-slate-700 border border-slate-200',
                        'Qualification' => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'Consultation' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                        'Proposal' => 'bg-cyan-100 text-cyan-700 border border-cyan-200',
                        'Negotiation' => 'bg-amber-100 text-amber-700 border border-amber-200',
                        'Payment' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'Activation' => 'bg-violet-100 text-violet-700 border border-violet-200',
                        'Closed Lost' => 'bg-red-100 text-red-700 border border-red-200',
                    ];
                    $dealStatusClasses = [
                        'Open' => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'Won' => 'bg-green-100 text-green-700 border border-green-200',
                        'Lost' => 'bg-red-100 text-red-700 border border-red-200',
                        'Pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
                    ];
                ?>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Related Deals</h2>
                        <p class="text-sm text-gray-500">Track all deals associated with this contact</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Add Deal</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Deal Name</th>
                                <th class="px-3 py-3 text-left">Stage</th>
                                <th class="px-3 py-3 text-left">Amount</th>
                                <th class="px-3 py-3 text-left">Closing Date</th>
                                <th class="px-3 py-3 text-left">Owner</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $tabData['deals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><?php echo e($deal['name']); ?></td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($dealStageClasses[$deal['stage']] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>">
                                            <?php echo e($deal['stage']); ?>

                                        </span>
                                    </td>
                                    <td class="px-3 py-3 font-semibold text-blue-600"><?php echo e($deal['amount']); ?></td>
                                    <td class="px-3 py-3 text-gray-700"><?php echo e($deal['closing_date']); ?></td>
                                    <td class="px-3 py-3 text-gray-700"><?php echo e($deal['owner']); ?></td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($dealStatusClasses[$deal['status']] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>">
                                            <?php echo e($deal['status']); ?>

                                        </span>
                                    </td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if($tab === 'company'): ?>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Related Companies</h2>
                        <p class="text-sm text-gray-500">Track companies linked to this contact.</p>
                    </div>
                    <a
                        href="<?php echo e(route('company.index', ['prefill_contact' => $contact->id, 'open_add_company' => 1])); ?>"
                        class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        + Add Company
                    </a>
                </div>
                <?php
                    $companyCustomFields = $companyCustomFields ?? [];
                ?>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <form method="GET" action="<?php echo e(route('contacts.show', $contact->id)); ?>" class="flex flex-wrap items-center gap-3">
                            <input type="hidden" name="tab" value="company">
                            <div class="relative w-full max-w-md">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input
                                    type="text"
                                    name="company_search"
                                    value="<?php echo e($companySearch); ?>"
                                    placeholder="Search company name..."
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                                <tr>
                                    <th class="w-10 px-3 py-3 text-left"><input type="checkbox" class="h-4 w-4 rounded border-gray-300"></th>
                                    <th class="px-3 py-3 text-left">Company Name</th>
                                    <th class="px-3 py-3 text-left">Email</th>
                                    <th class="px-3 py-3 text-left">Phone</th>
                                    <th class="px-3 py-3 text-left">Owner</th>
                                    <th class="px-3 py-3 text-left">Status</th>
                                    <th class="px-3 py-3 text-left">Action</th>
                                    <?php $__currentLoopData = $companyCustomFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="px-3 py-3 text-left"><?php echo e($field['name']); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th class="px-3 py-3 text-right normal-case">
                                        <button id="openContactCompanyCreateFieldDropdown" type="button" class="text-sm font-medium text-blue-600 hover:text-blue-700">+ Create Field</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $companyTabCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="text-gray-700">
                                        <td class="px-3 py-3"><input type="checkbox" class="h-4 w-4 rounded border-gray-300"></td>
                                        <td class="px-3 py-3 font-medium text-gray-900"><?php echo e($companyItem['company_name']); ?></td>
                                        <td class="px-3 py-3"><?php echo e($companyItem['email']); ?></td>
                                        <td class="px-3 py-3"><?php echo e($companyItem['phone']); ?></td>
                                        <td class="px-3 py-3"><?php echo e($companyItem['owner']); ?></td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                                <?php echo e($companyItem['status']); ?>

                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">
                                                <a href="<?php echo e($companyItem['show_url']); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                    <i class="far fa-eye mr-1"></i>View
                                                </a>
                                                <form method="POST" action="<?php echo e(route('contacts.companies.unlink', ['contact' => $contact->id, 'company' => $companyItem['id']])); ?>" onsubmit="return confirm('Unlink this company from the current contact?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="inline-flex h-8 items-center rounded-full border border-red-200 px-3 text-xs font-medium text-red-600 hover:bg-red-50">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <?php $__currentLoopData = $companyCustomFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $customValue = $companyItem['custom_fields'][$field['key']] ?? ($field['default_value'] ?? '');
                                            ?>
                                            <td class="px-3 py-3 text-gray-600">
                                                <?php if(($field['type'] ?? '') === 'checkbox'): ?>
                                                    <?php echo e($customValue === '1' ? 'Yes' : 'No'); ?>

                                                <?php elseif(($field['type'] ?? '') === 'currency' && $customValue !== ''): ?>
                                                    P<?php echo e(number_format((float) $customValue, 2)); ?>

                                                <?php elseif(is_array($customValue)): ?>
                                                    <?php echo e(implode(', ', array_filter($customValue, fn ($value) => filled($value))) ?: '-'); ?>

                                                <?php else: ?>
                                                    <?php echo e($customValue !== '' ? $customValue : '-'); ?>

                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td class="px-3 py-3"></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="<?php echo e(8 + count($companyCustomFields)); ?>" class="px-3 py-10 text-center text-sm text-gray-500">No related companies found for this contact.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php echo $__env->make('products.partials.create-field-dropdown', [
                    'fieldTypes' => $fieldTypes,
                    'dropdownId' => 'contactCompanyCreateFieldDropdownMenu',
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('products.partials.create-field-modal', [
                    'createFieldActionRoute' => route('company.custom-fields.store'),
                    'lookupModules' => $lookupModules,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const openCreateFieldDropdown = document.getElementById('openContactCompanyCreateFieldDropdown');
                        const createFieldDropdownMenu = document.getElementById('contactCompanyCreateFieldDropdownMenu');
                        const fieldTypeButtons = Array.from(document.querySelectorAll('#contactCompanyCreateFieldDropdownMenu .create-field-type-option'));
                        const createFieldModal = document.getElementById('createFieldModal');
                        const createFieldPanel = document.getElementById('createFieldPanel');
                        const createFieldModalOverlay = document.getElementById('createFieldModalOverlay');
                        const closeCreateFieldModal = document.getElementById('closeCreateFieldModal');
                        const cancelCreateFieldModal = document.getElementById('cancelCreateFieldModal');
                        const createFieldTypeInput = document.getElementById('createFieldTypeInput');
                        const createFieldTypeLabel = document.getElementById('createFieldTypeLabel');
                        const picklistOptionsSection = document.getElementById('picklistOptionsSection');
                        const picklistOptionsContainer = document.getElementById('picklistOptionsContainer');
                        const addPicklistOption = document.getElementById('addPicklistOption');
                        const defaultValueSection = document.getElementById('defaultValueSection');
                        const lookupSection = document.getElementById('lookupSection');
                        const defaultValueInput = document.getElementById('default_value');
                        let createFieldDropdownOpen = false;

                        if (!openCreateFieldDropdown || !createFieldDropdownMenu) {
                            return;
                        }

                        const buildPicklistOptionRow = (value = '') => {
                            const row = document.createElement('div');
                            row.className = 'flex items-center gap-2';
                            row.innerHTML = `
                                <input name="options[]" value="${value}" placeholder="Option value" class="h-10 flex-1 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <button type="button" class="remove-picklist-option h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                            `;

                            return row;
                        };

                        const ensurePicklistOptionRows = () => {
                            if (!picklistOptionsContainer) {
                                return;
                            }

                            if (picklistOptionsContainer.querySelectorAll('input[name="options[]"]').length === 0) {
                                picklistOptionsContainer.appendChild(buildPicklistOptionRow(''));
                            }
                        };

                        const applyCreateFieldTypeUI = (type, label) => {
                            if (!createFieldTypeInput || !createFieldTypeLabel) {
                                return;
                            }

                            createFieldTypeInput.value = type;
                            createFieldTypeLabel.textContent = label;
                            picklistOptionsSection?.classList.toggle('hidden', type !== 'picklist');
                            lookupSection?.classList.toggle('hidden', type !== 'lookup');
                            lookupSection?.classList.toggle('grid', type === 'lookup');
                            defaultValueSection?.classList.toggle('hidden', type === 'lookup');
                            defaultValueSection?.classList.toggle('grid', type !== 'lookup');

                            if (defaultValueInput) {
                                defaultValueInput.placeholder = type === 'date' ? 'YYYY-MM-DD' : 'Optional default value';
                            }

                            if (type === 'picklist') {
                                ensurePicklistOptionRows();
                            }
                        };

                        const positionCreateFieldDropdown = () => {
                            const rect = openCreateFieldDropdown.getBoundingClientRect();
                            const dropdownWidth = createFieldDropdownMenu.offsetWidth || 256;
                            const viewportPadding = 12;

                            let left = rect.left;
                            if (left + dropdownWidth > window.innerWidth - viewportPadding) {
                                left = rect.right - dropdownWidth;
                            }
                            if (left < viewportPadding) {
                                left = viewportPadding;
                            }

                            let top = rect.bottom + 8;
                            const dropdownHeight = createFieldDropdownMenu.offsetHeight || 320;
                            if (top + dropdownHeight > window.innerHeight - viewportPadding) {
                                top = Math.max(viewportPadding, rect.top - dropdownHeight - 8);
                            }

                            createFieldDropdownMenu.style.left = `${left}px`;
                            createFieldDropdownMenu.style.top = `${top}px`;
                        };

                        const closeCreateFieldDropdownFn = () => {
                            createFieldDropdownMenu.classList.add('hidden');
                            createFieldDropdownOpen = false;
                        };

                        const openCreateFieldModalFn = (type, label) => {
                            applyCreateFieldTypeUI(type, label);
                            closeCreateFieldDropdownFn();
                            createFieldModal?.classList.remove('hidden');
                            createFieldModal?.setAttribute('aria-hidden', 'false');
                            document.body.classList.add('overflow-hidden');
                            requestAnimationFrame(() => {
                                createFieldModalOverlay?.classList.remove('opacity-0');
                                createFieldPanel?.classList.remove('translate-x-full');
                            });
                        };

                        const closeCreateFieldModalFn = () => {
                            createFieldModalOverlay?.classList.add('opacity-0');
                            createFieldPanel?.classList.add('translate-x-full');
                            document.body.classList.remove('overflow-hidden');
                            window.setTimeout(() => {
                                createFieldModal?.classList.add('hidden');
                                createFieldModal?.setAttribute('aria-hidden', 'true');
                            }, 300);
                        };

                        openCreateFieldDropdown.addEventListener('click', function () {
                            if (createFieldDropdownOpen) {
                                closeCreateFieldDropdownFn();
                                return;
                            }

                            createFieldDropdownMenu.classList.remove('hidden');
                            createFieldDropdownOpen = true;
                            positionCreateFieldDropdown();
                        });

                        fieldTypeButtons.forEach((button) => {
                            button.addEventListener('click', function () {
                                const type = button.dataset.fieldType || 'picklist';
                                const label = button.dataset.fieldLabel || 'Picklist';
                                openCreateFieldModalFn(type, label);
                            });
                        });

                        closeCreateFieldModal?.addEventListener('click', closeCreateFieldModalFn);
                        cancelCreateFieldModal?.addEventListener('click', closeCreateFieldModalFn);
                        createFieldModalOverlay?.addEventListener('click', closeCreateFieldModalFn);

                        document.addEventListener('click', function (event) {
                            const clickedFieldTrigger = openCreateFieldDropdown.contains(event.target);
                            if (createFieldDropdownOpen && !createFieldDropdownMenu.contains(event.target) && !clickedFieldTrigger) {
                                closeCreateFieldDropdownFn();
                            }
                        });

                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'Escape') {
                                closeCreateFieldDropdownFn();
                                closeCreateFieldModalFn();
                            }
                        });

                        window.addEventListener('resize', function () {
                            if (createFieldDropdownOpen) {
                                positionCreateFieldDropdown();
                            }
                        });

                        document.addEventListener('scroll', function () {
                            if (createFieldDropdownOpen) {
                                positionCreateFieldDropdown();
                            }
                        }, true);

                        addPicklistOption?.addEventListener('click', function () {
                            picklistOptionsContainer?.appendChild(buildPicklistOptionRow(''));
                        });

                        picklistOptionsContainer?.addEventListener('click', function (event) {
                            const button = event.target.closest('.remove-picklist-option');
                            if (!button) {
                                return;
                            }

                            button.closest('.flex')?.remove();
                            ensurePicklistOptionRows();
                        });

                        const initialFieldType = createFieldTypeInput ? createFieldTypeInput.value : 'picklist';
                        const initialTypeButton = fieldTypeButtons.find((button) => (button.dataset.fieldType || '') === initialFieldType);
                        applyCreateFieldTypeUI(initialFieldType, initialTypeButton?.dataset.fieldLabel || 'Picklist');
                    });
                </script>
            <?php endif; ?>

            <?php if($tab === 'projects'): ?>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Projects</h2>
                        <p class="text-sm text-gray-500">Manage projects associated with this contact</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Create Project</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Project Name</th>
                                <th class="px-3 py-3 text-left">Project Type</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Start Date</th>
                                <th class="px-3 py-3 text-left">Assigned Team</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $tabData['projects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><?php echo e($project['name']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($project['type']); ?></td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs <?php echo e($project['status'] === 'In Progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'); ?>">
                                            <?php echo e($project['status']); ?>

                                        </span>
                                    </td>
                                    <td class="px-3 py-3"><?php echo e($project['start_date']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($project['team']); ?></td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if($tab === 'regular'): ?>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Recurring Services</h2>
                        <p class="text-sm text-gray-500">Manage retainer and subscription services</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Add Recurring Service</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Service Name</th>
                                <th class="px-3 py-3 text-left">Frequency</th>
                                <th class="px-3 py-3 text-left">Fee</th>
                                <th class="px-3 py-3 text-left">Start Date</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $tabData['regular']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><?php echo e($item['service']); ?></td>
                                    <td class="px-3 py-3"><span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700"><?php echo e($item['frequency']); ?></span></td>
                                    <td class="px-3 py-3 font-semibold text-blue-600"><?php echo e($item['fee']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['start_date']); ?></td>
                                    <td class="px-3 py-3"><span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"><?php echo e($item['status']); ?></span></td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50 p-5">
                    <p class="text-sm text-gray-600">Total Monthly Recurring Revenue</p>
                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-4xl font-semibold text-blue-700"><?php echo e($tabData['regular']['revenue']); ?></p>
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-2xl text-blue-600 shadow-sm">$</span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($tab === 'products'): ?>
                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-gray-900">Purchased Products</h2>
                    <p class="text-sm text-gray-500">View all products purchased by this contact</p>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Product Name</th>
                                <th class="px-3 py-3 text-left">Price</th>
                                <th class="px-3 py-3 text-left">Quantity</th>
                                <th class="px-3 py-3 text-left">Total</th>
                                <th class="px-3 py-3 text-left">Date Purchased</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $tabData['products']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><i class="far fa-cube mr-2 text-blue-600"></i><?php echo e($item['name']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['price']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['quantity']); ?></td>
                                    <td class="px-3 py-3 font-semibold text-blue-600"><?php echo e($item['total']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['date']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-3 py-3 text-right font-semibold text-gray-700">Grand Total:</td>
                                <td colspan="2" class="px-3 py-3 text-xl font-semibold text-blue-700"><?php echo e($tabData['products']['grand_total']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Products</p>
                        <p class="text-4xl font-semibold text-gray-900"><?php echo e($tabData['products']['total_products']); ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Quantity</p>
                        <p class="text-4xl font-semibold text-gray-900"><?php echo e($tabData['products']['total_quantity']); ?></p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-4xl font-semibold text-blue-700"><?php echo e($tabData['products']['total_revenue']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($tab === 'services'): ?>
                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-gray-900">Professional Services</h2>
                    <p class="text-sm text-gray-500">Services delivered to this contact</p>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Service Name</th>
                                <th class="px-3 py-3 text-left">Description</th>
                                <th class="px-3 py-3 text-left">Fee</th>
                                <th class="px-3 py-3 text-left">Assigned Staff</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $tabData['services']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><i class="fas fa-gift mr-2 text-purple-600"></i><?php echo e($item['name']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['description']); ?></td>
                                    <td class="px-3 py-3 font-semibold text-blue-600"><?php echo e($item['fee']); ?></td>
                                    <td class="px-3 py-3"><?php echo e($item['staff']); ?></td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs <?php echo e($item['status'] === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'); ?>">
                                            <?php echo e($item['status']); ?>

                                        </span>
                                    </td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Services</p>
                        <p class="text-4xl font-semibold text-gray-900"><?php echo e($tabData['services']['total_services']); ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Completed</p>
                        <p class="text-4xl font-semibold text-green-700"><?php echo e($tabData['services']['completed']); ?></p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-sm text-gray-500">Total Value</p>
                        <p class="text-4xl font-semibold text-blue-700"><?php echo e($tabData['services']['total_value']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php echo $__env->make('contacts.partials.kyc-intake-modal', ['contact' => $contact], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'sendCifModal','width' => 'sm:max-w-[560px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'sendCifModal','width' => 'sm:max-w-[560px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Send CIF</h2>
                <p class="mt-1 text-sm text-gray-500">Email a secure CIF link to the client so they can complete missing details and upload onboarding documents.</p>
            </div>
            <button type="button" data-close-send-cif-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('contacts.cif.send', $contact->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                The secure link opens a client CIF form. Submitted details update this contact's KYC profile and keep the Contact record as the main source of truth.
            </div>
            <div>
                <label for="cif_recipient_email" class="mb-1 block text-sm font-medium text-gray-700">Recipient Email</label>
                <input id="cif_recipient_email" name="recipient_email" type="email" value="<?php echo e($contact->email); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">Defaults to the contact email. You can change it before sending.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                <p class="font-semibold uppercase tracking-wide text-gray-500">What the client can do</p>
                <p class="mt-2">Complete personal details, address, citizenship, KYC details, and onboarding information from the secure CIF page.</p>
            </div>
        </div>
        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-send-cif-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="h-9 min-w-[140px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Send CIF Link</button>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'sendSpecimenModal','width' => 'sm:max-w-[560px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'sendSpecimenModal','width' => 'sm:max-w-[560px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Send Specimen Form</h2>
                <p class="mt-1 text-sm text-gray-500">Email a secure specimen signature form link so the client can complete the signature card remotely.</p>
            </div>
            <button type="button" data-close-send-specimen-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('contacts.specimen.send', $contact->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                The secure link opens a client specimen signature form. Submitted details populate the saved specimen signature record for this contact.
            </div>
            <div>
                <label for="specimen_recipient_email" class="mb-1 block text-sm font-medium text-gray-700">Recipient Email</label>
                <input id="specimen_recipient_email" name="recipient_email" type="email" value="<?php echo e($contact->email); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">Defaults to the contact email. You can change it before sending.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                <p class="font-semibold uppercase tracking-wide text-gray-500">What the client can do</p>
                <p class="mt-2">Complete the specimen signature card details, client references, signatory names, and related authentication fields.</p>
            </div>
        </div>
        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-send-specimen-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="h-9 min-w-[160px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Send Specimen Link</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const contactIntakeModal = document.getElementById('contactIntakeModal');
        const contactIntakeForm = document.getElementById('contactIntakeForm');
        const contactIntakeEditBtn = document.getElementById('contactIntakeEditBtn');
        const contactIntakeSaveBtn = document.getElementById('contactIntakeSaveBtn');
        const contactIntakeCancelBtn = document.getElementById('contactIntakeCancelBtn');
        const contactIntakeCloseBtn = document.getElementById('cancelContactIntakeModal');
        const contactIntakeOpenBtn = document.getElementById('openContactIntakeModal');
        const contactIntakeCloseButtons = document.querySelectorAll('[data-close-contact-intake-modal]');
        const contactIntakeFields = contactIntakeForm
            ? Array.from(contactIntakeForm.querySelectorAll('input, select, textarea')).filter((field) => !['hidden', 'submit', 'button'].includes(field.type))
            : [];

        const syncContactIntakeConditionalFields = () => {
            document.getElementById('intakeServiceInquiryOtherWrap')?.classList.toggle('hidden', !contactIntakeForm?.querySelector('input[name="service_inquiry_types[]"][value="Other"]')?.checked);
            document.getElementById('intakeRecommendationOtherWrap')?.classList.toggle('hidden', !contactIntakeForm?.querySelector('input[name="recommendation_options[]"][value="Others"]')?.checked);
            document.getElementById('intakeLeadSourceOtherWrap')?.classList.toggle('hidden', !contactIntakeForm?.querySelector('input[name="lead_source_channels[]"][value="Other"]')?.checked);
            document.getElementById('intakeOrganizationTypeOtherWrap')?.classList.toggle('hidden', contactIntakeForm?.querySelector('input[name="organization_type"]:checked')?.value !== 'Others');
            document.getElementById('intakeForeignBusinessNatureWrap')?.classList.toggle('hidden', contactIntakeForm?.querySelector('input[name="ownership_flag"]:checked')?.value !== 'Foreign-Owned Business');
        };

        const setContactIntakeEditMode = (isEditMode) => {
            contactIntakeFields.forEach((field) => {
                if (!field.readOnly) {
                    field.disabled = !isEditMode;
                }
            });

            contactIntakeEditBtn?.classList.toggle('hidden', isEditMode);
            contactIntakeSaveBtn?.classList.toggle('hidden', !isEditMode);
            contactIntakeCancelBtn?.classList.toggle('hidden', !isEditMode);
            contactIntakeCloseBtn?.classList.toggle('hidden', isEditMode);
        };

        const resetContactIntakeForm = () => {
            if (!contactIntakeForm) {
                return;
            }

            contactIntakeForm.reset();
            syncContactIntakeConditionalFields();
            setContactIntakeEditMode(false);
        };

        if (contactIntakeForm) {
            setContactIntakeEditMode(false);
            syncContactIntakeConditionalFields();
            contactIntakeForm.querySelectorAll('input, select, textarea').forEach((field) => {
                field.addEventListener('change', syncContactIntakeConditionalFields);
            });
        }

        contactIntakeOpenBtn?.addEventListener('click', () => {
            resetContactIntakeForm();
            window.jkncSlideOver.open(contactIntakeModal);
        });

        contactIntakeCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                resetContactIntakeForm();
                window.jkncSlideOver.close(contactIntakeModal);
            });
        });

        contactIntakeEditBtn?.addEventListener('click', () => setContactIntakeEditMode(true));
        contactIntakeCancelBtn?.addEventListener('click', () => resetContactIntakeForm());

        <?php if(old('_from_contact_intake_edit')): ?>
            setContactIntakeEditMode(true);
            syncContactIntakeConditionalFields();
            window.jkncSlideOver.open(contactIntakeModal);
        <?php endif; ?>

        const bindSlideOver = (modalId, openSelector, closeSelector) => {
            const modal = document.getElementById(modalId);
            const openButtons = document.querySelectorAll(openSelector);
            const closeButtons = document.querySelectorAll(closeSelector);
            const openModal = () => window.jkncSlideOver.open(modal);
            const closeModal = () => window.jkncSlideOver.close(modal);

            openButtons.forEach((button) => button.addEventListener('click', openModal));
            closeButtons.forEach((button) => button.addEventListener('click', closeModal));

            modal?.addEventListener('click', function (event) {
                if (event.target === modal || event.target.hasAttribute('data-drawer-overlay')) {
                    closeModal();
                }
            });
        };

        bindSlideOver('sendCifModal', '[data-open-send-cif-modal]', '[data-close-send-cif-modal]');
        bindSlideOver('sendSpecimenModal', '[data-open-send-specimen-modal]', '[data-close-send-specimen-modal]');

        const cifPreviewButton = document.querySelector('[data-cif-pdf-preview]');
        const cifDownloadButton = document.querySelector('[data-cif-pdf-download]');

        const openCifPrintFrame = (url) => {
            if (!url) {
                return;
            }

            const frame = document.createElement('iframe');
            frame.style.position = 'fixed';
            frame.style.width = '0';
            frame.style.height = '0';
            frame.style.border = '0';
            frame.style.opacity = '0';
            frame.setAttribute('aria-hidden', 'true');
            frame.src = url;
            document.body.appendChild(frame);

            const cleanup = () => {
                window.setTimeout(() => frame.remove(), 1500);
            };

            frame.addEventListener('load', cleanup, { once: true });
        };

        cifPreviewButton?.addEventListener('click', () => {
            window.open(cifPreviewButton.dataset.previewUrl, '_blank');
        });

        cifDownloadButton?.addEventListener('click', () => {
            openCifPrintFrame(cifDownloadButton.dataset.downloadUrl);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/contacts/show.blade.php ENDPATH**/ ?>