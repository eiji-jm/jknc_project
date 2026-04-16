@extends('layouts.app')
@section('title', 'Stock Transfer Book – Journal')
@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, showAddPanel: false, selectedEntry: null }">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Journal</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button type="button" data-open-add-panel @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Transaction
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        @include('corporate.stock-transfer-book.partials.section-tabs', ['currentStockTransferTab' => 'journal'])

        {{-- SEARCH --}}
        <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <input type="text" id="journal-search" placeholder="Search journal entries..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        {{-- JOURNAL TABLE VIEW --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full" id="journal-table">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Journal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Ledger Folio</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Particulars</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Transaction Type</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="journal-table-body">
    @forelse ($journals as $journal)
        <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('stock-transfer-book.journal.show', $journal) }}'">
            <td class="px-4 py-3">{{ optional($journal->entry_date)->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $journal->journal_no }}</td>
            <td class="px-4 py-3">{{ $journal->ledger_folio }}</td>
            <td class="px-4 py-3">{{ $journal->particulars }}</td>
            <td class="px-4 py-3">{{ $journal->no_shares }}</td>
            <td class="px-4 py-3">{{ $journal->transaction_type }}</td>
        </tr>
    @empty
        <tr data-empty-row>
            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No journal entries found.</td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>
        </div>

        {{-- PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedEntry">
                <div class="grid grid-cols-3 gap-6 h-[calc(100vh-13rem)]">

                    {{-- PDF VIEWER SIDE --}}
                    <div class="col-span-2 bg-gray-900 rounded-lg overflow-hidden flex flex-col">
                        {{-- PDF VIEWER TOOLBAR --}}
                        <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="text-gray-400 text-sm mx-2">Page 1 of 1</span>
                            <div class="flex-1"></div>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>

                        {{-- PDF DOCUMENT MOCKUP --}}
                        <div class="flex-1 overflow-auto p-6 flex items-center justify-center">
                            <div class="bg-white w-full max-w-md rounded-sm shadow-2xl" style="aspect-ratio: 8.5/11;">
                                <div class="p-8 h-full flex flex-col justify-between text-center">
                                    {{-- HEADER --}}
                                    <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                        <h1 class="text-2xl font-bold text-gray-900">STOCK CERTIFICATE</h1>
                                        <p class="text-sm text-gray-600 mt-2">John Kelly & Company</p>
                                    </div>

                                    {{-- MAIN CONTENT --}}
                                    <div class="flex-1 flex flex-col justify-center space-y-4">
                                        <p class="text-sm text-gray-700">
                                            This certifies that <strong x-text="selectedEntry.shareholder"></strong> is the owner of
                                        </p>
                                        <div class="border-2 border-gray-400 rounded p-3">
                                            <p class="text-2xl font-bold text-gray-900" x-text="selectedEntry.noShares"></p>
                                            <p class="text-xs text-gray-600">fully paid and non-assessable shares</p>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            Certificate No. <strong x-text="selectedEntry.certificateNo"></strong>
                                        </p>
                                    </div>

                                    {{-- SIGNATURE LINES --}}
                                    <div class="border-t-2 border-gray-800 pt-4 space-y-3">
                                        <div class="grid grid-cols-2 gap-4 text-xs">
                                            <div>
                                                <div class="h-6 border-t border-gray-800 mb-1"></div>
                                                <p class="font-semibold">President</p>
                                            </div>
                                            <div>
                                                <div class="h-6 border-t border-gray-800 mb-1"></div>
                                                <p class="font-semibold">Secretary</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500" x-text="selectedEntry.date"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAILS SIDE --}}
                    <div class="col-span-1 overflow-y-auto space-y-4">

                        {{-- CERTIFICATE INFORMATION --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Certificate Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Certificate No.</p>
                                    <p x-text="selectedEntry.certificateNo" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Journal Reference</p>
                                    <p x-text="selectedEntry.journalNo" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. of Shares</p>
                                    <p x-text="selectedEntry.noShares" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date Issued</p>
                                    <p x-text="selectedEntry.date" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- TRANSACTION DETAILS --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Transaction Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</p>
                                    <div class="mt-2">
                                            <span x-text="selectedEntry.transactionType" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                :class="{
                                                    'bg-green-100 text-green-800': selectedEntry.transactionType === 'Issuance',
                                                    'bg-blue-100 text-blue-800': selectedEntry.transactionType === 'Transfer',
                                                    'bg-red-100 text-red-800': selectedEntry.transactionType === 'Cancellation'
                                                }">
                                            </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Shareholder</p>
                                    <p x-text="selectedEntry.shareholder" class="text-sm text-gray-900 mt-1 font-medium"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Particulars</p>
                                    <p x-text="selectedEntry.particulars" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ledger Folio</p>
                                    <p x-text="selectedEntry.ledgerFolio" class="text-sm text-gray-900 mt-1 font-medium"></p>
                                </div>
                            </div>
                        </div>

                        {{-- REMARKS --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Remarks</h3>
                            <p x-text="selectedEntry.remarks" class="text-sm text-gray-700"></p>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="space-y-2 pt-2">
                            <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-download"></i>
                                Download PDF
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i>
                                Print
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>

    {{-- ADD TRANSACTION SLIDER --}}
    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
            <div x-show="showAddPanel" data-add-panel
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
                <div class="text-lg font-semibold">Add Journal Transaction</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.journal.store') }}" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Date</label>
                        <input type="date" name="entry_date" data-autofill-field="entry_date" data-default-field="today" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Journal No.</label>
                        <input type="text" name="journal_no" data-default-field="journal_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="JNL-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Ledger Folio</label>
                        <input type="text" name="ledger_folio" data-autofill-field="ledger_folio" data-default-field="ledger_folio" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="LED-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">No. Shares</label>
                        <input type="number" name="no_shares" data-autofill-field="no_shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Particulars</label>
                        <input type="text" name="particulars" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter particulars">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Transaction Type</label>
                        <select name="transaction_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option>Issuance</option>
                            <option>Cancellation</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Certificate No.</label>
                        <input type="text" name="certificate_no" data-autofill-key data-autofill-field="certificate_no" data-default-field="stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Shareholder</label>
                        <input type="text" name="shareholder" list="index-shareholders" data-autofill-field="shareholder" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Shareholder name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Remarks</label>
                        <textarea name="remarks" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add remarks"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

<datalist id="index-shareholders">
    @foreach (($indexShareholders ?? collect()) as $name)
        <option value="{{ $name }}"></option>
    @endforeach
</datalist>

<script>
    // Filter only actual journal rows and keep the empty state predictable.
    (function () {
        const searchInput = document.getElementById('journal-search');
        const tableBody = document.getElementById('journal-table-body');
        if (!searchInput || !tableBody) return;

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const rows = Array.from(tableBody.querySelectorAll('[data-search-row]'));
            const emptyRow = tableBody.querySelector('[data-empty-row]');
            let visibleCount = 0;

            rows.forEach((row) => {
                const matches = query === '' || row.textContent.toLowerCase().includes(query);
                row.style.display = matches ? '' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 || visibleCount === 0 ? '' : 'none';
            }
        };

        searchInput.addEventListener('input', () => {
            filterRows();
        });

        filterRows();
    })();

    (function () {
        const endpoint = "{{ route('stock-transfer-book.lookup') }}";
        const defaultsEndpoint = "{{ route('stock-transfer-book.defaults') }}";
        const container = document.currentScript.closest('body');
        const keyInput = container.querySelector('[data-autofill-key]');
        const shareholderInput = container.querySelector('[name="shareholder"]');

        const fieldInputs = Array.from(container.querySelectorAll('[data-autofill-field]'));

        const valueFrom = (field, data) => {
            const cert = data.certificate || {};
            const ledger = data.ledger || {};
            const journal = data.journal || {};
            const installment = data.installment || {};

            switch (field) {
                case 'certificate_no':
                    return cert.stock_number || ledger.certificate_no || journal.certificate_no || installment.stock_number || '';
                case 'shareholder':
                    return journal.shareholder || cert.stockholder_name || ledger.full_name || installment.subscriber || '';
                case 'ledger_folio':
                    return journal.ledger_folio || '';
                case 'no_shares':
                    return journal.no_shares || ledger.shares || cert.number || installment.no_shares || '';
                case 'entry_date':
                    return journal.entry_date || '';
                default:
                    return '';
            }
        };

        const runLookup = async (value) => {
            const key = (value ?? keyInput?.value ?? shareholderInput?.value ?? '').trim();
            if (!key) return;
            try {
                const res = await fetch(`${endpoint}?key=${encodeURIComponent(key)}`);
                if (!res.ok) return;
                const data = await res.json();
                fieldInputs.forEach((input) => {
                    const field = input.getAttribute('data-autofill-field');
                    const value = valueFrom(field, data);
                    if (value !== '' && value !== null && value !== undefined) {
                        input.value = value;
                    }
                });
            } catch (e) {
                // ignore lookup errors
            }
        };

        if (keyInput) {
            keyInput.addEventListener('change', () => runLookup(keyInput.value));
            keyInput.addEventListener('blur', () => runLookup(keyInput.value));
        }

        if (shareholderInput) {
            shareholderInput.addEventListener('change', () => runLookup(shareholderInput.value));
            shareholderInput.addEventListener('blur', () => runLookup(shareholderInput.value));
        }

        const addButton = container.querySelector('[data-open-add-panel]');
        const addPanel = container.querySelector('[data-add-panel]');
        if (addButton && addPanel) {
            addButton.addEventListener('click', async () => {
                try {
                    const res = await fetch(defaultsEndpoint);
                    if (!res.ok) return;
                    const defaults = await res.json();
                    const fields = addPanel.querySelectorAll('[data-default-field]');
                    fields.forEach((field) => {
                        const key = field.getAttribute('data-default-field');
                        if (!key) return;
                        if (key in defaults) {
                            field.value = defaults[key];
                        }
                    });
                } catch (e) {
                    // ignore defaults errors
                }
            });
        }
    })();
</script>
