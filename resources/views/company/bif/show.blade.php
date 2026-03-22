@extends('layouts.app')

@section('content')
<div class="mt-4 w-full px-4 pb-8 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-md border border-gray-100 bg-white">
        @include('company.partials.company-header', ['company' => $company])

        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.kyc', ['company' => $company->id, 'tab' => 'business-client-information']) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Business Client Information Form</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">BIF Preview</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4">
            @if (session('bif_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('bif_success') }}
                </div>
            @endif

            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $bif->title ?: 'Business Client Information Form' }}</h1>
                        <p class="mt-1 text-sm text-gray-500">Read-only business client information preview for {{ $company->company_name }}.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id]) }}" target="_blank" class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Print
                        </a>
                        <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id, 'autoprint' => 1]) }}" target="_blank" class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Export PDF
                        </a>
                        <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Edit
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                    <div class="bg-gray-50 p-4 sm:p-6">
                        <div class="mx-auto max-w-[1120px]">
                            @include('company.bif.partials.document', ['wrapperClass' => 'bif-doc'])
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
