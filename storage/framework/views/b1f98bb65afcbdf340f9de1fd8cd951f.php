<?php $__env->startSection('content'); ?>
<?php
    $phaseBadgeClasses = [
        'RSAT' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
        'Planning' => 'bg-blue-50 text-blue-700 border border-blue-200',
        'For NTP Approval' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Execution' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Reporting' => 'bg-cyan-50 text-cyan-700 border border-cyan-200',
        'Delivery' => 'bg-violet-50 text-violet-700 border border-violet-200',
        'Completed' => 'bg-green-50 text-green-700 border border-green-200',
    ];
    $serviceAreaOptions = $serviceCatalog['serviceAreaOptions'] ?? [];
    $serviceGroups = $serviceCatalog['serviceGroups'] ?? [];
    $productOptionsByServiceArea = $productCatalog['productOptionsByServiceArea'] ?? [];
    $oldSourceMode = old('source_mode', 'manual');
    $selectedServiceAreas = collect(old('service_area_options', preg_split('/,\s*/', (string) old('service_area', '')) ?: []))
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    $serviceAreaOtherEntries = collect(old('service_area_other', []))
        ->whenEmpty(function ($collection) use ($selectedServiceAreas) {
            return collect($selectedServiceAreas)
                ->filter(fn ($value): bool => \Illuminate\Support\Str::startsWith($value, 'Others: '))
                ->map(fn ($value): string => trim(\Illuminate\Support\Str::after($value, 'Others: ')))
                ->values();
        })
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    $selectedServiceAreas = collect($selectedServiceAreas)
        ->reject(fn ($value): bool => \Illuminate\Support\Str::startsWith($value, 'Others: '))
        ->values()
        ->all();
    if ($serviceAreaOtherEntries !== [] && ! in_array('Others', $selectedServiceAreas, true)) {
        $selectedServiceAreas[] = 'Others';
    }
    $selectedServices = collect(old('service_options', preg_split('/,\s*/', (string) old('services', '')) ?: []))
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && ! \Illuminate\Support\Str::startsWith(trim((string) $value), 'Custom: '))
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    $serviceCustomEntries = collect(old('services_other', []))
        ->whenEmpty(function () {
            return collect(preg_split('/,\s*/', (string) old('services', '')) ?: [])
                ->filter(fn ($value): bool => is_string($value) && \Illuminate\Support\Str::startsWith(trim((string) $value), 'Custom: '))
                ->map(fn ($value): string => trim(\Illuminate\Support\Str::after(trim((string) $value), 'Custom: ')))
                ->values();
        })
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    $selectedProducts = collect(old('product_options', preg_split('/,\s*/', (string) old('products', '')) ?: []))
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && ! \Illuminate\Support\Str::startsWith(trim((string) $value), 'Custom: '))
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    $productCustomEntries = collect(old('products_other_entries', []))
        ->whenEmpty(function () {
            return collect(preg_split('/,\s*/', (string) old('products', '')) ?: [])
                ->filter(fn ($value): bool => is_string($value) && \Illuminate\Support\Str::startsWith(trim((string) $value), 'Custom: '))
                ->map(fn ($value): string => trim(\Illuminate\Support\Str::after(trim((string) $value), 'Custom: ')))
                ->values();
        })
        ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
        ->map(fn ($value): string => trim((string) $value))
        ->values()
        ->all();
    if ($productCustomEntries !== [] && ! in_array('Others', $selectedProducts, true)) {
        $selectedProducts[] = 'Others';
    }
