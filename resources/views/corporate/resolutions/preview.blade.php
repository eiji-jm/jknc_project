@extends('layouts.app')

@section('content')
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

    $selectedRef = request('ref');
    $selected = collect($sampleResolutions)->firstWhere('resolutionNo', $selectedRef) ?? $sampleResolutions[0];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('resolutions') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Resolution Preview</div>
                <div class="text-xs text-gray-500">Resolution #: {{ $selected['resolutionNo'] }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected['typeOfMeeting'] }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- LEFT: PREVIEW --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 text-center">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Draft (Unnotarized)</div>
                        <div class="mt-4">
                            <input type="file" accept=".pdf" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white hover:file:bg-gray-800">
                        </div>
                        <p class="text-gray-500 text-xs mt-3">Not yet notarized</p>
                    </div>
                    <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 text-center">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Notarized Original</div>
                        <div class="mt-4">
                            <input type="file" accept=".pdf" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                        <p class="text-gray-500 text-xs mt-3">Official notarized copy</p>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Resolution PDF</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-8 text-center text-gray-300">
                        <p class="text-lg font-semibold">Resolution Document Preview</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selected['location'] }} - {{ $selected['dateOfMeeting'] }}</p>
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
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Location</span><div class="font-medium text-gray-900">{{ $selected['location'] }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">References</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Resolution No.</span><div class="font-medium text-gray-900">{{ $selected['resolutionNo'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice Ref</span><div class="font-medium text-gray-900">{{ $selected['noticeRef'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $selected['meetingNo'] }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Chairman</span><div class="font-medium text-gray-900">{{ $selected['chairman'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $selected['secretary'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Directors</span><div class="font-medium text-gray-900">{{ $selected['directors'] }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Notary Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notary Public</span><div class="font-medium text-gray-900">{{ $selected['notaryPublic'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Doc / Page / Book / Series</span><div class="font-medium text-gray-900">{{ $selected['notaryDocNo'] }} / {{ $selected['notaryPageNo'] }} / {{ $selected['notaryBookNo'] }} / {{ $selected['notarySeriesNo'] }}</div></div>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i>
                        Download PDF
                    </button>
                    <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
