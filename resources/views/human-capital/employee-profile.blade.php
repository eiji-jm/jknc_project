@extends('layouts.app')

@section('content')
<div
    x-data="employeePage({
        employees: @js($employees),
        officeOptions: @js($officeOptions),
        branchOptions: @js($branchOptions),
        departmentOptions: @js($departmentOptions),
        divisionOptions: @js($divisionOptions),
        unitOptions: @js($unitOptions),
        storeUrl: '{{ route('human-capital.employee-profile.store') }}',
        updateBaseUrl: '{{ url('/human-capital/employee-profile') }}'
    })"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Employee Profile</h1>
            </div>

            <button
                type="button"
                @click="openAdd()"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0 hover:bg-blue-700 transition"
            >
                + Add Employee
            </button>
        </div>

        {{-- TABLE --}}
        <div class="flex-1 min-h-0 overflow-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Employee ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Full Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Office</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Department</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Position</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Payroll Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Basic Salary</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Hourly Rate</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-if="employees.length === 0">
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-gray-400">
                                No employees found.
                            </td>
                        </tr>
                    </template>

                    <template x-for="employee in employees" :key="employee.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3" x-text="employee.employee_code"></td>
                            <td class="px-4 py-3" x-text="employee.full_name"></td>
                            <td class="px-4 py-3" x-text="employee.email"></td>
                            <td class="px-4 py-3" x-text="employee.office_name ?? '-'"></td>
                            <td class="px-4 py-3" x-text="employee.department_name ?? '-'"></td>
                            <td class="px-4 py-3" x-text="employee.position ?? '-'"></td>
                            <td class="px-4 py-3" x-text="employee.payroll_type"></td>
                            <td class="px-4 py-3" x-text="formatMoney(employee.basic_salary)"></td>
                            <td class="px-4 py-3" x-text="formatMoney(employee.hourly_rate)"></td>
                            <td class="px-4 py-3 text-right">
                                <button
                                    type="button"
                                    @click="openEdit(employee)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- SLIDER --}}
    <div
        x-show="showSlider"
        x-transition.opacity
        class="fixed inset-0 z-50"
        style="display: none;"
    >
        <div class="absolute inset-0 bg-black/40" @click="closeSlider()"></div>

        <div
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 h-full w-full max-w-[540px] bg-white shadow-xl flex flex-col"
        >
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900" x-text="isEdit ? 'Edit Employee' : 'Add Employee'"></h2>
                <button type="button" @click="closeSlider()" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
            </div>

            <form method="POST" :action="formAction" class="flex-1 overflow-auto px-5 py-5 space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" x-model="form.first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" x-model="form.last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="age" x-model="form.age" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone_number" x-model="form.phone_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" x-model="form.address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="form.email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="pt-2">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Organizational Assignment</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                    <select name="office_id" x-model="form.office_id" @change="onOfficeChange()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Office</option>
                        <template x-for="office in officeOptions" :key="office.id">
                            <option :value="office.id" x-text="office.office_name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select name="branch_id" x-model="form.branch_id" @change="onBranchChange()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Branch</option>
                        <template x-for="branch in filteredBranches" :key="branch.id">
                            <option :value="branch.id" x-text="branch.branch_name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department_id" x-model="form.department_id" @change="onDepartmentChange()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Department</option>
                        <template x-for="department in filteredDepartments" :key="department.id">
                            <option :value="department.id" x-text="department.department_name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <select name="division_id" x-model="form.division_id" @change="onDivisionChange()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Division</option>
                        <template x-for="division in filteredDivisions" :key="division.id">
                            <option :value="division.id" x-text="division.division_name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="unit_id" x-model="form.unit_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Unit</option>
                        <template x-for="unit in filteredUnits" :key="unit.id">
                            <option :value="unit.id" x-text="unit.unit_name"></option>
                        </template>
                    </select>
                </div>

                <div class="pt-2">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Compensation Details</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" x-model="form.position" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payroll Type</label>
                    <select name="payroll_type" x-model="form.payroll_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Select Payroll Type</option>
                        <option value="Monthly Paid">Monthly Paid</option>
                        <option value="Daily Paid">Daily Paid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" x-model="form.basic_salary" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate</label>
                    <input type="text" :value="hourlyRate" readonly class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-700">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2.5 text-sm font-medium">
                        Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function employeePage(config) {
    return {
        employees: config.employees ?? [],
        officeOptions: config.officeOptions ?? [],
        branchOptions: config.branchOptions ?? [],
        departmentOptions: config.departmentOptions ?? [],
        divisionOptions: config.divisionOptions ?? [],
        unitOptions: config.unitOptions ?? [],
        storeUrl: config.storeUrl,
        updateBaseUrl: config.updateBaseUrl,

        showSlider: false,
        isEdit: false,
        formAction: config.storeUrl,

        form: {
            id: null,
            first_name: '',
            last_name: '',
            age: '',
            address: '',
            phone_number: '',
            email: '',
            office_id: '',
            branch_id: '',
            department_id: '',
            division_id: '',
            unit_id: '',
            position: '',
            payroll_type: '',
            basic_salary: ''
        },

        get filteredBranches() {
            if (!this.form.office_id) return [];
            return this.branchOptions.filter(item => String(item.office_id) === String(this.form.office_id));
        },

        get filteredDepartments() {
            if (!this.form.office_id || !this.form.branch_id) return [];
            return this.departmentOptions.filter(item =>
                String(item.office_id) === String(this.form.office_id) &&
                String(item.branch_id) === String(this.form.branch_id)
            );
        },

        get filteredDivisions() {
            if (!this.form.office_id || !this.form.branch_id || !this.form.department_id) return [];
            return this.divisionOptions.filter(item =>
                String(item.office_id) === String(this.form.office_id) &&
                String(item.branch_id) === String(this.form.branch_id) &&
                String(item.department_id) === String(this.form.department_id)
            );
        },

        get filteredUnits() {
            if (!this.form.office_id || !this.form.branch_id || !this.form.department_id || !this.form.division_id) return [];
            return this.unitOptions.filter(item =>
                String(item.office_id) === String(this.form.office_id) &&
                String(item.branch_id) === String(this.form.branch_id) &&
                String(item.department_id) === String(this.form.department_id) &&
                String(item.division_id) === String(this.form.division_id)
            );
        },

        get hourlyRate() {
            const salary = parseFloat(this.form.basic_salary || 0);

            if (!salary || !this.form.payroll_type) return '0.00';

            if (this.form.payroll_type === 'Monthly Paid') {
                return (salary / 22 / 8).toFixed(2);
            }

            return (salary / 8).toFixed(2);
        },

        openAdd() {
            this.isEdit = false;
            this.formAction = this.storeUrl;
            this.resetForm();
            this.showSlider = true;
        },

        openEdit(employee) {
            this.isEdit = true;
            this.formAction = `${this.updateBaseUrl}/${employee.id}`;

            this.form = {
                id: employee.id,
                first_name: employee.first_name ?? '',
                last_name: employee.last_name ?? '',
                age: employee.age ?? '',
                address: employee.address ?? '',
                phone_number: employee.phone_number ?? '',
                email: employee.email ?? '',
                office_id: employee.office_id ?? '',
                branch_id: employee.branch_id ?? '',
                department_id: employee.department_id ?? '',
                division_id: employee.division_id ?? '',
                unit_id: employee.unit_id ?? '',
                position: employee.position ?? '',
                payroll_type: employee.payroll_type ?? '',
                basic_salary: employee.basic_salary ?? ''
            };

            this.showSlider = true;
        },

        closeSlider() {
            this.showSlider = false;
        },

        resetForm() {
            this.form = {
                id: null,
                first_name: '',
                last_name: '',
                age: '',
                address: '',
                phone_number: '',
                email: '',
                office_id: '',
                branch_id: '',
                department_id: '',
                division_id: '',
                unit_id: '',
                position: '',
                payroll_type: '',
                basic_salary: ''
            };
        },

        onOfficeChange() {
            this.form.branch_id = '';
            this.form.department_id = '';
            this.form.division_id = '';
            this.form.unit_id = '';
        },

        onBranchChange() {
            this.form.department_id = '';
            this.form.division_id = '';
            this.form.unit_id = '';
        },

        onDepartmentChange() {
            this.form.division_id = '';
            this.form.unit_id = '';
        },

        onDivisionChange() {
            this.form.unit_id = '';
        },

        formatMoney(value) {
            const number = parseFloat(value || 0);
            return '₱' + number.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endsection