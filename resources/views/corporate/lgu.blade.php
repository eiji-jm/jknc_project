@extends('layouts.app')

@section('content')
<div
    x-data="{ showSlideOver: false, hasExpiration: true }"
    class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col"
>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- SLIDE OVER FORM --}}
        <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div class="absolute inset-0">
                <div @click="showSlideOver=false" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>

                <div class="absolute inset-y-0 right-0 flex max-w-full">
                    <div
                        class="w-screen max-w-md bg-white shadow-2xl flex flex-col h-full"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                    >
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
                                <p class="text-xs text-gray-500 mt-1">
                                    If unchecked, expiration date will be disabled and status will automatically be Active.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Expiration Date of Registration</label>
                                <input
                                    id="expirationDateOfRegistrationInput"
                                    type="date"
                                    class="w-full border rounded-md p-2"
                                    :disabled="!hasExpiration"
                                    :class="!hasExpiration ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                                >
                            </div>
                        </div>

                        <div class="p-6 border-t flex gap-3">
                            <button @click="showSlideOver=false" class="flex-1 border py-2 rounded">Cancel</button>
                            <button
                                @click="addPermit().then(success => { if (success) { showSlideOver = false; hasExpiration = true; resetFormDefaults(); } })"
                                class="flex-1 bg-blue-600 text-white py-2 rounded"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OVERLAY PREVIEW --}}
        <div id="previewOverlay" class="hidden fixed inset-0 z-[60]">
            <div class="absolute inset-0 bg-black/30" onclick="closePreview()"></div>

            <div class="absolute inset-0 bg-[#f5f7fa] flex gap-5 p-4 overflow-hidden">
                <div class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <iframe id="previewFrame" class="w-full h-full" frameborder="0"></iframe>
                </div>

                <div class="w-[320px] shrink-0 flex flex-col gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-gray-900">Permit Preview</h2>
                        <button
                            type="button"
                            onclick="closePreview()"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Close
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl px-5 py-6">
                        <h3 class="text-[18px] font-semibold text-gray-900 mb-6">Permit Information</h3>

                        <div class="space-y-5 text-[14px]">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Permit No.</span>
                                <span id="infoPermitNumber" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Permit Type</span>
                                <span id="infoPermitType" class="text-right font-medium text-gray-900"></span>
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
                                <span class="text-gray-500">Date Registered</span>
                                <span id="infoDateReg" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Approved Date</span>
                                <span id="infoApprovedDate" class="text-right font-medium text-gray-900"></span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Expiration Date</span>
                                <span id="infoExpirationDate" class="text-right font-medium text-gray-900"></span>
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
                            <th class="w-40 p-3 text-left">Permit No.</th>
                            <th class="w-40 p-3 text-left">Date of Registration</th>
                            <th class="w-48 p-3 text-left">Approved Date</th>
                            <th class="w-44 p-3 text-left">Expiration Date</th>
                            <th class="w-32 p-3 text-left">Uploader</th>
                            <th class="w-32 p-3 text-left">TIN</th>
                            <th
                                id="permitTypeHeader"
                                class="w-40 p-3 text-left cursor-pointer select-none hover:bg-gray-100"
                                title="Sort by Permit Type"
                            >
                                <div class="flex items-center gap-2">
                                    <span>Permit Type</span>
                                    <span id="permitTypeSortIndicator" class="text-xs text-gray-500">↕</span>
                                </div>
                            </th>
                            <th class="w-32 p-3 text-left">Status</th>
                            <th class="w-32 p-3 text-left">Document</th>
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

const previewRoutes = {
    "Mayor's Permit": "mayors-permit",
    "Barangay Business Permit": "barangay-business-permit",
    "Fire Permit": "fire-permit",
    "Sanitary Permit": "sanitary-permit",
    "OBO": "obo-permit"
};

function resetFormDefaults() {
    document.getElementById('tinInput').value = '';
    document.getElementById('permitTypeInput').value = '';
    document.getElementById('dateOfRegistrationInput').value = '';
    document.getElementById('approvedDateOfRegistrationInput').value = '';
    document.getElementById('hasExpirationInput').checked = true;

    const expirationInput = document.getElementById('expirationDateOfRegistrationInput');
    expirationInput.value = '';
    expirationInput.disabled = false;
    expirationInput.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
}

async function fetchPermits(filterValue) {
    const url = filterValue === 'All Documents'
        ? `/permits/all`
        : `/permits/${encodeURIComponent(filterValue)}`;

    const res = await fetch(url);
    return await res.json();
}

