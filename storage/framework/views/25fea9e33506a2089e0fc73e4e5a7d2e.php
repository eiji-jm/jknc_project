<?php $__env->startSection('content'); ?>
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0 gap-4">
            <div class="flex items-center flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Accounting</h1>
            </div>

            <button onclick="openAddSection()" class="bg-blue-600 text-white px-6 py-2 rounded text-sm shrink-0">
                + Add
            </button>
        </div>

        <div class="px-4 pt-4 bg-white border-b border-gray-100">
            <div class="flex gap-8 text-[15px] text-gray-700 overflow-x-auto">
                <button onclick="applyWorkflowFilter('uploaded')" id="tab-uploaded" class="pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900">
                    Uploaded
                </button>
                <button onclick="applyWorkflowFilter('submitted')" id="tab-submitted" class="pb-3 whitespace-nowrap">
                    Submitted
                </button>
                <button onclick="applyWorkflowFilter('accepted')" id="tab-accepted" class="pb-3 whitespace-nowrap">
                    Accepted
                </button>
                <button onclick="applyWorkflowFilter('reverted')" id="tab-reverted" class="pb-3 whitespace-nowrap">
                    Reverted
                </button>
                <button onclick="applyWorkflowFilter('archived')" id="tab-archived" class="pb-3 whitespace-nowrap">
                    Archived
                </button>
            </div>

            <div id="statusMessage" class="mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md">
                These records are uploaded and ready for submission.
            </div>
        </div>

        <div id="tableSection" class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto bg-white">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-32 p-3 text-left">Date</th>
                            <th class="w-36 p-3 text-left">Uploader</th>
                            <th class="w-48 p-3 text-left">Client</th>
                            <th class="w-44 p-3 text-left">Statement Type</th>
                            <th class="w-36 p-3 text-left">Workflow Status</th>
                            <th class="w-36 p-3 text-left">Approval Status</th>
                            <th class="w-40 p-3 text-left">Document</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

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

                <div class="w-[360px] shrink-0 flex flex-col gap-4">
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
                                <span class="text-gray-500">Workflow</span>
                                <span id="infoWorkflow" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Approval</span>
                                <span id="infoApproval" class="text-right font-medium text-gray-900"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Review Note</span>
                                <span id="infoReviewNote" class="text-right font-medium text-gray-900 break-words"></span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Document Name</span>
                                <span id="infoDocumentName" class="text-right font-medium text-gray-900 break-all"></span>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-2" id="previewActions"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="addSection" class="hidden fixed inset-0 z-50" aria-hidden="true">
            <div id="addBackdrop" class="absolute inset-0 bg-black/40" onclick="closeAddSection()"></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div id="addPanel" class="w-screen max-w-[100vw] bg-white shadow-2xl flex h-full transform translate-x-full transition-transform duration-300 ease-in-out">
                    <div class="flex-1 min-w-0 p-4 bg-gray-50 border-r border-gray-200">
                        <div class="h-full bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div id="emptyPreviewStateLive" class="h-full flex items-center justify-center text-gray-400 text-sm">
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
                            <button onclick="addAccountingEntry()" class="flex-1 bg-blue-600 text-white rounded py-2">
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
let currentWorkflowFilter = 'uploaded';
let accountingRows = [];
let livePreviewObjectUrl = null;
let currentPreviewRecord = null;

const urlParams = new URLSearchParams(window.location.search);
const autoOpenRecordId = urlParams.get('record');
const autoOpenTab = (urlParams.get('tab') || '').toLowerCase();

if (['uploaded', 'submitted', 'accepted', 'reverted', 'archived'].includes(autoOpenTab)) {
    currentWorkflowFilter = autoOpenTab;
}

function workflowLabel(value) {
    if (!value) return 'Uploaded';
    return value;
}

function approvalLabel(value) {
    if (!value) return 'Pending';
    return value;
}

