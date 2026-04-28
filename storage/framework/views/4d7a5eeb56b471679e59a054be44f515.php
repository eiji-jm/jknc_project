<?php
    $readonly = $readonly ?? false;
    $clientMode = $clientMode ?? false;
    $form = $form ?? [];
    $contact = $contact ?? null;
    $isBusinessContact = ($isBusinessContact ?? null) ?? (($contact?->customer_type ?? null) === 'business');
    $signatories = array_pad($form['signatories'] ?? [], 6, null);
    $inputClass = 'h-9 w-full rounded border border-gray-300 px-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100';
    $readonlyClass = 'min-h-9 rounded border border-gray-200 bg-gray-50 px-2 py-2 text-sm text-gray-700';
?>

<div class="overflow-hidden rounded-xl border border-gray-300 bg-white">
    <table class="w-full border-collapse text-xs text-gray-900">
        <tr>
            <td colspan="4" class="border border-gray-300 p-3 text-right align-top">
                <p class="font-semibold uppercase"><?php echo e($isBusinessContact ? 'AUTHORIZED SIGNATORY' : 'AUTHORIZED SIGNATORY/SIGNATORY'); ?></p>
                <p class="font-semibold uppercase"><?php echo e($isBusinessContact ? 'SPECIMEN SIGNATURE CARD' : '(Sole / OPC / INDIVIDUAL)'); ?></p>
                <?php if($isBusinessContact): ?>
                    <p class="font-semibold uppercase italic">CORPORATION / PARTNERSHIP / OTHER JURIDICAL ENTITY</p>
                    <p class="text-[10px] uppercase">CASA-F-005-V1.0-03.16.26</p>
                <?php endif; ?>
                <?php if(! $isBusinessContact): ?>
                <p class="font-bold uppercase">SPECIMEN SIGNATURE CARD</p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BIF NO.</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly || $clientMode): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['bif_no'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="bif_no" value="<?php echo e(old('bif_no', $form['bif_no'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2 font-semibold">DATE</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['date'] ?: '-'); ?></div>
                <?php else: ?>
                    <input type="date" name="date" value="<?php echo e(old('date', $form['date'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <?php if($isBusinessContact): ?>
        <tr>
            <td colspan="4" class="border border-gray-300 p-2">
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <?php $__currentLoopData = ['new' => 'New Client', 'existing' => 'Existing Client', 'change' => 'Change Information']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2">
                            <?php if($readonly): ?>
                                <input type="checkbox" disabled <?php if(($form['client_type'] ?? '') === $value): echo 'checked'; endif; ?> class="h-4 w-4 rounded border-gray-300 text-blue-600">
                            <?php else: ?>
                                <input type="radio" name="client_type" value="<?php echo e($value); ?>" <?php if(old('client_type', $form['client_type'] ?? 'new') === $value): echo 'checked'; endif; ?> class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <?php endif; ?>
                            <span><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS NAME</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['business_name_left'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="business_name_left" value="<?php echo e(old('business_name_left', $form['business_name_left'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS NAME</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['business_name_right'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="business_name_right" value="<?php echo e(old('business_name_right', $form['business_name_right'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS ACCOUNT NUMBER</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['account_number_left'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="account_number_left" value="<?php echo e(old('account_number_left', $form['account_number_left'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS ACCOUNT NUMBER</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['account_number_right'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="account_number_right" value="<?php echo e(old('account_number_right', $form['account_number_right'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">SIGNATURE COMBINATION</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['signature_combination'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="signature_combination" value="<?php echo e(old('signature_combination', $form['signature_combination'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2 font-semibold">SIGNATURE CLASS</td>
            <td class="border border-gray-300 p-2">
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['signature_class'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="signature_class" value="<?php echo e(old('signature_class', $form['signature_class'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="border border-gray-300 p-2 text-center font-bold uppercase">AUTHORIZED SIGNATORIES</td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <div class="space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1"><span class="font-semibold">CLIENT NAME:</span></div>
                        <div class="col-span-2">
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['left_client_name'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="left_client_name" value="<?php echo e(old('left_client_name', $form['left_client_name'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-semibold">CIF NO.:</span>
                            <?php if($readonly || $clientMode): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['left_cif_no'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="left_cif_no" value="<?php echo e(old('left_cif_no', $form['left_cif_no'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="font-semibold">CIF Dated:</span>
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['left_cif_dated'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input type="date" name="left_cif_dated" value="<?php echo e(old('left_cif_dated', $form['left_cif_dated'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $__currentLoopData = [0, 1, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded border border-gray-300 p-2">
                            <p class="mb-1 font-semibold"><?php echo e($index + 1); ?></p>
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e(data_get($signatories[$index] ?? [], 'name') ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="signatory_names[]" value="<?php echo e(old('signatory_names.'.$index, data_get($signatories[$index] ?? [], 'name'))); ?>" placeholder="Name" class="<?php echo e($inputClass); ?>">
                                <p class="mt-1 text-[11px] text-gray-500">Signature (draw/upload optional)</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <div class="space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1"><span class="font-semibold">CLIENT NAME:</span></div>
                        <div class="col-span-2">
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['right_client_name'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="right_client_name" value="<?php echo e(old('right_client_name', $form['right_client_name'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-semibold">CIF NO.:</span>
                            <?php if($readonly || $clientMode): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['right_cif_no'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="right_cif_no" value="<?php echo e(old('right_cif_no', $form['right_cif_no'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="font-semibold">CIF Dated:</span>
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['right_cif_dated'] ?: '-'); ?></div>
                            <?php else: ?>
                                <input type="date" name="right_cif_dated" value="<?php echo e(old('right_cif_dated', $form['right_cif_dated'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $__currentLoopData = [3, 4, 5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded border border-gray-300 p-2">
                            <p class="mb-1 font-semibold"><?php echo e($index - 2); ?></p>
                            <?php if($readonly): ?>
                                <div class="<?php echo e($readonlyClass); ?>"><?php echo e(data_get($signatories[$index] ?? [], 'name') ?: '-'); ?></div>
                            <?php else: ?>
                                <input name="signatory_names[]" value="<?php echo e(old('signatory_names.'.$index, data_get($signatories[$index] ?? [], 'name'))); ?>" placeholder="Name" class="<?php echo e($inputClass); ?>">
                                <p class="mt-1 text-[11px] text-gray-500">Signature (draw/upload optional)</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top text-[11px] leading-relaxed">
                By my/our signature(s) herein, I/we certify that the information and specimen signatures provided are true, correct, and duly authorized for use by JK&C Inc. The above-listed individual(s) are the authorized signatory/ies of the business entity or the individual client, and JK&C Inc. may rely on these specimen signatures for verification, documentation, and official transactions. I/we undertake to notify JK&C Inc. in writing of any change to the authorized signatory/ies or their authority. In the absence of a Board Resolution, Secretary’s Certificate, or Special Power of Attorney (SPA), the signature(s) appearing herein shall be presumed to be the true and rightful authorized signatory/ies of the business entity or individual client, unless otherwise notified in writing.
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="text-center font-bold uppercase">AUTHENTICATED BY CORPORATE SECRETARY / AUTHORIZED REPRESENTATIVE</p>
                <div class="mt-2 space-y-2">
                    <div>
                        <span class="font-semibold">Board Resolution / SPA No.</span>
                        <?php if($readonly): ?>
                            <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['board_resolution_spa_no'] ?: '-'); ?></div>
                        <?php else: ?>
                            <input name="board_resolution_spa_no" value="<?php echo e(old('board_resolution_spa_no', $form['board_resolution_spa_no'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="font-semibold">Board Resolution / SPA Date</span>
                        <?php if($readonly): ?>
                            <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['board_resolution_spa_date'] ?: '-'); ?></div>
                        <?php else: ?>
                            <input type="date" name="board_resolution_spa_date" value="<?php echo e(old('board_resolution_spa_date', $form['board_resolution_spa_date'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="font-semibold">Authenticated by Corporate Secretary / Authorized Representative</span>
                        <?php if($readonly): ?>
                            <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['authenticated_by'] ?: '-'); ?></div>
                        <?php else: ?>
                            <input name="authenticated_by" value="<?php echo e(old('authenticated_by', $form['authenticated_by'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Authorized Signatory's Signature over Printed Name</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['signature_over_printed_name'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="signature_over_printed_name" value="<?php echo e(old('signature_over_printed_name', $form['signature_over_printed_name'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Authorized Signatory Signature</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['authorized_signatory_signature'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="authorized_signatory_signature" value="<?php echo e(old('authorized_signatory_signature', $form['authorized_signatory_signature'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Date</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['authorized_signatory_date'] ?: '-'); ?></div>
                <?php else: ?>
                    <input type="date" name="authorized_signatory_date" value="<?php echo e(old('authorized_signatory_date', $form['authorized_signatory_date'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <?php if(! $clientMode): ?>
        <tr>
            <td colspan="4" class="border border-gray-300 p-2 text-center font-bold uppercase">FOR JKNC USE ONLY</td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="font-semibold uppercase">PROCESSING INSTRUCTION (FOR JK&C USE ONLY)</p>
                <?php if($readonly): ?>
                    <p class="mt-2 text-[11px] leading-relaxed text-gray-700"><?php echo e($form['processing_instruction'] ?: '-'); ?></p>
                <?php else: ?>
                    <textarea name="processing_instruction" rows="4" class="mt-2 w-full rounded border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('processing_instruction', $form['processing_instruction'] ?? '')); ?></textarea>
                <?php endif; ?>
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="font-semibold uppercase">REMARKS:</p>
                <?php if($readonly): ?>
                    <div class="mt-2 min-h-24 rounded border border-gray-200 bg-gray-50 px-2 py-2 text-sm text-gray-700"><?php echo e($form['remarks'] ?: '-'); ?></div>
                <?php else: ?>
                    <textarea name="remarks" rows="4" class="mt-2 w-full rounded border border-gray-300 px-2 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('remarks', $form['remarks'] ?? '')); ?></textarea>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">SALES & MARKETING</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['sales_marketing'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="sales_marketing" value="<?php echo e(old('sales_marketing', $form['sales_marketing'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
                <p class="mt-3 border-t border-gray-400 pt-1 text-[11px]">Signature over Printed Name</p>
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">PROCESSED BY / DATE</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e(($form['processed_by'] ?: '-') . ' / ' . ($form['processed_date'] ?: '-')); ?></div>
                <?php else: ?>
                    <input name="processed_by" value="<?php echo e(old('processed_by', $form['processed_by'] ?? '')); ?>" placeholder="Processed By" class="<?php echo e($inputClass); ?>">
                    <input type="date" name="processed_date" value="<?php echo e(old('processed_date', $form['processed_date'] ?? '')); ?>" class="mt-2 <?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">FINANCE</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e($form['finance'] ?: '-'); ?></div>
                <?php else: ?>
                    <input name="finance" value="<?php echo e(old('finance', $form['finance'] ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                <?php endif; ?>
                <p class="mt-3 border-t border-gray-400 pt-1 text-[11px]">Signature over Printed Name</p>
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">SCANNED BY / DATE</p>
                <?php if($readonly): ?>
                    <div class="<?php echo e($readonlyClass); ?>"><?php echo e(($form['scanned_by'] ?: '-') . ' / ' . ($form['scanned_date'] ?: '-')); ?></div>
                <?php else: ?>
                    <input name="scanned_by" value="<?php echo e(old('scanned_by', $form['scanned_by'] ?? '')); ?>" placeholder="Scanned By" class="<?php echo e($inputClass); ?>">
                    <input type="date" name="scanned_date" value="<?php echo e(old('scanned_date', $form['scanned_date'] ?? '')); ?>" class="mt-2 <?php echo e($inputClass); ?>">
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\contacts\partials\specimen-signature-card.blade.php ENDPATH**/ ?>