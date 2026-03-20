@extends('layouts.app')

@section('content')
@php
    $certificateNo = $certificate->stock_number ?? '—';
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #certificate-print,
        #certificate-print * {
            visibility: visible;
        }
        #certificate-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Certificate Preview</div>
                <div class="text-xs text-gray-500">Certificate No. {{ $certificateNo }}</div>
            </div>
            <div class="flex-1"></div>
            @if (!empty($editRoute))
                <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Edit
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">

            {{-- LEFT: PREVIEW (60%) --}}
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Stock Certificate</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="certificate-print" class="bg-white w-full max-w-md rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col justify-between text-center">
                                <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                    <h1 class="text-xl font-bold text-gray-900">STOCK CERTIFICATE</h1>
                                    <p class="text-xs text-gray-600 mt-2">{{ $certificate->corporation_name ?? '—' }}</p>
                                </div>

                                <div class="flex-1 flex flex-col justify-center space-y-3">
                                    <p class="text-xs text-gray-700">
                                        This certifies that <strong>{{ $certificate->stockholder_name ?? '—' }}</strong> is the owner of
                                    </p>
                                    <div class="border-2 border-gray-400 rounded p-3">
                                        <p class="text-2xl font-bold text-gray-900">{{ $certificate->number ?? '—' }}</p>
                                        <p class="text-xs text-gray-600">fully paid and non-assessable shares</p>
                                    </div>
                                    <p class="text-xs text-gray-600">
                                        Certificate No. <strong>{{ $certificateNo }}</strong>
                                    </p>
                                </div>

                                <div class="border-t-2 border-gray-800 pt-3 space-y-2">
                                    <div class="grid grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <div class="h-5 border-t border-gray-800 mb-1"></div>
                                            <p class="font-semibold text-xs">President</p>
                                        </div>
                                        <div>
                                            <div class="h-5 border-t border-gray-800 mb-1"></div>
                                            <p class="font-semibold text-xs">Secretary</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ optional($certificate->date_issued)->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS (40%) --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900">{{ $certificateNo }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stockholder</span><div class="font-medium text-gray-900">{{ $certificate->stockholder_name ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Number</span><div class="font-medium text-gray-900">{{ $certificate->stock_number ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Par Value</span><div class="font-medium text-gray-900">{{ $certificate->par_value ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Number</span><div class="font-medium text-gray-900">{{ $certificate->number ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Amount</span><div class="font-medium text-gray-900">{{ $certificate->amount ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Issued</span><div class="font-medium text-gray-900">{{ optional($certificate->date_issued)->format('M d, Y') ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedJournals ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedInstallments ?? collect())->count() }} linked</div>
                        </div>
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
