@extends('layouts.app')

@section('content')

<div class="w-full px-6 py-6" x-data="{ tab:null, panel:null }">

<h1 class="text-2xl font-semibold mb-6">
General Information Sheet (GIS)
</h1>

<!-- TABS -->
<div class="flex gap-6 border-b text-sm font-medium mb-6">

<button
@click="tab = tab === 'capital' ? null : 'capital'"
:class="tab=='capital' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
class="pb-2">
Capital Structure
</button>

<button
@click="tab = tab === 'directors' ? null : 'directors'"
:class="tab=='directors' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
class="pb-2">
Board Of Directors / Officers
</button>

<button
@click="tab = tab === 'stockholders' ? null : 'stockholders'"
:class="tab=='stockholders' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
class="pb-2">
Stockholders
</button>

</div>



<!-- CAPITAL STRUCTURE -->
<div x-show="tab=='capital'" class="space-y-8 mb-10">

<!-- AUTHORIZED -->
<div class="bg-white border rounded-lg">

<div class="flex justify-between items-center px-6 py-4 border-b">
<h3 class="text-sm font-semibold uppercase text-gray-800">
Authorized Capital Stock
</h3>

<button
@click="panel='authorized'"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
Add
</button>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm border border-gray-300">

<thead class="bg-gray-50 text-xs uppercase">
<tr>
<th class="border px-4 py-3 text-left">Type of Shares</th>
<th class="border px-4 py-3 text-left">Number of Shares</th>
<th class="border px-4 py-3 text-left">Par/Stated Value</th>
<th class="border px-4 py-3 text-left">Amount (Php)</th>
</tr>
</thead>

<tbody>

@foreach($gis->authorizedCapital as $row)
<tr class="hover:bg-blue-50">
<td class="border px-4 py-3">{{ $row->share_type }}</td>
<td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
<td class="border px-4 py-3">{{ $row->par_value }}</td>
<td class="border px-4 py-3">{{ $row->amount }}</td>
</tr>
@endforeach

</tbody>

</table>

</div>
</div>



<!-- SUBSCRIBED -->
<div class="bg-white border rounded-lg">

<div class="flex justify-between items-center px-6 py-4 border-b">

<h3 class="text-sm font-semibold uppercase text-gray-800">
Subscribed Capital
</h3>

<button
@click="panel='subscribed'"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
Add
</button>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm border border-gray-300">

<thead class="bg-gray-50 text-xs uppercase">

<tr>
<th class="border px-4 py-3">Nationality</th>
<th class="border px-4 py-3">No. of Stockholders</th>
<th class="border px-4 py-3">Type of Shares</th>
<th class="border px-4 py-3">Number of Shares</th>
<th class="border px-4 py-3">Par Value</th>
<th class="border px-4 py-3">Amount (Php)</th>
<th class="border px-4 py-3">% Ownership</th>
</tr>

</thead>

<tbody>

@foreach($gis->subscribedCapital as $row)

<tr class="hover:bg-blue-50">
<td class="border px-4 py-3">{{ $row->nationality }}</td>
<td class="border px-4 py-3">{{ $row->no_of_stockholders }}</td>
<td class="border px-4 py-3">{{ $row->share_type }}</td>
<td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
<td class="border px-4 py-3">{{ $row->par_value }}</td>
<td class="border px-4 py-3">{{ $row->amount }}</td>
<td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>
</div>



<!-- PAID UP -->
<div class="bg-white border rounded-lg">

<div class="flex justify-between items-center px-6 py-4 border-b">

<h3 class="text-sm font-semibold uppercase text-gray-800">
Paid-Up Capital
</h3>

<button
@click="panel='paidup'"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
Add
</button>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm border border-gray-300">

<thead class="bg-gray-50 text-xs uppercase">

<tr>
<th class="border px-4 py-3">Nationality</th>
<th class="border px-4 py-3">No. of Stockholders</th>
<th class="border px-4 py-3">Type of Shares</th>
<th class="border px-4 py-3">Number of Shares</th>
<th class="border px-4 py-3">Par Value</th>
<th class="border px-4 py-3">Amount (Php)</th>
<th class="border px-4 py-3">% Ownership</th>
</tr>

</thead>

<tbody>

@foreach($gis->paidUpCapital as $row)

<tr class="hover:bg-blue-50">
<td class="border px-4 py-3">{{ $row->nationality }}</td>
<td class="border px-4 py-3">{{ $row->no_of_stockholders }}</td>
<td class="border px-4 py-3">{{ $row->share_type }}</td>
<td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
<td class="border px-4 py-3">{{ $row->par_value }}</td>
<td class="border px-4 py-3">{{ $row->amount }}</td>
<td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>
</div>

