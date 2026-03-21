@extends('layouts.app')

@section('content')
@php
    $issuedCancelled = ($journalEntries ?? collect())->filter(function ($entry) {
        return in_array(strtolower($entry->transaction_type ?? ''), ['issuance', 'cancellation'], true);
    })->values();
    $previewRows = $issuedCancelled->take(12);
    $blankRows = max(12 - $previewRows->count(), 0);
    $isIssuance = strtolower($journal->transaction_type ?? '') === 'issuance';
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #journal-print,
        #journal-print * {
            visibility: visible;
        }
        #journal-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            filter: none !important;
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
                <div class="text-lg font-semibold">Journal Entry Preview</div>
                <div class="text-xs text-gray-500">Journal No. {{ $journal->journal_no ?? '—' }}</div>
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
                        <span class="text-gray-300 text-sm font-medium">Stock Transfer Book Journal</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="journal-print" class="bg-white w-full rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col text-[11px] text-gray-900">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-semibold">JOURNAL</div>
                                    <div class="text-[10px] text-gray-500">Page 1</div>
                                </div>
                                <div class="mt-2 border border-gray-800">
                                    @if ($isIssuance)
                                        <table class="w-full text-[10px]">
                                            <thead>
                                                <tr class="border-b border-gray-800">
                                                    <th rowspan="2" class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">In Whose Name</th>
                                                    <th colspan="4" class="border-r border-gray-800 px-2 py-1 text-center uppercase tracking-wide">Certificate Issued</th>
                                                    <th rowspan="2" class="px-2 py-1 text-left uppercase tracking-wide">Received By (Signature)</th>
                                                </tr>
                                                <tr class="border-b border-gray-800">
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Ledger Folio</th>
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Certificate Number</th>
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">No. Shares</th>
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Total No. Shares</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($previewRows as $entry)
                                                    <tr class="border-b border-gray-300">
                                                        <td class="border-r border-gray-300 px-2 py-2">
                                                            <div class="font-medium">{{ $entry->shareholder ?? '—' }}</div>
                                                            <div class="text-[9px] text-gray-500">{{ optional($entry->entry_date)->format('M d, Y') }}</div>
                                                        </td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->ledger_folio ?? '—' }}</td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->certificate_no ?? '—' }}</td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->no_shares ?? '—' }}</td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->no_shares ?? '—' }}</td>
                                                        <td class="px-2 py-2">{{ $entry->shareholder ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                                @for ($i = 0; $i < $blankRows; $i++)
                                                    <tr class="border-b border-gray-300">
                                                        <td class="border-r border-gray-300 px-2 py-2">&nbsp;</td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="px-2 py-2"></td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    @else
                                        <table class="w-full text-[10px]">
                                            <thead>
                                                <tr class="border-b border-gray-800">
                                                    <th rowspan="2" class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                    <th rowspan="2" class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">By Whom Surrendered</th>
                                                    <th colspan="3" class="border-r border-gray-800 px-2 py-1 text-center uppercase tracking-wide">Certificate Cancelled</th>
                                                    <th rowspan="2" class="px-2 py-1 text-left uppercase tracking-wide">Surrendered By (Signature)</th>
                                                </tr>
                                                <tr class="border-b border-gray-800">
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Ledger Folio</th>
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Certificate No.</th>
                                                    <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">No. Shares</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($previewRows as $entry)
                                                    <tr class="border-b border-gray-300">
                                                        <td class="border-r border-gray-300 px-2 py-2">
                                                            {{ optional($entry->entry_date)->format('M d, Y') }}
                                                        </td>
                                                        <td class="border-r border-gray-300 px-2 py-2">
                                                            <div class="font-medium">{{ $entry->shareholder ?? '—' }}</div>
                                                            <div class="text-[9px] text-gray-500">{{ $entry->transaction_type ?? '—' }}</div>
                                                        </td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->ledger_folio ?? '—' }}</td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->certificate_no ?? '—' }}</td>
                                                        <td class="border-r border-gray-300 px-2 py-2">{{ $entry->no_shares ?? '—' }}</td>
                                                        <td class="px-2 py-2">{{ $entry->shareholder ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                                @for ($i = 0; $i < $blankRows; $i++)
                                                    <tr class="border-b border-gray-300">
                                                        <td class="border-r border-gray-300 px-2 py-2">&nbsp;</td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="border-r border-gray-300 px-2 py-2"></td>
                                                        <td class="px-2 py-2"></td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    @endif
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
                    <div class="text-sm font-semibold text-gray-900 mb-3">Journal Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Journal No.</span><div class="font-medium text-gray-900">{{ $journal->journal_no ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Entry Date</span><div class="font-medium text-gray-900">{{ optional($journal->entry_date)->format('M d, Y') ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Ledger Folio</span><div class="font-medium text-gray-900">{{ $journal->ledger_folio ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Transaction Type</span><div class="font-medium text-gray-900">{{ $journal->transaction_type ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900">{{ $journal->certificate_no ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. of Shares</span><div class="font-medium text-gray-900">{{ $journal->no_shares ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="text-sm font-semibold text-gray-900">Issued / Cancelled List</div>
                        <div class="flex-1"></div>
                        <a href="{{ route('stock-transfer-book.journal.create') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Add Entry</a>
                    </div>
                    <div class="space-y-3 max-h-72 overflow-auto">
                        @forelse ($issuedCancelled as $entry)
                            <div class="p-3 rounded-lg border border-gray-200 bg-white">
                                <div class="flex items-center gap-2">
                                    <div class="text-xs font-semibold text-gray-700">{{ $entry->certificate_no ?? '—' }}</div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $entry->transaction_type ?? '—' }}</span>
                                </div>
                                <div class="text-sm font-medium text-gray-900 mt-1">{{ $entry->shareholder ?? '—' }}</div>
                                <div class="text-[11px] text-gray-600 mt-1">Shares: {{ $entry->no_shares ?? '—' }} | {{ optional($entry->entry_date)->format('M d, Y') }}</div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No issued or cancelled certificates found.</div>
                        @endforelse
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
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Ledger Entries</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedInstallments ?? collect())->count() }} linked</div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-2">Particulars / Remarks</div>
                    <p class="text-sm text-gray-700">{{ $journal->particulars ?? '—' }}</p>
                    <p class="text-xs text-gray-600 mt-2">{{ $journal->remarks ?? '' }}</p>
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
