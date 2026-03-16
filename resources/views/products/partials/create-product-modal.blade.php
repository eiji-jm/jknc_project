<div id="createProductModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createProductModalOverlay" type="button" aria-label="Close create product panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>

    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createProductPanel" class="pointer-events-auto flex h-full w-full max-w-[620px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[560px]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                <h2 class="text-3xl font-semibold text-gray-900">Create Product</h2>
                <button id="closeCreateProductModal" type="button" class="text-3xl leading-none text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <form method="POST" action="{{ route('products.store') }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                @php
                    $selectedOwner = collect($owners)->firstWhere('id', (int) $defaultOwnerId) ?: collect($owners)->first();
                    $selectedOwnerId = (int) ($selectedOwner['id'] ?? 0);
                    $selectedOwnerName = $selectedOwner['name'] ?? 'Select Owner';
                @endphp
                <input id="product_owner_id" type="hidden" name="owner_id" value="{{ old('owner_id', $selectedOwnerId) }}">

                <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                    <div class="mb-6 flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-medium text-gray-500">Product Information</p>

                        <div class="relative sm:flex-shrink-0">
                            <button
                                id="productOwnerDropdownTrigger"
                                type="button"
                                class="inline-flex w-full items-center justify-between gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 sm:w-auto"
                            >
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <span id="productOwnerSelectedLabel">Owner: {{ $selectedOwnerName }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                            </button>

                            <div id="productOwnerDropdownMenu" class="absolute right-0 z-20 mt-2 hidden w-full min-w-0 rounded-xl border border-gray-200 bg-white p-2 shadow-lg sm:w-72">
                                <div class="relative mb-2">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                    <input id="productOwnerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
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
                                            class="product-owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
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

                    <div class="space-y-4">
                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                            <label for="product_name" class="text-right text-sm text-gray-700">Product Name:</label>
                            <input id="product_name" name="product_name" required value="{{ old('product_name') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                            <label for="product_code" class="text-right text-sm text-gray-700">Product Code:</label>
                            <input id="product_code" name="product_code" required value="{{ old('product_code') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                            <label for="product_category" class="text-right text-sm text-gray-700">Product Category:</label>
                            <select id="product_category" name="product_category" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category }}" {{ old('product_category', '-None-') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                            <label for="unit_price" class="text-right text-sm text-gray-700">Unit Price</label>
                            <div class="relative">
                                <input id="unit_price" name="unit_price" type="number" min="0" step="0.01" value="{{ old('unit_price') }}" class="h-10 w-full rounded border border-gray-300 px-3 pr-8 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">P</span>
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-start sm:gap-3">
                            <label for="description" class="pt-2 text-right text-sm text-gray-700">Description:</label>
                            <textarea id="description" name="description" rows="2" class="rounded border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                            <span></span>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="product_active" value="1" {{ old('product_active', '1') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                Product Active
                            </label>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                <div class="mt-auto flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                    <button id="cancelCreateProductModal" type="button" class="h-10 min-w-[100px] rounded-lg border border-gray-300 px-5 text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="h-10 min-w-[100px] rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