</div>



<!-- DIRECTORS -->
<div x-show="tab=='directors'" class="bg-white border rounded-lg mb-10">

<div class="flex justify-between px-6 py-4 border-b">

<h3 class="text-sm font-semibold uppercase text-gray-800">
Board of Directors / Officers
</h3>

<button
@click="panel='director'"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
Add
</button>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm border border-gray-300">

<thead class="bg-gray-50 text-xs uppercase">
<tr>
<th class="border px-4 py-3">Officer Name</th>
<th class="border px-4 py-3">Address</th>
<th class="border px-4 py-3">Gender</th>
<th class="border px-4 py-3">Nationality</th>
<th class="border px-4 py-3">INCR</th>
<th class="border px-4 py-3">Stockholder</th>
<th class="border px-4 py-3">Board</th>
<th class="border px-4 py-3">Type of Officer</th>
<th class="border px-4 py-3">Committee</th>
<th class="border px-4 py-3">TIN</th>
</tr>
</thead>

<tbody>

@foreach($gis->directors as $row)

<tr class="hover:bg-blue-50">
<td class="border px-4 py-3">{{ $row->officer_name }}</td>
<td class="border px-4 py-3">{{ $row->address }}</td>
<td class="border px-4 py-3">{{ $row->gender }}</td>
<td class="border px-4 py-3">{{ $row->nationality }}</td>
<td class="border px-4 py-3">{{ $row->incr }}</td>
<td class="border px-4 py-3">{{ $row->stockholder }}</td>
<td class="border px-4 py-3">{{ $row->board }}</td>
<td class="border px-4 py-3">{{ $row->officer_type }}</td>
<td class="border px-4 py-3">{{ $row->committee }}</td>
<td class="border px-4 py-3">{{ $row->tin }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>

</div>



<!-- STOCKHOLDERS -->
<div x-show="tab=='stockholders'" class="bg-white border rounded-lg mb-10">

<div class="flex justify-between px-6 py-4 border-b">

<h3 class="text-sm font-semibold uppercase text-gray-800">
Stockholders
</h3>

<button
@click="panel='stockholder'"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
Add
</button>

</div>

<div class="overflow-x-auto">

<table class="w-full text-sm border border-gray-300">

<thead class="bg-gray-50 text-xs uppercase">

<tr>
<th class="border px-4 py-3">Stockholder Name</th>
<th class="border px-4 py-3">Address</th>
<th class="border px-4 py-3">Gender</th>
<th class="border px-4 py-3">Nationality</th>
<th class="border px-4 py-3">INCR</th>
<th class="border px-4 py-3">Type</th>
<th class="border px-4 py-3">Shares</th>
<th class="border px-4 py-3">Amount (Php)</th>
<th class="border px-4 py-3">% Ownership</th>
<th class="border px-4 py-3">Amount Paid</th>
<th class="border px-4 py-3">TIN</th>
</tr>

</thead>

<tbody>

@foreach($gis->stockholders as $row)

<tr class="hover:bg-blue-50">
<td class="border px-4 py-3">{{ $row->stockholder_name }}</td>
<td class="border px-4 py-3">{{ $row->address }}</td>
<td class="border px-4 py-3">{{ $row->gender }}</td>
<td class="border px-4 py-3">{{ $row->nationality }}</td>
<td class="border px-4 py-3">{{ $row->incr }}</td>
<td class="border px-4 py-3">{{ $row->share_type }}</td>
<td class="border px-4 py-3">{{ $row->shares }}</td>
<td class="border px-4 py-3">{{ $row->amount }}</td>
<td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
<td class="border px-4 py-3">{{ $row->amount_paid }}</td>
<td class="border px-4 py-3">{{ $row->tin }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>

</div>



<!-- SLIDE PANEL -->
<div x-show="panel" class="fixed inset-0 bg-black/40 z-50">

<div
@click.away="panel=null"
class="absolute right-0 top-0 h-full w-[420px] bg-white shadow-xl p-6 overflow-y-auto">

<h2 class="text-lg font-semibold mb-6">
Add Record
</h2>


<!-- AUTHORIZED -->
<form x-show="panel=='authorized'" action="{{ route('authorized.store') }}" method="POST" class="space-y-4">
@csrf
<input type="hidden" name="gis_id" value="{{ $gis->id }}">
<input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
<input name="number_of_shares" placeholder="Shares" class="border w-full p-2 rounded">
<input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
<input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
<button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
</form>


<!-- SUBSCRIBED -->
<form x-show="panel=='subscribed'" action="{{ route('subscribed.store') }}" method="POST" class="space-y-4">
@csrf
<input type="hidden" name="gis_id" value="{{ $gis->id }}">
<input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
<input name="stockholders" placeholder="No. of Stockholders" class="border w-full p-2 rounded">
<input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
<input name="shares" placeholder="Shares" class="border w-full p-2 rounded">
<input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
<input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
<input name="ownership" placeholder="% Ownership" class="border w-full p-2 rounded">
<button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
</form>


<!-- PAIDUP -->
<form x-show="panel=='paidup'" action="{{ route('paidup.store') }}" method="POST" class="space-y-4">
@csrf
<input type="hidden" name="gis_id" value="{{ $gis->id }}">
<input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
<input name="stockholders" placeholder="No. of Stockholders" class="border w-full p-2 rounded">
<input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
<input name="shares" placeholder="Shares" class="border w-full p-2 rounded">
<input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
<input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
<input name="ownership" placeholder="% Ownership" class="border w-full p-2 rounded">
<button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
</form>


<!-- DIRECTOR -->
<form x-show="panel=='director'" action="{{ route('director.store') }}" method="POST" class="space-y-4">

@csrf

<input type="hidden" name="gis_id" value="{{ $gis->id }}">

<input name="officer_name" placeholder="Officer Name" class="border w-full p-2 rounded">

<input name="address" placeholder="Address" class="border w-full p-2 rounded">

<input name="gender" placeholder="Gender" class="border w-full p-2 rounded">

<input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">

<input name="incr" placeholder="INCR (1 or 0)" class="border w-full p-2 rounded">

<input name="stockholder" placeholder="Stockholder (1 or 0)" class="border w-full p-2 rounded">

<input name="board" placeholder="Board Role" class="border w-full p-2 rounded">

<input name="officer_type" placeholder="Officer Type" class="border w-full p-2 rounded">

<input name="committee" placeholder="Committee" class="border w-full p-2 rounded">

<input name="tin" placeholder="TIN" class="border w-full p-2 rounded">

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Save
</button>

</form>


<!-- STOCKHOLDER -->
<form x-show="panel=='stockholder'" action="{{ route('stockholder.store') }}" method="POST" class="space-y-4">

@csrf

<input type="hidden" name="gis_id" value="{{ $gis->id }}">

<input name="stockholder_name" placeholder="Stockholder Name" class="border w-full p-2 rounded">

<input name="address" placeholder="Address" class="border w-full p-2 rounded">

<input name="gender" placeholder="Gender" class="border w-full p-2 rounded">

<input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">

<input name="incr" placeholder="INCR (1 or 0)" class="border w-full p-2 rounded">

<input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">

<input name="shares" placeholder="Shares" class="border w-full p-2 rounded">

<input name="amount" placeholder="Amount" class="border w-full p-2 rounded">

<input name="ownership_percentage" placeholder="% Ownership" class="border w-full p-2 rounded">

<input name="amount_paid" placeholder="Amount Paid" class="border w-full p-2 rounded">

<input name="tin" placeholder="TIN" class="border w-full p-2 rounded">

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Save
</button>

</form>

</div>
</div>



<!-- MAIN CONTENT -->
<div class="grid grid-cols-3 gap-6">

<div class="col-span-2 bg-white border rounded-lg p-4">

@if($gis->file)
<iframe src="{{ asset('storage/'.$gis->file) }}" class="w-full h-[700px] border rounded"></iframe>
@else
<div class="text-gray-400 text-center p-20">
No GIS file uploaded
</div>
@endif

</div>



<div class="bg-white border rounded-lg p-6 space-y-4 h-fit">

<h2 class="text-lg font-semibold mb-4">
GIS Information
</h2>

<div class="flex justify-between">
<span class="text-gray-500">Corporation</span>
<span>{{ $gis->corporation_name }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Company Reg No.</span>
<span>{{ $gis->company_reg_no }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Uploaded By</span>
<span>{{ $gis->uploaded_by }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Submission Status</span>
<span>{{ $gis->submission_status }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Receive On</span>
<span>{{ $gis->receive_on }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Period Date</span>
<span>{{ $gis->period_date }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Annual Meeting</span>
<span>{{ $gis->annual_meeting }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Meeting Type</span>
<span>{{ $gis->meeting_type }}</span>
</div>

</div>

</div>

</div>

@endsection