@extends('layouts.app')

@section('content')
<div class="bg-white" x-data="{
    showPreview: false,
    selectedNotice: null,
    showAddPanel: false,
    formData: {
        noticeNumber: '',
        dateOfNotice: '',
        governingBody: '',
        typeOfMeeting: '',
        dateOfMeeting: '',
        timeStarted: '',
        location: '',
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
                    <h1 class="text-2xl font-bold text-gray-900">Notices of Meeting</h1>
                    <button @click="showAddPanel = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        + Add Notice
                    </button>
                </div>
            </div>

            {{-- SEARCH SECTION --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="grid grid-cols-3 gap-4">
                    <input type="text" placeholder="Search notice number..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <input type="date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <input type="text" placeholder="Search governing body..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            {{-- TABLE --}}
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0 border-b border-gray-200">
                        <tr class="divide-x divide-gray-200">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Notice #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Date Notice</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-28">Governing Body</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Meeting Type</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Meeting Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Time</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-28">Location</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-16">Meeting #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Chairman</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-20">Secretary</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-28">Uploaded By</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $sampleNotices = [
                                [
                                    'noticeNumber' => '2024-001',
                                    'dateOfNotice' => '2024-01-15',
                                    'governingBody' => 'Board of Directors',
                                    'typeOfMeeting' => 'Regular',
                                    'dateOfMeeting' => '2024-02-01',
                                    'timeStarted' => '10:00 AM',
                                    'location' => 'Conference Room A',
                                    'meetingNo' => '1',
                                    'chairman' => 'John Smith',
                                    'secretary' => 'Jane Doe',
                                    'uploadedBy' => 'Admin User',
                                    'dateUpdated' => '2024-01-15'
                                ],
                                [
                                    'noticeNumber' => '2024-002',
                                    'dateOfNotice' => '2024-01-20',
                                    'governingBody' => 'Shareholders',
                                    'typeOfMeeting' => 'Annual General',
                                    'dateOfMeeting' => '2024-02-15',
                                    'timeStarted' => '02:00 PM',
                                    'location' => 'Main Hall',
                                    'meetingNo' => '2',
                                    'chairman' => 'Robert Brown',
                                    'secretary' => 'Sarah Wilson',
                                    'uploadedBy' => 'Compliance Officer',
                                    'dateUpdated' => '2024-01-20'
                                ],
                                [
                                    'noticeNumber' => '2024-003',
                                    'dateOfNotice' => '2024-01-25',
                                    'governingBody' => 'Audit Committee',
                                    'typeOfMeeting' => 'Special',
                                    'dateOfMeeting' => '2024-02-08',
                                    'timeStarted' => '11:00 AM',
                                    'location' => 'Audit Office',
                                    'meetingNo' => '3',
                                    'chairman' => 'Michael Johnson',
                                    'secretary' => 'Emily Davis',
                                    'uploadedBy' => 'Finance Manager',
                                    'dateUpdated' => '2024-01-25'
                                ]
                            ];
                        @endphp

                        @foreach($sampleNotices as $notice)
                            <tr @click="showPreview = true; selectedNotice = @json($notice)" class="hover:bg-blue-50 cursor-pointer divide-x divide-gray-200">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $notice['noticeNumber'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($notice['dateOfNotice'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['governingBody'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['typeOfMeeting'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($notice['dateOfMeeting'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['timeStarted'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['location'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['meetingNo'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['chairman'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['secretary'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $notice['uploadedBy'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($notice['dateUpdated'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT COLUMN: PREVIEW PANEL --}}
        <div x-show="showPreview && selectedNotice" class="w-1/2 bg-gray-900 flex flex-col border-l border-gray-200 overflow-hidden">

            {{-- PREVIEW HEADER --}}
            <div class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-white">Notice #<span x-text="selectedNotice?.noticeNumber"></span></h2>
                <button @click="showPreview = false; selectedNotice = null" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- PDF VIEWER SECTION --}}
            <div class="flex-1 bg-gray-950 flex items-center justify-center overflow-auto m-4 rounded-lg border border-gray-700">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-400">PDF Document Viewer</p>
                    <p class="text-gray-500 text-sm mt-1">No document attached</p>
                </div>
            </div>

            {{-- DETAILS PANEL --}}
            <div class="bg-gray-800 border-t border-gray-700 p-6 space-y-4 max-h-80 overflow-y-auto">

                {{-- NOTICE DETAILS GRID --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Notice Number</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.noticeNumber"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Date of Notice</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.dateOfNotice"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Governing Body</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.governingBody"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Type of Meeting</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.typeOfMeeting"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Date of Meeting</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.dateOfMeeting"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Time Started</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.timeStarted"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Location</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.location"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Meeting Number</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.meetingNo"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Chairman/President</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.chairman"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Secretary</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.secretary"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Uploaded By</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.uploadedBy"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Date Updated</p>
                        <p class="text-white font-medium mt-1" x-text="selectedNotice?.dateUpdated"></p>
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
        </div>

    </div>

    {{-- ADD NOTICE FORM MODAL --}}
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
                <h2 class="text-lg font-semibold text-gray-900">Add Notice of Meeting</h2>
                <button @click="showAddPanel = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- FORM CONTENT --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4">

                {{-- Notice Number --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notice Number</label>
                    <input x-model="formData.noticeNumber" type="text" placeholder="e.g., 2024-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                {{-- Date of Notice --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Notice</label>
                    <input x-model="formData.dateOfNotice" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
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

                {{-- Location --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input x-model="formData.location" type="text" placeholder="e.g., Conference Room A" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Notice (PDF)</label>
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
                    Save Notice
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
