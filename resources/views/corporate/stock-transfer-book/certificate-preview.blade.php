@extends('layouts.app')

@section('content')
@php
    $certificateNo = $certificate->stock_number ?? '-';
    $stockholder = $certificate->stockholder_name ?? '-';
    $corporation = $certificate->corporation_name ?? 'Stock Certificate';
    $shares = $certificate->number ?? '-';
    $issueDay = optional($certificate->date_issued)->format('d') ?? '-';
    $issueMonthYear = optional($certificate->date_issued)->format('F Y') ?? '-';
@endphp

<style id="certificate-print-styles">
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    .certificate-sheet {
        width: min(100%, 1122px);
        max-width: 1122px;
        aspect-ratio: 297 / 210;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        background:
            radial-gradient(circle at 12% 14%, rgba(255, 255, 255, 0.48), transparent 22%),
            radial-gradient(circle at 88% 85%, rgba(255, 255, 255, 0.30), transparent 24%),
            repeating-radial-gradient(circle at center, rgba(184, 170, 102, 0.14) 0 3px, rgba(247, 243, 226, 0.18) 3px 11px),
            linear-gradient(135deg, #f7f2de 0%, #efe6c8 48%, #f8f4e7 100%);
        border: 18px solid #6f8851;
        box-shadow: inset 0 0 0 5px #d8d0af, inset 0 0 0 11px #879b64;
    }

    .certificate-sheet::before {
        content: "";
        position: absolute;
        inset: 10px;
        border: 2px solid rgba(255, 255, 255, 0.85);
        pointer-events: none;
    }

    .certificate-sheet::after {
        content: "";
        position: absolute;
        inset: 28px;
        border: 2px solid rgba(104, 118, 67, 0.32);
        pointer-events: none;
    }

    .certificate-body {
        position: relative;
        z-index: 1;
        height: 100%;
        padding: 30px 38px 24px;
        display: grid;
        grid-template-rows: auto auto 1fr auto auto;
        color: #2d2a21;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .certificate-top {
        display: grid;
        grid-template-columns: 150px 1fr 150px;
        gap: 18px;
        align-items: end;
        margin-bottom: 16px;
    }

    .certificate-box {
        min-height: 64px;
        padding: 10px 10px 8px;
        border: 3px solid #7c7c74;
        background: linear-gradient(180deg, #ebebeb 0%, #bdbdbd 100%);
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.65);
        text-align: center;
    }

    .certificate-box-label {
        font-size: 10px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #4f4f49;
    }

    .certificate-box-value {
        margin-top: 5px;
        font-size: 18px;
        font-weight: 700;
        color: #232323;
    }

    .certificate-oval {
        min-height: 92px;
        border: 3px solid #8e8d84;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.84);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 24px;
        text-align: center;
    }

    .certificate-corp {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #303024;
    }

    .certificate-banner {
        border: 4px solid #7f7f79;
        background: linear-gradient(180deg, #cfcfcf 0%, #9a9a9a 100%);
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.6);
        text-align: center;
        padding: 12px 24px;
        margin-bottom: 18px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        color: #1f1f1f;
    }

    .certificate-text-block {
        padding: 0 24px;
    }

    .certificate-lead {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .certificate-text {
        font-size: 19px;
        line-height: 1.52;
        font-style: italic;
    }

    .certificate-fill {
        display: inline-block;
        min-width: 140px;
        padding: 0 6px 2px;
        border-bottom: 2px solid rgba(62, 62, 50, 0.45);
        font-style: normal;
        font-weight: 700;
        text-align: center;
    }

    .certificate-witness {
        display: grid;
        grid-template-columns: 132px 1fr;
        gap: 24px;
        align-items: end;
        margin-top: 22px;
    }

    .certificate-seal {
        width: 124px;
        height: 124px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(244, 214, 99, 0.95) 0%, rgba(180, 142, 37, 0.96) 58%, rgba(128, 97, 17, 1) 100%);
        box-shadow: inset 0 0 0 8px rgba(181, 143, 35, 0.62);
    }

    .certificate-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        padding: 0 24px;
        margin-top: 20px;
    }

    .certificate-signature {
        text-align: center;
    }

    .certificate-signature-line {
        border-top: 2px solid rgba(53, 53, 45, 0.55);
        padding-top: 8px;
        min-height: 42px;
    }

    .certificate-signature-name {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .certificate-signature-role {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #555548;
    }

    .certificate-footer {
        display: grid;
        grid-template-columns: 1fr 220px 120px;
        gap: 18px;
        align-items: end;
        padding: 0 24px;
        margin-top: 18px;
    }

    .certificate-footer-box {
        min-height: 50px;
        padding: 10px 10px 8px;
        border: 3px solid #7f7f79;
        background: linear-gradient(180deg, #cfcfcf 0%, #9a9a9a 100%);
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.6);
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #232323;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            width: 297mm;
            height: 210mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow: hidden !important;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body * {
            visibility: hidden !important;
        }

        #certificate-print,
        #certificate-print * {
            visibility: visible !important;
        }

        #certificate-print {
            position: fixed !important;
            left: 18mm !important;
            top: 18mm !important;
            width: 228mm !important;
            min-width: 228mm !important;
            max-width: 228mm !important;
            height: 148mm !important;
            min-height: 148mm !important;
            margin: 0 !important;
            aspect-ratio: auto !important;
            box-shadow: none !important;
            filter: none !important;
            page-break-inside: avoid;
            break-inside: avoid;
            overflow: hidden !important;
        }

        #certificate-print .certificate-body {
            height: 100% !important;
            padding: 18px 24px 16px !important;
        }

        #certificate-print .certificate-box {
            min-height: 52px !important;
            padding: 8px 8px 6px !important;
        }

        #certificate-print .certificate-box-label {
            font-size: 8px !important;
        }

        #certificate-print .certificate-box-value {
            font-size: 15px !important;
        }

        #certificate-print .certificate-oval {
            min-height: 74px !important;
            padding: 6px 18px !important;
        }

        #certificate-print .certificate-corp {
            font-size: 20px !important;
        }

        #certificate-print .certificate-banner {
            padding: 9px 16px !important;
            margin-bottom: 12px !important;
            font-size: 13px !important;
        }

        #certificate-print .certificate-text-block {
            padding: 0 18px !important;
        }

        #certificate-print .certificate-lead {
            font-size: 16px !important;
            margin-bottom: 6px !important;
        }

        #certificate-print .certificate-text {
            font-size: 15px !important;
            line-height: 1.35 !important;
        }

        #certificate-print .certificate-fill {
            min-width: 110px !important;
            padding: 0 4px 1px !important;
        }

        #certificate-print .certificate-witness {
            grid-template-columns: 98px 1fr !important;
            gap: 16px !important;
            margin-top: 14px !important;
        }

        #certificate-print .certificate-seal {
            width: 92px !important;
            height: 92px !important;
        }

        #certificate-print .certificate-signatures {
            gap: 20px !important;
            padding: 0 18px !important;
            margin-top: 14px !important;
        }

        #certificate-print .certificate-signature-line {
            min-height: 32px !important;
            padding-top: 6px !important;
        }

        #certificate-print .certificate-signature-name {
            font-size: 11px !important;
        }

        #certificate-print .certificate-signature-role {
            font-size: 9px !important;
        }

        #certificate-print .certificate-footer {
            grid-template-columns: 1fr 170px 92px !important;
            gap: 12px !important;
            padding: 0 18px !important;
            margin-top: 12px !important;
        }

        #certificate-print .certificate-footer-box {
            min-height: 38px !important;
            padding: 8px 8px 6px !important;
            font-size: 11px !important;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4"
     x-data="{
        showVoidModal: false,
        showEditPanel: false,
        form: {
            certificate_type: @js($certificate->certificate_type ?: 'COS'),
            stock_number: @js($certificate->stock_number),
            stockholder_name: @js($certificate->stockholder_name),
            corporation_name: @js($certificate->corporation_name),
            company_reg_no: @js($certificate->company_reg_no),
            par_value: @js((string) ($certificate->par_value ?? '')),
            number: @js((string) ($certificate->number ?? '')),
            amount: @js((string) ($certificate->amount ?? '')),
            amount_in_words: @js($certificate->amount_in_words),
            date_issued: @js(optional($certificate->date_issued)->toDateString()),
            president: @js($certificate->president),
            corporate_secretary: @js($certificate->corporate_secretary),
        },
        formatDate(value) {
            if (!value) return '-';
            const date = new Date(value + 'T00:00:00');
            if (Number.isNaN(date.getTime())) return value;
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        issueDay() {
            if (!this.form.date_issued) return '-';
            const date = new Date(this.form.date_issued + 'T00:00:00');
            if (Number.isNaN(date.getTime())) return '-';
            return String(date.getDate()).padStart(2, '0');
        },
        issueMonthYear() {
            if (!this.form.date_issued) return '-';
            const date = new Date(this.form.date_issued + 'T00:00:00');
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },
        displayValue(value, fallback = '-') {
            return value === null || value === undefined || value === '' ? fallback : value;
        },
        displayAmount(value) {
            return value === null || value === undefined || value === '' ? '-' : value;
        }
     }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Certificate Preview</div>
                <div class="text-xs text-gray-500">Certificate No. {{ $certificateNo }}</div>
            </div>
            <div class="flex-1"></div>
            @if (!empty($editRoute))
                <button type="button" @click="showEditPanel = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Edit
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Stock Certificate</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="certificate-print" class="certificate-sheet shadow-2xl">
                            <div class="certificate-body">
                                <div class="certificate-top">
                                    <div class="certificate-box">
                                        <div class="certificate-box-label">Number</div>
                                        <div class="certificate-box-value" x-text="displayValue(form.stock_number)"></div>
                                    </div>
                                    <div class="certificate-oval">
                                        <div class="certificate-corp" x-text="displayValue(form.corporation_name, 'Stock Certificate')"></div>
                                    </div>
                                    <div class="certificate-box">
                                        <div class="certificate-box-label">No. Shares</div>
                                        <div class="certificate-box-value" x-text="displayValue(form.number)"></div>
                                    </div>
                                </div>

                                <div class="certificate-banner">Stock Certificate</div>

                                <div class="certificate-text-block">
                                    <div class="certificate-lead">This Certifies That</div>
                                    <p class="certificate-text">
                                        <span class="certificate-fill" x-text="displayValue(form.stockholder_name)"></span>
                                        is the owner of
                                        <span class="certificate-fill" x-text="displayValue(form.number)"></span>
                                        shares of the capital stock of
                                        <span class="certificate-fill" x-text="displayValue(form.corporation_name, 'Stock Certificate')"></span>
                                        transferable only on the books of the Corporation by the holder hereof in person or by Attorney upon surrender of this certificate properly endorsed.
                                    </p>

                                    <div class="certificate-witness">
                                        <div class="certificate-seal"></div>
                                        <p class="certificate-text">
                                            In Witness Whereof, the said Corporation has caused this certificate to be signed by its duly authorized officers and sealed this
                                            <span class="certificate-fill" x-text="issueDay()"></span>
                                            day of
                                            <span class="certificate-fill" x-text="issueMonthYear()"></span>.
                                        </p>
                                    </div>
                                </div>

                                <div class="certificate-signatures">
                                    <div class="certificate-signature">
                                        <div class="certificate-signature-line">
                                            <div class="certificate-signature-name" x-text="displayValue(form.president)"></div>
                                            <div class="certificate-signature-role">President</div>
                                        </div>
                                    </div>
                                    <div class="certificate-signature">
                                        <div class="certificate-signature-line">
                                            <div class="certificate-signature-name" x-text="displayValue(form.corporate_secretary)"></div>
                                            <div class="certificate-signature-role">Corporate Secretary</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certificate-footer">
                                    <div class="certificate-footer-box" x-text="displayValue(form.amount_in_words, 'Amount in Words')"></div>
                                    <div class="certificate-footer-box"><span x-text="displayValue(form.par_value)"></span> Par</div>
                                    <div class="certificate-footer-box">Each</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Certificate Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Certificate No.</span><div class="font-medium text-gray-900" x-text="displayValue(form.stock_number)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stockholder</span><div class="font-medium text-gray-900" x-text="displayValue(form.stockholder_name)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Number</span><div class="font-medium text-gray-900" x-text="displayValue(form.stock_number)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Par Value</span><div class="font-medium text-gray-900" x-text="displayValue(form.par_value)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Number</span><div class="font-medium text-gray-900" x-text="displayValue(form.number)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Amount</span><div class="font-medium text-gray-900" x-text="displayAmount(form.amount)"></div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Issued</span><div class="font-medium text-gray-900" x-text="formatDate(form.date_issued)"></div></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedJournals ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Installments</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedInstallments ?? collect())->count() }} linked</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-download"></i>
                        Download PDF
                    </button>
                    <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                    <button type="button"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2"
                            @click="showVoidModal = true">
                        <i class="fas fa-ban"></i>
                        Void Certificate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showEditPanel" class="fixed inset-0 bg-black/40 z-40" @click="showEditPanel = false"></div>
        <div x-show="showEditPanel"
             class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
             x-transition:enter="transform transition ease-in-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Edit Certificate</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showEditPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('stock-transfer-book.certificates.update', $certificate) }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Certificate Type</label>
                        <select name="certificate_type" x-model="form.certificate_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="COS">COS</option>
                            <option value="CV">CV</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Stock Number</label>
                        <input type="text" name="stock_number" x-model="form.stock_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Stockholder</label>
                        <input type="text" name="stockholder_name" x-model="form.stockholder_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Corporation Name</label>
                        <input type="text" name="corporation_name" x-model="form.corporation_name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Company Reg. No.</label>
                        <input type="text" name="company_reg_no" x-model="form.company_reg_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">PAR Value</label>
                        <input type="number" step="0.01" name="par_value" x-model="form.par_value" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Number</label>
                        <input type="number" name="number" x-model="form.number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Amount</label>
                        <input type="number" step="0.01" name="amount" x-model="form.amount" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Amount in Words</label>
                        <input type="text" name="amount_in_words" x-model="form.amount_in_words" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Issued</label>
                        <input type="date" name="date_issued" x-model="form.date_issued" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">President</label>
                        <input type="text" name="president" x-model="form.president" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Corporate Secretary</label>
                        <input type="text" name="corporate_secretary" x-model="form.corporate_secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Replace Document (PDF)</label>
                        <input type="file" name="document_path" class="mt-1 block w-full text-sm text-gray-600">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showEditPanel = false">
                        Close
                    </button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showVoidModal" class="fixed inset-0 bg-black/40 z-40" @click="showVoidModal = false"></div>
        <div x-show="showVoidModal"
             class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
             x-transition:enter="transform transition ease-in-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Cancellation Details</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showVoidModal = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('stock-transfer-book.certificates.destroy', $certificate) }}" class="p-6 overflow-y-auto space-y-4">
                @csrf
                @method('DELETE')
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                    This will mark the certificate as voided on record. Add the reason below before continuing.
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Date of Cancellation</label>
                        <input type="date" name="cancellation_date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Effective Date</label>
                        <input type="date" name="cancellation_effective_date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Reason for Cancellation</label>
                        <select name="cancellation_reason" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                            <option value="">Select reason</option>
                            @foreach (['Voided Certificate', 'Replacement', 'Loss', 'Damage', 'Encoding Error', 'Others'] as $reason)
                                <option value="{{ $reason }}">{{ $reason }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Remarks</label>
                        <textarea name="remarks" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add supporting notes for the voided certificate..." required></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showVoidModal = false">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                        Confirm Void
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
