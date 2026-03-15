<x-slide-over id="addCompanyModal" width="sm:max-w-[640px] lg:max-w-[760px]">
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Add a Company</h2>
                <p class="mt-1 text-sm text-gray-500">Fill out the details below to create a company record.</p>
            </div>
            <button
                type="button"
                data-close-company-modal
                class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50"
            >
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('company.store') }}" class="flex min-h-0 flex-1 flex-col">
        @csrf

        <div class="flex-1 overflow-y-auto px-4 py-4 sm:px-6">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Company Information</h3>
                <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="company_name" class="mb-1 block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
                        <input
                            id="company_name"
                            name="company_name"
                            type="text"
                            value="{{ old('company_name') }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                        @error('company_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_type" class="mb-1 block text-sm font-medium text-gray-700">Company Type</label>
                        <input
                            id="company_type"
                            name="company_type"
                            type="text"
                            value="{{ old('company_type', 'Corporation') }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('company_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone') }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="website" class="mb-1 block text-sm font-medium text-gray-700">Website</label>
                        <input
                            id="website"
                            name="website"
                            type="text"
                            value="{{ old('website') }}"
                            class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('website')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Address Information</h3>
                <div class="mt-3">
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button
                    type="button"
                    data-close-company-modal
                    class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Save
                </button>
            </div>
        </div>
    </form>
</x-slide-over>
