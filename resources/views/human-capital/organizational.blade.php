@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Organizational</h1>
            </div>

            <button type="button" onclick="openAddSection()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0 hover:bg-blue-700 transition">
                + Add
            </button>
        </div>

        {{-- TABS --}}
        <div class="px-4 py-3 border-b bg-white shrink-0 overflow-x-auto">
            <div class="inline-flex min-w-max rounded-md border border-gray-200 overflow-hidden">
                <button type="button" class="org-tab-btn px-4 py-2 text-sm border-r border-gray-200 bg-blue-50 text-blue-700 font-medium" data-tab="address" onclick="changeTab('address', this)">
                    Address
                </button>
                <button type="button" class="org-tab-btn px-4 py-2 text-sm border-r border-gray-200 bg-white text-gray-600 hover:bg-gray-50" data-tab="office" onclick="changeTab('office', this)">
                    Office
                </button>
                <button type="button" class="org-tab-btn px-4 py-2 text-sm border-r border-gray-200 bg-white text-gray-600 hover:bg-gray-50" data-tab="branch" onclick="changeTab('branch', this)">
                    Branch
                </button>
                <button type="button" class="org-tab-btn px-4 py-2 text-sm border-r border-gray-200 bg-white text-gray-600 hover:bg-gray-50" data-tab="department" onclick="changeTab('department', this)">
                    Department
                </button>
                <button type="button" class="org-tab-btn px-4 py-2 text-sm border-r border-gray-200 bg-white text-gray-600 hover:bg-gray-50" data-tab="division" onclick="changeTab('division', this)">
                    Division
                </button>
                <button type="button" class="org-tab-btn px-4 py-2 text-sm bg-white text-gray-600 hover:bg-gray-50" data-tab="unit" onclick="changeTab('unit', this)">
                    Unit
                </button>
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr id="tableHeadRow"></tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

        {{-- ADD SLIDE OVER --}}
        <div id="addSection" class="hidden fixed inset-0 z-50" aria-hidden="true">
            <div id="addBackdrop" class="absolute inset-0 bg-black/40" onclick="closeAddSection()"></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    id="addPanel"
                    class="w-[35%] min-w-[380px] max-w-[650px] bg-white shadow-2xl flex flex-col h-full transform translate-x-full transition-transform duration-300 ease-in-out"
                >
                    <div class="p-6 border-b flex items-center justify-between shrink-0">
                        <h2 id="formTitle" class="font-bold text-lg text-gray-900">Add Business Address</h2>
                        <button type="button" onclick="closeAddSection()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
                    </div>

                    <div class="px-6 pt-4">
                        <div id="formError" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                    </div>

                    <div class="p-6 space-y-4 flex-1 overflow-y-auto min-h-0">
                        {{-- ADDRESS --}}
                        <div id="form-address" class="org-form-section">
                            <div>
                                <label class="block text-sm font-medium mb-1">Business Address</label>
                                <textarea id="business_address" rows="5" class="w-full border rounded-md p-2" placeholder="Enter business address"></textarea>
                            </div>
                        </div>

                        {{-- OFFICE --}}
                        <div id="form-office" class="org-form-section hidden">
                            <div>
                                <label class="block text-sm font-medium mb-1">Office Name</label>
                                <input id="office_name" class="w-full border rounded-md p-2" placeholder="Office name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Office Address</label>
                                <textarea id="office_address" rows="4" class="w-full border rounded-md p-2" placeholder="Office address"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Head of Office</label>
                                <input id="office_head" class="w-full border rounded-md p-2" placeholder="Head of office">
                            </div>
                        </div>

                        {{-- BRANCH --}}
                        <div id="form-branch" class="org-form-section hidden">
                            <div>
                                <label class="block text-sm font-medium mb-1">Office</label>
                                <select id="branch_office_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch Name</label>
                                <input id="branch_name" class="w-full border rounded-md p-2" placeholder="Branch name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch Address</label>
                                <textarea id="branch_address" rows="4" class="w-full border rounded-md p-2" placeholder="Branch address"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch Head</label>
                                <input id="branch_head" class="w-full border rounded-md p-2" placeholder="Branch head">
                            </div>
                        </div>

                        {{-- DEPARTMENT --}}
                        <div id="form-department" class="org-form-section hidden">
                            <div>
                                <label class="block text-sm font-medium mb-1">Office</label>
                                <select id="department_office_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch</label>
                                <select id="department_branch_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department Name</label>
                                <input id="department_name" class="w-full border rounded-md p-2" placeholder="Department name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department Address</label>
                                <textarea id="department_address" rows="4" class="w-full border rounded-md p-2" placeholder="Department address"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department Head</label>
                                <input id="department_head" class="w-full border rounded-md p-2" placeholder="Department head">
                            </div>
                        </div>

                        {{-- DIVISION --}}
                        <div id="form-division" class="org-form-section hidden">
                            <div>
                                <label class="block text-sm font-medium mb-1">Office</label>
                                <select id="division_office_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch</label>
                                <select id="division_branch_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department</label>
                                <select id="division_department_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Division Name</label>
                                <input id="division_name" class="w-full border rounded-md p-2" placeholder="Division name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Division Address</label>
                                <textarea id="division_address" rows="4" class="w-full border rounded-md p-2" placeholder="Division address"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Division Head</label>
                                <input id="division_head" class="w-full border rounded-md p-2" placeholder="Division head">
                            </div>
                        </div>

                        {{-- UNIT --}}
                        <div id="form-unit" class="org-form-section hidden">
                            <div>
                                <label class="block text-sm font-medium mb-1">Office</label>
                                <select id="unit_office_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Branch</label>
                                <select id="unit_branch_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department</label>
                                <select id="unit_department_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Division</label>
                                <select id="unit_division_id" class="w-full border rounded-md p-2"></select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Unit Name</label>
                                <input id="unit_name" class="w-full border rounded-md p-2" placeholder="Unit name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Unit Address</label>
                                <textarea id="unit_address" rows="4" class="w-full border rounded-md p-2" placeholder="Unit address"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Unit Head</label>
                                <input id="unit_head" class="w-full border rounded-md p-2" placeholder="Unit head">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t flex gap-2 shrink-0">
                        <button type="button" onclick="closeAddSection()" class="flex-1 border rounded py-2">Cancel</button>
                        <button type="button" id="saveButton" onclick="saveOrganizationalEntry()" class="flex-1 bg-blue-600 text-white rounded py-2 hover:bg-blue-700 transition">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
