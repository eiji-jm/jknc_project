<?php
    $intakeServiceInquiryOptions = [
        'Business Registration / Entity Formation',
        'Business Permit (New / Renewal)',
        'Tax Compliance / BIR Filing',
        'Accounting / Bookkeeping',
        'Financial Statements Preparation',
        'Corporate Officers Services',
        'Business Advisory / Consultation',
        'Regulatory Compliance',
        'Other',
    ];
    $intakeRecommendationOptions = [
        'Proceed to Proposal Preparation',
        'Refer to Senior Consultant',
        'Refer to Subject Matter Expert',
        'For Further Study / Assessment',
        'For Due Diligence / Background Check',
        'Schedule Consultation Meeting',
        'Request Additional Information from Client',
        'Not Suitable for Engagement',
        'Others',
    ];
    $intakeLeadSourceOptions = [
        'Facebook', 'Instagram', 'LinkedIn', 'Tiktok', 'Website', 'Google Search',
        'Google Ads', 'Walk-In', 'Referral-Client', 'Referral-Partner', 'Referral-Employee',
        'Email Inquiry', 'Phone Call', 'SMS/Viber', 'WhatsApp', 'Online Market Place',
        'Event Seminar', 'Webinar', 'Trade Show Expo', 'Flyer / Brochure',
        'Radio Advertisement', 'Returning Client', 'Influencer / Content Creator',
        'Television Advertisement', 'Other',
    ];
    $intakeLeadStageOptions = ['Inquiry', 'Qualification', 'Consultation', 'Proposal', 'Negotiation', 'Payment', 'Activation', 'Closed Lost'];
    $intakeServiceTypes = old('_from_contact_intake_edit') ? old('service_inquiry_types', []) : ($contact->service_inquiry_types ?? []);
    $intakeRecommendations = old('_from_contact_intake_edit') ? old('recommendation_options', []) : ($contact->recommendation_options ?? []);
    $intakeLeadSources = old('_from_contact_intake_edit') ? old('lead_source_channels', []) : ($contact->lead_source_channels ?? []);
?>

