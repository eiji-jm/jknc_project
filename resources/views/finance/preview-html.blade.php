<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $record->record_number ?: $record->record_title ?: $moduleLabel }}</title>
    <style>
        :root {
            --blue: #1d4ed8;
            --border: #dbe2ea;
            --muted: #6b7280;
            --bg: #f8fafc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #eef2ff;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: flex-end;
            padding: 10px 12px;
            background: rgba(238,242,255,.95);
            border-bottom: 1px solid #d1d5db;
        }
        .toolbar button {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #1f2937;
            padding: 8px 14px;
            border-radius: 999px;
            cursor: pointer;
        }
        .page {
            max-width: 1120px;
            margin: 18px auto;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 18px;
            overflow: hidden;
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(90deg, #fff 0%, #fff 75%, #eff6ff 100%);
        }
        .brand {
            display: flex;
            gap: 14px;
            align-items: center;
        }
        .logo {
            width: 86px;
            height: 86px;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .logo img {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .eyebrow {
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: .24em;
            font-size: 10px;
            color: var(--muted);
        }
        .brand h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.1;
        }
        .brand .sub {
            margin: 5px 0 0;
            font-size: 12px;
            font-weight: 700;
            color: var(--blue);
        }
        .brand .meta {
            margin: 4px 0 0;
            font-size: 11px;
            color: var(--muted);
        }
        .status {
            text-align: right;
        }
        .status .title {
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: .24em;
            font-size: 10px;
            color: var(--muted);
        }
        .status p {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }
        .section-title {
            margin: 0;
            background: var(--blue);
            color: #fff;
            padding: 10px 16px;
            text-transform: uppercase;
            letter-spacing: .24em;
            font-size: 11px;
            font-weight: 700;
        }
        .summary,
        .details,
        .simple-table,
        .line-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td,
        .details td,
        .two-col td,
        .line-table th,
        .line-table td {
            border: 1px solid var(--border);
            vertical-align: top;
            padding: 10px 12px;
        }
        .summary td { width: 25%; height: 70px; }
        .label {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: var(--muted);
            font-size: 10px;
        }
        .value {
            margin: 6px 0 0;
            font-weight: 700;
            font-size: 14px;
            word-break: break-word;
        }
        .block {
            padding: 14px;
        }
        .box {
            margin: 14px;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }
        .note {
            color: var(--muted);
            font-size: 12px;
        }
        .attachments {
            padding: 14px;
        }
        .attachments a {
            display: block;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            text-decoration: none;
            color: #111827;
            margin-bottom: 10px;
        }
        .attachments a:hover { background: #f9fafb; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; border: 0; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="page">
        <div class="header">
            <div class="brand">
                @if($companyLogo)
                    <div class="logo"><img src="{{ $companyLogo }}" alt="{{ $companyName }}"></div>
                @endif
                <div>
                    <p class="eyebrow">Official Finance Form</p>
                    <h1>{{ $companyName }}</h1>
                    <p class="sub">{{ $companyLegalName }} | {{ $moduleLabel }}</p>
                    <p class="meta">{{ $record->record_number ?: 'N/A' }} - {{ $record->record_title ?: 'N/A' }}</p>
                </div>
            </div>

            <div class="status">
                <p class="title">Document Status</p>
                <p>Workflow: {{ $record->workflow_status ?: 'N/A' }}</p>
                <p>Approval: {{ $record->approval_status ?: 'N/A' }}</p>
                <p>Status: {{ $record->status ?: 'N/A' }}</p>
            </div>
        </div>

        <table class="summary">
            @foreach(array_chunk($summaryCards, 4) as $row)
                <tr>
                    @foreach($row as $card)
                        <td>
                            <p class="label">{{ $card['label'] }}</p>
                            <p class="value">{{ $card['value'] }}</p>
                        </td>
                    @endforeach
                    @for($i = count($row); $i < 4; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>

        <div class="box">
            <div class="section-title">Details</div>
            <div class="block">
                @if(count($detailRows))
                    <table class="details">
                        @foreach(array_chunk($detailRows, 2) as $pair)
                            <tr>
                                @foreach($pair as $detail)
                                    <td>
                                        <p class="label">{{ $detail['label'] }}</p>
                                        <p class="value">{{ $detail['value'] }}</p>
                                    </td>
                                @endforeach
                                @for($i = count($pair); $i < 2; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p class="note">No additional details provided.</p>
                @endif
            </div>
        </div>

        @if($record->module_key === 'pr')
            <div class="box">
                <div class="section-title">Items / Cost Details</div>
                <div class="block">
                    @if(count($lineItems))
                        <table class="line-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Total</th>
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
                        <p class="note">No line items added yet.</p>
                    @endif

                    <div class="section-title" style="margin-top: 14px;">Cost Summary</div>
                    <table class="details">
                        @foreach(array_chunk($costSummary, 2) as $pair)
                            <tr>
                                @foreach($pair as $item)
                                    <td>
                                        <p class="label">{{ $item['label'] }}</p>
                                        <p class="value">{{ $item['value'] }}</p>
                                    </td>
                                @endforeach
                                @for($i = count($pair); $i < 2; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>

                    <div class="section-title" style="margin-top: 14px;">Purpose & Notes</div>
                    <table class="details">
                        <tr>
                            <td>
                                <p class="label">Purpose / Justification</p>
                                <p class="value">{{ data_get($record->data, 'purpose') ?: 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="label">Remarks</p>
                                <p class="value">{{ data_get($record->data, 'remarks') ?: 'N/A' }}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        @if(count($attachments))
            <div class="box">
                <div class="section-title">Attachments</div>
                <div class="attachments">
                    @foreach($attachments as $attachment)
                        <a href="{{ data_get($attachment, 'path') }}" target="_blank">
                            {{ data_get($attachment, 'name') ?: data_get($attachment, 'path') ?: 'Attachment' }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
