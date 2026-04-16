<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Policy Preview</title>
    <style>
        @page { margin: 0.5in; }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #2d3748;
            margin: 0;
            padding: 20px;
            line-height: 1.5;
            background-color: #fff;
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
        .policy-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            vertical-align: top;
        }
        .label {
            background-color: #f8fafc;
            font-weight: bold;
            width: 140px;
            color: #4a5568;
        }

        /* --- THE ULTIMATE QUILL TABLE FIX --- */
        .description-content {
            margin-top: 20px;
            font-size: 13px;
            width: 100%;
        }

        /* 1. Force table to fill width and ignore Quill's internal math */
        .description-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 15px 0 !important;
            border: 1px solid #94a3b8 !important;
        }

        /* 2. Strip Quill's hidden column definitions that break layouts */
        .description-content colgroup,
        .description-content col {
            display: none !important;
            width: 0 !important;
            visibility: hidden !important;
        }

        /* 3. Force cells to stay horizontal and wrap text */
        .description-content th,
        .description-content td {
            border: 1px solid #94a3b8 !important;
            padding: 8px !important;
            vertical-align: top !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important; /* FIXES VERTICAL TEXT STACKING */
            min-width: 20px !important;    /* KEEPS EMPTY COLUMNS VISIBLE */
        }

        .description-content th {
            background-color: #f8fafc !important;
            font-weight: bold !important;
        }

        /* 4. Fix paragraphs inside cells */
        .description-content td p {
            margin: 0 !important;
            padding: 0 !important;
            word-break: break-word !important;
        }
    </style>
</head>
<body>

    <div class="letterhead">
        <p class="company-name">John Kelly & Company</p>
        <p style="font-size: 10px; color: #718096; margin:0;">Enterprise Operating System | Corporate Policy</p>
    </div>

    <div class="policy-title">
        {{ $data['policy'] ?: 'NEW POLICY DOCUMENT' }}
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Document Code</td>
            <td>AUTO-GENERATED</td>
            <td class="label">Version</td>
            <td>{{ $data['version'] ?? '1.0' }}</td>
        </tr>
        <tr>
            <td class="label">Effectivity Date</td>
            <td>{{ $data['effectivity_date'] ?: '-' }}</td>
            <td class="label">Classification</td>
            <td>{{ $data['classification'] ?? 'Internal Use' }}</td>
        </tr>
        <tr>
            <td class="label">Prepared By</td>
            <td colspan="3">{{ $data['prepared_by'] ?? 'System Admin' }}</td>
        </tr>
    </table>

    <div class="description-content">
        {{-- Pass raw HTML from Quill to the PDF engine --}}
        {!! $data['description'] ?? '<p style="color:#cbd5e0;">No description provided.</p>' !!}
    </div>

</body>
</html>
