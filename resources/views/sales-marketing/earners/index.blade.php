@extends('layouts.app')

@section('title', 'Sales & Marketing | Commission Earners')

@section('content')
<div class="flex-1 overflow-y-auto p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Commission Earners</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        This will be the master list of all Sales & Marketing commission earners.
                    </p>
                </div>

                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i>
                    Add Earner
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Master List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-3 font-semibold">Full Name</th>
                            <th class="px-6 py-3 font-semibold">Role</th>
                            <th class="px-6 py-3 font-semibold">Email</th>
                            <th class="px-6 py-3 font-semibold">Mobile Number</th>
                            <th class="px-6 py-3 font-semibold">Bank Name</th>
                            <th class="px-6 py-3 font-semibold">Account Name</th>
                            <th class="px-6 py-3 font-semibold">Account Number</th>
                            <th class="px-6 py-3 font-semibold">TIN</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-400">
                                No earners yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection