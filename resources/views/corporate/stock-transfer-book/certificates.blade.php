@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false, showRequestPanel: false, activeTab: 'stock' }" @keydown.escape.window="showAddPanel = false; showRequestPanel = false">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-0 overflow-x-auto">
                <a href="{{ route('corporate.formation') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">SEC-COI</a>
                <a href="{{ route('corporate.sec_aoi') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">SEC-AOI</a>
                <a href="{{ route('corporate.bylaws') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">Bylaws</a>
                <a href="{{ route('stock-transfer-book.index') }}" class="min-w-[180px] px-6 py-3 text-sm font-medium border-t border-b border-r border-blue-500 bg-blue-50 text-blue-700 text-center">Stock Transfer Book</a>
                <a href="{{ route('corporate.gis') }}" class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">GIS</a>
            </div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-bars text-sm"></i>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
                    <i class="fas fa-table-cells-large text-sm"></i>
                </button>

                <button type="button" @click="showAddPanel = true" class="px-4 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <span class="text-base leading-none">+</span>
                    New Certificate
                </button>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50 overflow-x-auto">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Certificates</a>
        </div>

        <div class="px-4 py-3 border-b border-gray-100 flex gap-4">
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

        <div x-show="activeTab === 'stock'" class="p-4">
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
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
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

        <div x-show="activeTab === 'voucher'" class="p-4">
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

        <div class="px-4 pb-6">
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

    </div>

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
            @click.stop>
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
                        <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Uploader name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Corporation Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Corporation name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Company Reg. No.</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="12345-ABC">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Stock Number</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Name of Stockholder</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Stockholder name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">PAR</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Number</label>
                        <input type="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount (PhP)</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100,000.00">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount in words</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="One Hundred Thousand Pesos">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Issued</label>
                        <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">President</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="President name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Corporate Secretary</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Secretary name">
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
            @click.stop>
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