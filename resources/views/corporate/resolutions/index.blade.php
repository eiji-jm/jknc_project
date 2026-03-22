@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{
    showAddPanel: false,
    formData: {
        resolutionNo: '',
        dateUploaded: '',
        uploadedBy: '',
        governingBody: '',
        typeOfMeeting: '',
        noticeRef: '',
        meetingNo: '',
        dateOfMeeting: '',
        location: '',
        boardResolution: '',
        directors: '',
        chairman: '',
        secretary: '',
        notaryDocNo: '',
        notaryPageNo: '',
        notaryBookNo: '',
        notarySeriesNo: '',
        notaryPublic: '',
        draftFile: '',
        notarizedFile: ''
    }
}" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Resolutions</div>
            <div class="flex-1"></div>
            <button @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Resolution
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH SECTION --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" placeholder="Search resolution number..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Resolution No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Governing Body</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notice Ref #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Board Resolution</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Directors</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Chairman</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Secretary</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notary Public</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @php
                            $sampleResolutions = [
                                [
                                    'resolutionNo' => 'RES-2024-001',
                                    'dateUploaded' => '2024-02-05',
                                    'uploadedBy' => 'Admin User',
                                    'governingBody' => 'Board of Directors',
                                    'typeOfMeeting' => 'Regular',
                                    'noticeRef' => '2024-001',
                                    'meetingNo' => '1',
                                    'dateOfMeeting' => '2024-02-01',
                                    'location' => 'Conference Room A',
                                    'boardResolution' => 'Approval of Budget',
                                    'directors' => 'J. Smith, R. Brown',
                                    'chairman' => 'John Smith',
                                    'secretary' => 'Jane Doe',
                                    'notaryDocNo' => '102',
                                    'notaryPageNo' => '21',
                                    'notaryBookNo' => 'III',
                                    'notarySeriesNo' => '2024',
                                    'notaryPublic' => 'Atty. Mark Cruz'
                                ],
                                [
                                    'resolutionNo' => 'RES-2024-002',
                                    'dateUploaded' => '2024-02-18',
                                    'uploadedBy' => 'Compliance Officer',
                                    'governingBody' => 'Shareholders',
                                    'typeOfMeeting' => 'Annual General',
                                    'noticeRef' => '2024-002',
                                    'meetingNo' => '2',
                                    'dateOfMeeting' => '2024-02-15',
                                    'location' => 'Main Hall',
                                    'boardResolution' => 'Election of Directors',
                                    'directors' => 'S. Wilson, M. Johnson',
                                    'chairman' => 'Robert Brown',
                                    'secretary' => 'Sarah Wilson',
                                    'notaryDocNo' => '207',
                                    'notaryPageNo' => '33',
                                    'notaryBookNo' => 'IV',
                                    'notarySeriesNo' => '2024',
                                    'notaryPublic' => 'Atty. Paula Reyes'
                                ],
                                [
                                    'resolutionNo' => 'RES-2024-003',
                                    'dateUploaded' => '2024-02-10',
                                    'uploadedBy' => 'Finance Manager',
                                    'governingBody' => 'Audit Committee',
                                    'typeOfMeeting' => 'Special',
                                    'noticeRef' => '2024-003',
                                    'meetingNo' => '3',
                                    'dateOfMeeting' => '2024-02-08',
                                    'location' => 'Audit Office',
                                    'boardResolution' => 'Internal Controls Update',
                                    'directors' => 'E. Davis, M. Johnson',
                                    'chairman' => 'Michael Johnson',
                                    'secretary' => 'Emily Davis',
                                    'notaryDocNo' => '255',
                                    'notaryPageNo' => '12',
                                    'notaryBookNo' => 'IV',
                                    'notarySeriesNo' => '2024',
                                    'notaryPublic' => 'Atty. Joel Perez'
                                ]
                            ];
                        @endphp

                        @foreach($sampleResolutions as $resolution)
                            <tr onclick="window.location='{{ route('resolutions.preview', ['ref' => $resolution['resolutionNo']]) }}'" class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3 font-medium">{{ $resolution['resolutionNo'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($resolution['dateUploaded'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $resolution['uploadedBy'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['governingBody'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['typeOfMeeting'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['noticeRef'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['meetingNo'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($resolution['dateOfMeeting'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $resolution['location'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['boardResolution'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['directors'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['chairman'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['secretary'] }}</td>
                                <td class="px-4 py-3">{{ $resolution['notaryPublic'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ADD RESOLUTION SLIDER --}}
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
                <div class="text-lg font-semibold">Add Resolution</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Resolution No.</label>
                        <input x-model="formData.resolutionNo" type="text" placeholder="e.g., RES-2024-001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
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
                        <label class="text-xs text-gray-600">Notice Reference #</label>
                        <input x-model="formData.noticeRef" type="text" placeholder="e.g., 2024-001" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting No.</label>
                        <input x-model="formData.meetingNo" type="text" placeholder="e.g., 1" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date of Meeting</label>
                        <input x-model="formData.dateOfMeeting" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Location of Meeting</label>
                        <input x-model="formData.location" type="text" placeholder="e.g., Conference Room A" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Board Resolution</label>
                        <input x-model="formData.boardResolution" type="text" placeholder="Subject or title" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Directors</label>
                        <input x-model="formData.directors" type="text" placeholder="Names" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Chairman</label>
                        <input x-model="formData.chairman" type="text" placeholder="Full name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Secretary</label>
                        <input x-model="formData.secretary" type="text" placeholder="Full name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Doc No.</label>
                        <input x-model="formData.notaryDocNo" type="text" placeholder="Doc No." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Page No.</label>
                        <input x-model="formData.notaryPageNo" type="text" placeholder="Page No." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Book No.</label>
                        <input x-model="formData.notaryBookNo" type="text" placeholder="Book No." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Series No.</label>
                        <input x-model="formData.notarySeriesNo" type="text" placeholder="Series No." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Public</label>
                        <input x-model="formData.notaryPublic" type="text" placeholder="Notary public" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Upload Draft (PDF)</label>
                        <input type="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white hover:file:bg-gray-800">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Upload Notarized (PDF)</label>
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
                    Save Resolution
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
