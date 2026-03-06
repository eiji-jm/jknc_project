@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ openPanel: false }">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <!-- TOP BAR -->
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-0 overflow-x-auto">
                <a href="{{ route('corporate.formation') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    SEC-COI
                </a>

                <a href="{{ route('corporate.sec_aoi') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    SEC-AOI
                </a>

                <a href="{{ route('corporate.bylaws') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-blue-500 bg-blue-50 text-blue-700 text-center">
                    bylaws
                </a>

                <a href="{{ route('corporate.gis') }}"
                   class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    GIS
                </a>
            </div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-bars text-sm"></i>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
                    <i class="fas fa-table-cells-large text-sm"></i>
                </button>

                <div class="flex items-center">
                    <button @click="openPanel = true"
                        class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        SEC-bylaws
                    </button>

                    <button class="w-10 h-9 rounded-r-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center border-l border-white/20">
                        <i class="fas fa-caret-down text-xs"></i>
                    </button>
                </div>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="bg-gray-50 min-h-[680px]">
            <div class="p-3">
                <div class="overflow-x-auto border border-gray-200 rounded-md bg-white">
                    <table class="min-w-full text-[10px] text-left text-gray-700">
                        <thead class="bg-white border-b border-gray-200 align-top">
                            <tr>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Date Upload</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Uploaded by</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Company Reg No.</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Corporation Name</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Type of Formation</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">SEC-AOI Version</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Type of SEC-AOI Version</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Date of SEC-AOI Version</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Regular ASM</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Notice Time</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Regular BODM</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Notice Time</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 align-top">
                                <td class="px-2 py-2">Feb 10, 2026</td>
                                <td class="px-2 py-2 font-semibold text-gray-800">Kelly, John</td>
                                <td class="px-2 py-2">CS202600123</td>
                                <td class="px-2 py-2">John Kelly &amp; Company, Inc.</td>
                                <td class="px-2 py-2">Stock Corporation</td>
                                <td class="px-2 py-2">v1.0</td>
                                <td class="px-2 py-2">Original</td>
                                <td class="px-2 py-2">Jan 15, 2026</td>
                                <td class="px-2 py-2">Every 2nd Monday of May</td>
                                <td class="px-2 py-2">15 days</td>
                                <td class="px-2 py-2">Every 1st Friday</td>
                                <td class="px-2 py-2">7 days</td>
                            </tr>

                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-2 py-2">Feb 12, 2026</td>
                                <td class="px-2 py-2 font-semibold text-gray-800">Dimpas, Mj</td>
                                <td class="px-2 py-2">CS202600124</td>
                                <td class="px-2 py-2">Cebu Prime Holdings Corporation</td>
                                <td class="px-2 py-2">Stock Corporation</td>
                                <td class="px-2 py-2">v2.0</td>
                                <td class="px-2 py-2">Amended</td>
                                <td class="px-2 py-2">Feb 01, 2026</td>
                                <td class="px-2 py-2">Every last Friday of April</td>
                                <td class="px-2 py-2">10 days</td>
                                <td class="px-2 py-2">Every 2nd Tuesday</td>
                                <td class="px-2 py-2">5 days</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DARK OVERLAY -->
    <div x-show="openPanel"
         x-transition.opacity
         class="fixed inset-0 z-[70] bg-black/35"
         style="display:none;"
         @click="openPanel = false">
    </div>

    <!-- SIDE PANEL -->
    <div x-show="openPanel"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 bottom-0 z-[80] w-[430px] bg-white border-l border-gray-300 shadow-2xl"
         style="display:none;">

        <div class="h-full flex flex-col">
            <div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-[26px] font-semibold text-gray-900 leading-none">Add Bylaws Record</h2>

                <button @click="openPanel = false"
                        class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Corporation Name</label>
                        <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Company Reg No.</label>
                        <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Type of Formation</label>
                        <select class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
                            <option>Stock Corporation</option>
                            <option>Non-Stock Corporation</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">SEC-AOI Version</label>
                            <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Type of SEC-AOI Version</label>
                            <select class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
                                <option>Original</option>
                                <option>Amended</option>
                                <option>Revised</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Date of SEC-AOI Version</label>
                        <input type="date" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Regular ASM</label>
                            <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="Meeting schedule">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Notice Time</label>
                            <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="e.g. 15 days">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Regular BODM</label>
                            <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="Board schedule">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Notice Time</label>
                            <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="e.g. 7 days">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">Uploaded By</label>
                        <input type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                    </div>

                    <div class="pt-2">
                        <label class="block text-[13px] font-medium text-gray-700 mb-2">File Upload</label>
                        <label class="w-full min-h-[84px] border border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center gap-2 px-4 cursor-pointer transition">
                            <i class="far fa-file-alt text-[26px] text-gray-500"></i>
                            <span class="text-[14px] text-blue-600 font-medium">Choose file to upload</span>
                            <span class="text-[11px] text-gray-400">PDF, DOC, DOCX supported</span>
                            <input type="file" class="hidden">
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button @click="openPanel = false"
                        class="min-w-[92px] px-6 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <button class="min-w-[92px] px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Save
                </button>
            </div>
        </div>
    </div>

</div>
@endsection