@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col" x-data="recruitmentPage()">

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
            <input
                type="text"
                x-model="search"
                :placeholder="'Search ' + activeTab + '...'"
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white"
            >
        </div>

        <button type="button" class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M6 12h12M10 18h4"/>
            </svg>
            Filter
        </button>

        <button type="button" class="flex items-center gap-2 px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"/>
            </svg>
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
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-blue-600 font-medium" x-text="row.id"></td>
                            <td class="px-4 py-3 text-gray-800" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.department"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.headcount"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.date"></td>
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
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm" x-text="'No ' + activeTab + ' records found.'"></span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-blue-600 font-medium" x-text="row.jobId"></td>
                            <td class="px-4 py-3 text-gray-800" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.type"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.location"></td>
                            <td class="px-4 py-3"><span x-text="row.status" :class="statusClass(row.status)" class="px-2 py-0.5 rounded-full text-xs font-medium"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.posted"></td>
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
                    <template x-for="(row, i) in filteredRows" :key="i">
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
                    <template x-for="(row, i) in filteredRows" :key="i">
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
                    <template x-for="(row, i) in filteredRows" :key="i">
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
                    <template x-for="(row, i) in filteredRows" :key="i">
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
    </div>
</div>

@push('scripts')
<script>
function recruitmentPage() {
    return {
        activeTab: 'MRF',
        search: '',
        tabs: [
            { key: 'MRF',        label: 'MRF' },
            { key: 'JPF',        label: 'JPF' },
            { key: 'CAF',        label: 'CAF' },
            { key: 'Assessment', label: 'Assessment' },
            { key: 'Interview',  label: 'Interview' },
            { key: 'Job Offer',  label: 'Job Offer' },
        ],
        data: {
            'MRF':        [],
            'JPF':        [],
            'CAF':        [],
            'Assessment': [],
            'Interview':  [],
            'Job Offer':  [],
        },
        statusClass(status) {
            const map = {
                'Approved':    'bg-green-100 text-green-700',
                'Completed':   'bg-green-100 text-green-700',
                'Passed':      'bg-green-100 text-green-700',
                'Accepted':    'bg-green-100 text-green-700',
                'Open':        'bg-blue-100 text-blue-700',
                'Active':      'bg-blue-100 text-blue-700',
                'In Progress': 'bg-blue-100 text-blue-700',
                'Pending':     'bg-yellow-100 text-yellow-700',
                'For Review':  'bg-yellow-100 text-yellow-700',
                'Rejected':    'bg-red-100 text-red-700',
                'Failed':      'bg-red-100 text-red-700',
                'Declined':    'bg-red-100 text-red-700',
            };
            return map[status] ?? 'bg-gray-100 text-gray-600';
        },
        get filteredRows() {
            const rows = this.data[this.activeTab] ?? [];
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