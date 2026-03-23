@extends('layouts.app')

@section('content')
<div
    x-data="{ showSlideOver: false, hasDeadline: true }"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="relative bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">

        {{-- SLIDE OVER FORM --}}
        <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div class="absolute inset-0">
                <div @click="showSlideOver=false" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>

                <div class="absolute inset-y-0 right-0 flex max-w-full">
                    <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col h-full"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full">

                        <div class="p-6 border-b flex justify-between items-center">
                            <h2 class="font-bold text-lg">Add Correspondence Entry</h2>
                            <button @click="showSlideOver=false" class="text-gray-500 hover:text-gray-700">✕</button>
                        </div>

                        <div class="p-6 space-y-4 flex-1 overflow-y-auto">
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

                        <div class="p-6 border-t flex gap-3">
                            <button @click="showSlideOver=false" class="flex-1 border py-2 rounded">Cancel</button>
                            <button
                                @click="addCorrespondence().then(success => { if (success) { showSlideOver = false; hasDeadline = true; resetFormDefaults(); } })"
                                class="flex-1 bg-blue-600 text-white py-2 rounded"
                            >
                                Save
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- PREVIEW INSIDE PAGE --}}
        <div id="previewOverlay" class="hidden absolute inset-0 z-40">
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px]" onclick="closePreview()"></div>

            <div class="absolute inset-0 bg-[#f5f7fa] flex gap-5 p-4 overflow-hidden">
                {{-- LEFT PREVIEW --}}
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">
                    <div class="border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[18px] font-semibold text-gray-900">Correspondence Preview</h2>

                        <div class="flex items-center gap-2">
                            <a
                                id="openPreviewBtn"
                                href="#"
                                target="_blank"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700"
                            >
                                Open
                            </a>
                            <button
                                type="button"
                                onclick="closePreview()"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700"
                            >
                                Close
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 bg-[#edf2f6] p-4 overflow-auto">
                        <div class="mx-auto max-w-[980px] h-full">
                            <iframe
                                id="previewFrame"
                                class="w-full h-full min-h-[780px] bg-white rounded-lg border border-gray-300"
                                frameborder="0"
                            ></iframe>
                        </div>
                    </div>
                </div>

                {{-- RIGHT INFO --}}
                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4">
                        <h2 class="text-[20px] font-semibold text-gray-900">Correspondence Preview</h2>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
            <div class="relative">
                <button id="correspondenceDropdownBtn" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-md font-medium hover:bg-gray-200">
                    <span id="selectedType">Letters</span> ▾
                </button>

                <div id="correspondenceMenu" class="hidden absolute left-0 mt-2 w-56 bg-white border shadow-xl rounded-md z-50 py-1">
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Letters</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Demand Letter</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Request Letter</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Follow Up Letter</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Memo</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Notice</div>
                </div>
            </div>

            <button
                @click="showSlideOver = true; $nextTick(() => resetFormDefaults())"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm"
            >
                + Add
            </button>
        </div>

        {{-- TABLE --}}
        <div class="p-4 flex-grow overflow-hidden">
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

    </div>
</div>

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

    document.getElementById('previewOverlay').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    document.getElementById('openPreviewBtn').href = '#';
    document.getElementById('previewOverlay').classList.add('hidden');
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

        tableBody.innerHTML += `
            <tr class="border-t hover:bg-gray-50">
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
                            ? `<button type="button" onclick="openPreview(${index})" class="text-blue-600 hover:underline">View</button>`
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

renderTable(currentType);

document.getElementById("correspondenceDropdownBtn").addEventListener("click", e => {
    e.stopPropagation();
    document.getElementById("correspondenceMenu").classList.toggle("hidden");
});

document.getElementById("correspondenceMenu").addEventListener("click", e => {
    if (e.target.tagName === 'DIV') {
        const selected = e.target.innerText;
        document.getElementById("selectedType").innerText = selected;
        currentType = selected;
        renderTable(selected);
        document.getElementById("correspondenceMenu").classList.add("hidden");
    }
});

document.addEventListener("click", (e) => {
    const menu = document.getElementById("correspondenceMenu");
    const button = document.getElementById("correspondenceDropdownBtn");

    if (!menu.contains(e.target) && !button.contains(e.target)) {
        menu.classList.add("hidden");
    }
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        closePreview();
    }
});
</script>
@endsection