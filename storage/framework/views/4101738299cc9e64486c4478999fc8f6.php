<?php
    $formDate = optional($rsat?->form_date ?? $rsat?->created_at)->format('m/d/Y');
    $dateStarted = optional($rsat?->date_started)->format('m/d/Y');
    $dateCompleted = optional($rsat?->date_completed)->format('m/d/Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RSAT Form</title>
    <style>
        @page { size: A4 landscape; margin: 6mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 8px; margin: 0; }
        .sheet { border: 2px solid #163b7a; }
        .header { padding: 8px 10px; }
        .title { text-align: right; font-family: "Times New Roman", serif; font-weight: 700; font-size: 18px; line-height: 1.05; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .grid td { padding: 2px 4px; vertical-align: top; }
        .meta-label { width: 110px; font-size: 7px; text-transform: uppercase; }
        .meta-value { border-bottom: 1px solid #111827; min-height: 10px; }
        .section-title { background: #163b7a; color: #fff; text-align: center; padding: 4px; font-family: "Times New Roman", serif; font-weight: 700; font-size: 12px; }
        .matrix { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .matrix th, .matrix td { border: 1px solid #111827; padding: 4px; vertical-align: top; }
        .matrix th { background: #eef4ff; font-size: 7px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div class="title">REGULAR SERVICE<br>ACTIVITY TRACKER (RSAT)</div>
            <table class="grid">
                <tr><td class="meta-label">Client Name:</td><td class="meta-value"><?php echo e(trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))); ?></td><td class="meta-label">Date Created:</td><td class="meta-value"><?php echo e($formDate); ?></td></tr>
                <tr><td class="meta-label">Business Name:</td><td class="meta-value"><?php echo e($regular->business_name ?: ''); ?></td><td class="meta-label">Engagement Type:</td><td class="meta-value"><?php echo e($regular->engagement_type ?: ''); ?></td></tr>
                <tr><td class="meta-label">Condeal Ref No.:</td><td class="meta-value"><?php echo e($regular->deal?->deal_code ?: ''); ?></td><td class="meta-label">Services:</td><td class="meta-value"><?php echo e($regular->services ?: ''); ?></td></tr>
                <tr><td class="meta-label">Service Area:</td><td class="meta-value"><?php echo e($regular->service_area ?: ''); ?></td><td class="meta-label">Date Started:</td><td class="meta-value"><?php echo e($dateStarted); ?></td></tr>
                <tr><td class="meta-label">Product:</td><td class="meta-value"><?php echo e($regular->products ?: ''); ?></td><td class="meta-label">Date Completed:</td><td class="meta-value"><?php echo e($dateCompleted); ?></td></tr>
            </table>
        </div>

        <table class="matrix">
            <thead>
                <tr>
                    <th style="width:6%;">Item #</th>
                    <th style="width:18%;">Service</th>
                    <th style="width:28%;">Activity / Output</th>
                    <th style="width:16%;">Frequency</th>
                    <th style="width:16%;">Reminder Lead Time</th>
                    <th style="width:16%;">Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rsatRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($item['purpose'] ?? ''); ?></td>
                        <td><?php echo e($item['requirement'] ?? ''); ?></td>
                        <td><?php echo e($item['notes'] ?? ''); ?></td>
                        <td><?php echo e($item['timeline'] ?? ''); ?></td>
                        <td><?php echo e($item['submitted_to'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="section-title">Internal Approval</div>
        <table class="matrix">
            <thead>
                <tr>
                    <th>Requirement</th>
                    <th>Responsible Person</th>
                    <th>Name and Signature</th>
                    <th>Date &amp; Time Done</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rsatApprovalSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item['requirement'] ?? ''); ?></td>
                        <td><?php echo e($item['responsible_person'] ?? ''); ?></td>
                        <td><?php echo e($item['name_and_signature'] ?? ''); ?></td>
                        <td><?php echo e($item['date_time_done'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\regular\pdf\rsat.blade.php ENDPATH**/ ?>