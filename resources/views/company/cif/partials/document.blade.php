@php
    $statusLabel = $statusLabels[$cif->status] ?? ucfirst(str_replace('_', ' ', (string) $cif->status));
    $sourceLabels = collect([
        'Salary' => $cif->source_of_funds_salary,
        'Remittance' => $cif->source_of_funds_remittance,
        'Business' => $cif->source_of_funds_business,
        'Others' => $cif->source_of_funds_others,
        'Commission / Fees' => $cif->source_of_funds_commission_fees,
        'Retirement / Pension' => $cif->source_of_funds_retirement_pension,
    ])->filter()->keys()->values();
    $logoPath = asset('images/imaglogo.png');
@endphp

<style>
    .cif-doc {
        border: 1px solid #4b5563;
        background: #fff;
        font-family: "Times New Roman", Georgia, serif;
        color: #111827;
    }
    .cif-doc *,
    .cif-doc *::before,
    .cif-doc *::after {
        box-sizing: border-box;
    }
    .cif-doc-head {
        display: grid;
        grid-template-columns: 168px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        border-bottom: 1px solid #4b5563;
        padding: 14px 12px 10px;
    }
    .cif-doc-brand img {
        max-width: 140px;
        height: auto;
        object-fit: contain;
    }
    .cif-doc-head-main {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .cif-doc-title {
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
    }
    .cif-doc-head-meta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px;
        gap: 12px;
        align-items: end;
    }
    .cif-doc-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 4px 12px;
        padding-top: 4px;
        font-size: 10px;
        line-height: 1.1;
    }
    .cif-doc-line {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 18px;
        font-size: 11px;
    }
    .cif-doc-row {
        display: grid;
        grid-template-columns: repeat(24, minmax(0, 1fr));
    }
    .cif-doc-cell {
        min-height: 44px;
        border-right: 1px solid #4b5563;
        border-bottom: 1px solid #4b5563;
        padding: 3px 5px;
        background: #fff;
    }
    .cif-doc-sign-cell {
        display: flex;
        flex-direction: column;
        padding: 8px 8px 6px;
    }
    .cif-doc-sign-fill {
        flex: 0 0 auto;
    }
    .cif-doc-row > .cif-doc-cell:last-child {
        border-right: 0;
    }
    .cif-doc-label,
    .cif-doc-label-lite {
        display: block;
        font-size: 9px;
        line-height: 1.05;
        font-weight: 700;
    }
    .cif-doc-label {
        text-transform: uppercase;
    }
    .cif-doc-label-lite {
        text-transform: none;
    }
    .cif-doc-muted {
        font-size: 8px;
        line-height: 1.05;
        font-weight: 600;
    }
    .cif-doc-value {
        display: block;
        padding-top: 4px;
        font-size: 11px;
        line-height: 1.15;
    }
    .cif-doc-mark {
        display: inline-flex;
        width: 12px;
        height: 12px;
        align-items: center;
        justify-content: center;
        border: 1px solid #4b5563;
        border-radius: 2px;
        background: #fff;
    }
    .cif-doc-mark.active {
        border-color: #1d54e2;
        background: #1d54e2;
    }
    .cif-doc-section {
        border-bottom: 1px solid #4b5563;
        padding: 3px 6px;
        background: #102d79;
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
    }
    .cif-doc-note {
        font-size: 8px;
        line-height: 1.2;
        text-align: justify;
    }
    .cif-doc-sign {
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
    .cif-doc-sign > span {
        position: absolute;
        top: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: max-content;
        text-align: center;
        white-space: nowrap;
    }
    .cif-doc-position {
        border-bottom: 1px solid #4b5563;
        margin-top: 5px;
        margin-left: -8px;
        margin-right: -8px;
        width: calc(100% + 16px);
        padding: 3px 0 2px;
        font-size: 9px;
        text-align: center;
    }
    .cif-doc-sign-name {
        padding-top: 1px;
        text-align: center;
    }
    .cif-doc-list {
        margin: 0;
        padding-left: 18px;
        font-size: 9px;
        line-height: 1.35;
    }
    .doc-col-4 { grid-column: span 4 / span 4; }
    .doc-col-5 { grid-column: span 5 / span 5; }
    .doc-col-6 { grid-column: span 6 / span 6; }
    .doc-col-7 { grid-column: span 7 / span 7; }
    .doc-col-8 { grid-column: span 8 / span 8; }
    .doc-col-9 { grid-column: span 9 / span 9; }
    .doc-col-11 { grid-column: span 11 / span 11; }
    .doc-col-12 { grid-column: span 12 / span 12; }
    .doc-col-15 { grid-column: span 15 / span 15; }
    .doc-col-18 { grid-column: span 18 / span 18; }
    .doc-col-24 { grid-column: span 24 / span 24; }
</style>

<div class="{{ $wrapperClass ?? 'cif-doc' }}">
    <div class="cif-doc-head">
        <div class="cif-doc-brand">
            <img src="{{ $logoPath }}" alt="John Kelly and Company">
        </div>
        <div class="cif-doc-head-main">
            <div class="cif-doc-title">Client Information Form</div>
            <div class="cif-doc-head-meta">
                <div class="cif-doc-line">
                    <span>CIF No.</span>
                    <span>{{ $cif->cif_no ?: '____________' }}</span>
                    @foreach ($clientTypeOptions as $value => $label)
                        <span><span class="cif-doc-mark {{ $cif->client_type === $value ? 'active' : '' }}"></span> {{ $label }}</span>
                    @endforeach
                </div>
                <div class="cif-doc-line">
                    <span>DATE:</span>
                    <span>{{ $cif->cif_date ? $cif->cif_date->format('m/d/Y') : '____________' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-5"><span class="cif-doc-label">First Name</span><span class="cif-doc-value">{{ $cif->first_name ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-5"><span class="cif-doc-label">Last Name</span><span class="cif-doc-value">{{ $cif->last_name ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label-lite">Name Extension</span><span class="cif-doc-muted">(Jr./Sr./III)</span><span class="cif-doc-value">{{ $cif->name_extension ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-5"><span class="cif-doc-label">Middle Name</span><span class="cif-doc-value">{{ $cif->middle_name ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-5">
            <div class="cif-doc-inline">
                <span><span class="cif-doc-mark {{ $cif->no_middle_name ? 'active' : '' }}"></span> I have no Middle Name</span>
                <span><span class="cif-doc-mark {{ $cif->first_name_only ? 'active' : '' }}"></span> I only have a First Name</span>
            </div>
            <div class="pt-1 text-[8px] leading-tight">(single name or mononym)</div>
        </div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-18">
            <span class="cif-doc-label">Present Address (No. / Street / District / Barangay / City / Town / Province)</span>
            <span class="cif-doc-value">{{ $cif->address ?: '-' }}</span>
        </div>
        <div class="cif-doc-cell doc-col-6"><span class="cif-doc-label">Zip Code</span><span class="cif-doc-value">{{ $cif->zip_code ?: '-' }}</span></div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-8"><span class="cif-doc-label">Email Address</span><span class="cif-doc-value">{{ $cif->email ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-8"><span class="cif-doc-label">Phone No.</span><span class="cif-doc-value">{{ $cif->phone_no ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-8"><span class="cif-doc-label">Mobile No.</span><span class="cif-doc-value">{{ $cif->mobile_no ?: '-' }}</span></div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">Date of Birth</span><span class="cif-doc-muted">(mm/dd/yyyy)</span><span class="cif-doc-value">{{ $cif->date_of_birth ? $cif->date_of_birth->format('m/d/Y') : '-' }}</span></div>
        <div class="cif-doc-cell doc-col-5"><span class="cif-doc-label">Place of Birth</span><span class="cif-doc-value">{{ $cif->place_of_birth ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-15">
            <span class="cif-doc-label">Citizenship / Nationality</span>
            <div class="cif-doc-inline">
                @foreach ($citizenshipOptions as $value => $label)
                    <span><span class="cif-doc-mark {{ $cif->citizenship_status === $value ? 'active' : '' }}"></span> {{ $label }}</span>
                @endforeach
            </div>
            <span class="cif-doc-value">{{ $cif->nationality ?: '-' }}</span>
        </div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-6">
            <span class="cif-doc-label">Gender</span>
            <div class="cif-doc-inline">
                @foreach ($genderOptions as $value => $label)
                    <span><span class="cif-doc-mark {{ $cif->gender === $value ? 'active' : '' }}"></span> {{ $label }}</span>
                @endforeach
            </div>
        </div>
        <div class="cif-doc-cell doc-col-18">
            <span class="cif-doc-label">Civil Status</span>
            <div class="cif-doc-inline">
                @foreach ($civilStatusOptions as $value => $label)
                    <span><span class="cif-doc-mark {{ $cif->marital_status === $value ? 'active' : '' }}"></span> {{ $label }}</span>
                @endforeach
                <span>Married: Spouse's Name {{ $cif->spouse_name ?: '__________' }}</span>
            </div>
        </div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-9"><span class="cif-doc-label">Nature of Work / Business</span><span class="cif-doc-value">{{ $cif->nature_of_work_business ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">TIN</span><span class="cif-doc-value">{{ $cif->tin ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-7"><span class="cif-doc-label">Other Government ID</span><span class="cif-doc-value">{{ $cif->other_government_id ?: '-' }}</span></div>
        <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">ID Number</span><span class="cif-doc-value">{{ $cif->id_number ?: '-' }}</span></div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-24"><span class="cif-doc-label">Mother's Maiden Name</span><span class="cif-doc-value">{{ $cif->mothers_maiden_name ?: '-' }}</span></div>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell doc-col-24">
            <div class="grid grid-cols-7 gap-x-3 gap-y-1 pt-1 text-[10px] leading-tight">
                <div class="cif-doc-label">Source of Funds</div>
                @foreach (['Salary', 'Business', 'Commission / Fees', 'Retirement / Pension', 'Remittance', 'Others'] as $label)
                    <span><span class="cif-doc-mark {{ $sourceLabels->contains($label) ? 'active' : '' }}"></span> {{ $label }}</span>
                @endforeach
            </div>
            <div class="pt-2 text-[10px]">If others, specify: {{ $cif->source_of_funds_other_text ?: '__________' }}</div>
        </div>
    </div>

    @if (in_array($cif->citizenship_status, ['foreigner', 'dual_citizen'], true))
        <div class="cif-doc-row">
            <div class="cif-doc-cell doc-col-9"><span class="cif-doc-label">If Foreigner Passport No.</span><span class="cif-doc-value">{{ $cif->passport_no ?: '-' }}</span></div>
            <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">Expiry Date</span><span class="cif-doc-value">{{ $cif->passport_expiry_date ? $cif->passport_expiry_date->format('m/d/Y') : '-' }}</span></div>
            <div class="cif-doc-cell doc-col-11"><span class="cif-doc-label">Place of Issue</span><span class="cif-doc-value">{{ $cif->passport_place_of_issue ?: '-' }}</span></div>
        </div>
        <div class="cif-doc-row">
            <div class="cif-doc-cell doc-col-9"><span class="cif-doc-label">If Foreigner ACR ID No.</span><span class="cif-doc-value">{{ $cif->acr_id_no ?: '-' }}</span></div>
            <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">Expiry Date</span><span class="cif-doc-value">{{ $cif->acr_expiry_date ? $cif->acr_expiry_date->format('m/d/Y') : '-' }}</span></div>
            <div class="cif-doc-cell doc-col-7"><span class="cif-doc-label">Place of Issue</span><span class="cif-doc-value">{{ $cif->acr_place_of_issue ?: '-' }}</span></div>
            <div class="cif-doc-cell doc-col-4"><span class="cif-doc-label">Visa Status</span><span class="cif-doc-value">{{ $cif->visa_status ?: '-' }}</span></div>
        </div>
    @endif

    <div class="cif-doc-section">Acknowledgment</div>
    <div class="border-b border-gray-600 px-2 py-2">
        <p class="cif-doc-note">By signing this Client Information Form, I certify that all personal information provided herein is true, correct, and complete to the best of my knowledge. I agree to comply with the policies, procedures, and service guidelines of John Kelly and Company and understand that any inaccurate, misleading, or incomplete information may result in delays, additional verification requirements, or rejection.</p>
        <p class="cif-doc-note mt-1">I authorize the collection, use, and processing of the information contained in this form for client onboarding, internal verification, regulatory compliance, and related business transactions. Supporting documents may be requested for validation.</p>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-12" style="min-height:88px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-value cif-doc-sign-name">{{ $cif->signature_printed_name ?: ' ' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
            <div class="cif-doc-position">{{ $cif->signature_position ?: 'Position' }}</div>
        </div>
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-12" style="min-height:88px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-value cif-doc-sign-name">{{ $cif->review_signature_printed_name ?: ' ' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
            <div class="cif-doc-position">{{ $cif->review_signature_position ?: 'Position' }}</div>
        </div>
    </div>

    <div class="cif-doc-section">Client Onboarding Requirements</div>
    <div class="border-b border-gray-600 px-2 py-2">
        <ol class="cif-doc-list">
            <li>2 Valid Government IDs</li>
            <li>TIN ID (Signatory / Representative / Stockholders / Partners / Others)</li>
            <li>Authorized Signatory / Specimen Signature / Signature Card</li>
        </ol>
    </div>

    <div class="cif-doc-row">
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-5" style="min-height:98px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-label">Referred By / Date</span>
                <span class="cif-doc-value">{{ $cif->referred_by ?: '-' }}</span>
                <span class="cif-doc-value">{{ $cif->referred_by_date ? $cif->referred_by_date->format('m/d/Y') : '-' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-6" style="min-height:98px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-label">Sales &amp; Marketing</span>
                <span class="cif-doc-value">{{ $cif->sales_marketing_name ?: '-' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-7" style="min-height:98px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-label">Finance</span>
                <span class="cif-doc-value">{{ $cif->finance_name ?: '-' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
        </div>
        <div class="cif-doc-cell cif-doc-sign-cell doc-col-6" style="min-height:98px;">
            <div class="cif-doc-sign-fill">
                <span class="cif-doc-label">President</span>
                <span class="cif-doc-value">{{ $cif->president_name ?: '-' }}</span>
            </div>
            <div class="cif-doc-sign"><span>Signature over Printed Name</span></div>
        </div>
    </div>

</div>
