@extends('layouts.app')

@section('content')
<div
    id="lguPage"
    class="w-full px-4 sm:px-6 lg:px-8 mt-4"
    x-data="{
        openPanel: false,
        statusTab: 'uploaded'
    }"
>
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">LGU</h1>
            </div>

            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-bars text-sm"></i>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
                    <i class="fas fa-table-cells-large text-sm"></i>
                </button>

                <div class="flex items-center">
                    <button
                        type="button"
                        @click="openPanel = true; openAddSection()"
                        class="px-4 h-9 rounded-l-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        LGU
                    </button>

                    <button
                        type="button"
                        class="w-10 h-9 rounded-r-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center border-l border-white/20">
                        <i class="fas fa-caret-down text-xs"></i>
                    </button>
                </div>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 pt-4 bg-white border-b border-gray-100">
            <div class="flex gap-8 text-[15px] text-gray-700 overflow-x-auto">
                <button
                    @click="statusTab = 'uploaded'; applyWorkflowFilter('uploaded')"
                    :class="statusTab === 'uploaded' ? 'text-gray-900 border-b-2 border-blue-600 font-medium' : 'text-gray-700'"
                    class="pb-3 whitespace-nowrap">
                    Uploaded
                </button>

                <button
                    @click="statusTab = 'submitted'; applyWorkflowFilter('submitted')"
                    :class="statusTab === 'submitted' ? 'text-gray-900 border-b-2 border-blue-600 font-medium' : 'text-gray-700'"
                    class="pb-3 whitespace-nowrap">
                    Submitted
                </button>

                <button
                    @click="statusTab = 'accepted'; applyWorkflowFilter('accepted')"
                    :class="statusTab === 'accepted' ? 'text-gray-900 border-b-2 border-blue-600 font-medium' : 'text-gray-700'"
                    class="pb-3 whitespace-nowrap">
                    Accepted
                </button>

                <button
                    @click="statusTab = 'reverted'; applyWorkflowFilter('reverted')"
                    :class="statusTab === 'reverted' ? 'text-gray-900 border-b-2 border-blue-600 font-medium' : 'text-gray-700'"
                    class="pb-3 whitespace-nowrap">
                    Reverted
                </button>

                <button
                    @click="statusTab = 'archived'; applyWorkflowFilter('archived')"
                    :class="statusTab === 'archived' ? 'text-gray-900 border-b-2 border-blue-600 font-medium' : 'text-gray-700'"
                    class="pb-3 whitespace-nowrap">
                    Archived
                </button>
            </div>
        </div>

        <div class="bg-gray-50 min-h-[680px] flex flex-col">

            <div class="px-4 pt-4">
                <div id="statusMessage" class="border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md">
                    These records are uploaded and ready for submission.
                </div>
            </div>

            <div id="tableSection" class="p-3 flex-1 min-h-0">
                <div class="overflow-x-auto border border-gray-200 rounded-md bg-white h-full">
                    <table class="min-w-full text-[11px] text-left text-gray-700">
                        <thead class="bg-white border-b border-gray-200 sticky top-0 z-20">
                            <tr>
                                <th class="px-3 py-2 font-semibold">Permit No.</th>
                                <th class="px-3 py-2 font-semibold">Date of Registration</th>
                                <th class="px-3 py-2 font-semibold">Approved Date</th>
                                <th class="px-3 py-2 font-semibold">Expiration Date</th>
                                <th class="px-3 py-2 font-semibold">Uploader</th>
                                <th class="px-3 py-2 font-semibold">TIN</th>
                                <th class="px-3 py-2 font-semibold">Permit Type</th>
                                <th class="px-3 py-2 font-semibold">Document Type</th>
                                <th class="px-3 py-2 font-semibold">Workflow Status</th>
                                <th class="px-3 py-2 font-semibold">Approval Status</th>
                                <th class="px-3 py-2 font-semibold">Validity</th>
                                <th class="px-3 py-2 font-semibold">Document</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="bg-white"></tbody>
                    </table>
                </div>
            </div>

            <div id="previewSection" class="hidden p-4 flex-1 min-h-0 overflow-hidden">
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

                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6">
                            <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Permit Information</h3>

                            <div class="space-y-5 text-[14px]">
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Permit No.</span><span id="infoPermitNumber" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Permit Type</span><span id="infoPermitType" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Document Type</span><span id="infoDocumentType" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Document Name</span><span id="infoDocumentName" class="text-right font-medium text-gray-900 break-all"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Uploader</span><span id="infoUser" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">TIN</span><span id="infoTin" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Date Registered</span><span id="infoDateReg" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Approved Date</span><span id="infoApprovedDate" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Expiration Date</span><span id="infoExpirationDate" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Workflow Status</span><span id="infoWorkflowStatus" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Approval Status</span><span id="infoApprovalStatus" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Validity</span><span id="infoValidityStatus" class="text-right font-medium text-gray-900"></span></div>
                                <div class="flex justify-between gap-4"><span class="text-gray-500">Review Note</span><span id="infoReviewNote" class="text-right font-medium text-gray-900 break-all"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div
        x-show="openPanel"
        x-transition.opacity
        class="fixed inset-0 z-[70] bg-black/35"
        style="display:none;"
        @click="openPanel = false; closeAddSection()">
    </div>

    <div
        x-show="openPanel"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 bottom-0 z-[80] w-[80vw] bg-white border-l border-gray-300 shadow-2xl"
        style="display:none;"
    >
        <div class="h-full flex">

            <div class="flex-1 min-w-0 p-4 bg-gray-50 border-r border-gray-200">
                <div class="h-full bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div id="emptyPreviewState" class="h-full flex items-center justify-center text-gray-400 text-sm">
                        Upload a PDF or image to preview it here.
                    </div>

                    <iframe id="livePdfPreview" class="w-full h-full hidden bg-white" frameborder="0"></iframe>

                    <div id="liveImagePreviewWrapper" class="hidden h-full items-center justify-center bg-white">
                        <img id="liveImagePreview" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                    </div>
                </div>
            </div>

            <div class="w-full max-w-sm bg-white flex flex-col h-full">
                <div class="h-16 px-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-[26px] font-semibold text-gray-900 leading-none">
                        Add LGU Record
                    </h2>

                    <button
                        type="button"
                        @click="openPanel = false; closeAddSection()"
                        class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <div class="space-y-5">

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">TIN</label>
                            <input id="tinInput" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm" placeholder="TIN">
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Permit Type</label>
                            <select id="permitTypeInput" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                                <option value="">Select Permit Type</option>
                                <option value="Mayor's Permit">Mayor's Permit</option>
                                <option value="Barangay Business Permit">Barangay Business Permit</option>
                                <option value="Fire Permit">Fire Permit</option>
                                <option value="Sanitary Permit">Sanitary Permit</option>
                                <option value="OBO">OBO</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Document Type</label>
                            <select id="documentTypeInput" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                                <option value="">Select Document Type</option>
                                <option value="PDF">PDF</option>
                                <option value="Image">Image</option>
                                <option value="Scanned Copy">Scanned Copy</option>
                                <option value="Signed Copy">Signed Copy</option>
                                <option value="Original Copy">Original Copy</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Date of Registration</label>
                            <input id="dateOfRegistrationInput" type="date" class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm">
                        </div>

                        <div class="border rounded-md p-3 bg-gray-50">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    id="hasExpirationInput"
                                    class="rounded border-gray-300"
                                    checked
                                    onchange="toggleExpirationField()"
                                >
                                This permit has an expiration date
                            </label>
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Expiration Date</label>
                            <input
                                id="expirationDateOfRegistrationInput"
                                type="date"
                                class="w-full h-11 border border-gray-300 rounded-md px-4 text-sm"
                            >
                        </div>

                        <div class="pt-2">
                            <label class="block text-[13px] font-medium text-gray-700 mb-2">Upload Document (PDF/Image)</label>

                            <label class="w-full min-h-[84px] border border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center gap-2 px-4 cursor-pointer transition">
                                <i class="far fa-file-alt text-[26px] text-gray-500"></i>
                                <span class="text-[14px] text-blue-600 font-medium">Choose document file</span>
                                <span class="text-[11px] text-gray-400">PDF, JPG, JPEG, PNG supported</span>
                                <input
                                    id="documentInput"
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="hidden"
                                >
                            </label>

                            <p id="selectedFileName" class="mt-2 text-xs text-gray-500">No file selected</p>
                        </div>

                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="openPanel = false; closeAddSection()"
                        class="min-w-[92px] px-6 py-2.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>

                    <button
                        id="savePermitBtn"
                        type="button"
                        onclick="addPermit()"
                        class="min-w-[92px] px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentWorkflowFilter = 'uploaded';
