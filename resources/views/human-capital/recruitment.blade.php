@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col" x-data="recruitmentPage({{ $mrfData->toJson() }}, {{ $jpfData->toJson() }}, {{ $cafData->toJson() }}, {{ $assessmentData->toJson() }}, {{ $interviewData->toJson() }}, {{ $jobOfferData->toJson() }})">

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
        <button type="button" @click="downloadCSV()" x-show="activeTab !== 'Assessment'"
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
                <thead class="bg-white text-gray-600 sticky top-0 z-10">
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
                                <button @click="editMRF(row)" class="text-xs text-amber-600 hover:underline mr-2">Edit</button>
                                <button @click="deleteMRF(i)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- JPF TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'JPF'">
                <thead class="bg-white text-gray-600 sticky top-0 z-10">
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
                                <button @click="editJPF(row)" class="text-xs text-amber-600 hover:underline mr-2">Edit</button>
                                <button @click="deleteJPF(i)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- CAF TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'CAF'">
                <thead class="bg-white text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Phone</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Applied</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
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
                            <td class="px-4 py-3 text-gray-500" x-text="row.applied_date || row.applied"></td>
                            <td class="px-4 py-3">
                                <button @click="viewCAF(row)" class="text-xs text-blue-600 hover:underline mr-2">View</button>
                                <button @click="editCAF(row)" class="text-xs text-amber-600 hover:underline mr-2">Edit</button>
                                <button @click="deleteCAF(row.id)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- ASSESSMENT TABLE --}}
            {{-- ASSESSMENT KANBAN --}}
            <div x-show="activeTab === 'Assessment'" class="flex-1 overflow-x-auto p-4 bg-gray-50/50">
                <div class="flex gap-4 h-full min-w-max">
                    <template x-for="col in [
                        { label: 'Pending Assessment', key: 'Pending Assessment', bg: 'bg-yellow-50', border: 'border-yellow-100', text: 'text-yellow-800' },
                        { label: 'In Progress', key: 'In Progress', bg: 'bg-blue-50', border: 'border-blue-100', text: 'text-blue-800' },
                        { label: 'Passed', key: 'Passed', bg: 'bg-green-50', border: 'border-green-100', text: 'text-green-800' },
                        { label: 'Failed', key: 'Failed', bg: 'bg-red-50', border: 'border-red-100', text: 'text-red-800' }
                    ]" :key="col.key">
                        <div class="w-80 flex flex-col shrink-0">
                            {{-- Column Header --}}
                            <div :class="col.bg + ' ' + col.border" class="px-4 py-3 border rounded-t-xl flex items-center justify-between shrink-0 mb-2">
                                <h3 :class="col.text" class="font-bold text-[13px] uppercase tracking-wider" x-text="col.label"></h3>
                                <span class="bg-white/80 px-2 py-0.5 rounded-full text-[10px] font-black text-gray-500 shadow-sm border border-gray-100" x-text="data['Assessment'].filter(a => a.status === col.key).length"></span>
                            </div>
                            
                            {{-- Column Body --}}
                            <div 
                                class="flex-1 p-1 space-y-3 overflow-y-auto rounded-b-xl min-h-[500px] transition-colors"
                                :class="draggedItem ? 'bg-blue-50/30 border-2 border-dashed border-blue-200' : ''"
                                @dragover.prevent
                                @drop="onDrop(col.key)"
                            >
                                <template x-for="item in data['Assessment'].filter(a => a.status === col.key)" :key="item.name">
                                    <div 
                                        class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition group relative cursor-move"
                                        draggable="true"
                                        @dragstart="onDragStart(item)"
                                    >
                                        <div 
                                            class="absolute top-5 right-5 flex flex-col gap-0.5 opacity-20 group-hover:opacity-60 transition cursor-pointer hover:scale-110 active:scale-95 z-20 bg-gray-100 p-1 rounded-md"
                                            @click.stop="viewAssessment(item)"
                                        >
                                            <div class="flex gap-0.5"><span class="w-1 h-1 bg-gray-900 rounded-full"></span><span class="w-1 h-1 bg-gray-900 rounded-full"></span></div>
                                            <div class="flex gap-0.5"><span class="w-1 h-1 bg-gray-900 rounded-full"></span><span class="w-1 h-1 bg-gray-900 rounded-full"></span></div>
                                            <div class="flex gap-0.5"><span class="w-1 h-1 bg-gray-900 rounded-full"></span><span class="w-1 h-1 bg-gray-900 rounded-full"></span></div>
                                        </div>

                                        <button 
                                            class="absolute top-5 right-12 opacity-0 group-hover:opacity-100 transition-all hover:text-red-600 text-gray-400 p-1 z-20"
                                            @click.stop="deleteAssessment(item.id)"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>

                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100 shrink-0">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-[15px] tracking-tight truncate pr-6" x-text="item.name"></h4>
                                            </div>

                                            <div class="space-y-2.5 ml-1">
                                                <div class="flex items-center gap-3 text-gray-500">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    <span class="text-[12px] font-medium" x-text="item.position"></span>
                                                </div>
                                                <div class="flex items-center gap-3 text-gray-500">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                                                    <span class="text-[12px] font-medium" x-text="item.test_type || item.test"></span>
                                                </div>
                                                <div class="flex items-center gap-3 text-gray-500">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[12px] font-medium" x-text="item.assessment_date || item.date"></span>
                                                </div>
                                                <div x-show="item.score" class="flex items-center gap-2.5 bg-gray-50 px-2.5 py-1.5 rounded-lg w-fit border border-gray-100">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                    <span class="text-[12px] font-black text-gray-700 uppercase" x-text="'Score: ' + item.score"></span>
                                                </div>
                                        </div>

                                        <div x-show="item.status === 'Passed'" class="mt-4 pt-4 border-t border-gray-50 flex justify-end">
                                            <button @click.stop="scheduleInterviewFromAssessment(item)" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold px-4 py-2 rounded-xl transition active:scale-95 shadow-lg shadow-blue-100 uppercase tracking-[0.1em] flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                                Interview
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- INTERVIEW TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Interview'">
                <thead class="bg-white text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Type</th>
                        <th class="px-4 py-3 text-left font-semibold">Interviewer</th>
                        <th class="px-4 py-3 text-left font-semibold">Date & Time</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="7" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in paginatedRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.type || row.round"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.interviewer"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.interview_date || row.date"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3">
                                <button @click="viewInterview(row)" class="text-xs text-blue-600 hover:underline mr-2">View</button>
                                <button @click="deleteInterview(row.id)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- JOB OFFER TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Job Offer'">
                <thead class="bg-white text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Salary</th>
                        <th class="px-4 py-3 text-left font-semibold">Start Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
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
                            <td class="px-4 py-3 text-gray-500" x-text="row.startDate || row.start_date"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-left whitespace-nowrap">
                                <button @click="viewJobOffer(row)" class="text-xs text-blue-600 hover:underline mr-2">View</button>
                                <button @click="deleteJobOffer(row.id)" class="text-xs text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>

        {{-- PAGINATION FOOTER --}}
        <div class="px-4 py-2 border-t border-gray-100 bg-blue-50/30 flex items-center justify-end text-[13px] font-semibold text-blue-600 gap-4" x-show="activeTab !== 'Assessment'">
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
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest" x-text="isEditing ? 'Edit Manpower Request' : 'New Manpower Request'"></h2>
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

    {{-- ===================== MRF VIEW SLIDE-OVER ===================== --}}
    <div x-show="showViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 w-[95vw] flex pointer-events-none">
            <div x-show="showViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col overflow-hidden pointer-events-auto">
                <div class="flex items-center justify-between px-8 py-5 border-b bg-gray-50 flex-shrink-0">
                    <h2 class="text-lg font-bold text-gray-800 tracking-widest uppercase">Manpower Request Form Details</h2>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 pr-6 border-r border-gray-300">
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Document Format: A4</span>
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
            <div class="flex justify-end gap-4 px-10 py-6 border-t bg-white shrink-0">
                <button @click="showViewModal = false" class="px-8 py-3 text-base border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition-all">Cancel</button>
                <button @click="approveMRF(viewData.id); showViewModal = false" class="px-10 py-3 text-base bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-lg shadow-green-200 transition-all transform hover:scale-[1.02] active:scale-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve
                </button>
            </div>
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
            class="bg-gray-100 shadow-2xl w-[95vw] h-full flex flex-col overflow-hidden"
        >
            {{-- Top bar --}}
            <div class="flex items-center justify-between px-6 py-3 bg-white border-b shrink-0">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest" x-text="isEditing ? 'Edit Job Posting (JPF)' : 'Create Job Posting (JPF)'"></h2>
                <button @click="showJpfModal = false" class="text-gray-400 hover:text-gray-600 transition">
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
                    <form @submit.prevent="submitJPF()" class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Position / Title</label>
                                <select x-model="jpfForm.position" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 bg-white font-medium transition-all text-sm">
                                    <option value="" disabled>Select Position...</option>
                                    <template x-for="pos in uniqueMrfPositions" :key="pos">
                                        <option :value="pos" x-text="pos"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Employment Type</label>
                                <select x-model="jpfForm.employmentType" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 bg-white font-medium transition-all text-sm">
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Internship">Internship</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Work Location</label>
                                <input type="text" x-model="jpfForm.location" required placeholder="e.g. Remote, On-site, Head Office"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Salary Range</label>
                                <input type="text" x-model="jpfForm.salaryRange" placeholder="e.g., $80,000 - $100,000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 transition-all text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detailed Job Description</label>
                            <textarea x-model="jpfForm.jobDescription" required rows="5" placeholder="Describe the role and responsibilities..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 resize-none bg-gray-50 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Candidate Requirements</label>
                            <textarea x-model="jpfForm.requirements" required rows="5" placeholder="List skills, education, and experience..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 resize-none bg-gray-50 text-sm"></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                            <button type="button" @click="showJpfModal = false"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Discard
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition" x-text="isEditing ? 'Update Job Posting' : 'Create Job Posting'">
                            </button>
                        </div>
                    </form>
                </div>

                {{-- LEFT: LIVE JPF DOCUMENT PREVIEW --}}
                <div class="flex-1 bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden">
                    <div class="px-5 py-3 border-b bg-gray-50 rounded-t-xl shrink-0 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live Preview</p>
                        <div class="flex items-center gap-2">

                            <button type="button" @click="downloadPDF('jpf-doc-create')" class="text-xs px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded text-gray-700 font-semibold flex items-center gap-1 transition shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-5 bg-gray-100">
                        <div id="jpf-doc-create" class="border border-gray-200 bg-white shadow-sm p-8 mx-auto w-[794px] shrink-0 font-sans">
                            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                                <div>
                                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight" x-text="jpfForm.position || 'Position Title'"></h1>
                                    <p class="text-lg text-blue-600 font-medium mt-1">John Kelly & Company</p>
                                </div>
                                <img src="{{ asset('images/imaglogo.png') }}" onerror="this.src='{{ asset('images/imag1logo.jpg') }}'" alt="Company Logo" class="h-16 w-auto object-contain mix-blend-multiply">
                            </div>
                            
                            <div class="grid grid-cols-3 gap-6 py-6 border-b border-gray-200 bg-gray-50/50 -mx-8 px-8 mb-6">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Employment Type</p>
                                    <p class="font-bold text-gray-800 text-sm" x-text="jpfForm.employmentType || '—'"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Work Location</p>
                                    <p class="font-bold text-gray-800 text-sm" x-text="jpfForm.location || '—'"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Salary Range</p>
                                    <p class="font-bold text-gray-800 text-sm" x-text="jpfForm.salaryRange || '—'"></p>
                                </div>
                            </div>
                            
                            <div class="space-y-8">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-blue-600 pl-3 mb-4">Job Description</h3>
                                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm ml-4" x-text="jpfForm.jobDescription || 'Please describe the role and responsibilities...' "></div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-indigo-600 pl-3 mb-4">Requirements</h3>
                                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm ml-4" x-text="jpfForm.requirements || 'Please list skills, education, and experience...' "></div>
                                </div>
                            </div>
                            
                            <div class="mt-12 pt-6 border-t border-gray-200 text-center">
                                <p class="text-sm font-semibold text-gray-800 mb-2">How to Apply</p>
                                <p class="text-sm text-gray-600">Please submit your resume and cover letter to <a href="mailto:careers@johnkellyandcompany.com" class="text-blue-600 hover:underline">careers@johnkellyandcompany.com</a></p>
                                <p class="text-xs text-gray-400 mt-6">John Kelly & Company is an Equal Opportunity Employer</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end split body --}}
        </div>
    </div>

    {{-- ===================== JPF VIEW SLIDE-OVER ===================== --}}
    <div x-show="showJpfViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showJpfViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showJpfViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 w-[95vw] flex pointer-events-none">
            <div x-show="showJpfViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col overflow-hidden pointer-events-auto">
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

    {{-- ===================== CAF MODAL ===================== --}}
    <div
        x-show="showCafModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showCafModal = false"
    >
        <div
            x-show="showCafModal"
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
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest" x-text="isEditing ? 'Edit Application (CAF)' : 'Candidate Application Form (CAF)'"></h2>
                <button @click="showCafModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Split body --}}
            <div class="flex flex-1 overflow-hidden gap-4 p-4">

                {{-- RIGHT: INPUT FORM --}}
                <div class="w-[42%] bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden shrink-0 order-last">
                    <div class="px-5 py-3 border-b bg-blue-700 rounded-t-xl">
                        <p class="text-xs font-bold text-white uppercase tracking-wider">Applicant Information</p>
                    </div>
                    <form @submit.prevent="submitCAF()" class="flex-1 overflow-y-auto px-5 py-5 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                                <input type="text" x-model="cafForm.fullName" required placeholder="Enter full name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Position Applied</label>
                                <select x-model="cafForm.positionApplied" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-white cursor-pointer shadow-sm">
                                    <option value="">Select Position</option>
                                    <template x-for="pos in uniqueJpfPositions" :key="pos">
                                        <option :value="pos" x-text="pos"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                                <input type="email" x-model="cafForm.email" required placeholder="email@example.com"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                                <input type="text" x-model="cafForm.phone" required placeholder="+63 9xx xxx xxxx"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Resume / CV</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg x-show="!cafForm.cv" class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <svg x-show="cafForm.cv" class="w-8 h-8 mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-gray-500" x-text="cafForm.cv ? cafForm.cv.name : 'Click to upload or drag and drop'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase" x-show="!cafForm.cv">PDF, DOCX up to 10MB</p>
                                </div>
                                <input type="file" class="hidden" @change="cafForm.cv = $event.target.files[0]" />
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cover Letter</label>
                            <textarea x-model="cafForm.coverLetter" rows="8" placeholder="Tell us why you're a great fit..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 resize-none bg-gray-50 text-sm"></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-5 border-t border-gray-100">
                            <button type="button" @click="showCafModal = false"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                Discard
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md shadow-blue-100 transition-all active:scale-95" x-text="isEditing ? 'Update Application' : 'Submit Application'">
                            </button>
                        </div>
                    </form>
                </div>

                {{-- LEFT: LIVE CAF DOCUMENT PREVIEW --}}
                <div class="flex-1 bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden">
                    <div class="px-5 py-3 border-b bg-gray-50 rounded-t-xl shrink-0 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live Preview</p>
                        <div class="flex items-center gap-2">

                            <button type="button" @click="downloadPDF('caf-doc-create')" class="text-xs px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded text-gray-700 font-semibold flex items-center gap-1 transition shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-8 bg-gray-100/50">
                        <div id="caf-doc-create" class="border border-gray-200 bg-white shadow-xl p-12 mx-auto w-[794px] shrink-0 font-sans min-h-[1000px]">
                            <div class="flex justify-between items-start border-b-2 border-gray-800 pb-8 mb-8">
                                <div>
                                    <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase mb-2" x-text="cafForm.fullName || 'Candidate Name'"></h1>
                                    <p class="text-xl text-blue-600 font-bold uppercase tracking-widest" x-text="cafForm.positionApplied || 'Position Title'"></p>
                                </div>
                                <div class="text-right">
                                    <img src="{{ asset('images/imaglogo.png') }}" onerror="this.src='{{ asset('images/imag1logo.jpg') }}'" alt="Logo" class="h-16 w-auto object-contain ml-auto mb-4">
                                    <div class="text-xs font-bold text-gray-500 space-y-1">
                                        <p x-text="cafForm.email || 'email@example.com'"></p>
                                        <p x-text="cafForm.phone || '+63 9xx xxx xxxx'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-10">
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="h-0.5 flex-1 bg-gray-200"></div>
                                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Application Details</h3>
                                        <div class="h-0.5 flex-1 bg-gray-200"></div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-8 text-sm px-4">
                                        <div class="space-y-1">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Application Date</p>
                                            <p class="font-bold text-gray-800" x-text="new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })"></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Resume Attachment</p>
                                            <p class="font-bold" :class="cafForm.cv ? 'text-green-600' : 'text-red-500'" x-text="cafForm.cv ? cafForm.cv.name : 'No file uploaded'"></p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="h-0.5 flex-1 bg-gray-200"></div>
                                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Cover Letter</h3>
                                        <div class="h-0.5 flex-1 bg-gray-200"></div>
                                    </div>
                                    
                                    <div class="bg-gray-50/50 p-6 rounded-xl border border-gray-100 min-h-[400px]">
                                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm" x-text="cafForm.coverLetter || 'Your cover letter content will appear here...'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-20 pt-8 border-t border-gray-100 flex justify-between items-center opacity-50">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Internal Candidate Record</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">John Kelly & Company</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end split body --}}
        </div>
    </div>

    {{-- ===================== ASSESSMENT VIEW SLIDE-OVER ===================== --}}
    <div x-show="showAssessmentViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showAssessmentViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showAssessmentViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 max-w-xl w-full flex pointer-events-none">
            <div x-show="showAssessmentViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col pointer-events-auto">
                <template x-if="viewAssessmentData">
                    <div class="flex flex-col h-full">
                        {{-- Header Banner --}}
                        <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700 p-8 relative shrink-0">
                            <button @click="showAssessmentViewModal = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition group bg-white/10 p-2 rounded-full backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="absolute -bottom-10 left-8">
                                <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center border-4 border-white">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 overflow-y-auto px-8 pt-16 pb-8 space-y-8">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight" x-text="viewAssessmentData.name"></h2>
                                <p class="text-blue-600 font-bold tracking-widest uppercase text-xs mt-1" x-text="viewAssessmentData.position"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Assessment Type</p>
                                    <p class="font-bold text-gray-800" x-text="viewAssessmentData.test"></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                                    <p class="font-bold uppercase text-[11px]" :class="statusClass(viewAssessmentData.status)" x-text="viewAssessmentData.status"></p>
                                </div>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Assessment Date</p>
                                            <p class="font-bold text-gray-800" x-text="viewAssessmentData.date"></p>
                                        </div>
                                    </div>
                                    <div x-show="viewAssessmentData.score" class="text-right">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Calculated Score</p>
                                        <p class="text-xl font-black text-indigo-600" x-text="viewAssessmentData.score"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-6 border-t border-gray-100 flex gap-3 shrink-0">
                            <button @click="showAssessmentViewModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition uppercase tracking-widest text-[11px]">
                                Close
                            </button>
                            <button class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200 uppercase tracking-widest text-[11px]">
                                Print Results
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ===================== CAF VIEW SLIDE-OVER ===================== --}}
    <div x-show="showCafViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showCafViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showCafViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 max-w-xl w-full flex pointer-events-none">
            <div x-show="showCafViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col pointer-events-auto">
                <template x-if="viewCafData">
                    <div class="flex flex-col h-full">
                        <div class="h-32 bg-gradient-to-r from-teal-500 to-emerald-600 p-8 relative shrink-0">
                            <button @click="showCafViewModal = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition group bg-white/10 p-2 rounded-full backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <div class="absolute -bottom-10 left-8">
                                <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center border-4 border-white text-teal-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-8 pt-16 pb-8 space-y-8">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight" x-text="viewCafData.name"></h2>
                                <p class="text-teal-600 font-bold tracking-widest uppercase text-xs mt-1" x-text="viewCafData.position"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 font-medium">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email Address</p>
                                    <p class="text-gray-800 break-all text-sm" x-text="viewCafData.email"></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 font-medium">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Phone Number</p>
                                    <p class="text-gray-800 text-sm" x-text="viewCafData.phone"></p>
                                </div>
                            </div>

                            <div x-show="viewCafData.cover_letter" class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Cover Letter / Message</p>
                                <div class="text-sm text-gray-700 leading-relaxed italic" x-text="viewCafData.cover_letter"></div>
                            </div>
                        </div>

                        <div class="px-8 py-6 border-t border-gray-100 flex gap-3 shrink-0">
                            <button @click="showCafViewModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition uppercase tracking-widest text-[11px]">
                                Close
                            </button>
                            <a :href="'/storage/' + viewCafData.cv_path" target="_blank" x-show="viewCafData.cv_path" 
                                class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition text-center shadow-lg shadow-teal-100 uppercase tracking-widest text-[11px] flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                                Download CV
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ===================== INTERVIEW VIEW SLIDE-OVER ===================== --}}
    <div x-show="showInterviewViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showInterviewViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showInterviewViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 max-w-xl w-full flex pointer-events-none">
            <div x-show="showInterviewViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col pointer-events-auto">
                <template x-if="viewInterviewData">
                    <div class="flex flex-col h-full">
                        {{-- Aesthetic Header --}}
                        <div class="h-32 bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 relative">
                            <div class="absolute inset-0 opacity-10">
                                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path></svg>
                            </div>
                            <div class="relative text-center">
                                <div class="inline-flex p-3 bg-white/20 backdrop-blur-md rounded-2xl mb-2 border border-white/30">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="text-white font-black text-xl tracking-tight uppercase">Interview Details</h3>
                            </div>
                            <button @click="showInterviewViewModal = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition group bg-white/10 p-2 rounded-full backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-white">
                            <div class="grid grid-cols-2 gap-8">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Candidate Name</p>
                                    <p class="text-gray-900 font-bold text-lg" x-text="viewInterviewData.name"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Position</p>
                                    <p class="text-purple-600 font-bold text-lg" x-text="viewInterviewData.position"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 pt-6 border-t border-gray-50 text-sm">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Interview Type</p>
                                    <p class="text-gray-700 font-medium" x-text="viewInterviewData.type || viewInterviewData.round"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</p>
                                    <p class="font-bold uppercase text-[11px]" :class="statusClass(viewInterviewData.status)" x-text="viewInterviewData.status"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 pt-6 border-t border-gray-50 text-sm">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Interviewer</p>
                                    <p class="text-gray-700 font-medium" x-text="viewInterviewData.interviewer"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Duration</p>
                                    <p class="text-gray-700 font-medium" x-text="(viewInterviewData.duration || '60') + ' minutes'"></p>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 font-medium">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Date & Time</p>
                                <p class="text-gray-800 text-sm" x-text="viewInterviewData.interview_date || viewInterviewData.date"></p>
                            </div>

                            <div x-show="viewInterviewData.meeting_link" class="space-y-2 pt-6 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meeting Link</p>
                                <a :href="viewInterviewData.meeting_link" target="_blank" class="text-blue-600 hover:underline font-medium break-all text-sm block bg-blue-50/50 p-4 rounded-2xl border border-blue-100" x-text="viewInterviewData.meeting_link"></a>
                            </div>
                        </div>

                        <div class="px-8 py-6 border-t border-gray-100 flex gap-4 shrink-0">
                            <button @click="showInterviewViewModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition uppercase tracking-widest text-[11px]">
                                Close
                            </button>
                            <button @click="openJobOfferModal(viewInterviewData)" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition shadow-lg shadow-purple-200 uppercase tracking-widest text-[11px]"
                                x-text="isDatePassed(viewInterviewData.interview_date || viewInterviewData.date) ? 'Send a Job Offer' : 'Send Reminder'">
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ===================== ADD NEW JOB OFFER MODAL ===================== --}}
    <div
        x-show="showJobOfferModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] flex justify-center items-center bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showJobOfferModal = false"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all"
            x-show="showJobOfferModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        >
            <div class="px-6 py-4 border-b flex items-center justify-between bg-white text-gray-800">
                <h3 class="font-bold text-lg tracking-tight">Add New Job Offer</h3>
                <button @click="showJobOfferModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitJobOffer()" class="p-6 space-y-5 bg-white">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Candidate Name</label>
                        <input type="text" x-model="jobOfferForm.name" required placeholder="Enter name"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Position</label>
                        <input type="text" x-model="jobOfferForm.position" required placeholder="Position title"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Salary</label>
                        <input type="text" x-model="jobOfferForm.salary" placeholder="$100,000"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Start Date</label>
                        <input type="date" x-model="jobOfferForm.startDate" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Employment Type</label>
                        <select x-model="jobOfferForm.employmentType" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all cursor-pointer appearance-none">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Project-based">Project-based</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Department</label>
                        <input type="text" x-model="jobOfferForm.department" placeholder="e.g. Engineering"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Benefits</label>
                    <textarea x-model="jobOfferForm.benefits" rows="4" placeholder="Health insurance, 401k, etc..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 resize-none"></textarea>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" @click="showJobOfferModal = false"
                        class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-100 transition active:scale-95">
                        Create Job Offer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== VIEW JOB OFFER SLIDE-OVER ===================== --}}
    <div x-show="showJobOfferViewModal" class="fixed inset-0 overflow-hidden z-[9999]" style="display:none;">
        <div @click="showJobOfferViewModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
            x-show="showJobOfferViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 max-w-xl w-full flex pointer-events-none">
            <div x-show="showJobOfferViewModal" 
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="h-full w-full bg-white shadow-2xl flex flex-col pointer-events-auto">
                <template x-if="viewJobOfferData">
                    <div class="flex flex-col h-full">
                        {{-- Aesthetic Header --}}
                        <div class="h-32 bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 relative">
                            <div class="absolute inset-0 opacity-10">
                                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path></svg>
                            </div>
                            <div class="relative text-center">
                                <div class="inline-flex p-3 bg-white/20 backdrop-blur-md rounded-2xl mb-2 border border-white/30">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-white font-black text-xl tracking-tight uppercase">Job Offer Details</h3>
                            </div>
                            <button @click="showJobOfferViewModal = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition group bg-white/10 p-2 rounded-full backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-8 pt-8 pb-8 space-y-8 bg-white">
                            <div class="grid grid-cols-2 gap-8">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Candidate Name</p>
                                    <p class="text-gray-900 font-bold text-lg" x-text="viewJobOfferData.name"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Position</p>
                                    <p class="text-blue-600 font-bold text-lg" x-text="viewJobOfferData.position"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 pt-6 border-t border-gray-50 text-sm">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Salary</p>
                                    <p class="text-gray-700 font-medium" x-text="viewJobOfferData.salary"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Start Date</p>
                                    <p class="text-gray-700 font-medium" x-text="viewJobOfferData.startDate || viewJobOfferData.start_date"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 pt-6 border-t border-gray-50 text-sm">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Employment Type</p>
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg font-bold text-[11px] uppercase tracking-wider border border-blue-100 w-fit" x-text="viewJobOfferData.employment_type || viewJobOfferData.employmentType"></span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Department</p>
                                    <p class="text-gray-700 font-medium" x-text="viewJobOfferData.department || 'N/A'"></p>
                                </div>
                            </div>

                            <div class="space-y-2 pt-6 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Benefits</p>
                                <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                    <p class="text-gray-600 text-[13px] leading-relaxed italic" x-text="viewJobOfferData.benefits || 'No benefits listed.'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-6 border-t border-gray-100 flex gap-4 shrink-0">
                            <button @click="showJobOfferViewModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition uppercase tracking-widest text-[11px]">
                                Close
                            </button>
                            <button class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-200 uppercase tracking-widest text-[11px]">
                                Send Email
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ===================== ADD NEW ASSESSMENT MODAL ===================== --}}
    <div
        x-show="showAssessmentModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] flex justify-center items-center bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showAssessmentModal = false"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all"
            x-show="showAssessmentModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        >
            <div class="px-6 py-4 border-b flex items-center justify-between bg-white text-gray-800">
                <h3 class="font-bold text-lg tracking-tight">Add New Assessment</h3>
                <button @click="showAssessmentModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitAssessment()" class="p-6 space-y-5 bg-white">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Candidate Name</label>
                        <input type="text" x-model="assessmentForm.name" required placeholder="Enter name"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Position</label>
                        <select x-model="assessmentForm.position" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50 cursor-pointer appearance-none">
                            <option value="">Select Position</option>
                            <template x-for="pos in uniqueCafPositions" :key="pos">
                                <option :value="pos" x-text="pos"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Test Type</label>
                        <select x-model="assessmentForm.test" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all cursor-pointer appearance-none">
                            <option value="Technical Test">Technical Test</option>
                            <option value="Amplitude Test">Amplitude Test</option>
                            <option value="Personality Test">Personality Test</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Assessment Date</label>
                        <input type="date" x-model="assessmentForm.date" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Notes</label>
                    <textarea x-model="assessmentForm.notes" rows="4" placeholder="Assessment notes..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 resize-none"></textarea>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" @click="showAssessmentModal = false"
                        class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Discard
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-100 transition active:scale-95">
                        Create Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== ADD NEW INTERVIEW MODAL ===================== --}}
    <div
        x-show="showInterviewModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] flex justify-center items-center bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showInterviewModal = false"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all"
            x-show="showInterviewModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        >
            <div class="px-6 py-4 border-b flex items-center justify-between bg-white text-gray-800">
                <h3 class="font-bold text-lg tracking-tight">Add New Interview</h3>
                <button @click="showInterviewModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitInterview()" class="p-6 space-y-5 bg-white">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Candidate Name</label>
                        <select x-model="interviewForm.name" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50 cursor-pointer appearance-none">
                            <option value="">Select Candidate</option>
                            <template x-for="name in uniqueCafNames" :key="name">
                                <option :value="name" x-text="name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Position</label>
                        <select x-model="interviewForm.position" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm transition-all bg-gray-50/50 cursor-pointer appearance-none">
                            <option value="">Select Position</option>
                            <template x-for="pos in uniqueCafPositions" :key="pos">
                                <option :value="pos" x-text="pos"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Type of Interview</label>
                        <select x-model="interviewForm.type" required
                            @change="interviewForm.type === 'Online' ? generateMeetingLink() : interviewForm.meeting_link = ''"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all cursor-pointer appearance-none">
                            <option value="Online">Online</option>
                            <option value="In Person">In Person</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Interviewer</label>
                        <input type="text" x-model="interviewForm.interviewer" required placeholder="Name of interviewer"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Date & Time</label>
                        <input type="datetime-local" x-model="interviewForm.interview_date" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Duration (minutes)</label>
                        <input type="number" x-model="interviewForm.duration" required placeholder="60"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 transition-all">
                    </div>
                </div>

                <div x-show="interviewForm.type === 'Online'">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Meeting Link</label>
                    <div class="flex gap-2 items-center">
                        <input type="url" x-model="interviewForm.meeting_link" placeholder="https://meet.jit.si/..."
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-gray-700 text-sm bg-gray-50/50 font-mono text-xs" readonly>
                        <button type="button" @click="generateMeetingLink()"
                            class="flex items-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-bold rounded-xl transition shadow-sm shadow-blue-200 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Generate
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5">Mock link — replace with a real meeting URL before sending.</p>
                </div>

                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" @click="showInterviewModal = false"
                        class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-100 transition active:scale-95">
                        Schedule Interview
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function recruitmentPage(initialMRF = [], initialJPF = [], initialCAF = [], initialAssessment = [], initialInterview = [], initialJobOffer = []) {
    return {
        activeTab: 'MRF',
        search: '',
        paperSize: 'a4',
        perPage: 50,
        currentPage: 1,
        showFilter: false,
        filterStatus: 'All',
        showModal: false,
        showViewModal: false,
        showJpfModal: false,
        showJpfViewModal: false,
        showCafModal: false,
        showCafViewModal: false,
        showInterviewModal: false,
        showInterviewViewModal: false,
        showAssessmentModal: false,
        showAssessmentViewModal: false,
        showJobOfferModal: false,
        showJobOfferViewModal: false,
        isEditing: false,
        editingId: null,
        viewData: null,
        viewJpfData: {},
        viewCafData: null,
        viewAssessmentData: null,
        viewInterviewData: null,
        viewJobOfferData: null,

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
            'CAF':        initialCAF,
            'Assessment': initialAssessment,
            'Interview':  initialInterview,
            'Job Offer':  initialJobOffer,
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

        cafForm: {
            fullName: '', positionApplied: '', email: '', phone: '', cv: null, coverLetter: ''
        },

        assessmentForm: {
            name: '', position: '', test: 'Technical Test', date: '', notes: ''
        },

        interviewForm: {
            name: '', position: '', type: 'Online', interviewer: '', 
            interview_date: '', duration: '60', meeting_link: ''
        },

        jobOfferForm: {
            name: '', position: '', salary: '', startDate: '',
            employmentType: 'Full-time', department: '', benefits: ''
        },

        draggedItem: null,
        onDragStart(item) { this.draggedItem = item; },
        onDrop(status) {
            if (this.draggedItem) {
                const oldStatus = this.draggedItem.status;
                this.draggedItem.status = status;
                
                axios.post(`/human-capital/recruitment/assessment/${this.draggedItem.id}/status`, { status: status })
                .catch(err => {
                    this.draggedItem.status = oldStatus;
                    console.error('Failed to update status:', err);
                });
                
                this.draggedItem = null;
            }
        },

        viewAssessment(item) {
            this.viewAssessmentData = item;
            this.showAssessmentViewModal = true;
        },

        deleteAssessment(id) {
            if (!confirm('Are you sure you want to delete this assessment?')) return;
            
            axios.delete(`/human-capital/recruitment/assessment/${id}`)
            .then(() => {
                this.data['Assessment'] = this.data['Assessment'].filter(a => a.id !== id);
            })
            .catch(err => console.error('Error deleting assessment:', err));
        },

        viewCAF(row) {
            this.viewCafData = row;
            this.showCafViewModal = true;
        },

        viewInterview(row) {
            this.viewInterviewData = row;
            this.showInterviewViewModal = true;
        },

        deleteCAF(id) {
            if (!confirm('Are you sure you want to delete this application?')) return;
            
            axios.delete(`/human-capital/recruitment/caf/${id}`)
            .then(() => {
                this.data['CAF'] = this.data['CAF'].filter(c => c.id !== id);
            })
            .catch(err => console.error('Error deleting CAF:', err));
        },

        submitAssessment() {
            axios.post('{{ route("human-capital.recruitment.store_assessment") }}', this.assessmentForm)
            .then(res => {
                this.data['Assessment'].unshift(res.data.data);
                this.showAssessmentModal = false;
            })
            .catch(err => console.error('Error submitting assessment:', err));
        },

        scheduleInterviewFromAssessment(item) {
            // Pre-fill interview form and switch to Interview tab
            this.interviewForm = {
                name: item.name,
                position: item.position,
                type: 'Online',
                interviewer: '', 
                interview_date: '',
                duration: '60',
                meeting_link: ''
            };
            this.generateMeetingLink();
            this.activeTab = 'Interview';
            this.showInterviewModal = true;
        },

        generateMeetingLink() {
            const seg = (len) => Array.from({length: len}, () => 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]).join('');
            this.interviewForm.meeting_link = `https://meet.google.com/${seg(3)}-${seg(4)}-${seg(3)}`;
        },

        submitInterview() {
            axios.post('{{ route("human-capital.recruitment.store_interview") }}', this.interviewForm)
            .then(res => {
                this.data['Interview'].unshift(res.data.data);
                this.showInterviewModal = false;
            })
            .catch(err => {
                alert('Error scheduling interview: ' + (err.response?.data?.message || err.message));
            });
        },

        deleteInterview(id) {
            if (!confirm('Are you sure you want to delete this interview record?')) return;
            
            axios.delete(`/human-capital/recruitment/interview/${id}`)
            .then(() => {
                this.data['Interview'] = this.data['Interview'].filter(i => i.id !== id);
            })
            .catch(err => console.error('Error deleting interview:', err));
        },

        submitJobOffer() {
            axios.post('{{ route("human-capital.recruitment.store_job_offer") }}', this.jobOfferForm)
            .then(res => {
                this.data['Job Offer'].unshift(res.data.data);
                this.showJobOfferModal = false;
            })
            .catch(err => {
                alert('Error saving Job Offer: ' + (err.response?.data?.message || err.message));
            });
        },

        deleteJobOffer(id) {
            if (!confirm('Are you sure you want to delete this job offer?')) return;
            axios.delete(`/human-capital/recruitment/job-offer/${id}`)
            .then(() => {
                this.data['Job Offer'] = this.data['Job Offer'].filter(j => j.id !== id);
            })
            .catch(err => console.error('Error deleting job offer:', err));
        },

        openJobOfferModal(interviewData = null) {
            if (interviewData) {
                this.jobOfferForm = {
                    name: interviewData.name,
                    position: interviewData.position,
                    salary: '',
                    startDate: '',
                    employmentType: 'Full-time',
                    department: '',
                    benefits: ''
                };
            } else {
                this.jobOfferForm = {
                    name: '', position: '', salary: '', startDate: '',
                    employmentType: 'Full-time', department: '', benefits: ''
                };
            }
            this.showJobOfferModal = true;
        },

        viewJobOffer(offer) {
            this.viewJobOfferData = offer;
            this.showJobOfferViewModal = true;
        },

        editMRF(row) {
            this.isEditing = true;
            this.editingId = row.id || row.request_id;
            this.form = {
                department: row.department,
                dateRequested: row.date_requested,
                dateRequired: row.date_required,
                position: row.position,
                employmentType: row.employment_type,
                duties: row.duties,
                natureOfRequest: row.nature_of_request,
                ageRange: row.age_range,
                civilStatus: row.civil_status,
                gender: row.gender,
                headcount: row.headcount,
                education: row.education,
                qualifications: row.qualifications,
                requestedBy: row.requested_by,
                approvedBy: row.approved_by,
                remarks: row.remarks,
                requestStatus: row.request_status,
                chargedTo: row.charged_to,
                breakdownDetails: row.breakdown_details,
                hiredPersonnel: row.hired_personnel,
                dateHired: row.date_hired,
                processedBy: row.processed_by,
                checkedBy: row.checked_by,
            };
            this.showModal = true;
        },

        editJPF(row) {
            this.isEditing = true;
            this.editingId = row.id || row.job_id;
            this.jpfForm = {
                position: row.position,
                employmentType: row.employment_type,
                location: row.location,
                salaryRange: row.salary_range,
                jobDescription: row.job_description,
                requirements: row.requirements,
                status: row.status
            };
            this.showJpfModal = true;
        },

        editCAF(row) {
            this.isEditing = true;
            this.editingId = row.id;
            this.cafForm = {
                fullName: row.name,
                positionApplied: row.position,
                email: row.email,
                phone: row.phone,
                cv: null,
                coverLetter: row.cover_letter,
                status: row.status
            };
            this.showCafModal = true;
        },

        openModal() {
            this.isEditing = false;
            this.editingId = null;
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
            } else if (this.activeTab === 'CAF') {
                this.cafForm = {
                    fullName: '', positionApplied: '', email: '', phone: '', cv: null, coverLetter: ''
                };
                this.showCafModal = true;
            } else if (this.activeTab === 'Interview') {
                this.interviewForm = {
                    name: '', position: '', type: 'Online', interviewer: '', 
                    interview_date: '', duration: '60', meeting_link: ''
                };
                this.generateMeetingLink();
                this.showInterviewModal = true;
            } else if (this.activeTab === 'Assessment') {
                this.assessmentForm = {
                    name: '', position: '', test: 'Technical Test', date: '', notes: ''
                };
                this.showAssessmentModal = true;
            } else if (this.activeTab === 'Job Offer') {
                this.openJobOfferModal();
            } else {
                alert('Add New functionality for ' + this.activeTab + ' is not yet implemented.');
            }
        },

        submitMRF() {
            const url = this.isEditing ? `/human-capital/recruitment/mrf/${this.editingId}` : '{{ route("human-capital.recruitment.store_mrf") }}';
            const method = this.isEditing ? 'put' : 'post';
            
            axios({
                method: method,
                url: url,
                data: {
                    ...this.form,
                    request_status: this.form.requestStatus || 'Pending'
                }
            })
            .then(res => {
                const item = res.data.data;
                if (this.isEditing) {
                    const idx = this.data['MRF'].findIndex(m => (m.id || m.request_id) === this.editingId);
                    if (idx !== -1) {
                        this.data['MRF'][idx] = {
                            ...item,
                            id: item.request_id,
                            date: item.date_requested
                        };
                    }
                } else {
                    this.data['MRF'].unshift({
                        ...item,
                        id: item.request_id,
                        date: item.date_requested
                    });
                }
                this.showModal = false;
            })
            .catch(err => {
                alert('Error saving MRF: ' + (err.response?.data?.message || err.message));
            });
        },

        submitJPF() {
            const url = this.isEditing ? `/human-capital/recruitment/jpf/${this.editingId}` : '{{ route("human-capital.recruitment.store_jpf") }}';
            const method = this.isEditing ? 'put' : 'post';

            axios({
                method: method,
                url: url,
                data: {
                    ...this.jpfForm,
                    posted_date: new Date().toISOString().split('T')[0]
                }
            })
            .then(res => {
                const item = res.data.data;
                if (this.isEditing) {
                    const idx = this.data['JPF'].findIndex(j => (j.id || j.job_id) === this.editingId);
                    if (idx !== -1) {
                        this.data['JPF'][idx] = {
                            ...item,
                            jobId: item.job_id,
                            type: item.employment_type,
                            posted: item.posted_date
                        };
                    }
                } else {
                    this.data['JPF'].unshift({
                        ...item,
                        jobId: item.job_id,
                        type: item.employment_type,
                        posted: item.posted_date
                    });
                }
                this.showJpfModal = false;
            })
            .catch(err => {
                alert('Error saving JPF: ' + (err.response?.data?.message || err.message));
            });
        },

        submitCAF() {
            let formData = new FormData();
            formData.append('fullName', this.cafForm.fullName);
            formData.append('positionApplied', this.cafForm.positionApplied);
            formData.append('email', this.cafForm.email);
            formData.append('phone', this.cafForm.phone);
            formData.append('coverLetter', this.cafForm.coverLetter);
            if (this.isEditing) {
                formData.append('status', this.cafForm.status);
                formData.append('_method', 'PUT');
            }
            if (this.cafForm.cv) {
                formData.append('cv', this.cafForm.cv);
            }

            const url = this.isEditing ? `/human-capital/recruitment/caf/${this.editingId}` : '{{ route("human-capital.recruitment.store_caf") }}';

            axios.post(url, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then(res => {
                const item = res.data.data;
                if (this.isEditing) {
                    const idx = this.data['CAF'].findIndex(c => c.id === this.editingId);
                    if (idx !== -1) {
                        this.data['CAF'][idx] = item;
                    }
                } else {
                    this.data['CAF'].unshift(item);
                }
                this.showCafModal = false;
            })
            .catch(err => console.error('Error submitting CAF:', err));
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

        get uniqueJpfPositions() {
            const positions = this.data['JPF'].map(jpf => jpf.position).filter(p => p && p.trim() !== '');
            return [...new Set(positions)];
        },

        get uniqueCafPositions() {
            const positions = this.data['CAF'].map(caf => caf.position).filter(p => p && p.trim() !== '');
            return [...new Set(positions)];
        },

        get uniqueCafNames() {
            const names = this.data['CAF'].map(caf => caf.name).filter(n => n && n.trim() !== '');
            return [...new Set(names)];
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
            
            let downloadFilename = 'Manpower_Request_Form.pdf';
            if (elementId.includes('jpf')) {
                downloadFilename = 'Job Placement form.pdf';
            } else if (elementId.includes('caf')) {
                downloadFilename = 'Candidate Application Form.pdf';
            }
            
            const opt = {
                margin:       0.3,
                filename:     downloadFilename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
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

        isDatePassed(dateStr) {
            if (!dateStr) return false;
            try {
                // Ensure date string is compatible (replace space with T for YYYY-MM-DD HH:MM:SS)
                const normalized = String(dateStr).trim().replace(' ', 'T');
                const interviewDate = new Date(normalized);
                
                if (isNaN(interviewDate.getTime())) {
                    console.warn('Invalid date format:', dateStr);
                    return false;
                }

                const now = new Date();
                const passed = interviewDate < now;
                
                // Debug log to help identify why it might not be changing
                console.log(`Checking Date: ${normalized} | Current: ${now.toISOString()} | Passed: ${passed}`);
                
                return passed;
            } catch (e) {
                console.error('Error in isDatePassed:', e);
                return false;
            }
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