@extends('layouts.app')
@section('title', 'Contacts')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: simple title + actions --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Contacts</div>

            <div class="flex-1"></div>

            {{-- actions --}}
            <div class="flex items-center gap-2">
                <!-- blue Add button -->
                <button type="button" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
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
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="contacts-table-body">
                        @forelse ($contacts as $contact)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-gray-900 hover:underline">
                                        {{ $contact->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">{{ $contact->email }}</td>
                                <td class="px-4 py-3">{{ $contact->nationality }}</td>
                                <td class="px-4 py-3">{{ $contact->address }}</td>
                                <td class="px-4 py-3">{{ $contact->tax_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No contacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

{{-- ADD CONTACT SLIDER --}}
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
            <div class="text-lg font-semibold">Add Contact</div>
            <div class="flex-1"></div>
            <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-600">Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Full name">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Position</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Position">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Email</label>
                    <input type="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="name@email.com">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Phone</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="+63 9xx xxx xxxx">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Nationality</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Nationality">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Tax ID</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Tax ID">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-600">Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Address">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
            <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                Cancel
            </button>
            <div class="flex-1"></div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="button">
                Save Contact
            </button>
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
