@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <section class="bg-gray-50 p-4 min-h-[560px]">
            <div class="rounded-md border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <a href="{{ route('company.services.index', $company->id) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Services</span>
                            </a>
                            <h1 class="mt-3 text-2xl font-bold tracking-tight text-gray-900">{{ $service->service_name }}</h1>
                            <p class="mt-1 text-sm text-gray-500">Service engagement details for {{ $company->company_name }}.</p>
                        </div>
                        <a href="{{ route('services.show', $service->id) }}" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Open Global View
                        </a>
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Staff</p>
                            <p class="mt-2 text-sm font-semibold text-gray-900">{{ $service->assigned_unit ?: '-' }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $service->frequency ?: '-' }}</p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</p>
                            @php($statusClasses = match($service->status) {
                                'Active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'Pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                'Completed' => 'border-blue-200 bg-blue-50 text-blue-700',
                                'On Hold' => 'border-violet-200 bg-violet-50 text-violet-700',
                                default => 'border-red-200 bg-red-50 text-red-700',
                            })
                            <span class="mt-2 inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">{{ $service->status }}</span>
                            <p class="mt-2 text-sm text-gray-500">Last updated {{ optional($service->updated_at)->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Company</p>
                            <p class="mt-2 text-sm font-semibold text-gray-900">{{ $company->company_name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $company->company_type ?? 'Company' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-md border border-gray-200 bg-white">
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <dt class="font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-gray-900">{{ $service->category }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Service ID</dt>
                                <dd class="mt-1 text-gray-900">{{ $service->service_id }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Service Area</dt>
                                <dd class="mt-1 text-gray-900">{{ implode(', ', $service->service_area ?? []) ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Engagement Structure</dt>
                                <dd class="mt-1 text-gray-900">{{ implode(', ', $service->engagement_structure ?? []) ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Unit</dt>
                                <dd class="mt-1 text-gray-900">{{ $service->unit ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Rate / Price</dt>
                                <dd class="mt-1 text-gray-900">{{ $service->rate_per_unit ? number_format((float) $service->rate_per_unit, 2) . ' / ' . $service->unit : ($service->price_fee ? number_format((float) $service->price_fee, 2) : '-') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Deadline</dt>
                                <dd class="mt-1 text-gray-900">{{ $service->deadline ? $service->deadline->format('M d, Y h:i A') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-4 rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Description</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-700">{{ $service->service_description }}</p>
                        <h2 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Activity / Output</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-700">{{ $service->service_activity_output }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
