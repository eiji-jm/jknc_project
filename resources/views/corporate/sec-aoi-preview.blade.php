@extends('layouts.app')

@section('content')

<div class="w-full px-6 py-6">

<h1 class="text-2xl font-semibold mb-6">
SEC – Articles Of Incorporation
</h1>


<div class="grid grid-cols-3 gap-6">


<!-- PDF VIEWER -->
<div class="col-span-2 bg-white border rounded-lg p-4">

<iframe
src="{{ asset('storage/'.$record->file_path) }}"
class="w-full h-[700px] border rounded">
</iframe>

</div>



<!-- INFORMATION PANEL -->
<div class="bg-white border rounded-lg p-6 space-y-4">

<h2 class="text-lg font-semibold mb-4">
AOI Information
</h2>

<div class="flex justify-between">
<span class="text-gray-500">Corporation</span>
<span class="font-medium">{{ $record->corporation_name }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Company Reg No.</span>
<span>{{ $record->company_reg_no }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Principal Address</span>
<span>{{ $record->principal_address }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Par Value</span>
<span>{{ $record->par_value }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Authorized Capital</span>
<span>{{ $record->authorized_capital_stock }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Directors</span>
<span>{{ $record->directors }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Formation</span>
<span>{{ $record->type_of_formation }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">AOI Version</span>
<span>{{ $record->aoi_version }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Version Type</span>
<span>{{ $record->aoi_type }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Uploaded By</span>
<span>{{ $record->uploaded_by }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Date Upload</span>
<span>{{ $record->date_upload }}</span>
</div>


<div class="pt-4">

<a href="{{ asset('storage/'.$record->file_path) }}"
download
class="block w-full text-center bg-blue-600 text-white py-2 rounded-md">

Download File

</a>

</div>

</div>


</div>

</div>

@endsection