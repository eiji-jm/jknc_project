@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('company.kyc', $company->id) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>KYC</span>
                </a>
                <span>/</span>
                <span class="font-semibold text-gray-900">Client Intake Form Review</span>
            </div>
        </div>

        <section class="bg-gray-50 p-4 min-h-[760px]">
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $cif->title ?: 'Client Intake Form' }}</h1>
                        <p class="mt-1 text-sm text-gray-500">Submitted details for {{ $company->company_name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-8 items-center rounded-full border border-blue-200 bg-blue-50 px-3 text-xs font-semibold text-blue-700">
                            {{ ucfirst($cif->status) }}
                        </span>
                        @if ($cif->submitted_at)
                            <span class="text-xs text-gray-500">Submitted {{ $cif->submitted_at->format('M j, Y g:i A') }}</span>
                        @endif
                    </div>
                </div>

                @if (session('cif_success'))
                    <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                        {{ session('cif_success') }}
                    </div>
                @endif

                <div class="mt-6 space-y-6">
                    <div class="rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Client Information</h2>
                        <dl class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                            <div><dt class="font-medium text-gray-700">First Name</dt><dd class="text-gray-600">{{ $cif->first_name ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Last Name</dt><dd class="text-gray-600">{{ $cif->last_name ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Preferred Name</dt><dd class="text-gray-600">{{ $cif->preferred_name ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Patient Identifier</dt><dd class="text-gray-600">{{ $cif->patient_identifier ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Gender</dt><dd class="text-gray-600">{{ $cif->gender ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Preferred Pronouns</dt><dd class="text-gray-600">{{ $cif->preferred_pronouns ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Date of Birth</dt><dd class="text-gray-600">{{ $cif->date_of_birth ? $cif->date_of_birth->format('F j, Y') : '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Marital Status</dt><dd class="text-gray-600">{{ $cif->marital_status ?: '-' }}</dd></div>
                            <div class="sm:col-span-2"><dt class="font-medium text-gray-700">Address</dt><dd class="text-gray-600">{{ $cif->address ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">City</dt><dd class="text-gray-600">{{ $cif->city ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">State</dt><dd class="text-gray-600">{{ $cif->state ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Zip Code</dt><dd class="text-gray-600">{{ $cif->zip_code ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Email</dt><dd class="text-gray-600">{{ $cif->email ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Preferred Phone Number</dt><dd class="text-gray-600">{{ $cif->preferred_phone ?: '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Emergency Contact</h2>
                        <dl class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-5">
                            <div class="lg:col-span-2"><dt class="font-medium text-gray-700">Contact 1 - Full Name</dt><dd class="text-gray-600">{{ $cif->emergency_contact_1_name ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Relationship</dt><dd class="text-gray-600">{{ $cif->emergency_contact_1_relationship ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Home Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_1_home_phone ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Cell Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_1_cell_phone ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Work Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_1_work_phone ?: '-' }}</dd></div>

                            <div class="lg:col-span-2"><dt class="font-medium text-gray-700">Contact 2 - Full Name</dt><dd class="text-gray-600">{{ $cif->emergency_contact_2_name ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Relationship</dt><dd class="text-gray-600">{{ $cif->emergency_contact_2_relationship ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Home Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_2_home_phone ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Cell Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_2_cell_phone ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Work Phone</dt><dd class="text-gray-600">{{ $cif->emergency_contact_2_work_phone ?: '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Insurance Information</h2>
                        <dl class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                            <div><dt class="font-medium text-gray-700">Insurance Carrier</dt><dd class="text-gray-600">{{ $cif->insurance_carrier ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Insurance Plan</dt><dd class="text-gray-600">{{ $cif->insurance_plan ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Contact Number</dt><dd class="text-gray-600">{{ $cif->insurance_contact_number ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Policy Number</dt><dd class="text-gray-600">{{ $cif->policy_number ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Group Number</dt><dd class="text-gray-600">{{ $cif->group_number ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Social Security Number</dt><dd class="text-gray-600">{{ $cif->social_security_number ?: '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Referrals and Adjunctive Care</h2>
                        <dl class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                            <div><dt class="font-medium text-gray-700">Currently under medical care?</dt><dd class="text-gray-600">{{ $cif->under_medical_care ? 'Yes' : 'No' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">If yes, for what?</dt><dd class="text-gray-600">{{ $cif->medical_care_for ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Primary Care Physician</dt><dd class="text-gray-600">{{ $cif->primary_care_physician ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-700">Contact Number</dt><dd class="text-gray-600">{{ $cif->physician_contact_number ?: '-' }}</dd></div>
                            <div class="sm:col-span-2"><dt class="font-medium text-gray-700">Address</dt><dd class="text-gray-600">{{ $cif->physician_address ?: '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Health Concerns / Symptoms</h2>
                        <div class="mt-3 space-y-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-700">Describe your main concerns</p>
                                <p class="mt-1 text-gray-600">{{ $cif->main_concerns ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">When did your chief complaint or illness begin?</p>
                                <p class="mt-1 text-gray-600">{{ $cif->illness_begin ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">What are your goals for today’s visit and for your long-term health?</p>
                                <p class="mt-1 text-gray-600">{{ $cif->visit_goals ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <a href="{{ route('company.kyc', $company->id) }}" class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                        Back to KYC
                    </a>
                    <a href="{{ route('company.cif.edit', ['company' => $company->id, 'cif' => $cif->id]) }}" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center justify-center">
                        Edit CIF
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
