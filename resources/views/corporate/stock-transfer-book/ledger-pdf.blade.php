@php
    $issuedRows = collect($journalEntries ?? [])
        ->filter(function ($entry) {
            $type = strtolower((string) ($entry->transaction_type ?? ''));
            $particulars = strtolower((string) ($entry->particulars ?? ''));
            $remarks = strtolower((string) ($entry->remarks ?? ''));
            $shares = (int) ($entry->no_shares ?? 0);

            if ($type === 'cancellation') {
                return false;
            }

            if ($shares <= 0) {
                return false;
            }

            if (str_contains($particulars, 'payment') || str_contains($remarks, 'payment')) {
                return false;
            }

            return true;
        })
        ->sortBy(fn ($entry) => sprintf(
            '%s-%010d',
            optional($entry->entry_date)->format('Ymd') ?: '00000000',
            (int) ($entry->id ?? 0)
        ))
        ->take(30)
        ->values();

    $cancelledRows = collect($journalEntries ?? [])
        ->filter(fn ($entry) => strtolower((string) ($entry->transaction_type ?? '')) === 'cancellation')
        ->sortBy(fn ($entry) => sprintf(
            '%s-%010d',
            optional($entry->entry_date)->format('Ymd') ?: '00000000',
            (int) ($entry->id ?? 0)
        ))
        ->take(30)
        ->values();

    $maxRows = max(30, $issuedRows->count(), $cancelledRows->count());
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 6mm; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111827; font-size: 7.8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #1f2937; padding: 2px 3px; vertical-align: middle; }
        .title { font-size: 11px; font-weight: 700; text-align: center; margin-bottom: 2px; text-transform: uppercase; }
        .header { font-size: 7px; font-weight: 700; text-transform: uppercase; }
        .subheader { font-size: 6.7px; font-weight: 700; }
        .row td { height: 16px; }
    </style>
</head>
<body>
    <div class="title">Ledger</div>
    <table>
        <thead>
            <tr>
                <th colspan="4" class="header">Certificate Cancelled</th>
                <th colspan="4" class="header">Share-Issuing Entries</th>
            </tr>
            <tr>
                <th class="subheader">Date</th>
                <th class="subheader">Journal Portfolio</th>
                <th class="subheader">Certificate Number</th>
                <th class="subheader">Number of Shares</th>
                <th class="subheader">Date</th>
                <th class="subheader">Journal Portfolio</th>
                <th class="subheader">Certificate Number</th>
                <th class="subheader">Number of Shares</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $maxRows; $i++)
                @php
                    $cancel = $cancelledRows->get($i);
                    $issue = $issuedRows->get($i);
                @endphp
                <tr class="row">
                    <td>{{ $cancel ? optional($cancel->entry_date)->format('m/d/Y') : '' }}</td>
                    <td>{{ $cancel->journal_no ?? '' }}</td>
                    <td>{{ $cancel->certificate_no ?? '' }}</td>
                    <td>{{ $cancel->no_shares ?? '' }}</td>
                    <td>{{ $issue ? optional($issue->entry_date)->format('m/d/Y') : '' }}</td>
                    <td>{{ $issue->journal_no ?? '' }}</td>
                    <td>{{ $issue->certificate_no ?? '' }}</td>
                    <td>{{ $issue->no_shares ?? '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>