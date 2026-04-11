@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedInstallment: null, showAddPanel: false }" @keydown.escape.window="showAddPanel = false">
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

        <div x-show="!showPreview">
            @include('corporate.stock-transfer-book.partials.section-tabs', ['currentStockTransferTab' => 'installment'])
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
                                <td class="px-4 py-3">{{ number_format((float) $installment->total_value, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $status = strtolower((string) ($installment->payment_status ?? $installment->status ?? 'unpaid'));
                                        $statusClasses = match ($status) {
                                            'paid' => 'bg-green-100 text-green-800',
                                            'partial' => 'bg-blue-100 text-blue-800',
                                            'unpaid' => 'bg-amber-100 text-amber-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'voided' => 'bg-gray-200 text-gray-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($status ?: 'unpaid') }}
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
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('stock-transfer-book.installment.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <input type="hidden" name="installment_mode" value="stock_subscribe">
                <input type="hidden" name="status" value="unpaid">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Stock Subscribed</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-600">Holder</label>
                                <select
                                    name="subscriber"
                                    id="holderSelect"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="">Select holder from Index</option>
                                    @foreach (($indexShareholders ?? []) as $holder)
                                        <option
                                            value="{{ $holder['name'] }}"
                                            data-stock-number="{{ $holder['stock_number'] }}"
                                            data-shares="{{ $holder['shares'] }}"
                                            data-par-value="{{ $holder['par_value'] }}"
                                        >
                                            {{ $holder['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-[11px] text-gray-500">This should come from Index.</p>
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Stock Number</label>
                                <input
                                    type="text"
                                    name="stock_number"
                                    id="stockNumberInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600"
                                    placeholder="Auto-filled from Index"
                                    readonly
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Date Subscribed</label>
                                <input
                                    type="date"
                                    name="installment_date"
                                    id="installmentDateInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">No. Shares</label>
                                <input
                                    type="number"
                                    name="no_shares"
                                    id="sharesInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                    placeholder="Auto-filled from Index"
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">No. of Installments</label>
                                <input
                                    type="number"
                                    name="no_installments"
                                    id="installmentsInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                    placeholder="4"
                                    min="1"
                                    required
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">PAR</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="par_value"
                                    id="parValueInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                    placeholder="100.00"
                                    value="{{ $defaultParValue }}"
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Total Value (PhP)</label>
                                <input
                                    type="text"
                                    name="total_value"
                                    id="totalValueInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm"
                                    placeholder="100000.00"
                                    readonly
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Per Installment</label>
                                <input
                                    type="text"
                                    name="installment_amount"
                                    id="perInstallmentInput"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm"
                                    placeholder="25000.00"
                                    readonly
                                >
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Initial Status</label>
                                <div class="mt-1 flex h-[42px] items-center rounded-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-700">
                                    Unpaid
                                </div>
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

<script>
    function updateInstallmentFinancials() {
        const sharesInput = document.getElementById('sharesInput');
        const installmentsInput = document.getElementById('installmentsInput');
        const parInput = document.getElementById('parValueInput');
        const totalValueInput = document.getElementById('totalValueInput');
        const perInstallmentInput = document.getElementById('perInstallmentInput');

        const shares = parseFloat(sharesInput?.value || '0');
        const installments = parseInt(installmentsInput?.value || '0', 10);
        const par = parseFloat(parInput?.value || '0');

        const totalValue = shares > 0 && par > 0 ? shares * par : 0;
        const perInstallment = totalValue > 0 && installments > 0 ? totalValue / installments : 0;

        if (totalValueInput) {
            totalValueInput.value = totalValue > 0 ? totalValue.toFixed(2) : '';
        }

        if (perInstallmentInput) {
            perInstallmentInput.value = perInstallment > 0 ? perInstallment.toFixed(2) : '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('installment-search');
        const tableBody = document.getElementById('installment-table-body');

        if (searchInput && tableBody) {
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
        }

        const holderSelect = document.getElementById('holderSelect');
        const stockNumberInput = document.getElementById('stockNumberInput');
        const installmentDateInput = document.getElementById('installmentDateInput');
        const sharesInput = document.getElementById('sharesInput');
        const installmentsInput = document.getElementById('installmentsInput');
        const parValueInput = document.getElementById('parValueInput');
        const totalValueInput = document.getElementById('totalValueInput');
        const perInstallmentInput = document.getElementById('perInstallmentInput');
        const openButton = document.querySelector('[data-open-add-panel]');
        const addPanelForm = document.querySelector('[data-add-panel] form');

        const today = new Date().toISOString().split('T')[0];

        function applyHolderData() {
            const selected = holderSelect.options[holderSelect.selectedIndex];
            if (!selected || !selected.value) {
                stockNumberInput.value = '';
                sharesInput.value = '';
                updateInstallmentFinancials();
                return;
            }

            stockNumberInput.value = selected.dataset.stockNumber || '';
            sharesInput.value = selected.dataset.shares || '';
            if ((!parValueInput.value || parseFloat(parValueInput.value || '0') <= 0) && selected.dataset.parValue) {
                parValueInput.value = selected.dataset.parValue;
            }
            if (!installmentDateInput.value) {
                installmentDateInput.value = today;
            }

            updateInstallmentFinancials();
        }

        if (openButton) {
            openButton.addEventListener('click', function () {
                if (addPanelForm) addPanelForm.reset();
                if (installmentDateInput) installmentDateInput.value = today;
                if (stockNumberInput) stockNumberInput.value = '';
                if (sharesInput) sharesInput.value = '';
                if (totalValueInput) totalValueInput.value = '';
                if (perInstallmentInput) perInstallmentInput.value = '';
                @if(!empty($defaultParValue))
                    parValueInput.value = "{{ $defaultParValue }}";
                @endif
            });
        }

        if (holderSelect) {
            holderSelect.addEventListener('change', applyHolderData);
            holderSelect.addEventListener('input', applyHolderData);
        }

        [sharesInput, installmentsInput, parValueInput].forEach((input) => {
            if (!input) return;
            input.addEventListener('input', updateInstallmentFinancials);
            input.addEventListener('change', updateInstallmentFinancials);
            input.addEventListener('keyup', updateInstallmentFinancials);
        });
    });
</script>