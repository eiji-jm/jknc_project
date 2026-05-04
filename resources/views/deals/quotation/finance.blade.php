@extends('layouts.app')
@section('title', 'Finance Quotation')

@section('content')
@php
    $formatCurrency = static fn ($amount): string => 'P'.number_format((float) $amount, 2);
    $status = $proposal->quotation_status ?: 'not_started';
    $isApproved = filled($proposal->quotation_approved_at) || $status === 'approved';
@endphp

<div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('deals.show', $deal) }}" class="font-semibold text-blue-700 hover:underline">Deal</a>
                <span>/</span>
                <span>{{ $deal->deal_code ?: 'Deal Quotation' }}</span>
            </div>
            <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Finance Quotation</p>
                    <h1 class="mt-1 text-2xl font-semibold text-slate-950">{{ $deal->company_name ?: $deal->deal_name ?: $deal->deal_code }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Upload the finance quotation and approve it for payment processing.</p>
                </div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700">
                    {{ str_replace('_', ' ', $status) }}
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Proposal Reference</h2>
                        <p class="mt-1 text-sm text-slate-500">Use the approved proposal PDF as the basis for the quotation.</p>
                    </div>
                    <a href="{{ $proposalDownloadUrl }}" target="_blank" class="inline-flex h-10 items-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800">
                        <i class="fas fa-file-pdf mr-2"></i>Download Proposal PDF
                    </a>
                </div>

                <div class="mt-5 grid gap-3 text-sm md:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Condeal Reference No.</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $deal->deal_code ?: '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Proposal Date</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ optional($proposal->proposal_date)->format('F j, Y') ?: '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Client Name</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $clientName ?: '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Business Name</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $deal->company_name ?: '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Service Type</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $documentData['service_type'] ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Total Proposal Price</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $formatCurrency($proposal->price_total ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-5 rounded-xl border border-slate-200 bg-white px-4 py-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Quotation Timeline</div>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <div>Uploaded: <span class="font-semibold text-slate-900">{{ $proposal->quotation_finance_file_path ? 'Yes' : '-' }}</span></div>
                        <div>Approved: <span class="font-semibold text-slate-900">{{ optional($proposal->quotation_approved_at)->format('F j, Y g:i A') ?: '-' }}</span></div>
                        <div>Approved By: <span class="font-semibold text-slate-900">{{ $proposal->quotation_approved_by_name ?: '-' }}</span></div>
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-950">Approval</h2>
                    <div class="mt-4 grid gap-2">
                        @if ($isApproved)
                            <button type="button" disabled class="inline-flex h-11 w-full cursor-not-allowed items-center justify-center rounded-lg bg-emerald-100 px-4 text-sm font-semibold text-emerald-700">
                                <i class="fas fa-circle-check mr-2"></i>Approved
                            </button>
                        @else
                            <form method="POST" action="{{ route('deals.quotation.approve', $deal) }}">
                                @csrf
                                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800">
                                    <i class="fas fa-circle-check mr-2"></i>Approve Quotation
                                </button>
                            </form>
                        @endif
                    </div>
                </section>

                <form method="POST" action="{{ route('deals.quotation.upload-finance', $deal) }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    @csrf
                    <h2 class="text-base font-semibold text-slate-950">Upload Finance Quotation</h2>
                    @if ($proposal->quotation_finance_file_path)
                        <a href="{{ route('uploads.show', ['path' => $proposal->quotation_finance_file_path]) }}" target="_blank" class="mt-3 inline-flex text-sm font-semibold text-blue-700 underline">
                            View uploaded quotation
                        </a>
                    @endif
                    <input type="file" name="quotation_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-4 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700 disabled:cursor-not-allowed disabled:opacity-60" required @disabled($isApproved)>
                    <button type="submit" class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" @disabled($isApproved)>
                        <i class="fas fa-upload mr-2"></i>Upload Quotation
                    </button>
                </form>
            </aside>
        </div>
    </div>
</div>
@endsection
