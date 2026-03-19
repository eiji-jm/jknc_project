@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: simple title + actions (reuse from other pages) --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">UBO FORM</div>

            <div class="flex-1"></div>

            {{-- actions just for show, copy from company-general-information --}}
            <div class="flex items-center gap-2">
                <!-- blue Add button with dropdown arrow matching design -->
                <button id="add-ubo-btn" type="button"
                   class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add
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

        {{-- CONTENT --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Complete Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Specific Residential Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date of Birth</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Tax Identification No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ownership %</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            </td>
                            <td class="px-4 py-3 font-medium">Kelly, John</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">Nov. 12, 1970</td>
                            <td class="px-4 py-3">123-45-6789</td>
                            <td class="px-4 py-3">100%</td>
                            <td class="px-4 py-3">Direct (D)</td>
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
                            <td class="px-4 py-3 font-medium">Dimpas, MJ</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">Hispanic</td>
                            <td class="px-4 py-3">Jan. 5, 1990</td>
                            <td class="px-4 py-3">987-65-4321</td>
                            <td class="px-4 py-3">50%</td>
                            <td class="px-4 py-3">Indirect (I)</td>
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
                            <td class="px-4 py-3">5678 Oak Avenue, Makati</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">Mar. 22, 1985</td>
                            <td class="px-4 py-3">456-78-9012</td>
                            <td class="px-4 py-3">25%</td>
                            <td class="px-4 py-3">Direct (D)</td>
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
                            <td class="px-4 py-3">9012 Cedar Road, BGC</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">Jul. 14, 1978</td>
                            <td class="px-4 py-3">234-56-7890</td>
                            <td class="px-4 py-3">15%</td>
                            <td class="px-4 py-3">Indirect (I)</td>
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
                            <td class="px-4 py-3">3456 Maple Drive, Ortigas</td>
                            <td class="px-4 py-3">American</td>
                            <td class="px-4 py-3">Sep. 8, 1992</td>
                            <td class="px-4 py-3">567-89-0123</td>
                            <td class="px-4 py-3">10%</td>
                            <td class="px-4 py-3">Direct (D)</td>
                            <td class="px-4 py-3 text-center space-x-2 flex justify-center">
                                <button class="text-blue-600 hover:text-blue-700 hover:underline text-xs font-medium">Edit</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-red-600 hover:text-red-700 hover:underline text-xs font-medium">Delete</button>
                            </td>
                        </tr>
                        {{-- additional rows as needed --}}
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- off-canvas sidebar for adding UBO -->
    <div id="ubo-sidebar" class="fixed inset-y-0 right-0 w-96 bg-white border-l border-gray-200 shadow-lg transform translate-x-full transition-transform duration-200 hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <div class="text-lg font-semibold">Add Ultimate Beneficial Owner</div>
            <button id="ubo-sidebar-close" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-4 overflow-auto h-full">
            <!-- search bar -->
            <div class="mb-4">
                <input id="ubo-search" type="text" placeholder="Search contacts..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2" />
            </div>
            <!-- contacts list -->
            <div id="ubo-search-results" class="space-y-3">
                <div class="p-3 border border-gray-200 rounded-md hover:bg-blue-50 cursor-pointer transition-colors contact-item" data-name="Kelly, John" data-email="john.kelly@email.com" data-nationality="Filipino" data-address="1234 Elm Street, Ayala" data-dob="Nov. 12, 1970" data-tax-id="123-45-6789" data-ownership="100%" data-type="Direct (D)">
                    <div class="font-medium text-gray-900">Kelly, John</div>
                    <div class="text-xs text-gray-500">john.kelly@email.com</div>
                </div>
                <div class="p-3 border border-gray-200 rounded-md hover:bg-blue-50 cursor-pointer transition-colors contact-item" data-name="Dimpsa, MJ" data-email="mj.dimpsa@email.com" data-nationality="Hispanic" data-address="1234 Elm Street, Ayala" data-dob="Jan. 5, 1990" data-tax-id="987-65-4321" data-ownership="50%" data-type="Indirect (I)">
                    <div class="font-medium text-gray-900">Dimpsa, MJ</div>
                    <div class="text-xs text-gray-500">mj.dimpsa@email.com</div>
                </div>
                <div class="p-3 border border-gray-200 rounded-md hover:bg-blue-50 cursor-pointer transition-colors contact-item" data-name="Rodriguez, Carmen" data-email="carmen.rodriguez@email.com" data-nationality="Filipino" data-address="5678 Oak Avenue, Makati" data-dob="Mar. 22, 1985" data-tax-id="456-78-9012" data-ownership="25%" data-type="Direct (D)">
                    <div class="font-medium text-gray-900">Rodriguez, Carmen</div>
                    <div class="text-xs text-gray-500">carmen.rodriguez@email.com</div>
                </div>
                <div class="p-3 border border-gray-200 rounded-md hover:bg-blue-50 cursor-pointer transition-colors contact-item" data-name="Santos, Miguel" data-email="miguel.santos@email.com" data-nationality="Filipino" data-address="9012 Cedar Road, BGC" data-dob="Jul. 14, 1978" data-tax-id="234-56-7890" data-ownership="15%" data-type="Indirect (I)">
                    <div class="font-medium text-gray-900">Santos, Miguel</div>
                    <div class="text-xs text-gray-500">miguel.santos@email.com</div>
                </div>
                <div class="p-3 border border-gray-200 rounded-md hover:bg-blue-50 cursor-pointer transition-colors contact-item" data-name="Thompson, Elizabeth" data-email="elizabeth.thompson@email.com" data-nationality="American" data-address="3456 Maple Drive, Ortigas" data-dob="Sep. 8, 1992" data-tax-id="567-89-0123" data-ownership="10%" data-type="Direct (D)">
                    <div class="font-medium text-gray-900">Thompson, Elizabeth</div>
                    <div class="text-xs text-gray-500">elizabeth.thompson@email.com</div>
                </div>
            </div>
            </div>

            <!-- sample form fields mirroring screenshot -->
            <div class="space-y-4 hidden" id="ubo-form-fields">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-gray-200 rounded-full"></div>
                    <div>
                        <div class="font-semibold">Rafael Ortiz</div>
                        <div class="text-xs text-gray-500">rafael.ortiz@email.com</div>
                    </div>
                </div>

                <!-- Read-only fields from contacts list -->
                <div class="border-t pt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact Information (Read-Only)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed" value="Filipino">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Specific Residential Address</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed" value="789 Pine Avenue, Ayala">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed" value="May 15, 1988">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tax Identification No.</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed" value="123-45-6789">
                </div>

                <!-- Editable fields -->
                <div class="border-t pt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Beneficial Ownership Details</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">% of Ownership / % of Voting Rights</label>
                    <div class="flex">
                        <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed px-3 py-2" value="100">
                        <span class="inline-flex items-center px-2 bg-gray-50 text-gray-700">%</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Type of Beneficial Owner</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed px-3 py-2" value="Direct (D)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category of Beneficial Ownership</label>
                    <input type="text" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed px-3 py-2" value="Primary (P)">
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <button class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // toggle sidebar
        const sidebar = document.getElementById('ubo-sidebar');
        document.getElementById('add-ubo-btn').addEventListener('click', () => {
            sidebar.classList.remove('hidden');
            sidebar.classList.remove('translate-x-full');
        });
        document.getElementById('ubo-sidebar-close').addEventListener('click', () => {
            sidebar.classList.add('translate-x-full');
            setTimeout(() => sidebar.classList.add('hidden'), 200);
        });

        // search bar behavior
        const searchInput = document.getElementById('ubo-search');
        const resultsContainer = document.getElementById('ubo-search-results');
        const formContainer = document.getElementById('ubo-form-fields');

        // Handle contact item clicks
        function attachContactListeners() {
            document.querySelectorAll('.contact-item').forEach(item => {
                item.addEventListener('click', () => {
                    const person = {
                        name: item.dataset.name,
                        email: item.dataset.email,
                        nationality: item.dataset.nationality,
                        address: item.dataset.address,
                        dob: item.dataset.dob,
                        taxId: item.dataset.taxId,
                        ownership: item.dataset.ownership,
                        type: item.dataset.type
                    };
                    populateFormFields(person);
                    resultsContainer.classList.add('hidden');
                    formContainer.classList.remove('hidden');
                });
            });
        }

        // Initial attachment
        attachContactListeners();

        // Search functionality
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            const contactItems = document.querySelectorAll('.contact-item');

            contactItems.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const email = item.dataset.email.toLowerCase();

                if (query === '' || name.includes(query) || email.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function populateFormFields(person) {
            // Update the form with selected person's data
            const nameDisplay = formContainer.querySelector('.font-semibold');
            if (nameDisplay) nameDisplay.textContent = person.name;

            const inputs = formContainer.querySelectorAll('input[disabled]');
            inputs[0].value = person.nationality;
            inputs[1].value = person.address;
            inputs[2].value = person.dob;
            inputs[3].value = person.taxId;
            inputs[4].value = person.ownership;
            inputs[5].value = person.type;
        }
    </script>

</div>
@endsection
