@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{ showAddPanel: false }"
     @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-0 overflow-x-auto">
                <a href="{{ route('corporate.formation') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">SEC-COI</a>
                <a href="{{ route('corporate.sec_aoi') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">SEC-AOI</a>
                <a href="{{ route('corporate.bylaws') }}" class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">Bylaws</a>
                <a href="{{ route('stock-transfer-book.index') }}" class="min-w-[180px] px-6 py-3 text-sm font-medium border-t border-b border-r border-blue-500 bg-blue-50 text-blue-700 text-center">Stock Transfer Book</a>
                <a href="{{ route('corporate.gis') }}" class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">GIS</a>
            </div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-bars text-sm"></i>
                </button>

                <button class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-50">
                    <i class="fas fa-table-cells-large text-sm"></i>
                </button>

                <button type="button"
                        @click="showAddPanel = true"
                        class="px-4 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <span class="text-base leading-none">+</span>
                    Add Ledger
                </button>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50 overflow-x-auto">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Ledger</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        @if(session('success'))
            <div class="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <input type="text"
                   id="ledger-search"
                   placeholder="Search shareholder or certificate..."
                   autocomplete="off"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
        </div>

        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Family Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">First Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Middle Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Current Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Certificate No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Registered</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="ledger-table-body">
                        @forelse ($ledgers as $ledger)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">{{ $ledger->family_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->first_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->middle_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->nationality }}</td>
                                <td class="px-4 py-3">{{ $ledger->current_address }}</td>
                                <td class="px-4 py-3">{{ $ledger->tin }}</td>
                                <td class="px-4 py-3">{{ $ledger->certificate_no ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $ledger->number_of_shares ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $ledger->date_registered ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $ledger->status ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">No ledger records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
                 @click.stop>

                <form action="{{ route('stock-transfer-book.ledger.store') }}"
                      method="POST"
                      class="h-full flex flex-col"
                      autocomplete="off">
                    @csrf

                    <input type="hidden" name="stock_transfer_book_index_id" id="stock_transfer_book_index_id">

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="text-lg font-semibold">Add Ledger</div>
                        <div class="flex-1"></div>
                        <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto space-y-5">

                        <div class="relative z-[300]">
                            <label class="text-xs text-gray-600">Family Name</label>
                            <input type="text"
                                   id="ledger_family_name"
                                   autocomplete="new-password"
                                   autocorrect="off"
                                   autocapitalize="off"
                                   spellcheck="false"
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                   placeholder="Search and select shareholder from Index">

                            <div id="ledger_family_name_suggestions"
                                 class="hidden absolute left-0 right-0 top-full mt-1 z-[9999] rounded-md border border-gray-200 bg-white shadow-xl max-h-60 overflow-y-auto">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">First Name</label>
                            <input type="text" id="ledger_first_name" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Middle Name</label>
                            <input type="text" id="ledger_middle_name" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Nationality</label>
                            <input type="text" id="ledger_nationality" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Current Residential Address</label>
                            <input type="text" id="ledger_current_address" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">TIN</label>
                            <input type="text" id="ledger_tin" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600">Certificate No.</label>
                                <input type="text" name="certificate_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="CERT-0001">
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Number of Shares</label>
                                <input type="number" name="number_of_shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Date Registered</label>
                                <input type="date" name="date_registered" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                    <option value="">Select status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="rounded-md bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-700">
                            Ledger uses the same details from Index, then adds ledger-specific stock details.
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                        <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                            Cancel
                        </button>
                        <div class="flex-1"></div>
                        <button id="save_ledger_btn"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                type="submit"
                                disabled>
                            Save Ledger
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    const indexRecords = @json($indexes);

    const ledgerSearchInput = document.getElementById('ledger-search');
    const ledgerTableBody = document.getElementById('ledger-table-body');

    if (ledgerSearchInput && ledgerTableBody) {
        ledgerSearchInput.addEventListener('input', () => {
            const query = ledgerSearchInput.value.trim().toLowerCase();
            const rows = ledgerTableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = (query === '' || rowText.includes(query)) ? '' : 'none';
            });
        });
    }

    const familyInput = document.getElementById('ledger_family_name');
    const suggestionBox = document.getElementById('ledger_family_name_suggestions');
    const indexIdInput = document.getElementById('stock_transfer_book_index_id');
    const firstNameInput = document.getElementById('ledger_first_name');
    const middleNameInput = document.getElementById('ledger_middle_name');
    const nationalityInput = document.getElementById('ledger_nationality');
    const addressInput = document.getElementById('ledger_current_address');
    const tinInput = document.getElementById('ledger_tin');
    const saveBtn = document.getElementById('save_ledger_btn');

    function clearLedgerIdentity() {
        indexIdInput.value = '';
        firstNameInput.value = '';
        middleNameInput.value = '';
        nationalityInput.value = '';
        addressInput.value = '';
        tinInput.value = '';
        saveBtn.disabled = true;
    }

    function hideSuggestions() {
        suggestionBox.classList.add('hidden');
        suggestionBox.innerHTML = '';
    }

    function fillLedgerContact(contact) {
        familyInput.value = contact.family_name || '';
        indexIdInput.value = contact.id || '';
        firstNameInput.value = contact.first_name || '';
        middleNameInput.value = contact.middle_name || '';
        nationalityInput.value = contact.nationality || '';
        addressInput.value = contact.current_address || '';
        tinInput.value = contact.tin || '';
        saveBtn.disabled = false;
        hideSuggestions();
    }

    function renderLedgerSuggestions(query = '') {
        const q = query.trim().toLowerCase();

        const filtered = indexRecords.filter(contact => {
            const full = `${contact.family_name || ''} ${contact.first_name || ''} ${contact.middle_name || ''}`.toLowerCase();
            return q === '' || full.includes(q);
        });

        suggestionBox.innerHTML = '';

        if (!filtered.length) {
            suggestionBox.innerHTML = `
                <div class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100">
                    No shareholder found from Index.
                </div>
            `;
            suggestionBox.classList.remove('hidden');
            return;
        }

        filtered.forEach(contact => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'w-full text-left px-3 py-3 hover:bg-gray-50 border-b border-gray-100 text-sm';

            option.innerHTML = `
                <div class="font-medium text-gray-900">
                    ${contact.family_name || ''}, ${contact.first_name || ''} ${contact.middle_name || ''}
                </div>
                <div class="text-xs text-gray-500 mt-0.5">
                    ${contact.nationality || ''}${contact.current_address ? ' • ' + contact.current_address : ''}
                </div>
            `;

            option.addEventListener('mousedown', (e) => {
                e.preventDefault();
                fillLedgerContact(contact);
            });

            suggestionBox.appendChild(option);
        });

        suggestionBox.classList.remove('hidden');
    }

    if (familyInput) {
        familyInput.addEventListener('focus', function () {
            renderLedgerSuggestions(this.value);
        });

        familyInput.addEventListener('input', function () {
            clearLedgerIdentity();
            renderLedgerSuggestions(this.value);
        });

        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== familyInput) {
                hideSuggestions();
            }
        });
    }
</script>
@endsection