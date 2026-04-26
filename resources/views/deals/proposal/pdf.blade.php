@php
    $logoPath = public_path('images/deal-proposal-template-logo.png');
    $logoSrc = null;

    if (is_file($logoPath)) {
        $mime = mime_content_type($logoPath) ?: 'image/png';
        $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deal Proposal</title>
    <style>
        @page { margin: 32px 36px; }
        body { font-family: Georgia, "Times New Roman", serif; color: #111827; font-size: 12px; line-height: 1.58; }
        .proposal-page { page-break-after: always; position: relative; padding: 0 22px 70px; }
        .proposal-page:last-child { page-break-after: auto; }
        .proposal-inner-page { min-height: 960px; padding-top: 26px; }
        .proposal-page-body { width: 100%; }
        .proposal-cover { min-height: 980px; position: relative; }
        .proposal-cover-logo-wrap { width: 100%; }
        .proposal-brand-logo { width: 470px; max-width: 100%; height: auto; }
        .proposal-cover-body { margin-top: 165px; color: #0031af; }
        .proposal-cover-year { font-size: 86px; line-height: 1; font-style: italic; font-weight: 700; }
        .proposal-cover-title { margin-top: 14px; font-size: 32px; line-height: 1.22; font-style: italic; }
        .proposal-cover-date { margin-top: 60px; font-size: 14px; color: #111827; font-style: italic; }
        .proposal-presented-label { margin-top: 70px; font-size: 15px; font-style: italic; }
        .proposal-presented-name, .proposal-presented-location { margin-top: 10px; font-size: 16px; font-weight: 700; font-style: italic; }
        .proposal-cover-footer { position: absolute; left: 8px; right: 8px; bottom: 26px; }
        .proposal-contact-strip { margin: 0; text-align: center; color: #0031af; font-style: italic; }
        .proposal-contact-inline { width: 100%; text-align: center; font-size: 12px; }
        .proposal-contact-inline span { display: inline-block; margin: 0 14px; }
        .proposal-contact-address { margin-top: 4px; text-align: center; color: #0031af; font-size: 13px; font-style: italic; }
        .proposal-page-footer { position: absolute; left: 22px; right: 22px; bottom: 0; font-size: 10px; line-height: 1.2; color: #111827; }
        .proposal-page-footer div { margin: 0; }
        .proposal-section-heading { margin: 10px 0 18px; font-size: 18px; line-height: 1.22; color: #0031af; font-style: italic; font-weight: 700; letter-spacing: 0.01em; }
        .proposal-section-number { display: inline-block; min-width: 34px; margin-right: 8px; }
        .proposal-subheading { margin: 18px 0 8px; font-size: 13px; line-height: 1.35; color: #111827; font-weight: 700; }
        .proposal-subheading-blue { color: #0031af; font-style: italic; font-weight: 700; }
        .proposal-subheading-tight { margin-top: 14px; }
        .proposal-term-number { display: inline-block; min-width: 18px; }
        .proposal-paragraph, .proposal-note, .proposal-system-note { margin: 0 0 12px; font-size: 11.5px; line-height: 1.7; text-align: justify; }
        .proposal-note { color: #475569; font-style: italic; }
        .proposal-system-note { margin-top: 18px; font-size: 10px; color: #475569; }
        .proposal-bullet-list, .proposal-numbered-list { margin: 0 0 10px 18px; padding: 0; font-size: 11.5px; line-height: 1.7; }
        .proposal-bullet-list li { margin-bottom: 5px; }
        .proposal-numbered-list li { margin-bottom: 5px; }
        .proposal-requirement-group { margin-bottom: 10px; }
        .proposal-requirement-label { margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #0031af; }
        .proposal-term-block { margin-bottom: 16px; }
        .proposal-service-table, .proposal-pricing-table, .proposal-data-table { width: 100%; border-collapse: collapse; margin-top: 12px; table-layout: fixed; }
        .proposal-service-table th, .proposal-service-table td, .proposal-pricing-table th, .proposal-pricing-table td, .proposal-data-table th, .proposal-data-table td { border: 1px solid #111827; padding: 8px 10px; font-size: 10.5px; vertical-align: top; }
        .proposal-service-table th, .proposal-pricing-table th, .proposal-data-table th { font-weight: 700; text-align: left; background: #f8fafc; }
        .proposal-service-no { width: 7%; }
        .proposal-service-area { width: 24%; }
        .proposal-service-scope { width: 69%; }
        .proposal-service-area-title { font-style: italic; font-size: 12px; }
        .proposal-service-scope-list ol { margin: 0; padding-left: 18px; }
        .proposal-service-scope-list li { margin: 0 0 4px; }
        .proposal-service-scope-list ol[type="a"] { list-style-type: lower-alpha; }
        .proposal-service-table { margin-top: 22px; }
        .proposal-pricing-table, .proposal-data-table { margin-top: 16px; }
        .proposal-pricing-table th:last-child, .proposal-pricing-table td:last-child { text-align: center; width: 34%; }
        .proposal-pricing-table .is-total td { font-weight: 700; color: #0031af; }
        .proposal-term-block + .proposal-term-block { margin-top: 8px; }
        .proposal-end-note { margin: 18px 0 10px; font-size: 11px; font-style: italic; }
        .proposal-signature-grid { width: 100%; margin-top: 28px; }
        .proposal-signature-block { width: 48%; display: inline-block; vertical-align: top; }
        .proposal-signature-block + .proposal-signature-block { margin-left: 3%; }
        .proposal-signature-label { font-size: 12px; font-weight: 700; margin-bottom: 50px; }
        .proposal-signature-line { border-bottom: 1px solid #111827; padding-bottom: 6px; font-size: 12px; }
        .proposal-signature-subline { margin-top: 8px; font-size: 12px; }
        .proposal-footer-note { margin-top: 90px; font-size: 10px; color: #5a6470; }
        .proposal-footer-note div { margin-bottom: 2px; }
    </style>
</head>
<body>
    @include('deals.proposal.partials.document', ['documentData' => $documentData])
</body>
</html>
