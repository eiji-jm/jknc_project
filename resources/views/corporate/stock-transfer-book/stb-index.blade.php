@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Index</div>

            <div class="flex-1"></div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- FILTER SEARCH --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <input type="text" id="index-search" placeholder="Search shareholder..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        {{-- INDEX TABLE --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Index</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Family Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">First Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Middle Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Current Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="index-table-body">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium">1</td>
                            <td class="px-4 py-3">Kelly</td>
                            <td class="px-4 py-3">John</td>
                            <td class="px-4 py-3">Michael</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">123-45-6789</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium">2</td>
                            <td class="px-4 py-3">Rodriguez</td>
                            <td class="px-4 py-3">Carmen</td>
                            <td class="px-4 py-3">Maria</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">5678 Oak Avenue, Makati</td>
                            <td class="px-4 py-3">456-78-9012</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium">3</td>
                            <td class="px-4 py-3">Santos</td>
                            <td class="px-4 py-3">Miguel</td>
                            <td class="px-4 py-3">Antonio</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">9012 Cedar Road, BGC</td>
                            <td class="px-4 py-3">234-56-7890</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium">4</td>
                            <td class="px-4 py-3">Thompson</td>
                            <td class="px-4 py-3">Elizabeth</td>
                            <td class="px-4 py-3">Anne</td>
                            <td class="px-4 py-3">American</td>
                            <td class="px-4 py-3">3456 Maple Drive, Ortigas</td>
                            <td class="px-4 py-3">567-89-0123</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script>
    // Search functionality
    const searchInput = document.getElementById('index-search');
    const tableBody = document.getElementById('index-table-body');
    const rows = tableBody.querySelectorAll('tr');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        rows.forEach(row => {
            const familyName = row.querySelector('td:nth-child(2)');
            const firstName = row.querySelector('td:nth-child(3)');
            const name = (familyName?.textContent + ' ' + firstName?.textContent).toLowerCase();

            if (query === '' || name.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
