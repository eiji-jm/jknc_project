<?php
    $certificateNo = $certificate->stock_number ?? '-';
    $corporation = strtoupper($certificate->corporation_name ?? 'JOHN KELLY & COMPANY');
    $shareholder = $certificate->stockholder_name ?? '-';
    $shareCount = $certificate->number ?? '-';
    $parValue = $certificate->par_value !== null && $certificate->par_value !== '' ? number_format((float) $certificate->par_value, 2, '.', ',') : '-';
    $amountWords = $certificate->amount_in_words ?? 'Amount in Words';
    $issueDay = optional($certificate->date_issued)->format('d') ?? '-';
    $issueMonthYear = optional($certificate->date_issued)->format('F Y') ?? '-';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Times New Roman", Georgia, serif;
            color: #3f3a22;
            background: #ffffff;
        }
        .page {
            width: 297mm;
            height: 210mm;
            margin: 0 auto;
            padding: 7mm;
            background:
                radial-gradient(circle at center, rgba(190, 168, 94, 0.18) 0, rgba(190, 168, 94, 0.18) 1.2mm, transparent 1.2mm) 0 0 / 8mm 8mm,
                repeating-linear-gradient(90deg, rgba(124, 115, 74, 0.07) 0, rgba(124, 115, 74, 0.07) 0.6mm, transparent 0.6mm, transparent 2.2mm),
                linear-gradient(180deg, #f6f0d9 0%, #efe7c7 52%, #f8f4e4 100%);
        }
        .frame {
            width: 100%;
            height: 100%;
            border: 6mm solid #7d8e55;
            padding: 4mm;
            background:
                linear-gradient(135deg, rgba(255,255,255,0.18), rgba(255,255,255,0)) border-box,
                linear-gradient(180deg, #8da163 0%, #5d7340 100%) border-box;
            position: relative;
        }
        .frame-inner {
            width: 100%;
            height: 100%;
            border: 1.8mm solid #e1d6aa;
            padding: 4mm;
            background:
                repeating-radial-gradient(circle at center, rgba(152, 136, 66, 0.11) 0, rgba(152, 136, 66, 0.11) 0.55mm, transparent 0.55mm, transparent 2.9mm),
                linear-gradient(180deg, #fdfaf0 0%, #f4ecd2 42%, #faf5e2 100%);
            position: relative;
        }
        .certificate {
            width: 100%;
            height: 100%;
            border: 1mm solid #a8a189;
            padding: 9mm 10mm 8mm;
            background:
                repeating-linear-gradient(90deg, rgba(126, 113, 52, 0.045) 0, rgba(126, 113, 52, 0.045) 0.7mm, transparent 0.7mm, transparent 2.3mm),
                linear-gradient(180deg, rgba(255,255,255,0.82), rgba(248,244,228,0.92));
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .guilloche {
            position: absolute;
            inset: 5mm;
            border: 0.6mm solid rgba(120, 109, 65, 0.5);
            pointer-events: none;
        }
        .top-grid {
            display: grid;
            grid-template-columns: 28mm 1fr 28mm;
            gap: 8mm;
            align-items: end;
        }
        .top-box,
        .top-oval,
        .ribbon,
        .bottom-plate {
            border: 0.8mm solid #87806e;
            background: linear-gradient(180deg, #d5d5d3 0%, #9fa3a6 100%);
            box-shadow: inset 0 0 0 0.6mm rgba(255,255,255,0.25);
        }
        .top-box {
            min-height: 22mm;
            padding: 3mm 2mm;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .top-box strong {
            display: block;
            margin-top: 2mm;
            font-size: 13px;
            letter-spacing: 0.04em;
        }
        .top-oval {
            min-height: 22mm;
            border-radius: 999px;
            background: linear-gradient(180deg, #ffffff 0%, #eff1f4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 10mm;
        }
        .top-oval span {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }
        .ribbon {
            margin: 7mm 8mm 8mm;
            min-height: 14mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 10mm;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.22em;
        }
        .certifies {
            margin-top: 1mm;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }
        .content {
            flex: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) 88mm;
            gap: 10mm;
            margin-top: 6mm;
            min-height: 0;
        }
        .body-copy {
            font-size: 13px;
            line-height: 1.85;
            font-style: italic;
            text-align: justify;
        }
        .body-copy strong {
            font-style: normal;
        }
        .fill {
            display: inline-block;
            min-width: 24mm;
            padding: 0 1.5mm 1mm;
            border-bottom: 0.45mm solid rgba(88, 79, 44, 0.5);
            font-style: normal;
            font-weight: 700;
            text-align: center;
        }
        .left-column {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .seal {
            width: 26mm;
            height: 26mm;
            border-radius: 50%;
            background:
                radial-gradient(circle at center, rgba(255, 233, 148, 0.9) 0%, rgba(180, 146, 37, 0.95) 58%, rgba(127, 95, 17, 0.98) 100%);
            box-shadow:
                inset 0 0 0 1.5mm rgba(158, 126, 22, 0.46),
                0 0 0 0.8mm rgba(157, 132, 43, 0.5);
        }
        .seal:before,
        .seal:after {
            content: "";
            position: absolute;
            inset: 4mm;
            border-radius: 50%;
            border: 0.5mm dashed rgba(255, 244, 182, 0.55);
        }
        .witness {
            margin-top: 8mm;
            font-size: 13px;
            line-height: 1.8;
            font-style: italic;
        }
        .witness strong {
            font-style: normal;
        }
        .side-column {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 0;
        }
        .side-panel {
            border: 0.5mm solid rgba(120, 109, 65, 0.4);
            background: rgba(255, 251, 238, 0.82);
            padding: 7mm 6mm;
        }
        .seal-wrap {
            display: flex;
            align-items: center;
            gap: 6mm;
            margin-top: 8mm;
        }
        .seal-copy {
            font-size: 11px;
            line-height: 1.7;
            font-style: italic;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8mm;
            margin-top: 10mm;
        }
        .signature {
            text-align: center;
            font-size: 12px;
        }
        .signature-line {
            border-top: 0.45mm solid rgba(78, 68, 42, 0.56);
            padding-top: 2.5mm;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .signature-role {
            margin-top: 1.5mm;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .footer-row {
            display: grid;
            grid-template-columns: 1fr 28mm 22mm;
            gap: 5mm;
            align-items: end;
            margin-top: 7mm;
        }
        .bottom-plate {
            min-height: 11mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5mm;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            text-align: center;
        }
        .bottom-plate.text-wrap {
            font-size: 7px;
            letter-spacing: 0.06em;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="frame">
            <div class="frame-inner">
                <div class="certificate">
                    <div class="guilloche"></div>

                    <div class="top-grid">
                        <div class="top-box">
                            Number
                            <strong><?php echo e($certificateNo); ?></strong>
                        </div>
                        <div class="top-oval">
                            <span><?php echo e($corporation); ?></span>
                        </div>
                        <div class="top-box">
                            No. Shares
                            <strong><?php echo e($shareCount); ?></strong>
                        </div>
                    </div>

                    <div class="ribbon">Stock Certificate</div>

                    <div class="content">
                        <div class="left-column">
                            <div class="certifies">This Certifies that</div>

                            <div class="body-copy">
                                <span class="fill"><?php echo e($shareholder); ?></span>
                                is the owner of
                                <span class="fill"><?php echo e($shareCount); ?></span>
                                shares of the Capital Stock of
                                <span class="fill"><?php echo e($corporation); ?></span>
                                transferable only on the books of the Corporation by the holder hereof in person or by Attorney upon surrender of this certificate properly endorsed.
                            </div>

                            <div class="witness">
                                <strong>In Witness Whereof,</strong> the said Corporation has caused this certificate to be signed by its duly authorized officers and to be sealed this
                                <span class="fill"><?php echo e($issueDay); ?></span>
                                day of
                                <span class="fill"><?php echo e($issueMonthYear); ?></span>.
                            </div>

                            <div class="seal-wrap">
                                <div class="seal"></div>
                                <div class="seal-copy">
                                    Corporate seal affixed for authentication of this stock certificate.
                                </div>
                            </div>
                        </div>

                        <div class="side-column">
                            <div class="side-panel">
                                <div class="body-copy" style="font-size:12px; line-height:1.7; text-align:left;">
                                    Certificate No.
                                    <span class="fill" style="min-width:22mm;"><?php echo e($certificateNo); ?></span><br>
                                    Par Value
                                    <span class="fill" style="min-width:20mm;"><?php echo e($parValue); ?></span><br>
                                    Amount
                                    <span class="fill" style="min-width:28mm;"><?php echo e($amountWords); ?></span>
                                </div>
                            </div>

                            <div class="signatures">
                                <div class="signature">
                                    <div class="signature-line"><?php echo e($certificate->president ?? '-'); ?></div>
                                    <div class="signature-role">President</div>
                                </div>
                                <div class="signature">
                                    <div class="signature-line"><?php echo e($certificate->corporate_secretary ?? '-'); ?></div>
                                    <div class="signature-role">Corporate Secretary</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-row">
                        <div class="bottom-plate text-wrap"><?php echo e($amountWords); ?></div>
                        <div class="bottom-plate"><?php echo e($parValue); ?></div>
                        <div class="bottom-plate">Each</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\corporate\stock-transfer-book\certificate-pdf.blade.php ENDPATH**/ ?>