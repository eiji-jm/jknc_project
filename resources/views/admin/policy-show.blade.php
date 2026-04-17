@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Policy Review</h1>
                <p class="text-sm text-gray-500 mt-1">Review submitted policy details</p>
            </div>

            <a href="{{ route('admin.policies.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Back
            </a>
        </div>

        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Policy Information</h2>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Code</p>
                            <p class="font-medium text-gray-800">{{ $policy->code ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Policy Title</p>
                            <p class="font-medium text-gray-800">{{ $policy->policy ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Version</p>
                            <p>{{ $policy->version ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Effectivity Date</p>
                            <p>{{ $policy->effectivity_date ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Prepared By</p>
                            <p>{{ $policy->prepared_by ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Reviewed By</p>
                            <p>{{ $policy->reviewed_by ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Approved By</p>
                            <p>{{ $policy->approved_by ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Classification</p>
                            <p>{{ $policy->classification ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Approval Status</p>
                            <p>{{ $policy->approval_status ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Workflow Status</p>
                            <p>{{ $policy->workflow_status ?? '-' }}</p>
                        </div>

                        @if(!empty($policy->review_note))
                            <div>
                                <p class="text-gray-500 text-xs">Review Note</p>
                                <p>{{ $policy->review_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl p-5 bg-white">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>

                    <div class="flex flex-wrap gap-3">
                        @if(($policy->workflow_status ?? null) === 'Submitted' && Auth::user()->hasPermission('approve_policies'))
                            <form method="POST" action="{{ route('admin.policies.approve', $policy->id) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition"
                                >
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.policies.reject', $policy->id) }}">
                                @csrf
                                <input type="hidden" name="review_note" value="Rejected by admin">
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition"
                                >
                                    Reject
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.policies.revise', $policy->id) }}">
                                @csrf
                                <input type="hidden" name="review_note" value="Needs revision">
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition"
                                >
                                    Revise
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('policies.preview', [
                                'policy' => $policy->policy,
                                'version' => $policy->version,
                                'effectivity_date' => $policy->effectivity_date,
                                'prepared_by' => $policy->prepared_by,
                                'reviewed_by' => $policy->reviewed_by,
                                'approved_by' => $policy->approved_by,
                                'classification' => $policy->classification,
                                'description' => $policy->description,
                            ]) }}"
                           target="_blank"
                           class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                            View PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-6 bg-white">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Policy Content</h2>

                <div class="prose prose-sm max-w-none policy-preview-body">
                    {!! $policy->description ?? '<p class="text-gray-400">No description provided.</p>' !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
