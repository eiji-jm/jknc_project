<?php
    $historyRows = ($journalEntries ?? collect())
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
        ->take(28)
        ->values();
    $blankRows = max(28 - $sheetRows->count(), 0);
    $runningTotal = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 6mm; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111827; font-size: 8.5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #1f2937; padding: 3px 4px; vertical-align: middle; }
        .title { font-size: 11px; font-weight: 700; text-align: center; margin-bottom: 2px; text-transform: uppercase; }
        .header { font-size: 7.5px; font-weight: 700; text-transform: uppercase; }
        .subheader { font-size: 7px; font-weight: 700; }
        .row td { height: 18px; }
    </style>
</head>
<body>
    <div class="title">Journal</div>
    <table>
        <thead>
            <?php if($isIssuanceSheet): ?>
                <tr>
                    <th rowspan="2" class="header">In Whose Name</th>
                    <th colspan="4" class="header">Certificate Issued</th>
                    <th rowspan="2" class="header">Received By<br>(Signature)</th>
                </tr>
                <tr>
                    <th class="subheader">Ledger Portfolio</th>
                    <th class="subheader">Certificate Number</th>
                    <th class="subheader">No. Shares</th>
                    <th class="subheader">Total No. Shares</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th rowspan="2" class="header">Date</th>
                    <th rowspan="2" class="header">By Whom Surrendered</th>
                    <th colspan="3" class="header">Certificate Cancelled</th>
                    <th rowspan="2" class="header">Surrendered By<br>(Signature)</th>
                </tr>
                <tr>
                    <th class="subheader">Ledger Portfolio</th>
                    <th class="subheader">Certificate Number</th>
                    <th class="subheader">No. Shares</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sheetRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $runningTotal += $isIssuanceSheet ? (int) ($entry->no_shares ?? 0) : 0;
                ?>
                <tr class="row">
                    <?php if($isIssuanceSheet): ?>
                        <td><?php echo e($entry->shareholder ?? ''); ?></td>
                        <td><?php echo e($entry->ledger_folio ?? ''); ?></td>
                        <td><?php echo e($entry->certificate_no ?? ''); ?></td>
                        <td><?php echo e($entry->no_shares ?? ''); ?></td>
                        <td><?php echo e($runningTotal); ?></td>
                        <td></td>
                    <?php else: ?>
                        <td><?php echo e(optional($entry->entry_date)->format('m/d/Y') ?: ''); ?></td>
                        <td><?php echo e($entry->shareholder ?? ''); ?></td>
                        <td><?php echo e($entry->ledger_folio ?? ''); ?></td>
                        <td><?php echo e($entry->certificate_no ?? ''); ?></td>
                        <td><?php echo e($entry->no_shares ?? ''); ?></td>
                        <td></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php for($i = 0; $i < $blankRows; $i++): ?>
                <tr class="row">
                    <?php for($j = 0; $j < 6; $j++): ?>
                        <td></td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/corporate/stock-transfer-book/journal-pdf.blade.php ENDPATH**/ ?>