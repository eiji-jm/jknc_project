<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 16mm; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; color: #000; font-size: 13px; line-height: 1.6; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; }
    </style>
</head>
<body>
    <?php echo $__env->make('company.requirements.partials.secretary-certificate-document', ['doc' => $doc], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\requirements\secretary-certificate-pdf.blade.php ENDPATH**/ ?>