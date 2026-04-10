@php
    $intakeServiceInquiryOptions = [
        'Business Registration / Entity Formation',
        'Business Permit (New / Renewal)',
        'Tax Compliance / BIR Filing',
        'Accounting / Bookkeeping',
        'Financial Statements Preparation',
        'Corporate Officers Services',
        'Business Advisory / Consultation',
        'Regulatory Compliance',
        'Other',
    ];
    $intakeRecommendationOptions = [
        'Proceed to Proposal Preparation',
        'Refer to Senior Consultant',
        'Refer to Subject Matter Expert',
        'For Further Study / Assessment',
        'For Due Diligence / Background Check',
        'Schedule Consultation Meeting',
        'Request Additional Information from Client',
        'Not Suitable for Engagement',
        'Others',
    ];
    $intakeLeadSourceOptions = [
        'Facebook', 'Instagram', 'LinkedIn', 'Tiktok', 'Website', 'Google Search',
        'Google Ads', 'Walk-In', 'Referral-Client', 'Referral-Partner', 'Referral-Employee',
        'Email Inquiry', 'Phone Call', 'SMS/Viber', 'WhatsApp', 'Online Market Place',
        'Event Seminar', 'Webinar', 'Trade Show Expo', 'Flyer / Brochure',
        'Radio Advertisement', 'Returning Client', 'Influencer / Content Creator',
        'Television Advertisement', 'Other',
    ];
    $intakeLeadStageOptions = ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'];
    $intakeServiceTypes = old('_from_contact_intake_edit') ? old('service_inquiry_types', []) : ($contact->service_inquiry_types ?? []);
    $intakeRecommendations = old('_from_contact_intake_edit') ? old('recommendation_options', []) : ($contact->recommendation_options ?? []);
    $intakeLeadSources = old('_from_contact_intake_edit') ? old('lead_source_channels', []) : ($contact->lead_source_channels ?? []);
@endphp

