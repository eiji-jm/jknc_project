@extends('layouts.app')
@section('title', 'Legal')
@section('content')
<div
    id="legal-page"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
    x-data="{
        showSlideOver: false,
        currentType: 'All Types',
        previewLegalType: 'Contract',
        previewClient: '',
        previewTin: '',
        previewDate: '{{ now()->format('Y-m-d') }}',
        previewUploader: '{{ Auth::user()->name ?? 'System' }}',
        previewFileUrl: '',
        previewFileName: '',
        previewFileKind: '',
        previewMode: 'empty'
    }"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Legal</h1>
            </div>

            <button onclick="openLegalAddSection()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        <div class="px-4 pt-4 bg-white border-b border-gray-100">
            <div class="flex gap-8 text-[15px] text-gray-700 overflow-x-auto">
                <button onclick="applyLegalWorkflowFilter('uploaded')" id="tab-uploaded" class="pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900">
                    Uploaded
                </button>
                <button onclick="applyLegalWorkflowFilter('submitted')" id="tab-submitted" class="pb-3 whitespace-nowrap">
                    Submitted
                </button>
                <button onclick="applyLegalWorkflowFilter('accepted')" id="tab-accepted" class="pb-3 whitespace-nowrap">
                    Accepted
                </button>
                <button onclick="applyLegalWorkflowFilter('reverted')" id="tab-reverted" class="pb-3 whitespace-nowrap">
                    Reverted
                </button>
                <button onclick="applyLegalWorkflowFilter('archived')" id="tab-archived" class="pb-3 whitespace-nowrap">
                    Archived
                </button>
            </div>

            <div id="legalStatusMessage" class="mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md">
                These records are uploaded and ready for submission.
            </div>
        </div>

        <div id="legalTableSection" class="flex-1 min-h-0 p-4">
            <div class="h-full border border-gray-200 rounded-xl overflow-hidden">
                <div class="h-full overflow-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Date</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Uploaded By</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Client</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">TIN</th>

                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap relative">
                                    <div class="flex items-center justify-between gap-2">
                                        <span>Type</span>

                                        <div class="relative">
                                            <button
                                                type="button"
                                                onclick="toggleLegalTypeFilterMenu(event)"
                                                class="text-gray-500 hover:text-gray-700"
                                            >
                                                <i class="fas fa-filter text-xs"></i>
                                            </button>

                                            <div
                                                id="legalTypeFilterMenu"
                                                class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-30 overflow-hidden"
                                            >
                                                <button type="button" onclick="setLegalTypeFilter('All Types')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">All Types</button>
                                                <button type="button" onclick="setLegalTypeFilter('Contract')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Contract</button>
                                                <button type="button" onclick="setLegalTypeFilter('Affidavit')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Affidavit</button>
                                                <button type="button" onclick="setLegalTypeFilter('Memorandum')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Memorandum</button>
                                                <button type="button" onclick="setLegalTypeFilter('Complaint')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Complaint</button>
                                                <button type="button" onclick="setLegalTypeFilter('Demand Letter')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Demand Letter</button>
                                                <button type="button" onclick="setLegalTypeFilter('Notice')" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Notice</button>
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Workflow Status</th>
                                <th class="px-4 py-3 border-b border-r border-gray-200 font-semibold whitespace-nowrap">Approval Status</th>
                                <th class="px-4 py-3 border-b font-semibold whitespace-nowrap">Document</th>
                            </tr>
                        </thead>
                        <tbody id="legalTableBody" class="bg-white"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="legalPreviewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div id="savedPreviewEmpty" class="hidden w-full h-full items-center justify-center bg-white text-gray-400 text-sm">
                        No document available.
                    </div>

                    <iframe id="savedPreviewPdf" class="hidden w-full h-full bg-white" frameborder="0"></iframe>

                    <div id="savedPreviewImageWrap" class="hidden w-full h-full bg-[#f8fafc] overflow-auto">
                        <img id="savedPreviewImage" src="" alt="Document Preview" class="block max-w-full h-auto mx-auto">
                    </div>
                </div>

                <div class="w-[330px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Document Preview</h2>
                        <button type="button" onclick="closeLegalPreview()" class="text-sm text-gray-500 hover:text-gray-700">
                            Close
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 overflow-y-auto">
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Legal Information</h3>

                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Type</span><span id="infoLegalType" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Client</span><span id="infoClient" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">TIN</span><span id="infoTin" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Date</span><span id="infoDate" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Document Name</span><span id="infoDocumentName" class="text-right font-medium text-gray-900 break-all"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Uploader</span><span id="infoUploader" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span id="infoStatus" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Workflow</span><span id="infoWorkflowStatus" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Approval</span><span id="infoApprovalStatus" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Review Note</span><span id="infoReviewNote" class="text-right font-medium text-gray-900 break-words"></span></div>

                            <div class="pt-2 border-t border-gray-200 space-y-3" id="legalPreviewActions">
                                <a id="openLegalPreviewBtn" href="#" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    Open in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/40" @click="closeLegalAddSection()"></div>

            <div class="absolute inset-0 flex">
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
                    <div class="max-w-[980px] mx-auto">
                        <div class="w-full bg-white border border-gray-200 rounded-xl overflow-hidden p-4">
                            <div class="w-full h-[calc(100vh-130px)] bg-white rounded-lg overflow-auto border border-gray-200 flex items-center justify-center">
                                <div x-show="previewMode === 'empty'" class="text-center px-6 text-gray-500">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Legal Document Preview</h3>
                                    <p class="text-sm">Upload a PDF or image file to preview the document here.</p>
                                </div>

                                <iframe
                                    x-show="previewMode === 'pdf'"
                                    :src="previewFileUrl"
                                    class="w-full h-full bg-white"
                                    frameborder="0"
                                ></iframe>

                                <div
                                    x-show="previewMode === 'image'"
                                    class="w-full h-full overflow-auto bg-white"
                                >
                                    <img
                                        :src="previewFileUrl"
                                        alt="Legal Preview"
                                        class="block max-w-full h-auto mx-auto"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <h2 class="text-lg font-semibold text-gray-800">Add Legal</h2>
                        <button type="button" @click="closeLegalAddSection()" class="text-gray-400 hover:text-gray-600 text-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        <div id="legalErrorBox" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
                        <div id="legalSuccessBox" class="hidden rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"></div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Type</label>
                            <select id="legalTypeInput" x-model="previewLegalType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="Contract">Contract</option>
                                <option value="Affidavit">Affidavit</option>
                                <option value="Memorandum">Memorandum</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Demand Letter">Demand Letter</option>
                                <option value="Notice">Notice</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Client</label>
                            <input id="clientInput" x-model="previewClient" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter client name">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">TIN</label>
                            <input id="tinInput" x-model="previewTin" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter TIN">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
                            <input id="dateInput" x-model="previewDate" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Upload Document</label>
                            <input
                                id="documentInput"
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:border-0 file:bg-blue-50 file:text-blue-700 file:px-3 file:py-1.5 file:rounded-md"
                            >
                            <p class="mt-1 text-xs text-gray-400">Accepted files: PDF, JPG, JPEG, PNG</p>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            <div class="flex justify-between gap-3">
                                <span>Preview File</span>
                                <span class="font-medium text-gray-900 break-all" x-text="previewFileName || 'No file selected'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
                        <button
                            type="button"
                            @click="closeLegalAddSection()"
                            class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Cancel
                        </button>

                        <button
                            id="saveLegalBtn"
                            type="button"
                            onclick="addLegal()"
                            class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .legal-type-tab.active {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
    }
