<?php $__env->startSection('content'); ?>
<?php
    $formatCurrency = static fn ($amount): string => 'P'.number_format((float) $amount, 2);
    $stageBadgeClasses = [
        'Inquiry' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'Qualification' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'Consultation' => 'bg-cyan-100 text-cyan-700 border border-cyan-200',
        'Proposal' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Negotiation' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'Payment' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'Activation' => 'bg-violet-100 text-violet-700 border border-violet-200',
        'Closed Lost' => 'bg-red-100 text-red-700 border border-red-200',
    ];
    $dealStatusClasses = [
        'Pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Approved' => 'bg-green-100 text-green-700 border border-green-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];
    $initials = strtoupper(substr((string) ($deal['contact_name'] ?? 'C'), 0, 1).substr(strrchr(' '.($deal['contact_name'] ?? 'C'), ' '), 1, 1));
    $progressCurrentStage = data_get($detail, 'progress.current_stage', []);
    $progressCurrentStagePosition = (int) (data_get($progressCurrentStage, 'position') ?? data_get($progressCurrentStage, 'order', 0));
    $stageBadgeClassesJson = $stageBadgeClasses;
    $currentStageNameJson = $deal['stage'] ?? data_get($progressCurrentStage, 'name');
    $currentStagePositionJson = $progressCurrentStagePosition;
    $stageDataJson = collect($stages ?? [])->values()->all();
    $currentStageIdJson = $deal['stage_id'] ?? null;
?>

<div class="bg-[#f7f6f2] p-6">
    <div class="mx-auto max-w-[1500px] space-y-4">
        <?php if(session('success')): ?>
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
            <a href="<?php echo e(route('deals.index')); ?>" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Deals</a>
            <span class="mx-1">/</span>
            <span class="font-medium text-gray-900"><?php echo e($deal['deal_code'] ?? 'DEAL'); ?></span>
        </div>

        <div class="flex flex-wrap items-start justify-between gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-semibold text-gray-900"><?php echo e($deal['deal_code'] ?? 'DEAL'); ?></h1>
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($dealStatusClasses[$detail['deal_status'] ?? 'Pending'] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>"><?php echo e($detail['deal_status'] ?? 'Pending'); ?></span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span id="dealStageBadge" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($stageBadgeClasses[$deal['stage']] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>"><?php echo e($deal['stage']); ?></span>
                    <span class="text-lg font-semibold text-gray-900"><?php echo e($formatCurrency($deal['amount'] ?? 0)); ?></span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button id="openCreateDealModalBtn" type="button" class="h-9 rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="far fa-pen-to-square mr-1"></i>Edit Deal
                </button>
                <button id="openStageUpdateModalBtn" type="button" class="h-9 rounded-lg bg-blue-700 px-3 text-sm font-medium text-white hover:bg-blue-800">
                    <i class="fas fa-arrow-up-right-dots mr-1"></i>Update Stage
                </button>
                <?php if(($deal['stage'] ?? '') === 'Proposal'): ?>
                    <a href="<?php echo e(route('deals.proposal.show', $deal['id'])); ?>" class="flex h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-sm font-medium text-amber-700 hover:bg-amber-100">
                        <i class="fas fa-file-signature mr-1"></i>Create Proposal
                    </a>
                <?php endif; ?>
                <?php if(data_get($detail, 'project.id')): ?>
                    <?php
                        $linkedEngagementType = strtolower(trim((string) data_get($detail, 'service.engagement_type', '')));
                        $linkedRoute = str_contains($linkedEngagementType, 'regular') ? 'regular.show' : 'project.show';
                        $linkedLabel = str_contains($linkedEngagementType, 'regular') ? 'Open Regular' : 'Open Project';
                    ?>
                    <a href="<?php echo e(route($linkedRoute, data_get($detail, 'project.id'))); ?>" class="flex h-9 items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                        <i class="fas fa-diagram-project mr-1"></i><?php echo e($linkedLabel); ?>

                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('deals.download-pdf', ['id' => $deal['id'], 'autoprint' => 1])); ?>" target="_blank" class="flex h-9 items-center rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-pdf mr-1"></i>Download PDF
                </a>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.85fr_0.85fr]">
            <div class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Information</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Deal Code</p><p class="font-medium text-gray-800"><?php echo e($deal['deal_code'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Company Name</p><p class="font-medium text-gray-800"><?php echo e($detail['related_company'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Contact Person Name</p><p class="font-medium text-gray-800"><?php echo e($detail['contact_person_name'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Contact Person Position</p><p class="font-medium text-gray-800"><?php echo e($detail['contact_person_position'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Email Address</p><p class="font-medium text-gray-800"><?php echo e($detail['email_address'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Contact Number</p><p class="font-medium text-gray-800"><?php echo e($detail['contact_number'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Client Type</p><p class="font-medium text-gray-800"><?php echo e($detail['client_type'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Industry</p><p class="font-medium text-gray-800"><?php echo e($detail['industry'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Qualification Result</p><p class="font-medium text-gray-800"><?php echo e($detail['qualification_result'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Qualification Notes</p><p class="font-medium text-gray-800"><?php echo e($detail['qualification_notes'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Deal Stage</p><p id="dealStageText" class="font-medium text-gray-800"><?php echo e($detail['deal_stage'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Expected Close Date</p><p class="font-medium text-gray-800"><?php echo e($detail['expected_close_date'] ?? '-'); ?></p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Service and Engagement Details</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Service Type</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'service.service_type', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Product Type</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'service.product_type', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Engagement Type</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'service.engagement_type', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Deal Code</p><p class="font-medium text-gray-800"><?php echo e($deal['deal_code'] ?? '-'); ?></p></div>
                        <div><p class="text-xs text-gray-500">Engagement Duration</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'service.engagement_duration', '-')); ?></p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Financial Details</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Deal Value</p><p class="font-medium text-gray-800"><?php echo e($formatCurrency(data_get($detail, 'financial.deal_value', 0))); ?></p></div>
                        <div><p class="text-xs text-gray-500">Pricing Model</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'financial.pricing_model', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Payment Terms</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'financial.payment_terms', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Commission Applicable</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'financial.commission_applicable', '-')); ?></p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Referral and Lead Source</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Lead Source</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'referral.lead_source', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Referred By</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'referral.referred_by', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Referral Type</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'referral.referral_type', '-')); ?></p></div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Ownership and Team Assignment</h2>
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs text-gray-500">Lead Consultant</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'ownership.lead_consultant', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Lead Associate</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'ownership.lead_associate', '-')); ?></p></div>
                        <div><p class="text-xs text-gray-500">Handling Team</p><p class="font-medium text-gray-800"><?php echo e(data_get($detail, 'ownership.handling_team', '-')); ?></p></div>
                        <div>
                            <p class="text-xs text-gray-500">Assigned Team Members</p>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                <?php $__empty_1 = true; $__currentLoopData = (array) data_get($detail, 'ownership.assigned_members', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="rounded-full border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs text-gray-700"><?php echo e($member); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-sm text-gray-500">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Deal Form Preview</h2>
                    <?php echo $__env->make('deals.partials.deal-form-document', ['dealFormData' => $dealFormData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Deal Stage Progress</h2>
                    <div id="dealStageProgress" class="flex flex-wrap items-start justify-between gap-y-4 overflow-x-auto pb-1">
                        <?php $__currentLoopData = ($stages ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $stagePosition = (int) (data_get($stage, 'position') ?? data_get($stage, 'order', 0));
                                $stageId = data_get($stage, 'id');
                                $dealStageId = data_get($deal, 'stage_id');
                                $isCompleted = (
                                    filled($stageId)
                                    && filled($dealStageId)
                                    && $stagePosition <= $progressCurrentStagePosition
                                ) || (
                                    blank($dealStageId)
                                    && $stagePosition <= $progressCurrentStagePosition
                                );
                                $circleClass = $isCompleted
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-gray-100 text-gray-300 border-gray-200';
                                $lineClass = $stagePosition < $progressCurrentStagePosition
                                    ? 'bg-blue-600'
                                    : 'bg-gray-200';
                            ?>
                            <div class="flex min-w-[88px] flex-1 items-start" data-stage-progress-item data-stage-id="<?php echo e(data_get($stage, 'id')); ?>" data-stage-position="<?php echo e($stagePosition); ?>">
                                <div class="flex w-full flex-col items-center text-center">
                                    <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full border text-xs <?php echo e($circleClass); ?>">
                                        <?php if($isCompleted): ?>
                                            <i class="fas fa-check text-sm"></i>
                                        <?php endif; ?>
                                    </span>
                                    <p class="mt-2 text-xs text-gray-600"><?php echo e(data_get($stage, 'name')); ?></p>
                                </div>
                                <?php if(! $loop->last): ?>
                                    <span class="mt-4 hidden h-0.5 flex-1 rounded-full lg:block <?php echo e($lineClass); ?>"></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100">
                        <div class="flex flex-wrap gap-1 p-2">
                            <?php $__currentLoopData = ['timeline' => 'Timeline', 'notes' => 'Notes', 'activities' => 'Activities', 'emails' => 'Emails', 'stage-history' => 'Stage History', 'files' => 'Files', 'products' => 'Products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" data-tab-button="<?php echo e($tabKey); ?>" class="deal-tab-btn rounded-lg px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 <?php echo e($tabKey === 'timeline' ? 'bg-blue-50 text-blue-700' : ''); ?>"><?php echo e($label); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="p-4 text-sm">
                        <div data-tab-panel="timeline" class="deal-tab-panel space-y-3">
                            <?php $__currentLoopData = (array) data_get($detail, 'timeline', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-start gap-3 rounded-lg border border-gray-100 p-3">
                                    <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                        <i class="fas <?php echo e($entry['icon'] ?? 'fa-clock'); ?> text-xs"></i>
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo e($entry['title'] ?? '-'); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($entry['timestamp'] ?? '-'); ?> - <?php echo e($entry['user'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div data-tab-panel="notes" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No notes added yet</div>
                        <div data-tab-panel="activities" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No activities added yet</div>
                        <div data-tab-panel="emails" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No emails found for this deal</div>

                        <div data-tab-panel="stage-history" class="deal-tab-panel hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Stage</th>
                                            <th class="px-3 py-2 text-left">Amount</th>
                                            <th class="px-3 py-2 text-left">Stage Duration</th>
                                            <th class="px-3 py-2 text-left">Modified By</th>
                                            <th class="px-3 py-2 text-left">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php $__currentLoopData = (array) data_get($detail, 'stage_history', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="px-3 py-2 text-gray-800"><?php echo e($history['stage'] ?? '-'); ?></td>
                                                <td class="px-3 py-2 font-medium text-blue-700"><?php echo e($formatCurrency($history['amount'] ?? 0)); ?></td>
                                                <td class="px-3 py-2 text-gray-700"><?php echo e($history['duration'] ?? '-'); ?></td>
                                                <td class="px-3 py-2 text-gray-700"><?php echo e($history['modified_by'] ?? '-'); ?></td>
                                                <td class="px-3 py-2 text-gray-700"><?php echo e($history['date'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div data-tab-panel="files" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No files uploaded yet.</div>
                        <div data-tab-panel="products" class="deal-tab-panel hidden py-6 text-center text-sm text-gray-500">No associated products yet.</div>
                    </div>
                </article>
            </div>

            <aside class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-3 text-base font-semibold text-gray-900">Related Contact</h3>
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700"><?php echo e($initials); ?></span>
                        <div>
                            <p class="font-medium text-gray-900"><?php echo e($deal['contact_name'] ?? '-'); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($deal['company_name'] ?? '-'); ?></p>
                            <p class="mt-2 text-xs text-gray-600"><i class="far fa-envelope mr-1"></i><?php echo e($detail['email_address'] ?? '-'); ?></p>
                            <p class="mt-1 text-xs text-gray-600"><i class="fas fa-phone mr-1"></i><?php echo e($detail['contact_number'] ?? '-'); ?></p>
                        </div>
                    </div>
                    <?php if(($hasSavedProposal ?? false) === true): ?>
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <a href="<?php echo e(route('deals.proposal.preview-page', $deal['id'])); ?>" class="block w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-center text-sm font-medium text-blue-700 hover:bg-blue-100">
                                <i class="fas fa-eye mr-1"></i><?php echo e($deal['deal_code'] ?? 'Proposal Preview'); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-2 text-base font-semibold text-gray-900">Tags</h3>
                    <button type="button" class="text-sm font-medium text-blue-600 hover:text-blue-700"><i class="fas fa-plus mr-1"></i>Add Tag</button>
                </article>


            </aside>
        </div>
    </div>
</div>

<div id="dealStageModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
    <button id="dealStageModalOverlay" type="button" class="absolute inset-0 bg-slate-900/35"></button>
    <div class="absolute left-1/2 top-1/2 w-full max-w-sm -translate-x-1/2 -translate-y-1/2 rounded-xl border border-gray-200 bg-white p-4 shadow-xl">
        <h3 class="text-base font-semibold text-gray-900">Update Deal Stage</h3>
        <p class="mt-1 text-xs text-gray-500">Select the next stage for this deal.</p>
        <form id="dealStageForm" method="POST" action="<?php echo e(route('deals.stage.update', $deal['id'])); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <div class="mt-4">
                <label for="dealStageSelect" class="mb-1 block text-sm font-medium text-gray-700">Select Stage</label>
                <select id="dealStageSelect" name="stage_id" required data-current-stage-name="<?php echo e($deal['stage'] ?? data_get($progressCurrentStage, 'name', '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <?php $__currentLoopData = ($stages ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $optionStageId = data_get($stage, 'id');
                            $optionStageName = (string) data_get($stage, 'name', '');
                            $optionValue = filled($optionStageId) ? (string) $optionStageId : $optionStageName;
                            $selectedById = filled($deal['stage_id'] ?? null) && filled($optionStageId) && (int) $optionStageId === (int) ($deal['stage_id'] ?? null);
                            $selectedByName = blank($deal['stage_id'] ?? null) && $optionStageName !== '' && $optionStageName === (string) ($deal['stage'] ?? data_get($progressCurrentStage, 'name', ''));
                            $isSelected = $selectedById || $selectedByName;
                        ?>
                        <option value="<?php echo e($optionValue); ?>" data-stage-id="<?php echo e($optionStageId); ?>" data-stage-name="<?php echo e($optionStageName); ?>" <?php if($isSelected): echo 'selected'; endif; ?>><?php echo e($optionStageName); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mt-4 flex items-center justify-end gap-2">
                <button id="dealStageModalCancel" type="button" class="h-9 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button id="dealStageModalSave" type="submit" class="h-9 rounded-lg bg-blue-600 px-3 text-sm font-medium text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="dealStageToast" class="pointer-events-none fixed right-6 top-6 z-[80] <?php echo e(session('stage_success') ? '' : 'hidden'); ?> rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-lg">
    <?php echo e(session('stage_success', 'Stage updated successfully.')); ?>

</div>

<?php echo $__env->make('deals.partials.create-deal-modal', [
    'stageOptions' => $stageOptions,
    'companyOptions' => $companyOptions,
    'contactOptions' => $contactOptions,
    'contactRecords' => $contactRecords,
    'productOptionsByServiceArea' => $productOptionsByServiceArea ?? [],
    'ownerLabel' => $ownerLabel,
    'owners' => $owners,
    'defaultOwnerId' => $defaultOwnerId,
    'dealDraft' => $dealFormData,
    'openDealModal' => $openDealModal ?? false,
    'formAction' => route('deals.update', $deal['id']),
    'formMethod' => 'PUT',
    'panelTitle' => 'Edit Deal',
    'panelSubtitle' => 'Update the selected deal details.',
    'submitLabel' => 'Update Deal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div
    id="dealShowScriptData"
    data-current-stage-name="<?php echo e(e((string) ($currentStageNameJson ?? ''))); ?>"
    data-current-stage-position="<?php echo e((int) ($currentStagePositionJson ?? 0)); ?>"
    data-current-stage-id="<?php echo e(e((string) ($currentStageIdJson ?? ''))); ?>"
></div>
<script id="dealShowStageBadgeClasses" type="application/json"><?php echo json_encode($stageBadgeClassesJson, 15, 512) ?></script>
<script id="dealShowStageData" type="application/json"><?php echo json_encode($stageDataJson, 15, 512) ?></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dealShowScriptData = document.getElementById('dealShowScriptData');
    const stageBadgeClassesNode = document.getElementById('dealShowStageBadgeClasses');
    const stageDataNode = document.getElementById('dealShowStageData');
    const stagePrimary = document.getElementById('openStageUpdateModalBtn');
    const stageModal = document.getElementById('dealStageModal');
    const stageModalOverlay = document.getElementById('dealStageModalOverlay');
    const stageModalCancel = document.getElementById('dealStageModalCancel');
    const stageSelect = document.getElementById('dealStageSelect');
    const stageToast = document.getElementById('dealStageToast');
    const stageBadge = document.getElementById('dealStageBadge');
    const stageText = document.getElementById('dealStageText');
    const stageProgress = document.getElementById('dealStageProgress');
    const stageBadgeClasses = stageBadgeClassesNode ? JSON.parse(stageBadgeClassesNode.textContent || '{}') : {};
    const currentStageName = dealShowScriptData?.dataset.currentStageName || '';
    const currentStagePosition = Number(dealShowScriptData?.dataset.currentStagePosition || 0);
    let stageData = stageDataNode ? JSON.parse(stageDataNode.textContent || '[]') : [];
    let currentStageId = dealShowScriptData?.dataset.currentStageId || null;
    let toastTimer = null;

    const buttons = Array.from(document.querySelectorAll('[data-tab-button]'));
    const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));
    const activate = (tabKey) => {
        buttons.forEach((button) => {
            const isActive = button.dataset.tabButton === tabKey;
            button.classList.toggle('bg-blue-50', isActive);
            button.classList.toggle('text-blue-700', isActive);
            button.classList.toggle('text-gray-600', !isActive);
        });
        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.tabPanel !== tabKey);
        });
    };
    buttons.forEach((button) => button.addEventListener('click', () => activate(button.dataset.tabButton)));

    const getStagePosition = (stage) => Number(stage?.position ?? stage?.order ?? 0);

    const isCurrentStage = (stage, selectedId) => {
        const stageId = stage?.id;
        if (stageId !== null && stageId !== undefined && selectedId !== null && selectedId !== undefined && selectedId !== '') {
            return Number(stageId) === Number(selectedId);
        }
        return getStagePosition(stage) === Number(currentStagePosition || 0) && String(stage?.name || '') === String(currentStageName || '');
    };

    const getActiveStage = (selectedId) => {
        return stageData.find((item) => isCurrentStage(item, selectedId))
            || stageData.find((item) => String(item?.name || '') === String(currentStageName || ''));
    };

    const isCompletedStage = (stage, selectedId) => {
        const activePosition = getStagePosition(getActiveStage(selectedId));

        return activePosition > 0 && getStagePosition(stage) <= activePosition;
    };

    const openStageModal = () => {
        const stageNameFromSelect = stageSelect?.dataset.currentStageName || '';
        const currentStage = stageData.find((stage) => isCurrentStage(stage, currentStageId))
            || stageData.find((stage) => String(stage.name) === stageNameFromSelect);
        if (stageSelect) {
            if (currentStage) {
                stageSelect.value = String(currentStage.id);
                if (stageSelect.value !== String(currentStage.id)) {
                    const matchedOption = Array.from(stageSelect.options).find((option) => option.text.trim() === currentStage.name);
                    if (matchedOption) {
                        stageSelect.value = matchedOption.value;
                    }
                }
            } else if (stageData.length > 0) {
                stageSelect.value = String(stageData[0].id);
            }
        }
        stageModal?.classList.remove('hidden');
        stageModal?.setAttribute('aria-hidden', 'false');
    };

    const closeStageModal = () => {
        stageModal?.classList.add('hidden');
        stageModal?.setAttribute('aria-hidden', 'true');
    };

    const showStageToast = (message = 'Stage updated successfully.') => {
        if (!stageToast) {
            return;
        }
        stageToast.textContent = message;
        stageToast.classList.remove('hidden');
        if (toastTimer) {
            window.clearTimeout(toastTimer);
        }
        toastTimer = window.setTimeout(() => {
            stageToast.classList.add('hidden');
        }, 2400);
    };

    const renderStageProgress = (stages, currentStageId) => {
        if (!stageProgress) {
            return;
        }

        const activePosition = getStagePosition(getActiveStage(currentStageId));

        stageProgress.innerHTML = stages.map((stage, index) => {
            const isCompleted = isCompletedStage(stage, currentStageId);
            const circleClass = isCompleted
                ? 'bg-blue-600 text-white border-blue-600'
                : 'bg-gray-100 text-gray-300 border-gray-200';
            const lineClass = getStagePosition(stage) < activePosition ? 'bg-blue-600' : 'bg-gray-200';

            return `
                <div class="flex min-w-[88px] flex-1 items-start" data-stage-progress-item data-stage-id="${stage.id}" data-stage-position="${getStagePosition(stage)}">
                    <div class="flex w-full flex-col items-center text-center">
                        <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full border text-xs ${circleClass}">
                            ${isCompleted ? '<i class="fas fa-check text-sm"></i>' : ''}
                        </span>
                        <p class="mt-2 text-xs text-gray-600">${stage.name}</p>
                    </div>
                    ${index < stages.length - 1 ? `<span class="mt-4 hidden h-0.5 flex-1 rounded-full lg:block ${lineClass}"></span>` : ''}
                </div>
            `;
        }).join('');
    };

    const applyStageDisplay = (stage) => {
        if (!stage) {
            return;
        }

        if (stageBadge) {
            stageBadge.className = `inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${stageBadgeClasses[stage.name] || 'bg-gray-100 text-gray-700 border border-gray-200'}`;
            stageBadge.textContent = stage.name;
        }

        if (stageText) {
            stageText.textContent = stage.name;
        }

        renderStageProgress(stageData, stage.id);
    };

    stagePrimary?.addEventListener('click', openStageModal);
    stageModalOverlay?.addEventListener('click', closeStageModal);
    stageModalCancel?.addEventListener('click', closeStageModal);
    if (!stageToast?.classList.contains('hidden')) {
        showStageToast(stageToast.textContent.trim() || 'Stage updated successfully.');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/deals/show.blade.php ENDPATH**/ ?>