@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="mx-auto max-w-6xl space-y-4 px-4">
        @if (session('success'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">
            <h1 class="text-xl font-semibold text-gray-900">Specimen Signature Form</h1>
            <p class="mt-1 text-sm text-gray-500">Complete the specimen signature form below. Your submission will update the contact KYC record.</p>
        </div>

        <form method="POST" action="{{ $clientFormAction }}" class="space-y-4">
            @csrf
            @include('contacts.partials.specimen-signature-card', [
                'form' => $specimenForm,
                'readonly' => false,
            ])

            <div class="flex items-center justify-end border-t border-gray-100 pt-4">
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                    Submit Specimen Form
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
