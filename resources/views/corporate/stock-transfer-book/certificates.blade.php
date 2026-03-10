@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedCert: null }">

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
                <button x-show="!showPreview" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    New Certificate
                </button>
                <button x-show="showPreview" @click="showPreview = false; selectedCert = null" class="h-9 px-4 rounded-full bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Certificates
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-4">
            <button class="text-sm font-medium text-blue-600 border-b-2 border-blue-600 pb-2 px-1">Certificate Stock</button>
            <button class="text-sm font-medium text-gray-600 hover:text-gray-900 pb-2 px-1">Certificate Voucher</button>
        </div>

        {{-- CERTIFICATE STOCK TABLE VIEW --}}
        <div x-show="!showPreview" class="p-4">
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">Jan 20, 2026</td>
                            <td class="px-4 py-3">Admin</td>
                            <td class="px-4 py-3">John Kelly & Company</td>
                            <td class="px-4 py-3">12345-ABC</td>
                            <td class="px-4 py-3">STK-001</td>
                            <td class="px-4 py-3">John Kelly</td>
                            <td class="px-4 py-3">100</td>
                            <td class="px-4 py-3">1000</td>
                            <td class="px-4 py-3">100,000.00</td>
                            <td class="px-4 py-3">One Hundred Thousand</td>
                            <td class="px-4 py-3">Jan 22, 2026</td>
                            <td class="px-4 py-3">John Kelly</td>
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button
                                    @click="showPreview = true; selectedCert = {
                                        certificateNo: 'CERT-0001',
                                        journalReference: 'JNL-001',
                                        stockholder: 'John Kelly',
                                        par: '100',
                                        numbers: '1000',
                                        amount: '100,000.00',
                                        amountInWords: 'One Hundred Thousand Pesos',
                                        dateIssued: 'Jan 22, 2026',
                                        president: 'John Kelly',
                                        corpSecetary: 'Maria Santos',
                                        corpName: 'John Kelly & Company',
                                        companyRegNo: '12345-ABC'
                                    }"
                                    class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">
                                    View
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">Feb 01, 2026</td>
                            <td class="px-4 py-3">Admin</td>
                            <td class="px-4 py-3">John Kelly & Company</td>
                            <td class="px-4 py-3">12345-ABC</td>
                            <td class="px-4 py-3">STK-002</td>
                            <td class="px-4 py-3">Carmen Rodriguez</td>
                            <td class="px-4 py-3">100</td>
                            <td class="px-4 py-3">500</td>
                            <td class="px-4 py-3">50,000.00</td>
                            <td class="px-4 py-3">Fifty Thousand</td>
                            <td class="px-4 py-3">Feb 03, 2026</td>
                            <td class="px-4 py-3">John Kelly</td>
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button
                                    @click="showPreview = true; selectedCert = {
                                        certificateNo: 'CERT-0002',
                                        journalReference: 'JNL-002',
                                        stockholder: 'Carmen Rodriguez',
                                        par: '100',
                                        numbers: '500',
                                        amount: '50,000.00',
                                        amountInWords: 'Fifty Thousand Pesos',
                                        dateIssued: 'Feb 03, 2026',
                                        president: 'John Kelly',
                                        corpSecetary: 'Maria Santos',
                                        corpName: 'John Kelly & Company',
                                        companyRegNo: '12345-ABC'
                                    }"
                                    class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">
                                    View
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

</div>
@endsection
