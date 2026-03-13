@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{
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

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Minutes of Meeting</div>
            <div class="flex-1"></div>
            <button @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Minutes
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH SECTION --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" placeholder="Search minutes reference..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="text" placeholder="Search governing body..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Minutes Ref</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Governing Body</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notice Ref #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time Started</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time Ended</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Chairman</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Secretary</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
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
                        <tr onclick="window.location='{{ route('minutes.preview', ['ref' => $minutes['minutesRef']]) }}'" class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                            <td class="px-4 py-3 font-medium">{{ $minutes['minutesRef'] }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($minutes['dateUploaded'])->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $minutes['uploadedBy'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['governingBody'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['typeOfMeeting'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['noticeRef'] }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($minutes['dateOfMeeting'])->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $minutes['timeStarted'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['timeEnded'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['location'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['meetingNo'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['chairman'] }}</td>
                            <td class="px-4 py-3">{{ $minutes['secretary'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>

    {{-- ADD MINUTES SLIDER --}}
    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Add Minutes of Meeting</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Minutes Reference</label>
                        <input x-model="formData.minutesRef" type="text" placeholder="e.g., MIN-2024-001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Uploaded</label>
                        <input x-model="formData.dateUploaded" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input x-model="formData.uploadedBy" type="text" placeholder="Name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Governing Body</label>
                        <select x-model="formData.governingBody" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select governing body...</option>
                            <option value="Board of Directors">Board of Directors</option>
                            <option value="Shareholders">Shareholders</option>
                            <option value="Audit Committee">Audit Committee</option>
                            <option value="Management">Management</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Type of Meeting</label>
                        <select x-model="formData.typeOfMeeting" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select meeting type...</option>
                            <option value="Regular">Regular</option>
                            <option value="Annual General">Annual General</option>
                            <option value="Special">Special</option>
                            <option value="Extraordinary">Extraordinary</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting Mode</label>
                        <select x-model="formData.meetingMode" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select meeting mode...</option>
                            <option value="In-Person">In-Person</option>
                            <option value="Virtual">Virtual</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notice Reference #</label>
                        <input x-model="formData.noticeRef" type="text" placeholder="e.g., 2024-001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date of Meeting</label>
                        <input x-model="formData.dateOfMeeting" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Time Started</label>
                        <input x-model="formData.timeStarted" type="time" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Time Ended</label>
                        <input x-model="formData.timeEnded" type="time" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Location</label>
                        <input x-model="formData.location" type="text" placeholder="e.g., Conference Room A" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Call Link (Virtual/Hybrid)</label>
                        <input x-model="formData.callLink" type="text" placeholder="https://meet.example.com/..." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Recording Notes (In-Person)</label>
                        <input x-model="formData.recordingNotes" type="text" placeholder="Recorder, file ref, etc." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting Number</label>
                        <input x-model="formData.meetingNo" type="text" placeholder="e.g., 1" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Chairman/President</label>
                        <input x-model="formData.chairman" type="text" placeholder="Full name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Secretary</label>
                        <input x-model="formData.secretary" type="text" placeholder="Full name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload Minutes (PDF)</label>
                        <input type="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save Minutes
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
