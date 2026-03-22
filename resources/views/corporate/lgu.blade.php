@extends('layouts.app')

@section('content')
<div
    x-data="{ showSlideOver: false, hasExpiration: true }"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- SLIDE OVER FORM WITH LIVE PREVIEW --}}
        <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div class="absolute inset-0">
                <div @click="showSlideOver=false" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>

                <div class="absolute inset-y-0 right-0 flex max-w-full">
                    <div
                        class="w-screen max-w-[95vw] bg-white shadow-2xl flex h-full"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                    >
                        {{-- LEFT: LIVE PREVIEW --}}
                        <div class="flex-1 bg-gray-50 flex flex-col h-full border-r">
                            <div class="p-6 border-b bg-white">
                                <h3 class="font-semibold text-lg">Live Document Preview</h3>
                                <p class="text-sm text-gray-500">Preview of the file before saving</p>
                            </div>

                            <div class="flex-1 p-6 overflow-auto">
                                <div class="bg-white border rounded-xl h-full overflow-hidden">
                                    <div id="emptyPreviewState" class="h-full flex items-center justify-center text-gray-400 text-sm">
                                        Upload a PDF or image to preview it here.
                                    </div>

                                    <iframe id="livePdfPreview" class="w-full h-full hidden" frameborder="0"></iframe>

                                    <div id="liveImagePreviewWrapper" class="hidden h-full items-center justify-center bg-gray-100">
                                        <img id="liveImagePreview" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: FORM --}}
                        <div class="w-full max-w-md flex flex-col h-full">
                            <div class="p-6 border-b flex justify-between items-center">
                                <h2 class="font-bold text-lg">Add Permit Entry</h2>
                                <button @click="showSlideOver=false" class="text-gray-500 hover:text-gray-700">✕</button>
                            </div>

                            <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                                <div>
                                    <label class="block text-sm font-medium mb-1">TIN</label>
                                    <input id="tinInput" class="w-full border rounded-md p-2" placeholder="TIN">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Permit Type</label>
                                    <select id="permitTypeInput" class="w-full border rounded-md p-2">
                                        <option value="">Select Permit Type</option>
                                        <option value="Mayor's Permit">Mayor's Permit</option>
                                        <option value="Barangay Business Permit">Barangay Business Permit</option>
                                        <option value="Fire Permit">Fire Permit</option>
                                        <option value="Sanitary Permit">Sanitary Permit</option>
                                        <option value="OBO">OBO</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Document Type</label>
                                    <select id="documentTypeInput" class="w-full border rounded-md p-2">
                                        <option value="">Select Document Type</option>
                                        <option value="PDF">PDF</option>
                                        <option value="Image">Image</option>
                                        <option value="Scanned Copy">Scanned Copy</option>
                                        <option value="Signed Copy">Signed Copy</option>
                                        <option value="Original Copy">Original Copy</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Date of Registration</label>
                                    <input id="dateOfRegistrationInput" type="date" class="w-full border rounded-md p-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Approved Date of Registration</label>
                                    <input id="approvedDateOfRegistrationInput" type="date" class="w-full border rounded-md p-2">
                                </div>

                                <div class="border rounded-md p-3 bg-gray-50">
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input
                                            type="checkbox"
                                            x-model="hasExpiration"
                                            id="hasExpirationInput"
                                            class="rounded border-gray-300"
                                            @change="if (!hasExpiration) document.getElementById('expirationDateOfRegistrationInput').value = ''"
                                        >
                                        This permit has an expiration date
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Expiration Date</label>
                                    <input
                                        id="expirationDateOfRegistrationInput"
                                        type="date"
                                        class="w-full border rounded-md p-2"
                                        :disabled="!hasExpiration"
                                        :class="!hasExpiration ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                                    >
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

                            <div class="p-6 border-t flex gap-3">
                                <button @click="showSlideOver=false" class="flex-1 border py-2 rounded">Cancel</button>
                                <button
                                    @click="addPermit().then(success => { if (success) { showSlideOver = false; resetFormDefaults(); } })"
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

        {{-- VIEW SAVED DOCUMENT OVERLAY --}}
        <div id="previewOverlay" class="hidden fixed inset-0 z-[60]">
            <div class="absolute inset-0 bg-black/30" onclick="closePreview()"></div>
            <div class="absolute inset-0 bg-[#f5f7fa] flex gap-5 p-4 overflow-hidden">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full" frameborder="0"></iframe>
                </div>

                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Document Preview</h2>
                        <button type="button" onclick="closePreview()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
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
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span id="infoStatus" class="text-right font-medium text-gray-900"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
            <div class="relative">
                <button id="permitDropdownBtn" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-md font-medium hover:bg-gray-200">
                    <span id="selectedPermitFilter">All Documents</span> ▾
                </button>
                <div id="permitMenu" class="hidden absolute left-0 mt-2 w-56 bg-white border shadow-xl rounded-md z-50 py-1">
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">All Documents</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Mayor's Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Barangay Business Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Fire Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Sanitary Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">OBO</div>
                </div>
            </div>

            <button @click="showSlideOver = true; $nextTick(() => resetFormDefaults())" class="bg-blue-600 text-white px-6 py-2 rounded text-sm">
                + Add
            </button>
        </div>

        {{-- TABLE --}}
        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-40 p-3 text-left">Permit No.</th>
                            <th class="w-40 p-3 text-left">Date of Registration</th>
                            <th class="w-48 p-3 text-left">Approved Date</th>
                            <th class="w-44 p-3 text-left">Expiration Date</th>
                            <th class="w-32 p-3 text-left">Uploader</th>
                            <th class="w-32 p-3 text-left">TIN</th>
                            <th id="permitTypeHeader" class="w-40 p-3 text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2"><span>Permit Type</span><span id="permitTypeSortIndicator">↕</span></div>
                            </th>
                            <th class="w-40 p-3 text-left">Document Type</th>
                            <th class="w-32 p-3 text-left">Status</th>
                            <th class="w-40 p-3 text-left">Document</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let currentPermitFilter = 'All Documents';
