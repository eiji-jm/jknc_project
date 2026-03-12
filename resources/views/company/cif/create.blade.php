@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.kyc', $company->id) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>KYC</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">Create Client Intake Form</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Client Intake Form</h1>
                <p class="mt-1 text-sm text-gray-500">Fill out the CIF details for {{ $company->company_name }}.</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        Please review the form fields and correct any errors.
                    </div>
                @endif

                <form method="POST" action="{{ route('company.cif.store', $company->id) }}" class="mt-6">
                    @csrf

                    @include('company.cif.partials.form-fields', ['cif' => null])

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                        <a href="{{ route('company.kyc', $company->id) }}" class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="draft" class="h-9 min-w-[120px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Save Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="h-9 min-w-[120px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                            Submit CIF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection
