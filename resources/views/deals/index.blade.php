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
        <span class="text-xs text-gray-500">{{ $totalDeals }} deals</span>
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
        <div class="flex min-w-max gap-3">
            @foreach ($stageColumns as $column)
                @php
                    $isClosedLost = $column['stage'] === 'Closed Lost';
                    $headerClass = $isClosedLost ? 'bg-red-600' : 'bg-slate-800';
                @endphp
                <section class="w-[230px] rounded-xl border border-gray-200 bg-gray-50">
                    <header class="group/column rounded-t-xl px-3 py-2 text-white {{ $headerClass }}">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <h2 class="text-xs font-semibold">{{ $column['stage'] }}</h2>
                                <p class="text-xs opacity-90">{{ $formatCurrency($column['total_amount']) }}</p>
                            </div>
                            <button type="button" class="opacity-0 transition group-hover/column:opacity-100">
                                <i class="fas fa-ellipsis-vertical text-xs text-white/90"></i>
                            </button>
                        </div>
                    </header>

                    <div class="space-y-2 p-2">
                        @forelse ($column['deals'] as $deal)
                            @php
                                $ownerInitials = strtoupper(substr($deal['owner_name'], 0, 1).substr(strrchr(' '.$deal['owner_name'], ' '), 1, 1));
                            @endphp
                            <article
                                class="deal-card group/deal relative rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition hover:cursor-pointer hover:border-blue-300 hover:shadow"
                                data-deal-id="{{ $deal['id'] }}"
                            >
                                <div class="deal-quick-actions pointer-events-none absolute right-2 top-2 flex items-center gap-1 opacity-0 transition duration-150 group-hover/deal:pointer-events-auto group-hover/deal:opacity-100">
                                    <label class="flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white hover:bg-gray-50">
                                        <input type="checkbox" class="deal-select-checkbox h-3.5 w-3.5" data-deal-select="{{ $deal['id'] }}">
                                    </label>
                                    <a href="{{ route('deals.show', $deal['id']) }}" class="flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="View">
                                        <i class="far fa-eye text-[11px]"></i>
                                    </a>
                                    <button type="button" class="deal-edit-btn flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="Edit" data-deal-name="{{ $deal['deal_name'] }}">
                                        <i class="far fa-pen-to-square text-[11px]"></i>
                                    </button>
                                    <button type="button" class="flex h-6 w-6 items-center justify-center rounded border border-gray-200 bg-white text-gray-500 hover:text-blue-700 hover:bg-blue-50" title="More actions">
                                        <i class="fas fa-ellipsis-h text-[11px]"></i>
                                    </button>
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
                </section>
            @endforeach
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
])

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectionBar = document.getElementById('dealSelectionBar');
        const selectedCount = document.getElementById('selectedDealCount');
        const clearBtn = document.getElementById('clearDealSelectionBtn');
        const checkboxes = Array.from(document.querySelectorAll('.deal-select-checkbox'));
        const editButtons = Array.from(document.querySelectorAll('.deal-edit-btn'));

        const updateSelectionUI = () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked);
            selectedCount.textContent = String(selected.length);
            selectionBar.classList.toggle('hidden', selected.length === 0);
            selectionBar.classList.toggle('flex', selected.length > 0);

            checkboxes.forEach((checkbox) => {
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

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('click', (event) => event.stopPropagation());
            checkbox.addEventListener('change', updateSelectionUI);
        });

        clearBtn?.addEventListener('click', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            updateSelectionUI();
        });

        editButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const name = button.dataset.dealName || 'deal';
                window.alert(`Edit placeholder for: ${name}`);
            });
        });
    });
</script>
@endsection
