@extends('layouts.app')

@section('content')
<div
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex-1 min-w-0 overflow-x-auto">
                <div id="accountingTabs" class="inline-flex min-w-max border border-gray-300 rounded-md overflow-hidden bg-white">
                    <button type="button" class="accounting-tab active px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="PNL">
                        PNL
                    </button>
                    <button type="button" class="accounting-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Balance Sheet">
                        Balance Sheet
                    </button>
                    <button type="button" class="accounting-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Cash Flow">
                        Cash Flow
                    </button>
                    <button type="button" class="accounting-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Income Statement">
                        Income Statement
                    </button>
                    <button type="button" class="accounting-tab px-5 py-2 text-sm bg-white hover:bg-gray-50" data-filter="AFS">
                        AFS
                    </button>
                </div>
            </div>

            <button onclick="openAddSection()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        {{-- TABLE VIEW --}}
        <div id="tableSection" class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-40 p-3 text-left">Date</th>
                            <th class="w-36 p-3 text-left">Uploader</th>
                            <th class="w-48 p-3 text-left">Client</th>
                            <th class="w-40 p-3 text-left">TIN</th>
                            <th class="w-32 p-3 text-left">Status</th>
                            <th class="w-40 p-3 text-left">Document</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

        {{-- PREVIEW VIEW --}}
        <div id="previewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full bg-white hidden" frameborder="0"></iframe>

                    <div id="previewImageWrapper" class="hidden h-full items-center justify-center bg-white">
                        <img id="previewImage" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                    </div>

                    <div id="previewEmptyState" class="h-full flex items-center justify-center text-gray-400 text-sm">
                        No document available for preview.
                    </div>
                </div>

                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Document Preview</h2>
                        <button type="button" onclick="closePreview()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6">
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Accounting Information</h3>
                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Type</span>
                                <span id="infoType" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Date</span>
                                <span id="infoDate" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Uploader</span>
                                <span id="infoUser" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Client</span>
                                <span id="infoClient" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">TIN</span>
                                <span id="infoTin" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Document Name</span>
                                <span id="infoDocumentName" class="text-right font-medium text-gray-900 break-all"></span>
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

        {{-- ADD SLIDE OVER --}}
        <div id="addSection" class="hidden fixed inset-0 z-50" aria-hidden="true">
            <div id="addBackdrop" class="absolute inset-0 bg-black/40" onclick="closeAddSection()"></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    id="addPanel"
                    class="w-screen max-w-[100vw] bg-white shadow-2xl flex h-full transform translate-x-full transition-transform duration-300 ease-in-out"
                >
                    {{-- LEFT: LIVE PREVIEW --}}
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

                    {{-- RIGHT: FORM --}}
                    <div class="w-full max-w-sm bg-white flex flex-col h-full">
                        <div class="p-6 border-b flex items-center justify-between shrink-0">
                            <h2 class="font-bold text-lg text-gray-900">Add Accounting Entry</h2>
                            <button type="button" onclick="closeAddSection()" class="text-sm text-gray-500 hover:text-gray-700">
                                Close
                            </button>
                        </div>

                        <div class="p-6 space-y-4 flex-1 overflow-y-auto min-h-0">
                            <div>
                                <label class="block text-sm font-medium mb-1">Client</label>
                                <input id="clientInput" class="w-full border rounded-md p-2" placeholder="Client">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">TIN</label>
                                <input id="tinInput" class="w-full border rounded-md p-2" placeholder="TIN">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Date</label>
                                <input id="dateInput" type="date" class="w-full border rounded-md p-2">
                            </div>

                            <div class="pt-2">
                                <label class="block text-sm font-medium mb-1 text-blue-700">Upload Document (PDF/Image)</label>
                                <input
                                    id="documentInput"
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full border border-blue-200 rounded-md p-2 bg-blue-50"
                                >
                                <p id="selectedFileName" class="mt-2 text-xs text-gray-500">No file selected</p>
                            </div>
                        </div>

                        <div class="p-6 border-t flex gap-2 shrink-0">
                            <button onclick="closeAddSection()" class="flex-1 border rounded py-2">Cancel</button>
                            <button onclick="addAccountingEntry().then(success => { if (success) { closeAddSection(); resetFormDefaults(); } })" class="flex-1 bg-blue-600 text-white rounded py-2">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .accounting-tab.active {
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 600;
    }
</style>

<script>
let currentAccountingFilter = 'PNL';
let accountingRows = [];
let livePreviewObjectUrl = null;

function showOnlySection(sectionId) {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById(sectionId).classList.remove('hidden');
}

function openAddSection() {
    resetFormDefaults();
    const addSection = document.getElementById('addSection');
    const addPanel = document.getElementById('addPanel');

    addSection.classList.remove('hidden');

    requestAnimationFrame(() => {
        addPanel.classList.remove('translate-x-full');
    });
}

