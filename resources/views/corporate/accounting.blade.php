@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Accounting</h1>
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
                            <th class="w-44 p-3 text-left relative">
                                <button
                                    type="button"
                                    id="statementTypeHeaderBtn"
                                    class="w-full flex items-center gap-2 hover:text-gray-900"
                                >
                                    <span>Statement Type</span>
                                    <span>▼</span>
                                </button>

                                <div
                                    id="statementTypeHeaderMenu"
                                    class="hidden absolute left-3 top-full mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-30 overflow-hidden"
                                >
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="All Statement Types">
                                        All Statement Types
                                    </button>
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="PNL">
                                        PNL
                                    </button>
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="Balance Sheet">
                                        Balance Sheet
                                    </button>
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="Cash Flow">
                                        Cash Flow
                                    </button>
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="Income Statement">
                                        Income Statement
                                    </button>
                                    <button type="button" class="header-statement-option w-full text-left px-4 py-2 text-sm hover:bg-gray-50" data-filter="AFS">
                                        AFS
                                    </button>
                                </div>
                            </th>
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
                                <span class="text-gray-500">Statement Type</span>
                                <span id="infoStatementType" class="text-right font-medium text-gray-900"></span>
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
                <div id="addPanel" class="w-screen max-w-[100vw] bg-white shadow-2xl flex h-full transform translate-x-full transition-transform duration-300 ease-in-out">
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
                        <div class="p-6 border-b flex items-center justify-between shrink-0">
                            <h2 class="font-bold text-lg text-gray-900">Add Accounting Entry</h2>
                            <button type="button" onclick="closeAddSection()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
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
                                <label class="block text-sm font-medium mb-1">Statement Type</label>
                                <select id="statementTypeInput" class="w-full border rounded-md p-2">
                                    <option value="">Select Statement Type</option>
                                    <option value="PNL">PNL</option>
                                    <option value="Balance Sheet">Balance Sheet</option>
                                    <option value="Cash Flow">Cash Flow</option>
                                    <option value="Income Statement">Income Statement</option>
                                    <option value="AFS">AFS</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Date</label>
                                <input id="dateInput" type="date" class="w-full border rounded-md p-2">
                            </div>
                            <div class="pt-2">
                                <label class="block text-sm font-medium mb-1 text-blue-700">Upload Document (PDF/Image)</label>
                                <input id="documentInput" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-blue-200 rounded-md p-2 bg-blue-50">
                                <p id="selectedFileName" class="mt-2 text-xs text-gray-500">No file selected</p>
                            </div>
                        </div>

                        <div class="p-6 border-t flex gap-2 shrink-0">
                            <button onclick="closeAddSection()" class="flex-1 border rounded py-2">Cancel</button>
                            <button onclick="addAccountingEntry().then(success => { if (success) { closeAddSection(); resetFormDefaults(); } })" class="flex-1 bg-blue-600 text-white rounded py-2">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStatementTypeFilter = 'All Statement Types';
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
    requestAnimationFrame(() => { addPanel.classList.remove('translate-x-full'); });
}

function closeAddSection() {
    resetFormDefaults();
    const addSection = document.getElementById('addSection');
    const addPanel = document.getElementById('addPanel');
    addPanel.classList.add('translate-x-full');
    setTimeout(() => { addSection.classList.add('hidden'); }, 300);
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
    document.getElementById('statementTypeInput').value = '';
    document.getElementById('dateInput').value = '';
    document.getElementById('documentInput').value = '';
    document.getElementById('selectedFileName').textContent = 'No file selected';
    clearLivePreview();
}

function clearLivePreview() {
    if (livePreviewObjectUrl) { URL.revokeObjectURL(livePreviewObjectUrl); livePreviewObjectUrl = null; }
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
    } else if (file.type.startsWith('image/')) {
        const image = document.getElementById('liveImagePreview');
        const wrapper = document.getElementById('liveImagePreviewWrapper');
        image.src = livePreviewObjectUrl;
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
        document.getElementById('emptyPreviewState').classList.add('hidden');
    }
}

document.getElementById('documentInput').addEventListener('change', function (e) {
    handleLivePreview(e.target.files[0]);
});

async function fetchAccounting() {
    const url = currentStatementTypeFilter !== 'All Statement Types'
        ? `/accounting?statement_type=${encodeURIComponent(currentStatementTypeFilter)}`
        : `/accounting`;
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
        tableBody.innerHTML = `<tr><td colspan="6" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    accountingRows.forEach(item => {
        const classes = getStatusClasses(item.status);
        const safeId = item.id;
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr class="border-t ${isClickable ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50'}" ${isClickable ? `onclick="openPreview(${safeId})"` : ''}>
                <td class="p-3 text-gray-900">${item.date ?? ''}</td>
                <td class="p-3 text-gray-900">${item.user ?? ''}</td>
                <td class="p-3 text-gray-900">${item.client ?? ''}</td>
                <td class="p-3 text-gray-900">
                    {{-- Just plain text now, no button --}}
                    ${item.statement_type ?? ''}
                </td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? ''}
                    </span>
                </td>
                <td class="p-3">
                    ${item.document_path ? `<button type="button" onclick="event.stopPropagation(); openPreview(${safeId})" class="text-blue-600 hover:underline">View</button>` : `<span class="text-gray-400">No File</span>`}
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

    frame.classList.add('hidden'); frame.src = '';
    imageWrapper.classList.add('hidden'); imageWrapper.classList.remove('flex');
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

    document.getElementById('infoDate').textContent = item.date ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoClient').textContent = item.client ?? '';
    document.getElementById('infoStatementType').textContent = item.statement_type ?? '';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? 'N/A';
    document.getElementById('infoStatus').textContent = item.status ?? '';

    showOnlySection('previewSection');
}

async function renderTable() {
    accountingRows = await fetchAccounting();
    drawTableRows();
    showOnlySection('tableSection');
}

async function addAccountingEntry() {
    const client = document.getElementById('clientInput').value.trim();
    const tin = document.getElementById('tinInput').value.trim();
    const statementType = document.getElementById('statementTypeInput').value;
    const date = document.getElementById('dateInput').value;
    const file = document.getElementById('documentInput').files[0];

    if (!client || !statementType || !date || !file) {
        alert('Please fill in all required fields and upload a document.');
        return false;
    }

    const formData = new FormData();
    formData.append('statement_type', statementType);
    formData.append('client', client);
    formData.append('tin', tin);
    formData.append('date', date);
    formData.append('document', file);

    const res = await fetch('/accounting', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    });

    if (!res.ok) {
        const data = await res.json();
        alert(data.message || 'Error saving accounting entry.');
        return false;
    }

    currentStatementTypeFilter = 'All Statement Types';
    await renderTable();
    return true;
}

document.getElementById('statementTypeHeaderBtn').addEventListener('click', (event) => {
    event.stopPropagation();
    document.getElementById('statementTypeHeaderMenu').classList.toggle('hidden');
});

document.querySelectorAll('.header-statement-option').forEach(option => {
    option.addEventListener('click', (event) => {
        event.stopPropagation();
        currentStatementTypeFilter = option.dataset.filter;
        document.getElementById('statementTypeHeaderMenu').classList.add('hidden');
        renderTable();
    });
});

document.addEventListener('click', function (event) {
    if (!document.getElementById('statementTypeHeaderBtn').contains(event.target)) {
        document.getElementById('statementTypeHeaderMenu').classList.add('hidden');
    }
});

renderTable();
</script>
@endsection