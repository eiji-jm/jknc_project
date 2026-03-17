@extends('layouts.app')

@section('content')
@php
    $display = static fn ($value) => filled($value) ? $value : '-';
    $currency = static fn ($value) => filled($value) ? 'P'.number_format((float) $value, 2) : '-';
@endphp

<div class="bg-white p-6">
    <div class="mb-5 flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <p class="text-sm text-gray-500">Deals / Preview</p>
            <h1 class="text-3xl font-semibold text-gray-900">Deal Preview</h1>
            <p class="mt-1 text-sm text-gray-500">Review the combined contact and deal information before final saving.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('deals.index', ['open_deal_modal' => 1]) }}" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to Edit</a>
            <form method="POST" action="{{ route('deals.draft') }}">
                @csrf
                @foreach ($hiddenFields as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="h-10 rounded-lg border border-amber-200 bg-amber-50 px-4 text-sm font-medium text-amber-700 hover:bg-amber-100">Save Draft</button>
            </form>
            <form method="POST" action="{{ route('deals.store') }}">
                @csrf
                @foreach ($hiddenFields as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Confirm and Save Deal</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.7fr_0.85fr]">
        <div class="space-y-4">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                </div>
                <div class="grid gap-4 px-5 py-5 text-sm md:grid-cols-2">
                    <div><p class="text-xs text-gray-500">Customer Type</p><p class="font-medium text-gray-900">{{ $display($draft['customer_type'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Salutation</p><p class="font-medium text-gray-900">{{ $display($draft['salutation'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">First Name</p><p class="font-medium text-gray-900">{{ $display($draft['first_name'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Middle Name</p><p class="font-medium text-gray-900">{{ $display($draft['middle_name'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Last Name</p><p class="font-medium text-gray-900">{{ $display($draft['last_name'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Email</p><p class="font-medium text-gray-900">{{ $display($draft['email'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Mobile</p><p class="font-medium text-gray-900">{{ $display($draft['mobile'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Position / Designation</p><p class="font-medium text-gray-900">{{ $display($draft['position'] ?? null) }}</p></div>
                    <div class="md:col-span-2"><p class="text-xs text-gray-500">Address</p><p class="font-medium text-gray-900">{{ $display($draft['address'] ?? null) }}</p></div>
                    <div class="md:col-span-2"><p class="text-xs text-gray-500">Company</p><p class="font-medium text-gray-900">{{ $display($draft['company_name'] ?? null) }}</p></div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Deal Information</h2>
                </div>
                <div class="grid gap-4 px-5 py-5 text-sm md:grid-cols-2">
                    <div><p class="text-xs text-gray-500">Deal Name</p><p class="font-medium text-gray-900">{{ $display($draft['deal_name'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Service Type</p><p class="font-medium text-gray-900">{{ $display($draft['service_area'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Services</p><p class="font-medium text-gray-900">{{ $display($draft['services'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Product Type</p><p class="font-medium text-gray-900">{{ $display($draft['products'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Engagement Type</p><p class="font-medium text-gray-900">{{ $display($draft['engagement_type'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Deal Value</p><p class="font-medium text-blue-700">{{ $currency($draft['total_estimated_engagement_value'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Pricing Model</p><p class="font-medium text-gray-900">{{ $display($draft['engagement_type'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Payment Terms</p><p class="font-medium text-gray-900">{{ $display($draft['payment_terms'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Timeline</p><p class="font-medium text-gray-900">{{ $display($draft['estimated_duration'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Lead Source</p><p class="font-medium text-gray-900">{{ $display($draft['lead_source'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Referred By</p><p class="font-medium text-gray-900">{{ $display($draft['referred_by'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Referral Type</p><p class="font-medium text-gray-900">{{ $display($draft['referral_type'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Assigned Consultant</p><p class="font-medium text-gray-900">{{ $display($draft['assigned_consultant'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Assigned Associate</p><p class="font-medium text-gray-900">{{ $display($draft['assigned_associate'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Handling Team</p><p class="font-medium text-gray-900">{{ $display($draft['service_department_unit'] ?? null) }}</p></div>
                    <div class="md:col-span-2"><p class="text-xs text-gray-500">Notes</p><p class="font-medium text-gray-900">{{ $display($draft['consultant_notes'] ?? null) }}</p></div>
                    <div class="md:col-span-2"><p class="text-xs text-gray-500">Scope of Work</p><p class="font-medium text-gray-900">{{ $display($draft['scope_of_work'] ?? null) }}</p></div>
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">System Summary</h2>
                </div>
                <div class="space-y-4 px-5 py-5 text-sm">
                    <div><p class="text-xs text-gray-500">Deal Reference Number</p><p class="font-medium text-gray-900">{{ $display($draft['deal_reference_number'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Selected Owner</p><p class="font-medium text-gray-900">{{ $display($draft['selected_owner'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Prepared By</p><p class="font-medium text-gray-900">{{ $display($draft['prepared_by'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Created Date</p><p class="font-medium text-gray-900">{{ $display($draft['created_date'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Status</p><p class="font-medium text-blue-700">{{ $display($draft['status'] ?? null) }}</p></div>
                    <div><p class="text-xs text-gray-500">Optional Remarks</p><p class="font-medium text-gray-900">{{ $display($draft['optional_remarks'] ?? null) }}</p></div>
                </div>
            </section>

            <section class="rounded-xl border border-blue-100 bg-blue-50 px-5 py-4 text-sm text-blue-800">
                Preview uses the current unsaved form values plus the selected contact record. Nothing is stored until you click `Confirm and Save Deal`.
            </section>
        </aside>
    </div>
</div>
@endsection
