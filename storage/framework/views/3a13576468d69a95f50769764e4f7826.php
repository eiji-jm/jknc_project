<?php $__env->startSection('content'); ?>
<?php
    $statusClasses = [
        'Pending Approval' => 'border-amber-200 bg-amber-50 text-amber-700',
        'Draft' => 'border-slate-200 bg-slate-50 text-slate-700',
        'Active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'Inactive' => 'border-amber-200 bg-amber-50 text-amber-700',
        'Archived' => 'border-rose-200 bg-rose-50 text-rose-700',
    ];
?>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Services</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage standardized services assigned to <?php echo e($company->company_name); ?>.</p>
                </div>
                <button type="button" id="openCompanyServiceModalCreate" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i> Service
                </button>
            </div>

            <?php if(session('services_success')): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?php echo e(session('services_success')); ?>

                </div>
            <?php endif; ?>

            <div class="mb-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Services</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['active']); ?></p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recurring Services</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['recurring']); ?></p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Due In 7 Days</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['due_soon']); ?></p>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('company.services.index', $company->id)); ?>" class="mb-4 flex flex-wrap items-center gap-3">
                <div class="relative w-full max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input type="text" name="search" value="<?php echo e($filters['search']); ?>" placeholder="Search company services..." class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
                <select name="status" class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all">Status: All</option>
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if($filters['status'] === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="category" class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all">Category: All</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if($filters['category'] === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="assigned_unit" class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all">Assigned Unit: All</option>
                    <?php $__currentLoopData = $assignedUnitOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if($filters['assigned_unit'] === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="h-10 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Apply</button>
            </form>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto overflow-y-visible">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-left font-medium">Service Name</th>
                                <th class="px-3 py-3 text-left font-medium">Category</th>
                                <th class="px-3 py-3 text-left font-medium">Frequency</th>
                                <th class="px-3 py-3 text-left font-medium">Engagement Type</th>
                                <th class="px-3 py-3 text-left font-medium">Price / Rate</th>
                                <th class="px-3 py-3 text-left font-medium">Assigned Unit</th>
                                <th class="px-3 py-3 text-left font-medium">Status</th>
                                <th class="px-3 py-3 text-left font-medium">Service Owner</th>
                                <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="px-3 py-3 text-left font-medium"><?php echo e($field->field_name); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-3 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="text-gray-700 hover:bg-gray-50">
                                    <td class="px-3 py-3">
                                        <a href="<?php echo e(route('company.services.show', [$company->id, $service->id])); ?>" class="font-medium text-gray-900 hover:text-blue-700"><?php echo e($service->service_name); ?></a>
                                        <div class="mt-1 text-xs text-gray-500">ID <?php echo e($service->service_id); ?></div>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600"><?php echo e($service->category ?: '-'); ?></td>
                                    <td class="px-3 py-3 text-gray-600"><?php echo e($service->frequency ?: '-'); ?></td>
                                    <td class="px-3 py-3 text-gray-600"><?php echo e(implode(', ', $service->engagement_structure ?? []) ?: '-'); ?></td>
                                    <td class="px-3 py-3 text-gray-600">
                                        <?php if($service->rate_per_unit): ?>
                                            <?php echo e(number_format((float) $service->rate_per_unit, 2)); ?> / <?php echo e($service->unit); ?>

                                        <?php elseif($service->price_fee): ?>
                                            <?php echo e(number_format((float) $service->price_fee, 2)); ?>

                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600"><?php echo e($service->assigned_unit ?: '-'); ?></td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium <?php echo e($statusClasses[$service->status] ?? 'border-gray-200 bg-gray-50 text-gray-700'); ?>"><?php echo e($service->status); ?></span>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600"><?php echo e($service->creator?->name ?: '-'); ?></td>
                                    <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="px-3 py-3 text-gray-600"><?php echo e(data_get($service->custom_field_values, $field->field_key, '-') ?: '-'); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" data-company-service-edit='<?php echo json_encode($service, 15, 512) ?>'>Edit</button>
                                            <form method="POST" action="<?php echo e(route('company.services.destroy', [$company->id, $service->id])); ?>" onsubmit="return confirm('Remove this service?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="rounded-full border border-red-200 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Remove</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="<?php echo e(9 + $customFields->count()); ?>" class="px-3 py-10 text-center text-sm text-gray-500">No company services found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php echo $__env->make('services.partials.service-form-modal', [
    'fieldPrefix' => 'companyService',
    'modalId' => 'companyServiceModal',
    'title' => 'Assign Service',
    'subtitle' => 'This service will be linked to ' . $company->company_name . '.',
    'action' => route('company.services.store', $company->id),
    'companyLocked' => true,
    'lockedCompany' => $company,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('companyServiceModal');
    const form = document.getElementById('companyServiceForm');
    const openButton = document.getElementById('openCompanyServiceModalCreate');
    const closeButtons = modal.querySelectorAll('[data-close-service-modal]');
    const editButtons = document.querySelectorAll('[data-company-service-edit]');
    const methodInput = document.getElementById('companyServiceFormMethod');
    const title = document.getElementById('companyServiceModalTitle');
    const submit = document.getElementById('companyServiceFormSubmit');
    const updateUrlTemplate = <?php echo json_encode(route('company.services.update', [$company->id, '__SERVICE__'])) ?>;
    const createUrl = <?php echo json_encode(route('company.services.store', $company->id), 512) ?>;

    const openModal = () => window.jkncSlideOver.open(modal);
    const closeModal = () => window.jkncSlideOver.close(modal);

    const formatDateTimeLocal = (value) => {
        if (!value) return '';
        return String(value).replace(' ', 'T').slice(0, 16);
    };

    const setMultiSelect = (id, values) => {
        const select = document.getElementById(id);
        const selected = Array.isArray(values) ? values : [];
        Array.from(select.options).forEach((option) => {
            option.selected = selected.includes(option.value);
        });
        select.dispatchEvent(new Event('change'));
    };

    const resetForm = () => {
        form.reset();
        form.action = createUrl;
        methodInput.value = 'POST';
        title.textContent = 'Assign Service';
        submit.textContent = 'Save';
        const statusField = document.getElementById('companyServiceFormStatus');
        if (statusField) {
            statusField.value = 'Pending Approval';
            statusField.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };

    const fillForm = (service) => {
        document.getElementById('companyServiceFormServiceName').value = service.service_name ?? '';
        document.getElementById('companyServiceFormServiceDescription').value = service.service_description ?? '';
        document.getElementById('companyServiceFormServiceOutput').value = service.service_activity_output ?? '';
        setMultiSelect('companyServiceFormServiceArea', service.service_area ?? []);
        document.getElementById('companyServiceFormServiceAreaOther').value = service.service_area_other ?? '';
        document.getElementById('companyServiceFormCategory').value = service.category ?? '';
        document.getElementById('companyServiceFormFrequency').value = service.frequency ?? '';
        document.getElementById('companyServiceFormFrequency').dispatchEvent(new Event('change'));
        document.getElementById('companyServiceFormScheduleRule').value = service.schedule_rule ?? '';
        document.getElementById('companyServiceFormDeadline').value = formatDateTimeLocal(service.deadline ?? '');
        document.getElementById('companyServiceFormReminder').value = service.reminder_lead_time ?? '';
        const requirementGroups = service.requirements?.groups ?? {};
        document.getElementById('companyServiceFormRequirementCategory').value = service.requirement_category ?? service.requirements?.category ?? '';
        document.getElementById('companyServiceFormRequirements').value = Array.isArray(service.requirements?.items) ? service.requirements.items.join('\n') : '';
        document.getElementById('companyServiceFormRequirementsIndividual').value = Array.isArray(requirementGroups.individual) ? requirementGroups.individual.join('\n') : '';
        document.getElementById('companyServiceFormRequirementsJuridical').value = Array.isArray(requirementGroups.juridical) ? requirementGroups.juridical.join('\n') : '';
        document.getElementById('companyServiceFormRequirementsOther').value = Array.isArray(requirementGroups.other) ? requirementGroups.other.join('\n') : '';

        if (service.requirements?.category && Array.isArray(service.requirements?.items)) {
            if (service.requirements.category === 'SOLE / NATURAL PERSON / INDIVIDUAL') {
                document.getElementById('companyServiceFormRequirementsIndividual').value = service.requirements.items.join('\n');
            } else if (service.requirements.category === 'JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)') {
                document.getElementById('companyServiceFormRequirementsJuridical').value = service.requirements.items.join('\n');
            } else {
                document.getElementById('companyServiceFormRequirementsOther').value = service.requirements.items.join('\n');
            }
        }
        ['companyServiceFormRequirementsIndividual', 'companyServiceFormRequirementsJuridical', 'companyServiceFormRequirementsOther'].forEach((id) => {
            document.getElementById(id)?.dispatchEvent(new Event('input', { bubbles: true }));
        });
        setMultiSelect('companyServiceFormEngagement', service.engagement_structure ?? []);
        document.getElementById('companyServiceFormUnit').value = service.unit ?? '';
        document.getElementById('companyServiceFormRatePerUnit').value = service.rate_per_unit ?? '';
        document.getElementById('companyServiceFormMinUnits').value = service.min_units ?? '';
        document.getElementById('companyServiceFormMaxCap').value = service.max_cap ?? '';
        document.getElementById('companyServiceFormPriceFee').value = service.price_fee ?? '';
        document.getElementById('companyServiceFormCost').value = service.cost_of_service ?? '';
        const companyTaxType = service.tax_type ?? 'Tax Exclusive';
        document.querySelectorAll('#companyServiceModal input[name="tax_type"]').forEach((input) => {
            input.checked = input.value === companyTaxType;
        });
        document.getElementById('companyServiceFormAssignedUnit').value = service.assigned_unit ?? '';
        document.getElementById('companyServiceFormAssignedUnit').dispatchEvent(new Event('change', { bubbles: true }));
        const statusField = document.getElementById('companyServiceFormStatus');
        if (statusField) {
            statusField.value = service.status ?? 'Pending Approval';
            statusField.dispatchEvent(new Event('input', { bubbles: true }));
        }
        Object.entries(service.custom_field_values ?? {}).forEach(([key, value]) => {
            const input = form.querySelector(`[name="custom_fields[${key}]"]`);
            if (!input) return;
            if (input.type === 'checkbox') {
                input.checked = value === '1' || value === 1 || value === true;
            } else {
                input.value = value ?? '';
            }
        });
    };

    openButton?.addEventListener('click', function () {
        resetForm();
        openModal();
    });

    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    editButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const service = JSON.parse(this.dataset.companyServiceEdit);
            resetForm();
            form.action = updateUrlTemplate.replace('__SERVICE__', service.id);
            methodInput.value = 'PUT';
            title.textContent = 'Edit Service';
            submit.textContent = 'Update';
            fillForm(service);
            openModal();
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\services.blade.php ENDPATH**/ ?>