@extends('layouts.app')

@section('content')
<div class="p-6 bg-[#f5f6f8] min-h-screen">

    <div class="max-w-[1200px] mx-auto">

        <div class="flex justify-between mb-4">
            <h1 class="text-xl font-semibold">Attachments</h1>

            <a href="{{ route('townhall') }}"
               class="border px-4 py-2 rounded-lg text-sm">
                ← Back
            </a>
        </div>

        <div class="grid grid-cols-3 gap-4">

            @forelse($communications as $item)

                <div class="bg-white border rounded-xl p-4 shadow">

                    <p class="text-xs text-gray-400 mb-1">
                        {{ $item->ref_no }}
                    </p>

                    <p class="text-sm font-semibold mb-2">
                        {{ $item->subject }}
                    </p>

                    @php
                        $ext = strtolower(pathinfo($item->attachment, PATHINFO_EXTENSION));
                    @endphp

                    {{-- PREVIEW --}}
                    <div class="h-[200px] border rounded mb-2 overflow-hidden">

                        @if(in_array($ext, ['jpg','jpeg','png','webp']))
                            <img src="{{ asset('storage/'.$item->attachment) }}" class="w-full h-full object-cover">
                        @elseif($ext === 'pdf')
                            <iframe src="{{ asset('storage/'.$item->attachment) }}" class="w-full h-full"></iframe>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                File
                            </div>
                        @endif

                    </div>

                    {{-- ACTION --}}
                    <a href="{{ asset('storage/'.$item->attachment) }}"
                       target="_blank"
                       class="text-blue-600 text-sm underline">
                        View / Download
                    </a>

                </div>

            @empty
                <p>No attachments found.</p>
            @endforelse

        </div>

    </div>

</div>
@endsection
