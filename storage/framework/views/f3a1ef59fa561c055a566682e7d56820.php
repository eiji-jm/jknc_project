<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>John Kelly & Company | CRM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: rgb(209 213 219);
        }

        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: rgb(209 213 219);
            min-height: 260px;
            font-size: 14px;
        }

        .ql-editor {
            min-height: 260px;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="bg-gray-50 font-sans">
    <?php
        $user = Auth::user();
        $canSeeCrmModules = !$user->isClient();

        $canSeeAdminIcon =
            $user->hasPermission('access_admin_dashboard') ||
            $user->hasPermission('approve_townhall') ||
            $user->hasPermission('manage_users');

        $adminLandingRoute = null;

        if ($user->hasPermission('manage_users')) {
            $adminLandingRoute = route('admin.users');
        } elseif ($user->hasPermission('access_admin_dashboard') || $user->hasPermission('approve_townhall')) {
            $adminLandingRoute = route('admin.dashboard');
        }
    ?>

    <!-- HEADER -->
    <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="h-full px-4 flex items-center justify-between">

            <div class="flex items-center gap-3 w-[260px]">
                <img src="/images/imaglogo.png" class="h-10 w-auto" alt="Logo">
            </div>

            <div class="flex-1 flex justify-center px-6">
                <div class="relative w-full max-w-xl">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        placeholder="Search"
                        class="w-full bg-gray-100 focus:bg-white border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-full pl-11 pr-4 py-2 text-sm outline-none transition"
                    >
                </div>
            </div>

            <div class="w-[260px] flex justify-end">
                <div class="flex items-center gap-4">

                    <button class="relative h-9 w-9 rounded-full hover:bg-gray-100 text-gray-500 flex items-center justify-center transition">
                        <i class="far fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div x-data="{ open:false }" class="relative">
                        <button
                            @click="open=!open"
                            class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold hover:ring-2 hover:ring-gray-300 transition"
                        >
                            <?php echo e(strtoupper(substr(Auth::user()->name,0,1))); ?>

                        </button>

                        <div
                            x-show="open"
                            @click.outside="open=false"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                            style="display:none;"
                        >
                            <div class="px-4 py-3 border-b text-sm">
                                <p class="font-semibold text-gray-800"><?php echo e(Auth::user()->name); ?></p>
                                <p class="text-gray-400 text-xs"><?php echo e(Auth::user()->role); ?></p>
                            </div>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button
                                    type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition"
                                >
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </header>

    <div class="flex h-[calc(100vh-4rem)]">

        <!-- MINI SIDEBAR -->
        <aside class="w-16 bg-white border-r border-gray-200 flex flex-col items-center py-3 gap-2">

            <?php if($canSeeAdminIcon && $adminLandingRoute): ?>
                <a href="<?php echo e($adminLandingRoute); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-user-shield text-base"></i>
                    <span>Admin</span>
                </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasPermission('access_townhall')): ?>
                <a href="<?php echo e(route('townhall')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                  <?php echo e(request()->routeIs('townhall*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-bullhorn text-base"></i>
                    <span>Town Hall</span>
                </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasPermission('access_corporate')): ?>
                <a href="<?php echo e(route('corporate')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('corporate') || request()->routeIs('corporate.formation') || request()->routeIs('corporate.sec_aoi') || request()->routeIs('corporate.bylaws') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-building text-base"></i>
                    <span>Corporate</span>
                </a>
            <?php endif; ?>

            <?php if(Auth::user()->hasPermission('access_activities')): ?>
                <a href="<?php echo e(route('activities')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('activities*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-list-check text-base"></i>
                    <span>Activities</span>
                </a>
            <?php endif; ?>

            <?php if($canSeeCrmModules && Auth::user()->hasPermission('access_contacts')): ?>
                <a href="<?php echo e(route('contacts.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-users text-base"></i>
                    <span>Contacts</span>
                </a>
            <?php endif; ?>

            <?php if($canSeeCrmModules && Auth::user()->hasPermission('access_company')): ?>
                <a href="<?php echo e(route('company.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-city text-base"></i>
                    <span>Company</span>
                </a>
            <?php endif; ?>

            <?php if($canSeeCrmModules): ?>
                <a href="<?php echo e(route('deals.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('deals*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-handshake text-base"></i>
                    <span>Deals</span>
                </a>

                <a href="<?php echo e(route('services.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('services*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-briefcase text-base"></i>
                    <span>Services</span>
                </a>

                <a href="<?php echo e(route('project.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('project*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-diagram-project text-base"></i>
                    <span>Project</span>
                </a>

                <a href="<?php echo e(route('regular.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('regular*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-arrows-rotate text-base"></i>
                    <span>Regular</span>
                </a>

                <a href="<?php echo e(route('products.index')); ?>"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   <?php echo e(request()->routeIs('products*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    <i class="fas fa-box-open text-base"></i>
                    <span>Product</span>
                </a>
            <?php endif; ?>

        </aside>

        <!-- SECOND SIDEBAR -->
        <?php if(request()->routeIs('townhall*')): ?>
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Town Hall</p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        <a href="<?php echo e(route('townhall')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->routeIs('townhall*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Communications
                        </a>

                        <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                            <a href="<?php echo e(route('townhall.department')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('townhall.department') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Department
                            </a>

                            <a href="<?php echo e(route('townhall.attachments')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('townhall.attachments') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Attachments
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        <?php elseif(request()->routeIs('admin.*') && $canSeeAdminIcon): ?>
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Admin Panel
                    </p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">

                        <?php if(Auth::user()->hasPermission('manage_users')): ?>
                            <a href="<?php echo e(route('admin.users')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Users
                            </a>
                        <?php endif; ?>

                        <?php if(Auth::user()->hasPermission('manage_users')): ?>
                            <a href="<?php echo e(route('admin.role-permissions')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('admin.role-permissions') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Role Permissions
                            </a>
                        <?php endif; ?>

                        <?php if(Auth::user()->hasPermission('manage_users')): ?>
                            <a href="<?php echo e(route('admin.user-permissions')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('admin.user-permissions') || request()->routeIs('admin.user-permissions.edit') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                User Permissions
                            </a>
                        <?php endif; ?>

                        <?php if(Auth::user()->hasPermission('access_admin_dashboard') || Auth::user()->hasPermission('approve_townhall')): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Town Hall
                            </a>
                        <?php endif; ?>

                        <?php if(Auth::user()->hasPermission('approve_corporate')): ?>
                            <a href="<?php echo e(route('admin.corporate.dashboard')); ?>"
                               class="block px-3 py-2 rounded-lg transition
                               <?php echo e(request()->routeIs('admin.corporate.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Corporate
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

            </aside>

        <?php elseif(
            $canSeeCrmModules
            && Auth::user()->hasPermission('access_company')
            && request()->routeIs('company.*')
            && ! request()->routeIs('company.index')
        ): ?>
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        <?php
                            $currentCompany = request()->route('company');
                            $currentCompanyId = is_object($currentCompany)
                                ? $currentCompany->id
                                : ($currentCompany ?: request()->segment(2));
                            $hasCompanyContext = filled($currentCompanyId);
                        ?>

                        <?php if(! $hasCompanyContext): ?>
                            <div class="px-3 py-2 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg">
                                Company context unavailable for sidebar links.
                            </div>
                        <?php endif; ?>

                        <?php if($hasCompanyContext): ?>
                            <div class="space-y-1">
                                <a href="<?php echo e(route('company.kyc', ['company' => $currentCompanyId, 'tab' => 'business-client-information'])); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.kyc') || request()->routeIs('company.bif.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    KYC
                                </a>

                                <a href="<?php echo e(route('company.history', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.history') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    History
                                </a>

                                <a href="<?php echo e(route('company.consultation-notes', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.consultation-notes*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Consultation Notes
                                </a>

                                <a href="<?php echo e(route('company.activities', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.activities*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Activities
                                </a>

                                <a href="<?php echo e(route('company.deals', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.deals*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Deals
                                </a>

                                <a href="<?php echo e(route('company.contacts', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.contacts*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Contacts
                                </a>

                                <a href="<?php echo e(route('company.projects', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.projects') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Projects
                                </a>

                                <a href="<?php echo e(route('company.regular', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.regular') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Regular
                                </a>

                                <a href="<?php echo e(route('company.products', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.products*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Products
                                </a>

                                <a href="<?php echo e(route('company.services.index', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.services.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Services
                                </a>

                                <a href="<?php echo e(route('company.lgu', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.lgu*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    LGU
                                </a>

                                <a href="<?php echo e(route('company.accounting', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.accounting*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Accounting
                                </a>

                                <a href="<?php echo e(route('company.banking', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.banking*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Banking
                                </a>

                                <a href="<?php echo e(route('company.operations', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.operations*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Operations
                                </a>

                                <a href="<?php echo e(route('company.correspondence', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.correspondence*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Correspondence
                                </a>

                                <a href="<?php echo e(route('company.bir-tax', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.bir-tax*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    BIR & Tax
                                </a>

                                <a href="<?php echo e(route('company.corporate-formation', $currentCompanyId)); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('company.corporate-formation*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Corporate Formation
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        <?php elseif(
            Auth::user()->hasPermission('access_corporate')
            && (
                request()->routeIs('corporate*')
                || request()->routeIs('stock-transfer-book*')
                || request()->routeIs('bir-tax*')
                || request()->routeIs('natgov*')
                || request()->routeIs('notices*')
                || request()->routeIs('minutes*')
                || request()->routeIs('resolutions*')
                || request()->routeIs('secretary-certificates*')
                || request()->routeIs('accounting')
                || request()->routeIs('banking')
                || request()->routeIs('legal')
                || request()->routeIs('operations')
                || request()->routeIs('correspondence')
            )
        ): ?>
            <aside x-data="{ scrollCorporateNav(amount) { this.$refs.corporateNav?.scrollBy({ top: amount, behavior: 'smooth' }); } }"
                   class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Corporate</p>
                </div>

                <div x-ref="corporateNav" class="flex-1 overflow-y-auto p-3 no-scrollbar">
                    <div class="space-y-1 text-sm">
                        <a href="<?php echo e(route('corporate')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->routeIs('corporate') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Company General Information
                        </a>

                        <a href="<?php echo e(route('corporate.formation')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->routeIs('corporate.formation') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Corporate Formation
                        </a>

                        <div x-data="{ open: <?php echo e(request()->routeIs('stock-transfer-book*') ? 'true' : 'false'); ?> }" class="space-y-1">
                            <button type="button"
                                    @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition border
                                    <?php echo e(request()->routeIs('stock-transfer-book*') ? 'bg-blue-50 text-blue-700 border-blue-100 font-semibold' : 'border-transparent hover:bg-gray-100 text-gray-700'); ?>">
                                <span>Stock and Transfer Book</span>
                                <i class="fas fa-chevron-down text-[11px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-cloak x-show="open" x-transition class="pl-3 space-y-1">
                                <a href="<?php echo e(route('stock-transfer-book.index')); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('stock-transfer-book.index*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Index
                                </a>
                                <a href="<?php echo e(route('stock-transfer-book.journal')); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('stock-transfer-book.journal*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Journal
                                </a>
                                <a href="<?php echo e(route('stock-transfer-book.ledger')); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('stock-transfer-book.ledger*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Ledger
                                </a>
                                <a href="<?php echo e(route('stock-transfer-book.installment')); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('stock-transfer-book.installment*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Installment
                                </a>
                                <a href="<?php echo e(route('stock-transfer-book.certificates')); ?>"
                                   class="block px-3 py-2 rounded-lg transition
                                   <?php echo e(request()->routeIs('stock-transfer-book.certificates*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                    Certificates
                                </a>
                            </div>
                        </div>

                        <a href="<?php echo e(route('bir-tax')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->routeIs('bir-tax*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            BIR & Tax
                        </a>

                        <a href="<?php echo e(route('natgov')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->routeIs('natgov*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            NatGov
                        </a>

                        <a href="<?php echo e(route('corporate.lgu')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('corporate/lgu') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            LGU
                        </a>

                        <a href="<?php echo e(route('accounting')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('accounting') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Accounting
                        </a>

                        <a href="<?php echo e(route('banking')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('banking') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Banking
                        </a>

                        <a href="<?php echo e(route('legal')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('legal') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Legal
                        </a>

                        <a href="<?php echo e(route('operations')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('operations') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Operations
                        </a>

                        <a href="<?php echo e(route('correspondence')); ?>"
                           class="block px-3 py-2 rounded-lg transition
                           <?php echo e(request()->is('correspondence') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                            Correspondence
                        </a>
                    </div>
                </div>
            </aside>
        <?php elseif($canSeeCrmModules && (request()->routeIs('project*') || request()->routeIs('regular*'))): ?>
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <?php echo e(request()->routeIs('project*') ? 'Project' : 'Regular'); ?>

                    </p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        <?php if(request()->routeIs('project*')): ?>
                            <a href="<?php echo e(route('project.index')); ?>"
                               class="block px-3 py-2 rounded-lg transition <?php echo e(request()->routeIs('project.index') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Dashboard
                            </a>
                            <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-xs leading-5 text-gray-600">
                                One-time or fixed-scope engagements. Expected flow: Work Order, SOW, NTP, Execution, Reporting, Delivery, Completion.
                            </div>
                        <?php else: ?>
                            <a href="<?php echo e(route('regular.index')); ?>"
                               class="block px-3 py-2 rounded-lg transition <?php echo e(request()->routeIs('regular.index') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700'); ?>">
                                Dashboard
                            </a>
                            <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-xs leading-5 text-gray-600">
                                Recurring or retainer engagements. Expected flow: RSAT, NTP, Execution, Reporting, Delivery, Continuation.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        <?php endif; ?>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/layouts/app.blade.php ENDPATH**/ ?>