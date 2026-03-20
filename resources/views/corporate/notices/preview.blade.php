@extends('layouts.app')

@section('content')
@php
    $selected = $notice;
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('notices') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Notice Preview</div>
                <div class="text-xs text-gray-500">Notice #: {{ $selected->notice_number }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected->type_of_meeting }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- LEFT: PREVIEW --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Notice PDF</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-8 text-center text-gray-300">
                        <p class="text-lg font-semibold">Notice Document Preview</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selected->location }} - {{ $selected->date_of_meeting }}</p>
                        <div class="mt-6">
                            <input type="file" accept=".pdf" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                    <div class="text-sm font-semibold text-gray-900">Attachments</div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Meeting File (PDF)</label>
                        <input type="file" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white hover:file:bg-gray-800">
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Meeting Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900">{{ $selected->governing_body }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Type</span><div class="font-medium text-gray-900">{{ $selected->type_of_meeting }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date</span><div class="font-medium text-gray-900">{{ $selected->date_of_meeting }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Time</span><div class="font-medium text-gray-900">{{ $selected->time_started }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Location</span><div class="font-medium text-gray-900">{{ $selected->location }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">References</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice No.</span><div class="font-medium text-gray-900">{{ $selected->notice_number }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $selected->meeting_no }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Updated</span><div class="font-medium text-gray-900">{{ $selected->date_updated }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Chairman</span><div class="font-medium text-gray-900">{{ $selected->chairman }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $selected->secretary }}</div></div>
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



