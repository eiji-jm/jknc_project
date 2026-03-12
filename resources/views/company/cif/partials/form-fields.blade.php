<div>
    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Client Information</h3>
    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <label for="first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $cif->first_name ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>
        <div>
            <label for="last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $cif->last_name ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>
        <div>
            <label for="preferred_name" class="mb-1 block text-sm font-medium text-gray-700">Preferred Name</label>
            <input id="preferred_name" name="preferred_name" type="text" value="{{ old('preferred_name', $cif->preferred_name ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="patient_identifier" class="mb-1 block text-sm font-medium text-gray-700">Patient Identifier (if known)</label>
            <input id="patient_identifier" name="patient_identifier" type="text" value="{{ old('patient_identifier', $cif->patient_identifier ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="gender" class="mb-1 block text-sm font-medium text-gray-700">Gender</label>
            <input id="gender" name="gender" type="text" value="{{ old('gender', $cif->gender ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="preferred_pronouns" class="mb-1 block text-sm font-medium text-gray-700">Preferred Pronouns</label>
            <input id="preferred_pronouns" name="preferred_pronouns" type="text" value="{{ old('preferred_pronouns', $cif->preferred_pronouns ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="date_of_birth" class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', isset($cif->date_of_birth) && $cif->date_of_birth ? $cif->date_of_birth->format('Y-m-d') : '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="marital_status" class="mb-1 block text-sm font-medium text-gray-700">Marital Status</label>
            <input id="marital_status" name="marital_status" type="text" value="{{ old('marital_status', $cif->marital_status ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        <div class="lg:col-span-2">
            <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address', $cif->address ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="city" class="mb-1 block text-sm font-medium text-gray-700">City</label>
            <input id="city" name="city" type="text" value="{{ old('city', $cif->city ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="state" class="mb-1 block text-sm font-medium text-gray-700">State</label>
            <input id="state" name="state" type="text" value="{{ old('state', $cif->state ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="zip_code" class="mb-1 block text-sm font-medium text-gray-700">Zip Code</label>
            <input id="zip_code" name="zip_code" type="text" value="{{ old('zip_code', $cif->zip_code ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $cif->email ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="preferred_phone" class="mb-1 block text-sm font-medium text-gray-700">Preferred Phone Number</label>
            <input id="preferred_phone" name="preferred_phone" type="text" value="{{ old('preferred_phone', $cif->preferred_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
</div>

<div class="mt-6">
    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Emergency Contact</h3>
    <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label for="emergency_contact_1_name" class="mb-1 block text-sm font-medium text-gray-700">Contact 1 - Full Name</label>
            <input id="emergency_contact_1_name" name="emergency_contact_1_name" type="text" value="{{ old('emergency_contact_1_name', $cif->emergency_contact_1_name ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_1_relationship" class="mb-1 block text-sm font-medium text-gray-700">Relationship</label>
            <input id="emergency_contact_1_relationship" name="emergency_contact_1_relationship" type="text" value="{{ old('emergency_contact_1_relationship', $cif->emergency_contact_1_relationship ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_1_home_phone" class="mb-1 block text-sm font-medium text-gray-700">Home Phone</label>
            <input id="emergency_contact_1_home_phone" name="emergency_contact_1_home_phone" type="text" value="{{ old('emergency_contact_1_home_phone', $cif->emergency_contact_1_home_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_1_cell_phone" class="mb-1 block text-sm font-medium text-gray-700">Cell Phone</label>
            <input id="emergency_contact_1_cell_phone" name="emergency_contact_1_cell_phone" type="text" value="{{ old('emergency_contact_1_cell_phone', $cif->emergency_contact_1_cell_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        <div class="lg:col-span-2">
            <label for="emergency_contact_2_name" class="mb-1 block text-sm font-medium text-gray-700">Contact 2 - Full Name</label>
            <input id="emergency_contact_2_name" name="emergency_contact_2_name" type="text" value="{{ old('emergency_contact_2_name', $cif->emergency_contact_2_name ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_2_relationship" class="mb-1 block text-sm font-medium text-gray-700">Relationship</label>
            <input id="emergency_contact_2_relationship" name="emergency_contact_2_relationship" type="text" value="{{ old('emergency_contact_2_relationship', $cif->emergency_contact_2_relationship ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_2_home_phone" class="mb-1 block text-sm font-medium text-gray-700">Home Phone</label>
            <input id="emergency_contact_2_home_phone" name="emergency_contact_2_home_phone" type="text" value="{{ old('emergency_contact_2_home_phone', $cif->emergency_contact_2_home_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_2_cell_phone" class="mb-1 block text-sm font-medium text-gray-700">Cell Phone</label>
            <input id="emergency_contact_2_cell_phone" name="emergency_contact_2_cell_phone" type="text" value="{{ old('emergency_contact_2_cell_phone', $cif->emergency_contact_2_cell_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="emergency_contact_1_work_phone" class="mb-1 block text-sm font-medium text-gray-700">Contact 1 - Work Phone</label>
            <input id="emergency_contact_1_work_phone" name="emergency_contact_1_work_phone" type="text" value="{{ old('emergency_contact_1_work_phone', $cif->emergency_contact_1_work_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="emergency_contact_2_work_phone" class="mb-1 block text-sm font-medium text-gray-700">Contact 2 - Work Phone</label>
            <input id="emergency_contact_2_work_phone" name="emergency_contact_2_work_phone" type="text" value="{{ old('emergency_contact_2_work_phone', $cif->emergency_contact_2_work_phone ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
