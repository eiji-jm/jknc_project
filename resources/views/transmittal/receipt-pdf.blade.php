<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transmittal Receipt</title>
    <style>
        @page {
            margin: 6mm 6mm 6mm 6mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
            font-size: 6px;
        }

        .page {
            width: 100%;
        }

        .sheet {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            width: 100%;
            margin-bottom: 3px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left,
        .header-right {
            vertical-align: top;
        }

        .header-right {
            text-align: right;
            width: 100px;
            font-size: 5px;
            line-height: 1.2;
        }

        .logo {
            width: 42px;
            height: auto;
            margin-bottom: 1px;
        }

        .company-name {
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 0;
            letter-spacing: 0.1px;
        }

        .company-sub {
            font-size: 5px;
            color: #4b5563;
        }

        .receipt-title {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            margin: 3px 0 1px;
            letter-spacing: 0.2px;
        }

        .receipt-subtitle {
            text-align: center;
            font-size: 5px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .top-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .top-info td {
            vertical-align: top;
            width: 50%;
            padding: 0 2px 0 0;
        }

        .info-box {
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 3px 4px;
        }

        .info-line {
            margin-bottom: 2px;
            line-height: 1.15;
        }

        .label {
            font-weight: bold;
        }

        .section {
            margin-top: 3px;
            margin-bottom: 3px;
        }

        .section-title {
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 2px;
            padding-bottom: 1px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.1px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .details-table td {
            padding: 1px 2px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 5px;
        }

        .details-table .label-col {
            width: 62px;
            font-weight: bold;
            background: #f8fafc;
        }

        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .signatures td {
            width: 25%;
            vertical-align: top;
            padding-right: 3px;
        }

        .sign-label {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 5px;
        }

        .sign-line {
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 1px;
            font-weight: bold;
            min-height: 5px;
            font-size: 5px;
        }

        .footer {
            margin-top: 3px;
            border-top: 1px solid #cbd5e1;
            padding-top: 2px;
            font-size: 4px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/imaglogo.png');
        $receipt = $transmittal->receipt;

        $receiptDate = $receipt && $receipt->created_at
            ? $receipt->created_at->format('Y-m-d')
            : now()->format('Y-m-d');

        $receivedAt = $transmittal->received_at
            ? $transmittal->received_at->format('Y-m-d H:i:s')
            : 'N/A';

        $deliveryType = 'N/A';

        if (($transmittal->delivery_type ?? '') === 'By Person') {
            $deliveryType = $transmittal->by_person_who
                ? 'By Person - ' . $transmittal->by_person_who
                : 'By Person';
        } elseif (($transmittal->delivery_type ?? '') === 'Registered Mail') {
            $deliveryType = $transmittal->registered_mail_provider
                ? 'Registered Mail - ' . $transmittal->registered_mail_provider
                : 'Registered Mail';
        } elseif (($transmittal->delivery_type ?? '') === 'Electronic') {
            $deliveryType = $transmittal->electronic_method
                ? 'Electronic - ' . $transmittal->electronic_method
                : 'Electronic';
        }

        $actions = collect([
            $transmittal->action_delivery ? 'Delivery' : null,
            $transmittal->action_pick_up ? 'Pick Up' : null,
            $transmittal->action_drop_off ? 'Drop Off' : null,
            $transmittal->action_email ? 'Email' : null,
        ])->filter()->implode(', ');

        if ($actions === '') {
            $actions = '—';
        }
    @endphp

    <div class="page">
        <div class="sheet">
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-left">
                            @if(file_exists($logoPath))
                                <img src="{{ $logoPath }}" alt="John Kelly Logo" class="logo">
                            @endif
                            <div class="company-name">JOHN KELLY &amp; COMPANY</div>
                            <div class="company-sub">Official Transmittal Receipt</div>
                        </td>
                        <td class="header-right">
                            <div><span class="label">Receipt No:</span> {{ $receipt->receipt_no ?? 'N/A' }}</div>
                            <div><span class="label">Receipt Date:</span> {{ $receiptDate }}</div>
                            <div><span class="label">Linked Ref No:</span> {{ $transmittal->transmittal_no ?? 'N/A' }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="receipt-title">TRANSMITTAL RECEIPT</div>
            <div class="receipt-subtitle">Generated upon approved transmittal</div>

            <table class="top-info">
                <tr>
                    <td>
                        <div class="info-box">
                            <div class="info-line"><span class="label">Mode:</span> {{ $transmittal->mode ?? 'N/A' }}</div>
                            <div class="info-line"><span class="label">Office:</span> {{ $transmittal->office_name ?? 'N/A' }}</div>
                            <div class="info-line"><span class="label">From:</span> {{ $transmittal->mode === 'SEND' ? ($transmittal->office_name ?? 'N/A') : ($transmittal->party_name ?? 'N/A') }}</div>
                            <div class="info-line"><span class="label">To:</span> {{ $transmittal->mode === 'SEND' ? ($transmittal->party_name ?? 'N/A') : ($transmittal->office_name ?? 'N/A') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="info-box">
                            <div class="info-line"><span class="label">Delivery Type:</span> {{ $deliveryType }}</div>
                            <div class="info-line"><span class="label">Recipient Email:</span> {{ $transmittal->recipient_email ?: '—' }}</div>
                            <div class="info-line"><span class="label">Workflow:</span> {{ $transmittal->workflow_status ?? 'N/A' }}</div>
                            <div class="info-line"><span class="label">Approval:</span> {{ $transmittal->approval_status ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="section">
                <div class="section-title">Receipt Details</div>

                <table class="details-table">
                    <tr>
                        <td class="label-col">Address</td>
                        <td>{{ $transmittal->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Actions</td>
                        <td>{{ $actions }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Prepared By</td>
                        <td>{{ $transmittal->prepared_by_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Approved By</td>
                        <td>{{ $transmittal->approved_by_name ?? 'N/A' }}{{ $transmittal->approved_position ? ' (' . $transmittal->approved_position . ')' : '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Delivered By</td>
                        <td>{{ $transmittal->delivered_by ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Received By</td>
                        <td>{{ $transmittal->received_by ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Date and Time Received</td>
                        <td>{{ $receivedAt }}</td>
                    </tr>
                </table>
            </div>

            <table class="signatures">
                <tr>
                    <td>
                        <div class="sign-label">Prepared by:</div>
                        <div class="sign-line">{{ $transmittal->prepared_by_name ?? ' ' }}</div>
                    </td>
                    <td>
                        <div class="sign-label">Approved by:</div>
                        <div class="sign-line">{{ $transmittal->approved_by_name ?? ' ' }}</div>
                    </td>
                    <td>
                        <div class="sign-label">Delivered by:</div>
                        <div class="sign-line">{{ $transmittal->delivered_by ?? ' ' }}</div>
                    </td>
                    <td>
                        <div class="sign-label">Received by:</div>
                        <div class="sign-line">{{ $transmittal->received_by ?? ' ' }}</div>
                    </td>
                </tr>
            </table>

            <div class="footer">
                This document is system-generated by John Kelly &amp; Company CRM. Linked Transmittal: {{ $transmittal->transmittal_no ?? 'N/A' }}.
            </div>
        </div>
    </div>
</body>
</html>