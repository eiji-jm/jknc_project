@extends('layouts.app')

@section('content')
@php
    $statusClasses = [
        'Draft' => 'border-slate-200 bg-slate-50 text-slate-700',
        'Active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'Inactive' => 'border-amber-200 bg-amber-50 text-amber-700',
        'Archived' => 'border-rose-200 bg-rose-50 text-rose-700',
    ];
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Services</h1>
            <p class="mt-1 text-sm text-gray-500">Standardized service catalog with configurable fields, routing, scheduling, and pricing.</p>
        </div>
        <button type="button" id="openGlobalServiceModalCreate" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
            <i class="fas fa-plus mr-2 text-xs"></i> Add Service
        </button>
    </div>

    @if (session('services_success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('services_success') }}
        </div>
    @endif

    <div class="mb-6 grid gap-3 xl:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Services</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recurring Services</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['recurring'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Due In 7 Days</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['due_soon'] }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('services.index') }}" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <input type="hidden" name="tab" value="{{ $filters['tab'] }}">
        <div class="grid gap-3 xl:grid-cols-[minmax(260px,1.6fr)_repeat(5,minmax(170px,1fr))_auto]">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search services..." class="h-11 w-full rounded-xl border border-gray-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <select name="status" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <option value="all">Status: All</option>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option }}" @selected($filters['status'] === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="category" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <option value="all">Category: All</option>
                @foreach ($categories as $option)
                    <option value="{{ $option }}" @selected($filters['category'] === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="assigned_unit" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <option value="all">Assigned Unit: All</option>
                @foreach ($assignedUnitOptions as $option)
                    <option value="{{ $option }}" @selected($filters['assigned_unit'] === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="frequency" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <option value="all">Frequency: All</option>
                @foreach (['One-time', 'Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually', 'Custom'] as $option)
                    <option value="{{ $option }}" @selected($filters['frequency'] === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="engagement_type" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <option value="all">Engagement: All</option>
                <option value="Project Engagement" @selected($filters['engagement_type'] === 'Project Engagement')>Project Engagement</option>
                <option value="Regular (Retainer)" @selected($filters['engagement_type'] === 'Regular (Retainer)')>Regular (Retainer)</option>
                <option value="Hybrid" @selected($filters['engagement_type'] === 'Hybrid')>Hybrid Engagement</option>
            </select>
            <button class="h-11 rounded-xl border border-gray-200 bg-gray-900 px-5 text-sm font-medium text-white hover:bg-gray-800">Apply</button>
        </div>
    </form>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $quickTabs = [
                            'all' => 'All Services',
                            'active' => 'Active',
                            'recurring' => 'Recurring',
                            'due_soon' => 'Due in 7 Days',
                        ];
                    @endphp
                    @foreach ($quickTabs as $tabKey => $tabLabel)
                        <a
                            href="{{ route('services.index', array_merge(request()->query(), ['tab' => $tabKey])) }}"
                            class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $filters['tab'] === $tabKey ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}"
                        >
                            {{ $tabLabel }}
                        </a>
                    @endforeach
                </div>
                <button id="openCreateFieldDropdown" type="button" class="self-start text-sm font-medium text-blue-600 hover:text-blue-700 lg:self-auto">+ Create Field</button>
            </div>
        </div>
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
                        @foreach ($customFields as $field)
                            <th class="px-3 py-3 text-left font-medium">{{ $field->field_name }}</th>
                        @endforeach
                        <th class="px-3 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($services as $service)
                        <tr class="text-gray-700 hover:bg-gray-50">
                            <td class="px-3 py-3">
                                <a href="{{ route('services.show', $service->id) }}" class="font-medium text-gray-900 hover:text-blue-700">{{ $service->service_name }}</a>
                                <div class="mt-1 text-xs text-gray-500">ID {{ $service->service_id }} @if($service->company)<span class="mx-1">|</span>{{ $service->company->company_name }}@endif</div>
                            </td>
                            <td class="px-3 py-3 text-gray-600">{{ $service->category ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ $service->frequency ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ implode(', ', $service->engagement_structure ?? []) ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600">
                                @if ($service->rate_per_unit)
                                    {{ number_format((float) $service->rate_per_unit, 2) }} / {{ $service->unit }}
                                @elseif ($service->price_fee)
                                    {{ number_format((float) $service->price_fee, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-3 text-gray-600">{{ $service->assigned_unit ?: '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClasses[$service->status] ?? 'border-gray-200 bg-gray-50 text-gray-700' }}">{{ $service->status }}</span>
                            </td>
                            <td class="px-3 py-3 text-gray-600">{{ $service->creator?->name ?: '-' }}</td>
                            @foreach ($customFields as $field)
                                <td class="px-3 py-3 text-gray-600">{{ data_get($service->custom_field_values, $field->field_key, '-') ?: '-' }}</td>
                            @endforeach
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" data-global-service-edit='@json($service)'>Edit</button>
                                    <form method="POST" action="{{ route('services.destroy', $service->id) }}" onsubmit="return confirm('Delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-red-200 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 9 + $customFields->count() }}" class="px-3 py-16 text-center text-sm text-gray-500">No services found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-5 py-4">
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-700">
                <span>Total Services: {{ $services->total() }}</span>
                <div class="ml-auto flex items-center gap-2 text-xs text-gray-600">
                    <form method="GET" action="{{ route('services.index') }}" class="flex items-center gap-2">
                        <span>Records per page</span>
                        <select name="per_page" class="h-9 rounded-lg border border-gray-200 px-3 text-xs" onchange="this.form.submit()">
                            @foreach ([5, 10, 25, 50] as $size)
                                <option value="{{ $size }}" {{ (int) $filters['per_page'] === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="search" value="{{ $filters['search'] }}">
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                        <input type="hidden" name="category" value="{{ $filters['category'] }}">
                        <input type="hidden" name="assigned_unit" value="{{ $filters['assigned_unit'] }}">
                        <input type="hidden" name="frequency" value="{{ $filters['frequency'] }}">
                        <input type="hidden" name="engagement_type" value="{{ $filters['engagement_type'] }}">
                        <input type="hidden" name="tab" value="{{ $filters['tab'] }}">
                    </form>
                    <span>{{ $services->firstItem() ?? 0 }} to {{ $services->lastItem() ?? 0 }} | Page {{ $services->currentPage() }} of {{ $services->lastPage() }}</span>
                </div>
            </div>
            <div class="mt-4">
                {{ $services->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
</div>

@include('services.partials.service-form-modal', [
    'fieldPrefix' => 'globalService',
    'modalId' => 'globalServiceModal',
    'title' => 'Add Service',
    'subtitle' => 'Create a configurable service entry with routing, requirements, and pricing.',
    'action' => route('services.store'),
    'companyLocked' => false,
])
@include('products.partials.create-field-dropdown', ['fieldTypes' => $fieldTypes])
@include('products.partials.create-field-modal', [
    'createFieldActionRoute' => route('services.custom-fields.store'),
    'lookupModules' => $lookupModules,
])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('globalServiceModal');
    const form = document.getElementById('globalServiceForm');
    const openButton = document.getElementById('openGlobalServiceModalCreate');
    const closeButtons = modal.querySelectorAll('[data-close-service-modal]');
    const editButtons = document.querySelectorAll('[data-global-service-edit]');
    const methodInput = document.getElementById('globalServiceFormMethod');
    const title = document.getElementById('globalServiceModalTitle');
    const submit = document.getElementById('globalServiceFormSubmit');
    const updateUrlTemplate = @json(route('services.update', '__SERVICE__'));
    const createUrl = @json(route('services.store'));

    const createFieldDropdownButton = document.getElementById('openCreateFieldDropdown');
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

    const openModal = () => window.jkncSlideOver.open(modal);
    const closeModal = () => window.jkncSlideOver.close(modal);

    const formatDateTimeLocal = (value) => {
        if (!value) return '';
        return String(value).replace(' ', 'T').slice(0, 16);
    };

    const resetForm = () => {
        form.reset();
        form.action = createUrl;
        methodInput.value = 'POST';
        title.textContent = 'Add Service';
        submit.textContent = 'Save';
        document.getElementById('globalServiceFormStatus').value = 'Draft';
    };

    const setMultiSelect = (id, values) => {
        const select = document.getElementById(id);
        const selected = Array.isArray(values) ? values : [];
        Array.from(select.options).forEach((option) => {
            option.selected = selected.includes(option.value);
        });
        select.dispatchEvent(new Event('change'));
    };

    const fillForm = (service) => {
        document.getElementById('globalServiceFormCompany').value = service.company_id ?? '';
        document.getElementById('globalServiceFormServiceName').value = service.service_name ?? '';
        document.getElementById('globalServiceFormServiceDescription').value = service.service_description ?? '';
        document.getElementById('globalServiceFormServiceOutput').value = service.service_activity_output ?? '';
        setMultiSelect('globalServiceFormServiceArea', service.service_area ?? []);
        document.getElementById('globalServiceFormServiceAreaOther').value = service.service_area_other ?? '';
        document.getElementById('globalServiceFormCategory').value = service.category ?? '';
        document.getElementById('globalServiceFormFrequency').value = service.frequency ?? '';
        document.getElementById('globalServiceFormFrequency').dispatchEvent(new Event('change'));
        document.getElementById('globalServiceFormScheduleRule').value = service.schedule_rule ?? '';
        document.getElementById('globalServiceFormDeadline').value = formatDateTimeLocal(service.deadline ?? '');
        document.getElementById('globalServiceFormReminder').value = service.reminder_lead_time ?? '';
        document.getElementById('globalServiceFormRequirementCategory').value = service.requirement_category ?? service.requirements?.category ?? '';
        document.getElementById('globalServiceFormRequirements').value = Array.isArray(service.requirements?.items) ? service.requirements.items.join('\n') : '';
        setMultiSelect('globalServiceFormEngagement', service.engagement_structure ?? []);
        document.getElementById('globalServiceFormUnit').value = service.unit ?? '';
        document.getElementById('globalServiceFormRatePerUnit').value = service.rate_per_unit ?? '';
        document.getElementById('globalServiceFormMinUnits').value = service.min_units ?? '';
        document.getElementById('globalServiceFormMaxCap').value = service.max_cap ?? '';
        document.getElementById('globalServiceFormPriceFee').value = service.price_fee ?? '';
        document.getElementById('globalServiceFormCost').value = service.cost_of_service ?? '';
        document.getElementById('globalServiceFormAssignedUnit').value = service.assigned_unit ?? '';
        document.getElementById('globalServiceFormStatus').value = service.status ?? 'Draft';
        document.getElementById('globalServiceFormReviewedBy').value = service.reviewed_by ?? '';
        document.getElementById('globalServiceFormReviewedAt').value = formatDateTimeLocal(service.reviewed_at ?? '');
        document.getElementById('globalServiceFormApprovedBy').value = service.approved_by ?? '';
        document.getElementById('globalServiceFormApprovedAt').value = formatDateTimeLocal(service.approved_at ?? '');
        document.getElementById('globalServiceFormCreatedByLabel').value = service.creator?.name ?? '-';
        document.getElementById('globalServiceFormUpdatedAtLabel').value = service.updated_at ?? '-';
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
            const service = JSON.parse(this.dataset.globalServiceEdit);
            resetForm();
            form.action = updateUrlTemplate.replace('__SERVICE__', service.id);
            methodInput.value = 'PUT';
            title.textContent = 'Edit Service';
            submit.textContent = 'Update';
            fillForm(service);
            openModal();
        });
    });

    createFieldDropdownButton?.addEventListener('click', function () {
        createFieldDropdownMenu.classList.toggle('hidden');
    });

    fieldTypeButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const value = this.dataset.fieldType;
            const label = this.dataset.fieldTypeLabel;
            createFieldTypeInput.value = value;
            createFieldTypeLabel.textContent = label;
            picklistOptionsSection.classList.toggle('hidden', value !== 'picklist');
            defaultValueSection.classList.toggle('hidden', false);
            lookupSection.classList.toggle('hidden', value !== 'lookup');
            createFieldDropdownMenu.classList.add('hidden');
            createFieldModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                createFieldModalOverlay.classList.remove('opacity-0');
                createFieldPanel.classList.remove('translate-x-full');
            });
        });
    });

    const closeCreateField = () => {
        createFieldModalOverlay.classList.add('opacity-0');
        createFieldPanel.classList.add('translate-x-full');
        setTimeout(() => createFieldModal.classList.add('hidden'), 200);
    };

    closeCreateFieldModal?.addEventListener('click', closeCreateField);
    cancelCreateFieldModal?.addEventListener('click', closeCreateField);
    createFieldModalOverlay?.addEventListener('click', closeCreateField);

    addPicklistOption?.addEventListener('click', function () {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center gap-2';
        wrapper.innerHTML = '<input name="options[]" placeholder="Option value" class="h-10 flex-1 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><button type="button" class="remove-picklist-option h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50"><i class="fas fa-minus text-xs"></i></button>';
        picklistOptionsContainer.appendChild(wrapper);
    });

    picklistOptionsContainer?.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-picklist-option');
        if (!removeButton) return;
        if (picklistOptionsContainer.children.length === 1) {
            const input = picklistOptionsContainer.querySelector('input');
            if (input) input.value = '';
            return;
        }
        removeButton.parentElement.remove();
    });

    document.addEventListener('click', function (event) {
        if (!createFieldDropdownButton?.contains(event.target) && !createFieldDropdownMenu?.contains(event.target)) {
            createFieldDropdownMenu?.classList.add('hidden');
        }
    });
});
</script>
@endsection
