<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Policy Preview</title>
    <style>
        @page {
            margin: 0.5in;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #2d3748;
            margin: 0;
            padding: 20px;
            line-height: 1.5;
            background: #ffffff;
            font-size: 12px;
        }

        .letterhead {
            border-bottom: 2px solid #2b6cb0;
            padding-bottom: 10px;
            margin-bottom: 25px;
            text-align: right;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2b6cb0;
            margin: 0;
        }

        .company-subtitle {
            font-size: 10px;
            color: #718096;
            margin: 2px 0 0;
        }

        .policy-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            color: #2d3748;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            table-layout: fixed;
        }

        .info-table td {
            padding: 8px 12px;
            border: 1px solid #cbd5e0;
            font-size: 11px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .label {
            background-color: #f8fafc;
            font-weight: bold;
            width: 140px;
            color: #4a5568;
        }

        .description-content {
            margin-top: 20px;
            width: 100%;
            font-size: 12px;
        }

        .description-content p {
            margin: 0 0 8px 0;
        }

        .description-content ul,
        .description-content ol {
            margin: 0 0 10px 20px;
            padding: 0;
        }

        .description-content li {
            margin-bottom: 4px;
        }

        .description-content h1,
        .description-content h2,
        .description-content h3,
        .description-content h4,
        .description-content h5,
        .description-content h6 {
            margin: 12px 0 8px 0;
            line-height: 1.3;
        }

        .description-content strong {
            font-weight: bold;
        }

        .description-content em {
            font-style: italic;
        }

        .description-content u {
            text-decoration: underline;
        }

        .description-content blockquote {
            border-left: 3px solid #cbd5e0;
            padding-left: 10px;
            margin: 10px 0;
            color: #4a5568;
        }

        .description-content hr {
            border: none;
            border-top: 1px solid #cbd5e0;
            margin: 12px 0;
        }

        .description-content img {
            max-width: 100% !important;
            height: auto !important;
        }

        /* TABLE FIX FOR DOMPDF */
        .description-content table {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 12px 0 !important;
            border: 1px solid #94a3b8 !important;
            page-break-inside: auto;
        }

        .description-content thead,
        .description-content tbody,
        .description-content tfoot {
            width: 100% !important;
        }

        .description-content tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .description-content th,
        .description-content td {
            border: 1px solid #94a3b8 !important;
            padding: 8px !important;
            vertical-align: top !important;
            text-align: left !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            min-height: 24px !important;
        }

        .description-content th {
            background: #f8fafc !important;
            font-weight: bold !important;
        }

        .description-content colgroup,
        .description-content col {
            display: none !important;
            width: auto !important;
        }

        .description-content td p,
        .description-content th p,
        .description-content td div,
        .description-content th div,
        .description-content td span,
        .description-content th span {
            margin: 0 !important;
            padding: 0 !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        .description-content td:empty::before,
        .description-content th:empty::before {
            content: " ";
            white-space: pre;
        }
    </style>
</head>
<body>

    <div class="letterhead">
        <p class="company-name">John Kelly & Company</p>
        <p class="company-subtitle">Enterprise Operating System | Corporate Policy</p>
    </div>

    <div class="policy-title">
        <?php echo e($data['policy'] ?: 'NEW POLICY DOCUMENT'); ?>

    </div>

    <table class="info-table">
        <tr>
            <td class="label">Document Code</td>
            <td>AUTO-GENERATED</td>
            <td class="label">Version</td>
            <td><?php echo e($data['version'] ?? '1.0'); ?></td>
        </tr>
        <tr>
            <td class="label">Effectivity Date</td>
            <td><?php echo e($data['effectivity_date'] ?: '-'); ?></td>
            <td class="label">Classification</td>
            <td><?php echo e($data['classification'] ?? 'Internal Use'); ?></td>
        </tr>
        <tr>
            <td class="label">Prepared By</td>
            <td colspan="3"><?php echo e($data['prepared_by'] ?? 'System Admin'); ?></td>
        </tr>
        <tr>
            <td class="label">Reviewed By</td>
            <td><?php echo e($data['reviewed_by'] ?: '-'); ?></td>
            <td class="label">Approved By</td>
            <td><?php echo e($data['approved_by'] ?: '-'); ?></td>
        </tr>
    </table>

    <div class="description-content">
        <?php echo $data['description'] ?? '<p style="color:#cbd5e0;">No description provided.</p>'; ?>

    </div>

</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\policies\pdf_preview.blade.php ENDPATH**/ ?>