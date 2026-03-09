@extends('layouts.app')

@section('content')

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ openPanel:false, tab:'gis' }">

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


<!-- SUB MODULES -->
<div class="flex gap-8 px-6 pt-4 text-sm text-gray-600">

<button
@click="tab='capital'"
:class="tab=='capital' ? 'border-b-2 border-blue-600 text-black' : ''"
class="pb-2">
Capital Structure
</button>

<button
@click="tab='directors'"
:class="tab=='directors' ? 'border-b-2 border-blue-600 text-black' : ''"
class="pb-2">
Board Of Directors/Officers
</button>

<button
@click="tab='stockholders'"
:class="tab=='stockholders' ? 'border-b-2 border-blue-600 text-black' : ''"
class="pb-2">
Stockholders
</button>

</div>


<div class="bg-gray-50 min-h-[680px] px-6 py-4">


<!-- GIS TABLE -->
<div x-show="tab=='gis'">

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

@foreach($gis ?? [] as $row)

<tr class="border-b hover:bg-gray-50">

<td class="px-3 py-2">{{ $row->created_at->format('M d, Y') }}</td>
<td class="px-3 py-2 font-medium">{{ $row->uploaded_by }}</td>
<td class="px-3 py-2">{{ $row->submission_status }}</td>
<td class="px-3 py-2">{{ $row->receive_on }}</td>
<td class="px-3 py-2">{{ $row->period_date }}</td>
<td class="px-3 py-2">{{ $row->company_reg_no }}</td>
<td class="px-3 py-2">{{ $row->corporation_name }}</td>
<td class="px-3 py-2">{{ $row->annual_meeting }}</td>
<td class="px-3 py-2">{{ $row->meeting_type }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>


<!-- CAPITAL STRUCTURE -->
<div x-show="tab=='capital'">

<h2 class="text-sm font-semibold mb-3 mt-4">AUTHORIZED CAPITAL STOCK</h2>

<div class="overflow-x-auto bg-white border border-gray-200 rounded mb-6">

<table class="min-w-full text-[11px]">

<thead class="bg-gray-50 border-b">
<tr>
<th class="px-3 py-2 text-left">Type Of Shares</th>
<th class="px-3 py-2 text-left">Number Of Shares</th>
<th class="px-3 py-2 text-left">PAR/STATED VALUE</th>
<th class="px-3 py-2 text-left">AMOUNT (Php)</th>
</tr>
</thead>

<tbody>
<tr class="border-b">
<td class="px-3 py-2">Common</td>
<td class="px-3 py-2">1000</td>
<td class="px-3 py-2">100</td>
<td class="px-3 py-2">100000</td>
</tr>
</tbody>

</table>

</div>


<h2 class="text-sm font-semibold mb-3">SUBSCRIBED CAPITAL</h2>

<div class="overflow-x-auto bg-white border border-gray-200 rounded mb-6">

<table class="min-w-full text-[11px]">

<thead class="bg-gray-50 border-b">
<tr>
<th class="px-3 py-2 text-left">Nationality</th>
<th class="px-3 py-2 text-left">No. of Stockholders</th>
<th class="px-3 py-2 text-left">Type Of Shares</th>
<th class="px-3 py-2 text-left">Number Of Shares</th>
<th class="px-3 py-2 text-left">PAR/STATED VALUE</th>
<th class="px-3 py-2 text-left">AMOUNT</th>
<th class="px-3 py-2 text-left">% Ownership</th>
</tr>
</thead>

<tbody>
<tr class="border-b">
<td class="px-3 py-2">Filipino</td>
<td class="px-3 py-2">2</td>
<td class="px-3 py-2">Common</td>
<td class="px-3 py-2">500</td>
<td class="px-3 py-2">100</td>
<td class="px-3 py-2">50000</td>
<td class="px-3 py-2">50%</td>
</tr>
</tbody>

</table>

</div>


<h2 class="text-sm font-semibold mb-3">PAID-UP CAPITAL</h2>

<div class="overflow-x-auto bg-white border border-gray-200 rounded">

<table class="min-w-full text-[11px]">

<thead class="bg-gray-50 border-b">
<tr>
<th class="px-3 py-2 text-left">Nationality</th>
<th class="px-3 py-2 text-left">No. of Stockholders</th>
<th class="px-3 py-2 text-left">Type Of Shares</th>
<th class="px-3 py-2 text-left">Number Of Shares</th>
<th class="px-3 py-2 text-left">PAR/STATED VALUE</th>
<th class="px-3 py-2 text-left">AMOUNT</th>
<th class="px-3 py-2 text-left">% Ownership</th>
</tr>
</thead>

<tbody>
<tr class="border-b">
<td class="px-3 py-2">Filipino</td>
<td class="px-3 py-2">2</td>
<td class="px-3 py-2">Common</td>
<td class="px-3 py-2">500</td>
<td class="px-3 py-2">100</td>
<td class="px-3 py-2">50000</td>
<td class="px-3 py-2">50%</td>
</tr>
</tbody>

</table>

</div>

</div>


<!-- DIRECTORS -->
<div x-show="tab=='directors'">

<div class="overflow-x-auto bg-white border border-gray-200 rounded">

<table class="min-w-full text-[11px]">

<thead class="bg-gray-50 border-b">

<tr>
<th class="px-3 py-2 text-left">Officers Name</th>
<th class="px-3 py-2 text-left">Address</th>
<th class="px-3 py-2 text-left">Gender</th>
<th class="px-3 py-2 text-left">Nationality</th>
<th class="px-3 py-2 text-left">INCR</th>
<th class="px-3 py-2 text-left">Stockholder</th>
<th class="px-3 py-2 text-left">Board</th>
<th class="px-3 py-2 text-left">Type Of Officer</th>
<th class="px-3 py-2 text-left">Committees</th>
<th class="px-3 py-2 text-left">TIN</th>
</tr>

</thead>

<tbody>

<tr class="border-b">
<td class="px-3 py-2">Juan Dela Cruz</td>
<td class="px-3 py-2">Cebu City</td>
<td class="px-3 py-2">Male</td>
<td class="px-3 py-2">Filipino</td>
<td class="px-3 py-2">Yes</td>
<td class="px-3 py-2">Yes</td>
<td class="px-3 py-2">Director</td>
<td class="px-3 py-2">President</td>
<td class="px-3 py-2">Audit</td>
<td class="px-3 py-2">123456789</td>
</tr>

</tbody>

</table>

</div>

</div>


<!-- STOCKHOLDERS -->
<div x-show="tab=='stockholders'">

<div class="overflow-x-auto bg-white border border-gray-200 rounded">

<table class="min-w-full text-[11px]">

<thead class="bg-gray-50 border-b">

<tr>
<th class="px-3 py-2 text-left">Stockholders Name</th>
<th class="px-3 py-2 text-left">Address</th>
<th class="px-3 py-2 text-left">Gender</th>
<th class="px-3 py-2 text-left">Nationality</th>
<th class="px-3 py-2 text-left">INCR</th>
<th class="px-3 py-2 text-left">Type</th>
<th class="px-3 py-2 text-left">Number</th>
<th class="px-3 py-2 text-left">Amount (Php)</th>
<th class="px-3 py-2 text-left">% Ownership</th>
<th class="px-3 py-2 text-left">Amount Paid</th>
<th class="px-3 py-2 text-left">TIN</th>
</tr>

</thead>

<tbody>

<tr class="border-b">
<td class="px-3 py-2">Juan Dela Cruz</td>
<td class="px-3 py-2">Cebu City</td>
<td class="px-3 py-2">Male</td>
<td class="px-3 py-2">Filipino</td>
<td class="px-3 py-2">Yes</td>
<td class="px-3 py-2">Common</td>
<td class="px-3 py-2">100</td>
<td class="px-3 py-2">10000</td>
<td class="px-3 py-2">10%</td>
<td class="px-3 py-2">10000</td>
<td class="px-3 py-2">123456789</td>
</tr>

</tbody>

</table>

</div>

</div>


</div>
</div>

</div>

@endsection