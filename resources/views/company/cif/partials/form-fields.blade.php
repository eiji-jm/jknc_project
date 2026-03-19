@php
    $selectedCitizenship = old('citizenship_status', $cif?->citizenship_status ?? 'filipino');
    $selectedMaritalStatus = old('marital_status', $cif?->marital_status ?? 'single');
    $sourceChecks = [
        'salary' => old('source_of_funds_salary', $cif?->source_of_funds_salary ?? false),
        'remittance' => old('source_of_funds_remittance', $cif?->source_of_funds_remittance ?? false),
        'business' => old('source_of_funds_business', $cif?->source_of_funds_business ?? false),
        'others' => old('source_of_funds_others', $cif?->source_of_funds_others ?? false),
        'commission_fees' => old('source_of_funds_commission_fees', $cif?->source_of_funds_commission_fees ?? false),
        'retirement_pension' => old('source_of_funds_retirement_pension', $cif?->source_of_funds_retirement_pension ?? false),
    ];
    $logoPath = asset('images/imaglogo.png');
@endphp

<style>
    .cif-sheet {
        border: 1px solid #4b5563;
        background: #fff;
        font-family: "Times New Roman", Georgia, serif;
        color: #111827;
    }
    .cif-sheet *,
    .cif-sheet *::before,
    .cif-sheet *::after {
        box-sizing: border-box;
    }
    .cif-head {
        display: grid;
        grid-template-columns: 168px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        border-bottom: 1px solid #4b5563;
        padding: 14px 12px 10px;
    }
    .cif-brand {
        display: flex;
        align-items: flex-start;
        min-height: 60px;
    }
    .cif-brand img {
        max-width: 140px;
        height: auto;
        object-fit: contain;
    }
    .cif-head-main {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .cif-main-title {
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
    }
    .cif-head-meta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px;
        gap: 12px;
        align-items: end;
    }
    .cif-meta-line,
    .cif-meta-checks {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 18px;
        font-size: 11px;
    }
    .cif-meta-line {
        gap: 8px;
    }
    .cif-meta-label {
        font-size: 11px;
        text-transform: uppercase;
    }
    .cif-line-input {
        min-width: 84px;
        border: 0;
        border-bottom: 1px solid #4b5563;
        background: transparent;
        padding: 1px 0 0;
        font-size: 11px;
        line-height: 1.1;
        outline: none;
    }
    .cif-line-input:focus,
    .cif-input:focus,
    .cif-textarea:focus {
        box-shadow: none;
    }
    .cif-row {
        display: grid;
        grid-template-columns: repeat(24, minmax(0, 1fr));
    }
    .cif-cell {
        min-height: 44px;
        border-right: 1px solid #4b5563;
        border-bottom: 1px solid #4b5563;
        padding: 3px 5px;
        background: #fff;
    }
    .cif-sign-cell {
        display: flex;
        flex-direction: column;
        padding: 8px 8px 6px;
    }
    .cif-sign-fill {
        flex: 0 0 auto;
    }
    .cif-row > .cif-cell:last-child {
        border-right: 0;
    }
    .cif-label,
    .cif-label-lite {
        display: block;
        font-size: 9px;
        line-height: 1.05;
        color: #111827;
        font-weight: 700;
    }
    .cif-label {
        text-transform: uppercase;
    }
    .cif-label-lite {
        text-transform: none;
    }
    .cif-label-muted {
        font-size: 8px;
        line-height: 1.05;
        text-transform: none;
        font-weight: 600;
    }
    .cif-input,
    .cif-textarea {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 4px 0 0;
        font-size: 11px;
        line-height: 1.15;
        outline: none;
    }
    .cif-textarea {
        resize: none;
    }
    .cif-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 4px 12px;
        padding-top: 5px;
        font-size: 10px;
        line-height: 1.1;
    }
    .cif-check {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .cif-check input {
        width: 12px;
        height: 12px;
        margin: 0;
        appearance: none;
        border: 1px solid #6b7280;
        background: #fff;
        border-radius: 2px;
    }
    .cif-check input[type="radio"] {
        border-radius: 9999px;
    }
    .cif-check input:checked {
        border-color: #1d54e2;
        background: #1d54e2;
        box-shadow: inset 0 0 0 2px #1d54e2;
    }
    .cif-check span {
        padding: 1px 6px;
        border-radius: 9999px;
    }
    .cif-check input:checked + span {
        background: transparent;
        color: inherit;
    }
    .cif-section-title {
        border-bottom: 1px solid #4b5563;
        padding: 3px 6px;
        background: #102d79;
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
    }
    .cif-note {
        font-size: 8px;
        line-height: 1.2;
        text-align: justify;
    }
    .cif-sign-line {
        border-top: 1px solid #4b5563;
        margin-top: 2px;
        margin-left: -8px;
        margin-right: -8px;
        padding-top: 14px;
        position: relative;
        width: calc(100% + 16px);
        font-size: 9px;
        line-height: 1.1;
    }
    .cif-sign-line > span {
        position: absolute;
        top: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: max-content;
        text-align: center;
        white-space: nowrap;
    }
    .cif-position-line {
        border-bottom: 1px solid #4b5563;
        margin-top: 5px;
        margin-left: -8px;
        margin-right: -8px;
        width: calc(100% + 16px);
        padding: 3px 0 2px;
        font-size: 9px;
        text-align: center;
    }
    .cif-sign-name {
        padding-top: 1px;
        text-align: center;
    }
    .cif-static-list {
        margin: 0;
        padding-left: 18px;
        font-size: 9px;
        line-height: 1.35;
    }
    .col-2 { grid-column: span 2 / span 2; }
    .col-3 { grid-column: span 3 / span 3; }
    .col-4 { grid-column: span 4 / span 4; }
    .col-5 { grid-column: span 5 / span 5; }
    .col-6 { grid-column: span 6 / span 6; }
    .col-7 { grid-column: span 7 / span 7; }
    .col-8 { grid-column: span 8 / span 8; }
    .col-9 { grid-column: span 9 / span 9; }
    .col-10 { grid-column: span 10 / span 10; }
    .col-11 { grid-column: span 11 / span 11; }
    .col-12 { grid-column: span 12 / span 12; }
    .col-14 { grid-column: span 14 / span 14; }
    .col-15 { grid-column: span 15 / span 15; }
    .col-16 { grid-column: span 16 / span 16; }
    .col-17 { grid-column: span 17 / span 17; }
    .col-18 { grid-column: span 18 / span 18; }
    .col-24 { grid-column: span 24 / span 24; }
</style>

<div
    class="cif-sheet"
    x-data="{
        citizenshipStatus: @js($selectedCitizenship),
        maritalStatus: @js($selectedMaritalStatus),
        nationalityValue: @js(old('nationality', $cif?->nationality ?? ($selectedCitizenship === 'filipino' ? 'Filipino' : ''))),
        syncCitizenship(value) {
            this.citizenshipStatus = value;
            if (value === 'filipino') this.nationalityValue = 'Filipino';
        },
        get showSpouseField() { return this.maritalStatus === 'married'; },
        get showForeignFields() { return this.citizenshipStatus === 'foreigner' || this.citizenshipStatus === 'dual_citizen'; }
    }"
