<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="<?php echo e(route('stock-transfer-book')); ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">Ledger</div>
            <div class="flex-1"></div>
            <a href="<?php echo e(route('stock-transfer-book.index')); ?>" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Go to Index</a>
        </div>

        <div class="border-t border-gray-100"></div>

        
        <?php echo $__env->make('corporate.stock-transfer-book.partials.section-tabs', ['currentStockTransferTab' => 'ledger'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <input type="text" id="ledger-search" placeholder="Search shareholders..." class="w-full rounded-md border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2 text-sm" />
        </div>

        
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full" id="ledger-table">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Shareholder</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Certificate No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Transaction</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Entry Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Shares</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="ledger-table-body">
                        <?php $__empty_1 = true; $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-search-row class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='<?php echo e(route('stock-transfer-book.ledger.show', $ledger)); ?>'">
                                <td class="px-4 py-3"><?php echo e(trim(collect([$ledger->first_name, $ledger->middle_name, $ledger->family_name])->filter()->implode(' ')) ?: $ledger->family_name); ?></td>
                                <td class="px-4 py-3"><?php echo e($ledger->certificate_no); ?></td>
                                <td class="px-4 py-3"><?php echo e($ledger->journal?->transaction_type ?: $ledger->journal?->particulars); ?></td>
                                <td class="px-4 py-3"><?php echo e(optional($ledger->date_registered)->format('M d, Y')); ?></td>
                                <td class="px-4 py-3"><?php echo e($ledger->shares); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr data-empty-row>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No shareholders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                @click.stop
            >
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="text-lg font-semibold">Add Shareholder</div>
                    <div class="flex-1"></div>
                    <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="<?php echo e(route('stock-transfer-book.ledger.store')); ?>" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <input type="text" data-contact-search class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Search contacts...">
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4" data-contact-empty>
                            <div class="text-sm text-gray-600">No contact selected.</div>
                            <a href="<?php echo e(route('contacts')); ?>" class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50">
                                Go to Contacts
                            </a>
                        </div>

                        <div class="hidden rounded-xl border border-gray-200 bg-white p-4" data-contact-card>
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900" data-contact-name></div>
                                    <div class="text-xs text-gray-500" data-contact-email></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600">Nationality</label>
                                <input type="text" name="nationality" data-autofill-field="nationality" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Specific Residential Address</label>
                                <input type="text" name="address" data-autofill-field="address" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Date Registered</label>
                                <input type="date" name="date_registered" data-autofill-field="date_registered" data-default-field="today" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Tax Identification No.</label>
                                <input type="text" name="tin" data-autofill-field="tin" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Number of Shares</label>
                                <input type="number" name="shares" data-autofill-field="shares" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Certificate No.</label>
                                <input type="text" name="certificate_no" data-autofill-key data-autofill-field="certificate_no" data-default-field="stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="STK-0001">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Phone</label>
                                <input type="text" name="phone" data-autofill-field="phone" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-600">Upload Document (PDF)</label>
                                <input type="file" name="document_path" class="mt-1 block w-full text-sm text-gray-600">
                            </div>
                        </div>

                        <input type="hidden" name="first_name" data-contact-first>
                        <input type="hidden" name="middle_name" data-contact-middle>
                        <input type="hidden" name="family_name" data-contact-last>
                        <input type="hidden" name="email" data-contact-email-input>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                        <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                            Cancel
                        </button>
                        <div class="flex-1"></div>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                            Save Shareholder
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
<?php $__env->stopSection(); ?>

<script>
    // Filter only actual ledger rows and keep the empty state predictable.
    (function () {
        const searchInput = document.getElementById('ledger-search');
        const tableBody = document.getElementById('ledger-table-body');
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
        const endpoint = "<?php echo e(route('stock-transfer-book.lookup')); ?>";
        const defaultsEndpoint = "<?php echo e(route('stock-transfer-book.defaults')); ?>";
        const container = document.currentScript.closest('body');
        const keyInput = container.querySelector('[data-autofill-key]');

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

        if (keyInput) {
            keyInput.addEventListener('change', runLookup);
            keyInput.addEventListener('blur', runLookup);
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

        const contacts = <?php echo json_encode($contacts ?? [], 15, 512) ?>;
        const searchInput = container.querySelector('[data-contact-search]');
        const emptyState = container.querySelector('[data-contact-empty]');
        const card = container.querySelector('[data-contact-card]');
        const cardName = container.querySelector('[data-contact-name]');
        const cardEmail = container.querySelector('[data-contact-email]');
        const firstInput = container.querySelector('[data-contact-first]');
        const middleInput = container.querySelector('[data-contact-middle]');
        const lastInput = container.querySelector('[data-contact-last]');
        const emailInput = container.querySelector('[data-contact-email-input]');
        const nationalityInput = container.querySelector('[name="nationality"]');
        const addressInput = container.querySelector('[name="address"]');
        const tinInput = container.querySelector('[name="tin"]');

        const list = document.createElement('div');
        list.className = 'rounded-xl border border-gray-200 bg-white divide-y divide-gray-100 max-h-48 overflow-auto';
        list.style.display = 'none';
        searchInput?.parentElement?.appendChild(list);

        const splitName = (name) => {
            if (!name) return { first: '', middle: '', last: '' };
            if (name.includes(',')) {
                const [last, rest] = name.split(',').map((part) => part.trim());
                const parts = rest.split(' ').filter(Boolean);
                return { first: parts[0] || '', middle: parts.slice(1).join(' '), last: last || '' };
            }
            const parts = name.split(' ').filter(Boolean);
            return { first: parts[0] || '', middle: parts.slice(1, -1).join(' '), last: parts.length > 1 ? parts[parts.length - 1] : '' };
        };

        const renderList = (items) => {
            list.innerHTML = '';
            if (!items.length) {
                list.style.display = 'none';
                return;
            }
            items.forEach((contact) => {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'w-full text-left px-3 py-2 hover:bg-gray-50';
                row.innerHTML = `<div class="text-sm font-medium text-gray-900">${contact.name}</div><div class="text-xs text-gray-500">${contact.email || ''}</div>`;
                row.addEventListener('click', () => {
                    const nameParts = splitName(contact.name);
                    firstInput.value = nameParts.first;
                    middleInput.value = nameParts.middle;
                    lastInput.value = nameParts.last;
                    emailInput.value = contact.email || '';
                    if (nationalityInput && !nationalityInput.value) {
                        nationalityInput.value = contact.nationality || '';
                    }
                    if (addressInput && !addressInput.value) {
                        addressInput.value = contact.address || '';
                    }
                    if (tinInput && !tinInput.value) {
                        tinInput.value = contact.tax_id || '';
                    }

                    cardName.textContent = contact.name;
                    cardEmail.textContent = contact.email || '';
                    card.classList.remove('hidden');
                    emptyState.classList.add('hidden');
                    list.style.display = 'none';
                });
                list.appendChild(row);
            });
            list.style.display = 'block';
        };

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.toLowerCase().trim();
                if (!term) {
                    list.style.display = 'none';
                    return;
                }
                const matches = contacts.filter((c) => (c.name || '').toLowerCase().includes(term));
                renderList(matches);
            });
        }
    })();
</script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/stock-transfer-book/ledger.blade.php ENDPATH**/ ?>