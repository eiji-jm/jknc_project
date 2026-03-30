<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>John Kelly & Company | CRM</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body class="bg-gray-50 font-sans">

@php
    $isCorporateSection =
        request()->is('/') ||
        request()->is('corporate') ||
        request()->is('lgu') ||
        request()->is('accounting') ||
        request()->is('banking') ||
        request()->is('legal') ||
        request()->is('operations') ||
        request()->is('correspondence');

    $isHumanCapitalSection =
        request()->is('human-capital') ||
        request()->is('human-capital/*');
@endphp

<!-- HEADER -->
<header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="h-full px-4 flex items-center justify-between">

        <div class="flex items-center gap-3 w-[260px]">
            <img src="/images/imaglogo.png" class="h-10 w-auto">
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
                    >
                        <div class="px-4 py-3 border-b text-sm">
                            <p class="font-semibold text-gray-800">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-gray-400 text-xs">
                                {{ Auth::user()->role }}
                            </p>
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

        <a href="#"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-bullhorn text-base"></i>
            <span>Town Hall</span>
        </a>

        <a href="{{ route('corporate') }}"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
           {{ $isCorporateSection ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-building text-base"></i>
            <span>Corporate</span>
        </a>

        <a href="{{ route('human-capital.dashboard') }}"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
           {{ $isHumanCapitalSection ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-user-tie text-base"></i>
            <span>Human Capital</span>
        </a>

        <a href="#"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-list-check text-base"></i>
            <span>Activities</span>
        </a>

        <a href="#"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-users text-base"></i>
            <span>Contacts</span>
        </a>

        <a href="#"
           class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-city text-base"></i>
            <span>Company</span>
        </a>

    </aside>

    <!-- SECOND SIDEBAR -->
    <aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                {{ $isHumanCapitalSection ? 'Human Capital' : 'Corporate' }}
            </p>
        </div>

        <div class="flex-1 overflow-y-auto p-3">
            <div class="space-y-1 text-sm">

                @if($isHumanCapitalSection)

                    <a href="{{ route('human-capital.dashboard') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('human-capital.organizational') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/organizational') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Organizational
                    </a>

                    <a href="{{ route('human-capital.payroll') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/payroll') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Payroll
                    </a>

                    <a href="{{ route('human-capital.employee-profile') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/employee-profile') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Employee Profile
                    </a>

                    <a href="{{ route('human-capital.recruitment') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/recruitment') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Recruitment
                    </a>

                    <a href="{{ route('human-capital.attendance') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/attendance') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Attendance
                    </a>

                    <a href="{{ route('human-capital.employee-requests') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/employee-requests') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Employee Requests
                    </a>

                    <a href="{{ route('human-capital.employee-relations') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/employee-relations') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Employee Relations
                    </a>

                    <a href="{{ route('human-capital.training') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/training') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Training
                    </a>

                    <a href="{{ route('human-capital.performance') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/performance') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Performance
                    </a>

                    <a href="{{ route('human-capital.offboarding') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('human-capital/offboarding') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        OffBoarding
                    </a>

                @else

                    <a href="{{ route('corporate') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('corporate') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Company General Information
                    </a>

                    <a href="#"
                       class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
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

                    <a href="{{ route('lgu') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('lgu') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        LGU
                    </a>

                    <a href="{{ route('accounting') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('accounting') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Accounting
                    </a>

                    <a href="{{ route('banking') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('banking') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}>
                        Banking
                    </a>

                    <a href="{{ route('legal') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('legal') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Legal
                    </a>

                    <a href="{{ route('operations') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('operations') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Operations
                    </a>

                    <a href="{{ route('correspondence') }}"
                       class="block px-3 py-2 rounded-lg transition
                       {{ request()->is('correspondence') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
                        Correspondence
                    </a>

                    <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
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

                @endif

            </div>
        </div>
    </aside>

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