@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5" x-data="{ activeTab: 'transmittal' }">
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[28px] font-semibold text-gray-800 leading-none">Transmittal Preview</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $transmittal->transmittal_no ?? 'N/A' }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url('/transmittal') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Back
                </a>
            </div>
        </div>

        <div class="p-5 bg-[#f8fafc]">
            <div class="grid grid-cols-1 2xl:grid-cols-[1fr_420px] gap-5">
                <div class="space-y-5">

                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <div class="px-4 pt-3 border-b border-gray-200 bg-white">
                            <div class="flex items-end gap-6">
                                <button
                                    type="button"
                                    @click="activeTab = 'transmittal'"
                                    :class="activeTab === 'transmittal'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Transmittal
                                </button>

                                <button
                                    type="button"
                                    @click="activeTab = 'receipt'"
                                    :class="activeTab === 'receipt'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Receipt
                                </button>

                                <button
                                    type="button"
                                    @click="activeTab = 'attachments'"
                                    :class="activeTab === 'attachments'
                                        ? 'text-blue-600 border-b-2 border-blue-600'
                                        : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700'"
                                    class="pb-3 text-sm font-medium transition">
                                    Attachments
                                </button>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-b border-gray-200 bg-white flex items-center justify-between">
                            <div x-show="activeTab === 'transmittal'">
                                <h3 class="text-sm font-semibold text-gray-900">Transmittal Form Preview</h3>
                                <p class="text-xs text-gray-500">{{ ucfirst($transmittal->workflow_status ?? 'N/A') }}</p>
                            </div>

                            <div x-show="activeTab === 'receipt'" x-cloak>
                                <h3 class="text-sm font-semibold text-gray-900">Receipt Preview</h3>
                                <p class="text-xs text-gray-500">{{ optional($transmittal->receipt)->receipt_no ?? 'No receipt number yet' }}</p>
                            </div>

                            <div x-show="activeTab === 'attachments'" x-cloak>
                                <h3 class="text-sm font-semibold text-gray-900">Item Attachments</h3>
                                <p class="text-xs text-gray-500">Uploaded files from transmittal items</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <a x-show="activeTab === 'transmittal'"
                                   href="{{ $transmittalPdfUrl }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                    Open
                                </a>

                                @if(!empty($receiptPdfUrl))
                                    <a x-show="activeTab === 'receipt'"
                                       x-cloak
                                       href="{{ $receiptPdfUrl }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                        Open
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-[#f3f4f6]">
                            <div x-show="activeTab === 'transmittal'">
                                <div class="pdf-preview-frame">
                                    <iframe
                                        src="{{ $transmittalPdfUrl }}"
                                        class="w-full h-[920px] bg-white"
                                        frameborder="0">
                                    </iframe>
                                </div>
                            </div>

                            <div x-show="activeTab === 'receipt'" x-cloak>
                                @if(!empty($receiptPdfUrl))
                                    <div class="pdf-preview-frame">
                                        <iframe
                                            src="{{ $receiptPdfUrl }}"
                                            class="w-full h-[920px] bg-white"
                                            frameborder="0">
                                        </iframe>
                                    </div>
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center text-sm text-gray-500">
                                        Receipt will appear here once the transmittal is approved.
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'attachments'" x-cloak>
                                @php
                                    $attachmentItems = $transmittal->items->filter(fn($item) => !empty($item->attachment_path));
                                @endphp

                                @if($attachmentItems->count())
                                    <div class="space-y-3">
                                        @foreach($attachmentItems as $item)
                                            <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 flex items-center justify-between gap-4">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $item->particular ?: 'Item ' . $item->no }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ basename($item->attachment_path) }}
                                                    </p>
                                                    @if(!empty($item->remarks))
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            Remarks: {{ $item->remarks }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <a href="{{ asset('storage/' . $item->attachment_path) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shrink-0">
                                                    Open
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center text-sm text-gray-500">
                                        No item attachments uploaded yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Transmittal Information</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Ref No</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->transmittal_no ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Date</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->transmittal_date ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Mode</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->mode ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">From</span>
                                <span class="font-medium text-gray-900">
                                    {{ $transmittal->mode === 'SEND' ? ($transmittal->office_name ?? 'N/A') : ($transmittal->party_name ?? 'N/A') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">To</span>
                                <span class="font-medium text-gray-900">
                                    {{ $transmittal->mode === 'SEND' ? ($transmittal->party_name ?? 'N/A') : ($transmittal->office_name ?? 'N/A') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Address</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->address ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Delivery Type</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->delivery_type ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Recipient Email</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->recipient_email ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Workflow</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->workflow_status ?? 'N/A' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Approval</span>
                                <span class="font-medium text-gray-900">{{ $transmittal->approval_status ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Receipt Information</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Receipt No</span>
                                <span class="font-medium text-gray-900">{{ optional($transmittal->receipt)->receipt_no ?? 'Not yet generated' }}</span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Receipt File</span>
                                <span class="font-medium text-gray-900">{{ !empty($receiptPdfUrl) ? 'Available' : 'Not available' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl bg-white p-5">
                        <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Attachment Summary</h3>

                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">With Files</span>
                                <span class="font-medium text-gray-900">
                                    {{ $transmittal->items->whereNotNull('attachment_path')->count() }}
                                </span>
                            </div>
                            <div class="grid grid-cols-[110px_1fr] gap-3">
                                <span class="text-gray-500">Total Items</span>
                                <span class="font-medium text-gray-900">
                                    {{ $transmittal->items->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .pdf-preview-frame {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
</style>
@endsection