@extends('layouts.app')

@section('content')
@php
    $activePillClasses = [
        'Active' => 'border border-green-200 bg-green-100 text-green-700',
        'Inactive' => 'border border-gray-300 bg-gray-100 text-gray-600',
        'Draft' => 'border border-amber-200 bg-amber-100 text-amber-700',
        'Archived' => 'border border-gray-300 bg-gray-100 text-gray-500',
    ];
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mb-5">
        <h1 class="text-3xl font-semibold text-gray-900">Products</h1>
        <p class="mt-1 text-sm text-gray-500">Manage product records, pricing, ownership, and configuration.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('products.index') }}" class="mb-4 flex flex-wrap items-center gap-3">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search Products..."
                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div class="relative">
            <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500"></i>
            <select
                name="active"
                class="h-10 min-w-[120px] rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                onchange="this.form.submit()"
            >
                <option value="All" {{ $activeFilter === 'All' ? 'selected' : '' }}>All</option>
                <option value="Active" {{ $activeFilter === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $activeFilter === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="Draft" {{ $activeFilter === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Archived" {{ $activeFilter === 'Archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        <input type="hidden" name="per_page" value="{{ $perPage }}">

        <button
            type="button"
            id="openCreateProductModal"
            class="ml-auto h-10 min-w-[120px] rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"
        >
            <i class="fas fa-plus mr-1"></i> Product
        </button>
    </form>

    <div id="productSelectionBar" class="mb-3 hidden items-center rounded-lg border border-blue-100 bg-blue-100 px-4 py-2">
        <div class="flex items-center gap-3 text-sm">
            <span id="selectedProductText" class="font-medium text-gray-800">0 Products selected</span>
            <button id="openChangeOwnerModal" type="button" class="h-8 rounded-md border border-gray-200 bg-white px-3 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fas fa-user-pen mr-1"></i> Change Owner
            </button>
        </div>
        <div class="ml-auto flex items-center gap-4 text-sm">
            <button id="clearProductSelection" type="button" class="text-gray-700 underline underline-offset-2 hover:text-gray-900">Clear</button>
            <button id="closeProductSelection" type="button" class="text-gray-700 hover:text-gray-900">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-700">
                    <tr>
                        <th class="w-10 px-3 py-3 text-left"><input id="selectAllProducts" type="checkbox" class="h-4 w-4 rounded border-gray-300"></th>
                        <th data-column-key="product_name" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Name</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_name" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="sku" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>SKU</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="sku" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="product_type" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Type</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_type" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="category" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Category</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="category" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="price" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Price</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="price" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="status" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Status</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="status" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="product_owner" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Owner</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_owner" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        @foreach ($customFields as $field)
                            <th data-column-key="{{ $field->field_key }}" data-column-type="custom" class="group px-3 py-3 text-left font-medium">
                                <div class="inline-flex items-center gap-1">
                                    <span>{{ $field->field_name }}</span>
                                    <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="{{ $field->field_key }}" data-column-type="custom">
                                        <i class="fas fa-ellipsis-v text-[10px]"></i>
                                    </button>
                                </div>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-right normal-case">
                            <button id="openCreateFieldDropdown" type="button" class="text-sm text-blue-600 hover:text-blue-700">+ Create Field</button>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $index => $product)
                            <tr class="product-row text-gray-700" data-row-index="{{ $index }}" data-product-url="{{ route('products.show', $product->product_id) }}">
                                <td class="px-3 py-3">
                                    <input type="checkbox" value="{{ $product->product_id }}" class="product-row-checkbox h-4 w-4 rounded border-gray-300">
                                </td>
                            <td data-column-key="product_name" class="px-3 py-3 font-medium text-gray-900">
                                <a href="{{ route('products.show', $product->product_id) }}" class="hover:text-blue-700">
                                    {{ $product->product_name }}
                                </a>
                            </td>
                            <td data-column-key="sku" class="px-3 py-3 text-gray-600">{{ $product->sku ?: '-' }}</td>
                            <td data-column-key="product_type" class="px-3 py-3 text-gray-600">{{ $product->product_type }}</td>
                            <td data-column-key="category" class="px-3 py-3 text-gray-600">{{ $product->category }}</td>
                            <td data-column-key="price" class="px-3 py-3 text-gray-600">P{{ number_format((float) $product->price, 2) }}</td>
                            <td data-column-key="status" class="px-3 py-3">
                                <select class="h-6 rounded-full px-2 text-xs outline-none {{ $activePillClasses[$product->status] ?? $activePillClasses['Inactive'] }}">
                                    @foreach (['Active', 'Inactive', 'Draft', 'Archived'] as $statusOption)
                                        <option value="{{ $statusOption }}" {{ $product->status === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td data-column-key="product_owner" class="product-owner-cell px-3 py-3 text-gray-600">{{ $product->owner_name ?: '-' }}</td>
                            @foreach ($customFields as $field)
                                @php
                                    $customValue = data_get($product->custom_field_values, $field->field_key, '');
                                @endphp
                                <td data-column-key="{{ $field->field_key }}" class="px-3 py-3 text-gray-600">
                                    @if (($field->field_type ?? '') === 'checkbox')
                                        {{ $customValue === '1' ? 'Yes' : 'No' }}
                                    @elseif (($field->field_type ?? '') === 'currency' && $customValue !== '')
                                        P{{ number_format((float) $customValue, 2) }}
                                    @else
                                        {{ $customValue !== '' ? $customValue : '-' }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-3"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + count($customFields) }}" class="px-3 py-10 text-center text-sm text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-700">
        <span>Total Products: {{ $totalProducts }}</span>
        <div class="ml-auto flex items-center gap-2 text-xs text-gray-600">
            <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                <span>Records per page</span>
                <select name="per_page" class="h-7 rounded border border-gray-200 px-2 text-xs" onchange="this.form.submit()">
                    @foreach ([5, 10, 25, 50] as $size)
                        <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="active" value="{{ $activeFilter }}">
            </form>
            <span>{{ $from }} to {{ $to }} | {{ $currentPage }} to {{ $totalPages }}</span>
        </div>
    </div>
</div>

@include('products.partials.create-product-modal', [
    'owners' => $owners,
    'defaultOwnerId' => $defaultOwnerId,
    'categoryOptions' => $categoryOptions,
    'productTypeOptions' => $productTypeOptions,
    'productAreaOptions' => $productAreaOptions,
    'pricingTypeOptions' => $pricingTypeOptions,
    'taxTypeOptions' => $taxTypeOptions,
    'inventoryTypeOptions' => $inventoryTypeOptions,
    'statusOptions' => $statusOptions,
    'unitOptions' => $unitOptions,
    'serviceOptions' => $serviceOptions,
    'customFields' => $customFields,
    'drawerMeta' => $drawerMeta,
])
@include('products.partials.create-field-dropdown', ['fieldTypes' => $fieldTypes])
@include('products.partials.field-actions-dropdown')
@include('products.partials.create-field-modal')
@include('products.partials.change-owner-modal', [
    'owners' => $owners,
])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const createProductModal = document.getElementById('createProductModal');
    const openCreateModalButton = document.getElementById('openCreateProductModal');
    const closeCreateModalButton = document.getElementById('closeCreateProductModal');
    const cancelCreateModalButton = document.getElementById('cancelCreateProductModal');
    const productOwnerDropdownTrigger = document.getElementById('productOwnerDropdownTrigger');
    const productOwnerDropdownMenu = document.getElementById('productOwnerDropdownMenu');
    const productOwnerSearch = document.getElementById('productOwnerSearch');
    const productOwnerIdInput = document.getElementById('product_owner_id');
    const productOwnerSelectedLabel = document.getElementById('productOwnerSelectedLabel');
    const productCreatedAtLiveValue = document.getElementById('productCreatedAtLiveValue');
    const productCreatedAtMetaValue = document.getElementById('productCreatedAtMetaValue');
    const inventoryTypeInput = document.getElementById('inventory_type');
    const stockQtyWrap = document.getElementById('stockQtyWrap');
    const productAreaOtherWrap = document.getElementById('productAreaOtherWrap');
    const productAreaCheckboxes = Array.from(document.querySelectorAll('.product-area-checkbox'));
    const linkedServiceInput = document.getElementById('linked_service_id');
    const productOwnerOptions = Array.from(document.querySelectorAll('.product-owner-option'));
    const selectionBar = document.getElementById('productSelectionBar');
    const selectedProductText = document.getElementById('selectedProductText');
    const clearSelection = document.getElementById('clearProductSelection');
    const closeSelection = document.getElementById('closeProductSelection');
    const selectAllProducts = document.getElementById('selectAllProducts');
    const openChangeOwnerModalButton = document.getElementById('openChangeOwnerModal');
    const rowCheckboxes = Array.from(document.querySelectorAll('.product-row-checkbox'));
    const tableRows = Array.from(document.querySelectorAll('.product-row'));
    const statusSelects = Array.from(document.querySelectorAll('.product-row select'));

    const changeOwnerModal = document.getElementById('changeOwnerModal');
    const changeOwnerPanel = document.getElementById('changeOwnerPanel');
    const changeOwnerModalOverlay = document.getElementById('changeOwnerModalOverlay');
    const cancelChangeOwnerModal = document.getElementById('cancelChangeOwnerModal');
    const closeChangeOwnerModalX = document.getElementById('closeChangeOwnerModalX');
    const changeOwnerForm = document.getElementById('changeOwnerForm');
    const selectedProductsFields = document.getElementById('selectedProductsFields');
    const changeOwnerModalCount = document.getElementById('changeOwnerModalCount');
    const selectedOwnerId = document.getElementById('selectedOwnerId');
    const ownerSearchInput = document.getElementById('changeOwnerSearchInput');
    const ownerDropdownMenu = document.getElementById('ownerDropdownMenu');
    const toggleOwnerDropdown = document.getElementById('toggleOwnerDropdown');
    const ownerOptions = Array.from(document.querySelectorAll('.owner-option'));
    const saveChangeOwnerBtn = document.getElementById('saveChangeOwnerBtn');
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
    const headerActionTriggers = Array.from(document.querySelectorAll('.field-header-trigger'));
    const fieldActionsMenu = document.getElementById('fieldActionsMenu');
    const fieldActionButtons = Array.from(document.querySelectorAll('.field-action-item'));
    const tableHead = document.querySelector('table thead');
    const tableBody = document.querySelector('table tbody');
    let createFieldDropdownOpen = false;
    let fieldActionsMenuOpen = false;
    let activeFieldColumnKey = null;
    let activeFieldIsCustom = false;
    const columnSortState = {};
    const columnFilters = {};

    const createProductPanel = document.getElementById('createProductPanel');
    const createProductModalOverlay = document.getElementById('createProductModalOverlay');
    let productCreatedAtIntervalId = null;

    const formatCreatedAt = (date) => new Intl.DateTimeFormat('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
    }).format(date);

    const renderProductCreatedAtClock = () => {
        const label = formatCreatedAt(new Date());
        if (productCreatedAtLiveValue) {
            productCreatedAtLiveValue.textContent = label;
        }
        if (productCreatedAtMetaValue) {
            productCreatedAtMetaValue.textContent = label;
        }
    };

    const stopProductCreatedAtClock = () => {
        if (productCreatedAtIntervalId) {
            window.clearInterval(productCreatedAtIntervalId);
            productCreatedAtIntervalId = null;
        }
    };

    const startProductCreatedAtClock = () => {
        stopProductCreatedAtClock();
        renderProductCreatedAtClock();
        productCreatedAtIntervalId = window.setInterval(renderProductCreatedAtClock, 1000);
    };

    const syncInventoryFields = () => {
        const showStock = inventoryTypeInput?.value === 'Inventory';
        stockQtyWrap?.classList.toggle('hidden', !showStock);
    };

    const syncProductAreaOther = () => {
        const hasOther = productAreaCheckboxes.some((checkbox) => checkbox.checked && checkbox.value === 'Others');
        productAreaOtherWrap?.classList.toggle('hidden', !hasOther);
    };

    const syncProductAreaSelection = (changedCheckbox = null) => {
        const noneCheckbox = productAreaCheckboxes.find((checkbox) => checkbox.value === 'None');
        const nonNoneCheckboxes = productAreaCheckboxes.filter((checkbox) => checkbox.value !== 'None');

        if (changedCheckbox?.value === 'None' && changedCheckbox.checked) {
            nonNoneCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        }

        if (changedCheckbox && changedCheckbox.value !== 'None' && changedCheckbox.checked && noneCheckbox) {
            noneCheckbox.checked = false;
        }

        const anyNonNoneChecked = nonNoneCheckboxes.some((checkbox) => checkbox.checked);
        if (!anyNonNoneChecked && noneCheckbox && !noneCheckbox.checked) {
            noneCheckbox.checked = true;
        }

        if (linkedServiceInput?.value === '' && !anyNonNoneChecked && noneCheckbox) {
            noneCheckbox.checked = true;
        }

        syncProductAreaOther();
    };

    const openCreateModal = () => {
        if (!createProductModal || !createProductPanel) {
            return;
        }

        createProductModal.classList.remove('hidden');
        createProductModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            createProductModalOverlay?.classList.remove('opacity-0');
            createProductPanel.classList.remove('translate-x-full');
        });

        startProductCreatedAtClock();
        syncInventoryFields();
        syncProductAreaOther();
    };

    const closeProductOwnerDropdown = () => {
        productOwnerDropdownMenu?.classList.add('hidden');
    };

    const closeCreateModal = () => {
        if (!createProductModal || !createProductPanel) {
            return;
        }

        closeProductOwnerDropdown();
        stopProductCreatedAtClock();
        createProductModalOverlay?.classList.add('opacity-0');
        createProductPanel.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');

        window.setTimeout(() => {
            createProductModal.classList.add('hidden');
            createProductModal.setAttribute('aria-hidden', 'true');
        }, 300);
    };

    const openChangeOwnerModal = () => {
        const selected = rowCheckboxes.filter((checkbox) => checkbox.checked);
        if (selected.length === 0) {
            return;
        }

        selectedProductsFields.innerHTML = selected
            .map((checkbox) => `<input type="hidden" name="selected_products[]" value="${checkbox.value}">`)
            .join('');

        changeOwnerModalCount.textContent = selected.length === 1
            ? '1 Product Selected'
            : `${selected.length} Products Selected`;

        ownerSearchInput.value = '';
        selectedOwnerId.value = '';
        saveChangeOwnerBtn.disabled = true;
        ownerOptions.forEach((option) => option.classList.remove('hidden', 'bg-blue-50'));
        ownerDropdownMenu.classList.add('hidden');
        changeOwnerModal.classList.remove('hidden');
        changeOwnerModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            changeOwnerModalOverlay?.classList.remove('opacity-0');
            changeOwnerPanel?.classList.remove('translate-x-full');
        });
    };

    const closeChangeOwnerModalFn = () => {
        changeOwnerModalOverlay?.classList.add('opacity-0');
        changeOwnerPanel?.classList.add('translate-x-full');
        ownerDropdownMenu.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        window.setTimeout(() => {
            changeOwnerModal.classList.add('hidden');
            changeOwnerModal.setAttribute('aria-hidden', 'true');
        }, 300);
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

        if (picklistOptionsSection) {
            picklistOptionsSection.classList.toggle('hidden', type !== 'picklist');
        }
        if (lookupSection) {
            lookupSection.classList.toggle('hidden', type !== 'lookup');
            lookupSection.classList.toggle('grid', type === 'lookup');
        }
        if (defaultValueSection) {
            defaultValueSection.classList.toggle('hidden', type === 'lookup');
            defaultValueSection.classList.toggle('grid', type !== 'lookup');
        }
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
        const dropdownWidth = createFieldDropdownMenu.offsetWidth || 208;
        const viewportPadding = 12;

        let left = rect.right - dropdownWidth;
        if (left < viewportPadding) {
            left = viewportPadding;
        }
        if (left + dropdownWidth > window.innerWidth - viewportPadding) {
            left = window.innerWidth - dropdownWidth - viewportPadding;
        }

        let top = rect.bottom + 6;
        const dropdownHeight = createFieldDropdownMenu.offsetHeight || 260;
        if (top + dropdownHeight > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, rect.top - dropdownHeight - 6);
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
        if (!createFieldDropdownMenu) {
            return;
        }
        createFieldDropdownMenu.classList.add('hidden');
        createFieldDropdownOpen = false;
    };

    const getColumnHeader = (columnKey) => {
        if (!tableHead) {
            return null;
        }
        return tableHead.querySelector(`th[data-column-key="${columnKey}"]`);
    };

    const getColumnCells = (columnKey) => {
        if (!tableBody) {
            return [];
        }
        return Array.from(tableBody.querySelectorAll(`td[data-column-key="${columnKey}"]`));
    };

    const cellValue = (cell) => {
        if (!cell) {
            return '';
        }
        const select = cell.querySelector('select');
        if (select) {
            return (select.value || '').trim().toLowerCase();
        }
        return (cell.textContent || '').trim().toLowerCase();
    };

    const applyRowFilters = () => {
        if (!tableBody) {
            return;
        }
        const rows = Array.from(tableBody.querySelectorAll('.product-row'));
        rows.forEach((row) => {
            let visible = true;
            Object.entries(columnFilters).forEach(([columnKey, filterText]) => {
                if (filterText === '') {
                    return;
                }
                const cell = row.querySelector(`td[data-column-key="${columnKey}"]`);
                if (!cellValue(cell).includes(filterText)) {
                    visible = false;
                }
            });
            row.classList.toggle('hidden', !visible);
        });
    };

    const sortByColumn = (columnKey) => {
        if (!tableBody) {
            return;
        }
        const rows = Array.from(tableBody.querySelectorAll('.product-row'));
        if (rows.length === 0) {
            return;
        }

        const nextDirection = columnSortState[columnKey] === 'asc' ? 'desc' : 'asc';
        columnSortState[columnKey] = nextDirection;

        rows.sort((rowA, rowB) => {
            const valueA = cellValue(rowA.querySelector(`td[data-column-key="${columnKey}"]`));
            const valueB = cellValue(rowB.querySelector(`td[data-column-key="${columnKey}"]`));
            const numericA = Number(valueA.replace(/[^0-9.-]/g, ''));
            const numericB = Number(valueB.replace(/[^0-9.-]/g, ''));
            let comparison = 0;

            if (!Number.isNaN(numericA) && !Number.isNaN(numericB) && valueA !== '' && valueB !== '') {
                comparison = numericA - numericB;
            } else {
                comparison = valueA.localeCompare(valueB);
            }

            return nextDirection === 'asc' ? comparison : -comparison;
        });

        rows.forEach((row) => tableBody.appendChild(row));
    };

    const collapseColumn = (columnKey) => {
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        if (!header) {
            return;
        }
        const willHide = !header.classList.contains('hidden');
        header.classList.toggle('hidden', willHide);
        cells.forEach((cell) => cell.classList.toggle('hidden', willHide));
    };

    const moveColumnLeft = (columnKey) => {
        if (!tableHead || !tableBody) {
            return;
        }
        const header = getColumnHeader(columnKey);
        if (!header || !header.previousElementSibling || !header.previousElementSibling.hasAttribute('data-column-key')) {
            return;
        }
        const previousHeader = header.previousElementSibling;
        const previousKey = previousHeader.getAttribute('data-column-key');
        if (!previousKey) {
            return;
        }

        tableHead.querySelector('tr')?.insertBefore(header, previousHeader);
        Array.from(tableBody.querySelectorAll('.product-row')).forEach((row) => {
            const cell = row.querySelector(`td[data-column-key="${columnKey}"]`);
            const previousCell = row.querySelector(`td[data-column-key="${previousKey}"]`);
            if (cell && previousCell) {
                row.insertBefore(cell, previousCell);
            }
        });
    };

    const autoFitColumn = (columnKey) => {
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        if (!header) {
            return;
        }
        header.style.width = '1%';
        header.classList.add('whitespace-nowrap');
        cells.forEach((cell) => {
            cell.classList.add('whitespace-nowrap');
        });
    };

    const removeCustomColumn = (columnKey) => {
        if (!activeFieldIsCustom) {
            return;
        }
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        header?.remove();
        cells.forEach((cell) => cell.remove());
    };

    const positionFieldActionsMenu = (trigger) => {
        if (!fieldActionsMenu || !trigger) {
            return;
        }
        const rect = trigger.getBoundingClientRect();
        const menuWidth = fieldActionsMenu.offsetWidth || 224;
        const viewportPadding = 12;
        let left = rect.right - menuWidth;
        if (left < viewportPadding) {
            left = viewportPadding;
        }
        if (left + menuWidth > window.innerWidth - viewportPadding) {
            left = window.innerWidth - menuWidth - viewportPadding;
        }
        let top = rect.bottom + 6;
        const menuHeight = fieldActionsMenu.offsetHeight || 300;
        if (top + menuHeight > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, rect.top - menuHeight - 6);
        }
        fieldActionsMenu.style.left = `${left}px`;
        fieldActionsMenu.style.top = `${top}px`;
    };

    const closeFieldActionsMenu = () => {
        if (!fieldActionsMenu) {
            return;
        }
        fieldActionsMenu.classList.add('hidden');
        fieldActionsMenuOpen = false;
        activeFieldColumnKey = null;
        activeFieldIsCustom = false;
    };

    const openFieldActionsMenu = (trigger, columnKey, isCustom) => {
        if (!fieldActionsMenu) {
            return;
        }
        activeFieldColumnKey = columnKey;
        activeFieldIsCustom = isCustom;
        const removeBtn = fieldActionsMenu.querySelector('[data-action="remove-column"]');
        if (removeBtn) {
            removeBtn.classList.toggle('opacity-50', !isCustom);
            removeBtn.classList.toggle('cursor-not-allowed', !isCustom);
            removeBtn.classList.toggle('pointer-events-none', !isCustom);
            removeBtn.setAttribute('aria-disabled', !isCustom ? 'true' : 'false');
        }
        fieldActionsMenu.classList.remove('hidden');
        fieldActionsMenuOpen = true;
        positionFieldActionsMenu(trigger);
    };

    const clearAllSelection = () => {
        rowCheckboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });
        if (selectAllProducts) {
            selectAllProducts.checked = false;
        }
    };

    const selectedCountLabel = (count) => {
        if (count === 1) {
            return '1 Product selected';
        }
        return `${count} Products selected`;
    };

    const refreshSelection = () => {
        const selectedCount = rowCheckboxes.filter((checkbox) => checkbox.checked).length;
        selectedProductText.textContent = selectedCountLabel(selectedCount);
        selectionBar.classList.toggle('hidden', selectedCount === 0);
        selectionBar.classList.toggle('flex', selectedCount > 0);

        if (selectAllProducts) {
            selectAllProducts.checked = rowCheckboxes.length > 0 && selectedCount === rowCheckboxes.length;
        }

        tableRows.forEach((row, index) => {
            const isSelected = rowCheckboxes[index] && rowCheckboxes[index].checked;
            row.classList.toggle('bg-blue-50', isSelected);
        });
    };

    const refreshStatusPill = (select) => {
        select.classList.remove(
            'border-green-200',
            'bg-green-100',
            'text-green-700',
            'border-amber-200',
            'bg-amber-100',
            'text-amber-700',
            'border-gray-300',
            'bg-gray-100',
            'text-gray-600'
        );

        if (select.value === 'Active') {
            select.classList.add('border-green-200', 'bg-green-100', 'text-green-700');
        } else if (select.value === 'Draft') {
            select.classList.add('border-amber-200', 'bg-amber-100', 'text-amber-700');
        } else {
            select.classList.add('border-gray-300', 'bg-gray-100', 'text-gray-600');
        }
    };

    openCreateModalButton?.addEventListener('click', openCreateModal);
    closeCreateModalButton?.addEventListener('click', closeCreateModal);
    cancelCreateModalButton?.addEventListener('click', closeCreateModal);

    createProductModalOverlay?.addEventListener('click', closeCreateModal);

    productOwnerDropdownTrigger?.addEventListener('click', function () {
        productOwnerDropdownMenu?.classList.toggle('hidden');
        if (productOwnerDropdownMenu && !productOwnerDropdownMenu.classList.contains('hidden')) {
            productOwnerSearch?.focus();
        }
    });

    productOwnerSearch?.addEventListener('input', function () {
        const keyword = productOwnerSearch.value.toLowerCase().trim();
        productOwnerOptions.forEach((option) => {
            const name = (option.dataset.ownerName || '').toLowerCase();
            const email = (option.dataset.ownerEmail || '').toLowerCase();
            option.classList.toggle('hidden', keyword !== '' && !name.includes(keyword) && !email.includes(keyword));
        });
    });

    productOwnerOptions.forEach((option) => {
        option.addEventListener('click', function () {
            if (productOwnerIdInput) {
                productOwnerIdInput.value = option.dataset.ownerId || '';
            }
            if (productOwnerSelectedLabel) {
                productOwnerSelectedLabel.textContent = `Owner: ${option.dataset.ownerName || ''}`;
            }
            closeProductOwnerDropdown();
        });
    });

    inventoryTypeInput?.addEventListener('change', syncInventoryFields);
    productAreaCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', () => syncProductAreaSelection(checkbox)));
    linkedServiceInput?.addEventListener('change', () => syncProductAreaSelection());

    openChangeOwnerModalButton?.addEventListener('click', openChangeOwnerModal);
    cancelChangeOwnerModal?.addEventListener('click', closeChangeOwnerModalFn);
    closeChangeOwnerModalX?.addEventListener('click', closeChangeOwnerModalFn);

    changeOwnerModalOverlay?.addEventListener('click', closeChangeOwnerModalFn);

    toggleOwnerDropdown?.addEventListener('click', function () {
        ownerDropdownMenu.classList.toggle('hidden');
        if (!ownerDropdownMenu.classList.contains('hidden')) {
            ownerSearchInput.focus();
        }
    });

    ownerSearchInput?.addEventListener('focus', function () {
        ownerDropdownMenu.classList.remove('hidden');
    });

    ownerSearchInput?.addEventListener('input', function () {
        const keyword = ownerSearchInput.value.trim().toLowerCase();
        selectedOwnerId.value = '';
        saveChangeOwnerBtn.disabled = true;
        ownerOptions.forEach((option) => {
            const ownerName = (option.dataset.ownerName || '').toLowerCase();
            const ownerEmail = (option.dataset.ownerEmail || '').toLowerCase();
            const matches = keyword === '' || ownerName.includes(keyword) || ownerEmail.includes(keyword);
            option.classList.toggle('hidden', !matches);
            option.classList.remove('bg-blue-50');
        });
    });

    ownerOptions.forEach((option) => {
        option.addEventListener('click', function () {
            selectedOwnerId.value = option.dataset.ownerId || '';
            ownerSearchInput.value = option.dataset.ownerName || '';
            ownerDropdownMenu.classList.add('hidden');
            ownerOptions.forEach((item) => item.classList.remove('bg-blue-50'));
            option.classList.add('bg-blue-50');
            saveChangeOwnerBtn.disabled = selectedOwnerId.value === '';
        });
    });

    document.addEventListener('click', function (event) {
        if (productOwnerDropdownMenu && !productOwnerDropdownMenu.classList.contains('hidden')) {
            const clickedProductOwnerTrigger = productOwnerDropdownTrigger ? productOwnerDropdownTrigger.contains(event.target) : false;
            const clickedProductOwnerSearch = productOwnerSearch ? productOwnerSearch.contains(event.target) : false;
            if (!productOwnerDropdownMenu.contains(event.target) && !clickedProductOwnerTrigger && !clickedProductOwnerSearch) {
                closeProductOwnerDropdown();
            }
        }

        if (createFieldDropdownMenu && createFieldDropdownOpen) {
            const clickedFieldTrigger = openCreateFieldDropdown ? openCreateFieldDropdown.contains(event.target) : false;
            if (!createFieldDropdownMenu.contains(event.target) && !clickedFieldTrigger) {
                closeCreateFieldDropdownFn();
            }
        }

        if (fieldActionsMenu && fieldActionsMenuOpen) {
            const clickedHeaderTrigger = headerActionTriggers.some((trigger) => trigger.contains(event.target));
            if (!fieldActionsMenu.contains(event.target) && !clickedHeaderTrigger) {
                closeFieldActionsMenu();
            }
        }

        if (ownerDropdownMenu && !ownerDropdownMenu.classList.contains('hidden')) {
            const clickedSearch = ownerSearchInput ? ownerSearchInput.contains(event.target) : false;
            const clickedToggle = toggleOwnerDropdown ? toggleOwnerDropdown.contains(event.target) : false;
            if (!ownerDropdownMenu.contains(event.target) && !clickedSearch && !clickedToggle) {
                ownerDropdownMenu.classList.add('hidden');
            }
        }
    });

    changeOwnerForm?.addEventListener('submit', function () {
        saveChangeOwnerBtn.disabled = true;
    });

    openCreateFieldDropdown?.addEventListener('click', function () {
        if (createFieldDropdownOpen) {
            closeCreateFieldDropdownFn();
            return;
        }
        closeFieldActionsMenu();
        openCreateFieldDropdownFn();
    });

    headerActionTriggers.forEach((trigger) => {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            const columnKey = trigger.dataset.columnKey || '';
            const columnType = trigger.dataset.columnType || 'base';
            const isCustom = columnType === 'custom';

            if (fieldActionsMenuOpen && activeFieldColumnKey === columnKey) {
                closeFieldActionsMenu();
                return;
            }

            closeCreateFieldDropdownFn();
            openFieldActionsMenu(trigger, columnKey, isCustom);
        });
    });

    fieldActionButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (!activeFieldColumnKey) {
                closeFieldActionsMenu();
                return;
            }

            const action = button.dataset.action || '';

            if (action === 'sort') {
                sortByColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'filter') {
                const currentFilter = columnFilters[activeFieldColumnKey] || '';
                const input = window.prompt('Filter value', currentFilter);
                if (input !== null) {
                    columnFilters[activeFieldColumnKey] = input.trim().toLowerCase();
                    applyRowFilters();
                }
                closeFieldActionsMenu();
                return;
            }

            if (action === 'edit-field') {
                window.alert('Edit Field is not available yet.');
                closeFieldActionsMenu();
                return;
            }

            if (action === 'add-column') {
                closeFieldActionsMenu();
                openCreateFieldDropdownFn();
                return;
            }

            if (action === 'collapse') {
                collapseColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'move-left') {
                moveColumnLeft(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'autofit') {
                autoFitColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'remove-column') {
                if (!activeFieldIsCustom) {
                    return;
                }
                removeCustomColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
            }
        });
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

    window.addEventListener('resize', function () {
        if (createFieldDropdownOpen) {
            positionCreateFieldDropdown();
        }
        if (fieldActionsMenuOpen) {
            const activeTrigger = headerActionTriggers.find((trigger) => trigger.dataset.columnKey === activeFieldColumnKey) || null;
            positionFieldActionsMenu(activeTrigger);
        }
    });

    document.addEventListener('scroll', function () {
        if (createFieldDropdownOpen) {
            positionCreateFieldDropdown();
        }
        if (fieldActionsMenuOpen) {
            const activeTrigger = headerActionTriggers.find((trigger) => trigger.dataset.columnKey === activeFieldColumnKey) || null;
            positionFieldActionsMenu(activeTrigger);
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeChangeOwnerModalFn();
            closeCreateFieldModalFn();
            closeProductOwnerDropdown();
            closeCreateFieldDropdownFn();
            closeFieldActionsMenu();
        }
    });

    addPicklistOption?.addEventListener('click', function () {
        picklistOptionsContainer?.appendChild(buildPicklistOptionRow(''));
    });

    picklistOptionsContainer?.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-picklist-option');
        if (!button) {
            return;
        }
        const row = button.closest('.flex');
        if (row) {
            row.remove();
        }
        ensurePicklistOptionRows();
    });

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', refreshSelection);
    });

    tableRows.forEach((row) => {
        row.classList.add('cursor-pointer');
        row.addEventListener('click', function (event) {
            const clickedInteractive = event.target.closest('a, button, input, select, textarea, label');
            if (clickedInteractive) {
                return;
            }
            const url = row.dataset.productUrl;
            if (url) {
                window.location.href = url;
            }
        });
    });

    selectAllProducts?.addEventListener('change', function () {
        rowCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAllProducts.checked;
        });
        refreshSelection();
    });

    clearSelection?.addEventListener('click', function () {
        clearAllSelection();
        refreshSelection();
    });

    closeSelection?.addEventListener('click', function () {
        clearAllSelection();
        refreshSelection();
    });

    statusSelects.forEach((select) => {
        select.addEventListener('change', function () {
            refreshStatusPill(select);
        });
        refreshStatusPill(select);
    });

    syncInventoryFields();
    syncProductAreaSelection();

    const initialFieldType = createFieldTypeInput ? createFieldTypeInput.value : 'picklist';
    const initialTypeButton = fieldTypeButtons.find((button) => (button.dataset.fieldType || '') === initialFieldType);
    applyCreateFieldTypeUI(initialFieldType, initialTypeButton?.dataset.fieldLabel || 'Picklist');

    @if ($errors->any() && (old('product_name') !== null || old('sku') !== null || old('owner_id') !== null || $errors->has('owner_id')))
        openCreateModal();
    @endif

    @if ($errors->any() && is_array(old('selected_products')))
        openChangeOwnerModal();
    @endif

    @if (old('field_type'))
        openCreateFieldModalFn('{{ old('field_type') }}', '{{ $fieldTypes->firstWhere('value', old('field_type'))['label'] ?? 'Picklist' }}');
    @endif
});
</script>
@endsection
