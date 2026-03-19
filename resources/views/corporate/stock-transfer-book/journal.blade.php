@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedEntry: null, showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold" x-show="!showPreview">Journal</div>
            <div class="text-lg font-semibold" x-show="showPreview">Journal Entry Preview</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button x-show="!showPreview" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Transaction
                </button>
                <button x-show="showPreview" @click="showPreview = false; selectedEntry = null" class="h-9 px-4 rounded-full bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Journal
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- NAVIGATION TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        {{-- JOURNAL TABLE VIEW --}}
        <div x-show="!showPreview" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Journal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ledger Folio</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Particulars</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Transaction Type</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedEntry = {
                            date: 'Jan 15, 2026',
                            journalNo: 'JNL-001',
                            ledgerFolio: 'LED-001',
                            particulars: 'Initial share issuance to John Kelly',
                            noShares: '1000',
                            transactionType: 'Issuance',
                            certificateNo: 'CERT-0001',
                            shareholder: 'John Kelly',
                            remarks: 'Original issuance of shares at par value'
                        }">
                            <td class="px-4 py-3">Jan 15, 2026</td>
                            <td class="px-4 py-3">JNL-001</td>
                            <td class="px-4 py-3">LED-001</td>
                            <td class="px-4 py-3">Initial share issuance to John Kelly</td>
                            <td class="px-4 py-3">1000</td>
                            <td class="px-4 py-3"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Issuance</span></td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedEntry = {
                            date: 'Feb 10, 2026',
                            journalNo: 'JNL-002',
                            ledgerFolio: 'LED-002',
                            particulars: 'Share transfer from Carmen Rodriguez to Miguel Santos',
                            noShares: '500',
                            transactionType: 'Transfer',
                            certificateNo: 'CERT-0002',
                            shareholder: 'Carmen Rodriguez → Miguel Santos',
                            remarks: 'Transfer of ownership documented'
                        }">
                            <td class="px-4 py-3">Feb 10, 2026</td>
                            <td class="px-4 py-3">JNL-002</td>
                            <td class="px-4 py-3">LED-002</td>
                            <td class="px-4 py-3">Share transfer from Carmen Rodriguez to Miguel Santos</td>
                            <td class="px-4 py-3">500</td>
                            <td class="px-4 py-3"><span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Transfer</span></td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedEntry = {
                            date: 'Feb 28, 2026',
                            journalNo: 'JNL-003',
                            ledgerFolio: 'LED-001',
                            particulars: 'Share cancellation',
                            noShares: '100',
                            transactionType: 'Cancellation',
                            certificateNo: 'CERT-0001B',
                            shareholder: 'John Kelly',
                            remarks: 'Shares cancelled and removed from circulation'
                        }">
                            <td class="px-4 py-3">Feb 28, 2026</td>
                            <td class="px-4 py-3">JNL-003</td>
                            <td class="px-4 py-3">LED-001</td>
                            <td class="px-4 py-3">Share cancellation</td>
                            <td class="px-4 py-3">100</td>
                            <td class="px-4 py-3"><span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancellation</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedEntry">
                <div class="grid grid-cols-3 gap-6 h-[calc(100vh-13rem)]">

                    {{-- PDF VIEWER SIDE --}}
                    <div class="col-span-2 bg-gray-900 rounded-lg overflow-hidden flex flex-col">
                        {{-- PDF VIEWER TOOLBAR --}}
                        <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="text-gray-400 text-sm mx-2">Page 1 of 1</span>
                            <div class="flex-1"></div>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>

                        {{-- PDF DOCUMENT MOCKUP --}}
                        <div class="flex-1 overflow-auto p-6 flex items-center justify-center">
                            <div class="bg-white w-full max-w-md rounded-sm shadow-2xl" style="aspect-ratio: 8.5/11;">
                                <div class="p-8 h-full flex flex-col justify-between text-center">
                                    {{-- HEADER --}}
                                    <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                        <h1 class="text-2xl font-bold text-gray-900">STOCK CERTIFICATE</h1>
                                        <p class="text-sm text-gray-600 mt-2">John Kelly & Company</p>
                                    </div>

                                    {{-- MAIN CONTENT --}}
                                    <div class="flex-1 flex flex-col justify-center space-y-4">
                                        <p class="text-sm text-gray-700">
                                            This certifies that <strong x-text="selectedEntry.shareholder"></strong> is the owner of
                                        </p>
                                        <div class="border-2 border-gray-400 rounded p-3">
                                            <p class="text-2xl font-bold text-gray-900" x-text="selectedEntry.noShares"></p>
                                            <p class="text-xs text-gray-600">fully paid and non-assessable shares</p>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            Certificate No. <strong x-text="selectedEntry.certificateNo"></strong>
                                        </p>
                                    </div>

                                    {{-- SIGNATURE LINES --}}
                                    <div class="border-t-2 border-gray-800 pt-4 space-y-3">
                                        <div class="grid grid-cols-2 gap-4 text-xs">
                                            <div>
                                                <div class="h-6 border-t border-gray-800 mb-1"></div>
                                                <p class="font-semibold">President</p>
                                            </div>
                                            <div>
                                                <div class="h-6 border-t border-gray-800 mb-1"></div>
                                                <p class="font-semibold">Secretary</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500" x-text="selectedEntry.date"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAILS SIDE --}}
                    <div class="col-span-1 overflow-y-auto space-y-4">

                        {{-- CERTIFICATE INFORMATION --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Certificate Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Certificate No.</p>
                                    <p x-text="selectedEntry.certificateNo" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Journal Reference</p>
                                    <p x-text="selectedEntry.journalNo" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. of Shares</p>
                                    <p x-text="selectedEntry.noShares" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date Issued</p>
                                    <p x-text="selectedEntry.date" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- TRANSACTION DETAILS --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Transaction Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</p>
                                    <div class="mt-2">
                                        <span x-text="selectedEntry.transactionType" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800': selectedEntry.transactionType === 'Issuance',
                                                'bg-blue-100 text-blue-800': selectedEntry.transactionType === 'Transfer',
                                                'bg-red-100 text-red-800': selectedEntry.transactionType === 'Cancellation'
                                            }">
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Shareholder</p>
                                    <p x-text="selectedEntry.shareholder" class="text-sm text-gray-900 mt-1 font-medium"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Particulars</p>
                                    <p x-text="selectedEntry.particulars" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ledger Folio</p>
                                    <p x-text="selectedEntry.ledgerFolio" class="text-sm text-gray-900 mt-1 font-medium"></p>
                                </div>
                            </div>
                        </div>

                        {{-- REMARKS --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Remarks</h3>
                            <p x-text="selectedEntry.remarks" class="text-sm text-gray-700"></p>
                        </div>

                        {{-- ACTION BUTTONS --}}
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
            </template>
        </div>

    </div>

    {{-- ADD TRANSACTION SLIDER --}}
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
                <div class="text-lg font-semibold">Add Journal Transaction</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Date</label>
                        <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Journal No.</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="JNL-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Ledger Folio</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="LED-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">No. Shares</label>
                        <input type="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Particulars</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter particulars">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Transaction Type</label>
                        <select class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option>Issuance</option>
                            <option>Transfer</option>
                            <option>Cancellation</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Certificate No.</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="CERT-0001">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Shareholder</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Shareholder name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Remarks</label>
                        <textarea rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save Transaction
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
