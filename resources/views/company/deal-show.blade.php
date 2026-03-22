@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <section class="bg-gray-50 p-4 min-h-[560px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">Deal Details</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">{{ $deal->name }}</h1>
                    </div>

                    <a href="{{ route('company.deals', $company->id) }}" class="inline-flex h-9 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Back to Deals
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Stage:</span> {{ $deal->stage }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Amount:</span> P{{ number_format((float) $deal->amount, 2) }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Expected Close Date:</span> {{ $deal->expected_close_date ? \Illuminate\Support\Carbon::parse($deal->expected_close_date)->format('M d, Y') : '-' }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Owner:</span> {{ $deal->owner }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Deal Source:</span> {{ $deal->deal_source ?: '-' }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Priority:</span> {{ $deal->priority ?: '-' }}</p>
                    <p class="text-gray-600"><span class="font-medium text-gray-700">Last Updated:</span> {{ $deal->updated_at }}</p>
                </div>

                @if (! empty($deal->notes))
                    <div class="mt-6 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <h2 class="text-sm font-semibold text-gray-900">Notes</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ $deal->notes }}</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
