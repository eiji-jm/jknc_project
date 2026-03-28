@extends('layouts.app')

@section('content')
<div
    id="correspondence-page"
    class="w-full h-full px-6 py-5"
    x-data="{
        showSlideOver: false,
        hasDeadline: true,
        previewRef: 'AUTO-INCREMENT',
        previewDate: '{{ now()->format('Y-m-d') }}',
        previewType: 'Letters',
        previewTin: '',
        previewSenderLabel: 'From',
        previewSender: '',
        previewDepartment: '',
        previewSubject: '',
        previewBody: '<p style=&quot;color:#9ca3af;&quot;>Write the formal communication here...</p>',
        previewDeadline: '',
        previewSentVia: 'Email',
        previewPreparedBy: '{{ Auth::user()->name ?? 'System' }}'
    }"
>
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Correspondence</h1>
            </div>

            <button
                type="button"
                @click="showSlideOver = true"
                onclick="openAddSection()"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0"
            >
                + Add
            </button>



        </div>
        

        {{-- TABLE --}}
        <div id="tableSection" class="px-5 pb-4 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-md overflow-hidden flex-1 overflow-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Date</th>

                            <th class="px-3 py-3 border-r border-gray-200 font-semibold relative">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Correspondence Type</span>
                                    <div class="relative">
                                        <button
                                            type="button"
                                            onclick="toggleTypeFilterMenu(event)"
                                            class="text-gray-500 hover:text-gray-700"
                                        >
                                            <i class="fas fa-filter text-xs"></i>
                                        </button>

                                        <div
                                            id="typeFilterMenu"
                                            class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-30 overflow-hidden"
                                        >
                                            <button type="button" onclick="setTypeFilter('All')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                All Types
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Letters')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Letters
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Demand Letter')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Demand Letter
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Request Letter')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Request Letter
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Follow Up Letter')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Follow Up Letter
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Memo')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Memo
                                            </button>
                                            <button type="button" onclick="setTypeFilter('Notice')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                                Notice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>

                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">TIN</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Department</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">From</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">To</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Subject</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Respond Before</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Sent Via</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-3 py-3 font-semibold">Template</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

        {{-- SAVED PREVIEW --}}
        <div id="previewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full bg-white" frameborder="0"></iframe>
                </div>

                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Document Preview</h2>
                        <button type="button" onclick="closePreview()" class="text-sm text-gray-500 hover:text-gray-700">
                            Close
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 overflow-y-auto">
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Correspondence Information</h3>

                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Type</span><span id="infoType" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Uploaded Date</span><span id="infoUploadedDate" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Uploader</span><span id="infoUser" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">TIN</span><span id="infoTin" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Subject</span><span id="infoSubject" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">From</span><span id="infoFrom" class="text-right font-medium text-gray-900 break-all"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">To</span><span id="infoTo" class="text-right font-medium text-gray-900 break-all"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Department</span><span id="infoDepartment" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Date Sent</span><span id="infoDate" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Time Sent</span><span id="infoTime" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Respond Before</span><span id="infoDeadline" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Sent Via</span><span id="infoSentVia" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span id="infoStatus" class="text-right font-medium text-gray-900"></span></div>

                            <div class="pt-2 border-t border-gray-200">
                                <a id="openPreviewBtn" href="#" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    Open in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLIDE OVER --}}
        <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/40" @click="closeAddSectionAlpine()"></div>

            <div class="absolute inset-0 flex">
                {{-- LEFT PREVIEW --}}
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="w-[70%] h-full bg-[#f5f6f8] overflow-y-auto p-6 border-r border-gray-200"
                >
                    <div class="max-w-[900px] mx-auto mb-4 flex justify-end sticky top-0 z-10">
                        <button
                            type="button"
                            id="download-preview-pdf"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow transition"
                        >
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </button>
                    </div>

                    <div class="max-w-[900px] mx-auto flex justify-center">
                        <div
                            id="correspondence-preview-pdf"
                            class="correspondence-a4-page bg-white border border-gray-300 shadow"
                        >
                            <div class="flex items-start justify-between border-b border-gray-300 pb-6 mb-8">
                                <div>
                                    <h1 class="text-[22px] font-bold tracking-wide text-gray-900">JOHN KELLY &amp; COMPANY</h1>
                                    <p class="text-[12px] text-gray-500 mt-1">Correspondence Preview</p>
                                </div>

                                <div class="text-right text-[12px] text-gray-600 leading-5">
                                    <p>Ref No: <span class="font-semibold" x-text="previewRef"></span></p>
                                    <p>Date: <span class="font-semibold" x-text="previewDate || '________________'"></span></p>
                                    <p>Type: <span class="font-semibold" x-text="previewType || '________________'"></span></p>
                                </div>
                            </div>

                            <div class="text-center mb-8">
                                <h2 class="text-[20px] font-bold tracking-[0.18em] text-gray-900" x-text="(previewType || 'LETTERS').toUpperCase()"></h2>
                            </div>

                            <div class="space-y-3 text-[14px] text-gray-800 mb-10">
                                <div class="grid grid-cols-[120px_1fr] gap-3">
                                    <p class="font-semibold uppercase tracking-wide">TIN</p>
                                    <p class="border-b border-dotted border-gray-300 pb-1 break-words" x-text="previewTin || '______________________________'"></p>
                                </div>

                                <div class="grid grid-cols-[120px_1fr] gap-3">
                                    <p class="font-semibold uppercase tracking-wide" x-text="previewSenderLabel"></p>
                                    <p class="border-b border-dotted border-gray-300 pb-1 break-words" x-text="previewSender || '______________________________'"></p>
                                </div>

                                <div class="grid grid-cols-[120px_1fr] gap-3">
                                    <p class="font-semibold uppercase tracking-wide">Department</p>
                                    <p class="border-b border-dotted border-gray-300 pb-1 break-words" x-text="previewDepartment || '______________________________'"></p>
                                </div>

                                <div class="grid grid-cols-[120px_1fr] gap-3">
                                    <p class="font-semibold uppercase tracking-wide">Subject</p>
                                    <p class="border-b border-dotted border-gray-300 pb-1 font-semibold break-words" x-text="previewSubject || '______________________________'"></p>
                                </div>

                                <div class="grid grid-cols-[120px_1fr] gap-3">
                                    <p class="font-semibold uppercase tracking-wide">Sent Via</p>
                                    <p class="border-b border-dotted border-gray-300 pb-1 break-words" x-text="previewSentVia || '______________________________'"></p>
                                </div>

                                <template x-if="hasDeadline">
                                    <div class="grid grid-cols-[120px_1fr] gap-3">
                                        <p class="font-semibold uppercase tracking-wide">Respond Before</p>
                                        <p class="border-b border-dotted border-gray-300 pb-1 break-words" x-text="previewDeadline || '______________________________'"></p>
                                    </div>
                                </template>
                            </div>

                            <div class="correspondence-body text-[15px] text-gray-900">
                                <div
                                    class="body-content"
                                    x-html="previewBody"
                                ></div>
                            </div>

                            <div class="mt-16 space-y-10 text-[14px] text-gray-800">
                                <div>
                                    <p>Respectfully,</p>
                                    <div class="mt-12 border-b border-gray-400 w-[260px]"></div>
                                    <p class="mt-2 font-semibold break-words" x-text="previewPreparedBy || '________________'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT FORM --}}
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-[30%] h-full bg-white shadow-2xl flex flex-col"
                >
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Add Correspondence</h2>
                        <button type="button" @click="closeAddSectionAlpine()" class="text-gray-400 hover:text-gray-600 text-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        <div id="sliderErrorBox" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
                        <div id="sliderSuccessBox" class="hidden rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"></div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Ref #</label>
                            <input type="text" value="AUTO-INCREMENT" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600">
                            <p class="mt-1 text-xs text-gray-400">Date is automatically set to today when saved.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Correspondence Type</label>
                            <select id="typeInput" x-model="previewType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="Letters">Letters</option>
                                <option value="Demand Letter">Demand Letter</option>
                                <option value="Request Letter">Request Letter</option>
                                <option value="Follow Up Letter">Follow Up Letter</option>
                                <option value="Memo">Memo</option>
                                <option value="Notice">Notice</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">TIN</label>
                            <input id="tinInput" x-model="previewTin" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter TIN">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
                            <input id="subjectInput" x-model="previewSubject" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter subject">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">From / To</label>
                            <select id="senderTypeInput" x-model="previewSenderLabel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="From">From</option>
                                <option value="To">To</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Name / Value</label>
                            <input id="senderInput" x-model="previewSender" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter From or To value">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Department / Stakeholder</label>
                            <input id="departmentInput" x-model="previewDepartment" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter department or stakeholder">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Body</label>
                            <div id="editor" class="bg-white"></div>
                            <input type="hidden" id="detailsInput">
                        </div>

                        <div class="border rounded-md p-3 bg-gray-50">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input type="checkbox" x-model="hasDeadline" id="hasDeadlineInput" class="rounded border-gray-300" @change="if (!hasDeadline) { previewDeadline = ''; document.getElementById('deadlineInput').value = ''; }">
                                This correspondence has a response deadline
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Respond Before</label>
                            <input id="deadlineInput" type="date" x-model="previewDeadline" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" :disabled="!hasDeadline" :class="!hasDeadline ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500'">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Sent Via</label>
                            <select id="sentViaInput" x-model="previewSentVia" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="Email">Email</option>
                                <option value="LBC">LBC</option>
                                <option value="Internal">Internal</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
                        <button type="button" @click="closeAddSectionAlpine()" class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition">
                            Cancel
                        </button>

                        <button id="saveCorrespondenceBtn" type="button" onclick="addCorrespondence().then(success => { if (success) { closeAddSection(); } })" class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- END SLIDE OVER --}}
    </div>
