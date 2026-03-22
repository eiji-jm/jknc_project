@extends('layouts.app')

@section('content')
@php
    $sampleMinutes = [
        [
            'minutesRef' => 'MIN-2024-001',
            'dateUploaded' => '2024-02-02',
            'uploadedBy' => 'Admin User',
            'governingBody' => 'Board of Directors',
            'typeOfMeeting' => 'Regular',
            'meetingMode' => 'In-Person',
            'noticeRef' => '2024-001',
            'dateOfMeeting' => '2024-02-01',
            'timeStarted' => '10:00 AM',
            'timeEnded' => '11:30 AM',
            'location' => 'Conference Room A',
            'callLink' => '',
            'meetingNo' => '1',
            'chairman' => 'John Smith',
            'secretary' => 'Jane Doe'
        ],
        [
            'minutesRef' => 'MIN-2024-002',
            'dateUploaded' => '2024-02-16',
            'uploadedBy' => 'Compliance Officer',
            'governingBody' => 'Shareholders',
            'typeOfMeeting' => 'Annual General',
            'meetingMode' => 'Hybrid',
            'noticeRef' => '2024-002',
            'dateOfMeeting' => '2024-02-15',
            'timeStarted' => '02:00 PM',
            'timeEnded' => '04:15 PM',
            'location' => 'Main Hall',
            'callLink' => 'https://meet.example.com/agm-2024',
            'meetingNo' => '2',
            'chairman' => 'Robert Brown',
            'secretary' => 'Sarah Wilson'
        ],
        [
            'minutesRef' => 'MIN-2024-003',
            'dateUploaded' => '2024-02-09',
            'uploadedBy' => 'Finance Manager',
            'governingBody' => 'Audit Committee',
            'typeOfMeeting' => 'Special',
            'meetingMode' => 'Virtual',
            'noticeRef' => '2024-003',
            'dateOfMeeting' => '2024-02-08',
            'timeStarted' => '11:00 AM',
            'timeEnded' => '12:30 PM',
            'location' => 'Audit Office',
            'callLink' => 'https://meet.example.com/audit-2024',
            'meetingNo' => '3',
            'chairman' => 'Michael Johnson',
            'secretary' => 'Emily Davis'
        ]
    ];

    $selectedRef = request('ref');
    $selected = collect($sampleMinutes)->firstWhere('minutesRef', $selectedRef) ?? $sampleMinutes[0];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('minutes') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Minutes Preview</div>
                <div class="text-xs text-gray-500">Reference: {{ $selected['minutesRef'] }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected['meetingMode'] }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- LEFT: PREVIEW + ACTIONS --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Meeting Preview</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 text-center text-gray-300">
                        <p class="text-lg font-semibold">Join or Record Meeting</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selected['location'] }} • {{ $selected['dateOfMeeting'] }}</p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            @if($selected['meetingMode'] !== 'In-Person')
                                <button class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                    Start Call
                                </button>
                            @endif
                            @if($selected['meetingMode'] === 'In-Person')
                                <button class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                    Record In-Person
                                </button>
                            @endif
                            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                Attach Meeting File
                            </button>
                        </div>
                        @if($selected['callLink'])
                            <p class="text-xs text-gray-400 mt-3">Call Link: {{ $selected['callLink'] }}</p>
                        @endif
                    </div>
                </div>

                {{-- ATTACHMENTS --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                    <div class="text-sm font-semibold text-gray-900">Attachments</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Meeting File (PDF/Video)</label>
                            <input type="file" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Meeting Scripts (PDF/DOCX)</label>
                            <input type="file" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white hover:file:bg-gray-800">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Script Notes</label>
                        <textarea rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Add script notes or outline..."></textarea>
                    </div>
                </div>

                {{-- MINI WORD NOTES --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center gap-2">
                        <div class="text-sm font-semibold text-gray-900">Meeting Notes</div>
                        <div class="flex-1"></div>
                        <select id="notes-font" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                            <option value="Arial">Arial</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Courier New">Courier New</option>
                        </select>
                        <select id="notes-size" class="border border-gray-300 rounded-lg px-2 py-1 text-xs">
                            <option value="1">10</option>
                            <option value="2">12</option>
                            <option value="3" selected>14</option>
                            <option value="4">16</option>
                            <option value="5">18</option>
                        </select>
                        <button class="px-2 py-1 border border-gray-300 rounded-lg text-xs" data-cmd="bold">Bold</button>
                        <button class="px-2 py-1 border border-gray-300 rounded-lg text-xs" data-cmd="italic">Italic</button>
                        <button class="px-2 py-1 border border-gray-300 rounded-lg text-xs" data-cmd="underline">Underline</button>
                        <button class="px-2 py-1 border border-gray-300 rounded-lg text-xs" data-cmd="hiliteColor" data-value="yellow">Highlight</button>
                        <button class="px-2 py-1 border border-gray-300 rounded-lg text-xs" data-cmd="removeFormat">Clear</button>
                    </div>
                    <div id="notes-editor" class="min-h-[220px] p-4 text-sm outline-none" contenteditable="true">
                        Start taking notes here...
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Meeting Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900">{{ $selected['governingBody'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Type</span><div class="font-medium text-gray-900">{{ $selected['typeOfMeeting'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date</span><div class="font-medium text-gray-900">{{ $selected['dateOfMeeting'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Time</span><div class="font-medium text-gray-900">{{ $selected['timeStarted'] }} - {{ $selected['timeEnded'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Location</span><div class="font-medium text-gray-900">{{ $selected['location'] }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">References</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Minutes Ref</span><div class="font-medium text-gray-900">{{ $selected['minutesRef'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice Ref</span><div class="font-medium text-gray-900">{{ $selected['noticeRef'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $selected['meetingNo'] }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Chairman</span><div class="font-medium text-gray-900">{{ $selected['chairman'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $selected['secretary'] }}</div></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const editor = document.getElementById('notes-editor');
    const fontSelect = document.getElementById('notes-font');
    const sizeSelect = document.getElementById('notes-size');

    document.querySelectorAll('[data-cmd]').forEach(btn => {
        btn.addEventListener('click', () => {
            const cmd = btn.dataset.cmd;
            const value = btn.dataset.value || null;
            document.execCommand(cmd, false, value);
            editor.focus();
        });
    });

    fontSelect.addEventListener('change', () => {
        document.execCommand('fontName', false, fontSelect.value);
        editor.focus();
    });

    sizeSelect.addEventListener('change', () => {
        document.execCommand('fontSize', false, sizeSelect.value);
        editor.focus();
    });
</script>
@endsection
