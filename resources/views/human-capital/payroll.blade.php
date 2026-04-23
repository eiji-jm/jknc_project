@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Payroll</h1>
            </div>

            <button type="button" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-60 p-3 text-left">Section</th>
                            <th class="p-3 text-left">Description</th>
                            <th class="w-40 p-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr class="border-t"><td class="p-3">Salary Grade</td><td class="p-3">Add salary grade and equivalent basic pay</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Payroll Level</td><td class="p-3">Assign payroll level and salary grade</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Pay Computation</td><td class="p-3">Monthly-paid and daily-paid computation setup</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Basic Salary</td><td class="p-3">Salary breakdown per year, month, hour, and minute</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Benefits</td><td class="p-3">Add benefits and compensation rules</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Allowance</td><td class="p-3">Add allowances and breakdown values</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Deductions</td><td class="p-3">Add percentage or fixed deduction computation</td><td class="p-3 text-gray-400">No data</td></tr>
                        <tr class="border-t"><td class="p-3">Payslip / Reports</td><td class="p-3">Payroll summary, payroll report, payslip report, and employee payslip</td><td class="p-3 text-gray-400">No data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection