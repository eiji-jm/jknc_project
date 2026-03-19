@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Index</div>

            <div class="flex-1"></div>
            <button @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Index
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- NAVIGATION TABS --}}
        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

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
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="index-table-body">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                            <td class="px-4 py-3 font-medium">1</td>
                            <td class="px-4 py-3">Kelly</td>
                            <td class="px-4 py-3">John</td>
                            <td class="px-4 py-3">Michael</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">123-45-6789</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                            <td class="px-4 py-3 font-medium">2</td>
                            <td class="px-4 py-3">Rodriguez</td>
                            <td class="px-4 py-3">Carmen</td>
                            <td class="px-4 py-3">Maria</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">5678 Oak Avenue, Makati</td>
                            <td class="px-4 py-3">456-78-9012</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                            <td class="px-4 py-3 font-medium">3</td>
                            <td class="px-4 py-3">Santos</td>
                            <td class="px-4 py-3">Miguel</td>
                            <td class="px-4 py-3">Antonio</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">9012 Cedar Road, BGC</td>
                            <td class="px-4 py-3">234-56-7890</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                            <td class="px-4 py-3 font-medium">4</td>
                            <td class="px-4 py-3">Thompson</td>
                            <td class="px-4 py-3">Elizabeth</td>
                            <td class="px-4 py-3">Anne</td>
                            <td class="px-4 py-3">American</td>
                            <td class="px-4 py-3">3456 Maple Drive, Ortigas</td>
                            <td class="px-4 py-3">567-89-0123</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ADD INDEX SLIDER --}}
    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Add Index Entry</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Family Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter family name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">First Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter first name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Middle Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter middle name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Nationality</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter nationality">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Current Address</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter current address">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">TIN</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter TIN">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save Index
                </button>
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
