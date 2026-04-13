<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        <div class="flex items-center gap-3 px-4 py-4">
            <div class="flex-1"></div>

            <?php if(!empty($gis->id)): ?>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('gis.show', $gis->id)); ?>"
                       class="h-9 px-4 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium flex items-center gap-2">
                        Back to GIS Show
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="border-t border-gray-100"></div>

        <div class="p-4">
            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">

                <div class="h-[760px] overflow-auto bg-gray-50 p-6">
                    <div class="mx-auto max-w-[980px] bg-white border border-gray-300">

                        <div class="px-6 pt-6 text-center">
                            <div class="text-[13px] font-bold tracking-wide text-gray-900">GENERAL INFORMATION</div>
                            <div class="text-[11px] font-semibold text-gray-900 mt-1">
                                FOR THE YEAR <span class="px-2 border-b border-gray-400"><?php echo e(now()->year); ?></span>
                            </div>
                            <div class="text-[11px] font-semibold text-gray-900 mt-1">STOCK CORPORATION</div>
                        </div>

                        

                        <div class="px-6 pb-6">
                            <style>
                                .gis-cell { border: 1px solid #6b7280; }
                                .gis-label { font-size: 10px; font-weight: 700; letter-spacing: .02em; }
                                .gis-input {
                                    width: 100%;
                                    border: none;
                                    outline: none;
                                    padding: 6px 8px;
                                    font-size: 11px;
                                    line-height: 1.1rem;
                                    background: transparent;
                                }
                            </style>

                            <div class="grid grid-cols-12 gap-0">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">CORPORATE NAME:</div>
                                    <input class="gis-input" value="<?php echo e($gis->corporation_name ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">DATE REGISTERED:</div>
                                    <input class="gis-input" value="<?php echo e(!empty($gis->date_registered) ? \Carbon\Carbon::parse($gis->date_registered)->format('F d, Y') : ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">BUSINESS/TRADE NAME:</div>
                                    <input class="gis-input" value="<?php echo e($gis->trade_name ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">FISCAL YEAR END:</div>
                                    <input class="gis-input" value="<?php echo e($gis->fiscal_year_end ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">SEC REGISTRATION NUMBER:</div>
                                    <input class="gis-input" value="<?php echo e($gis->company_reg_no ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">CORPORATE TAX IDENTIFICATION NUMBER (TIN):</div>
                                    <input class="gis-input" value="<?php echo e($gis->tin ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">DATE OF ANNUAL MEETING PER BY-LAWS:</div>
                                    <input class="gis-input" value="<?php echo e($gis->meeting_type ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">WEBSITE/URL ADDRESS:</div>
                                    <input class="gis-input" value="<?php echo e($gis->website ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ACTUAL DATE OF ANNUAL MEETING:</div>
                                    <input class="gis-input" value="<?php echo e(!empty($gis->annual_meeting) ? \Carbon\Carbon::parse($gis->annual_meeting)->format('F d, Y') : ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">E-MAIL ADDRESS:</div>
                                    <input class="gis-input" value="<?php echo e($gis->email ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-8 gis-cell">
                                    <div class="px-2 pt-1 gis-label">COMPLETE PRINCIPAL OFFICE ADDRESS:</div>
                                    <input class="gis-input" value="<?php echo e($gis->principal_address ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="px-2 pt-1 gis-label">FAX NUMBER:</div>
                                    <input class="gis-input" value="N/A" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-12 gis-cell">
                                    <div class="px-2 pt-1 gis-label">COMPLETE BUSINESS ADDRESS:</div>
                                    <input class="gis-input" value="<?php echo e($gis->business_address ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">OFFICIAL E-MAIL ADDRESS</div>
                                    <input class="gis-input" value="<?php echo e($gis->email ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ALTERNATE E-MAIL ADDRESS</div>
                                    <input class="gis-input" value="N/A" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">OFFICIAL MOBILE NUMBER</div>
                                    <input class="gis-input" value="<?php echo e($gis->official_mobile ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">ALTERNATE MOBILE NUMBER</div>
                                    <input class="gis-input" value="<?php echo e($gis->alternate_mobile ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-6 gis-cell">
                                    <div class="px-2 pt-1 gis-label">NAME OF EXTERNAL AUDITOR & ITS SIGNING PARTNER:</div>
                                    <input class="gis-input" value="<?php echo e($gis->auditor ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">SEC ACCREDITATION NUMBER (if applicable):</div>
                                    <input class="gis-input" value="N/A" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">TELEPHONE NUMBER(S):</div>
                                    <input class="gis-input" value="N/A" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-6 gis-cell">
                                    <div class="px-2 pt-1 gis-label">PRIMARY PURPOSE/ACTIVITY/INDUSTRY PRESENTLY ENGAGED IN:</div>
                                    <textarea class="gis-input" rows="4" readonly><?php echo e($gis->industry ?? ''); ?></textarea>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">INDUSTRY CLASSIFICATION:</div>
                                    <input class="gis-input" value="<?php echo e($gis->industry ?? ''); ?>" readonly>
                                </div>
                                <div class="col-span-3 gis-cell">
                                    <div class="px-2 pt-1 gis-label">GEOGRAPHICAL CODE:</div>
                                    <input class="gis-input" value="<?php echo e($gis->geo_code ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="gis-cell -mt-px">
                                <div class="text-center text-[10px] font-bold py-2 tracking-wide">
                                    INTERCOMPANY AFFILIATIONS
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">PARENT COMPANY</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SEC REGISTRATION NO.</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">ADDRESS</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->parent_company_name ?: 'N/A'); ?>" readonly></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->parent_company_sec_no ?: 'N/A'); ?>" readonly></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->parent_company_address ?: 'N/A'); ?>" readonly></div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SUBSIDIARY / AFFILIATE</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">SEC REGISTRATION NO.</div>
                                </div>
                                <div class="col-span-4 gis-cell">
                                    <div class="text-center text-[10px] font-bold py-2">ADDRESS</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-0 -mt-px">
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->subsidiary_name ?: 'N/A'); ?>" readonly></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->subsidiary_sec_no ?: 'N/A'); ?>" readonly></div>
                                <div class="col-span-4 gis-cell"><input class="gis-input text-center" value="<?php echo e($gis->subsidiary_address ?: 'N/A'); ?>" readonly></div>
                            </div>

                            <div class="gis-cell -mt-px">
                                <div class="text-center text-[10px] py-2 text-gray-700 font-semibold">
                                    NOTE: USE ADDITIONAL SHEET IF NECESSARY
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-3 text-xs text-gray-400">
                This page now displays the saved data from the selected GIS record.
            </div>
        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/company-general-information.blade.php ENDPATH**/ ?>