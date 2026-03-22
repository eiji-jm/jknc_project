@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR: title --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">Stock and Transfer Book</div>
            <div class="flex-1"></div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- CONTENT --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Ledger --}}
                <a href="{{ route('stock-transfer-book.ledger') }}" class="p-6 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition">
                            <i class="fas fa-book text-blue-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-700">Ledger</h3>
                            <p class="text-xs text-gray-500 mt-1">Manage shareholder records and ownership details</p>
                        </div>
                    </div>
                </a>

                {{-- Journal --}}
                <a href="{{ route('stock-transfer-book.journal') }}" class="p-6 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg bg-green-100 group-hover:bg-green-200 flex items-center justify-center transition">
                            <i class="fas fa-feather text-green-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-green-700">Journal</h3>
                            <p class="text-xs text-gray-500 mt-1">Record stock transactions and transfers</p>
                        </div>
                    </div>
                </a>

                {{-- Index --}}
                <a href="{{ route('stock-transfer-book.index') }}" class="p-6 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center transition">
                            <i class="fas fa-list-ol text-purple-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-purple-700">Index</h3>
                            <p class="text-xs text-gray-500 mt-1">View shareholder index and cross-references</p>
                        </div>
                    </div>
                </a>

                {{-- Installment --}}
                <a href="{{ route('stock-transfer-book.installment') }}" class="p-6 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg bg-yellow-100 group-hover:bg-yellow-200 flex items-center justify-center transition">
                            <i class="fas fa-calculator text-yellow-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-yellow-700">Installment</h3>
                            <p class="text-xs text-gray-500 mt-1">Track stock installment payments</p>
                        </div>
                    </div>
                </a>

                {{-- Certificates --}}
                <a href="{{ route('stock-transfer-book.certificates') }}" class="p-6 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center transition">
                            <i class="fas fa-certificate text-red-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-red-700">Certificates</h3>
                            <p class="text-xs text-gray-500 mt-1">Manage stock certificates and issues</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

    </div>

</div>
@endsection
