@extends('layouts.app')
@section('title', 'UBO Form')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: simple title + actions (reuse from other pages) --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">UBO FORM</div>

            <div class="flex-1"></div>

            {{-- actions just for show, copy from company-general-information --}}
            <div class="flex items-center gap-2">
                <!-- blue Add button with dropdown arrow matching design -->
                <button type="button" @click="showAddPanel = true"
                   class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- CONTENT --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Complete Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Specific Residential Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date of Birth</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Tax Identification No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ownership %</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($owners as $owner)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('corporate.ubo.show', $owner) }}" class="text-gray-900 hover:underline">
                                        {{ $owner->complete_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">{{ $owner->residential_address }}</td>
                                <td class="px-4 py-3">{{ $owner->nationality }}</td>
                                <td class="px-4 py-3">{{ optional($owner->date_of_birth)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $owner->tax_identification_no }}</td>
                                <td class="px-4 py-3">{{ $owner->ownership_percentage }}</td>
                                <td class="px-4 py-3">{{ $owner->ownership_type }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No UBO records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
{{-- ADD UBO SLIDER --}}
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
            <div class="text-lg font-semibold">Add UBO</div>
            <div class="flex-1"></div>
            <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-600">Complete Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Complete name">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-600">Specific Residential Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Address">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Nationality</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Nationality">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Date of Birth</label>
                    <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Tax Identification No.</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="TIN">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Ownership %</label>
                    <input type="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="0">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Type</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Direct/Indirect">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
            <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                Cancel
            </button>
            <div class="flex-1"></div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="button">
                Save UBO
            </button>
        </div>
    </div>
</div>
@endsection
