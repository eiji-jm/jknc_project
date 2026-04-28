<?php
    $sheetRows = collect($individualInstallmentSheetRows ?? []);
    $blankRows = max(26 - $sheetRows->count(), 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111827; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #1f2937; padding: 4px 5px; vertical-align: top; }
        th { font-size: 9px; }
        td { font-size: 8.5px; height: 20px; }
        .title { font-weight: 700; margin-bottom: 6px; letter-spacing: .15em; text-align: center; }
    </style>
</head>
<body>
    <div class="title">INDIVIDUAL INSTALLMENT</div>
    <table>
        <thead>
            <tr>
                <th colspan="4">STOCK SUBSCRIBED</th>
                <th colspan="4">STOCK PAYMENTS</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>No. Shares</th>
                <th>No. of Installments</th>
                <th>Value</th>
                <th>Date</th>
                <th>Date</th>
                <th>What Installments</th>
                <th>Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sheetRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sheetRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sheetRow['subscribed_date'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['subscribed_shares'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['subscribed_installments'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['subscribed_value'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['payment_date'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['posted_date'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['installment_no'] ?? ''); ?></td>
                    <td><?php echo e($sheetRow['amount_paid'] ?? ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php for($i = 0; $i < $blankRows; $i++): ?>
                <tr>
                    <?php for($j = 0; $j < 8; $j++): ?>
                        <td>&nbsp;</td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\installment-pdf.blade.php ENDPATH**/ ?>