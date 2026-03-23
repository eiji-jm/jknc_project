@extends('layouts.app')

@section('content')
<div
    x-data="{ hasExpiration: true }"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex-1 min-w-0 overflow-x-auto">
                <div id="permitTabs" class="inline-flex min-w-max border border-gray-300 rounded-md overflow-hidden bg-white">
                    <button type="button" class="permit-tab active px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="All Documents">
                        All Documents
                    </button>
                    <button type="button" class="permit-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Mayor's Permit">
                        Mayor's Permit
                    </button>
                    <button type="button" class="permit-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Barangay Business Permit">
                        Barangay Business Permit
                    </button>
                    <button type="button" class="permit-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Fire Permit">
                        Fire Permit
                    </button>
                    <button type="button" class="permit-tab px-5 py-2 text-sm border-r border-gray-300 bg-white hover:bg-gray-50" data-filter="Sanitary Permit">
                        Sanitary Permit
                    </button>
                    <button type="button" class="permit-tab px-5 py-2 text-sm bg-white hover:bg-gray-50" data-filter="OBO">
                        OBO Permit
                    </button>
                </div>
            </div>

            <button onclick="openAddSection()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        {{-- STATUS FILTERS --}}
        <div class="px-4 pt-4 shrink-0">
            <div class="flex items-center gap-2">
                <button type="button"
                    class="status-tab active px-4 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50"
                    data-status="All">
                    All
                </button>
                <button type="button"
                    class="status-tab px-4 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50"
                    data-status="Active">
                    Active
                </button>
                <button type="button"
                    class="status-tab px-4 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50"
                    data-status="Pending">
                    Pending
                </button>
                <button type="button"
                    class="status-tab px-4 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50"
                    data-status="Archive">
                    Archive
                </button>
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div id="tableSection" class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
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
                                <div class="flex items-center gap-2">
                                    <span>Permit Type</span>
                                    <span id="permitTypeSortIndicator">↕</span>
                                </div>
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

        {{-- PREVIEW VIEW --}}
        <div id="previewSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full bg-white" frameborder="0"></iframe>
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

        {{-- ADD VIEW --}}
        <div id="addSection" class="hidden p-4 flex-grow overflow-hidden">
            <div class="h-full flex gap-4">
                {{-- LEFT: LIVE PREVIEW --}}
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="h-full bg-white">
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
                <div class="w-[360px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Add Permit Entry</h2>
                        <button type="button" onclick="closeAddSection()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 flex-1 overflow-y-auto">
                        <div class="space-y-4">
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
                                        id="hasExpirationInput"
                                        class="rounded border-gray-300"
                                        checked
                                        onchange="toggleExpirationField()"
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
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex gap-3">
                        <button onclick="closeAddSection()" class="flex-1 border py-2 rounded">Cancel</button>
                        <button
                            onclick="addPermit().then(success => { if (success) { closeAddSection(); resetFormDefaults(); } })"
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
    .permit-tab.active {
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 600;
    }

    .status-tab.active {
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 600;
        border-color: #93c5fd;
    }
</style>

<script>
let currentPermitFilter = 'All Documents';
let currentStatusFilter = 'All';
let permitRows = [];
let permitTypeSortDirection = 'asc';
let livePreviewObjectUrl = null;

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
    resetFormDefaults();
    showOnlySection('tableSection');
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
    document.getElementById('approvedDateOfRegistrationInput').value = '';
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

async function fetchPermits(filterValue) {
    const url = `/permits?filter=${encodeURIComponent(filterValue)}`;
    const res = await fetch(url);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Active') return { textClass: 'text-green-600', dotClass: 'bg-green-500' };
    if (status === 'Expired') return { textClass: 'text-red-600', dotClass: 'bg-red-500' };
    if (status === 'Pending') return { textClass: 'text-yellow-600', dotClass: 'bg-yellow-500' };
    if (status === 'Archive') return { textClass: 'text-gray-600', dotClass: 'bg-gray-500' };
    return { textClass: 'text-gray-500', dotClass: 'bg-gray-400' };
}

function drawTableRows() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    let filteredRows = [...permitRows];

    if (currentStatusFilter !== 'All') {
        filteredRows = filteredRows.filter(item => {
            const status = (item.status || '').toLowerCase();

            if (currentStatusFilter === 'Archive') {
                return status === 'expired';
            }

            return status === currentStatusFilter.toLowerCase();
        });
    }

    if (!filteredRows.length) {
        tableBody.innerHTML = `<tr><td colspan="10" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    filteredRows.forEach(item => {
        const classes = getStatusClasses(item.status);
        const safePermitNumber = JSON.stringify(item.permit_number ?? '');
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr
                class="border-t ${isClickable ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50'}"
                ${isClickable ? `onclick='openPreviewByPermitNumber(${safePermitNumber})'` : ''}
            >
                <td class="p-3">
                    ${isClickable
                        ? `<span class="text-blue-600 font-medium hover:underline">${item.permit_number ?? ''}</span>`
                        : `${item.permit_number ?? ''}`
                    }
                </td>
                <td class="p-3">${item.date_of_registration ?? ''}</td>
                <td class="p-3">${item.approved_date_of_registration ?? ''}</td>
                <td class="p-3">${item.expiration_date_of_registration ?? 'No Expiration'}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">
                    ${isClickable
                        ? `<span class="text-blue-600 hover:underline">${item.permit_type ?? ''}</span>`
                        : `${item.permit_type ?? ''}`
                    }
                </td>
                <td class="p-3">${item.document_type ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? 'No Status'}
                    </span>
                </td>
                <td class="p-3">
                    ${item.document_path
                        ? `<button
                                type="button"
                                onclick='event.stopPropagation(); openPreviewByPermitNumber(${safePermitNumber})'
                                class="text-blue-600 hover:underline"
                           >View</button>`
                        : `<span class="text-gray-400">No File</span>`}
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
    document.getElementById('infoStatus').textContent = item.status ?? '';

    showOnlySection('previewSection');
}

async function renderTable(filterValue) {
    currentPermitFilter = filterValue;
    const permitData = await fetchPermits(filterValue);
    permitRows = permitData || [];
    drawTableRows();
    setActivePermitTab(filterValue);
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

function setActivePermitTab(filterValue) {
    document.querySelectorAll('.permit-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.filter === filterValue) {
            tab.classList.add('active');
        }
    });
}

function setActiveStatusTab(statusValue) {
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.status === statusValue) {
            tab.classList.add('active');
        }
    });
}

document.getElementById('permitTypeHeader').addEventListener('click', () => {
    permitTypeSortDirection = permitTypeSortDirection === 'asc' ? 'desc' : 'asc';
    sortPermitRows();
});

document.querySelectorAll('.permit-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const filterValue = tab.dataset.filter;
        renderTable(filterValue);
        showOnlySection('tableSection');
    });
});

document.querySelectorAll('.status-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        currentStatusFilter = tab.dataset.status;
        setActiveStatusTab(currentStatusFilter);
        drawTableRows();
        showOnlySection('tableSection');
    });
});

renderTable(currentPermitFilter);
setActiveStatusTab(currentStatusFilter);
</script>
@endsection