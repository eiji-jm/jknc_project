@php
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

    $sections = [
        ['title' => 'Executive Summary', 'content' => 'executive_summary'],
        ['title' => 'Our Role and Value', 'content' => 'role_and_value'],
        ['title' => 'Why John Kelly & Company is the Right Partner', 'content' => 'why_partner'],
    ];
@endphp

<div class="proposal-doc">
    <section class="proposal-page proposal-cover">
        <div class="proposal-cover-logo-wrap">
            <img src="{{ $proposalLogo }}" alt="John Kelly & Company" class="proposal-brand-logo">
        </div>

        <div class="proposal-cover-body">
            <div class="proposal-cover-year">{{ $d['year'] ?? now()->format('Y') }}</div>
            <div class="proposal-cover-title">{{ $d['service_type'] ?? 'BIR Compliance Services' }}</div>
            <div class="proposal-cover-date">{{ $d['date'] ?? now()->format('F d, Y') }}</div>

            <div class="proposal-presented-label">Presented For:</div>
            <div class="proposal-presented-name">{{ $d['client_name'] ?? 'Client Name' }}</div>
            <div class="proposal-presented-name">{{ $d['business_name'] ?? 'Business Name' }}</div>
            <div class="proposal-presented-location">{{ $d['location'] ?? 'Philippines' }}</div>
        </div>
    </section>

    <section class="proposal-page">
        <div class="proposal-contact-strip">
            <table class="proposal-contact-table">
                <tr>
                    <td>{{ $d['company_phone'] ?? '' }}</td>
                    <td>{{ $d['company_email'] ?? '' }}</td>
                    <td>{{ $d['company_website'] ?? '' }}</td>
                    <td>{{ $d['reference_id'] ?? '' }}</td>
                    <td>Confidential</td>
                </tr>
            </table>
            <div class="proposal-contact-address">{{ $d['company_address'] ?? '' }}</div>
        </div>

        @foreach ($sections as $index => $section)
            <h2 class="proposal-section-heading">
                <span class="proposal-section-number">{{ $index + 1 }}.</span>
                <span>{{ $section['title'] }}</span>
            </h2>

            @foreach (($d[$section['content']] ?? []) as $item)
                <p class="proposal-paragraph">{!! nl2br(e($item)) !!}</p>
            @endforeach
        @endforeach

        <table class="proposal-service-table">
            <thead>
                <tr>
                    <th class="proposal-service-no">No.</th>
                    <th class="proposal-service-area">Service Area</th>
                    <th class="proposal-service-scope">Scope of Support</th>
                </tr>
            </thead>
            <tbody>
                @foreach (($d['service_areas'] ?? []) as $serviceArea)
                    <tr>
                        <td class="proposal-service-no">{{ $serviceArea['no'] ?? '' }}</td>
                        <td class="proposal-service-area-title">{{ $serviceArea['service_area'] ?? '' }}</td>
                        <td class="proposal-service-scope-list">
                            <ol type="a">
                                @foreach (($serviceArea['scope'] ?? []) as $scope)
                                    <li>{{ $scope }}</li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="proposal-page">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">4.</span>
            <span>{{ $d['proposal_intro'] ?? 'Our Proposal' }}</span>
        </h2>
        <p class="proposal-paragraph">{{ $d['our_proposal_text'] ?? '' }}</p>

        <h3 class="proposal-subheading">Scope of Service / Assistance</h3>
        @foreach ($paragraphs($d['scope_of_service'] ?? '') as $line)
            <p class="proposal-paragraph">{{ $line }}</p>
        @endforeach

        <h3 class="proposal-subheading">What You Will Receive</h3>
        <ul class="proposal-bullet-list">
            @foreach ($paragraphs($d['what_you_will_receive'] ?? '') as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>

        @if ($requirements->isNotEmpty())
            <h3 class="proposal-subheading">Requirements</h3>
            <p class="proposal-paragraph">{{ $d['requirements_intro'] ?? '' }}</p>
            @foreach ($requirements as $group)
                <div class="proposal-requirement-group">
                    <div class="proposal-requirement-label">{{ $group['label'] }}</div>
                    <ul class="proposal-bullet-list">
                        @foreach ($group['items'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            <p class="proposal-note">{{ $d['requirements_note'] ?? '' }}</p>
        @endif

        <h3 class="proposal-subheading proposal-subheading-blue">Service Fee</h3>
        <table class="proposal-pricing-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount (P)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Regular Price</td><td>{{ $money($d['price_regular'] ?? 0) }}</td></tr>
                <tr><td>Discount</td><td>{{ $money($d['price_discount'] ?? 0) }}</td></tr>
                <tr><td>Subtotal (After Discount)</td><td>{{ $money($d['price_subtotal'] ?? 0) }}</td></tr>
                <tr><td>Tax</td><td>{{ $money($d['price_tax'] ?? 0) }}</td></tr>
                <tr class="is-total"><td>Total</td><td>{{ $money($d['price_total'] ?? 0) }}</td></tr>
                <tr><td>Downpayment</td><td>{{ $money($d['price_down'] ?? 0) }}</td></tr>
                <tr><td>Balance</td><td>{{ $money($d['price_balance'] ?? 0) }}</td></tr>
            </tbody>
        </table>
        <p class="proposal-note">{{ $d['supplemental_fee_note'] ?? '' }}</p>
    </section>

    <section class="proposal-page">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">5.</span>
            <span>Proposal Highlights</span>
        </h2>
        @foreach (($d['proposal_highlights'] ?? []) as $highlight)
            <p class="proposal-paragraph"><strong>{{ $highlight['title'] ?? '' }}.</strong> {{ $highlight['body'] ?? '' }}</p>
        @endforeach

        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">6.</span>
            <span>Our Commitment</span>
        </h2>
        @foreach (($d['commitment'] ?? []) as $item)
            <p class="proposal-paragraph">{{ $item }}</p>
        @endforeach

        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">7.</span>
            <span>Agreement Inclusions and Exclusions</span>
        </h2>
        <h3 class="proposal-subheading">Agreement Inclusions</h3>
        <ul class="proposal-bullet-list">
            @foreach (($d['agreement_inclusions'] ?? []) as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>

        <h3 class="proposal-subheading">Agreement Exclusions</h3>
        <ul class="proposal-bullet-list">
            @foreach (($d['agreement_exclusions'] ?? []) as $item)
                <li>{{ $item }}</li>
            @endforeach
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
                @foreach (($d['supplemental_fees'] ?? []) as $fee)
                    <tr>
                        <td>{{ $fee['service'] ?? '' }}</td>
                        <td>{{ $fee['description'] ?? '' }}</td>
                        <td>{{ $fee['fee'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="proposal-page">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">8.</span>
            <span>Terms and Conditions</span>
        </h2>
        @foreach (($d['terms_and_conditions'] ?? []) as $term)
            <div class="proposal-term-block">
                <h3 class="proposal-subheading proposal-subheading-tight">{{ $term['title'] ?? '' }}</h3>

                @if (!empty($term['intro']))
                    <p class="proposal-paragraph">{{ $term['intro'] }}</p>
                @endif

                @foreach (($term['paragraphs'] ?? []) as $paragraph)
                    <p class="proposal-paragraph">{{ $paragraph }}</p>
                @endforeach

                @if (!empty($term['items']))
                    <ul class="proposal-bullet-list">
                        @foreach ($term['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (!empty($term['outro']))
                    <p class="proposal-paragraph">{{ $term['outro'] }}</p>
                @endif
            </div>
        @endforeach

        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">9.</span>
            <span>Engagement Team</span>
        </h2>
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
                @foreach (($d['engagement_team'] ?? []) as $member)
                    <tr>
                        <td>{{ $member['name'] ?? '' }}</td>
                        <td>{{ $member['designation'] ?? '' }}</td>
                        <td>{{ $member['branch'] ?? '' }}</td>
                        <td>{{ $member['email'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="proposal-system-note">{{ $d['system_note'] ?? '' }}</p>
    </section>

    <section class="proposal-page">
        <h2 class="proposal-section-heading">
            <span class="proposal-section-number">10.</span>
            <span>Conforme and Acceptance</span>
        </h2>
        <p class="proposal-paragraph">By signing below, the parties acknowledge and accept the terms and conditions outlined in this proposal, which shall constitute a binding agreement.</p>

        <div class="proposal-signature-grid">
            <div class="proposal-signature-block">
                <div class="proposal-signature-label">For the Client</div>
                <div class="proposal-signature-line">{{ $d['client_name'] ?? '' }}</div>
                <div class="proposal-signature-subline">{{ $d['business_name'] ?? '' }}</div>
            </div>
            <div class="proposal-signature-block">
                <div class="proposal-signature-label">For John Kelly &amp; Company</div>
                <div class="proposal-signature-line">{{ $d['prepared_by_name'] ?? '' }}</div>
                <div class="proposal-signature-subline">ID Number: {{ $d['prepared_by_id'] ?? '' }}</div>
            </div>
        </div>

        <div class="proposal-footer-note">
            <div>John Kelly &amp; Company</div>
            <div>{{ $d['company_address'] ?? '' }}</div>
            <div>Email: {{ $d['company_email'] ?? '' }} &bull; Website: {{ $d['company_website'] ?? '' }} &bull; Phone: {{ $d['company_phone'] ?? '' }}</div>
        </div>
    </section>
</div>
