<?php
    $data = $dealFormData ?? [];
    $dealInfo = $deal ?? [];
    $detailInfo = $detail ?? [];
    $logoFilePath = public_path('images/imaglogo.png');
    $logoUrl = asset('images/imaglogo.png');

    $text = static function ($key, $fallback = '-') use ($data): string {
        $value = $data[$key] ?? null;

        if (is_array($value)) {
            $value = collect($value)
                ->flatten()
                ->filter(fn ($item) => filled($item))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->implode(', ');
        }

        return filled($value) ? (string) $value : $fallback;
    };

    $listText = static function (array $values, string $fallback = '-') : string {
        $normalized = collect($values)
            ->flatten()
            ->filter(fn ($item) => filled($item))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        return $normalized === [] ? $fallback : implode(', ', $normalized);
    };

    $formatDate = static function ($value, string $fallback = '-'): string {
        if (! filled($value)) {
            return $fallback;
        }

        try {
            return \Illuminate\Support\Carbon::parse((string) $value)->format('M d, Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    };

    $money = static function ($value): string {
        if (! filled($value)) {
            return 'P0.00';
        }

        $raw = str_replace(['P', ',', ' '], '', (string) $value);
        if (is_numeric($raw)) {
            return 'P'.number_format((float) $raw, 2);
        }

        return (string) $value;
    };

    $statusMap = is_array($data['requirements_status_map'] ?? null) ? $data['requirements_status_map'] : [];
    $requiredActions = collect($data['required_actions_options'] ?? [])
        ->merge($data['required_actions_custom'] ?? [])
        ->filter(fn ($item) => filled($item))
        ->map(fn ($item) => trim(str_replace('Custom: ', '', (string) $item)))
        ->filter()
        ->values()
        ->all();

    $serviceArea = $listText([
        $data['service_area_options'] ?? [],
        collect($data['service_area_other'] ?? [])->map(fn ($item) => 'Others: '.trim((string) $item))->all(),
    ], $text('service_area'));

    $services = $listText([
        $data['service_options'] ?? [],
        collect($data['service_identification_custom'] ?? [])->map(fn ($item) => 'Custom: '.trim((string) $item))->all(),
    ], $text('services'));

    $products = $listText([
        $data['product_options'] ?? [],
        collect($data['products_other_entries'] ?? [])->map(fn ($item) => 'Custom: '.trim((string) $item))->all(),
    ], $text('products'));

    $supportRequired = $listText([
        $data['support_required_options'] ?? [],
        collect($data['support_required_custom'] ?? [])->map(fn ($item) => trim((string) $item))->all(),
    ], $text('support_required'));

    $requirementRows = [
        'client_contact_form' => 'Client Contact Form',
        'deal_form' => 'Deal Form',
        'business_information_form' => 'Business Information Form',
        'client_information_form' => 'Client Information Form',
        'service_task_activation_routing_tracker' => 'Service Task Activation & Routing Tracker (Start)',
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulting and Deal Form</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        body {
            margin: 0;
            background: #eef2f7;
            color: #0f172a;
            font-family: "Times New Roman", Georgia, serif;
            font-size: 11px;
            line-height: 1.2;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .shell {
            width: 206mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 12px;
        }
        .toolbar {
            margin: 0 auto 12px;
            width: 206mm;
            max-width: 100%;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .toolbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid #1d4ed8;
            background: #1d4ed8;
            color: #fff;
            text-decoration: none;
            font: 600 12px Arial, sans-serif;
            cursor: pointer;
        }
        .toolbar-button.secondary {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }
        .sheet {
            background: #fff;
            width: 188mm;
            margin: 0 auto;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            padding: 3mm;
        }
        .sheet-frame {
            border: 1.5px solid #334155;
            overflow: hidden;
            background: #fff;
        }
        .sheet *, .sheet *::before, .sheet *::after { box-sizing: border-box; }
        .head {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr);
            gap: 12px;
            align-items: start;
            border-bottom: 1px solid #334155;
            padding: 10px 12px 8px;
        }
        .brand img {
            width: 180px;
            max-width: 100%;
            height: auto;
            display: block;
        }
        .title {
            text-align: right;
            font-family: Arial, sans-serif;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .meta {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 6px;
            font-size: 11px;
        }
        .section-title {
            padding: 5px 8px;
            border-bottom: 1px solid #334155;
            background: #102d79;
            color: #fff;
            text-align: center;
            font: 700 12px Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(24, minmax(0, 1fr));
        }
        .cell {
            min-height: 40px;
            padding: 6px 7px;
            border-right: 1px solid #334155;
            border-bottom: 1px solid #334155;
            background: #fff;
        }
        .grid > .cell:last-child { border-right: 0; }
        .label {
            display: block;
            font-size: 9px;
            line-height: 1.05;
            font-weight: 700;
            text-transform: uppercase;
            color: #111827;
            font-family: Arial, sans-serif;
        }
        .value {
            display: block;
            padding-top: 3px;
            font-size: 11px;
            line-height: 1.22;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }
        .mini-table td, .mini-table th {
            border-right: 1px solid #334155;
            border-bottom: 1px solid #334155;
            padding: 6px 7px;
            vertical-align: top;
            font-size: 11px;
        }
        .mini-table td:last-child, .mini-table th:last-child { border-right: 0; }
        .mini-table th {
            background: #f8fafc;
            font: 700 9px Arial, sans-serif;
            text-transform: uppercase;
            text-align: left;
        }
        .status-provided { background: #ecfdf5; }
        .status-pending { background: #fffbeb; }
        .footer-note {
            padding: 4px 8px;
            font-size: 8px;
            color: #475569;
            text-align: right;
            border-top: 1px solid #334155;
        }
        .col-3 { grid-column: span 3 / span 3; }
        .col-4 { grid-column: span 4 / span 4; }
        .col-5 { grid-column: span 5 / span 5; }
        .col-6 { grid-column: span 6 / span 6; }
        .col-7 { grid-column: span 7 / span 7; }
        .col-8 { grid-column: span 8 / span 8; }
        .col-10 { grid-column: span 10 / span 10; }
        .col-12 { grid-column: span 12 / span 12; }
        .col-24 { grid-column: span 24 / span 24; }
        @media print {
            body { background: #fff; }
            .shell { width: auto; max-width: none; margin: 0; padding: 0; }
            .toolbar { display: none !important; }
            .sheet {
                width: 100%;
                margin: 0;
                box-shadow: none;
                padding: 0;
            }
            .sheet-frame {
                border: 1.5px solid #334155;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="<?php echo e(route('deals.show', $dealInfo['id'] ?? 0)); ?>" class="toolbar-button secondary">Back</a>
        <button type="button" onclick="window.print()" class="toolbar-button">Print / Save as PDF</button>
    </div>

    <div class="shell">
        <div class="sheet">
            <div class="sheet-frame">
                <div class="head">
                    <div class="brand">
                        <?php if(is_file($logoFilePath)): ?>
                            <img src="<?php echo e($logoUrl); ?>" alt="John Kelly and Company">
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="title">Consulting &amp; Deal Form</div>
                        <div class="meta">
                            <span>Deal No. <?php echo e($dealInfo['deal_code'] ?? ('DEAL-'.$dealInfo['id'])); ?></span>
                            <span>Date <?php echo e(optional($generatedAt ?? null)->format('m/d/Y') ?? now()->format('m/d/Y')); ?></span>
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="cell col-8"><span class="label">Deal Name</span><span class="value"><?php echo e($dealInfo['deal_name'] ?? '-'); ?></span></div>
                    <div class="cell col-5"><span class="label">Stage</span><span class="value"><?php echo e($dealInfo['stage'] ?? $text('stage')); ?></span></div>
                    <div class="cell col-6"><span class="label">Engagement Type</span><span class="value"><?php echo e($text('engagement_type')); ?></span></div>
                    <div class="cell col-5"><span class="label">Total Value</span><span class="value"><?php echo e($money($dealInfo['value'] ?? ($data['total_estimated_engagement_value'] ?? null))); ?></span></div>
                </div>

                <div class="section-title">Contact Information</div>
                <div class="grid">
                    <div class="cell col-3"><span class="label">Salutation</span><span class="value"><?php echo e($text('salutation')); ?></span></div>
                    <div class="cell col-5"><span class="label">First Name</span><span class="value"><?php echo e($text('first_name')); ?></span></div>
                    <div class="cell col-3"><span class="label">Middle Initial</span><span class="value"><?php echo e($text('middle_initial')); ?></span></div>
                    <div class="cell col-5"><span class="label">Last Name</span><span class="value"><?php echo e($text('last_name')); ?></span></div>
                    <div class="cell col-4"><span class="label">Name Extension</span><span class="value"><?php echo e($text('name_extension')); ?></span></div>
                    <div class="cell col-4"><span class="label">Sex</span><span class="value"><?php echo e($text('sex')); ?></span></div>
                </div>
                <div class="grid">
                    <div class="cell col-5"><span class="label">Date of Birth</span><span class="value"><?php echo e($formatDate($data['date_of_birth'] ?? null)); ?></span></div>
                    <div class="cell col-7"><span class="label">Email Address</span><span class="value"><?php echo e($detailInfo['email_address'] ?? $text('email')); ?></span></div>
                    <div class="cell col-5"><span class="label">Mobile Number</span><span class="value"><?php echo e($detailInfo['contact_number'] ?? $text('mobile')); ?></span></div>
                    <div class="cell col-7"><span class="label">Position / Designation</span><span class="value"><?php echo e($detailInfo['contact_person_position'] ?? $text('position')); ?></span></div>
                </div>
                <div class="grid">
                    <div class="cell col-12"><span class="label">Address</span><span class="value"><?php echo e($text('address')); ?></span></div>
                    <div class="cell col-5"><span class="label">Company</span><span class="value"><?php echo e($dealInfo['company_name'] ?? $text('company_name')); ?></span></div>
                    <div class="cell col-7"><span class="label">Company Address</span><span class="value"><?php echo e($text('company_address')); ?></span></div>
                </div>

            <div class="section-title">Service Identification</div>
            <div class="grid">
                <div class="cell col-12"><span class="label">Service Area</span><span class="value"><?php echo e($serviceArea); ?></span></div>
                <div class="cell col-12"><span class="label">Services</span><span class="value"><?php echo e($services); ?></span></div>
            </div>

            <div class="section-title">Products</div>
            <div class="grid">
                <div class="cell col-12"><span class="label">Products / Deliverables</span><span class="value"><?php echo e($products); ?></span></div>
                <div class="cell col-12"><span class="label">Scope of Work</span><span class="value"><?php echo e($text('scope_of_work')); ?></span></div>
            </div>

            <div class="section-title">Client Requirements</div>
            <table class="mini-table">
                <tr>
                    <th style="width:46%;">Requirement</th>
                    <th style="width:18%;">Provided</th>
                    <th style="width:18%;">Pending</th>
                    <th style="width:18%;">Required Action</th>
                </tr>
                <?php $__currentLoopData = $requirementRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $status = strtolower((string) ($statusMap[$key] ?? 'pending')); ?>
                    <tr class="<?php echo e($status === 'provided' ? 'status-provided' : 'status-pending'); ?>">
                        <td><?php echo e($label); ?></td>
                        <td><?php echo e($status === 'provided' ? 'Yes' : '-'); ?></td>
                        <td><?php echo e($status === 'pending' ? 'Yes' : '-'); ?></td>
                        <td><?php echo e($listText($requiredActions)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>

            <div class="section-title">Timeline &amp; Assessment</div>
            <div class="grid">
                <div class="cell col-6"><span class="label">Planned Start Date</span><span class="value"><?php echo e($formatDate($data['planned_start_date'] ?? null)); ?></span></div>
                <div class="cell col-6"><span class="label">Estimated Duration</span><span class="value"><?php echo e($text('estimated_duration')); ?></span></div>
                <div class="cell col-6"><span class="label">Estimated Completion Date</span><span class="value"><?php echo e($formatDate($data['estimated_completion_date'] ?? null)); ?></span></div>
                <div class="cell col-6"><span class="label">Confirmed Delivery Date</span><span class="value"><?php echo e($formatDate($data['confirmed_delivery_date'] ?? null)); ?></span></div>
            </div>
            <div class="grid">
                <div class="cell col-8"><span class="label">Timeline Notes</span><span class="value"><?php echo e($text('timeline_notes')); ?></span></div>
                <div class="cell col-6"><span class="label">Service Complexity</span><span class="value"><?php echo e($text('service_complexity')); ?></span></div>
                <div class="cell col-10"><span class="label">Professional Support Required</span><span class="value"><?php echo e($supportRequired); ?></span></div>
            </div>

            <div class="section-title">Fees &amp; Payment</div>
            <div class="grid">
                <div class="cell col-6"><span class="label">Professional Fee</span><span class="value"><?php echo e($money($data['estimated_professional_fee'] ?? null)); ?></span></div>
                <div class="cell col-6"><span class="label">Government Fees</span><span class="value"><?php echo e($money($data['estimated_government_fees'] ?? null)); ?></span></div>
                <div class="cell col-6"><span class="label">Service Support Fee</span><span class="value"><?php echo e($money($data['estimated_service_support_fee'] ?? null)); ?></span></div>
                <div class="cell col-6"><span class="label">Payment Terms</span><span class="value"><?php echo e($text('payment_terms')); ?></span></div>
            </div>

                <div class="section-title">Proposal &amp; Internal Assignment</div>
                <div class="grid">
                    <div class="cell col-8"><span class="label">Proposal Decision</span><span class="value"><?php echo e($text('proposal_decision')); ?></span></div>
                    <div class="cell col-8"><span class="label">Decline Reason</span><span class="value"><?php echo e($text('decline_reason')); ?></span></div>
                    <div class="cell col-8"><span class="label">Service Department / Unit</span><span class="value"><?php echo e($text('service_department_unit')); ?></span></div>
                </div>
                <div class="grid">
                    <div class="cell col-12"><span class="label">Assigned Consultant</span><span class="value"><?php echo e($text('assigned_consultant')); ?></span></div>
                    <div class="cell col-12"><span class="label">Assigned Associate</span><span class="value"><?php echo e($text('assigned_associate')); ?></span></div>
                </div>

                <div class="section-title">Notes &amp; Approval</div>
                <div class="grid">
                    <div class="cell col-12"><span class="label">Consultant Notes</span><span class="value"><?php echo e($text('consultant_notes')); ?></span></div>
                    <div class="cell col-12"><span class="label">Associate Notes</span><span class="value"><?php echo e($text('associate_notes')); ?></span></div>
                </div>
                <div class="grid">
                    <div class="cell col-6"><span class="label">Prepared By</span><span class="value"><?php echo e($text('prepared_by')); ?></span></div>
                    <div class="cell col-6"><span class="label">Reviewed By</span><span class="value"><?php echo e($text('reviewed_by')); ?></span></div>
                    <div class="cell col-6"><span class="label">Date</span><span class="value"><?php echo e($formatDate($data['internal_date'] ?? null, $text('internal_date'))); ?></span></div>
                    <div class="cell col-6"><span class="label">Client Fullname &amp; Signature</span><span class="value"><?php echo e($text('client_fullname_signature')); ?></span></div>
                </div>
                <div class="grid">
                    <div class="cell col-4"><span class="label">Referred / Closed By</span><span class="value"><?php echo e($text('referred_closed_by')); ?></span></div>
                    <div class="cell col-4"><span class="label">Sales &amp; Marketing</span><span class="value"><?php echo e($text('internal_sales_marketing')); ?></span></div>
                    <div class="cell col-4"><span class="label">Lead Consultant</span><span class="value"><?php echo e($text('lead_consultant')); ?></span></div>
                    <div class="cell col-4"><span class="label">Lead Associate</span><span class="value"><?php echo e($text('lead_associate_assigned')); ?></span></div>
                    <div class="cell col-4"><span class="label">Finance</span><span class="value"><?php echo e($text('internal_finance')); ?></span></div>
                    <div class="cell col-4"><span class="label">President</span><span class="value"><?php echo e($text('internal_president')); ?></span></div>
                </div>

                <div class="footer-note">John Kelly &amp; Company</div>
            </div>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            window.onload = () => window.print();
        }
    </script>
</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views\pdf\deal.blade.php ENDPATH**/ ?>