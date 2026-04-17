<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .header-table {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }

        .header-cell {
            vertical-align: top;
            padding: 16px 18px;
            border-right: 1px solid #e5e7eb;
        }

        .header-cell:last-child {
            border-right: 0;
        }

        .logo-wrap {
            width: 86px;
            height: 86px;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            display: inline-block;
            text-align: center;
            line-height: 86px;
            margin-right: 14px;
            vertical-align: middle;
        }

        .logo-wrap img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            vertical-align: middle;
        }

        .brand-block {
            display: inline-block;
            vertical-align: middle;
            max-width: 70%;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.28em;
            font-size: 9px;
            color: #6b7280;
            margin: 0 0 6px;
        }

        .brand-name {
            font-size: 22px;
            line-height: 1.1;
            margin: 0;
            color: #111827;
            font-weight: 700;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 11px;
            color: #1d4ed8;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .brand-note {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 10px;
        }

        .status-box {
            text-align: right;
        }

        .status-title {
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.28em;
            font-size: 9px;
            color: #6b7280;
        }

        .status-line {
            margin: 0;
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }

        .section-title {
            margin: 14px 0 0;
            padding: 8px 12px;
            background: #1d4ed8;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.28em;
            font-size: 10px;
            font-weight: 700;
            border-radius: 5px;
        }

        .summary-table,
        .detail-table,
        .line-table,
        .simple-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td,
        .detail-table td,
        .line-table th,
        .line-table td,
        .simple-table td {
            border: 1px solid #dbe2ea;
            vertical-align: top;
        }

        .summary-table td {
            width: 25%;
            padding: 10px 12px;
            height: 66px;
        }

        .summary-label,
        .detail-label,
        .table-head {
            text-transform: uppercase;
            letter-spacing: 0.22em;
            font-size: 8.5px;
            color: #6b7280;
        }

        .summary-value,
        .detail-value {
            margin-top: 6px;
            font-size: 13px;
            color: #111827;
            font-weight: 700;
            word-break: break-word;
        }

        .detail-table td {
            width: 50%;
            padding: 10px 12px;
        }

        .detail-value {
            font-weight: 600;
        }

        .section-box {
            margin-top: 12px;
            border: 1px solid #dbe2ea;
            border-radius: 12px;
            overflow: hidden;
        }

        .section-box .section-title {
            margin: 0;
            border-radius: 0;
        }

        .section-body {
            padding: 12px;
        }

        .line-table th,
        .line-table td {
            padding: 8px 8px;
        }

        .line-table th {
            background: #f8fafc;
            font-size: 10px;
            text-align: left;
        }

        .line-table td {
            font-size: 10.5px;
        }

        .muted {
            color: #6b7280;
        }

        .attachment-list {
            margin: 0;
            padding-left: 16px;
        }

        .attachment-list li {
            margin: 0 0 4px;
        }

        .two-column {
            width: 100%;
            border-collapse: collapse;
        }

        .two-column td {
            width: 50%;
            vertical-align: top;
            border: 1px solid #dbe2ea;
            padding: 10px 12px;
        }
    </style>
