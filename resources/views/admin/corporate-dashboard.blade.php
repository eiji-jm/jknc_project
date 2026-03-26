@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5"
     x-data="{
        search: '',
        moduleFilter: '',
        statusFilter: '',
        matches(item) {
            const q = this.search.toLowerCase().trim();

            const matchesSearch =
                q === '' ||
                (item.title ?? '').toLowerCase().includes(q) ||
                (item.module ?? '').toLowerCase().includes(q) ||
                (item.company_reg_no ?? '').toLowerCase().includes(q) ||
                (item.uploaded_by ?? '').toLowerCase().includes(q) ||
                (item.date_uploaded ?? '').toLowerCase().includes(q);

            const matchesModule =
                this.moduleFilter === '' || item.module === this.moduleFilter;

            const matchesStatus =
                this.statusFilter === '' || item.status === this.statusFilter;

            return matchesSearch && matchesModule && matchesStatus;
        },
        clearFilters() {
            this.search = '';
            this.moduleFilter = '';
            this.statusFilter = '';
        }
     }">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Corporate Approval Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Review Corporate submissions for approval</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 px-5 pt-5">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-blue-600 uppercase">Submitted</p>
                <h2 class="text-3xl font-bold text-blue-700 mt-2">{{ $pendingCount }}</h2>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-green-600 uppercase">Accepted</p>
                <h2 class="text-3xl font-bold text-green-700 mt-2">{{ $approvedCount }}</h2>
            </div>

            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-red-600 uppercase">Rejected</p>
                <h2 class="text-3xl font-bold text-red-700 mt-2">{{ $rejectedCount }}</h2>
            </div>

            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-yellow-600 uppercase">Reverted</p>
                <h2 class="text-3xl font-bold text-yellow-700 mt-2">{{ $revisionCount }}</h2>
            </div>
        </div>

        <div class="px-5 pt-5">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                            Search
                        </label>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search corporation, module, company reg no., uploader..."
                            class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                            Module
                        </label>
                        <select
                            x-model="moduleFilter"
                            class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                            <option value="">All Modules</option>
                            <option value="SEC-COI">SEC-COI</option>
                            <option value="SEC-AOI">SEC-AOI</option>
                            <option value="Bylaws">Bylaws</option>
                            <option value="GIS">GIS</option>
                            <option value="LGU">LGU</option>
                            <option value="Accounting">Accounting</option>
                            <option value="Banking">Banking</option>
                            <option value="Operations">Operations</option>
                            <option value="Correspondence">Correspondence</option>
                            <option value="Legal">Legal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                            Workflow Status
                        </label>
                        <select
                            x-model="statusFilter"
                            class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                            <option value="">All Status</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Reverted">Reverted</option>
                            <option value="Archived">Archived</option>
                            <option value="Uploaded">Uploaded</option>
                        </select>
                    </div>

                </div>

                <div class="mt-3 flex justify-end">
                    <button
                        type="button"
                        @click="clearFilters()"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-white transition">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Module</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Corporation / Record</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Company Reg No.</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Uploaded By</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Date Uploaded</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Workflow Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($items as $item)
                            @php
                                $statusClasses = match($item->status) {
                                    'Accepted' => 'bg-green-50 text-green-700',
                                    'Archived' => 'bg-gray-100 text-gray-700',
                                    'Reverted' => 'bg-yellow-50 text-yellow-700',
                                    'Submitted' => 'bg-blue-50 text-blue-700',
                                    default => 'bg-orange-50 text-orange-700',
                                };
                            @endphp

                            <tr class="border-t border-gray-200 hover:bg-gray-50"
                                x-show="matches({
                                    title: @js($item->title),
                                    module: @js($item->module),
                                    company_reg_no: @js($item->company_reg_no),
                                    uploaded_by: @js((string) $item->uploaded_by),
                                    date_uploaded: @js((string) $item->date_uploaded),
                                    status: @js($item->status)
                                })">
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->id }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->module }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->title }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->company_reg_no }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->uploaded_by }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $item->date_uploaded }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses }} font-medium">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        @if($item->status !== 'Accepted' && $item->status !== 'Archived')
                                            <form action="{{ $item->approve_route }}" method="POST">
                                                @csrf
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ $item->reject_route }}" method="POST">
                                                @csrf
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                    Reject
                                                </button>
                                            </form>

                                            <form action="{{ $item->revise_route }}" method="POST">
                                                @csrf
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-yellow-600 text-white hover:bg-yellow-700 transition">
                                                    Revise
                                                </button>
                                            </form>
                                        @endif

                                        @if($item->status === 'Accepted')
                                            <form action="{{ $item->archive_route }}" method="POST">
                                                @csrf
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-700 text-white hover:bg-gray-800 transition">
                                                    Archive
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ $item->show_route }}"
                                           class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    No corporate submissions found.
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="false"></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection