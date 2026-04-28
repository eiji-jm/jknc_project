<?php
    $logoPath = asset('images/imaglogo.png');
?>

<?php
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

    $showSoleRequirements = $bif->business_organization === 'sole_proprietorship';
    $showJuridicalRequirements = in_array($bif->business_organization, ['partnership', 'corporation', 'cooperative', 'ngo', 'other'], true);
?>

<style>
    .bif-doc { border: 1.2px solid #334155; background: #fff; font-family: "Times New Roman", Georgia, serif; color: #0f172a; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .bif-doc *, .bif-doc *::before, .bif-doc *::after { box-sizing: border-box; }
    .bif-doc-head { display: grid; grid-template-columns: 164px minmax(0, 1fr); gap: 10px; align-items: start; border-bottom: 1.1px solid #334155; padding: 8px 9px 6px; }
    .bif-doc-brand img { max-width: 140px; height: auto; object-fit: contain; }
    .bif-doc-title { font-family: Arial, sans-serif; font-size: 14px; font-weight: 700; text-transform: uppercase; text-align: right; line-height: 1.05; }
    .bif-doc-head-main { display: flex; flex-direction: column; gap: 5px; }
    .bif-doc-head-meta { display: grid; grid-template-columns: minmax(0,1fr) 208px; gap: 10px; align-items: start; }
    .bif-doc-line { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; font-size: 9px; }
    .bif-doc-head-side { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; text-align: right; }
    .bif-doc-row { display: grid; grid-template-columns: repeat(24, minmax(0, 1fr)); }
    .bif-doc-cell { min-height: 29px; border-right: 1.05px solid #334155; border-bottom: 1.05px solid #334155; padding: 2px 4px; background: #fff; }
    .bif-doc-row > .bif-doc-cell:last-child { border-right: 0; }
    .bif-doc-label { display: block; font-size: 8px; line-height: 1.05; text-transform: uppercase; font-weight: 700; color: #111827; }
    .bif-doc-value { display: block; padding-top: 1px; font-size: 9px; line-height: 1.05; color: #0f172a; }
    .bif-doc-inline { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 3px 8px; padding-top: 2px; font-size: 8px; line-height: 1.05; }
    .bif-doc-inline-compact { display: flex; flex-wrap: wrap; gap: 3px 10px; padding-top: 2px; font-size: 8px; line-height: 1.05; }
    .bif-doc-mark { display: inline-flex; width: 12px; height: 12px; align-items: center; justify-content: center; border: 1px solid #334155; border-radius: 2px; background: #fff; margin-right: 4px; }
    .bif-doc-mark.active { border-color: #1d54e2; background: #1d54e2; box-shadow: inset 0 0 0 2px #1d54e2; }
    .bif-doc-section { border-bottom: 1.05px solid #334155; padding: 3px 6px; background: #102d79; color: #ffffff; font-size: 9px; font-weight: 700; text-align: center; text-transform: uppercase; letter-spacing: 0.02em; }
    .bif-doc-note { font-size: 8px; line-height: 1.28; text-align: justify; color: #1f2937; }
    .bif-doc-sign-cell { display: flex; flex-direction: column; padding: 5px 8px 4px; }
    .bif-doc-sign-fill { flex: 0 0 auto; min-height: 16px; }
    .bif-doc-sign { border-top: 1px solid #334155; margin-top: 2px; margin-left: -8px; margin-right: -8px; padding-top: 11px; position: relative; width: calc(100% + 16px); font-size: 8px; line-height: 1.1; }
    .bif-doc-sign > span { position: absolute; top: 2px; left: 50%; transform: translateX(-50%); width: max-content; text-align: center; white-space: nowrap; }
    .bif-doc-position { border-bottom: 1px solid #334155; margin-top: 4px; margin-left: -8px; margin-right: -8px; width: calc(100% + 16px); padding: 2px 0 1px; font-size: 8px; text-align: center; }
    .bif-doc-sign-name { padding-top: 0; text-align: center; }
    .bif-doc-static-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bif-doc-static-box { min-height: 66px; padding: 4px 6px; font-size: 8px; line-height: 1.3; }
    .bif-doc-static-box:last-child { border-right: 0; }
    .bif-doc-static-box h4 { margin: 0 0 4px; font-size: 8px; font-weight: 700; text-transform: uppercase; text-align: center; }
    .bif-doc-static-box ol { margin: 0; padding-left: 14px; }
    .bif-doc-numbered-list { display: grid; gap: 1px; }
    .bif-doc-numbered-item { display: grid; grid-template-columns: 22px minmax(0, 1fr); gap: 6px; align-items: start; }
    .bif-doc-number { font-weight: 700; }
    .doc-col-3 { grid-column: span 3 / span 3; } .doc-col-4 { grid-column: span 4 / span 4; } .doc-col-5 { grid-column: span 5 / span 5; }
    .doc-col-6 { grid-column: span 6 / span 6; } .doc-col-7 { grid-column: span 7 / span 7; } .doc-col-8 { grid-column: span 8 / span 8; }
    .doc-col-10 { grid-column: span 10 / span 10; } .doc-col-11 { grid-column: span 11 / span 11; } .doc-col-12 { grid-column: span 12 / span 12; }
    .doc-col-14 { grid-column: span 14 / span 14; } .doc-col-16 { grid-column: span 16 / span 16; } .doc-col-24 { grid-column: span 24 / span 24; }
</style>

<div class="<?php echo e($wrapperClass ?? 'bif-doc'); ?>">
    <div class="bif-doc-head">
        <div class="bif-doc-brand"><img src="<?php echo e($logoPath); ?>" alt="John Kelly and Company"></div>
        <div class="bif-doc-head-main">
            <div class="bif-doc-title">Business Information<br>Form</div>
            <div class="bif-doc-head-meta">
                <div></div>
                <div class="bif-doc-head-side">
                    <div class="bif-doc-line" style="justify-content:flex-end;"><span>DATE:</span><span><?php echo e($bif->bif_date ? $bif->bif_date->format('m/d/Y') : ''); ?></span></div>
                    <div class="bif-doc-line" style="justify-content:flex-end;"><span>BIF No.</span><span><?php echo e($bif->bif_no ?: ''); ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bif-doc-section">Business Information</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-16">
            <span class="bif-doc-label">Business Organization</span>
            <div class="bif-doc-inline">
                <?php $__currentLoopData = $organizationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><span class="bif-doc-mark <?php echo e($bif->business_organization === $value ? 'active' : ''); ?>"></span><?php echo e($label); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <span class="bif-doc-value"><?php echo e($bif->business_organization_other ?: ''); ?></span>
        </div>
        <div class="bif-doc-cell doc-col-8">
            <span class="bif-doc-label">Nationality</span>
            <div class="space-y-1 pt-2 text-[9px]">
                <?php $__currentLoopData = $nationalityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><span class="bif-doc-mark <?php echo e($bif->nationality_status === $value ? 'active' : ''); ?>"></span><?php echo e(strtoupper($label)); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-10">
            <span class="bif-doc-label">Type of Office</span>
            <div class="grid grid-cols-2 gap-1 pt-2 text-[9px]">
                <?php $__currentLoopData = $officeTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><span class="bif-doc-mark <?php echo e($bif->office_type === $value ? 'active' : ''); ?>"></span><?php echo e($label); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <span class="bif-doc-value"><?php echo e($bif->office_type_other ?: ''); ?></span>
        </div>
        <div class="bif-doc-cell doc-col-14"><span class="bif-doc-label">Business Name</span><span class="bif-doc-value"><?php echo e($bif->business_name ?: '-'); ?></span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-10"><span class="bif-doc-label">Alternative / Business Name / Style</span><span class="bif-doc-value"><?php echo e($bif->alternative_business_name ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-11"><span class="bif-doc-label">Business Address</span><span class="bif-doc-value"><?php echo e($bif->business_address ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Zip Code</span><span class="bif-doc-value"><?php echo e($bif->zip_code ?: '-'); ?></span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Business Phone</span><span class="bif-doc-value"><?php echo e($bif->business_phone ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Mobile No.</span><span class="bif-doc-value"><?php echo e($bif->mobile_no ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">TIN No.</span><span class="bif-doc-value"><?php echo e($bif->tin_no ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Place of Incorporation</span><span class="bif-doc-value"><?php echo e($bif->place_of_incorporation ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date of Incorporation</span><span class="bif-doc-value"><?php echo e($bif->date_of_incorporation ? $bif->date_of_incorporation->format('m/d/Y') : '-'); ?></span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-24">
            <span class="bif-doc-label">Industry Business / Nature of Business</span>
            <div class="bif-doc-inline">
                <?php $__currentLoopData = ['industry_services' => 'Services','industry_export_import' => 'Export/Import','industry_education' => 'Education','industry_financial_services' => 'Financial Services','industry_transportation' => 'Transportation','industry_distribution' => 'Distribution','industry_manufacturing' => 'Manufacturing','industry_government' => 'Government','industry_wholesale_retail_trade' => 'Whole Sale/Retail Trade','industry_other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><span class="bif-doc-mark <?php echo e($bif->{$field} ? 'active' : ''); ?>"></span><?php echo e($label); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <span class="bif-doc-value"><?php echo e($bif->industry_other_text ?: ''); ?></span>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-16">
            <span class="bif-doc-label">Authorized Capital / Capital</span>
            <div class="grid grid-cols-4 gap-1 pt-2 text-[9px]">
                <span><span class="bif-doc-mark <?php echo e($bif->capital_micro ? 'active' : ''); ?>"></span>&#8369;3,000,000 - Micro</span>
                <span><span class="bif-doc-mark <?php echo e($bif->capital_small ? 'active' : ''); ?>"></span>&#8369;3M to &#8369;15M - Small</span>
                <span><span class="bif-doc-mark <?php echo e($bif->capital_medium ? 'active' : ''); ?>"></span>&#8369;15M to &#8369;100M - Medium</span>
                <span><span class="bif-doc-mark <?php echo e($bif->capital_large ? 'active' : ''); ?>"></span>&#8369;100M Above - Large</span>
            </div>
        </div>
        <div class="bif-doc-cell doc-col-8">
            <span class="bif-doc-label">Number of Employee/s</span>
            <div class="grid grid-cols-2 gap-1 pt-2 text-[9px]"><span>Male: <?php echo e($bif->employee_male ?? '-'); ?></span><span>Female: <?php echo e($bif->employee_female ?? '-'); ?></span><span>PWD: <?php echo e($bif->employee_pwd ?? '-'); ?></span><span>Total: <?php echo e($bif->employee_total ?? '-'); ?></span></div>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-24">
            <span class="bif-doc-label">Source of Funds</span>
            <div class="grid grid-cols-5 gap-1 pt-2 text-[9px]">
                <span><span class="bif-doc-mark <?php echo e($bif->source_revenue_income ? 'active' : ''); ?>"></span>Revenue/Income</span>
                <span><span class="bif-doc-mark <?php echo e($bif->source_investments ? 'active' : ''); ?>"></span>Investments</span>
                <span><span class="bif-doc-mark <?php echo e($bif->source_remittance ? 'active' : ''); ?>"></span>Remittance</span>
                <span><span class="bif-doc-mark <?php echo e($bif->source_other ? 'active' : ''); ?>"></span>Other</span>
                <span><span class="bif-doc-mark <?php echo e($bif->source_fees ? 'active' : ''); ?>"></span>Fees</span>
            </div>
            <span class="bif-doc-value"><?php echo e($bif->source_other_text ?: ''); ?></span>
        </div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-12"><span class="bif-doc-label">Name of President</span><span class="bif-doc-value"><?php echo e($bif->president_name ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-12"><span class="bif-doc-label">Name of Treasurer</span><span class="bif-doc-value"><?php echo e($bif->treasurer_name ?: '-'); ?></span></div>
    </div>
    <div class="bif-doc-section">Authorized Signatories</div>
    <?php $__empty_1 = true; $__currentLoopData = $signatories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Full Name</span><span class="bif-doc-value"><?php echo e($row['full_name'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Address</span><span class="bif-doc-value"><?php echo e($row['address'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Nationality</span><span class="bif-doc-value"><?php echo e($row['nationality'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Date of Birth</span><span class="bif-doc-value"><?php echo e($row['date_of_birth'] ?? '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">TIN</span><span class="bif-doc-value"><?php echo e($row['tin'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Position</span><span class="bif-doc-value"><?php echo e($row['position'] ?: '-'); ?></span></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-24"><span class="bif-doc-value">No authorized signatories recorded.</span></div>
        </div>
    <?php endif; ?>
    <div class="bif-doc-section">Ultimate Beneficial Owners with at least 20% shares of stock holdings</div>
    <?php $__empty_1 = true; $__currentLoopData = $ubos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Full Name</span><span class="bif-doc-value"><?php echo e($row['full_name'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Address</span><span class="bif-doc-value"><?php echo e($row['address'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Nationality</span><span class="bif-doc-value"><?php echo e($row['nationality'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Date of Birth</span><span class="bif-doc-value"><?php echo e($row['date_of_birth'] ?? '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">TIN</span><span class="bif-doc-value"><?php echo e($row['tin'] ?: '-'); ?></span></div>
            <div class="bif-doc-cell doc-col-3"><span class="bif-doc-label">Position</span><span class="bif-doc-value"><?php echo e($row['position'] ?: '-'); ?></span></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bif-doc-row">
            <div class="bif-doc-cell doc-col-24"><span class="bif-doc-value">No beneficial owners recorded.</span></div>
        </div>
    <?php endif; ?>
    <div class="bif-doc-section">Authorized Contact Person</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-8"><span class="bif-doc-label">Name of Authorized Contact Person</span><span class="bif-doc-value"><?php echo e($bif->authorized_contact_person_name ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-4"><span class="bif-doc-label">Position</span><span class="bif-doc-value"><?php echo e($bif->authorized_contact_person_position ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-7"><span class="bif-doc-label">Email Address</span><span class="bif-doc-value"><?php echo e($bif->authorized_contact_person_email ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-5"><span class="bif-doc-label">Phone/Mobile No.</span><span class="bif-doc-value"><?php echo e($bif->authorized_contact_person_phone ?: '-'); ?></span></div>
    </div>
    <div class="bif-doc-section">Acknowledgment</div>
    <div class="border-b border-gray-600 px-3 py-2">
        <p class="bif-doc-note">By signing this Business Client Information Form, I/we certify that all information provided herein is true, correct, and complete to the best of my/our knowledge. I/we agree to comply with the policies, procedures, and service guidelines of JK&amp;C Inc. and authorize JK&amp;C Inc., its officers, employees, consultants, and representatives to collect, verify, record, process, store, and use the information provided for purposes of client registration, due diligence, compliance review, service engagement, documentation, billing, and regulatory requirements.</p>
        <p class="bif-doc-note mt-1">In accordance with the Data Privacy Act of 2012, I/we consent to the collection, processing, storage, and lawful use of all personal and business information contained in this form and confirm that the undersigned is duly authorized to provide this information on behalf of the business entity.</p>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-12" style="min-height:74px;"><div class="bif-doc-sign-fill"><span class="bif-doc-value bif-doc-sign-name"><?php echo e($bif->signature_printed_name ?: ' '); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div><div class="bif-doc-position"><?php echo e($bif->signature_position ?: 'Position'); ?></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-12" style="min-height:74px;"><div class="bif-doc-sign-fill"><span class="bif-doc-value bif-doc-sign-name"><?php echo e($bif->review_signature_printed_name ?: ' '); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div><div class="bif-doc-position"><?php echo e($bif->review_signature_position ?: 'Position'); ?></div></div>
    </div>
    <div class="bif-doc-section">Business Onboarding Requirements</div>
    <?php if($showSoleRequirements): ?>
        <div class="border-b border-gray-600">
            <div class="bif-doc-static-box">
                <div class="bif-doc-numbered-list">
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">1 |</span><span>DTI Certificate of Registration (if Sole Prop)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">2 |</span><span>BIR Certificate of Registration (COR)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">3 |</span><span>Business Permit / Mayor's Permit</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">4 |</span><span>Proof of Billing (Residential)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">5 |</span><span>Proof of Billing (Business Address if different)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">6 |</span><span>Special Power of Attorney (if representative)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">7 |</span><span>Representative's 2 Valid IDs (if applicable)</span></div>
                </div>
            </div>
        </div>
    <?php elseif($showJuridicalRequirements): ?>
        <div class="border-b border-gray-600">
            <div class="bif-doc-static-box">
                <div class="bif-doc-numbered-list">
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">1 |</span><span>SEC / CDA Certificate of Registration</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">2 |</span><span>BIR Certificate of Registration (COR)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">3 |</span><span>Business Permit / Mayor's Permit</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">4 |</span><span>Articles of Incorporation / Partnership</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">5 |</span><span>By-Laws</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">6 |</span><span>Latest General Information Sheet (GIS)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">7 |</span><span>Appointment of Officers (for OPC, if applicable)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">8 |</span><span>Secretary Certificate OR Board Resolution</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">9 |</span><span>Ultimate Beneficial Owner (UBO) Declaration</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">10 |</span><span>Proof of Billing (Company Address)</span></div>
                    <div class="bif-doc-numbered-item"><span class="bif-doc-number">11 |</span><span>Proof of Billing (Authorized Representative, if applicable)</span></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="border-b border-gray-600 px-3 py-2 text-[9px]">Select a business organization to display the applicable onboarding requirements.</div>
    <?php endif; ?>
    <div class="bif-doc-section">For JKNC Use Only</div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Sales &amp; Marketing</span><span class="bif-doc-value"><?php echo e($bif->sales_marketing_name ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date &amp; Signature</span><span class="bif-doc-value"><?php echo e($bif->sales_marketing_date_signature ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Finance</span><span class="bif-doc-value"><?php echo e($bif->finance_name ?: '-'); ?></span></div>
        <div class="bif-doc-cell doc-col-6"><span class="bif-doc-label">Date &amp; Signature</span><span class="bif-doc-value"><?php echo e($bif->finance_date_signature ?: '-'); ?></span></div>
    </div>
    <div class="bif-doc-row">
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:66px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Referred By / Date</span><span class="bif-doc-value"><?php echo e($bif->referred_by ?: '-'); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:66px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Consultant Lead</span><span class="bif-doc-value"><?php echo e($bif->consultant_lead ?: '-'); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:66px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">Lead Associate</span><span class="bif-doc-value"><?php echo e($bif->lead_associate ?: '-'); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
        <div class="bif-doc-cell bif-doc-sign-cell doc-col-6" style="min-height:66px;"><div class="bif-doc-sign-fill"><span class="bif-doc-label">President</span><span class="bif-doc-value"><?php echo e($bif->president_use_only_name ?: '-'); ?></span></div><div class="bif-doc-sign"><span>Signature over Printed Name</span></div></div>
    </div>
</div>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\bif\partials\document.blade.php ENDPATH**/ ?>