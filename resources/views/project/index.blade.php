@extends('layouts.app')

@section('content')
@php
    $projectStages = [
        ['title' => 'Registration', 'detail' => 'Project Order / Work Order'],
        ['title' => 'Planning', 'detail' => 'Scope of Work (SOW)'],
        ['title' => 'Approval', 'detail' => 'Reviewed SOW and client NTP'],
        ['title' => 'Execution', 'detail' => 'Assigned PM and delivery work'],
        ['title' => 'Closure', 'detail' => 'Report, Transmittal, Certificate of Completion'],
    ];

    $requiredControls = [
        'Approved Service Memo',
        'Project Order / Work Order',
        'Approved Scope of Work (SOW)',
        'Assigned Project Manager',
        'Client-approved Notice to Proceed (NTP)',
        'SOW Report and Transmittal before closure',
    ];

    $forms = [
        ['code' => 'PROJ-F-001', 'name' => 'Project Order / Work Order', 'owner' => 'Operations'],
        ['code' => 'PROJ-F-002', 'name' => 'Scope of Work (SOW)', 'owner' => 'Consultant'],
        ['code' => 'PROJ-F-003', 'name' => 'Notice to Proceed (NTP)', 'owner' => 'Sales / Consultant'],
        ['code' => 'PROJ-F-004', 'name' => 'SOW Report', 'owner' => 'Operations'],
        ['code' => 'PROJ-F-006', 'name' => 'Transmittal Form', 'owner' => 'Admin'],
        ['code' => 'PROJ-F-007', 'name' => 'Certificate of Completion', 'owner' => 'Operations'],
    ];
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mx-auto max-w-[1600px]">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Project</h1>
                <p class="mt-1 max-w-3xl text-sm text-gray-500">
                    Project is for one-time, fixed-scope, deliverable-based engagements. It should run from work order and planning through client approval, execution, delivery, and formal completion.
                </p>
            </div>
            <button type="button" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <i class="fas fa-plus mr-2 text-xs"></i> New Project
            </button>
        </div>

        <div class="mb-6 grid gap-3 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Use Case</p>
                <p class="mt-2 text-lg font-bold text-gray-900">One-Time Delivery</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Execution Gate</p>
                <p class="mt-2 text-lg font-bold text-gray-900">NTP Required</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Primary Owner</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Project Manager</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">End State</p>
                <p class="mt-2 text-lg font-bold text-gray-900">Completion Certificate</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recommended Module Layout</h2>
                    <p class="mt-1 text-sm text-gray-500">This is the first-pass structure based on your policy and can be refined later once you share the exact forms and fields.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    @foreach ($projectStages as $stage)
                        <article class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $loop->iteration }}</p>
                            <h3 class="mt-2 text-base font-semibold text-gray-900">{{ $stage['title'] }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $stage['detail'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-5">
                    <h3 class="text-base font-semibold text-gray-900">Suggested Tabs</h3>
                    <p class="mt-1 text-sm text-gray-500">A Project record can later use tabs similar to other modules, but focused on execution.</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-sm">
                        @foreach (['Overview', 'Work Order', 'SOW', 'NTP', 'Execution', 'Reports', 'Delivery', 'Completion', 'Timeline'] as $tab)
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
