<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page { size: A4; margin: 14mm 16mm 16mm; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: #000;
            font-size: 11.5px;
            line-height: 1.4;
            background: #fff;
        }
        .print-shell {
            margin: 0 auto;
            max-width: 960px;
            padding: 16px;
        }
        .document-page {
            background: #fff;
            padding: 30px 38px;
        }
        .toolbar {
            margin: 0 auto 16px;
            max-width: 960px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .toolbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #3153d4;
            padding: 8px 12px;
            background: #3153d4;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .toolbar-button:hover {
            background: #2745b3;
        }
        .spa-document {
            font-size: 11.5px !important;
            line-height: 1.4 !important;
        }
        .spa-document p,
        .spa-document li {
            page-break-inside: avoid;
        }
        .spa-document ul {
            margin-bottom: 12px !important;
        }
        .spa-document li {
            margin-bottom: 5px !important;
        }
        .spa-document [style*="margin-top: 36px"] {
            margin-top: 22px !important;
        }
        .spa-document [style*="margin-top: 54px"] {
            margin-top: 28px !important;
        }
        .spa-document [style*="margin-top: 56px"] {
            margin-top: 28px !important;
        }
        .spa-document [style*="height: 28px"] {
            height: 14px !important;
        }
        .spa-document [style*="height: 18px"] {
            height: 10px !important;
        }
        .spa-signature-section {
            break-inside: avoid-page;
            page-break-inside: avoid;
        }
        .spa-acknowledgement-section {
            break-before: page;
            page-break-before: always;
        }
        @media print {
            .toolbar {
                display: none !important;
            }
            .print-shell {
                max-width: none;
                padding: 0;
            }
            .document-page {
                padding: 0;
            }
            .spa-document {
                font-size: 11.5px !important;
                line-height: 1.4 !important;
            }
            .spa-acknowledgement-section {
                break-before: page;
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="toolbar-button" onclick="window.history.back()">Back</button>
    </div>
    <div class="print-shell">
        <div class="document-page">
            @include('company.requirements.partials.spa-document', ['doc' => $doc])
        </div>
    </div>

    @if (!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
