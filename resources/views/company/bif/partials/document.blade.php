@php
    $logoPath = asset('images/imaglogo.png');
@endphp

@php
    $signatories = collect($bif->authorized_signatories ?? [])
        ->filter(fn ($row) => is_array($row))
        ->values();
    if ($signatories->isEmpty() && ($bif->authorized_signatory_name || $bif->authorized_signatory_address || $bif->authorized_signatory_position)) {
        $signatories = collect([[
            'full_name' => $bif->authorized_signatory_name,
            'address' => $bif->authorized_signatory_address,
            'nationality' => $bif->authorized_signatory_nationality,
            'date_of_birth' => optional($bif->authorized_signatory_date_of_birth)?->format('m/d/Y'),
            'tin' => $bif->authorized_signatory_tin,
            'position' => $bif->authorized_signatory_position,
        ]]);
    }

    $ubos = collect($bif->ubos ?? [])
        ->filter(fn ($row) => is_array($row))
        ->values();
    if ($ubos->isEmpty() && ($bif->ubo_name || $bif->ubo_address || $bif->ubo_position)) {
        $ubos = collect([[
            'full_name' => $bif->ubo_name,
            'address' => $bif->ubo_address,
            'nationality' => $bif->ubo_nationality,
            'date_of_birth' => optional($bif->ubo_date_of_birth)?->format('m/d/Y'),
            'tin' => $bif->ubo_tin,
            'position' => $bif->ubo_position,
        ]]);
    }
@endphp

