@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedCert: null, showAddPanel: false, showRequestPanel: false, activeTab: 'stock' }" @keydown.escape.window="showAddPanel = false; showRequestPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold" x-show="!showPreview">Certificates</div>
            <div class="text-lg font-semibold" x-show="showPreview">Certificate Preview</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    New Certificate
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- NAVIGATION TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Certificates</a>
        </div>

        {{-- TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-4">
            <button
                class="text-sm font-medium pb-2 px-1"
                :class="activeTab === 'stock' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'"
                @click="activeTab = 'stock'"
                type="button"
            >Certificate Stock</button>
            <button
                class="text-sm font-medium pb-2 px-1"
                :class="activeTab === 'voucher' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'"
                @click="activeTab = 'voucher'"
                type="button"
            >Certificate Voucher</button>
        </div>

        {{-- CERTIFICATE STOCK TABLE VIEW --}}
        <div x-show="!showPreview && activeTab === 'stock'" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Corporation Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Company Reg. No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Name of Stockholder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">PAR</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount (PhP)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount in words</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Issued</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">President</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Corporate Secretary</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
    @forelse ($certificates as $certificate)
        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('stock-transfer-book.certificates.show', $certificate) }}'">
            <td class="px-4 py-3">{{ optional($certificate->date_uploaded)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $certificate->uploaded_by }}</td>
            <td class="px-4 py-3">{{ $certificate->corporation_name }}</td>
            <td class="px-4 py-3">{{ $certificate->company_reg_no }}</td>
            <td class="px-4 py-3">{{ $certificate->stock_number }}</td>
            <td class="px-4 py-3">{{ $certificate->stockholder_name }}</td>
            <td class="px-4 py-3">{{ $certificate->par_value }}</td>
            <td class="px-4 py-3">{{ $certificate->number }}</td>
            <td class="px-4 py-3">{{ $certificate->amount }}</td>
            <td class="px-4 py-3">{{ $certificate->amount_in_words }}</td>
            <td class="px-4 py-3">{{ optional($certificate->date_issued)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $certificate->president }}</td>
            <td class="px-4 py-3">{{ $certificate->corporate_secretary }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="13" class="px-4 py-6 text-center text-sm text-gray-500">No certificates found.</td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
        </div>

        {{-- CERTIFICATE VOUCHER TABLE VIEW --}}
        <div x-show="!showPreview && activeTab === 'voucher'" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Corporation Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Company Reg. No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Name of Stockholder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">PAR</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount (PhP)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount in words</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Issued</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">President</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Corporate Secretary</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Issued To</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Issued To Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Certificate Released Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr>
                            <td colspan="17" class="px-4 py-6 text-center text-sm text-gray-500">No certificate vouchers found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REQUEST FOR ISSUANCE SUBSECTION --}}
        <div x-show="!showPreview" class="px-4 pb-6">
            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2 mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Request for Issuance</h3>
                    <span class="text-xs text-gray-500">New COS, Loss COS, Damage COS, Digital Copy of COS, Certified True Copy of CV</span>
                    <div class="flex-1"></div>
                    <button class="px-3 py-1.5 text-xs font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700" type="button" @click="showRequestPanel = true">
                        Add Stockholder Request
                    </button>
                </div>
                <div class="overflow-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ref #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Requested</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type of Request</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Requester</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Received By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Issued By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-900">
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No issuance requests found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- CERTIFICATE PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedCert">
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
                                        <h1 class="text-xl font-bold text-gray-900">STOCK CERTIFICATE</h1>
                                        <p x-text="selectedCert.corpName" class="text-xs text-gray-600 mt-2"></p>
                                    </div>

                                    {{-- MAIN CONTENT --}}
                                    <div class="flex-1 flex flex-col justify-center space-y-3">
                                        <p class="text-xs text-gray-700">
                                            This certifies that <strong x-text="selectedCert.stockholder"></strong> is the owner of
                                        </p>
                                        <div class="border-2 border-gray-400 rounded p-3">
                                            <p class="text-2xl font-bold text-gray-900" x-text="selectedCert.numbers"></p>
                                            <p class="text-xs text-gray-600">fully paid and non-assessable shares</p>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            Certificate No. <strong x-text="selectedCert.certificateNo"></strong>
                                        </p>
                                    </div>

                                    {{-- SIGNATURE LINES --}}
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
                                        <p class="text-xs text-gray-500" x-text="selectedCert.dateIssued"></p>
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
                                    <p x-text="selectedCert.certificateNo" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Journal Reference</p>
                                    <p x-text="selectedCert.journalReference" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date Issued</p>
                                    <p x-text="selectedCert.dateIssued" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- STOCK DETAILS --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Stock Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stockholder</p>
                                    <p x-text="selectedCert.stockholder" class="text-sm text-gray-900 mt-1 font-medium"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Par Value</p>
                                    <p x-text="selectedCert.par" class="text-sm font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Number of Shares</p>
                                    <p x-text="selectedCert.numbers" class="text-sm font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Amount (PhP)</p>
                                    <p x-text="'₱' + selectedCert.amount" class="text-base font-bold text-blue-600 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- AMOUNT IN WORDS --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Amount in Words</p>
                            <p x-text="selectedCert.amountInWords" class="text-sm font-semibold text-gray-900 italic"></p>
                        </div>

                        {{-- SIGNATORY DETAILS --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Signatories</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">President</p>
                                    <p x-text="selectedCert.president" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Corporate Secretary</p>
                                    <p x-text="selectedCert.corpSecetary" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- COMPANY INFO --}}
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-center text-xs">
                            <p class="text-gray-700"><strong x-text="selectedCert.corpName"></strong></p>
                            <p class="text-gray-600">Reg. No.: <span x-text="selectedCert.companyRegNo" class="font-semibold"></span></p>
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

    {{-- ADD CERTIFICATE SLIDER --}}
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
                <div class="text-lg font-semibold">New Certificate</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Date Uploaded</label>
                        <input type="date" data-autofill-field="date_uploaded" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input type="text" data-autofill-field="uploaded_by" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Uploader name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Corporation Name</label>
                        <input type="text" data-autofill-field="corporation_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Corporation name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Company Reg. No.</label>
                        <input type="text" data-autofill-field="company_reg_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="12345-ABC">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Stock Number</label>
                        <input type="text" data-autofill-key data-autofill-field="stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Name of Stockholder</label>
                        <input type="text" data-autofill-field="stockholder_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Stockholder name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">PAR</label>
                        <input type="text" data-autofill-field="par_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Number</label>
                        <input type="number" data-autofill-field="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount (PhP)</label>
                        <input type="text" data-autofill-field="amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100,000.00">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount in words</label>
                        <input type="text" data-autofill-field="amount_in_words" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="One Hundred Thousand Pesos">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Issued</label>
                        <input type="date" data-autofill-field="date_issued" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">President</label>
                        <input type="text" data-autofill-field="president" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="President name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Corporate Secretary</label>
                        <input type="text" data-autofill-field="corporate_secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Secretary name">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save Certificate
                </button>
            </div>
        </div>
    </div>

    {{-- ADD REQUEST FOR ISSUANCE SLIDER --}}
    <div x-cloak>
        <div x-show="showRequestPanel" class="fixed inset-0 bg-black/40 z-40" @click="showRequestPanel = false"></div>
        <div x-show="showRequestPanel"
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
                <div class="text-lg font-semibold">Add Request for Issuance</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showRequestPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Ref #</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="REQ-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Requested</label>
                        <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Time</label>
                        <input type="time" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Type of Request</label>
                        <select class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option>New COS</option>
                            <option>Loss COS</option>
                            <option>Damage COS</option>
                            <option>Digital Copy of COS</option>
                            <option>Certified True Copy of CV</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Requester</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Requester name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Received By</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Receiver name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Issued By</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Issuer name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Status</label>
                        <select class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option>Review</option>
                            <option>Approved</option>
                            <option>Released</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showRequestPanel = false" type="button">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="button">
                    Save Request
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