</div>

<style>
    .correspondence-a4-page {
        width: 210mm;
        min-height: 297mm;
        padding: 18mm 18mm 20mm 18mm;
        box-sizing: border-box;
        background: #fff;
    }

    .correspondence-body {
        min-height: 420px;
        line-height: 1.85;
    }

    .body-content,
    .body-content * {
        max-width: 100%;
        box-sizing: border-box;
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: normal;
        white-space: normal;
    }

    .body-content p {
        margin: 0 0 14px 0;
        line-height: 1.85;
    }

    .body-content ul,
    .body-content ol {
        margin: 0 0 14px 22px;
        padding-left: 18px;
    }

    .body-content li {
        margin-bottom: 6px;
        line-height: 1.8;
    }

    .body-content img,
    .body-content video,
    .body-content iframe,
    .body-content table {
        max-width: 100%;
    }

    .body-content table {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .body-content td,
    .body-content th {
        overflow-wrap: break-word;
        word-wrap: break-word;
        white-space: normal;
    }

    #editor {
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    #editor .ql-toolbar.ql-snow {
        border: 0;
        border-bottom: 1px solid #e5e7eb;
    }

    #editor .ql-container.ql-snow {
        border: 0;
        font-size: 15px;
    }

    #editor .ql-editor {
        min-height: 320px;
        max-height: none;
        padding: 16px 18px;
        line-height: 1.85;
        overflow-wrap: break-word;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    #editor .ql-editor p,
    #editor .ql-editor li {
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    #editor .ql-editor img,
    #editor .ql-editor table {
        max-width: 100%;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
