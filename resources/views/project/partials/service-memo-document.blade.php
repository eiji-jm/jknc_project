<div class="project-ntp-sheet project-service-memo-sheet">
    <div class="project-ntp-doc project-service-memo-doc">
        <div class="text-center">
            <div class="project-ntp-title">{{ $serviceMemo['title'] ?? 'SERVICE MEMO' }}</div>
            <div class="project-ntp-code">{{ $serviceMemo['form_code'] ?? '' }}</div>
        </div>

        <table class="project-ntp-meta project-service-memo-meta">
            <tr>
                <td>Date Issued: <span class="project-ntp-light">{{ $serviceMemo['date_issued'] ?? '-' }}</span></td>
                <td>Engagement Type: <span class="project-ntp-light">{{ $serviceMemo['engagement_type'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>START Ref No: <span class="project-ntp-light">{{ $serviceMemo['start_ref_no'] ?? '-' }}</span></td>
                <td>START Cleared Date: <span class="project-ntp-light">{{ $serviceMemo['start_cleared_date'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Condeal Reference No.: <span class="project-ntp-light">{{ $serviceMemo['condeal_reference_no'] ?? '-' }}</span></td>
                <td>Client Name: <span class="project-ntp-light">{{ $serviceMemo['client_name'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Business Name: <span class="project-ntp-light">{{ $serviceMemo['business_name'] ?? '-' }}</span></td>
                <td>RSAT / SOW Ref No.: <span class="project-ntp-light">{{ $serviceMemo['engagement_reference_no'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>Approved Start Date: <span class="project-ntp-light">{{ $serviceMemo['approved_start_date'] ?? '-' }}</span></td>
                <td>Target Completion Date: <span class="project-ntp-light">{{ $serviceMemo['target_completion_date'] ?? '-' }}</span></td>
            </tr>
            <tr>
                <td>RSAT Template: <span class="project-ntp-light">{{ $serviceMemo['rsat_template'] ?? '-' }}</span></td>
                <td>SOW Template: <span class="project-ntp-light">{{ $serviceMemo['sow_template'] ?? '-' }}</span></td>
            </tr>
        </table>

        <div class="project-ntp-copy project-service-memo-copy">
            <p>Based on the completed <strong>CASA (START) Clearance Status</strong>, approved engagement requirements, and the details stated above, this Service Memo serves as the formal authority for <strong>John Kelly &amp; Company (JK&amp;C Inc.)</strong> personnel to commence and execute the approved engagement.</p>
            <p>Accordingly, the assigned personnel are hereby directed to proceed with the approved engagement, including coordination, execution, processing, drafting, implementation, reporting, and delivery of services in accordance with the approved scope, timelines, templates, and company standards stated above.</p>
            <p>All personnel shall act strictly within the approved authority. Any change in scope, delay, issue, or additional request must be elevated for proper approval.</p>
        </div>

        <table class="project-ntp-signatures project-service-memo-signatures">
            <tr>
                <td class="project-ntp-sign-box">Name, Signature and Date<br>Lead Consultant<br><span class="project-ntp-light">{{ $serviceMemo['lead_consultant'] ?? '' }}</span></td>
                <td class="project-ntp-sign-box">Name, Signature and Date<br>Associate<br><span class="project-ntp-light">{{ $serviceMemo['associate'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="project-ntp-sign-box">Name, Signature and Date<br>Sales and Marketing<br><span class="project-ntp-light">{{ $serviceMemo['sales_marketing'] ?? '' }}</span></td>
                <td class="project-ntp-sign-box">Name, Signature and Date<br>Finance<br><span class="project-ntp-light">{{ $serviceMemo['finance'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td colspan="2" class="project-ntp-sign-box">Name, Signature and Date<br>Office of the President<br><span class="project-ntp-light">{{ $serviceMemo['office_of_president'] ?? '' }}</span></td>
            </tr>
        </table>

        <div class="project-service-memo-footer">
            <p>John Kelly &amp; Company</p>
            <p>3F, Cebu Holdings Center, Cebu Business Park, Cebu City, Philippines 6000</p>
            <p>Email: start@jknc.io | Website: jknc.io | Phone: 0995-535-8729</p>
        </div>
    </div>
</div>
