@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Secretary Certificates</div>
            <div class="flex-1"></div>
            <button type="button" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Certificate
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH SECTION --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" placeholder="Search certificate number..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="text" placeholder="Search governing body..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Certificate No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Governing Body</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notice Ref #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Resolution No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Issued</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Secretary</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notary Public</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
    @forelse ($certificates as $certificate)
        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('secretary-certificates.preview', $certificate) }}'">
            <td class="px-4 py-3 font-medium">{{ $certificate->certificate_no }}</td>
            <td class="px-4 py-3">{{ optional($certificate->date_uploaded)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $certificate->uploaded_by }}</td>
            <td class="px-4 py-3">{{ $certificate->governing_body }}</td>
            <td class="px-4 py-3">{{ $certificate->type_of_meeting }}</td>
            <td class="px-4 py-3">{{ $certificate->notice_ref }}</td>
            <td class="px-4 py-3">{{ $certificate->meeting_no }}</td>
            <td class="px-4 py-3">{{ $certificate->resolution_no }}</td>
            <td class="px-4 py-3">{{ optional($certificate->date_issued)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $certificate->purpose }}</td>
            <td class="px-4 py-3">{{ optional($certificate->date_of_meeting)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $certificate->location }}</td>
            <td class="px-4 py-3">{{ $certificate->secretary }}</td>
            <td class="px-4 py-3">{{ $certificate->notary_public }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="14" class="px-4 py-6 text-center text-sm text-gray-500">No certificates found.</td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
        </div>

    {{-- ADD SECRETARY CERTIFICATE SLIDER --}}
    <div x-cloak>
    <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
    <div x-show="showAddPanel"
        class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
        x-transition:enter="transform transition ease-in-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.stop
    >
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="text-lg font-semibold">Add Secretary Certificate</div>
            <div class="flex-1"></div>
            <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-600">Certificate No.</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="SEC-2026-001">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Date Uploaded</label>
                    <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Uploaded By</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Uploader">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Governing Body</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Board">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Meeting Type</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Regular">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Notice Ref #</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="NOTICE-001">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Meeting No.</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Resolution No.</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="RES-001">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Date Issued</label>
                    <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Purpose</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Purpose">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Meeting Date</label>
                    <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Location</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Location">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Secretary</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Secretary">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Notary Public</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Notary Public">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
            <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                Cancel
            </button>
            <div class="flex-1"></div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="button">
                Save Certificate
            </button>
        </div>
    </div>
    </div>

    </div>
@endsection


