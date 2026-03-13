@extends('layouts.app')

@section('content')
<div x-data="{ showSlideOver: false }" 
     class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0">

        {{-- SLIDE OVER FORM --}}
    <div x-show="showSlideOver" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="absolute inset-0">
            
            <div @click="showSlideOver=false"
                 class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">

                <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col h-full"
                     x-transition:enter="transform transition ease-in-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">

                    {{-- HEADER --}}
                    <div class="p-6 border-b flex justify-between">
                        <h2 class="font-bold text-lg">Add Permit Entry</h2>
                        <button @click="showSlideOver=false">✕</button>
                    </div>

                    {{-- FORM --}}
                    <div class="p-6 space-y-4 flex-1 overflow-y-auto">

                        <div>
                            <label class="block text-sm font-medium">Client</label>
                            <input id="clientInput"
                                class="w-full border rounded-md p-2"
                                placeholder="Client Name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">TIN</label>
                            <input id="tinInput"
                                class="w-full border rounded-md p-2"
                                placeholder="TIN">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Registration Status</label>
                            <select id="regInput" class="w-full border rounded-md p-2">
                                <option>Active</option>
                                <option>Pending</option>
                                <option>Expired</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Status</label>
                            <select id="statusInput" class="w-full border rounded-md p-2">
                                <option>Active</option>
                                <option>For Review</option>
                                <option>Overdue</option>
                            </select>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="p-6 border-t flex gap-3">

                        <button
                            @click="showSlideOver=false"
                            class="flex-1 border py-2 rounded">
                            Cancel
                        </button>

                        <button
                            @click="addPermit(); showSlideOver=false"
                            class="flex-1 bg-blue-600 text-white py-2 rounded">
                            Save
                        </button>

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
            <button 
                @click="showSlideOver = true"
                class="bg-blue-600 text-white px-6 py-2 rounded text-sm">
                + Add
            </button>
        </div>

        {{-- TABLE WRAPPER --}}
        <div class="p-4 flex-grow overflow-hidden">
            <div class="border rounded-md h-full overflow-auto">
                <table class="w-full text-sm table-fixed border-collapse">
                    <thead class="bg-gray-50 text-gray-600 sticky top-0 z-20">
                        <tr>
                            <th id="sortDate" class="w-32 p-3 text-left cursor-pointer select-none hover:text-blue-600">Date <span id="dateIcon">↓</span></th>
                            <th id="sortUploader" class="w-32 p-3 text-left cursor-pointer select-none hover:text-blue-600">Uploader <span id="userIcon">↓</span></th>
                            <th id="sortClient" class="w-40 p-3 text-left cursor-pointer select-none hover:text-blue-600">Client <span id="clientIcon">↓</span></th>
                            <th class="w-24 p-3 text-left">TIN</th>
                            <th class="w-32 p-3 text-left">Reg Status</th>
                            <th class="w-32 p-3 text-left relative overflow-visible">
                                <button id="statusFilterBtn" class="flex items-center gap-1 hover:text-blue-600 font-bold">Status ▾</button>
                                <div id="statusMenu" class="hidden absolute left-0 mt-2 w-36 bg-white border shadow-xl rounded-md z-50 py-1">
                                    <div data-filter="all" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Show All</div>
                                    <div data-filter="Active" class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-green-600">Active</div>
                                    <div data-filter="For Review" class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-yellow-600">For Review</div>
                                    <div data-filter="Overdue" class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-red-600">Overdue</div>
                                </div>
                            </th>
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
    const permitData = {
        "Mayor's Permit": [
            { date: "2023-10-24", user: "Admin_Sarah", client: "TechFlow Inc.", tin: "009-123", reg: "Renewed", status: "Active" },
            { date: "2023-11-02", user: "User_John", client: "Green Cafe", tin: "112-987", reg: "Pending", status: "For Review" },
            { date: "2023-01-12", user: "Admin_Sarah", client: "Blue Logistics", tin: "445-556", reg: "Expired", status: "Overdue" },
            { date: "2024-02-10", user: "Admin_Mark", client: "Apex Hardware", tin: "999-000", reg: "Active", status: "Active" },
            { date: "2024-03-01", user: "Admin_Sarah", client: "Quick Bite", tin: "222-333", reg: "Pending", status: "For Review" },
            { date: "2022-12-15", user: "User_John", client: "Old Mill Co.", tin: "777-888", reg: "Expired", status: "Overdue" },
            { date: "2025-05-20", user: "Admin_Mark", client: "Zenith Solar", tin: "123-456", reg: "Active", status: "Active" },
            { date: "2024-08-14", user: "User_Anna", client: "Bright Path", tin: "555-111", reg: "Pending", status: "For Review" },
            { date: "2023-06-30", user: "Admin_Sarah", client: "Ocean View", tin: "998-776", reg: "Expired", status: "Overdue" }
        ],
        "Barangay Business Permit": [], "Fire Permit": [], "Sanitary Permit": [], "OBO": []
    };

    let sortDirs = { date: true, user: true, client: true };

    function renderTable(permitName, filter = "all") {
        currentPermit = permitName;
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";
        const data = permitData[permitName] || [];

        const filteredData = filter === "all" ? data : data.filter(item => item.status === filter);

        if (filteredData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="p-10 text-center text-gray-400 italic">No data found</td></tr>`;
            return;
        }

        filteredData.forEach(item => {
            const statusClass = item.status === 'Active' ? 'text-green-600' : (item.status === 'Overdue' ? 'text-red-600' : 'text-yellow-600');
            const dotClass = item.status === 'Active' ? 'bg-green-500' : (item.status === 'Overdue' ? 'bg-red-500' : 'bg-yellow-500');
            tableBody.innerHTML += `
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">${item.date}</td><td class="p-3">${item.user}</td><td class="p-3 truncate">${item.client}</td>
                    <td class="p-3">${item.tin}</td><td class="p-3">${item.reg}</td>
                    <td class="p-3"><span class="status-val flex items-center gap-1.5 ${statusClass}"><span class="w-2 h-2 ${dotClass} rounded-full"></span> ${item.status}</span></td>
                </tr>`;
        });
    }

    // Status Filter Listener
    document.getElementById("statusFilterBtn").addEventListener("click", (e) => { e.stopPropagation(); document.getElementById("statusMenu").classList.toggle("hidden"); });
    document.getElementById("statusMenu").addEventListener("click", (e) => {
        const filter = e.target.getAttribute("data-filter");
        if (filter) renderTable(currentPermit, filter);
        document.getElementById("statusMenu").classList.add("hidden");
    });

    // Sorting Logic
    function sortTable(key, iconId) {
        permitData[currentPermit].sort((a, b) => sortDirs[key] ? a[key].localeCompare(b[key]) : b[key].localeCompare(a[key]));
        sortDirs[key] = !sortDirs[key];
        document.getElementById(iconId).textContent = sortDirs[key] ? "↓" : "↑";
        renderTable(currentPermit);
    }

    document.getElementById("sortDate").addEventListener("click", () => sortTable('date', 'dateIcon'));
    document.getElementById("sortUploader").addEventListener("click", () => sortTable('user', 'userIcon'));
    document.getElementById("sortClient").addEventListener("click", () => sortTable('client', 'clientIcon'));

    // Dropdown listeners
    document.getElementById("permitDropdownBtn").addEventListener("click", (e) => { e.stopPropagation(); document.getElementById("permitMenu").classList.toggle("hidden"); });
    document.getElementById("permitMenu").addEventListener("click", (e) => {
        if(e.target.tagName === 'DIV') {
            document.getElementById("selectedPermit").innerText = e.target.innerText;
            renderTable(e.target.innerText);
            document.getElementById("permitMenu").classList.add("hidden");
        }
    });

    document.addEventListener("click", () => {
        document.getElementById("permitMenu").classList.add("hidden");
        document.getElementById("statusMenu").classList.add("hidden");
    });

    renderTable("Mayor's Permit");
</script>
@endsection