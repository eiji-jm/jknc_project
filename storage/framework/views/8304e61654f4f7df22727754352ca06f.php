<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Power of Attorney</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { min-height: 100%; }
        body {
            margin: 0;
            color: #111827;
            background: linear-gradient(180deg, #eaf1fb 0%, #f8fafc 100%);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .print-shell {
            margin: 0 auto;
            max-width: 1100px;
            padding: 24px;
        }
        .document-page {
            background: #fff;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }
        .spa-document {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 14px;
            line-height: 1.8;
        }
        .spa-line {
            display: inline-block;
            min-width: 160px;
            border-bottom: 1px solid #111827;
            padding: 0 4px 1px;
            line-height: 1.3;
        }
        .spa-line-lg { min-width: 280px; }
        .spa-sign-line {
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
        }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 10mm; size: A4 portrait; }
            body { background: #fff; }
            .print-shell { margin: 0; max-width: none; padding: 0; }
            .document-page { box-shadow: none; }
        }
    </style>
</head>
<body>
<div class="print-shell">
    <div class="no-print mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Special Power of Attorney</h2>
            <p class="text-sm text-gray-500">Generated from the saved Company BIF details. Review the autofilled information before printing for physical signature.</p>
        </div>

        <div class="flex gap-2">
            <button type="button" onclick="window.location.href='<?php echo e($backUrl); ?>'"
                class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-100">
                Back
            </button>

            <button type="button" onclick="window.print()"
                class="inline-flex h-10 items-center rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                Print / Save as PDF
            </button>
        </div>
    </div>

    <div class="document-page spa-document rounded-[24px] px-10 py-12">
        <div class="text-center">
            <h1 class="text-[24px] font-bold uppercase tracking-wide underline">Special Power of Attorney</h1>
        </div>

        <div class="mt-8">
            <p class="text-center text-[15px] font-semibold uppercase">Know All Men By These Presents:</p>
        </div>

        <div class="mt-8 space-y-5 text-justify">
            <p>
                I,
                <span class="spa-line spa-line-lg"><?php echo e($spaData['principal_name']); ?></span>,
                <?php echo e($spaData['nationality_label']); ?>, of legal age, and resident of
                <span class="spa-line spa-line-lg"><?php echo e($spaData['principal_address']); ?></span>,
                acting for and on behalf of
                <span class="spa-line spa-line-lg"><?php echo e($spaData['business_name'] ?: '____________________________'); ?></span>,
                do hereby name, constitute, and appoint
                <span class="spa-line"><?php echo e($spaData['attorney_name']); ?></span>
                <?php if(filled($spaData['attorney_position'])): ?>
                    , <?php echo e($spaData['attorney_position']); ?>

                <?php endif; ?>
                , of legal age, <?php echo e($spaData['nationality_label']); ?> and resident of
                <span class="spa-line spa-line-lg"><?php echo e($spaData['attorney_address']); ?></span>,
                whose signature appears herein below and which signature I now hereby attest to be true, genuine, and authentic, to be my true and lawful Attorney-in-Fact for me and in my name, place, and stead, to do and perform the following special powers, to wit:
            </p>

            <ol class="list-decimal space-y-2 pl-7">
                <li>To negotiate, sign, execute, and deliver contracts, engagement agreements, memoranda, and all related documents in connection with my engagement with John Kelly &amp; Company (JK&amp;C Inc.);</li>
                <li>To submit, receive, process, and manage all documents, records, applications, and communications required in relation to such engagement;</li>
                <li>To act as my sole and exclusive representative in all dealings, coordination, and communications with John Kelly &amp; Company (JK&amp;C Inc.);</li>
                <li>To receive copies of documents, reports, notices, and deliverables, whether physical or electronic, on my behalf;</li>
                <li>To approve, acknowledge, and accept services rendered, outputs, and deliverables;</li>
                <li>To sign letters, certifications, authorizations, affidavits, and other documents necessary for processing and compliance;</li>
                <li>To represent me before government agencies, financial institutions, and third parties, including but not limited to the SEC, BIR, LGUs, and banks, in relation to services facilitated by John Kelly &amp; Company (JK&amp;C Inc.);</li>
                <li>To perform any and all acts necessary or incidental to carry out the foregoing authority.</li>
            </ol>

            <p>
                I hereby expressly declare that the above-named Attorney-in-Fact is my sole and exclusive authorized representative in all dealings with John Kelly &amp; Company (JK&amp;C Inc.), and that said firm is authorized to rely solely on the acts, instructions, and representations of my Attorney-in-Fact, without obligation to recognize any other person unless I issue a written revocation.
            </p>

            <p>
                Hereby giving and granting unto my said Attorney-in-Fact full power and authority to do and perform any and every act and thing whatsoever requisite, necessary, or proper to be done in and about the premises, as I might or could do if personally and acting in person; and hereby ratifying and confirming all that my said Attorney-in-Fact shall lawfully do and cause to be done under and by virtue of these presents.
            </p>

            <p>
                In witness whereof, I have hereunto set my hand at
                <span class="spa-line"><?php echo e($spaData['signed_place']); ?></span>,
                Philippines, on this
                <span class="spa-line"><?php echo e($spaData['signed_day']); ?></span>
                day of
                <span class="spa-line"><?php echo e($spaData['signed_month']); ?></span>,
                <span class="spa-line"><?php echo e($spaData['signed_year']); ?></span>.
            </p>
        </div>

        <div class="mt-14 grid gap-12 md:grid-cols-2">
            <div class="space-y-3">
                <div class="h-16"></div>
                <div class="spa-sign-line">
                    <div class="font-semibold uppercase"><?php echo e($spaData['principal_name']); ?></div>
                    <div class="text-[13px]">Principal / Guarantor</div>
                    <div class="mt-2 text-[13px]">ID No.: ____________________</div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-center text-[13px] font-semibold">With Conformity:</div>
                <div class="h-12"></div>
                <div class="spa-sign-line">
                    <div class="font-semibold uppercase"><?php echo e($spaData['attorney_name']); ?></div>
                    <div class="text-[13px]">Attorney-in-Fact</div>
                    <div class="mt-2 text-[13px]">ID No.: ____________________</div>
                </div>
            </div>
        </div>

        <div class="mt-14">
            <div class="mb-10 text-center text-[13px] font-semibold uppercase tracking-wide">Signed In The Presence Of</div>
            <div class="grid gap-12 md:grid-cols-2">
                <div class="spa-sign-line text-[13px]">Witness</div>
                <div class="spa-sign-line text-[13px]">Witness</div>
            </div>
        </div>

        <div class="mt-16 space-y-5 border-t border-gray-300 pt-10">
            <div class="text-center text-[18px] font-bold uppercase">Acknowledgement</div>

            <div class="space-y-3 text-justify text-[13px]">
                <p>Republic of the Philippines )</p>
                <p>__________________________ ) S.S.</p>
                <p>
                    Before me, a Notary Public for and in the
                    <span class="spa-line">____________________________</span>,
                    Philippines, this
                    <span class="spa-line">______</span>
                    day of
                    <span class="spa-line">____________</span>,
                    20<span class="spa-line"><?php echo e($spaData['acknowledgement_year_short']); ?></span>,
                    personally appeared the above-named person/s, who has satisfactorily proven to me his/her/their identity through his/her/their identifying documents written below his/her/their name and signature, that they are the same person/s who executed and voluntarily signed the foregoing Special Power of Attorney, duly signed by his/her/their instrumental witnesses at the spaces herein provided which he/she/they acknowledged to me as his/her/their free and voluntary act and deed.
                </p>
                <p>
                    The foregoing instrument relates to a Special Power of Attorney consisting of
                    <span class="spa-line">___</span>
                    pages including the page on which this Acknowledgement is written, and has been signed on the left margin of each and every page by the parties and the witnesses.
                </p>
                <p>
                    Witness my hand and notarial seal, this
                    <span class="spa-line">______</span>
                    day of
                    <span class="spa-line">____________</span>,
                    20<span class="spa-line"><?php echo e($spaData['acknowledgement_year_short']); ?></span>,
                    at
                    <span class="spa-line">____________________________</span>.
                </p>
            </div>

            <div class="mt-10 grid gap-8 md:grid-cols-[1fr_280px]">
                <div class="space-y-2 text-[13px]">
                    <p>Doc No. _______;</p>
                    <p>Page No. _______;</p>
                    <p>Book No. _______;</p>
                    <p>Series of 20<?php echo e($spaData['acknowledgement_year_short']); ?>.</p>
                </div>
                <div class="pt-8">
                    <div class="spa-sign-line">
                        <div class="font-semibold uppercase">Notary Public</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($autoPrint)): ?>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
<?php endif; ?>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\company\requirements\spa-print.blade.php ENDPATH**/ ?>