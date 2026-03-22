<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice</title>
    <style>
        @page { size: A4 portrait; margin: 22mm 18mm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; color: #111; line-height: 1.5; margin: 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 24px; }
        .meta p { margin: 4px 0; }
        .signature { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="title">NOTICE</div>

    <div class="meta">
        <p><strong>Date:</strong> {{ $correspondence->date ? $correspondence->date->format('F d, Y') : 'N/A' }}</p>
        <p><strong>To:</strong> {{ $correspondence->to ?? 'N/A' }}</p>
        <p><strong>From:</strong> {{ $correspondence->from ?? $correspondence->user }}</p>
        <p><strong>Subject:</strong> {{ $correspondence->subject }}</p>
    </div>

    <p>Dear Sir/Madam,</p>

    <p>
        Please be informed of the following:
        {{ $correspondence->details ?? 'This notice is issued regarding the matter stated above.' }}
    </p>

    <p>
        Kindly take note of this notice and comply as necessary on or before
        {{ $correspondence->deadline ? $correspondence->deadline->format('F d, Y') : 'the stated date' }}.
    </p>

    <p><strong>Department / Stakeholder:</strong> {{ $correspondence->department ?? 'N/A' }}</p>
    <p><strong>Sent Via:</strong> {{ $correspondence->sent_via ?? 'Email' }}</p>

    <div class="signature">
        <p>Very truly yours,</p>
        <br><br>
        <p><strong>{{ $correspondence->user }}</strong></p>
        <p>Authorized Representative</p>
    </div>
</body>
</html>