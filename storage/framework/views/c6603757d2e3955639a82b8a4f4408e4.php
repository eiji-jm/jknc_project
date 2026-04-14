<?php $__env->startSection('content'); ?>
<div class="px-6 py-6 lg:px-8">
    <div class="mb-5">
        <h1 class="text-3xl font-semibold text-gray-900">Company</h1>
        <p class="mt-1 text-sm text-gray-500">Manage companies and deal relationships</p>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('company.index')); ?>" class="mb-4 flex flex-wrap items-center gap-3">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input
                type="text"
                name="search"
                value="<?php echo e($search); ?>"
                placeholder="Search Company..."
                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <select
            name="type"
            class="h-10 min-w-[180px] rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            onchange="this.form.submit()"
        >
            <option value="All" <?php echo e($typeFilter === 'All' ? 'selected' : ''); ?>>All</option>
            <?php $__currentLoopData = $companyTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($companyType); ?>" <?php echo e($typeFilter === $companyType ? 'selected' : ''); ?>><?php echo e($companyType); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">

        <button
            type="button"
            id="openAddCompanyModal"
            class="ml-auto h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"
        >
            + Add Company
        </button>
    </form>

    <div id="selectionActionBar" class="mb-3 hidden rounded-lg border border-blue-100 bg-blue-50 px-3 py-2">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-medium text-gray-800"><span id="selectedCount">0</span> selected</span>
            <button type="button" class="h-8 rounded-md border border-gray-200 bg-white px-3 hover:bg-gray-50">Assign Owner</button>
            <button type="button" id="clearSelection" class="ml-auto text-gray-700 hover:underline">Clear</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="w-10 px-3 py-3 text-left"><input id="selectAll" type="checkbox" class="h-4 w-4 rounded border-gray-300"></th>
                        <th class="px-3 py-3 text-left">Company Name</th>
                        <th class="px-3 py-3 text-left">Phone / Mobile</th>
                        <th class="px-3 py-3 text-left">TIN</th>
                        <th class="px-3 py-3 text-left">Status</th>
                        <th class="px-3 py-3 text-left">Company Owner</th>
                        <th class="px-3 py-3 text-left">Actions</th>
                        <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-3 py-3 text-left"><?php echo e($field['name']); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-3 py-3 text-right normal-case">
                            <button id="openCreateFieldDropdown" type="button" class="text-sm font-medium text-blue-600 hover:text-blue-700">+ Create Field</button>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $companyName = data_get($company, 'company_name', '');
                            $companyPhone = data_get($company, 'phone');
                            $companyMobile = data_get($company, 'mobile_no');
                            $companyTin = data_get($company, 'tin_no');
                            $companyStatus = data_get($company, 'status', 'draft');
                            $companyOwner = data_get($company, 'owner_name', 'Owner 1');
                            $initials = collect(explode(' ', (string) $company->company_name))
                                ->filter()
                                ->take(2)
                                ->map(fn (string $part) => mb_substr($part, 0, 1))
                                ->implode('');
                            $initials = strtoupper($initials !== '' ? $initials : mb_substr((string) $companyName, 0, 2));
                        ?>
                        <tr class="text-gray-700 hover:bg-gray-50">
                            <td class="px-3 py-3"><input type="checkbox" class="row-checkbox h-4 w-4 rounded border-gray-300"></td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700"><?php echo e($initials); ?></div>
                                    <a href="<?php echo e(route('company.kyc', ['company' => $company->id, 'tab' => 'business-client-information'])); ?>" class="font-medium text-gray-900 hover:text-blue-700">
                                        <?php echo e($companyName); ?>

                                    </a>
                                </div>
                            </td>
                            <td class="px-3 py-3"><?php echo e($companyPhone ?: ($companyMobile ?: '-')); ?></td>
                            <td class="px-3 py-3"><?php echo e($companyTin ?: '-'); ?></td>
                            <td class="px-3 py-3">
                                <?php
                                    $statusClasses = match ($companyStatus) {
                                        'approved' => 'bg-green-100 text-green-700 border border-green-200',
                                        'pending_approval' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                        'inactive', 'rejected' => 'bg-red-100 text-red-700 border border-red-200',
                                        default => 'bg-gray-100 text-gray-700 border border-gray-200',
                                    };
                                    $statusLabel = str_replace('_', ' ', ucfirst((string) $companyStatus));
                                ?>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($statusClasses); ?>"><?php echo e($statusLabel); ?></span>
                            </td>
                            <td class="px-3 py-3"><?php echo e($companyOwner ?: 'Owner 1'); ?></td>
                            <td class="px-3 py-3">
                                <a href="<?php echo e(route('company.kyc', ['company' => $company->id, 'tab' => 'business-client-information'])); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                    View
                                </a>
                            </td>
                            <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $customValue = $company->custom_fields[$field['key']] ?? ($field['default_value'] ?? '');
                                ?>
                                <td class="px-3 py-3 text-gray-600">
                                    <?php if(($field['type'] ?? '') === 'checkbox'): ?>
                                        <?php echo e($customValue === '1' ? 'Yes' : 'No'); ?>

                                    <?php elseif(($field['type'] ?? '') === 'currency' && $customValue !== ''): ?>
                                        P<?php echo e(number_format((float) $customValue, 2)); ?>

                                    <?php else: ?>
                                        <?php echo e($customValue !== '' ? $customValue : '-'); ?>

                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-3 py-3"></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(7 + count($customFields)); ?>" class="px-3 py-10 text-center text-sm text-gray-500">No companies found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-700">
        <span>Total Companies: <?php echo e($companies->total()); ?></span>
        <?php $__currentLoopData = $typeCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span><?php echo e($label); ?>: <span class="text-gray-900"><?php echo e($count); ?></span></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="ml-auto flex items-center gap-2 text-xs text-gray-600">
            <form method="GET" action="<?php echo e(route('company.index')); ?>" class="flex items-center gap-2">
                <span>Rows</span>
                <select name="per_page" class="h-7 rounded border border-gray-200 px-2 text-xs" onchange="this.form.submit()">
                    <?php $__currentLoopData = [5, 10, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($size); ?>" <?php echo e((int) $perPage === $size ? 'selected' : ''); ?>><?php echo e($size); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                <input type="hidden" name="type" value="<?php echo e($typeFilter); ?>">
            </form>
            <span><?php echo e($companies->firstItem() ?? 0); ?>-<?php echo e($companies->lastItem() ?? 0); ?> of <?php echo e($companies->total()); ?></span>
        </div>
    </div>
</div>

<?php echo $__env->make('company.partials.modal-add-company', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.create-field-dropdown', [
    'fieldTypes' => $fieldTypes,
    'dropdownId' => 'createFieldDropdownMenu',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.create-field-modal', [
    'createFieldActionRoute' => route('company.custom-fields.store'),
    'lookupModules' => $lookupModules,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('addCompanyModal');
    const openButton = document.getElementById('openAddCompanyModal');
    const closeButtons = document.querySelectorAll('[data-close-company-modal]');
    const openCreateFieldDropdown = document.getElementById('openCreateFieldDropdown');
    const createFieldDropdownMenu = document.getElementById('createFieldDropdownMenu');
    const fieldTypeButtons = Array.from(document.querySelectorAll('.create-field-type-option'));
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
    const selectAll = document.getElementById('selectAll');
    const rowChecks = Array.from(document.querySelectorAll('.row-checkbox'));
    const actionBar = document.getElementById('selectionActionBar');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelection = document.getElementById('clearSelection');
    let createFieldDropdownOpen = false;

    const openModal = () => {
        if (!modal) {
            return;
        }

        window.jkncSlideOver.open(modal);
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }

        window.jkncSlideOver.close(modal);
    };

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

    const openCreateFieldModalFn = (type, label) => {
        applyCreateFieldTypeUI(type, label);
        createFieldDropdownMenu?.classList.add('hidden');
        createFieldDropdownOpen = false;
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

    const positionCreateFieldDropdown = () => {
        if (!openCreateFieldDropdown || !createFieldDropdownMenu) {
            return;
        }

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

    const openCreateFieldDropdownFn = () => {
        if (!createFieldDropdownMenu) {
            return;
        }

        createFieldDropdownMenu.classList.remove('hidden');
        createFieldDropdownOpen = true;
        positionCreateFieldDropdown();
    };

    const closeCreateFieldDropdownFn = () => {
        createFieldDropdownMenu?.classList.add('hidden');
        createFieldDropdownOpen = false;
    };

    const refreshSelection = () => {
        const count = rowChecks.filter((item) => item.checked).length;
        selectedCount.textContent = count;
        actionBar.classList.toggle('hidden', count === 0);

        if (selectAll) {
            selectAll.checked = rowChecks.length > 0 && count === rowChecks.length;
        }
    };

    openButton?.addEventListener('click', openModal);

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal?.addEventListener('click', function (event) {
        if (event.target === modal || event.target.hasAttribute('data-drawer-overlay')) {
            closeModal();
        }
    });

    openCreateFieldDropdown?.addEventListener('click', function () {
        if (createFieldDropdownOpen) {
            closeCreateFieldDropdownFn();
            return;
        }

        openCreateFieldDropdownFn();
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
        if (createFieldDropdownMenu && createFieldDropdownOpen) {
            const clickedFieldTrigger = openCreateFieldDropdown ? openCreateFieldDropdown.contains(event.target) : false;
            if (!createFieldDropdownMenu.contains(event.target) && !clickedFieldTrigger) {
                closeCreateFieldDropdownFn();
            }
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
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

    selectAll?.addEventListener('change', function () {
        rowChecks.forEach((check) => { check.checked = selectAll.checked; });
        refreshSelection();
    });

    rowChecks.forEach((check) => check.addEventListener('change', refreshSelection));
    clearSelection?.addEventListener('click', function () {
        rowChecks.forEach((check) => { check.checked = false; });
        if (selectAll) {
            selectAll.checked = false;
        }
        refreshSelection();
    });

    const initialFieldType = createFieldTypeInput ? createFieldTypeInput.value : 'picklist';
    const initialTypeButton = fieldTypeButtons.find((button) => (button.dataset.fieldType || '') === initialFieldType);
    applyCreateFieldTypeUI(initialFieldType, initialTypeButton?.dataset.fieldLabel || 'Picklist');

    <?php if($errors->any()): ?>
        <?php if(old('field_type')): ?>
            openCreateFieldModalFn('<?php echo e(old('field_type')); ?>', '<?php echo e($fieldTypes->firstWhere('value', old('field_type'))['label'] ?? 'Picklist'); ?>');
        <?php else: ?>
            openModal();
        <?php endif; ?>
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/company/company.blade.php ENDPATH**/ ?>