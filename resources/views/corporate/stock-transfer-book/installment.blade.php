@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedInstallment: null, showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
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

        {{-- NAVIGATION TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        {{-- SEARCH --}}
        <div x-show="!showPreview" class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <input type="text" id="installment-search" placeholder="Search installments..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        {{-- INSTALLMENT TABLE VIEW --}}
        <div x-show="!showPreview" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full" id="installment-table">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Stock Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Subscriber</th>
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
                    $status = strtolower((string) ($installment->payment_status ?? 'pending'));
                    $statusClasses = match ($status) {
                        'completed' => 'bg-green-100 text-green-800',
                        'on-going' => 'bg-blue-100 text-blue-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        'voided' => 'bg-gray-200 text-gray-800',
                        default => 'bg-amber-100 text-amber-800',
                    };
                @endphp
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $statusClasses }}">
                    {{ ucfirst($status ?: 'pending') }}
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

        {{-- INSTALLMENT PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedInstallment">
                <div class="grid grid-cols-3 gap-6 h-[calc(100vh-13rem)]">

                    {{-- DOCUMENT SIDE --}}
                    <div class="col-span-2 bg-gray-900 rounded-lg overflow-hidden flex flex-col">
                        {{-- DOCUMENT VIEWER TOOLBAR --}}
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

                        {{-- DOCUMENT MOCKUP --}}
                        <div class="flex-1 overflow-auto p-6 flex items-center justify-center">
                            <div class="bg-white w-full max-w-md rounded-sm shadow-2xl" style="aspect-ratio: 8.5/11;">
                                <div class="p-8 h-full flex flex-col justify-start text-left">
                                    {{-- HEADER --}}
                                    <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                        <h1 class="text-lg font-bold text-gray-900">INSTALLMENT SCHEDULE</h1>
                                        <p class="text-xs text-gray-600 mt-2">John Kelly & Company</p>
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="space-y-3 text-xs">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="font-semibold text-gray-700">Subscriber:</p>
                                                <p x-text="selectedInstallment.subscriber" class="text-gray-900"></p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-700">Stock No.:</p>
                                                <p x-text="selectedInstallment.stockNumber" class="text-gray-900"></p>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-400 pt-2">
                                            <p class="font-semibold text-gray-700">Total Value: <span x-text="'₱' + selectedInstallment.totalValue" class="text-gray-900 font-bold"></span></p>
                                        </div>

                                        <div class="border-t border-gray-400 pt-2">
                                            <p class="font-semibold text-gray-700 mb-1">Payment Schedule:</p>
                                            <template x-for="(inst, index) in selectedInstallment.installmentDetails" :key="index">
                                                <div class="flex justify-between text-xs py-1 border-b border-gray-300">
                                                    <span><span x-text="inst.no"></span> - <span x-text="inst.dueDate"></span></span>
                                                    <span class="font-semibold" x-text="'₱' + inst.amount"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAILS SIDE --}}
                    <div class="col-span-1 overflow-y-auto space-y-4">

                        {{-- SUBSCRIBER INFORMATION --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Subscriber Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Subscriber Name</p>
                                    <p x-text="selectedInstallment.subscriber" class="text-base font-semibold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Stock Number</p>
                                    <p x-text="selectedInstallment.stockNumber" class="text-base font-semibold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date</p>
                                    <p x-text="selectedInstallment.date" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. of Shares</p>
                                    <p x-text="selectedInstallment.noShares" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</p>
                                    <span x-text="selectedInstallment.status" class="inline-block mt-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"></span>
                                </div>
                            </div>
                        </div>

                        {{-- PAYMENT SUMMARY --}}
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Payment Summary</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Value</p>
                                    <p x-text="'₱' + selectedInstallment.totalValue" class="text-2xl font-bold text-green-600 mt-1"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. Installments</p>
                                        <p x-text="selectedInstallment.noInstallments" class="text-lg font-bold text-blue-600 mt-1"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Per Installment</p>
                                        <p x-text="'₱' + selectedInstallment.installmentAmount" class="text-sm font-bold text-gray-900 mt-1"></p>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-blue-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid/Remaining</p>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        <span x-text="selectedInstallment.paidInstallments" class="text-green-600"></span>/<span x-text="selectedInstallment.noInstallments"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- INSTALLMENT SCHEDULE --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Installment Schedule</h3>
                            <div class="space-y-2">
                                <template x-for="(inst, index) in selectedInstallment.installmentDetails" :key="index">
                                    <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                            :class="{
                                                'bg-green-100 text-green-800': inst.status === 'Paid',
                                                'bg-yellow-100 text-yellow-800': inst.status === 'Pending'
                                            }">
                                            <i :class="inst.status === 'Paid' ? 'fas fa-check' : 'fas fa-clock'"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900">
                                                <span x-text="inst.no"></span> - <span x-text="inst.dueDate"></span>
                                            </p>
                                            <p class="text-xs text-gray-600" x-text="inst.status === 'Paid' ? 'Paid: ' + inst.paidDate : 'Pending'"></p>
                                        </div>
                                        <p class="text-xs font-bold text-gray-900 flex-shrink-0" x-text="'₱' + inst.amount"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="space-y-2 pt-2">
                            <button class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-money-check"></i>
                                Record Payment
                            </button>
                            <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-file-pdf"></i>
                                Download Schedule
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>

    {{-- ADD INSTALLMENT SLIDER --}}
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
                <div class="text-lg font-semibold">Add Installment Plan</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.installment.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Stock Number</label>
                        <input type="text" name="stock_number" data-autofill-key data-autofill-field="stock_number" data-default-field="stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Subscriber</label>
                        <input type="text" name="subscriber" list="index-shareholders" data-autofill-field="subscriber" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Subscriber name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date</label>
                        <input type="date" name="installment_date" data-autofill-field="installment_date" data-default-field="today" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">No. Shares</label>
                        <input type="number" name="no_shares" data-autofill-field="no_shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">No. of Installments</label>
                        <input type="number" name="no_installments" data-autofill-field="no_installments" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="4">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Total Value (PhP)</label>
                        <input type="text" name="total_value" data-autofill-field="total_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="100,000.00">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Per Installment</label>
                        <input type="text" name="installment_amount" data-autofill-field="installment_amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="25,000.00">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Payment Status</label>
                        <input type="text" data-status-display class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600" value="Pending" readonly>
                        <input type="hidden" name="status" value="pending" data-autofill-field="status">
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

<script>
    // Filter only actual installment rows and keep the empty state predictable.
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

        const fieldInputs = Array.from(container.querySelectorAll('[data-autofill-field]'));
        const today = new Date().toISOString().split('T')[0];

        const valueFrom = (field, data) => {
            const installment = data.installment || {};
            const cert = data.certificate || {};
            switch (field) {
                case 'stock_number':
                    return installment.stock_number || cert.stock_number || '';
                case 'subscriber':
                    return installment.subscriber || cert.stockholder_name || '';
                case 'installment_date':
                    return installment.installment_date || '';
                case 'no_shares':
                    return installment.no_shares || cert.number || '';
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

        const runLookup = async () => {
            const key = keyInput.value.trim();
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

                const statusDisplay = container.querySelector('[data-status-display]');
                const hiddenStatus = addPanel?.querySelector('[name="status"]');
                if (statusDisplay && hiddenStatus) {
                    statusDisplay.value = (hiddenStatus.value || 'pending').replace(/\b\w/g, (char) => char.toUpperCase());
                }
            } catch (e) {
                // ignore lookup errors
            }
        };

        if (keyInput) {
            keyInput.addEventListener('change', runLookup);
            keyInput.addEventListener('blur', runLookup);
        }

        const addPanel = container.querySelector('[data-add-panel]');
        const applyDefaults = (panel, defaults = {}) => {
            if (!panel) return;

            const fields = panel.querySelectorAll('[data-default-field]');
            fields.forEach((field) => {
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

        const addButton = container.querySelector('[data-open-add-panel]');
        if (addButton && addPanel) {
            addButton.addEventListener('click', async () => {
                applyDefaults(addPanel);

                try {
                    const res = await fetch(defaultsEndpoint);
                    if (!res.ok) return;
                    const defaults = await res.json();
                    applyDefaults(addPanel, defaults);

                    const hiddenStatus = addPanel.querySelector('[name="status"]');
                    const statusDisplay = addPanel.querySelector('[data-status-display]');
                    if (hiddenStatus) {
                        hiddenStatus.value = 'pending';
                    }
                    if (statusDisplay) {
                        statusDisplay.value = 'Pending';
                    }
                } catch (e) {
                    // ignore defaults errors
                }
            });
        }
    })();
</script>
