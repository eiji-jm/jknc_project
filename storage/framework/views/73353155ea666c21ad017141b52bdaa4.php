<?php
    $fieldPrefix = $fieldPrefix ?? 'service';
    $modalId = $modalId ?? $fieldPrefix . 'Modal';
    $formId = $formId ?? $fieldPrefix . 'Form';
    $methodId = $methodId ?? $fieldPrefix . 'FormMethod';
    $titleId = $titleId ?? $fieldPrefix . 'ModalTitle';
    $submitId = $submitId ?? $fieldPrefix . 'FormSubmit';
?>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => $modalId,'width' => 'sm:max-w-[680px] lg:max-w-[720px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modalId),'width' => 'sm:max-w-[680px] lg:max-w-[720px]']); ?>
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="<?php echo e($titleId); ?>" class="text-2xl font-semibold text-gray-900"><?php echo e($title); ?></h2>
                <?php if(! empty($subtitle)): ?>
                    <p class="mt-1 text-sm text-gray-500"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <button type="button" data-close-service-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form
        id="<?php echo e($formId); ?>"
        method="POST"
        action="<?php echo e($action); ?>"
        class="flex min-h-0 flex-1 flex-col"
        x-data="serviceFormState({
            serviceAreaOptions: <?php echo \Illuminate\Support\Js::from($serviceAreaOptions->values())->toHtml() ?>,
            categoryOptions: <?php echo \Illuminate\Support\Js::from($categories->values())->toHtml() ?>,
            engagementOptions: <?php echo \Illuminate\Support\Js::from($engagementOptions->values())->toHtml() ?>,
            initialServiceArea: <?php echo \Illuminate\Support\Js::from(array_values((array) old('service_area', [])))->toHtml() ?>,
            initialCategory: <?php echo \Illuminate\Support\Js::from(old('category_other', old('category', '')))->toHtml() ?>,
            initialEngagement: <?php echo \Illuminate\Support\Js::from(array_values((array) old('engagement_structure', [])))->toHtml() ?>,
            initialFrequency: <?php echo \Illuminate\Support\Js::from(old('frequency', ''))->toHtml() ?>,
            initialStatus: <?php echo \Illuminate\Support\Js::from(old('status', 'Pending Approval'))->toHtml() ?>,
            initialAssignedUnit: <?php echo \Illuminate\Support\Js::from(old('assigned_unit', ''))->toHtml() ?>,
            initialRequirementsIndividual: <?php echo \Illuminate\Support\Js::from(old('requirements_individual', ''))->toHtml() ?>,
            initialRequirementsJuridical: <?php echo \Illuminate\Support\Js::from(old('requirements_juridical', ''))->toHtml() ?>,
            initialRequirementsOther: <?php echo \Illuminate\Support\Js::from(old('requirements_other', ''))->toHtml() ?>
        })"
        x-init="initialize()"
    >
        <?php echo csrf_field(); ?>
        <input type="hidden" id="<?php echo e($methodId); ?>" name="_method" value="POST">

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="space-y-5">
                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Service Intake</p>
                            <p class="mt-1 text-sm text-gray-500">Configure the catalog record before routing, pricing, scheduling, and recurring rules. After saving, it will be submitted for admin approval.</p>
                        </div>
                        <input id="<?php echo e($fieldPrefix); ?>FormStatus" name="status" type="hidden" x-model="currentStatus" value="<?php echo e(old('status', 'Pending Approval')); ?>">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-gray-500">Submission Status</label>
                                <div class="flex h-10 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-sm font-medium text-amber-700" x-text="currentStatus || 'Pending Approval'"></div>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-gray-500">Assigned Unit</label>
                                <select id="<?php echo e($fieldPrefix); ?>FormAssignedUnit" name="assigned_unit" x-model="currentAssignedUnit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select unit</option>
                                    <?php $__currentLoopData = $assignedUnitOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if($companyLocked ?? false): ?>
                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Company Link</h3>
                        <p class="mb-4 text-xs text-gray-500">This service will be linked to the current company record.</p>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Linked Company</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900"><?php echo e($lockedCompany->company_name); ?></p>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Basic Information</h3>
                    <p class="mb-4 text-xs text-gray-500">Define the core name, summary, and delivery output of this service.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Name <span class="text-red-500">*</span></label>
                            <input id="<?php echo e($fieldPrefix); ?>FormServiceName" name="service_name" type="text" value="<?php echo e(old('service_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            <?php $__errorArgs = ['service_name'];
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
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Service Description <span class="text-red-500">*</span></label>
                                <textarea id="<?php echo e($fieldPrefix); ?>FormServiceDescription" name="service_description" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required><?php echo e(old('service_description')); ?></textarea>
                                <?php $__errorArgs = ['service_description'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Service Activity / Output <span class="text-red-500">*</span></label>
                                <textarea id="<?php echo e($fieldPrefix); ?>FormServiceOutput" name="service_activity_output" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required><?php echo e(old('service_activity_output')); ?></textarea>
                                <?php $__errorArgs = ['service_activity_output'];
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
                </section>

                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Service Classification</h3>
                    <p class="mb-4 text-xs text-gray-500">Categorize the service area and service family before pricing and routing.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Area <span class="text-red-500">*</span></label>
                            <select id="<?php echo e($fieldPrefix); ?>FormServiceArea" class="hidden" multiple x-model="selectedServiceAreas">
                                <?php $__currentLoopData = $serviceAreaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="rounded-lg border border-gray-200 bg-white p-3">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <template x-for="option in serviceAreaOptions" :key="option">
                                        <button
                                            type="button"
                                            @click="toggleServiceArea(option)"
                                            class="flex min-h-[50px] items-center gap-3 rounded-lg border px-3 py-2.5 text-left text-sm font-medium transition"
                                            :class="selectedServiceAreas.includes(option) ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50/40'"
                                        >
                                            <span class="flex h-4 w-4 items-center justify-center rounded border text-[10px]" :class="selectedServiceAreas.includes(option) ? 'border-blue-500 bg-blue-600 text-white' : 'border-gray-300 bg-white text-transparent'">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span x-text="option"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <template x-for="area in selectedServiceAreas" :key="'hidden-'+area">
                                <input type="hidden" name="service_area[]" :value="area">
                            </template>
                            <p class="mt-2 text-xs text-gray-500">Select one or more service areas.</p>
                            <?php $__errorArgs = ['service_area'];
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
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Service Area</label>
                            <input
                                id="<?php echo e($fieldPrefix); ?>FormServiceAreaOther"
                                name="service_area_other"
                                type="text"
                                value="<?php echo e(old('service_area_other')); ?>"
                                :disabled="!showOtherServiceArea"
                                :class="showOtherServiceArea ? 'bg-white text-gray-900' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                placeholder="Specify other service area"
                            >
                            <p class="mt-2 text-xs text-gray-500">Use this only when `Others` is selected.</p>
                            <?php $__errorArgs = ['service_area_other'];
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

                        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px]">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Category</label>
                                <select id="<?php echo e($fieldPrefix); ?>FormCategory" name="category" x-model="selectedCategory" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select category</option>
                                    <template x-for="option in categoryOptions" :key="option">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                </select>
                                <?php $__errorArgs = ['category'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Add Category</label>
                                <div class="flex gap-2">
                                    <input
                                        id="<?php echo e($fieldPrefix); ?>FormCategoryOther"
                                        x-model.trim="newCategory"
                                        name="category_other"
                                        type="text"
                                        value="<?php echo e(old('category_other')); ?>"
                                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        placeholder="Type new category"
                                        @keydown.enter.prevent="addCategory()"
                                    >
                                    <button
                                        type="button"
                                        @click="addCategory()"
                                        class="h-10 shrink-0 rounded-lg border border-blue-200 bg-blue-50 px-3 text-sm font-medium text-blue-700 hover:bg-blue-100"
                                    >
                                        Add
                                    </button>
                                </div>
                                <?php $__errorArgs = ['category_other'];
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
                </section>

                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Engagement & Scheduling</h3>
                    <p class="mb-4 text-xs text-gray-500">Define how the service is delivered and when it should be tracked.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Engagement Structure <span class="text-red-500">*</span></label>
                            <select id="<?php echo e($fieldPrefix); ?>FormEngagement" class="hidden" multiple x-model="selectedEngagements">
                                <?php $__currentLoopData = $engagementOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="rounded-lg border border-gray-200 bg-white p-3">
                                <div class="grid gap-2">
                                    <template x-for="option in engagementOptions" :key="option">
                                        <button
                                            type="button"
                                            @click="toggleEngagement(option)"
                                            class="flex min-h-[50px] items-center gap-3 rounded-lg border px-3 py-2.5 text-left text-sm font-medium transition"
                                            :class="selectedEngagements.includes(option) ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50/40'"
                                        >
                                            <span class="flex h-4 w-4 items-center justify-center rounded border text-[10px]" :class="selectedEngagements.includes(option) ? 'border-blue-500 bg-blue-600 text-white' : 'border-gray-300 bg-white text-transparent'">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span x-text="option"></span>
                                        </button>
                                    </template>
                                </div>
                                <template x-for="engagement in selectedEngagements" :key="'engagement-hidden-'+engagement">
                                    <input type="hidden" name="engagement_structure[]" :value="engagement">
                                </template>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Select one or more engagement types.</p>
                            <?php $__errorArgs = ['engagement_structure'];
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

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Frequency</label>
                                <select id="<?php echo e($fieldPrefix); ?>FormFrequency" name="frequency" @change="syncFrequency($event.target.value)" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select frequency</option>
                                    <?php $__currentLoopData = $frequencyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['frequency'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Reminder Lead Time</label>
                                <select id="<?php echo e($fieldPrefix); ?>FormReminder" name="reminder_lead_time" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select lead time</option>
                                    <?php $__currentLoopData = $reminderLeadTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div x-show="showScheduleRule" x-cloak class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Schedule Rule</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormScheduleRule" name="schedule_rule" type="text" value="<?php echo e(old('schedule_rule')); ?>" placeholder="Every 5th of the month" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <?php $__errorArgs = ['schedule_rule'];
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
                            <div x-show="showDeadline" x-cloak class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Deadline</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormDeadline" name="deadline" type="datetime-local" value="<?php echo e(old('deadline')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <?php $__errorArgs = ['deadline'];
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

                        <div class="grid gap-3 md:grid-cols-3">
                            <div class="rounded-lg border border-gray-200 bg-white px-3 py-3">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Status</p>
                                <p class="mt-1 text-sm font-semibold text-gray-800" x-text="currentStatus || 'Draft'"></p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white px-3 py-3">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Assigned Unit</p>
                                <p class="mt-1 text-sm font-semibold text-gray-800" x-text="currentAssignedUnit || 'Not set'"></p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white px-3 py-3">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500">Recurring</p>
                                <p class="mt-1 text-sm font-semibold" :class="isRecurring ? 'text-emerald-700' : 'text-gray-700'" x-text="isRecurring ? 'Yes' : 'No'"></p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Requirements</h3>
                    <p class="mb-4 text-xs text-gray-500">These are the default service requirements. Deals will fetch only the matching group based on the selected client or business organization type.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">SOLE / NATURAL PERSON / INDIVIDUAL</label>
                            <textarea id="<?php echo e($fieldPrefix); ?>FormRequirementsIndividual" name="requirements_individual" x-model="requirementsIndividual" rows="4" placeholder="Enter one requirement per line" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('requirements_individual', $requirementTemplateDefaults['individual'] ?? '')); ?></textarea>
                            <p class="mt-2 text-xs text-gray-500">Default template: Valid ID, DTI Registration. Each line is saved as one bullet item.</p>
                            <div class="mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2" x-show="bulletItems(requirementsIndividual).length" x-cloak>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Bullet Preview</p>
                                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                                    <template x-for="(item, index) in bulletItems(requirementsIndividual)" :key="'individual-' + index">
                                        <li class="flex items-start gap-2">
                                            <span class="mt-[2px] text-blue-600">&bull;</span>
                                            <span x-text="item"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)</label>
                            <textarea id="<?php echo e($fieldPrefix); ?>FormRequirementsJuridical" name="requirements_juridical" x-model="requirementsJuridical" rows="4" placeholder="Enter one requirement per line" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('requirements_juridical', $requirementTemplateDefaults['juridical'] ?? '')); ?></textarea>
                            <p class="mt-2 text-xs text-gray-500">Default template: SEC Registration, GIS, Articles of Incorporation. Each line is saved as one bullet item.</p>
                            <div class="mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2" x-show="bulletItems(requirementsJuridical).length" x-cloak>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Bullet Preview</p>
                                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                                    <template x-for="(item, index) in bulletItems(requirementsJuridical)" :key="'juridical-' + index">
                                        <li class="flex items-start gap-2">
                                            <span class="mt-[2px] text-blue-600">&bull;</span>
                                            <span x-text="item"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Requirements</label>
                            <textarea id="<?php echo e($fieldPrefix); ?>FormRequirementsOther" name="requirements_other" x-model="requirementsOther" rows="4" placeholder="Enter one requirement per line" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('requirements_other', $requirementTemplateDefaults['other'] ?? '')); ?></textarea>
                            <p class="mt-2 text-xs text-gray-500">Default template: Special Permit. Each line is saved as one bullet item.</p>
                            <div class="mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2" x-show="bulletItems(requirementsOther).length" x-cloak>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Bullet Preview</p>
                                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                                    <template x-for="(item, index) in bulletItems(requirementsOther)" :key="'other-' + index">
                                        <li class="flex items-start gap-2">
                                            <span class="mt-[2px] text-blue-600">&bull;</span>
                                            <span x-text="item"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="<?php echo e($fieldPrefix); ?>FormRequirementCategory" name="requirement_category" value="<?php echo e(old('requirement_category')); ?>">
                        <input type="hidden" id="<?php echo e($fieldPrefix); ?>FormRequirements" name="requirements" value="<?php echo e(old('requirements')); ?>">
                        <?php $__errorArgs = ['requirement_category'];
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
                </section>

                <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Pricing</h3>
                    <p class="mb-4 text-xs text-gray-500">Capture unit economics, caps, and fallback fees for service billing.</p>
                    <div class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Unit <span class="text-red-500">*</span></label>
                                <select id="<?php echo e($fieldPrefix); ?>FormUnit" name="unit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                    <option value="">Select unit</option>
                                    <?php $__currentLoopData = $unitOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['unit'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Rate per Unit</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormRatePerUnit" name="rate_per_unit" type="number" min="0" step="0.01" value="<?php echo e(old('rate_per_unit')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <?php $__errorArgs = ['rate_per_unit'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Minimum Units</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormMinUnits" name="min_units" type="number" min="1" step="1" value="<?php echo e(old('min_units')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Maximum Cap</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormMaxCap" name="max_cap" type="number" min="0" step="0.01" value="<?php echo e(old('max_cap')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Price / Fee</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormPriceFee" name="price_fee" type="number" min="0" step="0.01" value="<?php echo e(old('price_fee')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <?php $__errorArgs = ['price_fee'];
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
                                <label class="mb-2 block text-sm font-medium text-gray-700">Cost of Service</label>
                                <input id="<?php echo e($fieldPrefix); ?>FormCost" name="cost_of_service" type="number" min="0" step="0.01" value="<?php echo e(old('cost_of_service')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Tax Treatment <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $taxTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700">
                                        <input
                                            id="<?php echo e($fieldPrefix); ?>FormTaxType<?php echo e(\Illuminate\Support\Str::slug($option)); ?>"
                                            name="tax_type"
                                            type="radio"
                                            value="<?php echo e($option); ?>"
                                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                            <?php if(old('tax_type', 'Tax Exclusive') === $option): echo 'checked'; endif; ?>
                                        >
                                        <span><?php echo e($option); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Tax Inclusive means the entered amount already includes tax. Tax Exclusive means tax is added on top later.</p>
                            <?php $__errorArgs = ['tax_type'];
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
                </section>

                <?php if($customFields->isNotEmpty()): ?>
                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Custom Fields</h3>
                        <p class="mb-4 text-xs text-gray-500">Capture any additional metadata configured for service records.</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $fieldName = 'custom_fields['.$field->field_key.']';
                                    $fieldId = $fieldPrefix.'CustomField'.\Illuminate\Support\Str::studly($field->field_key);
                                ?>
                                <div class="<?php echo e($field->field_type === 'textarea' ? 'md:col-span-2' : ''); ?>">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        <?php echo e($field->field_name); ?>

                                        <?php if($field->is_required): ?>
                                            <span class="text-red-500">*</span>
                                        <?php endif; ?>
                                    </label>

                                    <?php if($field->field_type === 'textarea'): ?>
                                        <textarea
                                            id="<?php echo e($fieldId); ?>"
                                            name="<?php echo e($fieldName); ?>"
                                            rows="3"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        ><?php echo e(old($fieldName)); ?></textarea>
                                    <?php elseif($field->field_type === 'picklist'): ?>
                                        <select
                                            id="<?php echo e($fieldId); ?>"
                                            name="<?php echo e($fieldName); ?>"
                                            class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        >
                                            <option value="">Select <?php echo e(strtolower($field->field_name)); ?></option>
                                            <?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($option); ?>" <?php if(old($fieldName) === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php elseif($field->field_type === 'checkbox'): ?>
                                        <label class="flex h-10 items-center gap-3 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700">
                                            <input
                                                id="<?php echo e($fieldId); ?>"
                                                name="<?php echo e($fieldName); ?>"
                                                type="checkbox"
                                                value="1"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                <?php if(old($fieldName)): echo 'checked'; endif; ?>
                                            >
                                            <span>Enabled</span>
                                        </label>
                                    <?php else: ?>
                                        <input
                                            id="<?php echo e($fieldId); ?>"
                                            name="<?php echo e($fieldName); ?>"
                                            type="<?php echo e(in_array($field->field_type, ['number', 'currency'], true) ? 'number' : 'text'); ?>"
                                            value="<?php echo e(old($fieldName)); ?>"
                                            <?php if(in_array($field->field_type, ['number', 'currency'], true)): ?>
                                                step="0.01"
                                            <?php endif; ?>
                                            class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        >
                                    <?php endif; ?>

                                    <?php $__errorArgs = [$fieldName];
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
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <input
                    id="<?php echo e($fieldPrefix); ?>FormCompany"
                    name="company_id"
                    type="hidden"
                    value="<?php echo e(old('company_id', $companyLocked ? $lockedCompany->id : '')); ?>"
                >
            </div>
        </div>

        <div class="border-t border-gray-100 px-6 py-4 sm:px-8">
            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-service-modal class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="<?php echo e($submitId); ?>" type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
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
function serviceFormState(config) {
    return {
        serviceAreaOptions: config.serviceAreaOptions ?? [],
        categoryOptions: config.categoryOptions ?? [],
        engagementOptions: config.engagementOptions ?? [],
        selectedServiceAreas: config.initialServiceArea ?? [],
        selectedCategory: config.initialCategory ?? '',
        newCategory: '',
        selectedEngagements: config.initialEngagement ?? [],
        frequency: config.initialFrequency ?? '',
        showDeadline: false,
        showScheduleRule: false,
        showOtherServiceArea: false,
        isRecurring: false,
        currentStatus: config.initialStatus ?? 'Pending Approval',
        currentAssignedUnit: config.initialAssignedUnit ?? '',
        requirementsIndividual: config.initialRequirementsIndividual ?? '',
        requirementsJuridical: config.initialRequirementsJuridical ?? '',
        requirementsOther: config.initialRequirementsOther ?? '',

        initialize() {
            this.selectedServiceAreas = Array.isArray(this.selectedServiceAreas) ? this.selectedServiceAreas : [];
            this.selectedEngagements = Array.isArray(this.selectedEngagements) ? this.selectedEngagements : [];
            this.selectedCategory = this.selectedCategory ?? '';
            this.syncServiceAreaState();
            this.syncFrequency(this.frequency);
            this.syncRecurring();

            const serviceAreaSelect = document.getElementById('<?php echo e($fieldPrefix); ?>FormServiceArea');
            serviceAreaSelect?.addEventListener('change', (event) => {
                this.selectedServiceAreas = Array.from(event.target.selectedOptions).map((option) => option.value);
                this.syncServiceAreaState();
            });

            const engagementSelect = document.getElementById('<?php echo e($fieldPrefix); ?>FormEngagement');
            engagementSelect?.addEventListener('change', (event) => {
                this.selectedEngagements = Array.from(event.target.selectedOptions).map((option) => option.value);
                this.syncRecurring();
            });

            this.$watch('selectedServiceAreas', () => this.syncServiceAreaState());
            this.$watch('selectedEngagements', () => this.syncRecurring());
        },

        toggleServiceArea(option) {
            if (this.selectedServiceAreas.includes(option)) {
                this.selectedServiceAreas = this.selectedServiceAreas.filter((value) => value !== option);
            } else {
                this.selectedServiceAreas = [...this.selectedServiceAreas, option];
            }
            this.syncHiddenSelect('<?php echo e($fieldPrefix); ?>FormServiceArea', this.selectedServiceAreas);
            this.syncServiceAreaState();
        },

        toggleEngagement(option) {
            if (this.selectedEngagements.includes(option)) {
                this.selectedEngagements = this.selectedEngagements.filter((value) => value !== option);
            } else {
                this.selectedEngagements = [...this.selectedEngagements, option];
            }
            this.syncHiddenSelect('<?php echo e($fieldPrefix); ?>FormEngagement', this.selectedEngagements);
            this.syncRecurring();
        },

        syncHiddenSelect(id, values) {
            const select = document.getElementById(id);
            if (!select) return;
            Array.from(select.options).forEach((option) => {
                option.selected = values.includes(option.value);
            });
        },

        syncServiceAreaState() {
            this.showOtherServiceArea = this.selectedServiceAreas.includes('Others');
            if (!this.showOtherServiceArea) {
                const input = document.getElementById('<?php echo e($fieldPrefix); ?>FormServiceAreaOther');
                if (input) input.value = '';
            }
        },

        addCategory() {
            const value = (this.newCategory || '').trim();
            if (!value) return;
            if (!this.categoryOptions.includes(value)) {
                this.categoryOptions = [...this.categoryOptions, value].sort((a, b) => a.localeCompare(b));
            }
            this.selectedCategory = value;
            this.newCategory = '';
        },

        syncFrequency(value) {
            this.frequency = value || '';
            this.showDeadline = this.frequency !== '';
            this.showScheduleRule = ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually', 'Custom'].includes(this.frequency);
        },

        syncRecurring() {
            this.isRecurring = this.selectedEngagements.includes('Regular (Retainer)') || this.selectedEngagements.includes('Hybrid');
        },

        bulletItems(value) {
            return String(value || '')
                .split(/\r?\n/)
                .map((item) => item.trim())
                .filter((item) => item !== '');
        },
    };
}
</script>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/services/partials/service-form-modal.blade.php ENDPATH**/ ?>