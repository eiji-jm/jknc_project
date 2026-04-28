<?php
    $createFieldActionRoute = $createFieldActionRoute ?? route('products.custom-fields.store');
    $lookupModules = $lookupModules ?? [
        ['value' => 'deals', 'label' => 'Deals'],
        ['value' => 'company', 'label' => 'Company'],
        ['value' => 'contacts', 'label' => 'Contacts'],
    ];
?>

<div id="createFieldModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createFieldModalOverlay" type="button" aria-label="Close create field panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createFieldPanel" class="pointer-events-auto flex h-full w-full max-w-[560px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Create Field</h2>
                <p class="text-sm text-gray-500">Field Type: <span id="createFieldTypeLabel" class="font-medium text-gray-700">Picklist</span></p>
            </div>
            <button id="closeCreateFieldModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form method="POST" action="<?php echo e($createFieldActionRoute); ?>" class="flex min-h-0 flex-1 flex-col">
            <?php echo csrf_field(); ?>
            <input id="createFieldTypeInput" type="hidden" name="field_type" value="<?php echo e(old('field_type', 'picklist')); ?>">
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">

            <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                <label for="field_name" class="text-right text-sm font-medium text-gray-700">Field Name</label>
                <input id="field_name" name="field_name" value="<?php echo e(old('field_name')); ?>" required class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div id="picklistOptionsSection" class="space-y-2">
                <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-start sm:gap-3">
                    <label class="pt-2 text-right text-sm font-medium text-gray-700">Options</label>
                    <div>
                        <div id="picklistOptionsContainer" class="space-y-2">
                            <?php
                                $oldOptions = old('options', ['']);
                                if (! is_array($oldOptions) || count($oldOptions) === 0) {
                                    $oldOptions = [''];
                                }
                            ?>
                            <?php $__currentLoopData = $oldOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center gap-2">
                                    <input name="options[]" value="<?php echo e($value); ?>" placeholder="Option value" class="h-10 flex-1 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <button type="button" class="remove-picklist-option h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button id="addPicklistOption" type="button" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">
                            + Add Option
                        </button>
                    </div>
                </div>
            </div>

            <div id="defaultValueSection" class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                <label for="default_value" class="text-right text-sm font-medium text-gray-700">Default Value</label>
                <input id="default_value" name="default_value" value="<?php echo e(old('default_value')); ?>" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div id="lookupSection" class="hidden gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                <label for="lookup_module" class="text-right text-sm font-medium text-gray-700">Lookup Module</label>
                <select id="lookup_module" name="lookup_module" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Select module</option>
                    <?php $__currentLoopData = $lookupModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($module['value']); ?>" <?php echo e(old('lookup_module') === $module['value'] ? 'selected' : ''); ?>>
                            <?php echo e($module['label']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="grid gap-2 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center sm:gap-3">
                <span></span>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="required" value="1" <?php echo e(old('required') ? 'checked' : ''); ?> class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Required
                </label>
            </div>

            <?php if($errors->has('field_name') || $errors->has('field_type') || $errors->has('default_value') || $errors->has('lookup_module') || $errors->has('options')): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                <button id="cancelCreateFieldModal" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
        </div>
    </div>
</div>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\products\partials\create-field-modal.blade.php ENDPATH**/ ?>