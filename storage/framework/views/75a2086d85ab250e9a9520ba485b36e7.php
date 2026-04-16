<?php $__env->startSection('content'); ?>
<?php
    $form = $specimenForm ?? [];
    $signatories = array_pad($form['signatories'] ?? [], 6, '');
    $clientType = old('client_type', $form['client_type'] ?? 'new');
    $isBusinessContact = ($contact->customer_type ?? null) === 'business';
    $contactName = trim(($contact->first_name ?? '').' '.($contact->last_name ?? ''));
    $contactDisplayName = $contact->name ?? ($contactName !== '' ? $contactName : ('Contact #'.$contact->id));
    $lineInput = 'width:100%; height:18px; border:0; border-bottom:1px solid #000; padding:0 2px; box-sizing:border-box; font-family:\'Times New Roman\', serif; font-size:12px; background:transparent;';
    $boxInput = 'width:100%; height:18px; border:0; padding:0 2px; box-sizing:border-box; font-family:\'Times New Roman\', serif; font-size:12px; background:transparent;';
    $boxTextarea = 'width:100%; height:78px; border:0; padding:0 2px; box-sizing:border-box; font-family:\'Times New Roman\', serif; font-size:12px; line-height:1.15; resize:none; overflow:hidden; background:transparent;';
?>

