@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.index') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Company</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">{{ $company->company_name }}</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex flex-wrap items-start gap-5">
                        <div class="h-16 w-16 shrink-0 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-sm font-bold leading-tight">
                            JK<br>&amp;C
                        </div>

                        <div class="min-w-[280px]">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">JK&amp;Company</h1>
                            <p class="mt-1 text-sm text-gray-500">Corporation</p>

                            <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                                <div class="text-gray-600">
                                    <span class="font-medium text-gray-700"><i class="fas fa-location-dot mr-1 text-gray-400"></i>Address:</span>
                                    <span>{{ $company->address ?: '3F, Cebu Holdings Center, Cardinal Rosales Ave, Cebu Business Park, Cebu City' }}</span>
                                </div>
                                <div class="text-gray-600">
                                    <span class="font-medium text-gray-700"><i class="fas fa-phone mr-1 text-gray-400"></i>Phone:</span>
                                    <span>{{ $company->phone ?: '0995 353 3789' }}</span>
                                </div>
                                <div class="text-gray-600">
                                    <span class="font-medium text-gray-700"><i class="fas fa-globe mr-1 text-gray-400"></i>Website:</span>
                                    <a href="{{ $company->website ?: '#' }}" class="text-blue-600 underline">{{ $company->website ?: 'https://bigin.zoho.com/' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="mt-4 rounded-md border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <select class="h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option>All Tasks</option>
                                <option>My Tasks</option>
                                <option>Completed Tasks</option>
                            </select>

                            <div class="mt-3 inline-flex rounded-md border border-gray-200 bg-white p-1">
                                <button class="h-8 rounded-md bg-blue-50 px-4 text-sm font-medium text-blue-700">Tasks</button>
                                <button class="h-8 rounded-md px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">Events</button>
                                <button class="h-8 rounded-md px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">Calls</button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button class="h-9 rounded-full border border-blue-200 px-4 text-sm font-medium text-blue-700 hover:bg-blue-50">Task</button>
                            <button class="h-9 rounded-full border border-blue-200 px-4 text-sm font-medium text-blue-700 hover:bg-blue-50">Event</button>
                            <button class="h-9 rounded-full border border-blue-200 px-4 text-sm font-medium text-blue-700 hover:bg-blue-50">Call</button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Task Name</th>
                                        <th class="px-4 py-3 text-left font-medium">Due Date</th>
                                        <th class="px-4 py-3 text-left font-medium">Status</th>
                                        <th class="px-4 py-3 text-left font-medium">Task Owner</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    @foreach ($activities as $activity)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">{{ $activity['task_name'] }}</td>
                                            <td class="px-4 py-3">{{ $activity['due_date'] }}</td>
                                            <td class="px-4 py-3">
                                                @if ($activity['status'] === 'Completed')
                                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Completed</span>
                                                @elseif ($activity['status'] === 'In Progress')
                                                    <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">In Progress</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Not Started</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="h-7 w-7 rounded-full bg-gray-100 border border-gray-200 text-[11px] font-semibold text-gray-600 inline-flex items-center justify-center">{{ $activity['owner_initials'] }}</span>
                                                    <span>{{ $activity['owner'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