<x-slide-over id="contactIntakeModal" width="sm:max-w-[720px]">
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">View KYC Form</h2>
                <p class="mt-1 text-sm text-gray-500">Review the saved contact intake data. Switch to edit mode to update the full intake form.</p>
            </div>
            <button type="button" data-close-contact-intake-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form id="contactIntakeForm" method="POST" action="{{ route('contacts.update', $contact->id) }}" class="flex min-h-0 flex-1 flex-col">
        @csrf
        @method('PUT')
        <input type="hidden" name="_from_contact_intake_edit" value="1">
        <input type="hidden" name="owner_id" value="{{ old('owner_id', $contact->owner_id ?? '') }}">

        <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="space-y-4 border-b border-gray-100 pb-5">
                <div>
                    <p class="text-sm font-medium text-gray-500">Client Intake</p>
                    <p class="text-xs text-gray-400">Use this as the main contact and business record.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 sm:items-end">
                    <div>
                        <label for="intake_business_date" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Business Date</label>
                        <input id="intake_business_date" type="date" name="business_date" value="{{ old('business_date', $contact->business_date ? \Illuminate\Support\Carbon::parse($contact->business_date)->format('Y-m-d') : null) }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Owner</label>
                        <input type="text" value="{{ $contact->owner_name ?: 'Admin User' }}" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created At</label>
                        <input type="text" value="{{ optional($contact->created_at)->format('F j, Y • g:i A') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Customer Type</h3>
                <p class="mb-4 text-xs text-gray-500">Classify the record before entering the full client profile.</p>
                <div class="space-y-3">
                    <div class="grid items-center gap-2 sm:grid-cols-[72px_1fr]">
                        <label class="text-sm font-medium text-gray-700">Type</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['business' => 'Business', 'individual' => 'Individual'] as $value => $label)
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                    <input type="radio" name="customer_type" value="{{ $value }}" @checked(old('customer_type', $contact->customer_type) === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Contact Information</h3>
                <p class="mb-4 text-xs text-gray-500">Primary contact and profile details from the client form.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="intake_salutation" class="mb-1 block text-sm font-medium text-gray-700">Salutation</label><select id="intake_salutation" name="salutation" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="" disabled @selected(blank(old('salutation', $contact->salutation)))>Select salutation</option>@foreach (['Mr.', 'Ms.', 'Mrs.', 'Atty.', 'CPA', 'Engr.', 'Dr.'] as $option)<option value="{{ $option }}" @selected(old('salutation', $contact->salutation) === $option)>{{ $option }}</option>@endforeach</select></div>
                    <div><label for="intake_sex" class="mb-1 block text-sm font-medium text-gray-700">Sex</label><select id="intake_sex" name="sex" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="" disabled @selected(blank(old('sex', $contact->sex)))>Select sex</option>@foreach (['Male', 'Female', 'Prefer not to say'] as $option)<option value="{{ $option }}" @selected(old('sex', $contact->sex) === $option)>{{ $option }}</option>@endforeach</select></div>
                    <div><label for="intake_first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label><input id="intake_first_name" name="first_name" required value="{{ old('first_name', $contact->first_name) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">@error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label for="intake_middle_initial" class="mb-1 block text-sm font-medium text-gray-700">Middle Initial</label><input id="intake_middle_initial" name="middle_initial" value="{{ old('middle_initial', $contact->middle_initial) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_middle_name" class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label><input id="intake_middle_name" name="middle_name" value="{{ old('middle_name', $contact->middle_name) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label><input id="intake_last_name" name="last_name" required value="{{ old('last_name', $contact->last_name) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">@error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label for="intake_name_extension" class="mb-1 block text-sm font-medium text-gray-700">Name Extension</label><input id="intake_name_extension" name="name_extension" value="{{ old('name_extension', $contact->name_extension) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_date_of_birth" class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label><input id="intake_date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $contact->date_of_birth ? \Illuminate\Support\Carbon::parse($contact->date_of_birth)->format('Y-m-d') : null) }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_email" class="mb-1 block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label><input id="intake_email" name="email" type="email" required value="{{ old('email', $contact->email) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">@error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label for="intake_mobile_number" class="mb-1 block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label><input id="intake_mobile_number" name="mobile_number" required value="{{ old('mobile_number', $contact->phone) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">@error('mobile_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label for="intake_contact_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label><textarea id="intake_contact_address" name="contact_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('contact_address', $contact->contact_address) }}</textarea></div>
                    <div><label for="intake_company_name" class="mb-1 block text-sm font-medium text-gray-700">Company</label><input id="intake_company_name" name="company_name" value="{{ old('company_name', $contact->company_name) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_position" class="mb-1 block text-sm font-medium text-gray-700">Position / Designation</label><input id="intake_position" name="position" value="{{ old('position', $contact->position) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2"><label for="intake_company_address" class="mb-1 block text-sm font-medium text-gray-700">Company Address</label><textarea id="intake_company_address" name="company_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('company_address', $contact->company_address) }}</textarea></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Information</h3>
                <p class="mb-4 text-xs text-gray-500">Capture ownership, structure, and business capacity details.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="intake_business_type_organization" class="mb-1 block text-sm font-medium text-gray-700">Business Type / Organization</label><input id="intake_business_type_organization" name="business_type_organization" value="{{ old('business_type_organization', $contact->business_type_organization) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Organization Structure</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            @foreach (['Sole Proprietorship', 'Partnership', 'Non-Stock', 'Corporation', 'Stock', 'Others'] as $option)
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700"><input type="radio" name="organization_type" value="{{ $option }}" @checked(old('organization_type', $contact->organization_type) === $option) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"><span>{{ $option }}</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div id="intakeOrganizationTypeOtherWrap" class="{{ old('organization_type', $contact->organization_type) === 'Others' ? '' : 'hidden' }} sm:col-span-2"><label for="intake_organization_type_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Specify)</label><input id="intake_organization_type_other" name="organization_type_other" value="{{ old('organization_type_other', $contact->organization_type_other) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_nature_of_business" class="mb-1 block text-sm font-medium text-gray-700">Nature of Business / Industry</label><input id="intake_nature_of_business" name="nature_of_business" value="{{ old('nature_of_business', $contact->nature_of_business) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_capitalization_amount" class="mb-1 block text-sm font-medium text-gray-700">Capitalization / Capital Investment</label><input id="intake_capitalization_amount" name="capitalization_amount" value="{{ old('capitalization_amount', $contact->capitalization_amount) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_ownership_structure" class="mb-1 block text-sm font-medium text-gray-700">Ownership Structure</label><input id="intake_ownership_structure" name="ownership_structure" value="{{ old('ownership_structure', $contact->ownership_structure) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_previous_year_revenue" class="mb-1 block text-sm font-medium text-gray-700">Previous Year Total Sales / Revenue</label><input id="intake_previous_year_revenue" name="previous_year_revenue" value="{{ old('previous_year_revenue', $contact->previous_year_revenue) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_years_operating" class="mb-1 block text-sm font-medium text-gray-700">How Long the Business Has Been Operating</label><input id="intake_years_operating" name="years_operating" value="{{ old('years_operating', $contact->years_operating) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_projected_current_year_revenue" class="mb-1 block text-sm font-medium text-gray-700">Projected Sales / Revenue for the Current Year</label><input id="intake_projected_current_year_revenue" name="projected_current_year_revenue" value="{{ old('projected_current_year_revenue', $contact->projected_current_year_revenue) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Ownership Nationality</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            @foreach (['100% Filipino-Owned', 'With Foreign Ownership', 'Foreign-Owned Business'] as $option)
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700"><input type="radio" name="ownership_flag" value="{{ $option }}" @checked(old('ownership_flag', $contact->ownership_flag) === $option) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"><span>{{ $option }}</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div id="intakeForeignBusinessNatureWrap" class="{{ old('ownership_flag', $contact->ownership_flag) === 'Foreign-Owned Business' ? '' : 'hidden' }} sm:col-span-2"><label for="intake_foreign_business_nature" class="mb-1 block text-sm font-medium text-gray-700">Foreign-Owned Business (Specify Nature of Business)</label><textarea id="intake_foreign_business_nature" name="foreign_business_nature" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('foreign_business_nature', $contact->foreign_business_nature) }}</textarea></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Service Inquiry Type</h3>
                <p class="mb-4 text-xs text-gray-500">Select one or more service inquiry categories.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($intakeServiceInquiryOptions as $option)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="service_inquiry_types[]" value="{{ $option }}" @checked(in_array($option, $intakeServiceTypes, true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
                <div id="intakeServiceInquiryOtherWrap" class="{{ in_array('Other', $intakeServiceTypes, true) ? '' : 'hidden' }} mt-3"><label for="intake_service_inquiry_other" class="mb-1 block text-sm font-medium text-gray-700">Other Service Inquiry</label><input id="intake_service_inquiry_other" name="service_inquiry_other" value="{{ old('service_inquiry_other', $contact->service_inquiry_other) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4"><h3 class="text-base font-semibold text-gray-900">Inquiry</h3><p class="mb-4 text-xs text-gray-500">Add the client's inquiry details.</p><textarea id="intake_inquiry" name="inquiry" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('inquiry', $contact->inquiry) }}</textarea></section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">For JKNC Use Only</h3>
                <p class="mb-4 text-xs text-gray-500">Internal notes and assignment details.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="intake_jknc_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label><textarea id="intake_jknc_notes" name="jknc_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('jknc_notes', $contact->jknc_notes) }}</textarea></div>
                    <div class="sm:col-span-2"><label for="intake_sales_marketing" class="mb-1 block text-sm font-medium text-gray-700">Sales & Marketing</label><textarea id="intake_sales_marketing" name="sales_marketing" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('sales_marketing', $contact->sales_marketing) }}</textarea></div>
                    <div><label for="intake_consultant_lead" class="mb-1 block text-sm font-medium text-gray-700">Consultant Lead</label><input id="intake_consultant_lead" name="consultant_lead" value="{{ old('consultant_lead', $contact->consultant_lead) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_lead_associate" class="mb-1 block text-sm font-medium text-gray-700">Lead Associate</label><input id="intake_lead_associate" name="lead_associate" value="{{ old('lead_associate', $contact->lead_associate) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Recommendation</h3>
                <p class="mb-4 text-xs text-gray-500">Choose one or more recommended next actions.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($intakeRecommendationOptions as $option)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="recommendation_options[]" value="{{ $option }}" @checked(in_array($option, $intakeRecommendations, true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
                <div id="intakeRecommendationOtherWrap" class="{{ in_array('Others', $intakeRecommendations, true) ? '' : 'hidden' }} mt-3"><label for="intake_recommendation_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Specify)</label><input id="intake_recommendation_other" name="recommendation_other" value="{{ old('recommendation_other', $contact->recommendation_other) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Lead Source</h3>
                <p class="mb-4 text-xs text-gray-500">Track all channels that generated this lead.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($intakeLeadSourceOptions as $option)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="lead_source_channels[]" value="{{ $option }}" @checked(in_array($option, $intakeLeadSources, true)) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
                <div id="intakeLeadSourceOtherWrap" class="{{ in_array('Other', $intakeLeadSources, true) ? '' : 'hidden' }} mt-3"><label for="intake_lead_source_other" class="mb-1 block text-sm font-medium text-gray-700">Other Lead Source</label><input id="intake_lead_source_other" name="lead_source_other" value="{{ old('lead_source_other', $contact->lead_source_other) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Referral Information</h3>
                <p class="mb-4 text-xs text-gray-500">Who referred this client or lead source details.</p>
                <div>
                    <label for="intake_referred_by" class="mb-1 block text-sm font-medium text-gray-700">Referred By</label>
                    <input id="intake_referred_by" name="referred_by" value="{{ old('referred_by', $contact->referred_by) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Lead Stage</h3>
                <p class="mb-4 text-xs text-gray-500">Current stage of the lead in the pipeline.</p>
                <div>
                    <label for="intake_lead_stage" class="mb-1 block text-sm font-medium text-gray-700">Lead Stage</label>
                    <select id="intake_lead_stage" name="lead_stage" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        @foreach ($intakeLeadStageOptions as $option)
                            <option value="{{ $option }}" @selected(old('lead_stage', $contact->lead_stage ?: 'Inquiry') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </section>

            @if ($errors->any() && old('_from_contact_intake_edit'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
        </div>

        <div class="mt-auto flex items-center justify-between gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
            <button id="contactIntakeEditBtn" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Edit</button>
            <div class="flex items-center gap-3">
                <button id="cancelContactIntakeModal" type="button" data-close-contact-intake-modal class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button>
                <button id="contactIntakeCancelBtn" type="button" class="hidden h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button id="contactIntakeSaveBtn" type="submit" class="hidden h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save</button>
            </div>
        </div>
    </form>
</x-slide-over>
