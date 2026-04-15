@extends('layouts.app')

@section('content')
<div
    x-data="payrollPage()"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Payroll</h1>
            </div>

            <button
                type="button"
                @click="openAddSection(activeTab)"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0 hover:bg-blue-700 transition"
            >
                + Add
            </button>
        </div>

        {{-- TABS --}}
        <div class="px-4 py-3 border-b bg-white shrink-0 overflow-x-auto">
            <div class="inline-flex min-w-max rounded-md border border-gray-200 overflow-hidden text-sm">
                <template x-for="tab in tabs" :key="tab.key">
                    <button
                        type="button"
                        @click="activeTab = tab.key"
                        :class="activeTab === tab.key ? 'bg-blue-50 text-blue-700 font-medium' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 border-r last:border-r-0 whitespace-nowrap"
                        x-text="tab.label"
                    ></button>
                </template>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-4 mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold mb-1">Please fix the following:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">

                {{-- Salary Grades --}}
                <div x-show="activeTab === 'salary_grades'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium w-40">Code</th>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium w-40">ADR</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($salaryGrades as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->code }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">₱{{ number_format($item->applicable_daily_rate, 2) }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="3" class="p-10 text-center text-gray-400 italic">No salary grades yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Payroll Levels --}}
                <div x-show="activeTab === 'levels'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium w-44">Level</th>
                                <th class="p-3 text-left font-medium w-44">Salary Grade</th>
                                <th class="p-3 text-left font-medium w-32">Type</th>
                                <th class="p-3 text-left font-medium">Work Schedule</th>
                                <th class="p-3 text-left font-medium w-32">Hours/Day</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($payrollLevels as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->level_name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->salaryGrade?->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->computation_type }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->work_schedule ? str($item->work_schedule)->replace('_', ' ')->title() : '—' }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->hours_per_day }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="5" class="p-10 text-center text-gray-400 italic">No payroll levels yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Benefits --}}
                <div x-show="activeTab === 'benefits'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium w-32">Type</th>
                                <th class="p-3 text-left font-medium w-40">Value</th>
                                <th class="p-3 text-left font-medium w-24">Active</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($benefits as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->type }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->type === 'percentage' ? $item->value.'%' : '₱'.number_format($item->value, 2) }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->is_active ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="4" class="p-10 text-center text-gray-400 italic">No benefits yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Allowances --}}
                <div x-show="activeTab === 'allowances'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium w-32">Type</th>
                                <th class="p-3 text-left font-medium w-40">Value</th>
                                <th class="p-3 text-left font-medium w-24">Active</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($allowances as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->type }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->type === 'percentage' ? $item->value.'%' : '₱'.number_format($item->value, 2) }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->is_active ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="4" class="p-10 text-center text-gray-400 italic">No allowances yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Deductions --}}
                <div x-show="activeTab === 'deductions'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium w-32">Type</th>
                                <th class="p-3 text-left font-medium w-40">Value</th>
                                <th class="p-3 text-left font-medium w-24">Active</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($deductions as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->type }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->type === 'percentage' ? $item->value.'%' : '₱'.number_format($item->value, 2) }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->is_active ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="4" class="p-10 text-center text-gray-400 italic">No deductions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Holidays --}}
                <div x-show="activeTab === 'holidays'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium w-40">Date</th>
                                <th class="p-3 text-left font-medium w-32">Type</th>
                                <th class="p-3 text-left font-medium w-32">Multiplier</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($holidays as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->holiday_date->format('Y-m-d') }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->holiday_type }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->multiplier }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="4" class="p-10 text-center text-gray-400 italic">No holidays yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Periods --}}
                <div x-show="activeTab === 'periods'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium w-44">Name</th>
                                <th class="p-3 text-left font-medium">Coverage</th>
                                <th class="p-3 text-left font-medium w-36">Payroll Date</th>
                                <th class="p-3 text-left font-medium w-36">Pay Date</th>
                                <th class="p-3 text-left font-medium w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($periods as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->period_start->format('Y-m-d') }} to {{ $item->period_end->format('Y-m-d') }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->payroll_date->format('Y-m-d') }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->pay_date->format('Y-m-d') }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words capitalize">{{ $item->status }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="5" class="p-10 text-center text-gray-400 italic">No payroll periods yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Profiles --}}
                <div x-show="activeTab === 'profiles'" x-cloak class="h-full">
                    <table class="w-full text-sm table-fixed border-collapse">
                        <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left font-medium">Employee</th>
                                <th class="p-3 text-left font-medium w-40">Payroll Level</th>
                                <th class="p-3 text-left font-medium w-40">Salary Grade</th>
                                <th class="p-3 text-left font-medium w-32">Override</th>
                                <th class="p-3 text-left font-medium w-28">Night Diff</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($profiles as $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3 text-gray-900 align-top break-words">{{ trim(($item->employee->first_name ?? '').' '.($item->employee->last_name ?? '')) }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->payrollLevel?->level_name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->payrollLevel?->salaryGrade?->name }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->basic_salary_override ? '₱'.number_format($item->basic_salary_override, 2) : '—' }}</td>
                                    <td class="p-3 text-gray-900 align-top break-words">{{ $item->night_differential_enabled ? 'Enabled' : 'Disabled' }}</td>
                                </tr>
                            @empty
                                <tr class="border-t">
                                    <td colspan="5" class="p-10 text-center text-gray-400 italic">No employee payroll profiles yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Summaries --}}
                <div x-show="activeTab === 'summaries'" x-cloak class="h-full p-4 space-y-4">
                    <form action="{{ route('human-capital.payroll.generate-summary') }}" method="POST" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Payroll Period</label>
                            <select name="payroll_period_id" class="w-72 border rounded-md p-2 bg-white text-gray-900">
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}">
                                        {{ $period->name }} ({{ $period->period_start->format('Y-m-d') }} to {{ $period->period_end->format('Y-m-d') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition text-sm">
                            Generate Payroll Summary
                        </button>
                    </form>

                    <div class="border rounded-md overflow-auto bg-white">
                        <table class="w-full text-sm table-fixed border-collapse">
                            <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                                <tr>
                                    <th class="p-3 text-left font-medium">Employee</th>
                                    <th class="p-3 text-left font-medium w-44">Period</th>
                                    <th class="p-3 text-left font-medium w-32">Gross Pay</th>
                                    <th class="p-3 text-left font-medium w-32">Deductions</th>
                                    <th class="p-3 text-left font-medium w-32">Net Pay</th>
                                    <th class="p-3 text-left font-medium w-28">Payslip</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse($summaries as $item)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="p-3 text-gray-900 align-top break-words">{{ trim(($item->employee->first_name ?? '').' '.($item->employee->last_name ?? '')) }}</td>
                                        <td class="p-3 text-gray-900 align-top break-words">{{ $item->period?->name }}</td>
                                        <td class="p-3 text-gray-900 align-top break-words">₱{{ number_format($item->gross_pay, 2) }}</td>
                                        <td class="p-3 text-gray-900 align-top break-words">₱{{ number_format($item->total_deductions, 2) }}</td>
                                        <td class="p-3 text-green-700 font-semibold align-top break-words">₱{{ number_format($item->net_pay, 2) }}</td>
                                        <td class="p-3 text-gray-900 align-top break-words">
                                            <a href="{{ route('human-capital.payroll.payslip.show', $item->id) }}" target="_blank" class="text-blue-600 hover:underline">
                                                View Payslip
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-t">
                                        <td colspan="6" class="p-10 text-center text-gray-400 italic">No payroll summaries yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- ADD SLIDE OVER --}}
        <div
            x-show="showSlider"
            x-cloak
            class="fixed inset-0 z-50"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/40" @click="closeAddSection()"></div>

            <div
                x-ref="addPanel"
                class="absolute top-0 right-0 h-full w-full max-w-[500px] bg-white shadow-2xl flex flex-col overflow-hidden transform translate-x-full transition-transform duration-300 ease-in-out"
            >
                <div class="p-6 border-b flex items-center justify-between shrink-0">
                    <h2 class="font-bold text-lg text-gray-900" x-text="sliderTitle"></h2>
                    <button type="button" @click="closeAddSection()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
                </div>

                <div class="px-6 pt-4 shrink-0">
                    <div
                        x-show="sliderError"
                        x-text="sliderError"
                        class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                    ></div>
                </div>

                <div class="flex-1 overflow-y-auto overflow-x-hidden px-6 py-6">

                    {{-- Salary Grade --}}
                    <form x-show="activeForm === 'salary_grades'" action="{{ route('human-capital.payroll.salary-grades.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Code</label>
                            <input name="code" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Salary grade code" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Salary grade name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Applicable Daily Rate</label>
                            <input type="number" step="0.01" name="applicable_daily_rate" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="0.00" required>
                        </div>
                    </form>

                    {{-- Payroll Level --}}
                    <form x-show="activeForm === 'levels'" action="{{ route('human-capital.payroll.levels.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Salary Grade</label>
                            <select name="salary_grade_id" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                @foreach($salaryGrades as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} - ₱{{ number_format($item->applicable_daily_rate, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Level Name</label>
                            <input name="level_name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Level name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Computation Type</label>
                            <select name="computation_type" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="monthly">Monthly Paid</option>
                                <option value="daily">Daily Paid</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Work Schedule</label>
                            <select name="work_schedule" class="w-full border rounded-md p-2 bg-white text-gray-900">
                                <option value="every_day">Works Every Day</option>
                                <option value="no_sunday">Does Not Work on Sunday</option>
                                <option value="no_sat_sun">Does Not Work on Saturday and Sunday</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Hours Per Day</label>
                            <input type="number" step="0.01" name="hours_per_day" value="8" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                        </div>
                    </form>

                    {{-- Benefits --}}
                    <form x-show="activeForm === 'benefits'" action="{{ route('human-capital.payroll.benefits.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Benefit Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Benefit name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select name="type" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Value</label>
                            <input type="number" step="0.01" name="value" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="0.00" required>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active
                        </label>
                    </form>

                    {{-- Allowances --}}
                    <form x-show="activeForm === 'allowances'" action="{{ route('human-capital.payroll.allowances.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Allowance Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Allowance name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select name="type" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Value</label>
                            <input type="number" step="0.01" name="value" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="0.00" required>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active
                        </label>
                    </form>

                    {{-- Deductions --}}
                    <form x-show="activeForm === 'deductions'" action="{{ route('human-capital.payroll.deductions.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Deduction Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Deduction name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select name="type" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Value</label>
                            <input type="number" step="0.01" name="value" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="0.00" required>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active
                        </label>
                    </form>

                    {{-- Holidays --}}
                    <form x-show="activeForm === 'holidays'" action="{{ route('human-capital.payroll.holidays.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Holiday Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Holiday name" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Holiday Date</label>
                            <input type="date" name="holiday_date" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Holiday Type</label>
                            <select name="holiday_type" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="regular">Regular</option>
                                <option value="special">Special</option>
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Multiplier</label>
                            <input type="number" step="0.01" name="multiplier" value="2.00" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                        </div>
                    </form>

                    {{-- Periods --}}
                    <form x-show="activeForm === 'periods'" action="{{ route('human-capital.payroll.periods.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Name</label>
                            <input name="name" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="1st Half of April 2026" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div class="w-full">
                                <label class="block text-sm font-medium mb-1">Period Start</label>
                                <input type="date" name="period_start" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium mb-1">Period End</label>
                                <input type="date" name="period_end" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium mb-1">Payroll Date</label>
                                <input type="date" name="payroll_date" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium mb-1">Pay Date</label>
                                <input type="date" name="pay_date" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                            </div>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                <option value="draft">Draft</option>
                                <option value="open">Open</option>
                                <option value="processed">Processed</option>
                            </select>
                        </div>
                    </form>

                    {{-- Profiles --}}
                    <form x-show="activeForm === 'profiles'" action="{{ route('human-capital.payroll.profiles.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Employee</label>
                            <select name="employee_id" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ trim(($employee->first_name ?? '').' '.($employee->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Payroll Level</label>
                            <select name="payroll_level_id" class="w-full border rounded-md p-2 bg-white text-gray-900" required>
                                @foreach($payrollLevels as $level)
                                    <option value="{{ $level->id }}">
                                        {{ $level->level_name }} - {{ $level->salaryGrade?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-1">Basic Salary Override</label>
                            <input type="number" step="0.01" name="basic_salary_override" class="w-full border rounded-md p-2 bg-white text-gray-900" placeholder="Optional">
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="night_differential_enabled" value="1">
                            Enable Night Differential
                        </label>
                    </form>

                </div>

                <div class="p-6 border-t flex gap-2 shrink-0 bg-white">
                    <button
                        type="button"
                        @click="closeAddSection()"
                        class="flex-1 border rounded py-2"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        @click="submitActiveForm()"
                        class="flex-1 bg-blue-600 text-white rounded py-2 hover:bg-blue-700 transition"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payrollPage() {
    return {
        tabs: [
            { key: 'salary_grades', label: 'Salary Grade' },
            { key: 'levels', label: 'Payroll Level' },
            { key: 'benefits', label: 'Benefits' },
            { key: 'allowances', label: 'Allowances' },
            { key: 'deductions', label: 'Deductions' },
            { key: 'holidays', label: 'Holidays' },
            { key: 'periods', label: 'Payroll Date' },
            { key: 'profiles', label: 'Payroll Summary Setup' },
            { key: 'summaries', label: 'Payroll Summary' },
        ],
        activeTab: 'salary_grades',
        showSlider: false,
        activeForm: null,
        sliderTitle: 'Add Entry',
        sliderError: '',

        openAddSection(section) {
            this.activeForm = section;
            this.sliderTitle = this.getFormTitle(section);
            this.sliderError = '';
            this.showSlider = true;

            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    this.$refs.addPanel.classList.remove('translate-x-full');
                });
            });
        },

        closeAddSection() {
            if (this.$refs.addPanel) {
                this.$refs.addPanel.classList.add('translate-x-full');
            }

            setTimeout(() => {
                this.showSlider = false;
                this.activeForm = null;
                this.sliderError = '';
            }, 300);
        },

        getFormTitle(section) {
            const titles = {
                salary_grades: 'Add Salary Grade',
                levels: 'Add Payroll Level',
                benefits: 'Add Benefit',
                allowances: 'Add Allowance',
                deductions: 'Add Deduction',
                holidays: 'Add Holiday',
                periods: 'Add Payroll Period',
                profiles: 'Assign Payroll to Employee',
            };

            return titles[section] || 'Add Entry';
        },

        submitActiveForm() {
            this.sliderError = '';

            let form = null;

            if (this.activeForm === 'salary_grades') {
                form = document.querySelector('form[x-show="activeForm === \'salary_grades\'"]');
            } else if (this.activeForm === 'levels') {
                form = document.querySelector('form[x-show="activeForm === \'levels\'"]');
            } else if (this.activeForm === 'benefits') {
                form = document.querySelector('form[x-show="activeForm === \'benefits\'"]');
            } else if (this.activeForm === 'allowances') {
                form = document.querySelector('form[x-show="activeForm === \'allowances\'"]');
            } else if (this.activeForm === 'deductions') {
                form = document.querySelector('form[x-show="activeForm === \'deductions\'"]');
            } else if (this.activeForm === 'holidays') {
                form = document.querySelector('form[x-show="activeForm === \'holidays\'"]');
            } else if (this.activeForm === 'periods') {
                form = document.querySelector('form[x-show="activeForm === \'periods\'"]');
            } else if (this.activeForm === 'profiles') {
                form = document.querySelector('form[x-show="activeForm === \'profiles\'"]');
            }

            if (form) {
                form.submit();
            } else {
                this.sliderError = 'Unable to submit the form.';
            }
        },
    };
}
</script>
@endsection