let currentTypeFilter = "All";
let correspondenceRows = [];

const previewRoutes = {
    "Letters": "letters",
    "Demand Letter": "demand-letter",
    "Request Letter": "request-letter",
    "Follow Up Letter": "follow-up-letter",
    "Memo": "memo",
    "Notice": "notice"
};

function getAlpineData() {
    const root = document.getElementById('correspondence-page');
    return root ? Alpine.$data(root) : null;
}

function showOnlySection(sectionId) {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById(sectionId).classList.remove('hidden');
}

function showSliderError(message) {
    const box = document.getElementById('sliderErrorBox');
    const successBox = document.getElementById('sliderSuccessBox');
    successBox.classList.add('hidden');
    successBox.innerHTML = '';
    box.innerHTML = message;
    box.classList.remove('hidden');
}

function showSliderSuccess(message) {
    const box = document.getElementById('sliderSuccessBox');
    const errorBox = document.getElementById('sliderErrorBox');
    errorBox.classList.add('hidden');
    errorBox.innerHTML = '';
    box.innerHTML = message;
    box.classList.remove('hidden');
}

function clearSliderMessages() {
    const errorBox = document.getElementById('sliderErrorBox');
    const successBox = document.getElementById('sliderSuccessBox');

    if (errorBox) {
        errorBox.classList.add('hidden');
        errorBox.innerHTML = '';
    }

    if (successBox) {
        successBox.classList.add('hidden');
        successBox.innerHTML = '';
    }
}

