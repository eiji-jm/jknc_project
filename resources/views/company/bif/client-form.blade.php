<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Information Form</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f8fafc; color: #0f172a; }
        .min-h-screen { min-height: 100vh; }
        .bg-slate-50 { background: #f8fafc; }
        .text-slate-900 { color: #0f172a; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .max-w-5xl { max-width: 80rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .rounded-3xl { border-radius: 1.5rem; }
        .rounded-2xl { border-radius: 1rem; }
        .rounded-xl { border-radius: .75rem; }
        .rounded-full { border-radius: 9999px; }
        .bg-white { background: #fff; }
        .border { border: 1px solid; }
        .border-slate-200 { border-color: #e2e8f0; }
        .border-slate-100 { border-color: #f1f5f9; }
        .border-green-200 { border-color: #bbf7d0; }
        .border-red-200 { border-color: #fecaca; }
        .bg-green-50 { background: #f0fdf4; }
        .bg-red-50 { background: #fef2f2; }
        .text-green-700 { color: #15803d; }
        .text-red-700 { color: #b91c1c; }
        .text-slate-500 { color: #64748b; }
        .text-slate-600 { color: #475569; }
        .text-slate-700 { color: #334155; }
        .text-white { color: #fff; }
        .shadow-lg { box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12); }
        .shadow-sm { box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); }
        .bg-gradient { background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #06b6d4 100%); }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        .text-sm { font-size: .875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .uppercase { text-transform: uppercase; }
        .tracking-wide { letter-spacing: .08em; }
        .mt-1 { margin-top: .25rem; }
        .mt-2 { margin-top: .5rem; }
        .mt-3 { margin-top: .75rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .grid { display: grid; gap: 1rem; }
        .border-b { border-bottom: 1px solid #f1f5f9; }
        .px-5 { padding-left: 1.25rem; padding-right: 1.25rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
        .p-4 { padding: 1rem; }
        .w-full { width: 100%; }
        .h-11 { height: 2.75rem; }
        .h-12 { height: 3rem; }
        input, select, textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: .75rem; padding: .75rem .875rem; font-size: .875rem; }
        textarea { min-height: 84px; resize: vertical; }
        input[readonly] { background: #f8fafc; color: #64748b; }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 9999px; background: #2563eb; color: #fff; font-weight: 600; padding: 0 1.5rem; cursor: pointer; text-decoration: none; }
        .leading-6 { line-height: 1.5rem; }
        .pb-8 { padding-bottom: 2rem; }
        @media (min-width: 768px) {
            .md-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .md-span-2 { grid-column: span 2 / span 2; }
        }
        @media (min-width: 640px) {
            .sm-px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
            .sm-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    @php
        $selectedOrganization = old('business_organization', $bif->business_organization);
        $showSoleRequirements = $selectedOrganization === 'sole_proprietorship';
        $showJuridicalRequirements = in_array($selectedOrganization, ['partnership', 'corporation', 'cooperative', 'ngo', 'other'], true);
    @endphp
    <div class="mx-auto max-w-5xl px-4 sm-px-6 py-6">
        <div class="mb-6 rounded-3xl bg-gradient px-6 py-8 text-white shadow-lg">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-blue-100">Secure Client Form</p>
            <h1 class="mt-3 text-3xl font-semibold">Business Information Form</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-50">
                Complete the missing details for {{ $company->company_name }}. Your submission updates the company onboarding record directly and helps JK&C complete compliance review and service activation.
            </p>
        </div>

        @if (session('bif_success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                {{ session('bif_success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                Please review the highlighted fields and complete the missing information before submitting.
            </div>
        @endif

        <form method="POST" action="{{ $clientFormAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Form Snapshot</h2>
                    <p class="mt-1 text-sm text-slate-500">These details identify the company record that your submission will update.</p>
                </div>
                <div class="grid md-grid-3 px-5 py-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">BIF Number</label>
                        <input type="text" value="{{ $bif->bif_no ?: 'Will be assigned after submission' }}" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-500" readonly>
                    </div>
                    <div>
                        <label for="bif_date" class="mb-1 block text-sm font-medium text-slate-700">BIF Date</label>
                        <input id="bif_date" name="bif_date" type="date" value="{{ old('bif_date', optional($bif->bif_date)->format('Y-m-d') ?: now()->format('Y-m-d')) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                    </div>
                    <div>
                        <label for="client_type" class="mb-1 block text-sm font-medium text-slate-700">Client Type</label>
                        <select id="client_type" name="client_type" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            @foreach ($clientTypeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('client_type', $bif->client_type ?: 'new_client') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Business Details</h2>
                    <p class="mt-1 text-sm text-slate-500">Provide complete business organization, registration, address, and contact information.</p>
                </div>
                <div class="grid md-grid-2 px-5 py-5">
                    <div>
                        <label for="business_organization" class="mb-1 block text-sm font-medium text-slate-700">Business Organization</label>
                        <select id="business_organization" name="business_organization" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">Select organization</option>
                            @foreach ($organizationOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('business_organization', $bif->business_organization) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="business_organization_other" class="mb-1 block text-sm font-medium text-slate-700">If Other, please specify</label>
                        <input id="business_organization_other" name="business_organization_other" type="text" value="{{ old('business_organization_other', $bif->business_organization_other) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="nationality_status" class="mb-1 block text-sm font-medium text-slate-700">Nationality</label>
                        <select id="nationality_status" name="nationality_status" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @foreach ($nationalityOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('nationality_status', $bif->nationality_status ?: 'filipino') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="office_type" class="mb-1 block text-sm font-medium text-slate-700">Type of Office</label>
                        <select id="office_type" name="office_type" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">Select office type</option>
                            @foreach ($officeTypeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('office_type', $bif->office_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md-span-2">
                        <label for="business_name" class="mb-1 block text-sm font-medium text-slate-700">Business Name</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $bif->business_name ?: $company->company_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                    </div>
                    <div class="md-span-2">
                        <label for="alternative_business_name" class="mb-1 block text-sm font-medium text-slate-700">Alternative / Business Name / Style</label>
                        <input id="alternative_business_name" name="alternative_business_name" type="text" value="{{ old('alternative_business_name', $bif->alternative_business_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div class="md-span-2">
                        <label for="business_address" class="mb-1 block text-sm font-medium text-slate-700">Business Address</label>
                        <textarea id="business_address" name="business_address" rows="3" class="w-full rounded-2xl border border-slate-300 px-3 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('business_address', $bif->business_address) }}</textarea>
                    </div>
                    <div>
                        <label for="zip_code" class="mb-1 block text-sm font-medium text-slate-700">Zip Code</label>
                        <input id="zip_code" name="zip_code" type="text" value="{{ old('zip_code', $bif->zip_code) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="business_phone" class="mb-1 block text-sm font-medium text-slate-700">Business Phone</label>
                        <input id="business_phone" name="business_phone" type="text" value="{{ old('business_phone', $bif->business_phone) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="mobile_no" class="mb-1 block text-sm font-medium text-slate-700">Mobile Number</label>
                        <input id="mobile_no" name="mobile_no" type="text" value="{{ old('mobile_no', $bif->mobile_no) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="tin_no" class="mb-1 block text-sm font-medium text-slate-700">TIN Number</label>
                        <input id="tin_no" name="tin_no" type="text" value="{{ old('tin_no', $bif->tin_no) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="place_of_incorporation" class="mb-1 block text-sm font-medium text-slate-700">Place of Incorporation</label>
                        <input id="place_of_incorporation" name="place_of_incorporation" type="text" value="{{ old('place_of_incorporation', $bif->place_of_incorporation) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="date_of_incorporation" class="mb-1 block text-sm font-medium text-slate-700">Date of Incorporation</label>
                        <input id="date_of_incorporation" name="date_of_incorporation" type="date" value="{{ old('date_of_incorporation', optional($bif->date_of_incorporation)->format('Y-m-d')) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Ownership and Signatories</h2>
                    <p class="mt-1 text-sm text-slate-500">Capture the current business owners, signatories, and UBO details.</p>
                </div>
                <div class="grid md-grid-2 px-5 py-5">
                    <div>
                        <label for="president_name" class="mb-1 block text-sm font-medium text-slate-700">President / Managing Head</label>
                        <input id="president_name" name="president_name" type="text" value="{{ old('president_name', $bif->president_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="treasurer_name" class="mb-1 block text-sm font-medium text-slate-700">Treasurer</label>
                        <input id="treasurer_name" name="treasurer_name" type="text" value="{{ old('treasurer_name', $bif->treasurer_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_signatory_name" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Name</label>
                        <input id="authorized_signatory_name" name="authorized_signatory_name" type="text" value="{{ old('authorized_signatory_name', $bif->authorized_signatory_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_signatory_position" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Position</label>
                        <input id="authorized_signatory_position" name="authorized_signatory_position" type="text" value="{{ old('authorized_signatory_position', $bif->authorized_signatory_position) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div class="md-span-2">
                        <label for="authorized_signatory_address" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Address</label>
                        <input id="authorized_signatory_address" name="authorized_signatory_address" type="text" value="{{ old('authorized_signatory_address', $bif->authorized_signatory_address) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_signatory_nationality" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Nationality</label>
                        <input id="authorized_signatory_nationality" name="authorized_signatory_nationality" type="text" value="{{ old('authorized_signatory_nationality', $bif->authorized_signatory_nationality) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_signatory_date_of_birth" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Date of Birth</label>
                        <input id="authorized_signatory_date_of_birth" name="authorized_signatory_date_of_birth" type="date" value="{{ old('authorized_signatory_date_of_birth', optional($bif->authorized_signatory_date_of_birth)->format('Y-m-d')) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_signatory_tin" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory TIN</label>
                        <input id="authorized_signatory_tin" name="authorized_signatory_tin" type="text" value="{{ old('authorized_signatory_tin', $bif->authorized_signatory_tin) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="ubo_name" class="mb-1 block text-sm font-medium text-slate-700">UBO Name</label>
                        <input id="ubo_name" name="ubo_name" type="text" value="{{ old('ubo_name', $bif->ubo_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div class="md:col-span-2">
                        <label for="ubo_address" class="mb-1 block text-sm font-medium text-slate-700">UBO Address</label>
                        <input id="ubo_address" name="ubo_address" type="text" value="{{ old('ubo_address', $bif->ubo_address) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="ubo_nationality" class="mb-1 block text-sm font-medium text-slate-700">UBO Nationality</label>
                        <input id="ubo_nationality" name="ubo_nationality" type="text" value="{{ old('ubo_nationality', $bif->ubo_nationality) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="ubo_date_of_birth" class="mb-1 block text-sm font-medium text-slate-700">UBO Date of Birth</label>
                        <input id="ubo_date_of_birth" name="ubo_date_of_birth" type="date" value="{{ old('ubo_date_of_birth', optional($bif->ubo_date_of_birth)->format('Y-m-d')) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="ubo_tin" class="mb-1 block text-sm font-medium text-slate-700">UBO TIN</label>
                        <input id="ubo_tin" name="ubo_tin" type="text" value="{{ old('ubo_tin', $bif->ubo_tin) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="ubo_position" class="mb-1 block text-sm font-medium text-slate-700">UBO Position / Ownership Role</label>
                        <input id="ubo_position" name="ubo_position" type="text" value="{{ old('ubo_position', $bif->ubo_position) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Authorized Contact Person</h2>
                    <p class="mt-1 text-sm text-slate-500">This email is used for follow-up questions and future BIF reminders.</p>
                </div>
                <div class="grid md-grid-2 px-5 py-5">
                    <div>
                        <label for="authorized_contact_person_name" class="mb-1 block text-sm font-medium text-slate-700">Full Name</label>
                        <input id="authorized_contact_person_name" name="authorized_contact_person_name" type="text" value="{{ old('authorized_contact_person_name', $bif->authorized_contact_person_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_contact_person_position" class="mb-1 block text-sm font-medium text-slate-700">Position</label>
                        <input id="authorized_contact_person_position" name="authorized_contact_person_position" type="text" value="{{ old('authorized_contact_person_position', $bif->authorized_contact_person_position) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_contact_person_email" class="mb-1 block text-sm font-medium text-slate-700">Email Address</label>
                        <input id="authorized_contact_person_email" name="authorized_contact_person_email" type="email" value="{{ old('authorized_contact_person_email', $bif->authorized_contact_person_email ?: $bif->client_form_sent_to_email) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="authorized_contact_person_phone" class="mb-1 block text-sm font-medium text-slate-700">Phone / Mobile Number</label>
                        <input id="authorized_contact_person_phone" name="authorized_contact_person_phone" type="text" value="{{ old('authorized_contact_person_phone', $bif->authorized_contact_person_phone) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Supporting Documents</h2>
                    <p class="mt-1 text-sm text-slate-500">Upload the applicable onboarding documents based on your client type and business organization.</p>
                </div>
                <div class="space-y-6 px-5 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4" id="soleRequirementsCard" style="{{ $showSoleRequirements || (! $showSoleRequirements && ! $showJuridicalRequirements) ? '' : 'display:none;' }}">
                        <h3 class="text-sm font-semibold text-slate-900">Sole / Natural Person / Individual</h3>
                        <div class="grid md-grid-2" style="margin-top:1rem;">
                            @foreach ($documentFields['sole_proprietorship'] as $document)
                                <div>
                                    <label for="{{ $document['key'] }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $document['label'] }}</label>
                                    <input id="{{ $document['key'] }}" name="{{ $document['key'] }}" type="file" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4" id="juridicalRequirementsCard" style="{{ $showJuridicalRequirements || (! $showSoleRequirements && ! $showJuridicalRequirements) ? '' : 'display:none;' }}">
                        <h3 class="text-sm font-semibold text-slate-900">Juridical Entity</h3>
                        <div class="grid md-grid-2" style="margin-top:1rem;">
                            @foreach ($documentFields['juridical_entity'] as $document)
                                <div>
                                    <label for="{{ $document['key'] }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $document['label'] }}</label>
                                    <input id="{{ $document['key'] }}" name="{{ $document['key'] }}" type="file" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700">
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-lg font-semibold">Acknowledgment</h2>
                    <p class="mt-1 text-sm text-slate-500">Confirm that the submitted business details are complete and accurate.</p>
                </div>
                <div class="grid md-grid-2 px-5 py-5">
                    <div>
                        <label for="signature_printed_name" class="mb-1 block text-sm font-medium text-slate-700">Authorized Signatory Printed Name</label>
                        <input id="signature_printed_name" name="signature_printed_name" type="text" value="{{ old('signature_printed_name', $bif->signature_printed_name) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="signature_position" class="mb-1 block text-sm font-medium text-slate-700">Position</label>
                        <input id="signature_position" name="signature_position" type="text" value="{{ old('signature_position', $bif->signature_position) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </section>

            <div class="pb-8 sm-row" style="display:flex; flex-direction:column-reverse; gap:.75rem;">
                <p class="text-sm text-slate-500">Submitted data will be linked automatically to the Company KYC record for {{ $company->company_name }}.</p>
                <button type="submit" class="btn-primary h-12">
                    Submit Business Information Form
                </button>
            </div>
        </form>
    </div>
    <script>
        (() => {
            const organizationSelect = document.getElementById('business_organization');
            const soleCard = document.getElementById('soleRequirementsCard');
            const juridicalCard = document.getElementById('juridicalRequirementsCard');

            if (!organizationSelect || !soleCard || !juridicalCard) {
                return;
            }

            const juridicalTypes = new Set(['partnership', 'corporation', 'cooperative', 'ngo', 'other']);

            const syncRequirementCards = () => {
                const value = organizationSelect.value;
                const showSole = value === 'sole_proprietorship';
                const showJuridical = juridicalTypes.has(value);
                const showBoth = !showSole && !showJuridical;

                soleCard.style.display = (showSole || showBoth) ? '' : 'none';
                juridicalCard.style.display = (showJuridical || showBoth) ? '' : 'none';
            };

            organizationSelect.addEventListener('change', syncRequirementCards);
            syncRequirementCards();
        })();
    </script>
</body>
</html>
