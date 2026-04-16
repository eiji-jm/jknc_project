@extends('layouts.app')
@section('title', 'Ultimate Beneficial Owner Declaration')

@section('content')
<div class="mt-4 w-full px-4 pb-8 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Ultimate Beneficial Owner Declaration</h1>
                <p class="text-sm text-gray-500">Review, edit, and export the UBO declaration as PDF using the saved BIF details as a starting point.</p>
            </div>
            <a href="{{ $backUrl }}" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to KYC</a>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[minmax(0,1.45fr)_410px]">
            <div class="rounded-2xl border border-slate-200 bg-[#f8fafc] p-6">
                <div class="mx-auto max-w-[860px] rounded-sm bg-white px-14 py-12 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    @include('company.requirements.partials.ubo-declaration-document', ['doc' => $doc])
                </div>
            </div>

            <form method="GET" action="{{ route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'juridical_ubo_declaration']) }}" id="template-form" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Editable Fields</div>
                    <div class="mt-1 text-xs text-gray-500">Adjust the company, declarant, and beneficial owner details here. The preview updates as you type.</div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @foreach ([
                        ['company_name', 'Company Name'],
                        ['company_address', 'Company Address'],
                        ['company_tin', 'Company TIN'],
                        ['declarant_name', 'Declarant Name'],
                        ['declarant_position', 'Declarant Position'],
                        ['declarant_nationality', 'Declarant Nationality'],
                        ['declaration_day', 'Declaration Day'],
                        ['declaration_month', 'Declaration Month'],
                        ['declaration_year', 'Declaration Year'],
                        ['notary_city', 'Notary City'],
                        ['notary_day', 'Notary Day'],
                        ['notary_month', 'Notary Month'],
                        ['notary_year', 'Notary Year'],
                        ['notary_public', 'Notary Public'],
                        ['doc_no', 'Doc No.'],
                        ['page_no', 'Page No.'],
                        ['book_no', 'Book No.'],
                        ['series_year', 'Series Year'],
                    ] as [$name, $label])
                        <div>
                            <label class="text-xs font-medium text-gray-600">{{ $label }}</label>
                            <input type="text" name="{{ $name }}" value="{{ $doc[$name] }}" data-sync="{{ $name }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    @endforeach

                    @foreach ($doc['ubos'] as $index => $ubo)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">UBO {{ $index + 1 }}</div>
                            <div class="mt-2 space-y-2">
                                @foreach ([
                                    ['full_name', 'Full Name'],
                                    ['address', 'Address'],
                                    ['nationality', 'Nationality'],
                                    ['date_of_birth', 'Date of Birth'],
                                    ['tin', 'TIN'],
                                    ['position', 'Position / Ownership Role'],
                                ] as [$field, $label])
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">{{ $label }}</label>
                                        <input type="text" name="ubos[{{ $index }}][{{ $field }}]" value="{{ $ubo[$field] }}" data-sync="ubos_{{ $index }}_{{ $field }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="button" id="ubo-download-button" data-download-url="{{ $downloadUrl }}" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Download PDF</button>
                    <a href="{{ route('company.kyc.requirements.template', ['company' => $company->id, 'requirement' => 'juridical_ubo_declaration']) }}" class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset Autofill</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('#template-form [data-sync]').forEach(function (input) {
    input.addEventListener('input', function () {
        const value = input.value;
        document.querySelectorAll('[data-field="' + input.dataset.sync + '"]').forEach(function (target) {
            target.textContent = value;
        });
    });
});

const uboDownloadButton = document.getElementById('ubo-download-button');
const templateForm = document.getElementById('template-form');

if (uboDownloadButton && templateForm) {
    uboDownloadButton.addEventListener('click', function () {
        const params = new URLSearchParams(new FormData(templateForm));
        params.set('autoprint', '1');
        window.location.href = uboDownloadButton.dataset.downloadUrl + '?' + params.toString();
    });
}
</script>
@endsection
