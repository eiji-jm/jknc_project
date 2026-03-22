<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Specimen Signature</title>
    <style>
        @page { margin: 8mm; }
        body {
            font-family: "Times New Roman", serif;
            font-size: 10px;
            color: #000;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }
        .doc {
            width: 100%;
            border: 1px solid #000;
        }
        .line {
            border-bottom: 1px solid #000;
            min-height: 12px;
        }
    </style>
</head>
<body>
@php
    $authenticationData = (array) ($data->authentication_data ?? []);
    $signatories = collect((array) ($data->signatories ?? []))
        ->map(fn ($entry) => is_array($entry) ? ($entry['name'] ?? null) : null)
        ->pad(6, null)
        ->take(6)
        ->values()
        ->all();
    $logo = public_path('images/jk-logo.png');
@endphp

<div class="doc">
    <table>
        <tr>
            <td width="20%" style="border-right:0; border-bottom:0; padding:4px 4px 2px 4px;">
                @if (file_exists($logo))
                    <img src="{{ $logo }}" style="height:54px;" alt="JK Logo">
                @else
                    John Kelly &amp; Company
                @endif
            </td>
            <td width="46%" style="border-left:0; border-right:0; border-bottom:0;">&nbsp;</td>
            <td width="34%" style="border-left:0; border-bottom:0; text-align:right; line-height:1.2; font-weight:bold;">
                AUTHORIZED SIGNATORY/SIGNATORY<br>
                (Sole / OPC / INDIVIDUAL)<br>
                SPECIMEN SIGNATURE CARD
            </td>
        </tr>

        <tr>
            <td style="border-top:0; border-right:0; padding-top:14px;">
                <div style="font-weight:bold;">BIF NO.</div>
                <div class="line">{{ $data->bif_no ?? '' }}</div>
            </td>
            <td style="border-top:0; border-left:0; border-right:0;">&nbsp;</td>
            <td style="border-top:0; border-left:0; padding-top:14px;">
                <table style="border:0;">
                    <tr>
                        <td width="24%" style="border:0; font-weight:bold;">DATE:</td>
                        <td width="76%" style="border:0;"><div class="line">{{ optional($data->date)->format('Y-m-d') }}</div></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="padding-top:8px; padding-bottom:8px;">
                <table style="border:0;">
                    <tr>
                        <td width="4%" style="border:0; text-align:center;">[{{ ($data->client_type ?? '') === 'new' ? 'x' : ' ' }}]</td>
                        <td width="16%" style="border:0;">New Client</td>
                        <td width="4%" style="border:0; text-align:center;">[{{ ($data->client_type ?? '') === 'existing' ? 'x' : ' ' }}]</td>
                        <td width="17%" style="border:0;">Existing Client</td>
                        <td width="4%" style="border:0; text-align:center;">[{{ ($data->client_type ?? '') === 'change' ? 'x' : ' ' }}]</td>
                        <td width="25%" style="border:0;">Change Information</td>
                        <td width="30%" style="border:0;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="padding:0;">
                <table style="border:0;">
                    <tr>
                        <td width="50%" style="border:0; border-right:1px solid #000; padding:0;">
                            <table style="border:0;">
                                <tr><td style="border:0;">BUSINESS NAME</td></tr>
                                <tr><td style="border:0;"><div class="line">{{ $data->business_name_left ?? '' }}</div></td></tr>
                                <tr><td style="border:0;">BUSINESS ACCOUNT NUMBER</td></tr>
                                <tr><td style="border:0;"><div class="line">{{ $data->account_number_left ?? '' }}</div></td></tr>
                                <tr>
                                    <td style="border:0; padding:0;">
                                        <table style="border:0;">
                                            <tr>
                                                <td width="56%" style="border:0;">SIGNATURE COMBINATION</td>
                                                <td width="44%" style="border:0;">SIGNATURE CLASS</td>
                                            </tr>
                                            <tr>
                                                <td style="border:0;"><div class="line">{{ $authenticationData['signature_combination'] ?? '' }}</div></td>
                                                <td style="border:0;"><div class="line">{{ $authenticationData['signature_class'] ?? '' }}</div></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" style="border:0; padding:0;">
                            <table style="border:0;">
                                <tr><td style="border:0;">BUSINESS NAME</td></tr>
                                <tr><td style="border:0;"><div class="line">{{ $data->business_name_right ?? '' }}</div></td></tr>
                                <tr><td style="border:0;">BUSINESS ACCOUNT NUMBER</td></tr>
                                <tr><td style="border:0;"><div class="line">{{ $data->account_number_right ?? '' }}</div></td></tr>
                                <tr>
                                    <td style="border:0; padding:0;">
                                        <table style="border:0;">
                                            <tr>
                                                <td width="56%" style="border:0;">SIGNATURE COMBINATION</td>
                                                <td width="44%" style="border:0;">SIGNATURE CLASS</td>
                                            </tr>
                                            <tr>
                                                <td style="border:0;"><div class="line">{{ $authenticationData['signature_combination'] ?? '' }}</div></td>
                                                <td style="border:0;"><div class="line">{{ $authenticationData['signature_class'] ?? '' }}</div></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="text-align:center; font-weight:bold; font-size:12px; padding-top:2px; padding-bottom:2px;">AUTHORIZE SIGNATORIES</td>
        </tr>

        <tr>
            <td colspan="3" style="padding:0;">
                <table style="border:0;">
                    <tr>
                        <td width="50%" style="border:0; border-right:1px solid #000; vertical-align:top; padding:0;">
                            <table style="border:0;">
                                <tr><td colspan="2" style="border:0;">CLIENT NAME:</td></tr>
                                <tr><td colspan="2" style="border:0;"><div class="line">{{ $authenticationData['left_client_name'] ?? '' }}</div></td></tr>
                                <tr>
                                    <td width="45%" style="border:0;">CIF NO.:</td>
                                    <td width="55%" style="border:0;">CIF Dated:</td>
                                </tr>
                                <tr>
                                    <td style="border:0;"><div class="line">{{ $authenticationData['left_cif_no'] ?? '' }}</div></td>
                                    <td style="border:0;"><div class="line">{{ $authenticationData['left_cif_dated'] ?? '' }}</div></td>
                                </tr>
                                @for ($i = 0; $i < 3; $i++)
                                    <tr><td colspan="2" style="border:0; padding-top:18px;">{{ $i + 1 }}</td></tr>
                                    <tr><td colspan="2" style="border:0;"><div class="line">{{ $signatories[$i] ?? '' }}</div></td></tr>
                                @endfor
                            </table>
                        </td>
                        <td width="50%" style="border:0; vertical-align:top; padding:0;">
                            <table style="border:0;">
                                <tr><td colspan="2" style="border:0;">CLIENT NAME:</td></tr>
                                <tr><td colspan="2" style="border:0;"><div class="line">{{ $authenticationData['right_client_name'] ?? '' }}</div></td></tr>
                                <tr>
                                    <td width="45%" style="border:0;">CIF NO.:</td>
                                    <td width="55%" style="border:0;">CIF Dated:</td>
                                </tr>
                                <tr>
                                    <td style="border:0;"><div class="line">{{ $authenticationData['right_cif_no'] ?? '' }}</div></td>
                                    <td style="border:0;"><div class="line">{{ $authenticationData['right_cif_dated'] ?? '' }}</div></td>
                                </tr>
                                @for ($i = 3; $i < 6; $i++)
                                    <tr><td colspan="2" style="border:0; padding-top:18px;">{{ $i - 2 }}</td></tr>
                                    <tr><td colspan="2" style="border:0;"><div class="line">{{ $signatories[$i] ?? '' }}</div></td></tr>
                                @endfor
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="padding:0;">
                <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed; border:1px solid #000;">
                    <tr>
                        <td width="60%" style="vertical-align:top; font-size:10px; line-height:1.15; text-align:justify; border-right:1px solid #000;">
                            By my/our signature(s) herein, I/we certify that the information and specimen signatures provided are true, correct, and duly authorized for use by JK&amp;C Inc. The above-listed individual(s) are the authorized signatory/ies of the business entity or the individual client, and JK&amp;C Inc. may rely on these specimen signatures for verification, documentation, and official transactions. I/we undertake to notify JK&amp;C Inc. in writing of any change to the authorized signatory/ies or their authority. In the absence of a Board Resolution, Secretary's Certificate, or Special Power of Attorney (SPA), the signature(s) appearing herein shall be presumed to be the true and rightful authorized signatory/ies of the business entity or individual client, unless otherwise notified in writing.
                        </td>
                        <td width="40%" style="vertical-align:top; padding:0;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed;">
                                <tr>
                                    <td style="border:0; text-align:center; font-weight:bold; line-height:1.2; padding-top:4px;">
                                        AUTHENTICATED BY CORPORATE SECRETARY / AUTHORIZED REPRESENTATIVE
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0; padding-top:10px;">
                                        Board Resolution / Secretary's Certificate / Special Power of Attorney (SPA) No.
                                        <div class="line">{{ $authenticationData['board_resolution_spa_no'] ?? '' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0; padding-top:8px;">
                                        Board Resolution / Secretary's Certificate / Special Power of Attorney (SPA) Date
                                        <div class="line">{{ $authenticationData['board_resolution_spa_date'] ?? '' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0; text-align:center; padding-top:8px;">
                                        <div style="margin:0 auto 4px auto; width:72%; border-bottom:1px solid #000;"></div>
                                        Signature over Printed Name
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:0; text-align:center; padding-top:2px;">
                                        <div style="margin:0 auto 4px auto; width:72%; border-bottom:1px solid #000;"></div>
                                        Date
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed;">
                                <tr>
                                    <td width="68%" style="border:0; text-align:center; vertical-align:bottom; padding-top:10px;">
                                        <div style="margin:0 auto 4px auto; width:70%; border-bottom:1px solid #000;"></div>
                                        Authorized Signatory's Signature over Printed Name
                                    </td>
                                    <td width="32%" style="border:0; text-align:center; vertical-align:bottom; padding-top:10px;">
                                        <div style="margin:0 auto 4px auto; width:78%; border-bottom:1px solid #000;"></div>
                                        Date
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding:0; border-left:1px solid #000; border-top:1px solid #000;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center; font-weight:bold; padding-top:3px; padding-bottom:3px; border-top:1px solid #000; border-bottom:1px solid #000;">FOR JKNC USE ONLY</td>
                    </tr>
                    <tr>
                        <td width="36%" style="vertical-align:top; border-right:1px solid #000;">
                            <div style="font-weight:bold;">PROCESSING INSTRUCTION (FOR JK&amp;C USE ONLY)</div>
                            <div style="min-height:78px; line-height:1.15;">{{ $authenticationData['processing_instruction'] ?? '' }}</div>
                        </td>
                        <td width="64%" style="vertical-align:top;">
                            <div style="font-weight:bold;">REMARKS:</div>
                            <div style="min-height:78px; line-height:1.15;">{{ $data->remarks ?? '' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:0; border-top:1px solid #000; border-bottom:1px solid #000;">
                            <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse; table-layout:fixed; border-left:0; border-right:0; border-bottom:1px solid #000;">
                                <tr>
                                    <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">SALES &amp; MARKETING</td>
                                    <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">PROCESSED BY / DATE</td>
                                    <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:1px solid #000;">FINANCE</td>
                                    <td width="25%" style="text-align:center; border-top:0; border-bottom:0; border-left:0; border-right:0;">SCANNED BY / DATE</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                        <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                        {{ $authenticationData['sales_marketing'] ?? '' }}<br>
                                        Signature over Printed Name
                                    </td>
                                    <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                        <div style="margin:0 auto 4px auto; width:72%; border-bottom:1px solid #000;"></div>
                                        {{ $authenticationData['processed_by'] ?? '' }}<br>
                                        Signature over Printed Name
                                    </td>
                                    <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:1px solid #000;">
                                        <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                        {{ $authenticationData['finance'] ?? '' }}<br>
                                        Signature over Printed Name
                                    </td>
                                    <td style="text-align:center; vertical-align:bottom; padding:10px 4px 6px 4px; border-top:0; border-bottom:1px solid #000; border-left:0; border-right:0;">
                                        <div style="margin:0 auto 4px auto; width:68%; border-bottom:1px solid #000;"></div>
                                        {{ $authenticationData['scanned_by'] ?? '' }}<br>
                                        Signature over Printed Name
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