let activeTab = 'address';

let organizationalRows = {
    address: @json($addresses),
    office: @json($offices),
    branch: @json($branches),
    department: @json($departments),
    division: @json($divisions),
    unit: @json($units),
};

let officeOptions = @json($officeOptions);
let branchOptions = @json($branchOptions);
let departmentOptions = @json($departmentOptions);
let divisionOptions = @json($divisionOptions);

const storeUrl = @json(route('human-capital.organizational.store'));
const csrfToken = @json(csrf_token());

const tableColumns = {
    address: [
        { key: 'business_address', label: 'Business Address', width: '' },
    ],
    office: [
        { key: 'office_name', label: 'Office Name', width: 'w-56' },
        { key: 'office_address', label: 'Office Address', width: '' },
        { key: 'office_head', label: 'Head of Office', width: 'w-56' },
    ],
    branch: [
        { key: 'office_name', label: 'Office', width: 'w-44' },
        { key: 'branch_name', label: 'Branch Name', width: 'w-44' },
        { key: 'branch_address', label: 'Branch Address', width: '' },
        { key: 'branch_head', label: 'Branch Head', width: 'w-44' },
    ],
    department: [
        { key: 'office_name', label: 'Office', width: 'w-36' },
        { key: 'branch_name', label: 'Branch', width: 'w-36' },
        { key: 'department_name', label: 'Department Name', width: 'w-48' },
        { key: 'department_address', label: 'Department Address', width: '' },
        { key: 'department_head', label: 'Department Head', width: 'w-44' },
    ],
    division: [
        { key: 'office_name', label: 'Office', width: 'w-32' },
        { key: 'branch_name', label: 'Branch', width: 'w-32' },
        { key: 'department_name', label: 'Department', width: 'w-40' },
        { key: 'division_name', label: 'Division Name', width: 'w-40' },
        { key: 'division_address', label: 'Division Address', width: '' },
        { key: 'division_head', label: 'Division Head', width: 'w-40' },
    ],
    unit: [
        { key: 'office_name', label: 'Office', width: 'w-28' },
        { key: 'branch_name', label: 'Branch', width: 'w-28' },
        { key: 'department_name', label: 'Department', width: 'w-36' },
        { key: 'division_name', label: 'Division', width: 'w-36' },
        { key: 'unit_name', label: 'Unit Name', width: 'w-36' },
        { key: 'unit_address', label: 'Unit Address', width: '' },
        { key: 'unit_head', label: 'Unit Head', width: 'w-36' },
    ],
};

