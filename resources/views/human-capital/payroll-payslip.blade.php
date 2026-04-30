<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-xl p-8">
        <div class="flex items-start justify-between border-b pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payslip</h1>
                <p class="text-sm text-gray-500">John Kelly & Company</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Print</button>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <p><span class="font-semibold">Employee:</span> {{ trim(($summary->employee->first_name ?? '').' '.($summary->employee->last_name ?? '')) }}</p>
                <p><span class="font-semibold">Payroll Level:</span> {{ $summary->payrollLevel?->level_name }}</p>
                <p><span class="font-semibold">Salary Grade:</span> {{ $summary->payrollLevel?->salaryGrade?->name }}</p>
            </div>
            <div>
                <p><span class="font-semibold">Period:</span> {{ $summary->period?->name }}</p>
                <p><span class="font-semibold">Coverage:</span> {{ $summary->period?->period_start?->format('Y-m-d') }} to {{ $summary->period?->period_end?->format('Y-m-d') }}</p>
                <p><span class="font-semibold">Pay Date:</span> {{ $summary->period?->pay_date?->format('Y-m-d') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="border rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 font-semibold">Earnings</div>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                    @foreach($summary->items->where('item_type', 'earning') as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-4 py-3 font-semibold">Gross Pay</td>
                            <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($summary->gross_pay, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="border rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 font-semibold">Deductions</div>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                    @foreach($summary->items->where('item_type', 'deduction') as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-right">₱{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-4 py-3 font-semibold">Total Deductions</td>
                            <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($summary->total_deductions, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-6 border rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-green-50 font-semibold text-green-700">Net Pay</div>
            <div class="px-4 py-4 text-2xl font-bold text-green-700">
                ₱{{ number_format($summary->net_pay, 2) }}
            </div>
        </div>

        <div class="mt-6 border rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 font-semibold">Basic Salary Breakdown</div>
            <div class="grid grid-cols-5 gap-4 p-4 text-sm">
                <div>
                    <div class="text-gray-500">Yearly</div>
                    <div class="font-semibold">₱{{ number_format($summary->breakdown_json['yearly_basic_salary'] ?? 0, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Monthly</div>
                    <div class="font-semibold">₱{{ number_format($summary->breakdown_json['monthly_basic_salary'] ?? 0, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Daily</div>
                    <div class="font-semibold">₱{{ number_format($summary->breakdown_json['applicable_daily_rate'] ?? 0, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Hourly</div>
                    <div class="font-semibold">₱{{ number_format($summary->breakdown_json['hourly_rate'] ?? 0, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Per Minute</div>
                    <div class="font-semibold">₱{{ number_format($summary->breakdown_json['minute_rate'] ?? 0, 4) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>