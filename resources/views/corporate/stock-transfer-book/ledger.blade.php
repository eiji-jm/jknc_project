@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Ledger</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Shareholder
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- NAVIGATION TABS --}}
        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        {{-- SHAREHOLDER SUMMARY --}}
        <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Shareholder Information</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                <div>
                    <label class="text-xs text-gray-600">Current Residential Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter address">
                </div>
                <div>
                    <label class="text-xs text-gray-600">TIN</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter TIN">
                </div>
            </div>
        </div>

        {{-- LEDGER TABLE VIEW --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Family Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">First Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Middle Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($ledgers as $ledger)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('stock-transfer-book.ledger.show', $ledger) }}'">
                                <td class="px-4 py-3">{{ $ledger->family_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->first_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->middle_name }}</td>
                                <td class="px-4 py-3">{{ $ledger->nationality }}</td>
                                <td class="px-4 py-3">{{ $ledger->address }}</td>
                                <td class="px-4 py-3">{{ $ledger->tin }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No shareholders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ADD SHAREHOLDER SLIDER --}}
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
                    <div class="text-lg font-semibold">Add Shareholder</div>
                    <div class="flex-1"></div>
                    <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600">Family Name</label>
                            <input type="text" data-autofill-field="family_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Family name">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">First Name</label>
                            <input type="text" data-autofill-field="first_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="First name">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Middle Name</label>
                            <input type="text" data-autofill-field="middle_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Middle name">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Nationality</label>
                            <input type="text" data-autofill-field="nationality" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Nationality">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-600">Current Residential Address</label>
                            <input type="text" data-autofill-field="address" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Address">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">TIN</label>
                            <input type="text" data-autofill-field="tin" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="TIN">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Email</label>
                            <input type="email" data-autofill-field="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Email">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Phone</label>
                            <input type="text" data-autofill-field="phone" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Phone">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Number of Shares</label>
                            <input type="number" data-autofill-field="shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Certificate No.</label>
                            <input type="text" data-autofill-key data-autofill-field="certificate_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="CERT-0001">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Date Registered</label>
                            <input type="date" data-autofill-field="date_registered" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Status</label>
                            <input type="text" data-autofill-field="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Active">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="button">
                        Save Shareholder
                    </button>
                </div>
            </div>
        </div>
</div>
@endsection

<script>
    (function () {
        const endpoint = "{{ route('stock-transfer-book.lookup') }}";
        const container = document.currentScript.closest('body');
        const keyInput = container.querySelector('[data-autofill-key]');
        if (!keyInput) return;

        const fieldInputs = Array.from(container.querySelectorAll('[data-autofill-field]'));

        const valueFrom = (field, data) => {
            const ledger = data.ledger || {};
            const cert = data.certificate || {};
            switch (field) {
                case 'certificate_no':
                    return ledger.certificate_no || cert.stock_number || '';
                case 'family_name':
                    return ledger.family_name || '';
                case 'first_name':
                    return ledger.first_name || '';
                case 'middle_name':
                    return ledger.middle_name || '';
                case 'nationality':
                    return ledger.nationality || '';
                case 'address':
                    return ledger.address || '';
                case 'tin':
                    return ledger.tin || '';
                case 'email':
                    return ledger.email || '';
                case 'phone':
                    return ledger.phone || '';
                case 'shares':
                    return ledger.shares || '';
                case 'date_registered':
                    return ledger.date_registered || '';
                case 'status':
                    return ledger.status || '';
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
            } catch (e) {
                // ignore lookup errors
            }
        };

        keyInput.addEventListener('change', runLookup);
        keyInput.addEventListener('blur', runLookup);
    })();
</script>
