<div id="changeOwnerModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative mx-auto mt-24 w-full max-w-[520px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-semibold leading-none text-blue-900">Change Owner</h2>
                <span class="text-gray-400">&middot;</span>
                <p id="changeOwnerModalCount" class="text-sm font-normal text-gray-500">0 Product Selected</p>
            </div>
            <button id="closeChangeOwnerModalX" type="button" class="text-xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
        </div>

        <form id="changeOwnerForm" method="POST" action="{{ route('products.change-owner') }}" class="space-y-4 px-6 py-6">
            @csrf
            <div id="selectedProductsFields"></div>
            <input type="hidden" id="selectedOwnerId" name="owner_id" value="">

            <div class="grid grid-cols-[140px_1fr] items-start gap-4">
                <label for="changeOwnerSearchInput" class="pt-2 text-sm font-medium text-gray-700">Change Owner</label>
                <div class="relative">
                    <div class="relative">
                        <input
                            id="changeOwnerSearchInput"
                            type="text"
                            autocomplete="off"
                            placeholder="Select"
                            class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 pr-10 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        <button id="toggleOwnerDropdown" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>

                    <div id="ownerDropdownMenu" class="absolute left-0 right-0 z-20 mt-1 hidden max-h-56 overflow-y-auto rounded-md border border-blue-200 bg-white shadow-lg">
                        @foreach ($owners as $owner)
                            @php
                                $initials = strtoupper(collect(explode(' ', $owner['name']))
                                    ->filter()
                                    ->map(fn ($part) => mb_substr($part, 0, 1))
                                    ->take(2)
                                    ->implode(''));
                            @endphp
                            <button
                                type="button"
                                class="owner-option flex w-full items-center gap-3 border-b border-gray-100 px-3 py-2 text-left hover:bg-blue-50"
                                data-owner-id="{{ $owner['id'] }}"
                                data-owner-name="{{ $owner['name'] }}"
                                data-owner-email="{{ $owner['email'] }}"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-[11px] font-semibold text-blue-700">
                                    {{ $initials }}
                                </span>
                                <span>
                                    <span class="block text-sm font-medium text-gray-800">{{ $owner['name'] }}</span>
                                    <span class="block text-xs text-gray-500">{{ $owner['email'] }}</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            @error('owner_id')
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $message }}
                </div>
            @enderror

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <button id="cancelChangeOwnerModal" type="button" class="h-9 min-w-[96px] rounded-full border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="saveChangeOwnerBtn" type="submit" disabled class="h-9 min-w-[96px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white disabled:cursor-not-allowed disabled:bg-blue-300">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