let permitRows = [];
let livePreviewObjectUrl = null;
let isSavingPermit = false;

function setOpenPanel(value) {
    const root = document.getElementById('lguPage');
    if (root && root._x_dataStack && root._x_dataStack[0]) {
        root._x_dataStack[0].openPanel = value;
    }
}

function showOnlySection(sectionId) {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById(sectionId).classList.remove('hidden');
}

function openAddSection() {
    resetFormDefaults();
}

function closeAddSection() {
    resetFormDefaults();
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    showOnlySection('tableSection');
}

function toggleExpirationField() {
    const checkbox = document.getElementById('hasExpirationInput');
    const expirationInput = document.getElementById('expirationDateOfRegistrationInput');

    if (!checkbox.checked) {
        expirationInput.value = '';
        expirationInput.disabled = true;
        expirationInput.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
    } else {
        expirationInput.disabled = false;
        expirationInput.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
    }
}

function resetFormDefaults() {
    document.getElementById('tinInput').value = '';
    document.getElementById('permitTypeInput').value = '';
    document.getElementById('documentTypeInput').value = '';
    document.getElementById('dateOfRegistrationInput').value = '';
    document.getElementById('documentInput').value = '';
    document.getElementById('selectedFileName').textContent = 'No file selected';

    const checkbox = document.getElementById('hasExpirationInput');
    const expirationInput = document.getElementById('expirationDateOfRegistrationInput');

    checkbox.checked = true;
    expirationInput.value = '';
    expirationInput.disabled = false;
    expirationInput.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');

    clearLivePreview();
}