</style>
@endsection

@push('scripts')
<script>
let legalRows = [];
let currentLegalWorkflowFilter = 'uploaded';

const urlParams = new URLSearchParams(window.location.search);
const autoOpenRecordId = urlParams.get('record');
const autoOpenTab = (urlParams.get('tab') || '').toLowerCase();

if (['uploaded', 'submitted', 'accepted', 'reverted', 'archived'].includes(autoOpenTab)) {
    currentLegalWorkflowFilter = autoOpenTab;
}

function getLegalAlpineData() {
    const root = document.getElementById('legal-page');
    return root ? Alpine.$data(root) : null;
}

function showLegalOnlySection(sectionId) {
    document.getElementById('legalTableSection').classList.add('hidden');
    document.getElementById('legalPreviewSection').classList.add('hidden');
    document.getElementById(sectionId).classList.remove('hidden');
}

function updateLegalStatusMessage() {
    const messageBox = document.getElementById('legalStatusMessage');

    if (currentLegalWorkflowFilter === 'uploaded') {
        messageBox.className = 'mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are uploaded and ready for submission.';
    } else if (currentLegalWorkflowFilter === 'submitted') {
        messageBox.className = 'mt-3 mb-4 border border-yellow-200 bg-yellow-50 text-yellow-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are already submitted and waiting for admin approval.';
    } else if (currentLegalWorkflowFilter === 'accepted') {
        messageBox.className = 'mt-3 mb-4 border border-green-200 bg-green-50 text-green-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were already accepted.';
    } else if (currentLegalWorkflowFilter === 'reverted') {
        messageBox.className = 'mt-3 mb-4 border border-red-200 bg-red-50 text-red-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were reverted and can be corrected then resubmitted.';
    } else {
        messageBox.className = 'mt-3 mb-4 border border-gray-200 bg-gray-50 text-gray-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are archived.';
    }
}

