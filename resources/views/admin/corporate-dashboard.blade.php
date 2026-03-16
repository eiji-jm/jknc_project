@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Corporate Approval Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Review Corporate submissions for approval</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 px-5 pt-5">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-blue-600 uppercase">Pending Approval</p>
                <h2 class="text-3xl font-bold text-blue-700 mt-2">{{ $pendingCount }}</h2>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-green-600 uppercase">Approved</p>
                <h2 class="text-3xl font-bold text-green-700 mt-2">{{ $approvedCount }}</h2>
            </div>

            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-red-600 uppercase">Rejected</p>
                <h2 class="text-3xl font-bold text-red-700 mt-2">{{ $rejectedCount }}</h2>
            </div>

            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-yellow-600 uppercase">Needs Revision</p>
                <h2 class="text-3xl font-bold text-yellow-700 mt-2">{{ $revisionCount }}</h2>
            </div>
        </div>

        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Module</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Corporation</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Company Reg No.</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Uploaded By</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Date Uploaded</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($items as $item)
                            @php
                                $statusClasses = match($item->status) {
                                    'Approved' => 'bg-green-50 text-green-700',
                                    'Rejected' => 'bg-red-50 text-red-700',
                                    'Needs Revision' => 'bg-yellow-50 text-yellow-700',
                                    default => 'bg-blue-50 text-blue-700',
                                };
                            @endphp

                            <tr class="border-t border-gray-200 hover:bg-gray-50">
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
                                    <div class="flex items-center justify-center gap-2">
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
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection