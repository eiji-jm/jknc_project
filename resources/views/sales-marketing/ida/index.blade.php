@extends('layouts.app')

@section('title', 'Sales & Marketing | IDA Records')

@section('content')
<div class="flex-1 overflow-y-auto p-6" x-data="idaPage()">
    <div class="max-w-7xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- HEADER -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Transactions (IDA)</h1>
                <p class="text-sm text-gray-500">Commission distribution per earner</p>
            </div>

            <button
                type="button"
                @click="openAdd = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700"
            >
                + Add IDA
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white border rounded-2xl overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3">Condeal</th>
                        <th class="p-3">Client</th>
                        <th class="p-3">Business</th>
                        <th class="p-3">Deal Value</th>
                        <th class="p-3">Earner</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Rate</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($idas as $ida)
                    @foreach($ida->allocations as $allocation)
                        <tr class="border-t">
                            <td class="p-3">{{ $ida->condeal_ref_no }}</td>
                            <td class="p-3">{{ $ida->client_name }}</td>
                            <td class="p-3">{{ $ida->business_name }}</td>
                            <td class="p-3">₱ {{ number_format($ida->deal_value,2) }}</td>

                            <td class="p-3">{{ optional($allocation->earner)->full_name }}</td>
                            <td class="p-3">{{ $allocation->role }}</td>
                            <td class="p-3">{{ $allocation->commission_type }}</td>
                            <td class="p-3">{{ $allocation->commission_rate }}</td>
                            <td class="p-3">₱ {{ number_format($allocation->commission_amount,2) }}</td>
                            <td class="p-3">{{ $allocation->status }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="10" class="text-center p-6 text-gray-400">
                            No transactions yet
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div x-show="openAdd"
         x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 p-4">

        <div @click.outside="openAdd = false"
             class="bg-white w-full max-w-4xl rounded-2xl p-6">

            <h2 class="text-lg font-bold mb-4">Add IDA</h2>

            <form method="POST" action="{{ route('sales-marketing.ida.store') }}">
                @csrf

                <!-- DEAL SELECT -->
                <select x-model="selectedDealId" @change="fillDealInfo()" class="w-full border p-2 mb-3">
                    <option value="">Select Deal</option>
                    @foreach($deals as $deal)
                        <option value="{{ $deal['id'] }}">
                            {{ $deal['deal_code'] }} - {{ $deal['business_name'] }}
                        </option>
                    @endforeach
                </select>

                <input name="condeal_ref_no" x-model="form.condeal_ref_no" class="w-full border p-2 mb-2" placeholder="Condeal">
                <input name="client_name" x-model="form.client_name" class="w-full border p-2 mb-2" placeholder="Client">
                <input name="business_name" x-model="form.business_name" class="w-full border p-2 mb-2" placeholder="Business">
                <input name="deal_value" x-model="form.deal_value" class="w-full border p-2 mb-4" placeholder="Value">

                <!-- ALLOCATIONS -->
                <template x-for="(row, index) in allocations" :key="index">
                    <div class="border p-3 mb-3 rounded">
                        <select :name="`allocations[${index}][earner_id]`" class="w-full border p-2 mb-2">
                            <option value="">Select Earner</option>
                            @foreach($earners as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                            @endforeach
                        </select>

                        <input :name="`allocations[${index}][role]`" placeholder="Role" class="w-full border p-2 mb-2">

                        <select :name="`allocations[${index}][commission_type]`" class="w-full border p-2 mb-2">
                            <option value="Percentage">Percentage</option>
                            <option value="Fixed">Fixed</option>
                        </select>

                        <input :name="`allocations[${index}][commission_rate]`" placeholder="Rate" class="w-full border p-2 mb-2">
                        <input :name="`allocations[${index}][commission_amount]`" placeholder="Amount" class="w-full border p-2 mb-2">

                        <button type="button" @click="removeAllocation(index)" class="text-red-500 text-sm">Remove</button>
                    </div>
                </template>

                <button type="button" @click="addAllocation()" class="mb-4 bg-gray-200 px-3 py-1 rounded">
                    + Add Row
                </button>

                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" @click="openAdd=false" class="border px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function idaPage() {
    return {
        openAdd: false,
        selectedDealId: '',
        deals: @json($deals),

        form: {
            condeal_ref_no: '',
            client_name: '',
            business_name: '',
            deal_value: ''
        },

        allocations: [
            { earner_id:'', role:'', commission_type:'Percentage', commission_rate:'', commission_amount:'' }
        ],

        fillDealInfo() {
            let deal = this.deals.find(d => d.id == this.selectedDealId);
            if (!deal) return;

            this.form.condeal_ref_no = deal.deal_code;
            this.form.client_name = deal.client_name;
            this.form.business_name = deal.business_name;
            this.form.deal_value = deal.deal_value;
        },

        addAllocation() {
            this.allocations.push({
                earner_id:'', role:'', commission_type:'Percentage', commission_rate:'', commission_amount:''
            });
        },

        removeAllocation(index) {
            if (this.allocations.length > 1) {
                this.allocations.splice(index, 1);
            }
        }
    }
}
</script>
@endpush