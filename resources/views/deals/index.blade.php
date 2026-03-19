@extends('layouts.app')

@section('content')
@php
    $formatCurrency = static fn (int $amount): string => 'P'.number_format($amount);
@endphp

<div class="bg-white p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Deals</h1>
            <p class="text-sm text-gray-500">Track and manage your sales pipeline</p>
        </div>
        <button id="openCreateDealModalBtn" type="button" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i> Add Deal
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
        <select class="h-9 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            <option>All Deals</option>
        </select>
        <select class="h-9 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            <option>Created Date</option>
        </select>
        <span id="totalDealsCount" class="text-xs text-gray-500">{{ $totalDeals }} deals</span>
    </div>

    <div id="dealSelectionBar" class="mb-4 hidden items-center justify-between rounded-lg border border-blue-100 bg-blue-50 px-3 py-2">
        <p class="text-sm font-medium text-blue-700"><span id="selectedDealCount">0</span> selected</p>
        <div class="flex items-center gap-2 text-xs">
            <button type="button" class="rounded border border-blue-200 bg-white px-2.5 py-1 text-blue-700 hover:bg-blue-100">Assign Owner</button>
            <button type="button" class="rounded border border-blue-200 bg-white px-2.5 py-1 text-blue-700 hover:bg-blue-100">Update Stage</button>
            <button id="clearDealSelectionBtn" type="button" class="rounded border border-blue-200 bg-white px-2.5 py-1 text-blue-700 hover:bg-blue-100">Clear Selection</button>
        </div>
    </div>

    <div class="overflow-x-auto pb-2">
        <div id="dealsBoard" class="flex min-w-max gap-3">
            @foreach ($stageColumns as $column)
                @php
                    $isClosedLost = $column['stage'] === 'Closed Lost';
                    $headerClass = $isClosedLost ? 'bg-red-600' : 'bg-slate-800';
                @endphp
                <section class="stage-column w-[230px] rounded-xl border border-gray-200 bg-gray-50" data-stage-column="{{ $column['stage'] }}">
                    <header class="group/column rounded-t-xl px-3 py-2 text-white {{ $headerClass }}">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <h2 class="stage-name text-xs font-semibold">{{ $column['stage'] }}</h2>
                                <p class="stage-total text-xs opacity-90">{{ $formatCurrency($column['total_amount']) }} • {{ count($column['deals']) }} {{ count($column['deals']) === 1 ? 'Deal' : 'Deals' }}</p>
                            </div>
                            <div class="relative">
                            <button type="button" class="stage-actions-trigger opacity-0 transition group-hover/column:opacity-100" data-stage="{{ $column['stage'] }}" title="Stage actions">
                                <i class="fas fa-ellipsis-vertical text-xs text-white/90"></i>
                            </button>
                                <div class="stage-actions-menu absolute right-0 z-20 mt-2 hidden w-44 rounded-lg border border-gray-200 bg-white py-1 text-xs text-gray-700 shadow-lg" data-stage-menu="{{ $column['stage'] }}">
                                    <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="new-deal">New Deal</button>
                                    <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="new-stage">New Stage</button>
                                    <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="edit-description">Edit Description</button>
                                    <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left text-red-600 hover:bg-red-50" data-action="delete-stage">Delete Stage</button>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="stage-cards space-y-2 p-2">
                        @forelse ($column['deals'] as $deal)
                            @php
                                $ownerInitials = strtoupper(substr($deal['owner_name'], 0, 1).substr(strrchr(' '.$deal['owner_name'], ' '), 1, 1));
                            @endphp
                            <article
                                class="deal-card group/deal relative rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition hover:cursor-pointer hover:border-blue-300 hover:shadow"
                                data-deal-id="{{ $deal['id'] }}"
                                data-deal-name="{{ $deal['deal_name'] }}"
                                data-deal-stage="{{ $column['stage'] }}"
                                data-deal-contact="{{ $deal['contact_name'] }}"
                                data-deal-company="{{ $deal['company_name'] }}"
                                data-deal-amount="{{ $deal['amount'] }}"
                                data-deal-expected-close="{{ $deal['expected_close'] }}"
                                data-view-url="{{ route('deals.show', $deal['id']) }}"
                                data-edit-url="{{ route('deals.update', $deal['id']) }}"
                            >
                                <div class="deal-quick-actions pointer-events-none absolute right-2 top-2 flex items-center gap-1 opacity-0 transition duration-150 group-hover/deal:pointer-events-auto group-hover/deal:opacity-100">
                                    <label class="flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white hover:bg-gray-50">
                                        <input type="checkbox" class="deal-select-checkbox h-3.5 w-3.5" data-deal-select="{{ $deal['id'] }}">
                                    </label>
                                    <a href="{{ route('deals.show', $deal['id']) }}" class="flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="View">
                                        <i class="far fa-eye text-[11px]"></i>
                                    </a>
                                    <button type="button" class="deal-edit-btn flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="Edit">
                                        <i class="far fa-pen-to-square text-[11px]"></i>
                                    </button>
                                    <button type="button" class="deal-more-btn flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="More actions">
                                        <i class="fas fa-ellipsis-h text-[11px]"></i>
                                    </button>
                                    <div class="deal-more-menu absolute right-0 top-7 z-20 hidden w-36 rounded-lg border border-gray-200 bg-white py-1 text-xs text-gray-700 shadow-lg">
                                        <button type="button" class="deal-menu-item flex w-full px-3 py-2 text-left hover:bg-gray-50" data-action="view">View Deal</button>
                                        <button type="button" class="deal-menu-item flex w-full px-3 py-2 text-left hover:bg-gray-50" data-action="edit">Edit Deal</button>
                                        <button type="button" class="deal-menu-item flex w-full px-3 py-2 text-left text-red-600 hover:bg-red-50" data-action="delete">Delete Deal</button>
                                    </div>
                                </div>

                                <a
                                    id="deal-{{ $deal['id'] }}"
                                    href="{{ route('deals.show', $deal['id']) }}"
                                    class="block"
                                >
                                    <h3 class="pr-24 text-sm font-semibold text-gray-900">{{ $deal['deal_name'] }}</h3>
                                    <p class="mt-1 text-xs text-gray-700">{{ $deal['contact_name'] }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $deal['company_name'] }}</p>

                                    <p class="mt-3 text-lg font-semibold text-blue-700">{{ $formatCurrency($deal['amount']) }}</p>
                                    <p class="mt-2 text-[11px] text-gray-400">Expected Close</p>
                                    <p class="text-xs text-gray-700">{{ $deal['expected_close'] }}</p>

                                    <div class="mt-3 flex items-center gap-1.5 border-t border-gray-100 pt-2 text-[11px] text-gray-500">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-blue-100 text-[9px] font-semibold text-blue-700">{{ $ownerInitials }}</span>
                                        <span>{{ $deal['owner_name'] }}</span>
                                    </div>
                                </a>
                            </article>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-3 py-5 text-center text-xs text-gray-500">
                                No deals in this stage.
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-gray-200 px-2 py-2">
                        <p class="stage-description text-[11px] text-gray-500" data-stage-description="{{ $column['stage'] }}">No description yet.</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>

<div id="stageMiniModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
    <button id="stageMiniModalOverlay" type="button" class="absolute inset-0 bg-slate-900/35"></button>
    <div class="absolute left-1/2 top-1/2 w-full max-w-sm -translate-x-1/2 -translate-y-1/2 rounded-xl border border-gray-200 bg-white p-4 shadow-xl">
        <h3 id="stageMiniModalTitle" class="text-base font-semibold text-gray-900">New Stage</h3>
        <p id="stageMiniModalSubtitle" class="mt-1 text-xs text-gray-500">Add a new stage to the current board.</p>
        <textarea id="stageMiniModalInput" rows="3" class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
        <p id="stageMiniModalError" class="mt-2 hidden text-xs text-red-600"></p>
        <div class="mt-4 flex items-center justify-end gap-2">
            <button id="stageMiniModalCancel" type="button" class="h-9 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button id="stageMiniModalSave" type="button" class="h-9 rounded-lg bg-blue-600 px-3 text-sm font-medium text-white hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

@include('deals.partials.create-deal-modal', [
    'stageOptions' => $stageOptions,
    'companyOptions' => $companyOptions,
    'contactOptions' => $contactOptions,
    'contactRecords' => $contactRecords,
    'productOptions' => $productOptions,
    'ownerLabel' => $ownerLabel,
    'owners' => $owners,
    'defaultOwnerId' => $defaultOwnerId,
    'dealDraft' => $dealDraft ?? [],
    'openDealModal' => $openDealModal ?? false,
])

