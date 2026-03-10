@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: simple title + actions --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Contacts</div>

            <div class="flex-1"></div>

            {{-- actions --}}
            <div class="flex items-center gap-2">
                <!-- blue Add button -->
                <button id="add-contact-btn" type="button"
                   class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Contact
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2zm0 4h14a1 1 0 110 2H3a1 1 0 110-2z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH BAR --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <input type="text" id="contacts-search" placeholder="Search contacts by name..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        {{-- CONTENT --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Tax ID</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="contacts-table-body">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Kelly, John</td>
                            <td class="px-4 py-3">john.kelly@email.com</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">123-45-6789</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Dimpsa, MJ</td>
                            <td class="px-4 py-3">mj.dimpsa@email.com</td>
                            <td class="px-4 py-3">Hispanic</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">987-65-4321</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Rodriguez, Carmen</td>
                            <td class="px-4 py-3">carmen.rodriguez@email.com</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">5678 Oak Avenue, Makati</td>
                            <td class="px-4 py-3">456-78-9012</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Santos, Miguel</td>
                            <td class="px-4 py-3">miguel.santos@email.com</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">9012 Cedar Road, BGC</td>
                            <td class="px-4 py-3">234-56-7890</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Thompson, Elizabeth</td>
                            <td class="px-4 py-3">elizabeth.thompson@email.com</td>
                            <td class="px-4 py-3">American</td>
                            <td class="px-4 py-3">3456 Maple Drive, Ortigas</td>
                            <td class="px-4 py-3">567-89-0123</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
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
    const searchInput = document.getElementById('contacts-search');
    const tableBody = document.getElementById('contacts-table-body');
    const rows = tableBody.querySelectorAll('tr');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const name = nameCell ? nameCell.textContent.toLowerCase() : '';

            if (query === '' || name.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
