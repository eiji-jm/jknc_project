@extends('layouts.app')

@section('content')
@php
    $blankRows = 18;
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #installment-print,
        #installment-print * {
            visibility: visible;
        }
        #installment-print {
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
                <div class="text-lg font-semibold">Installment Preview</div>
                <div class="text-xs text-gray-500">Stock No. {{ $installment->stock_number ?? '—' }}</div>
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
                        <span class="text-gray-300 text-sm font-medium">Installment Ledger Template</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="installment-print" class="bg-white w-full rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col text-[11px] text-gray-900">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-semibold">LEDGER</div>
                                    <div class="text-[10px] text-gray-500">Page 1</div>
                                </div>
                                <div class="mt-2 border border-gray-800">
                                    <table class="w-full text-[10px]">
                                        <thead>
                                            <tr class="border-b border-gray-800">
                                                <th colspan="4" class="border-r border-gray-800 px-2 py-1 text-center uppercase tracking-wide">Certificate Cancelled</th>
                                                <th colspan="4" class="px-2 py-1 text-center uppercase tracking-wide">Certificate Issued</th>
                                            </tr>
                                            <tr class="border-b border-gray-800">
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Journal Folio</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Certificate Number</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Number of Shares</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Journal Folio</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Certificate Number</th>
                                                <th class="px-2 py-1 text-left uppercase tracking-wide">Number of Shares</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < $blankRows; $i++)
                                                <tr class="border-b border-gray-300">
                                                    <td class="border-r border-gray-300 px-2 py-2">&nbsp;</td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="px-2 py-2"></td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-auto pt-6">
                                    <div class="text-xs font-semibold">Certified Correct By:</div>
                                    <div class="mt-6 w-48 border-t border-gray-800 text-[10px] text-gray-600">
                                        Corporate Secretary<br>
                                        (Signature over Printed Name)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: DETAILS (40%) --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Installment Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Subscriber</span><div class="font-medium text-gray-900">{{ $installment->subscriber ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Number</span><div class="font-medium text-gray-900">{{ $installment->stock_number ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Installment Date</span><div class="font-medium text-gray-900">{{ optional($installment->installment_date)->format('M d, Y') ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. Shares</span><div class="font-medium text-gray-900">{{ $installment->no_shares ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. of Installments</span><div class="font-medium text-gray-900">{{ $installment->no_installments ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Per Installment</span><div class="font-medium text-gray-900">{{ $installment->installment_amount ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Total Value</span><div class="font-medium text-gray-900">{{ $installment->total_value ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="font-medium text-gray-900">{{ $installment->status ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Certificates</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedCertificates ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedJournals ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div>
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
