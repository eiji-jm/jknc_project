@php
    $formDate = optional($start?->form_date ?? $start?->created_at)->format('m/d/Y');
    $dateStarted = optional($start?->date_started)->format('m/d/Y');
    $dateCompleted = optional($start?->date_completed)->format('m/d/Y');
    $startKycSole = collect($startKycSole ?? []);
    $startKycJuridical = collect($startKycJuridical ?? []);
    $isSoleKyc = $startKycOrganization === 'sole_proprietorship'
        || ($startKycOrganization === 'unknown' && $startKycSole->isNotEmpty() && $startKycJuridical->isEmpty());
    $activeKycItems = $isSoleKyc ? $startKycSole : $startKycJuridical;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>START Form</title>
    <style>
        @page { size: A4 landscape; margin: 6mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 8px; margin: 0; }
        .sheet { border: 2px solid #163b7a; }
        .header { padding: 8px 10px 6px; }
        .title { text-align: center; font-family: "Times New Roman", serif; font-weight: 700; font-size: 19px; line-height: 1.05; }
        .code { text-align: center; font-size: 7px; margin-top: 2px; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .grid td { vertical-align: top; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 1px 4px; }
        .meta-label { width: 88px; font-size: 7px; text-transform: uppercase; }
        .meta-value { border-bottom: 1px solid #111827; min-height: 11px; font-size: 8px; }
        .section-title { background: #163b7a; color: #fff; text-align: center; padding: 4px; font-family: "Times New Roman", serif; font-weight: 700; font-size: 12px; }
        .subhead { background: #eef4ff; text-align: center; border-top: 1px solid #111827; border-bottom: 1px solid #111827; padding: 3px; font-size: 8px; font-weight: 700; }
        .kyc-list { padding: 6px 10px; min-height: 115px; }
        .kyc-row { margin: 1px 0; }
        .box { display: inline-block; width: 8px; }
        table.matrix { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .matrix th, .matrix td { border: 1px solid #111827; padding: 3px 4px; vertical-align: top; }
        .matrix th { background: #eef4ff; font-size: 7px; }
        .center { text-align: center; }
        .small { font-size: 7px; }
        .tiny { font-size: 6.5px; }
        .nowrap { white-space: nowrap; }
        .clearance-grid { width: 100%; border-collapse: collapse; }
        .clearance-grid td { border: 1px solid #111827; padding: 4px; vertical-align: top; }
        .sig-note { text-align: center; font-size: 6.5px; margin-top: 5px; }
        .line { border-bottom: 1px solid #111827; min-height: 10px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div class="title">SERVICE TASK ACTIVATION AND<br>ROUTING TRACKER (START)</div>
            <div class="code">CASA-F-010-v1.0-03.16.26</div>

            <table class="grid">
                <tr>
                    <td style="width:34%;">
                        <table class="meta-table">
                            <tr><td class="meta-label">Client Name:</td><td class="meta-value">{{ $project->client_name ?: $contactName ?: '' }}</td></tr>
                            <tr><td class="meta-label">Business Name:</td><td class="meta-value">{{ $project->business_name ?: '' }}</td></tr>
                            <tr><td class="meta-label">Condeal Ref No.</td><td class="meta-value">{{ $project->deal?->deal_code ?: '' }}</td></tr>
                            <tr><td class="meta-label">Service Area</td><td class="meta-value">{{ $project->service_area ?: '' }}</td></tr>
                        </table>
                    </td>
                    <td style="width:33%;">
                        <table class="meta-table">
                            <tr><td class="meta-label">Product:</td><td class="meta-value">{{ $project->products ?: '' }}</td></tr>
                            <tr><td class="meta-label">Services:</td><td class="meta-value">{{ $project->services ?: '' }}</td></tr>
                            <tr><td class="meta-label">Engagement Type:</td><td class="meta-value">{{ $project->engagement_type ?: '' }}</td></tr>
                        </table>
                    </td>
                    <td style="width:33%;">
                        <table class="meta-table">
                            <tr><td class="meta-label">Date:</td><td class="meta-value">{{ $formDate }}</td></tr>
                            <tr><td class="meta-label">Date Started:</td><td class="meta-value">{{ $dateStarted }}</td></tr>
                            <tr><td class="meta-label">Date Completed:</td><td class="meta-value">{{ $dateCompleted }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Client Due Diligence (KYC) Documents</div>
        <div class="subhead">
            {{ $isSoleKyc ? 'SOLE / NATURAL PERSON / INDIVIDUAL' : 'JURIDICAL ENTITY (Corporation / OPC / Partnership / Cooperative)' }}
        </div>
        <div class="kyc-list">
            @foreach ($activeKycItems as $item)
                <div class="kyc-row"><span class="box">{{ ($item['status'] ?? 'pending') === 'provided' ? '[x]' : '[ ]' }}</span> {{ $item['label'] ?? '' }}</div>
            @endforeach
        </div>

        <div class="section-title">Engagement-Specific Requirements</div>
        <table class="matrix">
            <thead>
                <tr>
                    <th style="width:4%;">No.</th>
                    <th style="width:16%;">Requirement / Document</th>
                    <th style="width:13%;">Notes</th>
                    <th style="width:15%;">Purpose</th>
                    <th style="width:10%;">Provided By</th>
                    <th style="width:10%;">Submitted To</th>
                    <th style="width:12%;">Assigned To</th>
                    <th style="width:20%;">Timeline</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($startReqs) as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item['requirement'] ?? '' }}</td>
                        <td>{{ $item['notes'] ?? '' }}</td>
                        <td>{{ $item['purpose'] ?? '' }}</td>
                        <td>{{ $item['provided_by'] ?? '' }}</td>
                        <td>{{ $item['submitted_to'] ?? '' }}</td>
                        <td>{{ $item['assigned_to'] ?? '' }}</td>
                        <td>{{ $item['timeline'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="matrix">
            <thead>
                <tr>
                    <th style="width:26%;">Requirement</th>
                    <th style="width:26%;">Responsible Person</th>
                    <th style="width:24%;">Name and Signature</th>
                    <th style="width:24%;">Date &amp; Time Done</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($startApprovalSteps) as $item)
                    <tr>
                        <td>{{ $item['requirement'] ?? '' }}</td>
                        <td>{{ $item['responsible_person'] ?? '' }}</td>
                        <td>{{ $item['name_and_signature'] ?? '' }}</td>
                        <td>{{ $item['date_time_done'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Clearance</div>
        <table class="clearance-grid">
            <tr class="center small" style="font-weight:700; background:#eef4ff;">
                <td>Assigned to Regular/Project Team Lead</td>
                <td>Lead Consultant Confirmed</td>
                <td>Lead Associate Assigned</td>
                <td>Sales &amp; Marketing</td>
            </tr>
            <tr>
                <td>
                    <div class="line">{{ $startClearance['assigned_team_lead'] ?? '' }}</div>
                    <div class="sig-note">Signature over Printed Name</div>
                    <div class="line">{{ $startClearance['assigned_team_lead_signature'] ?? '' }}</div>
                </td>
                <td>
                    <div class="line">{{ $startClearance['lead_consultant_confirmed'] ?? '' }}</div>
                    <div class="sig-note">Signature over Printed Name</div>
                    <div class="line">{{ $startClearance['lead_consultant_signature'] ?? '' }}</div>
                </td>
                <td>
                    <div class="line">{{ $startClearance['lead_associate_assigned'] ?? '' }}</div>
                    <div class="sig-note">Signature over Printed Name</div>
                    <div class="line">{{ $startClearance['lead_associate_signature'] ?? '' }}</div>
                </td>
                <td>
                    <div class="line">{{ $startClearance['sales_marketing'] ?? '' }}</div>
                    <div class="sig-note">Signature over Printed Name</div>
                    <div class="line">{{ $startClearance['sales_marketing_signature'] ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="center tiny">Record Custodian (Name and Signature)</div>
                    <div class="line">{{ $startClearance['record_custodian_name'] ?? '' }}</div>
                    <div class="line" style="margin-top:4px;">{{ $startClearance['record_custodian_signature'] ?? '' }}</div>
                </td>
                <td class="nowrap">
                    <div><strong>Date Recorded:</strong> {{ !empty($startClearance['date_recorded']) ? \Illuminate\Support\Carbon::parse($startClearance['date_recorded'])->format('m/d/Y') : '' }}</div>
                    <div style="margin-top:8px;"><strong>Date Signed:</strong> {{ !empty($startClearance['date_signed']) ? \Illuminate\Support\Carbon::parse($startClearance['date_signed'])->format('m/d/Y') : '' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
