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
                            <th class="w-36 p-3 text-left">From</th>
                            <th class="w-36 p-3 text-left">To</th>
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
        {{-- PREVIEW VIEW --}}
        <div id="previewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full bg-white" frameborder="0"></iframe>
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
                {{-- LEFT: LIVE PREVIEW --}}
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="h-full bg-white flex items-center justify-center">
                        <div class="text-center px-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Correspondence Entry Preview</h3>
                            <p class="text-sm text-gray-500">
                                Fill out the form on the right to add a new correspondence entry.
                            </p>
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
                                <input id="tinInput" class="w-full border rounded-md p-2" placeholder="TIN">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Subject</label>
                                <input id="subjectInput" class="w-full border rounded-md p-2" placeholder="Subject">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">From</label>
                                <input id="fromInput" class="w-full border rounded-md p-2" placeholder="From">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">To</label>
                                <input id="toInput" class="w-full border rounded-md p-2" placeholder="To">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Department / Stakeholder</label>
                                <input id="departmentInput" class="w-full border rounded-md p-2" placeholder="Department / Stakeholder">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Details</label>
                                <textarea id="detailsInput" class="w-full border rounded-md p-2 min-h-[120px]" placeholder="Enter details"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Date Sent</label>
                                <input id="dateInput" type="date" class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Time Sent</label>
                                <input id="timeInput" type="time" class="w-full border rounded-md p-2">
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
                                    class="w-full border rounded-md p-2"
                                    :disabled="!hasDeadline"
                                    :class="!hasDeadline ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Sent Via</label>
                                <select id="sentViaInput" class="w-full border rounded-md p-2">
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
</style>

<script>
let currentType = "Letters";
let correspondenceRows = [];

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
}

function closeAddSection() {
    showOnlySection('tableSection');
}

function resetFormDefaults() {
    document.getElementById('tinInput').value = '';
    document.getElementById('subjectInput').value = '';
    document.getElementById('fromInput').value = '';
    document.getElementById('toInput').value = '';
    document.getElementById('departmentInput').value = '';
    document.getElementById('detailsInput').value = '';

    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');

    document.getElementById('dateInput').value = `${year}-${month}-${day}`;
    document.getElementById('timeInput').value = `${hours}:${minutes}`;
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
    document.getElementById('infoFrom').textContent = item.from ?? '';
    document.getElementById('infoTo').textContent = item.to ?? '';
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
                <td class="p-3">${item.from ?? ''}</td>
                <td class="p-3">${item.to ?? ''}</td>
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
    const from = document.getElementById('fromInput').value;
    const to = document.getElementById('toInput').value;
    const department = document.getElementById('departmentInput').value;
    const details = document.getElementById('detailsInput').value;
    const date = document.getElementById('dateInput').value;
    const time = document.getElementById('timeInput').value;
    const hasDeadline = document.getElementById('hasDeadlineInput').checked;
    const deadline = hasDeadline ? document.getElementById('deadlineInput').value : null;
    const sentVia = document.getElementById('sentViaInput').value;

    if (!subject || !from || !to) {
        alert('Please fill in Subject, From, and To.');
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
            from: from,
            to: to,
            department: department,
            details: details,
            date: date,
            time: time,
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
        showOnlySection('tableSection');
    });
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