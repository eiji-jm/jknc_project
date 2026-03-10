@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedInstallment: null }">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold" x-show="!showPreview">Installment</div>
            <div class="text-lg font-semibold" x-show="showPreview">Installment Payment Plan</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button x-show="!showPreview" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Installment
                </button>
                <button x-show="showPreview" @click="showPreview = false; selectedInstallment = null" class="h-9 px-4 rounded-full bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Installments
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- INSTALLMENT TABLE VIEW --}}
        <div x-show="!showPreview" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Subscriber</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. of Installments</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Value</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">STK-001</td>
                            <td class="px-4 py-3">John Kelly</td>
                            <td class="px-4 py-3">Jan 15, 2026</td>
                            <td class="px-4 py-3">1000</td>
                            <td class="px-4 py-3">4</td>
                            <td class="px-4 py-3">100,000.00</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button 
                                    @click="showPreview = true; selectedInstallment = {
                                        stockNumber: 'STK-001',
                                        subscriber: 'John Kelly',
                                        date: 'Jan 15, 2026',
                                        noShares: '1000',
                                        noInstallments: '4',
                                        totalValue: '100,000.00',
                                        installmentAmount: '25,000.00',
                                        status: 'Ongoing',
                                        paidInstallments: '1',
                                        remainingInstallments: '3',
                                        installmentDetails: [
                                            { no: '1st', dueDate: 'Jan 15, 2026', amount: '25,000.00', status: 'Paid', paidDate: 'Jan 20, 2026' },
                                            { no: '2nd', dueDate: 'Feb 15, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' },
                                            { no: '3rd', dueDate: 'Mar 15, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' },
                                            { no: '4th', dueDate: 'Apr 15, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' }
                                        ]
                                    }"
                                    class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">
                                    View
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="text-gray-600 hover:text-gray-700 hover:underline text-xs font-medium">Pay</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">STK-002</td>
                            <td class="px-4 py-3">Carmen Rodriguez</td>
                            <td class="px-4 py-3">Feb 01, 2026</td>
                            <td class="px-4 py-3">500</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">50,000.00</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button 
                                    @click="showPreview = true; selectedInstallment = {
                                        stockNumber: 'STK-002',
                                        subscriber: 'Carmen Rodriguez',
                                        date: 'Feb 01, 2026',
                                        noShares: '500',
                                        noInstallments: '2',
                                        totalValue: '50,000.00',
                                        installmentAmount: '25,000.00',
                                        status: 'Ongoing',
                                        paidInstallments: '0',
                                        remainingInstallments: '2',
                                        installmentDetails: [
                                            { no: '1st', dueDate: 'Feb 01, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' },
                                            { no: '2nd', dueDate: 'Mar 01, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' }
                                        ]
                                    }"
                                    class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">
                                    View
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="text-gray-600 hover:text-gray-700 hover:underline text-xs font-medium">Pay</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">STK-003</td>
                            <td class="px-4 py-3">Miguel Santos</td>
                            <td class="px-4 py-3">Feb 15, 2026</td>
                            <td class="px-4 py-3">750</td>
                            <td class="px-4 py-3">3</td>
                            <td class="px-4 py-3">75,000.00</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button 
                                    @click="showPreview = true; selectedInstallment = {
                                        stockNumber: 'STK-003',
                                        subscriber: 'Miguel Santos',
                                        date: 'Feb 15, 2026',
                                        noShares: '750',
                                        noInstallments: '3',
                                        totalValue: '75,000.00',
                                        installmentAmount: '25,000.00',
                                        status: 'Ongoing',
                                        paidInstallments: '1',
                                        remainingInstallments: '2',
                                        installmentDetails: [
                                            { no: '1st', dueDate: 'Feb 15, 2026', amount: '25,000.00', status: 'Paid', paidDate: 'Feb 28, 2026' },
                                            { no: '2nd', dueDate: 'Mar 15, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' },
                                            { no: '3rd', dueDate: 'Apr 15, 2026', amount: '25,000.00', status: 'Pending', paidDate: '-' }
                                        ]
                                    }"
                                    class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">
                                    View
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="text-gray-600 hover:text-gray-700 hover:underline text-xs font-medium">Pay</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INSTALLMENT PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedInstallment">
                <div class="grid grid-cols-3 gap-6 h-[calc(100vh-13rem)]">
                    
                    {{-- DOCUMENT SIDE --}}
                    <div class="col-span-2 bg-gray-900 rounded-lg overflow-hidden flex flex-col">
                        {{-- DOCUMENT VIEWER TOOLBAR --}}
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
                        
                        {{-- DOCUMENT MOCKUP --}}
                        <div class="flex-1 overflow-auto p-6 flex items-center justify-center">
                            <div class="bg-white w-full max-w-md rounded-sm shadow-2xl" style="aspect-ratio: 8.5/11;">
                                <div class="p-8 h-full flex flex-col justify-start text-left">
                                    {{-- HEADER --}}
                                    <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                        <h1 class="text-lg font-bold text-gray-900">INSTALLMENT SCHEDULE</h1>
                                        <p class="text-xs text-gray-600 mt-2">John Kelly & Company</p>
                                    </div>
                                    
                                    {{-- CONTENT --}}
                                    <div class="space-y-3 text-xs">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="font-semibold text-gray-700">Subscriber:</p>
                                                <p x-text="selectedInstallment.subscriber" class="text-gray-900"></p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-700">Stock No.:</p>
                                                <p x-text="selectedInstallment.stockNumber" class="text-gray-900"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="border-t border-gray-400 pt-2">
                                            <p class="font-semibold text-gray-700">Total Value: <span x-text="'₱' + selectedInstallment.totalValue" class="text-gray-900 font-bold"></span></p>
                                        </div>

                                        <div class="border-t border-gray-400 pt-2">
                                            <p class="font-semibold text-gray-700 mb-1">Payment Schedule:</p>
                                            <template x-for="(inst, index) in selectedInstallment.installmentDetails" :key="index">
                                                <div class="flex justify-between text-xs py-1 border-b border-gray-300">
                                                    <span><span x-text="inst.no"></span> - <span x-text="inst.dueDate"></span></span>
                                                    <span class="font-semibold" x-text="'₱' + inst.amount"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- DETAILS SIDE --}}
                    <div class="col-span-1 overflow-y-auto space-y-4">
                        
                        {{-- SUBSCRIBER INFORMATION --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Subscriber Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Subscriber Name</p>
                                    <p x-text="selectedInstallment.subscriber" class="text-base font-semibold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Number</p>
                                    <p x-text="selectedInstallment.stockNumber" class="text-base font-semibold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date</p>
                                    <p x-text="selectedInstallment.date" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. of Shares</p>
                                    <p x-text="selectedInstallment.noShares" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</p>
                                    <span x-text="selectedInstallment.status" class="inline-block mt-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"></span>
                                </div>
                            </div>
                        </div>

                        {{-- PAYMENT SUMMARY --}}
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Payment Summary</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Value</p>
                                    <p x-text="'₱' + selectedInstallment.totalValue" class="text-2xl font-bold text-green-600 mt-1"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. Installments</p>
                                        <p x-text="selectedInstallment.noInstallments" class="text-lg font-bold text-blue-600 mt-1"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Per Installment</p>
                                        <p x-text="'₱' + selectedInstallment.installmentAmount" class="text-sm font-bold text-gray-900 mt-1"></p>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-blue-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid/Remaining</p>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        <span x-text="selectedInstallment.paidInstallments" class="text-green-600"></span>/<span x-text="selectedInstallment.noInstallments"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- INSTALLMENT SCHEDULE --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Installment Schedule</h3>
                            <div class="space-y-2">
                                <template x-for="(inst, index) in selectedInstallment.installmentDetails" :key="index">
                                    <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                            :class="{
                                                'bg-green-100 text-green-800': inst.status === 'Paid',
                                                'bg-yellow-100 text-yellow-800': inst.status === 'Pending'
                                            }">
                                            <i :class="inst.status === 'Paid' ? 'fas fa-check' : 'fas fa-clock'"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900">
                                                <span x-text="inst.no"></span> - <span x-text="inst.dueDate"></span>
                                            </p>
                                            <p class="text-xs text-gray-600" x-text="inst.status === 'Paid' ? 'Paid: ' + inst.paidDate : 'Pending'"></p>
                                        </div>
                                        <p class="text-xs font-bold text-gray-900 flex-shrink-0" x-text="'₱' + inst.amount"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="space-y-2 pt-2">
                            <button class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-money-check"></i>
                                Record Payment
                            </button>
                            <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-file-pdf"></i>
                                Download Schedule
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>

</div>
@endsection
