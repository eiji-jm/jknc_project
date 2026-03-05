@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <a
                href="{{ route('company.index') }}"
                class="inline-flex h-9 items-center gap-2 rounded-full border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                Back
            </a>

            <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="flex flex-wrap items-start gap-4">
                    <div class="h-12 w-12 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-xs font-bold leading-tight">
                        JK<br>&amp;C
                    </div>

                    <div class="flex-1 min-w-[260px]">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $company->company_name }}</h1>
                        <p class="mt-1 text-sm text-gray-500">Corporation</p>
                        <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-3">
                            <p><span class="font-medium text-gray-700">Address:</span> {{ $company->address ?: 'No address yet' }}</p>
                            <p><span class="font-medium text-gray-700">Phone:</span> {{ $company->phone ?: 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Website:</span> {{ $company->website ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Client Intake Form</button>
                        <button class="h-9 rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Business Client Information Form</button>
                        <button class="h-9 rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Doc Requirement</button>
                    </div>

                    <button class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                        Send CIF
                    </button>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Document</th>
                                        <th class="px-4 py-3 text-left font-medium">Status</th>
                                        <th class="px-4 py-3 text-left font-medium">Date Submitted</th>
                                        <th class="px-4 py-3 text-left font-medium">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">Client Information Form</td>
                                        <td class="px-4 py-3">Completed</td>
                                        <td class="px-4 py-3">March 1, 2026</td>
                                        <td class="px-4 py-3">Verified by team</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">SEC Registration</td>
                                        <td class="px-4 py-3">Pending</td>
                                        <td class="px-4 py-3">-</td>
                                        <td class="px-4 py-3">Awaiting upload</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">BIR Documents</td>
                                        <td class="px-4 py-3">In Review</td>
                                        <td class="px-4 py-3">March 3, 2026</td>
                                        <td class="px-4 py-3">Review in progress</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