<style>
    .bif-doc { border: 1px solid #4b5563; background: #fff; font-family: "Times New Roman", Georgia, serif; color: #111827; }
    .bif-doc *, .bif-doc *::before, .bif-doc *::after { box-sizing: border-box; }
    .bif-doc-head { display: grid; grid-template-columns: 168px minmax(0, 1fr); gap: 12px; align-items: start; border-bottom: 1px solid #4b5563; padding: 12px 10px 8px; }
    .bif-doc-brand img { max-width: 140px; height: auto; object-fit: contain; }
    .bif-doc-title { font-family: Arial, sans-serif; font-size: 15px; font-weight: 700; text-transform: uppercase; text-align: right; line-height: 1.1; }
    .bif-doc-head-main { display: flex; flex-direction: column; gap: 8px; }
    .bif-doc-head-meta { display: grid; grid-template-columns: minmax(0,1fr) 180px; gap: 12px; align-items: end; }
    .bif-doc-line { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; font-size: 10px; }
    .bif-doc-row { display: grid; grid-template-columns: repeat(24, minmax(0, 1fr)); }
    .bif-doc-cell { min-height: 42px; border-right: 1px solid #4b5563; border-bottom: 1px solid #4b5563; padding: 3px 4px; background: #fff; }
    .bif-doc-row > .bif-doc-cell:last-child { border-right: 0; }
    .bif-doc-label { display: block; font-size: 8px; line-height: 1.05; text-transform: uppercase; font-weight: 700; }
    .bif-doc-value { display: block; padding-top: 3px; font-size: 10px; line-height: 1.15; }
    .bif-doc-inline { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 4px 10px; padding-top: 4px; font-size: 9px; line-height: 1.1; }
    .bif-doc-mark { display: inline-flex; width: 12px; height: 12px; align-items: center; justify-content: center; border: 1px solid #4b5563; border-radius: 2px; background: #fff; margin-right: 4px; }
    .bif-doc-mark.active { border-color: #1d54e2; background: #1d54e2; box-shadow: inset 0 0 0 2px #1d54e2; }
    .bif-doc-section { border-bottom: 1px solid #4b5563; padding: 3px 6px; background: #102d79; color: #ffffff; font-size: 9px; font-weight: 700; text-align: center; text-transform: uppercase; }
    .bif-doc-note { font-size: 7px; line-height: 1.2; text-align: justify; }
    .bif-doc-sign-cell { display: flex; flex-direction: column; padding: 8px 8px 6px; }
    .bif-doc-sign-fill { flex: 0 0 auto; }
    .bif-doc-sign { border-top: 1px solid #4b5563; margin-top: 2px; margin-left: -8px; margin-right: -8px; padding-top: 14px; position: relative; width: calc(100% + 16px); font-size: 8px; line-height: 1.1; }
    .bif-doc-sign > span { position: absolute; top: 4px; left: 50%; transform: translateX(-50%); width: max-content; text-align: center; white-space: nowrap; }
    .bif-doc-position { border-bottom: 1px solid #4b5563; margin-top: 5px; margin-left: -8px; margin-right: -8px; width: calc(100% + 16px); padding: 3px 0 2px; font-size: 8px; text-align: center; }
    .bif-doc-sign-name { padding-top: 1px; text-align: center; }
    .bif-doc-static-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bif-doc-static-box { min-height: 190px; border-right: 1px solid #4b5563; padding: 4px 6px; font-size: 8px; line-height: 1.25; }
    .bif-doc-static-box:last-child { border-right: 0; }
    .bif-doc-static-box h4 { margin: 0 0 4px; font-size: 8px; font-weight: 700; text-transform: uppercase; text-align: center; }
    .bif-doc-static-box ol { margin: 0; padding-left: 14px; }
    .doc-col-3 { grid-column: span 3 / span 3; } .doc-col-4 { grid-column: span 4 / span 4; } .doc-col-5 { grid-column: span 5 / span 5; }
    .doc-col-6 { grid-column: span 6 / span 6; } .doc-col-7 { grid-column: span 7 / span 7; } .doc-col-8 { grid-column: span 8 / span 8; }
    .doc-col-10 { grid-column: span 10 / span 10; } .doc-col-11 { grid-column: span 11 / span 11; } .doc-col-12 { grid-column: span 12 / span 12; }
    .doc-col-14 { grid-column: span 14 / span 14; } .doc-col-16 { grid-column: span 16 / span 16; } .doc-col-24 { grid-column: span 24 / span 24; }
</style>

<div class="{{ $wrapperClass ?? 'bif-doc' }}">
    <div class="bif-doc-head">
        <div class="bif-doc-brand"><img src="{{ $logoPath }}" alt="John Kelly and Company"></div>
        <div class="bif-doc-head-main">
            <div class="bif-doc-title">Business Information<br>Form</div>
            <div class="bif-doc-head-meta">
                <div class="bif-doc-line">
                    <span>BIF No. {{ $bif->bif_no ?: '__________' }}</span>
                    @foreach ($clientTypeOptions as $value => $label)
                        <span><span class="bif-doc-mark {{ $bif->client_type === $value ? 'active' : '' }}"></span>{{ $label }}</span>
                    @endforeach
                </div>
                <div class="bif-doc-line"><span>DATE:</span><span>{{ $bif->bif_date ? $bif->bif_date->format('m/d/Y') : '__________' }}</span></div>
            </div>
        </div>
    </div>
    <div class="bif-doc-section">Business Information</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-16">
            <span class="bif-doc-label">Business Organization</span>
            <div class="bif-doc-inline">
                @foreach ($organizationOptions as $value => $label)
                    <span><span class="bif-doc-mark {{ $bif->business_organization === $value ? 'active' : '' }}"></span>{{ $label }}</span>
                @endforeach
            </div>
            <span class="bif-doc-value">{{ $bif->business_organization_other ?: '' }}</span>
        </div>
        <div class="bif-doc-cell doc-col-8">
            <span class="bif-doc-label">Nationality</span>
            <div class="space-y-1 pt-2 text-[9px]">
                @foreach ($nationalityOptions as $value => $label)
                    <div><span class="bif-doc-mark {{ $bif->nationality_status === $value ? 'active' : '' }}"></span>{{ strtoupper($label) }}</div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-10">
            <span class="bif-doc-label">Type of Office</span>
            <div class="grid grid-cols-2 gap-1 pt-2 text-[9px]">
                @foreach ($officeTypeOptions as $value => $label)
                    <span><span class="bif-doc-mark {{ $bif->office_type === $value ? 'active' : '' }}"></span>{{ $label }}</span>
                @endforeach
            </div>
            <span class="bif-doc-value">{{ $bif->office_type_other ?: '' }}</span>
        </div>
        <div class="bif-doc-cell doc-col-14"><span class="bif-doc-label">Business Name</span><span class="bif-doc-value">{{ $bif->business_name ?: '-' }}</span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-10"><span class="bif-doc-label">Alternative / Business Name / Style</span><span class="bif-doc-value">{{ $bif->alternative_business_name ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-11"><span class="bif-doc-label">Business Address</span><span class="bif-doc-value">{{ $bif->business_address ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Zip Code</span><span class="bif-doc-value">{{ $bif->zip_code ?: '-' }}</span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Business Phone</span><span class="bif-doc-value">{{ $bif->business_phone ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Mobile No.</span><span class="bif-doc-value">{{ $bif->mobile_no ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">TIN No.</span><span class="bif-doc-value">{{ $bif->tin_no ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Place of Incorporation</span><span class="bif-doc-value">{{ $bif->place_of_incorporation ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date of Incorporation</span><span class="bif-doc-value">{{ $bif->date_of_incorporation ? $bif->date_of_incorporation->format('m/d/Y') : '-' }}</span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-24">
            <span class="bif-doc-label">Industry Business / Nature of Business</span>
            <div class="bif-doc-inline">
                @foreach (['industry_services' => 'Services','industry_export_import' => 'Export/Import','industry_education' => 'Education','industry_financial_services' => 'Financial Services','industry_transportation' => 'Transportation','industry_distribution' => 'Distribution','industry_manufacturing' => 'Manufacturing','industry_government' => 'Government','industry_wholesale_retail_trade' => 'Whole Sale/Retail Trade','industry_other' => 'Other'] as $field => $label)
                    <span><span class="bif-doc-mark {{ $bif->{$field} ? 'active' : '' }}"></span>{{ $label }}</span>
                @endforeach
            </div>
            <span class="bif-doc-value">{{ $bif->industry_other_text ?: '' }}</span>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-16">
            <span class="bif-doc-label">Authorized Capital / Capital</span>
            <div class="grid grid-cols-4 gap-1 pt-2 text-[9px]">
                <span><span class="bif-doc-mark {{ $bif->capital_micro ? 'active' : '' }}"></span>&#8369;3,000,000 - Micro</span>
                <span><span class="bif-doc-mark {{ $bif->capital_small ? 'active' : '' }}"></span>&#8369;3M to &#8369;15M - Small</span>
                <span><span class="bif-doc-mark {{ $bif->capital_medium ? 'active' : '' }}"></span>&#8369;15M to &#8369;100M - Medium</span>
                <span><span class="bif-doc-mark {{ $bif->capital_large ? 'active' : '' }}"></span>&#8369;100M Above - Large</span>
            </div>
        </div>
        <div class="bif-doc-cell doc-col-8">
            <span class="bif-doc-label">Number of Employee/s</span>
            <div class="grid grid-cols-2 gap-1 pt-2 text-[9px]"><span>Male: {{ $bif->employee_male ?? '-' }}</span><span>Female: {{ $bif->employee_female ?? '-' }}</span><span>PWD: {{ $bif->employee_pwd ?? '-' }}</span><span>Total: {{ $bif->employee_total ?? '-' }}</span></div>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-24">
            <span class="bif-doc-label">Source of Funds</span>
            <div class="grid grid-cols-5 gap-1 pt-2 text-[9px]">
                <span><span class="bif-doc-mark {{ $bif->source_revenue_income ? 'active' : '' }}"></span>Revenue/Income</span>
                <span><span class="bif-doc-mark {{ $bif->source_investments ? 'active' : '' }}"></span>Investments</span>
                <span><span class="bif-doc-mark {{ $bif->source_remittance ? 'active' : '' }}"></span>Remittance</span>
                <span><span class="bif-doc-mark {{ $bif->source_other ? 'active' : '' }}"></span>Other</span>
                <span><span class="bif-doc-mark {{ $bif->source_fees ? 'active' : '' }}"></span>Fees</span>
            </div>
            <span class="bif-doc-value">{{ $bif->source_other_text ?: '' }}</span>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-12"><span class="bif-doc-label">Name of President</span><span class="bif-doc-value">{{ $bif->president_name ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-12"><span class="bif-doc-label">Name of Treasurer</span><span class="bif-doc-value">{{ $bif->treasurer_name ?: '-' }}</span></div>
    </div>
    <div class="bif-doc-section">Authorized Signatories</div>
    @forelse ($signatories as $row)
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Full Name</span><span class="bif-doc-value">{{ $row['full_name'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Address</span><span class="bif-doc-value">{{ $row['address'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Nationality</span><span class="bif-doc-value">{{ $row['nationality'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Date of Birth</span><span class="bif-doc-value">{{ $row['date_of_birth'] ?? '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">TIN</span><span class="bif-doc-value">{{ $row['tin'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Position</span><span class="bif-doc-value">{{ $row['position'] ?: '-' }}</span></div>
        </div>
    @empty
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-24"><span class="bif-doc-value">No authorized signatories recorded.</span></div>
        </div>
    @endforelse
    <div class="bif-doc-section">Ultimate Beneficial Owners with at least 20% shares of stock holdings</div>
    @forelse ($ubos as $row)
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Full Name</span><span class="bif-doc-value">{{ $row['full_name'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Address</span><span class="bif-doc-value">{{ $row['address'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Nationality</span><span class="bif-doc-value">{{ $row['nationality'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Date of Birth</span><span class="bif-doc-value">{{ $row['date_of_birth'] ?? '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">TIN</span><span class="bif-doc-value">{{ $row['tin'] ?: '-' }}</span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Position</span><span class="bif-doc-value">{{ $row['position'] ?: '-' }}</span></div>
        </div>
    @empty
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-24"><span class="bif-doc-value">No beneficial owners recorded.</span></div>
        </div>
    @endforelse
    <div class="bif-doc-section">Authorized Contact Person</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-8"><span class="bif-doc-label">Name of Authorized Contact Person</span><span class="bif-doc-value">{{ $bif->authorized_contact_person_name ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Position</span><span class="bif-doc-value">{{ $bif->authorized_contact_person_position ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Email Address</span><span class="bif-doc-value">{{ $bif->authorized_contact_person_email ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Phone/Mobile No.</span><span class="bif-doc-value">{{ $bif->authorized_contact_person_phone ?: '-' }}</span></div>
    </div>
    <div class="bif-doc-section">Acknowledgment</div>
    <div class="border-b border-gray-600 px-2 py-2">
        <p class="bif-doc-note">By signing this Business Client Information Form, I/we certify that all information provided herein is true, correct, and complete to the best of my/our knowledge. I/we agree to comply with the policies, procedures, and service guidelines of JK&amp;C Inc. and authorize JK&amp;C Inc., its officers, employees, consultants, and representatives to collect, verify, record, process, store, and use the information provided for purposes of client registration, due diligence, compliance review, service engagement, documentation, billing, and regulatory requirements.</p>
        <p class="bif-doc-note mt-1">In accordance with the Data Privacy Act of 2012, I/we consent to the collection, processing, storage, and lawful use of all personal and business information contained in this form and confirm that the undersigned is duly authorized to provide this information on behalf of the business entity.</p>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-12" style="min-height:82px;"><div class="bif-doc-sign-fill"><span class="bif-doc-value bif-doc-sign-name">{{ $bif->signature_printed_name ?: ' ' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div><div class="bif-doc-position">{{ $bif->signature_position ?: 'Position' }}</div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-12" style="min-height:82px;"><div class="bif-doc-sign-fill"><span class="bif-doc-value bif-doc-sign-name">{{ $bif->review_signature_printed_name ?: ' ' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div><div class="bif-doc-position">{{ $bif->review_signature_position ?: 'Position' }}</div></div>
    </div>
    <div class="bif-doc-section">Business Onboarding Requirements</div>
    <div class="bif-doc-static-cols border-b border-gray-600">
        <div class="bif-doc-static-box"><h4>Sole / Natural Person / Individual</h4><ol><li>Client Contact Form</li><li>Business Client Information Form</li><li>Authorized Signatory Specimen Signature Card</li><li>2 Valid Government IDs</li><li>TIN ID</li><li>DTI Certificate of Registration (if Sole Prop)</li><li>BMBE Certificate of Registration if any</li><li>BIR COR / Business Permit / Proof of Billing / SPA if applicable</li></ol></div>
        <div class="bif-doc-static-box"><h4>Juridical Entity (Corporation / OPC / Partnership / Cooperative)</h4><ol><li>Client Contact Form</li><li>Business Client Information Form</li><li>Proof of Billing / Secretary Certificate / Board Resolution</li><li>Articles of Incorporation / Partnership / By-Laws</li><li>Latest GIS / UBO Declaration / Appointment of Officers</li><li>SEC / CDA Certificate of Registration</li><li>BIR COR / Business Permit / Company Address Proof</li><li>Passport / Visa / ACR I-Card / AEP for foreign officers if applicable</li></ol></div>
    </div>
    <div class="bif-doc-section">For JKNC Use Only</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Sales &amp; Marketing</span><span class="bif-doc-value">{{ $bif->sales_marketing_name ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date &amp; Signature</span><span class="bif-doc-value">{{ $bif->sales_marketing_date_signature ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Finance</span><span class="bif-doc-value">{{ $bif->finance_name ?: '-' }}</span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date &amp; Signature</span><span class="bif-doc-value">{{ $bif->finance_date_signature ?: '-' }}</span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:78px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Referred By / Date</span><span class="bif-doc-value">{{ $bif->referred_by ?: '-' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:78px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Consultant Lead</span><span class="bif-doc-value">{{ $bif->consultant_lead ?: '-' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:78px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Lead Associate</span><span class="bif-doc-value">{{ $bif->lead_associate ?: '-' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:78px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">President</span><span class="bif-doc-value">{{ $bif->president_use_only_name ?: '-' }}</span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
    </div>
</div>
