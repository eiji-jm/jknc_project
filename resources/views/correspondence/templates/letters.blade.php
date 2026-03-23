<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Letter</title>
    <style>
        @page { size: A4 portrait; margin: 22mm 18mm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; color: #111; line-height: 1.5; margin: 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 24px; }
        .meta { margin-bottom: 20px; }
        .meta p { margin: 4px 0; }
        .section { margin-top: 18px; }
        .signature { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="title">LETTER</div>

    <div class="meta">
        <p><strong>Date:</strong> {{ $correspondence->date ? $correspondence->date->format('F d, Y') : 'N/A' }}</p>
        <p><strong>To:</strong> {{ $correspondence->to ?? 'N/A' }}</p>
        <p><strong>From:</strong> {{ $correspondence->from ?? $correspondence->user }}</p>
        <p><strong>Subject:</strong> {{ $correspondence->subject }}</p>
    </div>

    <div class="section">
        <p>Dear Sir/Madam,</p>
        <p>{{ $correspondence->details ?? 'This letter is issued in connection with the subject stated above.' }}</p>
        <p>Please give this matter your proper attention.</p>
    </div>

    <div class="section">
        <p><strong>Department / Stakeholder:</strong> {{ $correspondence->department ?? 'N/A' }}</p>
        <p><strong>Sent Via:</strong> {{ $correspondence->sent_via ?? 'Email' }}</p>
        <p><strong>Respond Before:</strong> {{ $correspondence->deadline ? $correspondence->deadline->format('F d, Y') : 'No Deadline' }}</p>
    </div>

    <div class="signature">
        <p>Sincerely,</p>
        <br><br>
        <p><strong>{{ $correspondence->user }}</strong></p>
        <p>Authorized Representative</p>
    </div>
</body>
</html>