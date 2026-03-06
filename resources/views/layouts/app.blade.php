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

<button class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold hover:ring-2 hover:ring-gray-300 transition">
A
</button>

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

<a href="/"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('/') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-building text-base"></i>
<span>Corporate</span>
</a>

<a href="/activities"
class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] transition
{{ request()->is('activities*') ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-600 hover:bg-gray-100' }}">
<i class="fas fa-list-check text-base"></i>
<span>Activities</span>
</a>

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
<i class="fas fa-users text-base"></i>
<span>Contacts</span>
</a>

<a href="#" class="w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-1 text-[10px] text-gray-600 hover:bg-gray-100 transition">
<i class="fas fa-city text-base"></i>
<span>Company</span>
</a>

</aside>



<!-- SECOND SIDEBAR -->
@if (request()->is('activities*'))
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

<div class="px-4 py-3 border-b border-gray-100">
<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Activities</p>
</div>

<div class="flex-1 overflow-y-auto p-3">

<div class="space-y-1 text-sm">

<a href="/activities" class="block px-3 py-2 rounded-lg transition {{ request()->is('activities*') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
    My Tasks
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
    Team Tasks
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
    Reports
</a>

</div>
</div>

</aside>
@else
<aside class="w-72 bg-white border-r border-gray-200 flex flex-col">

<div class="px-4 py-3 border-b border-gray-100">
<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Corporate</p>
</div>

<div class="flex-1 overflow-y-auto p-3">

<div class="space-y-1 text-sm">

<a href="/"
class="block px-3 py-2 rounded-lg transition
{{ request()->is('/') ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'hover:bg-gray-100 text-gray-700' }}">
Company General Information
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Corporate/Formation
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
BIR & Tax
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
NatGov
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
LGU
</a>

<div class="mt-3 pt-3 border-t border-gray-100 space-y-1">

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Accounting
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Priority
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Status
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Tag
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
Task Name
</a>

<a href="#" class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
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

</body>
</html>