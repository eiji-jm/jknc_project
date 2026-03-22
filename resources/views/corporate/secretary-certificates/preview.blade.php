@extends('layouts.app')

@section('content')
@php
    $documentUrl = $certificate->document_path ? route('uploads.show', ['path' => $certificate->document_path]) : null;
    $documentDownloadUrl = $certificate->document_path ? route('uploads.show', ['path' => $certificate->document_path, 'download' => 1]) : null;
    $resolution = $certificate->resolution;
    $companyName = 'JK&C INC.';
    $meetingDate = optional($certificate->date_of_meeting)->format('F d, Y') ?: '________________';
    $issuedDate = optional($certificate->date_issued)->format('F d, Y') ?: '________________';
    $companyAddress = '3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE. CEBU BUSINESS PARK HIPPODROMO, CEBU CITY (Capital), CEBU, REGION VII (CENTRAL VISAYAS), 6000';
    $defaultSecretary = 'MA. LOURDES T. MATA';
    $defaultTin = '903-658-744-000';
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #secretary-certificate-print,
        #secretary-certificate-print * {
            visibility: visible;
        }

        #secretary-certificate-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            filter: none !important;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ activeVersion: '{{ $documentUrl ? 'original' : 'draft' }}' }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Secretary Certificate Preview</div>
                <div class="text-xs text-gray-500">Certificate No. <span data-preview="certificate-no">{{ $certificate->certificate_no ?: 'Draft' }}</span></div>
            </div>
            <div class="flex-1"></div>
            <div class="inline-flex rounded-full bg-gray-100 p-1">
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'draft' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'draft'">Draft</button>
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'original' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'original'">Original</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div x-show="activeVersion === 'draft'" class="bg-[#ece6da] rounded-2xl p-6">
                    <div id="secretary-certificate-print" class="mx-auto max-w-4xl bg-white shadow-2xl rounded-sm p-12 min-h-[860px] text-[13px] leading-6 text-gray-900" style="font-family: Georgia, 'Times New Roman', serif;">
                        <div class="text-sm">
                            <div>Republic of the Philippines) City Of</div>
                            <div>________________) S.S.</div>
                        </div>

                        <div class="mt-8 text-center text-lg font-semibold">SECRETARY'S CERTIFICATE</div>

                        <div class="mt-10 space-y-5 text-justify">
                            <p>
                                I, <span class="font-semibold underline" data-preview="certificate-secretary">{{ $certificate->secretary ?: $defaultSecretary }}</span>, of legal age,
                                single, Filipino and with residence at <span class="font-semibold underline">PEARL ST. STA. TERESITA VILL. TISA, CEBU CITY</span>,
                                depose under oath and hereby state:
                            </p>

                            <p>
                                That, I am the incumbent Corporate Secretary of <span class="font-semibold underline">{{ $companyName }}</span>,
                                a corporation duly organized and existing under the laws of the Republic of the Philippines, with principal office at
                                <span class="font-semibold underline">{{ $companyAddress }}</span>.
                            </p>

                            <p>
                                That, as Corporate Secretary, I have access to the corporate records of
                                <span class="font-semibold underline">{{ $companyName }}</span>.
                            </p>

                            <p>
                                That, per corporate records, at the <span data-preview="certificate-meeting-type">{{ $certificate->type_of_meeting ?: 'Special' }}</span> Meeting of the
                                <span data-preview="certificate-governing-body">{{ $certificate->governing_body ?: 'Board of Directors' }}</span> of the Corporation held on
                                <span class="font-semibold underline" data-preview="certificate-meeting-date">{{ $meetingDate }}</span>, the following resolution was passed, duly approved,
                                and recorded in the Minute Book, a legal quorum being present and voting, viz:
                            </p>

                            <p>
                                That during the [Regular/Special] Meeting of the Board of Directors of said corporation the following resolution was duly approved and adopted to wit:
                            </p>

                            <div class="text-center font-semibold uppercase mt-8">
                                BOARD RESOLUTION NO. <span data-preview="certificate-resolution-no">{{ $certificate->resolution_no ?: '25-004' }}</span>
                            </div>

                            <div class="mt-4 font-semibold" data-preview="certificate-purpose">{{ $certificate->purpose }}</div>
                            <div class="mt-4 whitespace-pre-line">{{ $resolution?->resolution_body ?: 'The detailed clauses are not yet available.' }}</div>

                            <p>
                                That, the foregoing resolution shall be in full force and effect unless revoked by the Board of Directors.
                                Moreover, the foregoing resolution is in accordance and does not in any way contravene any provision
                                of the Articles of Incorporation or By-Laws of the Corporation.
                            </p>

                            <p>
                                WITNESS MY HAND this ________ of ___________, <span data-preview="certificate-issued-year">{{ optional($certificate->date_issued)->format('Y') ?: now()->year }}</span>
                                at <span data-preview="certificate-notarized-at">{{ $resolution?->notarized_at ?: 'Cebu City' }}</span>, Cebu, Philippines.
                            </p>
                        </div>

                        <div class="mt-14 text-right">
                            <div class="inline-block min-w-[260px] border-t border-black pt-2 text-center">
                                <div class="font-semibold uppercase" data-preview="certificate-secretary">{{ $certificate->secretary ?: $defaultSecretary }}</div>
                                <div>Corporate Secretary</div>
                                <div class="text-xs">TIN {{ $defaultTin }}</div>
                            </div>
                        </div>

                        <div class="mt-16 space-y-4 text-sm">
                            <p>
                                SUBSCRIBED AND SWORN to before me on the date and place above-mentioned, affiant exhibiting
                                his/her TIN issued on <span class="underline" data-preview="certificate-issued-date">{{ $issuedDate }}</span>, at Cebu City, Philippines.
                            </p>
                            <div class="text-right mt-10">
                                <div class="inline-block min-w-[220px] border-t border-black pt-2 text-center">
                                    <span data-preview="certificate-notary-public">{{ $certificate->notary_public ?: 'Notary Public' }}</span>
                                </div>
                            </div>
                            <div class="mt-8 space-y-1">
                                <div>Doc. No. <span data-preview="certificate-doc-no">{{ $certificate->notary_doc_no ?: '_____' }}</span>;</div>
                                <div>Page No. <span data-preview="certificate-page-no">{{ $certificate->notary_page_no ?: '_____' }}</span>;</div>
                                <div>Book No. <span data-preview="certificate-book-no">{{ $certificate->notary_book_no ?: '_____' }}</span>;</div>
                                <div>Series of <span data-preview="certificate-series-no" data-fallback-year="{{ now()->year }}">{{ $certificate->notary_series_no ?: now()->year }}</span>.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeVersion === 'original'" class="document-frame">
                    <div class="document-frame__toolbar">
                        <div class="document-frame__tools">
                            <span class="document-frame__chip"><i class="fas fa-file-signature"></i> Original Certificate</span>
                            <span class="document-frame__chip">{{ $certificate->certificate_no ?: 'Draft' }}</span>
                        </div>
                        <div class="document-frame__actions">
                            <i class="fas fa-search"></i>
                            <i class="far fa-copy"></i>
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="flex-1"></div>
                        @if ($documentUrl)
                            <a href="{{ $documentUrl }}" target="_blank" class="text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-3 py-1.5">Open PDF</a>
                        @endif
                    </div>
                    <div class="document-frame__body">
                        @if ($documentUrl)
                            <iframe src="{{ $documentUrl }}" class="document-frame__embed"></iframe>
                        @else
                            <div class="document-frame__empty">
                                <div>
                                    <div class="text-lg font-semibold">Original certificate not uploaded yet</div>
                                    <div class="text-sm text-gray-400 mt-2">Use the sidebar form to upload the signed original scan once it is available.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Resolution No.</span><div class="font-medium text-gray-900" data-preview="certificate-resolution-no">{{ $certificate->resolution_no }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice Ref</span><div class="font-medium text-gray-900">{{ $certificate->notice_ref }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Issued</span><div class="font-medium text-gray-900" data-preview="certificate-issued-date-short">{{ optional($certificate->date_issued)->format('M d, Y') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Purpose</span><div class="font-medium text-gray-900" data-preview="certificate-purpose">{{ $certificate->purpose }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Shared Resolution Data</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900" data-preview="certificate-governing-body">{{ $certificate->governing_body }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Type</span><div class="font-medium text-gray-900" data-preview="certificate-meeting-type">{{ $certificate->type_of_meeting }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Date</span><div class="font-medium text-gray-900" data-preview="certificate-meeting-date-short">{{ optional($certificate->date_of_meeting)->format('M d, Y') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Location</span><div class="font-medium text-gray-900">{{ $certificate->location }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900" data-preview="certificate-secretary">{{ $certificate->secretary }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Notary Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notary Public</span><div class="font-medium text-gray-900" data-preview="certificate-notary-public">{{ $certificate->notary_public }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Doc / Page / Book / Series</span><div class="font-medium text-gray-900"><span data-preview="certificate-doc-no">{{ $certificate->notary_doc_no }}</span> / <span data-preview="certificate-page-no">{{ $certificate->notary_page_no }}</span> / <span data-preview="certificate-book-no">{{ $certificate->notary_book_no }}</span> / <span data-preview="certificate-series-no" data-fallback-year="{{ now()->year }}">{{ $certificate->notary_series_no }}</span></div></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('secretary-certificates.update', $certificate) }}" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-xl p-4 space-y-4" id="certificate-live-form">
                    @csrf
                    @method('PUT')
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Template and Original Update</div>
                        <div class="text-xs text-gray-500 mt-1">As you edit these fields, the draft preview updates in real time. Upload the signed original scan here when it becomes available.</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600">Certificate No.</label>
                            <input type="text" name="certificate_no" value="{{ $certificate->certificate_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-no" data-live-empty="Draft">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Resolution No.</label>
                            <input type="text" name="resolution_no" value="{{ $certificate->resolution_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-resolution-no" data-live-empty="25-004">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Date Issued</label>
                            <input type="date" name="date_issued" value="{{ optional($certificate->date_issued)->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-issued-date" data-live-format="date-group">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Meeting Date</label>
                            <input type="date" name="date_of_meeting" value="{{ optional($certificate->date_of_meeting)->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-meeting-date" data-live-format="meeting-date-group">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Governing Body</label>
                            <select name="governing_body" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-governing-body">
                                @foreach (['Stockholders', 'Board of Directors', 'Joint Stockholders and Board of Directors'] as $bodyOption)
                                    <option value="{{ $bodyOption }}" @selected($certificate->governing_body === $bodyOption)>{{ $bodyOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Meeting Type</label>
                            <select name="type_of_meeting" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-meeting-type">
                                @foreach (['Regular', 'Special'] as $meetingTypeOption)
                                    <option value="{{ $meetingTypeOption }}" @selected($certificate->type_of_meeting === $meetingTypeOption)>{{ $meetingTypeOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-600">Purpose</label>
                            <input type="text" name="purpose" value="{{ $certificate->purpose }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-purpose">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Secretary</label>
                            <input type="text" name="secretary" value="{{ $certificate->secretary }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-secretary" data-live-empty="{{ $defaultSecretary }}">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Location</label>
                            <input type="text" name="location" value="{{ $certificate->location }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-notarized-at" data-live-empty="Cebu City">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Notary Public</label>
                            <input type="text" name="notary_public" value="{{ $certificate->notary_public }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-notary-public" data-live-empty="Notary Public">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Doc No.</label>
                            <input type="text" name="notary_doc_no" value="{{ $certificate->notary_doc_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-doc-no" data-live-empty="_____">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Page No.</label>
                            <input type="text" name="notary_page_no" value="{{ $certificate->notary_page_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-page-no" data-live-empty="_____">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Book No.</label>
                            <input type="text" name="notary_book_no" value="{{ $certificate->notary_book_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-book-no" data-live-empty="_____">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Series No.</label>
                            <input type="text" name="notary_series_no" value="{{ $certificate->notary_series_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="certificate-series-no">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Original Certificate PDF</label>
                        <input type="file" name="document_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        @if ($certificate->document_path)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-red-700">
                                <input type="checkbox" name="remove_document_path" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove current original certificate PDF
                            </label>
                        @endif
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Update Original Certificate
                    </button>
                </form>

                <div class="space-y-2">
                    <button type="button" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" @click="handleSecretaryDownload(activeVersion, @js($documentDownloadUrl))">
                        Download PDF
                    </button>
                    <button type="button" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="handleSecretaryPrint(activeVersion, @js($documentUrl))">
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleSecretaryDownload(activeVersion, documentUrl) {
        if (activeVersion === 'original' && documentUrl) {
            const link = document.createElement('a');
            link.href = documentUrl;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            link.remove();
            return;
        }

        window.print();
    }

    function handleSecretaryPrint(activeVersion, documentUrl) {
        if (activeVersion === 'original' && documentUrl) {
            const win = window.open(documentUrl, '_blank');
            if (win) {
                window.setTimeout(() => win.print(), 800);
            }
            return;
        }

        window.print();
    }

    (() => {
        const form = document.getElementById('certificate-live-form');
        if (!form) {
            return;
        }

        const formatDate = (value, style) => {
            if (!value) {
                return '';
            }

            const parsed = new Date(`${value}T00:00:00`);
            if (Number.isNaN(parsed.getTime())) {
                return value;
            }

            if (style === 'year') {
                return String(parsed.getFullYear());
            }

            if (style === 'short') {
                return parsed.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            }

            return parsed.toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
        };

        const applyValue = (input) => {
            const targetName = input.dataset.liveTarget;
            if (!targetName) {
                return;
            }

            const targets = document.querySelectorAll(`[data-preview="${targetName}"]`);
            if (!targets.length) {
                return;
            }

            let value = input.value.trim();
            if (input.dataset.liveFormat === 'date-group') {
                const longValue = value ? formatDate(value, 'long') : '';
                const shortValue = value ? formatDate(value, 'short') : '';
                const yearValue = value ? formatDate(value, 'year') : '';

                document.querySelectorAll('[data-preview="certificate-issued-date"]').forEach((target) => {
                    target.textContent = longValue || '________________';
                });
                document.querySelectorAll('[data-preview="certificate-issued-date-short"]').forEach((target) => {
                    target.textContent = shortValue || '';
                });
                document.querySelectorAll('[data-preview="certificate-issued-year"]').forEach((target) => {
                    target.textContent = yearValue || String(new Date().getFullYear());
                });
                return;
            }

            if (input.dataset.liveFormat === 'meeting-date-group') {
                const longValue = value ? formatDate(value, 'long') : '';
                const shortValue = value ? formatDate(value, 'short') : '';

                document.querySelectorAll('[data-preview="certificate-meeting-date"]').forEach((target) => {
                    target.textContent = longValue || '________________';
                });
                document.querySelectorAll('[data-preview="certificate-meeting-date-short"]').forEach((target) => {
                    target.textContent = shortValue || '';
                });
                return;
            }

            if (targetName === 'certificate-series-no' && !value) {
                value = String(new Date().getFullYear());
            }

            const fallback = input.dataset.liveEmpty || targets[0].dataset.fallbackYear || '';
            const finalValue = value || fallback;
            targets.forEach((target) => {
                target.textContent = finalValue;
            });
        };

        form.querySelectorAll('[data-live-target]').forEach((input) => {
            input.addEventListener('input', () => applyValue(input));
            input.addEventListener('change', () => applyValue(input));
        });
    })();
</script>
@endsection
