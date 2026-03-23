@extends('layouts.app')

@section('content')
@php
    $draftUrl = $tax->document_path ? route('uploads.show', ['path' => $tax->document_path]) : ($generatedDraftUrl ?? null);
    $approvedUrl = $tax->approved_document_path ? route('uploads.show', ['path' => $tax->approved_document_path]) : null;
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">BIR & Tax Preview</div>
                <div class="text-xs text-gray-500">TIN {{ $tax->tin ?? '-' }}</div>
            </div>
            <div class="flex-1"></div>
            <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Delete this BIR & Tax entry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Delete</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Draft BIR & Tax File</div>
                            <div class="text-xs text-slate-500">The internal draft uploaded from the slider appears here.</div>
                        </div>
                    </div>
                    @if ($draftUrl)
                        <iframe src="{{ $draftUrl }}" class="mt-4 w-full h-[700px] border rounded bg-white"></iframe>
                    @else
                        <div class="mt-4 w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">No draft file available yet.</div>
                    @endif
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Approved BIR & Tax File</div>
                            <div class="text-xs text-slate-500">Upload now from the slider or add the approved PDF later from this preview.</div>
                        </div>
                    </div>
                    @if ($approvedUrl)
                        <iframe src="{{ $approvedUrl }}" class="mt-4 w-full h-[700px] border rounded bg-white"></iframe>
                    @else
                        <div class="mt-4 w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">No approved file uploaded yet.</div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">BIR & Tax Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900">{{ $tax->tin ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Tax Payer</span><div class="font-medium text-gray-900">{{ $tax->tax_payer ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registering Office</span><div class="font-medium text-gray-900">{{ $tax->registering_office ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registered Address</span><div class="font-medium text-gray-900">{{ $tax->registered_address ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Form Type</span><div class="font-medium text-gray-900">{{ $tax->form_type ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Deadline</span><div class="font-medium text-gray-900">{{ optional($tax->due_date)->format('M d, Y') ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Uploaded</span><div class="font-medium text-gray-900">{{ optional($tax->date_uploaded)->format('M d, Y') ?? '-' }}</div></div>
                    </div>
                </div>

                <div class="bg-white border border-emerald-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900">Upload Approved File Later</div>
                    <div class="mt-1 text-xs text-gray-500">Use this when the approved BIR & Tax PDF becomes available after the draft was already saved.</div>
                    <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        @method('PUT')
                        <input type="file" name="approved_document_path" accept="application/pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            {{ $tax->approved_document_path ? 'Update Approved File' : 'Upload Approved File' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
