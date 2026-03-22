@php
    $selectedOwner = collect($owners)->firstWhere('id', (int) $defaultOwnerId) ?: collect($owners)->first();
    $selectedOwnerId = (int) ($selectedOwner['id'] ?? 0);
    $selectedOwnerName = $selectedOwner['name'] ?? 'Select Owner';
    $createdByDisplay = old('created_by', $drawerMeta['createdBy'] ?? 'Admin User');
    $createdAtDisplay = old('created_at_display', $drawerMeta['createdAtDisplay'] ?? now()->format('F j, Y g:i A'));
    $selectedProductAreas = old('product_area', ['None']);
@endphp

<div id="createProductModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createProductModalOverlay" type="button" aria-label="Close create product panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>

    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createProductPanel" class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[680px]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Create Product</h2>
                    <p class="mt-1 text-sm text-gray-500">Capture the complete product setup before linking it to deals and services.</p>
                </div>
                <button id="closeCreateProductModal" type="button" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <form method="POST" action="{{ route('products.store') }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input id="product_owner_id" type="hidden" name="owner_id" value="{{ old('owner_id', $selectedOwnerId) }}">

                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
                    <div class="space-y-4 border-b border-gray-100 pb-5">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Product Intake</p>
                            <p class="text-xs text-gray-400">Use the same structured format as the CRM master records.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 sm:items-end">
                            <div class="relative">
                                <label for="productOwnerDropdownTrigger" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Owner</label>
                                <button id="productOwnerDropdownTrigger" type="button" class="inline-flex h-10 w-full items-center justify-between gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
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

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created By</p>
                                    <p class="text-sm text-gray-500">{{ $createdByDisplay }}</p>
                                </div>
                                <div>
                                    <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created At</p>
                                    <p class="text-sm text-gray-500"><span id="productCreatedAtLiveValue">{{ $createdAtDisplay }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Product Information</h3>
                        <p class="mb-4 text-xs text-gray-500">Capture the core product identity and classification.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="product_name" class="mb-1 block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                                <input id="product_name" name="product_name" required value="{{ old('product_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="product_type" class="mb-1 block text-sm font-medium text-gray-700">Product Type <span class="text-red-500">*</span></label>
                                <select id="product_type" name="product_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled @selected(blank(old('product_type'))) >Select product type</option>
                                    @foreach ($productTypeOptions as $option)
                                        <option value="{{ $option }}" @selected(old('product_type') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="sku" class="mb-1 block text-sm font-medium text-gray-700">SKU / Code</label>
                                <input id="sku" name="sku" value="{{ old('sku') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Service Linking</h3>
                        <p class="mb-4 text-xs text-gray-500">Link the product to a service when available and classify the service area.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="linked_service_id" class="mb-1 block text-sm font-medium text-gray-700">Linked Service</label>
                                <select id="linked_service_id" name="linked_service_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">None</option>
                                    @foreach ($serviceOptions as $service)
                                        <option value="{{ $service['id'] }}" @selected((string) old('linked_service_id') === (string) $service['id'])>{{ $service['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Service Area <span class="text-red-500">*</span></label>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($productAreaOptions as $option)
                                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                            <input type="checkbox" name="product_area[]" value="{{ $option }}" @checked(in_array($option, $selectedProductAreas, true)) class="product-area-checkbox h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" @if ($option === 'Others') data-other-target="productAreaOtherWrap" @endif>
                                            <span>{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div id="productAreaOtherWrap" class="{{ in_array('Others', $selectedProductAreas, true) ? '' : 'hidden' }} sm:col-span-2">
                                <label for="product_area_other" class="mb-1 block text-sm font-medium text-gray-700">Other Product Area</label>
                                <input id="product_area_other" name="product_area_other" value="{{ old('product_area_other') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Description</h3>
                        <p class="mb-4 text-xs text-gray-500">Provide the main description and inclusions for quoting and internal review.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="product_description" class="mb-1 block text-sm font-medium text-gray-700">Product Description <span class="text-red-500">*</span></label>
                                <textarea id="product_description" name="product_description" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('product_description') }}</textarea>
                            </div>
                            <div>
                                <label for="product_inclusions" class="mb-1 block text-sm font-medium text-gray-700">Product Inclusions</label>
                                <textarea id="product_inclusions" name="product_inclusions" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('product_inclusions') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Each new line will be saved as a bullet.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Accounting</h3>
                        <p class="mb-4 text-xs text-gray-500">Map the product to the proper accounting category.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="category" class="mb-1 block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="category" name="category" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled @selected(blank(old('category'))) >Select category</option>
                                    @foreach ($categoryOptions as $category)
                                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Pricing</h3>
                        <p class="mb-4 text-xs text-gray-500">Define the pricing model, cost basis, and discount behavior.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="pricing_type" class="mb-1 block text-sm font-medium text-gray-700">Pricing Type <span class="text-red-500">*</span></label>
                                <select id="pricing_type" name="pricing_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled @selected(blank(old('pricing_type'))) >Select pricing type</option>
                                    @foreach ($pricingTypeOptions as $option)
                                        <option value="{{ $option }}" @selected(old('pricing_type') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Price <span class="text-red-500">*</span></label>
                                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="cost" class="mb-1 block text-sm font-medium text-gray-700">Cost</label>
                                <input id="cost" name="cost" type="number" min="0" step="0.01" value="{{ old('cost') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_discountable" value="1" @checked(old('is_discountable')) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>Discountable</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Tax</h3>
                        <p class="mb-4 text-xs text-gray-500">Set the correct tax handling for the product.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="tax_type" class="mb-1 block text-sm font-medium text-gray-700">Tax Type <span class="text-red-500">*</span></label>
                                <select id="tax_type" name="tax_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled @selected(blank(old('tax_type'))) >Select tax type</option>
                                    @foreach ($taxTypeOptions as $option)
                                        <option value="{{ $option }}" @selected(old('tax_type') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Inventory</h3>
                        <p class="mb-4 text-xs text-gray-500">Only inventory-managed products require stock quantity.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="inventory_type" class="mb-1 block text-sm font-medium text-gray-700">Inventory Type <span class="text-red-500">*</span></label>
                                <select id="inventory_type" name="inventory_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled @selected(blank(old('inventory_type'))) >Select inventory type</option>
                                    @foreach ($inventoryTypeOptions as $option)
                                        <option value="{{ $option }}" @selected(old('inventory_type') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="stockQtyWrap" class="{{ old('inventory_type') === 'Inventory' ? '' : 'hidden' }}">
                                <label for="stock_qty" class="mb-1 block text-sm font-medium text-gray-700">Stock Quantity</label>
                                <input id="stock_qty" name="stock_qty" type="number" min="0" step="1" value="{{ old('stock_qty') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="unit" class="mb-1 block text-sm font-medium text-gray-700">Unit</label>
                                <select id="unit" name="unit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select unit</option>
                                    @foreach ($unitOptions as $option)
                                        <option value="{{ $option }}" @selected(old('unit') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Status</h3>
                        <p class="mb-4 text-xs text-gray-500">Control record availability in the CRM.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select id="status" name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    @foreach ($statusOptions as $option)
                                        <option value="{{ $option }}" @selected(old('status', 'Active') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    @if ($customFields->count() > 0)
                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Custom Fields</h3>
                            <p class="mb-4 text-xs text-gray-500">Additional product attributes configured for the Products module.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($customFields as $field)
                                    @php
                                        $fieldName = 'custom_fields['.$field->field_key.']';
                                        $fieldValue = old('custom_fields.'.$field->field_key, $field->default_value);
                                    @endphp
                                    <div class="{{ $field->field_type === 'lookup' ? 'sm:col-span-2' : '' }}">
                                        <label for="field_{{ $field->field_key }}" class="mb-1 block text-sm font-medium text-gray-700">{{ $field->field_name }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                        @if ($field->field_type === 'picklist')
                                            <select id="field_{{ $field->field_key }}" name="{{ $fieldName }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                                <option value="">Select {{ strtolower($field->field_name) }}</option>
                                                @foreach ($field->options ?? [] as $option)
                                                    <option value="{{ $option }}" @selected((string) $fieldValue === (string) $option)>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($field->field_type === 'checkbox')
                                            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input id="field_{{ $field->field_key }}" type="checkbox" name="{{ $fieldName }}" value="1" @checked((string) $fieldValue === '1') class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span>Enabled</span>
                                            </label>
                                        @elseif ($field->field_type === 'date')
                                            <input id="field_{{ $field->field_key }}" type="date" name="{{ $fieldName }}" value="{{ $fieldValue }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        @elseif (in_array($field->field_type, ['currency', 'numerical'], true))
                                            <input id="field_{{ $field->field_key }}" type="number" step="0.01" name="{{ $fieldName }}" value="{{ $fieldValue }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        @else
                                            <input id="field_{{ $field->field_key }}" type="text" name="{{ $fieldName }}" value="{{ $fieldValue }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">System Info</h3>
                        <p class="mb-4 text-xs text-gray-500">Read-only metadata follows the same CRM panel behavior as Contacts.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created By</p>
                                <p class="text-sm text-gray-500">{{ $createdByDisplay }}</p>
                            </div>
                            <div>
                                <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created At</p>
                                <p class="text-sm text-gray-500"><span id="productCreatedAtMetaValue">{{ $createdAtDisplay }}</span></p>
                            </div>
                            <div>
                                <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Reviewed By / At</p>
                                <p class="text-sm text-gray-500">Pending review</p>
                            </div>
                            <div>
                                <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Approved By / At</p>
                                <p class="text-sm text-gray-500">Pending approval</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Updated At</p>
                                <p class="text-sm text-gray-500">Will update automatically after save.</p>
                            </div>
                        </div>
                    </section>

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
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
