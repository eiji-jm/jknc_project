@extends('layouts.app')

@section('content')
{{-- Wrap the entire view in Alpine state --}}
<div class="w-full px-6 mt-4" x-data="{ showSlideOver: false }">

    {{-- SLIDE-OVER COMPONENT --}}
    <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="showSlideOver" @click="showSlideOver = false" class="absolute inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
            <div class="absolute inset-y-0 right-0 max-w-full flex">
                <div x-show="showSlideOver" class="w-screen max-w-sm bg-white shadow-2xl flex flex-col h-full"
                     x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    
                    {{-- Form Header --}}
                    <div class="p-6 border-b flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800">Add Entry</h2>
                        <button @click="showSlideOver = false" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>

                    {{-- Form Body (Restored) --}}
                    <div class="p-6 flex-1 overflow-y-auto space-y-4">
                        <div><label class="block text-sm font-medium mb-1">Client *</label><select class="w-full border rounded-md p-2"><option>Select Client</option></select></div>
                        <div><label class="block text-sm font-medium mb-1">TIN *</label><input type="text" placeholder="Enter TIN" class="w-full border rounded-md p-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Reporting Date *</label><input type="date" class="w-full border rounded-md p-2"></div>
                        <div><label class="block text-sm font-medium mb-1">Fiscal Year End *</label><select class="w-full border rounded-md p-2"><option>Dec 31</option></select></div>
                        <div><label class="block text-sm font-medium mb-1">Upload Files *</label><div class="border-2 border-dashed p-6 text-center text-gray-400 rounded-lg text-sm">Drag & drop or <span class="text-blue-600 underline cursor-pointer">browse</span></div></div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="p-6 border-t flex gap-3">
                        <button @click="showSlideOver = false" class="flex-1 py-2 border rounded-md font-medium text-gray-600">Cancel</button>
                        <button class="flex-1 py-2 bg-blue-600 text-white rounded-md font-medium">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN PAGE DESIGN --}}
    <div class="bg-white rounded-xl border border-gray-200">
        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <div class="flex items-center gap-1 text-sm">
                <button class="px-6 py-2 bg-white border border-gray-300 border-b-0 rounded-t-md font-medium text-gray-800 -mb-[13px] z-10">PNL</button>
                <button class="px-6 py-2 text-gray-600 hover:bg-gray-50 rounded-t-md transition">Balance Sheet</button>
                <button class="px-6 py-2 text-gray-600 hover:bg-gray-50 rounded-t-md transition">Cash Flow</button>
                <button class="px-6 py-2 text-gray-600 hover:bg-gray-50 rounded-t-md transition">Income Statement</button>
                <button class="px-6 py-2 text-gray-600 hover:bg-gray-50 rounded-t-md transition">AFS</button>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex">
                    <button @click="showSlideOver = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-l-md text-sm font-medium transition">
                        + Add
                    </button>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 rounded-r-md border-l border-blue-500 transition">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="border rounded-md overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b">
                        <tr>
                            <th class="p-3 font-semibold">Date Uploaded</th>
                            <th class="p-3 font-semibold">Uploaded By</th>
                            <th class="p-3 font-semibold">Client</th>
                            <th class="p-3 font-semibold">TIN</th>
                            <th class="p-3 font-semibold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr><td class="p-3">April 22, 2024</td><td class="p-3">Jasper Bulac</td><td class="p-3">Benthel</td><td class="p-3">123-456-756</td><td class="p-3 text-right"><span class="font-semibold text-green-500">Completed</span></td></tr>
                    </tbody>
                </table>
            </div>

            {{-- FOOTER / STATS --}}
            <div class="mt-4 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5">Total Task <span class="w-2 h-2 rounded-full bg-blue-800"></span> 1</span>
                    <span class="flex items-center gap-1.5">Open Task <span class="w-2 h-2 rounded-full bg-yellow-400"></span> 1</span>
                    <span class="flex items-center gap-1.5">Completed <span class="w-2 h-2 rounded-full bg-green-500"></span> 1</span>
                    <span class="flex items-center gap-1.5">Overdue <span class="w-2 h-2 rounded-full bg-red-500"></span> 1</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Records per page <select class="bg-transparent border-none outline-none cursor-pointer font-semibold text-gray-700"><option>10</option></select></span>
                    <span>1 to 1</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection