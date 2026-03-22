@extends('layouts.app')

@section('content')
@php
    $blankRows = 26;
    $paymentStatus = strtolower((string) ($installment->payment_status ?? 'overdue'));
    $paymentStatusClasses = match ($paymentStatus) {
        'paid' => 'bg-green-100 text-green-800',
        'partial' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'voided' => 'bg-gray-200 text-gray-800',
        default => 'bg-amber-100 text-amber-800',
    };
    $paymentRows = collect($paymentRows ?? []);
    $installmentRows = collect($installmentRows ?? []);
    $remainingRows = max($blankRows - $paymentRows->count(), 0);
@endphp

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #installment-print,
        #installment-print * {
            visibility: visible;
        }
        #installment-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            filter: none !important;
        }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showCancelModal: false }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">Installment Preview</div>
                <div class="text-xs text-gray-500">Stock No. {{ $installment->stock_number ?? '-' }}</div>
            </div>
            <div class="flex-1"></div>
            @if (!empty($editRoute))
                <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Edit
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">Installment Ledger Template</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="installment-print" class="bg-white w-full rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col text-[11px] text-gray-900">
                                <div class="text-center text-xs tracking-[0.25em] font-semibold">INDIVIDUAL INSTALLMENT</div>

                                <div class="mt-3 border border-gray-800">
                                    <table class="w-full text-[10px]">
                                        <thead>
                                            <tr class="border-b border-gray-800">
                                                <th colspan="4" class="border-r border-gray-800 px-2 py-1 text-center uppercase tracking-wide">Stock Subscribed</th>
                                                <th colspan="4" class="px-2 py-1 text-center uppercase tracking-wide">Stock Payments</th>
                                            </tr>
                                            <tr class="border-b border-gray-800">
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">No. Shares</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">No. of Installments</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Value</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">Date</th>
                                                <th class="border-r border-gray-800 px-2 py-1 text-left uppercase tracking-wide">What Installments</th>
                                                <th class="px-2 py-1 text-left uppercase tracking-wide">Amount Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b border-gray-300">
                                                <td class="border-r border-gray-300 px-2 py-2">{{ optional($installment->installment_date)->format('m/d/Y') ?? '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $installment->no_shares ?? '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $installment->no_installments ?? '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ number_format((float) ($installment->total_value ?? 0), 2, '.', '') }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2"></td>
                                                <td class="border-r border-gray-300 px-2 py-2"></td>
                                                <td class="border-r border-gray-300 px-2 py-2"></td>
                                                <td class="px-2 py-2"></td>
                                            </tr>
                                            @foreach ($paymentRows as $paymentRow)
                                                <tr class="border-b border-gray-300">
                                                    <td class="border-r border-gray-300 px-2 py-2">&nbsp;</td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2">{{ $paymentRow['payment_date'] ?? '' }}</td>
                                                    <td class="border-r border-gray-300 px-2 py-2">{{ $paymentRow['posted_date'] ?? '' }}</td>
                                                    <td class="border-r border-gray-300 px-2 py-2">{{ $paymentRow['installment_no'] ?? '' }}</td>
                                                    <td class="px-2 py-2">{{ number_format((float) ($paymentRow['amount_paid'] ?? 0), 2, '.', '') }}</td>
                                                </tr>
                                            @endforeach
                                            @for ($i = 0; $i < $remainingRows; $i++)
                                                <tr class="border-b border-gray-300">
                                                    <td class="border-r border-gray-300 px-2 py-2">&nbsp;</td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="border-r border-gray-300 px-2 py-2"></td>
                                                    <td class="px-2 py-2"></td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-auto pt-6">
                                    <div class="text-xs font-semibold">Certified Correct By:</div>
                                    <div class="mt-6 w-48 border-t border-gray-800 text-[10px] text-gray-600">
                                        Corporate Secretary<br>
                                        (Signature over Printed Name)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Installment Information</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Holder</span><div class="font-medium text-gray-900">{{ $holderName ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Number</span><div class="font-medium text-gray-900">{{ $installment->stock_number ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Installment Date</span><div class="font-medium text-gray-900">{{ optional($installment->installment_date)->format('M d, Y') ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. Shares</span><div class="font-medium text-gray-900">{{ $installment->no_shares ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. of Installments</span><div class="font-medium text-gray-900">{{ $installment->no_installments ?? '-' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Per Installment</span><div class="font-medium text-gray-900">{{ number_format((float) ($installment->installment_amount ?? 0), 2, '.', '') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Total Value</span><div class="font-medium text-gray-900">{{ number_format((float) ($installment->total_value ?? 0), 2, '.', '') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Paid</span><div class="font-medium text-gray-900">{{ number_format((float) ($totalPaid ?? 0), 2, '.', '') }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Remaining Balance</span><div class="font-medium text-gray-900">{{ number_format((float) ($remainingBalance ?? 0), 2, '.', '') }}</div></div>
                        <div>
                            <span class="text-xs text-gray-600 uppercase tracking-wide">Status</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $paymentStatusClasses }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Payment Tracking</div>
                    <div class="space-y-2 text-sm">
                        @forelse ($installmentRows as $installmentRow)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <div>
                                    <div class="text-xs text-gray-600 uppercase tracking-wide">Installment {{ $installmentRow['no'] }}</div>
                                    <div class="font-medium text-gray-900">{{ $installmentRow['due_date'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-gray-900">{{ $installmentRow['amount'] }}</div>
                                    <div class="text-xs {{ $installmentRow['status'] === 'Paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $installmentRow['status'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No installment schedule saved yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Certificates</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedCertificates ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedJournals ?? collect())->count() }} linked</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div>
                            <div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div>
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
                            @click="showCancelModal = true">
                        <i class="fas fa-ban"></i>
                        Cancel Installment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showCancelModal" class="fixed inset-0 bg-black/40 z-40" @click="showCancelModal = false"></div>
        <div x-show="showCancelModal"
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
                <button class="text-gray-500 hover:text-gray-700" @click="showCancelModal = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('stock-transfer-book.installment.cancel', $installment) }}" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Date of Cancellation</label>
                        <input type="date" name="cancellation_date" value="{{ old('cancellation_date', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Effective Date</label>
                        <input type="date" name="cancellation_effective_date" value="{{ old('cancellation_effective_date', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Reason for Cancellation</label>
                        <select name="cancellation_reason" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                            <option value="">Select reason</option>
                            @foreach (['Delinquent', 'Buy-back', 'Redemption', 'Treasury Cancellation', 'Capital Reduction', 'Others'] as $reason)
                                <option value="{{ $reason }}" @selected(old('cancellation_reason') === $reason)>{{ $reason }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-600 mb-2">Type of Cancellation</div>
                        <div class="space-y-2 text-sm text-gray-900">
                            @foreach (['Delinquent', 'Buy-back', 'Redemption', 'Treasury Cancellation', 'Capital Reduction', 'Others'] as $type)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="cancellation_types[]" value="{{ $type }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        @checked(collect(old('cancellation_types', []))->contains($type))>
                                    <span>{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Others</label>
                        <input type="text" name="cancellation_other_reason" value="{{ old('cancellation_other_reason') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Specify other reason">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showCancelModal = false">
                        Close
                    </button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
