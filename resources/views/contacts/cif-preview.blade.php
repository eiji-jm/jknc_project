@extends('layouts.app')

@section('content')
<div class="{{ $downloadMode ? 'bg-white p-0' : 'bg-[#f7f6f2] p-6' }}">
    <div class="{{ $downloadMode ? '' : 'mx-auto max-w-6xl space-y-4' }}">
        @if (! $downloadMode)
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
                CIF preview uses saved structured CIF data from edit mode. Supporting documents remain separate attachments.
            </div>
        @endif

        @include('contacts.partials.cif-document', ['cifData' => $cifData])

        @if (! $downloadMode)
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">
                <h2 class="text-base font-semibold text-gray-900">Supporting Documents</h2>
                <p class="mt-1 text-xs text-gray-500">Attachments are supporting evidence and do not replace the CIF form data.</p>
                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($cifDocuments as $document)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                            <p class="font-medium text-gray-900">{{ $document['label'] ?? 'Attachment' }}</p>
                            <p class="text-xs text-gray-500">{{ $document['file_name'] ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $document['uploaded_at'] ?? '-' }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">No supporting documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>

@if ($downloadMode)
    <script>
        window.addEventListener('load', () => window.print());
    </script>
@endif
@endsection
