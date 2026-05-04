<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transmittal Receipt</title>
    <style>
        @page { margin: 6px 8px; }

        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
            font-size: 8px;
        }

        .header-table,
        .top-info,
        .details-table,
        .signatures {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            margin-bottom: 4px;
        }

        .header-left,
        .header-right {
            vertical-align: top;
        }

        .header-right {
            text-align: right;
            width: 170px;
            font-size: 7px;
            line-height: 1.2;
        }

        .company-name {
            font-size: 10px;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 1px;
        }

        .company-sub {
            font-size: 7px;
            color: #4b5563;
        }

        .receipt-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 2px 0 1px;
            line-height: 1.1;
        }

        .receipt-subtitle {
            text-align: center;
            font-size: 6px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .top-info td {
            vertical-align: top;
            width: 50%;
            padding-right: 4px;
        }

        .info-box {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
        }

        .info-line {
            margin-bottom: 2px;
            line-height: 1.15;
            font-size: 7px;
        }

        .label {
            font-weight: bold;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            margin-top: 3px;
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px solid #cbd5e1;
        }

        .details-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 7px;
            line-height: 1.15;
        }

        .details-table .label-col {
            width: 110px;
            font-weight: bold;
            background: #f8fafc;
        }

        .signatures {
            margin-top: 5px;
        }

        .signatures td {
            width: 25%;
            vertical-align: top;
            padding-right: 6px;
        }

        .sign-label {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 7px;
        }

        .sign-line {
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 2px;
            font-weight: bold;
            min-height: 9px;
            font-size: 7px;
        }

        .sign-datetime {
            margin-top: 2px;
            font-size: 6px;
            color: #4b5563;
            line-height: 1.15;
        }

        .footer {
            margin-top: 5px;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            font-size: 6px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
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
                    <div class="info-line"><span class="label">From:</span> {{ $fromValue }}</div>
                    <div class="info-line"><span class="label">To:</span> {{ $toValue }}</div>
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

    <div class="section-title">RECEIPT DETAILS</div>

    <table class="details-table">
        <tr><td class="label-col">Address</td><td>{{ $transmittal->address ?? 'N/A' }}</td></tr>
        <tr><td class="label-col">Actions</td><td>{{ $actions }}</td></tr>
        <tr><td class="label-col">Prepared By</td><td>{{ $transmittal->prepared_by_name ?? 'N/A' }}</td></tr>
        <tr><td class="label-col">Approved By</td><td>{{ $approvedByText }}</td></tr>
        <tr><td class="label-col">Delivered By</td><td>{{ $transmittal->delivered_by ?? 'N/A' }}</td></tr>
        <tr><td class="label-col">Received By</td><td>{{ $transmittal->received_by ?? 'N/A' }}</td></tr>
        <tr><td class="label-col">Affiliated to / Company</td><td>{{ $transmittal->receiver_affiliation ?? 'N/A' }}</td></tr>
        <tr><td class="label-col">Date and Time Received</td><td>{{ $receivedAt }}</td></tr>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="sign-label">Prepared by:</div>
                <div class="sign-line">{{ $transmittal->prepared_by_name ?? ' ' }}</div>
                <div class="sign-datetime">{{ $preparedAt }}</div>
            </td>
            <td>
                <div class="sign-label">Approved by:</div>
                <div class="sign-line">{{ $transmittal->approved_by_name ?? ' ' }}</div>
                <div class="sign-datetime">{{ $approvedAt }}</div>
            </td>
            <td>
                <div class="sign-label">Delivered by:</div>
                <div class="sign-line">{{ $transmittal->delivered_by ?? ' ' }}</div>
            </td>
            <td>
                <div class="sign-label">Received by:</div>
                <div class="sign-line">{{ $transmittal->received_by ?? ' ' }}</div>
                <div class="sign-datetime">{{ $transmittal->receiver_affiliation ?? ' ' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        This document is system-generated by John Kelly &amp; Company CRM. Linked Transmittal: {{ $transmittal->transmittal_no ?? 'N/A' }}.
    </div>
</body>
</html>