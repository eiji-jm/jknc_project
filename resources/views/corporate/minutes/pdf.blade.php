<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $minutesDocumentTitle }}</title>
    <style>
        @page {
            size: A4;
            margin: 18mm 16mm 20mm;
        }

        body {
            font-family: Georgia, "Times New Roman", serif;
            color: #111827;
            font-size: 12.5pt;
            line-height: 1.75;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .center {
            text-align: center;
        }

        .company-name {
            font-size: 15pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .meta-line {
            margin-top: 4px;
            font-size: 11.5pt;
        }

        .title {
            margin-top: 30px;
            font-size: 14pt;
            font-weight: 700;
            text-align: center;
        }

        .subtitle {
            margin-top: 8px;
            text-align: center;
            font-size: 12pt;
        }

        .section {
            margin-top: 28px;
        }

        .attendance-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table td,
        .signature-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .minutes-body {
            margin-top: 16px;
            min-height: 280px;
        }

        .minutes-body p {
            margin: 0 0 14px;
        }

        .minutes-body ol,
        .minutes-body ul {
            margin: 0 0 14px 28px;
        }

        .line {
            border-bottom: 1px solid #94a3b8;
            min-height: 18px;
        }

        .signature-label {
            padding-top: 6px;
            text-align: center;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="center">
            <div class="company-name">JOHN KELLY &amp; COMPANY</div>
            <div class="meta-line">COMPANY REG. NO.: 2025120230900-02</div>
            <div class="meta-line">3RD FLOOR, UNIT 305 CEBU HOLDINGS CENTER CARDINAL ROSALES AVE., CEBU BUSINESS PARK HIPPODROMO, CEBU CITY, 6000</div>
            <div class="title">{{ $minute->type_of_meeting ?: 'Special' }} {{ $minute->governing_body ?: 'Directors' }} Meeting</div>
            <div class="subtitle">of</div>
            <div class="subtitle" style="font-weight:700;text-transform:uppercase;">JOHN KELLY &amp; COMPANY</div>
            <div class="subtitle" style="margin-top:20px;font-style:italic;">Held at</div>
            <div class="subtitle" style="font-style:italic;">{{ $minute->location ?: '________________' }}</div>
            <div class="subtitle" style="margin-top:14px;font-style:italic;">On</div>
            <div class="subtitle" style="font-style:italic;">{{ optional($minute->date_of_meeting)->format('F d, Y') ?: '________________' }}</div>
        </div>

        <div class="section">
            <div style="font-weight:700;">Attending:</div>
            <table class="attendance-table">
                <tbody>
                    <tr>
                        <td style="width:24%;font-weight:700;">Directors:</td>
                        <td style="width:40%;">{{ $minute->chairman ?: '________________' }}</td>
                        <td>President/Chairman</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ $minute->secretary ?: '________________' }}</td>
                        <td>Corporate Secretary</td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Other Attendee:</td>
                        <td>{{ $minute->uploaded_by ?: '________________' }}</td>
                        <td>Recorder</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div style="font-weight:700;">Minutes Proper:</div>
            <div class="minutes-body">{!! $minute->recording_notes ?: '<p>________________</p>' !!}</div>
        </div>

        <div class="section">
            <table class="signature-table">
                <tbody>
                    <tr>
                        <td style="width:20%;font-weight:700;">Prepared by:</td>
                        <td style="width:32%;padding-right:28px;"><div class="line">{{ $minute->secretary ?: '________________' }}</div></td>
                        <td style="width:16%;"></td>
                        <td style="width:32%;"><div class="line">{{ $minute->chairman ?: '________________' }}</div></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="signature-label">Corporate Secretary</td>
                        <td></td>
                        <td class="signature-label">President/Chairman</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
