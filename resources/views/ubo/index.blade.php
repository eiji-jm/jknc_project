@extends('layouts.app')

@section('content')

<h4>UBO FORM</h4>

<table class="table table-bordered bg-white">

<thead>

<tr>
<th>Complete Name</th>
<th>Specific Residential Address</th>
<th>Nationality</th>
<th>Date of Birth</th>
<th>Tax Identification No.</th>
<th>% Ownership</th>
<th>Type</th>
<th>Category</th>
<th>Action</th>
</tr>

</thead>

<tbody>

@foreach($ubos as $ubo)

<tr>

<td>{{ $ubo->complete_name }}</td>
<td>{{ $ubo->address }}</td>
<td>{{ $ubo->nationality }}</td>
<td>{{ $ubo->date_of_birth }}</td>
<td>{{ $ubo->tax_identification_no }}</td>
<td>{{ $ubo->ownership_percentage }}%</td>
<td>{{ $ubo->ownership_type }}</td>
<td>{{ $ubo->category }}</td>

<td>

<a href="{{ route('ubo.edit',$ubo->id) }}" class="btn btn-sm btn-warning">Edit</a>

<form action="{{ route('ubo.destroy',$ubo->id) }}" method="POST" style="display:inline">
@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">Delete</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

{{ $ubos->links() }}

@endsection

<div class="modal fade" id="addModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form action="{{ route('ubo.store') }}" method="POST">

@csrf

<div class="modal-header">
<h5>Add UBO</h5>
</div>

<div class="modal-body">

<input name="complete_name" class="form-control mb-2" placeholder="Complete Name">

<input name="address" class="form-control mb-2" placeholder="Address">

<input name="nationality" class="form-control mb-2" placeholder="Nationality">

<input type="date" name="date_of_birth" class="form-control mb-2">

<input name="tax_identification_no" class="form-control mb-2" placeholder="TIN">

<input name="ownership_percentage" class="form-control mb-2" placeholder="Ownership %">

<select name="ownership_type" class="form-control mb-2">
<option>Direct</option>
<option>Indirect</option>
</select>

<input name="category" class="form-control mb-2" placeholder="Category">

</div>

<div class="modal-footer">

<button class="btn btn-success">Save</button>

</div>

</form>

</div>

</div>

</div>
