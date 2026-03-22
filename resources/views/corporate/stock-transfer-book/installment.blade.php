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

                <button type="button" @click="showAddPanel = true" class="px-4 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <span class="text-base leading-none">+</span>
                    Add Installment
                </button>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50 overflow-x-auto">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Installment</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
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

        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Subscriber</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. of Installments</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Value</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Per Installment</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($installments as $installment)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">{{ $installment->stock_number }}</td>
                                <td class="px-4 py-3">{{ $installment->subscriber }}</td>
                                <td class="px-4 py-3">{{ optional($installment->installment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $installment->no_shares }}</td>
                                <td class="px-4 py-3">{{ $installment->no_installments }}</td>
                                <td class="px-4 py-3">{{ $installment->total_value }}</td>
                                <td class="px-4 py-3">{{ $installment->installment_amount }}</td>
                                <td class="px-4 py-3">{{ $installment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No installment plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

            <form action="{{ route('stock-transfer-book.installment.store') }}" method="POST" class="h-full flex flex-col" autocomplete="off">
                @csrf

                <input type="hidden" name="stock_transfer_book_ledger_id" id="installment_ledger_id">

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="text-lg font-semibold">Add Installment Plan</div>
                    <div class="flex-1"></div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" @click="showAddPanel = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4">

                    <div class="relative z-[300]">
                        <label class="text-xs text-gray-600">Ledger / Subscriber</label>
                        <input type="text"
                               id="installment_ledger_picker"
                               autocomplete="new-password"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                               placeholder="Search and select ledger">

                        <div id="installment_ledger_suggestions"
                             class="hidden absolute left-0 right-0 top-full mt-1 z-[9999] rounded-md border border-gray-200 bg-white shadow-xl max-h-60 overflow-y-auto">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600">Stock Number</label>
                            <input type="text" name="stock_number" id="installment_stock_number" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm" placeholder="Auto-filled from ledger">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Subscriber</label>
                            <input type="text" name="subscriber" id="installment_subscriber" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm" placeholder="Auto-filled from ledger">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Date</label>
                            <input type="date" name="installment_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">No. Shares</label>
                            <input type="number" name="no_shares" id="installment_no_shares" class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm" placeholder="Auto-filled from ledger">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">No. of Installments</label>
                            <input type="number" name="no_installments" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="4">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Total Value (PhP)</label>
                            <input type="number" step="0.01" name="total_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100000.00">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Per Installment</label>
                            <input type="number" step="0.01" name="installment_amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="25000.00">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                <option value="">Select status</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" type="button" @click="showAddPanel = false">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button id="save_installment_btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" type="submit" disabled>
                        Save Installment
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    const ledgerRecords = @json($ledgerRecords);

    const pickerInput = document.getElementById('installment_ledger_picker');
    const suggestionBox = document.getElementById('installment_ledger_suggestions');
    const ledgerIdInput = document.getElementById('installment_ledger_id');
    const stockNumberInput = document.getElementById('installment_stock_number');
    const subscriberInput = document.getElementById('installment_subscriber');
    const noSharesInput = document.getElementById('installment_no_shares');
    const saveBtn = document.getElementById('save_installment_btn');

    function clearInstallmentSelection() {
        ledgerIdInput.value = '';
        stockNumberInput.value = '';
        subscriberInput.value = '';
        noSharesInput.value = '';
        saveBtn.disabled = true;
    }

    function hideSuggestions() {
        suggestionBox.classList.add('hidden');
        suggestionBox.innerHTML = '';
    }

    function fillInstallmentLedger(item) {
        pickerInput.value = item.shareholder || '';
        ledgerIdInput.value = item.id || '';
        stockNumberInput.value = item.certificate_no || '';
        subscriberInput.value = item.shareholder || '';
        noSharesInput.value = item.number_of_shares || '';
        saveBtn.disabled = false;
        hideSuggestions();
    }

    function renderInstallmentSuggestions(query = '') {
        const q = query.trim().toLowerCase();

        const filtered = ledgerRecords.filter(item => {
            const full = `${item.shareholder || ''} ${item.certificate_no || ''}`.toLowerCase();
            return q === '' || full.includes(q);
        });

        suggestionBox.innerHTML = '';

        if (!filtered.length) {
            suggestionBox.innerHTML = `<div class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100">No ledger record found.</div>`;
            suggestionBox.classList.remove('hidden');
            return;
        }

        filtered.forEach(item => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'w-full text-left px-3 py-3 hover:bg-gray-50 border-b border-gray-100 text-sm';

            option.innerHTML = `
                <div class="font-medium text-gray-900">${item.shareholder || ''}</div>
                <div class="text-xs text-gray-500 mt-0.5">${item.certificate_no || ''}${item.number_of_shares ? ' • ' + item.number_of_shares + ' shares' : ''}</div>
            `;

            option.addEventListener('mousedown', (e) => {
                e.preventDefault();
                fillInstallmentLedger(item);
            });

            suggestionBox.appendChild(option);
        });

        suggestionBox.classList.remove('hidden');
    }

    if (pickerInput) {
        pickerInput.addEventListener('focus', function () {
            renderInstallmentSuggestions(this.value);
        });

        pickerInput.addEventListener('input', function () {
            clearInstallmentSelection();
            renderInstallmentSuggestions(this.value);
        });

        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== pickerInput) {
                hideSuggestions();
            }
        });
    }
</script>
@endsection