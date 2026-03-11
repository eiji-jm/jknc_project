@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="{ showPreview: false, selectedShareholder: null, showAddPanel: false }" @keydown.escape.window="showAddPanel = false">

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center gap-3 px-4 py-4">
            <a href="{{ route('stock-transfer-book') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold" x-show="!showPreview">Ledger</div>
            <div class="text-lg font-semibold" x-show="showPreview">Shareholder Details</div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2">
                <button x-show="!showPreview" @click="showAddPanel = true" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Shareholder
                </button>
                <button x-show="showPreview" @click="showPreview = false; selectedShareholder = null" class="h-9 px-4 rounded-full bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Ledger
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- NAVIGATION TABS --}}
        <div x-show="!showPreview" class="px-4 py-3 border-b border-gray-100 flex gap-1 bg-gray-50">
            <a href="{{ route('stock-transfer-book.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Index</a>
            <a href="{{ route('stock-transfer-book.journal') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Journal</a>
            <a href="{{ route('stock-transfer-book.ledger') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white">Ledger</a>
            <a href="{{ route('stock-transfer-book.installment') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Installment</a>
            <a href="{{ route('stock-transfer-book.certificates') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Certificates</a>
        </div>

        {{-- SHAREHOLDER SUMMARY --}}
        <div x-show="!showPreview" class="px-4 py-4 bg-gray-50 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Shareholder Information</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs text-gray-600">Family Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter family name">
                </div>
                <div>
                    <label class="text-xs text-gray-600">First Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter first name">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Middle Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter middle name">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Nationality</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter nationality">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Current Residential Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter address">
                </div>
                <div>
                    <label class="text-xs text-gray-600">TIN</label>
                    <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter TIN">
                </div>
            </div>
        </div>

        {{-- LEDGER TABLE VIEW --}}
        <div x-show="!showPreview" class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Family Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">First Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Middle Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Nationality</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">TIN</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedShareholder = {
                            firstName: 'John',
                            middleName: 'Michael',
                            familyName: 'Kelly',
                            fullName: 'John Michael Kelly',
                            nationality: 'Filipino',
                            address: '1234 Elm Street, Ayala, Manila',
                            tin: '123-45-6789',
                            shares: '1000',
                            certificateNo: 'CERT-001',
                            dateRegistered: 'Jan 15, 2026',
                            status: 'Active',
                            email: 'john.kelly@jkc.com',
                            phone: '+63 2 1234 5678'
                        }">
                            <td class="px-4 py-3">Kelly</td>
                            <td class="px-4 py-3">John</td>
                            <td class="px-4 py-3">Michael</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">1234 Elm Street, Ayala</td>
                            <td class="px-4 py-3">123-45-6789</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedShareholder = {
                            firstName: 'Carmen',
                            middleName: 'Maria',
                            familyName: 'Rodriguez',
                            fullName: 'Carmen Maria Rodriguez',
                            nationality: 'Filipino',
                            address: '5678 Oak Avenue, Makati, Manila',
                            tin: '456-78-9012',
                            shares: '500',
                            certificateNo: 'CERT-002',
                            dateRegistered: 'Feb 01, 2026',
                            status: 'Active',
                            email: 'carmen.rodriguez@email.com',
                            phone: '+63 2 8765 4321'
                        }">
                            <td class="px-4 py-3">Rodriguez</td>
                            <td class="px-4 py-3">Carmen</td>
                            <td class="px-4 py-3">Maria</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">5678 Oak Avenue, Makati</td>
                            <td class="px-4 py-3">456-78-9012</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" @click="showPreview = true; selectedShareholder = {
                            firstName: 'Miguel',
                            middleName: 'Antonio',
                            familyName: 'Santos',
                            fullName: 'Miguel Antonio Santos',
                            nationality: 'Filipino',
                            address: '9012 Cedar Road, BGC, Manila',
                            tin: '234-56-7890',
                            shares: '750',
                            certificateNo: 'CERT-003',
                            dateRegistered: 'Feb 10, 2026',
                            status: 'Active',
                            email: 'miguel.santos@email.com',
                            phone: '+63 2 5432 1098'
                        }">
                            <td class="px-4 py-3">Santos</td>
                            <td class="px-4 py-3">Miguel</td>
                            <td class="px-4 py-3">Antonio</td>
                            <td class="px-4 py-3">Filipino</td>
                            <td class="px-4 py-3">9012 Cedar Road, BGC</td>
                            <td class="px-4 py-3">234-56-7890</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SHAREHOLDER PREVIEW VIEW --}}
        <div x-show="showPreview" class="p-6">
            <template x-if="selectedShareholder">
                <div class="grid grid-cols-3 gap-6 h-[calc(100vh-13rem)]">

                    {{-- DOCUMENT SIDE --}}
                    <div class="col-span-2 bg-gray-900 rounded-lg overflow-hidden flex flex-col">
                        {{-- DOCUMENT VIEWER TOOLBAR --}}
                        <div class="bg-gray-800 px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="text-gray-400 text-sm mx-2">Page 1 of 1</span>
                            <div class="flex-1"></div>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="p-2 hover:bg-gray-700 rounded text-gray-300 transition">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>

                        {{-- DOCUMENT MOCKUP --}}
                        <div class="flex-1 overflow-auto p-6 flex items-center justify-center">
                            <div class="bg-white w-full max-w-md rounded-sm shadow-2xl" style="aspect-ratio: 8.5/11;">
                                <div class="p-8 h-full flex flex-col justify-start text-left">
                                    {{-- HEADER --}}
                                    <div class="border-b-2 border-gray-800 pb-4 mb-6">
                                        <h1 class="text-lg font-bold text-gray-900">SHAREHOLDER LEDGER</h1>
                                        <p class="text-xs text-gray-600 mt-2">John Kelly & Company</p>
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="space-y-4 text-xs">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="font-semibold text-gray-700">Full Name:</p>
                                                <p x-text="selectedShareholder.fullName" class="text-gray-900"></p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-700">TIN:</p>
                                                <p x-text="selectedShareholder.tin" class="text-gray-900 font-mono"></p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-700">Nationality:</p>
                                                <p x-text="selectedShareholder.nationality" class="text-gray-900"></p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-700">Shares:</p>
                                                <p x-text="selectedShareholder.shares" class="text-gray-900 font-bold"></p>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-400 pt-3">
                                            <p class="font-semibold text-gray-700">Address:</p>
                                            <p x-text="selectedShareholder.address" class="text-gray-900 mt-1"></p>
                                        </div>

                                        <div class="border-t border-gray-400 pt-3">
                                            <p class="font-semibold text-gray-700">Certificate No.:</p>
                                            <p x-text="selectedShareholder.certificateNo" class="text-gray-900 font-bold"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAILS SIDE --}}
                    <div class="col-span-1 overflow-y-auto space-y-4">

                        {{-- PERSONAL INFORMATION --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Personal Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Full Name</p>
                                    <p x-text="selectedShareholder.fullName" class="text-base font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">First Name</p>
                                    <p x-text="selectedShareholder.firstName" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Middle Name</p>
                                    <p x-text="selectedShareholder.middleName" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Family Name</p>
                                    <p x-text="selectedShareholder.familyName" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Nationality</p>
                                    <p x-text="selectedShareholder.nationality" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">TIN</p>
                                    <p x-text="selectedShareholder.tin" class="text-sm text-gray-900 mt-1 font-mono"></p>
                                </div>
                            </div>
                        </div>

                        {{-- CONTACT INFORMATION --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Contact Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Email</p>
                                    <p x-text="selectedShareholder.email" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Phone</p>
                                    <p x-text="selectedShareholder.phone" class="text-sm text-gray-900 mt-1 font-mono"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Address</p>
                                    <p x-text="selectedShareholder.address" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                            </div>
                        </div>

                        {{-- SHAREHOLDING INFORMATION --}}
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Shareholding Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Number of Shares</p>
                                    <p x-text="selectedShareholder.shares" class="text-2xl font-bold text-green-600 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Certificate No.</p>
                                    <p x-text="selectedShareholder.certificateNo" class="text-lg font-bold text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date Registered</p>
                                    <p x-text="selectedShareholder.dateRegistered" class="text-sm text-gray-900 mt-1"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</p>
                                    <span x-text="selectedShareholder.status" class="inline-block mt-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"></span>
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="space-y-2 pt-2">
                            <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-edit"></i>
                                Edit Shareholder
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-download"></i>
                                Download
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>

    {{-- ADD SHAREHOLDER SLIDER --}}
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
                <div class="text-lg font-semibold">Add Shareholder</div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Family Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter family name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">First Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter first name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Middle Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter middle name">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Nationality</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter nationality">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-600">Current Residential Address</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter address">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">TIN</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Enter TIN">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Email</label>
                        <input type="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="name@company.com">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Phone</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="+63 2 1234 5678">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Number of Shares</label>
                        <input type="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="1000">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Certificate No.</label>
                        <input type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="CERT-0001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Registered</label>
                        <input type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Status</label>
                        <select class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false">
                    Cancel
                </button>
                <div class="flex-1"></div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save Shareholder
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
