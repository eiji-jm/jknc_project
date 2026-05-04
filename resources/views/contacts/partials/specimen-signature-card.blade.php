@php
    $readonly = $readonly ?? false;
    $clientMode = $clientMode ?? false;
    $form = $form ?? [];
    $contact = $contact ?? null;
    $isBusinessContact = ($isBusinessContact ?? null) ?? (($contact?->customer_type ?? null) === 'business');
    $signatories = array_pad($form['signatories'] ?? [], 6, null);
    $inputClass = 'h-9 w-full rounded border border-gray-300 px-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100';
    $readonlyClass = 'min-h-9 rounded border border-gray-200 bg-gray-50 px-2 py-2 text-sm text-gray-700';
@endphp

<div class="overflow-hidden rounded-xl border border-gray-300 bg-white">
    <table class="w-full border-collapse text-xs text-gray-900">
        <tr>
            <td colspan="4" class="border border-gray-300 p-3 text-right align-top">
                <p class="font-semibold uppercase">{{ $isBusinessContact ? 'AUTHORIZED SIGNATORY' : 'AUTHORIZED SIGNATORY/SIGNATORY' }}</p>
                <p class="font-semibold uppercase">{{ $isBusinessContact ? 'SPECIMEN SIGNATURE CARD' : '(Sole / OPC / INDIVIDUAL)' }}</p>
                @if ($isBusinessContact)
                    <p class="font-semibold uppercase italic">CORPORATION / PARTNERSHIP / OTHER JURIDICAL ENTITY</p>
                    <p class="text-[10px] uppercase">CASA-F-005-V1.0-03.16.26</p>
                @endif
                @if (! $isBusinessContact)
                <p class="font-bold uppercase">SPECIMEN SIGNATURE CARD</p>
                @endif
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BIF NO.</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly || $clientMode)
                    <div class="{{ $readonlyClass }}">{{ $form['bif_no'] ?: '-' }}</div>
                @else
                    <input name="bif_no" value="{{ old('bif_no', $form['bif_no'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2 font-semibold">DATE</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['date'] ?: '-' }}</div>
                @else
                    <input type="date" name="date" value="{{ old('date', $form['date'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
        </tr>
        @if ($isBusinessContact)
        <tr>
            <td colspan="4" class="border border-gray-300 p-2">
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    @foreach (['new' => 'New Client', 'existing' => 'Existing Client', 'change' => 'Change Information'] as $value => $label)
                        <label class="inline-flex items-center gap-2">
                            @if ($readonly)
                                <input type="checkbox" disabled @checked(($form['client_type'] ?? '') === $value) class="h-4 w-4 rounded border-gray-300 text-blue-600">
                            @else
                                <input type="radio" name="client_type" value="{{ $value }}" @checked(old('client_type', $form['client_type'] ?? 'new') === $value) class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            @endif
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </td>
        </tr>
        @endif
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS NAME</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['business_name_left'] ?: '-' }}</div>
                @else
                    <input name="business_name_left" value="{{ old('business_name_left', $form['business_name_left'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS NAME</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['business_name_right'] ?: '-' }}</div>
                @else
                    <input name="business_name_right" value="{{ old('business_name_right', $form['business_name_right'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS ACCOUNT NUMBER</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['account_number_left'] ?: '-' }}</div>
                @else
                    <input name="account_number_left" value="{{ old('account_number_left', $form['account_number_left'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2 font-semibold">BUSINESS ACCOUNT NUMBER</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['account_number_right'] ?: '-' }}</div>
                @else
                    <input name="account_number_right" value="{{ old('account_number_right', $form['account_number_right'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 font-semibold">SIGNATURE COMBINATION</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['signature_combination'] ?: '-' }}</div>
                @else
                    <input name="signature_combination" value="{{ old('signature_combination', $form['signature_combination'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2 font-semibold">SIGNATURE CLASS</td>
            <td class="border border-gray-300 p-2">
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['signature_class'] ?: '-' }}</div>
                @else
                    <input name="signature_class" value="{{ old('signature_class', $form['signature_class'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" class="border border-gray-300 p-2 text-center font-bold uppercase">AUTHORIZED SIGNATORIES</td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <div class="space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1"><span class="font-semibold">CLIENT NAME:</span></div>
                        <div class="col-span-2">
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ $form['left_client_name'] ?: '-' }}</div>
                            @else
                                <input name="left_client_name" value="{{ old('left_client_name', $form['left_client_name'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-semibold">CIF NO.:</span>
                            @if ($readonly || $clientMode)
                                <div class="{{ $readonlyClass }}">{{ $form['left_cif_no'] ?: '-' }}</div>
                            @else
                                <input name="left_cif_no" value="{{ old('left_cif_no', $form['left_cif_no'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                        <div>
                            <span class="font-semibold">CIF Dated:</span>
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ $form['left_cif_dated'] ?: '-' }}</div>
                            @else
                                <input type="date" name="left_cif_dated" value="{{ old('left_cif_dated', $form['left_cif_dated'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                    </div>
                    @foreach ([0, 1, 2] as $index)
                        <div class="rounded border border-gray-300 p-2">
                            <p class="mb-1 font-semibold">{{ $index + 1 }}</p>
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ data_get($signatories[$index] ?? [], 'name') ?: '-' }}</div>
                            @else
                                <input name="signatory_names[]" value="{{ old('signatory_names.'.$index, data_get($signatories[$index] ?? [], 'name')) }}" placeholder="Name" class="{{ $inputClass }}">
                                <p class="mt-1 text-[11px] text-gray-500">Signature (draw/upload optional)</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <div class="space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1"><span class="font-semibold">CLIENT NAME:</span></div>
                        <div class="col-span-2">
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ $form['right_client_name'] ?: '-' }}</div>
                            @else
                                <input name="right_client_name" value="{{ old('right_client_name', $form['right_client_name'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-semibold">CIF NO.:</span>
                            @if ($readonly || $clientMode)
                                <div class="{{ $readonlyClass }}">{{ $form['right_cif_no'] ?: '-' }}</div>
                            @else
                                <input name="right_cif_no" value="{{ old('right_cif_no', $form['right_cif_no'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                        <div>
                            <span class="font-semibold">CIF Dated:</span>
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ $form['right_cif_dated'] ?: '-' }}</div>
                            @else
                                <input type="date" name="right_cif_dated" value="{{ old('right_cif_dated', $form['right_cif_dated'] ?? '') }}" class="{{ $inputClass }}">
                            @endif
                        </div>
                    </div>
                    @foreach ([3, 4, 5] as $index)
                        <div class="rounded border border-gray-300 p-2">
                            <p class="mb-1 font-semibold">{{ $index - 2 }}</p>
                            @if ($readonly)
                                <div class="{{ $readonlyClass }}">{{ data_get($signatories[$index] ?? [], 'name') ?: '-' }}</div>
                            @else
                                <input name="signatory_names[]" value="{{ old('signatory_names.'.$index, data_get($signatories[$index] ?? [], 'name')) }}" placeholder="Name" class="{{ $inputClass }}">
                                <p class="mt-1 text-[11px] text-gray-500">Signature (draw/upload optional)</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top text-[11px] leading-relaxed">
                By my/our signature(s) herein, I/we certify that the information and specimen signatures provided are true, correct, and duly authorized for use by JK&C Inc. The above-listed individual(s) are the authorized signatory/ies of the business entity or the individual client, and JK&C Inc. may rely on these specimen signatures for verification, documentation, and official transactions. I/we undertake to notify JK&C Inc. in writing of any change to the authorized signatory/ies or their authority. In the absence of a Board Resolution, Secretary’s Certificate, or Special Power of Attorney (SPA), the signature(s) appearing herein shall be presumed to be the true and rightful authorized signatory/ies of the business entity or individual client, unless otherwise notified in writing.
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="text-center font-bold uppercase">AUTHENTICATED BY CORPORATE SECRETARY / AUTHORIZED REPRESENTATIVE</p>
                <div class="mt-2 space-y-2">
                    <div>
                        <span class="font-semibold">Board Resolution / SPA No.</span>
                        @if ($readonly)
                            <div class="{{ $readonlyClass }}">{{ $form['board_resolution_spa_no'] ?: '-' }}</div>
                        @else
                            <input name="board_resolution_spa_no" value="{{ old('board_resolution_spa_no', $form['board_resolution_spa_no'] ?? '') }}" class="{{ $inputClass }}">
                        @endif
                    </div>
                    <div>
                        <span class="font-semibold">Board Resolution / SPA Date</span>
                        @if ($readonly)
                            <div class="{{ $readonlyClass }}">{{ $form['board_resolution_spa_date'] ?: '-' }}</div>
                        @else
                            <input type="date" name="board_resolution_spa_date" value="{{ old('board_resolution_spa_date', $form['board_resolution_spa_date'] ?? '') }}" class="{{ $inputClass }}">
                        @endif
                    </div>
                    <div>
                        <span class="font-semibold">Authenticated by Corporate Secretary / Authorized Representative</span>
                        @if ($readonly)
                            <div class="{{ $readonlyClass }}">{{ $form['authenticated_by'] ?: '-' }}</div>
                        @else
                            <input name="authenticated_by" value="{{ old('authenticated_by', $form['authenticated_by'] ?? '') }}" class="{{ $inputClass }}">
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Authorized Signatory's Signature over Printed Name</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['signature_over_printed_name'] ?: '-' }}</div>
                @else
                    <input name="signature_over_printed_name" value="{{ old('signature_over_printed_name', $form['signature_over_printed_name'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Authorized Signatory Signature</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['authorized_signatory_signature'] ?: '-' }}</div>
                @else
                    <input name="authorized_signatory_signature" value="{{ old('authorized_signatory_signature', $form['authorized_signatory_signature'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2">
                <p class="text-center font-semibold">Date</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['authorized_signatory_date'] ?: '-' }}</div>
                @else
                    <input type="date" name="authorized_signatory_date" value="{{ old('authorized_signatory_date', $form['authorized_signatory_date'] ?? '') }}" class="{{ $inputClass }}">
                @endif
            </td>
        </tr>
        @if (! $clientMode)
        <tr>
            <td colspan="4" class="border border-gray-300 p-2 text-center font-bold uppercase">FOR JKNC USE ONLY</td>
        </tr>
        <tr>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="font-semibold uppercase">PROCESSING INSTRUCTION (FOR JK&C USE ONLY)</p>
                @if ($readonly)
                    <p class="mt-2 text-[11px] leading-relaxed text-gray-700">{{ $form['processing_instruction'] ?: '-' }}</p>
                @else
                    <textarea name="processing_instruction" rows="4" class="mt-2 w-full rounded border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('processing_instruction', $form['processing_instruction'] ?? '') }}</textarea>
                @endif
            </td>
            <td colspan="2" class="border border-gray-300 p-2 align-top">
                <p class="font-semibold uppercase">REMARKS:</p>
                @if ($readonly)
                    <div class="mt-2 min-h-24 rounded border border-gray-200 bg-gray-50 px-2 py-2 text-sm text-gray-700">{{ $form['remarks'] ?: '-' }}</div>
                @else
                    <textarea name="remarks" rows="4" class="mt-2 w-full rounded border border-gray-300 px-2 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('remarks', $form['remarks'] ?? '') }}</textarea>
                @endif
            </td>
        </tr>
        <tr>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">SALES & MARKETING</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['sales_marketing'] ?: '-' }}</div>
                @else
                    <input name="sales_marketing" value="{{ old('sales_marketing', $form['sales_marketing'] ?? '') }}" class="{{ $inputClass }}">
                @endif
                <p class="mt-3 border-t border-gray-400 pt-1 text-[11px]">Signature over Printed Name</p>
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">PROCESSED BY / DATE</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ ($form['processed_by'] ?: '-') . ' / ' . ($form['processed_date'] ?: '-') }}</div>
                @else
                    <input name="processed_by" value="{{ old('processed_by', $form['processed_by'] ?? '') }}" placeholder="Processed By" class="{{ $inputClass }}">
                    <input type="date" name="processed_date" value="{{ old('processed_date', $form['processed_date'] ?? '') }}" class="mt-2 {{ $inputClass }}">
                @endif
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">FINANCE</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ $form['finance'] ?: '-' }}</div>
                @else
                    <input name="finance" value="{{ old('finance', $form['finance'] ?? '') }}" class="{{ $inputClass }}">
                @endif
                <p class="mt-3 border-t border-gray-400 pt-1 text-[11px]">Signature over Printed Name</p>
            </td>
            <td class="border border-gray-300 p-2 text-center">
                <p class="font-semibold uppercase">SCANNED BY / DATE</p>
                @if ($readonly)
                    <div class="{{ $readonlyClass }}">{{ ($form['scanned_by'] ?: '-') . ' / ' . ($form['scanned_date'] ?: '-') }}</div>
                @else
                    <input name="scanned_by" value="{{ old('scanned_by', $form['scanned_by'] ?? '') }}" placeholder="Scanned By" class="{{ $inputClass }}">
                    <input type="date" name="scanned_date" value="{{ old('scanned_date', $form['scanned_date'] ?? '') }}" class="mt-2 {{ $inputClass }}">
                @endif
            </td>
        </tr>
        @endif
    </table>
</div>
