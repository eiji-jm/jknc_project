@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">

    {{-- PAGE WRAPPER --}}
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        {{-- HEADER --}}
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Review files for approval and rejection</p>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Export
                </button>
                <button class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + New Review
                </button>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 px-5 pt-5">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase">Pending Approval</p>
                        <h2 class="text-3xl font-bold text-blue-700 mt-2">18</h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase">Approved Today</p>
                        <h2 class="text-3xl font-bold text-green-700 mt-2">9</h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase">Rejected</p>
                        <h2 class="text-3xl font-bold text-red-700 mt-2">4</h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase">Needs Revision</p>
                        <h2 class="text-3xl font-bold text-yellow-700 mt-2">6</h2>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="px-5 pt-5">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col xl:flex-row xl:items-center gap-3 xl:justify-between">
                <div class="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="text"
                            placeholder="Search file, module, department, uploader..."
                            class="w-full md:w-80 border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                    </div>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Modules</option>
                        <option>Town Hall</option>
                        <option>Corporate</option>
                        <option>Accounting</option>
                    </select>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Departments</option>
                        <option>Town Hall</option>
                        <option>Corporate</option>
                        <option>Accounting</option>
                        <option>Operations</option>
                        <option>LGU</option>
                    </select>

                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option>All Status</option>
                        <option>Pending Approval</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                        <option>Needs Revision</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-white transition">
                        Reset
                    </button>
                    <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Module</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">File Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Department</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Uploaded By</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Date Uploaded</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Approver</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Priority</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        {{-- TOWN HALL FIRST --}}
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 border-r border-gray-200">0001</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 font-medium">Town Hall</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">CEO Memo Announcement.pdf</td>
                            <td class="px-4 py-3 border-r border-gray-200">Town Hall</td>
                            <td class="px-4 py-3 border-r border-gray-200">Jasper Bulac</td>
                            <td class="px-4 py-3 border-r border-gray-200">04/22/2024</td>
                            <td class="px-4 py-3 border-r border-gray-200">CEO</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-600 font-medium">High</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700 font-medium">Pending Approval</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Approve</button>
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Reject</button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 border-r border-gray-200">0002</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 font-medium">Town Hall</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">Board Meeting Memo.docx</td>
                            <td class="px-4 py-3 border-r border-gray-200">Town Hall</td>
                            <td class="px-4 py-3 border-r border-gray-200">Maria Santos</td>
                            <td class="px-4 py-3 border-r border-gray-200">04/23/2024</td>
                            <td class="px-4 py-3 border-r border-gray-200">Admin</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700 font-medium">Medium</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 font-medium">Needs Revision</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-white hover:bg-black transition">Review</button>
                                </div>
                            </td>
                        </tr>

                        {{-- CORPORATE --}}
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 border-r border-gray-200">0003</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 font-medium">Corporate</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">SEC Amendment Letter.pdf</td>
                            <td class="px-4 py-3 border-r border-gray-200">Corporate</td>
                            <td class="px-4 py-3 border-r border-gray-200">Jasper Bulac</td>
                            <td class="px-4 py-3 border-r border-gray-200">04/22/2024</td>
                            <td class="px-4 py-3 border-r border-gray-200">CEO</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-600 font-medium">High</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700 font-medium">Pending Approval</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Approve</button>
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Reject</button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 border-r border-gray-200">0004</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 font-medium">Corporate</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">GIS Draft 2024.docx</td>
                            <td class="px-4 py-3 border-r border-gray-200">Corporate</td>
                            <td class="px-4 py-3 border-r border-gray-200">Maria Santos</td>
                            <td class="px-4 py-3 border-r border-gray-200">04/23/2024</td>
                            <td class="px-4 py-3 border-r border-gray-200">Admin</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700 font-medium">Medium</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 font-medium">Needs Revision</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-800 text-white hover:bg-black transition">Review</button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 border-r border-gray-200">0005</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 font-medium">Accounting</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">Tax Clearance Request.pdf</td>
                            <td class="px-4 py-3 border-r border-gray-200">Accounting</td>
                            <td class="px-4 py-3 border-r border-gray-200">John Dela Cruz</td>
                            <td class="px-4 py-3 border-r border-gray-200">04/24/2024</td>
                            <td class="px-4 py-3 border-r border-gray-200">CEO</td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-50 text-green-700 font-medium">Low</span>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-50 text-green-700 font-medium">Approved</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">View</button>
                                </div>
                            </td>
                        </tr>

                        @for ($i = 0; $i < 6; $i++)
                            <tr class="border-t border-gray-200 h-14">
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div class="flex items-center gap-6">
                    <span>Total Files <span class="text-gray-800 font-semibold">18</span></span>
                    <span>Pending <span class="text-yellow-600 font-semibold">18</span></span>
                    <span>Approved <span class="text-green-600 font-semibold">9</span></span>
                    <span>Rejected <span class="text-red-600 font-semibold">4</span></span>
                </div>

                <div class="flex items-center gap-4">
                    <span>
                        Records per page
                        <select class="bg-transparent outline-none font-semibold text-gray-700">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                    </span>
                    <span>1 to 10</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
