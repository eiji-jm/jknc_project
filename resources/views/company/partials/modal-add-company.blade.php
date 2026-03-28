<x-slide-over id="addCompanyModal" width="sm:max-w-[720px] lg:max-w-[820px]">
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Create Company</h2>
                <p class="mt-1 text-sm text-gray-500">Capture the Business Information Form once and reuse it across Company KYC.</p>
            </div>
            <button type="button" data-close-company-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form method="POST" action="{{ route('company.store') }}" class="flex min-h-0 flex-1 flex-col" x-data="companyBifForm()" x-init="init()">
        @csrf
        <input type="hidden" name="client_type" value="new_client">

        <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Business Information Form</p>
                    <p class="text-xs text-gray-400">This becomes the default company onboarding record and auto-populates KYC.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 sm:items-end">
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">BIF No.</label>
                        <input type="text" name="bif_no" value="{{ old('bif_no') }}" placeholder="Auto-generated after save" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Date</label>
                        <input type="date" name="bif_date" value="{{ old('bif_date', now()->format('Y-m-d')) }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Primary Contact Link</h3>
                <p class="mb-4 text-xs text-gray-500">Select the approved contact record to link this company profile.</p>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Contact <span class="text-red-500">*</span></label>
                    <select name="contact_id" x-on:change="hydrateFromContact($event)" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                        <option value="">Select contact</option>
                        @foreach (($companyCreateContacts ?? collect()) as $contact)
                            @php
                                $contactName = trim(collect([$contact->first_name, $contact->last_name])->filter()->implode(' '));
                                $contactCompany = $contact->company_name ? ' • '.$contact->company_name : '';
                                $contactStatus = strtoupper((string) ($contact->cif_status ?? 'draft'));
                            @endphp
                            <option
                                value="{{ $contact->id }}"
                                data-company-name="{{ $contact->company_name }}"
                                data-email="{{ $contact->email }}"
                                data-phone="{{ $contact->phone }}"
                                data-address="{{ $contact->contact_address }}"
                                data-tin="{{ $contact->tin }}"
                                @selected((string) old('contact_id') === (string) $contact->id)
                            >
                                {{ $contactName !== '' ? $contactName : 'Contact #'.$contact->id }}{{ $contactCompany }} • CIF: {{ $contactStatus }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Information</h3>
                <p class="mb-4 text-xs text-gray-500">Store the primary business identity, organization, and registered office details.</p>
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Business Organization</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            @foreach (['sole_proprietorship' => 'Sole Proprietorship', 'partnership' => 'Partnership', 'corporation' => 'Corporation', 'cooperative' => 'Cooperative', 'ngo' => 'NGO', 'other' => 'Other'] as $value => $label)
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                    <input type="radio" name="business_organization" value="{{ $value }}" x-model="businessOrganization" @checked(old('business_organization') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Nationality</label>
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach (['filipino' => 'Filipino', 'foreign' => 'Foreign'] as $value => $label)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                        <input type="radio" name="nationality_status" value="{{ $value }}" @checked(old('nationality_status', 'filipino') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Type of Office</label>
                            <select name="office_type" x-model="officeType" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select office type</option>
                                <option value="head_office" @selected(old('office_type') === 'head_office')>Head Office</option>
                                <option value="branch" @selected(old('office_type') === 'branch')>Branch</option>
                                <option value="regional_headquarter" @selected(old('office_type') === 'regional_headquarter')>Regional Headquarter</option>
                                <option value="other" @selected(old('office_type') === 'other')>Other</option>
                            </select>
                        </div>
                        <div x-show="businessOrganization === 'other'" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Organization</label>
                            <input type="text" name="business_organization_other" value="{{ old('business_organization_other') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div x-show="officeType === 'other'" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Office Type</label>
                            <input type="text" name="office_type_other" value="{{ old('office_type_other') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Business Name <span class="text-red-500">*</span></label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Alternative / Business Name / Style</label>
                            <input type="text" name="alternative_business_name" value="{{ old('alternative_business_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">ZIP Code</label>
                            <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Business Address</label>
                            <textarea name="business_address" rows="3" placeholder="House/Unit No., Street, Barangay, City/Town, Province" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('business_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Contact & Registration</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Business Phone</label><input type="text" name="business_phone" value="{{ old('business_phone') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Mobile No.</label><input type="text" name="mobile_no" value="{{ old('mobile_no') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">TIN No.</label><input type="text" name="tin_no" value="{{ old('tin_no') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Place of Incorporation</label><input type="text" name="place_of_incorporation" value="{{ old('place_of_incorporation') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Date of Incorporation</label><input type="date" name="date_of_incorporation" value="{{ old('date_of_incorporation') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Industry / Nature of Business</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach (['services' => 'Services', 'export_import' => 'Export/Import', 'education' => 'Education', 'financial_services' => 'Financial Services', 'transportation' => 'Transportation', 'distribution' => 'Distribution', 'manufacturing' => 'Manufacturing', 'government' => 'Government', 'wholesale_retail_trade' => 'Wholesale/Retail Trade', 'other' => 'Other'] as $value => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="industry_types[]" value="{{ $value }}" @checked(in_array($value, old('industry_types', []), true)) class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Other Industry</label>
                    <input type="text" name="industry_other_text" value="{{ old('industry_other_text') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Capital & Employees</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Authorized Capital</label>
                        <div class="space-y-2">
                            @foreach (['micro' => 'Micro (P3M below)', 'small' => 'Small (P3M-P15M)', 'medium' => 'Medium (P15M-P100M)', 'large' => 'Large (P100M above)'] as $value => $label)
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                    <input type="radio" name="capital_category" value="{{ $value }}" @checked(old('capital_category') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Male</label><input type="number" min="0" name="employee_male" x-model.number="employees.male" @input="syncEmployeeTotal()" value="{{ old('employee_male') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Female</label><input type="number" min="0" name="employee_female" x-model.number="employees.female" @input="syncEmployeeTotal()" value="{{ old('employee_female') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">PWD</label><input type="number" min="0" name="employee_pwd" x-model.number="employees.pwd" @input="syncEmployeeTotal()" value="{{ old('employee_pwd') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Total</label><input type="number" min="0" name="employee_total" x-model.number="employees.total" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-700 outline-none"></div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Source of Funds</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach (['revenue_income' => 'Revenue/Income', 'investments' => 'Investments', 'remittance' => 'Remittance', 'fees' => 'Fees', 'other' => 'Other'] as $value => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="source_of_funds[]" value="{{ $value }}" @checked(in_array($value, old('source_of_funds', []), true)) class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Other Source of Funds</label>
                    <input type="text" name="source_other_text" value="{{ old('source_other_text') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Key Officers</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name of President</label><input type="text" name="president_name" value="{{ old('president_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name of Treasurer</label><input type="text" name="treasurer_name" value="{{ old('treasurer_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Authorized Signatories</h3>
                        <p class="text-xs text-gray-500">Add one or more signatories allowed to represent the business.</p>
                    </div>
                    <button type="button" @click="addSignatory()" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 hover:bg-gray-50">+ Add Row</button>
                </div>
                <div class="space-y-4">
                    <template x-for="(row, index) in signatories" :key="'signatory-'+index">
                        <div class="rounded-xl border border-gray-200 bg-white p-3">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">Signatory <span x-text="index + 1"></span></p>
                                <button type="button" @click="removeSignatory(index)" class="text-xs text-red-600 hover:underline" x-show="signatories.length > 1">Remove</button>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <input :name="`authorized_signatories[${index}][full_name]`" x-model="row.full_name" placeholder="Full Name" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`authorized_signatories[${index}][position]`" x-model="row.position" placeholder="Position" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`authorized_signatories[${index}][nationality]`" x-model="row.nationality" placeholder="Nationality" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`authorized_signatories[${index}][date_of_birth]`" x-model="row.date_of_birth" type="date" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`authorized_signatories[${index}][tin]`" x-model="row.tin" placeholder="TIN" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`authorized_signatories[${index}][address]`" x-model="row.address" placeholder="Address" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:col-span-2">
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Ultimate Beneficial Owners (UBO)</h3>
                        <p class="text-xs text-gray-500">Record the beneficial owners with significant control or ownership.</p>
                    </div>
                    <button type="button" @click="addUbo()" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 hover:bg-gray-50">+ Add Row</button>
                </div>
                <div class="space-y-4">
                    <template x-for="(row, index) in ubos" :key="'ubo-'+index">
                        <div class="rounded-xl border border-gray-200 bg-white p-3">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">UBO <span x-text="index + 1"></span></p>
                                <button type="button" @click="removeUbo(index)" class="text-xs text-red-600 hover:underline" x-show="ubos.length > 1">Remove</button>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <input :name="`ubos[${index}][full_name]`" x-model="row.full_name" placeholder="Full Name" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`ubos[${index}][position]`" x-model="row.position" placeholder="Position" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`ubos[${index}][nationality]`" x-model="row.nationality" placeholder="Nationality" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`ubos[${index}][date_of_birth]`" x-model="row.date_of_birth" type="date" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`ubos[${index}][tin]`" x-model="row.tin" placeholder="TIN" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <input :name="`ubos[${index}][address]`" x-model="row.address" placeholder="Address" class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:col-span-2">
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Authorized Contact Person</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name</label><input type="text" name="authorized_contact_person_name" value="{{ old('authorized_contact_person_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Position</label><input type="text" name="authorized_contact_person_position" value="{{ old('authorized_contact_person_position') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Email</label><input type="email" name="authorized_contact_person_email" value="{{ old('authorized_contact_person_email') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Phone / Mobile</label><input type="text" name="authorized_contact_person_phone" value="{{ old('authorized_contact_person_phone') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Acknowledgment</h3>
                <p class="mb-4 text-xs text-gray-500">By submitting this form, the company confirms that all business information provided is true and may be used for onboarding, compliance, and KYC review.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Signature over Printed Name</label><input type="text" name="signature_printed_name" value="{{ old('signature_printed_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Position</label><input type="text" name="signature_position" value="{{ old('signature_position') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Review Signature over Printed Name</label><input type="text" name="review_signature_printed_name" value="{{ old('review_signature_printed_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Review Position</label><input type="text" name="review_signature_position" value="{{ old('review_signature_position') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Onboarding Requirements</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">
                        <p class="mb-2 font-semibold text-gray-900">Sole / Individual</p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>Client Contact Form</li>
                            <li>Business Client Information Form</li>
                            <li>Authorized Signatory Specimen Signature Card</li>
                            <li>2 Valid Government IDs</li>
                            <li>TIN ID</li>
                        </ul>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">
                        <p class="mb-2 font-semibold text-gray-900">Juridical Entity</p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>Proof of Billing / Secretary Certificate / Board Resolution</li>
                            <li>Articles of Incorporation / Partnership / By-Laws</li>
                            <li>Latest GIS / UBO Declaration / Appointment of Officers</li>
                            <li>SEC / CDA Certificate of Registration</li>
                            <li>BIR COR / Business Permit</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">JK&C Internal Use</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Referred By</label><input type="text" name="referred_by" value="{{ old('referred_by') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Consultant Lead</label><input type="text" name="consultant_lead" value="{{ old('consultant_lead') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Lead Associate</label><input type="text" name="lead_associate" value="{{ old('lead_associate') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">President</label><input type="text" name="president_use_only_name" value="{{ old('president_use_only_name') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
        </div>

        <div class="mt-auto border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-company-modal class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save Company</button>
            </div>
        </div>
    </form>
</x-slide-over>

<script>
function companyBifForm() {
    return {
        businessOrganization: @js(old('business_organization', '')),
        officeType: @js(old('office_type', '')),
        employees: {
            male: Number(@js(old('employee_male', 0))) || 0,
            female: Number(@js(old('employee_female', 0))) || 0,
            pwd: Number(@js(old('employee_pwd', 0))) || 0,
            total: Number(@js(old('employee_total', 0))) || 0,
        },
        signatories: @js(old('authorized_signatories', [['full_name' => '', 'address' => '', 'nationality' => '', 'date_of_birth' => '', 'tin' => '', 'position' => '']])),
        ubos: @js(old('ubos', [['full_name' => '', 'address' => '', 'nationality' => '', 'date_of_birth' => '', 'tin' => '', 'position' => '']])),
        init() {
            this.syncEmployeeTotal();
            if (!Array.isArray(this.signatories) || this.signatories.length === 0) this.signatories = [this.emptyRow()];
            if (!Array.isArray(this.ubos) || this.ubos.length === 0) this.ubos = [this.emptyRow()];
            const selectedContact = document.querySelector('select[name="contact_id"]');
            if (selectedContact && selectedContact.value) {
                this.hydrateFromContact({ target: selectedContact });
            }
        },
        emptyRow() {
            return { full_name: '', address: '', nationality: '', date_of_birth: '', tin: '', position: '' };
        },
        addSignatory() { this.signatories.push(this.emptyRow()); },
        removeSignatory(index) { if (this.signatories.length > 1) this.signatories.splice(index, 1); },
        addUbo() { this.ubos.push(this.emptyRow()); },
        removeUbo(index) { if (this.ubos.length > 1) this.ubos.splice(index, 1); },
        fillIfBlank(fieldName, value) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (!input) return;
            if (String(input.value || '').trim() !== '') return;
            if (String(value || '').trim() === '') return;
            input.value = value;
        },
        hydrateFromContact(event) {
            const option = event?.target?.selectedOptions?.[0];
            if (!option) return;

            const companyName = option.dataset.companyName || '';
            const email = option.dataset.email || '';
            const phone = option.dataset.phone || '';
            const address = option.dataset.address || '';
            const tin = option.dataset.tin || '';

            this.fillIfBlank('business_name', companyName);
            this.fillIfBlank('authorized_contact_person_email', email);
            this.fillIfBlank('authorized_contact_person_phone', phone);
            this.fillIfBlank('business_phone', phone);
            this.fillIfBlank('mobile_no', phone);
            this.fillIfBlank('business_address', address);
            this.fillIfBlank('tin_no', tin);
        },
        syncEmployeeTotal() {
            this.employees.total = (Number(this.employees.male) || 0) + (Number(this.employees.female) || 0) + (Number(this.employees.pwd) || 0);
        },
    };
}
</script>