?>

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Regular</h1>
                <p class="mt-1 max-w-3xl text-sm text-gray-500">
                    Regular and retainer deals automatically open here, with RSAT, approvals, execution, reporting, delivery, and continuation tracked inside one record.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex h-11 items-center justify-center rounded-full bg-[#102d79] px-5 text-sm font-semibold text-white shadow-sm hover:bg-[#0d255f]"
                onclick="window.jkncSlideOver.open(document.getElementById('regularManualCreateDrawer'))"
            >
                Create Regular
            </button>
        </div>

        <div class="mb-6 grid gap-3 xl:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">All Regular</p>
                <p class="mt-2 text-3xl font-bold text-gray-900"><?php echo e($stats['all']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">RSAT</p>
                <p class="mt-2 text-3xl font-bold text-indigo-700"><?php echo e($stats['rsat']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Planning</p>
                <p class="mt-2 text-3xl font-bold text-blue-700"><?php echo e($stats['planning']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active</p>
                <p class="mt-2 text-3xl font-bold text-amber-700"><?php echo e($stats['active']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Completed</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700"><?php echo e($stats['completed']); ?></p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-semibold text-gray-900">Regular Registry</h2>
                <p class="mt-1 text-sm text-gray-500">This list is now backed by saved regular engagements instead of placeholder data.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Regular</th>
                            <th class="px-4 py-3 text-left font-medium">Deal</th>
                            <th class="px-4 py-3 text-left font-medium">Company</th>
                            <th class="px-4 py-3 text-left font-medium">Phase</th>
                            <th class="px-4 py-3 text-left font-medium">Owner</th>
                            <th class="px-4 py-3 text-left font-medium">Target</th>
                            <th class="px-4 py-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $regulars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regular): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900"><?php echo e($regular->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($regular->project_code); ?></p>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->deal?->deal_code ?? '-'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->company?->company_name ?: ($regular->business_name ?: '-')); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium <?php echo e($phaseBadgeClasses[$regular->status] ?? 'bg-gray-100 text-gray-700 border border-gray-200'); ?>"><?php echo e($regular->status); ?></span>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($regular->assigned_project_manager ?: '-'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e(optional($regular->target_completion_date)->format('M d, Y') ?: '-'); ?></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e(route('regular.show', $regular)); ?>" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">View</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No regular engagements have created regular records yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'regularManualCreateDrawer','width' => 'sm:max-w-[760px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'regularManualCreateDrawer','width' => 'sm:max-w-[760px]']); ?>
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Create Regular</h2>
            <p class="mt-1 text-sm text-gray-500">Manually create a regular engagement and open the RSAT form to fill out details, scope, activities, and requirements.</p> 
        </div>
        <button type="button" class="rounded-full p-2 text-gray-500 hover:bg-gray-100" onclick="window.jkncSlideOver.close(document.getElementById('regularManualCreateDrawer'))">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form method="POST" action="<?php echo e(route('regular.manual.store')); ?>" class="flex h-full flex-col overflow-hidden">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="source_mode" id="regular_source_mode" value="<?php echo e($oldSourceMode === 'deal' ? 'deal' : 'manual'); ?>">
        <input type="hidden" name="deal_id" id="regular_deal_id" value="<?php echo e(old('deal_id')); ?>">
        <input type="hidden" name="contact_id" id="regular_contact_id" value="<?php echo e(old('contact_id')); ?>">
        <input type="hidden" name="company_id" id="regular_company_id" value="<?php echo e(old('company_id')); ?>">
        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
            <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm font-semibold text-gray-900">How do you want to create this regular engagement?</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <button type="button" data-regular-source-option="deal" class="regular-source-option rounded-2xl border px-4 py-4 text-left transition <?php echo e($oldSourceMode === 'deal' ? 'border-[#102d79] bg-white ring-2 ring-[#102d79]/10' : 'border-gray-200 bg-white hover:border-gray-300'); ?>">
                        <span class="block text-sm font-semibold text-gray-900">Link Existing Deal</span>
                        <span class="mt-1 block text-xs text-gray-500">Pick a regular deal and preload its client, company, and staffing details.</span>
                    </button>
                    <button type="button" data-regular-source-option="manual" class="regular-source-option rounded-2xl border px-4 py-4 text-left transition <?php echo e($oldSourceMode !== 'deal' ? 'border-[#102d79] bg-white ring-2 ring-[#102d79]/10' : 'border-gray-200 bg-white hover:border-gray-300'); ?>">
                        <span class="block text-sm font-semibold text-gray-900">Manual</span>
                        <span class="mt-1 block text-xs text-gray-500">Start manually, then optionally select an existing contact or company.</span>
                    </button>
                </div>
            </section>

            <section id="regularDealLinkSection" class="space-y-3 <?php echo e($oldSourceMode === 'deal' ? '' : 'hidden'); ?>">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Search Existing Deal</label>
                    <input id="regularDealSearch" type="text" placeholder="Type deal code, deal name, client, or company..." autocomplete="off" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                    <p class="mt-2 text-xs text-gray-500">Only regular deals without a linked regular workspace are shown here.</p>
                </div>
                <div id="regularDealResults" class="hidden max-h-64 overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-sm"></div>
                <div id="regularDealSelectionSummary" class="<?php echo e(old('deal_id') ? '' : 'hidden'); ?> rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900"></div>
            </section>

            <section id="regularManualLinkSection" class="space-y-4 <?php echo e($oldSourceMode === 'deal' ? 'hidden' : ''); ?>">
                <div class="rounded-2xl border border-gray-200 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Customer Type</h3>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <?php $__currentLoopData = ['business' => 'Business', 'individual' => 'Individual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                <input type="radio" name="regular_customer_type" value="<?php echo e($value); ?>" <?php if(old('regular_customer_type', 'individual') === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span><?php echo e($label); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div>
                    <h3 id="regularSelectionSectionTitle" class="text-base font-semibold text-gray-900">Select Existing Contact / Client</h3>
                    <p id="regularSearchHelpText" class="mt-1 text-xs text-gray-500">Select a customer type, then search the matching records.</p>
                </div>
                <div class="relative">
                    <label id="regularContactSearchLabel" class="mb-2 block text-sm font-medium text-gray-700" for="regularContactSearch">Search Existing Client</label>
                    <input id="regularContactSearch" type="text" placeholder="Type name, company, email, or mobile..." autocomplete="off" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                    <div id="regularContactResults" class="mt-2 hidden max-h-64 overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-sm"></div>
                </div>
                <div id="regularManualSelectionSummary" class="<?php echo e(old('contact_id') || old('company_id') ? '' : 'hidden'); ?> rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"></div>
            </section>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">RSAT Template</label>
                    <select name="template_id" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                        <option value="">Start from blank/default</option>
                        <?php $__currentLoopData = $rsatTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($template->id); ?>" <?php if((string) old('template_id') === (string) $template->id): echo 'selected'; endif; ?>><?php echo e($template->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Regular Name</label>
                    <input name="name" id="regular_name" value="<?php echo e(old('name')); ?>" required class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Client Name</label>
                    <input name="client_name" id="regular_client_name" value="<?php echo e(old('client_name')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Business Name</label>
                    <input name="business_name" id="regular_business_name" value="<?php echo e(old('business_name')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Planned Start</label>
                    <input type="date" name="planned_start_date" id="regular_planned_start_date" value="<?php echo e(old('planned_start_date')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Target Completion</label>
                    <input type="date" name="target_completion_date" id="regular_target_completion_date" value="<?php echo e(old('target_completion_date')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Client Confirmation Name</label>
                    <input name="client_confirmation_name" id="regular_client_confirmation_name" value="<?php echo e(old('client_confirmation_name')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Project Manager</label>
                    <input name="assigned_project_manager" id="regular_assigned_project_manager" value="<?php echo e(old('assigned_project_manager')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Lead Consultant</label>
                    <input name="assigned_consultant" id="regular_assigned_consultant" value="<?php echo e(old('assigned_consultant')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Lead Associate</label>
                    <input name="assigned_associate" id="regular_assigned_associate" value="<?php echo e(old('assigned_associate')); ?>" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">RSAT Activities / Requirements</label>
                    <textarea name="engagement_requirements_text" rows="5" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900" placeholder="One requirement per line"><?php echo e(old('engagement_requirements_text')); ?></textarea>
                </div>
                <input type="hidden" name="service_area" id="regular_service_area" value="<?php echo e(old('service_area')); ?>">
                <textarea name="services" id="regular_services" class="hidden"><?php echo e(old('services')); ?></textarea>
                <textarea name="products" id="regular_products" class="hidden"><?php echo e(old('products')); ?></textarea>
            </div>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Service Identification</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Service Area</label>
                        <div id="regular-service-area-options-grid" class="grid gap-2 sm:grid-cols-2">
                            <?php $__currentLoopData = $serviceAreaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                    <input type="checkbox" name="service_area_options[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $selectedServiceAreas, true)): echo 'checked'; endif; ?> <?php echo e($option === 'Others' ? 'data-other-target=regular-service-area-other-wrapper' : ''); ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span><?php echo e($option); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div id="regular-service-area-other-wrapper" class="<?php echo e((in_array('Others', $selectedServiceAreas, true) || count($serviceAreaOtherEntries) > 0) ? '' : 'hidden'); ?> mt-2">
                            <input id="regular-service-area-other-input" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" placeholder="Enter custom service area and press Enter">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Services</label>
                        <div id="regularServicesGrid" class="mt-3 grid gap-4 lg:grid-cols-2 <?php echo e(count($selectedServiceAreas) > 0 ? '' : 'hidden'); ?>">
                            <?php $__currentLoopData = $serviceGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $options): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-3 <?php echo e(in_array($group, $selectedServiceAreas, true) ? '' : 'hidden'); ?>" data-regular-service-group="<?php echo e($group); ?>">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600"><?php echo e($group); ?></p>
                                    <div class="space-y-2">
                                        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-start gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="service_options[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $selectedServices, true)): echo 'checked'; endif; ?> class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span><?php echo e($option); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Products</label>
                        <div id="regular-product-options-grid" class="mt-3 grid gap-4">
                            <?php $__currentLoopData = $productOptionsByServiceArea; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceArea => $options): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e(in_array($serviceArea, $selectedServiceAreas, true) ? '' : 'hidden'); ?>" data-regular-product-group="<?php echo e($serviceArea); ?>">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600"><?php echo e($serviceArea); ?></p>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input type="checkbox" name="product_options[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $selectedProducts, true)): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span><?php echo e($option); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-end gap-3">
                <button type="button" class="inline-flex h-11 items-center rounded-full border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50" onclick="window.jkncSlideOver.close(document.getElementById('regularManualCreateDrawer'))">Cancel</button>
                <button type="submit" class="inline-flex h-11 items-center rounded-full bg-[#102d79] px-5 text-sm font-semibold text-white hover:bg-[#0d255f]">Create</button>
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
    (() => {
        const dealRecords = <?php echo json_encode($dealRecords ?? [], 15, 512) ?>;
        const contactRecords = <?php echo json_encode($contactRecords ?? [], 15, 512) ?>;
        const companyRecords = <?php echo json_encode($companyRecords ?? [], 15, 512) ?>;
        const sourceModeInput = document.getElementById('regular_source_mode');
        const dealIdInput = document.getElementById('regular_deal_id');
        const contactIdInput = document.getElementById('regular_contact_id');
        const companyIdInput = document.getElementById('regular_company_id');
        const dealSection = document.getElementById('regularDealLinkSection');
        const manualSection = document.getElementById('regularManualLinkSection');
        const dealSearch = document.getElementById('regularDealSearch');
        const dealResults = document.getElementById('regularDealResults');
        const dealSummary = document.getElementById('regularDealSelectionSummary');
        const contactSearch = document.getElementById('regularContactSearch');
        const contactResults = document.getElementById('regularContactResults');
        const manualSummary = document.getElementById('regularManualSelectionSummary');
        const sourceButtons = Array.from(document.querySelectorAll('[data-regular-source-option]'));
        const serviceAreaChecks = Array.from(document.querySelectorAll('input[name="service_area_options[]"]'));
        const serviceChecks = Array.from(document.querySelectorAll('input[name="service_options[]"]'));
        const productChecks = Array.from(document.querySelectorAll('input[name="product_options[]"]'));

        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value ?? '';
        };

        const applyCommonValues = (record) => {
            setValue('regular_client_name', record.client_name || record.label || '');
            setValue('regular_business_name', record.business_name || record.company_name || '');
            setValue('regular_planned_start_date', record.planned_start_date || '');
            setValue('regular_target_completion_date', record.target_completion_date || '');
            setValue('regular_assigned_project_manager', record.assigned_project_manager || '');
            setValue('regular_assigned_consultant', record.assigned_consultant || '');
            setValue('regular_assigned_associate', record.assigned_associate || '');
            setValue('regular_client_confirmation_name', record.client_confirmation_name || record.client_name || '');
            setValue('regular_service_area', record.service_area || '');
            setValue('regular_services', record.services || '');
            setValue('regular_products', record.products || '');
        };

        const setSourceMode = (mode) => {
            if (sourceModeInput) sourceModeInput.value = mode;
            dealSection?.classList.toggle('hidden', mode !== 'deal');
            manualSection?.classList.toggle('hidden', mode === 'deal');
            sourceButtons.forEach((button) => {
                const active = button.dataset.regularSourceOption === mode;
                button.classList.toggle('border-[#102d79]', active);
                button.classList.toggle('ring-2', active);
                button.classList.toggle('ring-[#102d79]/10', active);
            });
        };

        const renderDealResults = (keyword) => {
            const term = String(keyword || '').trim().toLowerCase();
            const matches = dealRecords.filter((record) => term === '' || String(record.search_blob || '').includes(term)).slice(0, 8);
            if (!dealResults) return;
            if (matches.length === 0) {
                dealResults.classList.add('hidden');
                dealResults.innerHTML = '';
                return;
            }
            dealResults.classList.remove('hidden');
            dealResults.innerHTML = matches.map((record) => `
                <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left hover:bg-gray-50" data-regular-deal-id="${record.id}">
                    <div class="text-sm font-semibold text-gray-900">${record.deal_code || record.label}</div>
                    <div class="text-xs text-gray-500">${record.client_name || ''} ${record.business_name ? '• ' + record.business_name : ''}</div>
                </button>
            `).join('');
            dealResults.querySelectorAll('[data-regular-deal-id]').forEach((button) => {
                button.addEventListener('click', () => {
                    const record = matches.find((item) => String(item.id) === String(button.dataset.regularDealId));
                    if (!record) return;
                    dealIdInput.value = record.id;
                    contactIdInput.value = record.contact_id || '';
                    applyCommonValues(record);
                    if (dealSummary) {
                        dealSummary.classList.remove('hidden');
                        dealSummary.textContent = `${record.deal_code || record.label} linked. Client and service details were prefilled.`;
                    }
                    dealResults.classList.add('hidden');
                });
            });
        };

        const renderContactResults = (keyword) => {
            const term = String(keyword || '').trim().toLowerCase();
            const matches = [...contactRecords, ...companyRecords].filter((record) => term === '' || String(record.search_blob || '').includes(term)).slice(0, 8);
            if (!contactResults) return;
            if (matches.length === 0) {
                contactResults.classList.add('hidden');
                contactResults.innerHTML = '';
                return;
            }
            contactResults.classList.remove('hidden');
            contactResults.innerHTML = matches.map((record) => `
                <button type="button" class="block w-full border-b border-gray-100 px-4 py-3 text-left hover:bg-gray-50">
                    <div class="text-sm font-semibold text-gray-900">${record.label || record.company_name || ''}</div>
                    <div class="text-xs text-gray-500">${record.company_name || record.email || ''}</div>
                </button>
            `).join('');
            contactResults.querySelectorAll('button').forEach((button, index) => {
                button.addEventListener('click', () => {
                    const record = matches[index];
                    if (!record) return;
                    if (record.primary_contact_id || record.company_name) {
                        companyIdInput.value = record.id;
                    } else {
                        contactIdInput.value = record.id;
                    }
                    applyCommonValues({
                        client_name: record.label,
                        business_name: record.company_name,
                        client_confirmation_name: record.label,
                    });
                    if (manualSummary) {
                        manualSummary.classList.remove('hidden');
                        manualSummary.textContent = `${record.label || record.company_name} selected.`;
                    }
                    contactResults.classList.add('hidden');
                });
            });
        };

        const syncSelections = () => {
            const areas = serviceAreaChecks.filter((item) => item.checked).map((item) => item.value);
            const services = serviceChecks.filter((item) => item.checked).map((item) => item.value);
            const products = productChecks.filter((item) => item.checked).map((item) => item.value);
            setValue('regular_service_area', areas.join(', '));
            setValue('regular_services', services.join(', '));
            setValue('regular_products', products.join(', '));
            document.querySelectorAll('[data-regular-service-group]').forEach((group) => {
                group.classList.toggle('hidden', !areas.includes(group.dataset.regularServiceGroup));
            });
            document.querySelectorAll('[data-regular-product-group]').forEach((group) => {
                group.classList.toggle('hidden', !areas.includes(group.dataset.regularProductGroup));
            });
            document.getElementById('regularServicesGrid')?.classList.toggle('hidden', areas.length === 0);
        };

        sourceButtons.forEach((button) => button.addEventListener('click', () => setSourceMode(button.dataset.regularSourceOption)));
        dealSearch?.addEventListener('input', () => renderDealResults(dealSearch.value));
        contactSearch?.addEventListener('input', () => renderContactResults(contactSearch.value));
        serviceAreaChecks.forEach((item) => item.addEventListener('change', syncSelections));
        serviceChecks.forEach((item) => item.addEventListener('change', syncSelections));
        productChecks.forEach((item) => item.addEventListener('change', syncSelections));

        setSourceMode(sourceModeInput?.value || 'manual');
        syncSelections();
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/regular/index.blade.php ENDPATH**/ ?>