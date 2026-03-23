@extends('layouts.app')

@section('content')
<div
    x-data="{ hasDeadline: true }"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex-1 min-w-0 overflow-x-auto">
                <div id="correspondenceTabs" class="inline-flex min-w-max border border-gray-300 rounded-md overflow-hidden bg-white">
                    <button type="button" class="correspondence-tab active px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-type="Letters">
                        Letters
                    </button>
                    <button type="button" class="correspondence-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-type="Demand Letter">
                        Demand Letter
                    </button>
                    <button type="button" class="correspondence-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-type="Request Letter">
                        Request Letter
                    </button>
                    <button type="button" class="correspondence-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-type="Follow Up Letter">
                        Follow Up Letter
                    </button>
                    <button type="button" class="correspondence-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-type="Memo">
                        Memo
                    </button>
                    <button type="button" class="correspondence-tab px-5 py-2 text-sm bg-white hover:bg-gray-50" data-type="Notice">
                        Notice
                    </button>
                </div>
            </div>

            <button
                onclick="openAddSection()"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0"
            >
                + Add
            </button>
        </div>

        {{-- TABLE VIEW --}}
        <div id="tableSection" class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-36 p-3 text-left">Date Uploaded</th>
                            <th class="w-36 p-3 text-left">Uploaded By</th>
                            <th class="w-28 p-3 text-left">TIN</th>
                            <th class="w-40 p-3 text-left">Type</th>
                            <th class="w-36 p-3 text-left">Date Sent</th>
                            <th class="w-28 p-3 text-left">Time Sent</th>
                            <th class="w-40 p-3 text-left">Department</th>
                            <th class="w-40 p-3 text-left">From</th>
                            <th class="w-40 p-3 text-left">To</th>
                            <th class="w-44 p-3 text-left">Subject</th>
                            <th class="w-36 p-3 text-left">Respond Before</th>
                            <th class="w-28 p-3 text-left">Sent Via</th>
                            <th class="w-28 p-3 text-left">Status</th>
                            <th class="w-28 p-3 text-left">Template</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

        {{-- PREVIEW VIEW --}}
        <div id="previewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-auto p-6">
                    <div class="preview-canvas">
                        <div class="preview-paper-shell">
                            <iframe id="previewFrame" class="preview-paper-frame" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Document Preview</h2>
                        <button
                            type="button"
                            onclick="closePreview()"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Close
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 overflow-y-auto">
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Correspondence Information</h3>

                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Type</span>
                                <span id="infoType" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Uploaded Date</span>
                                <span id="infoUploadedDate" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Uploader</span>
                                <span id="infoUser" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">TIN</span>
                                <span id="infoTin" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Subject</span>
                                <span id="infoSubject" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">From</span>
                                <span id="infoFrom" class="text-right font-medium text-gray-900 break-all"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">To</span>
                                <span id="infoTo" class="text-right font-medium text-gray-900 break-all"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Department</span>
                                <span id="infoDepartment" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Date Sent</span>
                                <span id="infoDate" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Time Sent</span>
                                <span id="infoTime" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Respond Before</span>
                                <span id="infoDeadline" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Sent Via</span>
                                <span id="infoSentVia" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Status</span>
                                <span id="infoStatus" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="pt-2 border-t border-gray-200">
                                <a
                                    id="openPreviewBtn"
                                    href="#"
                                    target="_blank"
                                    class="text-sm text-blue-600 hover:underline"
                                >
                                    Open in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADD VIEW --}}
        <div id="addSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                {{-- LEFT: LIVE TEMPLATE PREVIEW --}}
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-auto p-6">
                    <div class="preview-canvas">
                        <div class="preview-paper-shell">
                            <iframe id="draftPreviewFrame" class="preview-paper-frame" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: FORM --}}
                <div class="w-[360px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Add Correspondence Entry</h2>
                        <button
                            type="button"
                            onclick="closeAddSection()"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Close
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 flex-1 overflow-y-auto">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">TIN</label>
                                <input id="tinInput" class="w-full border rounded-md p-2 preview-sync" placeholder="TIN">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Subject</label>
                                <input id="subjectInput" class="w-full border rounded-md p-2 preview-sync" placeholder="Subject">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">From / To</label>
                                <select id="senderTypeInput" class="w-full border rounded-md p-2 preview-sync">
                                    <option value="From">From</option>
                                    <option value="To">To</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Sender</label>
                                <input
                                    id="senderInput"
                                    class="w-full border rounded-md p-2 preview-sync"
                                    placeholder="Enter sender"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department / Stakeholder</label>
                                <input id="departmentInput" class="w-full border rounded-md p-2 preview-sync" placeholder="Department / Stakeholder">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Details</label>
                                <textarea id="detailsInput" class="w-full border rounded-md p-2 min-h-[120px] preview-sync" placeholder="Enter details"></textarea>
                            </div>

                            <div class="border rounded-md p-3 bg-gray-50">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input
                                        type="checkbox"
                                        x-model="hasDeadline"
                                        id="hasDeadlineInput"
                                        class="rounded border-gray-300"
                                        @change="if (!hasDeadline) document.getElementById('deadlineInput').value = ''"
                                    >
                                    This correspondence has a response deadline
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    If unchecked, response deadline will be disabled and status will automatically be Open.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Respond Before</label>
                                <input
                                    id="deadlineInput"
                                    type="date"
                                    class="w-full border rounded-md p-2 preview-sync"
                                    :disabled="!hasDeadline"
                                    :class="!hasDeadline ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Sent Via</label>
                                <select id="sentViaInput" class="w-full border rounded-md p-2 preview-sync">
                                    <option value="Email">Email</option>
                                    <option value="LBC">LBC</option>
                                    <option value="Internal">Internal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex gap-3">
                        <button onclick="closeAddSection()" class="flex-1 border py-2 rounded">Cancel</button>
                        <button
                            onclick="addCorrespondence().then(success => { if (success) { closeAddSection(); resetFormDefaults(); } })"
                            class="flex-1 bg-blue-600 text-white py-2 rounded"
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
    .correspondence-tab.active {
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 600;
    }

    .preview-canvas {
        min-width: max-content;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .preview-paper-shell {
        width: 794px;
        height: 1123px;
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .preview-paper-frame {
        width: 100%;
        height: 100%;
        background: white;
        display: block;
    }
</style>

<script>
let currentType = "Letters";
let correspondenceRows = [];
let previewRefreshTimer = null;

const previewRoutes = {
    "Letters": "letters",
    "Demand Letter": "demand-letter",
    "Request Letter": "request-letter",
    "Follow Up Letter": "follow-up-letter",
    "Memo": "memo",
    "Notice": "notice"
};

function showOnlySection(sectionId) {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById('addSection').classList.add('hidden');
    document.getElementById(sectionId).classList.remove('hidden');
}

function openAddSection() {
    resetFormDefaults();
    showOnlySection('addSection');
    refreshDraftPreview();
}

function closeAddSection() {
    document.getElementById('draftPreviewFrame').src = '';
    showOnlySection('tableSection');
}

function resetFormDefaults() {
    document.getElementById('tinInput').value = '';
    document.getElementById('subjectInput').value = '';
    document.getElementById('senderTypeInput').value = 'From';
    document.getElementById('senderInput').value = '';
    document.getElementById('departmentInput').value = '';
    document.getElementById('detailsInput').value = '';

    document.getElementById('hasDeadlineInput').checked = true;
    document.getElementById('deadlineInput').value = '';
    document.getElementById('deadlineInput').disabled = false;
    document.getElementById('deadlineInput').classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
    document.getElementById('sentViaInput').value = 'Email';
}

async function fetchCorrespondence(type) {
    const res = await fetch(`/correspondence/${encodeURIComponent(type)}`);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Open') {
        return {
            textClass: 'text-green-600',
            dotClass: 'bg-green-500'
        };
    }

    if (status === 'Closed') {
        return {
            textClass: 'text-red-600',
            dotClass: 'bg-red-500'
        };
    }

    return {
        textClass: 'text-gray-500',
        dotClass: 'bg-gray-400'
    };
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

function buildDraftPreviewUrl() {
    const routeSlug = previewRoutes[currentType];
    if (!routeSlug) return '';

    const hasDeadline = document.getElementById('hasDeadlineInput').checked;

    const params = new URLSearchParams({
        type: currentType,
        tin: document.getElementById('tinInput').value ?? '',
        subject: document.getElementById('subjectInput').value ?? '',
        sender_type: document.getElementById('senderTypeInput').value ?? 'From',
        sender: document.getElementById('senderInput').value ?? '',
        department: document.getElementById('departmentInput').value ?? '',
        details: document.getElementById('detailsInput').value ?? '',
        deadline: hasDeadline ? (document.getElementById('deadlineInput').value ?? '') : '',
        sent_via: document.getElementById('sentViaInput').value ?? 'Email',
    });

    return `/correspondence/draft-preview/${routeSlug}?${params.toString()}`;
}

function refreshDraftPreview() {
    const frame = document.getElementById('draftPreviewFrame');
    const url = buildDraftPreviewUrl();
    if (!url) return;
    frame.src = url;
}

function refreshDraftPreviewDebounced() {
    clearTimeout(previewRefreshTimer);
    previewRefreshTimer = setTimeout(() => {
        if (!document.getElementById('addSection').classList.contains('hidden')) {
            refreshDraftPreview();
        }
    }, 250);
}

async function renderTable(type) {
    currentType = type;
    closePreview();

    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    const data = await fetchCorrespondence(type);
    correspondenceRows = data || [];

    if (!correspondenceRows || correspondenceRows.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="14" class="p-10 text-center text-gray-400 italic">No correspondence records found</td></tr>`;
        return;
    }

    correspondenceRows.forEach((item, index) => {
        const classes = getStatusClasses(item.status);
        const canView = !!previewRoutes[item.type];
        const safeIndex = JSON.stringify(index);

        tableBody.innerHTML += `
            <tr class="border-t hover:bg-blue-50 ${canView ? 'cursor-pointer' : ''}" ${canView ? `onclick="openPreview(${safeIndex})"` : ''}>
                <td class="p-3">${item.uploaded_date ?? ''}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">${item.type ?? ''}</td>
                <td class="p-3">${item.date ?? ''}</td>
                <td class="p-3">${item.time ?? ''}</td>
                <td class="p-3">${item.department ?? ''}</td>
                <td class="p-3">${getFromValue(item) || ''}</td>
                <td class="p-3">${getToValue(item) || ''}</td>
                <td class="p-3">${item.subject ?? ''}</td>
                <td class="p-3">${item.deadline ?? 'No Deadline'}</td>
                <td class="p-3">${item.sent_via ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? 'No Status'}
                    </span>
                </td>
                <td class="p-3">
                    ${
                        canView
                            ? `<button type="button" onclick="event.stopPropagation(); openPreview(${safeIndex})" class="text-blue-600 hover:underline">View</button>`
                            : `<span class="text-gray-400">N/A</span>`
                    }
                </td>
            </tr>
        `;
    });
}

async function addCorrespondence() {
    const tin = document.getElementById('tinInput').value;
    const subject = document.getElementById('subjectInput').value;
    const senderType = document.getElementById('senderTypeInput').value;
    const sender = document.getElementById('senderInput').value;
    const department = document.getElementById('departmentInput').value;
    const details = document.getElementById('detailsInput').value;
    const hasDeadline = document.getElementById('hasDeadlineInput').checked;
    const deadline = hasDeadline ? document.getElementById('deadlineInput').value : null;
    const sentVia = document.getElementById('sentViaInput').value;

    if (!subject || !sender) {
        alert('Please fill in Subject and Sender.');
        return false;
    }

    const res = await fetch('/correspondence', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: currentType,
            tin: tin,
            subject: subject,
            sender_type: senderType,
            sender: sender,
            department: department,
            details: details,
            deadline: deadline,
            sent_via: sentVia
        })
    });

    if (!res.ok) {
        alert('Failed to save correspondence.');
        return false;
    }

    await renderTable(currentType);
    return true;
}

function setActiveCorrespondenceTab(type) {
    document.querySelectorAll('.correspondence-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.type === type) {
            tab.classList.add('active');
        }
    });
}

document.querySelectorAll('.correspondence-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const type = tab.dataset.type;
        setActiveCorrespondenceTab(type);
        renderTable(type);

        if (!document.getElementById('addSection').classList.contains('hidden')) {
            refreshDraftPreview();
        } else {
            showOnlySection('tableSection');
        }
    });
});

document.querySelectorAll('.preview-sync').forEach(el => {
    el.addEventListener('input', refreshDraftPreviewDebounced);
    el.addEventListener('change', refreshDraftPreviewDebounced);
});

document.getElementById('hasDeadlineInput').addEventListener('change', () => {
    const deadlineInput = document.getElementById('deadlineInput');

    if (document.getElementById('hasDeadlineInput').checked) {
        deadlineInput.disabled = false;
        deadlineInput.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
    } else {
        deadlineInput.value = '';
        deadlineInput.disabled = true;
        deadlineInput.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
    }

    refreshDraftPreviewDebounced();
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        closePreview();
    }
});

renderTable(currentType);
setActiveCorrespondenceTab(currentType);
</script>
@endsection