@extends('layouts.app')

@section('content')

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ openPanel:false }">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <!-- TOP MODULES -->
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">

            <div class="flex items-center gap-0 overflow-x-auto">

                <a href="{{ route('corporate.formation') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 text-center hover:bg-gray-50">
                    SEC-COI
                </a>

                <a href="{{ route('corporate.sec_aoi') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 text-center hover:bg-gray-50">
                    SEC-AOI
                </a>

                <a href="{{ route('corporate.bylaws') }}"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 text-center hover:bg-gray-50">
                    Bylaws
                </a>

                <a href="{{ route('corporate.gis') }}"
                   class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-blue-500 bg-blue-50 text-blue-700 text-center">
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
                    <button @click="openPanel=true"
                            class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        SEC-GIS
                    </button>

                    <button class="w-10 h-9 rounded-r-full bg-blue-600 text-white flex items-center justify-center border-l border-white/20">
                        <i class="fas fa-caret-down text-xs"></i>
                    </button>
                </div>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>

            </div>

        </div>

        <!-- MAIN TABLE AREA -->
        <div class="bg-gray-50 min-h-[680px] px-6 py-4">

            <div class="overflow-x-auto bg-white border border-gray-200 rounded">

                <table class="min-w-full text-[11px]">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left">Date Upload</th>
                            <th class="px-3 py-2 text-left">Uploaded by</th>
                            <th class="px-3 py-2 text-left">Sec-Submission Status</th>
                            <th class="px-3 py-2 text-left">Sec-Receive on</th>
                            <th class="px-3 py-2 text-left">Sec-Period Date</th>
                            <th class="px-3 py-2 text-left">Company Reg No.</th>
                            <th class="px-3 py-2 text-left">Corporation Name</th>
                            <th class="px-3 py-2 text-left">Date of Annual Meeting</th>
                            <th class="px-3 py-2 text-left">Type of Meeting</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gis ?? [] as $row)
                            <tr
                                data-url="{{ route('gis.show', $row->id) }}"
                                onclick="window.location.href=this.dataset.url"
                                class="border-b hover:bg-gray-50 cursor-pointer">

                                <td class="px-3 py-2">{{ $row->created_at ? $row->created_at->format('M d, Y') : '' }}</td>
                                <td class="px-3 py-2 font-medium">{{ $row->uploaded_by }}</td>
                                <td class="px-3 py-2">{{ $row->submission_status }}</td>
                                <td class="px-3 py-2">{{ $row->receive_on }}</td>
                                <td class="px-3 py-2">{{ $row->period_date }}</td>
                                <td class="px-3 py-2">{{ $row->company_reg_no }}</td>
                                <td class="px-3 py-2">{{ $row->corporation_name }}</td>
                                <td class="px-3 py-2">{{ $row->annual_meeting }}</td>
                                <td class="px-3 py-2">{{ $row->meeting_type }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-6 text-center text-gray-400">
                                    No GIS records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <!-- OVERLAY -->
    <div x-show="openPanel"
         x-transition.opacity
         class="fixed inset-0 bg-black/40 z-40"
         @click="openPanel=false"
         style="display:none">
    </div>

    <!-- SLIDE PANEL -->
    <div x-show="openPanel"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 bottom-0 w-[430px] bg-white border-l border-gray-300 shadow-2xl z-50"
         style="display:none">

        <form action="{{ route('gis.store') }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
            @csrf

            <div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Add GIS Record</h2>

                <button type="button" @click="openPanel=false">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4">

                <input name="uploaded_by" placeholder="Uploaded By" class="w-full border rounded p-2">

                <select name="submission_status" class="w-full border rounded p-2">
                    <option>Submitted</option>
                    <option>Received</option>
                    <option>Pending</option>
                </select>

                <input name="receive_on" type="date" class="w-full border rounded p-2">

                <input name="period_date" placeholder="Period Date" class="w-full border rounded p-2">

                <input name="company_reg_no" placeholder="Company Reg No" class="w-full border rounded p-2">

                <input name="corporation_name" placeholder="Corporation Name" class="w-full border rounded p-2">

                <input name="annual_meeting" type="date" class="w-full border rounded p-2">

                <select name="meeting_type" class="w-full border rounded p-2">
                    <option>Regular Annual Meeting</option>
                    <option>Special Meeting</option>
                </select>

                <input type="file" name="file" class="w-full">

            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-3">

                <button type="button" @click="openPanel=false"
                        class="px-4 py-2 border rounded">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                    Save
                </button>

            </div>

        </form>

    </div>

</div>

@endsection