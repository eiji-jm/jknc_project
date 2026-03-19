@extends('layouts.app')

@section('content')
@php
    $sampleGov = [
        [
            'client' => 'John Kelly & Co.',
            'tin' => '123-456-789-000',
            'agency' => 'SSS',
            'registrationStatus' => 'Registered',
            'registrationDate' => '2021-06-15',
            'registrationNo' => 'SSS-001234',
            'status' => 'Active',
            'uploadedBy' => 'Admin User',
            'dateUploaded' => '2024-02-06'
        ],
        [
            'client' => 'John Kelly & Co.',
            'tin' => '123-456-789-000',
            'agency' => 'Pag-IBIG',
            'registrationStatus' => 'Registered',
            'registrationDate' => '2021-06-20',
            'registrationNo' => 'PAG-009876',
            'status' => 'Active',
            'uploadedBy' => 'Compliance Officer',
            'dateUploaded' => '2024-02-18'
        ],
        [
            'client' => 'John Kelly & Co.',
            'tin' => '123-456-789-000',
            'agency' => 'PhilHealth',
            'registrationStatus' => 'Registered',
            'registrationDate' => '2021-07-01',
            'registrationNo' => 'PH-004321',
            'status' => 'Active',
            'uploadedBy' => 'Finance Manager',
            'dateUploaded' => '2024-02-12'
        ],
        [
            'client' => 'John Kelly & Co.',
            'tin' => '123-456-789-000',
            'agency' => 'DOLE',
            'registrationStatus' => 'Registered',
            'registrationDate' => '2021-07-10',
            'registrationNo' => 'DOLE-778899',
            'status' => 'Active',
            'uploadedBy' => 'Admin User',
            'dateUploaded' => '2024-02-20'
        ]
    ];

    $selectedRef = request('ref');
    $selected = collect($sampleGov)->firstWhere('agency', $selectedRef) ?? $sampleGov[0];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('natgov') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">NatGov Preview</div>
                <div class="text-xs text-gray-500">Agency: {{ $selected['agency'] }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected['status'] }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- LEFT: PREVIEW --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">NatGov Document PDF</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-8 text-center text-gray-300">
                        <p class="text-lg font-semibold">NatGov Document Preview</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selected['client'] }}</p>
                        <div class="mt-6">
                            <input type="file" accept=".pdf" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Registration Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Client</span><div class="font-medium text-gray-900">{{ $selected['client'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900">{{ $selected['tin'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Govt Agency</span><div class="font-medium text-gray-900">{{ $selected['agency'] }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Status</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Status</span><div class="font-medium text-gray-900">{{ $selected['registrationStatus'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Date</span><div class="font-medium text-gray-900">{{ $selected['registrationDate'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration No.</span><div class="font-medium text-gray-900">{{ $selected['registrationNo'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="font-medium text-gray-900">{{ $selected['status'] }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Upload Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Uploaded By</span><div class="font-medium text-gray-900">{{ $selected['uploadedBy'] }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Uploaded</span><div class="font-medium text-gray-900">{{ $selected['dateUploaded'] }}</div></div>
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
