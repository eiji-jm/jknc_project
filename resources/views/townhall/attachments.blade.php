@extends('layouts.app')

@section('content')
<div class="px-6 py-5 h-full flex flex-col">

    {{-- HEADER --}}
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-[26px] font-semibold text-gray-800">Attachments</h1>
    </div>

    {{-- TABLE CONTAINER --}}
    <div class="bg-white border border-gray-200 rounded-xl flex flex-col flex-1 overflow-hidden">

        {{-- TABLE --}}
        <div class="overflow-auto flex-1">
            <table class="w-full text-sm text-left border-collapse">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 border-r font-semibold">Ref#</th>
                        <th class="px-4 py-3 border-r font-semibold">Subject</th>
                        <th class="px-4 py-3 border-r font-semibold">Type</th>
                        <th class="px-4 py-3 border-r font-semibold">Uploaded By</th>
                        <th class="px-4 py-3 border-r font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white text-gray-700">

                    @forelse($communications as $item)

                        @php
                            $ext = strtolower(pathinfo($item->attachment, PATHINFO_EXTENSION));

                            $type = match(true) {
                                in_array($ext, ['jpg','jpeg','png','gif','webp']) => 'Image',
                                $ext === 'pdf' => 'PDF',
                                in_array($ext, ['doc','docx']) => 'Document',
                                default => 'File'
                            };
                        @endphp

                        <tr class="border-t hover:bg-gray-50 transition">

                            <td class="px-4 py-3 border-r">
                                {{ $item->ref_no }}
                            </td>

                            <td class="px-4 py-3 border-r font-medium">
                                {{ $item->subject }}
                            </td>

                            <td class="px-4 py-3 border-r">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                    {{ $type }}
                                </span>
                            </td>

                            <td class="px-4 py-3 border-r">
                                {{ $item->from_name }}
                            </td>

                            <td class="px-4 py-3 border-r">
                                {{ $item->communication_date }}
                            </td>

                            <td class="px-4 py-3">

                                {{-- VIEW --}}
                                <a href="{{ asset('storage/'.$item->attachment) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:underline text-sm mr-3">
                                    View
                                </a>

                                {{-- OPEN MEMO --}}
                                <a href="{{ route('townhall.show', $item->id) }}"
                                   class="text-gray-600 hover:underline text-sm">
                                    Open Memo
                                </a>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                No attachments found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-t flex items-center justify-between text-xs text-gray-500">

            <div>
                Total Attachments:
                <span class="font-semibold text-gray-800">
                    {{ $communications->count() }}
                </span>
            </div>

        </div>

    </div>

</div>
@endsection