function closeAddSection() {
    resetFormDefaults();
    const addSection = document.getElementById('addSection');
    const addPanel = document.getElementById('addPanel');

    addPanel.classList.add('translate-x-full');

    setTimeout(() => {
        addSection.classList.add('hidden');
    }, 300);
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    document.getElementById('previewFrame').classList.add('hidden');
    document.getElementById('previewImageWrapper').classList.add('hidden');
    document.getElementById('previewImageWrapper').classList.remove('flex');
    document.getElementById('previewImage').src = '';
    document.getElementById('previewEmptyState').classList.remove('hidden');
    showOnlySection('tableSection');
}

function resetFormDefaults() {
    document.getElementById('clientInput').value = '';
    document.getElementById('tinInput').value = '';
    document.getElementById('dateInput').value = '';
    document.getElementById('documentInput').value = '';
    document.getElementById('selectedFileName').textContent = 'No file selected';
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
    }
}

document.getElementById('documentInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    handleLivePreview(file);
});

async function fetchAccounting(filterValue) {
    const url = `/accounting?filter=${encodeURIComponent(filterValue)}`;
    const res = await fetch(url);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Completed') return { textClass: 'text-green-600', dotClass: 'bg-green-500' };
    if (status === 'Open') return { textClass: 'text-yellow-600', dotClass: 'bg-yellow-500' };
    if (status === 'Overdue') return { textClass: 'text-red-600', dotClass: 'bg-red-500' };
    return { textClass: 'text-gray-500', dotClass: 'bg-gray-400' };
}

function getFileType(path) {
    if (!path) return '';
    const lower = path.toLowerCase();
    if (lower.endsWith('.pdf')) return 'pdf';
    if (lower.endsWith('.jpg') || lower.endsWith('.jpeg') || lower.endsWith('.png')) return 'image';
    return '';
}

function drawTableRows() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    if (!accountingRows.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="p-10 text-center text-gray-400 italic">No data found</td>
            </tr>
        `;
        return;
    }

    accountingRows.forEach(item => {
        const classes = getStatusClasses(item.status);
        const safeId = item.id;
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr
                class="border-t ${isClickable ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50'}"
                ${isClickable ? `onclick="openPreview(${safeId})"` : ''}
            >
                <td class="p-3">${item.date ?? ''}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.client ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? ''}
                    </span>
                </td>
                <td class="p-3">
                    ${item.document_path
                        ? `<button type="button" onclick="event.stopPropagation(); openPreview(${safeId})" class="text-blue-600 hover:underline">View</button>`
                        : `<span class="text-gray-400">No File</span>`
                    }
                </td>
            </tr>
        `;
    });
}

function openPreview(id) {
    const item = accountingRows.find(row => row.id === id);
    if (!item) return;

    const frame = document.getElementById('previewFrame');
    const image = document.getElementById('previewImage');
    const imageWrapper = document.getElementById('previewImageWrapper');
    const emptyState = document.getElementById('previewEmptyState');

    frame.classList.add('hidden');
    frame.src = '';
    imageWrapper.classList.add('hidden');
    imageWrapper.classList.remove('flex');
    image.src = '';
    emptyState.classList.remove('hidden');

    if (item.document_path) {
        const fileType = getFileType(item.document_path);

        if (fileType === 'pdf') {
            frame.src = '/' + item.document_path;
            frame.classList.remove('hidden');
            emptyState.classList.add('hidden');
        } else if (fileType === 'image') {
            image.src = '/' + item.document_path;
            imageWrapper.classList.remove('hidden');
            imageWrapper.classList.add('flex');
            emptyState.classList.add('hidden');
        }
    }

    document.getElementById('infoType').textContent = item.type ?? '';
    document.getElementById('infoDate').textContent = item.date ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoClient').textContent = item.client ?? '';
    document.getElementById('infoTin').textContent = item.tin ?? '';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? 'N/A';
    document.getElementById('infoStatus').textContent = item.status ?? '';

    showOnlySection('previewSection');
}

async function renderTable(filterValue) {
    currentAccountingFilter = filterValue;
    accountingRows = await fetchAccounting(filterValue);
    drawTableRows();
    setActiveAccountingTab(filterValue);
}

async function addAccountingEntry() {
    const client = document.getElementById('clientInput').value.trim();
    const tin = document.getElementById('tinInput').value.trim();
    const date = document.getElementById('dateInput').value;
    const fileInput = document.getElementById('documentInput');
    const file = fileInput.files[0];

    if (!client || !date) {
        alert('Please fill in Client and Date.');
        return false;
    }

    if (!file) {
        alert('Please upload a document.');
        return false;
    }

    const formData = new FormData();
    formData.append('type', currentAccountingFilter);
    formData.append('client', client);
    formData.append('tin', tin);
    formData.append('date', date);
    formData.append('document', file);

    const res = await fetch('/accounting', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || 'Error saving accounting entry.');
        return false;
    }

    await renderTable(currentAccountingFilter);
    return true;
}

function setActiveAccountingTab(filterValue) {
    document.querySelectorAll('.accounting-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.filter === filterValue) {
            tab.classList.add('active');
        }
    });
}

document.querySelectorAll('.accounting-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const filterValue = tab.dataset.filter;
        renderTable(filterValue);
        showOnlySection('tableSection');
    });
});

renderTable(currentAccountingFilter);
</script>
@endsection