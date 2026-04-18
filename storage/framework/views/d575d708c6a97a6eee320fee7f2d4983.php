<?php
    $selectedOwner = collect($owners)->firstWhere('id', (int) $defaultOwnerId) ?: collect($owners)->first();
    $selectedOwnerId = (int) ($selectedOwner['id'] ?? 0);
    $selectedOwnerName = $selectedOwner['name'] ?? 'Select Owner';
    $createdByDisplay = old('created_by', $drawerMeta['createdBy'] ?? 'Admin User');
    $createdAtDisplay = old('created_at_display', $drawerMeta['createdAtDisplay'] ?? now()->format('F j, Y g:i A'));
    $selectedProductAreas = old('product_area', ['None']);
    $selectedLinkedServiceIds = collect(old('linked_service_ids', []))
        ->map(fn ($value) => (string) $value)
        ->values()
        ->all();
?>

<div id="createProductModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createProductModalOverlay" type="button" aria-label="Close create product panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>

    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createProductPanel" class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[680px]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                <div>
                    <h2 id="createProductModalTitle" class="text-2xl font-semibold text-gray-900">Create Product</h2>
                    <p class="mt-1 text-sm text-gray-500">Capture the complete product setup before linking it to deals and services.</p>
                </div>
                <button id="closeCreateProductModal" type="button" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <form id="createProductForm" method="POST" action="<?php echo e(route('products.store')); ?>" class="flex min-h-0 flex-1 flex-col">
                <?php echo csrf_field(); ?>
                <input id="createProductFormMethod" type="hidden" name="_method" value="POST">
                <input id="product_owner_id" type="hidden" name="owner_id" value="<?php echo e(old('owner_id', $selectedOwnerId)); ?>">

                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Product Intake</p>
                            <p class="mt-1 text-sm text-gray-500">Configure the catalog record before linking it to services and deals. After saving, it will be submitted for admin approval.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 sm:items-end">
                            <div class="relative">
                                <label for="productOwnerDropdownTrigger" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Owner</label>
                                <button id="productOwnerDropdownTrigger" type="button" class="inline-flex h-10 w-full items-center justify-between gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                    <span id="productOwnerSelectedLabel">Owner: <?php echo e($selectedOwnerName); ?></span>
                                    <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                                </button>

                                <div id="productOwnerDropdownMenu" class="absolute right-0 z-20 mt-2 hidden w-full min-w-0 rounded-xl border border-gray-200 bg-white p-2 shadow-lg sm:w-72">
                                    <div class="relative mb-2">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                        <input id="productOwnerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    </div>

                                    <div class="max-h-56 space-y-1 overflow-y-auto">
                                        <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $ownerInitials = strtoupper(collect(explode(' ', trim($owner['name'])))
                                                    ->filter()
                                                    ->map(fn ($segment) => mb_substr($segment, 0, 1))
                                                    ->take(2)
                                                    ->implode(''));
                                            ?>
                                            <button
                                                type="button"
                                                class="product-owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                                data-owner-id="<?php echo e($owner['id']); ?>"
                                                data-owner-name="<?php echo e($owner['name']); ?>"
                                                data-owner-email="<?php echo e($owner['email']); ?>"
                                            >
                                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-semibold text-blue-700">
                                                    <?php echo e($ownerInitials); ?>

                                                </span>
                                                <span>
                                                    <span class="block text-sm text-gray-700"><?php echo e($owner['name']); ?></span>
                                                    <span class="block text-xs text-gray-500"><?php echo e($owner['email']); ?></span>
                                                </span>
                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created By</p>
                                    <p class="text-sm text-gray-500"><?php echo e($createdByDisplay); ?></p>
                                </div>
                                <div>
                                    <p class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created At</p>
                                    <p class="text-sm text-gray-500"><span id="productCreatedAtLiveValue"><?php echo e($createdAtDisplay); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Product Information</h3>
                        <p class="mb-4 text-xs text-gray-500">Capture the core product identity and classification.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="product_name" class="mb-1 block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                                <input id="product_name" name="product_name" required value="<?php echo e(old('product_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="product_type" class="mb-1 block text-sm font-medium text-gray-700">Product Type <span class="text-red-500">*</span></label>
                                <select id="product_type" name="product_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled <?php if(blank(old('product_type'))): echo 'selected'; endif; ?> >Select product type</option>
                                    <?php $__currentLoopData = $productTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php if(old('product_type') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div id="productTypeOtherWrap" class="mt-2 <?php echo e(old('product_type') === 'Other' ? '' : 'hidden'); ?>">
                                    <input id="product_type_other" name="product_type_other" value="<?php echo e(old('product_type_other')); ?>" placeholder="Enter custom product type" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="sku" class="mb-1 block text-sm font-medium text-gray-700">SKU / Code</label>
                                <input id="sku" name="sku" value="<?php echo e($nextSku ?? old('sku')); ?>" data-default-sku="<?php echo e($nextSku ?? old('sku')); ?>" readonly class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-600 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Auto-generated on create using the format `PRD-REG-001`.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Service Linking</h3>
                        <p class="mb-4 text-xs text-gray-500">Select one or more service areas, then link this product to the matching services.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Service Area <span class="text-red-500">*</span></label>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <?php $__currentLoopData = $productAreaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                            <input type="checkbox" name="product_area[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $selectedProductAreas, true)): echo 'checked'; endif; ?> class="product-area-checkbox h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" <?php if($option === 'Others'): ?> data-other-target="productAreaOtherWrap" <?php endif; ?>>
                                            <span><?php echo e($option); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <div id="productAreaOtherWrap" class="<?php echo e(in_array('Others', $selectedProductAreas, true) ? '' : 'hidden'); ?> sm:col-span-2">
                                <label for="product_area_other" class="mb-1 block text-sm font-medium text-gray-700">Other Product Area</label>
                                <input id="product_area_other" name="product_area_other" value="<?php echo e(old('product_area_other')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Linked Services</label>
                                <div id="linkedServicesEmptyState" class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-500">
                                    Select a service area to show matching services.
                                </div>
                                <div id="linkedServicesList" class="hidden grid gap-2"></div>
                                <template id="linkedServiceOptionTemplate">
                                    <label class="linked-service-option flex items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-700">
                                        <input type="checkbox" name="linked_service_ids[]" class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>
                                            <span class="linked-service-name block font-medium text-gray-800"></span>
                                            <span class="linked-service-meta mt-0.5 block text-xs text-gray-500"></span>
                                        </span>
                                    </label>
                                </template>
                                <p class="mt-1 text-xs text-gray-500">You can link this product to multiple services under the selected service areas.</p>
                                <div id="selectedLinkedServiceIds" data-selected-service-ids='<?php echo json_encode($selectedLinkedServiceIds, 15, 512) ?>'></div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Description</h3>
                        <p class="mb-4 text-xs text-gray-500">Provide the main description and inclusions for quoting and internal review.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="product_description" class="mb-1 block text-sm font-medium text-gray-700">Product Description <span class="text-red-500">*</span></label>
                                <textarea id="product_description" name="product_description" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('product_description')); ?></textarea>
                            </div>
                            <div>
                                <label for="product_inclusions" class="mb-1 block text-sm font-medium text-gray-700">Product Inclusions</label>
                                <textarea id="product_inclusions" name="product_inclusions" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('product_inclusions')); ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">Each new line will be saved as a bullet.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Requirements</h3>
                        <p class="mb-4 text-xs text-gray-500">Set the default product requirements by client type, similar to Services.</p>
                        <div class="space-y-4">
                            <div>
                                <label for="product_requirements_individual" class="mb-1 block text-sm font-medium text-gray-700">Individual Requirements</label>
                                <textarea id="product_requirements_individual" name="requirements_individual" rows="4" class="product-requirements-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Enter one requirement per line"><?php echo e(old('requirements_individual', $requirementTemplateDefaults['individual'] ?? '')); ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">Default template: Valid ID, DTI Registration.</p>
                                <div class="product-requirements-preview mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2 hidden"></div>
                            </div>
                            <div>
                                <label for="product_requirements_juridical" class="mb-1 block text-sm font-medium text-gray-700">Juridical Requirements</label>
                                <textarea id="product_requirements_juridical" name="requirements_juridical" rows="4" class="product-requirements-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Enter one requirement per line"><?php echo e(old('requirements_juridical', $requirementTemplateDefaults['juridical'] ?? '')); ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">Default template: SEC Registration, GIS, Articles of Incorporation.</p>
                                <div class="product-requirements-preview mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2 hidden"></div>
                            </div>
                            <div>
                                <label for="product_requirements_other" class="mb-1 block text-sm font-medium text-gray-700">Other Requirements</label>
                                <textarea id="product_requirements_other" name="requirements_other" rows="4" class="product-requirements-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Enter one requirement per line"><?php echo e(old('requirements_other', $requirementTemplateDefaults['other'] ?? '')); ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">Default template: Special Permit.</p>
                                <div class="product-requirements-preview mt-2 rounded-lg border border-dashed border-gray-200 bg-white px-3 py-2 hidden"></div>
                            </div>
                            <input id="product_requirement_category" type="hidden" name="requirement_category" value="<?php echo e(old('requirement_category')); ?>">
                            <input id="product_requirements_legacy" type="hidden" name="requirements" value="<?php echo e(old('requirements')); ?>">
                            <?php $__errorArgs = ['requirement_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Accounting</h3>
                        <p class="mb-4 text-xs text-gray-500">Map the product to the proper accounting category.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="category" class="mb-1 block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="category" name="category" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled <?php if(blank(old('category'))): echo 'selected'; endif; ?> >Select category</option>
                                    <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category); ?>" <?php if(old('category') === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div id="categoryOtherWrap" class="mt-2 <?php echo e(old('category') === 'Other' ? '' : 'hidden'); ?>">
                                    <input id="category_other" name="category_other" value="<?php echo e(old('category_other')); ?>" placeholder="Enter custom category" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Pricing</h3>
                        <p class="mb-4 text-xs text-gray-500">Define the pricing model, cost basis, and discount behavior.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="pricing_type" class="mb-1 block text-sm font-medium text-gray-700">Pricing Type <span class="text-red-500">*</span></label>
                                <select id="pricing_type" name="pricing_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled <?php if(blank(old('pricing_type'))): echo 'selected'; endif; ?> >Select pricing type</option>
                                    <?php $__currentLoopData = $pricingTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php if(old('pricing_type') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Price <span class="text-red-500">*</span></label>
                                <input id="price" name="price" type="number" min="0" step="0.01" value="<?php echo e(old('price')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="cost" class="mb-1 block text-sm font-medium text-gray-700">Cost</label>
                                <input id="cost" name="cost" type="number" min="0" step="0.01" value="<?php echo e(old('cost')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_discountable" value="1" <?php if(old('is_discountable')): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>Discountable</span>
                                </label>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="tax_type" class="mb-1 block text-sm font-medium text-gray-700">Tax Type <span class="text-red-500">*</span></label>
                                <select id="tax_type" name="tax_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="" disabled <?php if(blank(old('tax_type'))): echo 'selected'; endif; ?> >Select tax type</option>
                                    <?php $__currentLoopData = ['VAT', 'Non-VAT', 'Zero-rated', 'Exempt']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php if(old('tax_type', 'VAT') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Tax Treatment <span class="text-red-500">*</span></label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <?php $__currentLoopData = ['Tax Inclusive', 'Tax Exclusive']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700">
                                            <input
                                                name="tax_treatment"
                                                type="radio"
                                                value="<?php echo e($option); ?>"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                <?php if(old('tax_treatment', 'Tax Exclusive') === $option): echo 'checked'; endif; ?>
                                            >
                                            <span><?php echo e($option); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
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
                                    <option value="" disabled <?php if(blank(old('inventory_type'))): echo 'selected'; endif; ?> >Select inventory type</option>
                                    <?php $__currentLoopData = $inventoryTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php if(old('inventory_type') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div id="inventoryTypeOtherWrap" class="mt-2 <?php echo e(old('inventory_type') === 'Other' ? '' : 'hidden'); ?>">
                                    <input id="inventory_type_other" name="inventory_type_other" value="<?php echo e(old('inventory_type_other')); ?>" placeholder="Enter custom inventory type" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                            </div>
                            <div id="stockQtyWrap" class="<?php echo e(old('inventory_type') === 'Inventory' ? '' : 'hidden'); ?>">
                                <label for="stock_qty" class="mb-1 block text-sm font-medium text-gray-700">Stock Quantity</label>
                                <input id="stock_qty" name="stock_qty" type="number" min="0" step="1" value="<?php echo e(old('stock_qty')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="unit" class="mb-1 block text-sm font-medium text-gray-700">Unit</label>
                                <select id="unit" name="unit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select unit</option>
                                    <?php $__currentLoopData = $unitOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php if(old('unit') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </section>

                    <?php if($customFields->count() > 0): ?>
                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Custom Fields</h3>
                            <p class="mb-4 text-xs text-gray-500">Additional product attributes configured for the Products module.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $fieldName = 'custom_fields['.$field->field_key.']';
                                        $fieldValue = old('custom_fields.'.$field->field_key, $field->default_value);
                                    ?>
                                    <div class="<?php echo e($field->field_type === 'lookup' ? 'sm:col-span-2' : ''); ?>">
                                        <label for="field_<?php echo e($field->field_key); ?>" class="mb-1 block text-sm font-medium text-gray-700"><?php echo e($field->field_name); ?> <?php if($field->is_required): ?><span class="text-red-500">*</span><?php endif; ?></label>
                                        <?php if($field->field_type === 'picklist'): ?>
                                            <select id="field_<?php echo e($field->field_key); ?>" name="<?php echo e($fieldName); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                                <option value="">Select <?php echo e(strtolower($field->field_name)); ?></option>
                                                <?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($option); ?>" <?php if((string) $fieldValue === (string) $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        <?php elseif($field->field_type === 'checkbox'): ?>
                                            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input id="field_<?php echo e($field->field_key); ?>" type="checkbox" name="<?php echo e($fieldName); ?>" value="1" <?php if((string) $fieldValue === '1'): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span>Enabled</span>
                                            </label>
                                        <?php elseif($field->field_type === 'date'): ?>
                                            <input id="field_<?php echo e($field->field_key); ?>" type="date" name="<?php echo e($fieldName); ?>" value="<?php echo e($fieldValue); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <?php elseif(in_array($field->field_type, ['currency', 'numerical'], true)): ?>
                                            <input id="field_<?php echo e($field->field_key); ?>" type="number" step="0.01" name="<?php echo e($fieldName); ?>" value="<?php echo e($fieldValue); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <?php else: ?>
                                            <input id="field_<?php echo e($field->field_key); ?>" type="text" name="<?php echo e($fieldName); ?>" value="<?php echo e($fieldValue); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            <?php echo e($errors->first()); ?>

                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-auto flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                    <button id="cancelCreateProductModal" type="button" class="h-10 min-w-[100px] rounded-lg border border-gray-300 px-5 text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button id="createProductFormSubmit" type="submit" class="h-10 min-w-[100px] rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\School\ojt\jknc_work\jknc_project\resources\views/products/partials/create-product-modal.blade.php ENDPATH**/ ?>