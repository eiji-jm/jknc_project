<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OBO Permit</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22mm 18mm 22mm 18mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #111;
            line-height: 1.4;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .center {
            text-align: center;
        }

        .header-line {
            margin: 0;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }

        .section {
            margin-top: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .value {
            border-bottom: 1px solid #000;
        }

        .paragraph {
            text-align: justify;
            margin-top: 12px;
        }

        .signature-block {
            margin-top: 60px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            width: 220px;
            border-top: 1px solid #000;
            margin: 0 auto 8px auto;
            height: 1px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="center">
            <p class="header-line">REPUBLIC OF THE PHILIPPINES</p>
            <p class="header-line">CITY OF CEBU</p>
            <p class="header-line">OFFICE OF THE BUILDING OFFICIAL</p>
            <div class="title">OBO PERMIT</div>
        </div>

        <div class="section">
            <table class="info-table">
                <tr>
                    <td class="label">PERMIT NO.:</td>
                    <td class="value">{{ $permit->permit_number }}</td>
                </tr>
                <tr>
                    <td class="label">DATE ISSUED:</td>
                    <td class="value">
                        {{ $permit->date_of_registration ? $permit->date_of_registration->format('F d, Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">VALID UNTIL:</td>
                    <td class="value">
                        {{ $permit->expiration_date_of_registration ? $permit->expiration_date_of_registration->format('F d, Y') : 'NO EXPIRATION' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <p>This is to certify that the establishment described below is granted the necessary clearance/permit subject to existing building rules and regulations:</p>

            <table class="info-table">
                <tr>
                    <td class="label">BUSINESS NAME:</td>
                    <td class="value">John Kelly &amp; Company</td>
                </tr>
                <tr>
                    <td class="label">OWNER / REPRESENTATIVE:</td>
                    <td class="value">John Kelly Abalde</td>
                </tr>
                <tr>
                    <td class="label">PROJECT / BUILDING ADDRESS:</td>
                    <td class="value">Cebu Holdings Center, Cebu City, 6000 Cebu</td>
                </tr>
                <tr>
                    <td class="label">TIN:</td>
                    <td class="value">{{ $permit->tin ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="section paragraph">
            <p>
                This permit is issued upon evaluation of the submitted requirements and finding
                that the establishment or project has complied with the applicable provisions of
                the National Building Code and related city ordinances, rules, and regulations.
            </p>
        </div>

        <div class="section">
            <p>
                Issued this
                {{ $permit->date_of_registration ? $permit->date_of_registration->format('jS') : '___' }}
                day of
                {{ $permit->date_of_registration ? $permit->date_of_registration->format('F, Y') : '____________' }}
                at Cebu City, Philippines.
            </p>
        </div>

        <div class="signature-block">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div>Building Official</div>
                        <div>Approving Authority</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div>Jasper Bulac</div>
                        <div>Authorized Representative</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>