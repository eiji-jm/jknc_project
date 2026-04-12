<div style="font-family: Georgia, 'Times New Roman', serif; font-size: 14px; line-height: 1.7; color: #111827;">
    <div style="text-align: center; font-size: 16px;">REPUBLIC OF THE PHILIPPINES</div>
    <div style="text-align: center;">)</div>
    <div style="margin-top: 4px; text-align: center; font-size: 18px; font-weight: 700; text-transform: uppercase;">Secretary's Certificate</div>

    <div style="margin-top: 24px; text-align: justify;">
        <p style="margin: 0 0 16px;">
            I,
            <span data-field="affiant_name">{{ $doc['affiant_name'] }}</span>,
            of legal age,
            <span data-field="affiant_age">{{ $doc['affiant_age'] }}</span>,
            with address at
            <span data-field="affiant_address">{{ $doc['affiant_address'] }}</span>,
            after having been sworn in accordance with law, hereby depose and state that:
        </p>

        <p style="margin: 0 0 16px;">
            I am the duly designated Corporate Secretary of
            <span data-field="corporation_name">{{ $doc['corporation_name'] }}</span>.
            (SEC Registration No.:
            <span data-field="sec_registration_no">{{ $doc['sec_registration_no'] }}</span>),
            a corporation duly organized and existing under the laws of the Republic of the Philippines, with principal office address at
            <span data-field="principal_office_address">{{ $doc['principal_office_address'] }}</span>.
        </p>

        <p style="margin: 0 0 16px;">
            As Corporate Secretary, I am the custodian of the corporate books and records of the Corporation, including the Minutes and Resolutions of its Board of Directors.
        </p>

        <p style="margin: 0 0 16px;">
            That pursuant to Board Resolution No.
            <span data-field="board_resolution_no">{{ $doc['board_resolution_no'] }}</span>,
            approved during the meeting of the Board of Directors held on
            <span data-field="board_meeting_date">{{ $doc['board_meeting_date'] }}</span>,
            the Board resolved as follows:
        </p>

        <p style="margin: 0 0 16px;">
            <strong>RESOLVED,</strong> to appoint and designate the following as the duly authorized representative of the Corporation to act for and on its behalf in connection with its engagement with <strong>John Kelly &amp; Company (JK&amp;C Inc.):</strong>
        </p>
    </div>

    <table style="margin-top: 20px; width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 14px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 8px 12px; text-align: left;">Name</th>
                <th style="border: 1px solid #000; padding: 8px 12px; text-align: left;">Position</th>
                <th style="border: 1px solid #000; padding: 8px 12px; text-align: left;">Signature</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($doc['representatives'] as $index => $representative)
                <tr>
                    <td style="border: 1px solid #000; padding: 10px 12px;" data-field="representatives_{{ $index }}_name">{{ $representative['name'] }}</td>
                    <td style="border: 1px solid #000; padding: 10px 12px;" data-field="representatives_{{ $index }}_position">{{ $representative['position'] }}</td>
                    <td style="border: 1px solid #000; padding: 10px 12px;">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 24px; text-align: justify;">
        <p style="margin: 0 0 16px;"><strong>RESOLVED FURTHER,</strong> that only the above-named Authorized Representative is authorized to act for and on behalf of the Corporation in all dealings with John Kelly &amp; Company (JK&amp;C Inc.), including but not limited to the execution of contracts, engagement agreements, and related documents; submission and receipt of documents, records, and communications; approval and acceptance of services and deliverables; and performance of all acts necessary or incidental to such engagement;</p>

        <p style="margin: 0 0 16px;"><strong>RESOLVED FURTHER,</strong> that John Kelly &amp; Company (JK&amp;C Inc.) is authorized to rely exclusively on the acts, instructions, and representations of the above-named Authorized Representative, and shall have no obligation to recognize or act upon any communication, instruction, or document from any other person unless supported by a duly issued Board Resolution amending this authority;</p>

        <p style="margin: 0 0 16px;"><strong>RESOLVED FURTHER,</strong> that all acts performed by the Authorized Representative within the scope of this authority are hereby ratified and shall be binding upon the Corporation;</p>
    </div>

    <div style="margin-top: 56px; text-align: justify;">
        <p style="margin: 0 0 20px;">
            <strong>IN WITNESS WHEREOF,</strong> I have hereunto set my hand and affixed the seal of the Corporation in the City of
            <span data-field="witness_city">{{ $doc['witness_city'] }}</span>,
            Philippines, this
            <span data-field="witness_day">{{ $doc['witness_day'] }}</span>
            day of
            <span data-field="witness_month">{{ $doc['witness_month'] }}</span>,
            <span data-field="witness_year">{{ $doc['witness_year'] }}</span>.
        </p>

        <p style="margin: 0 0 16px;"><strong>RESOLVED FINALLY,</strong> that this authority shall remain valid and effective unless revoked or amended by a subsequent written Board Resolution duly communicated to <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong>, and that this Certificate may be relied upon as conclusive proof of such authority.</p>
    </div>

    <div style="margin-top: 48px;">
        <div style="font-weight: 600;" data-field="corporate_secretary_name">{{ $doc['corporate_secretary_name'] }}</div>
        <div>Corporate Secretary</div>
        <div>TIN NO: <span data-field="corporate_secretary_tin">{{ $doc['corporate_secretary_tin'] }}</span></div>
    </div>

    <table style="width: 100%; margin-top: 40px; border-collapse: collapse; font-size: 14px;">
        <tr>
            <td style="width: 70%; vertical-align: top; text-align: justify; padding-right: 18px;">
            <strong>SUBSCRIBED AND SWORN</strong> to before me this
            <span data-field="subscribed_day">{{ $doc['subscribed_day'] }}</span>
            day of
            <span data-field="subscribed_month">{{ $doc['subscribed_month'] }}</span>,
            <span data-field="subscribed_year">{{ $doc['subscribed_year'] }}</span>
            affiant exhibiting to me her TIN
            <span data-field="affiant_tin">{{ $doc['affiant_tin'] }}</span>.
            </td>
            <td style="width: 30%; vertical-align: top; padding-top: 40px; text-align: center;">
                <div style="font-weight: 600; text-transform: uppercase;" data-field="notary_public">{{ $doc['notary_public'] }}</div>
                <div>Notary Public</div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 28px; font-size: 14px;">
        <div>Doc. No. <span data-field="doc_no">{{ $doc['doc_no'] }}</span>;</div>
        <div>Page No. <span data-field="page_no">{{ $doc['page_no'] }}</span>;</div>
        <div>Book No. <span data-field="book_no">{{ $doc['book_no'] }}</span>;</div>
        <div>Series of <span data-field="series_year">{{ $doc['series_year'] }}</span>.</div>
    </div>
</div>
