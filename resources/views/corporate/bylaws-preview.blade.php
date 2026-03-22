@extends('layouts.app')

@section('content')

@php
    $draftUrl = !empty($record->file_path) ? asset('storage/' . ltrim($record->file_path, '/')) : null;
    $notaryUrl = !empty($record->notary_file_path) ? asset('storage/' . ltrim($record->notary_file_path, '/')) : null;
    $canEditRecord = in_array($record->workflow_status, ['Uploaded', 'Reverted']);
@endphp

<div class="w-full px-6 py-6"
     x-data="{
        fileTab: '{{ $draftUrl ? 'draft' : ($notaryUrl ? 'notary' : 'draft') }}',
        editDraft: false,
        editNotary: false
     }">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-2xl font-semibold mb-6">
        SEC – Corporate Bylaws
    </h1>

    <div class="grid grid-cols-3 gap-6">

        <div class="col-span-2 bg-white border rounded-lg p-4">

            <div class="flex items-center gap-6 border-b border-gray-200 mb-4">
                <button
                    @click="fileTab = 'draft'"
                    :class="fileTab === 'draft' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Draft
                </button>

                <button
                    @click="fileTab = 'notary'"
                    :class="fileTab === 'notary' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Notary
                </button>
            </div>

            <div x-show="fileTab === 'draft'">
                @if($draftUrl)
                    <iframe
                        src="{{ $draftUrl }}"
                        class="w-full h-[700px] border rounded">
                    </iframe>
                @else
                    <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                        No draft file attached for this Bylaws record.
                    </div>
                @endif
            </div>

            <div x-show="fileTab === 'notary'" x-cloak>
                @if($notaryUrl)
                    <iframe
                        src="{{ $notaryUrl }}"
                        class="w-full h-[700px] border rounded">
                    </iframe>
                @else
                    <div class="w-full h-[700px] border rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400 text-sm px-6 text-center">
                        <i class="far fa-file-alt text-4xl mb-4"></i>
                        <p class="font-medium text-gray-500 mb-1">No notarized file attached yet.</p>
                        <p class="text-gray-400">This section is reserved for the final notarized document.</p>
                    </div>
                @endif
            </div>

        </div>

        <div class="bg-white border rounded-lg p-6 space-y-4">

            <h2 class="text-lg font-semibold mb-4">
                Bylaws Information
            </h2>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Corporation</span>
                <span class="font-medium text-right">{{ $record->corporation_name }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Company Reg No.</span>
                <span class="text-right">{{ $record->company_reg_no }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Formation</span>
                <span class="text-right">{{ $record->type_of_formation }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">AOI Version</span>
                <span class="text-right">{{ $record->aoi_version }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Version Type</span>
                <span class="text-right">{{ $record->aoi_type }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">AOI Date</span>
                <span class="text-right">{{ $record->aoi_date }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Regular ASM</span>
                <span class="text-right">{{ $record->regular_asm }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">ASM Notice</span>
                <span class="text-right">{{ $record->asm_notice }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Regular BODM</span>
                <span class="text-right">{{ $record->regular_bodm }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">BODM Notice</span>
                <span class="text-right">{{ $record->bodm_notice }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Uploaded By</span>
                <span class="text-right">{{ $record->uploaded_by }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Date Upload</span>
                <span class="text-right">{{ $record->date_upload }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Workflow Status</span>
                <span class="text-right">{{ $record->workflow_status }}</span>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Draft File
                    </h3>

                    @if($canEditRecord && $draftUrl)
                        <button
                            type="button"
                            @click="editDraft = !editDraft"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editDraft ? 'Cancel' : 'Edit'"></span>
                        </button>
                    @endif
                </div>

                @if($draftUrl)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Draft file attached</p>
                            <p class="text-xs text-gray-400">{{ basename($record->file_path) }}</p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'draft'"
                            class="text-xs px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            View
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No draft file attached yet.
                    </div>
                @endif

                @if($canEditRecord)
                    <form x-show="editDraft || !{{ $draftUrl ? 'true' : 'false' }}" x-cloak
                          action="{{ route('corporate.bylaws.upload.draft', $record->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf

                        <input type="file" name="draft_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                            Save Draft File
                        </button>
                    </form>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Notary File
                    </h3>

                    @if($canEditRecord && $notaryUrl)
                        <button
                            type="button"
                            @click="editNotary = !editNotary"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editNotary ? 'Cancel' : 'Edit'"></span>
                        </button>
                    @endif
                </div>

                @if($notaryUrl)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Notary file attached</p>
                            <p class="text-xs text-gray-400">{{ basename($record->notary_file_path) }}</p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'notary'"
                            class="text-xs px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700">
                            View
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No notary file attached yet.
                    </div>
                @endif

                @if($canEditRecord)
                    <form x-show="editNotary || !{{ $notaryUrl ? 'true' : 'false' }}" x-cloak
                          action="{{ route('corporate.bylaws.upload.notary', $record->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf

                        <input type="file" name="notary_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                            Save Notary File
                        </button>
                    </form>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection