<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Kelly & Company | CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --navy: #102d79;
            --blue: #1d54e2;
            --fog: #f1f2f2;
        }

        body {
            font-family: "Space Grotesk", sans-serif;
            background-color: var(--fog);
            background-image:
                radial-gradient(1200px 300px at 10% -20%, rgba(29, 84, 226, 0.12), transparent 60%),
                radial-gradient(900px 260px at 90% 0%, rgba(16, 45, 121, 0.14), transparent 55%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.6), rgba(241, 242, 242, 0.9));
        }

        .brand-title {
            font-family: "Fraunces", serif;
            letter-spacing: 0.02em;
        }

        .soft-card {
            box-shadow: 0 14px 30px rgba(16, 45, 121, 0.08);
            border: 1px solid rgba(16, 45, 121, 0.08);
        }

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

        .document-frame {
            border: 1px solid #d8dee8;
            border-radius: 1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
            overflow: hidden;
        }

        .document-frame__toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid #d8dee8;
        }

        .document-frame__tools,
        .document-frame__actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.78rem;
        }

        .document-frame__chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.6rem;
            border: 1px solid #d8dee8;
            border-radius: 0.7rem;
            background: #fff;
        }

        .document-frame__body {
            padding: 0.9rem;
            background: #edf1f5;
        }

        .document-frame__embed {
            width: 100%;
            height: 780px;
            border: 1px solid #cbd5e1;
            border-radius: 0.85rem;
            background: #fff;
        }

        .document-frame__empty {
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #cbd5e1;
            border-radius: 0.85rem;
            background: rgba(255,255,255,0.9);
            color: #64748b;
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body class="overflow-x-hidden text-[#000000]">
    @php
        $isAdminRoute = request()->routeIs('admin.*');
        $isCorporateRoute = request()->routeIs('corporate*')
            || request()->routeIs('notices*')
            || request()->routeIs('minutes*')
            || request()->routeIs('resolutions*')
            || request()->routeIs('secretary-certificates*')
            || request()->routeIs('bir-tax*')
            || request()->routeIs('natgov*')
            || request()->routeIs('stock-transfer-book*')
            || request()->routeIs('contacts*');
    @endphp
    <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="h-full px-3 sm:px-4 flex items-center gap-3">
            <div class="flex min-w-0 flex-1 items-center gap-3 md:w-[260px] md:flex-none">
                <a href="{{ route('corporate') }}" class="inline-flex items-center" aria-label="Go to home">
                    <img src="/images/imaglogo.png" class="h-10 w-auto" alt="John Kelly & Company">
                </a>
            </div>

            <div class="hidden min-w-0 flex-1 justify-center px-4 md:flex md:px-6">
                <div class="relative w-full max-w-xl">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search" class="w-full bg-gray-100 focus:bg-white border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-full pl-11 pr-4 py-2 text-sm outline-none transition">
                </div>
            </div>

            <div class="flex items-center justify-end md:w-[260px]">
                <div class="flex items-center gap-4">
                    <button class="relative h-9 w-9 rounded-full hover:bg-gray-100 text-gray-500 flex items-center justify-center transition">
                        <i class="far fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div x-data="{ open:false }" class="relative">
                        <button @click="open=!open" class="h-9 w-9 rounded-full bg-[#f1f2f2] flex items-center justify-center text-[#102d79] font-semibold hover:ring-2 hover:ring-[#102d79]/20 transition">
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        </button>

                        <div x-show="open" @click.outside="open=false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl soft-card overflow-hidden" style="display:none;">
                            <div class="px-4 py-3 border-b border-[#102d79]/10 text-sm">
                                <p class="font-semibold text-[#102d79]">{{ Auth::user()->name }}</p>
                                <p class="text-[#102d79]/60 text-xs">{{ Auth::user()->role }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition">
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
        <aside class="w-16 shrink-0 bg-white border-r border-gray-200 flex flex-col items-center py-3 gap-2">
            @if(Auth::user()->hasPermission('access_admin_dashboard'))
                <a href="{{ route('admin.users') }}" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-user-shield text-base"></i>
                    <span>Admin</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('access_corporate') || $isCorporateRoute)
                <a href="{{ route('corporate') }}" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition {{ $isCorporateRoute ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-building text-base"></i>
                    <span>Corporate</span>
                </a>
            @endif
        </aside>

        @if((Auth::user()->hasPermission('access_admin_dashboard') || $isAdminRoute) && $isAdminRoute)
            <aside class="w-72 shrink-0 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Admin Panel</p>
                </div>
                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        @if(Auth::user()->hasPermission('manage_users'))
                            <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Users</a>
                            <a href="{{ route('admin.role-permissions') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.role-permissions') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Role Permissions</a>
                            <a href="{{ route('admin.user-permissions') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.user-permissions') || request()->routeIs('admin.user-permissions.edit') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">User Permissions</a>
                        @endif
                    </div>
                </div>
            </aside>
        @elseif(Auth::user()->hasPermission('access_corporate') || $isCorporateRoute)
            <aside class="w-72 shrink-0 bg-white border-r border-gray-200 flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Corporate</p>
                </div>
                <div class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1 text-sm">
                        <a href="{{ route('stock-transfer-book') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('stock-transfer-book*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Stock and Transfer Book</a>
                        <a href="{{ route('notices') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('notices*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Notices of Meeting</a>
                        <a href="{{ route('minutes') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('minutes*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Minutes of Meeting</a>
                        <a href="{{ route('resolutions') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('resolutions*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Resolutions</a>
                        <a href="{{ route('secretary-certificates') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('secretary-certificates*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Secretary Certificates</a>
                        <a href="{{ route('bir-tax') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('bir-tax*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">BIR & Tax</a>
                        <a href="{{ route('natgov') }}" class="block px-3 py-2 rounded-lg transition {{ request()->routeIs('natgov*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">NatGov</a>
                    </div>
                </div>
            </aside>
        @endif

        <main class="min-w-0 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        (() => {
            const currentUser = @js(auth()->user()?->name ?? '');
            const today = @js(now()->toDateString());

            const isVisible = (element) => {
                if (!element) return false;
                return !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
            };

            const applyPanelDefaults = (root = document) => {
                root.querySelectorAll('input[name="uploaded_by"], [data-default-field="current_user"]').forEach((field) => {
                    if (isVisible(field) && !field.value && currentUser) {
                        field.value = currentUser;
                    }
                });

                root.querySelectorAll('[data-default-field="today"]').forEach((field) => {
                    if (isVisible(field) && !field.value) {
                        field.value = today;
                    }
                });
            };

            document.addEventListener('click', () => window.setTimeout(() => applyPanelDefaults(), 0));
            document.addEventListener('focusin', () => applyPanelDefaults());
            document.addEventListener('DOMContentLoaded', () => applyPanelDefaults());
        })();
    </script>
    @stack('scripts')
</body>
</html>
