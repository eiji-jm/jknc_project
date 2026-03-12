@extends('layouts.app')

@section('content')

<div class="w-full px-6 py-6">

<h1 class="text-2xl font-semibold mb-6">
SEC – Certificate Of Incorporation
</h1>


<div class="grid grid-cols-3 gap-6">


<!-- PDF VIEWER -->
<div class="col-span-2 bg-white border rounded-lg p-4">

<iframe
src="{{ asset($record->file_path) }}"
class="w-full h-[700px] border rounded">
</iframe>

</div>



<!-- INFORMATION PANEL -->
<div class="bg-white border rounded-lg p-6 space-y-4">

<h2 class="text-lg font-semibold mb-4">
Certificate Information
</h2>

<div class="flex justify-between">
<span class="text-gray-500">Corporation</span>
<span class="font-medium">{{ $record->corporate_name }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Company Reg No.</span>
<span>{{ $record->company_reg_no }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Date Upload</span>
<span>{{ $record->date_upload }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Date Created</span>
<span>{{ $record->created_at->format('M d, Y') }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Issued On</span>
<span>{{ $record->issued_on }}</span>
</div>

<div class="flex justify-between">
<span class="text-gray-500">Issued By</span>
<span>{{ $record->issued_by }}</span>
</div>


<div class="pt-4">

<a href="{{ asset($record->file_path) }}"
download
class="block w-full text-center bg-blue-600 text-white py-2 rounded-md">

Download File

</a>

</div>

</div>


</div>

</div>

@endsection