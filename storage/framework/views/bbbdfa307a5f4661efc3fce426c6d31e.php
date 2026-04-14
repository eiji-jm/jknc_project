<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'addCompanyModal','width' => 'sm:max-w-[720px] lg:max-w-[820px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'addCompanyModal','width' => 'sm:max-w-[720px] lg:max-w-[820px]']); ?>
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Create Company</h2>
                <p class="mt-1 text-sm text-gray-500">Capture the Business Information Form once and reuse it across Company KYC.</p>
            </div>
            <button type="button" data-close-company-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('company.store')); ?>" class="flex min-h-0 flex-1 flex-col" x-data="companyBifForm()" x-init="init()">
        <?php echo csrf_field(); ?>
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
                        <input type="text" name="bif_no" value="<?php echo e(old('bif_no')); ?>" placeholder="Auto-generated after save" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Date</label>
                        <input type="date" name="bif_date" value="<?php echo e(old('bif_date', now()->format('Y-m-d'))); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Primary Contact Link</h3>
                <p class="mb-4 text-xs text-gray-500">Select the approved contact record to link this company profile.</p>
                <?php if(!($hasApprovedCompanyCreateContacts ?? false)): ?>
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        No approved contacts are available yet. Approve a contact CIF first before creating a company.
                    </div>
                <?php endif; ?>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Contact <span class="text-red-500">*</span></label>
                    <select name="contact_id" x-on:change="hydrateFromContact($event)" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:text-gray-500" required <?php if(!($hasApprovedCompanyCreateContacts ?? false)): echo 'disabled'; endif; ?>>
                        <option value="">Select contact</option>
                        <?php $__currentLoopData = ($companyCreateContacts ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $contactName = trim(collect([$contact->first_name, $contact->last_name])->filter()->implode(' '));
                                $contactCompany = $contact->company_name ? ' • '.$contact->company_name : '';
                            ?>
                            <option
                                value="<?php echo e($contact->id); ?>"
                                data-company-name="<?php echo e(($contact->company_autofill['business_name'] ?? null) ?: $contact->company_name); ?>"
                                data-email="<?php echo e(($contact->company_autofill['authorized_contact_person_email'] ?? null) ?: $contact->email); ?>"
                                data-phone="<?php echo e(($contact->company_autofill['authorized_contact_person_phone'] ?? null) ?: $contact->phone); ?>"
                                data-business-phone="<?php echo e(($contact->company_autofill['business_phone'] ?? null) ?: $contact->phone); ?>"
                                data-mobile="<?php echo e(($contact->company_autofill['mobile_no'] ?? null) ?: $contact->phone); ?>"
                                data-address="<?php echo e(($contact->company_autofill['business_address'] ?? null) ?: $contact->contact_address); ?>"
                                data-tin="<?php echo e(($contact->company_autofill['tin_no'] ?? null) ?: $contact->tin); ?>"
                                data-zip-code="<?php echo e($contact->company_autofill['zip_code'] ?? ''); ?>"
                                data-nationality-status="<?php echo e($contact->company_autofill['nationality_status'] ?? ''); ?>"
                                data-business-organization="<?php echo e($contact->company_autofill['business_organization'] ?? ''); ?>"
                                data-business-organization-other="<?php echo e($contact->company_autofill['business_organization_other'] ?? ''); ?>"
                                data-office-type="<?php echo e($contact->company_autofill['office_type'] ?? ''); ?>"
                                data-office-type-other="<?php echo e($contact->company_autofill['office_type_other'] ?? ''); ?>"
                                data-alternative-business-name="<?php echo e($contact->company_autofill['alternative_business_name'] ?? ''); ?>"
                                data-contact-name="<?php echo e(($contact->company_autofill['authorized_contact_person_name'] ?? null) ?: ($contact->contact_full_name ?: trim(collect([$contact->first_name, $contact->last_name])->filter()->implode(' ')))); ?>"
                                data-contact-position="<?php echo e(($contact->company_autofill['authorized_contact_person_position'] ?? null) ?: $contact->position); ?>"
                                <?php if((string) old('contact_id') === (string) $contact->id): echo 'selected'; endif; ?>
                            >
                                <?php echo e($contactName !== '' ? $contactName : 'Contact #'.$contact->id); ?><?php echo e($contactCompany); ?> • CIF: APPROVED
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Only contacts with approved CIF can be linked to a new company.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Information</h3>
                <p class="mb-4 text-xs text-gray-500">Store the primary business identity, organization, and registered office details.</p>
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Business Organization</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <?php $__currentLoopData = ['sole_proprietorship' => 'Sole Proprietorship', 'partnership' => 'Partnership', 'corporation' => 'Corporation', 'cooperative' => 'Cooperative', 'ngo' => 'NGO', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                    <input type="radio" name="business_organization" value="<?php echo e($value); ?>" x-model="businessOrganization" <?php if(old('business_organization') === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span><?php echo e($label); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Nationality</label>
                            <div class="grid gap-2 sm:grid-cols-2">
                                <?php $__currentLoopData = ['filipino' => 'Filipino', 'foreign' => 'Foreign']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                        <input type="radio" name="nationality_status" value="<?php echo e($value); ?>" <?php if(old('nationality_status', 'filipino') === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span><?php echo e($label); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Type of Office</label>
                            <select name="office_type" x-model="officeType" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select office type</option>
                                <option value="head_office" <?php if(old('office_type') === 'head_office'): echo 'selected'; endif; ?>>Head Office</option>
                                <option value="branch" <?php if(old('office_type') === 'branch'): echo 'selected'; endif; ?>>Branch</option>
                                <option value="regional_headquarter" <?php if(old('office_type') === 'regional_headquarter'): echo 'selected'; endif; ?>>Regional Headquarter</option>
                                <option value="other" <?php if(old('office_type') === 'other'): echo 'selected'; endif; ?>>Other</option>
                            </select>
                        </div>
                        <div x-show="businessOrganization === 'other'" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Organization</label>
                            <input type="text" name="business_organization_other" value="<?php echo e(old('business_organization_other')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div x-show="officeType === 'other'" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Other Office Type</label>
                            <input type="text" name="office_type_other" value="<?php echo e(old('office_type_other')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Business Name <span class="text-red-500">*</span></label>
                            <input type="text" name="business_name" value="<?php echo e(old('business_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Alternative / Business Name / Style</label>
                            <input type="text" name="alternative_business_name" value="<?php echo e(old('alternative_business_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">ZIP Code</label>
                            <input type="text" name="zip_code" value="<?php echo e(old('zip_code')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Business Address</label>
                            <textarea name="business_address" rows="3" placeholder="House/Unit No., Street, Barangay, City/Town, Province" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('business_address')); ?></textarea>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Contact & Registration</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Business Phone</label><input type="text" name="business_phone" value="<?php echo e(old('business_phone')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Mobile No.</label><input type="text" name="mobile_no" value="<?php echo e(old('mobile_no')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">TIN No.</label><input type="text" name="tin_no" value="<?php echo e(old('tin_no')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Place of Incorporation</label><input type="text" name="place_of_incorporation" value="<?php echo e(old('place_of_incorporation')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Date of Incorporation</label><input type="date" name="date_of_incorporation" value="<?php echo e(old('date_of_incorporation')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Industry / Nature of Business</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = ['services' => 'Services', 'export_import' => 'Export/Import', 'education' => 'Education', 'financial_services' => 'Financial Services', 'transportation' => 'Transportation', 'distribution' => 'Distribution', 'manufacturing' => 'Manufacturing', 'government' => 'Government', 'wholesale_retail_trade' => 'Wholesale/Retail Trade', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="industry_types[]" value="<?php echo e($value); ?>" <?php if(in_array($value, old('industry_types', []), true)): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Other Industry</label>
                    <input type="text" name="industry_other_text" value="<?php echo e(old('industry_other_text')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Capital & Employees</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Authorized Capital</label>
                        <div class="space-y-2">
                            <?php $__currentLoopData = ['micro' => 'Micro (P3M below)', 'small' => 'Small (P3M-P15M)', 'medium' => 'Medium (P15M-P100M)', 'large' => 'Large (P100M above)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                                    <input type="radio" name="capital_category" value="<?php echo e($value); ?>" <?php if(old('capital_category') === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span><?php echo e($label); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Male</label><input type="number" min="0" name="employee_male" x-model.number="employees.male" @input="syncEmployeeTotal()" value="<?php echo e(old('employee_male')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Female</label><input type="number" min="0" name="employee_female" x-model.number="employees.female" @input="syncEmployeeTotal()" value="<?php echo e(old('employee_female')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">PWD</label><input type="number" min="0" name="employee_pwd" x-model.number="employees.pwd" @input="syncEmployeeTotal()" value="<?php echo e(old('employee_pwd')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                        <div><label class="mb-2 block text-sm font-medium text-gray-700">Total</label><input type="number" min="0" name="employee_total" x-model.number="employees.total" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-700 outline-none"></div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Source of Funds</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = ['revenue_income' => 'Revenue/Income', 'investments' => 'Investments', 'remittance' => 'Remittance', 'fees' => 'Fees', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="source_of_funds[]" value="<?php echo e($value); ?>" <?php if(in_array($value, old('source_of_funds', []), true)): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Other Source of Funds</label>
                    <input type="text" name="source_other_text" value="<?php echo e(old('source_other_text')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Key Officers</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name of President</label><input type="text" name="president_name" value="<?php echo e(old('president_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name of Treasurer</label><input type="text" name="treasurer_name" value="<?php echo e(old('treasurer_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
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
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Name</label><input type="text" name="authorized_contact_person_name" value="<?php echo e(old('authorized_contact_person_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Position</label><input type="text" name="authorized_contact_person_position" value="<?php echo e(old('authorized_contact_person_position')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Email</label><input type="email" name="authorized_contact_person_email" value="<?php echo e(old('authorized_contact_person_email')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Phone / Mobile</label><input type="text" name="authorized_contact_person_phone" value="<?php echo e(old('authorized_contact_person_phone')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Acknowledgment</h3>
                <p class="mb-4 text-xs text-gray-500">By submitting this form, the company confirms that all business information provided is true and may be used for onboarding, compliance, and KYC review.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Signature over Printed Name</label><input type="text" name="signature_printed_name" value="<?php echo e(old('signature_printed_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Position</label><input type="text" name="signature_position" value="<?php echo e(old('signature_position')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Review Signature over Printed Name</label><input type="text" name="review_signature_printed_name" value="<?php echo e(old('review_signature_printed_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Review Position</label><input type="text" name="review_signature_position" value="<?php echo e(old('review_signature_position')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Onboarding Requirements</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700" x-show="businessOrganization === 'sole_proprietorship' || !businessOrganization" x-cloak>
                        <p class="mb-2 font-semibold text-gray-900">Sole / Individual</p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>DTI Certificate of Registration (if Sole Prop)</li>
                            <li>BIR Certificate of Registration (COR)</li>
                            <li>Business Permit / Mayor's Permit</li>
                            <li>Proof of Billing (Residential)</li>
                            <li>Proof of Billing (Business Address if different)</li>
                            <li>Special Power of Attorney (if representative)</li>
                            <li>Representative's 2 Valid IDs (if applicable)</li>
                        </ul>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700" x-show="['partnership', 'corporation', 'cooperative', 'ngo', 'other'].includes(businessOrganization) || !businessOrganization" x-cloak>
                        <p class="mb-2 font-semibold text-gray-900">Juridical Entity</p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>SEC / CDA Certificate of Registration</li>
                            <li>BIR Certificate of Registration (COR)</li>
                            <li>Business Permit / Mayor's Permit</li>
                            <li>Articles of Incorporation / Partnership</li>
                            <li>By-Laws</li>
                            <li>Latest General Information Sheet (GIS)</li>
                            <li>Appointment of Officers (for OPC, if applicable)</li>
                            <li>Secretary Certificate OR Board Resolution</li>
                            <li>Ultimate Beneficial Owner (UBO) Declaration</li>
                            <li>Proof of Billing (Company Address)</li>
                            <li>Proof of Billing (Authorized Representative, if applicable)</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">JK&C Internal Use</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Referred By</label><input type="text" name="referred_by" value="<?php echo e(old('referred_by')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Consultant Lead</label><input type="text" name="consultant_lead" value="<?php echo e(old('consultant_lead')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">Lead Associate</label><input type="text" name="lead_associate" value="<?php echo e(old('lead_associate')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label class="mb-2 block text-sm font-medium text-gray-700">President</label><input type="text" name="president_use_only_name" value="<?php echo e(old('president_use_only_name')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <?php if($errors->any()): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
        </div>

        <div class="mt-auto border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-company-modal class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" <?php if(!($hasApprovedCompanyCreateContacts ?? false)): echo 'disabled'; endif; ?>>Save Company</button>
            </div>
        </div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $attributes = $__attributesOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__attributesOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ef8dd008d82ca426db4c565227b1725)): ?>
<?php $component = $__componentOriginal6ef8dd008d82ca426db4c565227b1725; ?>
<?php unset($__componentOriginal6ef8dd008d82ca426db4c565227b1725); ?>
<?php endif; ?>

<script>
function companyBifForm() {
    return {
        businessOrganization: <?php echo \Illuminate\Support\Js::from(old('business_organization', ''))->toHtml() ?>,
        officeType: <?php echo \Illuminate\Support\Js::from(old('office_type', ''))->toHtml() ?>,
        employees: {
            male: Number(<?php echo \Illuminate\Support\Js::from(old('employee_male', 0))->toHtml() ?>) || 0,
            female: Number(<?php echo \Illuminate\Support\Js::from(old('employee_female', 0))->toHtml() ?>) || 0,
            pwd: Number(<?php echo \Illuminate\Support\Js::from(old('employee_pwd', 0))->toHtml() ?>) || 0,
            total: Number(<?php echo \Illuminate\Support\Js::from(old('employee_total', 0))->toHtml() ?>) || 0,
        },
        signatories: <?php echo \Illuminate\Support\Js::from(old('authorized_signatories', [['full_name' => '', 'address' => '', 'nationality' => '', 'date_of_birth' => '', 'tin' => '', 'position' => '']]))->toHtml() ?>,
        ubos: <?php echo \Illuminate\Support\Js::from(old('ubos', [['full_name' => '', 'address' => '', 'nationality' => '', 'date_of_birth' => '', 'tin' => '', 'position' => '']]))->toHtml() ?>,
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
        setFieldValue(fieldName, value) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (!input) return;
            const nextValue = String(value || '').trim();
            if (nextValue === '' && String(input.value || '').trim() !== '') return;
            input.value = String(value || '');
        },
        hydrateFromContact(event) {
            const option = event?.target?.selectedOptions?.[0];
            if (!option) return;

            const companyName = option.dataset.companyName || '';
            const email = option.dataset.email || '';
            const phone = option.dataset.phone || '';
            const businessPhone = option.dataset.businessPhone || phone;
            const mobile = option.dataset.mobile || phone;
            const address = option.dataset.address || '';
            const tin = option.dataset.tin || '';
            const zipCode = option.dataset.zipCode || '';
            const nationalityStatus = option.dataset.nationalityStatus || '';
            const businessOrganization = option.dataset.businessOrganization || '';
            const businessOrganizationOther = option.dataset.businessOrganizationOther || '';
            const officeType = option.dataset.officeType || '';
            const officeTypeOther = option.dataset.officeTypeOther || '';
            const alternativeBusinessName = option.dataset.alternativeBusinessName || '';
            const contactName = option.dataset.contactName || '';
            const contactPosition = option.dataset.contactPosition || '';

            this.setFieldValue('business_name', companyName);
            this.setFieldValue('alternative_business_name', alternativeBusinessName);
            this.setFieldValue('authorized_contact_person_email', email);
            this.setFieldValue('authorized_contact_person_phone', phone);
            this.setFieldValue('authorized_contact_person_name', contactName);
            this.setFieldValue('authorized_contact_person_position', contactPosition);
            this.setFieldValue('business_phone', businessPhone);
            this.setFieldValue('mobile_no', mobile);
            this.setFieldValue('business_address', address);
            this.setFieldValue('tin_no', tin);
            this.setFieldValue('zip_code', zipCode);
            if (String(businessOrganization || '').trim() !== '' || String(this.businessOrganization || '').trim() === '') {
                this.businessOrganization = businessOrganization;
            }
            this.setFieldValue('business_organization_other', businessOrganizationOther);
            if (String(officeType || '').trim() !== '' || String(this.officeType || '').trim() === '') {
                this.officeType = officeType;
            }
            this.setFieldValue('office_type_other', officeTypeOther);

            if (nationalityStatus) {
                const nationalityInput = document.querySelector(`[name="nationality_status"][value="${nationalityStatus}"]`);
                if (nationalityInput) {
                    nationalityInput.checked = true;
                }
            }
        },
        syncEmployeeTotal() {
            this.employees.total = (Number(this.employees.male) || 0) + (Number(this.employees.female) || 0) + (Number(this.employees.pwd) || 0);
        },
    };
}
</script>
<?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/company/partials/modal-add-company.blade.php ENDPATH**/ ?>