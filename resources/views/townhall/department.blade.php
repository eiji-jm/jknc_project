@extends('layouts.app')
@section('title', 'Department')

@section('content')
<div class="px-6 py-5 h-full flex flex-col">

    {{-- HEADER --}}
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-[26px] font-semibold text-gray-800">Department</h1>

        {{-- FILTER --}}
        <form method="GET">
            <select name="department"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                <option value="">All Departments</option>

                @foreach($departments as $dept)
                    <option value="{{ $dept }}"
                        {{ request('department') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- TABLE CONTAINER --}}
    <div class="bg-white border border-gray-200 rounded-xl flex flex-col flex-1 overflow-hidden">

        {{-- TABLE --}}
        <div class="overflow-auto flex-1">
            <table class="w-full text-sm text-left border-collapse">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 border-r font-semibold">Ref#</th>
                        <th class="px-4 py-3 border-r font-semibold">Subject</th>
                        <th class="px-4 py-3 border-r font-semibold">Department</th>
                        <th class="px-4 py-3 border-r font-semibold">From</th>
                        <th class="px-4 py-3 font-semibold">Date</th>
                    </tr>
                </thead>

                <tbody class="bg-white text-gray-700">

                    @forelse($communications as $item)
                        <tr
                            class="border-t hover:bg-gray-50 cursor-pointer transition"
                            onclick="window.location='{{ route('townhall.show', $item->id) }}'">

                            <td class="px-4 py-3 border-r">
                                {{ $item->ref_no }}
                            </td>

                            <td class="px-4 py-3 border-r font-medium">
                                {{ $item->subject }}
                            </td>

                            <td class="px-4 py-3 border-r">
                                {{ $item->department_stakeholder }}
                            </td>

                            <td class="px-4 py-3 border-r">
                                {{ $item->from_name }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $item->communication_date }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                No communications found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- FOOTER (LIKE PAGINATION BAR) --}}
        <div class="px-4 py-3 border-t flex items-center justify-between text-xs text-gray-500">

            <div>
                Total Records:
                <span class="font-semibold text-gray-800">
                    {{ $communications->count() }}
                </span>
            </div>

            <div class="flex items-center gap-4">
                <span>Department Filter Active:
                    <span class="font-medium text-blue-600">
                        {{ request('department') ?? 'All' }}
                    </span>
                </span>
            </div>

        </div>

    </div>

</div>
@endsection
