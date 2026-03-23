<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demand Letter</title>
    <style>
        @page { size: A4 portrait; margin: 22mm 18mm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; color: #111; line-height: 1.5; margin: 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 24px; }
        .meta p { margin: 4px 0; }
        .signature { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="title">DEMAND LETTER</div>

    <div class="meta">
        <p><strong>Date:</strong> {{ $correspondence->date ? $correspondence->date->format('F d, Y') : 'N/A' }}</p>
        <p><strong>To:</strong> {{ $correspondence->to ?? 'N/A' }}</p>
        <p><strong>From:</strong> {{ $correspondence->from ?? $correspondence->user }}</p>
        <p><strong>Subject:</strong> {{ $correspondence->subject }}</p>
    </div>

    <p>Dear Sir/Madam,</p>

    <p>
        This is a formal demand regarding
        {{ $correspondence->details ?? 'the matter stated above' }}.
        You are hereby requested to address this matter on or before
        {{ $correspondence->deadline ? $correspondence->deadline->format('F d, Y') : 'the required date' }}.
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
        <p><strong>{{ $correspondence->user }}</strong></p>
        <p>Authorized Representative</p>
    </div>
</body>
</html>