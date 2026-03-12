@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a
                    href="{{ route('company.index') }}"
                    class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900"
                >
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Company</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">{{ $company->company_name }}</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start gap-5">
                    <div class="h-16 w-16 shrink-0 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-sm font-bold leading-tight">
                        JK<br>&amp;C
                    </div>

                    <div class="flex-1 min-w-[280px]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $company->company_name }}</h1>
                                <p class="mt-1 text-sm text-gray-500">Corporation</p>
                            </div>
                            <button class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Address:</span>
                                <span>{{ $company->address ?: '3F, Cebu Holdings Center, Cardinal Rosales Ave, Cebu Business Park, Cebu City' }}</span>
                            </div>
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Phone:</span>
                                <span>{{ $company->phone ?: '0995 353 3789' }}</span>
                            </div>
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Website:</span>
                                <a href="{{ $company->website ?: '#' }}" class="text-blue-600 underline">{{ $company->website ?: 'https://bigin.zoho.com/' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-md border border-gray-200 bg-white overflow-hidden">
                <div class="border-b border-gray-100 px-4 py-4">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">HISTORY</h2>
                    <p class="mt-1 text-sm text-gray-500">View history logs of the company</p>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <div class="relative w-full sm:w-[320px]">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input
                                type="text"
                                placeholder="Search History..."
                                class="w-full h-9 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            >
                        </div>

                        <button class="h-9 min-w-[100px] px-4 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-filter text-xs"></i>
                            <span>All</span>
                        </button>
                    </div>
                </div>

                <div class="px-4 py-5">
                    @foreach ($historyGroups as $group)
                        <div class="{{ $loop->first ? '' : 'mt-8' }}">
                            <h3 class="text-xl font-semibold text-gray-700">{{ $group['label'] }}</h3>

                            <div class="mt-4 space-y-5">
                                @foreach ($group['items'] as $item)
                                    <div class="flex gap-3">
                                        <div class="pt-1">
                                            <div class="h-4 w-4 rounded-full border border-gray-300 bg-white"></div>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-baseline gap-2">
                                                <span class="text-xl font-semibold text-gray-800">{{ $item['actor'] }}</span>
                                                @if ($item['time'])
                                                    <span class="text-sm text-gray-500">{{ $item['time'] }}</span>
                                                @endif
                                            </div>

                                            <div class="mt-2 rounded border border-gray-100 bg-gray-50 px-4 py-3 text-base text-gray-700">
                                                <span class="font-semibold">{{ $item['actor'] }}</span> {{ $item['message'] }}
                                                @if ($item['date'])
                                                    <span class="ml-2 text-gray-500">{{ $item['date'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-end gap-3 text-sm text-gray-500">
                    <span>1 - 5 of 5 results</span>
                    <button class="h-8 w-8 rounded border border-gray-200 hover:bg-gray-50"><i class="fas fa-chevron-left text-xs"></i></button>
                    <span class="h-8 min-w-[32px] rounded border border-gray-200 inline-flex items-center justify-center">1</span>
                    <button class="h-8 w-8 rounded border border-gray-200 hover:bg-gray-50"><i class="fas fa-chevron-right text-xs"></i></button>
                    <span>10 per page</span>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
