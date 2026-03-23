@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="mx-auto max-w-5xl space-y-4 px-4">
        @if (session('success'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">
            <h1 class="text-xl font-semibold text-gray-900">Client Information Form</h1>
            <p class="mt-1 text-sm text-gray-500">Complete the missing CIF details below. Your submission will update the contact KYC profile.</p>
        </div>

        <form method="POST" action="{{ $clientFormAction }}" class="space-y-5">
            @csrf

            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Top / Meta</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Date</label><input type="date" name="cif_date" value="{{ old('cif_date', $cifData['cif_date'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">CIF No.</label><input name="cif_no" value="{{ old('cif_no', $cifData['cif_no'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500" readonly></div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Identity</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">First Name</label><input required name="first_name" value="{{ old('first_name', $cifData['first_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Last Name</label><input name="last_name" value="{{ old('last_name', $cifData['last_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Middle Name</label><input name="middle_name" value="{{ old('middle_name', $cifData['middle_name'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Name Extension</label><input name="name_extension" value="{{ old('name_extension', $cifData['name_extension'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Contact</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label><input type="email" name="email" value="{{ old('email', $cifData['email'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Phone No. / Mobile No.</label><input name="mobile" value="{{ old('mobile', $cifData['mobile'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Present Address</label><input name="present_address_line1" value="{{ old('present_address_line1', $cifData['present_address_line1'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Birth / Citizenship</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $cifData['date_of_birth'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Place of Birth</label><input name="place_of_birth" value="{{ old('place_of_birth', $cifData['place_of_birth'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Citizenship / Nationality</label><input name="citizenship_nationality" value="{{ old('citizenship_nationality', $cifData['citizenship_nationality'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach (['filipino' => 'Filipino', 'foreigner' => 'Foreigner', 'dual_citizen' => 'Dual Citizen'] as $value => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="radio" name="citizenship_type" value="{{ $value }}" @checked(old('citizenship_type', $cifData['citizenship_type'] ?? '') === $value)> {{ $label }}</label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Gender / Civil Status</h3>
                <div class="mb-3 flex flex-wrap gap-2">
                    @foreach (['male' => 'Male', 'female' => 'Female'] as $value => $label)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm"><input type="radio" name="gender" value="{{ $value }}" @checked(old('gender', $cifData['gender'] ?? '') === $value)> {{ $label }}</label>
                    @endforeach
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">Civil Status</label><input name="civil_status" value="{{ old('civil_status', $cifData['civil_status'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">TIN</label><input name="tin" value="{{ old('tin', $cifData['tin'] ?? '') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm"></div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Submit CIF</button>
            </div>
        </form>
    </div>
</div>
@endsection