function clearLivePreview() {
    if (livePreviewObjectUrl) {
        URL.revokeObjectURL(livePreviewObjectUrl);
        livePreviewObjectUrl = null;
    }

    document.getElementById('emptyPreviewState').classList.remove('hidden');
    document.getElementById('livePdfPreview').classList.add('hidden');
    document.getElementById('livePdfPreview').src = '';
    document.getElementById('liveImagePreviewWrapper').classList.add('hidden');
    document.getElementById('liveImagePreviewWrapper').classList.remove('flex');
    document.getElementById('liveImagePreview').src = '';
}

function handleLivePreview(file) {
    clearLivePreview();

    if (!file) return;

    document.getElementById('selectedFileName').textContent = file.name;
    livePreviewObjectUrl = URL.createObjectURL(file);

    if (file.type === 'application/pdf') {
        const pdfFrame = document.getElementById('livePdfPreview');
        pdfFrame.src = livePreviewObjectUrl;
        pdfFrame.classList.remove('hidden');
        document.getElementById('emptyPreviewState').classList.add('hidden');
        return;
    }

    if (file.type.startsWith('image/')) {
        const image = document.getElementById('liveImagePreview');
        const wrapper = document.getElementById('liveImagePreviewWrapper');
        image.src = livePreviewObjectUrl;
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
        document.getElementById('emptyPreviewState').classList.add('hidden');
        return;
    }
}

document.getElementById('documentInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    handleLivePreview(file);
});

async function fetchPermits() {
    const res = await fetch('/permits');
    return await res.json();
}

function workflowLabel(value) {
    if (!value) return 'Uploaded';
    return value.charAt(0).toUpperCase() + value.slice(1);
}

function approvalLabel(value) {
    if (!value) return 'Pending';
    return value;
}

function validityBadgeClass(value) {
    if (value === 'Expired') return 'bg-red-50 text-red-600';
    return 'bg-green-50 text-green-700';
}

function workflowBadgeClass(value) {
    const v = (value || '').toLowerCase();
    if (v === 'uploaded') return 'bg-orange-50 text-orange-700';
    if (v === 'submitted') return 'bg-blue-50 text-blue-700';
    if (v === 'accepted') return 'bg-green-50 text-green-700';
    if (v === 'reverted') return 'bg-yellow-50 text-yellow-700';
    if (v === 'archived') return 'bg-gray-100 text-gray-700';
    return 'bg-orange-50 text-orange-700';
}