function setActiveLegalTab() {
    ['uploaded', 'submitted', 'accepted', 'reverted', 'archived'].forEach(tab => {
        const el = document.getElementById(`tab-${tab}`);
        if (!el) return;

        if (tab === currentLegalWorkflowFilter) {
            el.className = 'pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900';
        } else {
            el.className = 'pb-3 whitespace-nowrap text-gray-700';
        }
    });
}

function applyLegalWorkflowFilter(filterValue) {
    currentLegalWorkflowFilter = filterValue;
    renderLegalTable();
}

function showLegalError(message) {
    const errorBox = document.getElementById('legalErrorBox');
    const successBox = document.getElementById('legalSuccessBox');

    successBox.classList.add('hidden');
    successBox.innerHTML = '';

    errorBox.innerHTML = message;
    errorBox.classList.remove('hidden');
}

function showLegalSuccess(message) {
    const errorBox = document.getElementById('legalErrorBox');
    const successBox = document.getElementById('legalSuccessBox');

    errorBox.classList.add('hidden');
    errorBox.innerHTML = '';

    successBox.innerHTML = message;
    successBox.classList.remove('hidden');
}

function clearLegalMessages() {
    const errorBox = document.getElementById('legalErrorBox');
    const successBox = document.getElementById('legalSuccessBox');

    if (errorBox) {
        errorBox.classList.add('hidden');
        errorBox.innerHTML = '';
    }

    if (successBox) {
        successBox.classList.add('hidden');
        successBox.innerHTML = '';
    }
}

function setLegalSaveLoading(isLoading) {
    const btn = document.getElementById('saveLegalBtn');
    if (!btn) return;

    btn.disabled = isLoading;
    btn.classList.toggle('opacity-60', isLoading);
    btn.classList.toggle('cursor-not-allowed', isLoading);
    btn.textContent = isLoading ? 'Saving...' : 'Save';
}

function resetLegalFormDefaults() {
    const alpineData = getLegalAlpineData();
    const today = new Date().toISOString().split('T')[0];
    const fileInput = document.getElementById('documentInput');

    document.getElementById('legalTypeInput').value = 'Contract';
    document.getElementById('clientInput').value = '';
    document.getElementById('tinInput').value = '';
    document.getElementById('dateInput').value = today;

    if (fileInput) {
        fileInput.value = '';
    }

    if (alpineData) {
        alpineData.previewLegalType = 'Contract';
        alpineData.previewClient = '';
        alpineData.previewTin = '';
        alpineData.previewDate = today;
        alpineData.previewFileUrl = '';
        alpineData.previewFileName = '';
        alpineData.previewFileKind = '';
        alpineData.previewMode = 'empty';
    }
}

function openLegalAddSection() {
    const alpineData = getLegalAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = true;
    }

    resetLegalFormDefaults();
    clearLegalMessages();
}

function closeLegalAddSection() {
    const alpineData = getLegalAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = false;
    }

    resetLegalFormDefaults();
    clearLegalMessages();
}

function getLegalStatusClasses(status) {
    if (status === 'Completed') {
        return { textClass: 'text-green-600', dotClass: 'bg-green-500' };
    }

    if (status === 'Pending') {
        return { textClass: 'text-amber-600', dotClass: 'bg-amber-500' };
    }

    return { textClass: 'text-gray-500', dotClass: 'bg-gray-400' };
}

function getWorkflowClasses(status) {
    if (status === 'Uploaded') return 'text-orange-600';
    if (status === 'Submitted') return 'text-blue-600';
    if (status === 'Accepted') return 'text-green-600';
    if (status === 'Reverted') return 'text-yellow-600';
    if (status === 'Archived') return 'text-gray-600';
    return 'text-gray-500';
}