const formTitles = {
    address: 'Add Business Address',
    office: 'Add Office',
    branch: 'Add Branch',
    department: 'Add Department',
    division: 'Add Division',
    unit: 'Add Unit',
};

function openAddSection() {
    resetFormDefaults();
    populateAllSelects();

    document.getElementById('formTitle').textContent = formTitles[activeTab] || 'Add Entry';
    showFormSection(activeTab);

    const addSection = document.getElementById('addSection');
    const addPanel = document.getElementById('addPanel');

    addSection.classList.remove('hidden');

    requestAnimationFrame(() => {
        addPanel.classList.remove('translate-x-full');
    });
}

function closeAddSection() {
    resetFormDefaults();

    const addSection = document.getElementById('addSection');
    const addPanel = document.getElementById('addPanel');

    addPanel.classList.add('translate-x-full');

    setTimeout(() => {
        addSection.classList.add('hidden');
    }, 300);
}

function changeTab(tab, button) {
    activeTab = tab;
    updateTabStyles(button);
    drawTableRows();
    document.getElementById('formTitle').textContent = formTitles[activeTab] || 'Add Entry';
    showFormSection(activeTab);
    hideFormError();
}

function updateTabStyles(activeButton) {
    document.querySelectorAll('.org-tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-50', 'text-blue-700', 'font-medium');
        btn.classList.add('bg-white', 'text-gray-600');
    });

    if (activeButton) {
        activeButton.classList.remove('bg-white', 'text-gray-600');
        activeButton.classList.add('bg-blue-50', 'text-blue-700', 'font-medium');
    } else {
        const btn = document.querySelector(`.org-tab-btn[data-tab="${activeTab}"]`);
        if (btn) {
            btn.classList.remove('bg-white', 'text-gray-600');
            btn.classList.add('bg-blue-50', 'text-blue-700', 'font-medium');
        }
    }
}

function showFormSection(tab) {
    document.querySelectorAll('.org-form-section').forEach(section => {
        section.classList.add('hidden');
    });

    const activeSection = document.getElementById(`form-${tab}`);
    if (activeSection) {
        activeSection.classList.remove('hidden');
    }
}

function drawTableHead() {
    const head = document.getElementById('tableHeadRow');
    const columns = tableColumns[activeTab] || [];

    head.innerHTML = columns.map(column => {
        return `<th class="p-3 text-left font-medium ${column.width || ''}">${column.label}</th>`;
    }).join('');
}

