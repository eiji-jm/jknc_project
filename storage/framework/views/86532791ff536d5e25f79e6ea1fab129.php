<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900">DEALS</h2>
                            <p class="mt-1 text-sm text-gray-500">Manage and track deals for this company.</p>
                        </div>

                        <button type="button" id="openDealModalCreate" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2">
                            <span class="text-base leading-none">+</span>
                            <span>Add Deal</span>
                        </button>
                    </div>

                    <?php if(session('deals_success')): ?>
                        <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            <?php echo e(session('deals_success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-4">
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Deals</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['total']); ?></p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Open Deals</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['open']); ?></p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Won Deals</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['won']); ?></p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pipeline Value</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">P<?php echo e(number_format((float) $summary['pipeline_value'], 2)); ?></p>
                        </div>
                    </div>

                    <form method="GET" action="<?php echo e(route('company.deals', $company->id)); ?>" class="mt-4 grid grid-cols-1 gap-2 lg:grid-cols-12">
                        <div class="relative lg:col-span-7">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input
                                type="text"
                                name="search"
                                value="<?php echo e($search); ?>"
                                placeholder="Search deal name, owner, source, or stage..."
                                class="w-full h-10 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            >
                        </div>

                        <div class="lg:col-span-3">
                            <select name="stage" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="all" <?php if($stage === 'all'): echo 'selected'; endif; ?>>All Deals</option>
                                <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealStage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dealStage); ?>" <?php if($stage === $dealStage): echo 'selected'; endif; ?>><?php echo e($dealStage); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="lg:col-span-2 flex items-center gap-2">
                            <button class="h-10 flex-1 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Apply</button>
                            <?php if($search !== '' || $stage !== 'all'): ?>
                                <a href="<?php echo e(route('company.deals', $company->id)); ?>" class="h-10 flex-1 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                                    Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Deal Name</th>
                                        <th class="px-4 py-3 text-left font-medium">Stage</th>
                                        <th class="px-4 py-3 text-left font-medium">Amount</th>
                                        <th class="px-4 py-3 text-left font-medium">Expected Close Date</th>
                                        <th class="px-4 py-3 text-left font-medium">Owner</th>
                                        <th class="px-4 py-3 text-left font-medium">Last Updated</th>
                                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-800"><?php echo e($deal['name']); ?></div>
                                                <div class="mt-1 text-xs text-gray-500"><?php echo e($deal['priority'] ?: 'Normal'); ?> priority<?php echo e($deal['deal_source'] ? ' / ' . $deal['deal_source'] : ''); ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?php ($stageClasses = match($deal['stage']) {
                                                    'Qualification' => 'border-blue-200 bg-blue-50 text-blue-700',
                                                    'Consultation' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                    'Proposal' => 'border-violet-200 bg-violet-50 text-violet-700',
                                                    'Negotiation' => 'border-amber-200 bg-amber-50 text-amber-700',
                                                    'Won' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                    'Lost' => 'border-red-200 bg-red-50 text-red-700',
                                                    default => 'border-gray-200 bg-gray-100 text-gray-600',
                                                }); ?>
                                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium <?php echo e($stageClasses); ?>"><?php echo e($deal['stage']); ?></span>
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-900">P<?php echo e(number_format((float) $deal['amount'], 2)); ?></td>
                                            <td class="px-4 py-3"><?php echo e($deal['expected_close_date'] ? \Illuminate\Support\Carbon::parse($deal['expected_close_date'])->format('M d, Y') : '-'); ?></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="h-7 w-7 rounded-full bg-gray-100 border border-gray-200 text-[11px] font-semibold text-gray-600 inline-flex items-center justify-center"><?php echo e($deal['owner_initials']); ?></span>
                                                    <span><?php echo e($deal['owner']); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3"><?php echo e($deal['updated_at']); ?></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="<?php echo e(route('company.deals.show', [$company->id, $deal['id']])); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                        View
                                                    </a>
                                                    <button type="button" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50" data-deal-edit='<?php echo json_encode($deal, 15, 512) ?>'>
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="<?php echo e(route('company.deals.destroy', [$company->id, $deal['id']])); ?>" onsubmit="return confirm('Delete this deal?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="inline-flex h-8 items-center rounded-full border border-red-200 px-3 text-xs font-medium text-red-600 hover:bg-red-50">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-12">
                                                <div class="flex flex-col items-center justify-center text-center">
                                                    <div class="h-12 w-12 rounded-full bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                                                        <i class="fas fa-handshake"></i>
                                                    </div>
                                                    <h3 class="mt-4 text-base font-semibold text-gray-900">No deals found for this company yet.</h3>
                                                    <p class="mt-1 max-w-md text-sm text-gray-500">Create the first deal for <?php echo e($company->company_name); ?> and keep all opportunity updates in one place.</p>
                                                    <button type="button" id="openFirstDealModal" class="mt-4 h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2">
                                                        <span class="text-base leading-none">+</span>
                                                        <span>Add Deal</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-4 py-3 flex flex-wrap items-center justify-end gap-3 text-sm text-gray-500">
                    <span><?php echo e($deals->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('deal', $deals->count())); ?></span>
                </div>
            </div>
        </section>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'dealModal','width' => 'sm:max-w-[640px] lg:max-w-[760px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'dealModal','width' => 'sm:max-w-[640px] lg:max-w-[760px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="dealModalTitle" class="text-lg font-semibold text-gray-900">Add Deal</h2>
                <p class="mt-1 text-sm text-gray-500">This deal will be linked automatically to <?php echo e($company->company_name); ?>.</p>
            </div>
            <button type="button" data-close-deal-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form id="dealForm" method="POST" action="<?php echo e(route('company.deals.store', $company->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="dealFormMethod" name="_method" value="POST">

        <div class="flex-1 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="linked_deal_id" class="mb-1 block text-sm font-medium text-gray-700">Link Existing Deal</label>
                        <select id="linked_deal_id" name="linked_deal_id" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            <option value="">Create a new deal manually</option>
                            <?php $__currentLoopData = $availableDeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $availableDeal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                    value="<?php echo e($availableDeal['id']); ?>"
                                    data-deal='<?php echo json_encode($availableDeal, 15, 512) ?>'
                                    <?php if((string) old('linked_deal_id') === (string) $availableDeal['id']): echo 'selected'; endif; ?>
                                >
                                    <?php echo e($availableDeal['name']); ?><?php echo e($availableDeal['company_name'] ? ' - ' . $availableDeal['company_name'] : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select from the main Deals list to auto-fill and link that deal to this company.</p>
                        <?php $__errorArgs = ['linked_deal_id'];
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

                    <div class="md:col-span-2">
                        <label for="deal_name" class="mb-1 block text-sm font-medium text-gray-700">Deal Name <span class="text-red-500">*</span></label>
                        <input id="deal_name" name="name" type="text" value="<?php echo e(old('name')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        <?php $__errorArgs = ['name'];
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

                    <div>
                        <label for="deal_stage" class="mb-1 block text-sm font-medium text-gray-700">Stage</label>
                        <select id="deal_stage" name="stage" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealStage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dealStage); ?>" <?php if(old('stage', 'Qualification') === $dealStage): echo 'selected'; endif; ?>><?php echo e($dealStage); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['stage'];
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

                    <div>
                        <label for="deal_amount" class="mb-1 block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
                        <input id="deal_amount" name="amount" type="number" step="0.01" min="0" value="<?php echo e(old('amount')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        <?php $__errorArgs = ['amount'];
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

                    <div>
                        <label for="deal_expected_close_date" class="mb-1 block text-sm font-medium text-gray-700">Expected Close Date</label>
                        <input id="deal_expected_close_date" name="expected_close_date" type="date" value="<?php echo e(old('expected_close_date')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['expected_close_date'];
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

                    <div>
                        <label for="deal_owner" class="mb-1 block text-sm font-medium text-gray-700">Owner <span class="text-red-500">*</span></label>
                        <input id="deal_owner" name="owner" type="text" value="<?php echo e(old('owner', $company->owner_name ?? '')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        <?php $__errorArgs = ['owner'];
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

                    <div>
                        <label for="deal_source" class="mb-1 block text-sm font-medium text-gray-700">Deal Source</label>
                        <input id="deal_source" name="deal_source" type="text" value="<?php echo e(old('deal_source')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['deal_source'];
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

                    <div>
                        <label for="deal_priority" class="mb-1 block text-sm font-medium text-gray-700">Priority</label>
                        <select id="deal_priority" name="priority" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            <?php $__currentLoopData = ['Low', 'Normal', 'High', 'Critical']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($priority); ?>" <?php if(old('priority', 'Normal') === $priority): echo 'selected'; endif; ?>><?php echo e($priority); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['priority'];
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

                    <div class="md:col-span-2">
                        <label for="deal_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes / Description</label>
                        <textarea id="deal_notes" name="notes" rows="4" class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
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
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-deal-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="dealFormSubmit" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Save
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dealModal = document.getElementById('dealModal');
        const openButtons = [document.getElementById('openDealModalCreate'), document.getElementById('openFirstDealModal')].filter(Boolean);
        const closeButtons = document.querySelectorAll('[data-close-deal-modal]');
        const dealEditButtons = document.querySelectorAll('[data-deal-edit]');
        const dealForm = document.getElementById('dealForm');
        const dealFormMethod = document.getElementById('dealFormMethod');
        const dealModalTitle = document.getElementById('dealModalTitle');
        const dealFormSubmit = document.getElementById('dealFormSubmit');
        const linkedDealSelect = document.getElementById('linked_deal_id');
        const updateUrlTemplate = <?php echo json_encode(route('company.deals.update', [$company->id, '__DEAL__'])) ?>;

        const openModal = () => window.jkncSlideOver.open(dealModal);
        const closeModal = () => window.jkncSlideOver.close(dealModal);

        const resetForm = () => {
            dealForm.reset();
            dealForm.action = <?php echo json_encode(route('company.deals.store', $company->id), 512) ?>;
            dealFormMethod.value = 'POST';
            dealModalTitle.textContent = 'Add Deal';
            dealFormSubmit.textContent = 'Save';
            if (linkedDealSelect) {
                linkedDealSelect.value = '';
            }
            document.getElementById('deal_stage').value = 'Qualification';
            document.getElementById('deal_priority').value = 'Normal';
            document.getElementById('deal_owner').value = <?php echo json_encode($company->owner_name ?? '', 15, 512) ?>;
        };

        const fillForm = (deal) => {
            document.getElementById('deal_name').value = deal.name ?? '';
            document.getElementById('deal_stage').value = deal.stage ?? 'Qualification';
            document.getElementById('deal_amount').value = deal.amount ?? '';
            document.getElementById('deal_expected_close_date').value = deal.expected_close_date ?? '';
            document.getElementById('deal_owner').value = deal.owner ?? '';
            document.getElementById('deal_source').value = deal.deal_source ?? '';
            document.getElementById('deal_priority').value = deal.priority ?? 'Normal';
            document.getElementById('deal_notes').value = deal.notes ?? '';
        };

        const applyLinkedDealSelection = () => {
            if (!linkedDealSelect) {
                return;
            }

            const selectedOption = linkedDealSelect.options[linkedDealSelect.selectedIndex];

            if (!selectedOption || !selectedOption.dataset.deal) {
                return;
            }

            try {
                const deal = JSON.parse(selectedOption.dataset.deal);
                fillForm(deal);
            } catch (error) {
                console.error('Unable to parse linked deal payload.', error);
            }
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', function () {
                resetForm();
                openModal();
            });
        });

        linkedDealSelect?.addEventListener('change', applyLinkedDealSelection);

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        dealEditButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const deal = JSON.parse(this.dataset.dealEdit);
                resetForm();
                dealForm.action = updateUrlTemplate.replace('__DEAL__', deal.id);
                dealFormMethod.value = 'PUT';
                dealModalTitle.textContent = 'Edit Deal';
                dealFormSubmit.textContent = 'Update';
                fillForm(deal);
                openModal();
            });
        });

        dealModal.addEventListener('click', function (event) {
            if (event.target === dealModal || event.target.hasAttribute('data-drawer-overlay')) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !dealModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        <?php if($errors->has('linked_deal_id') || $errors->has('name') || $errors->has('stage') || $errors->has('amount') || $errors->has('expected_close_date') || $errors->has('owner') || $errors->has('deal_source') || $errors->has('priority') || $errors->has('notes')): ?>
            resetForm();
            if (linkedDealSelect && <?php echo json_encode(old('linked_deal_id'), 15, 512) ?>) {
                linkedDealSelect.value = <?php echo json_encode(old('linked_deal_id'), 15, 512) ?>;
                applyLinkedDealSelection();
            }
            openModal();
        <?php endif; ?>
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/company/deals.blade.php ENDPATH**/ ?>