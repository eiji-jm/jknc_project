<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Letter</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22mm 18mm;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #111;
            line-height: 1.5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-sizing: border-box;
            padding: 22mm 18mm;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 24px;
            text-transform: uppercase;
        }

        .meta p {
            margin: 4px 0;
        }

        .signature {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="title">Request Letter</div>

        <div class="meta">
            <p><strong>Date:</strong> <?php echo e($correspondence->date ? \Carbon\Carbon::parse($correspondence->date)->format('F d, Y') : 'N/A'); ?></p>
            <p><strong><?php echo e($correspondence->sender_type ?? 'From'); ?>:</strong> <?php echo e($correspondence->sender ?? 'N/A'); ?></p>
            <p><strong>Subject:</strong> <?php echo e($correspondence->subject ?? 'N/A'); ?></p>
        </div>

        <p>Dear Sir/Madam,</p>

        <p>
            We respectfully request your attention regarding
            <?php echo e($correspondence->details ?? 'the matter described above'); ?>.
        </p>

        <p><strong>Department / Stakeholder:</strong> <?php echo e($correspondence->department ?? 'N/A'); ?></p>
        <p><strong>Sent Via:</strong> <?php echo e($correspondence->sent_via ?? 'Email'); ?></p>

        <div class="signature">
            <p>Respectfully,</p>
            <br><br>
            <p><strong><?php echo e($correspondence->user ?? 'Authorized Representative'); ?></strong></p>
            <p>Authorized Representative</p>
        </div>
    </div>
</body>
</html><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\correspondence\templates\request-letter.blade.php ENDPATH**/ ?>