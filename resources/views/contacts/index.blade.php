@extends('layouts.app')

@section('content')
@php
    $statusPillClasses = [
        'Verified' => 'bg-green-100 text-green-700 border border-green-200',
        'Pending Verification' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Not Submitted' => 'bg-gray-100 text-gray-600 border border-gray-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];

    $selectedOwner = collect($owners)->firstWhere('id', (int) $defaultOwnerId) ?: collect($owners)->first();
    $selectedOwnerId = $selectedOwner['id'] ?? null;
    $selectedOwnerName = $selectedOwner['name'] ?? 'Select Owner';
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mb-5">
        <h1 class="text-3xl font-semibold text-gray-900">Contacts</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your client contacts and deal relationships</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('contacts.index') }}" class="mb-4 flex flex-wrap items-center gap-3">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search Contacts..."
                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <select
            name="kyc"
            class="h-10 min-w-[130px] rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            onchange="this.form.submit()"
        >
            <option value="All" {{ $kycFilter === 'All' ? 'selected' : '' }}>All</option>
            @foreach ($kycStatuses as $status)
                <option value="{{ $status }}" {{ $kycFilter === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>

        <input type="hidden" name="per_page" value="{{ $perPage }}">

        <button
            type="button"
            id="openCreateContactModal"
            class="ml-auto h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"
        >
            + Add Contact
        </button>
    </form>

    <div id="selectionActionBar" class="mb-3 hidden rounded-lg border border-blue-100 bg-blue-50 px-3 py-2">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-medium text-gray-800"><span id="selectedCount">0</span> selected</span>
            <button type="button" class="h-8 rounded-md border border-gray-200 bg-white px-3 hover:bg-gray-50">Assign Owner</button>
            <select class="h-8 rounded-md border border-gray-200 bg-white px-2">
                <option>Mark KYC Status</option>
                <option>Verified</option>
                <option>Pending Verification</option>
                <option>Rejected</option>
            </select>
            <button type="button" id="clearSelection" class="ml-auto text-gray-700 hover:underline">Clear</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="w-10 px-3 py-3 text-left"><input id="selectAll" type="checkbox" class="h-4 w-4 rounded border-gray-300"></th>
                        <th class="px-3 py-3 text-left">Contact Name</th>
                        <th class="px-3 py-3 text-left">Company Name</th>
                        <th class="px-3 py-3 text-left">Email</th>
                        <th class="px-3 py-3 text-left">Phone</th>
                        <th class="px-3 py-3 text-left">KYC Status</th>
                        <th class="px-3 py-3 text-left">Contact Owner</th>
                        <th class="px-3 py-3 text-left">Last Activity</th>
                        @foreach ($customFields as $field)
                            <th class="px-3 py-3 text-left">{{ $field['name'] }}</th>
                        @endforeach
                        <th class="px-3 py-3 text-right normal-case">
                            <button id="openCreateFieldDropdown" type="button" class="text-sm font-medium text-blue-600 hover:text-blue-700">+ Create Field</button>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($contacts as $contact)
                        @php
                            $initials = strtoupper(mb_substr($contact->first_name ?? '', 0, 1).mb_substr($contact->last_name ?? '', 0, 1));
                        @endphp
                        <tr class="text-gray-700">
                            <td class="px-3 py-3"><input type="checkbox" class="row-checkbox h-4 w-4 rounded border-gray-300"></td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">{{ $initials }}</div>
                                    <a href="{{ route('contacts.show', $contact) }}" class="font-medium text-gray-900 hover:text-blue-700">
                                        {{ trim($contact->first_name.' '.$contact->last_name) }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-3 py-3">{{ $contact->company_name ?: '-' }}</td>
                            <td class="px-3 py-3">{{ $contact->email ?: '-' }}</td>
                            <td class="px-3 py-3">{{ $contact->phone ?: '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClasses[$contact->kyc_status] ?? $statusPillClasses['Not Submitted'] }}">
                                    {{ $contact->kyc_status }}
                                </span>
                            </td>
                            <td class="px-3 py-3">{{ $contact->owner_name ?: '-' }}</td>
                            <td class="px-3 py-3">{{ $contact->last_activity_at?->diffForHumans() ?? 'No activity' }}</td>
                            @foreach ($customFields as $field)
                                @php
                                    $customValue = $field['default_value'] ?? '';
                                @endphp
                                <td class="px-3 py-3 text-gray-600">
                                    @if (($field['type'] ?? '') === 'checkbox')
                                        {{ $customValue === '1' ? 'Yes' : 'No' }}
                                    @elseif (($field['type'] ?? '') === 'currency' && $customValue !== '')
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
                            <td colspan="{{ 9 + count($customFields) }}" class="px-3 py-10 text-center text-sm text-gray-500">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-700">
        <span>Total Contacts: {{ $contacts->total() }}</span>
        <span>Verified: <span class="text-green-600">{{ $statusCounts['Verified'] }}</span></span>
        <span>Pending: <span class="text-amber-600">{{ $statusCounts['Pending Verification'] }}</span></span>
        <span>Not Submitted: {{ $statusCounts['Not Submitted'] }}</span>
        <span>Rejected: <span class="text-red-600">{{ $statusCounts['Rejected'] }}</span></span>
        <div class="ml-auto flex items-center gap-2 text-xs text-gray-600">
            <form method="GET" action="{{ route('contacts.index') }}" class="flex items-center gap-2">
                <span>Rows</span>
                <select name="per_page" class="h-7 rounded border border-gray-200 px-2 text-xs" onchange="this.form.submit()">
                    @foreach ([5, 10, 25, 50] as $size)
                        <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="kyc" value="{{ $kycFilter }}">
            </form>
            <span>{{ $contacts->firstItem() ?? 0 }}-{{ $contacts->lastItem() ?? 0 }} of {{ $contacts->total() }}</span>
        </div>
    </div>
</div>

@include('contacts.partials.create-modal', [
    'owners' => $owners,
    'selectedOwnerId' => $selectedOwnerId,
    'selectedOwnerName' => $selectedOwnerName,
])
@include('products.partials.create-field-dropdown', [
    'fieldTypes' => $fieldTypes,
    'dropdownId' => 'createFieldDropdownMenu',
])
@include('products.partials.create-field-modal', [
    'createFieldActionRoute' => route('contacts.custom-fields.store'),
    'lookupModules' => $lookupModules,
])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('createContactModal');
    const openModalButton = document.getElementById('openCreateContactModal');
    const closeModalButton = document.getElementById('closeCreateContactModal');
    const cancelModalButton = document.getElementById('cancelCreateContactModal');
    const ownerTrigger = document.getElementById('ownerDropdownTrigger');
    const ownerMenu = document.getElementById('ownerDropdownMenu');
    const ownerSearch = document.getElementById('ownerSearch');
    const ownerInput = document.getElementById('owner_id');
    const ownerLabel = document.getElementById('ownerSelectedLabel');
    const ownerOptions = Array.from(document.querySelectorAll('.owner-option'));
    const contactPanel = document.getElementById('createContactPanel');
    const contactOverlay = document.getElementById('createContactModalOverlay');
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
    let createFieldDropdownOpen = false;

    const selectAll = document.getElementById('selectAll');
    const rowChecks = Array.from(document.querySelectorAll('.row-checkbox'));
    const actionBar = document.getElementById('selectionActionBar');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelection = document.getElementById('clearSelection');

    const openModal = () => {
        if (!modal || !contactPanel) {
            return;
        }
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            contactOverlay?.classList.remove('opacity-0');
            contactPanel.classList.remove('translate-x-full');
        });
    };

    const closeModal = () => {
        if (!modal || !contactPanel) {
            return;
        }
        ownerMenu?.classList.add('hidden');
        contactOverlay?.classList.add('opacity-0');
        contactPanel.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
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

    openModalButton?.addEventListener('click', openModal);
    closeModalButton?.addEventListener('click', closeModal);
    cancelModalButton?.addEventListener('click', closeModal);

    contactOverlay?.addEventListener('click', closeModal);

    ownerTrigger?.addEventListener('click', function () {
        ownerMenu.classList.toggle('hidden');
        if (!ownerMenu.classList.contains('hidden')) {
            ownerSearch?.focus();
        }
    });

    ownerSearch?.addEventListener('input', function () {
        const keyword = ownerSearch.value.toLowerCase().trim();
        ownerOptions.forEach((option) => {
            const value = (option.dataset.ownerName || '').toLowerCase();
            option.classList.toggle('hidden', keyword !== '' && !value.includes(keyword));
        });
    });

    ownerOptions.forEach((option) => {
        option.addEventListener('click', function () {
            ownerInput.value = option.dataset.ownerId;
            ownerLabel.textContent = `Owner: ${option.dataset.ownerName}`;
            ownerMenu.classList.add('hidden');
        });
    });

    document.addEventListener('click', function (event) {
        if (ownerMenu && !ownerMenu.classList.contains('hidden')) {
            if (!ownerMenu.contains(event.target) && !ownerTrigger.contains(event.target)) {
                ownerMenu.classList.add('hidden');
            }
        }

        if (createFieldDropdownMenu && createFieldDropdownOpen) {
            const clickedFieldTrigger = openCreateFieldDropdown ? openCreateFieldDropdown.contains(event.target) : false;
            if (!createFieldDropdownMenu.contains(event.target) && !clickedFieldTrigger) {
                closeCreateFieldDropdownFn();
            }
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

    @if ($errors->any())
        @if (old('field_type'))
            openCreateFieldModalFn('{{ old('field_type') }}', '{{ $fieldTypes->firstWhere('value', old('field_type'))['label'] ?? 'Picklist' }}');
        @else
            openModal();
        @endif
    @endif
});
</script>
@endsection
