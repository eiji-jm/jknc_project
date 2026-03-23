@extends('layouts.app')

@section('content')
@php
    $statusPillClasses = [
        'Verified' => 'bg-green-100 text-green-700 border border-green-200',
        'Approved' => 'bg-green-100 text-green-700 border border-green-200',
        'Pending Verification' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'For Review' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Not Submitted' => 'bg-gray-100 text-gray-600 border border-gray-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];

    $status = $contact->kyc_status ?: 'Not Submitted';
    $name = trim($contact->first_name.' '.$contact->last_name);
    $initials = strtoupper(mb_substr($contact->first_name ?? '', 0, 1).mb_substr($contact->last_name ?? '', 0, 1));
    $contactCifNo = $contact->cif_no ?: ($cifData['cif_no'] ?? '-');
    $kycRequirements = $kycRequirementState ?? [
        'cif_signed_document' => ['file' => null, 'complete' => false],
        'two_valid_ids' => ['count' => 0, 'files' => [], 'complete' => false],
        'specimen_signature_form' => ['form_exists' => false, 'file' => null, 'files' => [], 'complete' => false],
        'tin_proof' => ['file' => null, 'files' => [], 'complete' => false],
    ];
@endphp

<div class="bg-white">
    <div class="border-b border-gray-200 px-6 py-3 text-sm text-gray-600">
        <a href="{{ route('contacts.index') }}" class="hover:text-blue-700"><i class="fas fa-arrow-left mr-1"></i>Contacts</a>
        <span class="mx-1">/</span>
        <span class="font-medium text-gray-900">{{ $name }}</span>
    </div>

    <div class="border-b border-gray-200 px-6 py-4">
        <div class="flex flex-wrap items-center gap-5">
            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-blue-100 text-3xl font-semibold text-blue-700">
                {{ $initials ?: 'C' }}
            </div>
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold text-gray-900">{{ $name }}</h1>
                <p class="text-xl text-gray-700">{{ $contact->company_name ?: 'ABC Corporation' }}</p>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                    <span>Email: {{ $contact->email ?: 'juan@gmail.com' }}</span>
                    <span>Phone number: {{ $contact->phone ?: '09345234' }}</span>
                    <span>Customer Type: {{ $contact->customer_type ?: 'Corporation' }}</span>
                    <span>Position: {{ $contact->position ?: 'CEO' }}</span>
                    <span>CIF No: {{ $contactCifNo ?: '-' }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span id="contactKycHeaderBadge" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClasses[$status] ?? $statusPillClasses['Not Submitted'] }}">{{ $status }}</span>
                    <span class="text-sm text-gray-700">Contact Owner: {{ $contact->owner_name ?: 'John Admin' }}</span>
                </div>
                <p class="text-sm text-gray-600">Address: {{ $contact->contact_address ?: 'Cebu City, Philippines' }}</p>
            </div>
        </div>
    </div>

    <div class="flex">
        <aside class="w-48 border-r border-gray-200 p-3">
            <nav class="space-y-1">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a
                        href="{{ route('contacts.show', $contact).'?tab='.$tabKey }}"
                        class="block rounded-lg px-3 py-1.5 text-sm {{ $tab === $tabKey ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                    >
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <section class="flex-1 bg-white p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($tab === 'kyc')
                <div id="kyc">
                <div id="kycTabApp">
                    <div class="mb-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <div>
                                    <h2 class="text-base font-semibold text-gray-900">Client Information Form (CIF)</h2>
                                    <p class="mt-1 text-xs text-gray-500">Manual CIF data is the source of truth for this contact record.</p>
                                </div>
                                @if ($cifEditMode)
                                    <a href="{{ route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc']) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                                @else
                                    <a href="{{ route('contacts.show', ['contact' => $contact->id, 'tab' => 'kyc', 'edit_cif' => 1]) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                @endif
                            </div>
                            <div class="p-4">
                                @if ($cifEditMode)
                                    @include('contacts.partials.cif-document-edit', [
                                        'contact' => $contact,
                                        'cifData' => $cifData,
                                    ])
                                @else
                                    @include('contacts.partials.cif-document', ['cifData' => $cifData])
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Preview / PDF</h3>
                                </div>
                                <div class="px-4 py-4">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <h3 class="mb-2 text-sm font-semibold text-gray-900">PDF Tools</h3>
                                        <p class="mb-3 text-xs text-gray-500">
                                            Preview the current document or export a print-friendly PDF.
                                        </p>

                                        <button onclick="window.open('{{ route('contacts.cif.preview', $contact->id) }}', '_blank')"
                                            class="mb-2 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Preview PDF
                                        </button>

                                        <button onclick="window.open('{{ route('contacts.cif.download', $contact->id) }}?autoprint=1', '_blank')"
                                            class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                            Download PDF
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <h2 class="text-base font-semibold text-gray-900">KYC Information</h2>
                                    <button id="openKycEditModal" type="button" class="text-sm text-blue-600 hover:text-blue-700">Edit</button>
                                </div>
                                <div class="space-y-4 px-4 py-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">CIF</p>
                                        <p id="kycCifValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">TIN</p>
                                        <p id="kycTinValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">KYC Status</p>
                                        <span id="kycCardStatusBadge" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"></span>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Date Verified</p>
                                        <p id="kycDateVerifiedValue" class="font-medium text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Verified By</p>
                                        <p id="kycVerifiedByValue" class="font-medium text-gray-900"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                                </div>
                                <div class="space-y-2 px-4 py-4">
                                    <form id="submitKycForVerificationForm" method="POST" action="{{ route('contacts.kyc.submit', $contact->id) }}">
                                        @csrf
                                        <button id="submitForVerificationBtn" type="submit" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Submit For Verification</button>
                                    </form>
                                    <button id="approveKycBtn" type="button" class="h-10 w-full rounded-lg bg-green-600 text-sm font-medium text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-green-300">Approve</button>
                                    <button id="rejectKycBtn" type="button" class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-red-300">Reject</button>
                                    <p id="kycActionWarning" class="hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700"></p>
                                    <div class="pt-2">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">KYC Activity</p>
                                        <div id="kycActionLogs" class="space-y-1 text-xs text-gray-500"></div>
                                    </div>
                                    <p id="kycRejectionNote" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">KYC Requirements</h2>
                                <p class="mt-1 text-xs text-gray-500">Upload and manage only the required compliance items: CIF Document (Signed), Two Valid IDs, Specimen Signature Form, and TIN.</p>
                            </div>
                            <div class="max-h-[520px] space-y-3 overflow-y-auto p-4">
                                @php
                                    $cifSignedRequirement = $kycRequirements['cif_signed_document'];
                                    $twoValidIds = $kycRequirements['two_valid_ids'];
                                    $specimenRequirement = $kycRequirements['specimen_signature_form'];
                                    $tinRequirement = $kycRequirements['tin_proof'];
                                    $actionBtn = 'rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50';
                                    $primaryBtn = 'rounded-md border border-blue-200 px-2 py-1 text-blue-700 hover:bg-blue-50';
                                    $dangerBtn = 'rounded-md border border-red-200 px-2 py-1 text-red-600 hover:bg-red-50';
                                    $disabledBtn = 'opacity-40 pointer-events-none';
                                @endphp

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">CIF Document (Signed)</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $cifSignedRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600' }}">
                                                {{ $cifSignedRequirement['complete'] ? 'Complete' : 'Missing' }}
                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">{{ $cifSignedRequirement['file']['file_name'] ?? 'No file uploaded' }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.upload', $contact->id) }}" enctype="multipart/form-data" class="inline-flex">
                                                @csrf
                                                <input type="hidden" name="requirement" value="cif_signed_document">
                                                <label class="{{ $actionBtn }} cursor-pointer">
                                                    Upload
                                                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                            <button
                                                id="viewCifSignedDocumentBtn"
                                                type="button"
                                                @if ($cifSignedRequirement['file'])
                                                    onclick="openDocumentModal(@js($cifSignedRequirement['file']['file_path'] ?? $cifSignedRequirement['file']['path'] ?? ''), 'cif_signed_document')"
                                                @endif
                                                class="{{ $actionBtn }} {{ $cifSignedRequirement['file'] ? '' : $disabledBtn }}"
                                            >
                                                View
                                            </button>
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'cif_signed_document']) }}" class="inline-flex" onsubmit="return confirm('Remove the uploaded signed CIF document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $dangerBtn }} {{ $cifSignedRequirement['file'] ? '' : $disabledBtn }}">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Two Valid IDs</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $twoValidIds['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600' }}">
                                                {{ $twoValidIds['complete'] ? 'Complete' : 'Missing' }}
                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                {{ $twoValidIds['count'] > 0 ? $twoValidIds['count'].' file'.($twoValidIds['count'] === 1 ? '' : 's').' uploaded' : 'No file uploaded' }}
                                            </p>
                                            @if ($twoValidIds['count'] > 0)
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach ($twoValidIds['files'] as $fileIndex => $file)
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(@js($file['file_path'] ?? $file['path'] ?? ''), 'two_valid_ids', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $twoValidIds['files']))), {{ $fileIndex }})"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File {{ $loop->iteration }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.upload', $contact->id) }}" enctype="multipart/form-data" class="inline-flex">
                                                @csrf
                                                <input type="hidden" name="requirement" value="two_valid_ids">
                                                <label class="{{ $actionBtn }} cursor-pointer">
                                                    Upload
                                                    <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                            @php $twoValidFirstFile = $twoValidIds['files'][0] ?? null; @endphp
                                            <button
                                                type="button"
                                                @if ($twoValidFirstFile)
                                                    onclick="openDocumentModal(@js($twoValidFirstFile['file_path'] ?? $twoValidFirstFile['path'] ?? ''), 'two_valid_ids', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $twoValidIds['files']))), 0)"
                                                @endif
                                                class="{{ $actionBtn }} {{ $twoValidFirstFile ? '' : $disabledBtn }}"
                                            >
                                                View
                                            </button>
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'two_valid_ids']) }}" class="inline-flex" onsubmit="return confirm('Remove all uploaded valid IDs for this requirement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $dangerBtn }} {{ $twoValidIds['count'] > 0 ? '' : $disabledBtn }}">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Specimen Signature Form</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $specimenRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600' }}">
                                                {{ $specimenRequirement['complete'] ? 'Complete' : 'Missing' }}
                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                @if ($specimenRequirement['file'])
                                                    {{ $specimenRequirement['file']['file_name'] ?? 'Specimen signature file uploaded' }}
                                                @elseif ($specimenRequirement['form_exists'])
                                                    System form created
                                                @else
                                                    No file uploaded
                                                @endif
                                            </p>
                                            @if (!empty($specimenRequirement['files']))
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach ($specimenRequirement['files'] as $fileIndex => $file)
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(@js($file['file_path'] ?? $file['path'] ?? ''), 'specimen_signature_upload', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $specimenRequirement['files']))), {{ $fileIndex }})"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File {{ $loop->iteration }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            @if ($specimenRequirement['form_exists'])
                                                <a href="{{ route('contacts.specimen-signature', ['id' => $contact->id]) }}" class="{{ $actionBtn }}">View Form</a>
                                                <a href="{{ route('contacts.specimen-signature', ['id' => $contact->id, 'edit' => 1]) }}" class="{{ $actionBtn }}">Edit Form</a>
                                            @else
                                                <a href="{{ route('contacts.specimen-signature', ['id' => $contact->id]) }}" class="{{ $primaryBtn }}">Create Form</a>
                                            @endif
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.upload', $contact->id) }}" enctype="multipart/form-data" class="inline-flex">
                                                @csrf
                                                <input type="hidden" name="requirement" value="specimen_signature_upload">
                                                <label class="{{ $actionBtn }} cursor-pointer">
                                                    Upload
                                                    <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                            <button
                                                type="button"
                                                @if ($specimenRequirement['file'])
                                                    onclick="openDocumentModal(@js($specimenRequirement['file']['file_path'] ?? $specimenRequirement['file']['path'] ?? ''), 'specimen_signature_upload', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $specimenRequirement['files'] ?? []))), 0)"
                                                @endif
                                                class="{{ $actionBtn }} {{ $specimenRequirement['file'] ? '' : $disabledBtn }}"
                                            >
                                                View
                                            </button>
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'specimen_signature_upload']) }}" class="inline-flex" onsubmit="return confirm('Remove the uploaded specimen signature file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $dangerBtn }} {{ $specimenRequirement['file'] ? '' : $disabledBtn }}">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>

                                <article class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">TIN</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $tinRequirement['complete'] ? 'border border-green-200 bg-green-100 text-green-700' : 'border border-gray-200 bg-gray-100 text-gray-600' }}">
                                                {{ $tinRequirement['complete'] ? 'Complete' : 'Missing' }}
                                            </span>
                                            <p class="mt-2 text-xs text-gray-500">
                                                @if (!empty($tinRequirement['files']))
                                                    {{ count($tinRequirement['files']) > 1 ? count($tinRequirement['files']).' files uploaded' : ($tinRequirement['file']['file_name'] ?? 'No file uploaded') }}
                                                @else
                                                    No file uploaded
                                                @endif
                                            </p>
                                            @if (!empty($tinRequirement['files']))
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach ($tinRequirement['files'] as $fileIndex => $file)
                                                        <button
                                                            type="button"
                                                            onclick="openDocumentModal(@js($file['file_path'] ?? $file['path'] ?? ''), 'tin_proof', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $tinRequirement['files']))), {{ $fileIndex }})"
                                                            class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 hover:bg-gray-50"
                                                        >
                                                            File {{ $loop->iteration }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center justify-end gap-2 text-xs">
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.upload', $contact->id) }}" enctype="multipart/form-data" class="inline-flex">
                                                @csrf
                                                <input type="hidden" name="requirement" value="tin_proof">
                                                <label class="{{ $actionBtn }} cursor-pointer">
                                                    Upload
                                                    <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                            <button
                                                type="button"
                                                @if ($tinRequirement['file'])
                                                    onclick="openDocumentModal(@js($tinRequirement['file']['file_path'] ?? $tinRequirement['file']['path'] ?? ''), 'tin_proof', @js(array_values(array_map(fn ($entry) => $entry['file_path'] ?? $entry['path'] ?? '', $tinRequirement['files'] ?? []))), 0)"
                                                @endif
                                                class="{{ $actionBtn }} {{ $tinRequirement['file'] ? '' : $disabledBtn }}"
                                            >
                                                View
                                            </button>
                                            <form method="POST" action="{{ route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'tin_proof']) }}" class="inline-flex" onsubmit="return confirm('Remove the uploaded TIN document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="{{ $dangerBtn }} {{ $tinRequirement['file'] ? '' : $disabledBtn }}">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div id="kycEditModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[560px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 class="text-xl font-semibold text-gray-900">Edit KYC Information</h3>
                                <button id="closeKycEditModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="kycEditForm" class="flex min-h-0 flex-1 flex-col">
                                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-6 sm:px-8">
                                <div>
                                    <label for="kycEditCif" class="mb-1 block text-sm font-medium text-gray-700">CIF</label>
                                    <input id="kycEditCif" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorCif" class="mt-1 hidden text-xs text-red-600">CIF is required.</p>
                                </div>
                                <div>
                                    <label for="kycEditTin" class="mb-1 block text-sm font-medium text-gray-700">TIN</label>
                                    <input id="kycEditTin" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorTin" class="mt-1 hidden text-xs text-red-600">TIN is required.</p>
                                </div>
                                <div>
                                    <label for="kycEditStatus" class="mb-1 block text-sm font-medium text-gray-700">KYC Status</label>
                                    <select id="kycEditStatus" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option>Not Submitted</option>
                                        <option>Pending Verification</option>
                                        <option>For Review</option>
                                        <option>Approved</option>
                                        <option>Rejected</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="kycEditDateVerified" class="mb-1 block text-sm font-medium text-gray-700">Date Verified</label>
                                    <input id="kycEditDateVerified" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorDateVerified" class="mt-1 hidden text-xs text-red-600">Date Verified is required for Approved status.</p>
                                </div>
                                <div>
                                    <label for="kycEditVerifiedBy" class="mb-1 block text-sm font-medium text-gray-700">Verified By</label>
                                    <input id="kycEditVerifiedBy" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="kycErrorVerifiedBy" class="mt-1 hidden text-xs text-red-600">Verified By is required for Approved status.</p>
                                </div>
                                </div>
                                <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="cancelKycEdit" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <div id="documentModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[960px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 id="documentModalTitle" class="text-xl font-semibold text-gray-900">Edit CIF Document</h3>
                                <button id="closeDocumentModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="documentForm" method="POST" action="{{ route('contacts.kyc.requirements.upload', $contact->id) }}" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                                @csrf
                                <input type="hidden" name="requirement" value="cif_signed_document">
                                <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 overflow-y-auto lg:grid-cols-[1.1fr_0.9fr]">
                                <div class="border-b border-gray-100 p-5 lg:border-b-0 lg:border-r">
                                    <div id="documentPreviewPanel" class="flex min-h-[420px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 text-center text-sm text-gray-500">
                                        <i class="far fa-file-pdf text-6xl text-gray-400"></i>
                                        <p class="mt-2">No CIF document selected</p>
                                        <p class="text-xs">Upload a PDF or image file to preview</p>
                                    </div>
                                </div>
                                <div class="space-y-3 p-5">
                                    <div><label for="docTitle" class="mb-1 block text-sm font-medium text-gray-700">Document Title</label><input id="docTitle" name="document_title" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><p id="docErrorTitle" class="mt-1 hidden text-xs text-red-600">Document title is required.</p></div>
                                    <div><label for="docCertificateNo" class="mb-1 block text-sm font-medium text-gray-700">CIF No.</label><input id="docCertificateNo" name="cif_no" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                    <div><label for="docCompanyRegNo" class="mb-1 block text-sm font-medium text-gray-700">Company Reg No.</label><input id="docCompanyRegNo" name="company_reg_no" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label for="docUploadDate" class="mb-1 block text-sm font-medium text-gray-700">Date Upload</label><input id="docUploadDate" name="date_upload" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div><div><label for="docCreatedDate" class="mb-1 block text-sm font-medium text-gray-700">Date Created</label><input id="docCreatedDate" name="date_created" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label for="docIssuedOn" class="mb-1 block text-sm font-medium text-gray-700">Issued On</label><input id="docIssuedOn" name="issued_on" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div><div><label for="docIssuedBy" class="mb-1 block text-sm font-medium text-gray-700">Issued By</label><input id="docIssuedBy" name="issued_by" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></div></div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Document</label>
                                        <label for="docFileInput" class="flex h-11 cursor-pointer items-center rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-600 hover:bg-gray-50"><i class="fas fa-folder-open mr-2 text-blue-600"></i><span id="docFileNameLabel">Replace file</span></label>
                                        <input id="docFileInput" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                        <div class="mt-2 flex items-center justify-between gap-3 text-xs">
                                            <span id="docCurrentFileMeta" class="text-gray-500"></span>
                                            <button id="clearDocFileBtn" type="button" class="hidden text-gray-500 hover:text-red-600">Clear replacement file</button>
                                        </div>
                                        <p id="docErrorFile" class="mt-1 hidden text-xs text-red-600">Please upload a CIF document file.</p>
                                    </div>
                                    <div><label for="docRemarks" class="mb-1 block text-sm font-medium text-gray-700">Remarks</label><textarea id="docRemarks" name="remarks" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea></div>
                                </div>
                                </div>
                                <div class="flex justify-between gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="removeCifSignedDocumentBtn" type="button" class="h-10 rounded-lg border border-red-200 bg-white px-4 text-sm text-red-600 hover:bg-red-50">Remove file</button>
                                    <div class="flex justify-end gap-3">
                                        <button id="cancelDocumentModal" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                        <button type="submit" id="saveDocumentBtn" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <form id="removeCifSignedDocumentForm" method="POST" action="{{ route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => 'cif_signed_document']) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form id="removeKycDocumentForm" method="POST" action="" class="hidden">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="index" id="removeKycDocumentIndex" value="">
                    </form>

                    <div id="documentViewModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[900px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8"><h3 id="documentViewTitle" class="text-xl font-semibold text-gray-900">Document Details</h3><button id="closeDocumentViewModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button></div>
                            <div class="grid min-h-0 flex-1 grid-cols-1 gap-0 overflow-y-auto lg:grid-cols-[1.1fr_0.9fr]"><div id="documentViewPreview" class="m-4 flex min-h-[430px] items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500"></div><div class="space-y-3 border-l border-gray-100 p-5 text-sm"><div><p class="text-gray-500">Document Type</p><p id="viewDocType" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Title</p><p id="viewDocTitle" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">CIF No.</p><p id="viewDocCertificateNo" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Company Reg No.</p><p id="viewDocCompanyRegNo" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Date Upload</p><p id="viewDocUploadDate" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Date Created</p><p id="viewDocCreatedDate" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Issued On</p><p id="viewDocIssuedOn" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Issued By</p><p id="viewDocIssuedBy" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">Remarks</p><p id="viewDocRemarks" class="font-medium text-gray-900"></p></div><div><p class="text-gray-500">File Name</p><p id="viewDocFileName" class="font-medium text-gray-900"></p></div></div></div>
                            <div class="flex justify-end border-t border-gray-100 px-6 py-4"><button id="closeDocumentViewFooter" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button></div>
                        </div>
                        </div>
                    </div>

                    <div id="rejectKycModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[520px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="border-b border-gray-100 px-6 py-5 sm:px-8"><h3 class="text-xl font-semibold text-gray-900">Reject KYC</h3></div>
                            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto px-6 py-6 sm:px-8">
                                <p class="text-sm text-gray-600">Are you sure you want to reject this KYC submission?</p>
                                <div><label for="rejectReasonInput" class="mb-1 block text-sm font-medium text-gray-700">Rejection Reason (optional)</label><textarea id="rejectReasonInput" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea></div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8"><button id="cancelRejectKyc" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button><button id="confirmRejectKyc" type="button" class="h-10 rounded-lg bg-red-600 px-4 text-sm font-medium text-white hover:bg-red-700">Reject</button></div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const app = document.getElementById('kycTabApp');
                            if (!app) return;
                            const q = (id) => document.getElementById(id);
                            const mockUser = @json($contact->owner_name ?: 'John Admin');
                            const todayIso = new Date().toISOString().slice(0, 10);
                            const statusStyles = {'Not Submitted':'bg-gray-100 text-gray-600 border border-gray-200','Pending Verification':'bg-amber-100 text-amber-700 border border-amber-200','For Review':'bg-amber-100 text-amber-700 border border-amber-200','Approved':'bg-green-100 text-green-700 border border-green-200','Rejected':'bg-red-100 text-red-700 border border-red-200'};
                            const statusRaw = @json($status);
                            const statusInit = statusRaw === 'Verified' ? 'Approved' : statusRaw;
                            const specimenSignatureExists = @json((bool) $specimenSignature);
                            const kycRequirementState = @json($kycRequirements);
                            const cifSignedDocument = @json($cifSignedRequirement['file'] ?? null);
                            const specimenSignatureRoutes = {
                                create: @json(route('contacts.specimen-signature', ['id' => $contact->id])),
                                view: @json(route('contacts.specimen-signature', ['id' => $contact->id])),
                                edit: @json(route('contacts.specimen-signature', ['id' => $contact->id, 'edit' => 1])),
                                download: @json(route('contacts.specimen-signature.download', ['id' => $contact->id])),
                            };
                            let kyc = {
                                cif: @json($contact->cif_no ?: ($cifData['cif_no'] ?? '')),
                                tin: @json($contact->tin ?: ($cifData['tin'] ?? '')),
                                status: statusInit || 'Not Submitted',
                                dateVerified: @json($cifData['date_verified'] ?? ''),
                                verifiedBy: @json($cifData['verified_by'] ?? ''),
                                rejectionReason: '',
                                submitted: ['Pending Verification','For Review','Approved','Rejected', 'Verified'].includes(statusInit)
                            };
                            let logs = [`KYC profile loaded by ${mockUser}`];
                            let activeDoc = null; let file = null; let fileUrl = '';
                            let currentFiles = [];
                            let currentIndex = 0;
                            let currentDocs = [];

                            const fmtDate = (s) => { if (!s) return '-'; const d = new Date(s + 'T00:00:00'); return Number.isNaN(d.getTime()) ? s : new Intl.DateTimeFormat('en-US',{month:'short',day:'2-digit',year:'numeric'}).format(d); };
                            const fmtBytes = (n) => !n ? '-' : (n < 1024 ? `${n} B` : (n < 1048576 ? `${(n/1024).toFixed(1)} KB` : `${(n/1048576).toFixed(1)} MB`));
                            const open = (m) => {
                                const panel = m.querySelector('[data-slideover-panel]');
                                const overlay = m.querySelector('[data-slideover-overlay]');
                                m.classList.remove('hidden');
                                m.setAttribute('aria-hidden', 'false');
                                document.body.classList.add('overflow-hidden');
                                requestAnimationFrame(() => {
                                    overlay?.classList.remove('opacity-0');
                                    panel?.classList.remove('translate-x-full');
                                });
                            };
                            const close = (m) => {
                                const panel = m.querySelector('[data-slideover-panel]');
                                const overlay = m.querySelector('[data-slideover-overlay]');
                                overlay?.classList.add('opacity-0');
                                panel?.classList.add('translate-x-full');
                                window.setTimeout(() => {
                                    m.classList.add('hidden');
                                    m.setAttribute('aria-hidden', 'true');
                                    if ([q('kycEditModal'),q('documentModal'),q('documentViewModal'),q('rejectKycModal')].every((x) => x.classList.contains('hidden'))) document.body.classList.remove('overflow-hidden');
                                }, 300);
                            };
                            const badge = (el, status) => { el.className = `inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${statusStyles[status] || statusStyles['Not Submitted']}`; el.textContent = status; };
                            const addLog = (msg) => logs.unshift(`${msg} (${new Date().toLocaleString('en-US',{month:'short',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'})})`);
                            const allRequiredUploaded = () => !!(
                                kycRequirementState.cif_signed_document?.complete &&
                                kycRequirementState.two_valid_ids?.complete &&
                                kycRequirementState.specimen_signature_form?.complete &&
                                kycRequirementState.tin_proof?.complete
                            );

                            const render = () => {
                                q('kycCifValue').textContent = kyc.cif || '-';
                                q('kycTinValue').textContent = kyc.tin || '-';
                                q('kycDateVerifiedValue').textContent = fmtDate(kyc.dateVerified);
                                q('kycVerifiedByValue').textContent = kyc.verifiedBy || '-';
                                badge(q('kycCardStatusBadge'), kyc.status);
                                if (q('contactKycHeaderBadge')) badge(q('contactKycHeaderBadge'), kyc.status);
                                q('kycActionLogs').innerHTML = logs.slice(0, 7).map((l) => `<p>${l}</p>`).join('');
                                const canReview = kyc.submitted && ['Pending Verification','For Review'].includes(kyc.status);
                                q('approveKycBtn').disabled = !canReview; q('rejectKycBtn').disabled = !canReview;
                                q('kycRejectionNote').classList.toggle('hidden', !kyc.rejectionReason);
                                q('kycRejectionNote').textContent = kyc.rejectionReason ? `Rejection reason: ${kyc.rejectionReason}` : '';
                            };

                            const renderPreview = (name, url, mime) => {
                                const label = 'document';
                                if (!name) { q('documentPreviewPanel').innerHTML = `<i class="far fa-file-pdf text-6xl text-gray-400"></i><p class="mt-2">No ${label.toLowerCase()} selected</p><p class="text-xs">Upload a PDF or image file to preview</p>`; return; }
                                if ((mime || '').includes('pdf') && url && url !== '#') q('documentPreviewPanel').innerHTML = `<iframe src="${url}" class="h-[420px] w-full rounded-lg border border-gray-200 bg-white"></iframe>`;
                                else if ((mime || '').startsWith('image/') && url && url !== '#') q('documentPreviewPanel').innerHTML = `<img src="${url}" alt="Document preview" class="h-[420px] w-full rounded-lg border border-gray-200 bg-white object-contain">`;
                                else q('documentPreviewPanel').innerHTML = `<div class="text-center"><i class="far fa-file text-5xl text-blue-600"></i><p class="mt-2 font-medium text-gray-800">${name}</p><p class="text-xs text-gray-500">${mime || 'Document file'}</p></div>`;
                            };

                            const normalizeDateInput = (value) => {
                                if (!value) return '';
                                return String(value).slice(0, 10);
                            };

                            const renderPreviewSwitcher = () => {
                                let switcher = document.getElementById('documentPreviewSwitcher');
                                if (!switcher) {
                                    switcher = document.createElement('div');
                                    switcher.id = 'documentPreviewSwitcher';
                                    switcher.className = 'mt-3 flex flex-wrap gap-2';
                                    q('documentPreviewPanel').insertAdjacentElement('afterend', switcher);
                                }
                                if (currentFiles.length <= 1) {
                                    switcher.innerHTML = '';
                                    return;
                                }
                                switcher.innerHTML = currentFiles.map((_, index) => `
                                    <button type="button" data-preview-index="${index}" class="rounded-md border px-2 py-1 text-xs ${index === currentIndex ? 'border-blue-200 bg-blue-100 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'}">
                                        ${index + 1}
                                    </button>
                                `).join('');
                                switcher.querySelectorAll('[data-preview-index]').forEach((button) => {
                                    button.addEventListener('click', () => switchFile(Number(button.dataset.previewIndex)));
                                });
                            };

                            const renderActiveDocument = () => {
                                const doc = currentDocs[currentIndex] || {};
                                const filePath = currentFiles[currentIndex] || '';
                                const previewUrl = filePath ? `/storage/${filePath}` : (doc.url || '');
                                q('documentModalTitle').textContent = activeDoc === 'cif_signed_document' ? 'Edit CIF Document' : 'View Document';
                                q('docTitle').value = doc.document_title || (doc.file_name ? doc.file_name.replace(/\.[^.]+$/, '') : 'Document');
                                q('docCertificateNo').value = doc.cif_no || kyc.cif || '';
                                q('docCompanyRegNo').value = doc.company_reg_no || '';
                                q('docUploadDate').value = normalizeDateInput(doc.uploaded_at) || todayIso;
                                q('docCreatedDate').value = normalizeDateInput(doc.date_created);
                                q('docIssuedOn').value = normalizeDateInput(doc.issued_on);
                                q('docIssuedBy').value = doc.issued_by || '';
                                q('docRemarks').value = doc.remarks || '';
                                q('docFileNameLabel').textContent = doc.file_name ? 'Replace file' : 'Upload File';
                                q('docCurrentFileMeta').textContent = doc.file_name ? `Current file: ${doc.file_name}` : 'No file uploaded yet';
                                q('removeCifSignedDocumentBtn').classList.toggle('hidden', !doc.file_name);
                                const showCifFields = activeDoc === 'cif_signed_document';
                                q('docCertificateNo').closest('div').classList.toggle('hidden', !showCifFields);
                                q('docCompanyRegNo').closest('div').classList.toggle('hidden', !showCifFields);
                                q('docCreatedDate').closest('div').classList.toggle('hidden', !showCifFields);
                                renderPreview(doc.file_name || '', previewUrl, doc.mime_type || '');
                                renderPreviewSwitcher();
                            };

                            const switchFile = (index) => {
                                currentIndex = index;
                                renderActiveDocument();
                            };
                            window.switchFile = switchFile;

                            const openDocumentModal = (filePath, docType, files = [], startIndex = 0) => {
                                activeDoc = docType;
                                const requirementState = docType === 'cif_signed_document'
                                    ? { files: cifSignedDocument ? [cifSignedDocument] : [] }
                                    : kycRequirementState[docType];
                                currentDocs = Array.isArray(requirementState?.files) && requirementState.files.length
                                    ? requirementState.files
                                    : (requirementState?.file ? [requirementState.file] : []);
                                currentFiles = files.length ? files : currentDocs.map((doc) => doc.file_path || doc.path || '');
                                currentIndex = Math.min(Math.max(startIndex, 0), Math.max(currentFiles.length - 1, 0));
                                if (!currentFiles[currentIndex] && !(currentDocs[currentIndex]?.file_name)) return;
                                file = null;
                                fileUrl = '';
                                q('documentForm').reset();
                                q('docErrorTitle').classList.add('hidden');
                                q('docErrorFile').classList.add('hidden');
                                q('clearDocFileBtn').classList.add('hidden');
                                renderActiveDocument();
                                open(q('documentModal'));
                            };
                            window.openDocumentModal = openDocumentModal;

                            q('openKycEditModal').addEventListener('click', () => { q('kycEditCif').value = kyc.cif; q('kycEditTin').value = kyc.tin; q('kycEditStatus').value = kyc.status; q('kycEditDateVerified').value = kyc.dateVerified; q('kycEditVerifiedBy').value = kyc.verifiedBy; ['kycErrorCif','kycErrorTin','kycErrorDateVerified','kycErrorVerifiedBy'].forEach((id) => q(id).classList.add('hidden')); open(q('kycEditModal')); });
                            [q('closeKycEditModal'), q('cancelKycEdit')].forEach((b) => b.addEventListener('click', () => close(q('kycEditModal'))));
                            [q('closeDocumentModal'), q('cancelDocumentModal')].forEach((b) => b.addEventListener('click', () => close(q('documentModal'))));
                            [q('closeDocumentViewModal'), q('closeDocumentViewFooter')].forEach((b) => b.addEventListener('click', () => close(q('documentViewModal'))));
                            q('cancelRejectKyc').addEventListener('click', () => close(q('rejectKycModal')));

                            q('kycEditForm').addEventListener('submit', (e) => {
                                e.preventDefault();
                                const s = q('kycEditStatus').value; const req = s === 'Approved';
                                const okCif = !!q('kycEditCif').value.trim(), okTin = !!q('kycEditTin').value.trim(), okDate = !req || !!q('kycEditDateVerified').value, okBy = !req || !!q('kycEditVerifiedBy').value.trim();
                                q('kycErrorCif').classList.toggle('hidden', okCif); q('kycErrorTin').classList.toggle('hidden', okTin); q('kycErrorDateVerified').classList.toggle('hidden', okDate); q('kycErrorVerifiedBy').classList.toggle('hidden', okBy);
                                if (!(okCif && okTin && okDate && okBy)) return;
                                kyc = { ...kyc, cif: q('kycEditCif').value.trim(), tin: q('kycEditTin').value.trim(), status: s, dateVerified: q('kycEditDateVerified').value, verifiedBy: q('kycEditVerifiedBy').value.trim() };
                                addLog(`KYC information updated by ${mockUser}`); render(); close(q('kycEditModal'));
                            });

                            q('docFileInput').addEventListener('change', () => {
                                const f = q('docFileInput').files?.[0];
                                if (!f) return;
                                file = f;
                                fileUrl = URL.createObjectURL(f);
                                q('docFileNameLabel').textContent = 'Replace file';
                                q('docCurrentFileMeta').textContent = `Replacement file: ${f.name}`;
                                q('clearDocFileBtn').classList.remove('hidden');
                                renderPreview(f.name, fileUrl, f.type || '');
                            });
                            q('clearDocFileBtn').addEventListener('click', () => {
                                file = null;
                                fileUrl = '';
                                q('docFileInput').value = '';
                                q('clearDocFileBtn').classList.add('hidden');
                                renderActiveDocument();
                            });
                            q('removeCifSignedDocumentBtn')?.addEventListener('click', () => {
                                const activeFile = currentDocs[currentIndex] || {};
                                if (!activeFile?.file_name || !window.confirm('Delete this file?')) return;
                                if (activeDoc === 'cif_signed_document') {
                                    q('removeCifSignedDocumentForm').submit();
                                    return;
                                }
                                q('removeKycDocumentForm').setAttribute('action', @js(route('contacts.kyc.requirements.remove', ['contact' => $contact->id, 'requirement' => '__REQUIREMENT__'])).replace('__REQUIREMENT__', activeDoc));
                                q('removeKycDocumentIndex').value = String(currentIndex);
                                q('removeKycDocumentForm').submit();
                            });
                            q('documentForm').addEventListener('submit', (event) => {
                                const activeFile = currentDocs[currentIndex] || {};
                                const hasExistingFile = !!activeFile?.file_name;
                                const hasReplacementFile = !!q('docFileInput').files?.[0];
                                const hasTitle = !!q('docTitle').value.trim();
                                q('docErrorTitle').classList.toggle('hidden', hasTitle);
                                q('docErrorFile').classList.toggle('hidden', hasExistingFile || hasReplacementFile);
                                if (!hasTitle || (!hasExistingFile && !hasReplacementFile)) {
                                    event.preventDefault();
                                }
                                if (activeDoc !== 'cif_signed_document') {
                                    event.preventDefault();
                                }
                            });
                            q('submitKycForVerificationForm').addEventListener('submit', (event) => {
                                if (!allRequiredUploaded()) {
                                    event.preventDefault();
                                    q('kycActionWarning').textContent = 'Please complete the signed CIF document, Two Valid IDs, Specimen Signature Form, and TIN proof before submitting for verification.';
                                    q('kycActionWarning').classList.remove('hidden');
                                    setTimeout(() => q('kycActionWarning').classList.add('hidden'), 3400);
                                    return;
                                }
                            });
                            q('approveKycBtn').addEventListener('click', () => {
                                if (!kyc.submitted) {
                                    q('kycActionWarning').textContent = 'Submit for verification first before approving.';
                                    q('kycActionWarning').classList.remove('hidden');
                                    setTimeout(() => q('kycActionWarning').classList.add('hidden'), 3200);
                                    return;
                                }
                                kyc.status = 'Approved'; kyc.dateVerified = todayIso; kyc.verifiedBy = mockUser; kyc.rejectionReason = ''; addLog(`Approved KYC by ${mockUser}`); render();
                            });
                            q('rejectKycBtn').addEventListener('click', () => {
                                if (!kyc.submitted) {
                                    q('kycActionWarning').textContent = 'Submit for verification first before rejecting.';
                                    q('kycActionWarning').classList.remove('hidden');
                                    setTimeout(() => q('kycActionWarning').classList.add('hidden'), 3200);
                                    return;
                                }
                                open(q('rejectKycModal'));
                            });
                            q('confirmRejectKyc').addEventListener('click', () => { kyc.status = 'Rejected'; kyc.dateVerified = ''; kyc.rejectionReason = q('rejectReasonInput').value.trim(); q('rejectReasonInput').value = ''; addLog(`Rejected KYC by ${mockUser}`); render(); close(q('rejectKycModal')); });
                            [q('kycEditModal'), q('documentModal'), q('documentViewModal'), q('rejectKycModal')].forEach((m) => {
                                m.querySelector('[data-slideover-overlay]')?.addEventListener('click', () => close(m));
                            });
                            document.addEventListener('keydown', (event) => {
                                if (event.key !== 'Escape') return;
                                [q('kycEditModal'), q('documentModal'), q('documentViewModal'), q('rejectKycModal')].forEach((m) => {
                                    if (!m.classList.contains('hidden')) close(m);
                                });
                            });
                            render();
                        });

                        window.addEventListener('load', function () {
                            if (window.location.hash === '#kyc') {
                                document.getElementById('kyc')?.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    </script>
                </div>
                </div>
            @endif

            @if ($tab === 'history')
                @php
                    $historyChips = [
                        ['key' => 'all', 'label' => 'All Activities'],
                        ['key' => 'profile', 'label' => 'Profile Changes'],
                        ['key' => 'kyc', 'label' => 'KYC Updates'],
                        ['key' => 'deals', 'label' => 'Deals'],
                        ['key' => 'files', 'label' => 'Files'],
                        ['key' => 'notes', 'label' => 'Notes'],
                    ];

                    $typeStyles = [
                        'deals' => [
                            'badge' => 'bg-amber-100 text-amber-600',
                            'icon' => 'fa-arrow-trend-up',
                        ],
                        'notes' => [
                            'badge' => 'bg-yellow-100 text-yellow-700',
                            'icon' => 'fa-note-sticky',
                        ],
                        'profile' => [
                            'badge' => 'bg-blue-100 text-blue-600',
                            'icon' => 'fa-pen',
                        ],
                        'kyc' => [
                            'badge' => 'bg-green-100 text-green-600',
                            'icon' => 'fa-shield-halved',
                        ],
                        'files' => [
                            'badge' => 'bg-indigo-100 text-indigo-600',
                            'icon' => 'fa-file-arrow-up',
                        ],
                    ];
                @endphp

                <div id="historyFeed" class="rounded-xl bg-white">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50" aria-label="Filter">
                            <i class="fas fa-filter text-sm"></i>
                        </button>
                        @foreach ($historyChips as $chip)
                            <button
                                type="button"
                                data-history-chip="{{ $chip['key'] }}"
                                class="history-chip rounded-lg border px-3 py-1.5 text-sm {{ $chip['key'] === 'all' ? 'border-blue-200 bg-blue-700 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                {{ $chip['label'] }}
                            </button>
                        @endforeach
                        <span id="historyRecordCount" class="ml-auto text-sm text-gray-500">{{ count($tabData['history']['items']) }} records</span>
                    </div>

                    <div class="relative space-y-4 pl-12 before:absolute before:bottom-2 before:left-4 before:top-2 before:w-px before:bg-gray-200">
                        @foreach ($tabData['history']['items'] as $item)
                            @php
                                $type = $item['type'] ?? 'profile';
                                $style = $typeStyles[$type] ?? $typeStyles['profile'];
                            @endphp
                            <article data-history-item data-history-type="{{ $type }}" class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                                <span class="absolute -left-12 top-6 z-10 flex h-9 w-9 items-center justify-center rounded-full {{ $style['badge'] }}">
                                    <i class="fas {{ $style['icon'] }} text-xs"></i>
                                </span>

                                <h3 class="text-lg font-semibold leading-tight text-gray-900">{{ $item['title'] }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $item['description'] }}</p>

                                @if (!empty($item['extraLabel']) && !empty($item['extraValue']))
                                    <div class="mt-3 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        <span class="font-medium text-gray-700">{{ $item['extraLabel'] }}:</span> {{ $item['extraValue'] }}
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700">{{ $item['initials'] }}</span>
                                    <span>{{ $item['user'] }}</span>
                                    <span><i class="far fa-clock mr-1"></i>{{ $item['datetime'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const feed = document.getElementById('historyFeed');
                        if (!feed) {
                            return;
                        }

                        const chips = Array.from(feed.querySelectorAll('[data-history-chip]'));
                        const items = Array.from(feed.querySelectorAll('[data-history-item]'));
                        const countLabel = document.getElementById('historyRecordCount');

                        function setActiveChip(activeKey) {
                            chips.forEach((chip) => {
                                const isActive = chip.dataset.historyChip === activeKey;
                                chip.classList.toggle('bg-blue-700', isActive);
                                chip.classList.toggle('text-white', isActive);
                                chip.classList.toggle('border-blue-200', isActive);
                                chip.classList.toggle('bg-white', !isActive);
                                chip.classList.toggle('text-gray-700', !isActive);
                                chip.classList.toggle('border-gray-200', !isActive);
                            });
                        }

                        function applyFilter(filterKey) {
                            let visibleCount = 0;

                            items.forEach((item) => {
                                const itemType = item.dataset.historyType;
                                const visible = filterKey === 'all' || itemType === filterKey;
                                item.classList.toggle('hidden', !visible);
                                if (visible) {
                                    visibleCount += 1;
                                }
                            });

                            countLabel.textContent = `${visibleCount} records`;
                            setActiveChip(filterKey);
                        }

                        chips.forEach((chip) => {
                            chip.addEventListener('click', function () {
                                applyFilter(chip.dataset.historyChip);
                            });
                        });

                        applyFilter('all');
                    });
                </script>
            @endif

            @if ($tab === 'consultation-notes')
                <div id="consultationNotesApp">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">Consultation Notes</h2>
                            <p class="text-sm text-gray-500">Record and track all consultation sessions</p>
                        </div>
                        <button id="openConsultationNoteModal" type="button" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                            + Add Consultation Note
                        </button>
                    </div>

                    <div id="consultationNotesList" class="space-y-3"></div>

                    <div id="consultationFormModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 id="consultationFormTitle" class="text-xl font-semibold text-gray-900">Add Consultation Note</h3>
                                <button id="closeConsultationFormModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <form id="consultationForm" class="flex min-h-0 flex-1 flex-col">
                                <div class="min-h-0 flex-1 overflow-y-auto p-6 sm:px-8">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label for="noteTitle" class="mb-1 block text-sm font-medium text-gray-700">Note Title</label>
                                        <input id="noteTitle" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <p id="errorTitle" class="mt-1 hidden text-xs text-red-600">Note title is required.</p>
                                    </div>
                                    <div>
                                        <label for="consultationDate" class="mb-1 block text-sm font-medium text-gray-700">Consultation Date</label>
                                        <input id="consultationDate" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <p id="errorDate" class="mt-1 hidden text-xs text-red-600">Consultation date is required.</p>
                                    </div>
                                    <div>
                                        <label for="consultationAuthor" class="mb-1 block text-sm font-medium text-gray-700">Author / Created By</label>
                                        <input id="consultationAuthor" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationCategory" class="mb-1 block text-sm font-medium text-gray-700">Tags or Category</label>
                                        <input id="consultationCategory" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="e.g. Budget Review">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationSummary" class="mb-1 block text-sm font-medium text-gray-700">Consultation Summary</label>
                                        <textarea id="consultationSummary" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="consultationDetails" class="mb-1 block text-sm font-medium text-gray-700">Detailed Notes</label>
                                        <textarea id="consultationDetails" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                        <p id="errorBody" class="mt-1 hidden text-xs text-red-600">Provide a summary or detailed notes.</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Attachments</label>
                                        <label for="consultationAttachments" class="flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-sm text-gray-600 hover:bg-gray-100">
                                            <span><i class="fas fa-paperclip mr-2"></i>Upload files</span>
                                        </label>
                                        <input id="consultationAttachments" type="file" multiple class="hidden">
                                        <div id="selectedAttachments" class="mt-2 space-y-2"></div>
                                    </div>
                                </div>
                                </div>
                                <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                                    <button id="cancelConsultationForm" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button id="saveConsultationNote" type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save Consultation Note</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    <div id="consultationViewModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                        <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
                        <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                        <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[620px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                                <h3 class="text-xl font-semibold text-gray-900">Consultation Note Details</h3>
                                <button id="closeConsultationViewModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                            </div>
                            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-5 text-sm sm:px-8">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Note Title</p>
                                    <p id="viewNoteTitle" class="mt-1 text-base font-semibold text-gray-900"></p>
                                </div>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Consultation Date</p>
                                        <p id="viewConsultationDate" class="mt-1 text-gray-800"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Author</p>
                                        <p id="viewConsultationAuthor" class="mt-1 text-gray-800"></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Summary</p>
                                    <p id="viewConsultationSummary" class="mt-1 text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Detailed Notes</p>
                                    <p id="viewConsultationDetails" class="mt-1 whitespace-pre-wrap text-gray-700"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attached Files</p>
                                    <div id="viewConsultationAttachments" class="mt-2 space-y-2"></div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 sm:px-8">
                                <button id="editFromView" type="button" class="h-10 rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm text-blue-700 hover:bg-blue-100">Edit</button>
                                <button id="closeConsultationViewFooter" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Close</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const app = document.getElementById('consultationNotesApp');
                        if (!app) {
                            return;
                        }

                        const notesList = document.getElementById('consultationNotesList');
                        const openButton = document.getElementById('openConsultationNoteModal');
                        const formModal = document.getElementById('consultationFormModal');
                        const viewModal = document.getElementById('consultationViewModal');
                        const formTitle = document.getElementById('consultationFormTitle');
                        const form = document.getElementById('consultationForm');
                        const saveButton = document.getElementById('saveConsultationNote');
                        const attachmentInput = document.getElementById('consultationAttachments');
                        const selectedAttachments = document.getElementById('selectedAttachments');

                        const fields = {
                            title: document.getElementById('noteTitle'),
                            consultationDate: document.getElementById('consultationDate'),
                            author: document.getElementById('consultationAuthor'),
                            summary: document.getElementById('consultationSummary'),
                            details: document.getElementById('consultationDetails'),
                            category: document.getElementById('consultationCategory'),
                        };

                        const errors = {
                            title: document.getElementById('errorTitle'),
                            consultationDate: document.getElementById('errorDate'),
                            body: document.getElementById('errorBody'),
                        };

                        const defaultAuthor = @json($contact->owner_name ?: 'John Admin');
                        let notes = @json($tabData['consultation-notes']);
                        let editNoteId = null;
                        let viewNoteId = null;
                        let formAttachments = [];

                        const escapeHtml = (value) => String(value || '')
                            .replaceAll('&', '&amp;')
                            .replaceAll('<', '&lt;')
                            .replaceAll('>', '&gt;')
                            .replaceAll('"', '&quot;')
                            .replaceAll("'", '&#039;');

                        const formatDate = (value) => {
                            if (!value) return '-';
                            const date = new Date(value + 'T00:00:00');
                            return Number.isNaN(date.getTime())
                                ? value
                                : new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' }).format(date);
                        };

                        const formatBytes = (bytes) => {
                            if (!bytes || Number.isNaN(Number(bytes))) return '-';
                            if (bytes < 1024) return `${bytes} B`;
                            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
                            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
                        };

                        const normalizeType = (name, rawType) => {
                            if (rawType) {
                                const parts = rawType.split('/');
                                if (parts.length > 1 && parts[1]) return parts[1].toUpperCase();
                            }
                            const ext = name.includes('.') ? name.split('.').pop() : 'FILE';
                            return String(ext).toUpperCase();
                        };

                        const sortNotes = () => {
                            notes.sort((a, b) => {
                                const left = new Date(b.consultationDate || 0).getTime();
                                const right = new Date(a.consultationDate || 0).getTime();
                                if (left !== right) return left - right;
                                return new Date(b.updatedAt || 0).getTime() - new Date(a.updatedAt || 0).getTime();
                            });
                        };

                        const renderAttachmentsForForm = () => {
                            if (!formAttachments.length) {
                                selectedAttachments.innerHTML = '<p class="text-xs text-gray-500">No files selected.</p>';
                                return;
                            }
                            selectedAttachments.innerHTML = formAttachments.map((file) => `
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                        <p class="text-xs text-gray-500">${escapeHtml(file.type || 'FILE')} | ${escapeHtml(formatBytes(file.size))}</p>
                                    </div>
                                    <button type="button" class="remove-attachment text-gray-500 hover:text-red-600" data-file-id="${file.id}">
                                        <i class="fas fa-xmark"></i>
                                    </button>
                                </div>
                            `).join('');
                        };

                        const renderNotes = () => {
                            sortNotes();
                            if (!notes.length) {
                                notesList.innerHTML = '<div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-500">No consultation notes yet.</div>';
                                return;
                            }
                            notesList.innerHTML = notes.map((note) => {
                                const attachmentCount = (note.attachments || []).length;
                                const attachmentLabel = `${attachmentCount} attachment${attachmentCount === 1 ? '' : 's'}`;
                                return `
                                    <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="text-xl font-semibold text-gray-900">${escapeHtml(note.title)}</h3>
                                                <p class="mt-1 text-sm text-gray-600">${escapeHtml(note.summary || note.details || '')}</p>
                                                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                                    <span><i class="far fa-calendar mr-1"></i>${escapeHtml(formatDate(note.consultationDate))}</span>
                                                    <span><i class="far fa-user mr-1"></i>${escapeHtml(note.author || defaultAuthor)}</span>
                                                    <span><i class="fas fa-paperclip mr-1"></i>${attachmentLabel}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 text-gray-500">
                                                <button type="button" class="note-view hover:text-blue-600" data-note-id="${note.id}" aria-label="View note"><i class="far fa-eye"></i></button>
                                                <button type="button" class="note-edit hover:text-blue-600" data-note-id="${note.id}" aria-label="Edit note"><i class="far fa-pen-to-square"></i></button>
                                            </div>
                                        </div>
                                    </article>
                                `;
                            }).join('');
                        };

                        const showModal = (modal) => {
                            const panel = modal.querySelector('[data-slideover-panel]');
                            const overlay = modal.querySelector('[data-slideover-overlay]');
                            modal.classList.remove('hidden');
                            modal.setAttribute('aria-hidden', 'false');
                            document.body.classList.add('overflow-hidden');
                            requestAnimationFrame(() => {
                                overlay?.classList.remove('opacity-0');
                                panel?.classList.remove('translate-x-full');
                            });
                        };

                        const hideModal = (modal) => {
                            const panel = modal.querySelector('[data-slideover-panel]');
                            const overlay = modal.querySelector('[data-slideover-overlay]');
                            overlay?.classList.add('opacity-0');
                            panel?.classList.add('translate-x-full');
                            window.setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.setAttribute('aria-hidden', 'true');
                                if (formModal.classList.contains('hidden') && viewModal.classList.contains('hidden')) {
                                    document.body.classList.remove('overflow-hidden');
                                }
                            }, 300);
                        };

                        const resetValidation = () => {
                            Object.values(errors).forEach((el) => el.classList.add('hidden'));
                        };

                        const resetForm = () => {
                            editNoteId = null;
                            fields.title.value = '';
                            fields.consultationDate.value = '';
                            fields.author.value = defaultAuthor;
                            fields.summary.value = '';
                            fields.details.value = '';
                            fields.category.value = '';
                            formAttachments = [];
                            attachmentInput.value = '';
                            resetValidation();
                            renderAttachmentsForForm();
                        };

                        const openAddModal = () => {
                            resetForm();
                            formTitle.textContent = 'Add Consultation Note';
                            saveButton.textContent = 'Save Consultation Note';
                            showModal(formModal);
                        };

                        const openEditModal = (noteId) => {
                            const note = notes.find((item) => Number(item.id) === Number(noteId));
                            if (!note) return;

                            editNoteId = Number(note.id);
                            fields.title.value = note.title || '';
                            fields.consultationDate.value = note.consultationDate || '';
                            fields.author.value = note.author || defaultAuthor;
                            fields.summary.value = note.summary || '';
                            fields.details.value = note.details || '';
                            fields.category.value = note.category || '';
                            formAttachments = (note.attachments || []).map((file) => ({ ...file }));
                            attachmentInput.value = '';
                            resetValidation();
                            renderAttachmentsForForm();

                            formTitle.textContent = 'Edit Consultation Note';
                            saveButton.textContent = 'Update Consultation Note';
                            showModal(formModal);
                        };

                        const openViewModal = (noteId) => {
                            const note = notes.find((item) => Number(item.id) === Number(noteId));
                            if (!note) return;

                            viewNoteId = Number(note.id);
                            document.getElementById('viewNoteTitle').textContent = note.title || '-';
                            document.getElementById('viewConsultationDate').textContent = formatDate(note.consultationDate);
                            document.getElementById('viewConsultationAuthor').textContent = note.author || '-';
                            document.getElementById('viewConsultationSummary').textContent = note.summary || '-';
                            document.getElementById('viewConsultationDetails').textContent = note.details || '-';

                            const viewAttachmentList = document.getElementById('viewConsultationAttachments');
                            const attachments = note.attachments || [];
                            if (!attachments.length) {
                                viewAttachmentList.innerHTML = '<p class="text-xs text-gray-500">No attachments</p>';
                            } else {
                                viewAttachmentList.innerHTML = attachments.map((file) => `
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                            <p class="text-xs text-gray-500">${escapeHtml(file.type || 'FILE')} | ${escapeHtml(formatBytes(file.size))}</p>
                                        </div>
                                        <a href="${escapeHtml(file.url || '#')}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:text-blue-700">View</a>
                                    </div>
                                `).join('');
                            }

                            showModal(viewModal);
                        };

                        const validateForm = () => {
                            resetValidation();
                            let valid = true;
                            if (!fields.title.value.trim()) {
                                errors.title.classList.remove('hidden');
                                valid = false;
                            }
                            if (!fields.consultationDate.value) {
                                errors.consultationDate.classList.remove('hidden');
                                valid = false;
                            }
                            if (!fields.summary.value.trim() && !fields.details.value.trim()) {
                                errors.body.classList.remove('hidden');
                                valid = false;
                            }
                            return valid;
                        };

                        openButton.addEventListener('click', openAddModal);

                        document.getElementById('closeConsultationFormModal').addEventListener('click', () => hideModal(formModal));
                        document.getElementById('cancelConsultationForm').addEventListener('click', () => hideModal(formModal));
                        document.getElementById('closeConsultationViewModal').addEventListener('click', () => hideModal(viewModal));
                        document.getElementById('closeConsultationViewFooter').addEventListener('click', () => hideModal(viewModal));
                        document.getElementById('editFromView').addEventListener('click', () => {
                            hideModal(viewModal);
                            if (viewNoteId !== null) openEditModal(viewNoteId);
                        });

                        [formModal, viewModal].forEach((modal) => {
                            modal.querySelector('[data-slideover-overlay]')?.addEventListener('click', () => hideModal(modal));
                        });
                        document.addEventListener('keydown', function (event) {
                            if (event.key !== 'Escape') return;
                            [formModal, viewModal].forEach((modal) => {
                                if (!modal.classList.contains('hidden')) hideModal(modal);
                            });
                        });

                        attachmentInput.addEventListener('change', function () {
                            const files = Array.from(attachmentInput.files || []);
                            if (!files.length) return;

                            files.forEach((file, index) => {
                                formAttachments.push({
                                    id: Date.now() + index + Math.floor(Math.random() * 1000),
                                    name: file.name,
                                    type: normalizeType(file.name, file.type),
                                    size: file.size || 0,
                                    url: URL.createObjectURL(file),
                                });
                            });

                            attachmentInput.value = '';
                            renderAttachmentsForForm();
                        });

                        selectedAttachments.addEventListener('click', function (event) {
                            const button = event.target.closest('.remove-attachment');
                            if (!button) return;
                            const targetId = Number(button.dataset.fileId);
                            formAttachments = formAttachments.filter((file) => Number(file.id) !== targetId);
                            renderAttachmentsForForm();
                        });

                        notesList.addEventListener('click', function (event) {
                            const viewBtn = event.target.closest('.note-view');
                            const editBtn = event.target.closest('.note-edit');

                            if (viewBtn) {
                                openViewModal(viewBtn.dataset.noteId);
                            }
                            if (editBtn) {
                                openEditModal(editBtn.dataset.noteId);
                            }
                        });

                        form.addEventListener('submit', function (event) {
                            event.preventDefault();
                            if (!validateForm()) return;

                            const now = new Date().toISOString();
                            const payload = {
                                id: editNoteId ?? Date.now(),
                                title: fields.title.value.trim(),
                                consultationDate: fields.consultationDate.value,
                                author: fields.author.value.trim() || defaultAuthor,
                                summary: fields.summary.value.trim(),
                                details: fields.details.value.trim(),
                                category: fields.category.value.trim(),
                                attachments: formAttachments.map((item) => ({ ...item })),
                                createdAt: now,
                                updatedAt: now,
                            };

                            if (editNoteId !== null) {
                                notes = notes.map((item) => Number(item.id) === editNoteId
                                    ? { ...item, ...payload, createdAt: item.createdAt || now, updatedAt: now }
                                    : item);
                            } else {
                                notes.push(payload);
                            }

                            renderNotes();
                            hideModal(formModal);
                        });

                        renderNotes();
                    });
                </script>
            @endif

            @if ($tab === 'activities')
                <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-phone mr-1"></i>Log Call</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-video mr-1"></i>Schedule Meeting</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-envelope mr-1"></i>Send Email</button>
                    <button class="h-10 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fas fa-square-check mr-1"></i>Add Task</button>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-2xl font-semibold text-gray-900">Activity Timeline</h2>
                    <div class="space-y-3">
                        @foreach ($tabData['activities'] as $activity)
                            <article class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                            <i class="fas {{ $activity['icon'] }} text-xs"></i>
                                        </span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $activity['type'] }}</h3>
                                            <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                                            <p class="mt-2 text-xs text-gray-500">{{ $activity['when'] }} | {{ $activity['owner'] }}</p>
                                        </div>
                                    </div>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $activity['status'] === 'Completed' ? 'bg-green-100 text-green-700' : ($activity['status'] === 'Sent' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $activity['status'] }}
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($tab === 'deals')
                @php
                    $dealStageClasses = [
                        'Inquiry' => 'bg-slate-100 text-slate-700 border border-slate-200',
                        'Qualification' => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'Consultation' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                        'Proposal' => 'bg-cyan-100 text-cyan-700 border border-cyan-200',
                        'Negotiation' => 'bg-amber-100 text-amber-700 border border-amber-200',
                        'Payment' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'Activation' => 'bg-violet-100 text-violet-700 border border-violet-200',
                        'Closed Lost' => 'bg-red-100 text-red-700 border border-red-200',
                    ];
                    $dealStatusClasses = [
                        'Open' => 'bg-blue-100 text-blue-700 border border-blue-200',
                        'Won' => 'bg-green-100 text-green-700 border border-green-200',
                        'Lost' => 'bg-red-100 text-red-700 border border-red-200',
                        'Pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
                    ];
                @endphp
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Related Deals</h2>
                        <p class="text-sm text-gray-500">Track all deals associated with this contact</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Add Deal</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Deal Name</th>
                                <th class="px-3 py-3 text-left">Stage</th>
                                <th class="px-3 py-3 text-left">Amount</th>
                                <th class="px-3 py-3 text-left">Closing Date</th>
                                <th class="px-3 py-3 text-left">Owner</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tabData['deals'] as $deal)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">{{ $deal['name'] }}</td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $dealStageClasses[$deal['stage']] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                            {{ $deal['stage'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">{{ $deal['amount'] }}</td>
                                    <td class="px-3 py-3 text-gray-700">{{ $deal['closing_date'] }}</td>
                                    <td class="px-3 py-3 text-gray-700">{{ $deal['owner'] }}</td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $dealStatusClasses[$deal['status']] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                            {{ $deal['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($tab === 'company')
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5">
                        <h2 class="text-2xl font-semibold text-gray-900">Company Information</h2>
                        <p class="text-sm text-gray-500">Details about the linked company</p>
                    </div>
                    <div class="border-b border-gray-100 bg-blue-50 p-5">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-white text-3xl text-blue-600 shadow-sm"><i class="far fa-building"></i></div>
                            <div>
                                <h3 class="text-3xl font-semibold text-gray-900">{{ $contact->company_name ?: 'ABC Corporation' }}</h3>
                                <p class="text-sm text-gray-600">Information Technology</p>
                                <button class="mt-2 h-9 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                    <i class="fas fa-up-right-from-square mr-1"></i>View Company Profile
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-5 border-b border-gray-100 p-5 text-sm md:grid-cols-2">
                        <div class="space-y-3">
                            <p><span class="font-semibold text-gray-700">Phone Number</span><br>+63 2 8123 4567</p>
                            <p><span class="font-semibold text-gray-700">Website</span><br><a href="#" class="text-blue-600">www.abccorp.com.ph</a></p>
                            <p><span class="font-semibold text-gray-700">Company Owner</span><br>{{ $contact->owner_name ?: 'John Admin' }}</p>
                        </div>
                        <div class="space-y-3">
                            <p><span class="font-semibold text-gray-700">Number of Employees</span><br>500-1000</p>
                            <p><span class="font-semibold text-gray-700">Year Founded</span><br>2010</p>
                            <p><span class="font-semibold text-gray-700">Address</span><br>Makati City, Metro Manila, Philippines</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <h4 class="mb-1 text-sm font-semibold text-gray-700">About</h4>
                        <p class="text-sm leading-relaxed text-gray-600">
                            ABC Corporation is a leading provider of enterprise software solutions in the Philippines, specializing in business automation, cloud services, and digital transformation.
                        </p>
                    </div>
                </div>
            @endif

            @if ($tab === 'projects')
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Projects</h2>
                        <p class="text-sm text-gray-500">Manage projects associated with this contact</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Create Project</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Project Name</th>
                                <th class="px-3 py-3 text-left">Project Type</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Start Date</th>
                                <th class="px-3 py-3 text-left">Assigned Team</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tabData['projects'] as $project)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">{{ $project['name'] }}</td>
                                    <td class="px-3 py-3">{{ $project['type'] }}</td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $project['status'] === 'In Progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $project['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">{{ $project['start_date'] }}</td>
                                    <td class="px-3 py-3">{{ $project['team'] }}</td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($tab === 'regular')
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Recurring Services</h2>
                        <p class="text-sm text-gray-500">Manage retainer and subscription services</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Add Recurring Service</button>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Service Name</th>
                                <th class="px-3 py-3 text-left">Frequency</th>
                                <th class="px-3 py-3 text-left">Fee</th>
                                <th class="px-3 py-3 text-left">Start Date</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tabData['regular']['items'] as $item)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">{{ $item['service'] }}</td>
                                    <td class="px-3 py-3"><span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">{{ $item['frequency'] }}</span></td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">{{ $item['fee'] }}</td>
                                    <td class="px-3 py-3">{{ $item['start_date'] }}</td>
                                    <td class="px-3 py-3"><span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">{{ $item['status'] }}</span></td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50 p-5">
                    <p class="text-sm text-gray-600">Total Monthly Recurring Revenue</p>
                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-4xl font-semibold text-blue-700">{{ $tabData['regular']['revenue'] }}</p>
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-2xl text-blue-600 shadow-sm">$</span>
                    </div>
                </div>
            @endif

            @if ($tab === 'products')
                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-gray-900">Purchased Products</h2>
                    <p class="text-sm text-gray-500">View all products purchased by this contact</p>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Product Name</th>
                                <th class="px-3 py-3 text-left">Price</th>
                                <th class="px-3 py-3 text-left">Quantity</th>
                                <th class="px-3 py-3 text-left">Total</th>
                                <th class="px-3 py-3 text-left">Date Purchased</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tabData['products']['items'] as $item)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><i class="far fa-cube mr-2 text-blue-600"></i>{{ $item['name'] }}</td>
                                    <td class="px-3 py-3">{{ $item['price'] }}</td>
                                    <td class="px-3 py-3">{{ $item['quantity'] }}</td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">{{ $item['total'] }}</td>
                                    <td class="px-3 py-3">{{ $item['date'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-3 py-3 text-right font-semibold text-gray-700">Grand Total:</td>
                                <td colspan="2" class="px-3 py-3 text-xl font-semibold text-blue-700">{{ $tabData['products']['grand_total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Products</p>
                        <p class="text-4xl font-semibold text-gray-900">{{ $tabData['products']['total_products'] }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Quantity</p>
                        <p class="text-4xl font-semibold text-gray-900">{{ $tabData['products']['total_quantity'] }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-4xl font-semibold text-blue-700">{{ $tabData['products']['total_revenue'] }}</p>
                    </div>
                </div>
            @endif

            @if ($tab === 'services')
                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-gray-900">Professional Services</h2>
                    <p class="text-sm text-gray-500">Services delivered to this contact</p>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-3 py-3 text-left">Service Name</th>
                                <th class="px-3 py-3 text-left">Description</th>
                                <th class="px-3 py-3 text-left">Fee</th>
                                <th class="px-3 py-3 text-left">Assigned Staff</th>
                                <th class="px-3 py-3 text-left">Status</th>
                                <th class="px-3 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tabData['services']['items'] as $item)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900"><i class="fas fa-gift mr-2 text-purple-600"></i>{{ $item['name'] }}</td>
                                    <td class="px-3 py-3">{{ $item['description'] }}</td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">{{ $item['fee'] }}</td>
                                    <td class="px-3 py-3">{{ $item['staff'] }}</td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $item['status'] === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3"><a href="#" class="text-blue-600 hover:text-blue-700"><i class="far fa-eye mr-1"></i>View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Total Services</p>
                        <p class="text-4xl font-semibold text-gray-900">{{ $tabData['services']['total_services'] }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <p class="text-sm text-gray-500">Completed</p>
                        <p class="text-4xl font-semibold text-green-700">{{ $tabData['services']['completed'] }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-sm text-gray-500">Total Value</p>
                        <p class="text-4xl font-semibold text-blue-700">{{ $tabData['services']['total_value'] }}</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
