@extends('layouts.app')

@section('content')
@php
    $phaseBadgeClasses = [
        'RSAT' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
        'Planning' => 'bg-blue-50 text-blue-700 border border-blue-200',
        'For NTP Approval' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Execution' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Reporting' => 'bg-cyan-50 text-cyan-700 border border-cyan-200',
        'Delivery' => 'bg-violet-50 text-violet-700 border border-violet-200',
        'Completed' => 'bg-green-50 text-green-700 border border-green-200',
    ];
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Regular</h1>
                <p class="mt-1 max-w-3xl text-sm text-gray-500">
                    Regular and retainer deals automatically open here, with RSAT, approvals, execution, reporting, delivery, and continuation tracked inside one record.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex h-11 items-center justify-center rounded-full bg-[#102d79] px-5 text-sm font-semibold text-white shadow-sm hover:bg-[#0d255f]"
                onclick="window.jkncSlideOver.open(document.getElementById('regularManualCreateDrawer'))"
            >
                Create Regular
            </button>
        </div>

        <div class="mb-6 grid gap-3 xl:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">All Regular</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['all'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">RSAT</p>
                <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $stats['rsat'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Planning</p>
                <p class="mt-2 text-3xl font-bold text-blue-700">{{ $stats['planning'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active</p>
                <p class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Completed</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['completed'] }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-semibold text-gray-900">Regular Registry</h2>
                <p class="mt-1 text-sm text-gray-500">This list is now backed by saved regular engagements instead of placeholder data.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Regular</th>
                            <th class="px-4 py-3 text-left font-medium">Deal</th>
                            <th class="px-4 py-3 text-left font-medium">Company</th>
                            <th class="px-4 py-3 text-left font-medium">Phase</th>
                            <th class="px-4 py-3 text-left font-medium">Owner</th>
                            <th class="px-4 py-3 text-left font-medium">Target</th>
                            <th class="px-4 py-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                        @forelse ($regulars as $regular)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $regular->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $regular->project_code }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $regular->deal?->deal_code ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $regular->company?->company_name ?: ($regular->business_name ?: '-') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $phaseBadgeClasses[$regular->status] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">{{ $regular->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $regular->assigned_project_manager ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ optional($regular->target_completion_date)->format('M d, Y') ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('regular.show', $regular) }}" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No regular engagements have created regular records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-slide-over id="regularManualCreateDrawer" width="sm:max-w-[760px]">
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Create Regular</h2>
            <p class="mt-1 text-sm text-gray-500">Manually create a regular engagement and open the RSAT form to fill out details, scope, activities, and requirements.</p> 
        </div>
        <button type="button" class="rounded-full p-2 text-gray-500 hover:bg-gray-100" onclick="window.jkncSlideOver.close(document.getElementById('regularManualCreateDrawer'))">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form method="POST" action="{{ route('regular.manual.store') }}" class="flex h-full flex-col overflow-hidden">
        @csrf
        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">          
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Regular Name</label>
                    <input name="name" value="{{ old('name') }}" required class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Client Name</label>
                    <input name="client_name" value="{{ old('client_name') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Business Name</label>
                    <input name="business_name" value="{{ old('business_name') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Date Started</label>
                    <input type="date" name="planned_start_date" value="{{ old('planned_start_date') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Target Completion</label>
                    <input type="date" name="target_completion_date" value="{{ old('target_completion_date') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Service Area</label>
                    <input name="service_area" value="{{ old('service_area') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Client Signature Name</label>
                    <input name="client_confirmation_name" value="{{ old('client_confirmation_name') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Prepared By</label>
                    <input name="assigned_project_manager" value="{{ old('assigned_project_manager') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Lead Consultant</label>
                    <input name="assigned_consultant" value="{{ old('assigned_consultant') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Lead Associate</label>
                    <input name="assigned_associate" value="{{ old('assigned_associate') }}" class="h-11 w-full rounded-xl border border-gray-300 px-4 text-sm text-gray-900">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Services</label>
                    <textarea name="services" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900">{{ old('services') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Products</label>
                    <textarea name="products" rows="2" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900">{{ old('products') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">RSAT Activities / Requirements</label>
                    <textarea name="engagement_requirements_text" rows="6" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900" placeholder="One activity or requirement per line">{{ old('engagement_requirements_text') }}</textarea>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-end gap-3">
                <button type="button" class="inline-flex h-11 items-center rounded-full border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50" onclick="window.jkncSlideOver.close(document.getElementById('regularManualCreateDrawer'))">Cancel</button>
                <button type="submit" class="inline-flex h-11 items-center rounded-full bg-[#102d79] px-5 text-sm font-semibold text-white hover:bg-[#0d255f]">Create</button>
            </div>
        </div>
    </form>
</x-slide-over>
@endsection
