<div id="createDealModal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <button id="createDealModalBackdrop" type="button" aria-label="Close create deal panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createDealPanel" class="pointer-events-auto flex h-full w-full max-w-[620px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[580px]">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5 sm:px-8">
                <h2 class="text-3xl font-semibold text-gray-900">Create Deals</h2>
                <button id="closeCreateDealModalBtn" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
            </div>

        <form id="createDealForm" class="flex min-h-0 flex-1 flex-col">
            @php
                $selectedOwner = collect($owners)->firstWhere('id', (int) ($defaultOwnerId ?? 0)) ?: collect($owners)->first();
                $selectedOwnerId = (int) ($selectedOwner['id'] ?? 0);
                $selectedOwnerName = $selectedOwner['name'] ?? $ownerLabel;
            @endphp
            <input id="deal_owner_id" type="hidden" name="owner_id" value="{{ $selectedOwnerId }}">
            <div class="min-h-0 flex-1 overflow-y-auto px-6 pb-6 pt-5 sm:px-8">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Deal Information</p>
                <div class="relative">
                    <button
                        id="dealOwnerDropdownTrigger"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-700 hover:bg-gray-100"
                    >
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span id="dealOwnerSelectedLabel">Owner: {{ $selectedOwnerName }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                    </button>

                    <div id="dealOwnerDropdownMenu" class="absolute right-0 z-30 mt-2 hidden w-72 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                        <div class="relative mb-2">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input id="dealOwnerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="max-h-56 space-y-1 overflow-y-auto">
                            @foreach ($owners as $owner)
                                @php
                                    $ownerInitials = strtoupper(collect(explode(' ', trim($owner['name'])))
                                        ->filter()
                                        ->map(fn ($segment) => mb_substr($segment, 0, 1))
                                        ->take(2)
                                        ->implode(''));
                                @endphp
                                <button
                                    type="button"
                                    class="deal-owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                    data-owner-id="{{ $owner['id'] }}"
                                    data-owner-name="{{ $owner['name'] }}"
                                    data-owner-email="{{ $owner['email'] }}"
                                >
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-semibold text-blue-700">
                                        {{ $ownerInitials }}
                                    </span>
                                    <span>
                                        <span class="block text-sm text-gray-700">{{ $owner['name'] }}</span>
                                        <span class="block text-xs text-gray-500">{{ $owner['email'] }}</span>
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="dealNameInput" class="text-right text-sm font-semibold text-gray-700">Deal Name:</label>
                    <input id="dealNameInput" type="text" class="h-9 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="companyNameInput" class="text-right text-sm font-semibold text-gray-700">Company Name:</label>
                    <div class="relative" data-select-root="company">
                        <input id="companyNameInput" type="text" autocomplete="off" class="h-9 w-full rounded border border-gray-300 px-3 pr-9 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" data-select-toggle="company"><i class="fas fa-chevron-down text-xs"></i></button>
                        <div class="absolute left-0 right-0 top-[calc(100%+4px)] z-20 hidden max-h-40 overflow-y-auto rounded border border-gray-200 bg-white shadow-lg" data-select-menu="company"></div>
                    </div>
                </div>

                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="contactNameInput" class="text-right text-sm font-semibold text-gray-700">Contact Name:</label>
                    <div class="grid grid-cols-[1fr_auto] items-center gap-2">
                        <div class="relative" data-select-root="contact">
                            <input id="contactNameInput" type="text" autocomplete="off" class="h-9 w-full rounded border border-gray-300 px-3 pr-9 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" data-select-toggle="contact"><i class="fas fa-chevron-down text-xs"></i></button>
                            <div class="absolute left-0 right-0 top-[calc(100%+4px)] z-20 hidden max-h-40 overflow-y-auto rounded border border-gray-200 bg-white shadow-lg" data-select-menu="contact"></div>
                        </div>
                        <button type="button" class="flex h-6 w-6 items-center justify-center rounded-full border border-gray-400 text-xs text-gray-700 hover:bg-gray-50" title="Add Contact">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="stageInput" class="text-right text-sm font-semibold text-gray-700">Stage:</label>
                    <div class="relative" data-select-root="stage">
                        <input id="stageInput" type="text" readonly placeholder="Choose a stage" class="h-9 w-full rounded border border-gray-300 px-3 pr-9 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" data-select-toggle="stage"><i class="fas fa-chevron-down text-xs"></i></button>
                        <div class="absolute left-0 right-0 top-[calc(100%+4px)] z-20 hidden max-h-40 overflow-y-auto rounded border border-blue-400 bg-white shadow-lg" data-select-menu="stage"></div>
                    </div>
                </div>

                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="amountInput" class="text-right text-sm font-semibold text-gray-700">Amount:</label>
                    <input id="amountInput" type="text" class="h-9 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <label for="closingDateInput" class="text-right text-sm font-semibold text-gray-700">Closing Date:</label>
                    <input id="closingDateInput" type="text" placeholder="MM/DD/YYYY" class="h-9 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[130px_1fr] items-start gap-3">
                    <label for="descriptionInput" class="pt-2 text-right text-sm font-semibold text-gray-700">Description:</label>
                    <textarea id="descriptionInput" rows="1" placeholder="Few words about this deal" class="rounded border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                </div>
            </div>

            <div class="mt-4 border-t border-gray-200 pt-3">
                <button id="toggleProductsSectionBtn" type="button" class="text-sm text-gray-600 hover:text-gray-900">+ Products</button>
                <div id="productsSection" class="mt-3 hidden">
                    <h3 class="mb-2 text-base font-semibold text-gray-700">Associated Products</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-2 py-2 text-left">Product</th>
                                    <th class="px-2 py-2 text-left">List Price (₱)</th>
                                    <th class="px-2 py-2 text-left">Quantity</th>
                                    <th class="px-2 py-2 text-left">Discount (%)</th>
                                    <th class="px-2 py-2 text-left">Total (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-2 py-2">
                                        <input id="productSearchInput" list="dealProductOptions" type="text" placeholder="Search Product" class="h-8 w-full rounded border border-red-200 px-2 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <datalist id="dealProductOptions">
                                            @foreach ($productOptions as $product)
                                                <option value="{{ $product }}"></option>
                                            @endforeach
                                        </datalist>
                                    </td>
                                    <td class="px-2 py-2 text-gray-400">-</td>
                                    <td class="px-2 py-2 text-gray-400">-</td>
                                    <td class="px-2 py-2 text-gray-400">-</td>
                                    <td class="px-2 py-2 text-gray-400">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </div>

            <div class="mt-auto flex justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4 sm:px-8">
                <button id="cancelCreateDealBtn" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-9 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-10 text-sm font-medium text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('createDealModal');
        if (!modal) {
            return;
        }

        const openBtn = document.getElementById('openCreateDealModalBtn');
        const closeBtn = document.getElementById('closeCreateDealModalBtn');
        const cancelBtn = document.getElementById('cancelCreateDealBtn');
        const backdrop = document.getElementById('createDealModalBackdrop');
        const panel = document.getElementById('createDealPanel');
        const form = document.getElementById('createDealForm');
        const productsToggle = document.getElementById('toggleProductsSectionBtn');
        const productsSection = document.getElementById('productsSection');
        const dealOwnerDropdownTrigger = document.getElementById('dealOwnerDropdownTrigger');
        const dealOwnerDropdownMenu = document.getElementById('dealOwnerDropdownMenu');
        const dealOwnerSearch = document.getElementById('dealOwnerSearch');
        const dealOwnerSelectedLabel = document.getElementById('dealOwnerSelectedLabel');
        const dealOwnerIdInput = document.getElementById('deal_owner_id');
        const dealOwnerOptions = Array.from(document.querySelectorAll('.deal-owner-option'));

        const options = {
            company: @json($companyOptions),
            contact: @json($contactOptions),
            stage: @json($stageOptions),
        };

        const closeAllDropdowns = () => {
            modal.querySelectorAll('[data-select-menu]').forEach((menu) => menu.classList.add('hidden'));
            dealOwnerDropdownMenu?.classList.add('hidden');
        };

        const openModal = () => {
            if (!panel) {
                return;
            }
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
                backdrop?.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            });
        };

        const closeModal = () => {
            if (!panel) {
                return;
            }
            backdrop?.classList.add('opacity-0');
            panel.classList.add('translate-x-full');
            closeAllDropdowns();
            document.body.classList.remove('overflow-hidden');
            window.setTimeout(() => {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }, 300);
        };

        const renderMenu = (key, filterText = '') => {
            const menu = modal.querySelector(`[data-select-menu="${key}"]`);
            if (!menu) {
                return;
            }

            const lowered = filterText.trim().toLowerCase();
            const filtered = options[key].filter((item) => item.toLowerCase().includes(lowered));
            menu.innerHTML = '';

            filtered.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'block w-full px-2 py-1.5 text-left text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-700';
                button.textContent = item;
                button.addEventListener('click', () => {
                    const input = modal.querySelector(`#${key}NameInput, #${key}Input`);
                    if (input) {
                        input.value = item;
                    }
                    menu.classList.add('hidden');
                });
                menu.appendChild(button);
            });

            if (filtered.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'px-2 py-2 text-xs text-gray-400';
                empty.textContent = 'No results found';
                menu.appendChild(empty);
            }
        };

        ['company', 'contact', 'stage'].forEach((key) => {
            const input = modal.querySelector(`#${key}NameInput, #${key}Input`);
            const toggle = modal.querySelector(`[data-select-toggle="${key}"]`);
            const menu = modal.querySelector(`[data-select-menu="${key}"]`);
            if (!input || !toggle || !menu) {
                return;
            }

            renderMenu(key);

            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isHidden = menu.classList.contains('hidden');
                closeAllDropdowns();
                if (isHidden) {
                    menu.classList.remove('hidden');
                    if (key === 'stage') {
                        renderMenu(key);
                    } else {
                        renderMenu(key, input.value);
                        input.focus();
                    }
                }
            });

            if (key !== 'stage') {
                input.addEventListener('focus', () => {
                    closeAllDropdowns();
                    renderMenu(key, input.value);
                    menu.classList.remove('hidden');
                });

                input.addEventListener('input', () => {
                    renderMenu(key, input.value);
                    menu.classList.remove('hidden');
                });
            }
        });

        const closeOwnerDropdown = () => {
            dealOwnerDropdownMenu?.classList.add('hidden');
        };

        dealOwnerDropdownTrigger?.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = dealOwnerDropdownMenu?.classList.contains('hidden');
            closeAllDropdowns();
            if (isHidden) {
                dealOwnerDropdownMenu?.classList.remove('hidden');
                dealOwnerSearch?.focus();
            }
        });

        dealOwnerSearch?.addEventListener('input', () => {
            const keyword = (dealOwnerSearch.value || '').trim().toLowerCase();
            dealOwnerOptions.forEach((option) => {
                const name = (option.dataset.ownerName || '').toLowerCase();
                const email = (option.dataset.ownerEmail || '').toLowerCase();
                option.classList.toggle('hidden', keyword !== '' && !name.includes(keyword) && !email.includes(keyword));
            });
        });

        dealOwnerOptions.forEach((option) => {
            option.addEventListener('click', () => {
                if (dealOwnerSelectedLabel) {
                    dealOwnerSelectedLabel.textContent = `Owner: ${option.dataset.ownerName || ''}`;
                }
                if (dealOwnerIdInput) {
                    dealOwnerIdInput.value = option.dataset.ownerId || '';
                }
                closeOwnerDropdown();
            });
        });

        openBtn?.addEventListener('click', openModal);
        closeBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', closeModal);
        backdrop?.addEventListener('click', closeModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (dealOwnerDropdownMenu && !dealOwnerDropdownMenu.classList.contains('hidden')) {
                    closeOwnerDropdown();
                    return;
                }
                closeModal();
            }
        });

        document.addEventListener('click', (event) => {
            if (!modal.classList.contains('hidden') && !modal.contains(event.target)) {
                closeAllDropdowns();
                return;
            }

            if (dealOwnerDropdownMenu && !dealOwnerDropdownMenu.classList.contains('hidden')) {
                const clickedOwnerTrigger = dealOwnerDropdownTrigger ? dealOwnerDropdownTrigger.contains(event.target) : false;
                const clickedOwnerSearch = dealOwnerSearch ? dealOwnerSearch.contains(event.target) : false;
                if (!dealOwnerDropdownMenu.contains(event.target) && !clickedOwnerTrigger && !clickedOwnerSearch) {
                    closeOwnerDropdown();
                }
            }
        });

        productsToggle?.addEventListener('click', () => {
            const isHidden = productsSection.classList.toggle('hidden');
            productsToggle.textContent = isHidden ? '+ Products' : '- Products';
        });

        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            closeModal();
        });
    });
</script>
