@extends('layouts.app')

@section('title', 'Sales & Marketing | Earner Profile')

@section('content')
<div class="flex-1 overflow-y-auto p-6">
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $earner->full_name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Earner Profile
                    </p>
                </div>

                <a href="{{ route('sales-marketing.earners.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-white border border-gray-200 rounded-2xl p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Basic Information</h2>

                <div>
                    <p class="text-xs text-gray-500">Full Name</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $earner->full_name }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Source Type</p>
                    <p class="text-sm text-gray-900 capitalize">{{ $earner->source_type }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm text-gray-900">{{ $earner->email ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Mobile Number</p>
                    <p class="text-sm text-gray-900">{{ $earner->mobile_number ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">TIN</p>
                    <p class="text-sm text-gray-900">{{ $earner->tin ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $earner->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $earner->status }}
                    </span>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Bank Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Bank Name</p>
                        <p class="text-sm text-gray-900">{{ $earner->bank_name ?: '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Account Name</p>
                        <p class="text-sm text-gray-900">{{ $earner->account_name ?: '—' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Account Number</p>
                        <p class="text-sm text-gray-900">{{ $earner->account_number ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Transactions</h2>
                    <p class="text-xs text-gray-500 mt-1">This will show all related IDA transactions later.</p>
                </div>

                <button
                    type="button"
                    disabled
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl cursor-not-allowed"
                >
                    <i class="fas fa-wallet"></i>
                    Request for Payout
                </button>
            </div>

            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                No transactions yet.
            </div>
        </div>
    </div>
</div>
@endsection