@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-md overflow-hidden">
        @include('company.partials.company-header', [
            'company' => $company,
            'companyKycStatus' => $companyKycStatus,
            'companyKycStatusClass' => $companyKycStatusClass,
        ])

        <div class="bg-gray-50 p-4">
            @if (session('bif_success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('bif_success') }}
                </div>
            @endif

            @if (session('bif_client_link'))
                <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <p class="font-medium">Client BIF link generated</p>
                    <p class="mt-1 break-all">{{ session('bif_client_link') }}</p>
                </div>
            @endif

            <div id="companyKycApp">
                <div class="mb-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Business Information Form (BIF)</h2>
                                <p class="mt-1 text-xs text-gray-500">Saved business information is the source of truth for this company record.</p>
                            </div>
                            @if ($bif)
                                <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                            @endif
                        </div>

                        <div class="p-4">
                            @if ($bif)
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-[#f7f6f2] p-4">
                                    @include('company.bif.partials.document', ['wrapperClass' => 'bif-doc'])
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-gray-900">No business information submitted yet</h3>
                                    <p class="mt-2 text-sm text-gray-500">Create a Business Information Form to preview company KYC details here.</p>
                                    <a href="{{ route('company.bif.create', $company->id) }}" class="mt-5 inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                                        Create BIF
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Client Outreach</h3>
                            </div>
                            <div class="px-4 py-4">
                                @if ($bif)
                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure BIF link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">Send a secure BIF link to the client so they can complete missing details and upload onboarding documents.</p>
                                        <button type="button" data-open-send-bif-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send BIF
                                        </button>
                                    </div>
                                @else
                                    <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                                        <p class="text-sm font-semibold text-gray-900">Send secure BIF link</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600">You can send the client a secure BIF link even before the internal record is fully completed.</p>
                                        <button type="button" data-open-send-bif-modal class="mt-4 flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                            Send BIF
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Preview / PDF</h3>
                            </div>
                            <div class="space-y-4 px-4 py-4">
                                @if ($bif)
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="mb-3">
                                            <p class="text-sm font-semibold text-gray-900">PDF Tools</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-600">Preview the current BIF or export a print-friendly PDF copy for offline review.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id]) }}" target="_blank" class="flex h-12 w-full items-center justify-center rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition hover:bg-gray-100">Preview PDF</a>
                                            <a href="{{ route('company.bif.print', ['company' => $company->id, 'bif' => $bif->id, 'autoprint' => 1]) }}" target="_blank" class="flex h-12 w-full items-center justify-center rounded-xl bg-blue-600 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Download PDF</a>
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="mb-3">
                                            <p class="text-sm font-semibold text-gray-900">PDF Tools</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-600">Create or save the BIF first to unlock the printable preview and downloadable PDF.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <button type="button" disabled class="flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-400">Preview PDF</button>
                                            <button type="button" disabled class="flex h-12 w-full items-center justify-center rounded-xl bg-gray-200 text-sm font-semibold text-gray-500">Download PDF</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">Business Information Summary</h2>
                                @if ($bif)
                                    <a href="{{ route('company.bif.edit', ['company' => $company->id, 'bif' => $bif->id]) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                @endif
                            </div>
                            <div class="space-y-4 px-4 py-4 text-sm">
                                <div>
                                    <p class="text-gray-500">BIF Number</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->bif_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClass }}">{{ $statusLabel }}</span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date Submitted</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->submitted_at ? $bif->submitted_at->format('F j, Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Authorized Contact Person</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->authorized_contact_person_name ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TIN</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->tin_no ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Updated</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->updated_at ? $bif->updated_at->format('F j, Y g:i A') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Data Source</p>
                                    <p class="font-medium text-gray-900">
                                        @if (($bif?->last_submission_source ?? null) === 'client')
                                            Client-submitted
                                        @elseif (($bif?->last_submission_source ?? null) === 'manual')
                                            Manually encoded
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Last Client Submission</p>
                                    <p class="font-medium text-gray-900">{{ $bif?->client_submitted_at ? $bif->client_submitted_at->format('F j, Y g:i A') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                            </div>
                            <div class="space-y-2 px-4 py-4">
                                <button type="button" class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white {{ $bif ? 'hover:bg-blue-700' : 'cursor-not-allowed opacity-60' }}" @disabled(! $bif)>Submit For Verification</button>
                                <div class="pt-2">
                                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">KYC Activity</p>
                                    <div class="space-y-1 text-xs text-gray-500">
                                        @foreach ($kycActivityLogs as $log)
                                            <p>{{ $log }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <h2 class="text-base font-semibold text-gray-900">Business Onboarding Requirements</h2>
                            <p class="mt-1 text-xs text-gray-500">Upload and manage the required onboarding documents based on the client type.</p>
                        </div>
                        <div class="max-h-[520px] space-y-3 overflow-y-auto p-4">
                            @foreach ($kycRequirements as $group)
                                <section class="rounded-xl border border-gray-200 bg-white p-3">
                                    <div class="border-b border-gray-100 pb-3">
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $group['group'] }}</h3>
                                    </div>
                                    <div class="space-y-3 pt-3">
                                        @foreach ($group['items'] as $requirement)
                                            <article class="rounded-xl border border-gray-200 bg-white p-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $requirement['label'] }}</p>
                                                        <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $requirement['uploaded'] ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                                            {{ $requirement['uploaded'] ? 'Uploaded' : 'Missing' }}
                                                        </span>
                                                        <p class="mt-1 text-xs text-gray-500">{{ $requirement['helper'] }}</p>
                                                    </div>
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <button type="button" class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 hover:bg-gray-50">{{ $requirement['uploaded'] ? 'Replace' : 'Upload' }}</button>
                                                        <button type="button" class="rounded-md border border-gray-200 px-2 py-1 text-gray-600 {{ $requirement['uploaded'] ? 'hover:bg-gray-50' : 'opacity-40 pointer-events-none' }}">View</button>
                                                        <button type="button" class="rounded-md border border-red-200 px-2 py-1 text-red-600 {{ $requirement['uploaded'] ? 'hover:bg-red-50' : 'opacity-40 pointer-events-none' }}">Remove</button>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-slide-over id="sendBifModal" width="sm:max-w-[560px]">
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Send Business Information Form</h2>
                <p class="mt-1 text-sm text-gray-500">Email a secure BIF link to the client so they can complete missing business details and upload onboarding documents.</p>
            </div>
            <button type="button" data-close-send-bif-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('company.bif.send', $company->id) }}" class="flex min-h-0 flex-1 flex-col">
        @csrf
        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                The secure link opens a mobile-friendly client form. Submitted details populate the Company KYC BIF record and uploaded documents are linked back to this company profile.
            </div>

            <div>
                <label for="recipient_email" class="mb-1 block text-sm font-medium text-gray-700">Recipient Email</label>
                <input id="recipient_email" name="recipient_email" type="email" value="{{ old('recipient_email', $bifRecipientEmail) }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">Defaults to the authorized contact person email from the BIF when available. You can change it before sending.</p>
                @error('recipient_email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                <p class="font-semibold uppercase tracking-wide text-gray-500">What the client can do</p>
                <p class="mt-2">Complete business organization, address, ownership structure, signatories, UBOs, and upload supporting documents based on client type.</p>
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-send-bif-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-9 min-w-[140px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Send BIF Link
                </button>
            </div>
        </div>
    </form>
</x-slide-over>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sendBifModal = document.getElementById('sendBifModal');
        const openSendBifButtons = document.querySelectorAll('[data-open-send-bif-modal]');
        const closeSendBifButtons = document.querySelectorAll('[data-close-send-bif-modal]');
        const shouldOpenSendModal = @json($errors->has('recipient_email'));

        const openModal = () => window.jkncSlideOver.open(sendBifModal);
        const closeModal = () => window.jkncSlideOver.close(sendBifModal);

        openSendBifButtons.forEach((button) => {
            button.addEventListener('click', openModal);
        });

        closeSendBifButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        sendBifModal?.addEventListener('click', function (event) {
            if (event.target === sendBifModal || event.target.hasAttribute('data-drawer-overlay')) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        if (shouldOpenSendModal) {
            openModal();
        }
    });
</script>
@endsection
