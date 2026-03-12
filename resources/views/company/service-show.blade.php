@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.services.index', $company->id) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Services</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">{{ $service->name }}</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[560px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $service->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Service details for {{ $company->company_name }}</p>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Service Type:</span> {{ $service->service_type }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Category:</span> {{ $service->category }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Pricing Model:</span> {{ $service->pricing_model }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Base Price:</span> P{{ number_format($service->base_price) }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Status:</span> {{ $service->status ?? 'Active' }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Last Updated:</span> {{ $service->updated_at }}</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