<div class="mx-auto max-w-6xl">
    <div class="mb-4 text-sm text-gray-500">
        <a href="<?php echo e(route('contacts.show', $contact->id)); ?>" class="hover:underline"><span aria-hidden="true">&larr;</span> Contacts</a>
        / <?php echo e($contactDisplayName); ?>

    </div>

    <?php if(session('success')): ?>
        <div class="mb-3 border border-green-300 bg-green-50 px-3 py-2 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="mb-3 border border-red-300 bg-red-50 px-3 py-2 text-sm"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">Specimen Signature Form</h2>

                    <div class="flex gap-2">
                        <button type="button" id="editBtn" onclick="enterEditMode()"
                            class="<?php echo e($isEditMode ? 'hidden ' : ''); ?>rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Edit
                        </button>

                        <button type="submit" form="specimenForm" id="saveBtn"
                            class="<?php echo e($isEditMode ? '' : 'hidden '); ?>rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                            Save
                        </button>

                        <button type="button" id="cancelBtn" onclick="cancelEdit()"
                            class="<?php echo e($isEditMode ? '' : 'hidden '); ?>rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Cancel
                        </button>
                    </div>
                </div>

                <div id="previewMode" class="hidden"></div>

                <div id="editMode">
                    <div style="max-width:850px; margin:auto; border:1px solid #000; padding:0; background:#fff; font-family:'Times New Roman', serif; font-size:12px;">
                        <form id="specimenForm" method="POST" action="<?php echo e(route('contacts.specimen-signature.save', ['id' => $contact->id])); ?>" style="margin:0;">
                            <?php echo csrf_field(); ?>

            <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed; border-color:#000;">
                <tr>
                    <td width="20%" style="border-right:0; border-bottom:0; padding:4px 4px 2px 4px;">
                        <img src="/images/jk-logo.png" style="height:54px;" alt="JK Logo">
                    </td>
                    <td width="46%" style="border-left:0; border-right:0; border-bottom:0;">&nbsp;</td>
                    <td width="34%" style="border-left:0; border-bottom:0; text-align:right; line-height:1.2; font-weight:bold;">
                        <?php if($isBusinessContact): ?>
                            AUTHORIZED SIGNATORY<br>
                            SPECIMEN SIGNATURE CARD<br>
                            <span style="font-style:italic; font-size:11px;">CORPORATION / PARTNERSHIP / OTHER JURIDICAL ENTITY</span><br>
                            <span style="font-size:10px;">CASA-F-005-V1.0-03.16.26</span>
                        <?php else: ?>
                            AUTHORIZED SIGNATORY/SIGNATORY<br>
                            (Sole / OPC / INDIVIDUAL)<br>
                            SPECIMEN SIGNATURE CARD
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td style="border-top:0; border-right:0; padding-top:14px;">
                        <div style="font-weight:bold;">BIF NO.</div>
                        <?php if($isBusinessContact): ?>
                            <input type="text" name="bif_no" value="<?php echo e(old('bif_no', $form['bif_no'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" disabled class="preview-field">
                        <?php else: ?>
                            <input type="text" name="bif_no" value="<?php echo e(old('bif_no', $form['bif_no'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                        <?php endif; ?>
                    </td>
                    <td style="border-top:0; border-left:0; border-right:0;">&nbsp;</td>
                    <td style="border-top:0; border-left:0; padding-top:14px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; table-layout:fixed;">
                            <tr>
                                <td width="24%" style="border:0; font-weight:bold;">DATE:</td>
                                <td width="76%" style="border:0;"><input type="date" name="date" value="<?php echo e(old('date', $form['date'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <?php if(! $isBusinessContact): ?>
                    <input type="hidden" name="client_type" value="<?php echo e($clientType); ?>">
                <?php endif; ?>

                <?php if($isBusinessContact): ?>
                <tr>
                    <td colspan="3" style="padding:4px 8px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; table-layout:fixed;">
                            <tr>
                                <?php $__currentLoopData = ['new' => 'New Client', 'existing' => 'Existing Client', 'change' => 'Change Information']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td style="border:0; text-align:center;">
                                        <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px;">
                                            <input type="radio" name="client_type" value="<?php echo e($value); ?>" <?php if(old('client_type', $form['client_type'] ?? 'new') === $value): echo 'checked'; endif; ?> <?php if(! $isEditMode): echo 'disabled'; endif; ?>>
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td colspan="3" style="padding:0;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed;">
                            <tr>
                                <td width="50%" style="border:0; border-right:1px solid #000; padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                                        <tr><td style="border:0;">BUSINESS NAME</td></tr>
                                        <tr><td style="border:0;"><input type="text" name="business_name_left" value="<?php echo e(old('business_name_left', $form['business_name_left'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr><td style="border:0;">BUSINESS ACCOUNT NUMBER</td></tr>
                                        <tr><td style="border:0;"><input type="text" name="account_number_left" value="<?php echo e(old('account_number_left', $form['account_number_left'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr>
                                            <td style="border:0; padding:0;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                                                    <tr>
                                                        <td width="56%" style="border:0;">SIGNATURE COMBINATION</td>
                                                        <td width="44%" style="border:0;">SIGNATURE CLASS</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0;"><input type="text" name="signature_combination" value="<?php echo e(old('signature_combination', $form['signature_combination'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                                        <td style="border:0;"><input type="text" value="<?php echo e(old('signature_class', $form['signature_class'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" readonly <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="50%" style="border:0; padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                                        <tr><td style="border:0;">BUSINESS NAME</td></tr>
                                        <tr><td style="border:0;"><input type="text" name="business_name_right" value="<?php echo e(old('business_name_right', $form['business_name_right'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr><td style="border:0;">BUSINESS ACCOUNT NUMBER</td></tr>
                                        <tr><td style="border:0;"><input type="text" name="account_number_right" value="<?php echo e(old('account_number_right', $form['account_number_right'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr>
                                            <td style="border:0; padding:0;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                                                    <tr>
                                                        <td width="56%" style="border:0;">SIGNATURE COMBINATION</td>
                                                        <td width="44%" style="border:0;">SIGNATURE CLASS</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0;"><input type="text" value="<?php echo e(old('signature_combination', $form['signature_combination'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" readonly <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                                        <td style="border:0;"><input type="text" name="signature_class" value="<?php echo e(old('signature_class', $form['signature_class'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="text-align:center; font-weight:bold; font-size:14px; padding-top:2px; padding-bottom:2px;">AUTHORIZE SIGNATORIES</td>
                </tr>

                <tr>
                    <td colspan="3" style="padding:0;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; table-layout:fixed;">
                            <tr>
                                <td width="50%" style="border:0; border-right:1px solid #000; vertical-align:top; padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse:collapse; table-layout:fixed;">
                                        <tr><td colspan="2" style="border:0;">CLIENT NAME:</td></tr>
                                        <tr><td colspan="2" style="border:0;"><input type="text" name="left_client_name" value="<?php echo e(old('left_client_name', $form['left_client_name'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr>
                                            <td width="45%" style="border:0;">CIF NO.:</td>
                                            <td width="55%" style="border:0;">CIF Dated:</td>
                                        </tr>
                                        <tr>
                                            <td style="border:0;"><input type="text" name="left_cif_no" value="<?php echo e(old('left_cif_no', $form['left_cif_no'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                            <td style="border:0;"><input type="date" name="left_cif_dated" value="<?php echo e(old('left_cif_dated', $form['left_cif_dated'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                        </tr>
                                        <?php for($i = 0; $i < 3; $i++): ?>
                                            <tr><td colspan="2" style="border:0; padding-top:18px;"><?php echo e($i + 1); ?></td></tr>
                                            <tr><td colspan="2" style="border:0;"><input type="text" name="signatory_names[]" value="<?php echo e(old('signatory_names.'.$i, $signatories[$i] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <?php endfor; ?>
                                    </table>
                                </td>
                                <td width="50%" style="border:0; vertical-align:top; padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse:collapse; table-layout:fixed;">
                                        <tr><td colspan="2" style="border:0;">CLIENT NAME:</td></tr>
                                        <tr><td colspan="2" style="border:0;"><input type="text" name="right_client_name" value="<?php echo e(old('right_client_name', $form['right_client_name'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <tr>
                                            <td width="45%" style="border:0;">CIF NO.:</td>
                                            <td width="55%" style="border:0;">CIF Dated:</td>
                                        </tr>
                                        <tr>
                                            <td style="border:0;"><input type="text" name="right_cif_no" value="<?php echo e(old('right_cif_no', $form['right_cif_no'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                            <td style="border:0;"><input type="date" name="right_cif_dated" value="<?php echo e(old('right_cif_dated', $form['right_cif_dated'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td>
                                        </tr>
                                        <?php for($i = 3; $i < 6; $i++): ?>
                                            <tr><td colspan="2" style="border:0; padding-top:18px;"><?php echo e($i - 2); ?></td></tr>
                                            <tr><td colspan="2" style="border:0;"><input type="text" name="signatory_names[]" value="<?php echo e(old('signatory_names.'.$i, $signatories[$i] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"></td></tr>
                                        <?php endfor; ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="padding:0;">
                        <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed; border:1px solid #000;">
                            <tr>
                                <td width="60%" style="vertical-align:top; font-size:11px; line-height:1.18; text-align:justify; border-right:1px solid #000;">
                                    By my/our signature(s) herein, I/we certify that the information and specimen signatures provided are true, correct, and duly authorized for use by JK&amp;C Inc. The above-listed individual(s) are the authorized signatory/ies of the business entity or the individual client, and JK&amp;C Inc. may rely on these specimen signatures for verification, documentation, and official transactions. I/we undertake to notify JK&amp;C Inc. in writing of any change to the authorized signatory/ies or their authority. In the absence of a Board Resolution, Secretary's Certificate, or Special Power of Attorney (SPA), the signature(s) appearing herein shall be presumed to be the true and rightful authorized signatory/ies of the business entity or individual client, unless otherwise notified in writing.
                                </td>
                                <td width="40%" style="vertical-align:top; padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed;">
                                        <tr>
                                            <td style="border:0; text-align:center; font-weight:bold; line-height:1.2; padding-top:4px;">
                                                AUTHENTICATED BY CORPORATE SECRETARY / AUTHORIZED REPRESENTATIVE
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border:0; padding-top:10px;">
                                                Board Resolution / Secretary's Certificate / Special Power of Attorney (SPA) No.
                                                <input type="text" name="board_resolution_spa_no" value="<?php echo e(old('board_resolution_spa_no', $form['board_resolution_spa_no'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border:0; padding-top:8px;">
                                                Board Resolution / Secretary's Certificate / Special Power of Attorney (SPA) Date
                                                <input type="date" name="board_resolution_spa_date" value="<?php echo e(old('board_resolution_spa_date', $form['board_resolution_spa_date'] ?? '')); ?>" style="<?php echo e($lineInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border:0; text-align:center; padding-top:8px;">
                                                <div style="margin:0 auto 2px auto; width:72%; border-bottom:1px solid #000; min-height:16px; display:flex; align-items:flex-end; justify-content:center;">
                                                    <input type="text" name="signature_over_printed_name" value="<?php echo e(old('signature_over_printed_name', $form['signature_over_printed_name'] ?? '')); ?>" style="width:100%; border:0; padding:0 2px 1px; box-sizing:border-box; font-family:'Times New Roman', serif; font-size:12px; background:transparent; text-align:center;" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                </div>
                                                Signature over Printed Name
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border:0; text-align:center; padding-top:2px;">
                                                <div style="margin:0 auto 2px auto; width:72%; border-bottom:1px solid #000; min-height:16px; display:flex; align-items:flex-end; justify-content:center;">
                                                    <input type="date" name="authorized_signatory_date" value="<?php echo e(old('authorized_signatory_date', $form['authorized_signatory_date'] ?? '')); ?>" style="width:100%; border:0; padding:0 2px 1px; box-sizing:border-box; font-family:'Times New Roman', serif; font-size:12px; background:transparent; text-align:center;" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                </div>
                                                Date
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed;">
                                        <tr>
                                            <td width="68%" style="border:0; text-align:center; vertical-align:bottom; padding-top:10px;">
                                                <div style="margin:0 auto 2px auto; width:70%; border-bottom:1px solid #000; min-height:16px; display:flex; align-items:flex-end; justify-content:center;">
                                                    <input type="text" name="authorized_signatory_signature" value="<?php echo e(old('authorized_signatory_signature', $form['authorized_signatory_signature'] ?? '')); ?>" style="width:100%; border:0; padding:0 2px 1px; box-sizing:border-box; font-family:'Times New Roman', serif; font-size:12px; background:transparent; text-align:center;" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                </div>
                                                Authorized Signatory's Signature over Printed Name
                                            </td>
                                            <td width="32%" style="border:0; text-align:center; vertical-align:bottom; padding-top:10px;">
                                                <div style="margin:0 auto 2px auto; width:78%; border-bottom:1px solid #000; min-height:16px; display:flex; align-items:flex-end; justify-content:center;">
                                                    <input type="date" name="authorized_signatory_date" value="<?php echo e(old('authorized_signatory_date', $form['authorized_signatory_date'] ?? '')); ?>" style="width:100%; border:0; padding:0 2px 1px; box-sizing:border-box; font-family:'Times New Roman', serif; font-size:12px; background:transparent; text-align:center;" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                </div>
                                                Date
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="padding:0; border-left:1px solid #000; border-top:1px solid #000;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; font-weight:bold; padding-top:3px; padding-bottom:3px; border-top:1px solid #000; border-bottom:1px solid #000;">FOR JKNC USE ONLY</td>
                            </tr>
                            <tr>
                                <td width="36%" style="vertical-align:top; border-right:1px solid #000;">
                                    <div style="font-weight:bold;">PROCESSING INSTRUCTION (FOR JK&amp;C USE ONLY)</div>
                                    <textarea name="processing_instruction" rows="6" style="<?php echo e($boxTextarea); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"><?php echo e(old('processing_instruction', $form['processing_instruction'] ?? '')); ?></textarea>
                                </td>
                                <td width="64%" style="vertical-align:top;">
                                    <div style="font-weight:bold;">REMARKS:</div>
                                    <textarea name="remarks" rows="6" style="<?php echo e($boxTextarea); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field"><?php echo e(old('remarks', $form['remarks'] ?? '')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding:0; border-top:1px solid #000; border-bottom:1px solid #000;">
                                    <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed; border-left:0; border-right:0; border-bottom:1px solid #000;">
                                        <tr>
                                            <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">SALES &amp; MARKETING</td>
                                            <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">PROCESSED BY / DATE</td>
                                            <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">FINANCE</td>
                                            <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:0;">SCANNED BY / DATE</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                                <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                                <input type="text" name="sales_marketing" value="<?php echo e(old('sales_marketing', $form['sales_marketing'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                Signature over Printed Name
                                            </td>
                                            <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                                <div style="margin:0 auto 4px auto; width:72%; border-bottom:1px solid #000;"></div>
                                                <input type="text" name="processed_by" value="<?php echo e(old('processed_by', $form['processed_by'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                Signature over Printed Name
                                            </td>
                                            <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                                <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                                <input type="text" name="finance" value="<?php echo e(old('finance', $form['finance'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                Signature over Printed Name
                                            </td>
                                            <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:0;">
                                                <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                                <input type="text" name="scanned_by" value="<?php echo e(old('scanned_by', $form['scanned_by'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                Signature over Printed Name
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align:center; font-style:italic; vertical-align:bottom; padding:18px 8px 6px 8px; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">
                                                Record Custodian ( Name and Signature)
                                            </td>
                                            <td style="padding:0; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; table-layout:fixed;">
                                                    <tr>
                                                        <td style="border:0; border-bottom:1px solid #000; padding:6px 6px 4px 6px;">Date Recorded:</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0; padding:6px 6px 4px 6px;">Date Signed :</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="padding:0; border-top:0; border-bottom:0; border-left:0; border-right:0;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; table-layout:fixed;">
                                                    <tr>
                                                        <td style="border:0; border-bottom:1px solid #000; padding:2px 6px;">
                                                            <input type="date" name="processed_date" value="<?php echo e(old('processed_date', $form['processed_date'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0; padding:2px 6px;">
                                                            <input type="date" name="scanned_date" value="<?php echo e(old('scanned_date', $form['scanned_date'] ?? '')); ?>" style="<?php echo e($boxInput); ?>" <?php if(! $isEditMode): echo 'disabled'; endif; ?> class="preview-field">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-1">
            <div class="rounded-lg border bg-white p-4">
                <h3 class="mb-2 text-sm font-semibold">PDF Tools</h3>
                <p class="mb-3 text-xs text-gray-500">
                    Preview the document or export a print-friendly PDF.
                </p>

                <button type="button"
                    data-specimen-pdf-preview
                    data-preview-url="<?php echo e(route('contacts.specimen-signature.download', ['id' => $contact->id])); ?>"
                    class="mb-2 w-full rounded border px-4 py-2 text-sm hover:bg-gray-50">
                    Preview PDF
                </button>

                <button type="button"
                    data-specimen-pdf-download
                    data-download-url="<?php echo e(route('contacts.specimen-signature.download', ['id' => $contact->id])); ?>?autoprint=1"
                    class="w-full rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        function enterEditMode() {
            const editButton = document.getElementById('editBtn');
            const saveButton = document.getElementById('saveBtn');
            const cancelButton = document.getElementById('cancelBtn');
            const form = document.getElementById('specimenForm');

            if (!editButton || !saveButton || !cancelButton || !form) {
                return;
            }

            form.querySelectorAll('input, textarea, select').forEach((el) => {
                el.removeAttribute('disabled');
            });

            editButton.classList.add('hidden');
            saveButton.classList.remove('hidden');
            cancelButton.classList.remove('hidden');
        }

        function cancelEdit() {
            const editButton = document.getElementById('editBtn');
            const saveButton = document.getElementById('saveBtn');
            const cancelButton = document.getElementById('cancelBtn');
            const form = document.getElementById('specimenForm');

            if (!editButton || !saveButton || !cancelButton || !form) {
                return;
            }

            form.querySelectorAll('input, textarea, select').forEach((el) => {
                if (el.type !== 'hidden') {
                    el.setAttribute('disabled', true);
                }
            });

            form.reset();
            editButton.classList.remove('hidden');
            saveButton.classList.add('hidden');
            cancelButton.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const specimenPreviewButton = document.querySelector('[data-specimen-pdf-preview]');
            const specimenDownloadButton = document.querySelector('[data-specimen-pdf-download]');

            const openPrintFrame = (url) => {
                if (!url) {
                    return;
                }

                const frame = document.createElement('iframe');
                frame.style.position = 'fixed';
                frame.style.width = '0';
                frame.style.height = '0';
                frame.style.border = '0';
                frame.style.opacity = '0';
                frame.setAttribute('aria-hidden', 'true');
                frame.src = url;
                document.body.appendChild(frame);

                const cleanup = () => {
                    window.setTimeout(() => frame.remove(), 1500);
                };

                frame.addEventListener('load', cleanup, { once: true });
            };

            specimenPreviewButton?.addEventListener('click', () => {
                window.open(specimenPreviewButton.dataset.previewUrl, '_blank');
            });

            specimenDownloadButton?.addEventListener('click', () => {
                openPrintFrame(specimenDownloadButton.dataset.downloadUrl);
            });
        });
    </script>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/contacts/specimen-signature.blade.php ENDPATH**/ ?>