function setSaveLoading(isLoading) {
    const btn = document.getElementById('saveCorrespondenceBtn');
    if (!btn) return;

    btn.disabled = isLoading;
    btn.classList.toggle('opacity-60', isLoading);
    btn.classList.toggle('cursor-not-allowed', isLoading);
    btn.textContent = isLoading ? 'Saving...' : 'Save';
}

function openAddSection() {
    const alpineData = getAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = true;
    }

    resetFormDefaults();
    clearSliderMessages();
}

function closeAddSection() {
    const alpineData = getAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = false;
    }

    resetFormDefaults();
    clearSliderMessages();
    showOnlySection('tableSection');
}

function closeAddSectionAlpine() {
    closeAddSection();
}

function resetFormDefaults() {
    const alpineData = getAlpineData();
    const today = new Date().toISOString().split('T')[0];

    document.getElementById('typeInput').value = 'Letters';
    document.getElementById('tinInput').value = '';
    document.getElementById('subjectInput').value = '';
    document.getElementById('senderTypeInput').value = 'From';
    document.getElementById('senderInput').value = '';
    document.getElementById('departmentInput').value = '';
    document.getElementById('deadlineInput').value = '';
    document.getElementById('sentViaInput').value = 'Email';
    document.getElementById('hasDeadlineInput').checked = true;

    if (alpineData) {
        alpineData.hasDeadline = true;
        alpineData.previewRef = 'AUTO-INCREMENT';
        alpineData.previewDate = today;
        alpineData.previewType = 'Letters';
        alpineData.previewTin = '';
        alpineData.previewSubject = '';
        alpineData.previewSenderLabel = 'From';
        alpineData.previewSender = '';
        alpineData.previewDepartment = '';
        alpineData.previewBody = '<p style="color:#9ca3af;">Write the formal communication here...</p>';
        alpineData.previewDeadline = '';
        alpineData.previewSentVia = 'Email';
    }

    if (window.correspondenceQuill) {
        window.correspondenceQuill.setContents([]);
    }

    document.getElementById('detailsInput').value = '';
}

function toggleTypeFilterMenu(event) {
    event.stopPropagation();
    document.getElementById('typeFilterMenu').classList.toggle('hidden');
}

function setTypeFilter(type) {
    currentTypeFilter = type;
    document.getElementById('typeFilterMenu').classList.add('hidden');
    renderTable();
}

async function fetchCorrespondence() {
    const query = currentTypeFilter !== 'All'
        ? `?type=${encodeURIComponent(currentTypeFilter)}`
        : '';

    const res = await fetch(`/correspondence/data${query}`);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Open') {
        return { textClass: 'text-green-600', dotClass: 'bg-green-500' };
    }

    if (status === 'Closed') {
        return { textClass: 'text-red-600', dotClass: 'bg-red-500' };
    }

    return { textClass: 'text-gray-500', dotClass: 'bg-gray-400' };
}

function getFromValue(item) {
    return item.sender_type === 'From' ? (item.sender ?? '') : '';
}

function getToValue(item) {
    return item.sender_type === 'To' ? (item.sender ?? '') : '';
}

function openPreview(index) {
    const item = correspondenceRows[index];
    if (!item) return;

    const routeSlug = previewRoutes[item.type];
    if (!routeSlug) return;

    const previewUrl = `/correspondence/template/${routeSlug}/${item.id}`;

    document.getElementById('previewFrame').src = previewUrl;
    document.getElementById('openPreviewBtn').href = previewUrl;
    document.getElementById('infoType').textContent = item.type ?? '';
    document.getElementById('infoUploadedDate').textContent = item.uploaded_date ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoTin').textContent = item.tin ?? 'N/A';
    document.getElementById('infoSubject').textContent = item.subject ?? '';
    document.getElementById('infoFrom').textContent = getFromValue(item) || 'N/A';
    document.getElementById('infoTo').textContent = getToValue(item) || 'N/A';
    document.getElementById('infoDepartment').textContent = item.department ?? '';
    document.getElementById('infoDate').textContent = item.date ?? '';
    document.getElementById('infoTime').textContent = item.time ?? '';
    document.getElementById('infoDeadline').textContent = item.deadline ?? 'No Deadline';
    document.getElementById('infoSentVia').textContent = item.sent_via ?? '';
    document.getElementById('infoStatus').textContent = item.status ?? '';

    showOnlySection('previewSection');
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    document.getElementById('openPreviewBtn').href = '#';
    showOnlySection('tableSection');
}

