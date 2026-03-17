<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Kelly & Company | CRM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

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
    </style>
</head>

<body class="bg-gray-50 font-sans">

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
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open=false"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                            style="display:none;"
                        >
                            <div class="px-4 py-3 border-b text-sm">
                                <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-gray-400 text-xs">{{ Auth::user()->role }}</p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
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

            @if(Auth::user()->hasPermission('access_admin_dashboard'))
                <a href="{{ route('admin.dashboard') }}"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-user-shield text-base"></i>
                    <span>Admin</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_townhall'))
                <a href="{{ route('townhall') }}"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   {{ request()->routeIs('townhall') || request()->routeIs('townhall.show') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-bullhorn text-base"></i>
                    <span>Town Hall</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_corporate'))
                <a href="{{ route('corporate') }}"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
                   {{ request()->routeIs('corporate') || request()->routeIs('corporate.formation') || request()->routeIs('corporate.sec_aoi') || request()->routeIs('corporate.bylaws') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-building text-base"></i>
                    <span>Corporate</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_activities'))
                <a href="#"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-list-check text-base"></i>
                    <span>Activities</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_contacts'))
                <a href="#"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-users text-base"></i>
                    <span>Contacts</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_company'))
                <a href="#"
                   class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-city text-base"></i>
                    <span>Company</span>
                </a>
            @endif

        </aside>

        <!-- SECOND SIDEBAR -->
        @if(Auth::user()->hasPermission('access_townhall') && (request()->routeIs('townhall') || request()->routeIs('townhall.show')))
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Town Hall</p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        <a href="{{ route('townhall') }}"
                           class="block px-3 py-2 rounded-lg transition
                           {{ request()->routeIs('townhall') || request()->routeIs('townhall.show') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                            Communications
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            Status
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            Department
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            Recipient
                        </a>

                        <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Open
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Completed
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Overdue
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Attachments
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        @elseif(Auth::user()->hasPermission('access_admin_dashboard') && request()->routeIs('admin.*'))
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Admin Panel
                    </p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">

                        @if(Auth::user()->hasPermission('manage_users'))
                            <a href="{{ route('admin.users') }}"
                               class="block px-3 py-2 rounded-lg transition
                               {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                                Users
                            </a>

                            <a href="{{ route('admin.role-permissions') }}"
                               class="block px-3 py-2 rounded-lg transition
                               {{ request()->routeIs('admin.role-permissions') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                                Role Permissions
                            </a>

                            <a href="{{ route('admin.user-permissions') }}"
                               class="block px-3 py-2 rounded-lg transition
                               {{ request()->routeIs('admin.user-permissions') || request()->routeIs('admin.user-permissions.edit') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                                User Permissions
                            </a>
                        @endif

                        @if(Auth::user()->hasPermission('approve_townhall'))
                            <a href="{{ route('admin.dashboard') }}"
                               class="block px-3 py-2 rounded-lg transition
                               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                                Town Hall
                            </a>
                        @endif

                        @if(Auth::user()->hasPermission('approve_corporate'))
                            <a href="{{ route('admin.corporate.dashboard') }}"
                               class="block px-3 py-2 rounded-lg transition
                               {{ request()->routeIs('admin.corporate.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                                Corporate
                            </a>
                        @endif

                        @if(Auth::user()->hasPermission('access_activities'))
                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Activities
                            </a>
                        @endif

                        @if(Auth::user()->hasPermission('access_contacts'))
                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Contacts
                            </a>
                        @endif

                        @if(Auth::user()->hasPermission('access_company'))
                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Company
                            </a>
                        @endif

                    </div>
                </div>

            </aside>

        @elseif(Auth::user()->hasPermission('access_corporate'))
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Corporate</p>
                </div>

                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">

                        <a href="{{ route('corporate') }}"
                           class="block px-3 py-2 rounded-lg transition
                           {{ request()->routeIs('corporate') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                            Company General Information
                        </a>

                        <a href="{{ route('corporate.formation') }}"
                           class="block px-3 py-2 rounded-lg transition
                           {{ request()->routeIs('corporate.formation') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                            Corporate/Formation
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            BIR & Tax
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            NatGov
                        </a>

                        <a href="#"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                            LGU
                        </a>

                        <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Accounting
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Priority
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Status
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Tag
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Task Name
                            </a>

                            <a href="#"
                               class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                                Task Owner
                            </a>
                        </div>

                    </div>
                </div>
            </aside>
        @endif

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    @stack('scripts')
</body>
</html>