function getStatusClasses(status) {
    if (status === 'Active') {
        return {
            textClass: 'text-green-600',
            dotClass: 'bg-green-500'
        };
    }

    if (status === 'Expired') {
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

function updatePermitTypeSortIndicator() {
    const indicator = document.getElementById('permitTypeSortIndicator');

    if (permitTypeSortDirection === 'asc') {
        indicator.textContent = '↑';
    } else {
        indicator.textContent = '↓';
    }
}

function sortPermitRowsByType() {
    permitRows.sort((a, b) => {
        const typeA = (a.permit_type || '').toLowerCase();
        const typeB = (b.permit_type || '').toLowerCase();

        if (permitTypeSortDirection === 'asc') {
            return typeA.localeCompare(typeB);
        }

        return typeB.localeCompare(typeA);
    });

    updatePermitTypeSortIndicator();
    drawTableRows();
}

function togglePermitTypeSort() {
    permitTypeSortDirection = permitTypeSortDirection === 'asc' ? 'desc' : 'asc';
    sortPermitRowsByType();
}

function openPreview(index) {
    const item = permitRows[index];
    if (!item) return;

    const routeSlug = previewRoutes[item.permit_type];
    if (!routeSlug) return;

    const previewUrl = `/permits/template/${routeSlug}/${item.id}`;

    document.getElementById('previewFrame').src = previewUrl;
    document.getElementById('infoPermitNumber').textContent = item.permit_number ?? '';
    document.getElementById('infoPermitType').textContent = item.permit_type ?? '';
    document.getElementById('infoUser').textContent = item.user ?? '';
    document.getElementById('infoTin').textContent = item.tin ?? 'N/A';
    document.getElementById('infoDateReg').textContent = item.date_of_registration ?? '';
    document.getElementById('infoApprovedDate').textContent = item.approved_date_of_registration ?? '';
    document.getElementById('infoExpirationDate').textContent = item.expiration_date_of_registration ?? 'No Expiration';
    document.getElementById('infoStatus').textContent = item.status ?? '';

    document.getElementById('previewOverlay').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePreview() {
    document.getElementById('previewFrame').src = '';
    document.getElementById('previewOverlay').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function drawTableRows() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    if (!permitRows.length) {
        tableBody.innerHTML = `<tr><td colspan="9" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    permitRows.forEach((item, index) => {
        const classes = getStatusClasses(item.status);
        const canView = !!previewRoutes[item.permit_type];

        tableBody.innerHTML += `
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3">${item.permit_number ?? ''}</td>
                <td class="p-3">${item.date_of_registration ?? ''}</td>
                <td class="p-3">${item.approved_date_of_registration ?? ''}</td>
                <td class="p-3">${item.expiration_date_of_registration ?? 'No Expiration'}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">${item.permit_type ?? ''}</td>
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

async function renderTable(filterValue) {
    currentPermitFilter = filterValue;
    closePreview();

    const permitData = await fetchPermits(filterValue);
    permitRows = permitData || [];

    sortPermitRowsByType();
}

async function addPermit() {
    const tin = document.getElementById('tinInput').value;
    const permitType = document.getElementById('permitTypeInput').value;
    const dateOfRegistration = document.getElementById('dateOfRegistrationInput').value;
    const approvedDateOfRegistration = document.getElementById('approvedDateOfRegistrationInput').value;
    const hasExpiration = document.getElementById('hasExpirationInput').checked;
    const expirationDateOfRegistration = hasExpiration
        ? document.getElementById('expirationDateOfRegistrationInput').value
        : null;

    if (!permitType) {
        alert('Please select a Permit Type.');
        return false;
    }

    const res = await fetch('/permits', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            permit_type: permitType,
            tin: tin,
            date_of_registration: dateOfRegistration,
            approved_date_of_registration: approvedDateOfRegistration,
            expiration_date_of_registration: expirationDateOfRegistration
        })
    });

    if (!res.ok) {
        const errorText = await res.text();
        console.error(errorText);
        alert('Failed to save permit.');
        return false;
    }

    await renderTable(currentPermitFilter);
    return true;
}

renderTable(currentPermitFilter);

document.getElementById('permitDropdownBtn').addEventListener('click', e => {
    e.stopPropagation();
    document.getElementById('permitMenu').classList.toggle('hidden');
});

document.getElementById('permitMenu').addEventListener('click', e => {
    if (e.target.tagName === 'DIV') {
        const selected = e.target.innerText;
        document.getElementById('selectedPermitFilter').innerText = selected;
        currentPermitFilter = selected;
        renderTable(selected);
        document.getElementById('permitMenu').classList.add('hidden');
    }
});

document.getElementById('permitTypeHeader').addEventListener('click', () => {
    togglePermitTypeSort();
});

document.addEventListener('click', (e) => {
    const permitMenu = document.getElementById('permitMenu');
    const dropdownBtn = document.getElementById('permitDropdownBtn');

    if (!permitMenu.contains(e.target) && !dropdownBtn.contains(e.target)) {
        permitMenu.classList.add('hidden');
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closePreview();
    }
});
</script>
@endsection