<?php if (isset($component)) { $__componentOriginal6ef8dd008d82ca426db4c565227b1725 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ef8dd008d82ca426db4c565227b1725 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.slide-over','data' => ['id' => 'contactIntakeModal','width' => 'sm:max-w-[720px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('slide-over'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'contactIntakeModal','width' => 'sm:max-w-[720px]']); ?>
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">View KYC Form</h2>
                <p class="mt-1 text-sm text-gray-500">Review the saved contact intake data. Switch to edit mode to update the full intake form.</p>
            </div>
            <button type="button" data-close-contact-intake-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form id="contactIntakeForm" method="POST" action="<?php echo e(route('contacts.update', $contact->id)); ?>" class="flex min-h-0 flex-1 flex-col">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="_from_contact_intake_edit" value="1">
        <input type="hidden" name="owner_id" value="<?php echo e(old('owner_id', $contact->owner_id ?? '')); ?>">

        <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="space-y-4 border-b border-gray-100 pb-5">
                <div>
                    <p class="text-sm font-medium text-gray-500">Client Intake</p>
                    <p class="text-xs text-gray-400">Use this as the main contact and business record.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 sm:items-end">
                    <div>
                        <label for="intake_business_date" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Business Date</label>
                        <input id="intake_business_date" type="date" name="business_date" value="<?php echo e(old('business_date', $contact->business_date ? \Illuminate\Support\Carbon::parse($contact->business_date)->format('Y-m-d') : null)); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Owner</label>
                        <input type="text" value="<?php echo e($contact->owner_name ?: 'Admin User'); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Created At</label>
                        <input type="text" value="<?php echo e(optional($contact->created_at)->format('F j, Y • g:i A')); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 outline-none" readonly>
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                <h3 class="text-base font-semibold text-gray-900">Customer Type</h3>
                <p class="mb-4 text-xs text-gray-500">Classify the record before entering the full client profile.</p>
                <div class="space-y-3">
                    <div class="grid items-center gap-2 sm:grid-cols-[72px_1fr]">
                        <label class="text-sm font-medium text-gray-700">Type</label>
                        <div class="grid grid-cols-2 gap-2">
                            <?php $__currentLoopData = ['business' => 'Business', 'individual' => 'Individual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-blue-200 hover:bg-blue-50/40">
                                    <input type="radio" name="customer_type" value="<?php echo e($value); ?>" <?php if(old('customer_type', $contact->customer_type) === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span><?php echo e($label); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Contact Information</h3>
                <p class="mb-4 text-xs text-gray-500">Primary contact and profile details from the client form.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="intake_salutation" class="mb-1 block text-sm font-medium text-gray-700">Salutation</label><select id="intake_salutation" name="salutation" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="" disabled <?php if(blank(old('salutation', $contact->salutation))): echo 'selected'; endif; ?>>Select salutation</option><?php $__currentLoopData = ['Mr.', 'Ms.', 'Mrs.', 'Atty.', 'CPA', 'Engr.', 'Dr.']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php if(old('salutation', $contact->salutation) === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label for="intake_sex" class="mb-1 block text-sm font-medium text-gray-700">Sex</label><select id="intake_sex" name="sex" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="" disabled <?php if(blank(old('sex', $contact->sex))): echo 'selected'; endif; ?>>Select sex</option><?php $__currentLoopData = ['Male', 'Female', 'Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php if(old('sex', $contact->sex) === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label for="intake_first_name" class="mb-1 block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label><input id="intake_first_name" name="first_name" required value="<?php echo e(old('first_name', $contact->first_name)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div><label for="intake_middle_initial" class="mb-1 block text-sm font-medium text-gray-700">Middle Initial</label><input id="intake_middle_initial" name="middle_initial" value="<?php echo e(old('middle_initial', $contact->middle_initial)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_middle_name" class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label><input id="intake_middle_name" name="middle_name" value="<?php echo e(old('middle_name', $contact->middle_name)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_last_name" class="mb-1 block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label><input id="intake_last_name" name="last_name" required value="<?php echo e(old('last_name', $contact->last_name)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div><label for="intake_name_extension" class="mb-1 block text-sm font-medium text-gray-700">Name Extension</label><input id="intake_name_extension" name="name_extension" value="<?php echo e(old('name_extension', $contact->name_extension)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_date_of_birth" class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label><input id="intake_date_of_birth" type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', $contact->date_of_birth ? \Illuminate\Support\Carbon::parse($contact->date_of_birth)->format('Y-m-d') : null)); ?>" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_email" class="mb-1 block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label><input id="intake_email" name="email" type="email" required value="<?php echo e(old('email', $contact->email)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div><label for="intake_mobile_number" class="mb-1 block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label><input id="intake_mobile_number" name="mobile_number" required value="<?php echo e(old('mobile_number', $contact->phone)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php $__errorArgs = ['mobile_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="sm:col-span-2"><label for="intake_contact_address" class="mb-1 block text-sm font-medium text-gray-700">Address</label><textarea id="intake_contact_address" name="contact_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('contact_address', $contact->contact_address)); ?></textarea></div>
                    <div><label for="intake_company_name" class="mb-1 block text-sm font-medium text-gray-700">Company</label><input id="intake_company_name" name="company_name" value="<?php echo e(old('company_name', $contact->company_name)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_position" class="mb-1 block text-sm font-medium text-gray-700">Position / Designation</label><input id="intake_position" name="position" value="<?php echo e(old('position', $contact->position)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2"><label for="intake_company_address" class="mb-1 block text-sm font-medium text-gray-700">Company Address</label><textarea id="intake_company_address" name="company_address" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('company_address', $contact->company_address)); ?></textarea></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Business Information</h3>
                <p class="mb-4 text-xs text-gray-500">Capture ownership, structure, and business capacity details.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="intake_business_type_organization" class="mb-1 block text-sm font-medium text-gray-700">Business Type / Organization</label><input id="intake_business_type_organization" name="business_type_organization" value="<?php echo e(old('business_type_organization', $contact->business_type_organization)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Organization Structure</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <?php $__currentLoopData = ['Sole Proprietorship', 'Partnership', 'Non-Stock', 'Corporation', 'Stock', 'Others']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700"><input type="radio" name="organization_type" value="<?php echo e($option); ?>" <?php if(old('organization_type', $contact->organization_type) === $option): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"><span><?php echo e($option); ?></span></label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div id="intakeOrganizationTypeOtherWrap" class="<?php echo e(old('organization_type', $contact->organization_type) === 'Others' ? '' : 'hidden'); ?> sm:col-span-2"><label for="intake_organization_type_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Specify)</label><input id="intake_organization_type_other" name="organization_type_other" value="<?php echo e(old('organization_type_other', $contact->organization_type_other)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_nature_of_business" class="mb-1 block text-sm font-medium text-gray-700">Nature of Business / Industry</label><input id="intake_nature_of_business" name="nature_of_business" value="<?php echo e(old('nature_of_business', $contact->nature_of_business)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_capitalization_amount" class="mb-1 block text-sm font-medium text-gray-700">Capitalization / Capital Investment</label><input id="intake_capitalization_amount" name="capitalization_amount" value="<?php echo e(old('capitalization_amount', $contact->capitalization_amount)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_ownership_structure" class="mb-1 block text-sm font-medium text-gray-700">Ownership Structure</label><input id="intake_ownership_structure" name="ownership_structure" value="<?php echo e(old('ownership_structure', $contact->ownership_structure)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_previous_year_revenue" class="mb-1 block text-sm font-medium text-gray-700">Previous Year Total Sales / Revenue</label><input id="intake_previous_year_revenue" name="previous_year_revenue" value="<?php echo e(old('previous_year_revenue', $contact->previous_year_revenue)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_years_operating" class="mb-1 block text-sm font-medium text-gray-700">How Long the Business Has Been Operating</label><input id="intake_years_operating" name="years_operating" value="<?php echo e(old('years_operating', $contact->years_operating)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_projected_current_year_revenue" class="mb-1 block text-sm font-medium text-gray-700">Projected Sales / Revenue for the Current Year</label><input id="intake_projected_current_year_revenue" name="projected_current_year_revenue" value="<?php echo e(old('projected_current_year_revenue', $contact->projected_current_year_revenue)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Ownership Nationality</label>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <?php $__currentLoopData = ['100% Filipino-Owned', 'With Foreign Ownership', 'Foreign-Owned Business']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700"><input type="radio" name="ownership_flag" value="<?php echo e($option); ?>" <?php if(old('ownership_flag', $contact->ownership_flag) === $option): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"><span><?php echo e($option); ?></span></label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div id="intakeForeignBusinessNatureWrap" class="<?php echo e(old('ownership_flag', $contact->ownership_flag) === 'Foreign-Owned Business' ? '' : 'hidden'); ?> sm:col-span-2"><label for="intake_foreign_business_nature" class="mb-1 block text-sm font-medium text-gray-700">Foreign-Owned Business (Specify Nature of Business)</label><textarea id="intake_foreign_business_nature" name="foreign_business_nature" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('foreign_business_nature', $contact->foreign_business_nature)); ?></textarea></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Service Inquiry Type</h3>
                <p class="mb-4 text-xs text-gray-500">Select one or more service inquiry categories.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = $intakeServiceInquiryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="service_inquiry_types[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $intakeServiceTypes, true)): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span><?php echo e($option); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="intakeServiceInquiryOtherWrap" class="<?php echo e(in_array('Other', $intakeServiceTypes, true) ? '' : 'hidden'); ?> mt-3"><label for="intake_service_inquiry_other" class="mb-1 block text-sm font-medium text-gray-700">Other Service Inquiry</label><input id="intake_service_inquiry_other" name="service_inquiry_other" value="<?php echo e(old('service_inquiry_other', $contact->service_inquiry_other)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4"><h3 class="text-base font-semibold text-gray-900">Inquiry</h3><p class="mb-4 text-xs text-gray-500">Add the client's inquiry details.</p><textarea id="intake_inquiry" name="inquiry" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('inquiry', $contact->inquiry)); ?></textarea></section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">For JKNC Use Only</h3>
                <p class="mb-4 text-xs text-gray-500">Internal notes and assignment details.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="intake_jknc_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label><textarea id="intake_jknc_notes" name="jknc_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('jknc_notes', $contact->jknc_notes)); ?></textarea></div>
                    <div class="sm:col-span-2"><label for="intake_sales_marketing" class="mb-1 block text-sm font-medium text-gray-700">Sales & Marketing</label><textarea id="intake_sales_marketing" name="sales_marketing" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('sales_marketing', $contact->sales_marketing)); ?></textarea></div>
                    <div><label for="intake_consultant_lead" class="mb-1 block text-sm font-medium text-gray-700">Consultant Lead</label><input id="intake_consultant_lead" name="consultant_lead" value="<?php echo e(old('consultant_lead', $contact->consultant_lead)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                    <div><label for="intake_lead_associate" class="mb-1 block text-sm font-medium text-gray-700">Lead Associate</label><input id="intake_lead_associate" name="lead_associate" value="<?php echo e(old('lead_associate', $contact->lead_associate)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Recommendation</h3>
                <p class="mb-4 text-xs text-gray-500">Choose one or more recommended next actions.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = $intakeRecommendationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="recommendation_options[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $intakeRecommendations, true)): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span><?php echo e($option); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="intakeRecommendationOtherWrap" class="<?php echo e(in_array('Others', $intakeRecommendations, true) ? '' : 'hidden'); ?> mt-3"><label for="intake_recommendation_other" class="mb-1 block text-sm font-medium text-gray-700">Others (Specify)</label><input id="intake_recommendation_other" name="recommendation_other" value="<?php echo e(old('recommendation_other', $contact->recommendation_other)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Lead Source</h3>
                <p class="mb-4 text-xs text-gray-500">Track all channels that generated this lead.</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php $__currentLoopData = $intakeLeadSourceOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="lead_source_channels[]" value="<?php echo e($option); ?>" <?php if(in_array($option, $intakeLeadSources, true)): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span><?php echo e($option); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="intakeLeadSourceOtherWrap" class="<?php echo e(in_array('Other', $intakeLeadSources, true) ? '' : 'hidden'); ?> mt-3"><label for="intake_lead_source_other" class="mb-1 block text-sm font-medium text-gray-700">Other Lead Source</label><input id="intake_lead_source_other" name="lead_source_other" value="<?php echo e(old('lead_source_other', $contact->lead_source_other)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Referral Information</h3>
                <p class="mb-4 text-xs text-gray-500">Who referred this client or lead source details.</p>
                <div>
                    <label for="intake_referred_by" class="mb-1 block text-sm font-medium text-gray-700">Referred By</label>
                    <input id="intake_referred_by" name="referred_by" value="<?php echo e(old('referred_by', $contact->referred_by)); ?>" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900">Lead Stage</h3>
                <p class="mb-4 text-xs text-gray-500">Current stage of the lead in the pipeline.</p>
                <div>
                    <label for="intake_lead_stage" class="mb-1 block text-sm font-medium text-gray-700">Lead Stage</label>
                    <select id="intake_lead_stage" name="lead_stage" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <?php $__currentLoopData = $intakeLeadStageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option); ?>" <?php if(old('lead_stage', $contact->lead_stage ?: 'Inquiry') === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </section>

            <?php if($errors->any() && old('_from_contact_intake_edit')): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
        </div>

        <div class="mt-auto flex items-center justify-between gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
            <button id="contactIntakeEditBtn" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Edit</button>
            <div class="flex items-center gap-3">
                <button id="cancelContactIntakeModal" type="button" data-close-contact-intake-modal class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button>
                <button id="contactIntakeCancelBtn" type="button" class="hidden h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button id="contactIntakeSaveBtn" type="submit" class="hidden h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save</button>
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
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/contacts/partials/kyc-intake-modal.blade.php ENDPATH**/ ?>