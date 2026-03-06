@extends('layouts.app')

@section('content')
<div class="w-full px-6 mt-4">

    <div class="bg-white rounded-xl border border-gray-200">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between px-4 py-3 border-b">

            {{-- Tabs --}}
            <div class="flex items-center gap-2 text-sm">

                <button class="px-4 py-2 bg-gray-100 rounded-t-md font-medium">
                    Mayor's permit
                </button>

                <button class="px-4 py-2 hover:bg-gray-100 rounded-t-md">
                    Barangay Business Permit
                </button>

                <button class="px-4 py-2 hover:bg-gray-100 rounded-t-md">
                    Fire Permit
                </button>

                <button class="px-4 py-2 hover:bg-gray-100 rounded-t-md">
                    Sanitary Permit
                </button>

                <button class="px-4 py-2 hover:bg-gray-100 rounded-t-md">
                    Obo Permit
                </button>

            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-3">

                {{-- View Icons --}}
                <button class="p-2 border rounded-md hover:bg-gray-50">
                    ☰
                </button>

                <button class="p-2 border rounded-md hover:bg-gray-50">
                    ⧉
                </button>

                {{-- Add Button --}}
                <div class="flex">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-l-md text-sm">
                        + Add
                    </button>

                    <button class="bg-blue-600 text-white px-2 rounded-r-md border-l border-blue-500">
                        ▼
                    </button>
                </div>

                {{-- More --}}
                <button class="p-2 border rounded-md hover:bg-gray-50">
                    ⋮
                </button>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="p-4">

            <div class="border rounded-md overflow-hidden">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>

                            <th class="p-3 text-left">Date Uploaded</th>
                            <th class="p-3 text-left">Uploaded By</th>
                            <th class="p-3 text-left">Client</th>
                            <th class="p-3 text-left">TIN</th>
                            <th class="p-3 text-left">Government Body / Agency</th>
                            <th class="p-3 text-left">Registration Status</th>
                            <th class="p-3 text-left">Registration Date</th>
                            <th class="p-3 text-left">Registration No.</th>
                            <th class="p-3 text-left">Status</th>

                        </tr>
                    </thead>

                    <tbody class="bg-white">
                    {{-- Row 1: Active Permit --}}
                    <tr class="border-t hover:bg-gray-50 transition-colors">
                        <td class="p-3">Oct 24, 2023</td>
                        <td class="p-3">Admin_Sarah</td>
                        <td class="p-3 font-medium text-blue-600">TechFlow Solutions Inc.</td>
                        <td class="p-3 text-gray-600">009-123-456-000</td>
                        <td class="p-3">LGU - Quezon City</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Renewed</span>
                        </td>
                        <td class="p-3">Jan 15, 2024</td>
                        <td class="p-3 font-mono text-gray-500">2024-MP-8821</td>
                        <td class="p-3">
                            <span class="flex items-center gap-1.5 text-green-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span> Active
                            </span>
                        </td>
                    </tr>

                    {{-- Row 2: Pending/In Progress --}}
                    <tr class="border-t hover:bg-gray-50 transition-colors">
                        <td class="p-3">Nov 02, 2023</td>
                        <td class="p-3">User_John</td>
                        <td class="p-3 font-medium text-blue-600">Green Horizon Café</td>
                        <td class="p-3 text-gray-600">112-987-654-000</td>
                        <td class="p-3">LGU - Makati City</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                        </td>
                        <td class="p-3 text-gray-400">Processing...</td>
                        <td class="p-3 text-gray-400">—</td>
                        <td class="p-3">
                            <span class="flex items-center gap-1.5 text-yellow-600">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span> For Review
                            </span>
                        </td>
                    </tr>

                    {{-- Row 3: Expired/Attention Needed --}}
                    <tr class="border-t hover:bg-gray-50 transition-colors">
                        <td class="p-3">Jan 12, 2023</td>
                        <td class="p-3">Admin_Sarah</td>
                        <td class="p-3 font-medium text-blue-600">Blue Coast Logistics</td>
                        <td class="p-3 text-gray-600">445-556-778-000</td>
                        <td class="p-3">LGU - Pasig City</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Expired</span>
                        </td>
                        <td class="p-3">Jan 20, 2023</td>
                        <td class="p-3 font-mono text-gray-500">2023-MP-1102</td>
                        <td class="p-3">
                            <span class="flex items-center gap-1.5 text-red-600">
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span> Overdue
                            </span>
                        </td>
                    </tr>
                </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection