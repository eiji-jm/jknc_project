@extends('layouts.app')

@section('content')
@php
    $statusPillClasses = [
        'Verified' => 'bg-green-100 text-green-700 border border-green-200',
        'Pending Verification' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Not Submitted' => 'bg-gray-100 text-gray-600 border border-gray-200',
        'Rejected' => 'bg-red-100 text-red-700 border border-red-200',
    ];

    $status = $contact->kyc_status ?: 'Not Submitted';
    $name = trim($contact->first_name.' '.$contact->last_name);
    $initials = strtoupper(mb_substr($contact->first_name ?? '', 0, 1).mb_substr($contact->last_name ?? '', 0, 1));
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
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClasses[$status] ?? $statusPillClasses['Not Submitted'] }}">{{ strtolower($status) }}</span>
                    <span class="text-sm text-gray-700">Contact Owner: {{ $contact->owner_name ?: 'John Admin' }}</span>
                </div>
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
            @if ($tab === 'kyc')
                <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <h2 class="text-base font-semibold text-gray-900">KYC Information</h2>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                            </div>
                            <div class="space-y-4 px-4 py-4 text-sm">
                                <div>
                                    <p class="text-gray-500">CIF</p>
                                    <p class="font-medium text-gray-900">123456</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">TIN</p>
                                    <p class="font-medium text-gray-900">123-456-789</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">KYC Status</p>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusPillClasses[$status] ?? $statusPillClasses['Not Submitted'] }}">
                                        {{ $status }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date Verified</p>
                                    <p class="font-medium text-gray-900">02/26/2026</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Verified By</p>
                                    <p class="font-medium text-gray-900">{{ $contact->owner_name ?: 'John Admin' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                            </div>
                            <div class="space-y-2 px-4 py-4">
                                <button class="h-10 w-full rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">Submit For Verification</button>
                                <button class="h-10 w-full rounded-lg bg-green-600 text-sm font-medium text-white hover:bg-green-700">Approve</button>
                                <button class="h-10 w-full rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700">Reject</button>
                                <div class="pt-3 text-xs text-gray-500">
                                    <p>Submitted for verification on Apr 02, 2024 by John Admin</p>
                                    <p class="mt-2">Approved KYC on Apr 24, 2024 by John Admin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <h2 class="text-base font-semibold text-gray-900">Uploaded Documents</h2>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700">Send CIF</a>
                        </div>
                        <div class="flex min-h-[410px] flex-col items-center justify-center text-gray-500">
                            <i class="far fa-file-alt text-6xl"></i>
                            <p class="mt-3 text-sm font-medium text-gray-700">No File has been uploaded yet</p>
                        </div>
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
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Consultation Notes</h2>
                        <p class="text-sm text-gray-500">Record and track all consultation sessions</p>
                    </div>
                    <button class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">+ Add Consultation Note</button>
                </div>
                <div class="space-y-3">
                    @foreach ($tabData['consultation-notes'] as $note)
                        <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $note['title'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $note['description'] }}</p>
                                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                        <span><i class="far fa-calendar mr-1"></i>{{ $note['date'] }}</span>
                                        <span><i class="far fa-user mr-1"></i>{{ $note['owner'] }}</span>
                                        <span><i class="fas fa-paperclip mr-1"></i>{{ $note['attachments'] }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-gray-500">
                                    <a href="#"><i class="far fa-eye"></i></a>
                                    <a href="#"><i class="far fa-pen-to-square"></i></a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
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
                                    <td class="px-3 py-3"><span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">{{ $deal['stage'] }}</span></td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">{{ $deal['amount'] }}</td>
                                    <td class="px-3 py-3">{{ $deal['closing_date'] }}</td>
                                    <td class="px-3 py-3">{{ $deal['owner'] }}</td>
                                    <td class="px-3 py-3"><span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">{{ $deal['status'] }}</span></td>
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
