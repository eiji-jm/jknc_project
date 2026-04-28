<?php
    $resolution = $certificate->resolution;
    $minute = $certificate->minute;
    $companyName = 'JK&C INC.';
    $meetingDate = optional($certificate->date_of_meeting)->format('F d, Y') ?: '________________';
    $issuedDate = optional($certificate->date_issued)->format('F d, Y') ?: '________________';
    $companyAddress = '3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE. CEBU BUSINESS PARK HIPPODROMO, CEBU CITY (Capital), CEBU, REGION VII (CENTRAL VISAYAS), 6000';
    $defaultSecretary = 'MA. LOURDES T. MATA';
    $defaultTin = '903-658-744-000';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4; margin: 15mm 16mm 18mm; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; color: #000; font-size: 13px; line-height: 1.65; }
        .title { text-align: center; font-size: 20px; font-weight: 700; margin: 22px 0 28px; }
        .content p { margin: 0 0 16px; text-align: justify; }
        .resolution-title { text-align: center; font-weight: 700; text-transform: uppercase; margin: 26px 0 14px; }
        .signature { margin-top: 42px; text-align: right; }
        .signature-line { display: inline-block; min-width: 250px; border-top: 1px solid #000; padding-top: 8px; text-align: center; }
        .meta { margin-top: 36px; font-size: 12px; line-height: 1.5; }
    </style>
</head>
<body>
    <div>Republic of the Philippines) City Of</div>
    <div>________________) S.S.</div>

    <div class="title">SECRETARY'S CERTIFICATE</div>

    <div class="content">
        <p>I, <strong><?php echo e($certificate->secretary ?: $defaultSecretary); ?></strong>, of legal age, single, Filipino and with residence at <strong>PEARL ST. STA. TERESITA VILL. TISA, CEBU CITY</strong>, depose under oath and hereby state:</p>
        <p>That, I am the incumbent Corporate Secretary of <strong><?php echo e($companyName); ?></strong>, a corporation duly organized and existing under the laws of the Republic of the Philippines, with principal office at <strong><?php echo e($companyAddress); ?></strong>.</p>
        <p>That, as Corporate Secretary, I have access to the corporate records of <strong><?php echo e($companyName); ?></strong>.</p>
        <p>That, per corporate records, at the <?php echo e($certificate->type_of_meeting ?: 'Special'); ?> Meeting of the <?php echo e($certificate->governing_body ?: 'Board of Directors'); ?> of the Corporation held on <strong><?php echo e($meetingDate); ?></strong>, and recorded under Minutes Ref. <strong><?php echo e($certificate->minutes_ref ?: ($minute?->minutes_ref ?: '________________')); ?></strong>, the following corporate action was duly approved and recorded in the Minute Book, a legal quorum being present and voting, viz:</p>

        <div class="resolution-title"><?php echo e($certificate->resolution_no ? 'BOARD RESOLUTION NO. ' . $certificate->resolution_no : 'CERTIFIED MINUTES EXTRACT'); ?></div>
        <p><strong><?php echo e($certificate->purpose ?: 'Corporate Purpose'); ?></strong></p>
        <p><?php echo $certificate->resolution_body ?: nl2br(e($resolution?->resolution_body ?: ('Certified from Minutes Ref. ' . ($certificate->minutes_ref ?: ($minute?->minutes_ref ?: '________________')) . '.'))); ?></p>

        <p>That, the foregoing resolution shall be in full force and effect unless revoked by the Board of Directors. Moreover, the foregoing resolution is in accordance and does not in any way contravene any provision of the Articles of Incorporation or By-Laws of the Corporation.</p>
        <p>WITNESS MY HAND this ________ of ___________, <?php echo e(optional($certificate->date_issued)->format('Y') ?: now()->year); ?> at <?php echo e($resolution?->notarized_at ?: 'Cebu City'); ?>, Cebu, Philippines.</p>
    </div>

    <div class="signature">
        <div class="signature-line">
            <div><strong><?php echo e($certificate->secretary ?: $defaultSecretary); ?></strong></div>
            <div>Corporate Secretary</div>
            <div style="font-size:11px;">TIN <?php echo e($defaultTin); ?></div>
        </div>
    </div>

    <div class="meta">
        <p>SUBSCRIBED AND SWORN to before me on the date and place above-mentioned, affiant exhibiting his/her TIN issued on <strong><?php echo e($issuedDate); ?></strong>, at Cebu City, Philippines.</p>
        <div style="text-align:right; margin-top: 32px;">
            <div class="signature-line">
                <div><?php echo e($certificate->notary_public ?: 'Notary Public'); ?></div>
            </div>
        </div>
        <div style="margin-top: 22px;">
            <div>Doc. No. <?php echo e($certificate->notary_doc_no ?: '_____'); ?>;</div>
            <div>Page No. <?php echo e($certificate->notary_page_no ?: '_____'); ?>;</div>
            <div>Book No. <?php echo e($certificate->notary_book_no ?: '_____'); ?>;</div>
            <div>Series of <?php echo e($certificate->notary_series_no ?: now()->year); ?>.</div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\secretary-certificates\pdf.blade.php ENDPATH**/ ?>