@extends('layouts.app')

@section('content')

<div class="w-full px-6 mt-6">

<h2 class="text-lg font-semibold mb-4">Stockholders</h2>

<div class="border border-gray-200 rounded-lg overflow-hidden bg-white">

<table class="min-w-full text-sm text-left">

<thead class="bg-gray-50">
<tr>
<th class="px-4 py-2">Stockholder Name</th>
<th class="px-4 py-2">Nationality</th>
<th class="px-4 py-2">Shares Owned</th>
<th class="px-4 py-2">Amount Paid</th>
</tr>
</thead>

<tbody>

<tr class="border-t">
<td class="px-4 py-2">John Kelly</td>
<td class="px-4 py-2">Filipino</td>
<td class="px-4 py-2">100,000</td>
<td class="px-4 py-2">₱1,000,000</td>
</tr>

</tbody>

</table>

</div>

</div>

@endsection