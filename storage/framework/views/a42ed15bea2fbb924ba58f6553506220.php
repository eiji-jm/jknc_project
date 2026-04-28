<?php $__env->startSection('content'); ?>
<?php
    $fullName = trim(collect([$ledger->first_name ?? '', $ledger->middle_name ?? '', $ledger->family_name ?? ''])->filter()->implode(' '));

    $issuedRows = collect($journalEntries ?? [])
        ->filter(function ($entry) {
            $type = strtolower((string) ($entry->transaction_type ?? ''));
            $particulars = strtolower((string) ($entry->particulars ?? ''));
            $remarks = strtolower((string) ($entry->remarks ?? ''));
            $shares = (int) ($entry->no_shares ?? 0);

            if ($type === 'cancellation') {
                return false;
            }

            if ($shares <= 0) {
                return false;
            }

            if (str_contains($particulars, 'payment') || str_contains($remarks, 'payment')) {
                return false;
            }

            return true;
        })
        ->sortBy(fn ($entry) => sprintf(
            '%s-%010d',
            optional($entry->entry_date)->format('Ymd') ?: '00000000',
            (int) ($entry->id ?? 0)
        ))
        ->take(26)
        ->values();

    $cancelledRows = collect($journalEntries ?? [])
        ->filter(fn ($entry) => strtolower((string) ($entry->transaction_type ?? '')) === 'cancellation')
        ->sortBy(fn ($entry) => sprintf(
            '%s-%010d',
            optional($entry->entry_date)->format('Ymd') ?: '00000000',
            (int) ($entry->id ?? 0)
        ))
        ->take(26)
        ->values();

    $maxRows = max(26, $issuedRows->count(), $cancelledRows->count());
?>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="<?php echo e($backRoute); ?>" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold">Ledger Preview</div>
                <div class="text-xs text-gray-500">Shareholder: <?php echo e($fullName ?: '-'); ?></div>
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
                            <div class="text-sm font-semibold text-gray-900">Ledger In-System Table</div>
                            <div class="text-xs text-gray-500">Ledger shows shareholding records only. Payment transactions are not treated as additional share issuance.</div>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-800 text-[11px]">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th colspan="8" class="border-b border-gray-800 px-2 py-2 text-center text-sm font-bold uppercase tracking-wide">Ledger</th>
                                        </tr>
                                        <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                            <th colspan="4" class="border border-gray-800 px-2 py-2 text-center">Certificate Cancelled</th>
                                            <th colspan="4" class="border border-gray-800 px-2 py-2 text-center">Share-Issuing Entries</th>
                                        </tr>
                                        <tr class="bg-gray-50 text-[10px] font-semibold text-gray-700">
                                            <th class="border border-gray-800 px-2 py-2 text-center">Date</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Journal Portfolio</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Certificate Number</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Number of Shares</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Date</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Journal Portfolio</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Certificate Number</th>
                                            <th class="border border-gray-800 px-2 py-2 text-center">Number of Shares</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-900">
                                        <?php for($i = 0; $i < $maxRows; $i++): ?>
                                            <?php
                                                $cancel = $cancelledRows->get($i);
                                                $issue = $issuedRows->get($i);
                                            ?>
                                            <tr>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($cancel ? optional($cancel->entry_date)->format('m/d/Y') : ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($cancel->journal_no ?? ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($cancel->certificate_no ?? ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($cancel->no_shares ?? ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($issue ? optional($issue->entry_date)->format('m/d/Y') : ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($issue->journal_no ?? ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($issue->certificate_no ?? ''); ?></td>
                                                <td class="border border-gray-300 px-2 py-2"><?php echo e($issue->no_shares ?? ''); ?></td>
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
                            <div class="text-xs text-gray-500">Printable ledger copy using the same sheet.</div>
                        </div>
                        <div class="p-4">
                            <?php if(!empty($generatedPreviewUrl)): ?>
                                <iframe src="<?php echo e($generatedPreviewUrl); ?>" class="w-full h-[980px] border rounded bg-white"></iframe>
                            <?php else: ?>
                                <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">Ledger preview PDF unavailable.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-2 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Shareholder Information</div>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Name</span><div class="font-medium text-gray-900"><?php echo e($fullName ?: '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Nationality</span><div class="font-medium text-gray-900"><?php echo e($ledger->nationality ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Address</span><div class="font-medium text-gray-900"><?php echo e($ledger->address ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900"><?php echo e($ledger->tin ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900"><?php echo e($ledger->certificate_no ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Recorded Shareholding</span><div class="font-medium text-gray-900"><?php echo e($ledger->shares ?? '-'); ?></div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Registered</span><div class="font-medium text-gray-900"><?php echo e(optional($ledger->date_registered)->format('M d, Y') ?: '-'); ?></div></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                        <div class="space-y-3 text-sm">
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Certificates</div><div class="mt-1 text-gray-900"><?php echo e(($relatedCertificates ?? collect())->count()); ?> linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div><div class="mt-1 text-gray-900"><?php echo e(($relatedInstallments ?? collect())->count()); ?> linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Requests</div><div class="mt-1 text-gray-900"><?php echo e(($relatedRequests ?? collect())->count()); ?> linked</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\ledger-preview.blade.php ENDPATH**/ ?>