function workflowBadgeClass(value) {
    const v = (value || '').toLowerCase();
    if (v === 'uploaded') return 'text-orange-700';
    if (v === 'submitted') return 'text-blue-700';
    if (v === 'accepted') return 'text-green-700';
    if (v === 'reverted') return 'text-yellow-700';
    if (v === 'archived') return 'text-gray-700';
    return 'text-gray-700';
}

function approvalBadgeClass(value) {
    const v = (value || '').toLowerCase();
    if (v === 'approved') return 'text-green-700';
    if (v === 'pending') return 'text-yellow-700';
    if (v === 'needs revision') return 'text-yellow-700';
    if (v === 'rejected') return 'text-red-700';
    return 'text-gray-700';
}

function updateStatusMessage() {
    const messageBox = document.getElementById('statusMessage');

    if (currentWorkflowFilter === 'uploaded') {
        messageBox.className = 'mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are uploaded and ready for submission.';
    } else if (currentWorkflowFilter === 'submitted') {
        messageBox.className = 'mt-3 mb-4 border border-yellow-200 bg-yellow-50 text-yellow-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are already submitted and waiting for admin approval.';
    } else if (currentWorkflowFilter === 'accepted') {
        messageBox.className = 'mt-3 mb-4 border border-green-200 bg-green-50 text-green-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were already accepted.';
    } else if (currentWorkflowFilter === 'reverted') {
        messageBox.className = 'mt-3 mb-4 border border-red-200 bg-red-50 text-red-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were reverted and can be corrected then resubmitted.';
    } else if (currentWorkflowFilter === 'archived') {
        messageBox.className = 'mt-3 mb-4 border border-gray-200 bg-gray-50 text-gray-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are archived.';
    }
}

function setActiveTab() {
    ['uploaded', 'submitted', 'accepted', 'reverted', 'archived'].forEach(tab => {
        const el = document.getElementById(`tab-${tab}`);
        if (!el) return;

        if (tab === currentWorkflowFilter) {
            el.className = 'pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900';
        } else {
            el.className = 'pb-3 whitespace-nowrap text-gray-700';
        }
    });
}

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
    currentPreviewRecord = null;
    document.getElementById('previewFrame').src = '';
    document.getElementById('previewFrame').classList.add('hidden');
    document.getElementById('previewImageWrapper').classList.add('hidden');
    document.getElementById('previewImageWrapper').classList.remove('flex');
    document.getElementById('previewImage').src = '';
    document.getElementById('previewEmptyState').classList.remove('hidden');
    document.getElementById('previewActions').innerHTML = '';
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
    if (livePreviewObjectUrl) {
        URL.revokeObjectURL(livePreviewObjectUrl);
        livePreviewObjectUrl = null;
    }

    document.getElementById('emptyPreviewStateLive').classList.remove('hidden');
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
        document.getElementById('emptyPreviewStateLive').classList.add('hidden');
        return;
    }

    if (file.type.startsWith('image/')) {
        const image = document.getElementById('liveImagePreview');
        const wrapper = document.getElementById('liveImagePreviewWrapper');
        image.src = livePreviewObjectUrl;
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
        document.getElementById('emptyPreviewStateLive').classList.add('hidden');
    }
}

document.getElementById('documentInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    handleLivePreview(file);
});

