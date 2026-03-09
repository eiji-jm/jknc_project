<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Kelly & Company | CRM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans">

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
                            @click="open = !open"
                            class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold hover:ring-2 hover:ring-gray-300 transition"
                        >
                            A
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                        >
                            <div class="px-4 py-3 border-b text-sm">
                                <p class="font-semibold text-gray-800">Admin User</p>
                                <p class="text-gray-400 text-xs">Administrator</p>
                            </div>

                            <button
                                type="button"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition"
                            >
                                <i class="fas fa-cog mr-2"></i>
                                Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <div class="flex h-[calc(100vh-4rem)]">

        <aside class="w-16 bg-white border-r border-gray-200 flex flex-col items-center py-3 gap-2">
            <a href="{{ route('townhall') }}"
               class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
               {{ request()->is('townhall') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-bullhorn text-base"></i>
                <span>Town Hall</span>
            </a>
        </aside>

        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