</div>

<div class="mt-6">
    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Insurance Information</h3>
    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div>
            <label for="insurance_carrier" class="mb-1 block text-sm font-medium text-gray-700">Insurance Carrier</label>
            <input id="insurance_carrier" name="insurance_carrier" type="text" value="{{ old('insurance_carrier', $cif->insurance_carrier ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="insurance_plan" class="mb-1 block text-sm font-medium text-gray-700">Insurance Plan</label>
            <input id="insurance_plan" name="insurance_plan" type="text" value="{{ old('insurance_plan', $cif->insurance_plan ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="insurance_contact_number" class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
            <input id="insurance_contact_number" name="insurance_contact_number" type="text" value="{{ old('insurance_contact_number', $cif->insurance_contact_number ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="policy_number" class="mb-1 block text-sm font-medium text-gray-700">Policy Number</label>
            <input id="policy_number" name="policy_number" type="text" value="{{ old('policy_number', $cif->policy_number ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="group_number" class="mb-1 block text-sm font-medium text-gray-700">Group Number</label>
            <input id="group_number" name="group_number" type="text" value="{{ old('group_number', $cif->group_number ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="social_security_number" class="mb-1 block text-sm font-medium text-gray-700">Social Security Number</label>
            <input id="social_security_number" name="social_security_number" type="text" value="{{ old('social_security_number', $cif->social_security_number ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
</div>

<div class="mt-6">
    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Referrals and Adjunctive Care</h3>
    @php($underMedicalCare = old('under_medical_care', isset($cif) && $cif->under_medical_care ? '1' : '0'))
    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <p class="mb-2 text-sm font-medium text-gray-700">Are you currently under medical care?</p>
            <div class="flex items-center gap-6 text-sm text-gray-700">
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="under_medical_care" value="1" {{ $underMedicalCare === '1' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-blue-600">
                    <span>Yes</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="under_medical_care" value="0" {{ $underMedicalCare === '0' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-blue-600">
                    <span>No</span>
                </label>
            </div>
        </div>
        <div class="md:col-span-2">
            <label for="medical_care_for" class="mb-1 block text-sm font-medium text-gray-700">If yes, for what?</label>
            <input id="medical_care_for" name="medical_care_for" type="text" value="{{ old('medical_care_for', $cif->medical_care_for ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="primary_care_physician" class="mb-1 block text-sm font-medium text-gray-700">Primary Care Physician</label>
            <input id="primary_care_physician" name="primary_care_physician" type="text" value="{{ old('primary_care_physician', $cif->primary_care_physician ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="physician_contact_number" class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
            <input id="physician_contact_number" name="physician_contact_number" type="text" value="{{ old('physician_contact_number', $cif->physician_contact_number ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        <div class="md:col-span-2">
            <label for="physician_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
            <input id="physician_address" name="physician_address" type="text" value="{{ old('physician_address', $cif->physician_address ?? '') }}" class="h-9 w-full rounded border border-gray-200 px-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
</div>

<div class="mt-6">
    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Health Concerns / Symptoms</h3>
    <div class="mt-3 space-y-4">
        <div>
            <label for="main_concerns" class="mb-1 block text-sm font-medium text-gray-700">Describe your main concerns</label>
            <textarea id="main_concerns" name="main_concerns" rows="4" class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('main_concerns', $cif->main_concerns ?? '') }}</textarea>
        </div>
        <div>
            <label for="illness_begin" class="mb-1 block text-sm font-medium text-gray-700">When did your chief complaint or illness begin?</label>
            <textarea id="illness_begin" name="illness_begin" rows="2" class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('illness_begin', $cif->illness_begin ?? '') }}</textarea>
        </div>
        <div>
            <label for="visit_goals" class="mb-1 block text-sm font-medium text-gray-700">What are your goals for today’s visit and for your long-term health?</label>
            <textarea id="visit_goals" name="visit_goals" rows="3" class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('visit_goals', $cif->visit_goals ?? '') }}</textarea>
        </div>
    </div>
</div>
