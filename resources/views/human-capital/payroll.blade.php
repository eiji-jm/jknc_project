@extends('layouts.app')

@section('content')
<div
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
    x-data="payrollPage()"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-lg font-semibold text-gray-900">Payroll</h1>

                <form method="GET" action="{{ route('human-capital.payroll') }}" class="flex items-center gap-2 flex-wrap">
                    <select
                        name="department"
                        onchange="this.form.submit()"
                        class="h-9 rounded-lg border border-gray-300 text-sm px-3 bg-white text-gray-700"
                    >
                        <option value="All" {{ request('department', 'All') === 'All' ? 'selected' : '' }}>All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="h-9 rounded-lg border border-gray-300 text-sm px-3 bg-white text-gray-700"
                    >
                        <option value="All" {{ request('status', 'All') === 'All' ? 'selected' : '' }}>All Status</option>
                        <option value="Processed" {{ request('status') === 'Processed' ? 'selected' : '' }}>Processed</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Released" {{ request('status') === 'Released' ? 'selected' : '' }}>Released</option>
                    </select>
                </form>
            </div>

            <button
                type="button"
                @click="openCreate()"
                class="inline-flex items-center gap-2 rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
            >
                <i class="fas fa-plus text-xs"></i>
                Add Payroll
            </button>
        </div>

        {{-- TABLE --}}
        <div class="flex-1 min-h-0 overflow-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                    <tr class="text-gray-600">
                        <th class="px-4 py-3 font-medium">Pay Date</th>
                        <th class="px-4 py-3 font-medium">Employee</th>
                        <th class="px-4 py-3 font-medium">Employee ID</th>
                        <th class="px-4 py-3 font-medium">Department</th>
                        <th class="px-4 py-3 font-medium">Payroll Period</th>
                        <th class="px-4 py-3 font-medium">Basic Pay</th>
                        <th class="px-4 py-3 font-medium">Allowance</th>
                        <th class="px-4 py-3 font-medium">Deductions</th>
                        <th class="px-4 py-3 font-medium">Net Pay</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Prepared By</th>
                        <th class="px-4 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ optional($payroll->pay_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $payroll->employee_name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $payroll->employee_id ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $payroll->department ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $payroll->payroll_period }}</td>
                            <td class="px-4 py-3 text-gray-700">₱{{ number_format((float) $payroll->basic_pay, 2) }}</td>
                            <td class="px-4 py-3 text-gray-700">₱{{ number_format((float) $payroll->allowance, 2) }}</td>
                            <td class="px-4 py-3 text-gray-700">₱{{ number_format((float) $payroll->deductions, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">₱{{ number_format((float) $payroll->net_pay, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClasses = match($payroll->status) {
                                        'Processed' => 'bg-blue-50 text-blue-700',
                                        'Pending' => 'bg-yellow-50 text-yellow-700',
                                        'Released' => 'bg-green-50 text-green-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                                    {{ $payroll->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $payroll->prepared_by ?: '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        @click='openEdit({
                                            id: {{ $payroll->id }},
                                            employee_name: @js($payroll->employee_name),
                                            employee_id: @js($payroll->employee_id),
                                            department: @js($payroll->department),
                                            payroll_period: @js($payroll->payroll_period),
                                            pay_date: @js(optional($payroll->pay_date)->format("Y-m-d")),
                                            basic_pay: {{ (float) $payroll->basic_pay }},
                                            allowance: {{ (float) $payroll->allowance }},
                                            deductions: {{ (float) $payroll->deductions }},
                                            status: @js($payroll->status)
                                        })'
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('human-capital.payroll.destroy', $payroll) }}" onsubmit="return confirm('Delete this payroll record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-10 text-center text-sm text-gray-500">
                                No payroll records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ORGANIZATIONAL-STYLE SLIDER --}}
    <div
        x-show="showSlideOver"
        x-transition.opacity
        class="fixed inset-y-0 right-0 left-64 z-40"
        style="display: none;"
    >
        <div class="absolute inset-0 bg-black/30" @click="closeSlideOver()"></div>

        <div
            x-show="showSlideOver"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl flex flex-col"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b shrink-0">
                <h2 class="text-base font-semibold text-gray-900" x-text="isEdit ? 'Edit Payroll' : 'Add Payroll'"></h2>
                <button type="button" @click="closeSlideOver()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="formAction" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf

                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex-1 overflow-y-auto px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee Name</label>
                            <input
                                type="text"
                                name="employee_name"
                                x-model="form.employee_name"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <input
                                type="text"
                                name="employee_id"
                                x-model="form.employee_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <input
                                type="text"
                                name="department"
                                x-model="form.department"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payroll Period</label>
                            <input
                                type="text"
                                name="payroll_period"
                                x-model="form.payroll_period"
                                placeholder="March 16-31, 2026"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Date</label>
                            <input
                                type="date"
                                name="pay_date"
                                x-model="form.pay_date"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Pay</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="basic_pay"
                                x-model="form.basic_pay"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Allowance</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="allowance"
                                x-model="form.allowance"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deductions</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="deductions"
                                x-model="form.deductions"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                name="status"
                                x-model="form.status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                required
                            >
                                <option value="Processed">Processed</option>
                                <option value="Pending">Pending</option>
                                <option value="Released">Released</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Net Pay</label>
                            <input
                                type="text"
                                :value="formattedNetPay"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                                readonly
                            >
                        </div>
                    </div>
                </div>

                <div class="border-t px-6 py-4 flex items-center justify-end gap-3 shrink-0">
                    <button
                        type="button"
                        @click="closeSlideOver()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function payrollPage() {
        return {
            showSlideOver: false,
            isEdit: false,
            formAction: "{{ route('human-capital.payroll.store') }}",
            form: {
                id: null,
                employee_name: '',
                employee_id: '',
                department: '',
                payroll_period: '',
                pay_date: '',
                basic_pay: '',
                allowance: '',
                deductions: '',
                status: 'Processed',
            },

            get formattedNetPay() {
                const basic = parseFloat(this.form.basic_pay || 0);
                const allowance = parseFloat(this.form.allowance || 0);
                const deductions = parseFloat(this.form.deductions || 0);
                const total = (basic + allowance) - deductions;

                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                }).format(total);
            },

            resetForm() {
                this.form = {
                    id: null,
                    employee_name: '',
                    employee_id: '',
                    department: '',
                    payroll_period: '',
                    pay_date: '',
                    basic_pay: '',
                    allowance: '',
                    deductions: '',
                    status: 'Processed',
                };
            },

            openCreate() {
                this.isEdit = false;
                this.formAction = "{{ route('human-capital.payroll.store') }}";
                this.resetForm();
                this.showSlideOver = true;
            },

            openEdit(data) {
                this.isEdit = true;
                this.formAction = `/human-capital/payroll/${data.id}`;
                this.form = {
                    id: data.id,
                    employee_name: data.employee_name ?? '',
                    employee_id: data.employee_id ?? '',
                    department: data.department ?? '',
                    payroll_period: data.payroll_period ?? '',
                    pay_date: data.pay_date ?? '',
                    basic_pay: data.basic_pay ?? '',
                    allowance: data.allowance ?? '',
                    deductions: data.deductions ?? '',
                    status: data.status ?? 'Processed',
                };
                this.showSlideOver = true;
            },

            closeSlideOver() {
                this.showSlideOver = false;
            }
        }
    }
</script>
@endsection