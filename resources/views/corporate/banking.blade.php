@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Banking</h1>
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
                            <th class="w-36 p-3 text-left">Date Uploaded</th>
                            <th class="w-36 p-3 text-left">Uploaded By</th>
                            <th class="w-44 p-3 text-left">Client</th>
                            <th class="w-36 p-3 text-left">TIN</th>
                            <th class="w-40 p-3 text-left">Banks</th>
                            <th class="w-40 p-3 text-left">Bank Docs</th>
                            <th class="w-32 p-3 text-left">Status</th>
                            <th class="w-32 p-3 text-left">Document</th>
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
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Banking Information</h3>
                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Date Uploaded</span><span id="infoDateUploaded" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Uploaded By</span><span id="infoUser" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Client</span><span id="infoClient" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">TIN</span><span id="infoTin" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Bank</span><span id="infoBank" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Bank Doc</span><span id="infoBankDoc" class="text-right font-medium text-gray-900"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Document Name</span><span id="infoDocumentName" class="text-right font-medium text-gray-900 break-all"></span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span id="infoStatus" class="text-right font-medium text-gray-900"></span></div>
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
                            <div id="emptyPreviewStateAdd" class="h-full flex items-center justify-center text-gray-400 text-sm">
                                Upload a PDF or image to preview it here.
                            </div>

                            <iframe id="livePdfPreview" class="w-full h-full hidden bg-white" frameborder="0"></iframe>

                            <div id="liveImagePreviewWrapper" class="hidden h-full items-center justify-center bg-white">
                                <img id="liveImagePreview" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: FORM --}}
                    <div class="w-full max-w-[320px] bg-white flex flex-col h-full">
                        <div class="p-6 border-b flex items-center justify-between shrink-0">
                            <h2 class="font-bold text-lg text-gray-900">Add Banking Entry</h2>
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
                                <label class="block text-sm font-medium mb-1">Bank</label>
                                <input id="bankInput" class="w-full border rounded-md p-2" placeholder="Bank">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Bank Doc</label>
                                <input id="bankDocInput" class="w-full border rounded-md p-2" placeholder="Bank Document Type">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Date Uploaded</label>
                                <input id="dateUploadedInput" type="date" class="w-full border rounded-md p-2">
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
                            <button
                                onclick="addBankingEntry().then(success => { if (success) { closeAddSection(); resetFormDefaults(); } })"
                                class="flex-1 bg-blue-600 text-white rounded py-2"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
let bankingRows = [];
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
    document.getElementById('bankInput').value = '';
    document.getElementById('bankDocInput').value = '';
    document.getElementById('dateUploadedInput').value = '';
    document.getElementById('documentInput').value = '';
    document.getElementById('selectedFileName').textContent = 'No file selected';
    clearLivePreview();
}

function clearLivePreview() {
    if (livePreviewObjectUrl) {
        URL.revokeObjectURL(livePreviewObjectUrl);
        livePreviewObjectUrl = null;
    }

    document.getElementById('emptyPreviewStateAdd').classList.remove('hidden');
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
        document.getElementById('livePdfPreview').src = livePreviewObjectUrl;
        document.getElementById('livePdfPreview').classList.remove('hidden');
        document.getElementById('emptyPreviewStateAdd').classList.add('hidden');
        return;
    }

    if (file.type.startsWith('image/')) {
        document.getElementById('liveImagePreview').src = livePreviewObjectUrl;
        document.getElementById('liveImagePreviewWrapper').classList.remove('hidden');
        document.getElementById('liveImagePreviewWrapper').classList.add('flex');
        document.getElementById('emptyPreviewStateAdd').classList.add('hidden');
    }
}

document.getElementById('documentInput').addEventListener('change', function (e) {
    handleLivePreview(e.target.files[0]);
});

async function fetchBanking() {
    const res = await fetch('/banking/data');
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

    if (!bankingRows.length) {
        tableBody.innerHTML = `<tr><td colspan="8" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    bankingRows.forEach(item => {
        const classes = getStatusClasses(item.status);
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr class="border-t ${isClickable ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50'}"
                ${isClickable ? `onclick="openPreview(${item.id})"` : ''}>
                <td class="p-3">${item.date_uploaded ?? ''}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.client ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">${item.bank ?? ''}</td>
                <td class="p-3">${item.bank_doc ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? ''}
                    </span>
                </td>
                <td class="p-3">
                    ${item.document_path
                        ? `<button type="button" onclick="event.stopPropagation(); openPreview(${item.id})" class="text-blue-600 hover:underline">View</button>`
                        : `<span class="text-gray-400">No File</span>`}
                </td>
            </tr>
        `;
    });
}

function openPreview(id) {
    const item = bankingRows.find(row => row.id === id);
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

    document.getElementById('infoDateUploaded').textContent = item.date_uploaded ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoClient').textContent = item.client ?? '';
    document.getElementById('infoTin').textContent = item.tin ?? '';
    document.getElementById('infoBank').textContent = item.bank ?? '';
    document.getElementById('infoBankDoc').textContent = item.bank_doc ?? '';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? 'N/A';
    document.getElementById('infoStatus').textContent = item.status ?? '';

    showOnlySection('previewSection');
}

async function renderTable() {
    bankingRows = await fetchBanking();
    drawTableRows();
}

async function addBankingEntry() {
    const client = document.getElementById('clientInput').value.trim();
    const tin = document.getElementById('tinInput').value.trim();
    const bank = document.getElementById('bankInput').value.trim();
    const bankDoc = document.getElementById('bankDocInput').value.trim();
    const dateUploaded = document.getElementById('dateUploadedInput').value;
    const file = document.getElementById('documentInput').files[0];

    if (!client || !bank || !bankDoc || !dateUploaded) {
        alert('Please fill in all required fields.');
        return false;
    }

    if (!file) {
        alert('Please upload a document.');
        return false;
    }

    const formData = new FormData();
    formData.append('client', client);
    formData.append('tin', tin);
    formData.append('bank', bank);
    formData.append('bank_doc', bankDoc);
    formData.append('date_uploaded', dateUploaded);
    formData.append('document', file);

    const res = await fetch('/banking/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || 'Error saving banking entry.');
        return false;
    }

    await renderTable();
    return true;
}

renderTable();
</script>
@endsection