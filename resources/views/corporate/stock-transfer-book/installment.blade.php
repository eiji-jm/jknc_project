@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedInstallment: null, showAddPanel: false, installmentMode: 'both' }" @keydown.escape.window="showAddPanel = false">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold" x-show="!showPreview">Installment</div>
            <div class="text-lg font-semibold" x-show="showPreview">Installment Payment Plan</div>
            <div class="flex-1"></div>
            <div class="flex items-center gap-2">
                <button type="button" data-open-add-panel @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Installment
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        <div x-show="!showPreview" class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <input type="text" id="installment-search" placeholder="Search installments..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        <div x-show="!showPreview" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full" id="installment-table">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Holder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. Shares</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">No. of Installments</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Value</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="installment-table-body">
                        @forelse ($installments as $installment)
                            <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('stock-transfer-book.installment.show', $installment) }}'">
                                <td class="px-4 py-3">{{ $installment->stock_number }}</td>
                                <td class="px-4 py-3">{{ $installment->subscriber }}</td>
                                <td class="px-4 py-3">{{ optional($installment->installment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $installment->no_shares }}</td>
                                <td class="px-4 py-3">{{ $installment->no_installments }}</td>
                                <td class="px-4 py-3">{{ $installment->total_value }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $status = strtolower((string) ($installment->payment_status ?? 'overdue'));
                                        $statusClasses = match ($status) {
                                            'paid' => 'bg-green-100 text-green-800',
                                            'partial' => 'bg-blue-100 text-blue-800',
                                            'overdue' => 'bg-amber-100 text-amber-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'voided' => 'bg-gray-200 text-gray-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($status ?: 'overdue') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No installment plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
            @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Add Installment Plan</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.installment.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Installment Entry Type</div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-900">
                                <input type="radio" name="installment_mode" value="stock_subscribe" x-model="installmentMode" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span>Stock Subscribe</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-900">
                                <input type="radio" name="installment_mode" value="stock_payment" x-model="installmentMode" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span>Stock Payment</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-900">
                                <input type="radio" name="installment_mode" value="both" x-model="installmentMode" class="border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                <span>Both</span>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Stock Subscribed</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600">Stock Number</label>
                                <input type="text" name="stock_number" list="installment-stock-numbers" data-autofill-key data-autofill-field="stock_number" data-default-field="stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Holder</label>
                                <input type="text" name="subscriber" list="index-shareholders" data-autofill-field="subscriber" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Holder name">
                            </div>
                            <div x-show="installmentMode !== 'stock_payment'">
                                <label class="text-xs text-gray-600">Date</label>
                                <input type="date" name="installment_date" data-autofill-field="installment_date" data-default-field="today" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div x-show="installmentMode !== 'stock_payment'">
                                <label class="text-xs text-gray-600">No. Shares</label>
                                <input type="number" name="no_shares" data-autofill-field="no_shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                            </div>
                            <div x-show="installmentMode !== 'stock_payment'">
                                <label class="text-xs text-gray-600">No. of Installments</label>
                                <input type="number" name="no_installments" data-autofill-field="no_installments" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="4">
                            </div>
                            <div x-show="installmentMode !== 'stock_payment'">
                                <label class="text-xs text-gray-600">Total Value (PhP)</label>
                                <input type="text" name="total_value" data-autofill-field="total_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100000.00">
                            </div>
                            <div x-show="installmentMode !== 'stock_payment'">
                                <label class="text-xs text-gray-600">Per Installment</label>
                                <input type="text" name="installment_amount" data-autofill-field="installment_amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="25000.00">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Status</label>
                                <input type="text" data-status-display class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600" value="Overdue" readonly>
                                <input type="hidden" name="status" value="overdue" data-autofill-field="status">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-green-200 bg-green-50 p-4" x-show="installmentMode === 'stock_payment' || installmentMode === 'both'">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Stock Payment</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600">Payment Date</label>
                                <input type="date" name="payment_date" data-default-field="today" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Amount Paid</label>
                                <input type="number" step="0.01" name="payment_amount" data-payment-amount class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="25000.00">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-600">Payment Remarks</label>
                                <textarea name="payment_remarks" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Optional note for stock payment. Saving this section will also reflect in ledger and journal."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload Document (PDF)</label>
                        <input type="file" name="document_path" class="mt-1 block w-full text-sm text-gray-600">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                        Save Installment
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

<datalist id="installment-stock-numbers">
    @foreach (($installments ?? collect())->pluck('stock_number')->filter()->unique() as $stockNumber)
        <option value="{{ $stockNumber }}"></option>
    @endforeach
</datalist>

<script>
    (function () {
        const searchInput = document.getElementById('installment-search');
        const tableBody = document.getElementById('installment-table-body');
        if (!searchInput || !tableBody) return;

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const rows = Array.from(tableBody.querySelectorAll('[data-search-row]'));
            const emptyRow = tableBody.querySelector('[data-empty-row]');
            let visibleCount = 0;

            rows.forEach((row) => {
                const matches = query === '' || row.textContent.toLowerCase().includes(query);
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount += 1;
            });

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 || visibleCount === 0 ? '' : 'none';
            }
        };

        searchInput.addEventListener('input', filterRows);
        filterRows();
    })();

    (function () {
        const endpoint = "{{ route('stock-transfer-book.lookup') }}";
        const defaultsEndpoint = "{{ route('stock-transfer-book.defaults') }}";
        const addPanel = document.querySelector('[data-add-panel]');
        if (!addPanel) return;

        const keyInput = addPanel.querySelector('[data-autofill-key]');
        const holderInput = addPanel.querySelector('[name="subscriber"]');
        const fieldInputs = Array.from(addPanel.querySelectorAll('[data-autofill-field]'));
        const paymentAmountInput = addPanel.querySelector('[data-payment-amount]');
        const installmentAmountInput = addPanel.querySelector('[name="installment_amount"]');
        const totalValueInput = addPanel.querySelector('[name="total_value"]');
        const hiddenStatus = addPanel.querySelector('[name="status"]');
        const statusDisplay = addPanel.querySelector('[data-status-display]');
        const modeInputs = Array.from(addPanel.querySelectorAll('[name="installment_mode"]'));
        const today = new Date().toISOString().split('T')[0];
        let existingPaymentTotal = 0;

        const currentMode = () => modeInputs.find((input) => input.checked)?.value || 'both';

        const valueFrom = (field, data) => {
            const installment = data.installment || {};
            const cert = data.certificate || {};
            const ledger = data.ledger || {};

            switch (field) {
                case 'stock_number':
                    return installment.stock_number || cert.stock_number || ledger.certificate_no || '';
                case 'subscriber':
                    return installment.holder_name || installment.subscriber || cert.stockholder_name || ledger.full_name || '';
                case 'installment_date':
                    return installment.installment_date || '';
                case 'no_shares':
                    return installment.no_shares || cert.number || ledger.shares || '';
                case 'no_installments':
                    return installment.no_installments || '';
                case 'total_value':
                    return installment.total_value || cert.amount || '';
                case 'installment_amount':
                    return installment.installment_amount || '';
                case 'status':
                    return installment.status || '';
                default:
                    return '';
            }
        };

        const refreshStatus = () => {
            if (!hiddenStatus || !statusDisplay) return;

            const mode = currentMode();
            const paidAmount = parseFloat(paymentAmountInput?.value || '0') + existingPaymentTotal;
            const expectedAmount = parseFloat(totalValueInput?.value || installmentAmountInput?.value || '0');

            let nextStatus = 'overdue';
            if (paidAmount > 0 && (expectedAmount <= 0 || paidAmount >= expectedAmount)) {
                nextStatus = 'paid';
            } else if (paidAmount > 0) {
                nextStatus = 'partial';
            }

            if (mode === 'stock_subscribe' && paidAmount <= 0) {
                nextStatus = 'overdue';
            }

            hiddenStatus.value = nextStatus;
            statusDisplay.value = nextStatus.replace(/\b\w/g, (char) => char.toUpperCase());
        };

        const runLookup = async (value) => {
            const key = (value ?? keyInput?.value ?? holderInput?.value ?? '').trim();
            if (!key) {
                existingPaymentTotal = 0;
                refreshStatus();
                return;
            }

            try {
                const res = await fetch(`${endpoint}?key=${encodeURIComponent(key)}`);
                if (!res.ok) return;
                const data = await res.json();

                fieldInputs.forEach((input) => {
                    const field = input.getAttribute('data-autofill-field');
                    const nextValue = valueFrom(field, data);
                    if (nextValue !== '' && nextValue !== null && nextValue !== undefined) {
                        input.value = nextValue;
                    }
                });

                existingPaymentTotal = parseFloat(data.installment?.payment_total || '0');
                refreshStatus();
            } catch (e) {
                // Ignore lookup errors.
            }
        };

        const applyDefaults = (defaults = {}) => {
            addPanel.querySelectorAll('[data-default-field]').forEach((field) => {
                const key = field.getAttribute('data-default-field');
                if (!key) return;

                if (key in defaults && defaults[key] !== null && defaults[key] !== undefined && defaults[key] !== '') {
                    field.value = defaults[key];
                    return;
                }

                if (key === 'today' && !field.value) {
                    field.value = today;
                }
            });
        };

        document.querySelector('[data-open-add-panel]')?.addEventListener('click', async () => {
            existingPaymentTotal = 0;
            addPanel.querySelector('form')?.reset();
            modeInputs.forEach((input) => {
                input.checked = input.value === 'both';
            });
            modeInputs.find((input) => input.value === 'both')?.dispatchEvent(new Event('change', { bubbles: true }));
            applyDefaults();
            refreshStatus();

            try {
                const res = await fetch(defaultsEndpoint);
                if (!res.ok) return;
                const defaults = await res.json();
                applyDefaults(defaults);
                refreshStatus();
            } catch (e) {
                // Ignore defaults errors.
            }
        });

        keyInput?.addEventListener('change', () => runLookup(keyInput.value));
        keyInput?.addEventListener('blur', () => runLookup(keyInput.value));
        holderInput?.addEventListener('change', () => runLookup(holderInput.value));
        holderInput?.addEventListener('blur', () => runLookup(holderInput.value));

        [paymentAmountInput, installmentAmountInput, totalValueInput].forEach((input) => {
            input?.addEventListener('input', refreshStatus);
            input?.addEventListener('change', refreshStatus);
        });

        modeInputs.forEach((input) => {
            input.addEventListener('change', () => {
                if (currentMode() === 'stock_payment' && existingPaymentTotal === 0) {
                    if (keyInput) keyInput.value = '';
                    if (holderInput) holderInput.value = '';
                }
                refreshStatus();
            });
        });
    })();
</script>
