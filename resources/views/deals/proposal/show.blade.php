@extends('layouts.app')
@section('title', 'Create Proposal')

@section('content')
<style>
    .proposal-workspace-card { min-height: calc(100vh - 15rem); }
    .proposal-preview-frame { min-height: 1100px; }
    @media (max-width: 1279px) {
        .proposal-preview-frame { min-height: 900px; }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
            <a href="{{ route('deals.show', $deal->id) }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold">Create Proposal</div>
                <div class="text-xs text-gray-500">{{ $deal->deal_code }}</div>
            </div>
            <div class="flex-1"></div>
            <span id="proposal-preview-badge" class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ $generatedPdfUrl ? 'Exact preview ready' : 'Generating preview' }}
            </span>
            <a
                id="proposal-docx-download"
                href="{{ $generatedDocxDownloadUrl ?: '#' }}"
                class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 {{ $generatedDocxDownloadUrl ? '' : 'pointer-events-none opacity-50' }}"
            >
                Download DOCX
            </a>
            <a
                id="proposal-pdf-download"
                href="{{ $generatedPdfDownloadUrl ?: '#' }}"
                class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 {{ $generatedPdfDownloadUrl ? '' : 'pointer-events-none opacity-50' }}"
            >
                Download PDF
            </a>
        </div>
    </div>
