<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transmittal Delivery</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Good day,</p>

    <p>Please see attached files for the approved transmittal.</p>

    <p>
        <strong>Transmittal No:</strong> <?php echo e($transmittal->transmittal_no ?? 'N/A'); ?><br>
        <strong>Mode:</strong> <?php echo e($transmittal->mode ?? 'N/A'); ?><br>
        <strong>Date:</strong> <?php echo e($transmittal->transmittal_date ?? 'N/A'); ?>

    </p>

    <p>The email includes the following attachments:</p>
    <ul>
        <li>Transmittal Form PDF</li>
        <li>Receipt PDF</li>
        <li>Uploaded item attachments</li>
    </ul>

    <p>Thank you.</p>
</body>
</html><?php /**PATH C:\Users\dimpa\Herd\jknc_project\resources\views/emails/transmittal-delivery.blade.php ENDPATH**/ ?>