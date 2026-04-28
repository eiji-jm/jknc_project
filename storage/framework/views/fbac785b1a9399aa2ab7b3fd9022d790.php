<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($bif->title ?: 'Business Client Information Form'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { min-height: 100%; }
        body {
            background: linear-gradient(180deg, #eaf1fb 0%, #f8fafc 100%);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .document-page {
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            background: transparent;
        }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 6mm; size: A4 portrait; }
            body { background: #fff; }
            .print-shell { margin: 0; max-width: none; padding: 0; }
            .document-page { box-shadow: none; background: transparent; }
            .bif-print-document {
                max-width: none !important;
                width: 100% !important;
                transform: scale(0.965);
                transform-origin: top center;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="print-shell mx-auto max-w-6xl p-4 md:p-6">
        <div class="no-print mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3">
            <div>
                <h2 class="text-lg font-semibold">Business Information Form</h2>
                <p class="text-sm text-gray-500">Use your browser's print dialog and choose Save as PDF to export this document.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="window.location.href='<?php echo e($backUrl ?? route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id])); ?>'"
                    class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Back
                </button>
                <button type="button" onclick="window.print()" class="inline-flex h-10 items-center rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Print / Save as PDF
                </button>
            </div>
        </div>

        <div class="document-page">
            <?php echo $__env->make('company.bif.partials.document', ['wrapperClass' => 'bif-doc bif-print-document mx-auto w-full max-w-5xl'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1' || <?php echo json_encode(!empty($autoPrint), 15, 512) ?>) {
            window.onload = () => window.print();
        }
    </script>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\bif\print.blade.php ENDPATH**/ ?>