<?php
    $logoPath = asset('images/imaglogo.png');
    $selectedOrganization = old('business_organization', $bif?->business_organization ?? '');
    $showSoleRequirements = $selectedOrganization === 'sole_proprietorship';
    $showJuridicalRequirements = in_array($selectedOrganization, ['partnership', 'corporation', 'cooperative', 'ngo', 'other'], true);
    $showPlaceholderRequirements = ! $showSoleRequirements && ! $showJuridicalRequirements;
?>

<style>
    .bif-sheet { border: 1px solid #4b5563; background: #fff; font-family: "Times New Roman", Georgia, serif; color: #111827; }
    .bif-sheet *, .bif-sheet *::before, .bif-sheet *::after { box-sizing: border-box; }
    .bif-head { display: grid; grid-template-columns: 168px minmax(0, 1fr); gap: 12px; align-items: start; border-bottom: 1px solid #4b5563; padding: 12px 10px 8px; }
    .bif-brand img { max-width: 140px; height: auto; object-fit: contain; }
    .bif-title { font-family: Arial, sans-serif; font-size: 15px; font-weight: 700; text-transform: uppercase; text-align: right; line-height: 1.1; }
    .bif-head-main { display: flex; flex-direction: column; gap: 8px; }
    .bif-head-meta { display: grid; grid-template-columns: minmax(0,1fr) 180px; gap: 12px; align-items: end; }
    .bif-meta-checks, .bif-meta-line { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; font-size: 10px; }
    .bif-line-input { min-width: 90px; border: 0; border-bottom: 1px solid #4b5563; background: transparent; padding: 1px 0 0; font-size: 10px; line-height: 1.1; outline: none; }
    .bif-row { display: grid; grid-template-columns: repeat(24, minmax(0, 1fr)); }
    .bif-cell { min-height: 40px; border-right: 1px solid #4b5563; border-bottom: 1px solid #4b5563; padding: 3px 4px; background: #fff; }
    .bif-row > .bif-cell:last-child { border-right: 0; }
    .bif-label { display: block; font-size: 8px; line-height: 1.05; text-transform: uppercase; font-weight: 700; }
    .bif-input, .bif-textarea { width: 100%; border: 0; background: transparent; padding: 3px 0 0; font-size: 10px; line-height: 1.15; outline: none; }
    .bif-textarea { resize: none; }
    .bif-check-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 4px 10px; padding-top: 4px; font-size: 9px; line-height: 1.1; }
    .bif-check { display: inline-flex; align-items: center; gap: 4px; }
    .bif-check input { width: 12px; height: 12px; margin: 0; appearance: none; border: 1px solid #6b7280; background: #fff; border-radius: 2px; }
    .bif-check input[type="radio"] { border-radius: 9999px; }
    .bif-check input:checked { border-color: #1d54e2; background: #1d54e2; box-shadow: inset 0 0 0 2px #1d54e2; }
    .bif-section-title { border-bottom: 1px solid #4b5563; padding: 3px 6px; background: #102d79; color: #ffffff; font-size: 9px; font-weight: 700; text-align: center; text-transform: uppercase; }
    .bif-note { font-size: 8px; line-height: 1.3; text-align: justify; }
    .bif-sign-cell { display: flex; flex-direction: column; padding: 8px 8px 6px; }
    .bif-sign-fill { flex: 0 0 auto; }
    .bif-sign-line { border-top: 1px solid #4b5563; margin-top: 2px; margin-left: -8px; margin-right: -8px; padding-top: 14px; position: relative; width: calc(100% + 16px); font-size: 8px; line-height: 1.1; }
    .bif-sign-line > span { position: absolute; top: 4px; left: 50%; transform: translateX(-50%); width: max-content; text-align: center; white-space: nowrap; }
    .bif-position-line { border-bottom: 1px solid #4b5563; margin-top: 5px; margin-left: -8px; margin-right: -8px; width: calc(100% + 16px); padding: 3px 0 2px; font-size: 8px; text-align: center; }
    .bif-sign-name { padding-top: 1px; text-align: center; }
    .bif-static-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bif-static-box { min-height: 104px; border-right: 1px solid #4b5563; padding: 4px 6px; font-size: 8px; line-height: 1.18; }
    .bif-static-box:last-child { border-right: 0; }
    .bif-static-box h4 { margin: 0 0 4px; font-size: 8px; font-weight: 700; text-transform: uppercase; text-align: center; }
    .bif-static-box ol { margin: 0; padding-left: 14px; }
    .col-3 { grid-column: span 3 / span 3; } .col-4 { grid-column: span 4 / span 4; } .col-5 { grid-column: span 5 / span 5; }
    .col-6 { grid-column: span 6 / span 6; } .col-7 { grid-column: span 7 / span 7; } .col-8 { grid-column: span 8 / span 8; }
    .col-10 { grid-column: span 10 / span 10; } .col-11 { grid-column: span 11 / span 11; } .col-12 { grid-column: span 12 / span 12; }
    .col-14 { grid-column: span 14 / span 14; } .col-16 { grid-column: span 16 / span 16; } .col-24 { grid-column: span 24 / span 24; }
</style>

<div class="bif-sheet">
    <div class="bif-head">
        <div class="bif-brand"><img src="<?php echo e($logoPath); ?>" alt="John Kelly and Company"></div>
        <div class="bif-head-main">
            <div class="bif-title">Business Information<br>Form</div>
            <div class="bif-head-meta">
                <div class="bif-meta-checks">
                    <span>BIF No.</span>
                    <input name="bif_no" type="text" value="<?php echo e(old('bif_no', $bif?->bif_no ?? '')); ?>" class="bif-line-input">
                    <input name="client_type" type="hidden" value="<?php echo e(old('client_type', $bif?->client_type ?? 'new_client')); ?>">
                </div>
                <div class="bif-meta-line">
                    <span>DATE:</span>
                    <input name="bif_date" type="date" value="<?php echo e(old('bif_date', isset($bif?->bif_date) && $bif?->bif_date ? $bif->bif_date->format('Y-m-d') : now()->format('Y-m-d'))); ?>" class="bif-line-input">
                </div>
            </div>
        </div>
    </div>

    <div class="bif-section-title">Business Information</div>
    <div class="bif-row">
        <div class="bif-cell col-16">
            <label class="bif-label">Business Organization</label>
            <div class="bif-check-grid">
                <?php $__currentLoopData = $organizationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="bif-check"><input type="radio" name="business_organization" value="<?php echo e($value); ?>" data-business-organization-option <?php echo e(old('business_organization', $bif?->business_organization ?? '') === $value ? 'checked' : ''); ?>><span><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <input name="business_organization_other" type="text" value="<?php echo e(old('business_organization_other', $bif?->business_organization_other ?? '')); ?>" placeholder="Other organization" class="bif-input">
        </div>
        <div class="bif-cell col-8">
            <label class="bif-label">Nationality</label>
            <div class="space-y-2 pt-2 text-[9px]">
                <?php $__currentLoopData = $nationalityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="bif-check"><input type="radio" name="nationality_status" value="<?php echo e($value); ?>" <?php echo e(old('nationality_status', $bif?->nationality_status ?? 'filipino') === $value ? 'checked' : ''); ?>><span><?php echo e(strtoupper($label)); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-10">
            <label class="bif-label">Type of Office</label>
            <div class="grid grid-cols-2 gap-2 pt-2 text-[9px]">
                <?php $__currentLoopData = $officeTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="bif-check"><input type="radio" name="office_type" value="<?php echo e($value); ?>" <?php echo e(old('office_type', $bif?->office_type ?? '') === $value ? 'checked' : ''); ?>><span><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <input name="office_type_other" type="text" value="<?php echo e(old('office_type_other', $bif?->office_type_other ?? '')); ?>" placeholder="Other office type" class="bif-input">
        </div>
        <div class="bif-cell col-14"><label class="bif-label" for="business_name">Business Name</label><input id="business_name" name="business_name" type="text" value="<?php echo e(old('business_name', $bif?->business_name ?? '')); ?>" class="bif-input" required></div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-10"><label class="bif-label" for="alternative_business_name">Alternative / Business Name / Style</label><input id="alternative_business_name" name="alternative_business_name" type="text" value="<?php echo e(old('alternative_business_name', $bif?->alternative_business_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-11"><label class="bif-label" for="business_address">Business Address</label><textarea id="business_address" name="business_address" rows="2" class="bif-textarea"><?php echo e(old('business_address', $bif?->business_address ?? '')); ?></textarea></div>
        <div class="bif-cell col-3"><label class="bif-label" for="zip_code">Zip Code</label><input id="zip_code" name="zip_code" type="text" value="<?php echo e(old('zip_code', $bif?->zip_code ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-4"><label class="bif-label" for="business_phone">Business Phone</label><input id="business_phone" name="business_phone" type="text" value="<?php echo e(old('business_phone', $bif?->business_phone ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-4"><label class="bif-label" for="mobile_no">Mobile No.</label><input id="mobile_no" name="mobile_no" type="text" value="<?php echo e(old('mobile_no', $bif?->mobile_no ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-4"><label class="bif-label" for="tin_no">TIN No.</label><input id="tin_no" name="tin_no" type="text" value="<?php echo e(old('tin_no', $bif?->tin_no ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-6"><label class="bif-label" for="place_of_incorporation">Place of Incorporation</label><input id="place_of_incorporation" name="place_of_incorporation" type="text" value="<?php echo e(old('place_of_incorporation', $bif?->place_of_incorporation ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-6"><label class="bif-label" for="date_of_incorporation">Date of Incorporation</label><input id="date_of_incorporation" name="date_of_incorporation" type="date" value="<?php echo e(old('date_of_incorporation', isset($bif?->date_of_incorporation) && $bif?->date_of_incorporation ? $bif->date_of_incorporation->format('Y-m-d') : '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-24">
            <label class="bif-label">Industry Business / Nature of Business</label>
            <div class="bif-check-grid">
                <label class="bif-check"><input type="checkbox" name="industry_services" value="1" <?php echo e(old('industry_services', $bif?->industry_services ?? false) ? 'checked' : ''); ?>><span>Services</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_export_import" value="1" <?php echo e(old('industry_export_import', $bif?->industry_export_import ?? false) ? 'checked' : ''); ?>><span>Export/Import</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_education" value="1" <?php echo e(old('industry_education', $bif?->industry_education ?? false) ? 'checked' : ''); ?>><span>Education</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_financial_services" value="1" <?php echo e(old('industry_financial_services', $bif?->industry_financial_services ?? false) ? 'checked' : ''); ?>><span>Financial Services</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_transportation" value="1" <?php echo e(old('industry_transportation', $bif?->industry_transportation ?? false) ? 'checked' : ''); ?>><span>Transportation</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_distribution" value="1" <?php echo e(old('industry_distribution', $bif?->industry_distribution ?? false) ? 'checked' : ''); ?>><span>Distribution</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_manufacturing" value="1" <?php echo e(old('industry_manufacturing', $bif?->industry_manufacturing ?? false) ? 'checked' : ''); ?>><span>Manufacturing</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_government" value="1" <?php echo e(old('industry_government', $bif?->industry_government ?? false) ? 'checked' : ''); ?>><span>Government</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_wholesale_retail_trade" value="1" <?php echo e(old('industry_wholesale_retail_trade', $bif?->industry_wholesale_retail_trade ?? false) ? 'checked' : ''); ?>><span>Whole Sale/Retail Trade</span></label>
                <label class="bif-check"><input type="checkbox" name="industry_other" value="1" <?php echo e(old('industry_other', $bif?->industry_other ?? false) ? 'checked' : ''); ?>><span>Other</span></label>
            </div>
            <input name="industry_other_text" type="text" value="<?php echo e(old('industry_other_text', $bif?->industry_other_text ?? '')); ?>" class="bif-input">
        </div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-16">
            <label class="bif-label">Authorized Capital / Capital</label>
            <div class="grid grid-cols-4 gap-2 pt-2 text-[9px]">
                <label class="bif-check"><input type="checkbox" name="capital_micro" value="1" <?php echo e(old('capital_micro', $bif?->capital_micro ?? false) ? 'checked' : ''); ?>><span>&#8369;3,000,000 - Micro</span></label>
                <label class="bif-check"><input type="checkbox" name="capital_small" value="1" <?php echo e(old('capital_small', $bif?->capital_small ?? false) ? 'checked' : ''); ?>><span>&#8369;3M to &#8369;15M - Small</span></label>
                <label class="bif-check"><input type="checkbox" name="capital_medium" value="1" <?php echo e(old('capital_medium', $bif?->capital_medium ?? false) ? 'checked' : ''); ?>><span>&#8369;15M to &#8369;100M - Medium</span></label>
                <label class="bif-check"><input type="checkbox" name="capital_large" value="1" <?php echo e(old('capital_large', $bif?->capital_large ?? false) ? 'checked' : ''); ?>><span>&#8369;100M Above - Large</span></label>
            </div>
        </div>
        <div class="bif-cell col-8">
            <label class="bif-label">Number of Employee/s</label>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1 pt-2 text-[9px]">
                <label>Male <input name="employee_male" type="number" min="0" value="<?php echo e(old('employee_male', $bif?->employee_male ?? '')); ?>" class="bif-line-input w-full"></label>
                <label>Female <input name="employee_female" type="number" min="0" value="<?php echo e(old('employee_female', $bif?->employee_female ?? '')); ?>" class="bif-line-input w-full"></label>
                <label>PWD <input name="employee_pwd" type="number" min="0" value="<?php echo e(old('employee_pwd', $bif?->employee_pwd ?? '')); ?>" class="bif-line-input w-full"></label>
                <label>Total <input name="employee_total" type="number" min="0" value="<?php echo e(old('employee_total', $bif?->employee_total ?? '')); ?>" class="bif-line-input w-full"></label>
            </div>
        </div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-24">
            <label class="bif-label">Source of Funds</label>
            <div class="grid grid-cols-5 gap-2 pt-2 text-[9px]">
                <label class="bif-check"><input type="checkbox" name="source_revenue_income" value="1" <?php echo e(old('source_revenue_income', $bif?->source_revenue_income ?? false) ? 'checked' : ''); ?>><span>Revenue/Income</span></label>
                <label class="bif-check"><input type="checkbox" name="source_investments" value="1" <?php echo e(old('source_investments', $bif?->source_investments ?? false) ? 'checked' : ''); ?>><span>Investments</span></label>
                <label class="bif-check"><input type="checkbox" name="source_remittance" value="1" <?php echo e(old('source_remittance', $bif?->source_remittance ?? false) ? 'checked' : ''); ?>><span>Remittance</span></label>
                <label class="bif-check"><input type="checkbox" name="source_other" value="1" <?php echo e(old('source_other', $bif?->source_other ?? false) ? 'checked' : ''); ?>><span>Other</span></label>
                <label class="bif-check"><input type="checkbox" name="source_fees" value="1" <?php echo e(old('source_fees', $bif?->source_fees ?? false) ? 'checked' : ''); ?>><span>Fees</span></label>
            </div>
            <input name="source_other_text" type="text" value="<?php echo e(old('source_other_text', $bif?->source_other_text ?? '')); ?>" class="bif-input">
        </div>
    </div>
    <div class="bif-row">
        <div class="bif-cell col-12"><label class="bif-label" for="president_name">Name of President</label><input id="president_name" name="president_name" type="text" value="<?php echo e(old('president_name', $bif?->president_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-12"><label class="bif-label" for="treasurer_name">Name of Treasurer</label><input id="treasurer_name" name="treasurer_name" type="text" value="<?php echo e(old('treasurer_name', $bif?->treasurer_name ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-section-title">Authorized Signatories</div>
    <div class="bif-row">
        <div class="bif-cell col-5"><label class="bif-label" for="authorized_signatory_name">Full Name</label><input id="authorized_signatory_name" name="authorized_signatory_name" type="text" value="<?php echo e(old('authorized_signatory_name', $bif?->authorized_signatory_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-7"><label class="bif-label" for="authorized_signatory_address">Address</label><input id="authorized_signatory_address" name="authorized_signatory_address" type="text" value="<?php echo e(old('authorized_signatory_address', $bif?->authorized_signatory_address ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="authorized_signatory_nationality">Nationality</label><input id="authorized_signatory_nationality" name="authorized_signatory_nationality" type="text" value="<?php echo e(old('authorized_signatory_nationality', $bif?->authorized_signatory_nationality ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="authorized_signatory_date_of_birth">Date of Birth</label><input id="authorized_signatory_date_of_birth" name="authorized_signatory_date_of_birth" type="date" value="<?php echo e(old('authorized_signatory_date_of_birth', isset($bif?->authorized_signatory_date_of_birth) && $bif?->authorized_signatory_date_of_birth ? $bif->authorized_signatory_date_of_birth->format('Y-m-d') : '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="authorized_signatory_tin">TIN</label><input id="authorized_signatory_tin" name="authorized_signatory_tin" type="text" value="<?php echo e(old('authorized_signatory_tin', $bif?->authorized_signatory_tin ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="authorized_signatory_position">Position</label><input id="authorized_signatory_position" name="authorized_signatory_position" type="text" value="<?php echo e(old('authorized_signatory_position', $bif?->authorized_signatory_position ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-section-title">Ultimate Beneficial Owners with at least 20% shares of stock holdings</div>
    <div class="bif-row">
        <div class="bif-cell col-5"><label class="bif-label" for="ubo_name">Full Name</label><input id="ubo_name" name="ubo_name" type="text" value="<?php echo e(old('ubo_name', $bif?->ubo_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-7"><label class="bif-label" for="ubo_address">Address</label><input id="ubo_address" name="ubo_address" type="text" value="<?php echo e(old('ubo_address', $bif?->ubo_address ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="ubo_nationality">Nationality</label><input id="ubo_nationality" name="ubo_nationality" type="text" value="<?php echo e(old('ubo_nationality', $bif?->ubo_nationality ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="ubo_date_of_birth">Date of Birth</label><input id="ubo_date_of_birth" name="ubo_date_of_birth" type="date" value="<?php echo e(old('ubo_date_of_birth', isset($bif?->ubo_date_of_birth) && $bif?->ubo_date_of_birth ? $bif->ubo_date_of_birth->format('Y-m-d') : '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="ubo_tin">TIN</label><input id="ubo_tin" name="ubo_tin" type="text" value="<?php echo e(old('ubo_tin', $bif?->ubo_tin ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-3"><label class="bif-label" for="ubo_position">Position</label><input id="ubo_position" name="ubo_position" type="text" value="<?php echo e(old('ubo_position', $bif?->ubo_position ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-section-title">Authorized Contact Person</div>
    <div class="bif-row">
        <div class="bif-cell col-8"><label class="bif-label" for="authorized_contact_person_name">Name of Authorized Contact Person</label><input id="authorized_contact_person_name" name="authorized_contact_person_name" type="text" value="<?php echo e(old('authorized_contact_person_name', $bif?->authorized_contact_person_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-4"><label class="bif-label" for="authorized_contact_person_position">Position</label><input id="authorized_contact_person_position" name="authorized_contact_person_position" type="text" value="<?php echo e(old('authorized_contact_person_position', $bif?->authorized_contact_person_position ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-7"><label class="bif-label" for="authorized_contact_person_email">Email Address</label><input id="authorized_contact_person_email" name="authorized_contact_person_email" type="email" value="<?php echo e(old('authorized_contact_person_email', $bif?->authorized_contact_person_email ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-5"><label class="bif-label" for="authorized_contact_person_phone">Phone/Mobile No.</label><input id="authorized_contact_person_phone" name="authorized_contact_person_phone" type="text" value="<?php echo e(old('authorized_contact_person_phone', $bif?->authorized_contact_person_phone ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-section-title">Acknowledgment</div>
    <div class="border-b border-gray-600 px-3 py-2">
        <p class="bif-note">By signing this Business Client Information Form, I/we certify that all information provided herein is true, correct, and complete to the best of my/our knowledge. I/we agree to comply with the policies, procedures, and service guidelines of JK&amp;C Inc. and authorize JK&amp;C Inc., its officers, employees, consultants, and representatives to collect, verify, record, process, store, and use the information provided for purposes of client registration, due diligence, compliance review, service engagement, documentation, billing, and regulatory requirements.</p>
        <p class="bif-note mt-1">In accordance with the Data Privacy Act of 2012, I/we consent to the collection, processing, storage, and lawful use of all personal and business information contained in this form and confirm that the undersigned is duly authorized to provide this information on behalf of the business entity.</p>
    </div>
    <div class="bif-row">
        <div class="bif-cell bif-sign-cell col-12" style="min-height:74px;"><div class="bif-sign-fill"><input name="signature_printed_name" type="text" value="<?php echo e(old('signature_printed_name', $bif?->signature_printed_name ?? '')); ?>" class="bif-input bif-sign-name"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div><input name="signature_position" type="text" value="<?php echo e(old('signature_position', $bif?->signature_position ?? '')); ?>" placeholder="Position" class="bif-position-line bif-input"></div>
        <div class="bif-cell bif-sign-cell col-12" style="min-height:74px;"><div class="bif-sign-fill"><input name="review_signature_printed_name" type="text" value="<?php echo e(old('review_signature_printed_name', $bif?->review_signature_printed_name ?? '')); ?>" class="bif-input bif-sign-name"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div><input name="review_signature_position" type="text" value="<?php echo e(old('review_signature_position', $bif?->review_signature_position ?? '')); ?>" placeholder="Position" class="bif-position-line bif-input"></div>
    </div>
    <div class="bif-section-title">Business Onboarding Requirements</div>
        <div id="bif-sole-requirements" class="bif-static-cols border-b border-gray-600" style="grid-template-columns:minmax(0,1fr);<?php echo e($showSoleRequirements ? '' : 'display:none;'); ?>">
            <div class="bif-static-box"><h4>Sole Proprietorship</h4><ol><li>DTI Certificate of Registration (if Sole Prop)</li><li>BIR Certificate of Registration (COR)</li><li>Business Permit / Mayor's Permit</li><li>Proof of Billing (Residential)</li><li>Proof of Billing (Business Address if different)</li><li>Special Power of Attorney (if representative)</li><li>Representative's 2 Valid IDs (if applicable)</li></ol></div>
        </div>
        <div id="bif-juridical-requirements" class="bif-static-cols border-b border-gray-600" style="grid-template-columns:minmax(0,1fr);<?php echo e($showJuridicalRequirements ? '' : 'display:none;'); ?>">
            <div class="bif-static-box"><h4>Partnership / Corporation / Cooperative / NGO / Other Juridical Entity</h4><ol><li>SEC / CDA Certificate of Registration</li><li>BIR Certificate of Registration (COR)</li><li>Business Permit / Mayor's Permit</li><li>Articles of Incorporation / Partnership</li><li>By-Laws</li><li>Latest General Information Sheet (GIS)</li><li>Appointment of Officers (for OPC, if applicable)</li><li>Secretary Certificate OR Board Resolution</li><li>Ultimate Beneficial Owner (UBO) Declaration</li><li>Proof of Billing (Company Address)</li><li>Proof of Billing (Authorized Representative, if applicable)</li></ol></div>
        </div>
        <div id="bif-requirements-placeholder" class="border-b border-gray-600 px-2 py-2 text-[9px]" style="<?php echo e($showPlaceholderRequirements ? '' : 'display:none;'); ?>">Select a business organization to display the applicable onboarding requirements.</div>
    <div class="bif-section-title">For JKNC Use Only</div>
    <div class="bif-row">
        <div class="bif-cell col-6"><label class="bif-label" for="sales_marketing_name">Sales &amp; Marketing</label><input id="sales_marketing_name" name="sales_marketing_name" type="text" value="<?php echo e(old('sales_marketing_name', $bif?->sales_marketing_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-6"><label class="bif-label" for="sales_marketing_date_signature">Date &amp; Signature</label><input id="sales_marketing_date_signature" name="sales_marketing_date_signature" type="text" value="<?php echo e(old('sales_marketing_date_signature', $bif?->sales_marketing_date_signature ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-6"><label class="bif-label" for="finance_name">Finance</label><input id="finance_name" name="finance_name" type="text" value="<?php echo e(old('finance_name', $bif?->finance_name ?? '')); ?>" class="bif-input"></div>
        <div class="bif-cell col-6"><label class="bif-label" for="finance_date_signature">Date &amp; Signature</label><input id="finance_date_signature" name="finance_date_signature" type="text" value="<?php echo e(old('finance_date_signature', $bif?->finance_date_signature ?? '')); ?>" class="bif-input"></div>
    </div>
    <div class="bif-row">
        <div class="bif-cell bif-sign-cell col-6" style="min-height:66px;"><div class="bif-sign-fill"><label class="bif-label" for="referred_by">Referred By / Date</label><input id="referred_by" name="referred_by" type="text" value="<?php echo e(old('referred_by', $bif?->referred_by ?? '')); ?>" class="bif-input"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div></div>
        <div class="bif-cell bif-sign-cell col-6" style="min-height:66px;"><div class="bif-sign-fill"><label class="bif-label" for="consultant_lead">Consultant Lead</label><input id="consultant_lead" name="consultant_lead" type="text" value="<?php echo e(old('consultant_lead', $bif?->consultant_lead ?? '')); ?>" class="bif-input"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div></div>
        <div class="bif-cell bif-sign-cell col-6" style="min-height:66px;"><div class="bif-sign-fill"><label class="bif-label" for="lead_associate">Lead Associate</label><input id="lead_associate" name="lead_associate" type="text" value="<?php echo e(old('lead_associate', $bif?->lead_associate ?? '')); ?>" class="bif-input"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div></div>
        <div class="bif-cell bif-sign-cell col-6" style="min-height:66px;"><div class="bif-sign-fill"><label class="bif-label" for="president_use_only_name">President</label><input id="president_use_only_name" name="president_use_only_name" type="text" value="<?php echo e(old('president_use_only_name', $bif?->president_use_only_name ?? '')); ?>" class="bif-input"></div><div class="bif-sign-line"><span>Signature over Printed Name</span></div></div>
    </div>
</div>

<script>
    (() => {
        const soleRequirements = document.getElementById('bif-sole-requirements');
        const juridicalRequirements = document.getElementById('bif-juridical-requirements');
        const placeholder = document.getElementById('bif-requirements-placeholder');
        const organizationOptions = Array.from(document.querySelectorAll('input[name="business_organization"][data-business-organization-option]'));

        if (!soleRequirements || !juridicalRequirements || !placeholder || organizationOptions.length === 0) {
            return;
        }

        const juridicalOrganizations = ['partnership', 'corporation', 'cooperative', 'ngo', 'other'];

        const syncRequirements = () => {
            const selected = organizationOptions.find((option) => option.checked)?.value ?? '';
            const showSole = selected === 'sole_proprietorship';
            const showJuridical = juridicalOrganizations.includes(selected);

            soleRequirements.style.display = showSole ? '' : 'none';
            juridicalRequirements.style.display = showJuridical ? '' : 'none';
            placeholder.style.display = showSole || showJuridical ? 'none' : '';
        };

        organizationOptions.forEach((option) => option.addEventListener('change', syncRequirements));
        syncRequirements();
    })();
</script>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\bif\partials\form-fields.blade.php ENDPATH**/ ?>