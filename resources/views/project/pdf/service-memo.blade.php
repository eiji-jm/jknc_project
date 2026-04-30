<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Memo</title>
    <style>
        @page { size: letter portrait; margin: 6mm; }
        html, body { width: 100%; min-height: 100%; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: #000; font-size: 8.8pt; line-height: 1.18; }
        .sheet { padding: 8mm; box-sizing: border-box; }
        .center { text-align: center; }
        .title { font-family: Georgia, "Times New Roman", serif; font-size: 13.5pt; font-weight: 700; letter-spacing: 0.04em; }
        .code { font-family: Georgia, "Times New Roman", serif; font-size: 6.8pt; font-weight: 700; margin-top: 2px; letter-spacing: 0.08em; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; font-family: Georgia, "Times New Roman", serif; font-size: 8.8pt; font-weight: 700; }
        .copy { margin-top: 10px; text-align: justify; }
        .copy p { margin: 0 0 6px; }
        .signatures { margin-top: 14px; page-break-inside: avoid; break-inside: avoid; }
        .signatures tr { page-break-inside: avoid; break-inside: avoid; }
        .sign-box { min-height: 52px; text-align: center; vertical-align: middle; font-size: 8.4pt; font-weight: 700; padding: 4px 3px; }
        .footer { margin-top: 12px; font-family: Georgia, "Times New Roman", serif; font-size: 5.8pt; line-height: 1.1; }
        .footer p { margin: 0; }
        .no-wrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="center">
            <div class="title">{{ $serviceMemo['title'] }}</div>
            <div class="code">{{ $serviceMemo['form_code'] }}</div>
        </div>

        <table style="margin-top: 12px;">
            <tr>
                <td>Date Issued: {{ $serviceMemo['date_issued'] }}</td>
                <td>Engagement Type: {{ $serviceMemo['engagement_type'] }}</td>
            </tr>
            <tr>
                <td>START Ref No: {{ $serviceMemo['start_ref_no'] }}</td>
                <td>START Cleared Date: {{ $serviceMemo['start_cleared_date'] }}</td>
            </tr>
            <tr>
                <td>Condeal Reference No.: {{ $serviceMemo['condeal_reference_no'] }}</td>
                <td>Client Name: {{ $serviceMemo['client_name'] }}</td>
            </tr>
            <tr>
                <td>Business Name: {{ $serviceMemo['business_name'] }}</td>
                <td>RSAT / SOW Ref No.: {{ $serviceMemo['engagement_reference_no'] }}</td>
            </tr>
            <tr>
                <td>Approved Start Date: {{ $serviceMemo['approved_start_date'] }}</td>
                <td>Target Completion Date: {{ $serviceMemo['target_completion_date'] }}</td>
            </tr>
            <tr>
                <td>RSAT Template: {{ $serviceMemo['rsat_template'] }}</td>
                <td>SOW Template: {{ $serviceMemo['sow_template'] }}</td>
            </tr>
        </table>

        <div class="copy">
            <p>Based on the completed <strong>CASA (START) Clearance Status</strong>, approved engagement requirements, and the details stated above, this Service Memo serves as the formal authority for <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong> personnel to commence and execute the approved engagement.</p>
            <p>Accordingly, the assigned personnel are hereby directed to proceed with the approved engagement, including coordination, execution, processing, drafting, implementation, reporting, and delivery of services in accordance with the approved scope, timelines, templates, and company standards stated above.</p>
            <p>All personnel shall act strictly within the approved authority. Any change in scope, delay, issue, or additional request must be elevated for proper approval.</p>
        </div>

        <table class="signatures" style="margin-top: 14px;">
            <tr>
                <td class="sign-box">Name, Signature and Date | Lead Consultant<br>{{ $serviceMemo['lead_consultant'] }}</td>
                <td class="sign-box">Name, Signature and Date | Associate<br>{{ $serviceMemo['associate'] }}</td>
            </tr>
            <tr>
                <td class="sign-box">Name, Signature and Date | Sales and Marketing<br>{{ $serviceMemo['sales_marketing'] }}</td>
                <td class="sign-box">Name, Signature and Date | Finance<br>{{ $serviceMemo['finance'] }}</td>
            </tr>
            <tr>
                <td colspan="2" class="sign-box">Name, Signature and Date | Office of the President<br>{{ $serviceMemo['office_of_president'] }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>John Kelly &amp; Company</p>
            <p>3F, Cebu Holdings Center, Cebu Business Park, Cebu City, Philippines 6000</p>
            <p>Email: start@jknc.io • Website: jknc.io • Phone: 0995-535-8729</p>
        </div>
    </div>
</body>
</html>
