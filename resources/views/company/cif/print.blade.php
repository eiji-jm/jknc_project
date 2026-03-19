<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cif->title ?: 'Client Information Form' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #eef2f7;
        }

        .document-page {
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        @media print {
            .no-print {
                display: none !important;
            }

            @page {
                margin: 14mm;
            }

            body {
                background: #fff;
            }

            .print-shell {
                margin: 0;
                max-width: none;
                padding: 0;
            }

            .document-page {
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="print-shell mx-auto max-w-6xl p-6">
        <div class="no-print mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3">
            <div>
                <h1 class="text-lg font-semibold">Client Information Form</h1>
                <p class="text-sm text-gray-500">Use your browser's print dialog and choose Save as PDF to export this document.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('company.cif.show', ['company' => $company->id, 'cif' => $cif->id]) }}" class="inline-flex h-10 items-center rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back
                </a>
                <button type="button" onclick="window.print()" class="inline-flex h-10 items-center rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Print / Save as PDF
                </button>
            </div>
        </div>

        @include('company.cif.partials.document', ['wrapperClass' => 'cif-doc document-page'])
    </div>

    @if (!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
