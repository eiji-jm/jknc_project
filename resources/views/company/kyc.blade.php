@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', [
            'company' => $company,
            'companyKycStatus' => $companyKycStatus,
            'companyKycStatusClass' => $companyKycStatusClass,
        ])

        <div class="bg-gray-50 p-4">
            @if (session('bif_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('bif_success') }}
                </div>
            @endif

            <div id="companyKycApp">
                <div class="mb-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Business Information Form (BIF)</h2>
                                <p class="mt-1 text-xs text-gray-500">Saved business information is the source of truth for this company record.</p>
                            </div>
                            @if ($bif)
                                <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                            @endif
                        </div>

                        <div class="p-4">
                            @if ($bif)
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-[#f7f6f2] p-4">
                                    @include('company.bif.partials.document', ['wrapperClass' => 'bif-doc'])
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-gray-900">No business information submitted yet</h3>
                                    <p class="mt-2 text-sm text-gray-500">Create a Business Information Form to preview company KYC details here.</p>
                                    <a href="{{ route('company.bif.create', $company->id) }}" class="mt-5 inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                        Create BIF
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Preview / PDF</h3>
                            </div>
                            <div class="space-y-3 px-4 py-4">
                                @if ($bif)
                                    <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id]) }}" target="_blank" class="flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">Preview PDF</a>
                                    <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id, 'autoprint' => 1]) }}" target="_blank" class="flex h-10 items-center justify-center rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Download PDF</a>
                                    <p class="text-xs text-gray-500">Download opens a print-friendly BIF preview so the browser can export it as PDF.</p>
                                @else
                                    <button type="button" disabled class="flex h-10 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-sm font-medium text-gray-400">Preview PDF</button>
                                    <button type="button" disabled class="flex h-10 items-center justify-center rounded-lg bg-gray-200 text-sm font-medium text-gray-500">Download PDF</button>
                                    <p class="text-xs text-gray-500">PDF actions become available after a Business Information Form is created.</p>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Business Information Summary</h3>
                            </div>
                            <div class="space-y-4 px-4 py-4 text-sm">
                                <div>
                                    <p class="text-gray-500">BIF Number</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->bif_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClass }}">{{ $statusLabel }}</span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date Submitted</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->submitted_at ? $bif->submitted_at->format('F j, Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Authorized Contact Person</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->authorized_contact_person_name ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TIN</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->tin_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Updated</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->updated_at ? $bif->updated_at->format('F j, Y g:i A') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($bif)
                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                                </div>
                                <div class="space-y-2 px-4 py-4">
                                    <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">Edit Business Information Form</a>
                                    <a href="{{ route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id]) }}" class="flex h-10 items-center justify-center rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Open Full Preview</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">KYC Information</h2>
                                @if ($bif)
                                    <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                @endif
                            </div>
                            <div class="space-y-4 px-4 py-4 text-sm">
                                <div>
                                    <p class="text-gray-500">BIF</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->bif_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TIN</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->tin_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">KYC Status</p>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClass }}">{{ $statusLabel }}</span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date Verified</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->approved_at ? $bif->approved_at->format('F j, Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Verified By</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->approved_by_name ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                            </div>
                            <div class="space-y-2 px-4 py-4">
                                <button type="button" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white {{ $bif ? 'hover:bg-blue-700' : 'cursor-not-allowed opacity-60' }}" @disabled(! $bif)>Submit For Verification</button>
                                <button type="button" class="h-10 w-full rounded-lg bg-green-600 text-sm font-medium text-white {{ $bif ? 'hover:bg-green-700' : 'cursor-not-allowed opacity-60' }}" @disabled(! $bif)>Approve</button>
                                <button type="button" class="h-10 w-full rounded-lg bg-red-400 text-sm font-medium text-white {{ $bif ? 'hover:bg-red-500' : 'cursor-not-allowed opacity-60' }}" @disabled(! $bif)>Reject</button>
                                <div class="pt-2">
                                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">KYC Activity</p>
                                    <div class="space-y-1 text-xs text-gray-500">
                                        @foreach ($kycActivityLogs as $log)
                                            <p>{{ $log }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <h2 class="text-base font-semibold text-gray-900">KYC Requirements</h2>
                            <p class="mt-1 text-xs text-gray-500">Upload and manage only the required compliance items for the business: SEC / CDA registration, specimen signatures, and TIN.</p>
                        </div>
                        <div class="max-h-[520px] space-y-3 overflow-y-auto p-4">
                            @foreach ($kycRequirements as $requirement)
                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $requirement['label'] }}</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $requirement['uploaded'] ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                                {{ $requirement['uploaded'] ? 'Uploaded' : 'Missing' }}
                                            </span>
                                            <p class="mt-1 text-xs text-gray-500">{{ $requirement['helper'] }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <button type="button" class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50">{{ $requirement['uploaded'] ? 'Replace' : 'Upload' }}</button>
                                            <button type="button" class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 {{ $requirement['uploaded'] ? 'hover:bg-gray-50' : 'opacity-40 pointer-events-none' }}">View</button>
                                            <button type="button" class="rounded-md border border-red-200 px-2 py-1 text-red-600 {{ $requirement['uploaded'] ? 'hover:bg-red-50' : 'opacity-40 pointer-events-none' }}">Remove</button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
