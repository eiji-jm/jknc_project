<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 14mm; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        .card { border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; margin-bottom: 14px; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { color: #6b7280; margin-bottom: 18px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { border-bottom: 1px solid #e5e7eb; padding: 10px 0; vertical-align: top; }
        .label { width: 34%; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: .08em; }
        .value { font-weight: 600; }
    </style>
</head>
<body>
    <div class="title">NatGov Draft Preview</div>
    <div class="subtitle">Generated from the saved national government filing record.</div>
    <div class="card">
        <table class="grid">
            <tr><td class="label">Company</td><td class="value"><?php echo e($natgov->client ?: '-'); ?></td></tr>
            <tr><td class="label">TIN</td><td class="value"><?php echo e($natgov->tin ?: '-'); ?></td></tr>
            <tr><td class="label">Agency</td><td class="value"><?php echo e($natgov->agency ?: '-'); ?></td></tr>
            <tr><td class="label">Registration Status</td><td class="value"><?php echo e($natgov->registration_status ?: '-'); ?></td></tr>
            <tr><td class="label">Registration Date</td><td class="value"><?php echo e(optional($natgov->registration_date)->format('F d, Y') ?: '-'); ?></td></tr>
            <tr><td class="label">Deadline Date</td><td class="value"><?php echo e(optional($natgov->deadline_date)->format('F d, Y') ?: '-'); ?></td></tr>
            <tr><td class="label">Registration No.</td><td class="value"><?php echo e($natgov->registration_no ?: '-'); ?></td></tr>
            <tr><td class="label">Status</td><td class="value"><?php echo e($natgov->status ?: '-'); ?></td></tr>
            <tr><td class="label">Uploaded By</td><td class="value"><?php echo e($natgov->uploaded_by ?: '-'); ?></td></tr>
            <tr><td class="label">Date Uploaded</td><td class="value"><?php echo e(optional($natgov->date_uploaded)->format('F d, Y') ?: '-'); ?></td></tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\natgov\pdf.blade.php ENDPATH**/ ?>