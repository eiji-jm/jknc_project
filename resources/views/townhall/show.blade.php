@extends('layouts.app')

@section('content')
<div class="bg-[#f5f6f8] min-h-screen p-6">

    <div class="max-w-[1400px] mx-auto flex gap-6">

        {{-- LEFT SIDE --}}
        <div id="ack-scroll-container" class="w-[70%] h-[calc(100vh-80px)] overflow-y-auto pr-2">

            {{-- BACK --}}
            <div class="mb-4 flex justify-between">
                <a href="{{ route('townhall') }}"
                   class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                    ← Back
                </a>
            </div>

            {{-- MEMO --}}
            <div class="memo-preview bg-white border border-gray-300 shadow px-[72px] py-[72px] mb-6">

                {{-- HEADER --}}
                <div class="flex justify-between border-b pb-6 mb-8">
                    <div>
                        <h1 class="text-[22px] font-bold">JOHN KELLY & COMPANY</h1>
                        <p class="text-[12px] text-gray-500">Corporate Memorandum</p>
                    </div>

                    <div class="text-right text-sm">
                        <p>Ref No: <b>{{ $communication->ref_no }}</b></p>
                        <p>Date: <b>{{ $communication->communication_date }}</b></p>
                    </div>
                </div>

                {{-- TITLE --}}
                <div class="text-center mb-8">
                    <h2 class="text-[20px] font-bold tracking-[0.2em]">MEMORANDUM</h2>
                </div>

                {{-- META --}}
                <div class="space-y-3 text-sm mb-10">
                    <div class="grid grid-cols-[120px_1fr]">
                        <b>{{ $communication->recipient_label ?? 'To' }}</b>
                        <span class="border-b">{{ $communication->to_for }}</span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>From</b>
                        <span class="border-b">{{ $communication->from_name }}</span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Department</b>
                        <span class="border-b">{{ $communication->department_stakeholder }}</span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Priority</b>
                        <span class="border-b">{{ $communication->priority }}</span>
                    </div>

                    <div class="grid grid-cols-[120px_1fr]">
                        <b>Subject</b>
                        <span class="border-b font-semibold">{{ $communication->subject }}</span>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="text-[15px] leading-8 min-h-[300px]">
                    {!! $communication->message !!}
                </div>

                {{-- SIGNATURE --}}
                <div class="mt-16">
                    <p>Respectfully,</p>
                    <div class="mt-10 border-b w-[250px]"></div>
                    <p class="mt-2 font-semibold">{{ $communication->from_name }}</p>
                </div>

                {{-- FOOTER --}}
                <div class="mt-10 border-t pt-4 text-sm">
                    <p><b>CC:</b> {{ $communication->cc }}</p>
                    <p><b>Additional:</b> {{ $communication->additional }}</p>
                </div>
            </div>

            {{-- ATTACHMENT PREVIEW --}}
            @if($communication->attachment)
            <div class="bg-white border rounded-xl shadow p-5 mb-6">
                <h3 class="font-semibold mb-3">Attachment Preview</h3>

                <div class="h-[500px] overflow-auto border rounded-lg">
                    @php
                        $ext = strtolower(pathinfo($communication->attachment, PATHINFO_EXTENSION));
                    @endphp

                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                        <img src="{{ asset('storage/'.$communication->attachment) }}" class="w-full">
                    @elseif($ext === 'pdf')
                        <iframe src="{{ asset('storage/'.$communication->attachment) }}"
                                class="w-full h-[500px]"></iframe>
                    @else
                        <div class="p-4 text-center">
                            <a href="{{ asset('storage/'.$communication->attachment) }}"
                               target="_blank"
                               class="text-blue-600 underline">
                                Download Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ACKNOWLEDGEMENT --}}
            @if($communication->approval_status === 'Approved')
            <div class="bg-white border rounded-xl shadow p-5">

                <div class="flex justify-between mb-3">
                    <h3 class="font-semibold">Acknowledgement</h3>
                    <span>{{ $ackCount }}/{{ $totalEmployees }}</span>
                </div>

                {{-- PROGRESS --}}
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div class="bg-blue-600 h-3 rounded-full"
                         style="width: {{ $progress }}%"></div>
                </div>

                {{-- Acknowledgement BUTTON --}}
                @if($requiresAcknowledgement && !$hasAcknowledged)
                    <form method="POST" action="{{ route('townhall.acknowledge', $communication->id) }}">
                        @csrf

                        <button id="ack-btn"
                            disabled
                            class="bg-gray-400 text-white px-4 py-2 rounded-lg mb-4 cursor-not-allowed">
                            Acknowledge (Scroll + Wait 10s)
                        </button>

                        <p id="ack-status" class="text-xs text-gray-500">
                            Please scroll to the bottom and wait 10 seconds...
                        </p>
                    </form>
                @endif

                <div class="grid grid-cols-2 gap-4 text-sm">

                    <div>
                        <b>✔ Acknowledged</b>
                        @foreach($acknowledgedUsers as $user)
                            <p class="text-green-600">{{ $user->name }}</p>
                        @endforeach
                    </div>

                    <div>
                        <b>Pending</b>
                        @foreach($notAcknowledgedUsers as $user)
                            <p class="text-red-500">{{ $user->name }}</p>
                        @endforeach
                    </div>

                </div>

            </div>
            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                let hasScrolledToBottom = false;
                let timerDone = false;
                let seconds = 10;

                const container = document.getElementById('ack-scroll-container');
                const button = document.getElementById('ack-btn');
                const statusText = document.getElementById('ack-status');

                if (!container || !button || !statusText) return;

                function updateButtonState() {
                    if (hasScrolledToBottom && timerDone) {
                        button.disabled = false;
                        button.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.innerText = 'Acknowledge';
                        statusText.innerText = 'You can now acknowledge.';
                    } else if (hasScrolledToBottom && !timerDone) {
                        statusText.innerText = `Scrolled to bottom. Please wait ${seconds}s...`;
                    } else if (!hasScrolledToBottom && timerDone) {
                        statusText.innerText = 'Timer finished. Please scroll to the bottom.';
                    } else {
                        statusText.innerText = `Please scroll to the bottom and wait ${seconds}s...`;
                    }
                }

                const interval = setInterval(() => {
                    seconds--;

                    if (seconds <= 0) {
                        timerDone = true;
                        clearInterval(interval);
                    }

                    updateButtonState();
                }, 1000);

                container.addEventListener('scroll', function () {
                    const isAtBottom =
                        container.scrollTop + container.clientHeight >= container.scrollHeight - 10;

                    if (isAtBottom) {
                        hasScrolledToBottom = true;
                        updateButtonState();
                    }
                });

                updateButtonState();
            });
            </script>
            @endpush
            @endif

        </div>

        {{-- RIGHT SIDE PANEL --}}
        <div class="w-[30%]">

            <div class="bg-white border rounded-xl shadow p-5 sticky top-6 space-y-4">

                <h3 class="font-semibold text-lg">Communication Details</h3>

                <div class="text-sm space-y-3">

                    <div>
                        <p class="text-gray-500 text-xs">Ref</p>
                        <p>{{ $communication->ref_no }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Date</p>
                        <p>{{ $communication->communication_date }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">From</p>
                        <p>{{ $communication->from_name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">
                            {{ $communication->recipient_label ?? 'To' }}
                        </p>
                        <p>
                            {{ $communication->to_for }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Department</p>
                        <p>{{ $communication->department_stakeholder }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Status</p>
                        <p>{{ $communication->approval_status }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Subject</p>
                        <p>{{ $communication->subject }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">CC</p>
                        <p>{{ $communication->cc }}</p>
                    </div>

                </div>

                {{-- DOWNLOAD --}}
                @if($communication->approval_status === 'Approved')
                    <a href="{{ route('townhall.download.pdf', $communication->id) }}"
                       class="block text-center bg-red-600 text-white py-2 rounded-lg">
                        Download PDF
                    </a>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
