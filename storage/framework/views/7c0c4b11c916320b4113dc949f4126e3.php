<?php $__env->startSection('content'); ?>
<?php
    $certificateNo = $certificate->stock_number ?? '-';
    $documentUrl = $certificate->document_path ? route('uploads.show', ['path' => $certificate->document_path]) : null;
    $certificateStatus = strtolower((string) ($certificate->status ?? 'draft'));
    $isIssuedCertificate = in_array($certificateStatus, ['issued', 'released', 'approved'], true);
    $isVoidedCertificate = $certificateStatus === 'voided';
?>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{
        showVoidModal: false,
        showEditPanel: false,
        cancellationReason: '',
        cancellationTypes: [],
        form: {
            certificate_type: <?php echo \Illuminate\Support\Js::from($certificate->certificate_type ?: 'COS')->toHtml() ?>,
            stock_number: <?php echo \Illuminate\Support\Js::from($certificate->stock_number)->toHtml() ?>,
            stockholder_name: <?php echo \Illuminate\Support\Js::from($certificate->stockholder_name)->toHtml() ?>,
            corporation_name: <?php echo \Illuminate\Support\Js::from($certificate->corporation_name)->toHtml() ?>,
            company_reg_no: <?php echo \Illuminate\Support\Js::from($certificate->company_reg_no)->toHtml() ?>,
            par_value: <?php echo \Illuminate\Support\Js::from((string) ($certificate->par_value ?? ''))->toHtml() ?>,
            number: <?php echo \Illuminate\Support\Js::from((string) ($certificate->number ?? ''))->toHtml() ?>,
            amount: <?php echo \Illuminate\Support\Js::from((string) ($certificate->amount ?? ''))->toHtml() ?>,
            amount_in_words: <?php echo \Illuminate\Support\Js::from($certificate->amount_in_words)->toHtml() ?>,
            date_issued: <?php echo \Illuminate\Support\Js::from(optional($certificate->date_issued)->toDateString())->toHtml() ?>,
            president: <?php echo \Illuminate\Support\Js::from($certificate->president)->toHtml() ?>,
            corporate_secretary: <?php echo \Illuminate\Support\Js::from($certificate->corporate_secretary)->toHtml() ?>,
        },
        formatDate(value) {
            if (!value) return '-';
            const date = new Date(value + 'T00:00:00');
            if (Number.isNaN(date.getTime())) return value;
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        displayValue(value, fallback = '-') {
            return value === null || value === undefined || value === '' ? fallback : value;
        },
        displayAmount(value) {
            return value === null || value === undefined || value === '' ? '-' : value;
        },
        hasCancellationType(type) {
            return this.cancellationTypes.includes(type);
        }
     }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold">Certificate Preview</div>
                <div class="text-xs text-gray-500">Certificate No. <?php echo e($certificateNo); ?></div>
            </div>
            <div class="flex-1"></div>
            <?php if(!$isIssuedCertificate && !$isVoidedCertificate && !empty($editRoute)): ?>
                <button type="button" @click="showEditPanel = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</button>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3">
                <?php if(!empty($liveTemplateUrl)): ?>
                    <iframe src="<?php echo e($liveTemplateUrl); ?>" class="w-full h-[700px] border rounded bg-white"></iframe>
                <?php elseif(!empty($generatedPreviewUrl)): ?>
                    <iframe src="<?php echo e($generatedPreviewUrl); ?>" class="w-full h-[700px] border rounded bg-white"></iframe>
                <?php elseif($documentUrl): ?>
                    <iframe src="<?php echo e($documentUrl); ?>" class="w-full h-[700px] border rounded bg-white"></iframe>
                <?php else: ?>
                    <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">Certificate preview PDF unavailable.</div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900" x-text="displayValue(form.stock_number)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stockholder</span><div class="font-medium text-gray-900" x-text="displayValue(form.stockholder_name)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Par Value</span><div class="font-medium text-gray-900" x-text="displayValue(form.par_value)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Number</span><div class="font-medium text-gray-900" x-text="displayValue(form.number)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Amount</span><div class="font-medium text-gray-900" x-text="displayAmount(form.amount)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Issued</span><div class="font-medium text-gray-900" x-text="formatDate(form.date_issued)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="font-medium text-gray-900"><?php echo e(ucfirst($certificateStatus)); ?></div></div>
                    </div>
                </div>

                <?php if($isIssuedCertificate): ?>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        This certificate has already been issued. The digital copy is locked and can no longer be edited. If changes are needed, cancel the certificate first.
                    </div>
                <?php endif; ?>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                    <div class="space-y-3 text-sm">
                        <div><div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div><div class="mt-1 text-gray-900"><?php echo e(($relatedJournals ?? collect())->count()); ?> linked</div></div>
                        <div><div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div><div class="mt-1 text-gray-900"><?php echo e(($relatedLedgers ?? collect())->count()); ?> linked</div></div>
                        <div><div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div><div class="mt-1 text-gray-900"><?php echo e(($relatedInstallments ?? collect())->count()); ?> linked</div></div>
                    </div>
                </div>

                <?php if(!empty($voucherProfile)): ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Voucher</div>
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 text-sm">
                            <?php $__currentLoopData = $voucherProfile; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="border-b border-gray-200 px-3 py-2 odd:bg-gray-50 even:bg-white md:[&:nth-last-child(-n+2)]:border-b-0">
                                    <div class="text-[10px] uppercase tracking-wide text-gray-500"><?php echo e($row['label']); ?></div>
                                    <div class="mt-1 text-sm font-medium text-gray-900 leading-5 break-words"><?php echo e($row['value']); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="space-y-2 pt-2">
                    <?php if(!$isIssuedCertificate && !$isVoidedCertificate): ?>
                        <form method="POST" action="<?php echo e(route('stock-transfer-book.certificates.issue', $certificate)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-stamp"></i>
                                Issue Certificate
                            </button>
                        </form>
                    <?php endif; ?>
                    <button type="button" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" @click="showVoidModal = true">
                        <i class="fas fa-ban"></i>
                        Cancel Certificate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showEditPanel" class="fixed inset-0 bg-black/40 z-40" @click="showEditPanel = false"></div>
        <div x-show="showEditPanel" class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Edit Certificate</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showEditPanel = false" type="button"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="<?php echo e(route('stock-transfer-book.certificates.update', $certificate)); ?>" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs text-gray-600">Certificate Type</label><select name="certificate_type" x-model="form.certificate_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><option value="COS">COS</option><option value="CV">CV</option></select></div>
                    <div><label class="text-xs text-gray-600">Stock Number</label><input type="text" name="stock_number" x-model="form.stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Stockholder</label><input type="text" name="stockholder_name" x-model="form.stockholder_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Corporation Name</label><input type="text" name="corporation_name" x-model="form.corporation_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">Company Reg. No.</label><input type="text" name="company_reg_no" x-model="form.company_reg_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">PAR Value</label><input type="number" step="0.01" name="par_value" x-model="form.par_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">Number</label><input type="number" name="number" x-model="form.number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">Amount</label><input type="number" step="0.01" name="amount" x-model="form.amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Amount in Words</label><input type="text" name="amount_in_words" x-model="form.amount_in_words" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">Date Issued</label><input type="date" name="date_issued" x-model="form.date_issued" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">President</label><input type="text" name="president" x-model="form.president" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div><label class="text-xs text-gray-600">Corporate Secretary</label><input type="text" name="corporate_secretary" x-model="form.corporate_secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Replace Document (PDF)</label><input type="file" name="document_path" class="mt-1 block w-full text-sm text-gray-600"></div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showEditPanel = false">Close</button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showVoidModal" class="fixed inset-0 bg-black/40 z-40" @click="showVoidModal = false"></div>
        <div x-show="showVoidModal" class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Cancellation Details</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showVoidModal = false" type="button"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="<?php echo e(route('stock-transfer-book.certificates.destroy', $certificate)); ?>" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">This will cancel the certificate on record. Provide the cancellation details and required files below before continuing.</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs text-gray-600">Date of Cancellation</label><input type="date" name="cancellation_date" value="<?php echo e(now()->toDateString()); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required></div>
                    <div><label class="text-xs text-gray-600">Effective Date</label><input type="date" name="cancellation_effective_date" value="<?php echo e(now()->toDateString()); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required></div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Reason for Cancellation</label>
                        <select name="cancellation_reason" x-model="cancellationReason" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                            <option value="">Select reason</option>
                            <?php $__currentLoopData = ['Delinquent', 'Buy-back', 'Redemption', 'Treasury Cancellation', 'Capital Reduction', 'Others']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($reason); ?>"><?php echo e($reason); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Type of Cancellation</label>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-900">
                            <?php $__currentLoopData = ['Delinquent', 'Buy-back', 'Redemption', 'Treasury Cancellation', 'Capital Reduction', 'Others']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                    <input type="checkbox" name="cancellation_types[]" value="<?php echo e($type); ?>" x-model="cancellationTypes" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span><?php echo e($type); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Other Cancellation Details</label><textarea name="cancellation_other_reason" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add details when Others is selected."></textarea></div>
                    <div class="md:col-span-2"><label class="text-xs text-gray-600">Remarks</label><textarea name="remarks" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add supporting notes for the cancelled certificate..."></textarea></div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                    <div class="text-sm font-semibold text-gray-900">Required Upload Files</div>
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Core Documents</div>
                            <div class="mt-2 grid grid-cols-1 gap-3">
                                <div><label class="text-xs text-gray-600">Board Resolution (PDF)</label><input type="file" name="board_resolution" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600" required></div>
                                <div><label class="text-xs text-gray-600">Secretary’s Certificate (PDF)</label><input type="file" name="secretary_certificate_file" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600" required></div>
                                <div>
                                    <label class="text-xs text-gray-600">Stock Certificate (scanned copy)</label>
                                    <input type="file" name="scanned_stock_certificate" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600">
                                    <p class="mt-1 text-[11px] text-gray-500">Required unless the stock certificate is lost and you upload both Affidavit of Loss and Valid ID.</p>
                                </div>
                            </div>
                        </div>
                        <div x-show="cancellationReason === 'Others' || hasCancellationType('Others')" x-cloak>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">For Other Cancellation Cases</div>
                            <div class="mt-2 text-[11px] text-gray-500">Add the explanation in the Other Cancellation Details field above.</div>
                        </div>
                        <div x-show="hasCancellationType('Delinquent')" x-cloak>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">If Stock Certificate is Lost</div>
                            <div class="mt-2 grid grid-cols-1 gap-3">
                                <div><label class="text-xs text-gray-600">Affidavit of Loss</label><input type="file" name="affidavit_of_loss" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">Valid ID of Shareholder</label><input type="file" name="shareholder_valid_id" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600"></div>
                            </div>
                        </div>
                        <div x-show="hasCancellationType('Buy-back') || hasCancellationType('Redemption')" x-cloak>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Supporting Documents</div>
                            <div class="mt-2 grid grid-cols-1 gap-3">
                                <div><label class="text-xs text-gray-600">Subscription Agreement</label><input type="file" name="subscription_agreement" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div x-show="hasCancellationType('Buy-back')" x-cloak><label class="text-xs text-gray-600">Deed of Sale / Buy-back Agreement</label><input type="file" name="deed_or_buyback_agreement" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div x-show="hasCancellationType('Redemption')" x-cloak><label class="text-xs text-gray-600">Redemption Agreement</label><input type="file" name="redemption_agreement" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">Proof of Payment / Ledger</label><input type="file" name="proof_of_payment_ledger" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600"></div>
                            </div>
                        </div>
                        <div x-show="hasCancellationType('Delinquent')" x-cloak>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">For Delinquent Shares</div>
                            <div class="mt-2 grid grid-cols-1 gap-3">
                                <div><label class="text-xs text-gray-600">Notice of Delinquency</label><input type="file" name="notice_of_delinquency" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">Proof of Notice / Publication</label><input type="file" name="proof_of_notice_publication" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600"></div>
                            </div>
                        </div>
                        <div x-show="hasCancellationType('Capital Reduction')" x-cloak>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">If Capital is Affected (SEC Filing)</div>
                            <div class="mt-2 grid grid-cols-1 gap-3">
                                <div><label class="text-xs text-gray-600">Amended Articles of Incorporation</label><input type="file" name="amended_articles" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">Audited Financial Statements</label><input type="file" name="audited_financial_statements" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">Treasurer’s Affidavit</label><input type="file" name="treasurer_affidavit" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600"></div>
                                <div><label class="text-xs text-gray-600">SEC Forms / Filing Proof</label><input type="file" name="sec_filing_proof" accept="application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showVoidModal = false">Cancel</button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\certificate-preview.blade.php ENDPATH**/ ?>