function drawTableRows() {
    drawTableHead();

    const tableBody = document.getElementById('tableBody');
    const rows = organizationalRows[activeTab] || [];
    const columns = tableColumns[activeTab] || [];

    tableBody.innerHTML = '';

    if (!rows.length) {
        tableBody.innerHTML = `<tr class="border-t"><td colspan="${columns.length}" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    rows.forEach(row => {
        const tr = document.createElement('tr');
        tr.className = 'border-t hover:bg-gray-50';

        tr.innerHTML = columns.map(column => {
            return `<td class="p-3 text-gray-900 align-top break-words">${row[column.key] ?? ''}</td>`;
        }).join('');

        tableBody.appendChild(tr);
    });
}

function populateSelect(selectId, items, placeholder, labelKey) {
    const select = document.getElementById(selectId);
    if (!select) return;

    let html = `<option value="">${placeholder}</option>`;

    items.forEach(item => {
        html += `<option value="${item.id}">${item[labelKey] ?? ''}</option>`;
    });

    select.innerHTML = html;
}

function populateAllSelects() {
    populateSelect('branch_office_id', officeOptions, 'Select office', 'office_name');

    populateSelect('department_office_id', officeOptions, 'Select office', 'office_name');
    populateSelect('department_branch_id', branchOptions, 'Select branch', 'branch_name');

    populateSelect('division_office_id', officeOptions, 'Select office', 'office_name');
    populateSelect('division_branch_id', branchOptions, 'Select branch', 'branch_name');
    populateSelect('division_department_id', departmentOptions, 'Select department', 'department_name');

    populateSelect('unit_office_id', officeOptions, 'Select office', 'office_name');
    populateSelect('unit_branch_id', branchOptions, 'Select branch', 'branch_name');
    populateSelect('unit_department_id', departmentOptions, 'Select department', 'department_name');
    populateSelect('unit_division_id', divisionOptions, 'Select division', 'division_name');
}

function resetFormDefaults() {
    hideFormError();

    const fields = [
        'business_address',
        'office_name',
        'office_address',
        'office_head',
        'branch_office_id',
        'branch_name',
        'branch_address',
        'branch_head',
        'department_office_id',
        'department_branch_id',
        'department_name',
        'department_address',
        'department_head',
        'division_office_id',
        'division_branch_id',
        'division_department_id',
        'division_name',
        'division_address',
        'division_head',
        'unit_office_id',
        'unit_branch_id',
        'unit_department_id',
        'unit_division_id',
        'unit_name',
        'unit_address',
        'unit_head',
    ];

    fields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.value = '';
        }
    });
}

function showFormError(message) {
    const errorBox = document.getElementById('formError');
    errorBox.textContent = message;
    errorBox.classList.remove('hidden');
}

function hideFormError() {
    const errorBox = document.getElementById('formError');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');
}

function getPayload() {
    switch (activeTab) {
        case 'address':
            return {
                type: 'address',
                business_address: document.getElementById('business_address').value.trim(),
            };

        case 'office':
            return {
                type: 'office',
                office_name: document.getElementById('office_name').value.trim(),
                office_address: document.getElementById('office_address').value.trim(),
                office_head: document.getElementById('office_head').value.trim(),
            };

        case 'branch':
            return {
                type: 'branch',
                office_id: document.getElementById('branch_office_id').value,
                branch_name: document.getElementById('branch_name').value.trim(),
                branch_address: document.getElementById('branch_address').value.trim(),
                branch_head: document.getElementById('branch_head').value.trim(),
            };

        case 'department':
            return {
                type: 'department',
                office_id: document.getElementById('department_office_id').value,
                branch_id: document.getElementById('department_branch_id').value,
                department_name: document.getElementById('department_name').value.trim(),
                department_address: document.getElementById('department_address').value.trim(),
                department_head: document.getElementById('department_head').value.trim(),
            };

        case 'division':
            return {
                type: 'division',
                office_id: document.getElementById('division_office_id').value,
                branch_id: document.getElementById('division_branch_id').value,
                department_id: document.getElementById('division_department_id').value,
                division_name: document.getElementById('division_name').value.trim(),
                division_address: document.getElementById('division_address').value.trim(),
                division_head: document.getElementById('division_head').value.trim(),
            };

        case 'unit':
            return {
                type: 'unit',
                office_id: document.getElementById('unit_office_id').value,
                branch_id: document.getElementById('unit_branch_id').value,
                department_id: document.getElementById('unit_department_id').value,
                division_id: document.getElementById('unit_division_id').value,
                unit_name: document.getElementById('unit_name').value.trim(),
                unit_address: document.getElementById('unit_address').value.trim(),
                unit_head: document.getElementById('unit_head').value.trim(),
            };

        default:
            return {};
    }
}

function refreshOptions(recordType, record) {
    if (recordType === 'office') {
        officeOptions.unshift({
            id: record.id,
            office_name: record.office_name,
        });
    }

    if (recordType === 'branch') {
        branchOptions.unshift({
            id: record.id,
            branch_name: record.branch_name,
        });
    }

    if (recordType === 'department') {
        departmentOptions.unshift({
            id: record.id,
            department_name: record.department_name,
        });
    }

    if (recordType === 'division') {
        divisionOptions.unshift({
            id: record.id,
            division_name: record.division_name,
        });
    }
}

async function saveOrganizationalEntry() {
    hideFormError();

    const saveButton = document.getElementById('saveButton');
    const originalText = saveButton.textContent;

    saveButton.disabled = true;
    saveButton.textContent = 'Saving...';

    try {
        const response = await fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(getPayload()),
        });

        const contentType = response.headers.get('content-type') || '';
        let data = {};

        if (contentType.includes('application/json')) {
            data = await response.json();
        } else {
            throw new Error('Server did not return JSON.');
        }

        if (!response.ok) {
            if (data.errors) {
                const firstErrorGroup = Object.values(data.errors)[0];
                showFormError(Array.isArray(firstErrorGroup) ? firstErrorGroup[0] : 'Validation error.');
            } else {
                showFormError(data.message || 'Unable to save record.');
            }
            return false;
        }

        organizationalRows[activeTab].unshift(data.record);
        refreshOptions(activeTab, data.record);
        drawTableRows();
        closeAddSection();
        return true;
    } catch (error) {
        console.error(error);
        showFormError('Something went wrong while saving.');
        return false;
    } finally {
        saveButton.disabled = false;
        saveButton.textContent = originalText;
    }
}

populateAllSelects();
updateTabStyles();
showFormSection(activeTab);
drawTableRows();
</script>
@endsection