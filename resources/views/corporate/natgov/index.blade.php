@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{
    showAddPanel: false,
    formData: {
        client: '',
        tin: '',
        agency: '',
        registrationStatus: '',
        registrationDate: '',
        registrationNo: '',
        status: '',
        uploadedBy: '',
        dateUploaded: '',
        uploadedFile: ''
    }
}" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <div class="text-lg font-semibold">NatGov</div>
            <div class="flex-1"></div>
            <button @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add NatGov
            </button>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SEARCH SECTION --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" placeholder="Search client..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="text" placeholder="Search agency..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <input type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Govt Body/Agency</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Registration Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Reg. Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Registration No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Uploaded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @php
                            $sampleGov = [
                                [
                                    'client' => 'John Kelly & Co.',
                                    'tin' => '123-456-789-000',
                                    'agency' => 'SSS',
                                    'registrationStatus' => 'Registered',
                                    'registrationDate' => '2021-06-15',
                                    'registrationNo' => 'SSS-001234',
                                    'status' => 'Active',
                                    'uploadedBy' => 'Admin User',
                                    'dateUploaded' => '2024-02-06'
                                ],
                                [
                                    'client' => 'John Kelly & Co.',
                                    'tin' => '123-456-789-000',
                                    'agency' => 'Pag-IBIG',
                                    'registrationStatus' => 'Registered',
                                    'registrationDate' => '2021-06-20',
                                    'registrationNo' => 'PAG-009876',
                                    'status' => 'Active',
                                    'uploadedBy' => 'Compliance Officer',
                                    'dateUploaded' => '2024-02-18'
                                ],
                                [
                                    'client' => 'John Kelly & Co.',
                                    'tin' => '123-456-789-000',
                                    'agency' => 'PhilHealth',
                                    'registrationStatus' => 'Registered',
                                    'registrationDate' => '2021-07-01',
                                    'registrationNo' => 'PH-004321',
                                    'status' => 'Active',
                                    'uploadedBy' => 'Finance Manager',
                                    'dateUploaded' => '2024-02-12'
                                ],
                                [
                                    'client' => 'John Kelly & Co.',
                                    'tin' => '123-456-789-000',
                                    'agency' => 'DOLE',
                                    'registrationStatus' => 'Registered',
                                    'registrationDate' => '2021-07-10',
                                    'registrationNo' => 'DOLE-778899',
                                    'status' => 'Active',
                                    'uploadedBy' => 'Admin User',
                                    'dateUploaded' => '2024-02-20'
                                ]
                            ];
                        @endphp

                        @foreach($sampleGov as $gov)
                            <tr onclick="window.location='{{ route('natgov.preview', ['ref' => $gov['agency']]) }}'" class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3 font-medium">{{ $gov['client'] }}</td>
                                <td class="px-4 py-3">{{ $gov['tin'] }}</td>
                                <td class="px-4 py-3">{{ $gov['agency'] }}</td>
                                <td class="px-4 py-3">{{ $gov['registrationStatus'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($gov['registrationDate'])->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $gov['registrationNo'] }}</td>
                                <td class="px-4 py-3">{{ $gov['status'] }}</td>
                                <td class="px-4 py-3">{{ $gov['uploadedBy'] }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($gov['dateUploaded'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ADD NATGOV SLIDER --}}
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
                <div class="text-lg font-semibold">Add NatGov</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Client</label>
                        <input x-model="formData.client" type="text" placeholder="Client name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">TIN</label>
                        <input x-model="formData.tin" type="text" placeholder="e.g., 123-456-789-000" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Government Body/Agency</label>
                        <select x-model="formData.agency" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select agency...</option>
                            <option value="SSS">SSS</option>
                            <option value="Pag-IBIG">Pag-IBIG</option>
                            <option value="PhilHealth">PhilHealth</option>
                            <option value="DOLE">DOLE</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Registration Status</label>
                        <input x-model="formData.registrationStatus" type="text" placeholder="Registered / Pending" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Registration Date</label>
                        <input x-model="formData.registrationDate" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Registration No.</label>
                        <input x-model="formData.registrationNo" type="text" placeholder="Registration number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Status</label>
                        <input x-model="formData.status" type="text" placeholder="Active / Inactive" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
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
                        <label class="text-xs text-gray-600">Upload NatGov Document (PDF)</label>
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
                    Save NatGov
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
