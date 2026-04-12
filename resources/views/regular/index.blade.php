@extends('layouts.app')

@section('content')
@php
    $regularStages = [
        ['title' => 'Planning', 'detail' => 'RSAT setup per service cycle'],
        ['title' => 'Review', 'detail' => 'Internal validation and approval'],
        ['title' => 'Client Approval', 'detail' => 'Notice to Proceed (NTP)'],
        ['title' => 'Execution', 'detail' => 'Recurring service delivery'],
        ['title' => 'Reporting & Delivery', 'detail' => 'RSAT Report and Transmittal'],
        ['title' => 'Continuation', 'detail' => 'Repeat next service cycle'],
    ];

    $requiredControls = [
        'Approved Service Memo',
        'Approved RSAT before planning/execution',
        'Client-approved NTP before each cycle or revised plan',
        'Documented reports before delivery',
        'Traceable recurring cycle history',
    ];

    $forms = [
        ['code' => 'REG-F-001', 'name' => 'RSAT (Service Action & Task Plan)', 'owner' => 'Operations'],
        ['code' => 'REG-F-002', 'name' => 'RSAT Report', 'owner' => 'Operations'],
        ['code' => 'REG-F-003', 'name' => 'Notice to Proceed', 'owner' => 'Sales / Consultant'],
        ['code' => 'REG-F-004', 'name' => 'Transmittal Form', 'owner' => 'Admin'],
        ['code' => 'REG-F-005', 'name' => 'Service Memo', 'owner' => 'Operations'],
    ];
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Regular</h1>
                <p class="mt-1 max-w-3xl text-sm text-gray-500">
                    Regular is for recurring or retainer engagements. It should support repeatable planning, approval, execution, reporting, delivery, and continuation cycles.
                </p>
            </div>
            <button type="button" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <i class="fas fa-plus mr-2 text-xs"></i> New Regular Cycle
            </button>
        </div>

        <div class="mb-6 grid gap-3 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Use Case</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Recurring / Retainer</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Execution Gate</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Approved RSAT + NTP</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Delivery Style</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Cycle-Based</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">End State</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Continuation</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recommended Module Layout</h2>
                    <p class="mt-1 text-sm text-gray-500">This first version focuses on the operational flow described in your REGULAR policy.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($regularStages as $stage)
                        <article class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $loop->iteration }}</p>
                            <h3 class="mt-2 text-base font-semibold text-gray-900">{{ $stage['title'] }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $stage['detail'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-5">
                    <h3 class="text-base font-semibold text-gray-900">Suggested Tabs</h3>
                    <p class="mt-1 text-sm text-gray-500">A Regular record can later track the service cycle and all recurring approvals and outputs.</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-sm">
                        @foreach (['Overview', 'RSAT', 'NTP', 'Execution', 'Reports', 'Delivery', 'Cycle History', 'Timeline'] as $tab)
                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-gray-700">{{ $tab }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900">Control Rules</h2>
                    <div class="mt-4 space-y-2">
                        @foreach ($requiredControls as $rule)
                            <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                <i class="fas fa-shield-halved mt-0.5 text-amber-600"></i>
                                <span>{{ $rule }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900">Forms Registry Preview</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($forms as $form)
                            <article class="rounded-xl border border-gray-200 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $form['code'] }}</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $form['name'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">Owner: {{ $form['owner'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
