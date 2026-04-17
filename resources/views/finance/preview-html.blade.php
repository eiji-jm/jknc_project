<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $record->record_number ?: $record->record_title ?: $moduleLabel }}</title>
    <style>
        @page {
            size: letter;
            margin: 0;
        }
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
            max-width: 816px;
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
        .note-card {
            border: 1px solid #fde68a;
            border-radius: 12px;
            background: #fffbeb;
            padding: 12px;
        }
        .note-card-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            font-size: 11px;
            color: #6b7280;
        }
        .note-card-author {
            font-weight: 700;
            color: #1f2937;
        }
        .note-card-label {
            margin-top: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: #b45309;
        }
        .note-card-body {
            margin-top: 8px;
            font-size: 13px;
            color: #111827;
            white-space: pre-line;
        }
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
        @php
            $noteAuthor = $record->approved_by ?: $record->submitted_by ?: $record->user ?: 'Finance Team';
            $noteDate = $record->approved_at ?: $record->submitted_at ?: $record->updated_at;
        @endphp
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

        @foreach($previewSections as $section)
            <div class="box">
                <div class="block">
                    @if(data_get($section, 'type') === 'supplier_send' && blank($record->supplier_completed_at))
                        <table class="details">
                            <tr>
                                <td colspan="2">
                                    <p class="label">Supplier Completion Dispatch</p>
                                    <p class="value">A completion form will be sent to the supplier email address.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="label">Email Address</p>
                                    <p class="value">{{ data_get($record->data, 'email_address') ?: 'N/A' }}</p>
                                </td>
                                <td>
                                    <p class="label">Completion Mode</p>
                                    <p class="value">Send to Supplier</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="label">Representative Full Name</p>
                                    <p class="value">{{ data_get($record->data, 'representative_full_name') ?: 'N/A' }}</p>
                                </td>
                                <td>
                                    <p class="label">Phone Number</p>
                                    <p class="value">{{ data_get($record->data, 'phone_number') ?: 'N/A' }}</p>
                                </td>
                            </tr>
                        </table>
                    @elseif(data_get($section, 'type') === 'line_items')
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
                    @elseif(data_get($section, 'type') === 'cost_summary')
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
                    @elseif(data_get($section, 'type') === 'notes')
                        @if(trim((string) $record->review_note) !== '')
                            <div class="note-card">
                                <div class="note-card-header">
                                    <div>
                                        <span class="note-card-author">{{ $noteAuthor }}</span>
                                    </div>
                                    <div>{{ $noteDate ? $noteDate->timezone('Asia/Manila')->format('M d, Y h:i A') : '' }}</div>
                                </div>
                                <div class="note-card-label">Review Note</div>
                                <div class="note-card-body">{{ $record->review_note }}</div>
                            </div>
                        @else
                            <p class="note">No review notes yet.</p>
                        @endif
                    @else
                        @php
                            $rows = data_get($section, 'rows', []);
                        @endphp
                        @if(count($rows))
                            <table class="details">
                                @foreach(array_chunk($rows, 2) as $pair)
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
                    @endif
                </div>
            </div>
        @endforeach

        @if(count($attachments))
            <div class="box">
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
