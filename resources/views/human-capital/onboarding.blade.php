@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4 h-[calc(100vh-100px)] flex flex-col" x-data="onboardingPage()">

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

        <button type="button" class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
            Filter
        </button>

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

            {{-- PDS TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'PDS'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Phone</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Date Submitted</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="7" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm">No PDS records found. Click + Add New to create one.</span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.fullName"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.position"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.email"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.phone"></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Submitted</span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.submittedDate"></td>
                            <td class="px-4 py-3"><button @click="deletePds(i)" class="text-xs text-red-500 hover:underline">Delete</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- CHECKLIST TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Checklist Submission'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Docs Submitted</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Docs</th>
                        <th class="px-4 py-3 text-left font-semibold">Completion</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm">No checklist records found. Click + Add New to create one.</span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.employeeName"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.docsSubmitted"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.totalDocs"></td>
                            <td class="px-4 py-3">
                                <span :class="row.docsSubmitted === row.totalDocs ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="px-2 py-0.5 rounded-full text-xs font-medium" x-text="row.docsSubmitted === row.totalDocs ? 'Complete' : 'Incomplete'"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.submittedDate"></td>
                            <td class="px-4 py-3"><button @click="deleteChecklist(i)" class="text-xs text-red-500 hover:underline">Delete</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- EMPLOYEE REGISTRATION TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Employee Registration'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Full Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Employee ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Department</th>
                        <th class="px-4 py-3 text-left font-semibold">Start Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Work Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Manager</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="7" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm">No employee registrations found. Click + Add New to create one.</span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.fullName"></td>
                            <td class="px-4 py-3 text-blue-600 font-medium" x-text="row.employeeId"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.department"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.startDate"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.workEmail"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.manager"></td>
                            <td class="px-4 py-3"><button @click="deleteEmpReg(i)" class="text-xs text-red-500 hover:underline">Delete</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- TRAINING TABLE --}}
            <table class="w-full text-sm border-collapse" x-show="activeTab === 'Training'">
                <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Training Program</th>
                        <th class="px-4 py-3 text-left font-semibold">Trainer</th>
                        <th class="px-4 py-3 text-left font-semibold">Start Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Due Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredRows.length === 0">
                        <tr><td colspan="7" class="px-4 py-16 text-center text-gray-400"><div class="flex flex-col items-center gap-2"><svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg><span class="text-sm">No training records found. Click + Add New to create one.</span></div></td></tr>
                    </template>
                    <template x-for="(row, i) in filteredRows" :key="i">
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.employeeName"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.program"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.trainer"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.startDate"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.dueDate"></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Scheduled</span></td>
                            <td class="px-4 py-3"><button @click="deleteTraining(i)" class="text-xs text-red-500 hover:underline">Delete</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
    </div>

    {{-- ===================== PDS SLIDE-OVER ===================== --}}
    <div
        x-show="showPdsModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showPdsModal = false"
    >
        <div
            x-show="showPdsModal"
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
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest">New Personal Data Sheet (PDS)</h2>
                <button @click="showPdsModal = false" class="text-gray-400 hover:text-gray-600 transition">
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
                    <form @submit.prevent="submitPds()" class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

                        {{-- Personal Information --}}
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 pb-1 border-b border-blue-100">Personal Information</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="pdsForm.fullName" required placeholder="e.g. Juan Dela Cruz"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Position <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="pdsForm.position" required placeholder="e.g. Engineer"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                    <input type="email" x-model="pdsForm.email" placeholder="email@example.com"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                                    <input type="text" x-model="pdsForm.phone" placeholder="+63 9xx xxx xxxx"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date of Birth</label>
                                    <input type="date" x-model="pdsForm.dob"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Gender</label>
                                    <select x-model="pdsForm.gender"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none bg-white">
                                        <option value="">Select...</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Prefer not to say</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Address</label>
                                    <textarea x-model="pdsForm.address" rows="2" placeholder="Street, Barangay, City, Province"
                                        class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Emergency Contact Name</label>
                                    <input type="text" x-model="pdsForm.emergencyName" placeholder="Full name"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Emergency Contact Phone</label>
                                    <input type="text" x-model="pdsForm.emergencyPhone" placeholder="+63 9xx xxx xxxx"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Educational Background --}}
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 pb-1 border-b border-blue-100">Educational Background</p>

                            <p class="text-xs font-bold text-gray-500 mb-2">Secondary School</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">School Name</label>
                                    <input type="text" x-model="pdsForm.secSchool"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Year Graduated</label>
                                    <input type="text" x-model="pdsForm.secYear" placeholder="YYYY"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Honors/Achievements</label>
                                    <input type="text" x-model="pdsForm.secHonors"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>

                            <p class="text-xs font-bold text-gray-500 mt-4 mb-2">Tertiary Education</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Institution Name</label>
                                    <input type="text" x-model="pdsForm.tertInstitution"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Degree</label>
                                    <input type="text" x-model="pdsForm.tertDegree" placeholder="e.g., Bachelor of Science"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Major/Field of Study</label>
                                    <input type="text" x-model="pdsForm.tertMajor"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Year Graduated</label>
                                    <input type="text" x-model="pdsForm.tertYear" placeholder="YYYY"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Honors/Awards</label>
                                    <input type="text" x-model="pdsForm.tertHonors"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Work Experience --}}
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 pb-1 border-b border-blue-100">Work Experience</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Company Name</label>
                                    <input type="text" x-model="pdsForm.workCompany"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Job Position</label>
                                    <input type="text" x-model="pdsForm.workPosition"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                                    <input type="date" x-model="pdsForm.workStart"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                                    <input type="date" x-model="pdsForm.workEnd"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Key Responsibilities</label>
                                    <textarea x-model="pdsForm.workResponsibilities" rows="3" placeholder="Describe your main responsibilities and achievements..."
                                        class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Reference --}}
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 pb-1 border-b border-blue-100">Reference</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Reference Name</label>
                                    <input type="text" x-model="pdsForm.refName"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Position/Title</label>
                                    <input type="text" x-model="pdsForm.refTitle"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Company/Organization</label>
                                    <input type="text" x-model="pdsForm.refCompany"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Contact Number</label>
                                    <input type="text" x-model="pdsForm.refContact"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address</label>
                                    <input type="email" x-model="pdsForm.refEmail"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Relationship</label>
                                    <input type="text" x-model="pdsForm.refRelationship" placeholder="e.g., Former Manager, Colleague"
                                        class="w-full text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Signature --}}
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-3 pb-1 border-b border-blue-100">Signature</p>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Signature</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 flex flex-col items-center gap-2 bg-gray-50">
                                <input type="file" accept="image/*" @change="handleSignature($event)" class="hidden" id="sig-upload">
                                <label for="sig-upload" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Choose file
                                </label>
                                <p class="text-xs text-gray-400" x-text="pdsForm.signatureName || 'No file chosen'"></p>
                                <img x-show="pdsForm.signaturePreview" :src="pdsForm.signaturePreview" class="max-h-16 mt-1 rounded border border-gray-200">
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end gap-3 pt-2 pb-1">
                            <button type="button" @click="showPdsModal = false"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition">
                                Submit PDS
                            </button>
                        </div>

                    </form>
                </div>

                {{-- LEFT: LIVE PDS DOCUMENT PREVIEW --}}
                <div class="flex-1 bg-white rounded-xl shadow border border-gray-200 flex flex-col overflow-hidden">
                    <div class="px-5 py-3 border-b bg-gray-50 rounded-t-xl shrink-0 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live Preview — Personal Data Sheet</p>
                        <button type="button" @click="downloadPdsPdf()" class="text-xs px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded text-gray-700 font-semibold flex items-center gap-1 transition shadow-sm">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download PDF
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-5">
                        <div id="pds-doc-preview" class="border border-gray-400 text-xs text-gray-800 font-sans w-[794px] shrink-0 leading-[1.3] mx-auto shadow-sm bg-white">

                            {{-- Header --}}
                            <div class="flex items-center justify-center py-4 border-b border-gray-400 px-6">
                                <img src="{{ asset('images/imaglogo.png') }}" onerror="this.src='{{ asset('images/imag1logo.jpg') }}'" alt="John Kelly & Company" class="h-14 w-auto object-contain mix-blend-multiply">
                            </div>
                            <div class="bg-blue-700 text-white text-center font-bold py-2 text-sm tracking-widest uppercase border-b border-gray-400">
                                Personal Data Sheet
                            </div>

                            {{-- Section 1: Personal Information --}}
                            <div class="bg-blue-50 px-4 py-1.5 border-b border-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-800">I. Personal Information</span>
                            </div>
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-3">
                                    <span class="text-gray-500">Full Name:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.fullName || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Position:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.position || '—'"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-3">
                                    <span class="text-gray-500">Email:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.email || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Phone:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.phone || '—'"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-3">
                                    <span class="text-gray-500">Date of Birth:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.dob || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Gender:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.gender || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Emergency Contact:</span>
                                    <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="(pdsForm.emergencyName || '—') + (pdsForm.emergencyPhone ? ' · ' + pdsForm.emergencyPhone : '')"></p>
                                </div>
                            </div>
                            <div class="border-b border-gray-400 p-3">
                                <span class="text-gray-500">Address:</span>
                                <p class="font-semibold mt-0.5 min-h-[1rem]" x-text="pdsForm.address || '—'"></p>
                            </div>

                            {{-- Section 2: Educational Background --}}
                            <div class="bg-blue-50 px-4 py-1.5 border-b border-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-800">II. Educational Background</span>
                            </div>
                            <div class="border-b border-gray-400">
                                <div class="grid grid-cols-4 bg-gray-100 border-b border-gray-300 text-[10px] font-black text-gray-600 uppercase">
                                    <div class="p-2 border-r border-gray-300">Level</div>
                                    <div class="p-2 border-r border-gray-300">School / Institution</div>
                                    <div class="p-2 border-r border-gray-300">Year Graduated</div>
                                    <div class="p-2">Honors / Awards</div>
                                </div>
                                <div class="grid grid-cols-4 border-b border-gray-200 divide-x divide-gray-200">
                                    <div class="p-2 font-semibold text-gray-600">Secondary</div>
                                    <div class="p-2" x-text="pdsForm.secSchool || '—'"></div>
                                    <div class="p-2" x-text="pdsForm.secYear || '—'"></div>
                                    <div class="p-2" x-text="pdsForm.secHonors || '—'"></div>
                                </div>
                                <div class="grid grid-cols-4 divide-x divide-gray-200">
                                    <div class="p-2 font-semibold text-gray-600">Tertiary</div>
                                    <div class="p-2">
                                        <span x-text="pdsForm.tertInstitution || '—'"></span>
                                        <span x-show="pdsForm.tertDegree" class="block text-gray-500 text-[10px]" x-text="pdsForm.tertDegree + (pdsForm.tertMajor ? ' – ' + pdsForm.tertMajor : '')"></span>
                                    </div>
                                    <div class="p-2" x-text="pdsForm.tertYear || '—'"></div>
                                    <div class="p-2" x-text="pdsForm.tertHonors || '—'"></div>
                                </div>
                            </div>

                            {{-- Section 3: Work Experience --}}
                            <div class="bg-blue-50 px-4 py-1.5 border-b border-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-800">III. Work Experience</span>
                            </div>
                            <div class="border-b border-gray-400">
                                <div class="grid grid-cols-4 bg-gray-100 border-b border-gray-300 text-[10px] font-black text-gray-600 uppercase">
                                    <div class="p-2 border-r border-gray-300">Company</div>
                                    <div class="p-2 border-r border-gray-300">Position</div>
                                    <div class="p-2 border-r border-gray-300">Duration</div>
                                    <div class="p-2">Key Responsibilities</div>
                                </div>
                                <div class="grid grid-cols-4 divide-x divide-gray-200">
                                    <div class="p-2" x-text="pdsForm.workCompany || '—'"></div>
                                    <div class="p-2" x-text="pdsForm.workPosition || '—'"></div>
                                    <div class="p-2">
                                        <span x-text="pdsForm.workStart || '—'"></span>
                                        <span x-show="pdsForm.workEnd"> – <span x-text="pdsForm.workEnd"></span></span>
                                    </div>
                                    <div class="p-2 whitespace-pre-wrap" x-text="pdsForm.workResponsibilities || '—'"></div>
                                </div>
                            </div>

                            {{-- Section 4: Reference --}}
                            <div class="bg-blue-50 px-4 py-1.5 border-b border-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-800">IV. Reference</span>
                            </div>
                            <div class="grid grid-cols-3 border-b border-gray-400 divide-x divide-gray-400">
                                <div class="p-3">
                                    <span class="text-gray-500">Name:</span>
                                    <p class="font-semibold mt-0.5" x-text="pdsForm.refName || '—'"></p>
                                    <span class="text-gray-500 block mt-1">Position/Title:</span>
                                    <p class="font-semibold" x-text="pdsForm.refTitle || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Company:</span>
                                    <p class="font-semibold mt-0.5" x-text="pdsForm.refCompany || '—'"></p>
                                    <span class="text-gray-500 block mt-1">Relationship:</span>
                                    <p class="font-semibold" x-text="pdsForm.refRelationship || '—'"></p>
                                </div>
                                <div class="p-3">
                                    <span class="text-gray-500">Contact:</span>
                                    <p class="font-semibold mt-0.5" x-text="pdsForm.refContact || '—'"></p>
                                    <span class="text-gray-500 block mt-1">Email:</span>
                                    <p class="font-semibold" x-text="pdsForm.refEmail || '—'"></p>
                                </div>
                            </div>

                            {{-- Section 5: Signature --}}
                            <div class="bg-blue-50 px-4 py-1.5 border-b border-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-800">V. Signature</span>
                            </div>
                            <div class="p-4 flex flex-col items-center gap-2 min-h-[80px] border-b border-gray-400">
                                <img x-show="pdsForm.signaturePreview" :src="pdsForm.signaturePreview" class="max-h-16">
                                <p x-show="!pdsForm.signaturePreview" class="text-gray-300 italic text-xs">No signature uploaded</p>
                                <p class="text-gray-400 italic text-[9px] border-t border-gray-300 w-full text-center pt-1 mt-2" x-text="pdsForm.fullName || 'Signature Over Printed Name'"></p>
                            </div>

                            {{-- Footer --}}
                            <div class="bg-gray-50 px-4 py-2 text-[9px] text-gray-400 text-right">
                                John Kelly & Company — Personal Data Sheet &nbsp;|&nbsp; Date Submitted: <span x-text="new Date().toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'})"></span>
                            </div>

                        </div>{{-- end pds-doc-preview --}}
                    </div>
                </div>{{-- end live preview --}}

            </div>{{-- end split body --}}
        </div>
    </div>

    {{-- ===================== CHECKLIST SUBMISSION SLIDE-OVER ===================== --}}
    <div
        x-show="showChecklistModal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showChecklistModal = false"
    >
        <div
            x-show="showChecklistModal"
            x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="bg-white shadow-2xl w-full max-w-lg h-full flex flex-col overflow-hidden"
        >
            <div class="flex items-center justify-between px-6 py-3 bg-blue-700 shrink-0">
                <h2 class="text-sm font-bold text-white uppercase tracking-widest">New Checklist Submission</h2>
                <button @click="showChecklistModal = false" class="text-blue-200 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form @submit.prevent="submitChecklist()" class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Employee Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="checklistForm.employeeName" required
                        class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-3">Required Documents</label>
                    <div class="space-y-2">
                        <template x-for="doc in checklistDocs" :key="doc.key">
                            <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-blue-50 transition">
                                <input type="checkbox" :value="doc.key" x-model="checklistForm.checked" class="w-4 h-4 accent-blue-600 rounded">
                                <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium transition" x-text="doc.label"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div class="pt-4 border-t flex justify-end gap-3 pb-2">
                    <button type="button" @click="showChecklistModal = false"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition">Save Checklist</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== EMPLOYEE REGISTRATION SLIDE-OVER ===================== --}}
    <div
        x-show="showEmpRegModal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showEmpRegModal = false"
    >
        <div
            x-show="showEmpRegModal"
            x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="bg-white shadow-2xl w-full max-w-xl h-full flex flex-col overflow-hidden"
        >
            <div class="flex items-center justify-between px-6 py-3 bg-blue-700 shrink-0">
                <h2 class="text-sm font-bold text-white uppercase tracking-widest">New Employee Registration</h2>
                <button @click="showEmpRegModal = false" class="text-blue-200 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form @submit.prevent="submitEmpReg()" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="empRegForm.fullName" required
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Employee ID
                            <span class="ml-1 text-[10px] font-normal text-blue-500 inline-flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Auto-generated
                            </span>
                        </label>
                        <input type="text" x-model="empRegForm.employeeId" readonly
                            class="w-full text-sm px-3 py-2 border border-blue-200 rounded-lg bg-blue-50 text-blue-700 font-mono font-semibold outline-none cursor-default tracking-wide">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                        <select x-model="empRegForm.department"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none bg-white">
                            <option value="">Select...</option>
                            <option>Human Resources</option>
                            <option>Finance</option>
                            <option>Operations</option>
                            <option>Information Technology</option>
                            <option>Marketing</option>
                            <option>Sales</option>
                            <option>Legal</option>
                            <option>Administration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                        <input type="date" x-model="empRegForm.startDate"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Work Email</label>
                        <input type="email" x-model="empRegForm.workEmail" placeholder="employee@company.com"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Reporting Manager</label>
                        <input type="text" x-model="empRegForm.manager"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                </div>
                <div class="pt-4 border-t flex justify-end gap-3 pb-2">
                    <button type="button" @click="showEmpRegModal = false"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition">Register Employee</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== TRAINING SLIDE-OVER ===================== --}}
    <div
        x-show="showTrainingModal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex justify-end bg-black/50 backdrop-blur-sm"
        style="display:none;"
        @click.self="showTrainingModal = false"
    >
        <div
            x-show="showTrainingModal"
            x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="bg-white shadow-2xl w-full max-w-xl h-full flex flex-col overflow-hidden"
        >
            <div class="flex items-center justify-between px-6 py-3 bg-blue-700 shrink-0">
                <h2 class="text-sm font-bold text-white uppercase tracking-widest">New Training Assignment</h2>
                <button @click="showTrainingModal = false" class="text-blue-200 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form @submit.prevent="submitTraining()" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Employee Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="trainingForm.employeeName" required
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Training Program <span class="text-red-500">*</span></label>
                        <select x-model="trainingForm.program" required
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none bg-white">
                            <option value="">Select...</option>
                            <option>Onboarding Orientation</option>
                            <option>Company Policies &amp; Procedures</option>
                            <option>Safety &amp; Security Awareness</option>
                            <option>IT Systems &amp; Tools</option>
                            <option>Customer Service Excellence</option>
                            <option>Leadership Development</option>
                            <option>Communication Skills</option>
                            <option>Data Privacy &amp; Compliance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                        <input type="date" x-model="trainingForm.startDate"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Due Date</label>
                        <input type="date" x-model="trainingForm.dueDate"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Trainer / Instructor</label>
                    <input type="text" x-model="trainingForm.trainer"
                        class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Training Description</label>
                    <textarea x-model="trainingForm.description" rows="5" placeholder="Enter training details..."
                        class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none resize-none bg-gray-50"></textarea>
                </div>
                <div class="pt-4 border-t flex justify-end gap-3 pb-2">
                    <button type="button" @click="showTrainingModal = false"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition">Assign Training</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function onboardingPage() {
    return {
        activeTab: 'PDS',
        search: '',
        showPdsModal: false,
        showChecklistModal: false,
        showEmpRegModal: false,
        showTrainingModal: false,
        tabs: [
            { key: 'PDS',                  label: 'PDS' },
            { key: 'Checklist Submission', label: 'Checklist Submission' },
            { key: 'Employee Registration',label: 'Employee Registration' },
            { key: 'Training',             label: 'Training' },
        ],
        data: {
            'PDS': [],
            'Checklist Submission': [],
            'Employee Registration': [],
            'Training': [],
        },
        checklistDocs: [
            { key: 'valid_id',       label: 'Valid ID (Government-issued)' },
            { key: 'birth_cert',     label: 'Birth Certificate' },
            { key: 'ssn_tax',        label: 'SSN / Tax ID' },
            { key: 'edu_certs',      label: 'Educational Certificates' },
            { key: 'prev_employ',    label: 'Previous Employment Records' },
            { key: 'med_cert',       label: 'Medical Certificate' },
            { key: 'nbi',            label: 'NBI Clearance' },
            { key: 'bank',           label: 'Bank Account Details' },
            { key: 'id_photos',      label: '2x2 ID Photos' },
            { key: 'covid_vax',      label: 'COVID-19 Vaccination Card' },
        ],
        pdsForm: {
            fullName: '', position: '', email: '', phone: '',
            dob: '', gender: '', address: '',
            emergencyName: '', emergencyPhone: '',
            secSchool: '', secYear: '', secHonors: '',
            tertInstitution: '', tertDegree: '', tertMajor: '', tertYear: '', tertHonors: '',
            workCompany: '', workPosition: '', workStart: '', workEnd: '', workResponsibilities: '',
            refName: '', refTitle: '', refCompany: '', refContact: '', refEmail: '', refRelationship: '',
            signaturePreview: '', signatureName: '',
            submittedDate: '',
        },
        checklistForm: { employeeName: '', checked: [] },
        empRegForm: { fullName: '', employeeId: '', department: '', startDate: '', workEmail: '', manager: '' },
        trainingForm: { employeeName: '', program: '', startDate: '', dueDate: '', trainer: '', description: '' },

        get filteredRows() {
            const rows = this.data[this.activeTab] ?? [];
            if (!this.search.trim()) return rows;
            const q = this.search.toLowerCase();
            return rows.filter(r =>
                Object.values(r).some(v => String(v).toLowerCase().includes(q))
            );
        },

        openModal() {
            if (this.activeTab === 'PDS') {
                this.pdsForm = {
                    fullName: '', position: '', email: '', phone: '',
                    dob: '', gender: '', address: '',
                    emergencyName: '', emergencyPhone: '',
                    secSchool: '', secYear: '', secHonors: '',
                    tertInstitution: '', tertDegree: '', tertMajor: '', tertYear: '', tertHonors: '',
                    workCompany: '', workPosition: '', workStart: '', workEnd: '', workResponsibilities: '',
                    refName: '', refTitle: '', refCompany: '', refContact: '', refEmail: '', refRelationship: '',
                    signaturePreview: '', signatureName: '', submittedDate: '',
                };
                this.showPdsModal = true;
            } else if (this.activeTab === 'Checklist Submission') {
                this.checklistForm = { employeeName: '', checked: [] };
                this.showChecklistModal = true;
            } else if (this.activeTab === 'Employee Registration') {
                this.empRegForm = { fullName: '', employeeId: this.generateEmployeeId(), department: '', startDate: '', workEmail: '', manager: '' };
                this.showEmpRegModal = true;
            } else if (this.activeTab === 'Training') {
                this.trainingForm = { employeeName: '', program: '', startDate: '', dueDate: '', trainer: '', description: '' };
                this.showTrainingModal = true;
            }
        },

        handleSignature(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.pdsForm.signatureName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => { this.pdsForm.signaturePreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        submitPds() {
            const record = { ...this.pdsForm, submittedDate: new Date().toLocaleDateString('en-PH') };
            this.data['PDS'].unshift(record);
            this.showPdsModal = false;
        },
        deletePds(index) {
            if (!confirm('Delete this PDS record?')) return;
            this.data['PDS'].splice(index, 1);
        },

        submitChecklist() {
            const total = this.checklistDocs.length;
            const submitted = this.checklistForm.checked.length;
            this.data['Checklist Submission'].unshift({
                employeeName: this.checklistForm.employeeName,
                docsSubmitted: submitted,
                totalDocs: total,
                checked: [...this.checklistForm.checked],
                submittedDate: new Date().toLocaleDateString('en-PH'),
            });
            this.showChecklistModal = false;
        },
        deleteChecklist(index) {
            if (!confirm('Delete this checklist record?')) return;
            this.data['Checklist Submission'].splice(index, 1);
        },

        submitEmpReg() {
            this.data['Employee Registration'].unshift({ ...this.empRegForm });
            this.showEmpRegModal = false;
        },
        deleteEmpReg(index) {
            if (!confirm('Delete this employee registration?')) return;
            this.data['Employee Registration'].splice(index, 1);
        },

        submitTraining() {
            this.data['Training'].unshift({ ...this.trainingForm });
            this.showTrainingModal = false;
        },
        deleteTraining(index) {
            if (!confirm('Delete this training record?')) return;
            this.data['Training'].splice(index, 1);
        },

        generateEmployeeId() {
            const year = new Date().getFullYear();
            const existing = this.data['Employee Registration'];
            const seq = existing.length + 1;
            return 'EMP-' + year + '-' + String(seq).padStart(3, '0');
        },

        downloadPdsPdf() {
            const el = document.getElementById('pds-doc-preview');
            const name = this.pdsForm.fullName ? this.pdsForm.fullName.replace(/\s+/g, '_') : 'PDS';
            html2pdf().set({
                margin: 0,
                filename: `PDS_${name}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(el).save();
        },

        downloadCSV() {
            const tab = this.activeTab;
            const rows = this.data[tab] ?? [];
            if (rows.length === 0) { alert('No records to export for "' + tab + '".'); return; }

            const csvHeaders = {
                'PDS': ['Full Name','Position','Email','Phone','Date of Birth','Gender','Address','Emergency Name','Emergency Phone',
                    'Secondary School','Sec Year','Sec Honors','Tertiary Institution','Degree','Major','Tert Year','Tert Honors',
                    'Work Company','Work Position','Work Start','Work End','Responsibilities',
                    'Reference Name','Ref Title','Ref Company','Ref Contact','Ref Email','Ref Relationship','Date Submitted'],
                'Checklist Submission': ['Employee Name','Docs Submitted','Total Docs','Checked Documents','Date Submitted'],
                'Employee Registration': ['Full Name','Employee ID','Department','Start Date','Work Email','Reporting Manager'],
                'Training': ['Employee Name','Training Program','Start Date','Due Date','Trainer','Description'],
            };

            const csvRows = {
                'PDS': rows.map(r => [r.fullName,r.position,r.email,r.phone,r.dob,r.gender,r.address,
                    r.emergencyName,r.emergencyPhone,r.secSchool,r.secYear,r.secHonors,
                    r.tertInstitution,r.tertDegree,r.tertMajor,r.tertYear,r.tertHonors,
                    r.workCompany,r.workPosition,r.workStart,r.workEnd,r.workResponsibilities,
                    r.refName,r.refTitle,r.refCompany,r.refContact,r.refEmail,r.refRelationship,r.submittedDate]),
                'Checklist Submission': rows.map(r => [
                    r.employeeName, r.docsSubmitted, r.totalDocs,
                    (r.checked || []).map(k => this.checklistDocs.find(d=>d.key===k)?.label || k).join('; '),
                    r.submittedDate]),
                'Employee Registration': rows.map(r => [r.fullName,r.employeeId,r.department,r.startDate,r.workEmail,r.manager]),
                'Training': rows.map(r => [r.employeeName,r.program,r.startDate,r.dueDate,r.trainer,r.description]),
            };

            const escape = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
            const csvContent = [csvHeaders[tab].map(escape).join(','),
                ...csvRows[tab].map(row => row.map(escape).join(','))].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = tab.replace(/\s+/g,'_') + '_' + new Date().toISOString().slice(0,10) + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        },
    };
}
</script>
@endpush
@endsection
