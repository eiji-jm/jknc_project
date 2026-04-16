<?php
    $selectedCitizenshipType = old('citizenship_type', $cifData['citizenship_type'] ?? '');
    $selectedCivilStatus = old('civil_status', $cifData['civil_status'] ?? '');
?>

<form method="POST" action="<?php echo e(route('contacts.cif.save', $contact->id)); ?>" class="space-y-4" data-cif-document-form>
    <?php echo csrf_field(); ?>

    <div class="mx-auto w-full max-w-5xl border border-gray-700 bg-white text-[11px] leading-tight text-black">
        <div class="border-b border-gray-700 px-4 py-2 text-center">
            <h1 class="text-xl font-bold uppercase tracking-wide">Client Information Form</h1>
            <p class="mt-1 text-[10px] text-gray-600">Edit mode: update fields below, then Save CIF.</p>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-7 border-r border-gray-700 p-2">
                <div class="mb-1 text-[10px] text-gray-600">DATE</div>
                <input type="date" name="cif_date" value="<?php echo e(old('cif_date', $cifData['cif_date'] ?? '')); ?>" class="h-8 w-full border border-gray-300 px-2 text-[11px]">
            </div>
            <div class="col-span-5 p-2">
                <div class="mb-1 text-[10px] text-gray-600">CIF NO.</div>
                <input name="cif_no" value="<?php echo e(old('cif_no', $cifData['cif_no'] ?? '')); ?>" class="h-8 w-full border border-gray-300 px-2 text-[11px]">
            </div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase">
            <div class="col-span-12">Identity</div>
        </div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">FIRST NAME</div><input required name="first_name" value="<?php echo e(old('first_name', $cifData['first_name'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">LAST NAME</div><input name="last_name" value="<?php echo e(old('last_name', $cifData['last_name'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">NAME EXTENSION</div><input name="name_extension" value="<?php echo e(old('name_extension', $cifData['name_extension'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-4 p-2"><div class="text-[10px]">MIDDLE NAME</div><input name="middle_name" value="<?php echo e(old('middle_name', $cifData['middle_name'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <label class="col-span-6 border-r border-gray-700 p-2 text-[11px]"><input type="checkbox" name="no_middle_name" value="1" <?php if(old('no_middle_name', $cifData['no_middle_name'] ?? false)): echo 'checked'; endif; ?>> <span class="ml-1">I have no Middle Name</span></label>
            <label class="col-span-6 p-2 text-[11px]"><input type="checkbox" name="only_first_name" value="1" <?php if(old('only_first_name', $cifData['only_first_name'] ?? false)): echo 'checked'; endif; ?>> <span class="ml-1">I only have a First Name</span></label>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Address</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-9 border-r border-gray-700 p-2"><div class="text-[10px]">PRESENT ADDRESS</div><input name="present_address_line1" value="<?php echo e(old('present_address_line1', $cifData['present_address_line1'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 p-2"><div class="text-[10px]">ZIP CODE</div><input name="zip_code" value="<?php echo e(old('zip_code', $cifData['zip_code'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-12 p-2"><div class="text-[10px]">PRESENT ADDRESS (2ND LINE)</div><input name="present_address_line2" value="<?php echo e(old('present_address_line2', $cifData['present_address_line2'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Contact</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">EMAIL ADDRESS</div><input type="email" name="email" value="<?php echo e(old('email', $cifData['email'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-6 p-2"><div class="text-[10px]">PHONE NO. / MOBILE NO.</div><input name="mobile" value="<?php echo e(old('mobile', $cifData['mobile'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Birth / Citizenship</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">DATE OF BIRTH</div><input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', $cifData['date_of_birth'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">PLACE OF BIRTH</div><input name="place_of_birth" value="<?php echo e(old('place_of_birth', $cifData['place_of_birth'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-6 p-2"><div class="text-[10px]">CITIZENSHIP / NATIONALITY</div><input name="citizenship_nationality" value="<?php echo e(old('citizenship_nationality', $cifData['citizenship_nationality'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]" data-citizenship-nationality-input <?php if($selectedCitizenshipType === 'filipino'): ?> readonly <?php endif; ?>></div>
        </div>
        <div class="grid grid-cols-12 border-b border-gray-700 p-2" data-citizenship-radios>
            <div class="col-span-12 flex flex-wrap gap-4 text-[11px]">
                <?php $__currentLoopData = ['filipino' => 'Filipino', 'foreigner' => 'Foreigner', 'dual_citizen' => 'Dual Citizen']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label><input type="radio" name="citizenship_type" value="<?php echo e($value); ?>" <?php if(old('citizenship_type', $cifData['citizenship_type'] ?? '') === $value): echo 'checked'; endif; ?>> <span class="ml-1"><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Gender / Civil Status</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700 p-2">
            <div class="col-span-12 mb-2 flex flex-wrap gap-4 text-[11px]">
                <?php $__currentLoopData = ['male' => 'Male', 'female' => 'Female']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label><input type="radio" name="gender" value="<?php echo e($value); ?>" <?php if(old('gender', $cifData['gender'] ?? '') === $value): echo 'checked'; endif; ?>> <span class="ml-1"><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-span-12 mb-2 flex flex-wrap gap-4 text-[11px]" data-civil-status-radios>
                <?php $__currentLoopData = ['single' => 'Single', 'separated' => 'Separated', 'widowed' => 'Widowed', 'married' => 'Married']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label><input type="radio" name="civil_status" value="<?php echo e($value); ?>" <?php if(old('civil_status', $cifData['civil_status'] ?? '') === $value): echo 'checked'; endif; ?>> <span class="ml-1"><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-span-12" data-spouse-row <?php if($selectedCivilStatus !== 'married'): ?> style="display:none;" <?php endif; ?>><div class="text-[10px]">SPOUSE'S NAME</div><input name="spouse_name" value="<?php echo e(old('spouse_name', $cifData['spouse_name'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Work / IDs</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">NATURE OF WORK / BUSINESS</div><input name="nature_of_work_business" value="<?php echo e(old('nature_of_work_business', $cifData['nature_of_work_business'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">TIN</div><input name="tin" value="<?php echo e(old('tin', $cifData['tin'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">OTHER GOV'T ID</div><input name="other_government_id" value="<?php echo e(old('other_government_id', $cifData['other_government_id'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 p-2"><div class="text-[10px]">ID NUMBER</div><input name="id_number" value="<?php echo e(old('id_number', $cifData['id_number'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
        <div class="border-b border-gray-700 p-2"><div class="text-[10px]">MOTHER'S MAIDEN NAME</div><input name="mothers_maiden_name" value="<?php echo e(old('mothers_maiden_name', $cifData['mothers_maiden_name'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Source of Funds</div></div>
        <div class="border-b border-gray-700 p-2">
            <div class="mb-2 grid gap-2 sm:grid-cols-2 md:grid-cols-3 text-[11px]">
                <?php $__currentLoopData = ['salary' => 'Salary', 'remittance' => 'Remittance', 'business' => 'Business', 'others' => 'Others', 'commission_fees' => 'Commission / Fees', 'retirement_pension' => 'Retirement / Pension']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label><input type="checkbox" name="source_of_funds[]" value="<?php echo e($value); ?>" <?php if(in_array($value, old('source_of_funds', $cifData['source_of_funds'] ?? []), true)): echo 'checked'; endif; ?>> <span class="ml-1"><?php echo e($label); ?></span></label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <input name="source_of_funds_other_text" value="<?php echo e(old('source_of_funds_other_text', $cifData['source_of_funds_other_text'] ?? '')); ?>" placeholder="Others (Specify)" class="h-8 w-full border border-gray-300 px-2 text-[11px]">
        </div>

        <div data-foreign-section <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>
        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Foreigner Details</div></div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">PASSPORT NO.</div><input name="foreigner_passport_no" value="<?php echo e(old('foreigner_passport_no', $cifData['foreigner_passport_no'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">EXPIRY DATE</div><input type="date" name="foreigner_passport_expiry_date" value="<?php echo e(old('foreigner_passport_expiry_date', $cifData['foreigner_passport_expiry_date'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-6 p-2"><div class="text-[10px]">PLACE OF ISSUE</div><input name="foreigner_passport_place_of_issue" value="<?php echo e(old('foreigner_passport_place_of_issue', $cifData['foreigner_passport_place_of_issue'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">ACR ID NO.</div><input name="foreigner_acr_id_no" value="<?php echo e(old('foreigner_acr_id_no', $cifData['foreigner_acr_id_no'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">EXPIRY DATE</div><input type="date" name="foreigner_acr_expiry_date" value="<?php echo e(old('foreigner_acr_expiry_date', $cifData['foreigner_acr_expiry_date'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">PLACE OF ISSUE</div><input name="foreigner_acr_place_of_issue" value="<?php echo e(old('foreigner_acr_place_of_issue', $cifData['foreigner_acr_place_of_issue'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 p-2"><div class="text-[10px]">VISA STATUS</div><input name="visa_status" value="<?php echo e(old('visa_status', $cifData['visa_status'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
        </div>

        <div class="border-b border-gray-700 p-2">
            <div class="mb-1 text-center font-semibold uppercase">Acknowledgment</div>
            <p class="px-1 text-[11px] leading-snug text-gray-700 text-justify">
                By signing this Client Information Form, I certify that all personal information provided herein is true, correct, and complete to the best of my knowledge. I agree to comply with the policies, procedures, and service guidelines of JK&C Inc. and authorize JK&C Inc., its officers, employees, consultants, and representatives to collect, verify, record, process, store, and use the information provided for purposes of client registration, due diligence, compliance verification, service engagement, documentation, billing, and regulatory requirements. In accordance with the Data Privacy Act of 2012 (Republic Act No. 10173), I voluntarily consent to the collection, processing, storage, and lawful use of my personal information contained in this form. I acknowledge that the information provided shall constitute the official client information on record of JK&C Inc. and may be relied upon in official communications, notices, service documents, billing statements, formal correspondence, and demand letters relating to services rendered or obligations arising from the engagement. I undertake to promptly notify JK&C Inc. of any changes to the information provided and hereby waive and release JK&C Inc., its officers, employees, and representatives from any liability arising from reliance on the information provided, except in cases of gross negligence or willful misconduct.
            </p>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px] font-semibold uppercase"><div class="col-span-12">Client Onboarding Requirements</div></div>
        <div class="border-b border-gray-700 p-2 text-[11px] grid gap-1" data-onboarding-requirements>
            <div>1 | 2 Valid Government IDs</div>
            <div>2 | TIN ID (Signatory/Representative/Stockholders/Partners/Others)</div>
            <div>3 | AUTHORIZED SIGNATORY/SIGNATORY (Sole / OPC / Individual) SPECIMEN SIGNATURE CARD</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>4 | If Foreign Signatory/Director/Officer: Passport (Bio Page)</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>5 | If Foreign Signatory/Director/Officer: Valid Visa / ACR I-Card</div>
            <div data-foreign-requirement <?php if(!in_array($selectedCitizenshipType, ['foreigner', 'dual_citizen'], true)): ?> style="display:none;" <?php endif; ?>>6 | If Foreign Signatory/Director/Officer Alien Employment Permit (AEP)</div>
        </div>

        <div class="grid grid-cols-12 border-b border-gray-700">
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px] font-semibold">REFERRED BY / DATE</div><input name="referred_by_footer" value="<?php echo e(old('referred_by_footer', $cifData['referred_by_footer'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"><input type="date" name="referred_date" value="<?php echo e(old('referred_date', $cifData['referred_date'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px] font-semibold">SALES &amp; MARKETING</div><input name="sales_marketing_footer" value="<?php echo e(old('sales_marketing_footer', $cifData['sales_marketing_footer'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px] font-semibold">FINANCE</div><input name="finance_footer" value="<?php echo e(old('finance_footer', $cifData['finance_footer'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-3 p-2"><div class="text-[10px] font-semibold">PRESIDENT</div><input name="president_footer" value="<?php echo e(old('president_footer', $cifData['president_footer'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>

        <div class="grid grid-cols-12">
            <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">Signature over Printed Name</div><input name="sig_name_left" value="<?php echo e(old('sig_name_left', $cifData['sig_name_left'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"><div class="mt-2 text-[10px]">Position</div><input name="sig_position_left" value="<?php echo e(old('sig_position_left', $cifData['sig_position_left'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
            <div class="col-span-6 p-2"><div class="text-[10px]">Signature over Printed Name</div><input name="sig_name_right" value="<?php echo e(old('sig_name_right', $cifData['sig_name_right'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"><div class="mt-2 text-[10px]">Position</div><input name="sig_position_right" value="<?php echo e(old('sig_position_right', $cifData['sig_position_right'] ?? '')); ?>" class="mt-1 h-8 w-full border border-gray-300 px-2 text-[11px]"></div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
        <a href="<?php echo e(route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc'])); ?>" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save CIF</button>
    </div>
</form>

<script>
    (function () {
        const form = document.querySelector('[data-cif-document-form]');
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
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/contacts/partials/cif-document-edit.blade.php ENDPATH**/ ?>