</head>
<body>
    <div class="page">
        <table class="header-table">
            <tr>
                <td class="header-cell" style="width: 62%;">
                    <div>
                        @if($companyLogo)
                            <span class="logo-wrap">
                                <img src="{{ $companyLogo }}" alt="{{ $companyName }}">
                            </span>
                        @endif
                        <div class="brand-block">
                            <p class="eyebrow">Official Finance Form</p>
                            <h1 class="brand-name">{{ $companyName }}</h1>
                            <p class="brand-subtitle">{{ $companyLegalName }} | {{ $moduleLabel }}</p>
                            <p class="brand-note">{{ $record->record_number ?: 'N/A' }} - {{ $record->record_title ?: 'N/A' }}</p>
                        </div>
                    </div>
                </td>
                <td class="header-cell" style="width: 38%;">
                    <div class="status-box">
                        <p class="status-title">Document Status</p>
                        <p class="status-line">Workflow: {{ $record->workflow_status ?: 'N/A' }}</p>
                        <p class="status-line">Approval: {{ $record->approval_status ?: 'N/A' }}</p>
                        <p class="status-line">Status: {{ $record->status ?: 'N/A' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="summary-table" style="margin-top: 12px;">
            @foreach(array_chunk($summaryCards, 4) as $row)
                <tr>
                    @foreach($row as $card)
                        <td>
                            <div class="summary-label">{{ $card['label'] }}</div>
                            <div class="summary-value">{{ $card['value'] }}</div>
                        </td>
                    @endforeach
                    @for($pad = count($row); $pad < 4; $pad++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>

        <div class="section-box">
            <div class="section-title">Details</div>
            <div class="section-body">
                @if(count($detailRows ?? []))
                    <table class="detail-table">
                        @foreach(array_chunk($detailRows, 2) as $pair)
                            <tr>
                                @foreach($pair as $detail)
                                    <td>
                                        <div class="detail-label">{{ $detail['label'] }}</div>
                                        <div class="detail-value">{{ $detail['value'] }}</div>
                                    </td>
                                @endforeach
                                @for($pad = count($pair); $pad < 2; $pad++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div class="muted">No additional details provided.</div>
                @endif
            </div>
        </div>

        @if($record->module_key === 'pr')
            <div class="section-box">
                <div class="section-title">Items / Cost Details</div>
                <div class="section-body">
                    @if(count($lineItems))
                        <table class="line-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 18%;">Item</th>
                                    <th style="width: 28%;">Description</th>
                                    <th style="width: 16%;">Category</th>
                                    <th style="width: 8%;">Qty</th>
                                    <th style="width: 13%;">Amount</th>
                                    <th style="width: 12%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lineItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['item'] }}</td>
                                        <td>{{ $item['description'] }}</td>
                                        <td>{{ $item['category'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ $item['amount'] }}</td>
                                        <td><strong>{{ $item['total'] }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="muted">No line items added yet.</div>
                    @endif

                    <div class="section-title" style="margin-top: 12px;">Cost Summary</div>
                    <table class="detail-table" style="margin-top: 0;">
                        @foreach(array_chunk($costSummary, 2) as $pair)
                            <tr>
                                @foreach($pair as $item)
                                    <td>
                                        <div class="detail-label">{{ $item['label'] }}</div>
                                        <div class="detail-value">{{ $item['value'] }}</div>
                                    </td>
                                @endforeach
                                @for($pad = count($pair); $pad < 2; $pad++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>

                    <div class="section-title" style="margin-top: 12px;">Purpose & Notes</div>
                    <table class="two-column">
                        <tr>
                            <td>
                                <div class="detail-label">Purpose / Justification</div>
                                <div class="detail-value">{{ data_get($record->data, 'purpose') ?: 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="detail-label">Remarks</div>
                                <div class="detail-value">{{ data_get($record->data, 'remarks') ?: 'N/A' }}</div>
                            </td>
                        </tr>
                    </table>

                    <div class="section-title" style="margin-top: 12px;">Account Allocation</div>
                    <table class="two-column">
                        <tr>
                            <td>
                                <div class="detail-label">Chart of Account</div>
                                <div class="detail-value">{{ $chartAccountLabel }}</div>
                            </td>
                            <td>
                                <div class="detail-label">Requestor</div>
                                <div class="detail-value">{{ data_get($record->data, 'requestor') ?: 'N/A' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        @if(count($attachments))
            <div class="section-box">
                <div class="section-title">Attachments</div>
                <div class="section-body">
                    <ul class="attachment-list">
                        @foreach($attachments as $attachment)
                            <li>{{ data_get($attachment, 'name') ?: data_get($attachment, 'path') ?: 'Attachment' }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
