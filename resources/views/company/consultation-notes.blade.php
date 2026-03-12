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
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Consultation Notes</h2>
                            <p class="mt-1 text-sm text-gray-500">Record and track all consultation sessions</p>
                        </div>

                        <button class="h-9 px-4 rounded-full bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 inline-flex items-center gap-2">
                            <span class="text-base leading-none">+</span>
                            <span>Add Consultation Note</span>
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    @foreach ($consultationNotes as $note)
                        <article class="rounded-md border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $note['title'] }}</h3>
                                    <p class="mt-2 text-base text-gray-600">{{ $note['summary'] }}</p>
                                </div>

                                <div class="flex items-center gap-2 text-gray-500">
                                    <button class="h-8 w-8 rounded-full border border-transparent hover:border-gray-200 hover:bg-white">
                                        <i class="far fa-eye text-sm"></i>
                                    </button>
                                    <button class="h-8 w-8 rounded-full border border-transparent hover:border-gray-200 hover:bg-white">
                                        <i class="fas fa-pen text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-5 text-sm text-gray-500">
                                <span class="inline-flex items-center gap-2">
                                    <i class="far fa-calendar text-sm"></i>
                                    {{ $note['date'] }}
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <i class="far fa-user text-sm"></i>
                                    {{ $note['author'] }}
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <i class="fas fa-paperclip text-sm"></i>
                                    {{ $note['attachments'] }} {{ $note['attachments'] === 1 ? 'attachment' : 'attachments' }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
