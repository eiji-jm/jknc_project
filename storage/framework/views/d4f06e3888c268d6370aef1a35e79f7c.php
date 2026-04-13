<?php $__env->startSection('content'); ?>
<?php
    $tempContacts = [
        [
            'id' => 1,
            'family_name' => 'Caparoso',
            'first_name' => 'Brian',
            'middle_name' => 'P.',
            'nationality' => 'Filipino',
            'current_address' => 'Mandaue City, Cebu',
            'tin' => '123-456-789-000',
        ],
        [
            'id' => 2,
            'family_name' => 'Kelly',
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'nationality' => 'Filipino',
            'current_address' => 'Cebu City',
            'tin' => '234-567-890-111',
        ],
        [
            'id' => 3,
            'family_name' => 'Rodriguez',
            'first_name' => 'Carmen',
            'middle_name' => 'Maria',
            'nationality' => 'Filipino',
            'current_address' => 'Makati City',
            'tin' => '345-678-901-222',
        ],
        [
            'id' => 4,
            'family_name' => 'Santos',
            'first_name' => 'Miguel',
            'middle_name' => 'Antonio',
            'nationality' => 'Filipino',
            'current_address' => 'BGC, Taguig',
            'tin' => '456-789-012-333',
        ],
    ];
?>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{ showAddPanel: false }"
     @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-0 overflow-x-auto">
                <a href="<?php echo e(route('corporate.formation')); ?>"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    SEC-COI
                </a>

                <a href="<?php echo e(route('corporate.sec_aoi')); ?>"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    SEC-AOI
                </a>

                <a href="<?php echo e(route('corporate.bylaws')); ?>"
                   class="min-w-[118px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    Bylaws
                </a>

                <a href="<?php echo e(route('stock-transfer-book.index')); ?>"
                   class="min-w-[180px] px-6 py-3 text-sm font-medium border-t border-b border-r border-blue-500 bg-blue-50 text-blue-700 text-center">
                    Stock Transfer Book
                </a>

                <a href="<?php echo e(route('corporate.gis')); ?>"
                   class="min-w-[90px] px-6 py-3 text-sm font-medium border-t border-b border-r border-gray-200 bg-white text-gray-800 text-center hover:bg-gray-50">
                    GIS
                </a>
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
                    Add Index
                </button>

                <button class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50 overflow-x-auto">
            <a href="<?php echo e(route('stock-transfer-book.index')); ?>" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Index</a>
            <a href="<?php echo e(route('stock-transfer-book.installment')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="<?php echo e(route('stock-transfer-book.journal')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="<?php echo e(route('stock-transfer-book.ledger')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Ledger</a>
            <a href="<?php echo e(route('stock-transfer-book.certificates')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mx-4 mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc ml-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <input type="text"
                   id="index-search"
                   placeholder="Search shareholder..."
                   autocomplete="off"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
        </div>

        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Index</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Family Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">First Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Middle Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Current Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900" id="index-table-body">
                        <?php $__empty_1 = true; $__currentLoopData = $indexes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium"><?php echo e($loop->iteration); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->family_name); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->first_name); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->middle_name); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->nationality); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->current_address); ?></td>
                                <td class="px-4 py-3"><?php echo e($index->tin); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No index entries found.
                                </td>
                            </tr>
                        <?php endif; ?>
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

            <form action="<?php echo e(route('stock-transfer-book.index.store')); ?>"
                  method="POST"
                  class="h-full flex flex-col"
                  autocomplete="off">
                <?php echo csrf_field(); ?>

                <input type="hidden" name="contact_id" id="contact_id">

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="text-lg font-semibold">Add Index Entry</div>
                    <div class="flex-1"></div>
                    <button type="button" class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-5">

                    <div class="relative z-[300]">
                        <label class="text-xs text-gray-600">Family Name</label>
                        <input type="text"
                               id="family_name"
                               name="family_name"
                               autocomplete="new-password"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                               placeholder="Search and select contact">

                        <div id="family_name_suggestions"
                             class="hidden absolute left-0 right-0 top-full mt-1 z-[9999] rounded-md border border-gray-200 bg-white shadow-xl max-h-60 overflow-y-auto">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">First Name</label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               readonly
                               autocomplete="off"
                               class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm"
                               placeholder="">
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Middle Name</label>
                        <input type="text"
                               id="middle_name"
                               name="middle_name"
                               readonly
                               autocomplete="off"
                               class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm"
                               placeholder="">
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Nationality</label>
                        <input type="text"
                               id="nationality"
                               name="nationality"
                               readonly
                               autocomplete="off"
                               class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm"
                               placeholder="">
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Current Address</label>
                        <input type="text"
                               id="current_address"
                               name="current_address"
                               readonly
                               autocomplete="off"
                               class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm"
                               placeholder="">
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">TIN</label>
                        <input type="text"
                               id="tin"
                               name="tin"
                               readonly
                               autocomplete="off"
                               class="mt-1 block w-full rounded-md border border-gray-300 bg-slate-50 px-3 py-2 text-sm"
                               placeholder="">
                    </div>

                    <div class="rounded-md bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-700">
                        Temporary setup: choose from the sample contact list. Later, this will connect to the real Contacts module.
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                    <button type="button"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg"
                            @click="showAddPanel = false">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button type="submit"
                            id="save_index_btn"
                            disabled
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Index
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    const tempContacts = <?php echo json_encode($tempContacts, 15, 512) ?>;

    const searchInput = document.getElementById('index-search');
    const tableBody = document.getElementById('index-table-body');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const familyName = row.querySelector('td:nth-child(2)');
                const firstName = row.querySelector('td:nth-child(3)');
                const name = ((familyName?.textContent || '') + ' ' + (firstName?.textContent || '')).toLowerCase();
                row.style.display = (query === '' || name.includes(query)) ? '' : 'none';
            });
        });
    }

    const familyInput = document.getElementById('family_name');
    const suggestionBox = document.getElementById('family_name_suggestions');
    const firstNameInput = document.getElementById('first_name');
    const middleNameInput = document.getElementById('middle_name');
    const nationalityInput = document.getElementById('nationality');
    const addressInput = document.getElementById('current_address');
    const tinInput = document.getElementById('tin');
    const contactIdInput = document.getElementById('contact_id');
    const saveBtn = document.getElementById('save_index_btn');

    function clearFields() {
        firstNameInput.value = '';
        middleNameInput.value = '';
        nationalityInput.value = '';
        addressInput.value = '';
        tinInput.value = '';
        contactIdInput.value = '';
        saveBtn.disabled = true;
    }

    function hideSuggestions() {
        suggestionBox.classList.add('hidden');
        suggestionBox.innerHTML = '';
    }

    function fillFields(contact) {
        familyInput.value = contact.family_name || '';
        firstNameInput.value = contact.first_name || '';
        middleNameInput.value = contact.middle_name || '';
        nationalityInput.value = contact.nationality || '';
        addressInput.value = contact.current_address || '';
        tinInput.value = contact.tin || '';
        contactIdInput.value = contact.id || '';
        saveBtn.disabled = false;
        hideSuggestions();
    }

    function renderSuggestions(query = '') {
        const q = query.trim().toLowerCase();

        const filtered = tempContacts.filter(contact => {
            const full = `${contact.family_name} ${contact.first_name} ${contact.middle_name}`.toLowerCase();
            return q === '' || full.includes(q);
        });

        suggestionBox.innerHTML = '';

        if (!filtered.length) {
            suggestionBox.innerHTML = `
                <div class="px-3 py-3 text-sm text-gray-500 border-b border-gray-100">
                    No contact found.
                </div>
                <button type="button"
                        class="w-full text-left px-3 py-3 text-sm text-blue-600 hover:bg-blue-50">
                    Go to Contacts to add contact
                </button>
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
                    ${contact.family_name}, ${contact.first_name} ${contact.middle_name || ''}
                </div>
                <div class="text-xs text-gray-500 mt-0.5">
                    ${contact.nationality || ''}${contact.current_address ? ' • ' + contact.current_address : ''}
                </div>
            `;

            option.addEventListener('mousedown', (e) => {
                e.preventDefault();
                fillFields(contact);
            });

            suggestionBox.appendChild(option);
        });

        suggestionBox.classList.remove('hidden');
    }

    if (familyInput) {
        familyInput.addEventListener('focus', function () {
            renderSuggestions(this.value);
        });

        familyInput.addEventListener('input', function () {
            clearFields();
            renderSuggestions(this.value);
        });

        familyInput.addEventListener('keydown', function () {
            setTimeout(() => {
                renderSuggestions(this.value);
            }, 0);
        });

        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== familyInput) {
                hideSuggestions();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/stock-transfer-book/index.blade.php ENDPATH**/ ?>