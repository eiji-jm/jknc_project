@extends('layouts.app')

@section('title', 'Sales & Marketing | View IDA')

@section('content')
<div class="flex-1 overflow-y-auto p-6">
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">IDA Record</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        View Incentive Distribution & Allocation details.
                    </p>
                </div>

                <a href="{{ route('sales-marketing.ida.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="font-semibold text-gray-700">Condeal Ref No.:</span> {{ $ida->condeal_ref_no ?: '—' }}</div>
                <div><span class="font-semibold text-gray-700">Client Name:</span> {{ $ida->client_name ?: '—' }}</div>
                <div><span class="font-semibold text-gray-700">Business Name:</span> {{ $ida->business_name ?: '—' }}</div>
                <div><span class="font-semibold text-gray-700">Service Area:</span> {{ $ida->service_area ?: '—' }}</div>
                <div><span class="font-semibold text-gray-700">Engagement Structure:</span> {{ $ida->product_engagement_structure ?: '—' }}</div>
                <div><span class="font-semibold text-gray-700">Deal Value:</span> ₱ {{ number_format((float) $ida->deal_value, 2) }}</div>
                <div><span class="font-semibold text-gray-700">Workflow Status:</span> {{ $ida->workflow_status }}</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Allocations</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-3 font-semibold">Earner</th>
                            <th class="px-6 py-3 font-semibold">Role</th>
                            <th class="px-6 py-3 font-semibold">Commission Category</th>
                            <th class="px-6 py-3 font-semibold">Commission Type</th>
                            <th class="px-6 py-3 font-semibold">Commission Rate</th>
                            <th class="px-6 py-3 font-semibold">Commission Amount</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ida->allocations as $allocation)
                            <tr>
                                <td class="px-6 py-4">{{ optional($allocation->earner)->full_name ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $allocation->role ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $allocation->commission_category ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $allocation->commission_type ?: '—' }}</td>
                                <td class="px-6 py-4">{{ number_format((float) $allocation->commission_rate, 2) }}</td>
                                <td class="px-6 py-4">₱ {{ number_format((float) $allocation->commission_amount, 2) }}</td>
                                <td class="px-6 py-4">{{ $allocation->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                    No allocations yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection