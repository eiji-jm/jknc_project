<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title); ?> - Correspondence</title>
    <style>
        @page {
            margin: 18mm 18mm 20mm 18mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.75;
        }

        .page {
            width: 100%;
        }

        .header {
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 18px;
            margin-bottom: 28px;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 58%;
        }

        .header-right {
            float: right;
            width: 38%;
            text-align: right;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.04em;
            margin: 0 0 4px 0;
        }

        .subhead {
            font-size: 11px;
            color: #6b7280;
            margin: 0;
        }

        .title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.18em;
            margin: 0 0 28px 0;
        }

        .meta {
            margin-bottom: 28px;
        }

        .meta-row {
            width: 100%;
            margin-bottom: 10px;
        }

        .meta-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: top;
        }

        .meta-value {
            display: inline-block;
            width: calc(100% - 130px);
            border-bottom: 1px dotted #d1d5db;
            padding-bottom: 3px;
            vertical-align: top;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
        }

        .body-content,
        .body-content * {
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: normal;
            white-space: normal;
        }

        .body-content {
            min-height: 300px;
            font-size: 12px;
            line-height: 1.9;
        }

        .body-content p {
            margin: 0 0 12px 0;
            line-height: 1.9;
        }

        .body-content ul,
        .body-content ol {
            margin: 0 0 12px 20px;
            padding-left: 18px;
        }

        .body-content li {
            margin-bottom: 5px;
            line-height: 1.8;
        }

        .body-content img,
        .body-content video,
        .body-content iframe,
        .body-content table {
            max-width: 100% !important;
        }

        .body-content table {
            width: 100% !important;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .body-content td,
        .body-content th {
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
        }

        .signature {
            margin-top: 60px;
        }

        .signature-line {
            width: 220px;
            border-bottom: 1px solid #6b7280;
            margin-top: 42px;
            margin-bottom: 6px;
        }

        .signature-name {
            font-weight: bold;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header clearfix">
            <div class="header-left">
                <p class="company-name">JOHN KELLY &amp; COMPANY</p>
                <p class="subhead">Correspondence Preview</p>
            </div>

            <div class="header-right">
                <div>Ref No:
                    <strong>
                        <?php echo e($correspondence->id ? 'COR-' . str_pad($correspondence->id, 5, '0', STR_PAD_LEFT) : 'AUTO-INCREMENT'); ?>

                    </strong>
                </div>
                <div>Date:
                    <strong><?php echo e(optional($correspondence->date)->format('Y-m-d') ?? now()->format('Y-m-d')); ?></strong>
                </div>
                <div>Type:
                    <strong><?php echo e($title); ?></strong>
                </div>
            </div>
        </div>

        <div class="title"><?php echo e(strtoupper($title)); ?></div>

        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">TIN</span>
                <span class="meta-value"><?php echo e($correspondence->tin ?: '______________________________'); ?></span>
            </div>

            <div class="meta-row">
                <span class="meta-label"><?php echo e($correspondence->sender_type ?: 'From'); ?></span>
                <span class="meta-value"><?php echo e($correspondence->sender ?: '______________________________'); ?></span>
            </div>

            <div class="meta-row">
                <span class="meta-label">Department</span>
                <span class="meta-value"><?php echo e($correspondence->department ?: '______________________________'); ?></span>
            </div>

            <div class="meta-row">
                <span class="meta-label">Subject</span>
                <span class="meta-value"><strong><?php echo e($correspondence->subject ?: '______________________________'); ?></strong></span>
            </div>

            <div class="meta-row">
                <span class="meta-label">Sent Via</span>
                <span class="meta-value"><?php echo e($correspondence->sent_via ?: '______________________________'); ?></span>
            </div>

            <?php if(!empty($correspondence->deadline)): ?>
                <div class="meta-row">
                    <span class="meta-label">Respond Before</span>
                    <span class="meta-value">
                        <?php echo e(\Carbon\Carbon::parse($correspondence->deadline)->format('Y-m-d')); ?>

                    </span>
                </div>
            <?php endif; ?>
        </div>

        <div class="body-content">
            <?php echo !empty(trim(strip_tags($correspondence->details ?? '')))
                ? $correspondence->details
                : '<p style="color:#9ca3af;">Write the formal communication here...</p>'; ?>

        </div>

        <div class="signature">
            <div>Respectfully,</div>
            <div class="signature-line"></div>
            <div class="signature-name"><?php echo e($correspondence->user ?? 'System'); ?></div>
        </div>
    </div>
</body>
</html><?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\correspondence\templates\base-template.blade.php ENDPATH**/ ?>