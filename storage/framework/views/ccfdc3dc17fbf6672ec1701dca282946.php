<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.6;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .header {
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 16px;
            margin-bottom: 28px;
        }

        .company {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .sub {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .right-meta {
            text-align: right;
            font-size: 12px;
            line-height: 1.6;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 10px 0 28px;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .meta-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 130px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-value {
            border-bottom: 1px dotted #9ca3af;
            padding-left: 8px;
        }

        .body {
            min-height: 380px;
        }

        .body p {
            margin: 10px 0;
        }

        .body ul, .body ol {
            margin: 10px 0 10px 24px;
        }

        .signature {
            margin-top: 60px;
        }

        .line {
            width: 260px;
            border-bottom: 1px solid #6b7280;
            margin-top: 50px;
        }

        .sign-name {
            margin-top: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page">
        <table width="100%" class="header">
            <tr>
                <td>
                    <div class="company">JOHN KELLY &amp; COMPANY</div>
                    <div class="sub">Correspondence Preview</div>
                </td>
                <td class="right-meta">
                    <div><strong>Ref No:</strong> <?php echo e($correspondence->id ? 'COR-' . str_pad($correspondence->id, 5, '0', STR_PAD_LEFT) : 'AUTO-INCREMENT'); ?></div>
                    <div><strong>Date:</strong> <?php echo e($correspondence->date ? \Carbon\Carbon::parse($correspondence->date)->format('F d, Y') : '________________'); ?></div>
                    <div><strong>Type:</strong> <?php echo e($correspondence->type ?? $title); ?></div>
                </td>
            </tr>
        </table>

        <div class="title"><?php echo e($title); ?></div>

        <table class="meta-table">
            <tr>
                <td class="meta-label">TIN</td>
                <td class="meta-value"><?php echo e($correspondence->tin ?? '______________________________'); ?></td>
            </tr>
            <tr>
                <td class="meta-label"><?php echo e($correspondence->sender_type ?? 'From'); ?></td>
                <td class="meta-value"><?php echo e($correspondence->sender ?? '______________________________'); ?></td>
            </tr>
            <tr>
                <td class="meta-label">Department</td>
                <td class="meta-value"><?php echo e($correspondence->department ?? '______________________________'); ?></td>
            </tr>
            <tr>
                <td class="meta-label">Subject</td>
                <td class="meta-value"><strong><?php echo e($correspondence->subject ?? '______________________________'); ?></strong></td>
            </tr>
            <tr>
                <td class="meta-label">Sent Via</td>
                <td class="meta-value"><?php echo e($correspondence->sent_via ?? 'Email'); ?></td>
            </tr>
            <?php if($correspondence->deadline): ?>
            <tr>
                <td class="meta-label">Respond Before</td>
                <td class="meta-value"><?php echo e(\Carbon\Carbon::parse($correspondence->deadline)->format('F d, Y')); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <div class="body">
            <?php echo $correspondence->details ?: '<p style="color:#9ca3af;">Write the correspondence details here...</p>'; ?>

        </div>

        <div class="signature">
            <div>Respectfully,</div>
            <div class="line"></div>
            <div class="sign-name"><?php echo e($correspondence->user ?? 'Authorized Representative'); ?></div>
        </div>
    </div>
</body>
</html><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\correspondence\templates\partial\base-template.blade.php ENDPATH**/ ?>