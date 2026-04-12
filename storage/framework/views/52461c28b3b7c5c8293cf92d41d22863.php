<?php $__env->startSection('content'); ?>
<?php
    $activePillClasses = [
        'Pending Approval' => 'border-amber-200 bg-amber-50 text-amber-700',
        'Active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'Inactive' => 'border-slate-200 bg-slate-50 text-slate-700',
        'Rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
        'Archived' => 'border-rose-200 bg-rose-50 text-rose-700',
    ];
?>

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900"><?php echo e($isAdminReviewer ? 'Products Review' : 'Products'); ?></h1>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo e($isAdminReviewer
                        ? 'Review submitted products and keep the approved catalog aligned with the services and deals workflow.'
                        : 'Standardized product catalog with service-linked offerings, pricing, ownership, and approval controls.'); ?>

                </p>
            </div>
            <button
                type="button"
                id="openCreateProductModal"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <i class="fas fa-plus mr-2 text-xs"></i> Add Product
            </button>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="mb-6 grid gap-3 xl:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e($isAdminReviewer ? 'Pending Review' : 'Active Products'); ?></p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($isAdminReviewer ? $summary['pending'] : $summary['active']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e($isAdminReviewer ? 'Active Products' : 'Pending Approval'); ?></p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($isAdminReviewer ? $summary['active'] : $summary['pending']); ?></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rejected Products</p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($summary['rejected']); ?></p>
            </div>
        </div>

        <?php if($isAdminReviewer && $pendingChangeRequests->isNotEmpty()): ?>
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Pending Product Change Requests</h2>
                        <p class="text-sm text-gray-600">Requested edits and removals stay queued here until an admin approves them.</p>
                    </div>
                    <div class="text-sm font-medium text-amber-800">
                        <?php echo e($pendingChangeRequests->count()); ?> pending <?php echo e(\Illuminate\Support\Str::plural('request', $pendingChangeRequests->count())); ?>

                    </div>
                </div>
            </div>

            <div class="mb-6 space-y-4">
                <?php $__currentLoopData = $pendingChangeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $changeRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($changeRequest->record_name ?: 'Product'); ?></h3>
                                    <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Pending <?php echo e(ucfirst($changeRequest->action)); ?>

                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Product ID <?php echo e($changeRequest->record_public_id ?: '-'); ?> • Requested by <?php echo e($changeRequest->submitter?->name ?: 'Unknown user'); ?>

                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2 xl:justify-end">
                                <form method="POST" action="<?php echo e(route('catalog-change-requests.approve', $changeRequest)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex h-10 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-sm font-medium text-emerald-700 hover:bg-emerald-100">Approve</button>
                                </form>
                                <form method="POST" action="<?php echo e(route('catalog-change-requests.reject', $changeRequest)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex h-10 items-center rounded-lg border border-rose-200 bg-rose-50 px-4 text-sm font-medium text-rose-700 hover:bg-rose-100">Reject</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="GET" action="<?php echo e(route('products.index')); ?>" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 xl:grid-cols-[minmax(260px,1.6fr)_repeat(4,minmax(170px,1fr))_minmax(190px,1fr)_auto]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo e($filters['search'] ?? $search); ?>"
                        placeholder="Search products..."
                        class="h-11 w-full rounded-xl border border-gray-200 bg-white pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <select
                    name="status"
                    class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                    <option value="all" <?php if(($filters['status'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>Status: All</option>
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if(($filters['status'] ?? 'all') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="category" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all" <?php if(($filters['category'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>Category: All</option>
                    <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if(($filters['category'] ?? 'all') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="product_type" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all" <?php if(($filters['product_type'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>Product Type: All</option>
                    <?php $__currentLoopData = $productTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if(($filters['product_type'] ?? 'all') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="inventory_type" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all" <?php if(($filters['inventory_type'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>Inventory: All</option>
                    <?php $__currentLoopData = $inventoryTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php if(($filters['inventory_type'] ?? 'all') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="owner_id" class="h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="all" <?php if(($filters['owner_id'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>Owner: All</option>
                    <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($owner['id']); ?>" <?php if((string) ($filters['owner_id'] ?? 'all') === (string) $owner['id']): echo 'selected'; endif; ?>><?php echo e($owner['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                    <button class="h-11 rounded-xl border border-gray-200 bg-gray-900 px-5 text-sm font-medium text-white hover:bg-gray-800">Apply</button>
                </div>
            </div>
        </form>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <form method="GET" action="<?php echo e(route('products.index')); ?>" class="flex flex-col gap-2 sm:ml-6 sm:flex-row sm:items-center">
                        <label for="productAreaQuickFilter" class="text-sm font-medium text-gray-600">Service Area</label>
                        <select id="productAreaQuickFilter" name="service_area" onchange="this.form.submit()" class="h-10 min-w-[320px] rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="all" <?php if(($filters['service_area'] ?? 'all') === 'all'): echo 'selected'; endif; ?>>All Service Areas</option>
                            <?php $__currentLoopData = $productAreaFilterOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($option); ?>" <?php if(($filters['service_area'] ?? 'all') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="hidden" name="search" value="<?php echo e($filters['search'] ?? $search); ?>">
                        <input type="hidden" name="status" value="<?php echo e($filters['status'] ?? 'all'); ?>">
                        <input type="hidden" name="category" value="<?php echo e($filters['category'] ?? 'all'); ?>">
                        <input type="hidden" name="product_type" value="<?php echo e($filters['product_type'] ?? 'all'); ?>">
                        <input type="hidden" name="inventory_type" value="<?php echo e($filters['inventory_type'] ?? 'all'); ?>">
                        <input type="hidden" name="owner_id" value="<?php echo e($filters['owner_id'] ?? 'all'); ?>">
                        <input type="hidden" name="per_page" value="<?php echo e($filters['per_page'] ?? $perPage); ?>">
                    </form>
                </div>
                <button id="openCreateFieldDropdown" type="button" class="self-start text-sm font-medium text-blue-600 hover:text-blue-700 lg:self-auto">+ Create Field</button>
            </div>
        </div>
        <div class="overflow-x-auto overflow-y-visible">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-700">
                    <tr>
                        <th data-column-key="product_name" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Name</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_name" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="sku" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>SKU</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="sku" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="product_type" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Type</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_type" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="category" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Category</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="category" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="price" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Price</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="price" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="status" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Status</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="status" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <th data-column-key="product_owner" data-column-type="base" class="group px-3 py-3 text-left font-medium">
                            <div class="inline-flex items-center gap-1">
                                <span>Product Owner</span>
                                <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="product_owner" data-column-type="base">
                                    <i class="fas fa-ellipsis-v text-[10px]"></i>
                                </button>
                            </div>
                        </th>
                        <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th data-column-key="<?php echo e($field->field_key); ?>" data-column-type="custom" class="group px-3 py-3 text-left font-medium">
                                <div class="inline-flex items-center gap-1">
                                    <span><?php echo e($field->field_name); ?></span>
                                    <button type="button" class="field-header-trigger invisible inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-400 transition group-hover:visible hover:bg-gray-100 hover:text-gray-600" data-column-key="<?php echo e($field->field_key); ?>" data-column-type="custom">
                                        <i class="fas fa-ellipsis-v text-[10px]"></i>
                                    </button>
                                </div>
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="w-[180px] px-3 py-3 text-left font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $pendingRequest = $pendingRequestMap->get($product->id);
                            ?>
                            <tr class="product-row text-gray-700" data-row-index="<?php echo e($index); ?>" data-product-url="<?php echo e(route('products.show', $product->product_id)); ?>">
                            <td data-column-key="product_name" class="px-3 py-3 font-medium text-gray-900">
                                <a href="<?php echo e(route('products.show', $product->product_id)); ?>" class="hover:text-blue-700">
                                    <?php echo e($product->product_name); ?>

                                </a>
                            </td>
                            <td data-column-key="sku" class="px-3 py-3 text-gray-600"><?php echo e($product->sku ?: '-'); ?></td>
                            <td data-column-key="product_type" class="px-3 py-3 text-gray-600"><?php echo e($product->product_type); ?></td>
                            <td data-column-key="category" class="px-3 py-3 text-gray-600"><?php echo e($product->category); ?></td>
                            <td data-column-key="price" class="px-3 py-3 text-gray-600">P<?php echo e(number_format((float) $product->price, 2)); ?></td>
                            <td data-column-key="status" class="px-3 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex w-fit rounded-full border px-2.5 py-1 text-xs font-medium <?php echo e($activePillClasses[$product->status] ?? 'border-gray-200 bg-gray-50 text-gray-700'); ?>">
                                        <?php echo e($product->status); ?>

                                    </span>
                                    <?php if($pendingRequest): ?>
                                        <span class="inline-flex w-fit rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                            Pending <?php echo e(ucfirst($pendingRequest->action)); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-column-key="product_owner" class="product-owner-cell px-3 py-3 text-gray-600"><?php echo e($product->owner_name ?: '-'); ?></td>
                            <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $customValue = data_get($product->custom_field_values, $field->field_key, '');
                                ?>
                                <td data-column-key="<?php echo e($field->field_key); ?>" class="px-3 py-3 text-gray-600">
                                    <?php if(($field->field_type ?? '') === 'checkbox'): ?>
                                        <?php echo e($customValue === '1' ? 'Yes' : 'No'); ?>

                                    <?php elseif(($field->field_type ?? '') === 'currency' && $customValue !== ''): ?>
                                        P<?php echo e(number_format((float) $customValue, 2)); ?>

                                <?php else: ?>
                                    <?php echo e($customValue !== '' ? $customValue : '-'); ?>

                                <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-start gap-2 whitespace-nowrap">
                                    <?php if($pendingRequest): ?>
                                        <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                            Pending <?php echo e(ucfirst($pendingRequest->action)); ?>

                                        </span>
                                        <?php if($isAdminReviewer): ?>
                                            <form method="POST" action="<?php echo e(route('catalog-change-requests.approve', $pendingRequest)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="rounded-full border border-emerald-200 px-3 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50">Approve</button>
                                            </form>
                                            <form method="POST" action="<?php echo e(route('catalog-change-requests.reject', $pendingRequest)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="rounded-full border border-rose-200 px-3 py-1 text-xs font-medium text-rose-700 hover:bg-rose-50">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button type="button" class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" data-product-edit='<?php echo json_encode($product, 15, 512) ?>'>Edit</button>
                                        <form method="POST" action="<?php echo e(route('products.destroy', $product->product_id)); ?>" onsubmit="return confirm('Submit a delete request for this product?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="rounded-full border border-red-200 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(8 + count($customFields)); ?>" class="px-3 py-10 text-center text-sm text-gray-500">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-700">
        <span>Total Products: <?php echo e($totalProducts); ?></span>
        <div class="ml-auto flex items-center gap-2 text-xs text-gray-600">
            <form method="GET" action="<?php echo e(route('products.index')); ?>" class="flex items-center gap-2">
                <span>Records per page</span>
                <select name="per_page" class="h-7 rounded border border-gray-200 px-2 text-xs" onchange="this.form.submit()">
                    <?php $__currentLoopData = [5, 10, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($size); ?>" <?php echo e((int) $perPage === $size ? 'selected' : ''); ?>><?php echo e($size); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="hidden" name="search" value="<?php echo e($filters['search'] ?? $search); ?>">
                <input type="hidden" name="status" value="<?php echo e($filters['status'] ?? 'all'); ?>">
                <input type="hidden" name="category" value="<?php echo e($filters['category'] ?? 'all'); ?>">
                <input type="hidden" name="product_type" value="<?php echo e($filters['product_type'] ?? 'all'); ?>">
                <input type="hidden" name="inventory_type" value="<?php echo e($filters['inventory_type'] ?? 'all'); ?>">
                <input type="hidden" name="owner_id" value="<?php echo e($filters['owner_id'] ?? 'all'); ?>">
                <input type="hidden" name="service_area" value="<?php echo e($filters['service_area'] ?? 'all'); ?>">
            </form>
            <span><?php echo e($from); ?> to <?php echo e($to); ?> | <?php echo e($currentPage); ?> to <?php echo e($totalPages); ?></span>
        </div>
    </div>
    </div>
</div>

<?php echo $__env->make('products.partials.create-product-modal', [
    'owners' => $owners,
    'defaultOwnerId' => $defaultOwnerId,
    'categoryOptions' => $categoryOptions,
    'productTypeOptions' => $productTypeOptions,
    'productAreaOptions' => $productAreaOptions,
    'pricingTypeOptions' => $pricingTypeOptions,
    'inventoryTypeOptions' => $inventoryTypeOptions,
    'statusOptions' => $statusOptions,
    'unitOptions' => $unitOptions,
    'serviceOptions' => $serviceOptions,
    'customFields' => $customFields,
    'requirementTemplateDefaults' => $requirementTemplateDefaults,
    'drawerMeta' => $drawerMeta,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.create-field-dropdown', ['fieldTypes' => $fieldTypes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.field-actions-dropdown', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.create-field-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('products.partials.change-owner-modal', [
    'owners' => $owners,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceOptionsData = <?php echo json_encode($serviceOptions, 15, 512) ?>;
    const createProductModal = document.getElementById('createProductModal');
    const createProductForm = document.getElementById('createProductForm');
    const createProductFormMethod = document.getElementById('createProductFormMethod');
    const createProductModalTitle = document.getElementById('createProductModalTitle');
    const createProductFormSubmit = document.getElementById('createProductFormSubmit');
    const openCreateModalButton = document.getElementById('openCreateProductModal');
    const closeCreateModalButton = document.getElementById('closeCreateProductModal');
    const cancelCreateModalButton = document.getElementById('cancelCreateProductModal');
    const productOwnerDropdownTrigger = document.getElementById('productOwnerDropdownTrigger');
    const productOwnerDropdownMenu = document.getElementById('productOwnerDropdownMenu');
    const productOwnerSearch = document.getElementById('productOwnerSearch');
    const productOwnerIdInput = document.getElementById('product_owner_id');
    const productOwnerSelectedLabel = document.getElementById('productOwnerSelectedLabel');
    const productCreatedAtLiveValue = document.getElementById('productCreatedAtLiveValue');
    const productCreatedAtMetaValue = document.getElementById('productCreatedAtMetaValue');
    const productNameInput = document.getElementById('product_name');
    const inventoryTypeInput = document.getElementById('inventory_type');
    const productTypeInput = document.getElementById('product_type');
    const categoryInput = document.getElementById('category');
    const productTypeOtherWrap = document.getElementById('productTypeOtherWrap');
    const categoryOtherWrap = document.getElementById('categoryOtherWrap');
    const inventoryTypeOtherWrap = document.getElementById('inventoryTypeOtherWrap');
    const skuInput = document.getElementById('sku');
    const stockQtyWrap = document.getElementById('stockQtyWrap');
    const productAreaOtherWrap = document.getElementById('productAreaOtherWrap');
    const productAreaCheckboxes = Array.from(document.querySelectorAll('.product-area-checkbox'));
    const linkedServicesList = document.getElementById('linkedServicesList');
    const linkedServicesEmptyState = document.getElementById('linkedServicesEmptyState');
    const linkedServiceOptionTemplate = document.getElementById('linkedServiceOptionTemplate');
    const selectedLinkedServiceIdsElement = document.getElementById('selectedLinkedServiceIds');
    const productOwnerOptions = Array.from(document.querySelectorAll('.product-owner-option'));
    const productDescriptionInput = document.getElementById('product_description');
    const productInclusionsInput = document.getElementById('product_inclusions');
    const pricingTypeInput = document.getElementById('pricing_type');
    const priceInput = document.getElementById('price');
    const costInput = document.getElementById('cost');
    const isDiscountableInput = document.querySelector('input[name="is_discountable"]');
    const taxTypeInput = document.getElementById('tax_type');
    const unitInput = document.getElementById('unit');
    const tableRows = Array.from(document.querySelectorAll('.product-row'));
    const productEditButtons = Array.from(document.querySelectorAll('[data-product-edit]'));
    const productRequirementsInputs = Array.from(document.querySelectorAll('.product-requirements-input'));

    const openCreateFieldDropdown = document.getElementById('openCreateFieldDropdown');
    const createFieldDropdownMenu = document.getElementById('createFieldDropdownMenu');
    const fieldTypeButtons = Array.from(document.querySelectorAll('.create-field-type-option'));
    const createFieldModal = document.getElementById('createFieldModal');
    const createFieldPanel = document.getElementById('createFieldPanel');
    const createFieldModalOverlay = document.getElementById('createFieldModalOverlay');
    const closeCreateFieldModal = document.getElementById('closeCreateFieldModal');
    const cancelCreateFieldModal = document.getElementById('cancelCreateFieldModal');
    const createFieldTypeInput = document.getElementById('createFieldTypeInput');
    const createFieldTypeLabel = document.getElementById('createFieldTypeLabel');
    const picklistOptionsSection = document.getElementById('picklistOptionsSection');
    const picklistOptionsContainer = document.getElementById('picklistOptionsContainer');
    const addPicklistOption = document.getElementById('addPicklistOption');
    const defaultValueSection = document.getElementById('defaultValueSection');
    const lookupSection = document.getElementById('lookupSection');
    const defaultValueInput = document.getElementById('default_value');
    const headerActionTriggers = Array.from(document.querySelectorAll('.field-header-trigger'));
    const fieldActionsMenu = document.getElementById('fieldActionsMenu');
    const fieldActionButtons = Array.from(document.querySelectorAll('.field-action-item'));
    const tableHead = document.querySelector('table thead');
    const tableBody = document.querySelector('table tbody');
    let createFieldDropdownOpen = false;
    let fieldActionsMenuOpen = false;
    let activeFieldColumnKey = null;
    let activeFieldIsCustom = false;
    const columnSortState = {};
    const columnFilters = {};

    const createProductPanel = document.getElementById('createProductPanel');
    const createProductModalOverlay = document.getElementById('createProductModalOverlay');
    let productCreatedAtIntervalId = null;
    const createProductUrl = <?php echo json_encode(route('products.store'), 15, 512) ?>;
    const updateProductUrlTemplate = <?php echo json_encode(route('products.update', '__PRODUCT__'), 512) ?>;
    const defaultOwnerIdValue = <?php echo json_encode((string) $defaultOwnerId, 15, 512) ?>;
    const defaultOwnerNameValue = <?php echo json_encode($selectedOwnerName ?? 'Select Owner', 15, 512) ?>;

    const formatCreatedAt = (date) => new Intl.DateTimeFormat('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
    }).format(date);

    const renderProductCreatedAtClock = () => {
        const label = formatCreatedAt(new Date());
        if (productCreatedAtLiveValue) {
            productCreatedAtLiveValue.textContent = label;
        }
        if (productCreatedAtMetaValue) {
            productCreatedAtMetaValue.textContent = label;
        }
    };

    const stopProductCreatedAtClock = () => {
        if (productCreatedAtIntervalId) {
            window.clearInterval(productCreatedAtIntervalId);
            productCreatedAtIntervalId = null;
        }
    };

    const startProductCreatedAtClock = () => {
        stopProductCreatedAtClock();
        renderProductCreatedAtClock();
        productCreatedAtIntervalId = window.setInterval(renderProductCreatedAtClock, 1000);
    };

    const syncInventoryFields = () => {
        const showStock = inventoryTypeInput?.value === 'Inventory';
        stockQtyWrap?.classList.toggle('hidden', !showStock);
        inventoryTypeOtherWrap?.classList.toggle('hidden', inventoryTypeInput?.value !== 'Other');
    };

    const syncCustomSelectField = (selectInput, otherWrap) => {
        otherWrap?.classList.toggle('hidden', selectInput?.value !== 'Other');
    };

    const syncProductAreaOther = () => {
        const hasOther = productAreaCheckboxes.some((checkbox) => checkbox.checked && checkbox.value === 'Others');
        productAreaOtherWrap?.classList.toggle('hidden', !hasOther);
    };

    const syncProductAreaSelection = (changedCheckbox = null) => {
        const noneCheckbox = productAreaCheckboxes.find((checkbox) => checkbox.value === 'None');
        const nonNoneCheckboxes = productAreaCheckboxes.filter((checkbox) => checkbox.value !== 'None');

        if (changedCheckbox?.value === 'None' && changedCheckbox.checked) {
            nonNoneCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        }

        if (changedCheckbox && changedCheckbox.value !== 'None' && changedCheckbox.checked && noneCheckbox) {
            noneCheckbox.checked = false;
        }

        const anyNonNoneChecked = nonNoneCheckboxes.some((checkbox) => checkbox.checked);
        if (!anyNonNoneChecked && noneCheckbox && !noneCheckbox.checked) {
            noneCheckbox.checked = true;
        }

        if (!anyNonNoneChecked && noneCheckbox) {
            noneCheckbox.checked = true;
        }

        syncProductAreaOther();
        renderLinkedServiceOptions();
    };

    const selectedProductAreas = () => productAreaCheckboxes
        .filter((checkbox) => checkbox.checked && checkbox.value !== 'None')
        .map((checkbox) => checkbox.value);

    const selectedLinkedServiceIds = () => {
        const currentSelections = Array.from(document.querySelectorAll('input[name="linked_service_ids[]"]:checked'))
            .map((input) => String(input.value));

        if (currentSelections.length > 0) {
            return currentSelections;
        }

        if (!selectedLinkedServiceIdsElement?.dataset?.selectedServiceIds) {
            return [];
        }

        try {
            return JSON.parse(selectedLinkedServiceIdsElement.dataset.selectedServiceIds);
        } catch (error) {
            return [];
        }
    };

    const renderLinkedServiceOptions = () => {
        if (!linkedServicesList || !linkedServiceOptionTemplate) {
            return;
        }

        const activeAreas = selectedProductAreas();
        const preselectedIds = new Set(selectedLinkedServiceIds().map(String));
        const matchingServices = serviceOptionsData.filter((service) => {
            const serviceAreas = Array.isArray(service.service_area) ? service.service_area : [];
            return activeAreas.some((area) => serviceAreas.includes(area));
        });

        linkedServicesList.innerHTML = '';

        if (matchingServices.length === 0) {
            linkedServicesList.classList.add('hidden');
            linkedServicesEmptyState?.classList.remove('hidden');
            linkedServicesEmptyState.textContent = activeAreas.length === 0
                ? 'Select a service area to show matching services.'
                : 'No services found for the selected service area.';
            return;
        }

        linkedServicesEmptyState?.classList.add('hidden');
        linkedServicesList.classList.remove('hidden');

        matchingServices.forEach((service) => {
            const fragment = linkedServiceOptionTemplate.content.cloneNode(true);
            const label = fragment.querySelector('.linked-service-option');
            const input = fragment.querySelector('input[name="linked_service_ids[]"]');
            const name = fragment.querySelector('.linked-service-name');
            const meta = fragment.querySelector('.linked-service-meta');

            if (input) {
                input.value = String(service.id);
                input.checked = preselectedIds.has(String(service.id));
            }

            if (name) {
                name.textContent = service.service_name || service.name;
            }

            if (meta) {
                const areaLabel = Array.isArray(service.service_area) ? service.service_area.join(', ') : '';
                meta.textContent = [service.service_id, areaLabel].filter(Boolean).join(' | ');
            }

            linkedServicesList.appendChild(fragment);
        });
    };

    const openCreateModal = () => {
        if (!createProductModal || !createProductPanel) {
            return;
        }

        createProductModal.classList.remove('hidden');
        createProductModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            createProductModalOverlay?.classList.remove('opacity-0');
            createProductPanel.classList.remove('translate-x-full');
        });

        if (skuInput?.dataset?.defaultSku) {
            skuInput.value = skuInput.dataset.defaultSku;
        }
        startProductCreatedAtClock();
        syncInventoryFields();
        syncProductAreaOther();
        renderLinkedServiceOptions();
    };

    const resetCreateProductForm = () => {
        createProductForm?.reset();
        if (createProductForm) {
            createProductForm.action = createProductUrl;
        }
        if (createProductFormMethod) {
            createProductFormMethod.value = 'POST';
        }
        if (createProductModalTitle) {
            createProductModalTitle.textContent = 'Create Product';
        }
        if (createProductFormSubmit) {
            createProductFormSubmit.textContent = 'Save';
        }
        if (skuInput?.dataset?.defaultSku) {
            skuInput.value = skuInput.dataset.defaultSku;
        }
        productAreaCheckboxes.forEach((checkbox) => {
            checkbox.checked = checkbox.value === 'None';
        });
        if (selectedLinkedServiceIdsElement) {
            selectedLinkedServiceIdsElement.dataset.selectedServiceIds = '[]';
        }
        if (productOwnerIdInput) {
            productOwnerIdInput.value = defaultOwnerIdValue;
        }
        if (productOwnerSelectedLabel) {
            productOwnerSelectedLabel.textContent = `Owner: ${defaultOwnerNameValue}`;
        }
        document.querySelectorAll('input[name="tax_treatment"]').forEach((input) => {
            input.checked = input.value === 'Tax Exclusive';
        });
        document.getElementById('product_requirement_category').value = '';
        document.getElementById('product_requirements_legacy').value = '';
        syncInventoryFields();
        syncCustomSelectField(productTypeInput, productTypeOtherWrap);
        syncCustomSelectField(categoryInput, categoryOtherWrap);
        syncProductAreaSelection();
        renderLinkedServiceOptions();
        renderRequirementPreviews();
    };

    const fillProductForm = (product) => {
        if (createProductForm) {
            createProductForm.action = updateProductUrlTemplate.replace('__PRODUCT__', product.product_id);
        }
        if (createProductFormMethod) {
            createProductFormMethod.value = 'PUT';
        }
        if (createProductModalTitle) {
            createProductModalTitle.textContent = 'Edit Product';
        }
        if (createProductFormSubmit) {
            createProductFormSubmit.textContent = 'Update';
        }

        productOwnerIdInput.value = product.owner_id ?? '';
        productOwnerSelectedLabel.textContent = `Owner: ${product.owner_name || product.created_by || 'Select Owner'}`;
        productCreatedAtLiveValue.textContent = product.created_at ? formatCreatedAt(new Date(product.created_at)) : productCreatedAtLiveValue.textContent;
        if (productNameInput) {
            productNameInput.value = product.product_name ?? '';
        }
        const productTypeOtherInput = document.getElementById('product_type_other');
        const hasKnownProductType = Array.from(productTypeInput?.options ?? []).some((option) => option.value === (product.product_type ?? ''));
        productTypeInput.value = hasKnownProductType ? (product.product_type ?? '') : 'Other';
        if (productTypeOtherInput) {
            productTypeOtherInput.value = hasKnownProductType ? '' : (product.product_type ?? '');
        }
        skuInput.value = product.sku ?? '';
        productDescriptionInput.value = product.product_description ?? '';
        productInclusionsInput.value = product.product_inclusions ?? '';
        const requirementGroups = product.requirements?.groups ?? {};
        document.getElementById('product_requirements_individual').value = Array.isArray(requirementGroups.individual) ? requirementGroups.individual.join('\n') : '';
        document.getElementById('product_requirements_juridical').value = Array.isArray(requirementGroups.juridical) ? requirementGroups.juridical.join('\n') : '';
        document.getElementById('product_requirements_other').value = Array.isArray(requirementGroups.other) ? requirementGroups.other.join('\n') : '';
        document.getElementById('product_requirement_category').value = product.requirement_category ?? '';
        document.getElementById('product_requirements_legacy').value = '';
        const categoryOtherInput = document.getElementById('category_other');
        const hasKnownCategory = Array.from(categoryInput?.options ?? []).some((option) => option.value === (product.category ?? ''));
        categoryInput.value = hasKnownCategory ? (product.category ?? '') : 'Other';
        if (categoryOtherInput) {
            categoryOtherInput.value = hasKnownCategory ? '' : (product.category ?? '');
        }
        pricingTypeInput.value = product.pricing_type ?? '';
        priceInput.value = product.price ?? '';
        costInput.value = product.cost ?? '';
        if (isDiscountableInput) {
            isDiscountableInput.checked = Boolean(product.is_discountable);
        }
        taxTypeInput.value = product.tax_type ?? 'VAT';
        document.querySelectorAll('input[name="tax_treatment"]').forEach((input) => {
            input.checked = input.value === (product.tax_treatment ?? 'Tax Exclusive');
        });
        const inventoryTypeOtherInput = document.getElementById('inventory_type_other');
        const hasKnownInventoryType = Array.from(inventoryTypeInput?.options ?? []).some((option) => option.value === (product.inventory_type ?? ''));
        inventoryTypeInput.value = hasKnownInventoryType ? (product.inventory_type ?? '') : 'Other';
        if (inventoryTypeOtherInput) {
            inventoryTypeOtherInput.value = hasKnownInventoryType ? '' : (product.inventory_type ?? '');
        }
        document.getElementById('stock_qty').value = product.stock_qty ?? '';
        unitInput.value = product.unit ?? '';

        const selectedAreas = Array.isArray(product.product_area) ? product.product_area : [];
        productAreaCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectedAreas.includes(checkbox.value);
        });
        if (!productAreaCheckboxes.some((checkbox) => checkbox.checked)) {
            const noneCheckbox = productAreaCheckboxes.find((checkbox) => checkbox.value === 'None');
            if (noneCheckbox) {
                noneCheckbox.checked = true;
            }
        }
        document.getElementById('product_area_other').value = product.product_area_other ?? '';
        if (selectedLinkedServiceIdsElement) {
            selectedLinkedServiceIdsElement.dataset.selectedServiceIds = JSON.stringify(product.linked_service_ids ?? []);
        }

        createProductForm?.querySelectorAll('[name^="custom_fields["]').forEach((input) => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        const customFieldValues = product.custom_field_values ?? {};
        Object.entries(customFieldValues).forEach(([key, value]) => {
            const input = createProductForm?.querySelector(`[name="custom_fields[${key}]"]`);
            if (!input) return;
            if (input.type === 'checkbox') {
                input.checked = value === '1' || value === 1 || value === true;
            } else {
                input.value = value ?? '';
            }
        });

        syncInventoryFields();
        syncCustomSelectField(productTypeInput, productTypeOtherWrap);
        syncCustomSelectField(categoryInput, categoryOtherWrap);
        syncProductAreaSelection();
        renderLinkedServiceOptions();
        renderRequirementPreviews();
    };

    const buildRequirementPreview = (value) => {
        const items = String(value || '')
            .split(/\r?\n/)
            .map((item) => item.trim().replace(/^[^A-Za-z0-9]+/u, ''))
            .filter((item) => item !== '');

        if (items.length === 0) {
            return '';
        }

        return `
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Bullet Preview</p>
            <ul class="mt-2 space-y-1 text-sm text-gray-700">
                ${items.map((item) => `<li class="flex items-start gap-2"><span class="mt-[2px] text-blue-600">&bull;</span><span>${item}</span></li>`).join('')}
            </ul>
        `;
    };

    const renderRequirementPreviews = () => {
        productRequirementsInputs.forEach((input) => {
            const preview = input.parentElement?.querySelector('.product-requirements-preview');
            if (!preview) {
                return;
            }

            const html = buildRequirementPreview(input.value);
            preview.innerHTML = html;
            preview.classList.toggle('hidden', html === '');
        });
    };

    const closeProductOwnerDropdown = () => {
        productOwnerDropdownMenu?.classList.add('hidden');
    };

    const closeCreateModal = () => {
        if (!createProductModal || !createProductPanel) {
            return;
        }

        closeProductOwnerDropdown();
        stopProductCreatedAtClock();
        createProductModalOverlay?.classList.add('opacity-0');
        createProductPanel.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');

        window.setTimeout(() => {
            createProductModal.classList.add('hidden');
            createProductModal.setAttribute('aria-hidden', 'true');
        }, 300);
    };

    const buildPicklistOptionRow = (value = '') => {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
            <input name="options[]" value="${value}" placeholder="Option value" class="h-10 flex-1 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            <button type="button" class="remove-picklist-option h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50">
                <i class="fas fa-minus text-xs"></i>
            </button>
        `;
        return row;
    };

    const ensurePicklistOptionRows = () => {
        if (!picklistOptionsContainer) {
            return;
        }
        if (picklistOptionsContainer.querySelectorAll('input[name="options[]"]').length === 0) {
            picklistOptionsContainer.appendChild(buildPicklistOptionRow(''));
        }
    };

    const applyCreateFieldTypeUI = (type, label) => {
        if (!createFieldTypeInput || !createFieldTypeLabel) {
            return;
        }

        createFieldTypeInput.value = type;
        createFieldTypeLabel.textContent = label;

        if (picklistOptionsSection) {
            picklistOptionsSection.classList.toggle('hidden', type !== 'picklist');
        }
        if (lookupSection) {
            lookupSection.classList.toggle('hidden', type !== 'lookup');
            lookupSection.classList.toggle('grid', type === 'lookup');
        }
        if (defaultValueSection) {
            defaultValueSection.classList.toggle('hidden', type === 'lookup');
            defaultValueSection.classList.toggle('grid', type !== 'lookup');
        }
        if (defaultValueInput) {
            defaultValueInput.placeholder = type === 'date' ? 'YYYY-MM-DD' : 'Optional default value';
        }

        if (type === 'picklist') {
            ensurePicklistOptionRows();
        }
    };

    const openCreateFieldModalFn = (type, label) => {
        applyCreateFieldTypeUI(type, label);
        createFieldDropdownMenu?.classList.add('hidden');
        createFieldDropdownOpen = false;
        createFieldModal?.classList.remove('hidden');
        createFieldModal?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            createFieldModalOverlay?.classList.remove('opacity-0');
            createFieldPanel?.classList.remove('translate-x-full');
        });
    };

    const closeCreateFieldModalFn = () => {
        createFieldModalOverlay?.classList.add('opacity-0');
        createFieldPanel?.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        window.setTimeout(() => {
            createFieldModal?.classList.add('hidden');
            createFieldModal?.setAttribute('aria-hidden', 'true');
        }, 300);
    };

    const positionCreateFieldDropdown = () => {
        if (!openCreateFieldDropdown || !createFieldDropdownMenu) {
            return;
        }

        const rect = openCreateFieldDropdown.getBoundingClientRect();
        const dropdownWidth = createFieldDropdownMenu.offsetWidth || 208;
        const viewportPadding = 12;

        let left = rect.right - dropdownWidth;
        if (left < viewportPadding) {
            left = viewportPadding;
        }
        if (left + dropdownWidth > window.innerWidth - viewportPadding) {
            left = window.innerWidth - dropdownWidth - viewportPadding;
        }

        let top = rect.bottom + 6;
        const dropdownHeight = createFieldDropdownMenu.offsetHeight || 260;
        if (top + dropdownHeight > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, rect.top - dropdownHeight - 6);
        }

        createFieldDropdownMenu.style.left = `${left}px`;
        createFieldDropdownMenu.style.top = `${top}px`;
    };

    const openCreateFieldDropdownFn = () => {
        if (!createFieldDropdownMenu) {
            return;
        }
        createFieldDropdownMenu.classList.remove('hidden');
        createFieldDropdownOpen = true;
        positionCreateFieldDropdown();
    };

    const closeCreateFieldDropdownFn = () => {
        if (!createFieldDropdownMenu) {
            return;
        }
        createFieldDropdownMenu.classList.add('hidden');
        createFieldDropdownOpen = false;
    };

    const getColumnHeader = (columnKey) => {
        if (!tableHead) {
            return null;
        }
        return tableHead.querySelector(`th[data-column-key="${columnKey}"]`);
    };

    const getColumnCells = (columnKey) => {
        if (!tableBody) {
            return [];
        }
        return Array.from(tableBody.querySelectorAll(`td[data-column-key="${columnKey}"]`));
    };

    const cellValue = (cell) => {
        if (!cell) {
            return '';
        }
        const select = cell.querySelector('select');
        if (select) {
            return (select.value || '').trim().toLowerCase();
        }
        return (cell.textContent || '').trim().toLowerCase();
    };

    const applyRowFilters = () => {
        if (!tableBody) {
            return;
        }
        const rows = Array.from(tableBody.querySelectorAll('.product-row'));
        rows.forEach((row) => {
            let visible = true;
            Object.entries(columnFilters).forEach(([columnKey, filterText]) => {
                if (filterText === '') {
                    return;
                }
                const cell = row.querySelector(`td[data-column-key="${columnKey}"]`);
                if (!cellValue(cell).includes(filterText)) {
                    visible = false;
                }
            });
            row.classList.toggle('hidden', !visible);
        });
    };

    const sortByColumn = (columnKey) => {
        if (!tableBody) {
            return;
        }
        const rows = Array.from(tableBody.querySelectorAll('.product-row'));
        if (rows.length === 0) {
            return;
        }

        const nextDirection = columnSortState[columnKey] === 'asc' ? 'desc' : 'asc';
        columnSortState[columnKey] = nextDirection;

        rows.sort((rowA, rowB) => {
            const valueA = cellValue(rowA.querySelector(`td[data-column-key="${columnKey}"]`));
            const valueB = cellValue(rowB.querySelector(`td[data-column-key="${columnKey}"]`));
            const numericA = Number(valueA.replace(/[^0-9.-]/g, ''));
            const numericB = Number(valueB.replace(/[^0-9.-]/g, ''));
            let comparison = 0;

            if (!Number.isNaN(numericA) && !Number.isNaN(numericB) && valueA !== '' && valueB !== '') {
                comparison = numericA - numericB;
            } else {
                comparison = valueA.localeCompare(valueB);
            }

            return nextDirection === 'asc' ? comparison : -comparison;
        });

        rows.forEach((row) => tableBody.appendChild(row));
    };

    const collapseColumn = (columnKey) => {
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        if (!header) {
            return;
        }
        const willHide = !header.classList.contains('hidden');
        header.classList.toggle('hidden', willHide);
        cells.forEach((cell) => cell.classList.toggle('hidden', willHide));
    };

    const moveColumnLeft = (columnKey) => {
        if (!tableHead || !tableBody) {
            return;
        }
        const header = getColumnHeader(columnKey);
        if (!header || !header.previousElementSibling || !header.previousElementSibling.hasAttribute('data-column-key')) {
            return;
        }
        const previousHeader = header.previousElementSibling;
        const previousKey = previousHeader.getAttribute('data-column-key');
        if (!previousKey) {
            return;
        }

        tableHead.querySelector('tr')?.insertBefore(header, previousHeader);
        Array.from(tableBody.querySelectorAll('.product-row')).forEach((row) => {
            const cell = row.querySelector(`td[data-column-key="${columnKey}"]`);
            const previousCell = row.querySelector(`td[data-column-key="${previousKey}"]`);
            if (cell && previousCell) {
                row.insertBefore(cell, previousCell);
            }
        });
    };

    const autoFitColumn = (columnKey) => {
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        if (!header) {
            return;
        }
        header.style.width = '1%';
        header.classList.add('whitespace-nowrap');
        cells.forEach((cell) => {
            cell.classList.add('whitespace-nowrap');
        });
    };

    const removeCustomColumn = (columnKey) => {
        if (!activeFieldIsCustom) {
            return;
        }
        const header = getColumnHeader(columnKey);
        const cells = getColumnCells(columnKey);
        header?.remove();
        cells.forEach((cell) => cell.remove());
    };

    const positionFieldActionsMenu = (trigger) => {
        if (!fieldActionsMenu || !trigger) {
            return;
        }
        const rect = trigger.getBoundingClientRect();
        const menuWidth = fieldActionsMenu.offsetWidth || 224;
        const viewportPadding = 12;
        let left = rect.right - menuWidth;
        if (left < viewportPadding) {
            left = viewportPadding;
        }
        if (left + menuWidth > window.innerWidth - viewportPadding) {
            left = window.innerWidth - menuWidth - viewportPadding;
        }
        let top = rect.bottom + 6;
        const menuHeight = fieldActionsMenu.offsetHeight || 300;
        if (top + menuHeight > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, rect.top - menuHeight - 6);
        }
        fieldActionsMenu.style.left = `${left}px`;
        fieldActionsMenu.style.top = `${top}px`;
    };

    const closeFieldActionsMenu = () => {
        if (!fieldActionsMenu) {
            return;
        }
        fieldActionsMenu.classList.add('hidden');
        fieldActionsMenuOpen = false;
        activeFieldColumnKey = null;
        activeFieldIsCustom = false;
    };

    const openFieldActionsMenu = (trigger, columnKey, isCustom) => {
        if (!fieldActionsMenu) {
            return;
        }
        activeFieldColumnKey = columnKey;
        activeFieldIsCustom = isCustom;
        const removeBtn = fieldActionsMenu.querySelector('[data-action="remove-column"]');
        if (removeBtn) {
            removeBtn.classList.toggle('opacity-50', !isCustom);
            removeBtn.classList.toggle('cursor-not-allowed', !isCustom);
            removeBtn.classList.toggle('pointer-events-none', !isCustom);
            removeBtn.setAttribute('aria-disabled', !isCustom ? 'true' : 'false');
        }
        fieldActionsMenu.classList.remove('hidden');
        fieldActionsMenuOpen = true;
        positionFieldActionsMenu(trigger);
    };

    openCreateModalButton?.addEventListener('click', function () {
        resetCreateProductForm();
        openCreateModal();
    });
    closeCreateModalButton?.addEventListener('click', closeCreateModal);
    cancelCreateModalButton?.addEventListener('click', closeCreateModal);

    createProductModalOverlay?.addEventListener('click', closeCreateModal);

    productOwnerDropdownTrigger?.addEventListener('click', function () {
        productOwnerDropdownMenu?.classList.toggle('hidden');
        if (productOwnerDropdownMenu && !productOwnerDropdownMenu.classList.contains('hidden')) {
            productOwnerSearch?.focus();
        }
    });

    productOwnerSearch?.addEventListener('input', function () {
        const keyword = productOwnerSearch.value.toLowerCase().trim();
        productOwnerOptions.forEach((option) => {
            const name = (option.dataset.ownerName || '').toLowerCase();
            const email = (option.dataset.ownerEmail || '').toLowerCase();
            option.classList.toggle('hidden', keyword !== '' && !name.includes(keyword) && !email.includes(keyword));
        });
    });

    productOwnerOptions.forEach((option) => {
        option.addEventListener('click', function () {
            if (productOwnerIdInput) {
                productOwnerIdInput.value = option.dataset.ownerId || '';
            }
            if (productOwnerSelectedLabel) {
                productOwnerSelectedLabel.textContent = `Owner: ${option.dataset.ownerName || ''}`;
            }
            closeProductOwnerDropdown();
        });
    });

    inventoryTypeInput?.addEventListener('change', syncInventoryFields);
    productTypeInput?.addEventListener('change', () => syncCustomSelectField(productTypeInput, productTypeOtherWrap));
    categoryInput?.addEventListener('change', () => syncCustomSelectField(categoryInput, categoryOtherWrap));
    productAreaCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', () => syncProductAreaSelection(checkbox)));
    productRequirementsInputs.forEach((input) => {
        input.addEventListener('input', renderRequirementPreviews);
    });

    document.addEventListener('click', function (event) {
        if (productOwnerDropdownMenu && !productOwnerDropdownMenu.classList.contains('hidden')) {
            const clickedProductOwnerTrigger = productOwnerDropdownTrigger ? productOwnerDropdownTrigger.contains(event.target) : false;
            const clickedProductOwnerSearch = productOwnerSearch ? productOwnerSearch.contains(event.target) : false;
            if (!productOwnerDropdownMenu.contains(event.target) && !clickedProductOwnerTrigger && !clickedProductOwnerSearch) {
                closeProductOwnerDropdown();
            }
        }

        if (createFieldDropdownMenu && createFieldDropdownOpen) {
            const clickedFieldTrigger = openCreateFieldDropdown ? openCreateFieldDropdown.contains(event.target) : false;
            if (!createFieldDropdownMenu.contains(event.target) && !clickedFieldTrigger) {
                closeCreateFieldDropdownFn();
            }
        }

        if (fieldActionsMenu && fieldActionsMenuOpen) {
            const clickedHeaderTrigger = headerActionTriggers.some((trigger) => trigger.contains(event.target));
            if (!fieldActionsMenu.contains(event.target) && !clickedHeaderTrigger) {
                closeFieldActionsMenu();
            }
        }

    });

    openCreateFieldDropdown?.addEventListener('click', function () {
        if (createFieldDropdownOpen) {
            closeCreateFieldDropdownFn();
            return;
        }
        closeFieldActionsMenu();
        openCreateFieldDropdownFn();
    });

    headerActionTriggers.forEach((trigger) => {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            const columnKey = trigger.dataset.columnKey || '';
            const columnType = trigger.dataset.columnType || 'base';
            const isCustom = columnType === 'custom';

            if (fieldActionsMenuOpen && activeFieldColumnKey === columnKey) {
                closeFieldActionsMenu();
                return;
            }

            closeCreateFieldDropdownFn();
            openFieldActionsMenu(trigger, columnKey, isCustom);
        });
    });

    fieldActionButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (!activeFieldColumnKey) {
                closeFieldActionsMenu();
                return;
            }

            const action = button.dataset.action || '';

            if (action === 'sort') {
                sortByColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'filter') {
                const currentFilter = columnFilters[activeFieldColumnKey] || '';
                const input = window.prompt('Filter value', currentFilter);
                if (input !== null) {
                    columnFilters[activeFieldColumnKey] = input.trim().toLowerCase();
                    applyRowFilters();
                }
                closeFieldActionsMenu();
                return;
            }

            if (action === 'edit-field') {
                window.alert('Edit Field is not available yet.');
                closeFieldActionsMenu();
                return;
            }

            if (action === 'add-column') {
                closeFieldActionsMenu();
                openCreateFieldDropdownFn();
                return;
            }

            if (action === 'collapse') {
                collapseColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'move-left') {
                moveColumnLeft(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'autofit') {
                autoFitColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
                return;
            }

            if (action === 'remove-column') {
                if (!activeFieldIsCustom) {
                    return;
                }
                removeCustomColumn(activeFieldColumnKey);
                closeFieldActionsMenu();
            }
        });
    });

    fieldTypeButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const type = button.dataset.fieldType || 'picklist';
            const label = button.dataset.fieldLabel || 'Picklist';
            openCreateFieldModalFn(type, label);
        });
    });

    closeCreateFieldModal?.addEventListener('click', closeCreateFieldModalFn);
    cancelCreateFieldModal?.addEventListener('click', closeCreateFieldModalFn);

    createFieldModalOverlay?.addEventListener('click', closeCreateFieldModalFn);

    window.addEventListener('resize', function () {
        if (createFieldDropdownOpen) {
            positionCreateFieldDropdown();
        }
        if (fieldActionsMenuOpen) {
            const activeTrigger = headerActionTriggers.find((trigger) => trigger.dataset.columnKey === activeFieldColumnKey) || null;
            positionFieldActionsMenu(activeTrigger);
        }
    });

    document.addEventListener('scroll', function () {
        if (createFieldDropdownOpen) {
            positionCreateFieldDropdown();
        }
        if (fieldActionsMenuOpen) {
            const activeTrigger = headerActionTriggers.find((trigger) => trigger.dataset.columnKey === activeFieldColumnKey) || null;
            positionFieldActionsMenu(activeTrigger);
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeChangeOwnerModalFn();
            closeCreateFieldModalFn();
            closeProductOwnerDropdown();
            closeCreateFieldDropdownFn();
            closeFieldActionsMenu();
        }
    });

    addPicklistOption?.addEventListener('click', function () {
        picklistOptionsContainer?.appendChild(buildPicklistOptionRow(''));
    });

    picklistOptionsContainer?.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-picklist-option');
        if (!button) {
            return;
        }
        const row = button.closest('.flex');
        if (row) {
            row.remove();
        }
        ensurePicklistOptionRows();
    });

    tableRows.forEach((row) => {
        row.classList.add('cursor-pointer');
        row.addEventListener('click', function (event) {
            const clickedInteractive = event.target.closest('a, button, input, select, textarea, label');
            if (clickedInteractive) {
                return;
            }
            const url = row.dataset.productUrl;
            if (url) {
                window.location.href = url;
            }
        });
    });

    productEditButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const product = JSON.parse(button.dataset.productEdit || '{}');
            resetCreateProductForm();
            fillProductForm(product);
            openCreateModal();
        });
    });

    syncInventoryFields();
    syncCustomSelectField(productTypeInput, productTypeOtherWrap);
    syncCustomSelectField(categoryInput, categoryOtherWrap);
    syncProductAreaSelection();
    renderLinkedServiceOptions();
    renderRequirementPreviews();

    const initialFieldType = createFieldTypeInput ? createFieldTypeInput.value : 'picklist';
    const initialTypeButton = fieldTypeButtons.find((button) => (button.dataset.fieldType || '') === initialFieldType);
    applyCreateFieldTypeUI(initialFieldType, initialTypeButton?.dataset.fieldLabel || 'Picklist');

    <?php if($errors->any() && (old('product_name') !== null || old('sku') !== null || old('owner_id') !== null || $errors->has('owner_id'))): ?>
        openCreateModal();
    <?php endif; ?>

    <?php if(old('field_type')): ?>
        openCreateFieldModalFn('<?php echo e(old('field_type')); ?>', '<?php echo e($fieldTypes->firstWhere('value', old('field_type'))['label'] ?? 'Picklist'); ?>');
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/products/index.blade.php ENDPATH**/ ?>