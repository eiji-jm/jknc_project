<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Completion | JK&C INC.</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b bg-gray-50 flex flex-col sm:flex-row sm:items-center gap-4">
                <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly & Company" class="h-14 w-auto object-contain">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">John Kelly &amp; Company</p>
                    <h1 class="text-2xl font-semibold text-gray-900 mt-2">Supplier Completion Form</h1>
                    <p class="text-sm text-gray-600 mt-2">Complete the remaining supplier information below and submit it back for internal review.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mx-6 mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('finance.supplier.completion.submit', $record['share_token'] ?? '') }}" class="px-6 py-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Supplier Code / ID</label>
                        <input type="text" name="record_number" value="{{ old('record_number', $record['record_number'] ?? '') }}" class="w-full border rounded-md p-2 bg-gray-100" readonly>
                        @error('record_number')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Business Name</label>
                        <input type="text" name="record_title" value="{{ old('record_title', '') }}" class="w-full border rounded-md p-2" placeholder="Enter business name">
                        @error('record_title')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Created Date</label>
                        <input type="date" name="record_date" value="{{ old('record_date', $record['record_date'] ?? '') }}" class="w-full border rounded-md p-2 bg-gray-100" readonly>
                        @error('record_date')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Completion Mode</label>
                        <input type="text" value="Send to Supplier" class="w-full border rounded-md p-2 bg-gray-100" disabled>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Trade Name</label>
                        <input type="text" name="data[trade_name]" value="{{ old('data.trade_name', $record['data']['trade_name'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Supplier Type</label>
                        <input type="text" name="data[supplier_type]" value="{{ old('data.supplier_type', $record['data']['supplier_type'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Representative Full Name</label>
                        <input type="text" name="data[representative_full_name]" value="{{ old('data.representative_full_name', $record['data']['representative_full_name'] ?? '') }}" class="w-full border rounded-md p-2">
                        @error('data.representative_full_name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Designation</label>
                        <input type="text" name="data[designation]" value="{{ old('data.designation', $record['data']['designation'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email Address</label>
                        <input type="email" name="data[email_address]" value="{{ old('data.email_address', $record['data']['email_address'] ?? '') }}" class="w-full border rounded-md p-2">
                        @error('data.email_address')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number</label>
                        <input type="text" name="data[phone_number]" value="{{ old('data.phone_number', $record['data']['phone_number'] ?? '') }}" class="w-full border rounded-md p-2">
                        @error('data.phone_number')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Alternate Contact Number</label>
                        <input type="text" name="data[alternate_contact_number]" value="{{ old('data.alternate_contact_number', $record['data']['alternate_contact_number'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">TIN</label>
                        <input type="text" name="data[tin]" value="{{ old('data.tin', $record['data']['tin'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">VAT / Non-VAT</label>
                        <select name="data[vat_status]" class="w-full border rounded-md p-2">
                            <option value="">Select VAT status</option>
                            <option value="VAT" @selected(old('data.vat_status', $record['data']['vat_status'] ?? '') === 'VAT')>VAT</option>
                            <option value="Non-VAT" @selected(old('data.vat_status', $record['data']['vat_status'] ?? '') === 'Non-VAT')>Non-VAT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Terms</label>
                        <input type="text" name="data[payment_terms]" value="{{ old('data.payment_terms', $record['data']['payment_terms'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Accreditation Status</label>
                        <select name="data[accreditation_status]" class="w-full border rounded-md p-2">
                            <option value="">Select status</option>
                            <option value="Pending" @selected(old('data.accreditation_status', $record['data']['accreditation_status'] ?? '') === 'Pending')>Pending</option>
                            <option value="For Accreditation" @selected(old('data.accreditation_status', $record['data']['accreditation_status'] ?? '') === 'For Accreditation')>For Accreditation</option>
                            <option value="Accredited" @selected(old('data.accreditation_status', $record['data']['accreditation_status'] ?? '') === 'Accredited')>Accredited</option>
                            <option value="Blacklisted" @selected(old('data.accreditation_status', $record['data']['accreditation_status'] ?? '') === 'Blacklisted')>Blacklisted</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Business Address</label>
                        <textarea name="data[business_address]" rows="3" class="w-full border rounded-md p-2">{{ old('data.business_address', $record['data']['business_address'] ?? '') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Billing Address</label>
                        <textarea name="data[billing_address]" rows="3" class="w-full border rounded-md p-2">{{ old('data.billing_address', $record['data']['billing_address'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank Name</label>
                        <input type="text" name="data[bank_name]" value="{{ old('data.bank_name', $record['data']['bank_name'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank Account Name</label>
                        <input type="text" name="data[bank_account_name]" value="{{ old('data.bank_account_name', $record['data']['bank_account_name'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank Account Number</label>
                        <input type="text" name="data[bank_account_number]" value="{{ old('data.bank_account_number', $record['data']['bank_account_number'] ?? '') }}" class="w-full border rounded-md p-2">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Remarks</label>
                        <textarea name="data[remarks]" rows="3" class="w-full border rounded-md p-2">{{ old('data.remarks', $record['data']['remarks'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-between gap-4">
                    <div class="text-sm text-gray-500">
                        Supplier-facing completion page for the Finance Operations module.
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">
                        Submit Completion
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
