@php
    $preparedDate = old('date_prepared', optional($sow?->date_prepared)->format('Y-m-d'));
    $approvalStatus = old('approval_status', $sow?->approval_status ?? 'draft');
    $ntpStatus = old('ntp_status', $sow?->ntp_status ?? 'pending');
    $withinCount = $sowWithin->filter(fn ($row) => filled($row['main_task_description'] ?? null))->count();
    $outCount = $sowOut->filter(fn ($row) => filled($row['main_task_description'] ?? null))->count();
    $clientConfirmationName = old('client_confirmation_name', $sow?->client_confirmation_name ?: $project->client_name);
@endphp

<style>
    .project-sow-sheet {
        border: 1px solid #d7deea;
        background: #fff;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    }
    .project-sow-form {
        border: 2px solid #1c4587;
        padding: 28px 30px 34px;
        background: #fff;
    }
    .project-sow-head {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }
    .project-sow-logo {
        width: 170px;
        height: auto;
        object-fit: contain;
    }
    .project-sow-title {
        text-align: right;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-title h2 {
        font-size: 2rem;
        line-height: 1.04;
        font-weight: 700;
        color: #111827;
        letter-spacing: 0.02em;
    }
    .project-sow-code {
        margin-top: 4px;
        font-size: 0.8rem;
        color: #64748b;
    }
    .project-sow-meta {
        margin-top: 18px;
        display: grid;
        gap: 8px 28px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .project-sow-meta-row {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.9rem;
        color: #111827;
    }
    .project-sow-meta-label {
        color: #334155;
    }
    .project-sow-line,
    .project-sow-line-input,
    .project-sow-line-select {
        min-height: 32px;
        border: 0;
        border-bottom: 1px solid #111827;
        background: transparent;
        padding: 4px 0 5px;
        color: #111827;
        width: 100%;
    }
    .project-sow-line-input:focus,
    .project-sow-line-select:focus {
        outline: none;
        border-bottom-color: #1c4587;
        box-shadow: inset 0 -1px 0 #1c4587;
    }
    .project-sow-line-select {
        appearance: none;
    }
    .project-sow-section {
        margin-top: 18px;
    }
    .project-sow-section-title {
        background: #1c4587;
        border: 2px solid #1c4587;
        padding: 9px 16px;
        text-align: center;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #fff;
    }
    .project-sow-table-wrap {
        overflow-x: auto;
    }
    .project-sow-table {
        width: 100%;
        min-width: 1080px;
        border-collapse: collapse;
        table-layout: fixed;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-table th,
    .project-sow-table td {
        border: 1px solid #111827;
        padding: 0;
        vertical-align: middle;
    }
    .project-sow-table th {
        background: #fff;
        padding: 8px 6px;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 400;
        color: #111827;
    }
    .project-sow-table input {
        width: 100%;
        min-height: 34px;
        border: 0;
        background: transparent;
        padding: 6px 8px;
        font-size: 0.82rem;
        color: #111827;
    }
    .project-sow-table input:focus {
        outline: none;
        box-shadow: inset 0 0 0 1px #1c4587;
    }
    .project-sow-total {
        display: grid;
        grid-template-columns: 1fr 180px 180px;
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
    }
    .project-sow-total-spacer {
        min-height: 34px;
        border-right: 1px solid #111827;
    }
    .project-sow-total-label {
        min-height: 34px;
        border-right: 1px solid #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
    }
    .project-sow-total-value {
        min-height: 34px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        font-size: 0.95rem;
        color: #111827;
    }
    .project-sow-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .project-sow-meta-schedule {
        margin-top: 16px;
        display: grid;
        gap: 8px 28px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .project-sow-signature-box {
        border: 1px solid #111827;
        border-top: 0;
        min-height: 66px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 10px 16px;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.8rem;
        font-style: italic;
        color: #111827;
        text-align: center;
    }
    .project-sow-approval-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
    }
    .project-sow-approval-cell {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        padding: 8px 10px;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.82rem;
        color: #111827;
    }
    .project-sow-approval-cell:nth-child(2n) {
        border-right: 0;
    }
    .project-sow-approval-cell.full {
        grid-column: 1 / -1;
    }
    .project-sow-approval-label {
        font-style: italic;
        color: #334155;
    }
    .project-sow-record-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(280px, 0.85fr);
        border-left: 1px solid #111827;
        border-right: 1px solid #111827;
        border-bottom: 1px solid #111827;
    }
    .project-sow-record-box {
        border-right: 1px solid #111827;
        min-height: 78px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 10px 16px;
        text-align: center;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.8rem;
        font-style: italic;
        color: #111827;
    }
    .project-sow-record-dates {
        display: grid;
    }
    .project-sow-record-date {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        align-items: end;
        gap: 10px;
        padding: 8px 10px;
        border-bottom: 1px solid #111827;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 0.82rem;
    }
    .project-sow-record-date:last-child {
        border-bottom: 0;
    }
    .project-sow-upload {
        margin-top: 16px;
        display: grid;
        gap: 12px;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }
    .project-sow-file {
        border: 1px solid #cfd9e7;
        background: #fff;
        padding: 10px 12px;
        font-size: 0.9rem;
        color: #111827;
        width: 100%;
    }
    @media (max-width: 900px) {
        .project-sow-head,
        .project-sow-meta,
        .project-sow-meta-schedule,
        .project-sow-approval-grid,
        .project-sow-record-grid,
        .project-sow-upload {
            grid-template-columns: minmax(0, 1fr);
        }
        .project-sow-title {
            text-align: left;
        }
        .project-sow-approval-cell,
        .project-sow-record-box {
            border-right: 0;
        }
    }
</style>

<form method="POST" action="{{ route('project.sow.update', $project) }}" enctype="multipart/form-data">
    @csrf

    <section class="project-sow-sheet">
        <div class="project-sow-form">
            <div class="project-sow-head">
                <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly and Company" class="project-sow-logo">
                <div class="project-sow-title">
                    <h2>SCOPE OF WORK</h2>
                    <div class="project-sow-code">[ Form Code ]</div>
                </div>
            </div>

            <div class="project-sow-meta">
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Condeal Reference No.:</span>
                    <span class="project-sow-line">{{ $project->deal?->deal_code ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Version No.:</span>
                    <input name="version_number" value="{{ old('version_number', $sow?->version_number) }}" class="project-sow-line-input">
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Business Name:</span>
                    <span class="project-sow-line">{{ $project->business_name ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">SOW No.:</span>
                    <span class="project-sow-line">{{ $sow?->sow_number ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Client Name:</span>
                    <span class="project-sow-line">{{ $project->client_name ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Date Prepared:</span>
                    <input type="date" name="date_prepared" value="{{ $preparedDate }}" class="project-sow-line-input">
                </div>
            </div>

            @foreach ([
                'within' => ['label' => 'WITHIN SCOPE', 'rows' => $sowWithin, 'count' => $withinCount],
                'out' => ['label' => 'OUT OF SCOPE', 'rows' => $sowOut, 'count' => $outCount],
            ] as $prefix => $section)
                <div class="project-sow-section">
                    <div class="project-sow-section-title">{{ $section['label'] }}</div>
                    <div class="project-sow-table-wrap">
                        <table class="project-sow-table">
                            <thead>
                                <tr>
                                    <th style="width: 18%;">Main Task Description</th>
                                    <th style="width: 18%;">Sub Task Description</th>
                                    <th style="width: 13%;">Responsible</th>
                                    <th style="width: 12%;">Duration</th>
                                    <th style="width: 12%;">Start Date</th>
                                    <th style="width: 12%;">End Date</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 15%;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="{{ $prefix }}-scope-table">
                                @foreach ($section['rows'] as $index => $item)
                                    <tr>
                                        <td><input name="{{ $prefix }}_main_task_description[]" value="{{ old($prefix.'_main_task_description.'.$index, $item['main_task_description'] ?? '') }}"></td>
                                        <td><input name="{{ $prefix }}_sub_task_description[]" value="{{ old($prefix.'_sub_task_description.'.$index, $item['sub_task_description'] ?? '') }}"></td>
                                        <td><input name="{{ $prefix }}_responsible[]" value="{{ old($prefix.'_responsible.'.$index, $item['responsible'] ?? '') }}"></td>
                                        <td><input name="{{ $prefix }}_duration[]" value="{{ old($prefix.'_duration.'.$index, $item['duration'] ?? '') }}"></td>
                                        <td><input type="date" name="{{ $prefix }}_start_date[]" value="{{ old($prefix.'_start_date.'.$index, $item['start_date'] ?? '') }}"></td>
                                        <td><input type="date" name="{{ $prefix }}_end_date[]" value="{{ old($prefix.'_end_date.'.$index, $item['end_date'] ?? '') }}"></td>
                                        <td><input name="{{ $prefix }}_status[]" value="{{ old($prefix.'_status.'.$index, $item['status'] ?? '') }}"></td>
                                        <td><input name="{{ $prefix }}_remarks[]" value="{{ old($prefix.'_remarks.'.$index, $item['remarks'] ?? '') }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="project-sow-total">
                        <div class="project-sow-total-spacer"></div>
                        <div class="project-sow-total-label">Total:</div>
                        <div class="project-sow-total-value">{{ $section['count'] }} item(s)</div>
                    </div>
                    <div class="project-sow-actions">
                        <button type="button" class="project-doc-action" data-add-scope-row="{{ $prefix }}-scope-table">Add Row</button>
                    </div>
                </div>
            @endforeach

            <div class="project-sow-meta-schedule">
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Project Start Date:</span>
                    <span class="project-sow-line">{{ optional($project->planned_start_date)->format('M d, Y') ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Client Preferred Completion Date:</span>
                    <span class="project-sow-line">{{ optional($project->client_preferred_completion_date)->format('M d, Y') ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Target Completion Date:</span>
                    <span class="project-sow-line">{{ optional($project->target_completion_date)->format('M d, Y') ?: '-' }}</span>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">Approval Status:</span>
                    <select name="approval_status" class="project-sow-line-select">
                        @foreach (['draft' => 'Draft', 'pending_review' => 'Pending Review', 'approved' => 'Approved'] as $value => $label)
                            <option value="{{ $value }}" @selected($approvalStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="project-sow-meta-row">
                    <span class="project-sow-meta-label">NTP Status:</span>
                    <select name="ntp_status" class="project-sow-line-select">
                        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                            <option value="{{ $value }}" @selected($ntpStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="project-sow-section">
                <div class="project-sow-section-title">CLIENT CONFIRMATION</div>
                <div class="project-sow-signature-box">
                    Client Fullname &amp; Signature
                </div>
                <div class="project-sow-upload">
                    <div class="project-sow-meta-row">
                        <span class="project-sow-meta-label">Client Confirmation Name:</span>
                        <input name="client_confirmation_name" value="{{ $clientConfirmationName }}" class="project-sow-line-input">
                    </div>
                    <div>
                        <input type="file" name="client_signed_attachment" class="project-sow-file">
                        @if ($sow?->client_signed_attachment_path)
                            <a href="{{ route('uploads.show', ['path' => $sow->client_signed_attachment_path]) }}" target="_blank" class="mt-2 inline-block text-sm font-medium text-blue-700 hover:text-blue-800">View uploaded signed file</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="project-sow-section">
                <div class="project-sow-section-title">INTERNAL APPROVAL</div>
                <div class="project-sow-approval-grid">
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Prepared By:</span>
                        <input name="prepared_by" value="{{ old('prepared_by', $sowApproval['prepared_by'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Reviewed By:</span>
                        <input name="reviewed_by" value="{{ old('reviewed_by', $sowApproval['reviewed_by'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Referred By/Closed By:</span>
                        <input name="referred_by_closed_by" value="{{ old('referred_by_closed_by', $sowApproval['referred_by_closed_by'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Sales &amp; Marketing:</span>
                        <input name="sales_marketing" value="{{ old('sales_marketing', $sowApproval['sales_marketing'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Lead Consultant:</span>
                        <input name="lead_consultant" value="{{ old('lead_consultant', $sowApproval['lead_consultant'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Lead Associate Assigned:</span>
                        <input name="lead_associate_assigned" value="{{ old('lead_associate_assigned', $sowApproval['lead_associate_assigned'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">Finance:</span>
                        <input name="finance" value="{{ old('finance', $sowApproval['finance'] ?? '') }}" class="project-sow-line-input">
                    </div>
                    <div class="project-sow-approval-cell">
                        <span class="project-sow-approval-label">President:</span>
                        <input name="president" value="{{ old('president', $sowApproval['president'] ?? '') }}" class="project-sow-line-input">
                    </div>
                </div>
                <div class="project-sow-record-grid">
                    <div class="project-sow-record-box">Record Custodian ( Name and Signature )</div>
                    <div class="project-sow-record-dates">
                        <div class="project-sow-record-date">
                            <span class="project-sow-approval-label">Date Recorded :</span>
                            <input type="date" name="date_recorded" value="{{ old('date_recorded', $sowApproval['date_recorded'] ?? '') }}" class="project-sow-line-input">
                        </div>
                        <div class="project-sow-record-date">
                            <span class="project-sow-approval-label">Date Signed :</span>
                            <input type="date" name="date_signed" value="{{ old('date_signed', $sowApproval['date_signed'] ?? '') }}" class="project-sow-line-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="project-sow-actions" style="margin-top: 22px;">
                <button type="submit" class="project-doc-primary">Save Scope of Work</button>
            </div>
        </div>
    </section>
</form>
