@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{
        showAddPanel: false,
        showRequestPanel: false,
        showVoucherPanel: false,
        showCancelPanel: false,
        activeTab: 'stock',
        selectedVoucher: null,
        selectedCertificate: null
     }"
     @keydown.escape.window="showAddPanel = false; showRequestPanel = false; showVoucherPanel = false; showCancelPanel = false">

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

        @if(session('success'))
            <div class="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="px-4 py-3 border-b border-gray-100 flex gap-4">
            <button class="text-sm font-medium pb-2 px-1" :class="activeTab === 'stock' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'stock'" type="button">
                Certificate Stock
            </button>

            <button class="text-sm font-medium pb-2 px-1" :class="activeTab === 'voucher' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'voucher'" type="button">
                Certificate Voucher
            </button>

            <button class="text-sm font-medium pb-2 px-1" :class="activeTab === 'cancellation' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'cancellation'" type="button">
                Cancellation
            </button>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
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
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $certificate->status === 'Cancelled' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                        {{ $certificate->status ?: 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if(($certificate->status ?: 'Active') !== 'Cancelled')
                                        <button type="button"
                                                class="px-3 py-1.5 text-xs rounded-md bg-red-600 text-white hover:bg-red-700"
                                                @click="
                                                    selectedCertificate = {
                                                        id: '{{ $certificate->id }}',
                                                        stock_number: @js($certificate->stock_number),
                                                        stockholder_name: @js($certificate->stockholder_name)
                                                    };
                                                    showCancelPanel = true;
                                                ">
                                            Cancel
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-500">Already Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-4 py-6 text-center text-sm text-gray-500">No certificates found.</td>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($vouchers as $voucher)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">{{ optional($voucher->date_uploaded)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $voucher->uploaded_by }}</td>
                                <td class="px-4 py-3">{{ $voucher->corporation_name }}</td>
                                <td class="px-4 py-3">{{ $voucher->company_reg_no }}</td>
                                <td class="px-4 py-3">{{ $voucher->stock_number }}</td>
                                <td class="px-4 py-3">{{ $voucher->stockholder_name }}</td>
                                <td class="px-4 py-3">{{ $voucher->par_value }}</td>
                                <td class="px-4 py-3">{{ $voucher->number }}</td>
                                <td class="px-4 py-3">{{ $voucher->amount }}</td>
                                <td class="px-4 py-3">{{ $voucher->amount_in_words }}</td>
                                <td class="px-4 py-3">{{ optional($voucher->date_issued)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $voucher->president }}</td>
                                <td class="px-4 py-3">{{ $voucher->corporate_secretary }}</td>
                                <td class="px-4 py-3">{{ $voucher->issued_to }}</td>
                                <td class="px-4 py-3">{{ $voucher->issued_to_type }}</td>
                                <td class="px-4 py-3">{{ optional($voucher->certificate_released_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <button type="button"
                                            class="px-3 py-1.5 text-xs rounded-md bg-blue-600 text-white hover:bg-blue-700"
                                            @click="
                                                selectedVoucher = {
                                                    id: '{{ $voucher->id }}',
                                                    issued_to: @js($voucher->issued_to),
                                                    issued_to_type: @js($voucher->issued_to_type),
                                                    certificate_released_date: '{{ $voucher->certificate_released_date }}'
                                                };
                                                showVoucherPanel = true;
                                            ">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-4 py-6 text-center text-sm text-gray-500">No certificate vouchers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="activeTab === 'cancellation'" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stockholder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date of Cancellation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Effective Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type of Cancellation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Reason</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Others</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($cancellations as $cancel)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">{{ $cancel->certificate->stock_number ?? '' }}</td>
                                <td class="px-4 py-3">{{ $cancel->certificate->stockholder_name ?? '' }}</td>
                                <td class="px-4 py-3">{{ optional($cancel->date_of_cancellation)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ optional($cancel->effective_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $cancel->type_of_cancellation }}</td>
                                <td class="px-4 py-3">{{ $cancel->reason }}</td>
                                <td class="px-4 py-3">{{ $cancel->others_specify }}</td>
                                <td class="px-4 py-3">{{ $cancel->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No cancellation records found.</td>
                            </tr>
                        @endforelse
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
                            @forelse ($issuanceRequests as $requestRow)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">{{ $requestRow->ref_no }}</td>
                                    <td class="px-4 py-3">{{ optional($requestRow->date_requested)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->time_requested }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->type_of_request }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->requester }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->received_by }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->issued_by }}</td>
                                    <td class="px-4 py-3">{{ $requestRow->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No issuance requests found.</td>
                                </tr>
                            @endforelse
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
            <form action="{{ route('stock-transfer-book.certificates.store') }}" method="POST" class="h-full flex flex-col" autocomplete="off">
                @csrf
                <input type="hidden" name="stock_transfer_book_ledger_id" id="certificate_ledger_id">

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="text-lg font-semibold">New Certificate</div>
                    <div class="flex-1"></div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" @click="showAddPanel = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4">
                    <div class="relative z-[300]">
                        <label class="text-xs text-gray-600">Ledger / Shareholder</label>
                        <input type="text"
                               id="certificate_ledger_picker"
                               autocomplete="new-password"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                               placeholder="Search and select ledger">

                        <div id="certificate_ledger_suggestions"
                             class="hidden absolute left-0 right-0 top-full mt-1 z-[9999] rounded-md border border-gray-200 bg-white shadow-xl max-h-60 overflow-y-auto">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600">Date Uploaded</label>
                            <input type="date" name="date_uploaded" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Uploaded By</label>
                            <input type="text" name="uploaded_by" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Uploader name">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-600">Corporation Name</label>
                            <input type="text" name="corporation_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Corporation name">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Company Reg. No.</label>
                            <input type="text" name="company_reg_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="12345-ABC">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Stock Number</label>
                            <input type="text" name="stock_number" id="certificate_stock_number" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm" placeholder="Auto or enter manually">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-600">Name of Stockholder</label>
                            <input type="text" name="stockholder_name" id="certificate_stockholder_name" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">PAR</label>
                            <input type="text" name="par_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Number</label>
                            <input type="number" name="number" id="certificate_number" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm" placeholder="0">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Amount (PhP)</label>
                            <input type="number" step="0.01" name="amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100000.00">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Amount in words</label>
                            <input type="text" name="amount_in_words" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="One Hundred Thousand Pesos">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Date Issued</label>
                            <input type="date" name="date_issued" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">President</label>
                            <input type="text" name="president" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="President name">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Corporate Secretary</label>
                            <input type="text" name="corporate_secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Secretary name">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" type="button" @click="showAddPanel = false">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button id="save_certificate_btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" type="submit" disabled>
                        Save Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showVoucherPanel" class="fixed inset-0 bg-black/40 z-40" @click="showVoucherPanel = false"></div>
        <div x-show="showVoucherPanel"
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop>
            <template x-if="selectedVoucher">
                <form :action="`/corporate/stock-transfer-book/certificates/voucher/${selectedVoucher.id}/update`" method="POST" class="h-full flex flex-col">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="text-lg font-semibold">Edit Voucher</div>
                        <div class="flex-1"></div>
                        <button class="text-gray-500 hover:text-gray-700" type="button" @click="showVoucherPanel = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto space-y-4">
                        <div>
                            <label class="text-xs text-gray-600">Issued To</label>
                            <input type="text" name="issued_to" x-model="selectedVoucher.issued_to" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Issued To Type</label>
                            <select name="issued_to_type" x-model="selectedVoucher.issued_to_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="">Select type</option>
                                <option value="Stockholder">Stockholder</option>
                                <option value="Representative">Representative</option>
                                <option value="Buyer">Buyer</option>
                                <option value="Corporate Secretary">Corporate Secretary</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Certificate Released Date</label>
                            <input type="date" name="certificate_released_date" x-model="selectedVoucher.certificate_released_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                        <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" type="button" @click="showVoucherPanel = false">
                            Cancel
                        </button>
                        <div class="flex-1"></div>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                            Save Voucher
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showCancelPanel" class="fixed inset-0 bg-black/40 z-40" @click="showCancelPanel = false"></div>
        <div x-show="showCancelPanel"
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop>
            <template x-if="selectedCertificate">
                <form action="{{ route('stock-transfer-book.certificates.cancellation.store') }}" method="POST" class="h-full flex flex-col">
                    @csrf

                    <input type="hidden" name="stock_transfer_book_certificate_id" :value="selectedCertificate.id">

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="text-lg font-semibold">Cancel Certificate</div>
                        <div class="flex-1"></div>
                        <button class="text-gray-500 hover:text-gray-700" type="button" @click="showCancelPanel = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto space-y-4">
                        <div class="rounded-md bg-red-50 border border-red-200 px-3 py-3 text-sm text-red-700">
                            <div><strong>Certificate:</strong> <span x-text="selectedCertificate.stock_number"></span></div>
                            <div><strong>Stockholder:</strong> <span x-text="selectedCertificate.stockholder_name"></span></div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Date of Cancellation</label>
                            <input type="date" name="date_of_cancellation" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Effective Date</label>
                            <input type="date" name="effective_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Type of Cancellation</label>
                            <select name="type_of_cancellation" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="">Select type</option>
                                <option value="Delinquent">Delinquent</option>
                                <option value="Buy-back">Buy-back</option>
                                <option value="Redemption">Redemption</option>
                                <option value="Treasury Cancellation">Treasury Cancellation</option>
                                <option value="Capital Reduction">Capital Reduction</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Reason for Cancellation</label>
                            <textarea rows="4" name="reason" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter reason"></textarea>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Others Specify</label>
                            <input type="text" name="others_specify" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="If Others, specify here">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="Cancelled">Cancelled</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                        <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" type="button" @click="showCancelPanel = false">
                            Cancel
                        </button>
                        <div class="flex-1"></div>
                        <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg" type="submit">
                            Save Cancellation
                        </button>
                    </div>
                </form>
            </template>
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

            <form action="{{ route('stock-transfer-book.certificates.request.store') }}" method="POST" class="h-full flex flex-col" autocomplete="off">
                @csrf

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
                            <input type="text" name="ref_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Auto if blank">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Date Requested</label>
                            <input type="date" name="date_requested" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Time</label>
                            <input type="time" name="time_requested" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Type of Request</label>
                            <select name="type_of_request" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="">Select type</option>
                                <option value="New COS">New COS</option>
                                <option value="Loss COS">Loss COS</option>
                                <option value="Damage COS">Damage COS</option>
                                <option value="Digital Copy of COS">Digital Copy of COS</option>
                                <option value="Certified True Copy of CV">Certified True Copy of CV</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Requester</label>
                            <input type="text" name="requester" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Requester name">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Received By</label>
                            <input type="text" name="received_by" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Receiver name">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Issued By</label>
                            <input type="text" name="issued_by" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Issuer name">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="">Select status</option>
                                <option value="Review">Review</option>
                                <option value="Approved">Approved</option>
                                <option value="Released">Released</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showRequestPanel = false" type="button">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                        Save Request
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    const ledgerRecords = @json($ledgerRecords);

    const pickerInput = document.getElementById('certificate_ledger_picker');
    const suggestionBox = document.getElementById('certificate_ledger_suggestions');
    const ledgerIdInput = document.getElementById('certificate_ledger_id');
    const stockholderInput = document.getElementById('certificate_stockholder_name');
    const stockNumberInput = document.getElementById('certificate_stock_number');
    const numberInput = document.getElementById('certificate_number');
    const saveBtn = document.getElementById('save_certificate_btn');

    function clearCertificateSelection() {
        ledgerIdInput.value = '';
        stockholderInput.value = '';
        numberInput.value = '';
        saveBtn.disabled = true;
    }

    function hideSuggestions() {
        suggestionBox.classList.add('hidden');
        suggestionBox.innerHTML = '';
    }

    function fillCertificateLedger(item) {
        pickerInput.value = item.shareholder || '';
        ledgerIdInput.value = item.id || '';
        stockholderInput.value = item.shareholder || '';
        numberInput.value = item.number_of_shares || '';
        if (!stockNumberInput.value) {
            stockNumberInput.value = item.certificate_no || '';
        }
        saveBtn.disabled = false;
        hideSuggestions();
    }

    function renderCertificateSuggestions(query = '') {
        const q = query.trim().toLowerCase();

        const filtered = ledgerRecords.filter(item => {
            const full = `${item.shareholder || ''} ${item.certificate_no || ''}`.toLowerCase();
            return q === '' || full.includes(q);
        });

        suggestionBox.innerHTML = '';

        if (!filtered.length) {
            suggestionBox.innerHTML = `<div class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100">No ledger record found.</div>`;
            suggestionBox.classList.remove('hidden');
            return;
        }

        filtered.forEach(item => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'w-full text-left px-3 py-3 hover:bg-gray-50 border-b border-gray-100 text-sm';

            option.innerHTML = `
                <div class="font-medium text-gray-900">${item.shareholder || ''}</div>
                <div class="text-xs text-gray-500 mt-0.5">${item.certificate_no || ''}${item.number_of_shares ? ' • ' + item.number_of_shares + ' shares' : ''}</div>
            `;

            option.addEventListener('mousedown', (e) => {
                e.preventDefault();
                fillCertificateLedger(item);
            });

            suggestionBox.appendChild(option);
        });

        suggestionBox.classList.remove('hidden');
    }

    if (pickerInput) {
        pickerInput.addEventListener('focus', function () {
            renderCertificateSuggestions(this.value);
        });

        pickerInput.addEventListener('input', function () {
            clearCertificateSelection();
            renderCertificateSuggestions(this.value);
        });

        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== pickerInput) {
                hideSuggestions();
            }
        });
    }
</script>
@endsection