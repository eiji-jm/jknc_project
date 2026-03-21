@extends('layouts.app')

@section('content')
<div x-data="{ showSlideOver: false }" class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

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

                        {{-- HEADER --}}
                        <div class="p-6 border-b flex justify-between items-center">
                            <h2 class="font-bold text-lg">Add Permit Entry</h2>
                            <button @click="showSlideOver=false" class="text-gray-500 hover:text-gray-700">✕</button>
                        </div>

                        {{-- FORM --}}
                        <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                            <div>
                                <label class="block text-sm font-medium mb-1">Client</label>
                                <input id="clientInput" class="w-full border rounded-md p-2" placeholder="Client Name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">TIN</label>
                                <input id="tinInput" class="w-full border rounded-md p-2" placeholder="TIN">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Date of Registration</label>
                                <input id="dateOfRegistrationInput" type="date" class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Approved Date of Registration</label>
                                <input id="approvedDateOfRegistrationInput" type="date" class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Expiration Date of Registration</label>
                                <input id="expirationDateOfRegistrationInput" type="date" class="w-full border rounded-md p-2">
                            </div>

                            <div class="text-xs text-gray-500 bg-gray-50 border rounded-md p-3">
                                Status is now automatic based on the expiration date.
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="p-6 border-t flex gap-3">
                            <button @click="showSlideOver=false" class="flex-1 border py-2 rounded">Cancel</button>
                            <button @click="addPermit()" class="flex-1 bg-blue-600 text-white py-2 rounded">Save</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
            <div class="relative">
                <button id="permitDropdownBtn" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-md font-medium hover:bg-gray-200">
                    <span id="selectedPermit">Mayor's Permit</span> ▾
                </button>

                <div id="permitMenu" class="hidden absolute left-0 mt-2 w-56 bg-white border shadow-xl rounded-md z-50 py-1">
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Mayor's Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Barangay Business Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Fire Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Sanitary Permit</div>
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">OBO</div>
                </div>
            </div>

            <button @click="showSlideOver = true" class="bg-blue-600 text-white px-6 py-2 rounded text-sm">+ Add</button>
        </div>

        {{-- TABLE --}}
        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th class="w-40 p-3 text-left">Date of Registration</th>
                            <th class="w-48 p-3 text-left">Approved Date of Registration</th>
                            <th class="w-44 p-3 text-left">Expiration Date of Registration</th>
                            <th class="w-32 p-3 text-left">Uploader</th>
                            <th class="w-40 p-3 text-left">Client</th>
                            <th class="w-32 p-3 text-left">TIN</th>
                            <th class="w-32 p-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
let currentPermit = "Mayor's Permit";

async function fetchPermits(permitName) {
    const res = await fetch(`/permits/${encodeURIComponent(permitName)}`);
    const data = await res.json();
    console.log('Fetched permits:', data);
    return data;
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

async function renderTable(permitName) {
    currentPermit = permitName;

    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    const permitData = await fetchPermits(permitName);

    if (!permitData || permitData.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
        return;
    }

    permitData.forEach(item => {
        const classes = getStatusClasses(item.status);

        tableBody.innerHTML += `
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3">${item.date_of_registration ?? ''}</td>
                <td class="p-3">${item.approved_date_of_registration ?? ''}</td>
                <td class="p-3">${item.expiration_date_of_registration ?? ''}</td>
                <td class="p-3">${item.user ?? ''}</td>
                <td class="p-3 truncate">${item.client ?? ''}</td>
                <td class="p-3">${item.tin ?? ''}</td>
                <td class="p-3">
                    <span class="flex items-center gap-1.5 ${classes.textClass}">
                        <span class="w-2 h-2 ${classes.dotClass} rounded-full"></span>
                        ${item.status ?? 'No Status'}
                    </span>
                </td>
            </tr>
        `;
    });
}

async function addPermit() {
    const client = document.getElementById('clientInput').value;
    const tin = document.getElementById('tinInput').value;
    const dateOfRegistration = document.getElementById('dateOfRegistrationInput').value;
    const approvedDateOfRegistration = document.getElementById('approvedDateOfRegistrationInput').value;
    const expirationDateOfRegistration = document.getElementById('expirationDateOfRegistrationInput').value;

    const res = await fetch('/permits', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            permit_type: currentPermit,
            client: client,
            tin: tin,
            date_of_registration: dateOfRegistration,
            approved_date_of_registration: approvedDateOfRegistration,
            expiration_date_of_registration: expirationDateOfRegistration
        })
    });

    const saved = await res.json();
    console.log('Saved permit:', saved);

    document.getElementById('clientInput').value = '';
    document.getElementById('tinInput').value = '';
    document.getElementById('dateOfRegistrationInput').value = '';
    document.getElementById('approvedDateOfRegistrationInput').value = '';
    document.getElementById('expirationDateOfRegistrationInput').value = '';

    renderTable(currentPermit);
}

renderTable(currentPermit);

document.getElementById("permitDropdownBtn").addEventListener("click", e => {
    e.stopPropagation();
    document.getElementById("permitMenu").classList.toggle("hidden");
});

document.getElementById("permitMenu").addEventListener("click", e => {
    if (e.target.tagName === 'DIV') {
        document.getElementById("selectedPermit").innerText = e.target.innerText;
        renderTable(e.target.innerText);
        document.getElementById("permitMenu").classList.add("hidden");
    }
});

document.addEventListener("click", () => {
    document.getElementById("permitMenu").classList.add("hidden");
});
</script>
@endsection