@php
    $data = $dealFormData ?? [];
    $dealInfo = $deal ?? [];
    $detailInfo = $detail ?? [];
    $text = static fn ($key, $fallback = '-') => filled($data[$key] ?? null) ? (string) $data[$key] : $fallback;
    $list = static fn ($key): array => collect($data[$key] ?? [])->filter(fn ($value) => filled($value))->map(fn ($value) => (string) $value)->values()->all();
    $statusMap = is_array($data['requirements_status_map'] ?? null) ? $data['requirements_status_map'] : [];
    $customRequirements = collect($data['client_requirements_custom'] ?? [])->filter(fn ($value) => filled($value))->values()->all();
    $money = static fn ($value): string => filled($value) ? (string) $value : 'P0.00';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deal PDF</title>
    <style>
        body { margin: 0; background: #eef2f7; font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        @page { margin: 18mm 12mm; }
        .print-shell { max-width: 1100px; margin: 0 auto; padding: 24px; }
        .toolbar { margin-bottom: 16px; padding: 12px 16px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; }
        .toolbar-title { margin: 0 0 4px; font-size: 18px; font-weight: 700; }
        .toolbar-copy { margin: 0; font-size: 13px; color: #6b7280; }
        .toolbar-actions { margin-top: 12px; }
        .toolbar-button { display: inline-block; margin-right: 8px; padding: 10px 16px; border-radius: 999px; border: 1px solid #d1d5db; color: #374151; text-decoration: none; font-size: 13px; font-weight: 600; }
        .toolbar-button.primary { background: #2563eb; border-color: #2563eb; color: #ffffff; }
        .sheet { width: 100%; border: 1px solid #374151; background: #ffffff; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08); }
        .title { padding: 10px 12px; text-align: center; border-bottom: 1px solid #374151; }
        .title h1 { margin: 0; font-size: 18px; letter-spacing: 0.06em; text-transform: uppercase; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta td { width: 50%; padding: 6px 8px; border-bottom: 1px solid #374151; }
        .section-title { background: #1e3a8a; color: #ffffff; text-align: center; font-weight: bold; padding: 6px 8px; border-top: 1px solid #374151; border-bottom: 1px solid #374151; text-transform: uppercase; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td, .grid th { border-bottom: 1px solid #374151; border-right: 1px solid #374151; padding: 6px 8px; vertical-align: top; }
        .grid td:last-child, .grid th:last-child { border-right: 0; }
        .grid th { text-align: left; font-size: 10px; text-transform: uppercase; background: #f9fafb; }
        .label { display: block; margin-bottom: 4px; font-size: 9px; color: #4b5563; text-transform: uppercase; }
        .value { min-height: 16px; }
        .muted { color: #6b7280; }
        .list { margin: 0; padding-left: 16px; }
        .list li { margin-bottom: 2px; }
        .footer { margin-top: 10px; font-size: 10px; color: #6b7280; text-align: right; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff; }
            .print-shell { max-width: none; margin: 0; padding: 0; }
            .sheet { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="print-shell">
        <div class="toolbar no-print">
            <p class="toolbar-title">Deal Information Form</p>
            <p class="toolbar-copy">Use your browser's print dialog and choose Save as PDF to export this document.</p>
            <div class="toolbar-actions">
                <a href="{{ route('deals.show', $dealInfo['id'] ?? 0) }}" class="toolbar-button">Back</a>
                <button type="button" onclick="window.print()" class="toolbar-button primary">Print / Save as PDF</button>
            </div>
        </div>

        <div class="sheet">
        <div class="title">
            <h1>Deal Information Form</h1>
        </div>

        <table class="meta">
            <tr>
                <td>
                    <span class="label">Generated</span>
                    <div class="value">{{ optional($generatedAt ?? null)->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}</div>
                </td>
                <td>
                    <span class="label">Deal ID</span>
                    <div class="value">{{ $dealInfo['deal_code'] ?? ('Deal-'.$dealInfo['id']) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Deal Header</div>
        <table class="grid">
            <tr>
                <td style="width:25%;">
                    <span class="label">Deal Name</span>
                    <div class="value">{{ $dealInfo['deal_name'] ?? '-' }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Stage</span>
                    <div class="value">{{ $dealInfo['stage'] ?? $text('stage') }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Value</span>
                    <div class="value">{{ $dealInfo['value'] ?? $money($data['total_estimated_engagement_value'] ?? null) }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Owner</span>
                    <div class="value">{{ $dealInfo['owner_name'] ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Contact Information</div>
        <table class="grid">
            <tr>
                <td style="width:33%;">
                    <span class="label">Contact Person</span>
                    <div class="value">{{ $dealInfo['contact_name'] ?? trim($text('first_name', '').' '.$text('last_name', '')) }}</div>
                </td>
                <td style="width:33%;">
                    <span class="label">Position</span>
                    <div class="value">{{ $detailInfo['contact_person_position'] ?? $text('position') }}</div>
                </td>
                <td style="width:34%;">
                    <span class="label">Client Type</span>
                    <div class="value">{{ $detailInfo['client_type'] ?? $text('customer_type') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Email Address</span>
                    <div class="value">{{ $detailInfo['email_address'] ?? $text('email') }}</div>
                </td>
                <td>
                    <span class="label">Contact Number</span>
                    <div class="value">{{ $detailInfo['contact_number'] ?? $text('mobile') }}</div>
                </td>
                <td>
                    <span class="label">Address</span>
                    <div class="value">{{ $text('address') }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Company Information</div>
        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <span class="label">Company Name</span>
                    <div class="value">{{ $dealInfo['company_name'] ?? $text('company_name') }}</div>
                </td>
                <td style="width:50%;">
                    <span class="label">Company Address</span>
                    <div class="value">{{ $text('company_address') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Industry / Service Area</span>
                    <div class="value">{{ $detailInfo['industry'] ?? $text('service_area') }}</div>
                </td>
                <td>
                    <span class="label">Expected Close Date</span>
                    <div class="value">{{ $detailInfo['expected_close_date'] ?? $text('estimated_completion_date') }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Service &amp; Engagement Details</div>
        <table class="grid">
            <tr>
                <td style="width:25%;">
                    <span class="label">Service Type</span>
                    <div class="value">{{ $text('service_area') }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Engagement Type</span>
                    <div class="value">{{ $text('engagement_type') }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Estimated Duration</span>
                    <div class="value">{{ $text('estimated_duration') }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Status</span>
                    <div class="value">{{ $detailInfo['deal_status'] ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Products / Services Selected</div>
        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <span class="label">Services</span>
                    @php $services = $list('service_options'); @endphp
                    @if ($services !== [])
                        <ul class="list">
                            @foreach ($services as $service)
                                <li>{{ $service }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="value">{{ $text('services') }}</div>
                    @endif
                </td>
                <td style="width:50%;">
                    <span class="label">Products</span>
                    @php $products = $list('product_options'); @endphp
                    @if ($products !== [])
                        <ul class="list">
                            @foreach ($products as $product)
                                <li>{{ $product }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="value">{{ $text('products') }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="section-title">Scope of Work</div>
        <table class="grid">
            <tr>
                <td>
                    <div class="value">{{ $text('scope_of_work') }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Requirements / Actions</div>
        <table class="grid">
            <tr>
                <th style="width:40%;">Requirement</th>
                <th style="width:20%;">Status</th>
                <th style="width:40%;">Required Actions</th>
            </tr>
            @foreach ([
                'client_contact_form' => 'Client Contact Form',
                'deal_form' => 'Deal Form',
                'business_information_form' => 'Business Information Form',
                'client_information_form' => 'Client Information Form',
                'service_task_activation_routing_tracker' => 'Service Task Activation & Routing Tracker',
                'others' => 'Others',
            ] as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ ucfirst($statusMap[$key] ?? 'pending') }}</td>
                    <td>{{ $text('required_actions', '-') }}</td>
                </tr>
            @endforeach
            @foreach ($customRequirements as $requirement)
                <tr>
                    <td>{{ $requirement }}</td>
                    <td>{{ ucfirst($statusMap[\Illuminate\Support\Str::slug((string) $requirement, '_')] ?? 'pending') }}</td>
                    <td>{{ $text('required_actions', '-') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="section-title">Financial Details</div>
        <table class="grid">
            <tr>
                <td style="width:25%;">
                    <span class="label">Professional Fee</span>
                    <div class="value">{{ $money($data['estimated_professional_fee'] ?? null) }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Government Fees</span>
                    <div class="value">{{ $money($data['estimated_government_fees'] ?? null) }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Service Support Fee</span>
                    <div class="value">{{ $money($data['estimated_service_support_fee'] ?? null) }}</div>
                </td>
                <td style="width:25%;">
                    <span class="label">Total Engagement Value</span>
                    <div class="value">{{ $money($data['total_estimated_engagement_value'] ?? null) }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">Payment Terms</span>
                    <div class="value">{{ $text('payment_terms') }}</div>
                </td>
                <td colspan="2">
                    <span class="label">Support Required</span>
                    <div class="value">{{ $text('support_required') }}</div>
                </td>
            </tr>
        </table>
        </div>

        <div class="footer">
            John Kelly &amp; Company CRM
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
