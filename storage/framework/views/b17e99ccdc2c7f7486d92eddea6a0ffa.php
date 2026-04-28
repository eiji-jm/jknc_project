<?php
    $certificate = $requestRecord->certificate;
    $requestedAt = optional($requestRecord->requested_at)->timezone('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issuance Request <?php echo e($requestRecord->reference_no); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 16mm;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.45;
        }

        .page {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.06em;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .title {
            margin: 16px 0 18px;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th,
        td {
            border: 1px solid #374151;
            padding: 7px 8px;
            vertical-align: top;
        }

        th {
            width: 31%;
            text-align: left;
            background: #f3f4f6;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .notes {
            min-height: 110px;
            white-space: pre-line;
        }

        .footer {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
        }

        .signature-box {
            width: 48%;
            text-align: center;
            padding-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 6px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1><?php echo e(strtoupper($certificate->corporation_name ?: 'JOHN KELLY & COMPANY')); ?></h1>
            <p>Company Reg. No.: <?php echo e($certificate->company_reg_no ?: '-'); ?></p>
            <p>Request for Certificate Issuance</p>
        </div>

        <div class="title">Issuance Request Sheet</div>

        <table>
            <tr><th>Reference No.</th><td><?php echo e($requestRecord->reference_no); ?></td></tr>
            <tr><th>Date and Time</th><td><?php echo e($requestedAt ? $requestedAt->format('F d, Y h:i A') : '-'); ?></td></tr>
            <tr><th>Type of Request</th><td><?php echo e($requestRecord->request_type); ?></td></tr>
            <tr><th>COS / CV</th><td><?php echo e($requestRecord->issuance_type); ?></td></tr>
            <tr><th>Requester</th><td><?php echo e($requestRecord->requester); ?></td></tr>
            <tr><th>Received By</th><td><?php echo e($requestRecord->received_by ?: '-'); ?></td></tr>
            <tr><th>Issued By</th><td><?php echo e($requestRecord->issued_by ?: '-'); ?></td></tr>
            <tr><th>Status</th><td><?php echo e(ucfirst($requestRecord->status)); ?></td></tr>
        </table>

        <table>
            <tr><th>Certificate Stock</th><td><?php echo e($certificate?->certificate_type ?: $requestRecord->issuance_type); ?> - <?php echo e($certificate?->stock_number ?: '-'); ?></td></tr>
            <tr><th>Stockholder</th><td><?php echo e($certificate?->stockholder_name ?: '-'); ?></td></tr>
            <tr><th>Number of Shares</th><td><?php echo e($certificate?->number ?: '-'); ?></td></tr>
            <tr><th>Par Value</th><td><?php echo e($certificate?->par_value !== null ? number_format((float) $certificate->par_value, 2) : '-'); ?></td></tr>
            <tr><th>Total Amount</th><td><?php echo e($certificate?->amount !== null ? number_format((float) $certificate->amount, 2) : '-'); ?></td></tr>
            <tr><th>Notes</th><td class="notes"><?php echo e($requestRecord->notes ?: '-'); ?></td></tr>
        </table>

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"><?php echo e($requestRecord->received_by ?: 'Received By'); ?></div>
            </div>
            <div class="signature-box">
                <div class="signature-line"><?php echo e($requestRecord->issued_by ?: 'Issued By'); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\issuance-request-pdf.blade.php ENDPATH**/ ?>