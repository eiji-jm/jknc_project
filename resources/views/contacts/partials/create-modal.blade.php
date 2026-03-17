<div id="createContactModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createContactModalOverlay" type="button" aria-label="Close create contact panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createContactPanel" class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[680px]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Create Contact</h2>
                    <p class="mt-1 text-sm text-gray-500">Store the client master record before creating deals.</p>
                </div>
                <button id="closeCreateContactModal" type="button" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <form method="POST" action="{{ route('contacts.store') }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input id="owner_id" type="hidden" name="owner_id" value="{{ old('owner_id', $selectedOwnerId) }}">

                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Client Intake</p>
                            <p class="text-xs text-gray-400">Use this as the main contact and business record.</p>
                        </div>

                        <div class="relative sm:flex-shrink-0">
                            <button id="ownerDropdownTrigger" type="button" class="inline-flex w-full items-center justify-between gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 sm:w-auto">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <span id="ownerSelectedLabel">Owner: {{ $selectedOwnerName }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                            </button>

                            <div id="ownerDropdownMenu" class="absolute right-0 z-20 mt-2 hidden w-full min-w-0 rounded-xl border border-gray-200 bg-white p-2 shadow-lg sm:w-72">
                                <div class="relative mb-2">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                    <input id="ownerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div class="max-h-56 space-y-1 overflow-y-auto">
                                    @foreach ($owners as $owner)
                                        @php
                                            $ownerInitials = strtoupper(collect(explode(' ', trim($owner['name'])))->filter()->map(fn ($segment) => mb_substr($segment, 0, 1))->take(2)->implode(''));
                                        @endphp
                                        <button type="button" class="owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" data-owner-id="{{ $owner['id'] }}" data-owner-name="{{ $owner['name'] }}">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-semibold text-blue-700">{{ $ownerInitials }}</span>
                                            <span>{{ $owner['name'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Customer Type</h3>
                            <p class="text-xs text-gray-500">Identify whether the client is an individual or business record.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="customer_type" class="mb-1 block text-sm font-medium text-gray-700">Customer Type</label>
                                <select id="customer_type" name="customer_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select type</option>
                                    @foreach (['Individual', 'Business', 'Corporate', 'Partnership', 'Other'] as $option)
                                        <option value="{{ $option }}" @selected(old('customer_type') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="service_inquiry_type" class="mb-1 block text-sm font-medium text-gray-700">Service Inquiry Type</label>
                                <input id="service_inquiry_type" name="service_inquiry_type" value="{{ old('service_inquiry_type') }}" placeholder="Tax, corporate, compliance, etc." class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Contact Information</h3>
                            <p class="text-xs text-gray-500">Primary client details that will auto-fill into deals later.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="salutation" class="mb-1 block text-sm font-medium text-gray-700">Salutation</label>
                                <select id="salutation" name="salutation" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select salutation</option>
                                    @foreach (['Mr.', 'Ms.', 'Mrs.', 'Atty.', 'CPA', 'Engr.', 'Dr.'] as $option)
                                        <option value="{{ $option }}" @selected(old('salutation') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="position" class="mb-1 block text-sm font-medium text-gray-700">Position / Designation</label>
                                <input id="position" name="position" value="{{ old('position') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name</label>
                                <input id="first_name" name="first_name" required value="{{ old('first_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="middle_name" class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label>
                                <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name</label>
                                <input id="last_name" name="last_name" value="{{ old('last_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="mobile" class="mb-1 block text-sm font-medium text-gray-700">Mobile</label>
                                <input id="mobile" name="mobile" value="{{ old('mobile') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="contact_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                                <textarea id="contact_address" name="contact_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('contact_address') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Business Information</h3>
                            <p class="text-xs text-gray-500">Link the contact to the company or business if applicable.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="company_name" class="mb-1 block text-sm font-medium text-gray-700">Company / Business Name</label>
                                <input id="company_name" name="company_name" value="{{ old('company_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="company_address" class="mb-1 block text-sm font-medium text-gray-700">Company Address</label>
                                <textarea id="company_address" name="company_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('company_address') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Lead Details</h3>
                            <p class="text-xs text-gray-500">Capture source, referral, and current stage of the client intake.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="lead_source" class="mb-1 block text-sm font-medium text-gray-700">Lead Source</label>
                                <input id="lead_source" name="lead_source" value="{{ old('lead_source') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="referred_by" class="mb-1 block text-sm font-medium text-gray-700">Referred By</label>
                                <input id="referred_by" name="referred_by" value="{{ old('referred_by') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="lead_stage" class="mb-1 block text-sm font-medium text-gray-700">Lead Stage</label>
                                <select id="lead_stage" name="lead_stage" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select stage</option>
                                    @foreach (['New Inquiry', 'Qualified', 'Consultation Scheduled', 'Proposal Preparation', 'For Follow-up', 'Converted', 'Declined'] as $option)
                                        <option value="{{ $option }}" @selected(old('lead_stage') === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Notes and Recommendation</h3>
                            <p class="text-xs text-gray-500">Keep intake notes visible for future deal creation.</p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                            </div>
                            <div>
                                <label for="recommendation" class="mb-1 block text-sm font-medium text-gray-700">Recommendation</label>
                                <textarea id="recommendation" name="recommendation" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('recommendation') }}</textarea>
                            </div>
                        </div>
                    </section>

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                </div>

                <div class="mt-auto flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                    <button id="cancelCreateContactModal" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>
