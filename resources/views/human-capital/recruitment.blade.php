@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col" x-data="recruitmentPage({{ $mrfData->toJson() }}, {{ $jpfData->toJson() }})">

    {{-- TABS --}}
    <div class="flex items-center border-b border-gray-200 mb-4 gap-1">
        <template x-for="tab in tabs" :key="tab.key">
            <button
                type="button"
                @click="activeTab = tab.key"
                :class="activeTab === tab.key
                    ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 text-sm transition-colors -mb-px focus:outline-none"
                x-text="tab.label"
            ></button>
        </template>
    </div>

    {{-- TOOLBAR --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" x-model="search" :placeholder="'Search ' + activeTab + '...'"
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white">
        </div>
        <div class="relative" @click.away="showFilter = false">
            <button type="button" @click="showFilter = !showFilter" 
                class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition"
                :class="filterStatus !== 'All' ? 'border-blue-500 bg-blue-50' : ''">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
                Filter <span x-show="filterStatus !== 'All'" class="ml-1 text-blue-600 font-bold" x-text="'('+filterStatus+')'"></span>
            </button>
            
            <div x-show="showFilter" x-transition class="absolute left-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl z-30 p-2">
                <p class="px-2 py-1.5 text-[10px] uppercase font-bold text-gray-400 tracking-wider">Filter by Status</p>
                <div class="space-y-1">
                    <template x-for="st in ['All', 'Pending', 'Open', 'Filled', 'Hold', 'Cancelled', 'Disapproved']">
                        <button @click="filterStatus = st; showFilter = false" 
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition font-medium"
                            :class="filterStatus === st ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                            x-text="st"></button>
                    </template>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <button @click="filterStatus = 'All'; showFilter = false" class="w-full text-center text-xs text-gray-400 hover:text-gray-600 font-medium">Clear all filters</button>
                </div>
            </div>
        </div>
        <button type="button" @click="downloadCSV()"
            class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/></svg>
            Download CSV
        </button>
        <button type="button" @click="openModal()"
            class="flex items-center gap-2 px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Add New
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col flex-grow min-h-0 overflow-hidden">
        <div class="overflow-auto flex-grow">

            {{-- MRF TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'MRF'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Request ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Department</th>
                        <th class="px-4 py-3 text-left font-semibold">Headcount</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="7" class="px-4 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                                <span class="text-sm">No MRF records found. Click <strong>+ Add New</strong> to create one.</span>
                            </div>
                        </td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-blue-600 font-medium" x-text="row.request_id"></td>
                            <td class="px-4 py-3 text-gray-800" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.department"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.headcount"></td>
                            <td class="px-4 py-3">
                                <span x-text="row.request_status" :class="statusClass(row.request_status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.date_requested"></td>
                            <td class="px-4 py-3">
                                <button @click="viewMRF(row)" class="text-xs text-blue-600 hover:underline mr-2">View</button>
                                <button @click="deleteMRF(i)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- JPF TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'JPF'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Job ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Type</th>
                        <th class="px-4 py-3 text-left font-semibold">Location</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Posted</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-blue-600 font-medium" x-text="row.job_id"></td>
                            <td class="px-4 py-3 text-gray-800" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.employment_type"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.location"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.posted_date"></td>
                            <td class="px-4 py-3">
                                <button @click="viewJPF(row)" class="text-xs text-blue-600 hover:underline mr-2">View</button>
                                <button @click="deleteJPF(i)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- CAF TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'CAF'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Phone</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Applied</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.email"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.phone"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.applied"></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- ASSESSMENT TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Assessment'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Test</th>
                        <th class="px-4 py-3 text-left font-semibold">Score</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.test"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.score"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.date"></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- INTERVIEW TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Interview'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Round</th>
                        <th class="px-4 py-3 text-left font-semibold">Interviewer</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.round"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.interviewer"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.date"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- JOB OFFER TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Job Offer'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Salary</th>
                        <th class="px-4 py-3 text-left font-semibold">Start Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="5" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.salary"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.startDate"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>

        {{-- PAGINATION FOOTER --}}
        <div class="px-4 py-2 border-t border-gray-100 bg-blue-50/30 flex items-center justify-end text-[13px] font-semibold text-blue-600 gap-4">
            <div class="flex items-center gap-2">
                <span>Records per page</span>
                <div class="relative">
                    <select x-model="perPage" @change="currentPage = 1"
                        class="bg-transparent border-none focus:ring-0 cursor-pointer pr-5 appearance-none">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <svg class="w-3 h-3 absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
            
            <div class="h-4 w-px bg-blue-200"></div>

            <div class="flex items-center gap-4">
                <span x-text="`${startRange} - ${endRange} of ${filteredRows.length}`"></span>
                <div class="flex items-center gap-1">
                    <button @click="prevPage()" :disabled="currentPage === 1" 
                        class="p-1 hover:bg-blue-100 rounded transition disabled:opacity-30 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="m15 19-7-7 7-7"/></svg>
                    </button>
                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        class="p-1 hover:bg-blue-100 rounded transition disabled:opacity-30 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="m9 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MRF MODAL (SPLIT PANEL) ===================== --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showModal = false"
    >
        <div
            x-show="showModal"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="bg-gray-100 shadow-2xl w-[95vw] h-full flex flex-col overflow-hidden"
        >
            {{-- Top bar --}}
            <div class="flex items-center justify-between px-6 py-3 bg-white border-b shrink-0">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest">New Manpower Request</h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Split body --}}
            <div class="flex flex-1 overflow-hidden gap-4 p-4">

                {{-- RIGHT: INPUT FORM --}}
                <div class="w-[42%] bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden shrink-0 order-last">
                    <div class="px-5 py-3 border-b bg-blue-700 rounded-t-xl">
                        <p class="text-xs font-bold text-white uppercase tracking-wider">Fill Up Form</p>
                    </div>
                    <form @submit.prevent="submitMRF()" class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Requesting Department <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.department" required placeholder="e.g. Human Resources"
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Position / Title <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.position" required placeholder="e.g. Engineer"
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Date Requested <span class="text-red-500">*</span></label>
                                <input type="date" x-model="form.dateRequested" required
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Date Required</label>
                                <input type="date" x-model="form.dateRequired"
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Employment Type</label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="et in ['Student Trainee','Project Hire','Contractual','Regular']" :key="et">
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 hover:bg-blue-50 hover:border-blue-300 transition"
                                        :class="form.employmentType === et ? 'bg-blue-50 border-blue-400 text-blue-700 font-semibold' : ''">
                                        <input type="radio" x-model="form.employmentType" :value="et" class="accent-blue-600">
                                        <span x-text="et"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Brief Description of Duties</label>
                            <textarea x-model="form.duties" rows="3" placeholder="Describe duties and responsibilities..."
                                class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nature of Request</label>
                                <template x-for="n in ['New / Addition','Replacement']" :key="n">
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer mb-1">
                                        <input type="radio" x-model="form.natureOfRequest" :value="n" class="accent-blue-600">
                                        <span x-text="n"></span>
                                    </label>
                                </template>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Civil Status</label>
                                <template x-for="s in ['Single','Married','No Preference']" :key="s">
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer mb-1">
                                        <input type="radio" x-model="form.civilStatus" :value="s" class="accent-blue-600">
                                        <span x-text="s"></span>
                                    </label>
                                </template>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Gender</label>
                                <template x-for="g in ['Male','Female','No Preference']" :key="g">
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer mb-1">
                                        <input type="radio" x-model="form.gender" :value="g" class="accent-blue-600">
                                        <span x-text="g"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Age Range</label>
                                <input type="text" x-model="form.ageRange" placeholder="e.g. 20-35"
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Headcount <span class="text-red-500">*</span></label>
                                <input type="number" x-model="form.headcount" min="1" required placeholder="e.g. 2"
                                    class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Educational Requirement</label>
                            <input type="text" x-model="form.education" placeholder="e.g. Bachelor's Degree in IT"
                                class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Preferred Qualifications / Experience</label>
                            <textarea x-model="form.qualifications" rows="2" placeholder="List preferred skills or experience..."
                                class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                        </div>

                        <div class="border-t pt-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Approvals</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Requested by</label>
                                    <input type="text" x-model="form.requestedBy" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Approved by</label>
                                    <input type="text" x-model="form.approvedBy" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">For HRS Use Only</p>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Additional Remarks</label>
                                <textarea x-model="form.remarks" rows="2" placeholder="Reason for request..."
                                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-2">Request Status</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="rs in ['Filled','Cancelled','Hold','Disapproved']" :key="rs">
                                        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                            <input type="radio" x-model="form.requestStatus" :value="rs" class="accent-blue-600">
                                            <span x-text="rs"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Charged to (Dept)</label>
                                    <input type="text" x-model="form.chargedTo" placeholder="Department"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Breakdown Details</label>
                                    <input type="text" x-model="form.breakdownDetails" placeholder="Details"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Name of Hired Personnel</label>
                                    <input type="text" x-model="form.hiredPersonnel" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date Hired</label>
                                    <input type="date" x-model="form.dateHired"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Processed by</label>
                                    <input type="text" x-model="form.processedBy" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Checked / Approved by</label>
                                    <input type="text" x-model="form.checkedBy" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end gap-3 pt-2 pb-1">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition">
                                Submit Request
                            </button>
                        </div>

                    </form>
                </div>

                {{-- LEFT: LIVE MRF DOCUMENT PREVIEW --}}
                <div class="flex-1 bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden">
                    <div class="px-5 py-3 border-b bg-gray-50 rounded-t-xl shrink-0 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live Preview</p>
                        <div class="flex items-center gap-2">
                            <select x-model="paperSize" class="text-xs border border-gray-300 rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:border-blue-500 cursor-pointer">
                                <option value="a4">A4 Size</option>
                                <option value="letter">Letter Size</option>
                                <option value="legal">Legal Size</option>
                            </select>
                            <button type="button" @click="downloadPDF('mrf-doc-create')" class="text-xs px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded text-gray-700 font-semibold flex items-center gap-1 transition shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-5">
                        <div id="mrf-doc-create" class="border border-gray-400 text-xs text-gray-800 font-sans w-[794px] shrink-0 leading-[1.2] mx-auto shadow-sm bg-white p-4">

                            {{-- Form Header with Logo --}}
                            <div class="flex items-center justify-center pb-4 pt-2 border-b border-gray-400">
                                <img src="{{ asset('images/imaglogo.png') }}" onerror="this.src='{{ asset('images/imag1logo.jpg') }}'" alt="John Kelly & Company" class="h-14 w-auto object-contain mix-blend-multiply">
                            </div>

                            {{-- Title --}}
                            <div class="bg-blue-700 text-white text-center font-bold py-2 text-sm tracking-widest uppercase border-b border-gray-400">
                                Manpower Request Form
                            </div>

                            {{-- Row 1 --}}
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2">
                                    <span class="text-gray-500">Requesting Department:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.department || ''"></p>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Date Requested:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.dateRequested || ''"></p>
                                    <span class="text-gray-500 block mt-2">Date Required:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.dateRequired || ''"></p>
                                </div>
                            </div>

                            {{-- Row 2 --}}
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2">
                                    <span class="text-gray-500">Position / Title:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.position || ''"></p>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Employment Type:</span>
                                    <div class="grid grid-cols-2 gap-x-2 mt-1">
                                        <template x-for="et in ['Student Trainee','Project Hire','Contractual','Regular']" :key="et">
                                            <div class="flex items-center gap-1">
                                                <span class="inline-flex items-center justify-center w-3 h-3 border border-gray-400 rounded-sm shrink-0"
                                                    :class="form.employmentType === et ? 'bg-blue-600 border-blue-600' : ''">
                                                    <svg x-show="form.employmentType === et" class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 12 12"><path d="M10 3L5 8.5 2 5.5"/></svg>
                                                </span>
                                                <span x-text="et"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 3: Duties --}}
                            <div class="border-b border-gray-400 p-2">
                                <span class="text-gray-500">Brief Description of Duties <em>(or attach the Job Description)</em>:</span>
                                <p class="mt-1 whitespace-pre-wrap min-h-[3rem]" x-text="form.duties || ''"></p>
                            </div>

                            {{-- Row 4: Nature / Age / Status / Gender --}}
                            <div class="grid grid-cols-4 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2">
                                    <span class="text-gray-500">Nature of Request:</span>
                                    <template x-for="n in ['New / Addition','Replacement']" :key="n">
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="inline-flex items-center justify-center w-3 h-3 border border-gray-400 rounded-sm shrink-0"
                                                :class="form.natureOfRequest === n ? 'bg-blue-600 border-blue-600' : ''">
                                                <svg x-show="form.natureOfRequest === n" class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 12 12"><path d="M10 3L5 8.5 2 5.5"/></svg>
                                            </span>
                                            <span x-text="n"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Age Range:</span>
                                    <p class="font-semibold mt-1 min-h-[1rem]" x-text="form.ageRange || ''"></p>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Status:</span>
                                    <template x-for="s in ['Single','Married','No Preference']" :key="s">
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="inline-flex items-center justify-center w-3 h-3 border border-gray-400 rounded-sm shrink-0"
                                                :class="form.civilStatus === s ? 'bg-blue-600 border-blue-600' : ''">
                                                <svg x-show="form.civilStatus === s" class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 12 12"><path d="M10 3L5 8.5 2 5.5"/></svg>
                                            </span>
                                            <span x-text="s"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Gender:</span>
                                    <template x-for="g in ['Male','Female','No Preference']" :key="g">
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="inline-flex items-center justify-center w-3 h-3 border border-gray-400 rounded-sm shrink-0"
                                                :class="form.gender === g ? 'bg-blue-600 border-blue-600' : ''">
                                                <svg x-show="form.gender === g" class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 12 12"><path d="M10 3L5 8.5 2 5.5"/></svg>
                                            </span>
                                            <span x-text="g"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Row 5: Education / Headcount --}}
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2">
                                    <span class="text-gray-500">Educational Requirement:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.education || ''"></p>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500">Headcount Requested:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.headcount || ''"></p>
                                </div>
                            </div>

                            {{-- Row 6: Qualifications --}}
                            <div class="border-b border-gray-400 p-2">
                                <span class="text-gray-500">Preferred Qualifications / Experience <em>(not mentioned above or in the JD)</em>:</span>
                                <p class="mt-1 whitespace-pre-wrap min-h-[2.5rem]" x-text="form.qualifications || ''"></p>
                            </div>

                            {{-- APPROVALS header --}}
                            <div class="bg-blue-700 text-white text-center font-bold py-1.5 text-xs tracking-widest uppercase border-b border-gray-400">
                                Approvals
                            </div>
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2 flex flex-col items-center">
                                    <span class="text-gray-500 self-start text-[10px] uppercase">Requested by:</span>
                                    <p class="font-bold text-gray-800 mt-4 text-xs" x-text="form.requestedBy || ''"></p>
                                    <p class="text-gray-400 italic text-[9px] border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                                </div>
                                <div class="p-2 flex flex-col items-center">
                                    <span class="text-gray-500 self-start text-[10px] uppercase">Approved by:</span>
                                    <p class="font-bold text-gray-800 mt-4 text-xs" x-text="form.approvedBy || ''"></p>
                                    <p class="text-gray-400 italic text-[9px] border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                                </div>
                            </div>

                            {{-- FOR HRS USE ONLY --}}
                            <div class="bg-gray-200 text-center font-bold py-1.5 text-xs tracking-widest uppercase border-b border-gray-400 text-gray-700">
                                For HRS Use Only
                            </div>
                            <div class="border-b border-gray-400 p-2">
                                <span class="text-gray-500">Additional Remarks <em>(Reason for Request)</em>:</span>
                                <p class="mt-1 whitespace-pre-wrap min-h-[2rem]" x-text="form.remarks || ''"></p>
                            </div>
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-2">
                                    <span class="text-gray-500">Request Status:</span>
                                    <div class="grid grid-cols-2 gap-x-2 mt-1">
                                        <template x-for="rs in ['Filled','Cancelled','Hold','Disapproved']" :key="rs">
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span class="inline-flex items-center justify-center w-3 h-3 border border-gray-400 rounded-sm shrink-0"
                                                    :class="form.requestStatus === rs ? 'bg-blue-600 border-blue-600' : ''">
                                                    <svg x-show="form.requestStatus === rs" class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 12 12"><path d="M10 3L5 8.5 2 5.5"/></svg>
                                                </span>
                                                <span x-text="rs"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="mt-2 text-gray-500">Name of Hired Personnel:</p>
                                    <p class="font-semibold min-h-[1rem]" x-text="form.hiredPersonnel || ''"></p>
                                    <p class="mt-2 text-gray-500">Date Hired:</p>
                                    <p class="font-semibold min-h-[1rem]" x-text="form.dateHired || ''"></p>
                                    <p class="text-gray-500 mt-2 text-[10px] uppercase">Processed by:</p>
                                    <div class="flex flex-col items-center mt-2">
                                        <p class="font-bold text-gray-800 text-xs" x-text="form.processedBy || ''"></p>
                                        <p class="text-gray-400 italic text-[9px] border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <span class="text-gray-500 text-[10px] uppercase">Charged to (Department):</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="form.chargedTo || ''"></p>
                                    <p class="mt-2 text-gray-500 text-[10px] uppercase">Breakdown Details:</p>
                                    <p class="font-semibold min-h-[1rem]" x-text="form.breakdownDetails || ''"></p>
                                    <p class="mt-4 text-gray-500 text-[10px] uppercase">Checked / Approved by:</p>
                                    <div class="flex flex-col items-center mt-2">
                                        <p class="font-bold text-gray-800 text-xs" x-text="form.checkedBy || ''"></p>
                                        <p class="text-gray-400 italic text-[9px] border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end form doc --}}
                    </div>
                </div>{{-- end right panel --}}

            </div>{{-- end split body --}}
        </div>
    </div>

    {{-- ===================== MRF VIEW MODAL ===================== --}}

    <div
        x-show="showViewModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-50 flex bg-white"
        style="display:none;"
        @click.self="showViewModal = false"
    >
        <div
            x-show="showViewModal"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="bg-white w-full h-full flex flex-col overflow-hidden shadow-2xl"
        >
            <div class="flex items-center justify-between px-8 py-5 border-b bg-gray-50 flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800 tracking-widest uppercase">Manpower Request Form Details</h2>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pr-6 border-r border-gray-300">
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Document Format:</span>
                        <select x-model="paperSize" class="text-sm border border-gray-300 rounded-lg px-3 py-2 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 hover:border-gray-400 transition cursor-pointer font-medium">
                            <option value="a4">A4 Size</option>
                            <option value="letter">Letter Size</option>
                            <option value="legal">Legal Size</option>
                        </select>
                    </div>
                    <button type="button" @click="downloadPDF('mrf-doc-view')" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition-all flex items-center gap-2 transform hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export to PDF
                    </button>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto bg-gray-100 py-10 px-6" x-show="viewData">
                <template x-if="viewData">
                <div id="mrf-doc-view" class="border border-gray-300 bg-white p-4 mx-auto w-[794px] shrink-0">
                    <div class="flex items-center justify-center pb-4 pt-2 border-b border-gray-300">
                        <img src="{{ asset('images/imaglogo.png') }}" onerror="this.src='{{ asset('images/imag1logo.jpg') }}'" alt="John Kelly & Company" class="h-14 w-auto object-contain mix-blend-multiply">
                    </div>
                    <div class="bg-blue-700 text-white text-center font-bold py-2 text-sm tracking-widest uppercase border-gray-300">Manpower Request Form</div>
                    <div class="grid grid-cols-2 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3"><span class="text-xs text-gray-500">Requesting Department:</span><p class="font-medium mt-1" x-text="viewData.department"></p></div>
                        <div class="p-3">
                            <span class="text-xs text-gray-500">Date Requested:</span><p class="font-medium mt-1" x-text="viewData.dateRequested"></p>
                            <span class="text-xs text-gray-500 block mt-2">Date Required:</span><p class="font-medium mt-1" x-text="viewData.dateRequired || '—'"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3"><span class="text-xs text-gray-500">Position / Title:</span><p class="font-medium mt-1" x-text="viewData.position"></p></div>
                        <div class="p-3"><span class="text-xs text-gray-500">Employment Type:</span><p class="font-medium mt-1" x-text="viewData.employmentType || '—'"></p></div>
                    </div>
                    <div class="border-b border-gray-300 p-3">
                        <span class="text-xs text-gray-500">Brief Description of Duties:</span>
                        <p class="mt-1 whitespace-pre-wrap" x-text="viewData.duties || '—'"></p>
                    </div>
                    <div class="grid grid-cols-4 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3"><span class="text-xs text-gray-500">Nature of Request:</span><p class="font-medium mt-1" x-text="viewData.natureOfRequest || '—'"></p></div>
                        <div class="p-3"><span class="text-xs text-gray-500">Age Range:</span><p class="font-medium mt-1" x-text="viewData.ageRange || '—'"></p></div>
                        <div class="p-3"><span class="text-xs text-gray-500">Civil Status:</span><p class="font-medium mt-1" x-text="viewData.civilStatus || '—'"></p></div>
                        <div class="p-3"><span class="text-xs text-gray-500">Gender:</span><p class="font-medium mt-1" x-text="viewData.gender || '—'"></p></div>
                    </div>
                    <div class="grid grid-cols-2 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3"><span class="text-xs text-gray-500">Headcount:</span><p class="font-medium mt-1" x-text="viewData.headcount"></p></div>
                        <div class="p-3"><span class="text-xs text-gray-500">Educational Requirement:</span><p class="font-medium mt-1" x-text="viewData.education || '—'"></p></div>
                    </div>
                    <div class="border-b border-gray-300 p-3">
                        <span class="text-xs text-gray-500">Preferred Qualifications / Experience:</span>
                        <p class="mt-1 whitespace-pre-wrap" x-text="viewData.qualifications || '—'"></p>
                    </div>
                    <div class="bg-blue-700 text-white text-center text-xs font-bold py-1.5 tracking-widest uppercase">Approvals</div>
                    <div class="grid grid-cols-2 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3 flex flex-col items-center">
                            <span class="text-xs text-gray-500 self-start uppercase">Requested by:</span>
                            <p class="font-bold text-gray-800 mt-6 text-sm" x-text="viewData.requestedBy"></p>
                            <p class="text-[10px] text-gray-400 italic border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                        </div>
                        <div class="p-3 flex flex-col items-center">
                            <span class="text-xs text-gray-500 self-start uppercase">Approved by:</span>
                            <p class="font-bold text-gray-800 mt-6 text-sm" x-text="viewData.approvedBy"></p>
                            <p class="text-[10px] text-gray-400 italic border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                        </div>
                    </div>
                    <div class="bg-gray-100 text-center text-xs font-bold py-1.5 tracking-widest uppercase text-gray-700">For HRS Use Only</div>
                    <div class="border-b border-gray-300 p-3"><span class="text-xs text-gray-500">Additional Remarks:</span><p class="mt-1" x-text="viewData.remarks || '—'"></p></div>
                    <div class="grid grid-cols-2 border-b border-gray-300 divide-x divide-gray-300">
                        <div class="p-3">
                            <span class="text-xs text-gray-500">Request Status:</span><p class="font-medium mt-1 text-gray-800" x-text="viewData.requestStatus || '—'"></p>
                            <span class="text-xs text-gray-500 block mt-2">Name of Hired Personnel:</span><p class="font-medium mt-1" x-text="viewData.hiredPersonnel || '—'"></p>
                            <span class="text-xs text-gray-500 block mt-2">Date Hired:</span><p class="font-medium mt-1" x-text="viewData.dateHired || '—'"></p>
                            <span class="text-xs text-gray-500 block mt-3 uppercase">Processed by:</span>
                            <div class="flex flex-col items-center mt-4">
                                <p class="font-bold text-gray-800 text-sm" x-text="viewData.processedBy"></p>
                                <p class="text-[10px] text-gray-400 italic border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                            </div>
                        </div>
                        <div class="p-3">
                            <span class="text-xs text-gray-500 block uppercase">Charged to (Department):</span><p class="font-medium mt-1" x-text="viewData.chargedTo || '—'"></p>
                            <span class="text-xs text-gray-500 block mt-3 uppercase">Breakdown Details:</span><p class="font-medium mt-1" x-text="viewData.breakdownDetails || '—'"></p>
                            <span class="text-xs text-gray-500 block mt-3 uppercase">Checked / Approved by:</span>
                            <div class="flex flex-col items-center mt-4">
                                <p class="font-bold text-gray-800 text-sm" x-text="viewData.checkedBy"></p>
                                <p class="text-[10px] text-gray-400 italic border-t border-gray-300 w-full text-center pt-1">Signature Over Printed Name</p>
                            </div>
                        </div>
                    </div>
                </div>
                </template>
            </div>
            <div class="flex justify-end px-10 py-6 border-t bg-white shrink-0">
                <button @click="showViewModal = false" class="px-10 py-3 text-base bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition-all transform hover:scale-[1.02] active:scale-100">Close Form</button>
            </div>
        </div>
    </div>

    {{-- ===================== JPF MODAL ===================== --}}
    <div
        x-show="showJpfModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showJpfModal = false"
    >
        <div
            x-show="showJpfModal"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="bg-white shadow-2xl w-[95vw] md:w-[60vw] h-full flex flex-col overflow-hidden"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-widest">Create Job Posting (JPF)</h2>
                <button type="button" @click="showJpfModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form @submit.prevent="submitJPF()" class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Position / Title</label>
                        <select x-model="jpfForm.position" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 bg-white font-medium transition-all">
                            <option value="" disabled>Select Position...</option>
                            <template x-for="pos in uniqueMrfPositions" :key="pos">
                                <option :value="pos" x-text="pos"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Employment Type</label>
                        <select x-model="jpfForm.employmentType" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 bg-white font-medium transition-all">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Work Location</label>
                        <input type="text" x-model="jpfForm.location" required placeholder="e.g. Remote, On-site, Head Office"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Salary Range</label>
                        <input type="text" x-model="jpfForm.salaryRange" placeholder="e.g., $80,000 - $100,000"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detailed Job Description</label>
                    <textarea x-model="jpfForm.jobDescription" required rows="6" placeholder="Describe the role and responsibilities..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 resize-none bg-gray-50"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Candidate Requirements</label>
                    <textarea x-model="jpfForm.requirements" required rows="6" placeholder="List skills, education, and experience..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 resize-none bg-gray-50"></textarea>
                </div>
                
                <div class="flex justify-end gap-4 pt-6 mt-4 border-t border-gray-100">
                    <button type="button" @click="showJpfModal = false"
                        class="px-8 py-3 text-sm font-bold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                        Discard
                    </button>
                    <button type="submit"
                        class="px-8 py-3 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        Create Job Posting
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== JPF VIEW MODAL (FULL PAGE) ===================== --}}
    <div
        x-show="showJpfViewModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-50 flex bg-white"
        style="display:none;"
    >
        <div class="flex flex-col w-full h-full overflow-hidden">
            {{-- Toolbar --}}
            <div class="h-16 px-8 border-b border-gray-100 flex items-center justify-between bg-white shadow-sm shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="showJpfViewModal = false" class="p-2 hover:bg-gray-100 rounded-full transition text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800" x-text="viewJpfData.job_id"></h2>
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold" x-text="viewJpfData.position"></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="window.print()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Details
                    </button>
                    <button @click="showJpfViewModal = false" class="px-6 py-2 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md shadow-blue-100 uppercase tracking-wide">Close</button>
                </div>
            </div>

            <div class="flex-grow overflow-auto bg-gray-50/50 p-8">
                <div class="max-w-4xl mx-auto space-y-6 pb-12">
                    {{-- Quick Summary Card --}}
                    <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-6 flex items-center justify-between">
                        <div class="flex items-center gap-6 divide-x divide-gray-100">
                            <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight mb-0.5">Employment Type</p><p class="font-bold text-gray-800" x-text="viewJpfData.employment_type"></p></div>
                            <div class="pl-6"><p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight mb-0.5">Location</p><p class="font-bold text-gray-800" x-text="viewJpfData.location"></p></div>
                            <div class="pl-6"><p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight mb-0.5">Salary Range</p><p class="font-bold text-gray-800" x-text="viewJpfData.salary_range || 'Not Specified'"></p></div>
                            <div class="pl-6"><p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight mb-0.5">Date Posted</p><p class="font-bold text-gray-800" x-text="viewJpfData.posted_date"></p></div>
                        </div>
                        <span :class="statusClass(viewJpfData.status)" class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm" x-text="viewJpfData.status"></span>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        {{-- Job Description --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                            <h3 class="text-base font-bold text-blue-800 mb-4 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                                Job Description
                            </h3>
                            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm" x-text="viewJpfData.job_description"></div>
                        </div>

                        {{-- Requirements --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                            <h3 class="text-base font-bold text-indigo-800 mb-4 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                Job Requirements
                            </h3>
                            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm" x-text="viewJpfData.requirements"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function recruitmentPage(initialMRF = [], initialJPF = []) {
    return {
        activeTab: 'MRF',
        search: '',
        paperSize: 'letter',
        perPage: 50,
        currentPage: 1,
        showFilter: false,
        filterStatus: 'All',
        showModal: false,
        showViewModal: false,
        showJpfModal: false,
        showJpfViewModal: false,
        viewData: null,
        viewJpfData: {},

        tabs: [
            { key: 'MRF',        label: 'MRF' },
            { key: 'JPF',        label: 'JPF' },
            { key: 'CAF',        label: 'CAF' },
            { key: 'Assessment', label: 'Assessment' },
            { key: 'Interview',  label: 'Interview' },
            { key: 'Job Offer',  label: 'Job Offer' },
        ],

        data: {
            'MRF': initialMRF.map(item => ({
                ...item,
                req_id_display: item.request_id, 
                date_display: item.date_requested
            })),
            'JPF': initialJPF.map(item => ({
                ...item,
                job_id_display: item.job_id,
                posted_display: item.posted_date
            })),
            'CAF':        [],
            'Assessment': [],
            'Interview':  [],
            'Job Offer':  [],
        },

        form: {
            department: '', dateRequested: '', dateRequired: '',
            position: '', employmentType: '',
            duties: '', natureOfRequest: '', ageRange: '',
            civilStatus: 'No Preference', gender: 'No Preference',
            headcount: '', education: '', qualifications: '',
            requestedBy: '', approvedBy: '',
            remarks: '', requestStatus: '',
            chargedTo: '', breakdownDetails: '',
            hiredPersonnel: '', dateHired: '',
            processedBy: '', checkedBy: '',
        },

        jpfForm: {
            position: '', employmentType: 'Full-time', location: '',
            salaryRange: '', jobDescription: '', requirements: ''
        },

        openModal() {
            if (this.activeTab === 'MRF') {
                // Reset form
                this.form = {
                    department: '', dateRequested: '', dateRequired: '',
                    position: '', employmentType: '',
                    duties: '', natureOfRequest: '', ageRange: '',
                    civilStatus: 'No Preference', gender: 'No Preference',
                    headcount: '', education: '', qualifications: '',
                    requestedBy: '', approvedBy: '',
                    remarks: '', requestStatus: '',
                    chargedTo: '', breakdownDetails: '',
                    hiredPersonnel: '', dateHired: '',
                    processedBy: '', checkedBy: '',
                };
                this.showModal = true;
            } else if (this.activeTab === 'JPF') {
                this.jpfForm = {
                    position: '', employmentType: 'Full-time', location: '',
                    salaryRange: '', jobDescription: '', requirements: ''
                };
                this.showJpfModal = true;
            } else {
                alert('Add New functionality for ' + this.activeTab + ' is not yet implemented.');
            }
        },

        submitMRF() {
            axios.post('{{ route("human-capital.recruitment.store_mrf") }}', {
                ...this.form,
                request_status: this.form.requestStatus || 'Pending'
            })
            .then(res => {
                const item = res.data.data;
                this.data['MRF'].unshift({
                    ...item,
                    id: item.request_id,
                    date: item.date_requested
                });
                this.showModal = false;
            })
            .catch(err => {
                alert('Error saving MRF: ' + (err.response?.data?.message || err.message));
            });
        },

        submitJPF() {
            axios.post('{{ route("human-capital.recruitment.store_jpf") }}', {
                ...this.jpfForm,
                posted_date: new Date().toISOString().split('T')[0]
            })
            .then(res => {
                const item = res.data.data;
                this.data['JPF'].unshift({
                    ...item,
                    jobId: item.job_id,
                    type: item.employment_type,
                    posted: item.posted_date
                });
                this.showJpfModal = false;
            })
            .catch(err => {
                alert('Error saving JPF: ' + (err.response?.data?.message || err.message));
            });
        },

        viewJPF(row) {
            this.viewJpfData = { ...row };
            this.showJpfViewModal = true;
        },

        downloadCSV() {
            const rows = this.filteredRows;
            if (rows.length === 0) {
                alert('No data available to download.');
                return;
            }

            // Get headers from the first object
            const headers = Object.keys(rows[0]).filter(k => typeof rows[0][k] !== 'object');
            
            const csvContent = [
                headers.join(','),
                ...rows.map(row => headers.map(h => {
                    let val = row[h] === null || row[h] === undefined ? '' : String(row[h]);
                    // Escape commas and quotes
                    if (val.includes(',') || val.includes('"') || val.includes('\n')) {
                        val = `"${val.replace(/"/g, '""')}"`;
                    }
                    return val;
                }).join(','))
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `${this.activeTab}_list_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        get paginatedRows() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredRows.slice(start, start + parseInt(this.perPage));
        },

        get totalPages() {
            return Math.ceil(this.filteredRows.length / this.perPage) || 1;
        },

        get startRange() {
            if (this.filteredRows.length === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },

        get endRange() {
            return Math.min(this.currentPage * this.perPage, this.filteredRows.length);
        },

        get uniqueMrfPositions() {
            const positions = this.data['MRF'].map(mrf => mrf.position).filter(p => p && p.trim() !== '');
            return [...new Set(positions)];
        },

        viewMRF(row) {
            this.viewData = row;
            this.showViewModal = true;
        },

        deleteMRF(index) {
            if (confirm('Delete this MRF record?')) {
                const item = this.data['MRF'][index];
                axios.delete('/human-capital/recruitment/mrf/' + item.id)
                .then(() => {
                    this.data['MRF'].splice(index, 1);
                })
                .catch(err => {
                    alert('Error deleting MRF: ' + (err.response?.data?.message || err.message));
                });
            }
        },

        deleteJPF(index) {
            if (confirm('Delete this Job Posting?')) {
                const item = this.data['JPF'][index];
                axios.delete('/human-capital/recruitment/jpf/' + item.id)
                .then(() => {
                    this.data['JPF'].splice(index, 1);
                })
                .catch(err => {
                    alert('Error deleting JPF: ' + (err.response?.data?.message || err.message));
                });
            }
        },

        downloadPDF(elementId) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const opt = {
                margin:       0.3,
                filename:     'Manpower_Request_Form.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: this.paperSize, orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).save();
        },

        statusClass(status) {
            const map = {
                'Approved':    'bg-green-100 text-green-700',
                'Completed':   'bg-green-100 text-green-700',
                'Passed':      'bg-green-100 text-green-700',
                'Accepted':    'bg-green-100 text-green-700',
                'Filled':      'bg-green-100 text-green-700',
                'Open':        'bg-blue-100 text-blue-700',
                'Active':      'bg-blue-100 text-blue-700',
                'In Progress': 'bg-blue-100 text-blue-700',
                'Pending':     'bg-yellow-100 text-yellow-700',
                'For Review':  'bg-yellow-100 text-yellow-700',
                'Hold':        'bg-yellow-100 text-yellow-700',
                'Rejected':    'bg-red-100 text-red-700',
                'Failed':      'bg-red-100 text-red-700',
                'Declined':    'bg-red-100 text-red-700',
                'Cancelled':   'bg-red-100 text-red-700',
                'Disapproved': 'bg-red-100 text-red-700',
            };
            return map[status] ?? 'bg-gray-100 text-gray-600';
        },

        get filteredRows() {
            let rows = this.data[this.activeTab] ?? [];
            
            // Apply Status Filter
            if (this.filterStatus !== 'All') {
                rows = rows.filter(r => r.status === this.filterStatus);
            }

            // Apply Search Filter
            if (!this.search.trim()) return rows;
            const q = this.search.toLowerCase();
            return rows.filter(r =>
                Object.values(r).some(v => String(v).toLowerCase().includes(q))
            );
        },
    };
}
</script>
@endpush
@endsection