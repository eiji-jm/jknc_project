@extends('layouts.app')

@section('title', 'Sales & Marketing | IDA Records')

@section('content')
<div class="flex-1 overflow-y-auto p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">IDA Records</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Incentive Distribution & Allocation records will appear here.
                    </p>
                </div>

                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i>
                    Add IDA
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
            No IDA records yet.
        </div>
    </div>
</div>
@endsection