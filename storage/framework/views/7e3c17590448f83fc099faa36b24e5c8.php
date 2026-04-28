<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($cancelRoute); ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold"><?php echo e($title); ?></div>
        </div>

        <div class="p-6">
            <?php if($errors->any()): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e($action); ?>" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <?php if($method !== 'POST'): ?>
                    <?php echo method_field($method); ?>
                <?php endif; ?>

                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $fieldName = $field['name'];
                        $fieldType = $field['type'] ?? 'text';
                        $fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
                        $fieldRequired = $field['required'] ?? false;
                        $fieldStep = $field['step'] ?? null;
                        $value = old($fieldName, data_get($item, $fieldName));
                        $isFile = $fieldType === 'file';
                        $isTextarea = $fieldType === 'textarea';
                        $isSelect = $fieldType === 'select';
                        $fieldOptions = $field['options'] ?? [];
                        $isFullWidth = $isTextarea || $isFile;
                    ?>

                    <div class="<?php echo e($isFullWidth ? 'md:col-span-2' : ''); ?>">
                        <label class="text-xs text-gray-600">
                            <?php echo e($fieldLabel); ?>

                            <?php if($fieldRequired): ?>
                                <span class="text-red-500">*</span>
                            <?php endif; ?>
                        </label>

                        <?php if($isTextarea): ?>
                            <textarea name="<?php echo e($fieldName); ?>" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e($value); ?></textarea>
                        <?php elseif($isSelect): ?>
                            <select name="<?php echo e($fieldName); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" <?php if($fieldRequired): ?> required <?php endif; ?>>
                                <?php $__currentLoopData = $fieldOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>" <?php if($value === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif($isFile): ?>
                            <input type="file" name="<?php echo e($fieldName); ?>" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            <?php if($value): ?>
                                <div class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                                    <p class="text-xs text-gray-700">Existing file stored.</p>
                                    <label class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-red-700">
                                        <input type="checkbox" name="remove_<?php echo e($fieldName); ?>" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        Remove current file
                                    </label>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <input
                                type="<?php echo e($fieldType); ?>"
                                name="<?php echo e($fieldName); ?>"
                                value="<?php echo e($value); ?>"
                                <?php if($fieldStep): ?> step="<?php echo e($fieldStep); ?>" <?php endif; ?>
                                <?php if($fieldRequired): ?> required <?php endif; ?>
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            >
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="md:col-span-2 flex items-center gap-2 pt-2">
                    <a href="<?php echo e($cancelRoute); ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg">
                        Cancel
                    </a>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\common\form.blade.php ENDPATH**/ ?>