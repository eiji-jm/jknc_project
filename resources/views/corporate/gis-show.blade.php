@extends('layouts.app')

@section('content')

@php
    $authorizedTotalShares = $gis->authorizedCapital->sum('number_of_shares');
    $authorizedTotalAmount = $gis->authorizedCapital->sum('amount');

    $subscribedTotalStockholders = $gis->subscribedCapital->sum('no_of_stockholders');
    $subscribedTotalShares = $gis->subscribedCapital->sum('number_of_shares');
    $subscribedTotalAmount = $gis->subscribedCapital->sum('amount');
    $subscribedTotalOwnership = $gis->subscribedCapital->sum('ownership_percentage');

    $paidupTotalStockholders = $gis->paidUpCapital->sum('no_of_stockholders');
    $paidupTotalShares = $gis->paidUpCapital->sum('number_of_shares');
    $paidupTotalAmount = $gis->paidUpCapital->sum('amount');
    $paidupTotalOwnership = $gis->paidUpCapital->sum('ownership_percentage');

    $stockholderTotalShares = $gis->stockholders->sum('shares');
    $stockholderTotalAmount = $gis->stockholders->sum('amount');
    $stockholderTotalOwnership = $gis->stockholders->sum('ownership_percentage');
    $stockholderTotalPaid = $gis->stockholders->sum('amount_paid');

    $uboTotalOwnership = $gis->ubos->sum('ownership_voting_rights');

    $draftUrl = !empty($gis->file) ? asset('storage/' . ltrim($gis->file, '/')) : null;
    $notaryUrl = !empty($gis->notary_file_path) ? asset('storage/' . ltrim($gis->notary_file_path, '/')) : null;
    $canEditRecord = in_array($gis->workflow_status, ['Uploaded', 'Reverted']);
@endphp

