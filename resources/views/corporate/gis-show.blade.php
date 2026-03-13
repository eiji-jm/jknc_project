@extends('layouts.app')

@section('content')

<div class="w-full px-6 py-6" x-data="{ tab:null, panel:null }">

    <h1 class="text-2xl font-semibold mb-6">
        General Information Sheet (GIS)
    </h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABS -->
    <div class="flex gap-6 border-b text-sm font-medium mb-6">
        <button
            @click="tab = tab === 'capital' ? null : 'capital'"
            :class="tab=='capital' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="pb-2">
            Capital Structure
        </button>

        <button
            @click="tab = tab === 'directors' ? null : 'directors'"
            :class="tab=='directors' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="pb-2">
            Board Of Directors / Officers
        </button>

        <button
            @click="tab = tab === 'stockholders' ? null : 'stockholders'"
            :class="tab=='stockholders' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="pb-2">
            Stockholders
        </button>
    </div>

    <!-- CAPITAL STRUCTURE -->
    <div x-show="tab=='capital'" class="space-y-8 mb-10">

        <div class="bg-white border rounded-lg">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-sm font-semibold uppercase text-gray-800">
                    Authorized Capital Stock
                </h3>

                <button
                    @click="panel='authorized'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Add
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="border px-4 py-3 text-left">Type of Shares</th>
                            <th class="border px-4 py-3 text-left">Number of Shares</th>
                            <th class="border px-4 py-3 text-left">Par/Stated Value</th>
                            <th class="border px-4 py-3 text-left">Amount (Php)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gis->authorizedCapital as $row)
                            <tr class="hover:bg-blue-50">
                                <td class="border px-4 py-3">{{ $row->share_type }}</td>
                                <td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
                                <td class="border px-4 py-3">{{ $row->par_value }}</td>
                                <td class="border px-4 py-3">{{ $row->amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border rounded-lg">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-sm font-semibold uppercase text-gray-800">
                    Subscribed Capital
                </h3>

                <button
                    @click="panel='subscribed'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Add
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="border px-4 py-3">Nationality</th>
                            <th class="border px-4 py-3">No. of Stockholders</th>
                            <th class="border px-4 py-3">Type of Shares</th>
                            <th class="border px-4 py-3">Number of Shares</th>
                            <th class="border px-4 py-3">Par Value</th>
                            <th class="border px-4 py-3">Amount (Php)</th>
                            <th class="border px-4 py-3">% Ownership</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gis->subscribedCapital as $row)
                            <tr class="hover:bg-blue-50">
                                <td class="border px-4 py-3">{{ $row->nationality }}</td>
                                <td class="border px-4 py-3">{{ $row->no_of_stockholders }}</td>
                                <td class="border px-4 py-3">{{ $row->share_type }}</td>
                                <td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
                                <td class="border px-4 py-3">{{ $row->par_value }}</td>
                                <td class="border px-4 py-3">{{ $row->amount }}</td>
                                <td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border rounded-lg">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-sm font-semibold uppercase text-gray-800">
                    Paid-Up Capital
                </h3>

                <button
                    @click="panel='paidup'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Add
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="border px-4 py-3">Nationality</th>
                            <th class="border px-4 py-3">No. of Stockholders</th>
                            <th class="border px-4 py-3">Type of Shares</th>
                            <th class="border px-4 py-3">Number of Shares</th>
                            <th class="border px-4 py-3">Par Value</th>
                            <th class="border px-4 py-3">Amount (Php)</th>
                            <th class="border px-4 py-3">% Ownership</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gis->paidUpCapital as $row)
                            <tr class="hover:bg-blue-50">
                                <td class="border px-4 py-3">{{ $row->nationality }}</td>
                                <td class="border px-4 py-3">{{ $row->no_of_stockholders }}</td>
                                <td class="border px-4 py-3">{{ $row->share_type }}</td>
                                <td class="border px-4 py-3">{{ $row->number_of_shares }}</td>
                                <td class="border px-4 py-3">{{ $row->par_value }}</td>
                                <td class="border px-4 py-3">{{ $row->amount }}</td>
                                <td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DIRECTORS -->
    <div x-show="tab=='directors'" class="bg-white border rounded-lg mb-10">
        <div class="flex justify-between px-6 py-4 border-b">
            <h3 class="text-sm font-semibold uppercase text-gray-800">
                Board of Directors / Officers
            </h3>

            <button
                @click="panel='director'"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Add
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-300">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="border px-4 py-3">Officer Name</th>
                        <th class="border px-4 py-3">Address</th>
                        <th class="border px-4 py-3">Gender</th>
                        <th class="border px-4 py-3">Nationality</th>
                        <th class="border px-4 py-3">INCR</th>
                        <th class="border px-4 py-3">Stockholder</th>
                        <th class="border px-4 py-3">Board</th>
                        <th class="border px-4 py-3">Type of Officer</th>
                        <th class="border px-4 py-3">Committee</th>
                        <th class="border px-4 py-3">TIN</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gis->directors as $row)
                        <tr class="hover:bg-blue-50">
                            <td class="border px-4 py-3">{{ $row->officer_name }}</td>
                            <td class="border px-4 py-3">{{ $row->address }}</td>
                            <td class="border px-4 py-3">{{ $row->gender }}</td>
                            <td class="border px-4 py-3">{{ $row->nationality }}</td>
                            <td class="border px-4 py-3">{{ $row->incr }}</td>
                            <td class="border px-4 py-3">{{ $row->stockholder }}</td>
                            <td class="border px-4 py-3">{{ $row->board }}</td>
                            <td class="border px-4 py-3">{{ $row->officer_type }}</td>
                            <td class="border px-4 py-3">{{ $row->committee }}</td>
                            <td class="border px-4 py-3">{{ $row->tin }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- STOCKHOLDERS -->
    <div x-show="tab=='stockholders'" class="bg-white border rounded-lg mb-10">
        <div class="flex justify-between px-6 py-4 border-b">
            <h3 class="text-sm font-semibold uppercase text-gray-800">
                Stockholders
            </h3>

            <button
                @click="panel='stockholder'"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Add
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-300">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="border px-4 py-3">Stockholder Name</th>
                        <th class="border px-4 py-3">Address</th>
                        <th class="border px-4 py-3">Gender</th>
                        <th class="border px-4 py-3">Nationality</th>
                        <th class="border px-4 py-3">INCR</th>
                        <th class="border px-4 py-3">Type</th>
                        <th class="border px-4 py-3">Shares</th>
                        <th class="border px-4 py-3">Amount (Php)</th>
                        <th class="border px-4 py-3">% Ownership</th>
                        <th class="border px-4 py-3">Amount Paid</th>
                        <th class="border px-4 py-3">TIN</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gis->stockholders as $row)
                        <tr class="hover:bg-blue-50">
                            <td class="border px-4 py-3">{{ $row->stockholder_name }}</td>
                            <td class="border px-4 py-3">{{ $row->address }}</td>
                            <td class="border px-4 py-3">{{ $row->gender }}</td>
                            <td class="border px-4 py-3">{{ $row->nationality }}</td>
                            <td class="border px-4 py-3">{{ $row->incr }}</td>
                            <td class="border px-4 py-3">{{ $row->share_type }}</td>
                            <td class="border px-4 py-3">{{ $row->shares }}</td>
                            <td class="border px-4 py-3">{{ $row->amount }}</td>
                            <td class="border px-4 py-3">{{ $row->ownership_percentage }}</td>
                            <td class="border px-4 py-3">{{ $row->amount_paid }}</td>
                            <td class="border px-4 py-3">{{ $row->tin }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SLIDE PANEL -->
    <div x-show="panel" class="fixed inset-0 bg-black/40 z-50" x-cloak>
        <div
            @click.away="panel=null"
            class="absolute right-0 top-0 h-full w-[460px] bg-white shadow-xl p-6 overflow-y-auto">

            <h2 class="text-lg font-semibold mb-6">
                Add Record
            </h2>

            <form x-show="panel=='authorized'" action="{{ route('authorized.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">
                <input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
                <input name="number_of_shares" placeholder="Shares" class="border w-full p-2 rounded">
                <input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
                <input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='subscribed'" action="{{ route('subscribed.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">
                <input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
                <input name="stockholders" placeholder="No. of Stockholders" class="border w-full p-2 rounded">
                <input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
                <input name="shares" placeholder="Shares" class="border w-full p-2 rounded">
                <input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
                <input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
                <input name="ownership" placeholder="% Ownership" class="border w-full p-2 rounded">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='paidup'" action="{{ route('paidup.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">
                <input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
                <input name="stockholders" placeholder="No. of Stockholders" class="border w-full p-2 rounded">
                <input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
                <input name="shares" placeholder="Shares" class="border w-full p-2 rounded">
                <input name="par_value" placeholder="Par Value" class="border w-full p-2 rounded">
                <input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
                <input name="ownership" placeholder="% Ownership" class="border w-full p-2 rounded">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='director'" action="{{ route('director.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">
                <input name="officer_name" placeholder="Officer Name" class="border w-full p-2 rounded">
                <input name="address" placeholder="Address" class="border w-full p-2 rounded">
                <input name="gender" placeholder="Gender" class="border w-full p-2 rounded">
                <input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
                <input name="incr" placeholder="INCR (1 or 0)" class="border w-full p-2 rounded">
                <input name="stockholder" placeholder="Stockholder (1 or 0)" class="border w-full p-2 rounded">
                <input name="board" placeholder="Board Role" class="border w-full p-2 rounded">
                <input name="officer_type" placeholder="Officer Type" class="border w-full p-2 rounded">
                <input name="committee" placeholder="Committee" class="border w-full p-2 rounded">
                <input name="tin" placeholder="TIN" class="border w-full p-2 rounded">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='stockholder'" action="{{ route('stockholder.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">
                <input name="stockholder_name" placeholder="Stockholder Name" class="border w-full p-2 rounded">
                <input name="address" placeholder="Address" class="border w-full p-2 rounded">
                <input name="gender" placeholder="Gender" class="border w-full p-2 rounded">
                <input name="nationality" placeholder="Nationality" class="border w-full p-2 rounded">
                <input name="incr" placeholder="INCR (1 or 0)" class="border w-full p-2 rounded">
                <input name="share_type" placeholder="Share Type" class="border w-full p-2 rounded">
                <input name="shares" placeholder="Shares" class="border w-full p-2 rounded">
                <input name="amount" placeholder="Amount" class="border w-full p-2 rounded">
                <input name="ownership_percentage" placeholder="% Ownership" class="border w-full p-2 rounded">
                <input name="amount_paid" placeholder="Amount Paid" class="border w-full p-2 rounded">
                <input name="tin" placeholder="TIN" class="border w-full p-2 rounded">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <!-- COMPLETE GIS INFORMATION -->
            <form x-show="panel=='completegis'" action="{{ route('gis.company.update', $gis->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <h3 class="text-sm font-semibold text-gray-700 uppercase">Complete GIS Information</h3>

                <input type="date" name="date_registered" class="border w-full p-2 rounded" value="{{ $gis->date_registered }}">
                <input name="trade_name" placeholder="Trade Name" class="border w-full p-2 rounded" value="{{ $gis->trade_name }}">
                <input name="fiscal_year_end" placeholder="Fiscal Year End" class="border w-full p-2 rounded" value="{{ $gis->fiscal_year_end }}">
                <input name="tin" placeholder="TIN" class="border w-full p-2 rounded" value="{{ $gis->tin }}">
                <input name="website" placeholder="Website" class="border w-full p-2 rounded" value="{{ $gis->website }}">
                <input type="email" name="email" placeholder="Email" class="border w-full p-2 rounded" value="{{ $gis->email }}">
                <input name="principal_address" placeholder="Principal Address" class="border w-full p-2 rounded" value="{{ $gis->principal_address }}">
                <input name="business_address" placeholder="Business Address" class="border w-full p-2 rounded" value="{{ $gis->business_address }}">
                <input name="official_mobile" placeholder="Official Mobile" class="border w-full p-2 rounded" value="{{ $gis->official_mobile }}">
                <input name="alternate_mobile" placeholder="Alternate Mobile" class="border w-full p-2 rounded" value="{{ $gis->alternate_mobile }}">
                <input name="auditor" placeholder="Auditor" class="border w-full p-2 rounded" value="{{ $gis->auditor }}">
                <input name="industry" placeholder="Industry" class="border w-full p-2 rounded" value="{{ $gis->industry }}">
                <input name="geo_code" placeholder="Geo Code" class="border w-full p-2 rounded" value="{{ $gis->geo_code }}">

                <div class="border-t pt-4 mt-2">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Intercompany Affiliations</h4>

                    <input name="parent_company_name" placeholder="Parent Company Name" class="border w-full p-2 rounded mb-3" value="{{ $gis->parent_company_name }}">
                    <input name="parent_company_sec_no" placeholder="Parent Company SEC Registration No." class="border w-full p-2 rounded mb-3" value="{{ $gis->parent_company_sec_no }}">
                    <input name="parent_company_address" placeholder="Parent Company Address" class="border w-full p-2 rounded mb-3" value="{{ $gis->parent_company_address }}">

                    <input name="subsidiary_name" placeholder="Subsidiary / Affiliate Name" class="border w-full p-2 rounded mb-3" value="{{ $gis->subsidiary_name }}">
                    <input name="subsidiary_sec_no" placeholder="Subsidiary / Affiliate SEC Registration No." class="border w-full p-2 rounded mb-3" value="{{ $gis->subsidiary_sec_no }}">
                    <input name="subsidiary_address" placeholder="Subsidiary / Affiliate Address" class="border w-full p-2 rounded" value="{{ $gis->subsidiary_address }}">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Save
                    </button>

                    <button type="button" @click="panel=null" class="border px-4 py-2 rounded">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 bg-white border rounded-lg p-4">
            @if($gis->file)
                <iframe src="{{ asset('storage/'.$gis->file) }}" class="w-full h-[700px] border rounded"></iframe>
            @else
                <div class="text-gray-400 text-center p-20">
                    No GIS file uploaded
                </div>
            @endif
        </div>

        <div class="bg-white border rounded-lg p-6 space-y-4 h-fit">
            <h2 class="text-lg font-semibold mb-4">
                GIS Information
            </h2>

            <div class="flex justify-between">
                <span class="text-gray-500">Corporation</span>
                <span>{{ $gis->corporation_name }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Company Reg No.</span>
                <span>{{ $gis->company_reg_no }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Uploaded By</span>
                <span>{{ $gis->uploaded_by }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Submission Status</span>
                <span>{{ $gis->submission_status }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Receive On</span>
                <span>{{ $gis->receive_on }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Period Date</span>
                <span>{{ $gis->period_date }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Annual Meeting</span>
                <span>{{ $gis->annual_meeting }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Meeting Type</span>
                <span>{{ $gis->meeting_type }}</span>
            </div>

            <hr>

            <h3 class="text-sm font-semibold text-gray-800 uppercase">
                Completed GIS Details
            </h3>

            <div class="flex justify-between">
                <span class="text-gray-500">Date Registered</span>
                <span>{{ $gis->date_registered ? \Carbon\Carbon::parse($gis->date_registered)->format('F d, Y') : '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Trade Name</span>
                <span>{{ $gis->trade_name ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Fiscal Year End</span>
                <span>{{ $gis->fiscal_year_end ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">TIN</span>
                <span>{{ $gis->tin ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Website</span>
                <span class="text-right break-all">{{ $gis->website ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Email</span>
                <span class="text-right break-all">{{ $gis->email ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Principal Address</span>
                <span class="text-right max-w-[180px]">{{ $gis->principal_address ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Business Address</span>
                <span class="text-right max-w-[180px]">{{ $gis->business_address ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Official Mobile</span>
                <span>{{ $gis->official_mobile ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Alternate Mobile</span>
                <span>{{ $gis->alternate_mobile ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Auditor</span>
                <span class="text-right max-w-[180px]">{{ $gis->auditor ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Industry</span>
                <span class="text-right max-w-[180px]">{{ $gis->industry ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Geo Code</span>
                <span>{{ $gis->geo_code ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Parent Company</span>
                <span class="text-right max-w-[180px]">{{ $gis->parent_company_name ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Parent SEC No.</span>
                <span>{{ $gis->parent_company_sec_no ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Parent Address</span>
                <span class="text-right max-w-[180px]">{{ $gis->parent_company_address ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Subsidiary / Affiliate</span>
                <span class="text-right max-w-[180px]">{{ $gis->subsidiary_name ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Subsidiary SEC No.</span>
                <span>{{ $gis->subsidiary_sec_no ?: '—' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Subsidiary Address</span>
                <span class="text-right max-w-[180px]">{{ $gis->subsidiary_address ?: '—' }}</span>
            </div>

            @if(!$gis->date_registered)
                <button
                    @click="panel='completegis'"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm mb-4 w-full">
                    Complete GIS Information
                </button>
            @else
                <div class="w-full bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm text-center">
                    GIS Information already completed
                </div>
            @endif
        </div>
    </div>

</div>

@endsection