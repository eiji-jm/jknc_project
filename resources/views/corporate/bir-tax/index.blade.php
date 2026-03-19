@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{
    showAddPanel: false,
    formData: {
        tin: '',
        taxPayer: '',
        registeringOffice: '',
        registeredAddress: '',
        taxTypes: '',
        formType: '',
        filingFrequency: '',
        dueDate: '',
        uploadedBy: '',
        dateUploaded: '',
        uploadedFile: ''
    }
}" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">BIR & Tax</div>
            <div class="flex-1"></div>
            <button @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add BIR & Tax
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH SECTION --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" placeholder="Search TIN..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="text" placeholder="Search taxpayer..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Tax Payer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Registering Office</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Registered Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Tax Types</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Form Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Filing Frequency</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Due Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @php
                            $sampleTaxes = [
                                [
                                    'tin' => '123-456-789-000',
                                    'taxPayer' => 'John Kelly & Co.',
                                    'registeringOffice' => 'BIR RDO 44',
                                    'registeredAddress' => 'Makati City, PH',
                                    'taxTypes' => 'VAT, WHT',
                                    'formType' => '1701Q',
                                    'filingFrequency' => 'Quarterly',
                                    'dueDate' => '2024-04-30',
                                    'uploadedBy' => 'Admin User',
                                    'dateUploaded' => '2024-02-06'
                                ],
                                [
                                    'tin' => '987-654-321-000',
                                    'taxPayer' => 'JKC Holdings',
                                    'registeringOffice' => 'BIR RDO 51',
                                    'registeredAddress' => 'Quezon City, PH',
                                    'taxTypes' => 'Income Tax',
                                    'formType' => '1702',
                                    'filingFrequency' => 'Annual',
                                    'dueDate' => '2024-04-15',
                                    'uploadedBy' => 'Compliance Officer',
                                    'dateUploaded' => '2024-02-18'
                                ],
                                [
                                    'tin' => '555-222-333-000',
                                    'taxPayer' => 'JKC Services',
                                    'registeringOffice' => 'BIR RDO 38',
                                    'registeredAddress' => 'Cebu City, PH',
                                    'taxTypes' => 'Percentage Tax',
                                    'formType' => '2551M',
                                    'filingFrequency' => 'Monthly',
                                    'dueDate' => '2024-03-20',
                                    'uploadedBy' => 'Finance Manager',
                                    'dateUploaded' => '2024-02-12'
                                ]
                            ];
                        @endphp

                        @foreach($sampleTaxes as $tax)
                            <tr onclick="window.location='{{ route('bir-tax.preview', ['ref' => $tax['tin']]) }}'" class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3 font-medium">{{ $tax['tin'] }}</td>
                                <td class="px-4 py-3">{{ $tax['taxPayer'] }}</td>
                                <td class="px-4 py-3">{{ $tax['registeringOffice'] }}</td>
                                <td class="px-4 py-3">{{ $tax['registeredAddress'] }}</td>
                                <td class="px-4 py-3">{{ $tax['taxTypes'] }}</td>
                                <td class="px-4 py-3">{{ $tax['formType'] }}</td>
                                <td class="px-4 py-3">{{ $tax['filingFrequency'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tax['dueDate'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $tax['uploadedBy'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tax['dateUploaded'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ADD BIR & TAX SLIDER --}}
    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="text-lg font-semibold">Add BIR & Tax</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">TIN</label>
                        <input x-model="formData.tin" type="text" placeholder="e.g., 123-456-789-000" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Tax Payer</label>
                        <input x-model="formData.taxPayer" type="text" placeholder="Tax payer name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Registering Office</label>
                        <input x-model="formData.registeringOffice" type="text" placeholder="BIR RDO" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Registered Address</label>
                        <input x-model="formData.registeredAddress" type="text" placeholder="Address" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Tax Types</label>
                        <input x-model="formData.taxTypes" type="text" placeholder="VAT, WHT, etc." class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Form Type</label>
                        <input x-model="formData.formType" type="text" placeholder="Form type" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Filing Frequency</label>
                        <input x-model="formData.filingFrequency" type="text" placeholder="Monthly, Quarterly, Annual" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Due Date</label>
                        <input x-model="formData.dueDate" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input x-model="formData.uploadedBy" type="text" placeholder="Name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Uploaded</label>
                        <input x-model="formData.dateUploaded" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Upload BIR & Tax Document (PDF)</label>
                        <input type="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save BIR & Tax
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
