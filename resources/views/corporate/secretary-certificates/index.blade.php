@extends('layouts.app')

@section('content')
@php
    $today = now()->toDateString();
    $currentUser = auth()->user()?->name ?? '';
    $resolutionOptions = $resolutions->map(fn ($resolution) => [
        'id' => $resolution->id,
        'resolution_no' => $resolution->resolution_no,
        'notice_id' => $resolution->notice_id,
        'notice_ref' => $resolution->notice_ref,
        'governing_body' => $resolution->governing_body,
        'type_of_meeting' => $resolution->type_of_meeting,
        'meeting_no' => $resolution->meeting_no,
        'date_of_meeting' => optional($resolution->date_of_meeting)->toDateString(),
        'location' => $resolution->location,
        'board_resolution' => $resolution->board_resolution,
        'secretary' => $resolution->secretary,
        'notary_public' => $resolution->notary_public,
        'notary_doc_no' => $resolution->notary_doc_no,
        'notary_page_no' => $resolution->notary_page_no,
        'notary_book_no' => $resolution->notary_book_no,
        'notary_series_no' => $resolution->notary_series_no,
    ])->values();
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="secretaryCertificateForm({{ Js::from($resolutionOptions) }})" @keydown.escape.window="showAddPanel = false">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <div class="text-lg font-semibold">Secretary Certificates</div>
            <div class="flex-1"></div>
            <button type="button" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Certificate
            </button>
        </div>

        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Certificate No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Linked Resolution</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Original Upload</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($certificates as $certificate)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('secretary-certificates.preview', $certificate) }}'">
                                <td class="px-4 py-3 font-medium">{{ $certificate->certificate_no }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ $certificate->resolution_no }}</div>
                                    <div class="text-xs text-gray-500">{{ $certificate->notice_ref }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $certificate->purpose }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ optional($certificate->date_of_meeting)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $certificate->location }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $certificate->document_path ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $certificate->document_path ? 'Original uploaded' : 'Draft only' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No certificates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div>
                    <div class="text-lg font-semibold">Add Secretary Certificate</div>
                    <div class="text-xs text-gray-500">Choose a resolution and the shared details will fill automatically.</div>
                </div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('secretary-certificates.store') }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Certificate No.</label>
                        <input type="text" name="certificate_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="SEC-2026-001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Uploaded</label>
                        <input type="date" name="date_uploaded" value="{{ $today }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input type="text" name="uploaded_by" value="{{ $currentUser }}" data-default-field="current_user" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Linked Resolution</label>
                        <select name="resolution_id" x-model="selectedResolutionId" @change="applyResolution()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select a resolution</option>
                            <template x-for="resolution in resolutions" :key="resolution.id">
                                <option :value="resolution.id" x-text="`${resolution.resolution_no || 'Draft Resolution'} • ${resolution.board_resolution || ''}`"></option>
                            </template>
                        </select>
                    </div>
                    <input type="hidden" name="notice_id" x-ref="noticeId">
                    <div>
                        <label class="text-xs text-gray-600">Notice Ref #</label>
                        <input type="text" name="notice_ref" x-ref="noticeRef" class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Resolution No.</label>
                        <input type="text" name="resolution_no" x-ref="resolutionNo" class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Governing Body</label>
                        <select name="governing_body" x-ref="governingBody" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="Stockholders">Stockholders</option>
                            <option value="Board of Directors">Board of Directors</option>
                            <option value="Joint Stockholders and Board of Directors">Joint Stockholders and Board of Directors</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Type of Meeting</label>
                        <select name="type_of_meeting" x-ref="meetingType" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting No.</label>
                        <input type="text" name="meeting_no" x-ref="meetingNo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Issued</label>
                        <input type="date" name="date_issued" value="{{ $today }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Purpose</label>
                        <input type="text" name="purpose" x-ref="purpose" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting Date</label>
                        <input type="date" name="date_of_meeting" x-ref="meetingDate" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Location</label>
                        <input type="text" name="location" x-ref="location" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Secretary</label>
                        <input type="text" name="secretary" x-ref="secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Notary Public</label>
                        <input type="text" name="notary_public" x-ref="notaryPublic" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Doc No.</label>
                        <input type="text" name="notary_doc_no" x-ref="docNo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Page No.</label>
                        <input type="text" name="notary_page_no" x-ref="pageNo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Book No.</label>
                        <input type="text" name="notary_book_no" x-ref="bookNo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Series No.</label>
                        <input type="text" name="notary_series_no" x-ref="seriesNo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload Original Certificate (PDF)</label>
                        <input type="file" name="document_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                        Save Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function secretaryCertificateForm(resolutions) {
        return {
            showAddPanel: false,
            resolutions,
            selectedResolutionId: '',
            applyResolution() {
                const selected = this.resolutions.find((resolution) => String(resolution.id) === String(this.selectedResolutionId));
                if (!selected) {
                    return;
                }

                this.$refs.noticeId.value = selected.notice_id || '';
                this.$refs.noticeRef.value = selected.notice_ref || '';
                this.$refs.resolutionNo.value = selected.resolution_no || '';
                this.$refs.governingBody.value = selected.governing_body || 'Board of Directors';
                this.$refs.meetingType.value = selected.type_of_meeting || 'Regular';
                this.$refs.meetingNo.value = selected.meeting_no || '';
                this.$refs.meetingDate.value = selected.date_of_meeting || '';
                this.$refs.location.value = selected.location || '';
                this.$refs.purpose.value = selected.board_resolution || '';
                this.$refs.secretary.value = selected.secretary || '';
                this.$refs.notaryPublic.value = selected.notary_public || '';
                this.$refs.docNo.value = selected.notary_doc_no || '';
                this.$refs.pageNo.value = selected.notary_page_no || '';
                this.$refs.bookNo.value = selected.notary_book_no || '';
                this.$refs.seriesNo.value = selected.notary_series_no || '';
            },
        };
    }
</script>
@endsection
