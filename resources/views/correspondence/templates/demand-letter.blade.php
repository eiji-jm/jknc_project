<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demand Letter</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 22mm 18mm;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #111;
            line-height: 1.5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-sizing: border-box;
            padding: 22mm 18mm;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 24px;
            text-transform: uppercase;
        }

        .meta p {
            margin: 4px 0;
        }

        .signature {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="title">Demand Letter</div>

        <div class="meta">
            <p><strong>Date:</strong> {{ $correspondence->date ? \Carbon\Carbon::parse($correspondence->date)->format('F d, Y') : 'N/A' }}</p>
            <p><strong>{{ $correspondence->sender_type ?? 'From' }}:</strong> {{ $correspondence->sender ?? 'N/A' }}</p>
            <p><strong>Subject:</strong> {{ $correspondence->subject ?? 'N/A' }}</p>
        </div>

        <p>Dear Sir/Madam,</p>

        <p>
            This is a formal demand regarding
            {{ $correspondence->details ?? 'the matter stated above' }}.
            You are hereby requested to address this matter on or before
            {{ $correspondence->deadline ? \Carbon\Carbon::parse($correspondence->deadline)->format('F d, Y') : 'the required date' }}.
        </p>

        <p>
            Failure to comply within the specified period may compel the undersigned to take the
            necessary action permitted under applicable rules and company policy.
        </p>

        <p><strong>Department / Stakeholder:</strong> {{ $correspondence->department ?? 'N/A' }}</p>
        <p><strong>Sent Via:</strong> {{ $correspondence->sent_via ?? 'Email' }}</p>

        <div class="signature">
            <p>Sincerely,</p>
            <br><br>
            <p><strong>{{ $correspondence->user ?? 'Authorized Representative' }}</strong></p>
            <p>Authorized Representative</p>
        </div>
    </div>
</body>
</html>