function approvalBadgeClass(value) {
    const v = (value || '').toLowerCase();
    if (v === 'approved') return 'bg-green-50 text-green-700';
    if (v === 'pending') return 'bg-yellow-50 text-yellow-700';
    if (v === 'reverted') return 'bg-red-50 text-red-700';
    if (v === 'archived') return 'bg-gray-100 text-gray-700';
    return 'bg-yellow-50 text-yellow-700';
}

function updateStatusMessage() {
    const messageBox = document.getElementById('statusMessage');

    if (currentWorkflowFilter === 'uploaded') {
        messageBox.className = 'border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are uploaded and ready for submission.';
    } else if (currentWorkflowFilter === 'submitted') {
        messageBox.className = 'border border-yellow-200 bg-yellow-50 text-yellow-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records have already been submitted and are waiting for approval.';
    } else if (currentWorkflowFilter === 'accepted') {
        messageBox.className = 'border border-green-200 bg-green-50 text-green-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were already accepted and approved.';
    } else if (currentWorkflowFilter === 'reverted') {
        messageBox.className = 'border border-red-200 bg-red-50 text-red-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were reverted and need correction.';
    } else if (currentWorkflowFilter === 'archived') {
        messageBox.className = 'border border-gray-200 bg-gray-50 text-gray-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are archived or already expired.';
    }
}

function normalizeWorkflow(value) {
    const v = (value || 'Uploaded').toLowerCase();
    if (v === 'uploaded' || v === 'submitted' || v === 'accepted' || v === 'reverted' || v === 'archived') {
        return v;
    }
    return 'uploaded';
}

function shouldShowInCurrentTab(item) {
    const workflow = normalizeWorkflow(item.workflow_status);
    const validity = item.status || 'Active';

    if (currentWorkflowFilter === 'archived') {
        return workflow === 'archived' || validity === 'Expired';
    }

    if (validity === 'Expired') {
        return false;
    }

    return workflow === currentWorkflowFilter;
}