async function fetchAccounting() {
   const url = `/corporate/accounting/data?workflow_status=${encodeURIComponent(currentWorkflowFilter)}`;
    const res = await fetch(url, {
        headers: {
            'Accept': 'application/json'
        }
    });
    return await res.json();
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
                <td colspan="7" class="p-10 text-center text-gray-400 italic">No data found</td>
            </tr>
        `;
        return;
    }

    accountingRows.forEach(item => {
        const isClickable = !!item.document_path;

        tableBody.innerHTML += `
            <tr class="border-t ${isClickable ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50'}"
                ${isClickable ? `onclick="openPreview(${item.id})"` : ''}>
                <td class="p-3">${item.date ?? ''}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.client ?? ''}</td>
                <td class="p-3">${item.statement_type ?? ''}</td>
                <td class="p-3 ${workflowBadgeClass(item.workflow_status)} font-medium">${workflowLabel(item.workflow_status)}</td>
                <td class="p-3 ${approvalBadgeClass(item.approval_status)} font-medium">${approvalLabel(item.approval_status)}</td>
                <td class="p-3">
                    ${item.document_path
                        ? `<button type="button" onclick="event.stopPropagation(); openPreview(${item.id})" class="text-blue-600 hover:underline">View</button>`
                        : `<span class="text-gray-400">No File</span>`
                    }
                </td>
            </tr>
        `;
    });
}

function renderPreviewActions(item) {
    const actions = document.getElementById('previewActions');
    actions.innerHTML = '';

    if (item.can_submit) {
        actions.innerHTML += `
            <button type="button" onclick="submitAccounting(${item.id})" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">
                Submit for Approval
            </button>
        `;
    }
}

function openPreview(id) {
    const item = accountingRows.find(row => row.id == id);
    if (!item) return;

    currentPreviewRecord = item;

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

    document.getElementById('infoDate').textContent = item.date ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoClient').textContent = item.client ?? '';
    document.getElementById('infoStatementType').textContent = item.statement_type ?? '';
    document.getElementById('infoWorkflow').textContent = item.workflow_status ?? '';
    document.getElementById('infoApproval').textContent = item.approval_status ?? '';
    document.getElementById('infoReviewNote').textContent = item.review_note ?? '—';
    document.getElementById('infoDocumentName').textContent = item.document_name ?? 'N/A';

    renderPreviewActions(item);
    showOnlySection('previewSection');
}

async function renderTable() {
    accountingRows = await fetchAccounting();
    drawTableRows();
    updateStatusMessage();
    setActiveTab();
    showOnlySection('tableSection');

    if (autoOpenRecordId) {
        const target = accountingRows.find(row => String(row.id) === String(autoOpenRecordId));
        if (target) {
            openPreview(target.id);
        }
    }
}

function applyWorkflowFilter(filterValue) {
    currentWorkflowFilter = filterValue;
    renderTable();
}

async function addAccountingEntry() {
    const client = document.getElementById('clientInput').value.trim();
    const tin = document.getElementById('tinInput').value.trim();
    const statementType = document.getElementById('statementTypeInput').value;
    const date = document.getElementById('dateInput').value;
    const fileInput = document.getElementById('documentInput');
    const file = fileInput.files[0];

    if (!client || !statementType || !date) {
        alert('Please fill in Client, Statement Type, and Date.');
        return;
    }

    if (!file) {
        alert('Please upload a document.');
        return;
    }

    const formData = new FormData();
    formData.append('statement_type', statementType);
    formData.append('client', client);
    formData.append('tin', tin);
    formData.append('date', date);
    formData.append('document', file);

    try {
        const res = await fetch('/corporate/accounting', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: formData
        });

        let data = {};
        const contentType = res.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            data = await res.json();
        } else {
            const text = await res.text();
            console.error('Non-JSON response:', text);
            alert('Server returned an unexpected response. Check Laravel error page or browser console.');
            return;
        }

        if (!res.ok) {
            console.error(data);
            if (data.errors) {
                const firstError = Object.values(data.errors)[0]?.[0];
                alert(firstError || data.message || 'Error saving accounting entry.');
            } else {
                alert(data.message || 'Error saving accounting entry.');
            }
            return;
        }

        closeAddSection();
        currentWorkflowFilter = 'uploaded';
        await renderTable();
        alert(data.message || 'Accounting entry saved successfully.');
    } catch (error) {
        console.error('Save failed:', error);
        alert('Something went wrong while saving. Open browser console for details.');
    }
}

async function submitAccounting(id) {
    const res = await fetch(`/corporate/accounting/${id}/submit`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || 'Unable to submit record.');
        return;
    }

    alert(data.message || 'Submitted successfully.');
    currentWorkflowFilter = 'submitted';
    closePreview();
    await renderTable();
}

renderTable();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\accounting.blade.php ENDPATH**/ ?>