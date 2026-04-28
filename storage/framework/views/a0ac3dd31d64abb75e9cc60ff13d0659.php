<?php $__env->startSection('content'); ?>
<div class="w-full h-full px-6 py-5">

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">
        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Role Permissions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage access rights per role</p>
        </div>

        <div class="p-5 space-y-5">
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isProtected = $permission->role === 'SuperAdmin';
                ?>

                <form action="<?php echo e(route('admin.role-permissions.update', $permission->id)); ?>" method="POST" class="border border-gray-200 rounded-xl p-5">
                    <?php echo csrf_field(); ?>

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <?php echo e($permission->role); ?>


                                <?php if($isProtected): ?>
                                    <span class="px-2 py-1 text-[10px] rounded-full bg-gray-100 text-gray-600 font-medium">
                                        Protected
                                    </span>
                                <?php endif; ?>
                            </h2>

                            <?php if($isProtected): ?>
                                <p class="text-xs text-gray-500 mt-1">
                                    SuperAdmin has full access and cannot be modified.
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if($isProtected): ?>
                            <button
                                type="button"
                                disabled
                                class="px-4 py-2 text-sm font-medium bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed"
                            >
                                Protected
                            </button>
                        <?php else: ?>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Save Changes
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 text-sm">
                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="manage_users" <?php echo e($permission->manage_users ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Manage Users</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_admin_dashboard" <?php echo e($permission->access_admin_dashboard ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Admin Dashboard</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="approve_townhall" <?php echo e($permission->approve_townhall ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Approve Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="create_townhall" <?php echo e($permission->create_townhall ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Create Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="create_corporate" <?php echo e($permission->create_corporate ? 'checked' : ''); ?><?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Create Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="approve_corporate" <?php echo e($permission->approve_corporate ? 'checked' : ''); ?><?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Approve Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_townhall" <?php echo e($permission->access_townhall ? 'checked' : ''); ?><?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_corporate" <?php echo e($permission->access_corporate ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_activities" <?php echo e($permission->access_activities ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Activities</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_contacts" <?php echo e($permission->access_contacts ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Contacts</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="access_company" <?php echo e($permission->access_company ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Access Company</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 <?php echo e($isProtected ? 'bg-gray-50' : ''); ?>">
                            <input type="checkbox" name="approve_policies" <?php echo e($permission->approve_policies ? 'checked' : ''); ?> <?php echo e($isProtected ? 'disabled' : ''); ?>>
                            <span>Approve Policies</span>
                        </label>
                    </div>
                </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\admin\role-permissions.blade.php ENDPATH**/ ?>