function getApprovalClasses(status) {
    if (status === 'Approved') return 'text-green-600';
    if (status === 'Rejected') return 'text-red-600';
    if (status === 'Needs Revision') return 'text-yellow-600';
    if (status === 'Pending') return 'text-blue-600';
    return 'text-gray-500';
}

function detectFileKind(path = '') {
    const lower = path.toLowerCase();

    if (lower.endsWith('.pdf')) return 'pdf';
    if (lower.endsWith('.jpg') || lower.endsWith('.jpeg') || lower.endsWith('.png')) return 'image';

    return '';
}

function toggleLegalTypeFilterMenu(event) {
    event.stopPropagation();
    document.getElementById('legalTypeFilterMenu').classList.toggle('hidden');
}

function setLegalTypeFilter(type) {
    const alpineData = getLegalAlpineData();
    if (alpineData) {
        alpineData.currentType = type;
    }

    document.getElementById('legalTypeFilterMenu').classList.add('hidden');
    renderLegalTable();
}

function openLegalPreview(index) {
    const item = legalRows[index];
    if (!item) return;

    const pdfFrame = document.getElementById('savedPreviewPdf');
    const imageWrap = document.getElementById('savedPreviewImageWrap');
    const image = document.getElementById('savedPreviewImage');
    const empty = document.getElementById('savedPreviewEmpty');
    const actions = document.getElementById('legalPreviewActions');

    pdfFrame.classList.add('hidden');
    imageWrap.classList.add('hidden');
    empty.classList.add('hidden');

    if (item.document_url) {
        const kind = detectFileKind(item.document_url);

        if (kind === 'pdf') {
            pdfFrame.src = item.document_url;
            pdfFrame.classList.remove('hidden');
        } else if (kind === 'image') {
            image.src = item.document_url;
            imageWrap.classList.remove('hidden');
        } else {
            empty.classList.remove('hidden');
        }
    } else {
        pdfFrame.src = '';
        image.src = '';
        empty.classList.remove('hidden');
    }

    document.getElementById('infoLegalType').textContent = item.legal_type ?? '';
    document.getElementById('infoClient').textContent = item.client ?? '';
    document.getElementById('infoTin').textContent = item.tin ?? 'N/A';
    document.getElementById('infoDate').textContent = item.date ?? '';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? 'N/A';
    document.getElementById('infoUploader').textContent = item.user ?? '';
    document.getElementById('infoStatus').textContent = item.status ?? '';
    document.getElementById('infoWorkflowStatus').textContent = item.workflow_status ?? '';
    document.getElementById('infoApprovalStatus').textContent = item.approval_status ?? '';
    document.getElementById('infoReviewNote').textContent = item.review_note ?? '—';

    actions.innerHTML = `
        <a id="openLegalPreviewBtn" href="${item.document_url ?? '#'}" target="_blank" class="text-sm text-blue-600 hover:underline">
            Open in New Tab
        </a>
    `;

    if (item.can_submit) {
        actions.innerHTML += `
            <button type="button" onclick="submitLegal(${item.id})" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 mt-3">
                Submit for Approval
            </button>
        `;
    }

    showLegalOnlySection('legalPreviewSection');
}

function closeLegalPreview() {
    document.getElementById('savedPreviewPdf').src = '';
    document.getElementById('savedPreviewImage').src = '';
    document.getElementById('savedPreviewPdf').classList.add('hidden');
    document.getElementById('savedPreviewImageWrap').classList.add('hidden');
    document.getElementById('savedPreviewEmpty').classList.add('hidden');
    showLegalOnlySection('legalTableSection');
}

async function fetchLegalData() {
    const alpineData = getLegalAlpineData();

    const params = new URLSearchParams();
    if (alpineData?.currentType && alpineData.currentType !== 'All Types') {
        params.append('type', alpineData.currentType);
    }

    params.append('workflow_status', currentLegalWorkflowFilter);

    const query = params.toString() ? `?${params.toString()}` : '';
    const response = await fetch(`/legal/data${query}`, {
        headers: { 'Accept': 'application/json' }
    });

    return await response.json();
}

