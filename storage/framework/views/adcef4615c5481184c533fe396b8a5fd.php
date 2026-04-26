<?php $__env->startSection('content'); ?>
<style>
    .proposal-workspace-card { min-height: calc(100vh - 15rem); }
    .proposal-preview-shell { min-height: 1100px; }
    .proposal-preview-scroll { max-height: calc(100vh - 16rem); overflow: auto; background: #eef2f7; }
    .proposal-doc { font-family: Georgia, "Times New Roman", serif; color: #111827; font-size: 12px; line-height: 1.58; }
    .proposal-page {
        width: min(100%, 760px);
        margin: 0 auto 24px;
        background: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid #dbe2ea;
        padding: 56px 64px 84px;
        position: relative;
    }
    .proposal-inner-page { min-height: 1020px; padding-top: 62px; }
    .proposal-page-body { width: 100%; }
    .proposal-cover { min-height: 1100px; position: relative; }
    .proposal-cover-logo-wrap { width: 100%; }
    .proposal-brand-logo { width: 470px; max-width: 100%; height: auto; object-fit: contain; }
    .proposal-cover-body { margin-top: 165px; color: #0031af; }
    .proposal-cover-year { font-size: 86px; line-height: 1; font-style: italic; font-weight: 700; }
    .proposal-cover-title { margin-top: 14px; font-size: 32px; line-height: 1.22; font-style: italic; }
    .proposal-cover-date { margin-top: 60px; font-size: 14px; color: #111827; font-style: italic; }
    .proposal-presented-label { margin-top: 70px; font-size: 15px; font-style: italic; }
    .proposal-presented-name, .proposal-presented-location { margin-top: 10px; font-size: 16px; font-weight: 700; font-style: italic; }
    .proposal-cover-footer { position: absolute; left: 58px; right: 58px; bottom: 54px; }
    .proposal-contact-strip { margin: 0; text-align: center; color: #0031af; font-style: italic; }
    .proposal-contact-inline { display: flex; justify-content: center; flex-wrap: nowrap; gap: 26px; font-size: 12px; }
    .proposal-contact-address { margin-top: 4px; text-align: center; color: #0031af; font-size: 13px; font-style: italic; }
    .proposal-page-footer { position: absolute; left: 64px; right: 64px; bottom: 22px; font-size: 10px; line-height: 1.2; color: #111827; }
    .proposal-page-footer div { margin: 0; }
    .proposal-section-heading { margin: 10px 0 18px; font-size: 18px; line-height: 1.22; color: #0031af; font-style: italic; font-weight: 700; letter-spacing: 0.01em; }
    .proposal-section-number { display: inline-block; min-width: 34px; margin-right: 8px; }
    .proposal-subheading { margin: 18px 0 8px; font-size: 13px; line-height: 1.35; color: #111827; font-weight: 700; }
    .proposal-subheading-blue { color: #0031af; font-style: italic; font-weight: 700; }
    .proposal-subheading-tight { margin-top: 16px; }
    .proposal-term-number { display: inline-block; min-width: 18px; }
    .proposal-paragraph, .proposal-note, .proposal-system-note { margin: 0 0 12px; font-size: 11.5px; line-height: 1.7; text-align: justify; }
    .proposal-note { color: #475569; font-style: italic; }
    .proposal-system-note { margin-top: 18px; font-size: 10px; color: #475569; }
    .proposal-bullet-list, .proposal-numbered-list { margin: 0 0 10px 18px; padding: 0; font-size: 11.5px; line-height: 1.7; }
    .proposal-bullet-list li { margin-bottom: 6px; }
    .proposal-numbered-list li { margin-bottom: 6px; }
    .proposal-requirement-group { margin-bottom: 12px; }
    .proposal-requirement-label { margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #0031af; }
    .proposal-term-block { margin-bottom: 16px; }
    .proposal-service-table, .proposal-pricing-table, .proposal-data-table { width: 100%; border-collapse: collapse; margin-top: 12px; table-layout: fixed; }
    .proposal-service-table th, .proposal-service-table td, .proposal-pricing-table th, .proposal-pricing-table td, .proposal-data-table th, .proposal-data-table td {
        border: 1px solid #111827;
        padding: 8px 10px;
        font-size: 10.5px;
        vertical-align: top;
    }
    .proposal-service-table th, .proposal-pricing-table th, .proposal-data-table th { text-align: left; font-weight: 700; background: #f8fafc; }
    .proposal-service-no { width: 7%; }
    .proposal-service-area { width: 24%; }
    .proposal-service-scope { width: 69%; }
    .proposal-service-area-title { font-style: italic; font-size: 12px; }
    .proposal-service-scope-list ol { margin: 0; padding-left: 18px; }
    .proposal-service-scope-list li { margin: 0 0 4px; }
    .proposal-service-scope-list ol[type="a"] { list-style-type: lower-alpha; }
    .proposal-service-table { margin-top: 22px; }
    .proposal-pricing-table, .proposal-data-table { margin-top: 16px; }
    .proposal-pricing-table th:last-child, .proposal-pricing-table td:last-child { text-align: center; width: 34%; }
    .proposal-pricing-table .is-total td { font-weight: 700; color: #0031af; }
    .proposal-term-block + .proposal-term-block { margin-top: 8px; }
    .proposal-end-note { margin: 18px 0 10px; font-size: 11px; font-style: italic; }
    .proposal-signature-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px; margin-top: 30px; }
    .proposal-signature-label { font-size: 12px; font-weight: 700; margin-bottom: 50px; }
    .proposal-signature-line { border-bottom: 1px solid #111827; padding-bottom: 6px; font-size: 12px; }
    .proposal-signature-subline { margin-top: 8px; font-size: 12px; }
    .proposal-footer-note { margin-top: 90px; font-size: 10px; color: #5a6470; }
    .proposal-footer-note div { margin-bottom: 2px; }
    @media (max-width: 1279px) {
        .proposal-preview-shell { min-height: 900px; }
    }
    @media (max-width: 768px) {
        .proposal-page { padding: 32px 20px; }
        .proposal-cover { min-height: auto; }
        .proposal-cover-body { margin-top: 72px; }
        .proposal-cover-year { font-size: 56px; }
        .proposal-cover-title { font-size: 26px; }
        .proposal-cover-footer { position: static; margin-top: 72px; }
        .proposal-page-footer { left: 20px; right: 20px; bottom: 18px; }
        .proposal-contact-inline { flex-wrap: wrap; gap: 12px 18px; }
        .proposal-signature-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
            <a href="<?php echo e(route('deals.show', $deal->id)); ?>" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold"><?php echo e(($readOnlyPreview ?? false) ? $deal->deal_code : 'Create Proposal'); ?></div>
                <div class="text-xs text-gray-500"><?php echo e(($readOnlyPreview ?? false) ? ($proposal->reference_id ?: 'Saved proposal preview') : $deal->deal_code); ?></div>
            </div>
            <div class="flex-1"></div>
            <span id="proposal-preview-badge" class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                <?php echo e($generatedPdfUrl ? (($readOnlyPreview ?? false) ? 'Proposal preview ready' : 'Exact preview ready') : 'Generating preview'); ?>

            </span>
            <a
                id="proposal-pdf-download"
                href="<?php echo e($generatedPdfDownloadUrl ?: '#'); ?>"
                class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 <?php echo e($generatedPdfDownloadUrl ? '' : 'pointer-events-none opacity-50'); ?>"
            >
                Download PDF
            </a>
        </div>
    </div>
</div>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(430px,0.95fr)_minmax(0,1.55fr)] gap-6 p-6">
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden proposal-workspace-card">
                <div class="border-b border-gray-100 px-5 py-4">
                    <div class="text-sm font-semibold text-gray-900"><?php echo e(($readOnlyPreview ?? false) ? 'Saved Proposal Details' : 'Create Proposal Form'); ?></div>
                    <div class="mt-1 text-xs text-gray-500">
                        <?php echo e(($readOnlyPreview ?? false)
                            ? 'This proposal has already been prepared and is shown here as a read-only record.'
                            : 'The form is auto-filled from the deal. Edit any field and the right-side preview regenerates for the final PDF output.'); ?>

                    </div>
                </div>

                <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-5 py-5">
                    <?php if($readOnlyPreview ?? false): ?>
                    <div class="space-y-5 text-sm text-gray-700">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Deal Code</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e($deal->deal_code); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Reference ID</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e($proposal->reference_id ?: '-'); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Prepared Date</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e(optional($proposal->proposal_date)->format('F d, Y') ?: '-'); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Prepared By</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e($proposal->prepared_by_name ?: '-'); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 md:col-span-2">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Location</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e($proposal->location ?: '-'); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 md:col-span-2">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Service Type</div>
                                <div class="mt-1 font-semibold text-gray-900"><?php echo e($proposal->service_type ?: '-'); ?></div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Proposal Message</div>
                            <p class="mt-2 whitespace-pre-line leading-6 text-gray-800"><?php echo e($proposal->our_proposal_text ?: '-'); ?></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Scope of Service / Assistance</div>
                            <p class="mt-2 whitespace-pre-line leading-6 text-gray-800"><?php echo e($proposal->scope_of_service ?: '-'); ?></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">What You Will Receive</div>
                            <p class="mt-2 whitespace-pre-line leading-6 text-gray-800"><?php echo e($proposal->what_you_will_receive ?: '-'); ?></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                <?php echo e($requirementGroup === 'sole' ? 'Requirements - Sole / Individual' : 'Requirements - Juridical'); ?>

                            </div>
                            <p class="mt-2 whitespace-pre-line leading-6 text-gray-800"><?php echo e($requirementGroup === 'sole' ? ($proposal->requirements_sole ?: '-') : ($proposal->requirements_juridical ?: '-')); ?></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Optional Requirements</div>
                            <p class="mt-2 whitespace-pre-line leading-6 text-gray-800"><?php echo e($proposal->requirements_optional ?: '-'); ?></p>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Regular Price</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_regular, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Discount</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_discount, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Subtotal</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_subtotal, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Tax</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_tax, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_total, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Downpayment</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_down, 2)); ?></div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 md:col-span-2">
                                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Balance</div>
                                <div class="mt-1 font-semibold text-gray-900">P<?php echo e(number_format((float) $proposal->price_balance, 2)); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <form method="POST" action="<?php echo e(route('deals.proposal.update', $deal)); ?>" id="proposal-live-form" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600">Reference ID</label>
                                <input type="text" name="reference_id" value="<?php echo e(old('reference_id', $proposal->reference_id)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">CRUD ID</label>
                                <input type="text" name="crud_id" value="<?php echo e(old('crud_id', $proposal->crud_id)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Date</label>
                                <input type="date" name="proposal_date" value="<?php echo e(old('proposal_date', optional($proposal->proposal_date)->format('Y-m-d'))); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Location</label>
                                <input type="text" name="location" value="<?php echo e(old('location', $proposal->location)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Service Type</label>
                                <input type="text" name="service_type" value="<?php echo e(old('service_type', $proposal->service_type)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Prepared By Name</label>
                                <input type="text" name="prepared_by_name" value="<?php echo e(old('prepared_by_name', $proposal->prepared_by_name)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Prepared By ID</label>
                                <input type="text" name="prepared_by_id" value="<?php echo e(old('prepared_by_id', $proposal->prepared_by_id)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Our Proposal Text</label>
                            <textarea name="our_proposal_text" rows="6" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('our_proposal_text', $proposal->our_proposal_text)); ?></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Scope of Service / Assistance</label>
                            <textarea name="scope_of_service" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('scope_of_service', $proposal->scope_of_service)); ?></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">What You Will Receive</label>
                            <textarea name="what_you_will_receive" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('what_you_will_receive', $proposal->what_you_will_receive)); ?></textarea>
                        </div>
                        <?php if($requirementGroup === 'sole'): ?>
                            <div>
                                <label class="text-xs text-gray-600">Requirements - Sole / Individual</label>
                                <textarea name="requirements_sole" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('requirements_sole', $proposal->requirements_sole)); ?></textarea>
                            </div>
                        <?php else: ?>
                            <div>
                                <label class="text-xs text-gray-600">Requirements - Juridical</label>
                                <textarea name="requirements_juridical" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('requirements_juridical', $proposal->requirements_juridical)); ?></textarea>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="text-xs text-gray-600">Optional Requirements</label>
                            <textarea name="requirements_optional" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('requirements_optional', $proposal->requirements_optional)); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600">Regular Price</label>
                                <input type="number" step="0.01" min="0" name="price_regular" value="<?php echo e(old('price_regular', $proposal->price_regular)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Discount</label>
                                <input type="number" step="0.01" min="0" name="price_discount" value="<?php echo e(old('price_discount', $proposal->price_discount)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Subtotal</label>
                                <input type="number" step="0.01" min="0" name="price_subtotal" value="<?php echo e(old('price_subtotal', $proposal->price_subtotal)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Tax</label>
                                <input type="number" step="0.01" min="0" name="price_tax" value="<?php echo e(old('price_tax', $proposal->price_tax)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Total</label>
                                <input type="number" step="0.01" min="0" name="price_total" value="<?php echo e(old('price_total', $proposal->price_total)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Downpayment</label>
                                <input type="number" step="0.01" min="0" name="price_down" value="<?php echo e(old('price_down', $proposal->price_down)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-600">Balance</label>
                                <input type="number" step="0.01" min="0" name="price_balance" value="<?php echo e(old('price_balance', $proposal->price_balance)); ?>" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Proposal</button>
                            <button type="button" id="proposal-refresh-button" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Refresh Preview</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-[#f8fafc] overflow-hidden flex flex-col proposal-workspace-card">
                <div class="border-b border-gray-100 bg-white px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e(($readOnlyPreview ?? false) ? $deal->deal_code.' Preview' : 'Proposal Preview'); ?></div>
                            <div id="proposal-preview-status" class="mt-1 text-xs text-gray-500">
                                <?php echo e($generatedPdfUrl
                                    ? (($readOnlyPreview ?? false) ? 'This is the saved proposal preview aligned with the downloadable PDF output.' : 'This preview is aligned with the downloadable PDF output.')
                                    : (($readOnlyPreview ?? false) ? 'This saved proposal preview is aligned with the PDF output while the downloadable file is generated in the background.' : 'This preview is aligned with the PDF output while the downloadable file is generated in the background.')); ?>

                            </div>
                        </div>
                    </div>
                    <div id="proposal-preview-error" class="mt-3 <?php echo e($previewError ? '' : 'hidden'); ?> rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        <?php echo e($previewError ?: ''); ?>

                    </div>
                </div>

                <div class="flex-1 p-5">
                    <div id="proposal-preview-panel" class="proposal-preview-shell h-full rounded-2xl border border-gray-200 bg-white overflow-hidden">
                        <div id="proposal-preview-scroll" class="proposal-preview-scroll h-full">
                            <div id="proposal-preview-html">
                                <?php echo $proposalDocumentHtml; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (! ($readOnlyPreview ?? false)): ?>
<script type="application/json" id="dealProposalPreviewData"><?php echo json_encode([
    'previewUrl' => route('deals.proposal.preview', $deal), ]) ?></script>

<script>
(() => {
    const previewDataElement = document.getElementById('dealProposalPreviewData');
    const previewData = previewDataElement ? JSON.parse(previewDataElement.textContent || '{}') : {};
    const previewUrl = previewData.previewUrl || '';
    const form = document.getElementById('proposal-live-form');
    const refreshButton = document.getElementById('proposal-refresh-button');
    const htmlPreview = document.getElementById('proposal-preview-html');
    const status = document.getElementById('proposal-preview-status');
    const errorBox = document.getElementById('proposal-preview-error');
    const badge = document.getElementById('proposal-preview-badge');
    const pdfDownload = document.getElementById('proposal-pdf-download');

    if (!form || !refreshButton || !htmlPreview || !status || !errorBox || !badge || !pdfDownload) {
        return;
    }

    let previewTimer = null;
    let activeController = null;

    const setButtonState = (link, href) => {
        if (href) {
            link.href = href;
            link.classList.remove('pointer-events-none', 'opacity-50');
            return;
        }

        link.href = '#';
        link.classList.add('pointer-events-none', 'opacity-50');
    };

    const setBusy = (message) => {
        badge.textContent = 'Generating preview';
        badge.classList.remove('bg-emerald-50', 'text-emerald-700', 'bg-amber-50', 'text-amber-700');
        badge.classList.add('bg-blue-50', 'text-blue-700');
        status.textContent = message;
        errorBox.classList.add('hidden');
    };

    const setReady = (payload) => {
        badge.textContent = payload.pdf_url ? 'Preview ready' : 'HTML preview ready';
        badge.classList.remove('bg-blue-50', 'text-blue-700', 'bg-amber-50', 'text-amber-700');
        badge.classList.add('bg-emerald-50', 'text-emerald-700');
        status.textContent = payload.pdf_url
            ? 'Preview updated and downloadable PDF refreshed.'
            : 'Preview updated from the same proposal data used for the generated PDF.';
        errorBox.classList.add('hidden');
        htmlPreview.innerHTML = payload.html || htmlPreview.innerHTML;

        setButtonState(pdfDownload, payload.pdf_download_url || null);
    };

    const setError = (message) => {
        badge.textContent = 'Preview failed';
        badge.classList.remove('bg-blue-50', 'text-blue-700', 'bg-emerald-50', 'text-emerald-700');
        badge.classList.add('bg-amber-50', 'text-amber-700');
        status.textContent = 'The proposal preview could not be refreshed just yet.';
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const refreshPreview = async () => {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        setBusy('Regenerating the proposal preview for the PDF output.');

        try {
            const formData = new FormData(form);
            formData.delete('_method');

            const response = await fetch(previewUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                signal: activeController.signal,
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Preview generation failed.');
            }

            setReady(payload);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            setError(error.message || 'Preview generation failed.');
        }
    };

    const queuePreview = () => {
        window.clearTimeout(previewTimer);
        previewTimer = window.setTimeout(refreshPreview, 700);
    };

    form.addEventListener('input', queuePreview);
    form.addEventListener('change', queuePreview);
    refreshButton.addEventListener('click', refreshPreview);
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/deals/proposal/show.blade.php ENDPATH**/ ?>