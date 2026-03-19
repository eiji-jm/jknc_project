@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', ['company' => $company])

        <div class="bg-gray-50 p-4">
            @if (session('cif_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('cif_success') }}
                </div>
            @endif

            @if (session('bif_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('bif_success') }}
                </div>
            @endif

            @if (session('kyc_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('kyc_success') }}
                </div>
            @endif

            <div id="companyKycApp" class="rounded-md border border-gray-200 bg-white" x-data="{ showDrawer: false }">
                <div x-cloak>
                    <div x-show="showDrawer" class="fixed inset-0 bg-black/40 z-40" @click="showDrawer = false"></div>
                    <div
                        x-show="showDrawer"
                        class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
                        x-transition:enter="transform transition ease-in-out duration-200"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-200"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                    >
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div>
                                <h2 id="kycDrawerTitle" class="text-lg font-semibold">Add KYC Record</h2>
                                <p class="text-sm text-gray-500">Document records here are automatically associated with {{ $company->company_name }}.</p>
                            </div>
                            <div class="flex-1"></div>
                            <button class="text-gray-500 hover:text-gray-700" @click="showDrawer = false" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form id="kycRecordForm" method="POST" action="{{ route('company.kyc.store', $company->id) }}" class="flex min-h-0 flex-1 flex-col">
                            @csrf
                            <input type="hidden" id="kycRecordFormMethod" name="_method" value="POST">

                            <div class="p-6 overflow-y-auto grid grid-cols-1 gap-4">
                                <div>
                                    <label class="text-xs text-gray-600">Date Uploaded</label>
                                    <input id="kycDateUploadedInput" name="date_uploaded" type="date" value="{{ old('date_uploaded') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Uploaded By</label>
                                    <input id="kycUploadedByInput" name="uploaded_by" type="text" value="{{ old('uploaded_by') }}" placeholder="Uploader name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Client</label>
                                    <input type="text" value="{{ $company->company_name }}" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-500">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">TIN</label>
                                    <input id="kycTinInput" name="tin" type="text" value="{{ old('tin') }}" placeholder="123-456-789-000" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Government Type</label>
                                    <select id="kycGovernmentTypeInput" name="government_type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white" onchange="syncGovernmentBodyOptions()">
                                        @foreach ($governmentTypes as $type)
                                            <option value="{{ $type }}" @selected(old('government_type') === $type)>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Government Body</label>
                                    <select id="kycGovernmentBodyInput" name="government_body" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white"></select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Registration Status</label>
                                    <select id="kycRegistrationStatusInput" name="registration_status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                        @foreach ($registrationStatuses as $status)
                                            <option value="{{ $status }}" @selected(old('registration_status') === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Registration Date</label>
                                    <input id="kycRegistrationDateInput" name="registration_date" type="date" value="{{ old('registration_date') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Registration No.</label>
                                    <input id="kycRegistrationNoInput" name="registration_no" type="text" value="{{ old('registration_no') }}" placeholder="Registration number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Document File</label>
                                    <input id="kycDocumentFileInput" name="document_file" type="text" value="{{ old('document_file') }}" placeholder="document.pdf" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Remarks</label>
                                    <textarea id="kycRemarksInput" name="remarks" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('remarks') }}</textarea>
                                </div>
                                @if ($errors->any())
                                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                        Please complete the required KYC document fields.
                                    </div>
                                @endif
                            </div>

                            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showDrawer = false" type="button">
                                    Cancel
                                </button>
                                <div class="flex-1"></div>
                                <button id="kycSubmitButton" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                                    Save Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('company.kyc', ['company' => $company->id, 'tab' => 'client-intake']) }}" class="inline-flex h-9 items-center rounded-md px-4 text-sm font-medium {{ $activeTab === 'client-intake' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                            Client Intake Form
                        </a>
                        <a href="{{ route('company.kyc', ['company' => $company->id, 'tab' => 'business-client-information']) }}" class="inline-flex h-9 items-center rounded-md px-4 text-sm font-medium {{ $activeTab === 'business-client-information' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                            Business Client Information Form
                        </a>
                        <a href="{{ route('company.kyc', ['company' => $company->id, 'tab' => 'doc-requirement']) }}" class="inline-flex h-9 items-center rounded-md px-4 text-sm font-medium {{ $activeTab === 'doc-requirement' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                            Doc Requirement
                        </a>
                    </div>

                    @if ($activeTab === 'client-intake')
                        <a href="{{ route('company.cif.create', $company->id) }}" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center">
                            Send CIF
                        </a>
                    @elseif ($activeTab === 'business-client-information')
                        <a href="{{ route('company.bif.create', $company->id) }}" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center">
                            Send BIF
                        </a>
                    @elseif ($activeTab === 'doc-requirement')
                        <button @click="showDrawer = true; resetKycRecordForm()" type="button" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center">
                            Add KYC Record
                        </button>
                    @endif
                </div>

                <div class="p-4">
                    @if ($activeTab === 'client-intake')
                        <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">Date Uploaded</th>
                                            <th class="px-4 py-3 text-left font-medium">Uploaded By</th>
                                            <th class="px-4 py-3 text-left font-medium">Client</th>
                                            <th class="px-4 py-3 text-left font-medium">TIN</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Type</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Body/Agency</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Status</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Date</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration No.</th>
                                            <th class="px-4 py-3 text-left font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                        @forelse ($cifDocuments as $cif)
                                            @php
                                                $statusLabel = match ($cif->status) {
                                                    'approved' => 'Approved',
                                                    'rejected' => 'Rejected',
                                                    'draft' => 'Draft',
                                                    default => 'Waiting for Approval',
                                                };
                                                $statusClasses = match ($cif->status) {
                                                    'approved' => 'border-green-200 bg-green-50 text-green-700',
                                                    'rejected' => 'border-red-200 bg-red-50 text-red-700',
                                                    'draft' => 'border-gray-200 bg-gray-50 text-gray-700',
                                                    default => 'border-amber-200 bg-amber-50 text-amber-700',
                                                };
                                                $uploadedBy = $cif->approved_by_name
                                                    ?: $cif->rejected_by_name
                                                    ?: $cif->review_signature_printed_name
                                                    ?: $cif->sales_marketing_name
                                                    ?: $cif->signature_printed_name
                                                    ?: '-';
                                                $clientName = trim(implode(' ', array_filter([
                                                    $cif->first_name,
                                                    $cif->middle_name,
                                                    $cif->last_name,
                                                    $cif->name_extension,
                                                ]))) ?: ($cif->title ?: $company->company_name);
                                                $governmentType = $cif->citizenship_status === 'filipino' ? 'NatGov' : 'Local';
                                                $governmentBody = $cif->other_government_id ?: 'Client Information Form';
                                                $registrationDate = $cif->cif_date ?: $cif->submitted_at;
                                                $registrationNo = $cif->cif_no ?: 'Pending';
                                                $registrationStatusLabel = $statusLabel;
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $cif->submitted_at ? $cif->submitted_at->format('F j, Y') : ($cif->created_at ? $cif->created_at->format('F j, Y') : '-') }}</td>
                                                <td class="px-4 py-3">{{ $uploadedBy }}</td>
                                                <td class="px-4 py-3">
                                                    <a href="{{ route('company.cif.show', ['company' => $company->id, 'cif' => $cif->id]) }}" class="font-medium text-blue-700 hover:underline">
                                                        {{ $clientName }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3">{{ $cif->tin ?: '-' }}</td>
                                                <td class="px-4 py-3">{{ $governmentType }}</td>
                                                <td class="px-4 py-3">{{ $governmentBody }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                                        {{ $registrationStatusLabel }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $registrationDate ? \Illuminate\Support\Carbon::parse($registrationDate)->format('F j, Y') : '-' }}</td>
                                                <td class="px-4 py-3">{{ $registrationNo }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <a href="{{ route('company.cif.show', ['company' => $company->id, 'cif' => $cif->id]) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                            View
                                                        </a>
                                                        <a href="{{ route('company.cif.edit', ['company' => $company->id, 'cif' => $cif->id]) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                            Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="px-4 py-10 text-center text-gray-400 italic">No client intake forms submitted for this company yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif ($activeTab === 'business-client-information')
                        <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">Date Uploaded</th>
                                            <th class="px-4 py-3 text-left font-medium">Uploaded By</th>
                                            <th class="px-4 py-3 text-left font-medium">Client</th>
                                            <th class="px-4 py-3 text-left font-medium">TIN</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Type</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Body/Agency</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Status</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Date</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration No.</th>
                                            <th class="px-4 py-3 text-left font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                        @forelse ($bifDocuments as $bif)
                                            @php
                                                $statusLabel = match ($bif->status) {
                                                    'approved' => 'Approved',
                                                    'rejected' => 'Rejected',
                                                    'draft' => 'Draft',
                                                    default => 'Waiting for Approval',
                                                };
                                                $uploadedBy = $bif->approved_by_name
                                                    ?: $bif->sales_marketing_name
                                                    ?: $bif->signature_printed_name
                                                    ?: $company->owner_name
                                                    ?: '-';
                                                $governmentType = $bif->nationality_status === 'foreign' ? 'Local' : 'NatGov';
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $bif->submitted_at ? $bif->submitted_at->format('F j, Y') : ($bif->created_at ? $bif->created_at->format('F j, Y') : '-') }}</td>
                                                <td class="px-4 py-3">{{ $uploadedBy }}</td>
                                                <td class="px-4 py-3">
                                                    <a href="{{ route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id]) }}" class="font-medium text-blue-700 hover:underline">
                                                        {{ $bif->business_name ?: 'Business Information Form' }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3">{{ $bif->tin_no ?: '-' }}</td>
                                                <td class="px-4 py-3">{{ $governmentType }}</td>
                                                <td class="px-4 py-3">Business Information Form</td>
                                                <td class="px-4 py-3">{{ $statusLabel }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">{{ $bif->bif_date ? $bif->bif_date->format('F j, Y') : '-' }}</td>
                                                <td class="px-4 py-3">{{ $bif->bif_no ?: 'Pending' }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <a href="{{ route('company.bif.show', ['company' => $company->id, 'bif' => $bif->id]) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                            View
                                                        </a>
                                                        <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                            Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="px-4 py-10 text-center text-gray-400 italic">No business information forms submitted for this company yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-gray-200 bg-white">
                            <form method="GET" action="{{ route('company.kyc', $company->id) }}" class="border-b border-gray-100 px-4 py-4">
                                <input type="hidden" name="tab" value="doc-requirement">
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Search TIN, uploader, body, registration no..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                    <select name="government_type" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                        <option value="">All Government Types</option>
                                        @foreach ($governmentTypes as $type)
                                            <option value="{{ $type }}" @selected($governmentType === $type)>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <select name="registration_status" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
                                        <option value="">All Registration Status</option>
                                        @foreach ($registrationStatuses as $status)
                                            <option value="{{ $status }}" @selected($registrationStatus === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Filter</button>
                                        <a href="{{ route('company.kyc', ['company' => $company->id, 'tab' => 'doc-requirement']) }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
                                    </div>
                                </div>
                            </form>

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">Date Uploaded</th>
                                            <th class="px-4 py-3 text-left font-medium">Uploaded By</th>
                                            <th class="px-4 py-3 text-left font-medium">TIN</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Type</th>
                                            <th class="px-4 py-3 text-left font-medium">Government Body</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Status</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration Date</th>
                                            <th class="px-4 py-3 text-left font-medium">Registration No.</th>
                                            <th class="px-4 py-3 text-left font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                        @forelse ($records as $record)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($record['date_uploaded'])->format('F j, Y') }}</td>
                                                <td class="px-4 py-3">{{ $record['uploaded_by'] }}</td>
                                                <td class="px-4 py-3">{{ $record['tin'] }}</td>
                                                <td class="px-4 py-3">{{ $record['government_type'] }}</td>
                                                <td class="px-4 py-3">{{ $record['government_body'] }}</td>
                                                <td class="px-4 py-3">{{ $record['registration_status'] }}</td>
                                                <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($record['registration_date'])->format('F j, Y') }}</td>
                                                <td class="px-4 py-3">{{ $record['registration_no'] }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" onclick='editKycRecord(@json($record))' class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('company.kyc.destroy', [$company->id, $record['id']]) }}" onsubmit="return confirm('Delete this KYC record?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex h-8 items-center rounded-full border border-red-200 px-3 text-xs font-medium text-red-600 hover:bg-red-50">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="px-4 py-10 text-center text-gray-400 italic">
                                                    No KYC document records for this company yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const governmentBodyOptions = @json($governmentBodies);
    const initialGovernmentBody = @json(old('government_body'));

    function syncGovernmentBodyOptions(selectedBody = null) {
        const typeSelect = document.getElementById('kycGovernmentTypeInput');
        const bodySelect = document.getElementById('kycGovernmentBodyInput');

        if (!typeSelect || !bodySelect) {
            return;
        }

        const selectedType = typeSelect.value;
        const options = governmentBodyOptions[selectedType] || [];
        bodySelect.innerHTML = '';

        options.forEach((option) => {
            const element = document.createElement('option');
            element.value = option;
            element.textContent = option;
            if ((selectedBody && selectedBody === option) || (!selectedBody && initialGovernmentBody === option)) {
                element.selected = true;
            }
            bodySelect.appendChild(element);
        });
    }

    function resetKycRecordForm() {
        const form = document.getElementById('kycRecordForm');
        form.reset();
        form.action = @json(route('company.kyc.store', $company->id));
        document.getElementById('kycRecordFormMethod').value = 'POST';
        document.getElementById('kycDrawerTitle').textContent = 'Add KYC Record';
        document.getElementById('kycSubmitButton').textContent = 'Save Record';
        syncGovernmentBodyOptions();
    }

    function editKycRecord(record) {
        const container = document.getElementById('companyKycApp');
        resetKycRecordForm();
        document.getElementById('kycRecordForm').action = @json(route('company.kyc.update', [$company->id, '__RECORD__'])).replace('__RECORD__', record.id);
        document.getElementById('kycRecordFormMethod').value = 'PUT';
        document.getElementById('kycDrawerTitle').textContent = 'Edit KYC Record';
        document.getElementById('kycSubmitButton').textContent = 'Update Record';
        document.getElementById('kycDateUploadedInput').value = record.date_uploaded ?? '';
        document.getElementById('kycUploadedByInput').value = record.uploaded_by ?? '';
        document.getElementById('kycTinInput').value = record.tin ?? '';
        document.getElementById('kycGovernmentTypeInput').value = record.government_type ?? 'NatGov';
        syncGovernmentBodyOptions(record.government_body ?? '');
        document.getElementById('kycRegistrationStatusInput').value = record.registration_status ?? 'Pending';
        document.getElementById('kycRegistrationDateInput').value = record.registration_date ?? '';
        document.getElementById('kycRegistrationNoInput').value = record.registration_no ?? '';
        document.getElementById('kycDocumentFileInput').value = record.document_file ?? '';
        document.getElementById('kycRemarksInput').value = record.remarks ?? '';
        if (container && container.__x) {
            container.__x.$data.showDrawer = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        syncGovernmentBodyOptions();

        @if ($errors->any())
            document.getElementById('companyKycApp')?.__x?.$data && (document.getElementById('companyKycApp').__x.$data.showDrawer = true);
        @endif
    });
</script>
@endsection
