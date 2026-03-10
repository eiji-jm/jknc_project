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
BYLAWS
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

<button
@click="openPanel = true"
class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">

<span class="text-base leading-none">+</span>
SEC-BYLAWS

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

<th class="px-2 py-2 font-semibold">Date Upload</th>
<th class="px-2 py-2 font-semibold">Uploaded By</th>
<th class="px-2 py-2 font-semibold">Company Reg No.</th>
<th class="px-2 py-2 font-semibold">Corporation Name</th>
<th class="px-2 py-2 font-semibold">Type of Formation</th>
<th class="px-2 py-2 font-semibold">SEC-AOI Version</th>
<th class="px-2 py-2 font-semibold">Type of Version</th>
<th class="px-2 py-2 font-semibold">Date of Version</th>
<th class="px-2 py-2 font-semibold">Regular ASM</th>
<th class="px-2 py-2 font-semibold">Notice Time</th>
<th class="px-2 py-2 font-semibold">Regular BODM</th>
<th class="px-2 py-2 font-semibold">Notice Time</th>

</tr>

</thead>


<tbody>

@foreach($records as $row)

<tr
data-url="{{ route('corporate.bylaws.show',$row->id) }}"
onclick="window.location.href=this.dataset.url"
class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer">

<td class="px-2 py-2">{{ $row->date_upload }}</td>

<td class="px-2 py-2 font-semibold text-gray-800">
{{ $row->uploaded_by }}
</td>

<td class="px-2 py-2">
{{ $row->company_reg_no }}
</td>

<td class="px-2 py-2">
{{ $row->corporation_name }}
</td>

<td class="px-2 py-2">
{{ $row->type_of_formation }}
</td>

<td class="px-2 py-2">
{{ $row->aoi_version }}
</td>

<td class="px-2 py-2">
{{ $row->aoi_type }}
</td>

<td class="px-2 py-2">
{{ $row->aoi_date }}
</td>

<td class="px-2 py-2">
{{ $row->regular_asm }}
</td>

<td class="px-2 py-2">
{{ $row->asm_notice }}
</td>

<td class="px-2 py-2">
{{ $row->regular_bodm }}
</td>

<td class="px-2 py-2">
{{ $row->bodm_notice }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>


<!-- OVERLAY -->
<div
x-show="openPanel"
x-transition.opacity
class="fixed inset-0 z-[70] bg-black/35"
style="display:none;"
@click="openPanel = false">
</div>


<!-- SLIDE PANEL -->
<div
x-show="openPanel"
x-transition:enter="transform transition ease-out duration-300"
x-transition:enter-start="translate-x-full"
x-transition:enter-end="translate-x-0"
x-transition:leave="transform transition ease-in duration-200"
x-transition:leave-start="translate-x-0"
x-transition:leave-end="translate-x-full"
class="fixed top-0 right-0 bottom-0 z-[80] w-[430px] bg-white border-l border-gray-300 shadow-2xl"
style="display:none;">


<form action="{{ route('corporate.bylaws.store') }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">

@csrf

<div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">

<h2 class="text-[26px] font-semibold text-gray-900 leading-none">
Add Bylaws Record
</h2>

<button
type="button"
@click="openPanel = false"
class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center transition">
<i class="fas fa-times text-sm"></i>
</button>

</div>


<div class="flex-1 overflow-y-auto px-6 py-6">

<div class="space-y-5">


<input type="text" name="corporation_name" placeholder="Corporation Name"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="company_reg_no" placeholder="Company Reg No."
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="type_of_formation" placeholder="Type of Formation"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="aoi_version" placeholder="SEC-AOI Version"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="aoi_type" placeholder="Type of Version"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="date" name="aoi_date"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="regular_asm" placeholder="Regular ASM"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="asm_notice" placeholder="ASM Notice Time"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="regular_bodm" placeholder="Regular BODM"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="bodm_notice" placeholder="BODM Notice Time"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="text" name="uploaded_by" placeholder="Uploaded By"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">

<input type="date" name="date_upload"
class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">


<label class="block text-sm text-gray-600">Upload File</label>

<input type="file" name="file_upload"
class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">


</div>

</div>


<div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">

<button
type="button"
@click="openPanel = false"
class="px-6 py-2 border border-gray-300 rounded-md text-sm">
Cancel
</button>

<button
type="submit"
class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm">
Save
</button>

</div>

</form>

</div>

</div>

@endsection