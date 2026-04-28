<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page { size: A4; margin: 16mm; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: #000;
            font-size: 13px;
            line-height: 1.6;
            background: #fff;
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; }
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
        }
        @media print {
            .toolbar { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="toolbar-button" onclick="window.history.back()">Back</button>
    </div>

    <?php echo $__env->make('company.requirements.partials.ubo-declaration-document', ['doc' => $doc], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(!empty($autoPrint)): ?>
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\requirements\ubo-declaration-pdf.blade.php ENDPATH**/ ?>