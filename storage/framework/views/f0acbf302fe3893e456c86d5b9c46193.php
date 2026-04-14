<div class="border-b border-gray-100 px-4 py-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a
                    href="<?php echo e(route('company.index')); ?>"
                    class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900"
                >
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Company</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900 truncate"><?php echo e($company->company_name); ?></span>
            </div>

            <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex min-w-0 flex-1 flex-wrap items-start gap-5">
                        <div class="h-16 w-16 shrink-0 rounded-lg border border-gray-200 bg-gray-100 text-gray-600 flex items-center justify-center text-sm font-bold leading-tight">
                            <?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($company->company_name, 0, 2))); ?>

                        </div>

                        <div class="min-w-[240px] flex-1">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900"><?php echo e($company->company_name); ?></h1>
                            <p class="mt-1 text-sm text-gray-500"><?php echo e($company->company_type ?: 'Corporation'); ?></p>

                            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
                                <?php if(! empty($company->address)): ?>
                                    <p><span class="font-medium text-gray-700">Address:</span> <?php echo e($company->address); ?></p>
                                <?php endif; ?>
                                <?php if(! empty($company->bif_no ?? null)): ?>
                                    <p><span class="font-medium text-gray-700">BIF Number:</span> <?php echo e($company->bif_no); ?></p>
                                <?php endif; ?>
                                <?php if(! empty($company->phone)): ?>
                                    <p><span class="font-medium text-gray-700">Phone:</span> <?php echo e($company->phone); ?></p>
                                <?php endif; ?>
                                <?php if(! empty($company->website)): ?>
                                    <p>
                                        <span class="font-medium text-gray-700">Website:</span>
                                        <a href="<?php echo e($company->website); ?>" class="text-blue-600 underline" target="_blank" rel="noreferrer"><?php echo e($company->website); ?></a>
                                    </p>
                                <?php endif; ?>
                                <?php if(! empty($companyKycStatus ?? null)): ?>
                                    <p class="flex items-center gap-2">
                                        <span class="font-medium text-gray-700">Status:</span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($companyKycStatusClass ?? 'bg-gray-100 text-gray-600 border border-gray-200'); ?>">
                                            <?php echo e($companyKycStatus); ?>

                                        </span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            id="openEditCompanyModal"
                            class="h-9 rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Edit Company
                        </button>
                        <div class="relative">
                            <button
                                type="button"
                                id="toggleCompanyHeaderMenu"
                                class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50"
                            >
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>

                            <div id="companyHeaderMenu" class="hidden absolute right-0 top-11 z-30 min-w-[220px] rounded-md border border-gray-200 bg-white py-1 shadow-sm">
                                <a href="<?php echo e(route('company.history', $company->id)); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    View Audit History
                                </a>
                                <form method="POST" action="<?php echo e(route('company.destroy', $company->id)); ?>" onsubmit="return confirm('Delete this company? This will remove it from the company list.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                        Delete Company
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'editCompanyModal','width' => 'sm:max-w-[640px] lg:max-w-[760px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'editCompanyModal','width' => 'sm:max-w-[640px] lg:max-w-[760px]']); ?>
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Edit Company</h2>
                <p class="mt-1 text-sm text-gray-500">Update the company profile shown across all Company pages.</p>
            </div>
            <button type="button" data-close-edit-company-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form id="editCompanyForm" method="POST" action="<?php echo e(route('company.update', $company->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="flex-1 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="edit_company_name" class="mb-1 block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
                        <input id="edit_company_name" name="company_name" type="text" value="<?php echo e(old('company_name', $company->company_name)); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="edit_company_type" class="mb-1 block text-sm font-medium text-gray-700">Company Type</label>
                        <input id="edit_company_type" name="company_type" type="text" value="<?php echo e(old('company_type', $company->company_type ?: 'Corporation')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['company_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="edit_company_email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input id="edit_company_email" name="email" type="email" value="<?php echo e(old('email', $company->email ?? '')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="edit_company_phone" class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                        <input id="edit_company_phone" name="phone" type="text" value="<?php echo e(old('phone', $company->phone ?? '')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="edit_company_website" class="mb-1 block text-sm font-medium text-gray-700">Website</label>
                        <input id="edit_company_website" name="website" type="text" value="<?php echo e(old('website', $company->website ?? '')); ?>" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="edit_company_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                        <textarea id="edit_company_address" name="address" rows="3" class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"><?php echo e(old('address', $company->address ?? '')); ?></textarea>
                        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="edit_company_description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="edit_company_description" name="description" rows="3" class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"><?php echo e(old('description', $company->description ?? '')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-edit-company-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $attributes = $__attributesOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $component = $__componentOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__componentOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editCompanyModal = document.getElementById('editCompanyModal');
        const openEditCompanyModal = document.getElementById('openEditCompanyModal');
        const closeEditCompanyButtons = document.querySelectorAll('[data-close-edit-company-modal]');
        const menuButton = document.getElementById('toggleCompanyHeaderMenu');
        const menu = document.getElementById('companyHeaderMenu');
        const shouldOpenEditModal = <?php echo json_encode($errors->has('company_name') || $errors->has('company_type') || $errors->has('email') || $errors->has('phone') || $errors->has('website') || $errors->has('address') || $errors->has('description'), 15, 512) ?>;

        const openModal = () => window.jkncSlideOver.open(editCompanyModal);
        const closeModal = () => window.jkncSlideOver.close(editCompanyModal);

        const closeMenu = () => {
            menu.classList.add('hidden');
        };

        openEditCompanyModal?.addEventListener('click', openModal);

        closeEditCompanyButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        editCompanyModal?.addEventListener('click', function (event) {
            if (event.target === editCompanyModal || event.target.hasAttribute('data-drawer-overlay')) {
                closeModal();
            }
        });

        menuButton?.addEventListener('click', function (event) {
            event.stopPropagation();
            menu.classList.toggle('hidden');
        });

        menu?.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', closeMenu);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
                closeMenu();
            }
        });

        if (shouldOpenEditModal) {
            openModal();
        }
    });
</script>
<?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/company/partials/company-header.blade.php ENDPATH**/ ?>