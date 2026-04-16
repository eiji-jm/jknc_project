@extends('layouts.app')
@section('title', 'Installment')
@section('content')
@php
    $paymentStatus = strtolower((string) ($installment->payment_status ?? 'unpaid'));
    $paymentStatusClasses = match ($paymentStatus) {
        'paid' => 'bg-green-100 text-green-800',
        'partial' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'voided' => 'bg-gray-200 text-gray-800',
        default => 'bg-amber-100 text-amber-800',
    };
    $installmentRows = collect($installmentRows ?? []);
    $individualInstallmentSheetRows = collect($individualInstallmentSheetRows ?? []);
    $installmentCancellationRules = [
        'Delinquent' => [
            'allowed' => (float) ($remainingBalance ?? 0) > 0 && (int) ($remainingInstallmentCount ?? 0) > 0,
            'condition' => 'remaining_balance > 0 AND installments_remaining > 0',
            'disable_if' => 'Fully paid',
            'notes' => 'Auto-detect recommended',
        ],
        'Buy-back' => [
            'allowed' => (float) ($totalPaid ?? 0) > 0,
            'condition' => 'paid_amount > 0',
            'disable_if' => 'No payment history',
            'notes' => 'Requires buy-back agreement',
        ],
        'Redemption' => [
            'allowed' => false,
            'condition' => 'share_type == "redeemable"',
            'disable_if' => 'Common shares',
            'notes' => 'Only for redeemable shares',
        ],
        'Treasury Cancellation' => [
            'allowed' => false,
            'condition' => 'is_treasury_share == true',
            'disable_if' => 'Not treasury shares',
            'notes' => 'Shares must be owned by company',
        ],
        'Capital Reduction' => [
            'allowed' => false,
            'condition' => 'affects_capital == true',
            'disable_if' => 'Normal users',
            'notes' => 'Admin only + SEC process',
        ],
        'Others' => [
            'allowed' => true,
            'condition' => 'Always allowed',
            'disable_if' => '—',
            'notes' => 'Require explanation field',
        ],
    ];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showCancelModal: false }">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="text-lg font-semibold">Installment Preview</div>
                <div class="text-xs text-gray-500">Stock No. {{ $installment->stock_number ?? '-' }}</div>
            </div>
            <div class="flex-1"></div>
            @if (!empty($editRoute))
                <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            @endif
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                <div class="xl:col-span-3 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Individual Installment</div>
                                <div class="text-xs text-gray-500">In-system table following the individual installment sheet format for stock subscribed and stock payments.</div>
                            </div>
                            @if ($canRecordPreviewPayment && !empty($nextPaymentRow))
                                <div class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                                    Next payment: Installment {{ $nextPaymentRow['no'] }} | Due {{ $nextPaymentRow['due_date'] ?: '-' }} | {{ number_format((float) ($nextPaymentRow['amount_value'] ?? 0), 2, '.', '') }}
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-[11px] border border-gray-800">
                                    <thead>
                                        <tr class="border-b border-gray-800 bg-gray-50">
                                            <th colspan="4" class="border-r border-gray-800 px-2 py-1 text-center text-[11px] font-semibold uppercase tracking-wide">Stock Subscribed</th>
                                            <th colspan="4" class="px-2 py-1 text-center text-[11px] font-semibold uppercase tracking-wide">Stock Payments</th>
                                        </tr>
                                        <tr class="border-b border-gray-800 bg-gray-50 text-[10px] font-semibold text-gray-700">
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">Date</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">No. Shares</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">No. of Installments</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">Value</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">Date</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">Date</th>
                                            <th class="border-r border-gray-800 px-2 py-2 text-center">What Installments</th>
                                            <th class="px-2 py-2 text-center">Amount Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-900">
                                        @forelse ($individualInstallmentSheetRows as $sheetRow)
                                            <tr class="border-b border-gray-300">
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['subscribed_date'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['subscribed_shares'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['subscribed_installments'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['subscribed_value'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['payment_date'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['posted_date'] ?: '' }}</td>
                                                <td class="border-r border-gray-300 px-2 py-2">{{ $sheetRow['installment_no'] ?: '' }}</td>
                                                <td class="px-2 py-2">{{ $sheetRow['amount_paid'] ?: '' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No installment sheet data available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="text-sm font-semibold text-gray-900">Digital PDF Preview</div>
                            <div class="text-xs text-gray-500">PDF preview of the individual installment sheet.</div>
                        </div>
                        <div class="p-4">
                            @if (!empty($generatedPreviewUrl))
                                <iframe src="{{ $generatedPreviewUrl }}" class="w-full h-[980px] border rounded bg-white"></iframe>
                            @else
                                <div class="w-full h-[700px] border rounded flex items-center justify-center bg-gray-50 text-gray-400 text-sm">Installment preview PDF unavailable.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-2 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Installment Information</div>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Holder</span><div class="font-medium text-gray-900">{{ $holderName ?? '-' }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Stock Number</span><div class="font-medium text-gray-900">{{ $installment->stock_number ?? '-' }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Installment Date</span><div class="font-medium text-gray-900">{{ optional($installment->installment_date)->format('M d, Y') ?? '-' }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. Shares</span><div class="font-medium text-gray-900">{{ $installment->no_shares ?? '-' }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">No. of Installments</span><div class="font-medium text-gray-900">{{ $remainingInstallmentCount ?? ($installment->no_installments ?? '-') }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">PAR</span><div class="font-medium text-gray-900">{{ number_format((float) ($installment->par_value ?? 0), 2, '.', '') }}</div></div>
                        </div>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Payment Summary</div>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Per Installment</span><div class="font-medium text-gray-900">{{ number_format((float) ($installment->installment_amount ?? 0), 2, '.', '') }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Total Value</span><div class="font-medium text-gray-900">{{ number_format((float) ($installment->total_value ?? 0), 2, '.', '') }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Paid</span><div class="font-medium text-gray-900">{{ number_format((float) ($totalPaid ?? 0), 2, '.', '') }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Remaining Balance</span><div class="font-medium text-gray-900">{{ number_format((float) ($remainingBalance ?? 0), 2, '.', '') }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Installments Paid</span><div class="font-medium text-gray-900">{{ $paidInstallmentCount ?? 0 }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Installments Remaining</span><div class="font-medium text-gray-900">{{ $remainingInstallmentCount ?? ($installment->no_installments ?? 0) }}</div></div>
                            <div><span class="text-xs text-gray-600 uppercase tracking-wide">Status</span><div class="mt-1"><span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $paymentStatusClasses }}">{{ ucfirst($paymentStatus) }}</span></div></div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Payment Tracker</div>
                        <div class="space-y-2 text-sm">
                            @forelse ($installmentRows as $installmentRow)
                                <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-xs text-gray-600 uppercase tracking-wide">Installment {{ $installmentRow['no'] }}</div>
                                            <div class="font-medium text-gray-900">Due {{ $installmentRow['due_date'] ?: '-' }}</div>
                                            <div class="text-xs text-gray-500 mt-1">Payment Date: {{ $installmentRow['payment_date'] ?: '-' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">{{ $installmentRow['scheduled_amount'] ?: '0.00' }}</div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $installmentRow['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $installmentRow['status'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">No installment schedule saved yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl border {{ $canRecordPreviewPayment ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-gray-50' }} p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Record Installment Payment</div>
                        @if ($canRecordPreviewPayment && !empty($nextPaymentRow))
                            <form method="POST" action="{{ route('stock-transfer-book.installment.payments.store', $installment) }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-600">Installment</label>
                                        <input type="text" value="Installment {{ $nextPaymentRow['no'] }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900" readonly>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600">Due Date</label>
                                        <input type="text" value="{{ $nextPaymentRow['due_date'] ?: '-' }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900" readonly>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600">Amount</label>
                                        <input type="text" value="{{ number_format((float) ($nextPaymentRow['amount_value'] ?? 0), 2, '.', '') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900" readonly>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600">Payment Date</label>
                                        <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-gray-600">Payment Option</label>
                                        <select name="payment_scope" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900">
                                            <option value="next">Pay next installment only</option>
                                            <option value="all_remaining">Pay all remaining installments</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-gray-600">Payment Remarks</label>
                                        <textarea name="payment_remarks" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900" placeholder="Optional note for this installment payment."></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">
                                    Record Payment
                                </button>
                            </form>
                        @else
                            <div class="text-sm text-gray-600">
                                {{ in_array($paymentStatus, ['cancelled', 'voided'], true) ? 'This installment is no longer accepting payments.' : 'All scheduled installments have already been paid.' }}
                            </div>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-900 mb-3">Linked Records</div>
                        <div class="space-y-3 text-sm">
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Certificates</div><div class="mt-1 text-gray-900">{{ ($relatedCertificates ?? collect())->count() }} linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Journal Entries</div><div class="mt-1 text-gray-900">{{ ($relatedJournals ?? collect())->count() }} linked</div></div>
                            <div><div class="text-xs text-gray-600 uppercase tracking-wide">Ledgers</div><div class="mt-1 text-gray-900">{{ ($relatedLedgers ?? collect())->count() }} linked</div></div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <button type="button" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" @click="showCancelModal = true">
                            <i class="fas fa-ban"></i>
                            Cancel Installment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showCancelModal" class="fixed inset-0 bg-black/40 z-40" @click="showCancelModal = false"></div>
        <div x-show="showCancelModal" class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Cancellation Details</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showCancelModal = false" type="button"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('stock-transfer-book.installment.cancel', $installment) }}" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-gray-50 overflow-hidden">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Cancellation Type</th>
                                <th class="px-3 py-2 text-left font-semibold">Conditions</th>
                                <th class="px-3 py-2 text-left font-semibold">Allow?</th>
                                <th class="px-3 py-2 text-left font-semibold">Disable If</th>
                                <th class="px-3 py-2 text-left font-semibold">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($installmentCancellationRules as $reason => $rule)
                                <tr class="align-top">
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $reason }}</td>
                                    <td class="px-3 py-2 text-gray-700"><code>{{ $rule['condition'] }}</code></td>
                                    <td class="px-3 py-2 {{ $rule['allowed'] ? 'text-emerald-700' : 'text-amber-700' }}">{{ $rule['allowed'] ? 'Yes' : 'Restricted' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $rule['disable_if'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $rule['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="text-xs text-gray-600">Date of Cancellation</label><input type="date" name="cancellation_date" value="{{ old('cancellation_date', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required></div>
                    <div><label class="text-xs text-gray-600">Effective Date</label><input type="date" name="cancellation_effective_date" value="{{ old('cancellation_effective_date', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required></div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Reason for Cancellation</label>
                        <select name="cancellation_reason" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                            <option value="">Select reason</option>
                            @foreach ($installmentCancellationRules as $reason => $rule)
                                <option value="{{ $reason }}" @disabled(!$rule['allowed']) @selected(old('cancellation_reason') === $reason)>{{ $reason }}{{ !$rule['allowed'] ? ' (Restricted)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Other Reason Details</label>
                        <textarea name="cancellation_other_reason" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Add details when 'Others' is selected.">{{ old('cancellation_other_reason') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6 mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showCancelModal = false">Close</button>
                    <div class="flex-1"></div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
