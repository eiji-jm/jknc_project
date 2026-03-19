@extends('layouts.app')

@section('content')
@php
    $sampleCertificates = [
        [
            'certificateNo' => 'SEC-2024-001',
            'dateUploaded' => '2024-02-07',
            'uploadedBy' => 'Admin User',
            'governingBody' => 'Board of Directors',
            'typeOfMeeting' => 'Regular',
            'noticeRef' => '2024-001',
            'meetingNo' => '1',
            'resolutionNo' => 'RES-2024-001',
            'dateIssued' => '2024-02-03',
            'purpose' => 'Bank Account Opening',
            'dateOfMeeting' => '2024-02-01',
            'location' => 'Conference Room A',
            'secretary' => 'Jane Doe',
            'notaryDocNo' => '110',
            'notaryPageNo' => '28',
            'notaryBookNo' => 'III',
            'notarySeriesNo' => '2024',
            'notaryPublic' => 'Atty. Mark Cruz'
        ],
        [
            'certificateNo' => 'SEC-2024-002',
            'dateUploaded' => '2024-02-20',
            'uploadedBy' => 'Compliance Officer',
            'governingBody' => 'Shareholders',
            'typeOfMeeting' => 'Annual General',
            'noticeRef' => '2024-002',
            'meetingNo' => '2',
            'resolutionNo' => 'RES-2024-002',
            'dateIssued' => '2024-02-16',
            'purpose' => 'Appointment of Officers',
            'dateOfMeeting' => '2024-02-15',
            'location' => 'Main Hall',
            'secretary' => 'Sarah Wilson',
            'notaryDocNo' => '221',
            'notaryPageNo' => '34',
            'notaryBookNo' => 'IV',
            'notarySeriesNo' => '2024',
            'notaryPublic' => 'Atty. Paula Reyes'
        ],
        [
            'certificateNo' => 'SEC-2024-003',
            'dateUploaded' => '2024-02-12',
            'uploadedBy' => 'Finance Manager',
            'governingBody' => 'Audit Committee',
            'typeOfMeeting' => 'Special',
            'noticeRef' => '2024-003',
            'meetingNo' => '3',
            'resolutionNo' => 'RES-2024-003',
            'dateIssued' => '2024-02-09',
            'purpose' => 'Tax Compliance Filing',
            'dateOfMeeting' => '2024-02-08',
            'location' => 'Audit Office',
            'secretary' => 'Emily Davis',
            'notaryDocNo' => '259',
            'notaryPageNo' => '15',
            'notaryBookNo' => 'IV',
            'notarySeriesNo' => '2024',
            'notaryPublic' => 'Atty. Joel Perez'
        ]
    ];

    $selectedRef = request('ref');
    $selected = collect($sampleCertificates)->firstWhere('certificateNo', $selectedRef) ?? $sampleCertificates[0];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('secretary-certificates') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Certificate Preview</div>
                <div class="text-xs text-gray-500">Certificate #: {{ $selected['certificateNo'] }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected['purpose'] }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- LEFT: PREVIEW --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Certificate PDF</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-8 text-center text-gray-300">
                        <p class="text-lg font-semibold">Secretary Certificate Preview</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selected['location'] }} - {{ $selected['dateOfMeeting'] }}</p>
                        <div class="mt-6">
                            <input type="file" accept=".pdf" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900">{{ $selected['certificateNo'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Issued</span><div class="font-medium text-gray-900">{{ $selected['dateIssued'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Purpose</span><div class="font-medium text-gray-900">{{ $selected['purpose'] }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Meeting References</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Resolution No.</span><div class="font-medium text-gray-900">{{ $selected['resolutionNo'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice Ref</span><div class="font-medium text-gray-900">{{ $selected['noticeRef'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $selected['meetingNo'] }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $selected['secretary'] }}</div></div>
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
