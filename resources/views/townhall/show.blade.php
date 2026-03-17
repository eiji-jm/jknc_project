@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] overflow-hidden">
        <div class="grid grid-cols-12 h-[calc(100vh-9rem)]">

            {{-- LEFT PREVIEW --}}
            <div class="col-span-8 border-r border-gray-200 bg-gray-50 p-4">
                <div
                    id="ack-scroll-area"
                    class="w-full h-full rounded-xl border border-gray-200 bg-white overflow-auto flex items-center justify-center"
                >
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
                            $priority = $communication->priority ?? 'Low';

                            $priorityClasses = match($priority) {
                                'High' => 'bg-red-50 text-red-700',
                                'Low' => 'bg-green-50 text-green-700',
                                default => 'bg-gray-50 text-gray-700',
                            };
                        @endphp
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Priority</p>

                        <span class="inline-flex px-2 py-1 text-xs rounded-full font-medium {{ $priorityClasses }}">
                            {{ $priority }}
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

                    {{-- EMPLOYEE ACKNOWLEDGEMENT --}}
                    @if($requiresAcknowledgement)
                        @php
                            $requiresScroll = !$communication->attachment;
                        @endphp

                        <div
                            class="rounded-xl border border-gray-200 p-4"
                            x-data="{
                                canAcknowledge: false,
                                secondsLeft: 10,
                                timerDone: false,
                                scrolledDone: {{ $requiresScroll ? 'false' : 'true' }}
                            }"
                            x-init="
                                let timer = setInterval(() => {
                                    if (secondsLeft > 0) {
                                        secondsLeft--;
                                    } else {
                                        timerDone = true;
                                        if (scrolledDone) canAcknowledge = true;
                                        clearInterval(timer);
                                    }
                                }, 1000);

                                @if($requiresScroll)
                                    setTimeout(() => {
                                        let el = document.getElementById('ack-scroll-area');
                                        if (el) {
                                            el.addEventListener('scroll', () => {
                                                if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10) {
                                                    scrolledDone = true;
                                                    if (timerDone) canAcknowledge = true;
                                                }
                                            });
                                        }
                                    }, 400);
                                @endif
                            "
                        >
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Acknowledgment Required</p>

                            @if($hasAcknowledged)
                                <button
                                    type="button"
                                    disabled
                                    class="w-full px-4 py-2 text-sm font-medium rounded-lg bg-green-100 text-green-700 cursor-not-allowed"
                                >
                                    Acknowledged
                                </button>
                            @else
                                <div class="text-xs text-gray-500 mb-3 space-y-1">
                                    <p>
                                        Timer:
                                        <span x-show="!timerDone">Wait <span x-text="secondsLeft"></span>s</span>
                                        <span x-show="timerDone" class="text-green-600">✔ Done</span>
                                    </p>

                                    @if($requiresScroll)
                                        <p>
                                            Scroll:
                                            <span x-show="!scrolledDone">Scroll to bottom</span>
                                            <span x-show="scrolledDone" class="text-green-600">✔ Done</span>
                                        </p>
                                    @else
                                        <p class="text-green-600">✔ Attachment preview opened; timer required only</p>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('townhall.acknowledge', $communication->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        :disabled="!canAcknowledge"
                                        :class="canAcknowledge
                                            ? 'bg-blue-600 text-white hover:bg-blue-700'
                                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                        class="w-full px-4 py-2 text-sm font-medium rounded-lg transition"
                                    >
                                        Acknowledge
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    {{-- ADMIN ACKNOWLEDGEMENT TRACKER --}}
                    @if(Auth::user()->hasPermission('approve_townhall'))
                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">
                                Acknowledgement Progress
                            </p>

                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                <div
                                    class="bg-green-500 h-3 rounded-full transition-all"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>

                            <div class="text-xs text-gray-600 mb-3">
                                {{ $ackCount }} / {{ $totalEmployees }} acknowledged ({{ $progress }}%)
                            </div>

                            <div class="mb-3">
                                <p class="text-xs font-semibold text-green-600 mb-1">✔ Acknowledged</p>
                                <div class="max-h-28 overflow-y-auto text-sm space-y-1">
                                    @forelse($acknowledgedUsers as $user)
                                        <div class="px-2 py-1 bg-green-50 rounded">
                                            {{ $user->name }}
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-xs">None</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-red-500 mb-1">✖ Not Yet</p>
                                <div class="max-h-28 overflow-y-auto text-sm space-y-1">
                                    @forelse($notAcknowledgedUsers as $user)
                                        <div class="px-2 py-1 bg-red-50 rounded">
                                            {{ $user->name }}
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-xs">All acknowledged 🎉</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
