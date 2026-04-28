<?php
    $selected = $notice;
    $meetingTitle = strtoupper(trim(($selected->type_of_meeting ?: 'Special') . ' ' . ($selected->governing_body ?: 'Board of Directors') . ' Meeting'));
    $noticeDate = optional($selected->date_of_notice)->format('F d, Y') ?: optional($selected->created_at)->format('F d, Y') ?: now()->format('F d, Y');
    $meetingDate = optional($selected->date_of_meeting)->format('F d, Y') ?: '________________';
    $meetingTime = $selected->time_started ? \Carbon\Carbon::parse($selected->time_started)->format('h:i a') : '________________';
    $recipientLabel = match ($selected->governing_body) {
        'Stockholders' => 'ALL STOCKHOLDERS',
        'Joint Stockholders and Board of Directors' => 'ALL STOCKHOLDERS AND DIRECTORS',
        default => 'ALL DIRECTORS',
    };
    $companyName = strtoupper($selected->corporation_name ?: 'JOHN KELLY & COMPANY');
    $companyRegNo = $selected->company_reg_no ?: '2025120230900-02';
    $companyAddress = $selected->company_address ?: '3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE., CEBU BUSINESS PARK HIPPODROMO, CEBU CITY, 6000';
    $meetingTypeLabel = $selected->type_of_meeting ?: 'Special';
    $governingBodyLabel = $selected->governing_body ?: 'Board of Directors';
    $meetingLocation = $selected->location ?: '________________';
    $secretaryName = $selected->secretary ?: 'Corporate Secretary';
    $agendaHtml = $bodyHtml ?: '<p>&nbsp;</p>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice <?php echo e($selected->notice_number ?: 'Draft Notice'); ?></title>
    <style>
        @page {
            size: A4;
            margin: 12mm 12mm 16mm;
        }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: #000;
            font-size: 14px;
            line-height: 1.75;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page {
            box-sizing: border-box;
            width: 100%;
        }
        .center {
            text-align: center;
            line-height: 1.4;
        }
        .title {
            margin-top: 28px;
            text-align: center;
            font-size: 1.05rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .meta {
            margin-top: 34px;
        }
        .meta-row {
            font-weight: 700;
            margin-bottom: 16px;
        }
        .body {
            margin-top: 30px;
            text-align: justify;
        }
        .body p {
            margin: 0 0 18px;
        }
        .agenda {
            margin-top: 16px;
        }
        .agenda ol,
        .agenda ul {
            margin: 12px 0 0 24px;
            padding: 0;
        }
        .agenda li {
            margin: 6px 0;
        }
        .footer {
            margin-top: 64px;
            display: flex;
            align-items: end;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.4;
        }
        .signature {
            margin-top: 48px;
        }
        .signature .name {
            margin-top: 36px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="center">
            <div style="font-size:1.1rem;font-weight:700;text-transform:uppercase;"><?php echo e($companyName); ?></div>
            <div style="font-size:0.95rem;font-weight:700;">COMPANY REG. NO.: <?php echo e($companyRegNo); ?></div>
            <div style="margin-top:4px;font-size:0.95rem;"><?php echo e($companyAddress); ?></div>
        </div>

        <div class="title">Notice and Agenda of the <?php echo e($meetingTitle); ?></div>

        <div class="meta">
            <div class="meta-row">To: <span style="margin-left:12px;"><?php echo e($recipientLabel); ?></span></div>
            <div class="meta-row">Date: <span style="margin-left:12px;"><?php echo e($noticeDate); ?></span></div>
        </div>

        <div class="body">
            <p><strong>NOTICE is hereby given that a <?php echo e($meetingTypeLabel); ?> <?php echo e($governingBodyLabel); ?> Meeting of <?php echo e($companyName); ?> will be held at <?php echo e($meetingLocation); ?> on <?php echo e($meetingDate); ?> at <?php echo e($meetingTime); ?>.</strong></p>
            <div class="agenda">
                <div><strong>Agenda:</strong></div>
                <?php echo $agendaHtml; ?>

            </div>
        </div>

        <div class="signature">
            <div>Very truly yours,</div>
            <div class="name"><?php echo e($secretaryName); ?></div>
            <div>Corporate Secretary</div>
        </div>

        <div class="footer">
            <div>
                <div style="font-weight:700;text-transform:uppercase;">Notice for <?php echo e($meetingTitle); ?></div>
                <div><?php echo e($companyName); ?></div>
                <div>Company Reg. No.: <?php echo e($companyRegNo); ?></div>
                <div><?php echo e($companyAddress); ?></div>
            </div>
            <div style="font-weight:700;">Page 1 of 1</div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\notices\pdf.blade.php ENDPATH**/ ?>