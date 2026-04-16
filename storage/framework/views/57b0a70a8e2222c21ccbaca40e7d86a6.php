<?php $__env->startSection('content'); ?>
<div class="w-full h-full px-6 py-5" x-data="{ showCreateUser: false }">
    <?php
        $authUser = auth()->user();
    ?>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div x-show="showCreateUser" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div
                x-show="showCreateUser"
                @click="showCreateUser = false"
                class="absolute inset-0 bg-black/40"
            ></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="showCreateUser"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-xl bg-white shadow-2xl h-full flex flex-col"
                >
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Create User</h2>
                        <button
                            type="button"
                            @click="showCreateUser = false"
                            class="text-gray-400 hover:text-gray-600 text-lg"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Full Name</label>
                            <input
                                type="text"
                                name="name"
                                value="<?php echo e(old('name')); ?>"
                                placeholder="Enter full name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo e(old('email')); ?>"
                                placeholder="Enter email"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Role</label>
                            <select
                                name="role"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="">Select role</option>
                                <option value="Admin" <?php echo e(old('role') == 'Admin' ? 'selected' : ''); ?>>Admin</option>
                                <option value="Employee" <?php echo e(old('role') == 'Employee' ? 'selected' : ''); ?>>Employee</option>
                                <option value="Client" <?php echo e(old('role') == 'Client' ? 'selected' : ''); ?>>Client</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Password</label>
                            <input
                                type="password"
                                name="password"
                                placeholder="Enter password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                placeholder="Confirm password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div class="pt-4 border-t border-gray-200 flex gap-3">
                            <button
                                type="button"
                                @click="showCreateUser = false"
                                class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                            >
                                Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Users</h1>
                <p class="text-sm text-gray-500 mt-1">Manage login credentials, permissions, and roles</p>
            </div>

            <button
                @click="showCreateUser = true"
                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                + Create User
            </button>
        </div>

        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Email</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Role</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Permissions</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Created At</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-t border-gray-200 hover:bg-gray-50 align-top">
                                <td class="px-4 py-3 border-r border-gray-200"><?php echo e($user->id); ?></td>
                                <td class="px-4 py-3 border-r border-gray-200"><?php echo e($user->name); ?></td>
                                <td class="px-4 py-3 border-r border-gray-200"><?php echo e($user->email); ?></td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php if($authUser->canManageRoles() && !$user->isSuperAdmin()): ?>
                                        <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST" class="space-y-2">
                                            <?php echo csrf_field(); ?>

                                            <select
                                                name="role"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                            >
                                                <option value="Admin" <?php echo e($user->role === 'Admin' ? 'selected' : ''); ?>>Admin</option>
                                                <option value="Employee" <?php echo e($user->role === 'Employee' ? 'selected' : ''); ?>>Employee</option>
                                                <option value="Client" <?php echo e($user->role === 'Client' ? 'selected' : ''); ?>>Client</option>
                                            </select>

                                            <?php if($authUser->isSuperAdmin()): ?>
                                                <div class="space-y-1 text-xs">
                                                    <label class="flex items-center gap-2">
                                                        <input
                                                            type="checkbox"
                                                            name="can_edit_user_roles"
                                                            value="1"
                                                            <?php echo e($user->can_edit_user_roles ? 'checked' : ''); ?>

                                                        >
                                                        <span>Can edit user roles</span>
                                                    </label>

                                                    <label class="flex items-center gap-2">
                                                        <input
                                                            type="checkbox"
                                                            name="can_delete_users"
                                                            value="1"
                                                            <?php echo e($user->can_delete_users ? 'checked' : ''); ?>

                                                        >
                                                        <span>Can delete users</span>
                                                    </label>
                                                </div>
                                            <?php endif; ?>

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition"
                                            >
                                                Update
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <?php
                                            $roleClasses = $user->role === 'Admin'
                                                ? 'bg-blue-50 text-blue-700'
                                                : ($user->role === 'Superadmin'
                                                    ? 'bg-purple-50 text-purple-700'
                                                    : 'bg-gray-100 text-gray-700');
                                        ?>

                                        <span class="px-2 py-1 text-xs rounded-full font-medium <?php echo e($roleClasses); ?>">
                                            <?php echo e($user->role); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200 text-xs">
                                    <div class="space-y-1">
                                        <div>
                                            <span class="font-medium text-gray-600">Edit Roles:</span>
                                            <span class="<?php echo e($user->can_edit_user_roles ? 'text-green-600' : 'text-gray-400'); ?>">
                                                <?php echo e($user->can_edit_user_roles ? 'Yes' : 'No'); ?>

                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-medium text-gray-600">Delete Users:</span>
                                            <span class="<?php echo e($user->can_delete_users ? 'text-green-600' : 'text-gray-400'); ?>">
                                                <?php echo e($user->can_delete_users ? 'Yes' : 'No'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    <?php echo e($user->created_at?->format('Y-m-d')); ?>

                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <?php if($authUser->canDeleteUsers() && $authUser->id !== $user->id && !$user->isSuperAdmin()): ?>
                                            <form
                                                action="<?php echo e(route('admin.users.destroy', $user->id)); ?>"
                                                method="POST"
                                                onsubmit="return confirm('Delete this user?')"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">
                                                <?php echo e($authUser->id === $user->id ? 'Current user' : 'No action'); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div>
                    Total Users <span class="text-gray-800 font-semibold"><?php echo e($users->total()); ?></span>
                </div>

                <div class="flex items-center gap-4">
                    <span><?php echo e($users->firstItem() ?? 0); ?> to <?php echo e($users->lastItem() ?? 0); ?></span>
                </div>
            </div>

            <div class="mt-4">
                <?php echo e($users->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/admin/users.blade.php ENDPATH**/ ?>