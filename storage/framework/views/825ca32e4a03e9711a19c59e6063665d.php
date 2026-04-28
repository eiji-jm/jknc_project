<?php
    $selectedCitizenshipType = old('citizenship_type', $cifData['citizenship_type'] ?? '');
    $selectedCivilStatus = old('civil_status', $cifData['civil_status'] ?? '');
?>

<form method="POST" action="<?php echo e(route('contacts.cif.save', $contact->id)); ?>" class="space-y-5" data-cif-card-form>
    <?php echo csrf_field(); ?>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Top / Meta</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Date</label><input type="date" name="cif_date" value="<?php echo e(old('cif_date', $cifData['cif_date'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">CIF No.</label><input name="cif_no" value="<?php echo e(old('cif_no', $cifData['cif_no'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Identity</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">First Name</label><input required name="first_name" value="<?php echo e(old('first_name', $cifData['first_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Last Name</label><input required name="last_name" value="<?php echo e(old('last_name', $cifData['last_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Name Extension</label><input name="name_extension" value="<?php echo e(old('name_extension', $cifData['name_extension'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label><input name="middle_name" value="<?php echo e(old('middle_name', $cifData['middle_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="checkbox" name="no_middle_name" value="1" <?php if(old('no_middle_name', $cifData['no_middle_name'] ?? false)): echo 'checked'; endif; ?>> I have no Middle Name</label>
            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="checkbox" name="only_first_name" value="1" <?php if(old('only_first_name', $cifData['only_first_name'] ?? false)): echo 'checked'; endif; ?>> I only have a First Name</label>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Address</h3>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Present Address</label><input name="present_address_line1" value="<?php echo e(old('present_address_line1', $cifData['present_address_line1'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">ZIP Code</label><input name="zip_code" value="<?php echo e(old('zip_code', $cifData['zip_code'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div class="md:col-span-3"><label class="mb-1 block text-sm font-medium text-gray-700">Present Address (2nd line)</label><input name="present_address_line2" value="<?php echo e(old('present_address_line2', $cifData['present_address_line2'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Contact</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label><input type="email" name="email" value="<?php echo e(old('email', $cifData['email'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Phone No. / Mobile No.</label><input name="mobile" value="<?php echo e(old('mobile', $cifData['mobile'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Birth / Citizenship</h3>
        <div class="grid gap-4 md:grid-cols-3">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label><input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', $cifData['date_of_birth'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Place of Birth</label><input name="place_of_birth" value="<?php echo e(old('place_of_birth', $cifData['place_of_birth'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Citizenship / Nationality</label><input name="citizenship_nationality" value="<?php echo e(old('citizenship_nationality', $cifData['citizenship_nationality'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" data-citizenship-nationality-input <?php if($selectedCitizenshipType === 'filipino'): ?> readonly <?php endif; ?>></div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
            <?php $__currentLoopData = ['filipino' => 'Filipino', 'foreigner' => 'Foreigner', 'dual_citizen' => 'Dual Citizen']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="radio" name="citizenship_type" value="<?php echo e($value); ?>" <?php if(old('citizenship_type', $cifData['citizenship_type'] ?? '') === $value): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Gender / Civil Status</h3>
        <div class="mb-3 flex flex-wrap gap-2">
            <?php $__currentLoopData = ['male' => 'Male', 'female' => 'Female']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="radio" name="gender" value="<?php echo e($value); ?>" <?php if(old('gender', $cifData['gender'] ?? '') === $value): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Civil Status</label>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = ['single' => 'Single', 'separated' => 'Separated', 'widowed' => 'Widowed', 'married' => 'Married']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="radio" name="civil_status" value="<?php echo e($value); ?>" <?php if(old('civil_status', $cifData['civil_status'] ?? '') === $value): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div data-spouse-row <?php if($selectedCivilStatus !== 'married'): ?> style="display:none;" <?php endif; ?>><label class="mb-1 block text-sm font-medium text-gray-700">Spouse's Name</label><input name="spouse_name" value="<?php echo e(old('spouse_name', $cifData['spouse_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Work / IDs</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Nature of Work / Business</label><input name="nature_of_work_business" value="<?php echo e(old('nature_of_work_business', $cifData['nature_of_work_business'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">TIN</label><input name="tin" value="<?php echo e(old('tin', $cifData['tin'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Other Government ID</label><input name="other_government_id" value="<?php echo e(old('other_government_id', $cifData['other_government_id'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">ID Number</label><input name="id_number" value="<?php echo e(old('id_number', $cifData['id_number'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Mother's Maiden Name</label><input name="mothers_maiden_name" value="<?php echo e(old('mothers_maiden_name', $cifData['mothers_maiden_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Source of Funds</h3>
        <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-3">
            <?php $__currentLoopData = ['salary' => 'Salary', 'remittance' => 'Remittance', 'business' => 'Business', 'others' => 'Others', 'commission_fees' => 'Commission / Fees', 'retirement_pension' => 'Retirement / Pension']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="checkbox" name="source_of_funds[]" value="<?php echo e($value); ?>" <?php if(in_array($value, old('source_of_funds', $cifData['source_of_funds'] ?? []), true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-3"><label class="mb-1 block text-sm font-medium text-gray-700">Others (Specify)</label><input name="source_of_funds_other_text" value="<?php echo e(old('source_of_funds_other_text', $cifData['source_of_funds_other_text'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4" data-foreign-section <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Foreigner Details</h3>
        <div class="grid gap-4 md:grid-cols-3">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Foreigner Passport No.</label><input name="foreigner_passport_no" value="<?php echo e(old('foreigner_passport_no', $cifData['foreigner_passport_no'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Expiry Date</label><input type="date" name="foreigner_passport_expiry_date" value="<?php echo e(old('foreigner_passport_expiry_date', $cifData['foreigner_passport_expiry_date'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Place of Issue</label><input name="foreigner_passport_place_of_issue" value="<?php echo e(old('foreigner_passport_place_of_issue', $cifData['foreigner_passport_place_of_issue'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Foreigner ACR ID No.</label><input name="foreigner_acr_id_no" value="<?php echo e(old('foreigner_acr_id_no', $cifData['foreigner_acr_id_no'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Expiry Date</label><input type="date" name="foreigner_acr_expiry_date" value="<?php echo e(old('foreigner_acr_expiry_date', $cifData['foreigner_acr_expiry_date'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Place of Issue</label><input name="foreigner_acr_place_of_issue" value="<?php echo e(old('foreigner_acr_place_of_issue', $cifData['foreigner_acr_place_of_issue'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div class="md:col-span-3"><label class="mb-1 block text-sm font-medium text-gray-700">Visa Status</label><input name="visa_status" value="<?php echo e(old('visa_status', $cifData['visa_status'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Acknowledgment / Signature Lines</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Signature Name (Left)</label><input name="sig_name_left" value="<?php echo e(old('sig_name_left', $cifData['sig_name_left'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Signature Name (Right)</label><input name="sig_name_right" value="<?php echo e(old('sig_name_right', $cifData['sig_name_right'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Position (Left)</label><input name="sig_position_left" value="<?php echo e(old('sig_position_left', $cifData['sig_position_left'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Position (Right)</label><input name="sig_position_right" value="<?php echo e(old('sig_position_right', $cifData['sig_position_right'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Client Onboarding Requirements</h3>
        <div class="grid gap-2 text-sm">
            <div>1 | 2 Valid Government IDs</div>
            <div>2 | TIN ID (Signatory/Representative/Stockholders/Partners/Others)</div>
            <div>3 | AUTHORIZED SIGNATORY/SIGNATORY (Sole / OPC / Individual) SPECIMEN SIGNATURE CARD</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>4 | If Foreign Signatory/Director/Officer: Passport (Bio Page)</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>5 | If Foreign Signatory/Director/Officer: Valid Visa / ACR I-Card</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>6 | If Foreign Signatory/Director/Officer Alien Employment Permit (AEP)</div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">Internal Footer</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Referred By</label><input name="referred_by_footer" value="<?php echo e(old('referred_by_footer', $cifData['referred_by_footer'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Date</label><input type="date" name="referred_date" value="<?php echo e(old('referred_date', $cifData['referred_date'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Sales &amp; Marketing</label><input name="sales_marketing_footer" value="<?php echo e(old('sales_marketing_footer', $cifData['sales_marketing_footer'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Finance</label><input name="finance_footer" value="<?php echo e(old('finance_footer', $cifData['finance_footer'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">President</label><input name="president_footer" value="<?php echo e(old('president_footer', $cifData['president_footer'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
        </div>
    </section>

    <section class="rounded-lg border border-gray-200 p-4">
        <h3 class="mb-3 text-sm font-semibold text-gray-900">KYC Internal</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Owner Name</label><input name="owner_name" value="<?php echo e(old('owner_name', $cifData['owner_name'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">KYC Status</label><input name="kyc_status" value="<?php echo e(old('kyc_status', $cifData['kyc_status'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Date Verified</label><input type="date" name="date_verified" value="<?php echo e(old('date_verified', $cifData['date_verified'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Verified By</label><input name="verified_by" value="<?php echo e(old('verified_by', $cifData['verified_by'] ?? '')); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Remarks</label><textarea name="remarks" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('remarks', $cifData['remarks'] ?? '')); ?></textarea></div>
        </div>
    </section>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
        <a href="<?php echo e(route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc'])); ?>" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save CIF</button>
    </div>
</form>

<script>
    (function () {
        const form = document.querySelector('[data-cif-card-form]');
        if (!form) return;

        const spouseRow = form.querySelector('[data-spouse-row]');
        const foreignSection = form.querySelector('[data-foreign-section]');
        const foreignRequirements = form.querySelectorAll('[data-foreign-requirement]');
        const citizenshipNationalityInput = form.querySelector('[data-citizenship-nationality-input]');

        const getSelectedValue = (name) => form.querySelector(`input[name="${name}"]:checked`)?.value || '';

        const syncVisibility = () => {
            const citizenshipType = getSelectedValue('citizenship_type');
            const civilStatus = getSelectedValue('civil_status');
            const showForeign = citizenshipType === 'foreigner' || citizenshipType === 'dual_citizen';

            if (citizenshipNationalityInput) {
                if (citizenshipType === 'filipino') {
                    citizenshipNationalityInput.value = 'FILIPINO';
                    citizenshipNationalityInput.readOnly = true;
                    citizenshipNationalityInput.classList.add('bg-gray-100');
                } else {
                    citizenshipNationalityInput.readOnly = false;
                    citizenshipNationalityInput.classList.remove('bg-gray-100');
                    if (citizenshipNationalityInput.value.trim().toUpperCase() === 'FILIPINO') {
                        citizenshipNationalityInput.value = '';
                    }
                }
            }

            if (spouseRow) {
                spouseRow.style.display = civilStatus === 'married' ? '' : 'none';
            }

            if (foreignSection) {
                foreignSection.style.display = showForeign ? '' : 'none';
            }

            foreignRequirements.forEach((item) => {
                item.style.display = showForeign ? '' : 'none';
            });
        };

        form.querySelectorAll('input[name="citizenship_type"], input[name="civil_status"]').forEach((input) => {
            input.addEventListener('change', syncVisibility);
        });

        syncVisibility();
    })();
</script>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\contacts\partials\cif-edit-form.blade.php ENDPATH**/ ?>