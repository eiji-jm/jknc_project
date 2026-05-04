@extends('layouts.app')
@section('title', 'Specimen Signature Preview')

@section('content')
<div class="{{ ($downloadMode ?? false) ? 'bg-white p-0' : 'bg-[#f7f6f2] p-6' }}">
    <div class="{{ ($downloadMode ?? false) ? '' : 'mx-auto max-w-6xl space-y-4' }}">
        @if (! ($downloadMode ?? false))
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">
                Printable Specimen Signature Form preview based on saved KYC data.
            </div>
        @endif

        @include('contacts.partials.specimen-signature-card', [
            'form' => $specimenForm,
            'readonly' => true,
            'contact' => $contact,
        ])
    </div>
</div>

@if ($downloadMode ?? false)
    <script>
        window.addEventListener('load', () => window.print());
    </script>
@endif
@endsection