>
    <div class="cif-head">
        <div class="cif-brand">
            <img src="{{ $logoPath }}" alt="John Kelly and Company">
        </div>
        <div class="cif-head-main">
            <div class="cif-main-title">Client Information Form</div>
            <div class="cif-head-meta">
                <div class="cif-meta-checks">
                    <span class="cif-meta-label">CIF No.</span>
                    <input name="cif_no" type="text" value="{{ old('cif_no', $cif?->cif_no ?? '') }}" class="cif-line-input" placeholder="__________">
                    @foreach ($clientTypeOptions as $value => $label)
                        <label class="cif-check">
                            <input type="radio" name="client_type" value="{{ $value }}" {{ old('client_type', $cif?->client_type ?? 'new_client') === $value ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="cif-meta-line">
                    <span class="cif-meta-label">Date:</span>
                    <input name="cif_date" type="date" value="{{ old('cif_date', isset($cif?->cif_date) && $cif?->cif_date ? $cif->cif_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="cif-line-input">
                </div>
            </div>
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-5">
            <label class="cif-label" for="first_name">First Name</label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $cif?->first_name ?? '') }}" class="cif-input" required>
        </div>
        <div class="cif-cell col-5">
            <label class="cif-label" for="last_name">Last Name</label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $cif?->last_name ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-4">
            <label class="cif-label-lite" for="name_extension">Name Extension</label>
            <div class="cif-label-muted">(Jr./Sr./III)</div>
            <input id="name_extension" name="name_extension" type="text" value="{{ old('name_extension', $cif?->name_extension ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-5">
            <label class="cif-label" for="middle_name">Middle Name</label>
            <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name', $cif?->middle_name ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-5">
            <div class="cif-inline">
                <label class="cif-check">
                    <input type="checkbox" name="no_middle_name" value="1" {{ old('no_middle_name', $cif?->no_middle_name ?? false) ? 'checked' : '' }}>
                    <span>I have no Middle Name</span>
                </label>
                <label class="cif-check">
                    <input type="checkbox" name="first_name_only" value="1" {{ old('first_name_only', $cif?->first_name_only ?? false) ? 'checked' : '' }}>
                    <span>I only have a First Name</span>
                </label>
            </div>
            <div class="pt-1 text-[8px] leading-tight">(single name or mononym)</div>
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-18">
            <label class="cif-label" for="address">Present Address (No. / Street / District / Barangay / City / Town / Province)</label>
            <textarea id="address" name="address" rows="2" class="cif-textarea">{{ old('address', $cif?->address ?? '') }}</textarea>
        </div>
        <div class="cif-cell col-6">
            <label class="cif-label" for="zip_code">Zip Code</label>
            <input id="zip_code" name="zip_code" type="text" value="{{ old('zip_code', $cif?->zip_code ?? '') }}" class="cif-input">
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-8">
            <label class="cif-label" for="email">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $cif?->email ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-8">
            <label class="cif-label" for="phone_no">Phone No.</label>
            <input id="phone_no" name="phone_no" type="text" value="{{ old('phone_no', $cif?->phone_no ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-8">
            <label class="cif-label" for="mobile_no">Mobile No.</label>
            <input id="mobile_no" name="mobile_no" type="text" value="{{ old('mobile_no', $cif?->mobile_no ?? '') }}" class="cif-input">
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-4">
            <label class="cif-label" for="date_of_birth">Date of Birth</label>
            <div class="cif-label-muted">(mm/dd/yyyy)</div>
            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', isset($cif?->date_of_birth) && $cif?->date_of_birth ? $cif->date_of_birth->format('Y-m-d') : '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-5">
            <label class="cif-label" for="place_of_birth">Place of Birth</label>
            <input id="place_of_birth" name="place_of_birth" type="text" value="{{ old('place_of_birth', $cif?->place_of_birth ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-15">
            <label class="cif-label">Citizenship / Nationality</label>
            <div class="cif-inline">
                @foreach ($citizenshipOptions as $value => $label)
                    <label class="cif-check">
                        <input type="radio" name="citizenship_status" value="{{ $value }}" x-on:change="syncCitizenship('{{ $value }}')" {{ old('citizenship_status', $cif?->citizenship_status ?? 'filipino') === $value ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="pt-2">
                <input name="nationality" type="text" x-model="nationalityValue" x-bind:readonly="citizenshipStatus === 'filipino'" placeholder="Nationality" class="cif-line-input">
            </div>
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-6">
            <label class="cif-label">Gender</label>
            <div class="cif-inline">
                @foreach ($genderOptions as $value => $label)
                    <label class="cif-check">
                        <input type="radio" name="gender" value="{{ $value }}" {{ old('gender', $cif?->gender ?? '') === $value ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="cif-cell col-18">
            <label class="cif-label">Civil Status</label>
            <div class="cif-inline">
                @foreach ($civilStatusOptions as $value => $label)
                    <label class="cif-check">
                        <input type="radio" name="marital_status" value="{{ $value }}" x-model="maritalStatus" {{ old('marital_status', $cif?->marital_status ?? 'single') === $value ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
                <label class="cif-check" x-show="showSpouseField" x-cloak>
                    <span>Married: Spouse's Name</span>
                    <input name="spouse_name" type="text" value="{{ old('spouse_name', $cif?->spouse_name ?? '') }}" class="cif-line-input" style="min-width: 170px;">
                </label>
            </div>
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-9">
            <label class="cif-label" for="nature_of_work_business">Nature of Work / Business</label>
            <input id="nature_of_work_business" name="nature_of_work_business" type="text" value="{{ old('nature_of_work_business', $cif?->nature_of_work_business ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-4">
            <label class="cif-label" for="tin">TIN</label>
            <input id="tin" name="tin" type="text" value="{{ old('tin', $cif?->tin ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-7">
            <label class="cif-label" for="other_government_id">Other Government ID</label>
            <input id="other_government_id" name="other_government_id" type="text" value="{{ old('other_government_id', $cif?->other_government_id ?? '') }}" class="cif-input">
        </div>
        <div class="cif-cell col-4">
            <label class="cif-label" for="id_number">ID Number</label>
            <input id="id_number" name="id_number" type="text" value="{{ old('id_number', $cif?->id_number ?? '') }}" class="cif-input">
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-24">
            <label class="cif-label" for="mothers_maiden_name">Mother's Maiden Name</label>
            <input id="mothers_maiden_name" name="mothers_maiden_name" type="text" value="{{ old('mothers_maiden_name', $cif?->mothers_maiden_name ?? '') }}" class="cif-input">
        </div>
    </div>

    <div class="cif-row">
        <div class="cif-cell col-24">
            <div class="grid grid-cols-7 gap-x-3 gap-y-1 pt-1 text-[10px] leading-tight">
                <div class="cif-label">Source of Funds</div>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_salary" value="1" {{ $sourceChecks['salary'] ? 'checked' : '' }}><span>Salary</span></label>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_business" value="1" {{ $sourceChecks['business'] ? 'checked' : '' }}><span>Business</span></label>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_commission_fees" value="1" {{ $sourceChecks['commission_fees'] ? 'checked' : '' }}><span>Commission / Fees</span></label>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_retirement_pension" value="1" {{ $sourceChecks['retirement_pension'] ? 'checked' : '' }}><span>Retirement / Pension</span></label>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_remittance" value="1" {{ $sourceChecks['remittance'] ? 'checked' : '' }}><span>Remittance</span></label>
                <label class="cif-check"><input type="checkbox" name="source_of_funds_others" value="1" {{ $sourceChecks['others'] ? 'checked' : '' }}><span>Others</span></label>
            </div>
            <div class="pt-2">
                <input name="source_of_funds_other_text" type="text" value="{{ old('source_of_funds_other_text', $cif?->source_of_funds_other_text ?? '') }}" placeholder="If others, specify" class="cif-line-input" style="width: 100%;">
            </div>
        </div>
    </div>

    <template x-if="showForeignFields">
        <div>
            <div class="cif-row">
                <div class="cif-cell col-9">
                    <label class="cif-label" for="passport_no">If Foreigner Passport No.</label>
                    <input id="passport_no" name="passport_no" type="text" value="{{ old('passport_no', $cif?->passport_no ?? '') }}" class="cif-input">
                </div>
                <div class="cif-cell col-4">
                    <label class="cif-label" for="passport_expiry_date">Expiry Date</label>
                    <input id="passport_expiry_date" name="passport_expiry_date" type="date" value="{{ old('passport_expiry_date', isset($cif?->passport_expiry_date) && $cif?->passport_expiry_date ? $cif->passport_expiry_date->format('Y-m-d') : '') }}" class="cif-input">
                </div>
                <div class="cif-cell col-11">
                    <label class="cif-label" for="passport_place_of_issue">Place of Issue</label>
                    <input id="passport_place_of_issue" name="passport_place_of_issue" type="text" value="{{ old('passport_place_of_issue', $cif?->passport_place_of_issue ?? '') }}" class="cif-input">
                </div>
            </div>
            <div class="cif-row">
                <div class="cif-cell col-9">
                    <label class="cif-label" for="acr_id_no">If Foreigner ACR ID No.</label>
                    <input id="acr_id_no" name="acr_id_no" type="text" value="{{ old('acr_id_no', $cif?->acr_id_no ?? '') }}" class="cif-input">
                </div>
                <div class="cif-cell col-4">
                    <label class="cif-label" for="acr_expiry_date">Expiry Date</label>
                    <input id="acr_expiry_date" name="acr_expiry_date" type="date" value="{{ old('acr_expiry_date', isset($cif?->acr_expiry_date) && $cif?->acr_expiry_date ? $cif->acr_expiry_date->format('Y-m-d') : '') }}" class="cif-input">
                </div>
                <div class="cif-cell col-7">
                    <label class="cif-label" for="acr_place_of_issue">Place of Issue</label>
                    <input id="acr_place_of_issue" name="acr_place_of_issue" type="text" value="{{ old('acr_place_of_issue', $cif?->acr_place_of_issue ?? '') }}" class="cif-input">
                </div>
                <div class="cif-cell col-4">
                    <label class="cif-label" for="visa_status">Visa Status</label>
                    <input id="visa_status" name="visa_status" type="text" value="{{ old('visa_status', $cif?->visa_status ?? '') }}" class="cif-input">
                </div>
            </div>
        </div>
    </template>

    <div class="cif-section-title">Acknowledgment</div>
    <div class="border-b border-gray-600 px-2 py-2">
        <p class="cif-note">
            By signing this Client Information Form, I certify that all personal information provided herein is true, correct, and complete to the best of my knowledge. I agree to comply with the policies, procedures, and service guidelines of John Kelly and Company and understand that any inaccurate, misleading, or incomplete information may result in delays, additional verification requirements, or rejection.
        </p>
        <p class="cif-note mt-1">
            I authorize the collection, use, and processing of the information contained in this form for client onboarding, internal verification, regulatory compliance, and related business transactions. Supporting documents may be requested for validation.
        </p>
    </div>

    <div class="cif-row">
        <div class="cif-cell cif-sign-cell col-12" style="min-height:88px;">
            <div class="cif-sign-fill">
                <input name="signature_printed_name" type="text" value="{{ old('signature_printed_name', $cif?->signature_printed_name ?? '') }}" class="cif-input cif-sign-name">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
            <input name="signature_position" type="text" value="{{ old('signature_position', $cif?->signature_position ?? '') }}" placeholder="Position" class="cif-position-line cif-input">
        </div>
        <div class="cif-cell cif-sign-cell col-12" style="min-height:88px;">
            <div class="cif-sign-fill">
                <input name="review_signature_printed_name" type="text" value="{{ old('review_signature_printed_name', $cif?->review_signature_printed_name ?? '') }}" class="cif-input cif-sign-name">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
            <input name="review_signature_position" type="text" value="{{ old('review_signature_position', $cif?->review_signature_position ?? '') }}" placeholder="Position" class="cif-position-line cif-input">
        </div>
    </div>

    <div class="cif-section-title">Client Onboarding Requirements</div>
    <div class="border-b border-gray-600 px-2 py-2">
        <ol class="cif-static-list">
            <li>2 Valid Government IDs</li>
            <li>TIN ID (Signatory / Representative / Stockholders / Partners / Others)</li>
            <li>Authorized Signatory / Specimen Signature / Signature Card</li>
        </ol>
    </div>

    <div class="cif-row">
        <div class="cif-cell cif-sign-cell col-5" style="min-height:98px;">
            <div class="cif-sign-fill">
                <label class="cif-label" for="referred_by">Referred By / Date</label>
                <input id="referred_by" name="referred_by" type="text" value="{{ old('referred_by', $cif?->referred_by ?? '') }}" class="cif-input">
                <input id="referred_by_date" name="referred_by_date" type="date" value="{{ old('referred_by_date', isset($cif?->referred_by_date) && $cif?->referred_by_date ? $cif->referred_by_date->format('Y-m-d') : '') }}" class="cif-input">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-cell cif-sign-cell col-6" style="min-height:98px;">
            <div class="cif-sign-fill">
                <label class="cif-label" for="sales_marketing_name">Sales &amp; Marketing</label>
                <input id="sales_marketing_name" name="sales_marketing_name" type="text" value="{{ old('sales_marketing_name', $cif?->sales_marketing_name ?? '') }}" class="cif-input">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-cell cif-sign-cell col-7" style="min-height:98px;">
            <div class="cif-sign-fill">
                <label class="cif-label" for="finance_name">Finance</label>
                <input id="finance_name" name="finance_name" type="text" value="{{ old('finance_name', $cif?->finance_name ?? '') }}" class="cif-input">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-cell cif-sign-cell col-6" style="min-height:98px;">
            <div class="cif-sign-fill">
                <label class="cif-label" for="president_name">President</label>
                <input id="president_name" name="president_name" type="text" value="{{ old('president_name', $cif?->president_name ?? '') }}" class="cif-input">
            </div>
            <div class="cif-sign-line"><span>Signature over Printed Name</span></div>
        </div>
    </div>

</div>
