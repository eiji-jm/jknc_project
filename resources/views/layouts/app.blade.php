<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>John Kelly & Company | CRM</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans">

<!-- HEADER -->
<header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-50">
<div class="h-full px-4 flex items-center justify-between">

<div class="flex items-center gap-3 w-[260px]">
<img src="/images/imaglogo.png" class="h-10 w-auto">
</div>

<div class="flex-1 flex justify-center px-6">
<div class="relative w-full max-w-xl">
<i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
<input type="text" placeholder="Search"
class="w-full bg-gray-100 focus:bg-white border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-full pl-11 pr-4 py-2 text-sm outline-none transition">
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
class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold hover:ring-2 hover:ring-gray-300 transition">
{{ strtoupper(substr(Auth::user()->name,0,1)) }}
</button>

<div
x-show="open"
@click.outside="open=false"
x-transition
class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
style="display:none;">

<div class="px-4 py-3 border-b text-sm">
<p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
<p class="text-gray-400 text-xs">{{ Auth::user()->role }}</p>
</div>

<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit"
class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition">
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

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
<i class="fas fa-bullhorn text-base"></i>
<span>Town Hall</span>
</a>

<a href="{{ route('corporate') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->routeIs('corporate') || request()->routeIs('corporate.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-building text-base"></i>
<span>Corporate</span>
</a>

<a href="/activities"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ (request()->is('activities*') || request()->is('/')) ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-list-check text-base"></i>
<span>Activities</span>
</a>

<a href="{{ route('contacts.index') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('contacts*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-users text-base"></i>
<span>Contacts</span>
</a>

<a href="{{ route('deals.index') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('deals*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-handshake-angle text-base"></i>
<span>Deals</span>
</a>

<a href="{{ route('company.index') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->routeIs('company.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-city text-base"></i>
<span>Company</span>
</a>

<a href="{{ route('services.index') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-briefcase text-base"></i>
<span>Services</span>
</a>

<a href="{{ route('products.index') }}"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('products*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-cube text-base"></i>
<span>Product</span>
</a>

</aside>

<!-- SECOND SIDEBAR -->
@if (request()->routeIs('company.show') || request()->routeIs('company.kyc') || request()->routeIs('company.bif.*') || request()->routeIs('company.history') || request()->routeIs('company.consultation-notes') || request()->routeIs('company.activities') || request()->routeIs('company.deals') || request()->routeIs('company.deals.*') || request()->routeIs('company.contacts') || request()->routeIs('company.projects') || request()->routeIs('company.regular') || request()->routeIs('company.products.*') || request()->routeIs('company.products') || request()->routeIs('company.services.*') || request()->routeIs('company.corporate-formation*') || request()->routeIs('company.lgu*') || request()->routeIs('company.accounting*') || request()->routeIs('company.banking*') || request()->routeIs('company.operations*') || request()->routeIs('company.correspondence*') || request()->routeIs('company.bir-tax*'))
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

<div class="px-4 py-3 border-b border-gray-100">
<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</p>
</div>

<div class="flex-1 overflow-y-auto p-3">
<div class="space-y-1 text-sm">
@php($companyRouteParam = request()->route('company'))

<a href="{{ route('company.kyc', ['company' => $companyRouteParam, 'tab' => 'business-client-information']) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.show') || request()->routeIs('company.kyc') || request()->routeIs('company.bif.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
KYC
</a>
<a href="{{ route('company.history', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.history') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">History</a>
<a href="{{ route('company.consultation-notes', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.consultation-notes') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Consultation notes</a>
<a href="{{ route('company.activities', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.activities') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Activities</a>
<a href="{{ route('company.deals', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.deals') || request()->routeIs('company.deals.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Deals</a>
<a href="{{ route('company.contacts', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.contacts') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Contacts</a>
<a href="{{ route('company.projects', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.projects') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Projects</a>
<a href="{{ route('company.regular', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.regular') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Regular</a>
<a href="{{ route('company.products', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.products') || request()->routeIs('company.products.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Products</a>
<a href="{{ route('company.services.index', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.services.*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Services</a>

<div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
<a href="{{ route('company.corporate-formation', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.corporate-formation*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Corporate/Formation</a>
<a href="{{ route('company.bir-tax', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.bir-tax*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">BIR & Tax</a>
<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">National</a>
<a href="{{ route('company.lgu', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.lgu*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">LGU</a>
<a href="{{ route('company.accounting', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.accounting*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Accounting</a>
<a href="{{ route('company.banking', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.banking*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Banking</a>
<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">Legal</a>
<a href="{{ route('company.operations', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.operations*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Operations</a>
<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">201 Files</a>
<a href="{{ route('company.correspondence', $companyRouteParam) }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('company.correspondence*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Correspondence</a>
</div>

</div>
</div>

</aside>
@elseif (request()->routeIs('activities'))
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

<div class="px-4 py-3 border-b border-gray-100">
<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Activities</p>
</div>

<div class="flex-1 overflow-y-auto p-3">
<div class="space-y-1 text-sm">
<a href="{{ route('activities') }}#task" class="block px-3 py-2 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 font-semibold">Task</a>
<a href="{{ route('activities') }}#events" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">Events</a>
<a href="{{ route('activities') }}#call" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">Call</a>
<a href="{{ route('activities') }}#meetings" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">Meetings</a>
</div>
</div>

</aside>
@elseif (!request()->routeIs('company.*') && !request()->routeIs('contacts.*') && !request()->routeIs('deals.*') && !request()->routeIs('products.*') && !request()->routeIs('activities') && !request()->routeIs('services.*'))
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

<div class="px-4 py-3 border-b border-gray-100">
<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Corporate</p>
</div>

<div class="flex-1 overflow-y-auto p-3">
<div class="space-y-1 text-sm">

<a href="{{ route('corporate') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate') || request()->routeIs('corporate.companyinfo') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
Company General Information
</a>

<a href="{{ route('corporate.formation') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.formation') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
Corporate/Formation
</a>

<a href="{{ route('bir-tax') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('bir-tax*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
BIR & Tax
</a>

<a href="{{ route('natgov') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('natgov*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
NatGov
</a>

<a href="{{ route('corporate.lgu') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.lgu') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
LGU
</a>

<a href="{{ route('corporate.ubo') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.ubo') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
Ultimate Beneficial Owner
</a>

<a href="{{ route('stock-transfer-book') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('stock-transfer-book*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
Stock and Transfer Book
</a>

<div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
<a href="{{ route('corporate.accounting') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.accounting') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Accounting</a>
<a href="{{ route('corporate.banking') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.banking') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Banking</a>
<a href="{{ route('corporate.operations') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.operations') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Operations</a>
<a href="{{ route('corporate.correspondence') }}"
class="block px-3 py-2 rounded-lg transition
{{ request()->routeIs('corporate.correspondence') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Correspondence</a>
<a href="{{ route('notices') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('notices*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Notices of Meeting</a>
<a href="{{ route('minutes') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('minutes*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Minutes of Meeting</a>
<a href="{{ route('resolutions') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('resolutions*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Resolutions</a>
<a href="{{ route('secretary-certificates') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('secretary-certificates*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">Secretary Certificates</a>
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

</body>
</html>
