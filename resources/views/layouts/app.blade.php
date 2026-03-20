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
<style>
    :root {
        --navy: #102d79;
        --blue: #1d54e2;
        --black: #000000;
        --fog: #f1f2f2;
        --white: #ffffff;
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
</style>
</head>

<body class="text-[#000000]">

<!-- HEADER -->
<header class="h-16 bg-[#ffffff] border-b border-[#102d79]/10 sticky top-0 z-50">
<div class="h-full px-4 flex items-center justify-between">

<div class="flex items-center gap-3 w-[260px]">
<img src="/images/imaglogo.png" class="h-10 w-auto">
</div>

<div class="flex-1 flex justify-center px-6">
<div class="relative w-full max-w-xl">
<i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-[#102d79]/60 text-sm"></i>
<input type="text" placeholder="Search"
class="w-full bg-[#f1f2f2] focus:bg-[#ffffff] border border-transparent focus:border-[#1d54e2] focus:ring-2 focus:ring-[#1d54e2]/20 rounded-full pl-11 pr-4 py-2 text-sm outline-none transition">
</div>
</div>

<div class="w-[260px] flex justify-end">
<div class="flex items-center gap-4">

<button class="relative h-9 w-9 rounded-full hover:bg-[#f1f2f2] text-[#102d79]/70 flex items-center justify-center transition">
<i class="far fa-bell text-lg"></i>
<span class="absolute top-2 right-2 w-2 h-2 bg-[#1d54e2] rounded-full"></span>
</button>

<div x-data="{ open:false }" class="relative">

<button
@click="open=!open"
class="h-9 w-9 rounded-full bg-[#f1f2f2] flex items-center justify-center text-[#102d79] font-semibold hover:ring-2 hover:ring-[#102d79]/20 transition">

{{ strtoupper(substr(Auth::user()->name,0,1)) }}

</button>

<div
x-show="open"
@click.outside="open=false"
x-transition
class="absolute right-0 mt-2 w-48 bg-[#ffffff] rounded-xl soft-card overflow-hidden">

<div class="px-4 py-3 border-b border-[#102d79]/10 text-sm">

<p class="font-semibold text-[#102d79]">
{{ Auth::user()->name }}
</p>

<p class="text-[#102d79]/60 text-xs">
{{ Auth::user()->role }}
</p>

</div>

<form method="POST" action="{{ route('logout') }}">
@csrf

<button type="submit"
class="w-full text-left px-4 py-2 text-sm text-[#1d54e2] hover:bg-[#f1f2f2] transition">
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
<aside class="w-16 bg-[#ffffff] border-r border-[#102d79]/10 flex flex-col items-center py-3 gap-2">

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-[#102d79]/70 hover:bg-[#f1f2f2] transition">
<i class="fas fa-bullhorn text-base"></i>
<span>Town Hall</span>
</a>

<a href="/"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('/') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20' : 'text-[#102d79]/70 hover:bg-[#f1f2f2]' }}">
<i class="fas fa-building text-base"></i>
<span>Corporate</span>
</a>

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-[#102d79]/70 hover:bg-[#f1f2f2] transition">
<i class="fas fa-list-check text-base"></i>
<span>Activities</span>
</a>

<a href="{{ route('contacts') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('contacts') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20' : 'text-[#102d79]/70 hover:bg-[#f1f2f2]' }}">
<i class="fas fa-users text-base"></i>
<span>Contacts</span>
</a>

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-[#102d79]/70 hover:bg-[#f1f2f2] transition">
<i class="fas fa-city text-base"></i>
<span>Company</span>
</a>

</aside>



<!-- SECOND SIDEBAR -->
<aside class="w-72 bg-[#ffffff] border-r border-[#102d79]/10 flex flex-col">

<div class="px-4 py-3 border-b border-[#102d79]/10">
<p class="text-xs font-semibold text-[#102d79]/60 uppercase tracking-wide">Corporate</p>
</div>

<div class="flex-1 overflow-y-auto p-3">

<div class="space-y-1 text-sm">

<a href="{{ route('corporate') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->is('corporate') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Company General Information
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-[#f1f2f2] text-[#102d79]">
Corporate/Formation
</a>

                <a href="{{ route('corporate.ubo') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('corporate/ubo') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Ultimate Beneficial Owner
</a>

<a href="{{ route('stock-transfer-book') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('stock-transfer-book*') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Stock and Transfer Book
</a>

<a href="{{ route('notices') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('notices') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Notices of Meeting
</a>

<a href="{{ route('minutes') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('minutes') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Minutes of Meeting
</a>

<a href="{{ route('resolutions') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('resolutions') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Resolutions
</a>

<a href="{{ route('secretary-certificates') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('secretary-certificates') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
Secretary Certificates
</a>

<a href="{{ route('bir-tax') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('bir-tax') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
BIR & Tax
</a>

<a href="{{ route('natgov') }}" class="block px-3 py-2 rounded-lg transition
{{ request()->is('natgov') ? 'bg-[#1d54e2]/10 text-[#1d54e2] border border-[#1d54e2]/20 font-semibold' : 'hover:bg-[#f1f2f2] text-[#102d79]' }}">
NatGov
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-[#f1f2f2] text-[#102d79]">
LGU
</a>

<div class="mt-3 pt-3 border-t border-[#102d79]/10 space-y-1">

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-[#f1f2f2] text-[#102d79]">
Accounting
</a>

</a>

</div>

</div>
</div>

</aside>



<!-- MAIN CONTENT -->
<main class="flex-1 overflow-y-auto">
@yield('content')
</main>

</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
