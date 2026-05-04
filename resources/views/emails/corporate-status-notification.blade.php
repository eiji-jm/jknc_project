<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corporate Submission Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">

    <h2 style="margin-bottom: 16px;">Corporate Submission Update</h2>

    <p>Hi {{ $employeeName }},</p>

    <p>
        Your corporate submission has been reviewed.
    </p>

    <table cellpadding="8" cellspacing="0" border="0" style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td><strong>Module:</strong></td>
            <td>{{ $moduleName }}</td>
        </tr>
        <tr>
            <td><strong>Corporation Name:</strong></td>
            <td>{{ $corporationName }}</td>
        </tr>
        <tr>
            <td><strong>Company Reg No.:</strong></td>
            <td>{{ $companyRegNo }}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>{{ $decision }}</td>
        </tr>
    </table>

    @if(!empty($reviewNote))
        <p><strong>Review Note:</strong></p>
        <p style="background: #f9fafb; padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px;">
            {{ $reviewNote }}
        </p>
    @endif

    @if($decision === 'Approved')
        <p>Your submission has been approved successfully.</p>
    @elseif($decision === 'Needs Revision')
        <p>Your submission needs revision. Please check the note above, update the record, and resubmit.</p>
    @elseif($decision === 'Rejected')
        <p>Your submission has been rejected. Please review the note above for more details.</p>
    @endif

    <p style="margin-top: 24px;">
        Thank you,<br>
        JKNC Portal
    </p>

</body>
</html>