<script>
document.addEventListener('DOMContentLoaded', () => {
    const board = document.getElementById('dealsBoard');
    const selectionBar = document.getElementById('dealSelectionBar');
    const selectedCount = document.getElementById('selectedDealCount');
    const clearBtn = document.getElementById('clearDealSelectionBtn');
    const totalDealsCount = document.getElementById('totalDealsCount');
    const addDealButton = document.getElementById('openCreateDealModalBtn');
    const modal = document.getElementById('createDealModal');
    const form = document.getElementById('createDealForm');
    const formTitle = document.getElementById('dealPanelTitle');
    const formSubtitle = document.getElementById('dealPanelSubtitle');
    const saveDealBtn = document.getElementById('saveDealBtn');
    const stageInput = document.getElementById('stage');
    const dealNameInput = document.getElementById('deal_name');
    const contactSearchInput = document.getElementById('dealContactSearch');
    const contactIdInput = document.getElementById('deal_selected_contact_id');
    const ownerInput = document.getElementById('deal_selected_owner_id');
    const requiredMessage = document.getElementById('dealContactRequiredMessage');
    const dependentSections = document.getElementById('dealDependentSections');
    const stageModal = document.getElementById('stageMiniModal');
    const stageModalOverlay = document.getElementById('stageMiniModalOverlay');
    const stageModalTitle = document.getElementById('stageMiniModalTitle');
    const stageModalSubtitle = document.getElementById('stageMiniModalSubtitle');
    const stageModalInput = document.getElementById('stageMiniModalInput');
    const stageModalSave = document.getElementById('stageMiniModalSave');
    const stageModalCancel = document.getElementById('stageMiniModalCancel');
    const stageModalError = document.getElementById('stageMiniModalError');
    const stageDescriptionKey = 'deals.stage-descriptions.v1';
    let stageModalMode = null;
    let activeStageName = null;

    const getStageDescriptions = () => {
        try {
            return JSON.parse(window.localStorage.getItem(stageDescriptionKey) || '{}');
        } catch (_error) {
            return {};
        }
    };
    const setStageDescriptions = (data) => window.localStorage.setItem(stageDescriptionKey, JSON.stringify(data));

    const setFormModeCreate = () => {
        if (!form) {
            return;
        }
        const currentOwnerId = ownerInput?.value || '';
        form.reset();
        if (ownerInput && currentOwnerId !== '') {
            ownerInput.value = currentOwnerId;
        }
        if (contactIdInput) {
            contactIdInput.value = '';
        }
        form.action = @json(route('deals.store'));
        form.querySelector('input[name="_method"]')?.remove();
        formTitle.textContent = 'Create Deal';
        formSubtitle.textContent = 'Select an existing client, then complete the consulting and deal form.';
        saveDealBtn.textContent = 'Save & View Deal';
        enableDependentSections(false);
    };

    const ensureStageOption = (stageName) => {
        if (!stageInput || !stageName) {
            return;
        }
        const exists = Array.from(stageInput.options).some((option) => option.value === stageName);
        if (!exists) {
            const option = document.createElement('option');
            option.value = stageName;
            option.textContent = stageName;
            stageInput.appendChild(option);
        }
    };

    const setFormModeEdit = (card) => {
        if (!form || !card) {
            return;
        }
        const editUrl = card.dataset.editUrl || '';
        if (!editUrl) {
            return;
        }
        form.action = editUrl;
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        formTitle.textContent = 'Edit Deal';
        formSubtitle.textContent = 'Update the selected deal details.';
        saveDealBtn.textContent = 'Update Deal';
    };

    const enableDependentSections = (enabled) => {
        if (!dependentSections) {
            return;
        }
        dependentSections.classList.toggle('opacity-60', !enabled);
        dependentSections.classList.toggle('pointer-events-none', !enabled);
        requiredMessage?.classList.toggle('hidden', enabled);
        if (saveDealBtn) {
            saveDealBtn.disabled = !enabled;
            saveDealBtn.classList.toggle('opacity-60', !enabled);
            saveDealBtn.classList.toggle('cursor-not-allowed', !enabled);
        }
    };

    const openDealPanel = (options = {}) => {
        addDealButton?.click();
        window.setTimeout(() => {
            if (options.mode === 'edit' && options.card) {
                const card = options.card;
                setFormModeEdit(card);
                dealNameInput.value = card.dataset.dealName || '';
                if (stageInput && card.dataset.dealStage) {
                    ensureStageOption(card.dataset.dealStage);
                    stageInput.value = card.dataset.dealStage;
                }
                const amount = Number.parseFloat(card.dataset.dealAmount || '0');
                const totalField = document.getElementById('total_estimated_engagement_value');
                if (totalField && amount > 0) {
                    totalField.value = amount.toFixed(2);
                }
                const contactName = card.dataset.dealContact || '';
                if (contactSearchInput) {
                    contactSearchInput.value = contactName;
                }
                const contactRecords = @json($contactRecords);
                const matched = contactRecords.find((record) => (record.label || '').toLowerCase().includes(contactName.toLowerCase()));
                if (contactIdInput && matched) {
                    contactIdInput.value = String(matched.id);
                    enableDependentSections(true);
                }
            } else {
                setFormModeCreate();
                if (options.stage && stageInput) {
                    ensureStageOption(options.stage);
                    stageInput.value = options.stage;
                }
            }
        }, 80);
    };

    const cards = () => Array.from(document.querySelectorAll('.deal-card'));
    const checkboxes = () => Array.from(document.querySelectorAll('.deal-select-checkbox'));

    const updateTotalDealsCount = () => {
        if (!totalDealsCount) {
            return;
        }
        totalDealsCount.textContent = `${cards().length} deals`;
    };

    const updateStageHeaderMeta = (section) => {
        if (!section) {
            return;
        }
        const stageCards = Array.from(section.querySelectorAll('.deal-card'));
        const total = stageCards.reduce((sum, card) => sum + Number.parseFloat(card.dataset.dealAmount || '0'), 0);
        const stageTotal = section.querySelector('.stage-total');
        if (stageTotal) {
            const formatted = `P${Math.round(total).toLocaleString()}`;
            stageTotal.textContent = `${formatted} • ${stageCards.length} ${stageCards.length === 1 ? 'Deal' : 'Deals'}`;
        }
    };

    const refreshAllStageMeta = () => {
        Array.from(document.querySelectorAll('.stage-column')).forEach((section) => updateStageHeaderMeta(section));
        updateTotalDealsCount();
    };

    const updateSelectionUI = () => {
        const selected = checkboxes().filter((checkbox) => checkbox.checked);
        selectedCount.textContent = String(selected.length);
        selectionBar.classList.toggle('hidden', selected.length === 0);
        selectionBar.classList.toggle('flex', selected.length > 0);

        checkboxes().forEach((checkbox) => {
            const card = checkbox.closest('.deal-card');
            if (!card) {
                return;
            }
            card.classList.toggle('ring-2', checkbox.checked);
            card.classList.toggle('ring-blue-400', checkbox.checked);
            card.classList.toggle('border-blue-300', checkbox.checked);
            const quickActions = card.querySelector('.deal-quick-actions');
            if (quickActions && checkbox.checked) {
                quickActions.classList.remove('opacity-0', 'pointer-events-none');
                quickActions.classList.add('opacity-100', 'pointer-events-auto');
            }
        });
    };

    const closeAllMenus = () => {
        document.querySelectorAll('.stage-actions-menu').forEach((menu) => menu.classList.add('hidden'));
        document.querySelectorAll('.deal-more-menu').forEach((menu) => menu.classList.add('hidden'));
    };

    const applyStoredDescriptions = () => {
        const descriptions = getStageDescriptions();
        document.querySelectorAll('[data-stage-description]').forEach((item) => {
            const stage = item.dataset.stageDescription;
            item.textContent = descriptions[stage] || 'No description yet.';
        });
    };

    const openStageMiniModal = (mode, stageName) => {
        stageModalMode = mode;
        activeStageName = stageName;
        stageModalError.classList.add('hidden');
        stageModalError.textContent = '';
        stageModal.classList.remove('hidden');
        stageModal.setAttribute('aria-hidden', 'false');
        if (mode === 'new-stage') {
            stageModalTitle.textContent = 'New Stage';
            stageModalSubtitle.textContent = 'Add a new stage name to this board.';
            stageModalInput.value = '';
            stageModalInput.rows = 1;
        } else {
            stageModalTitle.textContent = 'Edit Description';
            stageModalSubtitle.textContent = `Update description for ${stageName}.`;
            const current = getStageDescriptions()[stageName] || '';
            stageModalInput.value = current;
            stageModalInput.rows = 3;
        }
        stageModalInput.focus();
    };

    const closeStageMiniModal = () => {
        stageModal.classList.add('hidden');
        stageModal.setAttribute('aria-hidden', 'true');
        stageModalMode = null;
        activeStageName = null;
    };

    const handleCreateStage = () => {
        const stageName = stageModalInput.value.trim();
        if (stageName === '') {
            stageModalError.textContent = 'Stage name is required.';
            stageModalError.classList.remove('hidden');
            return;
        }
        const exists = Array.from(document.querySelectorAll('.stage-column')).some((column) => (column.dataset.stageColumn || '').toLowerCase() === stageName.toLowerCase());
        if (exists) {
            stageModalError.textContent = 'That stage already exists.';
            stageModalError.classList.remove('hidden');
            return;
        }

        const column = document.createElement('section');
        column.className = 'stage-column w-[230px] rounded-xl border border-gray-200 bg-gray-50';
        column.dataset.stageColumn = stageName;
        column.innerHTML = `
            <header class="group/column rounded-t-xl bg-slate-800 px-3 py-2 text-white">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="stage-name text-xs font-semibold">${stageName}</h2>
                        <p class="stage-total text-xs opacity-90">P0 • 0 Deals</p>
                    </div>
                    <div class="relative">
                        <button type="button" class="stage-actions-trigger opacity-0 transition group-hover/column:opacity-100" data-stage="${stageName}" title="Stage actions">
                            <i class="fas fa-ellipsis-vertical text-xs text-white/90"></i>
                        </button>
                        <div class="stage-actions-menu absolute right-0 z-20 mt-2 hidden w-44 rounded-lg border border-gray-200 bg-white py-1 text-xs text-gray-700 shadow-lg" data-stage-menu="${stageName}">
                            <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="new-deal">New Deal</button>
                            <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="new-stage">New Stage</button>
                            <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50" data-action="edit-description">Edit Description</button>
                            <button type="button" class="stage-action-item flex w-full items-center gap-2 px-3 py-2 text-left text-red-600 hover:bg-red-50" data-action="delete-stage">Delete Stage</button>
                        </div>
                    </div>
                </div>
            </header>
            <div class="stage-cards space-y-2 p-2">
                <div class="rounded-lg border border-dashed border-gray-300 bg-white px-3 py-5 text-center text-xs text-gray-500">No deals in this stage.</div>
            </div>
            <div class="border-t border-gray-200 px-2 py-2">
                <p class="stage-description text-[11px] text-gray-500" data-stage-description="${stageName}">No description yet.</p>
            </div>
        `;
        board.appendChild(column);
        ensureStageOption(stageName);
        closeStageMiniModal();
        refreshAllStageMeta();
    };

    const handleEditDescription = () => {
        const descriptions = getStageDescriptions();
        descriptions[activeStageName] = stageModalInput.value.trim();
        setStageDescriptions(descriptions);
        applyStoredDescriptions();
        closeStageMiniModal();
    };

    const deleteCard = (card) => {
        if (!card) {
            return;
        }
        if (!window.confirm('Delete this deal card from the board?')) {
            return;
        }
        const section = card.closest('.stage-column');
        const cardsContainer = section?.querySelector('.stage-cards');
        card.remove();
        if (cardsContainer && cardsContainer.querySelectorAll('.deal-card').length === 0) {
            const empty = document.createElement('div');
            empty.className = 'rounded-lg border border-dashed border-gray-300 bg-white px-3 py-5 text-center text-xs text-gray-500';
            empty.textContent = 'No deals in this stage.';
            cardsContainer.appendChild(empty);
        }
        updateSelectionUI();
        updateStageHeaderMeta(section);
    };

    addDealButton?.addEventListener('click', () => {
        setFormModeCreate();
    });

    clearBtn?.addEventListener('click', () => {
        checkboxes().forEach((checkbox) => {
            checkbox.checked = false;
        });
        updateSelectionUI();
    });

    stageModalCancel?.addEventListener('click', closeStageMiniModal);
    stageModalOverlay?.addEventListener('click', closeStageMiniModal);
    stageModalSave?.addEventListener('click', () => {
        if (stageModalMode === 'new-stage') {
            handleCreateStage();
            return;
        }
        if (stageModalMode === 'edit-description') {
            handleEditDescription();
        }
    });

    board?.addEventListener('click', (event) => {
        const stageTrigger = event.target.closest('.stage-actions-trigger');
        const stageAction = event.target.closest('.stage-action-item');
        const dealMoreBtn = event.target.closest('.deal-more-btn');
        const dealMenuItem = event.target.closest('.deal-menu-item');
        const editBtn = event.target.closest('.deal-edit-btn');
        const viewBtn = event.target.closest('a[title="View"]');

        if (viewBtn) {
            return;
        }

        if (stageTrigger) {
            event.preventDefault();
            event.stopPropagation();
            const stage = stageTrigger.dataset.stage;
            const menu = Array.from(document.querySelectorAll('[data-stage-menu]')).find((item) => item.dataset.stageMenu === stage);
            const isOpen = menu && !menu.classList.contains('hidden');
            closeAllMenus();
            if (menu && !isOpen) {
                menu.classList.remove('hidden');
            }
            return;
        }

        if (stageAction) {
            event.preventDefault();
            const section = stageAction.closest('.stage-column');
            const stage = section?.dataset.stageColumn || stageAction.closest('[data-stage-menu]')?.dataset.stageMenu || '';
            const action = stageAction.dataset.action;
            closeAllMenus();

            if (action === 'new-deal') {
                openDealPanel({ mode: 'create', stage });
                return;
            }
            if (action === 'new-stage') {
                openStageMiniModal('new-stage', stage);
                return;
            }
            if (action === 'edit-description') {
                openStageMiniModal('edit-description', stage);
                return;
            }
            if (action === 'delete-stage') {
                const hasDeals = section?.querySelector('.deal-card');
                if (hasDeals) {
                    window.alert('Stage cannot be deleted while it has deals. Move or delete its deals first.');
                    return;
                }
                if (window.confirm(`Delete stage "${stage}"?`)) {
                    section?.remove();
                    refreshAllStageMeta();
                }
            }
            return;
        }

        if (editBtn) {
            event.preventDefault();
            event.stopPropagation();
            const card = editBtn.closest('.deal-card');
            openDealPanel({ mode: 'edit', card });
            return;
        }

        if (dealMoreBtn) {
            event.preventDefault();
            event.stopPropagation();
            const menu = dealMoreBtn.parentElement.querySelector('.deal-more-menu');
            const isOpen = menu && !menu.classList.contains('hidden');
            closeAllMenus();
            if (menu && !isOpen) {
                menu.classList.remove('hidden');
            }
            return;
        }

        if (dealMenuItem) {
            event.preventDefault();
            const card = dealMenuItem.closest('.deal-card');
            const action = dealMenuItem.dataset.action;
            closeAllMenus();
            if (!card) {
                return;
            }

            if (action === 'view') {
                window.location.href = card.dataset.viewUrl || '#';
                return;
            }
            if (action === 'edit') {
                openDealPanel({ mode: 'edit', card });
                return;
            }
            if (action === 'delete') {
                deleteCard(card);
                return;
            }
        }
    });

    document.addEventListener('click', (event) => {
        const clickedStage = event.target.closest('.stage-actions-menu, .stage-actions-trigger');
        const clickedDeal = event.target.closest('.deal-more-menu, .deal-more-btn');
        if (!clickedStage && !clickedDeal) {
            closeAllMenus();
        }
    });

    cards().forEach((card) => {
        card.addEventListener('click', (event) => {
            const interactive = event.target.closest('a,button,input,label,textarea,select');
            if (interactive) {
                return;
            }
            const viewUrl = card.dataset.viewUrl;
            if (viewUrl) {
                window.location.href = viewUrl;
            }
        });
    });

    checkboxes().forEach((checkbox) => checkbox.addEventListener('change', updateSelectionUI));
    applyStoredDescriptions();
    refreshAllStageMeta();
});
</script>
@endsection
