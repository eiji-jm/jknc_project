@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ openPanel: false }">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <!-- TOP BAR -->
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-0 overflow-x-auto">
                <button class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 bg-white text-gray-800">
                    SEC-COI
                </button>

                <button class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800">
                    SEC-AOI
                </button>

                <button class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800">
                    bylaws
                </button>

                <button class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800">
                    GIS
                </button>
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
                    <button
                        @click="openPanel = true"
                        class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        SEC-COI
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
            <div class="p-3 transition-all duration-300 ease-in-out" :class="openPanel ? 'pr-[650px]' : ''">
                <div class="overflow-x-auto border border-gray-200 rounded-md bg-white">
                    <table class="min-w-full text-[11px] text-left text-gray-700">
                        <thead class="bg-white border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 font-semibold">Date Upload</th>
                                <th class="px-3 py-2 font-semibold">Date Created</th>
                                <th class="px-3 py-2 font-semibold">Company Reg No.</th>
                                <th class="px-3 py-2 font-semibold">Corporation Name</th>
                                <th class="px-3 py-2 font-semibold">Issued On</th>
                                <th class="px-3 py-2 font-semibold">Issued by</th>
                                <th class="px-3 py-2 font-semibold">File Upload</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2">Jan 08, 2026</td>
                                <td class="px-3 py-2">Jan 08, 2026</td>
                                <td class="px-3 py-2">CS202600123</td>
                                <td class="px-3 py-2 font-semibold text-gray-800">John Kelly &amp; Company, Inc.</td>
                                <td class="px-3 py-2">Jan 05, 2026</td>
                                <td class="px-3 py-2">SEC Region VII</td>
                                <td class="px-3 py-2 text-blue-600 font-medium">sec-coi-jkc.pdf</td>
                            </tr>

                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2">Jan 10, 2026</td>
                                <td class="px-3 py-2">Jan 10, 2026</td>
                                <td class="px-3 py-2">CS202600124</td>
                                <td class="px-3 py-2 font-semibold text-gray-800">Cebu Prime Holdings Corporation</td>
                                <td class="px-3 py-2">Jan 07, 2026</td>
                                <td class="px-3 py-2">SEC Main Office</td>
                                <td class="px-3 py-2 text-blue-600 font-medium">sec-coi-cphc.pdf</td>
                            </tr>

                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2">Jan 12, 2026</td>
                                <td class="px-3 py-2">Jan 12, 2026</td>
                                <td class="px-3 py-2">CS202600125</td>
                                <td class="px-3 py-2 font-semibold text-gray-800">Visayan Business Solutions, Inc.</td>
                                <td class="px-3 py-2">Jan 09, 2026</td>
                                <td class="px-3 py-2">SEC Region VII</td>
                                <td class="px-3 py-2 text-blue-600 font-medium">sec-coi-vbsi.pdf</td>
                            </tr>

                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2">Jan 15, 2026</td>
                                <td class="px-3 py-2">Jan 15, 2026</td>
                                <td class="px-3 py-2">CS202600126</td>
                                <td class="px-3 py-2 font-semibold text-gray-800">Metro South Development Corp.</td>
                                <td class="px-3 py-2">Jan 13, 2026</td>
                                <td class="px-3 py-2">SEC Main Office</td>
                                <td class="px-3 py-2 text-blue-600 font-medium">sec-coi-msdc.pdf</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">Jan 18, 2026</td>
                                <td class="px-3 py-2">Jan 18, 2026</td>
                                <td class="px-3 py-2">CS202600127</td>
                                <td class="px-3 py-2 font-semibold text-gray-800">Blue Horizon Trading Corporation</td>
                                <td class="px-3 py-2">Jan 16, 2026</td>
                                <td class="px-3 py-2">SEC Region VII</td>
                                <td class="px-3 py-2 text-blue-600 font-medium">sec-coi-bhtc.pdf</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- FULL HEIGHT APP-SIDE PANEL -->
    <div x-show="openPanel"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 bottom-0 z-[80] w-[610px] bg-white border-l border-gray-300 shadow-2xl"
         style="display:none;">

        <div class="h-full flex flex-col">

            <!-- PANEL HEADER -->
            <div class="h-16 px-7 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-[34px] font-semibold text-gray-900 leading-none">Add Record</h2>

                <button @click="openPanel = false" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-chevron-up text-sm"></i>
                </button>
            </div>

            <!-- PANEL BODY -->
            <div class="flex-1 overflow-y-auto px-7 py-7">
                <div class="space-y-7">

                    <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                        <label class="text-[15px] text-gray-700">Corporate Name</label>
                        <input type="text"
                               class="w-full h-12 border border-gray-300 rounded-md px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                        <label class="text-[15px] text-gray-700">Company Reg No.</label>
                        <input type="text"
                               class="w-full h-12 border border-gray-300 rounded-md px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                        <label class="text-[15px] text-gray-700">Issued by</label>
                        <input type="text"
                               class="w-full h-12 border border-gray-300 rounded-md px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                        <label class="text-[15px] text-gray-700">Issued On</label>
                        <input type="date"
                               class="w-full h-12 border border-gray-300 rounded-md px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                        <label class="text-[15px] text-gray-700">Date Upload</label>
                        <input type="date"
                               class="w-full h-12 border border-gray-300 rounded-md px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="pt-10">
                        <div class="grid grid-cols-[150px_1fr] gap-7 items-center">
                            <label class="text-[15px] text-gray-700">File Upload</label>

                            <label class="w-full h-[64px] border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 flex items-center gap-4 px-4 cursor-pointer">
                                <i class="far fa-file-alt text-[28px] text-gray-500"></i>
                                <span class="text-[15px] text-blue-600">File Upload</span>
                                <input type="file" class="hidden">
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PANEL FOOTER -->
            <div class="px-7 py-5 border-t border-gray-200 flex justify-end gap-5">
                <button @click="openPanel = false"
                        class="min-w-[96px] px-7 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <button
                        class="min-w-[96px] px-7 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    Save
                </button>
            </div>

        </div>
    </div>

</div>
@endsection