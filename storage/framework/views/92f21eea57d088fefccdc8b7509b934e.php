<?php $__env->startSection('content'); ?>
<div class="mt-4 w-full px-4 pb-8 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-md border border-gray-100 bg-white">
        <?php echo $__env->make('company.partials.company-header', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="min-h-[760px] bg-gray-50 p-4">
            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900">CONTACTS</h2>
                            <p class="mt-1 text-sm text-gray-500">View contact records from the Contacts module for <?php echo e($company->company_name); ?>.</p>
                        </div>

                        <a href="<?php echo e($contactsModuleCreateUrl); ?>" class="inline-flex h-9 items-center gap-2 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                            <span class="text-base leading-none">+</span>
                            <span>Add Contact</span>
                        </a>
                    </div>

                    <?php if(session('success')): ?>
                        <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <form method="GET" action="<?php echo e(route('company.contacts', $company->id)); ?>" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            <div class="relative w-full sm:w-[320px]">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                                <input
                                    type="text"
                                    name="search"
                                    value="<?php echo e($search); ?>"
                                    placeholder="Search contacts..."
                                    class="h-10 w-full rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>

                            <button class="h-10 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Search
                            </button>

                            <?php if($search !== ''): ?>
                                <a href="<?php echo e(route('company.contacts', $company->id)); ?>" class="inline-flex h-10 items-center justify-center rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Clear
                                </a>
                            <?php endif; ?>
                        </form>

                        <div class="text-sm text-gray-500">
                            <?php echo e($contacts->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('contact', $contacts->count())); ?> in Contacts
                            <?php if(($roleContacts->count() ?? 0) > 0): ?>
                                <span class="ml-2">• <?php echo e($roleContacts->count()); ?> role-based BIF <?php echo e(\Illuminate\Support\Str::plural('entry', $roleContacts->count())); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <?php if(($roleContacts->count() ?? 0) > 0): ?>
                        <div class="mb-6 overflow-hidden rounded-md border border-gray-200 bg-white">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Required Company Role Contacts</h3>
                                <p class="mt-1 text-sm text-gray-500">Authorized Signatories, UBOs with at least 20% holdings, and the Authorized Contact Person should exist in the Contacts module.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">Role</th>
                                            <th class="px-4 py-3 text-left font-medium">Full Name</th>
                                            <th class="px-4 py-3 text-left font-medium">Position</th>
                                            <th class="px-4 py-3 text-left font-medium">Email</th>
                                            <th class="px-4 py-3 text-left font-medium">Phone</th>
                                            <th class="px-4 py-3 text-left font-medium">Status</th>
                                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                        <?php $__currentLoopData = $roleContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleContact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3"><?php echo e($roleContact['role_label']); ?></td>
                                                <td class="px-4 py-3 font-medium text-gray-800"><?php echo e($roleContact['full_name'] ?: '-'); ?></td>
                                                <td class="px-4 py-3"><?php echo e($roleContact['position'] ?: '-'); ?></td>
                                                <td class="px-4 py-3"><?php echo e($roleContact['email'] ?: '-'); ?></td>
                                                <td class="px-4 py-3"><?php echo e($roleContact['phone'] ?: '-'); ?></td>
                                                <td class="px-4 py-3">
                                                    <?php if($roleContact['exists_in_contacts']): ?>
                                                        <span class="inline-flex rounded-full border border-green-200 bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Exists in Contacts</span>
                                                    <?php else: ?>
                                                        <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">Missing from Contacts</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <?php if($roleContact['exists_in_contacts'] && $roleContact['linked_contact']): ?>
                                                            <a href="<?php echo e(route('contacts.show', $roleContact['linked_contact'])); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                                View Contact
                                                            </a>
                                                        <?php elseif($roleContact['add_to_contacts_url']): ?>
                                                            <a href="<?php echo e($roleContact['add_to_contacts_url']); ?>" class="inline-flex h-8 items-center rounded-full bg-blue-600 px-3 text-xs font-medium text-white hover:bg-blue-700">
                                                                Add to Contacts
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="overflow-hidden rounded-md border border-gray-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium">Contact Name</th>
                                        <th class="px-4 py-3 text-left font-medium">Company</th>
                                        <th class="px-4 py-3 text-left font-medium">Email</th>
                                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                                        <th class="px-4 py-3 text-left font-medium">KYC Status</th>
                                        <th class="px-4 py-3 text-left font-medium">Owner</th>
                                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $fullName = trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? ''));
                                            $initials = strtoupper(\Illuminate\Support\Str::substr($contact->first_name ?? '', 0, 1) . \Illuminate\Support\Str::substr($contact->last_name ?? '', 0, 1));
                                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-gray-100 text-[11px] font-semibold text-gray-600">
                                                        <?php echo e($initials !== '' ? $initials : '--'); ?>

                                                    </span>
                                                    <a href="<?php echo e(route('contacts.show', $contact)); ?>" class="font-medium text-gray-800 hover:text-blue-700">
                                                        <?php echo e($fullName !== '' ? $fullName : 'Unnamed Contact'); ?>

                                                    </a>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3"><?php echo e($contact->company_name ?: '-'); ?></td>
                                            <td class="px-4 py-3"><?php echo e($contact->email ?: '-'); ?></td>
                                            <td class="px-4 py-3"><?php echo e($contact->phone ?: '-'); ?></td>
                                            <td class="px-4 py-3"><?php echo e($contact->kyc_status ?: 'Not Submitted'); ?></td>
                                            <td class="px-4 py-3"><?php echo e($contact->owner_name ?: '-'); ?></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="<?php echo e(route('contacts.show', $contact)); ?>" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                        View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-12">
                                                <div class="flex flex-col items-center justify-center text-center">
                                                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                                        <i class="fas fa-address-book"></i>
                                                    </div>
                                                    <h3 class="mt-4 text-base font-semibold text-gray-900">No linked contacts found for this company yet.</h3>
                                                    <p class="mt-1 max-w-md text-sm text-gray-500">Create the first contact in the Contacts module and assign <?php echo e($company->company_name); ?> as the company name.</p>
                                                    <a href="<?php echo e($contactsModuleCreateUrl); ?>" class="mt-4 inline-flex h-9 items-center gap-2 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                                        <span class="text-base leading-none">+</span>
                                                        <span>Add Contact</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/company/contacts.blade.php ENDPATH**/ ?>