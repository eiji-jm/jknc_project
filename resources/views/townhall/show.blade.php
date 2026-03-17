@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] overflow-hidden">
        <div class="grid grid-cols-12 h-[calc(100vh-9rem)]">

            {{-- LEFT PREVIEW --}}
            <div class="col-span-8 border-r border-gray-200 bg-gray-50 p-4">
                <div class="w-full h-full rounded-xl border border-gray-200 bg-white overflow-hidden flex items-center justify-center">

                    @if($communication->attachment)
                        @if($attachmentType === 'image')
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <img
                                    src="{{ asset('storage/' . $communication->attachment) }}"
                                    alt="Attachment Preview"
                                    class="max-w-full max-h-full object-contain"
                                >
                            </div>
                        @elseif($attachmentType === 'pdf')
                            <iframe
                                src="{{ asset('storage/' . $communication->attachment) }}"
                                class="w-full h-full"
                            ></iframe>
                        @else
                            <div class="text-center px-6">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-file text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Preview not available for this file type.</p>
                                <a
                                    href="{{ asset('storage/' . $communication->attachment) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline"
                                >
                                    <i class="fas fa-paperclip"></i>
                                    <span>Open Attachment</span>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="w-full h-full overflow-y-auto p-10">
                            <div class="max-w-4xl mx-auto bg-white text-gray-900">
                                <div class="border border-gray-200 rounded-lg p-8 shadow-sm">
                                    <div class="mb-8">
                                        <h1 class="text-2xl font-bold text-center">Town Hall Communication</h1>
                                    </div>

                                    <div class="space-y-3 text-sm">
                                        <p><span class="font-semibold">Date:</span> {{ $communication->communication_date ?? '—' }}</p>
                                        <p><span class="font-semibold">From:</span> {{ $communication->from_name ?? '—' }}</p>
                                        <p><span class="font-semibold">To / For:</span> {{ $communication->to_for ?? '—' }}</p>
                                        <p><span class="font-semibold">Department / Stakeholder:</span> {{ $communication->department_stakeholder ?? '—' }}</p>
                                        <p><span class="font-semibold">Subject:</span> {{ $communication->subject ?? '—' }}</p>
                                    </div>

                                    <hr class="my-6">

                                    <div class="prose prose-sm max-w-none">
                                        {!! $communication->message ?: '<p>—</p>' !!}
                                    </div>

                                    @if($communication->cc)
                                        <hr class="my-6">
                                        <p class="text-sm"><span class="font-semibold">CC:</span> {{ $communication->cc }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT DETAILS --}}
            <div class="col-span-4 bg-white overflow-y-auto p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Communication Details</h2>
                        <p class="text-sm text-gray-500">{{ $communication->ref_no }}</p>
                    </div>

                    <a
                        href="{{ route('townhall') }}"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Date</p>
                        <p class="text-sm text-gray-800">{{ $communication->communication_date ?? '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">From</p>
                        <p class="text-sm text-gray-800">{{ $communication->from_name ?? '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">To / For</p>
                        <p class="text-sm text-gray-800">{{ $communication->to_for ?? '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Department / Stakeholder</p>
                        <p class="text-sm text-gray-800">{{ $communication->department_stakeholder ?? '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Status</p>
                        @php
                            $status = $communication->status ?? 'Open';
                            $statusClasses = match($status) {
                                'Completed' => 'bg-green-50 text-green-700',
                                'Overdue' => 'bg-red-50 text-red-700',
                                default => 'bg-yellow-50 text-yellow-700',
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs rounded-full font-medium {{ $statusClasses }}">
                            {{ $status }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Subject</p>
                        <p class="text-sm text-gray-800">{{ $communication->subject ?? '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Body</p>
                        <div class="prose prose-sm max-w-none text-gray-800">
                            {!! $communication->message ?: '<p>—</p>' !!}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">CC</p>
                        <p class="text-sm text-gray-800">{{ $communication->cc ?: '—' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Attachment</p>
                        @if($communication->attachment)
                            <a
                                href="{{ asset('storage/' . $communication->attachment) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline"
                            >
                                <i class="fas fa-paperclip"></i>
                                <span>View Attachment</span>
                            </a>
                        @else
                            <p class="text-sm text-gray-800">—</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
