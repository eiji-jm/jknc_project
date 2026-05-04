@extends('layouts.app')
@section('title', 'Company Activities')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])
        @include('company.partials.activities-app', ['company' => $company, 'activities' => $activities])
    </div>
</div>
@endsection
