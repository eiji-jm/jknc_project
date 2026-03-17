@extends('layouts.app')

@section('content')
@php
    $display = static fn ($value) => filled($value) ? $value : '-';
@endphp

<div class="bg-[#f7f6f2] p-6">
    <div class="mx-auto max-w-5xl rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-200 px-8 py-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.22em] text-stone-400">Client Information Form</p>
                    <h1 class="mt-2 text-3xl font-semibold text-stone-900">{{ $display($cifData['company_name'] ?? $contact->company_name ?? null) }}</h1>
                    <p class="mt-1 text-sm text-stone-500">Structured CIF preview generated from saved contact data.</p>
                </div>
                <div class="text-right text-sm text-stone-500">
                    <p>Contact ID: {{ $contact->id }}</p>
                    <p>KYC Status: {{ $display($cifData['kyc_status'] ?? null) }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-8 px-8 py-8 lg:grid-cols-[1.15fr_0.85fr]">
            <section class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-stone-900">Client Information</h2>
                    <div class="mt-4 grid gap-4 text-sm md:grid-cols-2">
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">CIF No.</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['cif_no'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">TIN</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['tin'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Customer Type</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['customer_type'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Salutation</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['salutation'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">First Name</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['first_name'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Middle Name</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['middle_name'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Last Name</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['last_name'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Position</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['position'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Email</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['email'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Mobile</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['mobile'] ?? null) }}</p></div>
                        <div class="md:col-span-2"><p class="text-xs uppercase tracking-wide text-stone-400">Address</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['address'] ?? null) }}</p></div>
                        <div class="md:col-span-2"><p class="text-xs uppercase tracking-wide text-stone-400">Company Address</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['company_address'] ?? null) }}</p></div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-stone-900">Remarks</h2>
                    <div class="mt-4 rounded-xl border border-stone-200 bg-stone-50 px-4 py-4 text-sm text-stone-700">
                        {{ $display($cifData['remarks'] ?? null) }}
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="rounded-xl border border-stone-200 bg-stone-50 px-5 py-5">
                    <h2 class="text-base font-semibold text-stone-900">Verification</h2>
                    <div class="mt-4 space-y-4 text-sm">
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Owner</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['owner_name'] ?? $contact->owner_name ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">KYC Status</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['kyc_status'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Date Verified</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['date_verified'] ?? null) }}</p></div>
                        <div><p class="text-xs uppercase tracking-wide text-stone-400">Verified By</p><p class="mt-1 font-medium text-stone-900">{{ $display($cifData['verified_by'] ?? null) }}</p></div>
                    </div>
                </div>

                <div class="rounded-xl border border-stone-200 bg-white px-5 py-5">
                    <h2 class="text-base font-semibold text-stone-900">Supporting Documents</h2>
                    <div class="mt-4 space-y-3 text-sm">
                        @forelse ($cifDocuments as $document)
                            <div class="rounded-lg border border-stone-200 px-3 py-3">
                                <p class="font-medium text-stone-900">{{ $document['label'] ?? 'Attachment' }}</p>
                                <p class="text-xs text-stone-500">{{ $document['file_name'] ?? '-' }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $document['uploaded_at'] ?? '-' }}</p>
                            </div>
                        @empty
                            <p class="text-stone-500">No supporting documents uploaded.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@if ($downloadMode)
    <script>
        window.addEventListener('load', () => window.print());
    </script>
@endif
@endsection