async function renderTable() {
    closePreview();

    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const data = await fetchCorrespondence();
    correspondenceRows = data || [];

    if (!correspondenceRows.length) {
        tableBody.innerHTML = `<tr><td colspan="12" class="px-3 py-8 text-center text-gray-500">No correspondence records found.</td></tr>`;
        return;
    }

    correspondenceRows.forEach((item, index) => {
        const classes = getStatusClasses(item.status);
        const canView = !!previewRoutes[item.type];

        tableBody.innerHTML += `
            <tr class="border-t border-gray-200 hover:bg-gray-50 ${canView ? 'cursor-pointer' : ''}" ${canView ? `onclick="openPreview(${index})"` : ''}>
                <td class="px-3 py-3 border-r border-gray-200">COR-${String(item.id).padStart(5, '0')}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.date ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.type ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.tin ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.department ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${getFromValue(item) || ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${getToValue(item) || ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.subject ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.deadline ?? 'No Deadline'}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.sent_via ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? ''}
                    </span>
                </td>
                <td class="px-3 py-3">
                    ${canView ? `<button type="button" onclick="event.stopPropagation(); openPreview(${index})" class="text-blue-600 hover:underline">View</button>` : '—'}
                </td>
            </tr>
        `;
    });
}

async function addCorrespondence() {
    clearSliderMessages();
    setSaveLoading(true);

    const payload = {
        type: document.getElementById('typeInput').value,
        tin: document.getElementById('tinInput').value,
        subject: document.getElementById('subjectInput').value,
        sender_type: document.getElementById('senderTypeInput').value,
        sender: document.getElementById('senderInput').value,
        department: document.getElementById('departmentInput').value,
        details: document.getElementById('detailsInput').value,
        deadline: document.getElementById('hasDeadlineInput').checked ? document.getElementById('deadlineInput').value : null,
        sent_via: document.getElementById('sentViaInput').value
    };

    if (!payload.subject || !payload.sender || !payload.type) {
        showSliderError('Please fill in Correspondence Type, Subject, and Sender.');
        setSaveLoading(false);
        return false;
    }

    try {
        const res = await fetch('/correspondence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            if (data.errors) {
                const messages = Object.values(data.errors).flat().join('<br>');
                showSliderError(messages);
            } else if (data.message) {
                showSliderError(data.message);
            } else {
                showSliderError('Failed to save correspondence.');
            }

            setSaveLoading(false);
            return false;
        }

        showSliderSuccess('Correspondence saved successfully.');
        await renderTable();
        setSaveLoading(false);
        return true;
    } catch (error) {
        showSliderError('Something went wrong while saving.');
        setSaveLoading(false);
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const editorEl = document.getElementById('editor');
    const hiddenInput = document.getElementById('detailsInput');
    const rootEl = document.getElementById('correspondence-page');

    if (editorEl && hiddenInput && rootEl) {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write the formal communication here...',
            modules: {
                toolbar: [
                    [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        window.correspondenceQuill = quill;

        const alpineData = Alpine.$data(rootEl);

        quill.on('text-change', function () {
            const html = quill.root.innerHTML;
            const plainText = quill.getText().trim();

            hiddenInput.value = plainText ? html : '';

            if (alpineData) {
                alpineData.previewBody = plainText
                    ? html
                    : '<p style="color:#9ca3af;">Write the formal communication here...</p>';
            }
        });
    }

    const downloadBtn = document.getElementById('download-preview-pdf');

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            const element = document.getElementById('correspondence-preview-pdf');
            if (!element) return;

            const subject = document.getElementById('subjectInput')?.value?.trim() || 'correspondence';
            const safeFileName = subject.replace(/[\\/:*?"<>|]+/g, '').replace(/\s+/g, '-').toLowerCase();

            html2pdf().set({
                margin: [0, 0, 0, 0],
                filename: `${safeFileName}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(element).save();
        });
    }

    document.addEventListener('click', function (e) {
        const menu = document.getElementById('typeFilterMenu');
        if (menu && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const alpineData = getAlpineData();
            if (alpineData?.showSlideOver) {
                closeAddSection();
            } else {
                closePreview();
            }
        }
    });

    renderTable();
});
</script>
@endpush