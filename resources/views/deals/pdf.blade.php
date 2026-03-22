@extends('layouts.app')

@section('content')
<div class="{{ $downloadMode ? 'bg-white p-0' : 'bg-[#f7f6f2] p-6' }}">
    <div class="{{ $downloadMode ? '' : 'mx-auto max-w-6xl space-y-4' }}">
        @if (! $downloadMode)
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
                Deal form preview uses structured Consulting & Deal Form data.
            </div>
        @endif

        @include('deals.partials.deal-form-document', ['dealFormData' => $dealFormData])
    </div>
</div>

@if ($downloadMode)
    <script>
        window.addEventListener('load', () => window.print());
    </script>
@endif
@endsection
