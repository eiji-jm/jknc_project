<?php
    $d = $documentData;

    $paragraphs = function ($value) {
        return collect(preg_split("/\r\n|\n|\r/", (string) $value) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();
    };

    $money = fn ($value) => number_format((float) $value, 2);

    $requirements = collect([
        ['label' => 'For Sole Proprietor / Professional / Individual', 'items' => $paragraphs($d['requirements_sole'] ?? '')],
        ['label' => 'For Juridical / Corporation / Partnership', 'items' => $paragraphs($d['requirements_juridical'] ?? '')],
        ['label' => 'Optional / As Applicable', 'items' => $paragraphs($d['requirements_optional'] ?? '')],
    ])->filter(fn ($group) => $group['items']->isNotEmpty())->values();

    $proposalLogo = $logoSrc ?? asset('images/deal-proposal-template-logo.png');

    $toRoman = function (int $number): string {
        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];

        $result = '';
        foreach ($map as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    };

    $sections = [
        ['title' => 'Executive Summary', 'content' => 'executive_summary'],
        ['title' => 'Our Role and Value to You', 'content' => 'role_and_value'],
        ['title' => 'Why John Kelly & Company is the Right Partner', 'content' => 'why_partner'],
    ];

    $totalPages = 8;
    $renderPageFooter = function (int $pageNumber) use ($d, $totalPages) {
        return '
            <div class="proposal-page-footer">
                <div>John Kelly &amp; Company</div>
                <div>'.e($d['company_address'] ?? '').'</div>
                <div>Email: '.e($d['company_email'] ?? '').' &bull; Website: '.e($d['company_website'] ?? '').' &bull; Phone: '.e($d['company_phone'] ?? '').'</div>
                <div>Page '.$pageNumber.' of '.$totalPages.'</div>
            </div>
        ';
    };
?>

<div class="proposal-doc">
    <section class="proposal-page proposal-cover">
        <div class="proposal-cover-logo-wrap">
            <img src="<?php echo e($proposalLogo); ?>" alt="John Kelly & Company" class="proposal-brand-logo">
        </div>

        <div class="proposal-cover-body">
            <div class="proposal-cover-year"><?php echo e($d['year'] ?? now()->format('Y')); ?></div>
            <div class="proposal-cover-title"><?php echo e($d['service_type'] ?? 'BIR Compliance Services'); ?></div>
            <div class="proposal-cover-date"><?php echo e($d['date'] ?? now()->format('F d, Y')); ?></div>

            <div class="proposal-presented-label">Presented For:</div>
            <div class="proposal-presented-name"><?php echo e($d['client_name'] ?? 'Client Name'); ?></div>
            <div class="proposal-presented-name"><?php echo e($d['business_name'] ?? 'Business Name'); ?></div>
            <div class="proposal-presented-location"><?php echo e($d['location'] ?? 'Philippines'); ?></div>
        </div>

        <div class="proposal-cover-footer">
            <div class="proposal-contact-strip">
                <div class="proposal-contact-inline">
                    <span><?php echo e($d['company_phone'] ?? ''); ?></span>
                    <span><?php echo e($d['company_email'] ?? ''); ?></span>
                    <span><?php echo e($d['company_website'] ?? ''); ?></span>
                    <span><?php echo e($d['reference_id'] ?? ''); ?></span>
                    <span>Confidential</span>
                </div>
                <div class="proposal-contact-address"><?php echo e($d['company_address'] ?? ''); ?></div>
            </div>
        </div>
    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <?php $__currentLoopData = collect($sections)->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h2 class="proposal-section-heading">
                <span class="proposal-section-number"><?php echo e($toRoman($index + 1)); ?>.</span>
                <span><?php echo e($section['title']); ?></span>
            </h2>

            <?php $__currentLoopData = ($d[$section['content']] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="proposal-paragraph"><?php echo nl2br(e($item)); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php echo $renderPageFooter(2); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <?php ($thirdSection = $sections[2]); ?>
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(3)); ?>.</span>
            <span><?php echo e($thirdSection['title']); ?></span>
        </h2>

        <?php $__currentLoopData = ($d[$thirdSection['content']] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="proposal-paragraph"><?php echo nl2br(e($item)); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <table class="proposal-service-table">
            <thead>
                <tr>
                    <th class="proposal-service-no">No.</th>
                    <th class="proposal-service-area">Service Area</th>
                    <th class="proposal-service-scope">Scope of Support</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = ($d['service_areas'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceArea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="proposal-service-no"><?php echo e($serviceArea['no'] ?? ''); ?></td>
                        <td class="proposal-service-area-title"><?php echo e($serviceArea['service_area'] ?? ''); ?></td>
                        <td class="proposal-service-scope-list">
                            <ol type="a">
                                <?php $__currentLoopData = ($serviceArea['scope'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($scope); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <?php echo $renderPageFooter(3); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(4)); ?>.</span>
            <span><?php echo e($d['proposal_intro'] ?? 'Our Proposal'); ?></span>
        </h2>
        <p class="proposal-paragraph"><?php echo e($d['our_proposal_text'] ?? ''); ?></p>

        <h3 class="proposal-subheading">Scope of Service / Assistance</h3>
        <?php $__currentLoopData = $paragraphs($d['scope_of_service'] ?? ''); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="proposal-paragraph"><?php echo e($line); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <h3 class="proposal-subheading">What You Will Receive</h3>
        <ul class="proposal-bullet-list">
            <?php $__currentLoopData = $paragraphs($d['what_you_will_receive'] ?? ''); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($line); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <?php if($requirements->isNotEmpty()): ?>
            <h3 class="proposal-subheading">Requirements</h3>
            <p class="proposal-paragraph"><?php echo e($d['requirements_intro'] ?? ''); ?></p>
            <?php $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="proposal-requirement-group">
                    <div class="proposal-requirement-label"><?php echo e($group['label']); ?></div>
                    <ul class="proposal-bullet-list">
                        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($line); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <p class="proposal-note"><?php echo e($d['requirements_note'] ?? ''); ?></p>
        <?php endif; ?>

        <h3 class="proposal-subheading proposal-subheading-blue">Service Fee</h3>
        <table class="proposal-pricing-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount (P)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Regular Price</td><td><?php echo e($money($d['price_regular'] ?? 0)); ?></td></tr>
                <tr><td>Discount</td><td><?php echo e($money($d['price_discount'] ?? 0)); ?></td></tr>
                <tr><td>Subtotal (After Discount)</td><td><?php echo e($money($d['price_subtotal'] ?? 0)); ?></td></tr>
                <tr><td>Tax</td><td><?php echo e($money($d['price_tax'] ?? 0)); ?></td></tr>
                <tr class="is-total"><td>Total</td><td><?php echo e($money($d['price_total'] ?? 0)); ?></td></tr>
                <tr><td>Downpayment</td><td><?php echo e($money($d['price_down'] ?? 0)); ?></td></tr>
                <tr><td>Balance</td><td><?php echo e($money($d['price_balance'] ?? 0)); ?></td></tr>
            </tbody>
        </table>
        <p class="proposal-note"><?php echo e($d['supplemental_fee_note'] ?? ''); ?></p>
        </div>
        <?php echo $renderPageFooter(4); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(5)); ?>.</span>
            <span>Proposal Highlights</span>
        </h2>
        <?php $__currentLoopData = ($d['proposal_highlights'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="proposal-paragraph"><strong><?php echo e($highlight['title'] ?? ''); ?>.</strong> <?php echo e($highlight['body'] ?? ''); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(6)); ?>.</span>
            <span>Our Commitment</span>
        </h2>
        <?php $__currentLoopData = ($d['commitment'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="proposal-paragraph"><?php echo e($item); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(7)); ?>.</span>
            <span>Agreement Inclusions and Exclusions</span>
        </h2>
        <h3 class="proposal-subheading">Agreement Inclusions</h3>
        <ul class="proposal-bullet-list">
            <?php $__currentLoopData = ($d['agreement_inclusions'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($item); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <h3 class="proposal-subheading">Agreement Exclusions</h3>
        <ul class="proposal-bullet-list">
            <?php $__currentLoopData = ($d['agreement_exclusions'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($item); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <h3 class="proposal-subheading">Supplemental Fees</h3>
        <table class="proposal-data-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Description</th>
                    <th>Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = ($d['supplemental_fees'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($fee['service'] ?? ''); ?></td>
                        <td><?php echo e($fee['description'] ?? ''); ?></td>
                        <td><?php echo e($fee['fee'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <?php echo $renderPageFooter(5); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(8)); ?>.</span>
            <span>Terms and Conditions</span>
        </h2>
        <?php $__currentLoopData = ($d['terms_and_conditions'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $termIndex => $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="proposal-term-block">
                <h3 class="proposal-subheading proposal-subheading-tight">
                    <span class="proposal-term-number"><?php echo e($termIndex + 1); ?>.</span>
                    <span><?php echo e($term['title'] ?? ''); ?></span>
                </h3>

                <?php if(!empty($term['intro'])): ?>
                    <p class="proposal-paragraph"><?php echo e($term['intro']); ?></p>
                <?php endif; ?>

                <?php $__currentLoopData = ($term['paragraphs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="proposal-paragraph"><?php echo e($paragraph); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if(!empty($term['items'])): ?>
                    <ol class="proposal-numbered-list">
                        <?php $__currentLoopData = $term['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($item); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                <?php endif; ?>

                <?php if(!empty($term['outro'])): ?>
                    <p class="proposal-paragraph"><?php echo e($term['outro']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php echo $renderPageFooter(6); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(9)); ?>.</span>
            <span>Client Engagement Team</span>
        </h2>
        <p class="proposal-paragraph"><?php echo e($d['engagement_team_intro'] ?? ''); ?></p>
        <table class="proposal-data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Branch</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = ($d['engagement_team'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($member['name'] ?? ''); ?></td>
                        <td><?php echo e($member['designation'] ?? ''); ?></td>
                        <td><?php echo e($member['branch'] ?? ''); ?></td>
                        <td><?php echo e($member['email'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <p class="proposal-end-note">-End-</p>
        <p class="proposal-system-note"><?php echo e($d['system_note'] ?? ''); ?></p>
        </div>
        <?php echo $renderPageFooter(7); ?>

    </section>

    <section class="proposal-page proposal-inner-page">
        <div class="proposal-page-body">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number"><?php echo e($toRoman(10)); ?>.</span>
            <span>Conforme and Acceptance</span>
        </h2>
        <p class="proposal-paragraph">By signing below, the parties acknowledge and accept the terms and conditions outlined in this proposal, which shall constitute a binding agreement.</p>

        <div class="proposal-signature-grid">
            <div class="proposal-signature-block">
                <div class="proposal-signature-label">For the Client</div>
                <div class="proposal-signature-line"><?php echo e($d['client_name'] ?? ''); ?></div>
                <div class="proposal-signature-subline"><?php echo e($d['business_name'] ?? ''); ?></div>
            </div>
            <div class="proposal-signature-block">
                <div class="proposal-signature-label">For John Kelly &amp; Company</div>
                <div class="proposal-signature-line"><?php echo e($d['prepared_by_name'] ?? ''); ?></div>
                <div class="proposal-signature-subline">ID Number: <?php echo e($d['prepared_by_id'] ?? ''); ?></div>
            </div>
        </div>

        <div class="proposal-footer-note">
            <div>John Kelly &amp; Company</div>
            <div><?php echo e($d['company_address'] ?? ''); ?></div>
            <div>Email: <?php echo e($d['company_email'] ?? ''); ?> &bull; Website: <?php echo e($d['company_website'] ?? ''); ?> &bull; Phone: <?php echo e($d['company_phone'] ?? ''); ?></div>
        </div>
        </div>
        <?php echo $renderPageFooter(8); ?>

    </section>
</div>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/deals/proposal/partials/document.blade.php ENDPATH**/ ?>