<div id="createDealModal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <button id="createDealModalBackdrop" type="button" aria-label="Close create deal panel" class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
    <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
        <div id="createDealPanel" class="pointer-events-auto flex h-full w-full max-w-[780px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out sm:max-w-[740px]">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5 sm:px-8">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Create Deal</h2>
                    <p class="mt-1 text-sm text-gray-500">Select an existing contact or business first, then complete deal-specific details.</p>
                </div>
                <button id="closeCreateDealModalBtn" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
            </div>
            <form id="createDealForm" method="POST" action="{{ route('deals.store') }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input id="dealContactId" type="hidden" name="contact_id" value="{{ old('contact_id', $dealDraft['contact_id'] ?? '') }}">
                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 pb-6 pt-5 sm:px-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Contact First, Deal Second</p>
                            <p class="text-xs text-gray-400">Shared client data stays editable before you save the deal.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Owner</span>
                            <button type="button" class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-700"><span class="mr-1 inline-block h-2 w-2 rounded-full bg-blue-500"></span><span class="max-w-[130px] truncate">{{ $ownerLabel }}</span></button>
                        </div>
                    </div>

                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">1. Select Contact or Business</h3>
                            <p class="text-xs text-gray-500">Search by contact name, company, email, or mobile.</p>
                        </div>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3.5 text-xs text-gray-400"></i>
                            <input id="dealContactSearch" type="text" autocomplete="off" value="{{ old('contact_selector_label', $dealDraft['contact_selector_label'] ?? '') }}" placeholder="Search existing contact or business" class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-9 pr-10 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <button id="dealSelectorToggle" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="fas fa-chevron-down text-xs"></i></button>
                            <div id="dealSelectorMenu" class="absolute left-0 right-0 top-[calc(100%+6px)] z-20 hidden max-h-64 overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-xl"></div>
                        </div>
                        <p id="selectedContactSummary" class="mt-3 hidden rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-700"></p>
                        <p id="contactSelectionWarning" class="mt-3 hidden rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">Select an existing contact or business before saving this deal.</p>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">2. Shared Contact Snapshot</h3>
                                <p class="text-xs text-gray-500">These fields auto-fill from the selected contact and remain editable.</p>
                            </div>
                            <span class="rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-700">Auto-filled</span>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="dealCustomerType" class="mb-1 block text-sm font-medium text-gray-700">Customer Type</label><input id="dealCustomerType" name="customer_type" value="{{ old('customer_type', $dealDraft['customer_type'] ?? '') }}" data-contact-field="customer_type" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealSalutation" class="mb-1 block text-sm font-medium text-gray-700">Salutation</label><input id="dealSalutation" name="salutation" value="{{ old('salutation', $dealDraft['salutation'] ?? '') }}" data-contact-field="salutation" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealFirstName" class="mb-1 block text-sm font-medium text-gray-700">First Name</label><input id="dealFirstName" name="first_name" value="{{ old('first_name', $dealDraft['first_name'] ?? '') }}" data-contact-field="first_name" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealMiddleName" class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label><input id="dealMiddleName" name="middle_name" value="{{ old('middle_name', $dealDraft['middle_name'] ?? '') }}" data-contact-field="middle_name" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealLastName" class="mb-1 block text-sm font-medium text-gray-700">Last Name</label><input id="dealLastName" name="last_name" value="{{ old('last_name', $dealDraft['last_name'] ?? '') }}" data-contact-field="last_name" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealEmail" class="mb-1 block text-sm font-medium text-gray-700">Email</label><input id="dealEmail" name="email" type="email" value="{{ old('email', $dealDraft['email'] ?? '') }}" data-contact-field="email" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealMobile" class="mb-1 block text-sm font-medium text-gray-700">Mobile</label><input id="dealMobile" name="mobile" value="{{ old('mobile', $dealDraft['mobile'] ?? '') }}" data-contact-field="mobile" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="dealPosition" class="mb-1 block text-sm font-medium text-gray-700">Position / Designation</label><input id="dealPosition" name="position" value="{{ old('position', $dealDraft['position'] ?? '') }}" data-contact-field="position" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div class="sm:col-span-2"><label for="dealAddress" class="mb-1 block text-sm font-medium text-gray-700">Address</label><textarea id="dealAddress" name="address" rows="2" data-contact-field="address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('address', $dealDraft['address'] ?? '') }}</textarea></div>
                            <div><label for="dealCompanyName" class="mb-1 block text-sm font-medium text-gray-700">Company</label><input id="dealCompanyName" name="company_name" value="{{ old('company_name', $dealDraft['company_name'] ?? '') }}" data-contact-field="company_name" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div class="sm:col-span-2"><label for="dealCompanyAddress" class="mb-1 block text-sm font-medium text-gray-700">Company Address</label><textarea id="dealCompanyAddress" name="company_address" rows="2" data-contact-field="company_address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('company_address', $dealDraft['company_address'] ?? '') }}</textarea></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-4"><h3 class="text-base font-semibold text-gray-900">3. Deal and Engagement Details</h3><p class="text-xs text-gray-500">Complete the consulting and engagement-specific details.</p></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2"><label for="dealNameInput" class="mb-1 block text-sm font-medium text-gray-700">Deal Name</label><input id="dealNameInput" name="deal_name" required value="{{ old('deal_name', $dealDraft['deal_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="stageInput" class="mb-1 block text-sm font-medium text-gray-700">Stage</label><select id="stageInput" name="stage" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">@foreach ($stageOptions as $stageOption)<option value="{{ $stageOption }}" @selected(old('stage', $dealDraft['stage'] ?? 'Inquiry') === $stageOption)>{{ $stageOption }}</option>@endforeach</select></div>
                            <div><label for="serviceAreaInput" class="mb-1 block text-sm font-medium text-gray-700">Service Area</label><input id="serviceAreaInput" name="service_area" value="{{ old('service_area', $dealDraft['service_area'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="servicesInput" class="mb-1 block text-sm font-medium text-gray-700">Services</label><input id="servicesInput" name="services" value="{{ old('services', $dealDraft['services'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="productsInput" class="mb-1 block text-sm font-medium text-gray-700">Products</label><input id="productsInput" name="products" value="{{ old('products', $dealDraft['products'] ?? '') }}" list="dealProductOptions" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><datalist id="dealProductOptions">@foreach ($productOptions as $product)<option value="{{ $product }}"></option>@endforeach</datalist></div>
                            <div class="sm:col-span-2"><label for="scopeOfWorkInput" class="mb-1 block text-sm font-medium text-gray-700">Scope of Work</label><textarea id="scopeOfWorkInput" name="scope_of_work" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('scope_of_work', $dealDraft['scope_of_work'] ?? '') }}</textarea></div>
                            <div><label for="engagementTypeInput" class="mb-1 block text-sm font-medium text-gray-700">Engagement Type</label><select id="engagementTypeInput" name="engagement_type" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select engagement</option>@foreach (['Project Engagement', 'Regular (Retainer) Engagement', 'Hybrid Engagement'] as $option)<option value="{{ $option }}" @selected(old('engagement_type', $dealDraft['engagement_type'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div><label for="requirementsStatusInput" class="mb-1 block text-sm font-medium text-gray-700">Requirements Status</label><select id="requirementsStatusInput" name="requirements_status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select status</option>@foreach (['Requirements Provided', 'Pending Requirements'] as $option)<option value="{{ $option }}" @selected(old('requirements_status', $dealDraft['requirements_status'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div class="sm:col-span-2"><label for="requiredActionsInput" class="mb-1 block text-sm font-medium text-gray-700">Required Actions</label><textarea id="requiredActionsInput" name="required_actions" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('required_actions', $dealDraft['required_actions'] ?? '') }}</textarea></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Fees and Payment Terms</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="professionalFeeInput" class="mb-1 block text-sm font-medium text-gray-700">Estimated Professional Fee</label><input id="professionalFeeInput" name="estimated_professional_fee" value="{{ old('estimated_professional_fee', $dealDraft['estimated_professional_fee'] ?? '') }}" data-money-field class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="governmentFeesInput" class="mb-1 block text-sm font-medium text-gray-700">Estimated Government Fees</label><input id="governmentFeesInput" name="estimated_government_fees" value="{{ old('estimated_government_fees', $dealDraft['estimated_government_fees'] ?? '') }}" data-money-field class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="supportFeeInput" class="mb-1 block text-sm font-medium text-gray-700">Estimated Service Support Fee</label><input id="supportFeeInput" name="estimated_service_support_fee" value="{{ old('estimated_service_support_fee', $dealDraft['estimated_service_support_fee'] ?? '') }}" data-money-field class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="totalValueInput" class="mb-1 block text-sm font-medium text-gray-700">Total Estimated Engagement Value</label><input id="totalValueInput" name="total_estimated_engagement_value" value="{{ old('total_estimated_engagement_value', $dealDraft['total_estimated_engagement_value'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="paymentTermsInput" class="mb-1 block text-sm font-medium text-gray-700">Payment Terms</label><select id="paymentTermsInput" name="payment_terms" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select terms</option>@foreach (['Full Payment', '50/50', 'Milestone-Based', 'Monthly Retainer', 'Others'] as $option)<option value="{{ $option }}" @selected(old('payment_terms', $dealDraft['payment_terms'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div><label for="paymentTermsOtherInput" class="mb-1 block text-sm font-medium text-gray-700">Others</label><input id="paymentTermsOtherInput" name="payment_terms_other" value="{{ old('payment_terms_other', $dealDraft['payment_terms_other'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Timeline and Complexity</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="plannedStartDateInput" class="mb-1 block text-sm font-medium text-gray-700">Planned Start Date</label><input id="plannedStartDateInput" name="planned_start_date" type="date" value="{{ old('planned_start_date', $dealDraft['planned_start_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="estimatedDurationInput" class="mb-1 block text-sm font-medium text-gray-700">Estimated Duration</label><input id="estimatedDurationInput" name="estimated_duration" value="{{ old('estimated_duration', $dealDraft['estimated_duration'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="estimatedCompletionDateInput" class="mb-1 block text-sm font-medium text-gray-700">Estimated Completion Date</label><input id="estimatedCompletionDateInput" name="estimated_completion_date" type="date" value="{{ old('estimated_completion_date', $dealDraft['estimated_completion_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="preferredCompletionDateInput" class="mb-1 block text-sm font-medium text-gray-700">Client Preferred Completion Date</label><input id="preferredCompletionDateInput" name="client_preferred_completion_date" type="date" value="{{ old('client_preferred_completion_date', $dealDraft['client_preferred_completion_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="confirmedDeliveryDateInput" class="mb-1 block text-sm font-medium text-gray-700">Confirmed Delivery Date</label><input id="confirmedDeliveryDateInput" name="confirmed_delivery_date" type="date" value="{{ old('confirmed_delivery_date', $dealDraft['confirmed_delivery_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="serviceComplexityInput" class="mb-1 block text-sm font-medium text-gray-700">Service Complexity Assessment</label><select id="serviceComplexityInput" name="service_complexity" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select complexity</option>@foreach (['Standard Service', 'Complex Case'] as $option)<option value="{{ $option }}" @selected(old('service_complexity', $dealDraft['service_complexity'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div><label for="supportRequiredInput" class="mb-1 block text-sm font-medium text-gray-700">Support Required</label><select id="supportRequiredInput" name="support_required" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select support</option>@foreach (['Yes', 'No'] as $option)<option value="{{ $option }}" @selected(old('support_required', $dealDraft['support_required'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div class="sm:col-span-2"><label for="timelineNotesInput" class="mb-1 block text-sm font-medium text-gray-700">Timeline Notes</label><textarea id="timelineNotesInput" name="timeline_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('timeline_notes', $dealDraft['timeline_notes'] ?? '') }}</textarea></div>
                            <div class="sm:col-span-2"><label for="complexityNotesInput" class="mb-1 block text-sm font-medium text-gray-700">Complexity Notes</label><textarea id="complexityNotesInput" name="complexity_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('complexity_notes', $dealDraft['complexity_notes'] ?? '') }}</textarea></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 p-4">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Proposal, Assignment, and Notes</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="proposalDecisionInput" class="mb-1 block text-sm font-medium text-gray-700">Proposal Decision</label><select id="proposalDecisionInput" name="proposal_decision" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Select decision</option>@foreach (['Prepare Proposal', 'Prepare Engagement Letter', 'Schedule Consultation', 'Request Additional Documents', 'Decline Engagement'] as $option)<option value="{{ $option }}" @selected(old('proposal_decision', $dealDraft['proposal_decision'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
                            <div><label for="departmentInput" class="mb-1 block text-sm font-medium text-gray-700">Service Department / Unit</label><input id="departmentInput" name="service_department_unit" value="{{ old('service_department_unit', $dealDraft['service_department_unit'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="assignedConsultantInput" class="mb-1 block text-sm font-medium text-gray-700">Assigned Consultant</label><input id="assignedConsultantInput" name="assigned_consultant" value="{{ old('assigned_consultant', $dealDraft['assigned_consultant'] ?? $ownerLabel) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div><label for="assignedAssociateInput" class="mb-1 block text-sm font-medium text-gray-700">Assigned Associate</label><input id="assignedAssociateInput" name="assigned_associate" value="{{ old('assigned_associate', $dealDraft['assigned_associate'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                            <div class="sm:col-span-2"><label for="declineReasonInput" class="mb-1 block text-sm font-medium text-gray-700">Decline Reason</label><textarea id="declineReasonInput" name="decline_reason" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('decline_reason', $dealDraft['decline_reason'] ?? '') }}</textarea></div>
                            <div class="sm:col-span-2"><label for="consultantNotesInput" class="mb-1 block text-sm font-medium text-gray-700">Consultant Notes</label><textarea id="consultantNotesInput" name="consultant_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('consultant_notes', $dealDraft['consultant_notes'] ?? '') }}</textarea></div>
                            <div class="sm:col-span-2"><label for="associateNotesInput" class="mb-1 block text-sm font-medium text-gray-700">Associate Notes</label><textarea id="associateNotesInput" name="associate_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('associate_notes', $dealDraft['associate_notes'] ?? '') }}</textarea></div>
                        </div>
                    </section>

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                </div>

                <div class="mt-auto flex justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4 sm:px-8">
                    <button id="cancelCreateDealBtn" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-6 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" formaction="{{ route('deals.preview') }}" class="h-10 rounded-lg border border-blue-200 bg-blue-50 px-6 text-sm font-medium text-blue-700 hover:bg-blue-100">Preview</button>
                    <button type="submit" class="h-10 rounded-lg bg-blue-600 px-8 text-sm font-medium text-white hover:bg-blue-700">Save Deal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('createDealModal');
    if (!modal) return;
    const openBtn = document.getElementById('openCreateDealModalBtn');
    const closeBtn = document.getElementById('closeCreateDealModalBtn');
    const cancelBtn = document.getElementById('cancelCreateDealBtn');
    const backdrop = document.getElementById('createDealModalBackdrop');
    const panel = document.getElementById('createDealPanel');
    const form = document.getElementById('createDealForm');
    const selectorInput = document.getElementById('dealContactSearch');
    const selectorToggle = document.getElementById('dealSelectorToggle');
    const selectorMenu = document.getElementById('dealSelectorMenu');
    const contactIdInput = document.getElementById('dealContactId');
    const selectedSummary = document.getElementById('selectedContactSummary');
    const selectionWarning = document.getElementById('contactSelectionWarning');
    const totalValueInput = document.getElementById('totalValueInput');
    const moneyInputs = Array.from(modal.querySelectorAll('[data-money-field]'));
    const contactFields = Array.from(modal.querySelectorAll('[data-contact-field]'));
    const contactRecords = @json($contactRecords);

    const closeSelector = () => selectorMenu?.classList.add('hidden');
    const openModal = () => { modal.classList.remove('hidden'); modal.setAttribute('aria-hidden', 'false'); document.body.classList.add('overflow-hidden'); requestAnimationFrame(() => { backdrop?.classList.remove('opacity-0'); panel?.classList.remove('translate-x-full'); }); };
    const closeModal = () => { backdrop?.classList.add('opacity-0'); panel?.classList.add('translate-x-full'); closeSelector(); document.body.classList.remove('overflow-hidden'); window.setTimeout(() => { modal.classList.add('hidden'); modal.setAttribute('aria-hidden', 'true'); }, 300); };

    const updateSummary = (record) => {
        if (!selectedSummary) return;
        if (!record) { selectedSummary.classList.add('hidden'); selectedSummary.textContent = ''; return; }
        const parts = [record.label || 'Selected contact'];
        if (record.company_name) parts.push(record.company_name);
        if (record.email) parts.push(record.email);
        selectedSummary.textContent = `Selected: ${parts.join(' | ')}`;
        selectedSummary.classList.remove('hidden');
    };

    const applyContactRecord = (record) => {
        if (contactIdInput) contactIdInput.value = record.id;
        contactFields.forEach((field) => { const key = field.dataset.contactField; if (key) field.value = record[key] || ''; });
        if (selectorInput) selectorInput.value = record.label || record.company_name || '';
        selectionWarning?.classList.add('hidden');
        updateSummary(record);
        closeSelector();
    };

    const renderSelectorResults = (filterText = '') => {
        if (!selectorMenu) return;
        selectorMenu.innerHTML = '';
        const keyword = filterText.trim().toLowerCase();
        const filtered = contactRecords.filter((record) => keyword === '' || (record.search_blob || '').includes(keyword));
        if (filtered.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'px-3 py-3 text-sm text-gray-500';
            empty.textContent = 'No matching contact or business found.';
            selectorMenu.appendChild(empty);
            return;
        }
        filtered.forEach((record) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'block w-full rounded-lg px-3 py-2 text-left hover:bg-blue-50';
            button.innerHTML = `<span class="block text-sm font-medium text-gray-900">${record.label || 'Unnamed Contact'}</span><span class="mt-1 block text-xs text-gray-500">${[record.company_name, record.email, record.mobile].filter(Boolean).join(' | ')}</span>`;
            button.addEventListener('click', () => applyContactRecord(record));
            selectorMenu.appendChild(button);
        });
    };

    const calculateTotal = () => {
        const total = moneyInputs.reduce((sum, input) => {
            const parsed = Number.parseFloat((input.value || '').replace(/,/g, ''));
            return sum + (Number.isFinite(parsed) ? parsed : 0);
        }, 0);
        if (totalValueInput) totalValueInput.value = total > 0 ? total.toFixed(2) : '';
    };

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);
    selectorToggle?.addEventListener('click', (event) => { event.stopPropagation(); const hidden = selectorMenu?.classList.contains('hidden'); if (hidden) { renderSelectorResults(selectorInput?.value || ''); selectorMenu?.classList.remove('hidden'); selectorInput?.focus(); } else { closeSelector(); } });
    selectorInput?.addEventListener('focus', () => { renderSelectorResults(selectorInput.value); selectorMenu?.classList.remove('hidden'); });
    selectorInput?.addEventListener('input', () => { renderSelectorResults(selectorInput.value); selectorMenu?.classList.remove('hidden'); });
    document.addEventListener('click', (event) => { const insideSelector = selectorMenu?.contains(event.target) || selectorInput?.contains(event.target) || selectorToggle?.contains(event.target); if (!insideSelector) closeSelector(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });
    moneyInputs.forEach((input) => input.addEventListener('input', calculateTotal));
    form?.addEventListener('submit', (event) => { if (!contactIdInput?.value) { event.preventDefault(); selectionWarning?.classList.remove('hidden'); selectorInput?.focus(); } });
    if (contactIdInput?.value) { const record = contactRecords.find((item) => String(item.id) === String(contactIdInput.value)); if (record) applyContactRecord(record); }
    calculateTotal();
    @if ($errors->any() || $openDealModal)
        openModal();
    @endif
});
</script>
