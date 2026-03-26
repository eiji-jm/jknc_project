
@php
    $formAction = $formAction ?? route('deals.store');
    $formMethod = strtoupper($formMethod ?? 'POST');
    $submitLabel = $submitLabel ?? 'Save & View Deal';
    $draft = $dealDraft ?? [];
    $serviceAreaOptions = [
        'Corporate & Regulatory Advisory',
        'Governance & Policy Advisory',
        'People & Talent Solutions',
        'Strategic Situations Advisory',
        'Accounting & Compliance Advisory',
        'Business Strategy & Process Advisory',
        'Learning & Capability Development',
        'Others',
    ];
    $serviceGroups = [
        'Corporate & Regulatory Advisory' => [
            'Business Registration (SEC / DTI / BIR)',
            'Business Permit Processing / Renewal',
            'Regulatory Compliance',
            'Loan Application Assistance',
            'Foreign Business Entry Support',
        ],
        'Accounting & Compliance Advisory' => [
            'Bookkeeping Services',
            'Tax Filing & Compliance (BIR)',
            'AFS Preparation',
            'Audit Support / Coordination',
            'Accounting Services',
        ],
        'Governance & Policy Advisory' => [
            'Corporate Secretary Services',
            'Corporate Officers Services',
            'Policy Development (HR, Finance, Ops)',
            'Board Resolutions & Minutes',
            'Risk & Internal Control Setup',
        ],
        'Business Strategy & Process Advisory' => [
            'Business Consulting / Strategy',
            'Process Improvement / SOP Development',
            'Organizational Structuring',
            'Digital Transformation',
            'Financial Planning & Analysis',
        ],
        'Strategic Situations Advisory' => [
            'Corporate Deadlock Resolution',
            'Crisis Assessment & Stabilization',
            'Business Restructuring Strategy',
            'Stakeholder Negotiation Support',
            'High-Risk / Complex Case Advisory',
        ],
        'People & Talent Solutions' => [
            'Recruitment & Hiring Support',
            'HR Structuring & Organization Design',
            'KPI & Performance Management Systems',
            'HR Documentation & Contracts',
            'Executive / Virtual Assistant Support',
        ],
        'Learning & Capability Development' => [
            'Accounting & Compliance Training',
            'Corporate Governance Workshops',
            'Business & Strategy Training',
            'Client Capability Development Programs',
            'JKNC Academy Courses',
        ],
    ];
    $productOptions = [
        'Printing',
        'Photocopy',
        'Drafting of Letters',
        'Drafting of Notices',
        'Drafting of Demand Letters',
        'Drafting of Emails (Formal / Business)',
        'Archive Retrieval',
        'Digital Archive Copy',
        'Drafting of Responses to Letters / Notices',
        'Drafting of Memorandum (Internal / External)',
        'Drafting of Certifications',
        'Drafting of Compliance Documents',
        'Document Delivery (Metro Cebu)',
        'Document Delivery (Outside Metro Cebu/LBC)',
        'Drafting of Affidavits (Non-Legal Advice)',
        'Drafting of Agreements / Simple Contracts)',
        'Drafting of Board Resolutions',
        'Drafting of Endorsement / Request Letters',
        'Notarization - Simple Documents',
        'Notarization - Complex Documents',
        "Drafting of Secretary's Certificates",
        'Drafting of Policies & Procedures',
        'Drafting of Reports / Formal Documents',
        'Others',
    ];
    $requirementRows = [
        'client_contact_form' => 'Client Contact Form',
        'deal_form' => 'Deal Form',
        'business_information_form' => 'Business Information Form',
        'client_information_form' => 'Client Information Form',
        'service_task_activation_routing_tracker' => 'Service Task Activation & Routing Tracker (Start)',
        'others' => 'Others',
    ];
    $requiredActions = [
        'Document Review',
        'Regulatory Research',
        'Drafting of Documents',
        'Client Consultation',
        'Compliance Check',
        'Financial Analysis',
        'Government Filing / Processing',
        'Internal Approval',
    ];
    $selectedServiceAreas = old('service_area_options', $draft['service_area_options'] ?? []);
    $selectedServices = old('service_options', $draft['service_options'] ?? []);
    $selectedProducts = old('product_options', $draft['product_options'] ?? []);
    $selectedRequiredActions = old('required_actions_options', $draft['required_actions_options'] ?? []);
    $selectedSupportRequired = old('support_required_options', $draft['support_required_options'] ?? []);
    $selectedOwner = collect($owners)->firstWhere('id', (int) old('owner_id', $defaultOwnerId)) ?: collect($owners)->first();
    $selectedOwnerId = (int) ($selectedOwner['id'] ?? $defaultOwnerId ?? 0);
    $selectedOwnerName = $selectedOwner['name'] ?? $ownerLabel ?? 'Select Owner';
    $currentUserName = auth()->user()->name ?? 'System';
    $draftDealCode = old('deal_code', $draft['deal_code'] ?? 'Auto-generated after save');
    $draftCreatedBy = old('created_by', $draft['created_by'] ?? $currentUserName);
    $draftCreatedAt = old('created_at_label', $draft['created_at_label'] ?? now()->format('F d, Y • h:i:s A'));
