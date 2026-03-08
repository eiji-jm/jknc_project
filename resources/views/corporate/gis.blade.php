@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ openPanel: false }">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

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
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    bylaws
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
                    <button @click="openPanel = true"
                        class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        SEC-GIS
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

        <div class="bg-gray-50 min-h-[680px]">
            <div class="p-3">
                <div class="overflow-x-auto border border-gray-200 rounded-md bg-white">
                    <table class="min-w-full text-[10px] text-left text-gray-700">
                        <thead class="bg-white border-b border-gray-200 align-top">
                            <tr>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Date Upload</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Uploaded by</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Sec-Submission Status</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Sec-Receive on</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Sec-Period Date</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Company Reg No.</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Corporation Name</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">DATE OF ANNUAL MEETING</th>
                                <th class="px-2 py-2 font-semibold whitespace-nowrap">Type of Meeting</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($gis ?? [] as $row)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 align-top">

                        <td class="px-2 py-2">{{ $row->created_at->format('M d, Y') }}</td>
                        <td class="px-2 py-2 font-semibold text-gray-800">{{ $row->uploaded_by }}</td>
                        <td class="px-2 py-2">{{ $row->submission_status }}</td>
                        <td class="px-2 py-2">{{ $row->receive_on }}</td>
                        <td class="px-2 py-2">{{ $row->period_date }}</td>
                        <td class="px-2 py-2">{{ $row->company_reg_no }}</td>
                        <td class="px-2 py-2">{{ $row->corporation_name }}</td>
                        <td class="px-2 py-2">{{ $row->annual_meeting }}</td>
                        <td class="px-2 py-2">{{ $row->meeting_type }}</td>

                        </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

<div x-show="openPanel" x-transition.opacity class="fixed inset-0 z-[70] bg-black/35" style="display:none;" @click="openPanel = false"></div>

<div x-show="openPanel"
     x-transition:enter="transform transition ease-out duration-300"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transform transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     class="fixed top-0 right-0 bottom-0 z-[80] w-[430px] bg-white border-l border-gray-300 shadow-2xl"
     style="display:none;">

<form action="{{ route('gis.store') }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
@csrf

<div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
<h2 class="text-[26px] font-semibold text-gray-900 leading-none">Add GIS Record</h2>

<button type="button" @click="openPanel = false"
class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 flex items-center justify-center">
<i class="fas fa-times text-sm"></i>
</button>
</div>

<div class="flex-1 overflow-y-auto px-6 py-6">
<div class="space-y-5">

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">Uploaded By</label>
<input name="uploaded_by" type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
</div>

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">SEC-Submission Status</label>
<select name="submission_status" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
<option>Submitted</option>
<option>Received</option>
<option>Pending</option>
</select>
</div>

<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">SEC-Receive On</label>
<input name="receive_on" type="date" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
</div>

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">SEC-Period Date</label>
<input name="period_date" type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="2026 Filing Period">
</div>
</div>

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">Company Reg No.</label>
<input name="company_reg_no" type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
</div>

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">Corporation Name</label>
<input name="corporation_name" type="text" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
</div>

<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">Date of Annual Meeting</label>
<input name="annual_meeting" type="date" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
</div>

<div>
<label class="block text-[13px] font-medium text-gray-700 mb-2">Type of Meeting</label>
<select name="meeting_type" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm bg-white">
<option>Regular Annual Meeting</option>
<option>Special Meeting</option>
</select>
</div>
</div>

<div class="pt-2">
<label class="block text-[13px] font-medium text-gray-700 mb-2">File Upload</label>
<input type="file" name="file" class="w-full border border-gray-300 rounded-md p-2">
</div>

</div>
</div>

<div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">

<button type="button" @click="openPanel = false"
class="min-w-[92px] px-6 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
Cancel
</button>

<button type="submit"
class="min-w-[92px] px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
Save
</button>

</div>

</form>

</div>

</div>
@endsection