@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4">

    <div class="bg-white rounded-xl border border-gray-200">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <div class="text-sm font-medium text-gray-700">Correspondence</div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center border rounded-md overflow-hidden bg-gray-50">
                    <button class="p-2 hover:bg-white transition border-r"><i class="fas fa-bars text-gray-400"></i></button>
                    <button class="p-2 hover:bg-white transition"><i class="fas fa-th-large text-gray-400"></i></button>
                </div>

                <div class="flex">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-l-md text-sm font-medium transition">
                        + Contact
                    </button>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 rounded-r-md border-l border-blue-500 transition">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                </div>

                <button class="p-2 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="border rounded-md overflow-hidden">
                <table class="w-full text-[13px] text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b">
                        <tr>
                            <th class="p-3 font-semibold border-r">Date Uploaded</th>
                            <th class="p-3 font-semibold border-r">Uploaded By:</th>
                            <th class="p-3 font-semibold border-r">Client</th>
                            <th class="p-3 font-semibold border-r">TIN</th>
                            <th class="p-3 font-semibold border-r">Banks</th>
                            <th class="p-3 font-semibold border-r">Bank Docs</th>
                            <th class="p-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        {{-- Empty state rows to match your images --}}
                        <tr class="h-10"><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td></td></tr>
                        <tr class="h-10 bg-gray-50/30"><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td class="border-r"></td><td></td></tr>
                    </tbody>
                </table>
            </div>

            {{-- FOOTER STATS --}}
            <div class="mt-4 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5">Total Task <span class="w-2 h-2 rounded-full bg-blue-900"></span> 1</span>
                    <span class="flex items-center gap-1.5">Open Task <span class="w-2 h-2 rounded-full bg-yellow-400"></span> 1</span>
                    <span class="flex items-center gap-1.5">Completed <span class="w-2 h-2 rounded-full bg-green-500"></span> 1</span>
                    <span class="flex items-center gap-1.5">Overdue <span class="w-2 h-2 rounded-full bg-red-500"></span> 1</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Records per page 
                        <select class="bg-transparent border-none outline-none cursor-pointer font-semibold text-gray-700">
                            <option>10</option>
                        </select>
                    </span>
                    <span>1 to 1</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection