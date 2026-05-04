<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transmittal Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A5 portrait;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 24px;
        }

        .receipt-sheet {
            width: 148mm;
            min-height: 105mm;
            background: white;
            margin: 0 auto;
            border: 1px solid #d1d5db;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 14mm 12mm;
            box-sizing: border-box;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-sheet {
                box-shadow: none;
                border: none;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print max-w-[148mm] mx-auto mb-4 flex justify-end">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
            Print Receipt
        </button>
    </div>

    <div class="receipt-sheet">
        <div class="text-center mb-5">
            <h1 class="text-lg font-bold text-gray-900">TRANSMITTAL RECEIPT</h1>
            <p class="text-xs text-gray-500 mt-1">Generated upon approved transmittal</p>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm mb-5">
            <div><span class="font-semibold">Receipt No:</span> {{ $receipt->receipt_no }}</div>
            <div><span class="font-semibold">Receipt Date:</span> {{ optional($receipt->receipt_date)->format('Y-m-d') }}</div>
            <div><span class="font-semibold">Mode:</span> {{ $receipt->mode }}</div>
            <div><span class="font-semibold">Office:</span> {{ $receipt->office_name }}</div>
        </div>

        <div class="space-y-2 text-sm mb-5">
            <div><span class="font-semibold">From:</span> {{ $receipt->from_name }}</div>
            <div><span class="font-semibold">To:</span> {{ $receipt->to_name }}</div>
            <div><span class="font-semibold">Delivery Type:</span> {{ $receipt->delivery_detail ?: '—' }}</div>
            <div><span class="font-semibold">Recipient Email:</span> {{ $receipt->recipient_email ?: '—' }}</div>
            <div><span class="font-semibold">Actions:</span> {{ $receipt->actions_summary ?: '—' }}</div>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm mb-6">
            <div><span class="font-semibold">Prepared by:</span> {{ $receipt->prepared_by_name ?: '—' }}</div>
            <div><span class="font-semibold">Approved by:</span> {{ $receipt->approved_by_name ?: '—' }}</div>
            <div><span class="font-semibold">Delivered by:</span> {{ $receipt->delivered_by ?: '—' }}</div>
            <div><span class="font-semibold">Received by:</span> {{ $receipt->received_by ?: '—' }}</div>
        </div>

        <div class="pt-5 border-t border-gray-300 text-xs text-gray-500">
            Linked Transmittal:
            {{ optional($receipt->transmittal)->transmittal_no ?: ('TRN-' . $receipt->transmittal_id) }}
        </div>
    </div>
</body>
</html>