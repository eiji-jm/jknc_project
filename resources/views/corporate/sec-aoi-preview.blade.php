@extends('layouts.app')

@section('content')

@php
    $fileUrl = !empty($record->file_path) ? asset('storage/' . ltrim($record->file_path, '/')) : null;
@endphp

<div class="w-full px-6 py-6">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-2xl font-semibold mb-6">
        SEC – Articles Of Incorporation
    </h1>

    <div class="grid grid-cols-3 gap-6">

        <!-- FILE VIEWER -->
        <div class="col-span-2 bg-white border rounded-lg p-4">
            @if($fileUrl)
                <iframe
                    src="{{ $fileUrl }}"
                    class="w-full h-[700px] border rounded">
                </iframe>
            @else
                <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                    No file attached for this SEC-AOI record.
                </div>
            @endif
        </div>

        <!-- INFORMATION PANEL -->
        <div class="bg-white border rounded-lg p-6 space-y-4">

            <h2 class="text-lg font-semibold mb-4">
                AOI Information
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
                <span class="text-gray-500">Principal Address</span>
                <span class="text-right">{{ $record->principal_address }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Par Value</span>
                <span class="text-right">{{ $record->par_value }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Authorized Capital</span>
                <span class="text-right">{{ $record->authorized_capital_stock }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Directors</span>
                <span class="text-right">{{ $record->directors }}</span>
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
                <span class="text-gray-500">Uploaded By</span>
                <span class="text-right">{{ $record->uploaded_by }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Date Upload</span>
                <span class="text-right">{{ $record->date_upload }}</span>
            </div>

            <div class="pt-4">
                @if($fileUrl)
                    <a href="{{ $fileUrl }}"
                       download
                       class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                        Download File
                    </a>
                @else
                    <form action="{{ route('corporate.sec_aoi.upload', $record->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf

                        <input type="file" name="file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                            Attach File
                        </button>
                    </form>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection