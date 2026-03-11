@extends('layouts.app')

@section('content')
<div class="bg-white" x-data="{
    showPreview: false,
    selectedMinutes: null,
    showAddPanel: false,
    formData: {
        minutesRef: '',
        dateUploaded: '',
        uploadedBy: '',
        governingBody: '',
        typeOfMeeting: '',
        meetingMode: '',
        noticeRef: '',
        dateOfMeeting: '',
        timeStarted: '',
        timeEnded: '',
        location: '',
        callLink: '',
        recordingNotes: '',
        meetingNo: '',
        chairman: '',
        secretary: '',
        uploadedFile: ''
    }
}" @keydown.escape.window="showAddPanel = false">

    {{-- MAIN CONTAINER --}}
    <div class="flex h-screen">

        {{-- LEFT COLUMN: TABLE --}}
        <div class="flex-1 flex flex-col" x-show="!showPreview">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Minutes of Meeting</h1>
                    <button @click="showAddPanel = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        + Add Minutes
                    </button>
                </div>
            </div>

            {{-- SEARCH SECTION --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="grid grid-cols-3 gap-4">
                    <input type="text" placeholder="Search minutes reference..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <input type="date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <input type="text" placeholder="Search governing body..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            {{-- TABLE --}}
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0 border-b border-gray-200">
                        <tr class="divide-x divide-gray-200">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Minutes Ref</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Date Uploaded</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Uploaded By</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Governing Body</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Meeting Type</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Notice Ref #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Meeting Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Time Started</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Time Ended</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-28">Location</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-16">Meeting #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Chairman</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Secretary</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
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
                    @endphp

                    @foreach($sampleMinutes as $minutes)
                        <tr onclick="window.location='{{ route('minutes.preview', ['ref' => $minutes['minutesRef']]) }}'" class="hover:bg-blue-50 cursor-pointer divide-x divide-gray-200">
                            <td class="px-4 py-3 text-gray-900 font-medium">{{ $minutes['minutesRef'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($minutes['dateUploaded'])->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['uploadedBy'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['governingBody'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['typeOfMeeting'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['noticeRef'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($minutes['dateOfMeeting'])->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['timeStarted'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['timeEnded'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['location'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['meetingNo'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['chairman'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $minutes['secretary'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- RIGHT COLUMN: PREVIEW PANEL --}}
        <div x-show="showPreview && selectedMinutes" class="w-1/2 bg-gray-900 flex flex-col border-l border-gray-200 overflow-hidden">

        {{-- PREVIEW HEADER --}}
        <div class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white">Minutes #<span x-text="selectedMinutes?.minutesRef"></span></h2>
            <button @click="showPreview = false; selectedMinutes = null" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- PDF VIEWER SECTION --}}
            <div class="flex-1 bg-gray-950 flex items-center justify-center overflow-auto m-4 rounded-lg border border-gray-700">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-400 text-lg font-semibold">Join Meeting</p>
                    <p class="text-gray-500 text-sm mt-2">Video conference for this minutes session</p>
                    <button class="mt-6 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        + Join Meeting
                    </button>
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <button x-show="selectedMinutes?.meetingMode !== 'In-Person'" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            Start Call
                        </button>
                        <button x-show="selectedMinutes?.meetingMode === 'In-Person'" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                            Record In-Person
                        </button>
                    </div>
            </div>
        </div>

        {{-- DETAILS PANEL --}}
        <div class="bg-gray-800 border-t border-gray-700 p-6 space-y-4 max-h-80 overflow-y-auto">

                {{-- MINUTES DETAILS GRID --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Minutes Reference</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.minutesRef"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Date Uploaded</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.dateUploaded"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Uploaded By</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.uploadedBy"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Governing Body</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.governingBody"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Type of Meeting</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.typeOfMeeting"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Notice Reference</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.noticeRef"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Meeting Mode</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.meetingMode"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Call Link</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.callLink || '—'"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Date of Meeting</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.dateOfMeeting"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Time Started</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.timeStarted"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Time Ended</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.timeEnded"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Location</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.location"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Meeting Number</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.meetingNo"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Chairman/President</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.chairman"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Secretary</p>
                        <p class="text-white font-medium mt-1" x-text="selectedMinutes?.secretary"></p>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex gap-2 pt-4 border-t border-gray-700">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                        Download
                    </button>
                    <button class="flex-1 px-3 py-2 bg-gray-700 text-white text-sm rounded hover:bg-gray-600 transition">
                        Print
                    </button>
                </div>
            </div>

    {{-- ADD MINUTES FORM MODAL --}}
    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-50" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full md:w-1/3 bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >

            {{-- FORM HEADER --}}
            <div class="bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Add Minutes of Meeting</h2>
                <button @click="showAddPanel = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- FORM CONTENT --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4">

                {{-- Minutes Reference --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minutes Reference</label>
                    <input x-model="formData.minutesRef" type="text" placeholder="e.g., MIN-2024-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Date Uploaded --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Uploaded</label>
                    <input x-model="formData.dateUploaded" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Uploaded By --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uploaded By</label>
                    <input x-model="formData.uploadedBy" type="text" placeholder="Name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Governing Body --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Governing Body</label>
                    <select x-model="formData.governingBody" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="">Select governing body...</option>
                        <option value="Board of Directors">Board of Directors</option>
                        <option value="Shareholders">Shareholders</option>
                        <option value="Audit Committee">Audit Committee</option>
                        <option value="Management">Management</option>
                    </select>
                </div>

                {{-- Type of Meeting --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type of Meeting</label>
                    <select x-model="formData.typeOfMeeting" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="">Select meeting type...</option>
                        <option value="Regular">Regular</option>
                        <option value="Annual General">Annual General</option>
                        <option value="Special">Special</option>
                        <option value="Extraordinary">Extraordinary</option>
                    </select>
                </div>

                {{-- Meeting Mode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Mode</label>
                    <select x-model="formData.meetingMode" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="">Select meeting mode...</option>
                        <option value="In-Person">In-Person</option>
                        <option value="Virtual">Virtual</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>

                {{-- Notice Reference --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notice Reference #</label>
                    <input x-model="formData.noticeRef" type="text" placeholder="e.g., 2024-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Date of Meeting --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Meeting</label>
                    <input x-model="formData.dateOfMeeting" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Time Started --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Started</label>
                    <input x-model="formData.timeStarted" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Time Ended --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Ended</label>
                    <input x-model="formData.timeEnded" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Location --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input x-model="formData.location" type="text" placeholder="e.g., Conference Room A" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Call Link --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Call Link (Virtual/Hybrid)</label>
                    <input x-model="formData.callLink" type="text" placeholder="https://meet.example.com/..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Recording Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recording Notes (In-Person)</label>
                    <input x-model="formData.recordingNotes" type="text" placeholder="Audio/Video recorder, file ref, etc." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Meeting Number --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Number</label>
                    <input x-model="formData.meetingNo" type="text" placeholder="e.g., 1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Chairman/President --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chairman/President</label>
                    <input x-model="formData.chairman" type="text" placeholder="Full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Secretary --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secretary</label>
                    <input x-model="formData.secretary" type="text" placeholder="Full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- File Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Minutes (PDF)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition cursor-pointer">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Click to upload PDF</p>
                        <input type="file" accept=".pdf" class="hidden">
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="border-t border-gray-200 px-6 py-4 flex gap-2 bg-gray-50">
                <button @click="showAddPanel = false" class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save Minutes
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
