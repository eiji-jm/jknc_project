@extends('layouts.app')
@section('title', 'Stock Transfer Book - Certificates')

@php($currentUser = auth()->user()?->name ?? '')
@php($stockNumberOptions = collect($availableStockNumbers ?? collect())->merge(collect($availableInstallments ?? collect())->pluck('stock_number'))->filter()->unique()->values())
@php($stockNumberDirectory = collect($stockNumberDirectory ?? collect()))
@php($requestPanelFields = ['reference_no', 'requested_at', 'request_type', 'issuance_type', 'requester', 'received_by', 'issued_by', 'certificate_id', 'notes', 'document_path'])
@php($requestPanelHasErrors = collect($requestPanelFields)->contains(fn ($field) => $errors->has($field)))

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false, showRequestPanel: @js($requestPanelHasErrors), activeTab: @js($requestPanelHasErrors ? 'requests' : 'stock') }" @keydown.escape.window="showAddPanel = false; showRequestPanel = false">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Certificates</div>
        </div>

        <div class="border-t border-gray-100"></div>

        @include('corporate.stock-transfer-book.partials.section-tabs', ['currentStockTransferTab' => 'certificates'])

        <div class="px-4 py-3 border-b border-gray-100 flex gap-4">
            <button type="button" class="text-sm font-medium pb-2 px-1" :class="activeTab === 'stock' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'stock'">Certificate Stock</button>
            <button type="button" class="text-sm font-medium pb-2 px-1" :class="activeTab === 'voucher' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'voucher'">Certificate Voucher</button>
            <button type="button" class="text-sm font-medium pb-2 px-1" :class="activeTab === 'requests' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'" @click="activeTab = 'requests'">Request for Issuance</button>
        </div>

        <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <input type="text" id="certificate-search" placeholder="Search current tab..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>

        <div x-show="activeTab === 'stock'" class="p-4">
            <div class="flex items-center gap-3 mb-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Certificate Stock</div>
                    <div class="text-xs text-gray-500">Create stock records from existing stock numbers.</div>
                </div>
                <div class="flex-1"></div>
                <button type="button" data-open-add-panel @click="showAddPanel = true; activeTab = 'stock'" class="px-3 py-1.5 text-xs font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-plus text-[10px]"></i>
                    New Certificate Stock
                </button>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stockholder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Issued</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody id="certificate-stock-body" class="text-sm text-gray-900">
                        @forelse ($certificateStocks as $certificate)
                            <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('stock-transfer-book.certificates.show', $certificate) }}'">
                                <td class="px-4 py-3">{{ $certificate->certificate_type ?: 'COS' }}</td>
                                <td class="px-4 py-3">{{ $certificate->stock_number }}</td>
                                <td class="px-4 py-3">{{ $certificate->stockholder_name }}</td>
                                <td class="px-4 py-3">{{ optional($certificate->date_issued)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $certificate->number }}</td>
                                <td class="px-4 py-3">{{ $certificate->amount }}</td>
                                <td class="px-4 py-3">{{ ucfirst($certificate->status ?: 'active') }}</td>
                            </tr>
                        @empty
                            <tr data-empty-row>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No certificate stock records found.</td>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Source Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Issued To</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Issued To Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Released</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="certificate-voucher-body" class="text-sm text-gray-900">
                        @forelse ($certificateVouchers as $voucher)
                            <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $voucher->certificate_type ?: 'COS' }}</td>
                                <td class="px-4 py-3">{{ $voucher->sourceCertificate?->stock_number ?: $voucher->stock_number }}</td>
                                <td class="px-4 py-3">{{ $voucher->issued_to ?: $voucher->stockholder_name }}</td>
                                <td class="px-4 py-3">{{ $voucher->issued_to_type ?: 'Stockholder' }}</td>
                                <td class="px-4 py-3">{{ optional($voucher->released_at)->format('M d, Y h:i A') ?: optional($voucher->date_issued)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ ucfirst($voucher->status ?: 'released') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('stock-transfer-book.certificates.show', $voucher) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No certificate vouchers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="activeTab === 'requests'" class="p-4">
            <div class="flex items-center gap-3 mb-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Request for Issuance</div>
                    <div class="text-xs text-gray-500">Create and track COS/CV issuance requests.</div>
                </div>
                <div class="flex-1"></div>
                <button type="button" data-open-request-panel @click="showRequestPanel = true; activeTab = 'requests'" class="px-3 py-1.5 text-xs font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700">
                    Add Request
                </button>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ref #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Requested</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Request Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">COS/CV</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Requester</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Received By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="certificate-request-body" class="text-sm text-gray-900">
                        @forelse ($issuanceRequests as $requestRecord)
                            <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $requestRecord->reference_no }}</td>
                                <td class="px-4 py-3">{{ optional($requestRecord->requested_at)->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3">{{ $requestRecord->request_type }}</td>
                                <td class="px-4 py-3">{{ $requestRecord->issuance_type }}</td>
                                <td class="px-4 py-3">{{ $requestRecord->requester }}</td>
                                <td class="px-4 py-3">{{ $requestRecord->received_by }}</td>
                                <td class="px-4 py-3">{{ ucfirst($requestRecord->status) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('stock-transfer-book.certificates.requests.show', $requestRecord) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Preview</a>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No issuance requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel" data-add-panel class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col" x-transition @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">New Certificate Stock</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.certificates.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Certificate Type</label>
                        <select name="certificate_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="COS">COS</option>
                            <option value="CV">CV</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Uploaded</label>
                        <input type="date" name="date_uploaded" data-default-field="today" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input type="text" name="uploaded_by" value="{{ $currentUser }}" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Stock Number</label>
                        <input type="text" name="stock_number" list="certificate-stock-numbers" x-ref="certificateStockNumber" data-certificate-stock-key class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Select or type stock number">
                        @if ($stockNumberDirectory->isNotEmpty())
                            <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-2">
                                <div class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Available Stock Numbers</div>
                                <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
                                    @foreach ($stockNumberDirectory as $stockOption)
                                        <button
                                            type="button"
                                            class="flex w-full items-start justify-between gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50"
                                            data-stock-number-option="{{ $stockOption->stock_number }}"
                                            @click.prevent="$refs.certificateStockNumber.value = '{{ $stockOption->stock_number }}'; window.syncCertificateStockFields($refs.certificateStockNumber)"
                                        >
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">{{ $stockOption->stock_number }}</div>
                                                <div class="truncate text-xs text-gray-600">{{ $stockOption->holder_name ?: 'No stockholder linked yet' }}</div>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <div class="text-[11px] font-medium text-gray-700">{{ $stockOption->source }}</div>
                                                <div class="text-[11px] {{ $stockOption->is_recommended ? 'text-green-600' : 'text-amber-600' }}">{{ $stockOption->status_label }}</div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Name of Stockholder</label>
                        <input type="text" name="stockholder_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Auto-filled from stock number or enter manually">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Corporation Name</label>
                        <input type="text" name="corporation_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Based on contact / manual entry">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Company Reg. No.</label>
                        <input type="text" name="company_reg_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Based on contact / manual entry">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">PAR</label>
                        <input type="number" step="0.01" name="par_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Number of Shares</label>
                        <input type="number" name="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Amount in Words</label>
                        <input type="text" name="amount_in_words" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Auto-filled from stock number or enter manually">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Issued</label>
                        <input type="date" name="date_issued" data-default-field="today" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">President</label>
                        <input type="text" name="president" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Corporate Secretary</label>
                        <input type="text" name="corporate_secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload Document (PDF)</label>
                        <input type="file" name="document_path" class="mt-1 block w-full text-sm text-gray-600">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">Cancel</button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">Save Certificate</button>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showRequestPanel" class="fixed inset-0 bg-black/40 z-40" @click="showRequestPanel = false"></div>
        <div x-show="showRequestPanel" data-request-panel class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col" x-transition @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Add Request for Issuance</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showRequestPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.certificates.requests.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                @if ($requestPanelHasErrors)
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <div class="font-semibold">Request could not be saved.</div>
                        <div class="mt-1">{{ $errors->first() }}</div>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Ref #</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $nextIssuanceRequestReference ?? 'REQ-0001') }}" data-default-field="reference_no" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date and Time</label>
                        <input type="datetime-local" name="requested_at" value="{{ old('requested_at', $defaultRequestedAt ?? now()->format('Y-m-d\\TH:i')) }}" data-default-field="now" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Type of Request</label>
                        <select name="request_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            @foreach (['New COS', 'Loss COS', 'Damage COS', 'Digital Copy of COS', 'Certified True Copy of CV'] as $requestTypeOption)
                                <option value="{{ $requestTypeOption }}" @selected(old('request_type') === $requestTypeOption)>{{ $requestTypeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Choose COS/CV</label>
                        <select name="issuance_type" data-issuance-type class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="COS" @selected(old('issuance_type', 'COS') === 'COS')>COS</option>
                            <option value="CV" @selected(old('issuance_type') === 'CV')>CV</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Requester</label>
                        <input type="text" name="requester" list="index-shareholders" value="{{ old('requester') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Choose from index or enter manually">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Received By</label>
                        <input type="text" name="received_by" value="{{ old('received_by', $currentUser) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Issued By</label>
                        <input type="text" name="issued_by" value="{{ old('issued_by', $currentUser) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Certificate Stock</label>
                        <select name="certificate_id" data-certificate-stock class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select certificate stock</option>
                            @foreach ($certificateStocks as $certificate)
                                <option value="{{ $certificate->id }}" data-type="{{ $certificate->certificate_type ?: 'COS' }}" @selected((string) old('certificate_id') === (string) $certificate->id)>
                                    {{ $certificate->certificate_type ?: 'COS' }} - {{ $certificate->stock_number }} - {{ $certificate->stockholder_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Optional request notes">{{ old('notes') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload Request File (PDF)</label>
                        <input type="file" name="document_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600">
                        <div class="mt-1 text-[11px] text-gray-500">Attach the scanned request or supporting request PDF. If blank, the system preview will generate a request sheet automatically.</div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showRequestPanel = false" type="button">Cancel</button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">Save Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<datalist id="certificate-stock-numbers">
    @foreach ($stockNumberOptions as $stockNumberOption)
        <option value="{{ $stockNumberOption }}"></option>
    @endforeach
</datalist>

<datalist id="index-shareholders">
    @foreach (($indexShareholders ?? collect()) as $name)
        <option value="{{ $name }}"></option>
    @endforeach
</datalist>

<script>
    window.syncCertificateStockFields = async function (stockInput) {
        if (!stockInput) return;

        const form = stockInput.closest('form');
        if (!form) return;

        const setField = (name, value, allowBlank = false) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (!field) return;
            if (!allowBlank && (value === undefined || value === null || value === '')) return;
            field.value = value ?? '';
        };

        const preservedDateUploaded = form.querySelector('[name="date_uploaded"]')?.value || '';
        const preservedDateIssued = form.querySelector('[name="date_issued"]')?.value || '';

        const key = (stockInput.value || '').trim();
        setField('stock_number', key, true);
        if (!key) {
            setField('date_uploaded', preservedDateUploaded, true);
            setField('date_issued', preservedDateIssued, true);
            return;
        }

        try {
            const res = await fetch(`{{ route('stock-transfer-book.lookup') }}?key=${encodeURIComponent(key)}`);
            if (!res.ok) return;
            const data = await res.json();
            const ledger = data.ledger || {};
            const cert = data.certificate || {};
            const installment = data.installment || {};
            const stockholderRecord = data.stockholder_record || {};
            const company = data.company || {};

            setField('stockholder_name', stockholderRecord.stockholder_name || installment.holder_name || installment.subscriber || cert.stockholder_name || ledger.full_name || '', true);
            setField('par_value', installment.par_value || cert.par_value || company.par_value || '', true);
            setField('number', installment.no_shares || cert.number || ledger.shares || '', true);
            setField('amount', installment.total_value || company.computed_amount || cert.amount || stockholderRecord.amount || '', true);
            setField('amount_in_words', installment.amount_in_words || company.computed_amount_in_words || cert.amount_in_words || '', true);
            setField('date_uploaded', installment.installment_date || cert.date_uploaded || preservedDateUploaded, true);
            setField('date_issued', installment.installment_date || cert.date_issued || preservedDateIssued, true);
        } catch (error) {
            // Ignore lookup enrichment failures and allow manual certificate creation.
        }
    };

    (function () {
        const searchInput = document.getElementById('certificate-search');
        const bodies = [
            document.getElementById('certificate-stock-body'),
            document.getElementById('certificate-voucher-body'),
            document.getElementById('certificate-request-body'),
        ];

        if (!searchInput) return;

        const filterBody = (body, query) => {
            if (!body) return;
            const rows = Array.from(body.querySelectorAll('[data-search-row]'));
            const emptyRow = body.querySelector('[data-empty-row]');
            let visibleCount = 0;

            rows.forEach((row) => {
                const matches = query === '' || row.textContent.toLowerCase().includes(query);
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount += 1;
            });

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 || visibleCount === 0 ? '' : 'none';
            }
        };

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            bodies.forEach((body) => filterBody(body, query));
        });
    })();

    (function () {
        const defaultsEndpoint = "{{ route('stock-transfer-book.defaults') }}";
        const container = document;
        const stockSelect = container.querySelector('[data-certificate-stock]');
        const issuanceType = container.querySelector('[data-issuance-type]');
        const addPanel = container.querySelector('[data-add-panel]');
        const requestPanel = container.querySelector('[data-request-panel]');
        const addStockNumberInput = addPanel?.querySelector('[data-certificate-stock-key]');
        const stockNumberButtons = Array.from(container.querySelectorAll('[data-stock-number-option]'));
        const requesterInput = requestPanel?.querySelector('[name="requester"]');
        const requestReceivedBy = requestPanel?.querySelector('[name="received_by"]');
        const requestIssuedBy = requestPanel?.querySelector('[name="issued_by"]');
        const currentUser = @json($currentUser);

        const lookup = async (value) => {
            const key = (value || '').trim();
            if (!key) return null;

            try {
                const res = await fetch(`{{ route('stock-transfer-book.lookup') }}?key=${encodeURIComponent(key)}`);
                if (!res.ok) return null;
                return await res.json();
            } catch (error) {
                return null;
            }
        };

        const applyDefaults = async (panel) => {
            if (!panel) return;

            panel.querySelectorAll('[data-default-field]').forEach((field) => {
                const key = field.getAttribute('data-default-field');
                if (key === 'today' && !field.value) {
                    field.value = new Date().toISOString().split('T')[0];
                }
            });

            try {
                const res = await fetch(defaultsEndpoint);
                if (!res.ok) return;
                const defaults = await res.json();
                panel.querySelectorAll('[data-default-field]').forEach((field) => {
                    const key = field.getAttribute('data-default-field');
                    if (key && defaults[key]) {
                        field.value = defaults[key];
                    }
                });
            } catch (error) {
                // ignore defaults errors
            }
        };

        const filterStockOptions = () => {
            if (!stockSelect || !issuanceType) return;
            const selectedType = issuanceType.value;
            let firstVisibleValue = '';
            let currentValueStillVisible = false;

            Array.from(stockSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const matches = option.dataset.type === selectedType;
                option.hidden = !matches;
                if (matches && option.value === stockSelect.value) {
                    currentValueStillVisible = true;
                }
                if (matches && !firstVisibleValue) {
                    firstVisibleValue = option.value;
                }
            });

            if (currentValueStillVisible) {
                return;
            }

            if (firstVisibleValue) {
                stockSelect.value = firstVisibleValue;
            } else {
                stockSelect.value = '';
            }
        };

        issuanceType?.addEventListener('change', filterStockOptions);
        filterStockOptions();

        const resetCertificateAddPanel = () => {
            if (!addPanel) return;

            addPanel.querySelector('form')?.reset();

            const stockNumberField = addPanel.querySelector('[name="stock_number"]');
            if (stockNumberField) {
                stockNumberField.value = '';
            }

            [
                'stockholder_name',
                'par_value',
                'number',
                'amount',
                'amount_in_words',
            ].forEach((name) => {
                const field = addPanel.querySelector(`[name="${name}"]`);
                if (field) {
                    field.value = '';
                }
            });
        };

        container.querySelector('[data-open-add-panel]')?.addEventListener('click', () => {
            resetCertificateAddPanel();
            applyDefaults(addPanel);
        });

        container.querySelector('[data-open-request-panel]')?.addEventListener('click', () => {
            requestPanel?.querySelector('form')?.reset();
            applyDefaults(requestPanel);
            if (requestReceivedBy) {
                requestReceivedBy.value = currentUser;
            }
            if (requestIssuedBy) {
                requestIssuedBy.value = currentUser;
            }
            filterStockOptions();
        });

        const syncCertificateAddPanel = async (value) => {
            const data = await lookup(value);
            if (!data || !addPanel) return;

            const ledger = data.ledger || {};
            const cert = data.certificate || {};
            const installment = data.installment || {};
            const stockholderRecord = data.stockholder_record || {};
            const company = data.company || {};

            const setField = (name, value) => {
                const field = addPanel.querySelector(`[name="${name}"]`);
                if (field && value !== undefined && value !== null && value !== '') {
                    field.value = value;
                }
            };

            // Prefer installment data for fields that directly correspond to the
            // subscriber's stock subscription record, then fall back to existing
            // certificate data or the index record.
            setField('stock_number', installment.stock_number || cert.stock_number || ledger.certificate_no || '');
            setField('stockholder_name', stockholderRecord.stockholder_name || installment.holder_name || installment.subscriber || cert.stockholder_name || ledger.full_name || '');
            setField('number', installment.no_shares || cert.number || ledger.shares || '');
            setField('amount', installment.total_value || company.computed_amount || cert.amount || stockholderRecord.amount || '');
            setField('date_uploaded', installment.installment_date || cert.date_uploaded || addPanel.querySelector('[name="date_uploaded"]')?.value || '');
            setField('date_issued', installment.installment_date || cert.date_issued || addPanel.querySelector('[name="date_issued"]')?.value || '');

            setField('par_value', installment.par_value || cert.par_value || company.par_value || '');
            setField('amount_in_words', installment.amount_in_words || company.computed_amount_in_words || cert.amount_in_words || '');
        };

        const syncCertificateAddPanelFromOption = () => {
            if (!addStockNumberInput) return;
            window.syncCertificateStockFields(addStockNumberInput);
        };

        addStockNumberInput?.addEventListener('change', syncCertificateAddPanelFromOption);
        addStockNumberInput?.addEventListener('input', syncCertificateAddPanelFromOption);
        addStockNumberInput?.addEventListener('blur', syncCertificateAddPanelFromOption);

        stockNumberButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                if (!addStockNumberInput) return;
                addStockNumberInput.value = button.dataset.stockNumberOption || '';
                await syncCertificateAddPanelFromOption();
            });
        });

        const syncRequestPanelFromRequester = async () => {
            if (!requesterInput) return;

            const data = await lookup(requesterInput.value);
            const cert = data?.certificate || {};
            const installment = data?.installment || {};
            if (!requestPanel) return;

            const preferredStockNumber = cert.stock_number || installment.stock_number || '';
            if (preferredStockNumber && stockSelect) {
                const matchingOption = Array.from(stockSelect.options).find((option) => {
                    return !option.hidden && option.textContent.includes(preferredStockNumber);
                });
                if (matchingOption && !matchingOption.hidden) {
                    stockSelect.value = matchingOption.value;
                }
            }
        };

        requesterInput?.addEventListener('change', syncRequestPanelFromRequester);
        requesterInput?.addEventListener('blur', syncRequestPanelFromRequester);
    })();
</script>
