@extends('layouts.app')

@section('content')
<div class="px-6 py-5 h-full flex flex-col bg-[#f5f6f8]">

    {{-- HEADER --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-[26px] font-semibold text-gray-800">Attachments</h1>
            <p class="text-sm text-gray-500">Browse all uploaded Town Hall files</p>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-5">
        <form method="GET" class="flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <div class="flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by subject, ref no, department, uploader..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>

            <div class="flex gap-3">
                <select
                    name="type"
                    class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
                    <option value="">All Types</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="pdf" {{ request('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Documents</option>
                </select>

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium"
                >
                    Filter
                </button>

                <a
                    href="{{ route('townhall.attachments') }}"
                    class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- FILE MANAGER GRID --}}
    <div class="bg-white border border-gray-200 rounded-xl flex-1 flex flex-col overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Total Files:
                <span class="font-semibold text-gray-800">{{ $communications->total() }}</span>
            </div>
        </div>

        <div class="p-5 overflow-auto flex-1">
            @if($communications->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
                    @foreach($communications as $item)
                        @php
                            $ext = strtolower(pathinfo($item->attachment, PATHINFO_EXTENSION));

                            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                                $type = 'Image';
                                $badge = 'bg-green-50 text-green-700';
                                $icon = 'fa-image';
                            } elseif ($ext === 'pdf') {
                                $type = 'PDF';
                                $badge = 'bg-red-50 text-red-700';
                                $icon = 'fa-file-pdf';
                            } elseif (in_array($ext, ['doc','docx'])) {
                                $type = 'Document';
                                $badge = 'bg-blue-50 text-blue-700';
                                $icon = 'fa-file-word';
                            } else {
                                $type = 'File';
                                $badge = 'bg-gray-100 text-gray-700';
                                $icon = 'fa-file';
                            }
                        @endphp

                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition">
                            {{-- PREVIEW --}}
                            <div class="h-44 bg-gray-50 border-b border-gray-200 flex items-center justify-center overflow-hidden">
                                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <img
                                        src="{{ asset('storage/' . $item->attachment) }}"
                                        alt="{{ $item->subject }}"
                                        class="w-full h-full object-cover"
                                    >
                                @elseif($ext === 'pdf')
                                    <div class="flex flex-col items-center justify-center text-red-600">
                                        <i class="fas {{ $icon }} text-5xl mb-2"></i>
                                        <span class="text-xs font-medium">PDF Preview</span>
                                    </div>
                                @elseif(in_array($ext, ['doc','docx']))
                                    <div class="flex flex-col items-center justify-center text-blue-600">
                                        <i class="fas {{ $icon }} text-5xl mb-2"></i>
                                        <span class="text-xs font-medium">Document File</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas {{ $icon }} text-5xl mb-2"></i>
                                        <span class="text-xs font-medium">File</span>
                                    </div>
                                @endif
                            </div>

                            {{-- DETAILS --}}
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                            {{ $item->subject ?: 'Untitled Attachment' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $item->ref_no }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 px-2 py-1 rounded-full text-[11px] font-medium {{ $badge }}">
                                        {{ $type }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-xs text-gray-500 mb-4">
                                    <p><span class="font-medium text-gray-700">From:</span> {{ $item->from_name }}</p>
                                    <p><span class="font-medium text-gray-700">Department:</span> {{ $item->department_stakeholder }}</p>
                                    <p><span class="font-medium text-gray-700">Date:</span> {{ $item->communication_date }}</p>
                                </div>

                                {{-- ACTIONS --}}
                                <div class="flex flex-wrap gap-2">
                                    <a
                                        href="{{ asset('storage/' . $item->attachment) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>

                                    <a
                                        href="{{ asset('storage/' . $item->attachment) }}"
                                        download
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        <i class="fas fa-download"></i>
                                        Download
                                    </a>

                                    <a
                                        href="{{ route('townhall.show', $item->id) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700"
                                    >
                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                        Open Memo
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-full flex items-center justify-center text-center text-gray-500 py-16">
                    <div>
                        <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium">No attachments found</p>
                        <p class="text-xs text-gray-400 mt-1">Try changing your filters or search keyword.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if(method_exists($communications, 'links'))
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $communications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