async function submitPermit(id) {
    try {
        const res = await fetch(`/permits/${id}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const rawText = await res.text();
        let data = {};

        try {
            data = rawText ? JSON.parse(rawText) : {};
        } catch (e) {
            alert('Submit response is not valid JSON.');
            console.error(rawText);
            return;
        }

        if (!res.ok) {
            alert(data.message || 'Unable to submit permit.');
            return;
        }

        await renderTable();
        alert(data.message || 'Permit submitted for approval.');
    } catch (error) {
        console.error(error);
        alert('Something went wrong while submitting.');
    }
}

function drawTableRows() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const filteredRows = permitRows.filter(item => shouldShowInCurrentTab(item));

    if (!filteredRows.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="12" class="px-3 py-6 text-center text-gray-400">
                    No records found.
                </td>
            </tr>
        `;
        return;
    }

    filteredRows.forEach(item => {
        const workflow = normalizeWorkflow(item.workflow_status);
        const validity = item.status || 'Active';
        const approval = item.approval_status || 'Pending';
        const safePermitNumber = JSON.stringify(item.permit_number ?? '');
        const canSubmit = (workflow === 'uploaded' || workflow === 'reverted') && validity !== 'Expired';
        const hasFile = !!item.document_path;
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr
                class="border-b border-gray-200 ${isClickable ? 'hover:bg-blue-50 cursor-pointer transition' : 'hover:bg-gray-50 transition'}"
                ${isClickable ? `onclick='openPreviewByPermitNumber(${safePermitNumber})'` : ''}
            >
                <td class="px-3 py-2 font-medium ${isClickable ? 'text-blue-600' : 'text-gray-700'}">
                    ${item.permit_number ?? ''}
                </td>
                <td class="px-3 py-2">${item.date_of_registration ?? ''}</td>
                <td class="px-3 py-2">${item.approved_date_of_registration ?? ''}</td>
                <td class="px-3 py-2">${item.expiration_date_of_registration ?? 'No Expiration'}</td>
                <td class="px-3 py-2">${item.user ?? ''}</td>
                <td class="px-3 py-2">${item.tin ?? ''}</td>
                <td class="px-3 py-2">${item.permit_type ?? ''}</td>
                <td class="px-3 py-2">${item.document_type ?? ''}</td>
                <td class="px-3 py-2">
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium ${workflowBadgeClass(workflow)}">
                        ${workflowLabel(workflow)}
                    </span>
                </td>
                <td class="px-3 py-2">
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium ${approvalBadgeClass(approval)}">
                        ${approvalLabel(approval)}
                    </span>
                </td>
                <td class="px-3 py-2">
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium ${validityBadgeClass(validity)}">
                        ${validity}
                    </span>
                </td>
                <td class="px-3 py-2">
                    <div class="flex items-center gap-2">
                        ${hasFile
                            ? `<button type="button" onclick='event.stopPropagation(); openPreviewByPermitNumber(${safePermitNumber})' class="text-blue-600 font-medium hover:underline">View</button>`
                            : `<span class="text-gray-400">No File</span>`}
                        ${canSubmit && hasFile
                            ? `<button type="button" onclick="event.stopPropagation(); submitPermit(${item.id})" class="px-2 py-1 rounded bg-blue-600 text-white text-[10px] hover:bg-blue-700">Submit</button>`
                            : ``}
                    </div>
                </td>
            </tr>
        `;
    });
}

function openPreviewByPermitNumber(permitNumber) {
    const item = permitRows.find(row => (row.permit_number ?? '') === permitNumber);
    if (!item || !item.document_path) return;

    document.getElementById('previewFrame').src = '/' + item.document_path;
    document.getElementById('infoPermitNumber').textContent = item.permit_number ?? '';
    document.getElementById('infoPermitType').textContent = item.permit_type ?? '';
    document.getElementById('infoDocumentType').textContent = item.document_type ?? '';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoTin').textContent = item.tin || 'N/A';
    document.getElementById('infoDateReg').textContent = item.date_of_registration || '';
    document.getElementById('infoApprovedDate').textContent = item.approved_date_of_registration || '';
    document.getElementById('infoExpirationDate').textContent = item.expiration_date_of_registration || 'No Expiration';
    document.getElementById('infoWorkflowStatus').textContent = workflowLabel(normalizeWorkflow(item.workflow_status));
    document.getElementById('infoApprovalStatus').textContent = approvalLabel(item.approval_status);
    document.getElementById('infoValidityStatus').textContent = item.status || 'Active';
    document.getElementById('infoReviewNote').textContent = item.review_note || '-';

    showOnlySection('previewSection');
}

async function renderTable() {
    const permitData = await fetchPermits();
    permitRows = permitData || [];
    updateStatusMessage();
    drawTableRows();
}

async function addPermit() {
    if (isSavingPermit) return;

    const permitType = document.getElementById('permitTypeInput').value;
    const documentType = document.getElementById('documentTypeInput').value;
    const fileInput = document.getElementById('documentInput');
    const saveBtn = document.getElementById('savePermitBtn');

    if (!permitType) {
        alert('Please select a Permit Type.');
        return;
    }

    if (!documentType) {
        alert('Please select a Document Type.');
        return;
    }

    if (fileInput.files.length === 0) {
        alert('Please upload a document.');
        return;
    }

    isSavingPermit = true;
    saveBtn.disabled = true;
    saveBtn.classList.add('opacity-50', 'cursor-not-allowed');

    try {
        const formData = new FormData();
        formData.append('permit_type', permitType);
        formData.append('document_type', documentType);
        formData.append('tin', document.getElementById('tinInput').value);
        formData.append('date_of_registration', document.getElementById('dateOfRegistrationInput').value);

        const hasExp = document.getElementById('hasExpirationInput').checked;
        if (hasExp) {
            formData.append('expiration_date_of_registration', document.getElementById('expirationDateOfRegistrationInput').value);
        }

        formData.append('document', fileInput.files[0]);

        const res = await fetch('/permits', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const rawText = await res.text();
        let data = {};

        try {
            data = rawText ? JSON.parse(rawText) : {};
        } catch (e) {
            console.error('Invalid JSON response:', rawText);
            alert('Saved in database, but response is not valid JSON.');
            return;
        }

        if (!res.ok) {
            alert(data.message || 'Error saving permit.');
            return;
        }

        setOpenPanel(false);
        resetFormDefaults();
        await renderTable();
        alert(data.message || 'Permit saved successfully.');
    } catch (error) {
        console.error(error);
        alert('Something went wrong while saving.');
    } finally {
        isSavingPermit = false;
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function applyWorkflowFilter(filterValue) {
    currentWorkflowFilter = filterValue;
    updateStatusMessage();
    drawTableRows();
    showOnlySection('tableSection');
}

renderTable();
</script>
@endsection