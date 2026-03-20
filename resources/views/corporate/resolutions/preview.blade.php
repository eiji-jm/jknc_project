@extends('layouts.app')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #resolution-print, #resolution-print * { visibility: visible; }
        #resolution-print { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Resolution Preview</div>
                <div class="text-xs text-gray-500">Resolution No. {{ $resolution->resolution_no ?? '—' }}</div>
            </div>
            <div class="flex-1"></div>
            <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Delete this resolution?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Delete</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Resolution Document</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()"><i class="fas fa-print"></i></button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()"><i class="fas fa-download"></i></button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="resolution-print" class="bg-white w-full max-w-md rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col justify-between text-center">
                                <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                    <h1 class="text-xl font-bold text-gray-900">RESOLUTION</h1>
                                    <p class="text-xs text-gray-600 mt-2">{{ $resolution->governing_body ?? '—' }}</p>
                                </div>
                                <div class="flex-1 flex flex-col justify-center space-y-3 text-xs text-gray-700">
                                    <p><strong>Resolution No.:</strong> {{ $resolution->resolution_no ?? '—' }}</p>
                                    <p><strong>Meeting:</strong> {{ $resolution->type_of_meeting ?? '—' }} | {{ optional($resolution->date_of_meeting)->format('M d, Y') }}</p>
                                    <p><strong>Location:</strong> {{ $resolution->location ?? '—' }}</p>
                                    <p><strong>Board Resolution:</strong> {{ $resolution->board_resolution ?? '—' }}</p>
                                </div>
                                <div class="border-t-2 border-gray-800 pt-3 text-xs text-gray-600">
                                    Chairman: {{ $resolution->chairman ?? '—' }} | Secretary: {{ $resolution->secretary ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Resolution Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Resolution No.</span><div class="font-medium text-gray-900">{{ $resolution->resolution_no ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900">{{ $resolution->governing_body ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Type</span><div class="font-medium text-gray-900">{{ $resolution->type_of_meeting ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $resolution->meeting_no ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Date</span><div class="font-medium text-gray-900">{{ optional($resolution->date_of_meeting)->format('M d, Y') ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Uploaded By</span><div class="font-medium text-gray-900">{{ $resolution->uploaded_by ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Notary Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notary Public</span><div class="font-medium text-gray-900">{{ $resolution->notary_public ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Doc / Page / Book / Series</span><div class="font-medium text-gray-900">{{ $resolution->notary_doc_no ?? '—' }} / {{ $resolution->notary_page_no ?? '—' }} / {{ $resolution->notary_book_no ?? '—' }} / {{ $resolution->notary_series_no ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-download"></i>
                        Download PDF
                    </button>
                    <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
