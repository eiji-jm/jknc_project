@extends('layouts.app')

@section('content')
<div
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
    x-data="organizationalModule()"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Organizational</h1>
            </div>

            <button
                type="button"
                @click="openAddForm()"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0 hover:bg-blue-700 transition"
            >
                + Add
            </button>
        </div>

        {{-- SUBMODULE TABS --}}
        <div class="px-4 py-3 border-b bg-white shrink-0 overflow-x-auto">
            <div class="inline-flex min-w-max rounded-md border border-gray-200 overflow-hidden">
                <template x-for="tab in tabs" :key="tab.key">
                    <button
                        type="button"
                        @click="changeTab(tab.key)"
                        class="px-4 py-2 text-sm border-r border-gray-200 last:border-r-0 transition"
                        :class="activeTab === tab.key
                            ? 'bg-blue-50 text-blue-700 font-medium'
                            : 'bg-white text-gray-600 hover:bg-gray-50'"
                        x-text="tab.label"
                    ></button>
                </template>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <template x-for="column in currentColumns" :key="column.key">
                                <th
                                    class="p-3 text-left font-medium"
                                    :class="column.width ?? ''"
                                    x-text="column.label"
                                ></th>
                            </template>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <template x-if="currentRows.length === 0">
                            <tr class="border-t">
                                <td
                                    class="p-10 text-center text-gray-400 italic"
                                    :colspan="currentColumns.length"
                                >
                                    No data found
                                </td>
                            </tr>
                        </template>

                        <template x-for="(row, index) in currentRows" :key="index">
                            <tr class="border-t hover:bg-gray-50">
                                <template x-for="column in currentColumns" :key="column.key">
                                    <td class="p-3 text-gray-900 align-top break-words" x-text="row[column.key] ?? ''"></td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SLIDE OVER --}}
        <div
            x-show="showSlideOver"
            x-transition.opacity
            class="fixed inset-0 z-50"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/40" @click="closeAddForm()"></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-[1100px] bg-white shadow-2xl flex h-full"
                    style="display: none;"
                >
                    {{-- LEFT PREVIEW / GUIDE --}}
                    <div class="flex-1 min-w-0 p-4 bg-gray-50 border-r border-gray-200">
                        <div class="h-full bg-white border border-gray-200 rounded-xl overflow-auto p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4" x-text="currentFormTitle"></h2>

                            <div class="space-y-4 text-sm text-gray-600">
                                <div>
                                    <p class="font-medium text-gray-800 mb-1">Purpose</p>
                                    <p x-text="currentFormDescription"></p>
                                </div>

                                <div>
                                    <p class="font-medium text-gray-800 mb-1">Expected Flow</p>
                                    <div class="space-y-2">
                                        <template x-if="activeTab === 'address'">
                                            <p>Save the company’s main business address.</p>
                                        </template>

                                        <template x-if="activeTab === 'office'">
                                            <p>Create offices under the business structure.</p>
                                        </template>

                                        <template x-if="activeTab === 'branch'">
                                            <p>Create branches under a selected office.</p>
                                        </template>

                                        <template x-if="activeTab === 'department'">
                                            <p>Create departments under a selected office and branch.</p>
                                        </template>

                                        <template x-if="activeTab === 'division'">
                                            <p>Create divisions under a selected department.</p>
                                        </template>

                                        <template x-if="activeTab === 'unit'">
                                            <p>Create units under a selected division.</p>
                                        </template>

                                        <template x-if="activeTab === 'position'">
                                            <p>Create position names for the organizational structure.</p>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <p class="font-medium text-gray-800 mb-1">Notes</p>
                                    <ul class="space-y-1 text-gray-600">
                                        <li>• This is the base UI only.</li>
                                        <li>• Backend saving can be connected later.</li>
                                        <li>• Hierarchy should go Office → Branch → Department → Division → Unit.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT FORM --}}
                    <div class="w-full max-w-sm bg-white flex flex-col h-full">
                        <div class="p-6 border-b flex items-center justify-between shrink-0">
                            <h2 class="font-bold text-lg text-gray-900" x-text="currentFormTitle"></h2>
                            <button
                                type="button"
                                @click="closeAddForm()"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Close
                            </button>
                        </div>

                        <div class="p-6 space-y-4 flex-1 overflow-y-auto min-h-0">
                            {{-- ADDRESS --}}
                            <template x-if="activeTab === 'address'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Business Address</label>
                                        <textarea x-model="form.business_address" rows="5" class="w-full border rounded-md p-2" placeholder="Enter business address"></textarea>
                                    </div>
                                </div>
                            </template>

                            {{-- OFFICE --}}
                            <template x-if="activeTab === 'office'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office Name</label>
                                        <input x-model="form.office_name" class="w-full border rounded-md p-2" placeholder="Office name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office Address</label>
                                        <textarea x-model="form.office_address" rows="4" class="w-full border rounded-md p-2" placeholder="Office address"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Head of Office</label>
                                        <input x-model="form.office_head" class="w-full border rounded-md p-2" placeholder="Head of office">
                                    </div>
                                </div>
                            </template>

                            {{-- BRANCH --}}
                            <template x-if="activeTab === 'branch'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office</label>
                                        <select x-model="form.office_name" class="w-full border rounded-md p-2">
                                            <option value="">Select office</option>
                                            <template x-for="office in officeOptions" :key="office">
                                                <option :value="office" x-text="office"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch Name</label>
                                        <input x-model="form.branch_name" class="w-full border rounded-md p-2" placeholder="Branch name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch Address</label>
                                        <textarea x-model="form.branch_address" rows="4" class="w-full border rounded-md p-2" placeholder="Branch address"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch Head</label>
                                        <input x-model="form.branch_head" class="w-full border rounded-md p-2" placeholder="Branch head">
                                    </div>
                                </div>
                            </template>

                            {{-- DEPARTMENT --}}
                            <template x-if="activeTab === 'department'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office</label>
                                        <select x-model="form.office_name" class="w-full border rounded-md p-2">
                                            <option value="">Select office</option>
                                            <template x-for="office in officeOptions" :key="office">
                                                <option :value="office" x-text="office"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch</label>
                                        <select x-model="form.branch_name" class="w-full border rounded-md p-2">
                                            <option value="">Select branch</option>
                                            <template x-for="branch in branchOptions" :key="branch">
                                                <option :value="branch" x-text="branch"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Department Name</label>
                                        <input x-model="form.department_name" class="w-full border rounded-md p-2" placeholder="Department name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Department Address</label>
                                        <textarea x-model="form.department_address" rows="4" class="w-full border rounded-md p-2" placeholder="Department address"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Department Head</label>
                                        <input x-model="form.department_head" class="w-full border rounded-md p-2" placeholder="Department head">
                                    </div>
                                </div>
                            </template>

                            {{-- DIVISION --}}
                            <template x-if="activeTab === 'division'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office</label>
                                        <select x-model="form.office_name" class="w-full border rounded-md p-2">
                                            <option value="">Select office</option>
                                            <template x-for="office in officeOptions" :key="office">
                                                <option :value="office" x-text="office"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch</label>
                                        <select x-model="form.branch_name" class="w-full border rounded-md p-2">
                                            <option value="">Select branch</option>
                                            <template x-for="branch in branchOptions" :key="branch">
                                                <option :value="branch" x-text="branch"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Department</label>
                                        <select x-model="form.department_name" class="w-full border rounded-md p-2">
                                            <option value="">Select department</option>
                                            <template x-for="department in departmentOptions" :key="department">
                                                <option :value="department" x-text="department"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Division Name</label>
                                        <input x-model="form.division_name" class="w-full border rounded-md p-2" placeholder="Division name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Division Address</label>
                                        <textarea x-model="form.division_address" rows="4" class="w-full border rounded-md p-2" placeholder="Division address"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Division Head</label>
                                        <input x-model="form.division_head" class="w-full border rounded-md p-2" placeholder="Division head">
                                    </div>
                                </div>
                            </template>

                            {{-- UNIT --}}
                            <template x-if="activeTab === 'unit'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Office</label>
                                        <select x-model="form.office_name" class="w-full border rounded-md p-2">
                                            <option value="">Select office</option>
                                            <template x-for="office in officeOptions" :key="office">
                                                <option :value="office" x-text="office"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Branch</label>
                                        <select x-model="form.branch_name" class="w-full border rounded-md p-2">
                                            <option value="">Select branch</option>
                                            <template x-for="branch in branchOptions" :key="branch">
                                                <option :value="branch" x-text="branch"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Department</label>
                                        <select x-model="form.department_name" class="w-full border rounded-md p-2">
                                            <option value="">Select department</option>
                                            <template x-for="department in departmentOptions" :key="department">
                                                <option :value="department" x-text="department"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Division</label>
                                        <select x-model="form.division_name" class="w-full border rounded-md p-2">
                                            <option value="">Select division</option>
                                            <template x-for="division in divisionOptions" :key="division">
                                                <option :value="division" x-text="division"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Unit Name</label>
                                        <input x-model="form.unit_name" class="w-full border rounded-md p-2" placeholder="Unit name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Unit Address</label>
                                        <textarea x-model="form.unit_address" rows="4" class="w-full border rounded-md p-2" placeholder="Unit address"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Unit Head</label>
                                        <input x-model="form.unit_head" class="w-full border rounded-md p-2" placeholder="Unit head">
                                    </div>
                                </div>
                            </template>

                            {{-- POSITION --}}
                            <template x-if="activeTab === 'position'">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Position Name</label>
                                        <input x-model="form.position_name" class="w-full border rounded-md p-2" placeholder="Position name">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="p-6 border-t flex gap-2 shrink-0">
                            <button
                                type="button"
                                @click="closeAddForm()"
                                class="flex-1 border rounded py-2"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                @click="saveEntry()"
                                class="flex-1 bg-blue-600 text-white rounded py-2 hover:bg-blue-700 transition"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function organizationalModule() {
    return {
        showSlideOver: false,
        activeTab: 'address',

        tabs: [
            { key: 'address', label: 'Address' },
            { key: 'office', label: 'Office' },
            { key: 'branch', label: 'Branch' },
            { key: 'department', label: 'Department' },
            { key: 'division', label: 'Division' },
            { key: 'unit', label: 'Unit' },
            { key: 'position', label: 'Position' },
        ],

        rows: {
            address: [],
            office: [],
            branch: [],
            department: [],
            division: [],
            unit: [],
            position: [],
        },

        form: {
            business_address: '',

            office_name: '',
            office_address: '',
            office_head: '',

            branch_name: '',
            branch_address: '',
            branch_head: '',

            department_name: '',
            department_address: '',
            department_head: '',

            division_name: '',
            division_address: '',
            division_head: '',

            unit_name: '',
            unit_address: '',
            unit_head: '',

            position_name: '',
        },

        get officeOptions() {
            return this.rows.office.map(item => item.office_name);
        },

        get branchOptions() {
            return this.rows.branch.map(item => item.branch_name);
        },

        get departmentOptions() {
            return this.rows.department.map(item => item.department_name);
        },

        get divisionOptions() {
            return this.rows.division.map(item => item.division_name);
        },

        get currentColumns() {
            const map = {
                address: [
                    { key: 'business_address', label: 'Business Address' },
                ],
                office: [
                    { key: 'office_name', label: 'Office Name', width: 'w-56' },
                    { key: 'office_address', label: 'Office Address' },
                    { key: 'office_head', label: 'Head of Office', width: 'w-56' },
                ],
                branch: [
                    { key: 'office_name', label: 'Office', width: 'w-44' },
                    { key: 'branch_name', label: 'Branch Name', width: 'w-44' },
                    { key: 'branch_address', label: 'Branch Address' },
                    { key: 'branch_head', label: 'Branch Head', width: 'w-44' },
                ],
                department: [
                    { key: 'office_name', label: 'Office', width: 'w-36' },
                    { key: 'branch_name', label: 'Branch', width: 'w-36' },
                    { key: 'department_name', label: 'Department Name', width: 'w-48' },
                    { key: 'department_address', label: 'Department Address' },
                    { key: 'department_head', label: 'Department Head', width: 'w-44' },
                ],
                division: [
                    { key: 'office_name', label: 'Office', width: 'w-32' },
                    { key: 'branch_name', label: 'Branch', width: 'w-32' },
                    { key: 'department_name', label: 'Department', width: 'w-40' },
                    { key: 'division_name', label: 'Division Name', width: 'w-40' },
                    { key: 'division_address', label: 'Division Address' },
                    { key: 'division_head', label: 'Division Head', width: 'w-40' },
                ],
                unit: [
                    { key: 'office_name', label: 'Office', width: 'w-28' },
                    { key: 'branch_name', label: 'Branch', width: 'w-28' },
                    { key: 'department_name', label: 'Department', width: 'w-36' },
                    { key: 'division_name', label: 'Division', width: 'w-36' },
                    { key: 'unit_name', label: 'Unit Name', width: 'w-36' },
                    { key: 'unit_address', label: 'Unit Address' },
                    { key: 'unit_head', label: 'Unit Head', width: 'w-36' },
                ],
                position: [
                    { key: 'position_name', label: 'Position Name' },
                ],
            };

            return map[this.activeTab] || [];
        },

        get currentRows() {
            return this.rows[this.activeTab] || [];
        },

        get currentFormTitle() {
            const titles = {
                address: 'Add Business Address',
                office: 'Add Office',
                branch: 'Add Branch',
                department: 'Add Department',
                division: 'Add Division',
                unit: 'Add Unit',
                position: 'Add Position',
            };

            return titles[this.activeTab] || 'Add Entry';
        },

        get currentFormDescription() {
            const descriptions = {
                address: 'Save the business address for the company.',
                office: 'Create an office record with its address and head of office.',
                branch: 'Create a branch under a selected office.',
                department: 'Create a department under the selected office and branch.',
                division: 'Create a division under the selected department.',
                unit: 'Create a unit under the selected division.',
                position: 'Create a position name for the organizational structure.',
            };

            return descriptions[this.activeTab] || '';
        },

        changeTab(tab) {
            this.activeTab = tab;
        },

        openAddForm() {
            this.resetForm();
            this.showSlideOver = true;
        },

        closeAddForm() {
            this.showSlideOver = false;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                business_address: '',

                office_name: '',
                office_address: '',
                office_head: '',

                branch_name: '',
                branch_address: '',
                branch_head: '',

                department_name: '',
                department_address: '',
                department_head: '',

                division_name: '',
                division_address: '',
                division_head: '',

                unit_name: '',
                unit_address: '',
                unit_head: '',

                position_name: '',
            };
        },

        saveEntry() {
            const payloadMap = {
                address: () => {
                    if (!this.form.business_address.trim()) {
                        alert('Business Address is required.');
                        return null;
                    }

                    return {
                        business_address: this.form.business_address.trim(),
                    };
                },

                office: () => {
                    if (!this.form.office_name.trim() || !this.form.office_address.trim() || !this.form.office_head.trim()) {
                        alert('Please fill in all Office fields.');
                        return null;
                    }

                    return {
                        office_name: this.form.office_name.trim(),
                        office_address: this.form.office_address.trim(),
                        office_head: this.form.office_head.trim(),
                    };
                },

                branch: () => {
                    if (!this.form.office_name || !this.form.branch_name.trim() || !this.form.branch_address.trim() || !this.form.branch_head.trim()) {
                        alert('Please fill in all Branch fields.');
                        return null;
                    }

                    return {
                        office_name: this.form.office_name,
                        branch_name: this.form.branch_name.trim(),
                        branch_address: this.form.branch_address.trim(),
                        branch_head: this.form.branch_head.trim(),
                    };
                },

                department: () => {
                    if (!this.form.office_name || !this.form.branch_name || !this.form.department_name.trim() || !this.form.department_address.trim() || !this.form.department_head.trim()) {
                        alert('Please fill in all Department fields.');
                        return null;
                    }

                    return {
                        office_name: this.form.office_name,
                        branch_name: this.form.branch_name,
                        department_name: this.form.department_name.trim(),
                        department_address: this.form.department_address.trim(),
                        department_head: this.form.department_head.trim(),
                    };
                },

                division: () => {
                    if (!this.form.office_name || !this.form.branch_name || !this.form.department_name || !this.form.division_name.trim() || !this.form.division_address.trim() || !this.form.division_head.trim()) {
                        alert('Please fill in all Division fields.');
                        return null;
                    }

                    return {
                        office_name: this.form.office_name,
                        branch_name: this.form.branch_name,
                        department_name: this.form.department_name,
                        division_name: this.form.division_name.trim(),
                        division_address: this.form.division_address.trim(),
                        division_head: this.form.division_head.trim(),
                    };
                },

                unit: () => {
                    if (!this.form.office_name || !this.form.branch_name || !this.form.department_name || !this.form.division_name || !this.form.unit_name.trim() || !this.form.unit_address.trim() || !this.form.unit_head.trim()) {
                        alert('Please fill in all Unit fields.');
                        return null;
                    }

                    return {
                        office_name: this.form.office_name,
                        branch_name: this.form.branch_name,
                        department_name: this.form.department_name,
                        division_name: this.form.division_name,
                        unit_name: this.form.unit_name.trim(),
                        unit_address: this.form.unit_address.trim(),
                        unit_head: this.form.unit_head.trim(),
                    };
                },

                position: () => {
                    if (!this.form.position_name.trim()) {
                        alert('Position Name is required.');
                        return null;
                    }

                    return {
                        position_name: this.form.position_name.trim(),
                    };
                },
            };

            const payload = payloadMap[this.activeTab] ? payloadMap[this.activeTab]() : null;

            if (!payload) return;

            this.rows[this.activeTab].push(payload);
            this.closeAddForm();
        },
    };
}
</script>
@endpush
@endsection