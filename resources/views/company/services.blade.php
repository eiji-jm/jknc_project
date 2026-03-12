@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.index') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Company</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">{{ $company->company_name }}</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start gap-5">
                    <div class="h-16 w-16 shrink-0 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-sm font-bold leading-tight">
                        JK<br>&amp;C
                    </div>

                    <div class="flex-1 min-w-[280px]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $company->company_name }}</h1>
                                <p class="mt-1 text-sm text-gray-500">Corporation</p>
                            </div>
                            <button class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Address:</span>
                                <span>{{ $company->address ?: '3F, Cebu Holdings Center, Cardinal Rosales Ave, Cebu Business Park, Cebu City' }}</span>
                            </div>
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Phone:</span>
                                <span>{{ $company->phone ?: '0995 353 3789' }}</span>
                            </div>
                            <div class="text-gray-600">
                                <span class="font-medium text-gray-700">Website:</span>
                                <a href="{{ $company->website ?: '#' }}" class="text-blue-600 underline">{{ $company->website ?: 'https://bigin.zoho.com/' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-md border border-gray-200 bg-white overflow-hidden">
                <div class="border-b border-gray-100 px-4 py-4">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">SERVICES</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage services of the company.</p>

                    @if (session('services_success'))
                        <div id="servicesSuccessMessage" class="mt-3 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                            {{ session('services_success') }}
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 gap-2 lg:grid-cols-12">
                        <div class="relative lg:col-span-4">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input
                                id="servicesSearchInput"
                                type="text"
                                placeholder="Search services..."
                                class="w-full h-10 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            >
                        </div>

                        <div class="lg:col-span-2">
                            <select id="servicesStatusFilter" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="all">Status: All</option>
                                <option value="active">Status: Active</option>
                                <option value="inactive">Status: Inactive</option>
                                <option value="archived">Status: Archived</option>
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <select id="servicesCategoryFilter" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="all">Service Category: All</option>
                                @foreach (collect($services)->pluck('category')->unique()->sort()->values() as $category)
                                    <option value="{{ strtolower($category) }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-1">
                            <button id="servicesSortButton" class="w-full h-10 rounded border border-gray-200 text-gray-700 text-sm hover:bg-gray-50 inline-flex items-center justify-center gap-2">
                                <i class="fas fa-sort text-xs"></i>
                                <span>Latest</span>
                            </button>
                        </div>

                        <div class="lg:col-span-2">
                            <button id="openLinkServiceModal" class="w-full h-10 rounded-full bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 inline-flex items-center justify-center gap-2">
                                <span class="text-base leading-none">+</span>
                                <span>Link Service</span>
                            </button>
                        </div>
                    </div>

                    <div id="bulkActionsBar" class="mt-3 hidden items-center justify-between gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2">
                        <p class="text-sm text-gray-700"><span id="bulkSelectionCount">0</span> selected</p>
                        <div class="flex flex-wrap items-center gap-2">
                            <button id="bulkUnlinkButton" class="h-8 rounded-full border border-gray-200 px-3 text-sm text-gray-700 hover:bg-white">Bulk Unlink</button>
                            <select id="bulkStatusSelect" class="h-8 rounded border border-gray-200 bg-white px-2 text-sm text-gray-700">
                                <option value="active">Set Active</option>
                                <option value="inactive">Set Inactive</option>
                                <option value="archived">Set Archived</option>
                            </select>
                            <button id="bulkStatusApplyButton" class="h-8 rounded-full border border-gray-200 px-3 text-sm text-gray-700 hover:bg-white">Bulk Status Update</button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="w-10 px-3 py-3 text-left">
                                            <input id="selectAllServices" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                                        </th>
                                        <th class="px-3 py-3 text-left font-medium">Service Name</th>
                                        <th class="px-3 py-3 text-left font-medium">Service Type</th>
                                        <th class="px-3 py-3 text-left font-medium">Category</th>
                                        <th class="px-3 py-3 text-left font-medium">Pricing Model</th>
                                        <th class="px-3 py-3 text-left font-medium">Base Price</th>
                                        <th class="px-3 py-3 text-left font-medium">Last Updated</th>
                                        <th class="px-3 py-3 text-left font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="servicesTableBody" class="divide-y divide-gray-200 bg-white text-gray-700"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-end gap-3 text-sm text-gray-500">
                    <span id="servicesResultCount">0 - 0 of 0 results</span>
                </div>
            </div>
        </section>
    </div>
</div>

<div id="linkServicesModal" class="fixed inset-0 z-[70] hidden bg-black/35 p-4 sm:p-6">
    <div class="mx-auto mt-8 w-full max-w-3xl rounded-md border border-gray-200 bg-white overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4 flex items-start justify-between gap-3">
            <div>
                <h3 class="text-2xl font-bold tracking-tight text-gray-900">Link Services</h3>
                <p class="mt-1 text-sm text-gray-500">Select services below to link them to the company.</p>
            </div>
            <button type="button" id="closeLinkServicesModalTop" class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="px-4 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full bg-gray-100 border border-gray-200 text-gray-600 flex items-center justify-center text-lg font-semibold">A</div>
                <div>
                    <p class="text-xl font-semibold text-gray-900">{{ $company->company_name }}</p>
                    <p class="text-sm text-gray-500">Corporation</p>
                </div>
            </div>
        </div>

        <form id="linkServicesForm" method="POST" action="{{ route('company.services.link', $company->id) }}">
            @csrf
            <div class="px-4 py-4 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        id="modalServiceSearchInput"
                        type="text"
                        placeholder="Search services..."
                        class="w-full h-10 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                    >
                </div>
            </div>

            <div id="modalServiceList" class="max-h-[320px] overflow-y-auto divide-y divide-gray-100"></div>

            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" id="closeLinkServicesModalFooter" class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" id="submitLinkServicesButton" class="h-9 min-w-[120px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Link Services
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initialServices = @json($services);
        const serviceCatalog = @json($serviceCatalog);
        const showRouteTemplate = @json(route('company.services.show', ['company' => $company->id, 'service' => '__SERVICE__']));

        const tableBody = document.getElementById('servicesTableBody');
        const searchInput = document.getElementById('servicesSearchInput');
        const statusFilter = document.getElementById('servicesStatusFilter');
        const categoryFilter = document.getElementById('servicesCategoryFilter');
        const sortButton = document.getElementById('servicesSortButton');
        const selectAll = document.getElementById('selectAllServices');
        const resultCount = document.getElementById('servicesResultCount');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const bulkSelectionCount = document.getElementById('bulkSelectionCount');
        const bulkUnlinkButton = document.getElementById('bulkUnlinkButton');
        const bulkStatusSelect = document.getElementById('bulkStatusSelect');
        const bulkStatusApplyButton = document.getElementById('bulkStatusApplyButton');

        const modal = document.getElementById('linkServicesModal');
        const openModalButton = document.getElementById('openLinkServiceModal');
        const closeModalTop = document.getElementById('closeLinkServicesModalTop');
        const closeModalFooter = document.getElementById('closeLinkServicesModalFooter');
        const modalList = document.getElementById('modalServiceList');
        const modalSearchInput = document.getElementById('modalServiceSearchInput');
        const linkForm = document.getElementById('linkServicesForm');
        const submitLinkButton = document.getElementById('submitLinkServicesButton');

        let services = [...initialServices];
        let filteredServices = [...services];
        let selectedIds = new Set();
        let sortLatestFirst = true;

        const toMoney = (amount) => `P${Number(amount || 0).toLocaleString('en-US')}`;
        const normalize = (value) => String(value || '').toLowerCase().trim();

        function showInlineSuccess(message) {
            let messageEl = document.getElementById('servicesSuccessMessage');
            if (!messageEl) {
                messageEl = document.createElement('div');
                messageEl.id = 'servicesSuccessMessage';
                messageEl.className = 'mt-3 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700';
                const target = document.querySelector('.border-b.border-gray-100.px-4.py-4 p.mt-1');
                target?.insertAdjacentElement('afterend', messageEl);
            }
            messageEl.textContent = message;
        }

        function applyFiltersAndSort() {
            const searchTerm = normalize(searchInput.value);
            const selectedStatus = normalize(statusFilter.value);
            const selectedCategory = normalize(categoryFilter.value);

            filteredServices = services
                .filter((service) => {
                    const haystack = [
                        service.name,
                        service.service_type,
                        service.category,
                        service.pricing_model,
                        service.status,
                    ].map(normalize).join(' ');

                    const statusMatches = selectedStatus === 'all' || normalize(service.status) === selectedStatus;
                    const categoryMatches = selectedCategory === 'all' || normalize(service.category) === selectedCategory;
                    const searchMatches = !searchTerm || haystack.includes(searchTerm);

                    return statusMatches && categoryMatches && searchMatches;
                })
                .sort((a, b) => {
                    const aDate = new Date(a.updated_at).getTime();
                    const bDate = new Date(b.updated_at).getTime();
                    return sortLatestFirst ? bDate - aDate : aDate - bDate;
                });

            renderTable();
        }

        function updateBulkBar() {
            const count = selectedIds.size;
            bulkSelectionCount.textContent = count;
            bulkActionsBar.classList.toggle('hidden', count === 0);
            bulkActionsBar.classList.toggle('flex', count > 0);
        }

        function closeAllMenus() {
            document.querySelectorAll('[data-row-menu]').forEach((menu) => menu.classList.add('hidden'));
        }

        function renderTable() {
            tableBody.innerHTML = '';

            if (filteredServices.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">No services found.</td>';
                tableBody.appendChild(row);
            } else {
                filteredServices.forEach((service) => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.dataset.id = service.id;

                    const viewHref = showRouteTemplate.replace('__SERVICE__', service.id);

                    row.innerHTML = `
                        <td class="px-3 py-3">
                            <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 row-service-checkbox" data-id="${service.id}" ${selectedIds.has(service.id) ? 'checked' : ''}>
                        </td>
                        <td class="px-3 py-3 font-medium text-gray-800">${service.name}</td>
                        <td class="px-3 py-3">${service.service_type}</td>
                        <td class="px-3 py-3">${service.category}</td>
                        <td class="px-3 py-3">${service.pricing_model}</td>
                        <td class="px-3 py-3 font-medium text-gray-900">${toMoney(service.base_price)}</td>
                        <td class="px-3 py-3 text-gray-600">${service.updated_at}</td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2 relative">
                                <a href="${viewHref}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">View</a>
                                <button type="button" class="h-8 w-8 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 row-menu-toggle" data-id="${service.id}">
                                    <i class="fas fa-ellipsis-h text-xs"></i>
                                </button>
                                <div data-row-menu="${service.id}" class="hidden absolute right-0 top-9 z-20 min-w-[150px] rounded-md border border-gray-200 bg-white shadow-sm py-1">
                                    <a href="${viewHref}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">View service</a>
                                    <button type="button" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 row-action-edit" data-id="${service.id}">Edit link</button>
                                    <button type="button" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 row-action-unlink" data-id="${service.id}">Unlink service</button>
                                    <button type="button" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 row-action-archive" data-id="${service.id}">Archive service</button>
                                </div>
                            </div>
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            }

            if (filteredServices.length === 0) {
                resultCount.textContent = '0 - 0 of 0 results';
            } else {
                resultCount.textContent = `1 - ${filteredServices.length} of ${filteredServices.length} results`;
            }
            selectAll.checked = filteredServices.length > 0 && filteredServices.every((row) => selectedIds.has(row.id));
            updateBulkBar();
            bindRowEvents();
            renderModalList();
        }

        function bindRowEvents() {
            document.querySelectorAll('.row-service-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    const id = Number(this.dataset.id);
                    if (this.checked) {
                        selectedIds.add(id);
                    } else {
                        selectedIds.delete(id);
                    }
                    updateBulkBar();
                });
            });

            document.querySelectorAll('.row-menu-toggle').forEach((button) => {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const id = this.dataset.id;
                    const menu = document.querySelector(`[data-row-menu="${id}"]`);
                    const isHidden = menu.classList.contains('hidden');
                    closeAllMenus();
                    if (isHidden) {
                        menu.classList.remove('hidden');
                    }
                });
            });

            document.querySelectorAll('.row-action-unlink').forEach((button) => {
                button.addEventListener('click', function () {
                    const id = Number(this.dataset.id);
                    services = services.filter((item) => item.id !== id);
                    selectedIds.delete(id);
                    closeAllMenus();
                    showInlineSuccess('Service unlinked successfully.');
                    applyFiltersAndSort();
                });
            });

            document.querySelectorAll('.row-action-archive').forEach((button) => {
                button.addEventListener('click', function () {
                    const id = Number(this.dataset.id);
                    services = services.map((item) => item.id === id
                        ? { ...item, status: 'Archived', updated_at: new Date().toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
                        : item
                    );
                    closeAllMenus();
                    showInlineSuccess('Service archived successfully.');
                    applyFiltersAndSort();
                });
            });

            document.querySelectorAll('.row-action-edit').forEach((button) => {
                button.addEventListener('click', function () {
                    const id = Number(this.dataset.id);
                    services = services.map((item) => item.id === id
                        ? { ...item, status: item.status === 'Active' ? 'Inactive' : 'Active', updated_at: new Date().toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
                        : item
                    );
                    closeAllMenus();
                    showInlineSuccess('Service link updated.');
                    applyFiltersAndSort();
                });
            });
        }

        function renderModalList() {
            const linkedIds = new Set(services.map((service) => Number(service.id)));
            const searchTerm = normalize(modalSearchInput.value);
            const existingSelection = new Set(
                Array.from(modalList.querySelectorAll('input[name="service_ids[]"]:checked')).map((input) => Number(input.value))
            );

            modalList.innerHTML = '';
            const visibleCatalog = serviceCatalog.filter((service) => normalize(service.name).includes(searchTerm));

            visibleCatalog.forEach((service) => {
                const isLinked = linkedIds.has(Number(service.id));
                const row = document.createElement('label');
                row.className = `flex items-center gap-3 px-4 py-3 ${isLinked ? 'bg-gray-50' : 'hover:bg-gray-50'} cursor-pointer`;

                row.innerHTML = `
                    <input
                        type="checkbox"
                        name="service_ids[]"
                        value="${service.id}"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600"
                        ${isLinked ? 'disabled' : ''}
                        ${existingSelection.has(Number(service.id)) ? 'checked' : ''}
                    >
                    <div class="flex-1">
                        <p class="text-sm font-medium ${isLinked ? 'text-gray-400' : 'text-gray-800'}">${service.name}</p>
                        ${isLinked ? '<p class="text-xs text-gray-400 mt-0.5">Already linked</p>' : ''}
                    </div>
                `;

                modalList.appendChild(row);
            });

            bindModalCheckboxState();
        }

        function bindModalCheckboxState() {
            const checkboxes = modalList.querySelectorAll('input[name="service_ids[]"]:not([disabled])');
            const selected = Array.from(checkboxes).filter((checkbox) => checkbox.checked).length;
            submitLinkButton.disabled = selected === 0;

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const count = Array.from(modalList.querySelectorAll('input[name="service_ids[]"]:not([disabled])')).filter((el) => el.checked).length;
                    submitLinkButton.disabled = count === 0;
                });
            });
        }

        function openModal() {
            renderModalList();
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            linkForm.reset();
            modalSearchInput.value = '';
            renderModalList();
        }

        searchInput.addEventListener('input', applyFiltersAndSort);
        statusFilter.addEventListener('change', applyFiltersAndSort);
        categoryFilter.addEventListener('change', applyFiltersAndSort);

        sortButton.addEventListener('click', function () {
            sortLatestFirst = !sortLatestFirst;
            this.querySelector('span').textContent = sortLatestFirst ? 'Latest' : 'Oldest';
            applyFiltersAndSort();
        });

        selectAll.addEventListener('change', function () {
            if (this.checked) {
                filteredServices.forEach((service) => selectedIds.add(service.id));
            } else {
                filteredServices.forEach((service) => selectedIds.delete(service.id));
            }
            renderTable();
        });

        bulkUnlinkButton.addEventListener('click', function () {
            services = services.filter((service) => !selectedIds.has(service.id));
            selectedIds.clear();
            showInlineSuccess('Selected services were unlinked.');
            applyFiltersAndSort();
        });

        bulkStatusApplyButton.addEventListener('click', function () {
            const nextStatus = bulkStatusSelect.value;
            services = services.map((service) => selectedIds.has(service.id)
                ? { ...service, status: nextStatus.charAt(0).toUpperCase() + nextStatus.slice(1), updated_at: new Date().toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
                : service
            );
            showInlineSuccess('Status updated for selected services.');
            applyFiltersAndSort();
        });

        openModalButton.addEventListener('click', openModal);
        closeModalTop.addEventListener('click', closeModal);
        closeModalFooter.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        modalSearchInput.addEventListener('input', renderModalList);
        document.addEventListener('click', closeAllMenus);

        linkForm.addEventListener('submit', function () {
            const checked = modalList.querySelectorAll('input[name="service_ids[]"]:checked').length;
            submitLinkButton.disabled = checked === 0;
        });

        applyFiltersAndSort();
    });
</script>
@endsection
