@extends('layouts.app')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #bir-print, #bir-print * { visibility: visible; }
        #bir-print { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="text-lg font-semibold">BIR & Tax Preview</div>
                <div class="text-xs text-gray-500">TIN {{ $tax->tin ?? '—' }}</div>
            </div>
            <div class="flex-1"></div>
            <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Edit</a>
            <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Delete this BIR & Tax entry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Delete</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 p-6">
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-gray-900 rounded-xl overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="text-gray-300 text-sm font-medium">BIR & Tax Document</span>
                        <div class="flex-1"></div>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()"><i class="fas fa-print"></i></button>
                        <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition" onclick="window.print()"><i class="fas fa-download"></i></button>
                    </div>
                    <div class="p-6 overflow-auto">
                        <div id="bir-print" class="bg-white w-full max-w-md rounded-sm shadow-2xl mx-auto" style="aspect-ratio: 8.5/11;">
                            <div class="p-8 h-full flex flex-col justify-between text-center">
                                <div class="border-b-2 border-gray-800 pb-4 mb-4">
                                    <h1 class="text-xl font-bold text-gray-900">BIR & TAX</h1>
                                    <p class="text-xs text-gray-600 mt-2">{{ $tax->tax_payer ?? '—' }}</p>
                                </div>
                                <div class="flex-1 flex flex-col justify-center space-y-3 text-xs text-gray-700">
                                    <p><strong>TIN:</strong> {{ $tax->tin ?? '—' }}</p>
                                    <p><strong>Tax Types:</strong> {{ $tax->tax_types ?? '—' }}</p>
                                    <p><strong>Filing Frequency:</strong> {{ $tax->filing_frequency ?? '—' }}</p>
                                    <p><strong>Due Date:</strong> {{ optional($tax->due_date)->format('M d, Y') ?? '—' }}</p>
                                </div>
                                <div class="border-t-2 border-gray-800 pt-3 text-xs text-gray-600">
                                    Uploaded by {{ $tax->uploaded_by ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="text-sm font-semibold text-gray-900 mb-3">BIR & Tax Details</div>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">TIN</span><div class="font-medium text-gray-900">{{ $tax->tin ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Tax Payer</span><div class="font-medium text-gray-900">{{ $tax->tax_payer ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registering Office</span><div class="font-medium text-gray-900">{{ $tax->registering_office ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Registered Address</span><div class="font-medium text-gray-900">{{ $tax->registered_address ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Form Type</span><div class="font-medium text-gray-900">{{ $tax->form_type ?? '—' }}</div></div>
                        <div><span class="text-xs text-gray-600 uppercase tracking-wide">Date Uploaded</span><div class="font-medium text-gray-900">{{ optional($tax->date_uploaded)->format('M d, Y') ?? '—' }}</div></div>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-download"></i>
                        Download PDF
                    </button>
                    <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