<script>
    (function () {
        const endpoint = "{{ route('stock-transfer-book.lookup') }}";
        const container = document.currentScript.closest('body');
        const keyInput = container.querySelector('[data-autofill-key]');
        if (!keyInput) return;

        const fieldInputs = Array.from(container.querySelectorAll('[data-autofill-field]'));

        const valueFrom = (field, data) => {
            const cert = data.certificate || {};
            switch (field) {
                case 'date_uploaded':
                    return cert.date_uploaded || '';
                case 'uploaded_by':
                    return cert.uploaded_by || '';
                case 'corporation_name':
                    return cert.corporation_name || '';
                case 'company_reg_no':
                    return cert.company_reg_no || '';
                case 'stock_number':
                    return cert.stock_number || '';
                case 'stockholder_name':
                    return cert.stockholder_name || '';
                case 'par_value':
                    return cert.par_value || '';
                case 'number':
                    return cert.number || '';
                case 'amount':
                    return cert.amount || '';
                case 'amount_in_words':
                    return cert.amount_in_words || '';
                case 'date_issued':
                    return cert.date_issued || '';
                case 'president':
                    return cert.president || '';
                case 'corporate_secretary':
                    return cert.corporate_secretary || '';
                default:
                    return '';
            }
        };

        const runLookup = async () => {
            const key = keyInput.value.trim();
            if (!key) return;
            try {
                const res = await fetch(`${endpoint}?key=${encodeURIComponent(key)}`);
                if (!res.ok) return;
                const data = await res.json();
                fieldInputs.forEach((input) => {
                    const field = input.getAttribute('data-autofill-field');
                    const value = valueFrom(field, data);
                    if (value !== '' && value !== null && value !== undefined) {
                        input.value = value;
                    }
                });
            } catch (e) {
                // ignore lookup errors
            }
        };

        keyInput.addEventListener('change', runLookup);
        keyInput.addEventListener('blur', runLookup);
    })();
</script>

