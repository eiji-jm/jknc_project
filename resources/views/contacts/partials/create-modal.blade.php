<div id="createContactModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative mx-auto mt-16 w-full max-w-2xl rounded-2xl border border-gray-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-2xl font-semibold text-gray-900">Create Contact</h2>
            <button id="closeCreateContactModal" type="button" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form method="POST" action="{{ route('contacts.store') }}" class="p-6">
            @csrf
            <input id="owner_id" type="hidden" name="owner_id" value="{{ old('owner_id', $selectedOwnerId) }}">

            <div class="mb-5 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Contact Information</p>

                <div class="relative">
                    <button
                        id="ownerDropdownTrigger"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-700 hover:bg-gray-100"
                    >
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span id="ownerSelectedLabel">Owner: {{ $selectedOwnerName }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                    </button>

                    <div id="ownerDropdownMenu" class="absolute right-0 z-20 mt-2 hidden w-72 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                        <div class="relative mb-2">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input id="ownerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="max-h-56 space-y-1 overflow-y-auto">
                            @foreach ($owners as $owner)
                                @php
                                    $ownerInitials = strtoupper(collect(explode(' ', trim($owner['name'])))
                                        ->filter()
                                        ->map(fn ($segment) => mb_substr($segment, 0, 1))
                                        ->take(2)
                                        ->implode(''));
                                @endphp
                                <button
                                    type="button"
                                    class="owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                    data-owner-id="{{ $owner['id'] }}"
                                    data-owner-name="{{ $owner['name'] }}"
                                >
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-semibold text-blue-700">
                                        {{ $ownerInitials }}
                                    </span>
                                    <span>{{ $owner['name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-[120px_1fr] items-center gap-3">
                    <label for="first_name" class="text-right text-sm text-gray-700">First Name</label>
                    <input id="first_name" name="first_name" required value="{{ old('first_name') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[120px_1fr] items-center gap-3">
                    <label for="last_name" class="text-right text-sm text-gray-700">Last Name</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[120px_1fr] items-center gap-3">
                    <label for="lead_source" class="text-right text-sm text-gray-700">Lead Source</label>
                    <input id="lead_source" name="lead_source" value="{{ old('lead_source') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[120px_1fr] items-center gap-3">
                    <label for="email" class="text-right text-sm text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="h-10 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="grid grid-cols-[120px_1fr] items-center gap-3">
                    <label for="mobile" class="text-right text-sm text-gray-700">Mobile</label>
                    <div class="flex items-center gap-2">
                        <input id="mobile" name="mobile" value="{{ old('mobile') }}" class="h-10 flex-1 rounded border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 text-gray-600">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-[120px_1fr] items-start gap-3">
                    <label for="description" class="pt-2 text-right text-sm text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3" class="rounded border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between">
                <button id="cancelCreateContactModal" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
