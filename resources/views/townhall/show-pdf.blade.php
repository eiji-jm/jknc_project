<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memo</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
        }
        .header {
            border-bottom: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            letter-spacing: 4px;
            margin: 20px 0;
        }
        .meta p {
            margin: 5px 0;
        }
        .body {
            margin-top: 20px;
            line-height: 1.8;
        }
        .signature {
            margin-top: 60px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>JOHN KELLY & COMPANY</h2>
    <p>Ref: {{ $communication->ref_no }}</p>
    <p>Date: {{ $communication->communication_date }}</p>
</div>

<div class="title">MEMORANDUM</div>

<div class="meta">
    <p><strong>{{ $communication->recipient_label ?? 'To' }}:</strong> {{ $communication->to_for }}</p>
    <p><strong>From:</strong> {{ $communication->from_name }}</p>
    <p><strong>Department:</strong> {{ $communication->department_stakeholder }}</p>
    <p><strong>Priority:</strong> {{ $communication->priority }}</p>
    <p><strong>Subject:</strong> {{ $communication->subject }}</p>
</div>

<div class="body">
    {!! $communication->message !!}
</div>

<div class="signature">
    <p>Respectfully,</p>
    <br><br>
    <strong>{{ $communication->from_name }}</strong>
</div>

</body>
</html>
