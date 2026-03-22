@extends('layouts.app')

@section('content')
@php
    $draftUrl = $natgov->document_path ? route('uploads.show', ['path' => $natgov->document_path]) : null;
    $draftDownloadUrl = $natgov->document_path ? route('uploads.show', ['path' => $natgov->document_path, 'download' => 1]) : null;
    $approvedUrl = $natgov->approved_document_path ? route('uploads.show', ['path' => $natgov->approved_document_path]) : null;
    $approvedDownloadUrl = $natgov->approved_document_path ? route('uploads.show', ['path' => $natgov->approved_document_path, 'download' => 1]) : null;
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">NatGov Preview</div>
                <div class="text-xs text-gray-500">Client {{ $natgov->client ?? '-' }}</div>
            </div>
            <div class="flex-1"></div>
            <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Delete this NatGov entry?');">
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
                            <div class="text-sm font-semibold text-slate-900">Draft NatGov File</div>
                            <div class="text-xs text-slate-500">The internal draft uploaded from the slider appears here.</div>
                        </div>
                        @if ($draftDownloadUrl)
                            <a href="{{ $draftDownloadUrl }}" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Download Draft</a>
                        @endif
                    </div>
                    <div class="mt-4 document-frame">
                        <div class="document-frame__toolbar">
                            <div class="document-frame__tools">
                                <span class="document-frame__chip"><i class="fas fa-file-pdf"></i> Draft Preview</span>
                                <span class="document-frame__chip">{{ $natgov->client ?? 'NatGov File' }}</span>
                            </div>
                            <div class="document-frame__actions">
                                <i class="fas fa-search"></i>
                                <i class="far fa-copy"></i>
                                <i class="fas fa-print"></i>
                            </div>
                        </div>
                        <div class="document-frame__body">
                        @if ($draftUrl)
                            <iframe src="{{ $draftUrl }}" class="document-frame__embed"></iframe>
                        @else
                            <div class="document-frame__empty">No draft file uploaded yet.</div>
                        @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Approved NatGov File</div>
                            <div class="text-xs text-slate-500">Upload now from the slider or add the approved PDF later from this preview.</div>
                        </div>
                        @if ($approvedDownloadUrl)
                            <a href="{{ $approvedDownloadUrl }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Download Approved</a>
                        @endif
                    </div>
                    <div class="mt-4 document-frame">
                        <div class="document-frame__toolbar">
                            <div class="document-frame__tools">
                                <span class="document-frame__chip"><i class="fas fa-file-signature"></i> Approved Preview</span>
                                <span class="document-frame__chip">{{ $natgov->client ?? 'Approved File' }}</span>
                            </div>
                            <div class="document-frame__actions">
                                <i class="fas fa-search"></i>
                                <i class="far fa-copy"></i>
                                <i class="fas fa-print"></i>
                            </div>
                        </div>
                        <div class="document-frame__body">
                        @if ($approvedUrl)
                            <iframe src="{{ $approvedUrl }}" class="document-frame__embed"></iframe>
                        @else
                            <div class="document-frame__empty">No approved file uploaded yet.</div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">NatGov Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Client</span><div class="font-medium text-gray-900">{{ $natgov->client ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900">{{ $natgov->tin ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Agency</span><div class="font-medium text-gray-900">{{ $natgov->agency ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Status</span><div class="font-medium text-gray-900">{{ $natgov->registration_status ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registration Date</span><div class="font-medium text-gray-900">{{ optional($natgov->registration_date)->format('M d, Y') ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Uploaded</span><div class="font-medium text-gray-900">{{ optional($natgov->date_uploaded)->format('M d, Y') ?? '-' }}</div></div>
                    </div>
                </div>

                <div class="bg-white border border-emerald-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900">Upload Approved File Later</div>
                    <div class="mt-1 text-xs text-gray-500">Use this when the approved NatGov PDF becomes available after the draft was already saved.</div>
                    <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        @method('PUT')
                        <input type="file" name="approved_document_path" accept="application/pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            {{ $natgov->approved_document_path ? 'Update Approved File' : 'Upload Approved File' }}
                        </button>
                    </form>
                </div>

                <div class="space-y-2 pt-2">
                    @if ($draftDownloadUrl)
                        <a href="{{ $draftDownloadUrl }}" class="w-full inline-flex px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            Download Draft PDF
                        </a>
                    @endif
                    @if ($approvedDownloadUrl)
                        <a href="{{ $approvedDownloadUrl }}" class="w-full inline-flex px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            Download Approved PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
