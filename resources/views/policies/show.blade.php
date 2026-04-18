@extends('layouts.app')

@section('content')
<div class="bg-[#f5f6f8] min-h-screen p-6">
    <div class="max-w-[1400px] mx-auto flex gap-6">

        {{-- LEFT SIDE --}}
        <div class="w-[70%] h-[calc(100vh-80px)] overflow-y-auto pr-2">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('policies.index') }}"
                   class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                    ← Back
                </a>
            </div>

            <div class="policy-preview bg-white border border-gray-300 shadow px-[72px] py-[72px] mb-6">

                <div class="flex justify-between border-b pb-6 mb-8">
                    <div>
                        <h1 class="text-[22px] font-bold">JOHN KELLY & COMPANY</h1>
                        <p class="text-[12px] text-gray-500">Corporate Policy</p>
                    </div>

                    <div class="text-right text-sm">
                        <p>Code: <b>{{ $policy->code ?? 'AUTO-GENERATED' }}</b></p>
                        <p>Effectivity Date: <b>{{ $policy->effectivity_date ?? '-' }}</b></p>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-[20px] font-bold tracking-[0.2em] uppercase">
                        {{ $policy->policy ?? 'POLICY DOCUMENT' }}
                    </h2>
                </div>

                <div class="space-y-3 text-sm mb-10">
                    <div class="grid grid-cols-[140px_1fr]">
                        <b>Version</b>
                        <span class="border-b">{{ $policy->version ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-[140px_1fr]">
                        <b>Prepared By</b>
                        <span class="border-b">{{ $policy->prepared_by ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-[140px_1fr]">
                        <b>Reviewed By</b>
                        <span class="border-b">{{ $policy->reviewed_by ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-[140px_1fr]">
                        <b>Approved By</b>
                        <span class="border-b">{{ $policy->approved_by ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-[140px_1fr]">
                        <b>Classification</b>
                        <span class="border-b">{{ $policy->classification ?? '-' }}</span>
                    </div>
                </div>

                <div class="text-[15px] leading-8 min-h-[300px] policy-preview-body">
                    {!! $policy->description ?? '<p class="text-gray-400">No description provided.</p>' !!}
                </div>

                @if($policy->attachment)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Attachment Preview</h3>

                        <div class="border rounded-lg overflow-hidden bg-white">
                            @php
                                $ext = strtolower(pathinfo($policy->attachment, PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/'.$policy->attachment) }}" class="w-full">
                            @elseif($ext === 'pdf')
                                <iframe src="{{ asset('storage/'.$policy->attachment) }}" class="w-full h-[500px]"></iframe>
                            @else
                                <div class="p-4 text-center">
                                    <a href="{{ asset('storage/'.$policy->attachment) }}"
                                       target="_blank"
                                       class="text-blue-600 underline">
                                        Download Attachment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT SIDE PANEL --}}
        <div class="w-[30%]">
            <div class="bg-white border rounded-xl shadow p-5 sticky top-6 space-y-4">

                <h3 class="font-semibold text-lg">Policy Details</h3>

                <div class="text-sm space-y-3">
                    <div>
                        <p class="text-gray-500 text-xs">Code</p>
                        <p>{{ $policy->code ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Policy Title</p>
                        <p>{{ $policy->policy ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Version</p>
                        <p>{{ $policy->version ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Effectivity Date</p>
                        <p>{{ $policy->effectivity_date ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Prepared By</p>
                        <p>{{ $policy->prepared_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Reviewed By</p>
                        <p>{{ $policy->reviewed_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Approved By</p>
                        <p>{{ $policy->approved_by ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Classification</p>
                        <p>{{ $policy->classification ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Approval Status</p>
                        <p>{{ $policy->approval_status ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-xs">Workflow Status</p>
                        <p>{{ $policy->workflow_status ?? '-' }}</p>
                    </div>

                    @if($policy->review_note)
                        <div>
                            <p class="text-gray-500 text-xs">Review Note</p>
                            <p>{{ $policy->review_note }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-gray-500 text-xs">Attachment</p>
                        @if($policy->attachment)
                            <a href="{{ asset('storage/' . $policy->attachment) }}"
                               target="_blank"
                               class="text-blue-600 hover:underline">
                                View Attachment
                            </a>
                        @else
                            <p>-</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('policies.preview', [
                        'policy' => $policy->policy,
                        'version' => $policy->version,
                        'effectivity_date' => $policy->effectivity_date,
                        'prepared_by' => $policy->prepared_by,
                        'reviewed_by' => $policy->reviewed_by,
                        'approved_by' => $policy->approved_by,
                        'classification' => $policy->classification,
                        'description' => $policy->description,
                    ]) }}"
                   target="_blank"
                   class="block text-center bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    Download PDF
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .policy-preview {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .policy-preview-body table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        margin: 12px 0 !important;
    }

    .policy-preview-body table colgroup,
    .policy-preview-body table col {
        width: auto !important;
    }

    .policy-preview-body th,
    .policy-preview-body td {
        min-width: 0 !important;
        border: 1px solid #94a3b8 !important;
        padding: 10px 12px !important;
        vertical-align: top !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        white-space: normal !important;
        background: #fff !important;
    }

    .policy-preview-body th {
        background: #f8fafc !important;
        font-weight: 600 !important;
    }

    .policy-preview-body p,
    .policy-preview-body li,
    .policy-preview-body span,
    .policy-preview-body div {
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .policy-preview-body h1,
    .policy-preview-body h2,
    .policy-preview-body h3 {
        line-height: 1.35;
        margin: 0.75rem 0;
    }

    .policy-preview-body ul,
    .policy-preview-body ol {
        padding-left: 1.5rem;
    }
</style>
@endpush