</div>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(430px,0.95fr)_minmax(0,1.55fr)] gap-6 p-6">
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden proposal-workspace-card">
                <div class="border-b border-gray-100 px-5 py-4">
                    <div class="text-sm font-semibold text-gray-900">Create Proposal Form</div>
                    <div class="mt-1 text-xs text-gray-500">The form is auto-filled from the deal. Edit any field and the right-side preview regenerates from the exact Word template.</div>
                </div>

                <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-5 py-5">
                    <form method="POST" action="{{ route('deals.proposal.update', $deal) }}" id="proposal-live-form" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600">Reference ID</label>
                                <input type="text" name="reference_id" value="{{ old('reference_id', $proposal->reference_id) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">CRUD ID</label>
                                <input type="text" name="crud_id" value="{{ old('crud_id', $proposal->crud_id) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Date</label>
                                <input type="date" name="proposal_date" value="{{ old('proposal_date', optional($proposal->proposal_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Location</label>
                                <input type="text" name="location" value="{{ old('location', $proposal->location) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Service Type</label>
                                <input type="text" name="service_type" value="{{ old('service_type', $proposal->service_type) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Prepared By Name</label>
                                <input type="text" name="prepared_by_name" value="{{ old('prepared_by_name', $proposal->prepared_by_name) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Prepared By ID</label>
                                <input type="text" name="prepared_by_id" value="{{ old('prepared_by_id', $proposal->prepared_by_id) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Our Proposal Text</label>
                            <textarea name="our_proposal_text" rows="6" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('our_proposal_text', $proposal->our_proposal_text) }}</textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Scope of Service / Assistance</label>
                            <textarea name="scope_of_service" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('scope_of_service', $proposal->scope_of_service) }}</textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">What You Will Receive</label>
                            <textarea name="what_you_will_receive" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('what_you_will_receive', $proposal->what_you_will_receive) }}</textarea>
                        </div>
                        @if ($requirementGroup === 'sole')
                            <div>
                                <label class="text-xs text-gray-600">Requirements - Sole / Individual</label>
                                <textarea name="requirements_sole" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('requirements_sole', $proposal->requirements_sole) }}</textarea>
                            </div>
                        @else
                            <div>
                                <label class="text-xs text-gray-600">Requirements - Juridical</label>
                                <textarea name="requirements_juridical" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('requirements_juridical', $proposal->requirements_juridical) }}</textarea>
                            </div>
                        @endif
                        <div>
                            <label class="text-xs text-gray-600">Optional Requirements</label>
                            <textarea name="requirements_optional" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('requirements_optional', $proposal->requirements_optional) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600">Regular Price</label>
                                <input type="number" step="0.01" min="0" name="price_regular" value="{{ old('price_regular', $proposal->price_regular) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Discount</label>
                                <input type="number" step="0.01" min="0" name="price_discount" value="{{ old('price_discount', $proposal->price_discount) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Subtotal</label>
                                <input type="number" step="0.01" min="0" name="price_subtotal" value="{{ old('price_subtotal', $proposal->price_subtotal) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Tax</label>
                                <input type="number" step="0.01" min="0" name="price_tax" value="{{ old('price_tax', $proposal->price_tax) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Total</label>
                                <input type="number" step="0.01" min="0" name="price_total" value="{{ old('price_total', $proposal->price_total) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Downpayment</label>
                                <input type="number" step="0.01" min="0" name="price_down" value="{{ old('price_down', $proposal->price_down) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-600">Balance</label>
                                <input type="number" step="0.01" min="0" name="price_balance" value="{{ old('price_balance', $proposal->price_balance) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Proposal</button>
                            <button type="button" id="proposal-refresh-button" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Refresh Preview</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-[#f8fafc] overflow-hidden flex flex-col proposal-workspace-card">
                <div class="border-b border-gray-100 bg-white px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Exact Proposal Preview</div>
                            <div id="proposal-preview-status" class="mt-1 text-xs text-gray-500">
                                {{ $generatedPdfUrl ? 'This preview is rendered from the generated Word template output, so it should match the full proposal document instead of a rebuilt HTML copy.' : 'Generating the exact Word-template preview.' }}
                            </div>
                        </div>
                    </div>
                    <div id="proposal-preview-error" class="mt-3 {{ $previewError ? '' : 'hidden' }} rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        {{ $previewError ?: '' }}
                    </div>
                </div>

                <div class="flex-1 p-5">
                    <div id="proposal-preview-panel" class="h-full rounded-2xl border border-gray-200 bg-white overflow-hidden">
                        <iframe
                            id="proposal-preview-frame"
                            src="{{ $generatedPdfUrl ?: 'about:blank' }}"
                            class="proposal-preview-frame h-full w-full {{ $generatedPdfUrl ? '' : 'hidden' }}"
                            title="Exact proposal preview"
                        ></iframe>

                        <div id="proposal-preview-placeholder" class="flex h-full min-h-[420px] items-center justify-center px-8 text-center text-sm text-gray-500 {{ $generatedPdfUrl ? 'hidden' : '' }}">
                            The exact template preview will appear here once the proposal file is generated successfully.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="dealProposalPreviewData">@json([
    'previewUrl' => route('deals.proposal.preview', $deal),
])</script>

<script>
(() => {
    const previewDataElement = document.getElementById('dealProposalPreviewData');
    const previewData = previewDataElement ? JSON.parse(previewDataElement.textContent || '{}') : {};
    const previewUrl = previewData.previewUrl || '';
    const form = document.getElementById('proposal-live-form');
    const refreshButton = document.getElementById('proposal-refresh-button');
    const frame = document.getElementById('proposal-preview-frame');
    const placeholder = document.getElementById('proposal-preview-placeholder');
    const status = document.getElementById('proposal-preview-status');
    const errorBox = document.getElementById('proposal-preview-error');
    const badge = document.getElementById('proposal-preview-badge');
    const docxDownload = document.getElementById('proposal-docx-download');
    const pdfDownload = document.getElementById('proposal-pdf-download');

    if (!form || !refreshButton || !frame || !placeholder || !status || !errorBox || !badge || !docxDownload || !pdfDownload) {
        return;
    }

    let previewTimer = null;
    let activeController = null;

    const setButtonState = (link, href) => {
        if (href) {
            link.href = href;
            link.classList.remove('pointer-events-none', 'opacity-50');
            return;
        }

        link.href = '#';
        link.classList.add('pointer-events-none', 'opacity-50');
    };

    const setBusy = (message) => {
        badge.textContent = 'Generating preview';
        badge.classList.remove('bg-emerald-50', 'text-emerald-700', 'bg-amber-50', 'text-amber-700');
        badge.classList.add('bg-blue-50', 'text-blue-700');
        status.textContent = message;
        errorBox.classList.add('hidden');
    };

    const setReady = (payload) => {
        badge.textContent = 'Exact preview ready';
        badge.classList.remove('bg-blue-50', 'text-blue-700', 'bg-amber-50', 'text-amber-700');
        badge.classList.add('bg-emerald-50', 'text-emerald-700');
        status.textContent = 'Preview updated from the exact Word template output.';
        errorBox.classList.add('hidden');

        setButtonState(docxDownload, payload.docx_url || null);
        setButtonState(pdfDownload, payload.pdf_download_url || null);

        if (payload.pdf_url) {
            frame.src = `${payload.pdf_url}${payload.pdf_url.includes('?') ? '&' : '?'}t=${Date.now()}`;
            frame.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
    };

    const setError = (message) => {
        badge.textContent = 'Preview failed';
        badge.classList.remove('bg-blue-50', 'text-blue-700', 'bg-emerald-50', 'text-emerald-700');
        badge.classList.add('bg-amber-50', 'text-amber-700');
        status.textContent = 'The exact Word-template preview could not be generated yet.';
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const refreshPreview = async () => {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        setBusy('Regenerating the exact proposal preview from the Word template.');

        try {
            const formData = new FormData(form);
            formData.delete('_method');

            const response = await fetch(previewUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                signal: activeController.signal,
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Preview generation failed.');
            }

            setReady(payload);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            setError(error.message || 'Preview generation failed.');
        }
    };

    const queuePreview = () => {
        window.clearTimeout(previewTimer);
        previewTimer = window.setTimeout(refreshPreview, 700);
    };

    form.addEventListener('input', queuePreview);
    form.addEventListener('change', queuePreview);
    refreshButton.addEventListener('click', refreshPreview);
})();
</script>
@endsection
