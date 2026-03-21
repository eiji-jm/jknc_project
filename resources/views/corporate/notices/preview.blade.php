@extends('layouts.app')

@section('content')
@php
    $selected = $notice;
    $documentUrl = $selected->document_path ? route('uploads.show', ['path' => $selected->document_path]) : null;
    $documentDownloadUrl = $selected->document_path ? route('uploads.show', ['path' => $selected->document_path, 'download' => 1]) : null;
    $meetingTitle = strtoupper(trim(($selected->type_of_meeting ?: 'Special') . ' ' . ($selected->governing_body ?: 'Board of Directors') . ' Meeting'));
    $noticeDate = optional($selected->date_of_notice)->format('F d, Y') ?: optional($selected->created_at)->format('F d, Y') ?: now()->format('F d, Y');
    $meetingDate = optional($selected->date_of_meeting)->format('F d, Y') ?: '________________';
    $meetingTime = $selected->time_started ? \Carbon\Carbon::createFromFormat('H:i:s', $selected->time_started)->format('h:i a') : ($selected->time_started ? \Carbon\Carbon::parse($selected->time_started)->format('h:i a') : '________________');
    $recipientLabel = match ($selected->governing_body) {
        'Stockholders' => 'ALL STOCKHOLDERS',
        'Joint Stockholders and Board of Directors' => 'ALL STOCKHOLDERS AND DIRECTORS',
        default => 'ALL DIRECTORS',
    };
    $companyName = strtoupper($selected->corporation_name ?: 'JOHN KELLY & COMPANY');
    $companyRegNo = $selected->company_reg_no ?: '2025120230900-02';
    $companyAddress = $selected->company_address ?: '3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE., CEBU BUSINESS PARK HIPPODROMO, CEBU CITY, 6000';
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #notice-print,
        #notice-print * {
            visibility: visible;
        }

        #notice-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            filter: none !important;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ route('notices') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Notice Preview</div>
                <div class="text-xs text-gray-500">Notice #: {{ $selected->notice_number ?: 'Draft Notice' }}</div>
            </div>
            <div class="flex-1"></div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $selected->type_of_meeting }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                @if ($documentUrl)
                    <div class="bg-gray-900 rounded-xl overflow-hidden">
                        <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex items-center gap-2">
                            <span class="text-gray-300 text-sm font-medium">Uploaded Notice PDF</span>
                            <div class="flex-1"></div>
                            <a href="{{ $documentUrl }}" target="_blank" class="text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-3 py-1.5">Open PDF</a>
                        </div>
                        <iframe src="{{ $documentUrl }}" class="w-full h-[820px] bg-white"></iframe>
                    </div>
                @else
                    <div class="bg-gray-900 rounded-xl overflow-hidden">
                        <div class="bg-gray-800 px-4 py-3 border-b border-gray-700">
                            <span class="text-gray-300 text-sm font-medium">Generated Notice Draft</span>
                        </div>
                        <div class="p-8 bg-[#f7f1e3]">
                            <div id="notice-print" class="mx-auto max-w-3xl bg-white shadow-xl rounded-sm p-12 min-h-[820px] text-[15px] leading-7 text-black" style="font-family: Georgia, 'Times New Roman', serif;">
                                <div class="text-center leading-tight text-red-600">
                                    <div class="text-[1.1rem] font-bold uppercase">{{ $companyName }}</div>
                                    <div class="text-sm font-semibold">COMPANY REG. NO.: {{ $companyRegNo }}</div>
                                    <div class="mt-1 text-sm">{{ $companyAddress }}</div>
                                </div>

                                <div class="mt-10 text-center text-[1.05rem] font-bold uppercase tracking-tight text-red-600">
                                    Notice and Agenda of the {{ $meetingTitle }}
                                </div>

                                <div class="mt-12 space-y-5">
                                    <div class="font-bold">To: <span class="ml-2">{{ $recipientLabel }}</span></div>
                                    <div class="font-bold">Date: <span class="ml-2 text-red-600">{{ $noticeDate }}</span></div>
                                </div>

                                <div class="mt-10 space-y-6 text-justify">
                                    <p class="font-semibold">
                                        NOTICE is hereby given that a <span class="text-red-600">{{ $selected->type_of_meeting ?: 'Special' }} {{ $selected->governing_body ?: 'Board of Directors' }} Meeting</span>
                                        of <span class="text-red-600">{{ $companyName }}</span> will be held at
                                        <span class="text-red-600">{{ $selected->location ?: '________________' }}</span>
                                        on <span class="text-red-600">{{ $meetingDate }}</span> at
                                        <span class="text-red-600">{{ $meetingTime }}</span>.
                                    </p>

                                    <div>
                                        <div class="font-semibold">Agenda:</div>
                                        <div class="prose prose-sm max-w-none mt-3 prose-p:my-2 prose-li:my-0.5 prose-ol:my-2 prose-ul:my-2 prose-strong:text-black">
                                            {!! $selected->body_html ?: '
                                                <ol>
                                                    <li>Invocation</li>
                                                    <li>Call to Order</li>
                                                    <li>Proof of Notice</li>
                                                    <li>Determination of Quorum</li>
                                                    <li>Reading and Approval of the Previous Minutes</li>
                                                    <li>Matters for Discussion and Approval</li>
                                                    <li>Other Business</li>
                                                    <li>Adjournment</li>
                                                </ol>
                                            ' !!}
                                        </div>
                                    </div>

                                    <p>
                                        In the instance that the meeting shall be conducted in-person, the minutes of the meeting shall be
                                        properly documented and securely stored as part of the official corporate records in accordance with
                                        applicable corporate governance requirements.
                                    </p>

                                    <p>
                                        Directors or stockholders intending to participate via video conferencing are requested to inform the
                                        Presiding Officer and the Corporate Secretary in advance so the quorum and participation records may be
                                        properly documented.
                                    </p>
                                </div>

                                <div class="mt-16">
                                    <div>Very truly yours,</div>
                                    <div class="mt-12 font-bold">{{ $selected->secretary ?: 'Corporate Secretary' }}</div>
                                    <div>Corporate Secretary</div>
                                </div>

                                <div class="mt-16 flex items-end justify-between text-[11px] leading-4 text-red-600">
                                    <div>
                                        <div class="font-bold uppercase">Notice for {{ $meetingTitle }}</div>
                                        <div>{{ $companyName }}</div>
                                        <div>Company Reg. No.: {{ $companyRegNo }}</div>
                                        <div>{{ $companyAddress }}</div>
                                    </div>
                                    <div class="font-bold">Page 1 of 1</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Meeting Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Governing Body</span><div class="font-medium text-gray-900">{{ $selected->governing_body }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Type of Meeting</span><div class="font-medium text-gray-900">{{ $selected->type_of_meeting }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting Date</span><div class="font-medium text-gray-900">{{ optional($selected->date_of_meeting)->format('M d, Y') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Time</span><div class="font-medium text-gray-900">{{ $selected->time_started }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Location</span><div class="font-medium text-gray-900">{{ $selected->location }}</div></div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">References</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Notice No.</span><div class="font-medium text-gray-900">{{ $selected->notice_number }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Meeting No.</span><div class="font-medium text-gray-900">{{ $selected->meeting_no }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Updated</span><div class="font-medium text-gray-900">{{ optional($selected->date_updated)->format('M d, Y') }}</div></div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Connected Records</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Minutes</div>
                            <div class="font-medium text-gray-900">{{ $selected->minutes->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Resolutions</div>
                            <div class="font-medium text-gray-900">{{ $selected->resolutions->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Secretary Certificates</div>
                            <div class="font-medium text-gray-900">{{ $selected->secretaryCertificates->count() }} linked</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Signatories</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Chairman</span><div class="font-medium text-gray-900">{{ $selected->chairman }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Secretary</span><div class="font-medium text-gray-900">{{ $selected->secretary }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Uploaded By</span><div class="font-medium text-gray-900">{{ $selected->uploaded_by }}</div></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="button" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" onclick="handleNoticeDownload(@js($documentDownloadUrl))">
                        Download PDF
                    </button>
                    <button type="button" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" onclick="handleNoticePrint(@js($documentUrl))">
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleNoticeDownload(url) {
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

    function handleNoticePrint(url) {
        if (url) {
            const win = window.open(url, '_blank');
            if (win) {
                window.setTimeout(() => win.print(), 800);
            }
            return;
        }

        window.print();
    }
</script>
@endsection
