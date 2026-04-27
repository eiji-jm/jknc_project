<div id="changeOwnerModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="changeOwnerModalOverlay" type="button" aria-label="Close change owner panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="changeOwnerPanel" class="pointer-events-auto flex h-full w-full max-w-[560px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5 sm:px-8">
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-semibold leading-none text-blue-900">Change Owner</h2>
                <span class="text-gray-400">&middot;</span>
                <p id="changeOwnerModalCount" class="text-sm font-normal text-gray-500">0 Product Selected</p>
            </div>
            <button id="closeChangeOwnerModalX" type="button" class="text-xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
        </div>

        <form id="changeOwnerForm" method="POST" action="<?php echo e(route('products.change-owner')); ?>" class="flex min-h-0 flex-1 flex-col">
            <?php echo csrf_field(); ?>
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                <div id="selectedProductsFields"></div>
                <input type="hidden" id="selectedOwnerId" name="owner_id" value="">

            <div class="grid gap-3 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-start sm:gap-4">
                <label for="changeOwnerSearchInput" class="pt-2 text-sm font-medium text-gray-700">Change Owner</label>
                <div class="relative">
                    <div class="relative">
                        <input
                            id="changeOwnerSearchInput"
                            type="text"
                            autocomplete="off"
                            placeholder="Select"
                            class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 pr-10 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        <button id="toggleOwnerDropdown" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>

                    <div id="ownerDropdownMenu" class="absolute left-0 right-0 z-20 mt-1 hidden max-h-56 overflow-y-auto rounded-md border border-blue-200 bg-white shadow-lg">
                        <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $initials = strtoupper(collect(explode(' ', $owner['name']))
                                    ->filter()
                                    ->map(fn ($part) => mb_substr($part, 0, 1))
                                    ->take(2)
                                    ->implode(''));
                            ?>
                            <button
                                type="button"
                                class="owner-option flex w-full items-center gap-3 border-b border-gray-100 px-3 py-2 text-left hover:bg-blue-50"
                                data-owner-id="<?php echo e($owner['id']); ?>"
                                data-owner-name="<?php echo e($owner['name']); ?>"
                                data-owner-email="<?php echo e($owner['email']); ?>"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-[11px] font-semibold text-blue-700">
                                    <?php echo e($initials); ?>

                                </span>
                                <span>
                                    <span class="block text-sm font-medium text-gray-800"><?php echo e($owner['name']); ?></span>
                                    <span class="block text-xs text-gray-500"><?php echo e($owner['email']); ?></span>
                                </span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

                <?php $__errorArgs = ['owner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        <?php echo e($message); ?>

                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                <button id="cancelChangeOwnerModal" type="button" class="h-9 min-w-[96px] rounded-full border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="saveChangeOwnerBtn" type="submit" disabled class="h-9 min-w-[96px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white disabled:cursor-not-allowed disabled:bg-blue-300">
                    Save
                </button>
            </div>
        </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/products/partials/change-owner-modal.blade.php ENDPATH**/ ?>