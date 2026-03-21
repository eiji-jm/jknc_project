@extends('layouts.app')

@section('content')
@php
    $draftUrl = $resolution->draft_file_path ? route('uploads.show', ['path' => $resolution->draft_file_path]) : null;
    $draftDownloadUrl = $resolution->draft_file_path ? route('uploads.show', ['path' => $resolution->draft_file_path, 'download' => 1]) : null;
    $notarizedUrl = $resolution->notarized_file_path ? route('uploads.show', ['path' => $resolution->notarized_file_path]) : null;
    $notarizedDownloadUrl = $resolution->notarized_file_path ? route('uploads.show', ['path' => $resolution->notarized_file_path, 'download' => 1]) : null;
    $clauseText = trim((string) $resolution->resolution_body);
    $companyName = config('app.name', 'JK&C INC.');
    $meetingDate = optional($resolution->date_of_meeting)->format('F d, Y') ?: '________________';
    $notaryYear = optional($resolution->notarized_on)->format('Y') ?: now()->year;
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #resolution-print,
        #resolution-print * {
            visibility: visible;
        }

        #resolution-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            filter: none !important;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ activeVersion: '{{ $notarizedUrl ? 'original' : 'draft' }}' }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Resolution Preview</div>
                <div class="text-xs text-gray-500">Resolution No. {{ $resolution->resolution_no ?: 'Draft' }}</div>
            </div>
            <div class="flex-1"></div>
            <div class="inline-flex rounded-full bg-gray-100 p-1">
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'draft' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'draft'">Draft</button>
                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-full" :class="activeVersion === 'original' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" @click="activeVersion = 'original'">Original / Notarized</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div x-show="activeVersion === 'draft'" class="bg-[#ece6da] rounded-2xl p-6">
                    @if ($draftUrl)
                        <div class="bg-gray-900 rounded-xl overflow-hidden">
                            <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex items-center gap-2">
                                <span class="text-gray-300 text-sm font-medium">Uploaded Draft PDF</span>
                                <div class="flex-1"></div>
                                <a href="{{ $draftUrl }}" target="_blank" class="text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-3 py-1.5">Open PDF</a>
                            </div>
                            <iframe src="{{ $draftUrl }}" class="w-full h-[880px] bg-white"></iframe>
                        </div>
                    @else
                        <div id="resolution-print" class="mx-auto max-w-4xl bg-white shadow-2xl rounded-sm p-12 min-h-[880px] text-[13px] leading-6 text-gray-900" style="font-family: Georgia, 'Times New Roman', serif;">
                            <div class="text-center text-black">
                                <div class="leading-none tracking-tight">
                                    <div class="text-[4.25rem] font-normal">John Kelly</div>
                                    <div class="flex items-end justify-center gap-3">
                                        <span class="text-[3.25rem] leading-none font-semibold text-blue-600">&amp;</span>
                                        <span class="text-[4.25rem] leading-none font-normal">Company</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-[2rem] font-medium tracking-tight">JK&amp;C INC.</div>
                                <div class="mt-3 text-sm">COMPANY REG. NO.: 2025120230900-02</div>
                                <div class="mt-1 text-sm">3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE CEBU</div>
                                <div class="text-sm">BUSINESS PARK HIPPODROMO, CEBU CITY (Capital), CEBU, REGION VII</div>
                                <div class="text-sm">(CENTRAL VISAYAS), 6000;</div>
                            </div>

                            <div class="mt-12 text-center border-b border-black pb-2">
                                <div class="text-lg font-semibold uppercase underline">Board Resolution No. <span data-preview="resolution-no">{{ $resolution->resolution_no ?: '25-002' }}</span></div>
                            </div>

                            <div class="mt-8 space-y-5 text-justify">
                                <p>
                                    <span class="font-semibold">WHEREAS,</span> during the <span data-preview="meeting-type-lower">{{ strtolower($resolution->type_of_meeting ?: 'special') }}</span>
                                    meeting of the <span data-preview="governing-body">{{ $resolution->governing_body ?: 'Board of Directors' }}</span> of
                                    <span class="font-semibold underline">{{ $companyName }}</span> held on
                                    <span class="font-semibold underline" data-preview="meeting-date">{{ $meetingDate }}</span>, where a quorum was present and acted all throughout,
                                    the body approved the following action:
                                </p>

                                <p>
                                    <span data-preview="resolution-body">{!! nl2br(e($clauseText ?: ($resolution->board_resolution ?: 'No board resolution text has been encoded yet.'))) !!}</span>
                                </p>

                                <p>
                                    <span class="font-semibold">WHEREAS RESOLVED;</span> that the foregoing resolutions are hereby approved and adopted.
                                </p>

                                <p>
                                    <span class="font-semibold">WHEREAS FINALLY RESOLVED,</span> that the foregoing resolution is valid and existing
                                    until withdrawn, revoked, or modified by the Corporation.
                                </p>

                                <p>
                                    <span class="font-semibold">BE IT FURTHER RESOLVED,</span> that the Corporate Secretary is hereby authorized and directed
                                    to include this Resolution in the Company's Minute Book and to notify all concerned parties of the adoption of this Resolution.
                                </p>

                                <p>
                                    <span class="font-semibold underline">FINALLY BE IT FURTHER RESOLVED</span> that the undersigned affirm the foregoing resolution
                                    and adopt it on this <span class="font-semibold underline" data-preview="meeting-date">{{ $meetingDate }}</span>.
                                </p>
                            </div>

                            <div class="mt-16 text-right">
                                <div class="inline-block min-w-[240px] border-t border-black pt-2 text-center">
                                    <div class="font-semibold uppercase" data-preview="chairman">{{ $resolution->chairman ?: 'JOSE BAYBAYANON OGANG' }}</div>
                                    <div>Chairman</div>
                                </div>
                            </div>

                            <div class="mt-16 space-y-5 text-justify">
                                <p>
                                    IN WITNESS WHEREOF, I, <span class="font-semibold underline" data-preview="chairman">{{ $resolution->chairman ?: '____________________' }}</span>,
                                    in my capacity as chairman of the board, have signed these presents this ______ day of __________ at
                                    <span data-preview="notarized-at">{{ $resolution->notarized_at ?: '______________' }}</span>.
                                </p>
                            </div>

                            <div class="mt-12 text-right">
                                <div class="inline-block min-w-[260px] border-t border-black pt-2 text-center">
                                    <div class="font-semibold uppercase" data-preview="chairman">{{ $resolution->chairman ?: 'JOSE BAYBAYANON OGANG' }}</div>
                                    <div>Chairman</div>
                                </div>
                            </div>

                            <div class="mt-16 space-y-4 text-sm">
                                <p>
                                    <span class="font-semibold uppercase">Subscribed and sworn to before me,</span> a Notary Public for and in
                                    <span data-preview="notarized-at">{{ $resolution->notarized_at ?: '______________' }}</span> this day of __________, affiant presented to me __________ issued at __________.
                                </p>
                                <div class="text-right mt-10">
                                    <div class="inline-block min-w-[220px] border-t border-black pt-2 text-center font-semibold uppercase">
                                        <span data-preview="notary-public">{{ $resolution->notary_public ?: 'Notary Public' }}</span>
                                    </div>
                                </div>
                                <div class="mt-8 space-y-1">
                                    <div>Doc. No. <span data-preview="notary-doc-no">{{ $resolution->notary_doc_no ?: '_____' }}</span>;</div>
                                    <div>Page No. <span data-preview="notary-page-no">{{ $resolution->notary_page_no ?: '_____' }}</span>;</div>
                                    <div>Book No. <span data-preview="notary-book-no">{{ $resolution->notary_book_no ?: '_____' }}</span>;</div>
                                    <div>Series of <span data-preview="notary-series-no" data-fallback-year="{{ $notaryYear }}">{{ $resolution->notary_series_no ?: $notaryYear }}</span>.</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="activeVersion === 'original'" class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex items-center gap-2">
                        <span class="text-gray-300 text-sm font-medium">Original / Notarized Resolution</span>
                        <div class="flex-1"></div>
                        @if ($notarizedUrl)
                            <a href="{{ $notarizedUrl }}" target="_blank" class="text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-3 py-1.5">Open PDF</a>
                        @endif
                    </div>

                    @if ($notarizedUrl)
                        <iframe src="{{ $notarizedUrl }}" class="w-full h-[880px] bg-white"></iframe>
                    @else
                        <div class="p-10 text-center text-gray-300">
                            <div class="text-lg font-semibold">Original / notarized copy not uploaded yet</div>
                            <div class="text-sm text-gray-400 mt-2">Use the sidebar form to upload the signed and notarized scan once it is available.</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Resolution Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Linked Notice</span><div class="font-medium text-gray-900">{{ $resolution->notice_ref }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900" data-preview="governing-body">{{ $resolution->governing_body }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Type</span><div class="font-medium text-gray-900" data-preview="meeting-type">{{ $resolution->type_of_meeting }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Date</span><div class="font-medium text-gray-900" data-preview="meeting-date-short">{{ optional($resolution->date_of_meeting)->format('M d, Y') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Board Resolution</span><div class="font-medium text-gray-900" data-preview="board-resolution">{{ $resolution->board_resolution }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary Certificates</span><div class="font-medium text-gray-900">{{ $resolution->secretaryCertificates->count() }} linked</div></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('resolutions.update', $resolution) }}" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-xl p-4 space-y-4" id="resolution-live-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="minute_id" value="{{ $resolution->minute_id }}">
                    <input type="hidden" name="notice_id" value="{{ $resolution->notice_id }}">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Template and File Editor</div>
                        <div class="text-xs text-gray-500 mt-1">Edit the resolution details here and the draft preview updates in real time while you type. Upload draft or original files below when they are available.</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600">Resolution No.</label>
                            <input type="text" name="resolution_no" value="{{ $resolution->resolution_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="resolution-no" data-live-empty="25-002">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Meeting Date</label>
                            <input type="date" name="date_of_meeting" value="{{ optional($resolution->date_of_meeting)->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="meeting-date" data-live-format="resolution-date-group">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Governing Body</label>
                            <select name="governing_body" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="governing-body">
                                @foreach (['Stockholders', 'Board of Directors', 'Joint Stockholders and Board of Directors'] as $bodyOption)
                                    <option value="{{ $bodyOption }}" @selected($resolution->governing_body === $bodyOption)>{{ $bodyOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Meeting Type</label>
                            <select name="type_of_meeting" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="meeting-type" data-live-format="meeting-type-group">
                                @foreach (['Regular', 'Special'] as $meetingTypeOption)
                                    <option value="{{ $meetingTypeOption }}" @selected($resolution->type_of_meeting === $meetingTypeOption)>{{ $meetingTypeOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-600">Board Resolution</label>
                            <input type="text" name="board_resolution" value="{{ $resolution->board_resolution }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="board-resolution">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-600">Resolution Body</label>
                            <textarea name="resolution_body" rows="6" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="resolution-body" data-live-format="multiline">{{ $resolution->resolution_body }}</textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Chairman</label>
                            <input type="text" name="chairman" value="{{ $resolution->chairman }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="chairman" data-live-empty="JOSE BAYBAYANON OGANG">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Secretary</label>
                            <input type="text" name="secretary" value="{{ $resolution->secretary }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-600">Directors</label>
                            <input type="text" name="directors" value="{{ $resolution->directors }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Original / Notarized PDF</label>
                        <input type="file" name="notarized_file_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        @if ($resolution->notarized_file_path)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-red-700">
                                <input type="checkbox" name="remove_notarized_file_path" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove current original / notarized PDF
                            </label>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Draft PDF</label>
                        <input type="file" name="draft_file_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-700 file:text-white hover:file:bg-slate-800">
                        @if ($resolution->draft_file_path)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-red-700">
                                <input type="checkbox" name="remove_draft_file_path" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove current draft PDF
                            </label>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600">Notary Public</label>
                            <input type="text" name="notary_public" value="{{ $resolution->notary_public }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-public" data-live-empty="Notary Public">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Notarized At</label>
                            <input type="text" name="notarized_at" value="{{ $resolution->notarized_at }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notarized-at" data-live-empty="______________">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Notarized On</label>
                            <input type="date" name="notarized_on" value="{{ optional($resolution->notarized_on)->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-series-no" data-live-format="year-fallback">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Doc No.</label>
                            <input type="text" name="notary_doc_no" value="{{ $resolution->notary_doc_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-doc-no" data-live-empty="_____">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Page No.</label>
                            <input type="text" name="notary_page_no" value="{{ $resolution->notary_page_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-page-no" data-live-empty="_____">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Book No.</label>
                            <input type="text" name="notary_book_no" value="{{ $resolution->notary_book_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-book-no" data-live-empty="_____">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-600">Series No.</label>
                            <input type="text" name="notary_series_no" value="{{ $resolution->notary_series_no }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" data-live-target="notary-series-no">
                        </div>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Update Resolution Files
                    </button>
                </form>

                <div class="space-y-2">
                    <button type="button" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" @click="handleResolutionDownload(activeVersion, @js($draftDownloadUrl), @js($notarizedDownloadUrl))">
                        Download PDF
                    </button>
                    <button type="button" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="handleResolutionPrint(activeVersion, @js($draftUrl), @js($notarizedUrl))">
                        Print
                    </button>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Chairman</span><div class="font-medium text-gray-900">{{ $resolution->chairman }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $resolution->secretary }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Directors</span><div class="font-medium text-gray-900">{{ $resolution->directors }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function getResolutionUrl(activeVersion, draftUrl, notarizedUrl) {
        return activeVersion === 'original' ? (notarizedUrl || null) : (draftUrl || null);
    }

    function handleResolutionDownload(activeVersion, draftUrl, notarizedUrl) {
        const url = getResolutionUrl(activeVersion, draftUrl, notarizedUrl);
        if (url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            link.remove();
            return;
        }

        window.print();
    }

    function handleResolutionPrint(activeVersion, draftUrl, notarizedUrl) {
        const url = getResolutionUrl(activeVersion, draftUrl, notarizedUrl);
        if (url) {
            const win = window.open(url, '_blank');
            if (win) {
                window.setTimeout(() => win.print(), 800);
            }
            return;
        }

        window.print();
    }

    (() => {
        const form = document.getElementById('resolution-live-form');
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
            if (input.dataset.liveFormat === 'resolution-date-group') {
                const longValue = value ? formatDate(value, 'long') : '';
                const shortValue = value ? formatDate(value, 'short') : '';

                document.querySelectorAll('[data-preview="meeting-date"]').forEach((target) => {
                    target.textContent = longValue || '________________';
                });
                document.querySelectorAll('[data-preview="meeting-date-short"]').forEach((target) => {
                    target.textContent = shortValue || '';
                });
                return;
            }

            if (input.dataset.liveFormat === 'meeting-type-group') {
                document.querySelectorAll('[data-preview="meeting-type"]').forEach((target) => {
                    target.textContent = value || 'Special';
                });
                document.querySelectorAll('[data-preview="meeting-type-lower"]').forEach((target) => {
                    target.textContent = (value || 'special').toLowerCase();
                });
                return;
            }

            if (input.dataset.liveFormat === 'multiline') {
                const finalHtml = value
                    ? value.replace(/\n/g, '<br>')
                    : 'No board resolution text has been encoded yet.';

                targets.forEach((target) => {
                    target.innerHTML = finalHtml;
                });
                return;
            }

            if (input.dataset.liveFormat === 'year-fallback') {
                const directSeriesInput = form.querySelector('[name="notary_series_no"]');
                const directSeriesValue = directSeriesInput ? directSeriesInput.value.trim() : '';
                value = directSeriesValue || formatDate(value, 'year');
            }

            if (targetName === 'notary-series-no' && !value) {
                const notarizedOnInput = form.querySelector('[name="notarized_on"]');
                value = notarizedOnInput ? formatDate(notarizedOnInput.value, 'year') : '';
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
