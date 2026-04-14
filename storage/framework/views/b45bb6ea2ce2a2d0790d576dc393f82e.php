<?php $__env->startSection('content'); ?>
<?php
    $historyRows = collect($journalEntries ?? [])
        ->sortBy(fn ($entry) => sprintf(
            '%s-%010d',
            optional($entry->entry_date)->format('Ymd') ?: '00000000',
            (int) ($entry->id ?? 0)
        ))
        ->values();
    $isIssuanceSheet = strtolower((string) ($journal->transaction_type ?? '')) !== 'cancellation';
    $sheetRows = $historyRows
        ->filter(fn ($entry) => $isIssuanceSheet
            ? strtolower((string) ($entry->transaction_type ?? '')) !== 'cancellation'
            : strtolower((string) ($entry->transaction_type ?? '')) === 'cancellation')
        ->take(26)
        ->values();
    $blankRows = max(26 - $sheetRows->count(), 0);
    $runningTotal = 0;
    $baseCertificateNo = $journal->certificate_no ?? null;
    $baseSubscriptionShares = collect($relatedInstallments ?? [])
        ->filter(fn ($installment) => ($installment->stock_number ?? null) === $baseCertificateNo)
        ->sum(fn ($installment) => (int) ($installment->no_shares ?? 0));
?>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold">Journal Preview</div>
                <div class="text-xs text-gray-500">Journal No. <?php echo e($journal->journal_no ?? '-'); ?></div>
            </div>
            <div class="flex-1"></div>
            <?php if(!empty($editRoute)): ?>
                <a href="<?php echo e($editRoute); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <?php endif; ?>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                <div class="xl:col-span-3 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="text-sm font-semibold text-gray-900">Journal In-System Table</div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($isIssuanceSheet ? 'Journal records subscription and payment transactions.' : 'Journal records cancellation transactions.'); ?>

                            </div>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-800 text-[11px]">
                                    <thead>
                                        <?php if($isIssuanceSheet): ?>
                                            <tr class="bg-gray-50">
                                                <th colspan="6" class="border-b border-gray-800 px-2 py-2 text-center text-sm font-bold uppercase tracking-wide">Journal</th>
                                            </tr>
                                            <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                                <th rowspan="2" class="border border-gray-800 px-2 py-2 text-center">In Whose Name</th>
                                                <th colspan="4" class="border border-gray-800 px-2 py-2 text-center">Certificate Issued</th>
                                                <th rowspan="2" class="border border-gray-800 px-2 py-2 text-center">Received By<br>(Signature)</th>
                                            </tr>
                                            <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                                <th class="border border-gray-800 px-2 py-2 text-center">Ledger Portfolio</th>
                                                <th class="border border-gray-800 px-2 py-2 text-center">Certificate Number</th>
                                                <th class="border border-gray-800 px-2 py-2 text-center">No. Shares</th>
                                                <th class="border border-gray-800 px-2 py-2 text-center">Running Share Balance</th>
                                            </tr>
                                        <?php else: ?>
                                            <tr class="bg-gray-50">
                                                <th colspan="6" class="border-b border-gray-800 px-2 py-2 text-center text-sm font-bold uppercase tracking-wide">Journal</th>
                                            </tr>
                                            <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                                <th rowspan="2" class="border border-gray-800 px-2 py-2 text-center">Date</th>
                                                <th rowspan="2" class="border border-gray-800 px-2 py-2 text-center">By Whom Surrendered</th>
                                                <th colspan="3" class="border border-gray-800 px-2 py-2 text-center">Certificate Cancelled</th>
                                                <th rowspan="2" class="border border-gray-800 px-2 py-2 text-center">Surrendered By<br>(Signature)</th>
                                            </tr>
                                            <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                                <th class="border border-gray-800 px-2 py-2 text-center">Ledger Portfolio</th>
                                                <th class="border border-gray-800 px-2 py-2 text-center">Certificate Number</th>
                                                <th class="border border-gray-800 px-2 py-2 text-center">No. Shares</th>
                                            </tr>
                                        <?php endif; ?>
                                    </thead>
                                    <tbody class="text-gray-900">
                                        <?php $__currentLoopData = $sheetRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $entryType = strtolower((string) ($entry->transaction_type ?? ''));
                                                $isShareAddingEntry = $isIssuanceSheet
                                                    && (
                                                        str_contains(strtolower((string) ($entry->particulars ?? '')), 'subscription')
                                                        || str_contains(strtolower((string) ($entry->remarks ?? '')), 'subscription')
                                                    );

                                                if ($isShareAddingEntry) {
                                                    $runningTotal += (int) ($entry->no_shares ?? 0);
                                                } elseif ($runningTotal === 0 && $baseSubscriptionShares > 0) {
                                                    $runningTotal = (int) $baseSubscriptionShares;
                                                }
                                            ?>
                                            <tr>
                                                <?php if($isIssuanceSheet): ?>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->shareholder ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->ledger_folio ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->certificate_no ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2">
                                                        <?php echo e($isShareAddingEntry ? ($entry->no_shares ?? '') : ''); ?>

                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($runningTotal); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"></td>
                                                <?php else: ?>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e(optional($entry->entry_date)->format('m/d/Y') ?: ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->shareholder ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->ledger_folio ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->certificate_no ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"><?php echo e($entry->no_shares ?? ''); ?></td>
                                                    <td class="border border-gray-300 px-2 py-2"></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php for($i = 0; $i < $blankRows; $i++): ?>
                                            <tr>
                                                <?php for($j = 0; $j < 6; $j++): ?>
                                                    <td class="border border-gray-300 px-2 py-4"></td>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="text-sm font-semibold text-gray-900">Digital PDF Preview</div>
                            <div class="text-xs text-gray-500">Printable journal copy using the same sheet.</div>
                        </div>
                        <div class="p-4">
                            <?php if(!empty($generatedPreviewUrl)): ?>
                                <iframe src="<?php echo e($generatedPreviewUrl); ?>" class="w-full h-[980px] border rounded bg-white"></iframe>
                            <?php else: ?>
                                <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">Journal preview PDF unavailable.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-2 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Journal Information</div>
                        <div class="text-xs text-gray-500 mb-3">Journal shows transaction history such as subscription, payment, and cancellation. Payment entries do not add to share balance.</div>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Journal No.</span><div class="font-medium text-gray-900"><?php echo e($journal->journal_no ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Entry Date</span><div class="font-medium text-gray-900"><?php echo e(optional($journal->entry_date)->format('M d, Y') ?: '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Ledger Folio</span><div class="font-medium text-gray-900"><?php echo e($journal->ledger_folio ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Transaction Type</span><div class="font-medium text-gray-900"><?php echo e($journal->transaction_type ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900"><?php echo e($journal->certificate_no ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Shares for This Entry</span><div class="font-medium text-gray-900"><?php echo e($journal->no_shares ?? '-'); ?></div></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                        <div class="space-y-3 text-sm">
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Certificates</div><div class="mt-1 text-gray-900"><?php echo e(($relatedCertificates ?? collect())->count()); ?> linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Ledger Entries</div><div class="mt-1 text-gray-900"><?php echo e(($relatedLedgers ?? collect())->count()); ?> linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div><div class="mt-1 text-gray-900"><?php echo e(($relatedInstallments ?? collect())->count()); ?> linked</div></div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-2">Particulars / Remarks</div>
                        <p class="text-sm text-gray-700"><?php echo e($journal->particulars ?? '-'); ?></p>
                        <p class="text-xs text-gray-600 mt-2"><?php echo e($journal->remarks ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/stock-transfer-book/journal-preview.blade.php ENDPATH**/ ?>