<div class="w-full px-6 py-6"
     x-data="{
        tab:null,
        panel:null,
        fileTab: '{{ $draftUrl ? 'draft' : ($notaryUrl ? 'notary' : 'draft') }}',
        editDraft: false,
        editNotary: false
     }">

    <h1 class="text-2xl font-semibold mb-6">
        General Information Sheet (GIS)
    </h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

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

        <button
            @click="tab = tab === 'ubo' ? null : 'ubo'"
            :class="tab=='ubo' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="pb-2">
            UBO
        </button>
    </div>

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
                                <td class="border px-4 py-3">{{ number_format($row->number_of_shares, 0) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->par_value, 2) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="border px-4 py-3 text-right">TOTAL</td>
                            <td class="border px-4 py-3">{{ number_format($authorizedTotalShares, 0) }}</td>
                            <td class="border px-4 py-3">—</td>
                            <td class="border px-4 py-3">{{ number_format($authorizedTotalAmount, 2) }}</td>
                        </tr>
                    </tfoot>
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
                                <td class="border px-4 py-3">{{ number_format($row->no_of_stockholders, 0) }}</td>
                                <td class="border px-4 py-3">{{ $row->share_type }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->number_of_shares, 0) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->par_value, 2) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->amount, 2) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->ownership_percentage, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="border px-4 py-3 text-right">TOTAL</td>
                            <td class="border px-4 py-3">{{ number_format($subscribedTotalStockholders, 0) }}</td>
                            <td class="border px-4 py-3">—</td>
                            <td class="border px-4 py-3">{{ number_format($subscribedTotalShares, 0) }}</td>
                            <td class="border px-4 py-3">—</td>
                            <td class="border px-4 py-3">{{ number_format($subscribedTotalAmount, 2) }}</td>
                            <td class="border px-4 py-3">{{ number_format($subscribedTotalOwnership, 2) }}%</td>
                        </tr>
                    </tfoot>
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
                                <td class="border px-4 py-3">{{ number_format($row->no_of_stockholders, 0) }}</td>
                                <td class="border px-4 py-3">{{ $row->share_type }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->number_of_shares, 0) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->par_value, 2) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->amount, 2) }}</td>
                                <td class="border px-4 py-3">{{ number_format($row->ownership_percentage, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="border px-4 py-3 text-right">TOTAL</td>
                            <td class="border px-4 py-3">{{ number_format($paidupTotalStockholders, 0) }}</td>
                            <td class="border px-4 py-3">—</td>
                            <td class="border px-4 py-3">{{ number_format($paidupTotalShares, 0) }}</td>
                            <td class="border px-4 py-3">—</td>
                            <td class="border px-4 py-3">{{ number_format($paidupTotalAmount, 2) }}</td>
                            <td class="border px-4 py-3">{{ number_format($paidupTotalOwnership, 2) }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

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
                        <th class="border px-4 py-3">Nationality</th>
                        <th class="border px-4 py-3">INCR</th>
                        <th class="border px-4 py-3">Board</th>
                        <th class="border px-4 py-3">Gender</th>
                        <th class="border px-4 py-3">Stockholder</th>
                        <th class="border px-4 py-3">Officer</th>
                        <th class="border px-4 py-3">Exec. Comm.</th>
                        <th class="border px-4 py-3">TIN</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gis->directors as $row)
                        <tr class="hover:bg-blue-50">
                            <td class="border px-4 py-3">{{ $row->officer_name }}</td>
                            <td class="border px-4 py-3">{{ $row->address }}</td>
                            <td class="border px-4 py-3">{{ $row->nationality }}</td>
                            <td class="border px-4 py-3">{{ $row->incr ? 'Y' : 'N' }}</td>
                            <td class="border px-4 py-3">{{ $row->board }}</td>
                            <td class="border px-4 py-3">{{ $row->gender }}</td>
                            <td class="border px-4 py-3">{{ $row->stockholder ? 'Y' : 'N' }}</td>
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
                            <td class="border px-4 py-3">{{ $row->incr ? 'Y' : 'N' }}</td>
                            <td class="border px-4 py-3">{{ $row->share_type }}</td>
                            <td class="border px-4 py-3">{{ number_format($row->shares, 0) }}</td>
                            <td class="border px-4 py-3">{{ number_format($row->amount, 2) }}</td>
                            <td class="border px-4 py-3">{{ number_format($row->ownership_percentage, 2) }}%</td>
                            <td class="border px-4 py-3">{{ number_format($row->amount_paid, 2) }}</td>
                            <td class="border px-4 py-3">{{ $row->tin }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="6" class="border px-4 py-3 text-right">TOTAL</td>
                        <td class="border px-4 py-3">{{ number_format($stockholderTotalShares, 0) }}</td>
                        <td class="border px-4 py-3">{{ number_format($stockholderTotalAmount, 2) }}</td>
                        <td class="border px-4 py-3">{{ number_format($stockholderTotalOwnership, 2) }}%</td>
                        <td class="border px-4 py-3">{{ number_format($stockholderTotalPaid, 2) }}</td>
                        <td class="border px-4 py-3">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div x-show="tab=='ubo'" class="bg-white border rounded-lg mb-10">
        <div class="flex justify-between px-6 py-4 border-b">
            <h3 class="text-sm font-semibold uppercase text-gray-800">
                Ultimate Beneficial Owners
            </h3>

            <button
                @click="panel='ubo'"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Add
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-300">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="border px-4 py-3">Complete Name</th>
                        <th class="border px-4 py-3">Specific Residential Address</th>
                        <th class="border px-4 py-3">Nationality</th>
                        <th class="border px-4 py-3">Date of Birth</th>
                        <th class="border px-4 py-3">Tax Identification No.</th>
                        <th class="border px-4 py-3">% Ownership / % Voting Rights</th>
                        <th class="border px-4 py-3">Type of Beneficial Owner</th>
                        <th class="border px-4 py-3">Category of Beneficial Ownership</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gis->ubos as $row)
                        <tr class="hover:bg-blue-50">
                            <td class="border px-4 py-3">{{ $row->complete_name }}</td>
                            <td class="border px-4 py-3">{{ $row->specific_residential_address ?: '—' }}</td>
                            <td class="border px-4 py-3">{{ $row->nationality ?: '—' }}</td>
                            <td class="border px-4 py-3">
                                {{ $row->date_of_birth ? $row->date_of_birth->format('M d, Y') : '—' }}
                            </td>
                            <td class="border px-4 py-3">{{ $row->tax_identification_no ?: '—' }}</td>
                            <td class="border px-4 py-3">
                                {{ $row->ownership_voting_rights !== null ? number_format($row->ownership_voting_rights, 2) . '%' : '—' }}
                            </td>
                            <td class="border px-4 py-3">
                                {{ $row->beneficial_owner_type === 'D' ? 'Direct (D)' : ($row->beneficial_owner_type === 'I' ? 'Indirect (I)' : '—') }}
                            </td>
                            <td class="border px-4 py-3">{{ $row->beneficial_ownership_category ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border px-4 py-6 text-center text-gray-400">No records found</td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="5" class="border px-4 py-3 text-right">TOTAL</td>
                        <td class="border px-4 py-3">{{ number_format($uboTotalOwnership, 2) }}%</td>
                        <td colspan="2" class="border px-4 py-3">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

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

                <select name="share_type" class="border w-full p-2 rounded" required>
                    <option value="">Type of Shares</option>
                    <option value="Common Stock">Common Stock</option>
                    <option value="Preferred Stock">Preferred Stock</option>
                </select>

                <input name="number_of_shares" type="number" min="1" step="1" placeholder="Number of Shares" class="border w-full p-2 rounded" required>
                <input name="par_value" type="number" min="0" step="0.01" placeholder="Par / Stated Value" class="border w-full p-2 rounded" required>
                <input name="amount" type="number" min="0" step="0.01" placeholder="Amount" class="border w-full p-2 rounded" required>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='subscribed'" action="{{ route('subscribed.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">

                <select name="nationality" class="border w-full p-2 rounded" required>
                    <option value="">Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Foreign">Foreign</option>
                    <option value="N.A.">N.A.</option>
                </select>

                <input name="stockholders" type="number" min="0" step="1" placeholder="No. of Stockholders" class="border w-full p-2 rounded" required>

                <select name="share_type" class="border w-full p-2 rounded" required>
                    <option value="">Type of Shares</option>
                    <option value="Common Stock">Common Stock</option>
                    <option value="Preferred Stock">Preferred Stock</option>
                </select>

                <input name="shares" type="number" min="0" step="1" placeholder="Number of Shares" class="border w-full p-2 rounded" required>
                <input name="par_value" type="number" min="0" step="0.01" placeholder="Par Value" class="border w-full p-2 rounded" required>
                <input name="amount" type="number" min="0" step="0.01" placeholder="Amount" class="border w-full p-2 rounded" required>
                <input name="ownership" type="number" min="0" step="0.01" placeholder="% Ownership" class="border w-full p-2 rounded" required>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='paidup'" action="{{ route('paidup.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">

                <select name="nationality" class="border w-full p-2 rounded" required>
                    <option value="">Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Foreign">Foreign</option>
                    <option value="N.A.">N.A.</option>
                </select>

                <input name="stockholders" type="number" min="0" step="1" placeholder="No. of Stockholders" class="border w-full p-2 rounded" required>

                <select name="share_type" class="border w-full p-2 rounded" required>
                    <option value="">Type of Shares</option>
                    <option value="Common Stock">Common Stock</option>
                    <option value="Preferred Stock">Preferred Stock</option>
                </select>

                <input name="shares" type="number" min="0" step="1" placeholder="Number of Shares" class="border w-full p-2 rounded" required>
                <input name="par_value" type="number" min="0" step="0.01" placeholder="Par Value" class="border w-full p-2 rounded" required>
                <input name="amount" type="number" min="0" step="0.01" placeholder="Amount" class="border w-full p-2 rounded" required>
                <input name="ownership" type="number" min="0" step="0.01" placeholder="% Ownership" class="border w-full p-2 rounded" required>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='director'" action="{{ route('director.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">

                <input name="officer_name" placeholder="Officer Name" class="border w-full p-2 rounded" required>
                <input name="address" placeholder="Address" class="border w-full p-2 rounded" required>

                <select name="nationality" class="border w-full p-2 rounded" required>
                    <option value="">Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Foreign">Foreign</option>
                </select>

                <select name="incr" class="border w-full p-2 rounded" required>
                    <option value="">INCR</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                </select>

                <select name="board" class="border w-full p-2 rounded" required>
                    <option value="">Board</option>
                    <option value="C">C</option>
                    <option value="M">M</option>
                </select>

                <select name="gender" class="border w-full p-2 rounded" required>
                    <option value="">Gender</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>

                <select name="stockholder" class="border w-full p-2 rounded" required>
                    <option value="">Stockholder</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                </select>

                <input name="officer_type" placeholder="Officer / Position" class="border w-full p-2 rounded" required>
                <input name="committee" placeholder="Exec. Comm. / Committee" class="border w-full p-2 rounded" required>
                <input name="tin" placeholder="TIN" class="border w-full p-2 rounded" required>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='stockholder'" action="{{ route('stockholder.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">

                <input name="stockholder_name" placeholder="Stockholder Name" class="border w-full p-2 rounded" required>
                <input name="address" placeholder="Address" class="border w-full p-2 rounded" required>

                <select name="gender" class="border w-full p-2 rounded" required>
                    <option value="">Gender</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>

                <select name="nationality" class="border w-full p-2 rounded" required>
                    <option value="">Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Foreign">Foreign</option>
                </select>

                <select name="incr" class="border w-full p-2 rounded" required>
                    <option value="">INCR</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                </select>

                <select name="share_type" class="border w-full p-2 rounded" required>
                    <option value="">Type of Shares</option>
                    <option value="Common Stock">Common Stock</option>
                    <option value="Preferred Stock">Preferred Stock</option>
                </select>

                <input name="shares" type="number" min="0" step="1" placeholder="Shares" class="border w-full p-2 rounded" required>
                <input name="amount" type="number" min="0" step="0.01" placeholder="Amount" class="border w-full p-2 rounded" required>
                <input name="ownership_percentage" type="number" min="0" step="0.01" placeholder="% Ownership" class="border w-full p-2 rounded" required>
                <input name="amount_paid" type="number" min="0" step="0.01" placeholder="Amount Paid" class="border w-full p-2 rounded" required>
                <input name="tin" placeholder="TIN" class="border w-full p-2 rounded" required>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='ubo'" action="{{ route('ubo.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="gis_id" value="{{ $gis->id }}">

                <input name="complete_name" placeholder="Complete Name" class="border w-full p-2 rounded" required>
                <input name="specific_residential_address" placeholder="Specific Residential Address" class="border w-full p-2 rounded">

                <select name="nationality" class="border w-full p-2 rounded">
                    <option value="">Nationality</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Foreign">Foreign</option>
                </select>

                <input name="date_of_birth" type="date" class="border w-full p-2 rounded">
                <input name="tax_identification_no" placeholder="Tax Identification No." class="border w-full p-2 rounded">
                <input name="ownership_voting_rights" type="number" min="0" max="100" step="0.01" placeholder="% Ownership / % Voting Rights" class="border w-full p-2 rounded">

                <select name="beneficial_owner_type" class="border w-full p-2 rounded">
                    <option value="">Type of Beneficial Owner</option>
                    <option value="D">Direct (D)</option>
                    <option value="I">Indirect (I)</option>
                </select>

                <select name="beneficial_ownership_category" class="border w-full p-2 rounded">
                    <option value="">Category of Beneficial Ownership</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                    <option value="H">H</option>
                    <option value="I">I</option>
                </select>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </form>

            <form x-show="panel=='completegis'" action="{{ route('gis.company.update', $gis->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <h3 class="text-sm font-semibold text-gray-700 uppercase">Complete / Edit GIS Information</h3>

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

    <div class="grid grid-cols-3 gap-6">

        <div class="col-span-2 bg-white border rounded-lg p-4">

            <div class="flex items-center gap-6 border-b border-gray-200 mb-4">
                <button
                    @click="fileTab = 'draft'"
                    :class="fileTab === 'draft' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Draft
                </button>

                <button
                    @click="fileTab = 'notary'"
                    :class="fileTab === 'notary' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-600'"
                    class="pb-3 text-sm">
                    Notary
                </button>
            </div>

            <div x-show="fileTab === 'draft'">
                @if($draftUrl)
                    <iframe src="{{ $draftUrl }}" class="w-full h-[700px] border rounded"></iframe>
                @else
                    <div class="text-gray-400 text-center p-20 border rounded bg-gray-50">
                        No draft GIS file uploaded
                    </div>
                @endif
            </div>

            <div x-show="fileTab === 'notary'" x-cloak>
                @if($notaryUrl)
                    <iframe src="{{ $notaryUrl }}" class="w-full h-[700px] border rounded"></iframe>
                @else
                    <div class="text-gray-400 text-center p-20 border rounded bg-gray-50">
                        No notary GIS file uploaded
                    </div>
                @endif
            </div>

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

            <div class="flex justify-between">
                <span class="text-gray-500">Workflow Status</span>
                <span>{{ $gis->workflow_status ?? 'Uploaded' }}</span>
            </div>

            <hr>

            <div class="pt-1 border-t-0">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Draft File
                    </h3>

                    @if($canEditRecord && $draftUrl)
                        <button
                            type="button"
                            @click="editDraft = !editDraft"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editDraft ? 'Cancel' : 'Edit'"></span>
                        </button>
                    @endif
                </div>

                @if($draftUrl)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Draft file attached</p>
                            <p class="text-xs text-gray-400">{{ basename($gis->file) }}</p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'draft'"
                            class="text-xs px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            View
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No draft file attached yet.
                    </div>
                @endif

                @if($canEditRecord)
                    <form x-show="editDraft || !{{ $draftUrl ? 'true' : 'false' }}" x-cloak
                          action="{{ route('corporate.gis.upload.draft', $gis->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf

                        <input type="file" name="draft_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                            Save Draft File
                        </button>
                    </form>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">
                        Notary File
                    </h3>

                    @if($canEditRecord && $notaryUrl)
                        <button
                            type="button"
                            @click="editNotary = !editNotary"
                            class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            <span x-text="editNotary ? 'Cancel' : 'Edit'"></span>
                        </button>
                    @endif
                </div>

                @if($notaryUrl)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Notary file attached</p>
                            <p class="text-xs text-gray-400">{{ basename($gis->notary_file_path) }}</p>
                        </div>

                        <button
                            type="button"
                            @click="fileTab = 'notary'"
                            class="text-xs px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700">
                            View
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-400 mb-3">
                        No notary file attached yet.
                    </div>
                @endif

                @if($canEditRecord)
                    <form x-show="editNotary || !{{ $notaryUrl ? 'true' : 'false' }}" x-cloak
                          action="{{ route('corporate.gis.upload.notary', $gis->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf

                        <input type="file" name="notary_file" class="w-full border rounded p-2" required>

                        <button type="submit"
                                class="block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                            Save Notary File
                        </button>
                    </form>
                @endif
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
                <div class="w-full bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm text-center mb-3">
                    GIS Information already completed
                </div>

                <button
                    @click="panel='completegis'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm w-full">
                    Edit GIS Information
                </button>
            @endif
        </div>

    </div>

</div>

@endsection