async function renderLegalTable() {
    closeLegalPreview();

    const tableBody = document.getElementById('legalTableBody');
    tableBody.innerHTML = '';

    const data = await fetchLegalData();
    legalRows = data || [];

    updateLegalStatusMessage();
    setActiveLegalTab();

    if (!legalRows.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                    No legal records found.
                </td>
            </tr>
        `;
        return;
    }

    legalRows.forEach((item, index) => {
        const classes = getLegalStatusClasses(item.status);
        const hasDocument = !!item.document_url;

        tableBody.innerHTML += `
            <tr class="border-t border-gray-200 hover:bg-gray-50 ${hasDocument ? 'cursor-pointer' : ''}" ${hasDocument ? `onclick="openLegalPreview(${index})"` : ''}>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">${item.date ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">${item.user ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200">${item.client ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">${item.tin ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">${item.legal_type ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? ''}
                    </span>
                </td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap ${getWorkflowClasses(item.workflow_status)} font-medium">${item.workflow_status ?? ''}</td>
                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap ${getApprovalClasses(item.approval_status)} font-medium">${item.approval_status ?? ''}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    ${hasDocument
                        ? `<button type="button" onclick="event.stopPropagation(); openLegalPreview(${index})" class="text-blue-600 hover:underline">View</button>`
                        : '—'
                    }
                </td>
            </tr>
        `;
    });

    if (autoOpenRecordId) {
        const targetIndex = legalRows.findIndex(row => String(row.id) === String(autoOpenRecordId));
        if (targetIndex !== -1) {
            openLegalPreview(targetIndex);
        }
    }
}

async function addLegal() {
    clearLegalMessages();
    setLegalSaveLoading(true);

    const legalType = document.getElementById('legalTypeInput').value;
    const client = document.getElementById('clientInput').value.trim();
    const tin = document.getElementById('tinInput').value.trim();
    const date = document.getElementById('dateInput').value;
    const documentInput = document.getElementById('documentInput');

    if (!legalType || !client) {
        showLegalError('Please fill in Type and Client.');
        setLegalSaveLoading(false);
        return;
    }

    const formData = new FormData();
    formData.append('legal_type', legalType);
    formData.append('client', client);
    formData.append('tin', tin);
    formData.append('date', date);

    if (documentInput.files.length > 0) {
        formData.append('document', documentInput.files[0]);
    }

    try {
        const response = await fetch('/legal/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (data.errors) {
                const messages = Object.values(data.errors).flat().join('<br>');
                showLegalError(messages);
            } else if (data.message) {
                showLegalError(data.message);
            } else {
                showLegalError('Failed to save legal document.');
            }

            setLegalSaveLoading(false);
            return;
        }

        showLegalSuccess(data.message || 'Legal document saved successfully.');
        currentLegalWorkflowFilter = 'uploaded';
        await renderLegalTable();
        setLegalSaveLoading(false);
        closeLegalAddSection();
    } catch (error) {
        showLegalError('Something went wrong while saving.');
        setLegalSaveLoading(false);
    }
}

async function submitLegal(id) {
    const res = await fetch(`/legal/${id}/submit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || 'Unable to submit record.');
        return;
    }

    alert(data.message || 'Submitted successfully.');
    currentLegalWorkflowFilter = 'submitted';
    closeLegalPreview();
    await renderLegalTable();
}

document.addEventListener('DOMContentLoaded', function () {
    const alpineData = getLegalAlpineData();
    const documentInput = document.getElementById('documentInput');

    if (documentInput) {
        documentInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file || !alpineData) {
                if (alpineData) {
                    alpineData.previewFileUrl = '';
                    alpineData.previewFileName = '';
                    alpineData.previewFileKind = '';
                    alpineData.previewMode = 'empty';
                }
                return;
            }

            const fileUrl = URL.createObjectURL(file);
            const mime = file.type || '';
            let kind = '';

            if (mime.includes('pdf') || file.name.toLowerCase().endsWith('.pdf')) {
                kind = 'pdf';
            } else if (
                mime.includes('image') ||
                file.name.toLowerCase().endsWith('.jpg') ||
                file.name.toLowerCase().endsWith('.jpeg') ||
                file.name.toLowerCase().endsWith('.png')
            ) {
                kind = 'image';
            }

            alpineData.previewFileUrl = fileUrl;
            alpineData.previewFileName = file.name;
            alpineData.previewFileKind = kind;
            alpineData.previewMode = kind || 'empty';
        });
    }

    document.addEventListener('click', function (e) {
        const menu = document.getElementById('legalTypeFilterMenu');
        if (menu && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const data = getLegalAlpineData();
            if (data?.showSlideOver) {
                closeLegalAddSection();
            } else {
                closeLegalPreview();
            }
        }
    });

    renderLegalTable();
});
</script>
@endpush