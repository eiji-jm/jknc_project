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

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold mb-1">Please check the form fields.</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">IDA Records</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Incentive Distribution & Allocation records.
                    </p>
                </div>

                @if(auth()->user()->hasPermission('create_sales_marketing'))
                    <button
                        type="button"
                        @click="openAdd = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition"
                    >
                        <i class="fas fa-plus"></i>
                        Add IDA
                    </button>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">IDA List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-3 font-semibold">Condeal Ref No.</th>
                            <th class="px-6 py-3 font-semibold">Client Name</th>
                            <th class="px-6 py-3 font-semibold">Business Name</th>
                            <th class="px-6 py-3 font-semibold">Service Area</th>
                            <th class="px-6 py-3 font-semibold">Engagement Structure</th>
                            <th class="px-6 py-3 font-semibold">Deal Value</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($idas as $ida)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $ida->condeal_ref_no ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $ida->client_name ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $ida->business_name ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $ida->service_area ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $ida->product_engagement_structure ?: '—' }}</td>
                                <td class="px-6 py-4">₱ {{ number_format((float) $ida->deal_value, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $ida->workflow_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('sales-marketing.ida.show', $ida) }}"
                                           class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                                    No IDA records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display:none;">
        <div @click.outside="openAdd = false" class="w-full max-w-7xl max-h-[95vh] overflow-y-auto bg-white rounded-2xl shadow-xl border border-gray-200">
            <form action="{{ route('sales-marketing.ida.store') }}" method="POST">
                @csrf

                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                    <h2 class="text-lg font-semibold text-gray-900">Add IDA Record</h2>
                    <button type="button" @click="openAdd = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-8">
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Deal Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Condeal Ref No.</label>
                                <select
                                    name="deal_id"
                                    x-model="selectedDealId"
                                    @change="fillDealInfo()"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select Deal</option>
                                    @foreach($deals as $deal)
                                        <option value="{{ $deal['id'] }}">
                                            {{ $deal['deal_code'] }}{{ $deal['business_name'] ? ' - '.$deal['business_name'] : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Condeal Ref No. (Text)</label>
                                <input type="text" name="condeal_ref_no" x-model="form.condeal_ref_no" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Name</label>
                                <input type="text" name="client_name" x-model="form.client_name" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                <input type="text" name="business_name" x-model="form.business_name" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service Area</label>
                                <input type="text" name="service_area" x-model="form.service_area" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Engagement Structure</label>
                                <input type="text" name="product_engagement_structure" x-model="form.product_engagement_structure" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deal Value</label>
                                <input type="number" step="0.01" name="deal_value" x-model="form.deal_value" readonly class="w-full rounded-xl border-gray-300 bg-gray-100">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Allocation Table</h3>

                            <button
                                type="button"
                                @click="addAllocation()"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700"
                            >
                                <i class="fas fa-plus"></i>
                                Add Row
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-gray-600">
                                        <th class="px-4 py-3 font-semibold">Earner</th>
                                        <th class="px-4 py-3 font-semibold">Role</th>
                                        <th class="px-4 py-3 font-semibold">Commission Category</th>
                                        <th class="px-4 py-3 font-semibold">Commission Type</th>
                                        <th class="px-4 py-3 font-semibold">Commission Rate</th>
                                        <th class="px-4 py-3 font-semibold">Commission Amount</th>
                                        <th class="px-4 py-3 font-semibold">Status</th>
                                        <th class="px-4 py-3 font-semibold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(allocation, index) in allocations" :key="index">
                                        <tr class="border-t border-gray-100">
                                            <td class="px-4 py-3">
                                                <select :name="`allocations[${index}][earner_id]`" class="w-48 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Select Earner</option>
                                                    @foreach($earners as $earner)
                                                        <option value="{{ $earner->id }}">{{ $earner->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td class="px-4 py-3">
                                                <select :name="`allocations[${index}][role]`" class="w-40 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Select Role</option>
                                                    <option value="Associate Consultant">Associate Consultant</option>
                                                    <option value="Consultant">Consultant</option>
                                                    <option value="Lead Consultant">Lead Consultant</option>
                                                    <option value="Referrer">Referrer</option>
                                                </select>
                                            </td>

                                            <td class="px-4 py-3">
                                                <select :name="`allocations[${index}][commission_category]`" class="w-44 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Select Category</option>
                                                    <option value="Referral">Referral</option>
                                                    <option value="Incentive">Incentive</option>
                                                    <option value="Closing">Closing</option>
                                                </select>
                                            </td>

                                            <td class="px-4 py-3">
                                                <select :name="`allocations[${index}][commission_type]`" class="w-40 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Select Type</option>
                                                    <option value="Percentage">Percentage</option>
                                                    <option value="Fixed">Fixed</option>
                                                </select>
                                            </td>

                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" :name="`allocations[${index}][commission_rate]`" class="w-32 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            </td>

                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" :name="`allocations[${index}][commission_amount]`" class="w-36 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            </td>

                                            <td class="px-4 py-3">
                                                <select :name="`allocations[${index}][status]`" class="w-36 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="Pending">Pending</option>
                                                    <option value="Ready for Payout">Ready for Payout</option>
                                                    <option value="Paid">Paid</option>
                                                </select>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    @click="removeAllocation(index)"
                                                    class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                        Save IDA
                    </button>
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
                service_area: '',
                product_engagement_structure: '',
                deal_value: '',
            },
            allocations: [
                {
                    earner_id: '',
                    role: '',
                    commission_category: '',
                    commission_type: '',
                    commission_rate: '',
                    commission_amount: '',
                    status: 'Pending',
                }
            ],

            fillDealInfo() {
                const deal = this.deals.find(d => String(d.id) === String(this.selectedDealId));

                if (!deal) {
                    this.form = {
                        condeal_ref_no: '',
                        client_name: '',
                        business_name: '',
                        service_area: '',
                        product_engagement_structure: '',
                        deal_value: '',
                    };
                    return;
                }

                this.form.condeal_ref_no = deal.deal_code ?? '';
                this.form.client_name = deal.client_name ?? '';
                this.form.business_name = deal.business_name ?? '';
                this.form.service_area = deal.service_area ?? '';
                this.form.product_engagement_structure = deal.product_engagement_structure ?? '';
                this.form.deal_value = deal.deal_value ?? '';
            },

            addAllocation() {
                this.allocations.push({
                    earner_id: '',
                    role: '',
                    commission_category: '',
                    commission_type: '',
                    commission_rate: '',
                    commission_amount: '',
                    status: 'Pending',
                });
            },

            removeAllocation(index) {
                if (this.allocations.length === 1) return;
                this.allocations.splice(index, 1);
            }
        };
    }
</script>
@endpush