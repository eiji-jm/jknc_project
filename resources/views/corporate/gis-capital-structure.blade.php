@extends('layouts.app')

@section('content')

<div class="w-full px-6 mt-6">

<h2 class="text-lg font-semibold mb-4">Capital Structure</h2>

<div class="border border-gray-200 rounded-lg overflow-hidden bg-white">

<table class="min-w-full text-sm text-left">

<thead class="bg-gray-50">
<tr>
<th class="px-4 py-2">Class of Shares</th>
<th class="px-4 py-2">Authorized Capital</th>
<th class="px-4 py-2">Subscribed Capital</th>
<th class="px-4 py-2">Paid Up Capital</th>
</tr>
</thead>

<tbody>

<tr class="border-t">
<td class="px-4 py-2">Common</td>
<td class="px-4 py-2">1,000,000</td>
<td class="px-4 py-2">500,000</td>
<td class="px-4 py-2">300,000</td>
</tr>

</tbody>

</table>

</div>

</div>

@endsection