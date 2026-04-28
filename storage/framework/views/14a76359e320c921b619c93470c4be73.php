<?php
    $selected = $resolution;
    $clauseText = trim((string) $selected->resolution_body);
    $companyName = strtoupper(config('app.name', 'JOHN KELLY & COMPANY'));
    $meetingDate = optional($selected->date_of_meeting)->format('F d, Y') ?: '________________';
    $notaryYear = optional($selected->notarized_on)->format('Y') ?: now()->year;
    $meetingType = strtolower($selected->type_of_meeting ?: 'special');
    $governingBody = $selected->governing_body ?: 'Board of Directors';
    $resolutionNumber = $selected->resolution_no ?: '25-002';
    $chairman = $selected->chairman ?: '____________________';
    $notarizedAt = $selected->notarized_at ?: '______________';
    $notaryPublic = $selected->notary_public ?: 'Notary Public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolution <?php echo e($selected->resolution_no ?: 'Draft Resolution'); ?></title>
    <style>
        @page {
            size: A4;
            margin: 12mm 12mm 16mm;
        }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: #000;
            font-size: 13px;
            line-height: 1.65;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page {
            box-sizing: border-box;
            width: 100%;
        }
        .brand {
            text-align: center;
            color: #000;
            line-height: 1.2;
        }
        .brand-top {
            font-size: 34px;
        }
        .brand-amp {
            color: #2563eb;
            font-size: 30px;
            font-weight: 700;
            padding: 0 8px;
        }
        .brand-sub {
            margin-top: 6px;
            font-size: 19px;
            font-weight: 600;
        }
        .brand-meta {
            margin-top: 10px;
            font-size: 12px;
        }
        .title {
            margin-top: 34px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .content {
            margin-top: 26px;
            text-align: justify;
        }
        .content p {
            margin: 0 0 18px;
        }
        .signature-block {
            margin-top: 48px;
            text-align: right;
        }
        .signature-line {
            display: inline-block;
            min-width: 240px;
            border-top: 1px solid #000;
            padding-top: 8px;
            text-align: center;
        }
        .notary {
            margin-top: 44px;
            font-size: 12px;
        }
        .notary-meta {
            margin-top: 24px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="brand">
            <div class="brand-top">
                <span>John Kelly</span>
                <span class="brand-amp">&amp;</span>
                <span>Company</span>
            </div>
            <div class="brand-sub">JK&amp;C INC.</div>
            <div class="brand-meta">COMPANY REG. NO.: 2025120230900-02</div>
            <div class="brand-meta">3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE CEBU BUSINESS PARK HIPPODROMO, CEBU CITY (Capital), CEBU, REGION VII (CENTRAL VISAYAS), 6000</div>
        </div>

        <div class="title">Board Resolution No. <?php echo e($resolutionNumber); ?></div>

        <div class="content">
            <p><strong>WHEREAS,</strong> during the <?php echo e($meetingType); ?> meeting of the <?php echo e($governingBody); ?> of <strong><u><?php echo e($companyName); ?></u></strong> held on <strong><u><?php echo e($meetingDate); ?></u></strong>, where a quorum was present and acted all throughout, the body approved the following action:</p>

            <p><?php echo nl2br(e($clauseText ?: ($selected->board_resolution ?: 'No board resolution text has been encoded yet.'))); ?></p>

            <p><strong>WHEREAS RESOLVED;</strong> that the foregoing resolutions are hereby approved and adopted.</p>

            <p><strong>WHEREAS FINALLY RESOLVED,</strong> that the foregoing resolution is valid and existing until withdrawn, revoked, or modified by the Corporation.</p>

            <p><strong>BE IT FURTHER RESOLVED,</strong> that the Corporate Secretary is hereby authorized and directed to include this Resolution in the Company's Minute Book and to notify all concerned parties of the adoption of this Resolution.</p>

            <p><strong><u>FINALLY BE IT FURTHER RESOLVED</u></strong> that the undersigned affirm the foregoing resolution and adopt it on this <strong><u><?php echo e($meetingDate); ?></u></strong>.</p>
        </div>

        <div class="signature-block">
            <div class="signature-line">
                <div style="font-weight:700; text-transform:uppercase;"><?php echo e($chairman); ?></div>
                <div>Chairman</div>
            </div>
        </div>

        <div class="content" style="margin-top: 44px;">
            <p>IN WITNESS WHEREOF, I, <strong><u><?php echo e($chairman); ?></u></strong>, in my capacity as chairman of the board, have signed these presents this ______ day of __________ at <?php echo e($notarizedAt); ?>.</p>
        </div>

        <div class="signature-block">
            <div class="signature-line">
                <div style="font-weight:700; text-transform:uppercase;"><?php echo e($chairman); ?></div>
                <div>Chairman</div>
            </div>
        </div>

        <div class="notary">
            <p><strong>SUBSCRIBED AND SWORN</strong> to before me, a Notary Public for and in <?php echo e($notarizedAt); ?> this day of __________, affiant presented to me __________ issued at __________.</p>

            <div class="signature-block" style="margin-top: 30px;">
                <div class="signature-line">
                    <div style="font-weight:700; text-transform:uppercase;"><?php echo e($notaryPublic); ?></div>
                </div>
            </div>

            <div class="notary-meta">
                <div>Doc. No. <?php echo e($selected->notary_doc_no ?: '_____'); ?>;</div>
                <div>Page No. <?php echo e($selected->notary_page_no ?: '_____'); ?>;</div>
                <div>Book No. <?php echo e($selected->notary_book_no ?: '_____'); ?>;</div>
                <div>Series of <?php echo e($selected->notary_series_no ?: $notaryYear); ?>.</div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\resolutions\pdf.blade.php ENDPATH**/ ?>