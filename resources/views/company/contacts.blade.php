@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900">CONTACTS</h2>
                            <p class="mt-1 text-sm text-gray-500">Manage contact records for {{ $company->company_name }}.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                id="openCustomFieldModal"
                                class="h-9 rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2"
                            >
                                <i class="fas fa-table-columns text-xs"></i>
                                <span>Custom Field</span>
                            </button>
                            <button
                                type="button"
                                id="openContactCreateModal"
                                class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2"
                            >
                                <span class="text-base leading-none">+</span>
                                <span>Add Contact</span>
                            </button>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <form method="GET" action="{{ route('company.contacts', $company->id) }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            <div class="relative w-full sm:w-[320px]">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search contacts..."
                                    class="w-full h-10 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                                >
                            </div>

                            <button class="h-10 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Search
                            </button>

                            @if ($search !== '')
                                <a href="{{ route('company.contacts', $company->id) }}" class="h-10 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                                    Clear
                                </a>
                            @endif
                        </form>

                        <div class="text-sm text-gray-500">
                            {{ $contacts->count() }} {{ \Illuminate\Support\Str::plural('contact', $contacts->count()) }}
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Contact Name</th>
                                        <th class="px-4 py-3 text-left font-medium">Email</th>
                                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                                        <th class="px-4 py-3 text-left font-medium">Mobile</th>
                                        <th class="px-4 py-3 text-left font-medium">Owner</th>
                                        @foreach ($customFields as $field)
                                            <th class="px-4 py-3 text-left font-medium">{{ $field['label'] }}</th>
                                        @endforeach
                                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    @forelse ($contacts as $contact)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <span class="h-9 w-9 rounded-full bg-gray-100 border border-gray-200 text-[11px] font-semibold text-gray-600 inline-flex items-center justify-center">
                                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($contact['full_name'], 0, 1) . \Illuminate\Support\Str::substr(\Illuminate\Support\Str::after($contact['full_name'], ' '), 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <div class="font-medium text-gray-800">{{ $contact['full_name'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">{{ $contact['email'] ?: '-' }}</td>
                                            <td class="px-4 py-3">{{ $contact['phone'] ?: '-' }}</td>
                                            <td class="px-4 py-3">{{ $contact['mobile'] ?: '-' }}</td>
                                            <td class="px-4 py-3">{{ $contact['owner_name'] ?: '-' }}</td>
                                            @foreach ($customFields as $field)
                                                <td class="px-4 py-3">{{ $contact['custom_fields'][$field['key']] ?? '-' }}</td>
                                            @endforeach
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        class="inline-flex h-8 px-3 rounded-full border border-gray-200 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                                        data-contact-view
                                                        data-contact='@json($contact)'
                                                    >
                                                        View
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex h-8 px-3 rounded-full border border-gray-200 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                                        data-contact-edit
                                                        data-contact='@json($contact)'
                                                    >
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="{{ route('company.contacts.destroy', [$company->id, $contact['id']]) }}" onsubmit="return confirm('Delete this contact?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex h-8 px-3 rounded-full border border-red-200 text-xs font-medium text-red-600 hover:bg-red-50">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 6 + count($customFields) }}" class="px-4 py-12">
                                                <div class="flex flex-col items-center justify-center text-center">
                                                    <div class="h-12 w-12 rounded-full bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                                                        <i class="fas fa-address-book"></i>
                                                    </div>
                                                    <h3 class="mt-4 text-base font-semibold text-gray-900">No contacts added for this company yet.</h3>
                                                    <p class="mt-1 max-w-md text-sm text-gray-500">Create the first contact for {{ $company->company_name }} and keep all communication details in one place.</p>
                                                    <button
                                                        type="button"
                                                        id="openFirstContactModal"
                                                        class="mt-4 h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2"
                                                    >
                                                        <span class="text-base leading-none">+</span>
                                                        <span>Add Contact</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div id="contactModal" class="fixed inset-0 z-[60] hidden bg-black/35 p-4 sm:p-6">
    <div class="mx-auto mt-6 w-full max-w-3xl rounded-xl border border-gray-200 bg-white overflow-hidden" id="contact-form">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="contactModalTitle" class="text-lg font-semibold text-gray-900">Add Contact</h2>
                    <p class="mt-1 text-sm text-gray-500">This contact will be saved under {{ $company->company_name }}.</p>
                </div>
                <button type="button" data-close-contact-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <form id="contactForm" method="POST" action="{{ route('company.contacts.store', $company->id) }}" class="max-h-[75vh] overflow-y-auto px-4 py-4">
            @csrf
            <input type="hidden" id="contactFormMethod" name="_method" value="POST">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="full_name" class="mb-1 block text-sm font-medium text-gray-700">Contact Name <span class="text-red-500">*</span></label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="owner_name" class="mb-1 block text-sm font-medium text-gray-700">Contact Owner</label>
                    <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name', $company->owner_name ?? 'Owner 1') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('owner_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile" class="mb-1 block text-sm font-medium text-gray-700">Mobile</label>
                    <input id="mobile" name="mobile" type="text" value="{{ old('mobile') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    @error('mobile')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @foreach ($customFields as $field)
                    <div class="{{ $loop->last && count($customFields) % 2 === 1 ? 'md:col-span-2' : '' }}">
                        <label for="custom_field_{{ $field['key'] }}" class="mb-1 block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                        <input
                            id="custom_field_{{ $field['key'] }}"
                            name="custom_fields[{{ $field['key'] }}]"
                            type="text"
                            value="{{ old('custom_fields.' . $field['key']) }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('custom_fields.' . $field['key'])
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" data-close-contact-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="contactFormSubmit" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<div id="contactViewModal" class="fixed inset-0 z-[60] hidden bg-black/35 p-4 sm:p-6">
    <div class="mx-auto mt-10 w-full max-w-2xl rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Contact Details</h2>
                    <p class="mt-1 text-sm text-gray-500">Contact information linked to {{ $company->company_name }}.</p>
                </div>
                <button type="button" data-close-view-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 py-4">
            <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div><span class="font-medium text-gray-700">Contact Name:</span> <span id="view_full_name" class="text-gray-600">-</span></div>
                <div><span class="font-medium text-gray-700">Contact Owner:</span> <span id="view_owner_name" class="text-gray-600">-</span></div>
                <div><span class="font-medium text-gray-700">Email:</span> <span id="view_email" class="text-gray-600">-</span></div>
                <div><span class="font-medium text-gray-700">Phone:</span> <span id="view_phone" class="text-gray-600">-</span></div>
                <div><span class="font-medium text-gray-700">Mobile:</span> <span id="view_mobile" class="text-gray-600">-</span></div>
                @foreach ($customFields as $field)
                    <div><span class="font-medium text-gray-700">{{ $field['label'] }}:</span> <span id="view_custom_{{ $field['key'] }}" class="text-gray-600">-</span></div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                <button type="button" data-close-view-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="customFieldModal" class="fixed inset-0 z-[60] hidden bg-black/35 p-4 sm:p-6">
    <div class="mx-auto mt-16 w-full max-w-lg rounded-xl border border-gray-200 bg-white overflow-hidden" id="custom-field-form">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Add Custom Field</h2>
                    <p class="mt-1 text-sm text-gray-500">This column will be available for all contacts under {{ $company->company_name }}.</p>
                </div>
                <button type="button" data-close-custom-field-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('company.contacts.custom-fields.store', $company->id) }}" class="px-4 py-4">
            @csrf
            <div>
                <label for="label" class="mb-1 block text-sm font-medium text-gray-700">Column Name</label>
                <input id="label" name="label" type="text" value="{{ old('label') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                @error('label')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" data-close-custom-field-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Add Column
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const contactModal = document.getElementById('contactModal');
        const contactViewModal = document.getElementById('contactViewModal');
        const customFieldModal = document.getElementById('customFieldModal');
        const contactForm = document.getElementById('contactForm');
        const contactFormMethod = document.getElementById('contactFormMethod');
        const contactModalTitle = document.getElementById('contactModalTitle');
        const contactFormSubmit = document.getElementById('contactFormSubmit');
        const openCustomFieldModalButton = document.getElementById('openCustomFieldModal');
        const createButtons = [document.getElementById('openContactCreateModal'), document.getElementById('openFirstContactModal')].filter(Boolean);
        const editButtons = document.querySelectorAll('[data-contact-edit]');
        const viewButtons = document.querySelectorAll('[data-contact-view]');
        const closeContactButtons = document.querySelectorAll('[data-close-contact-modal]');
        const closeViewButtons = document.querySelectorAll('[data-close-view-modal]');
        const closeCustomFieldButtons = document.querySelectorAll('[data-close-custom-field-modal]');
        const formFields = ['first_name', 'last_name', 'full_name', 'email', 'owner_name', 'phone', 'mobile'];
        const customFields = @json($customFields);
        const baseStoreAction = @json(route('company.contacts.store', $company->id));
        const updateActionTemplate = @json(route('company.contacts.update', [$company->id, '__CONTACT__']));
        const oldMode = @json(old('_contact_form'));
        const oldContactId = @json(old('contact_id'));
        const oldCustomFieldMode = @json(old('_custom_field_form'));
        const oldValues = {
            first_name: @json(old('first_name')),
            last_name: @json(old('last_name')),
            full_name: @json(old('full_name')),
            email: @json(old('email')),
            owner_name: @json(old('owner_name', $company->owner_name ?? 'Owner 1')),
            phone: @json(old('phone')),
            mobile: @json(old('mobile')),
        };
        const oldCustomFieldValues = @json(old('custom_fields', []));

        const setBodyLocked = (locked) => {
            document.body.classList.toggle('overflow-hidden', locked);
        };

        const openModal = (modal) => {
            modal.classList.remove('hidden');
            setBodyLocked(true);
        };

        const closeModal = (modal) => {
            modal.classList.add('hidden');
            if (contactModal.classList.contains('hidden') && contactViewModal.classList.contains('hidden') && customFieldModal.classList.contains('hidden')) {
                setBodyLocked(false);
            }
        };

        const resetContactForm = () => {
            contactForm.reset();
            contactForm.action = baseStoreAction;
            contactFormMethod.value = 'POST';
            contactModalTitle.textContent = 'Add Contact';
            contactFormSubmit.textContent = 'Save';
            document.getElementById('owner_name').value = @json($company->owner_name ?? 'Owner 1');
            customFields.forEach((field) => {
                const input = document.getElementById(`custom_field_${field.key}`);
                if (input) {
                    input.value = '';
                }
            });
        };

        const fillContactForm = (contact) => {
            formFields.forEach((field) => {
                const input = document.getElementById(field);
                if (input) {
                    input.value = contact[field] ?? '';
                }
            });

            customFields.forEach((field) => {
                const input = document.getElementById(`custom_field_${field.key}`);
                if (input) {
                    input.value = contact.custom_fields?.[field.key] ?? '';
                }
            });
        };

        const openCreateModal = () => {
            resetContactForm();
            openModal(contactModal);
        };

        const openEditModal = (contact) => {
            resetContactForm();
            contactForm.action = updateActionTemplate.replace('__CONTACT__', contact.id);
            contactFormMethod.value = 'PUT';
            contactModalTitle.textContent = 'Edit Contact';
            contactFormSubmit.textContent = 'Update';
            fillContactForm(contact);
            openModal(contactModal);
        };

        const openViewModal = (contact) => {
            ['full_name', 'owner_name', 'email', 'phone', 'mobile'].forEach((field) => {
                const target = document.getElementById(`view_${field}`);
                if (target) {
                    target.textContent = contact[field] || '-';
                }
            });

            customFields.forEach((field) => {
                const target = document.getElementById(`view_custom_${field.key}`);
                if (target) {
                    target.textContent = contact.custom_fields?.[field.key] || '-';
                }
            });

            openModal(contactViewModal);
        };

        if (openCustomFieldModalButton) {
            openCustomFieldModalButton.addEventListener('click', function () {
                openModal(customFieldModal);
            });
        }

        createButtons.forEach((button) => {
            button.addEventListener('click', openCreateModal);
        });

        editButtons.forEach((button) => {
            button.addEventListener('click', function () {
                openEditModal(JSON.parse(this.dataset.contact));
            });
        });

        viewButtons.forEach((button) => {
            button.addEventListener('click', function () {
                openViewModal(JSON.parse(this.dataset.contact));
            });
        });

        closeContactButtons.forEach((button) => {
            button.addEventListener('click', function () {
                closeModal(contactModal);
            });
        });

        closeViewButtons.forEach((button) => {
            button.addEventListener('click', function () {
                closeModal(contactViewModal);
            });
        });

        closeCustomFieldButtons.forEach((button) => {
            button.addEventListener('click', function () {
                closeModal(customFieldModal);
            });
        });

        [contactModal, contactViewModal, customFieldModal].forEach((modal) => {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                if (!contactModal.classList.contains('hidden')) {
                    closeModal(contactModal);
                }

                if (!contactViewModal.classList.contains('hidden')) {
                    closeModal(contactViewModal);
                }

                if (!customFieldModal.classList.contains('hidden')) {
                    closeModal(customFieldModal);
                }
            }
        });

        if (oldMode === 'create') {
            resetContactForm();
            fillContactForm(oldValues);
            customFields.forEach((field) => {
                const input = document.getElementById(`custom_field_${field.key}`);
                if (input) {
                    input.value = oldCustomFieldValues[field.key] ?? '';
                }
            });
            openModal(contactModal);
        }

        if (oldMode === 'edit') {
            const matchingEditButton = Array.from(editButtons).find((button) => {
                try {
                    return String(JSON.parse(button.dataset.contact).id) === String(oldContactId);
                } catch (error) {
                    return false;
                }
            });

            if (matchingEditButton) {
                openEditModal(JSON.parse(matchingEditButton.dataset.contact));
                fillContactForm(oldValues);
                customFields.forEach((field) => {
                    const input = document.getElementById(`custom_field_${field.key}`);
                    if (input) {
                        input.value = oldCustomFieldValues[field.key] ?? '';
                    }
                });
            }
        }

        if (oldCustomFieldMode === 'create') {
            openModal(customFieldModal);
        }
    });
</script>
@endsection
