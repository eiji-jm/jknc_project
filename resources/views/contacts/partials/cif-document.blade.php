@php
    $text = static fn ($key, $fallback = '-') => filled($cifData[$key] ?? null) ? $cifData[$key] : $fallback;
    $checked = static fn (bool $state): string => $state ? 'bg-gray-900' : 'bg-white';
    $hasFund = static fn (string $value): bool => in_array($value, $cifData['source_of_funds'] ?? [], true);
@endphp

<div class="mx-auto w-full max-w-5xl border border-gray-700 bg-white text-[11px] leading-tight text-black">
    <div class="border-b border-gray-700 px-4 py-2 text-center">
        <h1 class="text-xl font-bold uppercase tracking-wide">Client Information Form</h1>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-7 border-r border-gray-700 p-2">
            <div class="mb-2 text-[10px] text-gray-600">DATE</div>
            <div class="h-6 border-b border-gray-700 px-1">{{ $text('cif_date', '') }}</div>
        </div>
        <div class="col-span-5 p-2">
            <div class="mb-2 text-[10px] text-gray-600">CIF NO.</div>
            <div class="h-6 border-b border-gray-700 px-1">{{ $text('cif_no', '') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2">New Client <span class="ml-2 inline-block h-3 w-3 border border-gray-700 align-middle {{ $checked((bool) ($cifData['is_new_client'] ?? false)) }}"></span></div>
        <div class="col-span-4 border-r border-gray-700 p-2">Existing Client <span class="ml-2 inline-block h-3 w-3 border border-gray-700 align-middle {{ $checked((bool) ($cifData['is_existing_client'] ?? false)) }}"></span></div>
        <div class="col-span-4 p-2">Change Information <span class="ml-2 inline-block h-3 w-3 border border-gray-700 align-middle {{ $checked((bool) ($cifData['is_change_information'] ?? false)) }}"></span></div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">FIRST NAME</div><div class="mt-1 min-h-8">{{ $text('first_name') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">LAST NAME</div><div class="mt-1 min-h-8">{{ $text('last_name') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">NAME EXTENSION</div><div class="mt-1 min-h-8">{{ $text('name_extension') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">MIDDLE NAME</div><div class="mt-1 min-h-8">{{ $text('middle_name') }}</div></div>
        <div class="col-span-1 p-2">
            <div class="mb-1 inline-block h-3 w-3 border border-gray-700 {{ $checked((bool) ($cifData['no_middle_name'] ?? false)) }}"></div>
            <div class="text-[9px]">No Middle Name</div>
            <div class="mb-1 mt-2 inline-block h-3 w-3 border border-gray-700 {{ $checked((bool) ($cifData['only_first_name'] ?? false)) }}"></div>
            <div class="text-[9px]">Only First Name</div>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-9 border-r border-gray-700 p-2"><div class="text-[10px]">PRESENT ADDRESS</div><div class="mt-1 min-h-6">{{ $text('present_address_line1') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">ZIP CODE</div><div class="mt-1 min-h-6">{{ $text('zip_code') }}</div></div>
    </div>
    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-9 border-r border-gray-700 p-2"><div class="text-[10px]">PRESENT ADDRESS (2nd Line)</div><div class="mt-1 min-h-6">{{ $text('present_address_line2') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">ZIP CODE</div><div class="mt-1 min-h-6">{{ $text('zip_code') }}</div></div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-6 border-r border-gray-700 p-2"><div class="text-[10px]">EMAIL ADDRESS</div><div class="mt-1 min-h-6">{{ $text('email') }}</div></div>
        <div class="col-span-6 p-2"><div class="text-[10px]">PHONE NO. / MOBILE NO.</div><div class="mt-1 min-h-6">{{ $text('mobile') }}</div></div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">DATE OF BIRTH</div><div class="mt-1 min-h-6">{{ $text('date_of_birth') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">PLACE OF BIRTH</div><div class="mt-1 min-h-6">{{ $text('place_of_birth') }}</div></div>
        <div class="col-span-7 p-2">
            <div class="text-[10px]">CITIZENSHIP / NATIONALITY</div>
            <div class="mt-1 min-h-6">{{ $text('citizenship_nationality') }}</div>
            <div class="mt-2 flex flex-wrap gap-4 text-[10px]">
                <span>Filipino <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['citizenship_type'] ?? '') === 'filipino') }}"></span></span>
                <span>Foreigner <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['citizenship_type'] ?? '') === 'foreigner') }}"></span></span>
                <span>Dual Citizen <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['citizenship_type'] ?? '') === 'dual_citizen') }}"></span></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-2 border-r border-gray-700 p-2 text-[10px]">GENDER
            <div class="mt-1">Male <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['gender'] ?? '') === 'male') }}"></span></div>
            <div>Female <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['gender'] ?? '') === 'female') }}"></span></div>
        </div>
        <div class="col-span-10 p-2 text-[10px]">Civil Status
            <div class="mt-1 flex flex-wrap gap-4">
                <span>Single <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['civil_status'] ?? '') === 'single') }}"></span></span>
                <span>Separated <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['civil_status'] ?? '') === 'separated') }}"></span></span>
                <span>Widowed <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['civil_status'] ?? '') === 'widowed') }}"></span></span>
                <span>Married <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked(($cifData['civil_status'] ?? '') === 'married') }}"></span></span>
                <span>Spouse's Name: {{ $text('spouse_name', '') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">NATURE OF WORK / BUSINESS</div><div class="mt-1 min-h-6">{{ $text('nature_of_work_business') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">TIN</div><div class="mt-1 min-h-6">{{ $text('tin') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">OTHER GOVERNMENT ID</div><div class="mt-1 min-h-6">{{ $text('other_government_id') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">ID NUMBER</div><div class="mt-1 min-h-6">{{ $text('id_number') }}</div></div>
    </div>

    <div class="border-b border-gray-700 p-2">
        <div class="text-[10px]">MOTHER'S MAIDEN NAME</div>
        <div class="mt-1 min-h-6">{{ $text('mothers_maiden_name') }}</div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700 p-2 text-[10px]">
        <div class="col-span-2 font-semibold">SOURCE OF FUNDS</div>
        <div class="col-span-10 flex flex-wrap gap-x-6 gap-y-1">
            <span>Salary <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('salary')) }}"></span></span>
            <span>Remittance <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('remittance')) }}"></span></span>
            <span>Business <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('business')) }}"></span></span>
            <span>Others <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('others')) }}"></span> {{ $text('source_of_funds_other_text', '') }}</span>
            <span>Commission / Fees <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('commission_fees')) }}"></span></span>
            <span>Retirement / Pension <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked($hasFund('retirement_pension')) }}"></span></span>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">IF FOREIGNER PASSPORT NO.</div><div class="mt-1 min-h-6">{{ $text('foreigner_passport_no') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">EXPIRY DATE</div><div class="mt-1 min-h-6">{{ $text('foreigner_passport_expiry_date') }}</div></div>
        <div class="col-span-6 p-2"><div class="text-[10px]">PLACE OF ISSUE</div><div class="mt-1 min-h-6">{{ $text('foreigner_passport_place_of_issue') }}</div></div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700">
        <div class="col-span-4 border-r border-gray-700 p-2"><div class="text-[10px]">IF FOREIGNER ACR ID NO.</div><div class="mt-1 min-h-6">{{ $text('foreigner_acr_id_no') }}</div></div>
        <div class="col-span-2 border-r border-gray-700 p-2"><div class="text-[10px]">EXPIRY DATE</div><div class="mt-1 min-h-6">{{ $text('foreigner_acr_expiry_date') }}</div></div>
        <div class="col-span-3 border-r border-gray-700 p-2"><div class="text-[10px]">PLACE OF ISSUE</div><div class="mt-1 min-h-6">{{ $text('foreigner_acr_place_of_issue') }}</div></div>
        <div class="col-span-3 p-2"><div class="text-[10px]">VISA STATUS</div><div class="mt-1 min-h-6">{{ $text('visa_status') }}</div></div>
    </div>

    <div class="border-b border-gray-700 p-2">
        <div class="mb-1 text-center font-semibold uppercase">Acknowledgment</div>
        <p class="px-1 text-[11px] leading-snug text-gray-700 text-justify">
            By signing this Client Information Form, I certify that all personal information provided herein is true, correct, and complete to the best of my knowledge. I agree to comply with the policies, procedures, and service guidelines of JK&C Inc. and authorize JK&C Inc., its officers, employees, consultants, and representatives to collect, verify, record, process, store, and use the information provided for purposes of client registration, due diligence, compliance verification, service engagement, documentation, billing, and regulatory requirements. In accordance with the Data Privacy Act of 2012 (Republic Act No. 10173), I voluntarily consent to the collection, processing, storage, and lawful use of my personal information contained in this form. I acknowledge that the information provided shall constitute the official client information on record of JK&C Inc. and may be relied upon in official communications, notices, service documents, billing statements, formal correspondence, and demand letters relating to services rendered or obligations arising from the engagement. I undertake to promptly notify JK&C Inc. of any changes to the information provided and hereby waive and release JK&C Inc., its officers, employees, and representatives from any liability arising from reliance on the information provided, except in cases of gross negligence or willful misconduct.
        </p>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700 p-2">
        <div class="col-span-6 border-r border-gray-700 px-2">
            <div class="mt-6 border-b border-gray-700 text-center">{{ $text('sig_name_left', '') }}</div>
            <div class="text-center text-[10px]">Signature over Printed Name</div>
            <div class="mt-4 border-b border-gray-700 text-center">{{ $text('sig_position_left', '') }}</div>
            <div class="text-center text-[10px]">Position</div>
        </div>
        <div class="col-span-6 px-2">
            <div class="mt-6 border-b border-gray-700 text-center">{{ $text('sig_name_right', '') }}</div>
            <div class="text-center text-[10px]">Signature over Printed Name</div>
            <div class="mt-4 border-b border-gray-700 text-center">{{ $text('sig_position_right', '') }}</div>
            <div class="text-center text-[10px]">Position</div>
        </div>
    </div>

    <div class="border-b border-gray-700 p-2">
        <div class="text-center font-semibold uppercase">Client Onboarding Requirements</div>
        <div class="mt-2 grid gap-1 text-[10px]">
            <div>1. Two (2) Valid Government IDs <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked((bool) ($cifData['onboarding_two_valid_ids'] ?? false)) }}"></span></div>
            <div>2. TIN ID <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked((bool) ($cifData['onboarding_tin_id'] ?? false)) }}"></span></div>
            <div>3. Authorized Signatory / Specimen Signature Card <span class="ml-1 inline-block h-3 w-3 border border-gray-700 {{ $checked((bool) ($cifData['onboarding_authorized_signatory_card'] ?? false)) }}"></span></div>
        </div>
    </div>

    <div class="grid grid-cols-12 border-b border-gray-700 text-[10px]">
        <div class="col-span-3 border-r border-gray-700 p-2">
            <div class="font-semibold">REFERRED BY / DATE</div>
            <div class="mt-1">{{ $text('referred_by_footer', '') }} {{ filled($cifData['referred_date'] ?? null) ? ' / '.$cifData['referred_date'] : '' }}</div>
            <div class="mt-4 border-b border-gray-700 text-center">&nbsp;</div>
            <div class="text-center">Signature over Printed Name</div>
        </div>
        <div class="col-span-3 border-r border-gray-700 p-2">
            <div class="font-semibold">SALES &amp; MARKETING</div>
            <div class="mt-1">{{ $text('sales_marketing_footer', '') }}</div>
            <div class="mt-4 border-b border-gray-700 text-center">&nbsp;</div>
            <div class="text-center">Signature over Printed Name</div>
        </div>
        <div class="col-span-3 border-r border-gray-700 p-2">
            <div class="font-semibold">FINANCE</div>
            <div class="mt-1">{{ $text('finance_footer', '') }}</div>
            <div class="mt-4 border-b border-gray-700 text-center">&nbsp;</div>
            <div class="text-center">Signature over Printed Name</div>
        </div>
        <div class="col-span-3 p-2">
            <div class="font-semibold">PRESIDENT</div>
            <div class="mt-1">{{ $text('president_footer', '') }}</div>
            <div class="mt-4 border-b border-gray-700 text-center">&nbsp;</div>
            <div class="text-center">Signature over Printed Name</div>
        </div>
    </div>
</div>
