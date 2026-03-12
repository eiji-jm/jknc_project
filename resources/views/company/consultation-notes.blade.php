@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white overflow-hidden">
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