let permitRows = [];
let permitTypeSortDirection = 'asc';
let livePreviewObjectUrl = null;

function resetFormDefaults() {
    document.getElementById('tinInput').value = '';
    document.getElementById('permitTypeInput').value = '';
    document.getElementById('documentTypeInput').value = '';
    document.getElementById('dateOfRegistrationInput').value = '';
    document.getElementById('approvedDateOfRegistrationInput').value = '';
    document.getElementById('hasExpirationInput').checked = true;
    document.getElementById('documentInput').value = '';
    document.getElementById('selectedFileName').textContent = 'No file selected';

    const expirationInput = document.getElementById('expirationDateOfRegistrationInput');
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

async function fetchPermits(filterValue) {
    const url = `/permits?filter=${encodeURIComponent(filterValue)}`;
    const res = await fetch(url);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Active') return { textClass: 'text-green-600', dotClass: 'bg-green-500' };
    if (status === 'Expired') return { textClass: 'text-red-600', dotClass: 'bg-red-500' };
    return { textClass: 'text-gray-500', dotClass: 'bg-gray-400' };
}

function drawTableRows() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    if (!permitRows.length) {
        tableBody.innerHTML = `<tr><td colspan="10" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    permitRows.forEach((item, index) => {
        const classes = getStatusClasses(item.status);
        tableBody.innerHTML += `
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3">${item.permit_number ?? ''}</td>
                <td class="p-3">${item.date_of_registration ?? ''}</td>
                <td class="p-3">${item.approved_date_of_registration ?? ''}</td>
                <td class="p-3">${item.expiration_date_of_registration ?? 'No Expiration'}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">${item.permit_type ?? ''}</td>
                <td class="p-3">${item.document_type ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? 'No Status'}
                    </span>
                </td>
                <td class="p-3">
                    ${item.document_path
                        ? `<button type="button" onclick="openPreview(${index})" class="text-blue-600 hover:underline">View</button>`
                        : `<span class="text-gray-400">No File</span>`}
                </td>
            </tr>
        `;
    });
}

function openPreview(index) {
    const item = permitRows[index];
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
    document.getElementById('infoStatus').textContent = item.status ?? '';

    document.getElementById('previewOverlay').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    document.getElementById('previewOverlay').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

async function renderTable(filterValue) {
    currentPermitFilter = filterValue;
    const permitData = await fetchPermits(filterValue);
    permitRows = permitData || [];
    drawTableRows();
}

async function addPermit() {
    const permitType = document.getElementById('permitTypeInput').value;
    const documentType = document.getElementById('documentTypeInput').value;
    const fileInput = document.getElementById('documentInput');

    if (!permitType) {
        alert('Please select a Permit Type.');
        return false;
    }

    if (!documentType) {
        alert('Please select a Document Type.');
        return false;
    }

    if (fileInput.files.length === 0) {
        alert('Please upload a document.');
        return false;
    }

    const formData = new FormData();
    formData.append('permit_type', permitType);
    formData.append('document_type', documentType);
    formData.append('tin', document.getElementById('tinInput').value);
    formData.append('date_of_registration', document.getElementById('dateOfRegistrationInput').value);
    formData.append('approved_date_of_registration', document.getElementById('approvedDateOfRegistrationInput').value);

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

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || 'Error saving permit.');
        return false;
    }

    await renderTable(currentPermitFilter);
    return true;
}

function sortPermitRows() {
    permitRows.sort((a, b) => {
        const valueA = (a.permit_type || '').toLowerCase();
        const valueB = (b.permit_type || '').toLowerCase();

        if (permitTypeSortDirection === 'asc') {
            return valueA.localeCompare(valueB);
        }
        return valueB.localeCompare(valueA);
    });

    document.getElementById('permitTypeSortIndicator').textContent = permitTypeSortDirection === 'asc' ? '↑' : '↓';
    drawTableRows();
}

document.getElementById('permitTypeHeader').addEventListener('click', () => {
    permitTypeSortDirection = permitTypeSortDirection === 'asc' ? 'desc' : 'asc';
    sortPermitRows();
});

// Initial Load
renderTable(currentPermitFilter);

// Dropdown Handlers
document.getElementById('permitDropdownBtn').addEventListener('click', e => {
    e.stopPropagation();
    document.getElementById('permitMenu').classList.toggle('hidden');
});

document.getElementById('permitMenu').addEventListener('click', e => {
    if (e.target.tagName === 'DIV') {
        const selected = e.target.innerText;
        document.getElementById('selectedPermitFilter').innerText = selected;
        renderTable(selected);
        document.getElementById('permitMenu').classList.add('hidden');
    }
});

document.addEventListener('click', () => document.getElementById('permitMenu').classList.add('hidden'));
</script>
@endsection