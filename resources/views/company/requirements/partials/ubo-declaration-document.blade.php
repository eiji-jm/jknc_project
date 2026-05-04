@php
    $logoPath = asset('images/imaglogo.png');
    $ubos = collect($doc['ubos'] ?? [])->values();
    $primaryUbo = $ubos->first() ?? [
        'full_name' => '',
        'address' => '',
        'nationality' => '',
        'date_of_birth' => '',
        'tin' => '',
        'position' => '',
    ];

    $addressLine1 = (string) ($primaryUbo['address'] ?? '');
    $addressLine2 = '';

    if ($addressLine1 !== '' && str_contains($addressLine1, ',')) {
        $parts = preg_split('/\s*,\s*/', $addressLine1, 2);
        $addressLine1 = $parts[0] ?? $addressLine1;
        $addressLine2 = $parts[1] ?? '';
    }
@endphp

<div style="font-family: 'Times New Roman', Georgia, serif; color: #000; font-size: 12px; line-height: 1.2;">
    <div style="max-width: 760px; margin: 0 auto; padding: 10px 8px 0;">
        <div style="display: grid; grid-template-columns: 250px minmax(0, 1fr); align-items: start; gap: 16px; margin-bottom: 18px;">
            <div>
                <img src="{{ $logoPath }}" alt="John Kelly and Company" style="max-width: 180px; height: auto; display: block;">
            </div>
            <div style="padding-top: 14px; text-align: left;">
                <div style="font-family: Arial, sans-serif; font-size: 18px; font-weight: 700; text-transform: uppercase;">Ultimate Beneficial Owner</div>
                <div style="margin-top: 8px; font-family: Arial, sans-serif; font-size: 10px;">CASA-F-009-v1.0-03.16.26</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 26px; font-size: 11px;">
            <div style="display: flex; align-items: center; gap: 4px;">
                <span>CIF NO.</span>
                <span style="display: inline-block; min-width: 78px; border-bottom: 1px solid #000; height: 12px;"></span>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 4px;">
                <span style="display: inline-block; width: 10px; height: 10px; border: 1px solid #000;"></span>
                <span>New Client</span>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 4px;">
                <span style="display: inline-block; width: 10px; height: 10px; border: 1px solid #000;"></span>
                <span>Existing Client</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px; margin-left: auto;">
                <span>Date</span>
                <span style="display: inline-block; min-width: 94px; border-bottom: 1px solid #000; height: 12px;">
                    <span data-field="declaration_month">{{ $doc['declaration_month'] }}</span>
                    <span data-field="declaration_day">{{ $doc['declaration_day'] }}</span>
                    <span data-field="declaration_year">{{ $doc['declaration_year'] }}</span>
                </span>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; table-layout: fixed; border: 2px solid #000; font-size: 11px;">
            <tr>
                <td colspan="2" style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">CORPORATION / BUSINESS NAME</div>
                    <div style="padding-top: 8px; min-height: 20px;" data-field="company_name">{{ $doc['company_name'] }}</div>
                </td>
                <td style="width: 148px; border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">CIF NO. <span style="font-style: italic; font-weight: 400;">(For JKNC Use Only)</span></div>
                    <div style="padding-top: 8px; min-height: 20px;"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">NAME <span style="font-style: italic; font-weight: 400;">(Last Name, First Name, Middle Name)</span></div>
                    <div style="padding-top: 8px; min-height: 20px;" data-field="ubos_0_full_name">{{ $primaryUbo['full_name'] }}</div>
                </td>
                <td style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">PLACE OF BIRTH / GENDER</div>
                    <div style="padding-top: 8px; min-height: 20px;"></div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">ADDRESS LINE 1 <span style="font-style: italic; font-weight: 400;">(House No. / Building Name / Block and Lot No. / Street)</span></div>
                    <div style="padding-top: 8px; min-height: 28px;" data-field="ubos_0_address">{{ $addressLine1 }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">ADDRESS LINE 2 <span style="font-style: italic; font-weight: 400;">(Barangay / District / Village / Town / City / Province)</span></div>
                    <div style="padding-top: 8px; min-height: 28px;">{{ $addressLine2 }}</div>
                </td>
            </tr>
            <tr>
                <td style="width: 170px; border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">CONTACT NO.:</div>
                    <div style="padding-top: 8px; min-height: 18px;"></div>
                </td>
                <td style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">E-MAIL ADDRESS:</div>
                    <div style="padding-top: 8px; min-height: 18px;"></div>
                </td>
                <td style="width: 128px; border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">NATIONALITY:</div>
                    <div style="padding-top: 8px; min-height: 18px;" data-field="ubos_0_nationality">{{ $primaryUbo['nationality'] }}</div>
                </td>
            </tr>
            <tr>
                <td style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">OCCUPATION:</div>
                    <div style="padding-top: 8px; min-height: 28px;" data-field="ubos_0_position">{{ $primaryUbo['position'] }}</div>
                </td>
                <td colspan="2" style="border: 2px solid #000; padding: 4px 6px; vertical-align: top;">
                    <div style="font-weight: 700;">NATURE OF WORK</div>
                    <div style="padding-top: 8px; min-height: 28px;"></div>
                </td>
            </tr>
            <tr>
                <td style="border: 2px solid #000; padding: 6px 6px 4px; vertical-align: bottom; text-align: center; font-style: italic;">
                    Record Custodian ( Name and Signature)
                </td>
                <td style="border: 2px solid #000; padding: 4px 6px;">
                    <div>Date Recorded:</div>
                    <div style="padding-top: 8px; min-height: 14px;"></div>
                </td>
                <td style="border: 2px solid #000; padding: 4px 6px;">
                    <div>Date Signed :</div>
                    <div style="padding-top: 8px; min-height: 14px;"></div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 18px; border: 2px solid #000; background: #163d7a; padding: 0;"></td>
            </tr>
        </table>

        <div style="margin-top: 24px; font-style: italic; font-size: 12px;">(Specimen Signature)</div>
        <div style="margin-top: 24px; font-size: 12px;">
            <div style="margin-bottom: 24px;">1.&nbsp;&nbsp;<span style="display: inline-block; width: 220px; border-bottom: 1px solid #000; vertical-align: middle;"></span></div>
            <div style="margin-bottom: 24px;">2.&nbsp;&nbsp;<span style="display: inline-block; width: 220px; border-bottom: 1px solid #000; vertical-align: middle;"></span></div>
            <div>3.&nbsp;&nbsp;<span style="display: inline-block; width: 220px; border-bottom: 1px solid #000; vertical-align: middle;"></span></div>
        </div>
    </div>
</div>
