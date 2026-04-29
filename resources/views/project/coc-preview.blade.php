<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $coc['title'] ?? 'Certificate of Completion' }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 16mm 14mm 16mm; }
        body {
            margin: 0;
            color: #000;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.1;
            background: #fff;
        }
        .doc {
            width: 100%;
            box-sizing: border-box;
        }
        .title {
            text-align: center;
            font-size: 18pt;
            font-weight: 700;
            margin-top: 4px;
            line-height: 1.05;
        }
        .code {
            text-align: center;
            font-size: 7.8pt;
            font-weight: 700;
            margin-bottom: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .meta td,
        .signatures td {
            border: 1px solid #000;
            vertical-align: top;
        }
        .meta td {
            padding: 4px 6px;
            font-size: 10.8pt;
            font-weight: 700;
        }
        .value {
            font-weight: 400;
        }
        .copy {
            margin-top: 10px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.6pt;
        }
        .copy p {
            margin: 0 0 10px;
            text-align: justify;
        }
        .copy strong {
            font-weight: 700;
        }
        .signatures {
            margin-top: 10px;
        }
        .sign-head {
            padding: 6px 8px;
            font-size: 10.8pt;
            font-weight: 700;
        }
        .sign-box {
            height: 92px;
            text-align: center;
            vertical-align: middle !important;
            font-size: 10.4pt;
            font-weight: 700;
            padding: 6px 8px;
        }
        .sign-sub {
            display: block;
            font-weight: 700;
        }
        .sign-name {
            display: block;
            margin-top: 8px;
            font-weight: 400;
        }
        .footer {
            margin-top: 22px;
            font-size: 5.7pt;
            line-height: 1.25;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <div class="doc">
        <div class="title">{{ $coc['title'] ?? 'CERTIFICATE OF COMPLETION' }}</div>
        <div class="code">{{ $coc['form_code'] ?? '' }}</div>

        <table class="meta">
            <tr>
                <td>Date Issued: <span class="value">{{ $coc['date_issued'] ?? '-' }}</span></td>
                <td>COC No.: <span class="value">{{ $coc['coc_no'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>NTP No.: <span class="value">{{ $coc['ntp_no'] ?? '-' }}</span></td>
                <td>Engagement Type: <span class="value">{{ $coc['engagement_type'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Condeal Reference No.: <span class="value">{{ $coc['condeal_reference_no'] ?? '-' }}</span></td>
                <td>Client Name: <span class="value">{{ $coc['client_name'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Business Name: <span class="value">{{ $coc['business_name'] ?? '-' }}</span></td>
                <td>{{ $coc['engagement_reference_label'] ?? 'RSAT / SOW Ref No.:' }} <span class="value">{{ $coc['engagement_reference_no'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Approved Start Date: <span class="value">{{ $coc['approved_start_date'] ?? '-' }}</span></td>
                <td>Target Completion Date: <span class="value">{{ $coc['target_completion_date'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Approved Start Date: <span class="value">{{ $coc['approved_start_date'] ?? '-' }}</span></td>
                <td>Actual Completion Date: <span class="value">{{ $coc['actual_completion_date'] ?? '-' }}</span></td>
            </tr>
        </table>

        <div class="copy">
            <p>This is to certify that <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong> has completed and delivered the agreed services, outputs, and deliverables for the above engagement in accordance with the approved scope, timelines, and applicable terms, subject only to any items expressly noted in writing as pending, excluded, or separate.</p>
            <p>The Client acknowledges that the services under the approved RSAT, SOW, Notice to Proceed, and related engagement documents have been rendered, received, and completed to the extent applicable.</p>
            <p>Upon signing this Certificate of Completion, the engagement shall be deemed completed and closed, and <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong> shall have no further obligation on the completed scope except those separately agreed in writing.</p>
            <p>The Client further confirms acceptance of the completed work and releases <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong>, its consultants, associates, officers, and representatives from further claims or liabilities arising from the completed and accepted scope, except obligations expressly retained in writing.</p>
            <p>Any further assistance, revision, continuation, or additional service shall require separate approval and may be subject to corresponding fees.</p>
            <p>I, the undersigned <strong>Client and/or duly authorized representative</strong>, hereby confirm receipt, acceptance, and completion of the engagement stated above.</p>
        </div>

        <table class="signatures">
            <tr>
                <td class="sign-head">FOR THE CLIENT</td>
                <td class="sign-head">FOR JOHN KELLY &amp; COMPANY</td>
            </tr>
            <tr>
                <td class="sign-box" rowspan="3">
                    <span class="sign-sub">Name, Signature and Date</span>
                    Authorized Representative
                    <br>
                    Name/Client
                    <span class="sign-name">{{ $coc['client_confirmation_name'] ?? '' }}</span>
                </td>
                <td class="sign-box">
                    <span class="sign-sub">Name, Signature and Date</span>
                    Lead Consultant
                    <span class="sign-name">{{ $coc['lead_consultant'] ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td class="sign-box"></td>
            </tr>
            <tr>
                <td class="sign-box">
                    <span class="sign-sub">Name, Signature and Date</span>
                    Associate
                    <span class="sign-name">{{ $coc['associate'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <div class="footer">
            John Kelly &amp; Company
            <br>
            3F, Cebu Holdings Center, Cebu Business Park, Cebu City, Philippines 6000
            <br>
            Email: start@jknc.io • Website: jknc.io • Phone: 0995-535-8729
            <br>
            Page 1 of 1
        </div>
    </div>
</body>
</html>