@endphp

<div id="createDealModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <button id="createDealModalOverlay" type="button" aria-label="Close create deal panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>

    <div class="pointer-events-none absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden">
        <div id="createDealPanel" class="pointer-events-auto flex h-full w-full max-w-[860px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[820px]">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                <div>
                    <h2 id="dealPanelTitle" class="text-2xl font-semibold text-gray-900">Create Deal</h2>
                    <p id="dealPanelSubtitle" class="mt-1 text-sm text-gray-500">Select an existing client, then complete the consulting and deal form.</p>
                </div>
                <button id="closeCreateDealModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <form id="createDealForm" method="POST" action="{{ $formAction }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                @if ($formMethod !== 'POST')
                    @method($formMethod)
                @endif
                <input id="deal_selected_owner_id" type="hidden" name="owner_id" value="{{ old('owner_id', $selectedOwnerId) }}">
                <input id="deal_selected_contact_id" type="hidden" name="contact_id" value="{{ old('contact_id', $draft['contact_id'] ?? '') }}">

                <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-medium text-gray-500">Consulting & Deal Form</p>
                        <div class="relative sm:flex-shrink-0">
                            <button id="dealOwnerDropdownTrigger" type="button" class="inline-flex h-10 w-full items-center justify-between gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 sm:w-auto">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <span id="dealOwnerSelectedLabel">Owner: {{ $selectedOwnerName }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                            </button>
                            <div id="dealOwnerDropdownMenu" class="absolute right-0 z-20 mt-2 hidden w-full min-w-0 rounded-xl border border-gray-200 bg-white p-2 shadow-lg sm:w-72">
                                <div class="relative mb-2">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                    <input id="dealOwnerSearch" type="text" placeholder="Search owner..." class="h-9 w-full rounded-lg border border-gray-200 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
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
                                        <button type="button" class="deal-owner-option flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" data-owner-id="{{ $owner['id'] }}" data-owner-name="{{ $owner['name'] }}" data-owner-email="{{ $owner['email'] }}">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-semibold text-blue-700">{{ $ownerInitials }}</span>
                                            <span>
                                                <span class="block text-sm text-gray-700">{{ $owner['name'] }}</span>
                                                <span class="block text-xs text-gray-500">{{ $owner['email'] }}</span>
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Deal Information</h3>
                        <p class="mb-4 text-xs text-gray-500">Auto-generated metadata appears here after the deal is saved.</p>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Deal Code</p>
                                <p id="deal_info_code" class="mt-1 text-sm text-gray-700">{{ $draftDealCode }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Created By</p>
                                <p id="deal_info_created_by" class="mt-1 text-sm text-gray-700">{{ $draftCreatedBy }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Created At</p>
                                <p id="deal_info_created_at" class="mt-1 text-sm text-gray-700">{{ $draftCreatedAt }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Select Existing Contact / Client</h3>
                        <p class="mb-4 text-xs text-gray-500">Search by contact name, company, email, or mobile number.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="relative sm:col-span-2">
                                <label for="dealContactSearch" class="mb-1 block text-sm font-medium text-gray-700">Search Existing Contact</label>
                                <i class="fas fa-search pointer-events-none absolute left-3 top-[42px] text-xs text-gray-400"></i>
                                <input id="dealContactSearch" type="text" placeholder="Type name, company, email, or mobile..." class="h-10 w-full rounded-lg border border-gray-300 pl-8 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <div id="dealContactResults" class="absolute left-0 right-0 z-20 mt-1 hidden max-h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-lg"></div>
                            </div>
                            <div>
                                <label for="deal_name" class="mb-1 block text-sm font-medium text-gray-700">Deal Name</label>
                                <input id="deal_name" name="deal_name" required value="{{ old('deal_name', $draft['deal_name'] ?? '') }}" placeholder="Enter deal name" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="stage" class="mb-1 block text-sm font-medium text-gray-700">Pipeline Stage</label>
                                <select id="stage" name="stage" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    @foreach ($stageOptions as $stage)
                                        <option value="{{ $stage }}" @selected(old('stage', $draft['stage'] ?? 'Inquiry') === $stage)>{{ $stage }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p id="dealContactRequiredMessage" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">Contact selection is required before completing the rest of the deal form.</p>
                    </section>

                    <div id="dealDependentSections" class="space-y-5">
                        <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Customer Type</h3>
                            <div class="mt-3 space-y-3">
                                <div class="grid items-center gap-2 sm:grid-cols-[72px_1fr]">
                                    <label class="text-sm font-medium text-gray-700">Type</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach (['business' => 'Business', 'individual' => 'Individual'] as $value => $label)
                                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input type="radio" name="customer_type" value="{{ $value }}" @checked(old('customer_type', $draft['customer_type'] ?? '') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="grid items-center gap-2 sm:grid-cols-[72px_1fr]">
                                    <label class="text-sm font-medium text-gray-700">Status</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach (['new' => 'New Client', 'existing' => 'Existing Client'] as $value => $label)
                                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input type="radio" name="client_status" value="{{ $value }}" @checked(old('client_status', $draft['client_status'] ?? '') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Contact Information</h3>
                            <p class="mb-4 text-xs text-gray-500">Fields auto-fill from selected contact and remain editable.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="deal_salutation" class="mb-1 block text-sm font-medium text-gray-700">Salutation</label>
                                    <select id="deal_salutation" name="salutation" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Select salutation</option>
                                        @foreach (['Mr.', 'Ms.', 'Mrs.', 'Atty.', 'CPA', 'Engr.', 'Dr.'] as $option)
                                            <option value="{{ $option }}" @selected(old('salutation', $draft['salutation'] ?? '') === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="deal_sex" class="mb-1 block text-sm font-medium text-gray-700">Sex</label>
                                    <select id="deal_sex" name="sex" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Select sex</option>
                                        @foreach (['Male', 'Female', 'Prefer not to say'] as $option)
                                            <option value="{{ $option }}" @selected(old('sex', $draft['sex'] ?? '') === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label for="deal_first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name</label><input id="deal_first_name" name="first_name" value="{{ old('first_name', $draft['first_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_middle_initial" class="mb-1 block text-sm font-medium text-gray-700">Middle Initial</label><input id="deal_middle_initial" name="middle_initial" value="{{ old('middle_initial', $draft['middle_initial'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name</label><input id="deal_last_name" name="last_name" value="{{ old('last_name', $draft['last_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_name_extension" class="mb-1 block text-sm font-medium text-gray-700">Name Extension</label><input id="deal_name_extension" name="name_extension" value="{{ old('name_extension', $draft['name_extension'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_date_of_birth" class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label><input id="deal_date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $draft['date_of_birth'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_email" class="mb-1 block text-sm font-medium text-gray-700">Email Address</label><input id="deal_email" type="email" name="email" value="{{ old('email', $draft['email'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_mobile" class="mb-1 block text-sm font-medium text-gray-700">Mobile Number</label><input id="deal_mobile" name="mobile" value="{{ old('mobile', $draft['mobile'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div class="sm:col-span-2"><label for="deal_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label><textarea id="deal_address" name="address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('address', $draft['address'] ?? '') }}</textarea></div>
                                <div><label for="deal_company_name" class="mb-1 block text-sm font-medium text-gray-700">Company</label><input id="deal_company_name" name="company_name" value="{{ old('company_name', $draft['company_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="deal_position" class="mb-1 block text-sm font-medium text-gray-700">Position / Designation</label><input id="deal_position" name="position" value="{{ old('position', $draft['position'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div class="sm:col-span-2"><label for="deal_company_address" class="mb-1 block text-sm font-medium text-gray-700">Company Address</label><textarea id="deal_company_address" name="company_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('company_address', $draft['company_address'] ?? '') }}</textarea></div>
                            </div>
                        </section>
                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Service Identification</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Service Area</label>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach ($serviceAreaOptions as $option)
                                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                                <input type="checkbox" name="service_area_options[]" value="{{ $option }}" @checked(in_array($option, $selectedServiceAreas, true)) @if ($option === 'Others') data-other-target="deal_service_area_other_wrap" @endif class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="deal_service_area_other_wrap" class="{{ in_array('Others', $selectedServiceAreas, true) ? '' : 'hidden' }} mt-3">
                                        <label for="service_area_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Indicate Areas)</label>
                                        <input id="service_area_other" name="service_area_other" value="{{ old('service_area_other', $draft['service_area_other'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label class="block text-sm font-medium text-gray-700">Services</label>
                                    <div class="grid gap-4 lg:grid-cols-2">
                                        @foreach ($serviceGroups as $group => $options)
                                            <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-3">
                                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600">{{ $group }}</p>
                                                <div class="space-y-2">
                                                    @foreach ($options as $option)
                                                        <label class="flex items-start gap-2 text-sm text-gray-700">
                                                            <input type="checkbox" name="service_options[]" value="{{ $option }}" @checked(in_array($option, $selectedServices, true)) class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                            <span>{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <label for="services_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Indicate Services)</label>
                                        <input id="services_other" name="services_other" value="{{ old('services_other', $draft['services_other'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Products</h3>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach ($productOptions as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="checkbox" name="product_options[]" value="{{ $option }}" @checked(in_array($option, $selectedProducts, true)) @if ($option === 'Others') data-other-target="deal_products_other_wrap" @endif class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="deal_products_other_wrap" class="{{ in_array('Others', $selectedProducts, true) ? '' : 'hidden' }} mt-3">
                                <label for="products_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Indicate Product)</label>
                                <input id="products_other" name="products_other" value="{{ old('products_other', $draft['products_other'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div class="mt-3">
                                <label for="scope_of_work" class="mb-1 block text-sm font-medium text-gray-700">Scope of Work</label>
                                <textarea id="scope_of_work" name="scope_of_work" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('scope_of_work', $draft['scope_of_work'] ?? '') }}</textarea>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Engagement Type</h3>
                            <div class="mt-3 grid gap-2 sm:grid-cols-3">
                                @foreach (['Project Engagement', 'Regular (Retainer) Engagement', 'Hybrid Engagement'] as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="radio" name="engagement_type" value="{{ $option }}" @checked(old('engagement_type', $draft['engagement_type'] ?? '') === $option) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Client Requirements</h3>
                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-full border border-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                                        <tr>
                                            <th class="border border-gray-200 px-3 py-2 text-left">Requirement</th>
                                            <th class="border border-gray-200 px-3 py-2 text-center">Provided</th>
                                            <th class="border border-gray-200 px-3 py-2 text-center">Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requirementRows as $key => $label)
                                            @php
                                                $current = old(
                                                    "requirements_status.$key",
                                                    data_get($draft, "requirements_status_map.$key", data_get($draft, "requirements_status.$key"))
                                                );
                                            @endphp
                                            <tr>
                                                <td class="border border-gray-200 px-3 py-2 text-gray-700">{{ $label }}</td>
                                                <td class="border border-gray-200 px-3 py-2 text-center">
                                                    <input type="radio" name="requirements_status[{{ $key }}]" value="provided" @checked($current === 'provided') class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                                </td>
                                                <td class="border border-gray-200 px-3 py-2 text-center">
                                                    <input type="radio" name="requirements_status[{{ $key }}]" value="pending" @checked($current === 'pending') class="h-4 w-4 border-gray-300 text-amber-600 focus:ring-amber-500">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Required Actions</h3>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach ($requiredActions as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="checkbox" name="required_actions_options[]" value="{{ $option }}" @checked(in_array($option, $selectedRequiredActions, true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <label for="required_actions_other" class="mb-1 block text-sm font-medium text-gray-700">Other Internal Requirements</label>
                                <textarea id="required_actions_other" name="required_actions_other" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('required_actions_other', $draft['required_actions_other'] ?? '') }}</textarea>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Fees</h3>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <div><label for="estimated_professional_fee" class="mb-1 block text-sm font-medium text-gray-700">Estimated Professional Fee</label><input id="estimated_professional_fee" name="estimated_professional_fee" value="{{ old('estimated_professional_fee', $draft['estimated_professional_fee'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="estimated_government_fees" class="mb-1 block text-sm font-medium text-gray-700">Estimated Government Fees</label><input id="estimated_government_fees" name="estimated_government_fees" value="{{ old('estimated_government_fees', $draft['estimated_government_fees'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="estimated_service_support_fee" class="mb-1 block text-sm font-medium text-gray-700">Estimated Service Support Fee</label><input id="estimated_service_support_fee" name="estimated_service_support_fee" value="{{ old('estimated_service_support_fee', $draft['estimated_service_support_fee'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="total_estimated_engagement_value" class="mb-1 block text-sm font-medium text-gray-700">Total Estimated Engagement Value</label><input id="total_estimated_engagement_value" name="total_estimated_engagement_value" value="{{ old('total_estimated_engagement_value', $draft['total_estimated_engagement_value'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Payment Terms</h3>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach (['Full Payment Before Service', '50% Downpayment / 50% Completion', 'Milestone-Based Payment', 'Monthly Retainer', 'Others'] as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="radio" name="payment_terms" value="{{ $option }}" @checked(old('payment_terms', $draft['payment_terms'] ?? '') === $option) @if ($option === 'Others') data-other-target="deal_payment_terms_other_wrap" @endif class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="deal_payment_terms_other_wrap" class="{{ old('payment_terms', $draft['payment_terms'] ?? '') === 'Others' ? '' : 'hidden' }} mt-3">
                                <label for="payment_terms_other" class="mb-1 block text-sm font-medium text-gray-700">Other Payment Terms</label>
                                <input id="payment_terms_other" name="payment_terms_other" value="{{ old('payment_terms_other', $draft['payment_terms_other'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </section>
                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Estimated Timeline</h3>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <div><label for="planned_start_date" class="mb-1 block text-sm font-medium text-gray-700">Planned Start Date</label><input id="planned_start_date" type="date" name="planned_start_date" value="{{ old('planned_start_date', $draft['planned_start_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="estimated_duration" class="mb-1 block text-sm font-medium text-gray-700">Estimated Duration (Days)</label><input id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $draft['estimated_duration'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="estimated_completion_date" class="mb-1 block text-sm font-medium text-gray-700">Estimated Completion Date</label><input id="estimated_completion_date" type="date" name="estimated_completion_date" value="{{ old('estimated_completion_date', $draft['estimated_completion_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="client_preferred_completion_date" class="mb-1 block text-sm font-medium text-gray-700">Client Preferred Completion Date</label><input id="client_preferred_completion_date" type="date" name="client_preferred_completion_date" value="{{ old('client_preferred_completion_date', $draft['client_preferred_completion_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="confirmed_delivery_date" class="mb-1 block text-sm font-medium text-gray-700">Confirmed Delivery Date</label><input id="confirmed_delivery_date" type="date" name="confirmed_delivery_date" value="{{ old('confirmed_delivery_date', $draft['confirmed_delivery_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div class="sm:col-span-2"><label for="timeline_notes" class="mb-1 block text-sm font-medium text-gray-700">Timeline Notes</label><textarea id="timeline_notes" name="timeline_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('timeline_notes', $draft['timeline_notes'] ?? '') }}</textarea></div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Service Complexity Assessment</h3>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                @foreach (['Standard Service', 'Complex Case'] as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="radio" name="service_complexity" value="{{ $option }}" @checked(old('service_complexity', $draft['service_complexity'] ?? '') === $option) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <label class="mt-4 mb-2 block text-sm font-medium text-gray-700">Professional Support Required</label>
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach (['Requires Senior Consultant', 'Requires Subject Matter Expert', 'Requires Lawyer / Legal Counsel', 'Requires CPA / Certified Public Accountant'] as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="checkbox" name="support_required_options[]" value="{{ $option }}" @checked(in_array($option, $selectedSupportRequired, true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <label for="complexity_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes / Explanation</label>
                                <textarea id="complexity_notes" name="complexity_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('complexity_notes', $draft['complexity_notes'] ?? '') }}</textarea>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Proposal Decision</h3>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach (['Prepare Proposal', 'Prepare Engagement Letter', 'Schedule Client Consultation', 'Request Additional Documents', 'Decline Engagement'] as $option)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                        <input type="radio" name="proposal_decision" value="{{ $option }}" @checked(old('proposal_decision', $draft['proposal_decision'] ?? '') === $option) @if ($option === 'Decline Engagement') data-other-target="deal_decline_reason_wrap" @endif class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="deal_decline_reason_wrap" class="{{ old('proposal_decision', $draft['proposal_decision'] ?? '') === 'Decline Engagement' ? '' : 'hidden' }} mt-3">
                                <label for="decline_reason" class="mb-1 block text-sm font-medium text-gray-700">Reason (if declined)</label>
                                <textarea id="decline_reason" name="decline_reason" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('decline_reason', $draft['decline_reason'] ?? '') }}</textarea>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Internal Assignment</h3>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <div><label for="assigned_consultant" class="mb-1 block text-sm font-medium text-gray-700">Assigned Consultant</label><input id="assigned_consultant" name="assigned_consultant" value="{{ old('assigned_consultant', $draft['assigned_consultant'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="assigned_associate" class="mb-1 block text-sm font-medium text-gray-700">Assigned Associate</label><input id="assigned_associate" name="assigned_associate" value="{{ old('assigned_associate', $draft['assigned_associate'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div class="sm:col-span-2"><label for="service_department_unit" class="mb-1 block text-sm font-medium text-gray-700">Service Department / Unit</label><input id="service_department_unit" name="service_department_unit" value="{{ old('service_department_unit', $draft['service_department_unit'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Notes</h3>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <div><label for="consultant_notes" class="mb-1 block text-sm font-medium text-gray-700">Consultant Notes</label><textarea id="consultant_notes" name="consultant_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('consultant_notes', $draft['consultant_notes'] ?? '') }}</textarea></div>
                                <div><label for="associate_notes" class="mb-1 block text-sm font-medium text-gray-700">Associate Notes</label><textarea id="associate_notes" name="associate_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('associate_notes', $draft['associate_notes'] ?? '') }}</textarea></div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-gray-200 p-4">
                            <h3 class="text-base font-semibold text-gray-900">Internal Approval</h3>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <div><label for="prepared_by" class="mb-1 block text-sm font-medium text-gray-700">Prepared By</label><input id="prepared_by" name="prepared_by" value="{{ old('prepared_by', $draft['prepared_by'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="reviewed_by" class="mb-1 block text-sm font-medium text-gray-700">Reviewed By</label><input id="reviewed_by" name="reviewed_by" value="{{ old('reviewed_by', $draft['reviewed_by'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="internal_name" class="mb-1 block text-sm font-medium text-gray-700">Name</label><input id="internal_name" name="internal_name" value="{{ old('internal_name', $draft['internal_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="internal_date" class="mb-1 block text-sm font-medium text-gray-700">Date</label><input id="internal_date" type="date" name="internal_date" value="{{ old('internal_date', $draft['internal_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div class="sm:col-span-2"><label for="client_fullname_signature" class="mb-1 block text-sm font-medium text-gray-700">Client Fullname & Signature</label><input id="client_fullname_signature" name="client_fullname_signature" value="{{ old('client_fullname_signature', $draft['client_fullname_signature'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="referred_closed_by" class="mb-1 block text-sm font-medium text-gray-700">Referred By / Closed By</label><input id="referred_closed_by" name="referred_closed_by" value="{{ old('referred_closed_by', $draft['referred_closed_by'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="internal_sales_marketing" class="mb-1 block text-sm font-medium text-gray-700">Sales & Marketing</label><input id="internal_sales_marketing" name="internal_sales_marketing" value="{{ old('internal_sales_marketing', $draft['internal_sales_marketing'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="lead_consultant" class="mb-1 block text-sm font-medium text-gray-700">Lead Consultant</label><input id="lead_consultant" name="lead_consultant" value="{{ old('lead_consultant', $draft['lead_consultant'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="lead_associate_assigned" class="mb-1 block text-sm font-medium text-gray-700">Lead Associate Assigned</label><input id="lead_associate_assigned" name="lead_associate_assigned" value="{{ old('lead_associate_assigned', $draft['lead_associate_assigned'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="internal_finance" class="mb-1 block text-sm font-medium text-gray-700">Finance</label><input id="internal_finance" name="internal_finance" value="{{ old('internal_finance', $draft['internal_finance'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                <div><label for="internal_president" class="mb-1 block text-sm font-medium text-gray-700">President</label><input id="internal_president" name="internal_president" value="{{ old('internal_president', $draft['internal_president'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            </div>
                        </section>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                </div>

                <div class="mt-auto flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                    <button id="cancelCreateDealModal" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button id="saveDealBtn" type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">{{ $submitLabel }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('createDealModal');
    const panel = document.getElementById('createDealPanel');
    const overlay = document.getElementById('createDealModalOverlay');
    const openBtn = document.getElementById('openCreateDealModalBtn');
    const closeBtn = document.getElementById('closeCreateDealModal');
    const cancelBtn = document.getElementById('cancelCreateDealModal');
    const ownerTrigger = document.getElementById('dealOwnerDropdownTrigger');
    const ownerMenu = document.getElementById('dealOwnerDropdownMenu');
    const ownerSearch = document.getElementById('dealOwnerSearch');
    const ownerInput = document.getElementById('deal_selected_owner_id');
    const ownerLabel = document.getElementById('dealOwnerSelectedLabel');
    const ownerOptions = Array.from(document.querySelectorAll('.deal-owner-option'));
    const contactSearch = document.getElementById('dealContactSearch');
    const contactResults = document.getElementById('dealContactResults');
    const contactIdInput = document.getElementById('deal_selected_contact_id');
    const dependentSections = document.getElementById('dealDependentSections');
    const requiredMessage = document.getElementById('dealContactRequiredMessage');
    const saveBtn = document.getElementById('saveDealBtn');
    const feeInputs = [
        document.getElementById('estimated_professional_fee'),
        document.getElementById('estimated_government_fees'),
        document.getElementById('estimated_service_support_fee'),
    ].filter(Boolean);
    const totalInput = document.getElementById('total_estimated_engagement_value');
    const contactRecords = @json($contactRecords);

    const fieldMap = {
        salutation: 'deal_salutation',
        first_name: 'deal_first_name',
        middle_initial: 'deal_middle_initial',
        last_name: 'deal_last_name',
        name_extension: 'deal_name_extension',
        sex: 'deal_sex',
        date_of_birth: 'deal_date_of_birth',
        email: 'deal_email',
        mobile: 'deal_mobile',
        address: 'deal_address',
        company_name: 'deal_company_name',
        company_address: 'deal_company_address',
        position: 'deal_position',
    };

    const closeOwnerMenu = () => ownerMenu?.classList.add('hidden');

    const openModal = () => {
        if (!modal || !panel) {
            return;
        }
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        document.dispatchEvent(new CustomEvent('deal-drawer:opened'));
        requestAnimationFrame(() => {
            overlay?.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
        });
    };

    const closeModal = () => {
        if (!modal || !panel) {
            return;
        }
        closeOwnerMenu();
        contactResults?.classList.add('hidden');
        overlay?.classList.add('opacity-0');
        panel.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        document.dispatchEvent(new CustomEvent('deal-drawer:closed'));
        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        }, 300);
    };

    const normalizeCurrency = (value) => {
        const cleaned = String(value || '').replace(/,/g, '').trim();
        const parsed = Number.parseFloat(cleaned);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

    const updateEstimatedTotal = () => {
        if (!totalInput) {
            return;
        }
        const total = feeInputs.reduce((sum, field) => sum + normalizeCurrency(field.value), 0);
        if (total > 0) {
            totalInput.value = total.toFixed(2);
        } else if (!totalInput.dataset.manualEntry || totalInput.dataset.manualEntry !== 'true') {
            totalInput.value = '';
        }
    };

    const setDependentDisabled = (isDisabled) => {
        if (!dependentSections) {
            return;
        }
        dependentSections.classList.toggle('opacity-60', isDisabled);
        dependentSections.classList.toggle('pointer-events-none', isDisabled);
        requiredMessage?.classList.toggle('hidden', !isDisabled);
        if (saveBtn) {
            saveBtn.disabled = isDisabled;
            saveBtn.classList.toggle('opacity-60', isDisabled);
            saveBtn.classList.toggle('cursor-not-allowed', isDisabled);
        }
    };

    const applyOtherFieldToggles = () => {
        const toggleInputs = Array.from(document.querySelectorAll('[data-other-target]'));
        const targets = [...new Set(toggleInputs.map((item) => item.dataset.otherTarget).filter(Boolean))];
        targets.forEach((targetId) => {
            const target = document.getElementById(targetId);
            if (!target) {
                return;
            }
            const visible = toggleInputs.some((item) => item.dataset.otherTarget === targetId && item.checked);
            target.classList.toggle('hidden', !visible);
        });
    };

    const setFieldValue = (fieldId, value) => {
        const field = document.getElementById(fieldId);
        if (!field) {
            return;
        }
        if (field.tagName === 'SELECT') {
            const hasOption = Array.from(field.options).some((option) => option.value === (value || ''));
            field.value = hasOption ? (value || '') : '';
            return;
        }
        field.value = value || '';
    };

    const renderContactResults = (keyword) => {
        if (!contactResults) {
            return;
        }
        const query = keyword.trim().toLowerCase();
        const matches = contactRecords
            .filter((record) => {
                if (query === '') {
                    return true;
                }
                const blob = (record.search_blob || '').toLowerCase();
                const label = (record.label || '').toLowerCase();
                return blob.includes(query) || label.includes(query);
            })
            .slice(0, 30);

        if (matches.length === 0) {
            contactResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">No matching contacts found.</div>';
            contactResults.classList.remove('hidden');
            return;
        }

        contactResults.innerHTML = matches.map((record) => {
            const safeLabel = record.label || 'Unnamed contact';
            const safeCompany = record.company_name || '-';
            const safeEmail = record.email || '-';
            const safeMobile = record.mobile || '-';
            return `
                <button type="button" class="deal-contact-result block w-full rounded-md px-3 py-2 text-left hover:bg-blue-50" data-contact-id="${record.id}">
                    <p class="text-sm font-medium text-gray-800">${safeLabel}</p>
                    <p class="text-xs text-gray-500">${safeCompany}</p>
                    <p class="text-[11px] text-gray-400">${safeEmail} · ${safeMobile}</p>
                </button>
            `;
        }).join('');

        contactResults.classList.remove('hidden');

        Array.from(contactResults.querySelectorAll('.deal-contact-result')).forEach((button) => {
            button.addEventListener('click', () => {
                const selectedId = Number.parseInt(button.dataset.contactId || '', 10);
                const record = contactRecords.find((item) => Number(item.id) === selectedId);
                if (!record) {
                    return;
                }

                contactIdInput.value = String(record.id);
                contactSearch.value = record.label || '';
                setFieldValue('deal_first_name', record.first_name || '');
                setFieldValue('deal_last_name', record.last_name || '');
                setFieldValue('deal_middle_initial', record.middle_initial || (record.middle_name ? String(record.middle_name).charAt(0) : ''));

                Object.entries(fieldMap).forEach(([key, fieldId]) => {
                    if (['first_name', 'middle_initial', 'last_name'].includes(key)) {
                        return;
                    }
                    setFieldValue(fieldId, record[key] || '');
                });

                if (!document.querySelector('input[name="customer_type"]:checked') && record.customer_type) {
                    const normalized = String(record.customer_type).toLowerCase().includes('individual') ? 'individual' : 'business';
                    const radio = document.querySelector(`input[name="customer_type"][value="${normalized}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                }

                contactResults.classList.add('hidden');
                setDependentDisabled(false);
            });
        });
    };

    ownerTrigger?.addEventListener('click', () => {
        ownerMenu?.classList.toggle('hidden');
        if (ownerMenu && !ownerMenu.classList.contains('hidden')) {
            ownerSearch?.focus();
        }
    });

    ownerSearch?.addEventListener('input', () => {
        const keyword = ownerSearch.value.trim().toLowerCase();
        ownerOptions.forEach((option) => {
            const name = (option.dataset.ownerName || '').toLowerCase();
            const email = (option.dataset.ownerEmail || '').toLowerCase();
            const matches = keyword === '' || name.includes(keyword) || email.includes(keyword);
            option.classList.toggle('hidden', !matches);
        });
    });

    ownerOptions.forEach((option) => {
        option.addEventListener('click', () => {
            ownerInput.value = option.dataset.ownerId || '';
            ownerLabel.textContent = `Owner: ${option.dataset.ownerName || ''}`;
            closeOwnerMenu();
        });
    });

    contactSearch?.addEventListener('focus', () => renderContactResults(contactSearch.value));
    contactSearch?.addEventListener('input', () => renderContactResults(contactSearch.value));

    feeInputs.forEach((field) => field.addEventListener('input', updateEstimatedTotal));
    totalInput?.addEventListener('input', () => {
        totalInput.dataset.manualEntry = 'true';
    });

    Array.from(document.querySelectorAll('[data-other-target]')).forEach((input) => {
        input.addEventListener('change', applyOtherFieldToggles);
    });

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    document.addEventListener('click', (event) => {
        if (ownerMenu && !ownerMenu.classList.contains('hidden')) {
            if (!ownerMenu.contains(event.target) && !ownerTrigger?.contains(event.target)) {
                closeOwnerMenu();
            }
        }

        if (contactResults && !contactResults.classList.contains('hidden')) {
            if (!contactResults.contains(event.target) && !contactSearch?.contains(event.target)) {
                contactResults.classList.add('hidden');
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    setDependentDisabled(!contactIdInput?.value);
    applyOtherFieldToggles();
    updateEstimatedTotal();

    @if ($openDealModal || $errors->any())
        openModal();
    @endif
});
</script>

