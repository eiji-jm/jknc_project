@extends('layouts.app')

@section('title', 'Sales & Marketing | Commission Earners')

@section('content')
<div class="flex-1 overflow-y-auto p-6" x-data="earnersPage()">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Commission Earners</h1>
                <p class="text-sm text-gray-500">Master list of all commission earners.</p>
            </div>

            <button @click="openAdd = true"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                + Add Earner
            </button>
        </div>

        {{-- TABLE --}}
        <div class="bg-white border rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Master List</h2>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Name</th>
                        <th>Source</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($earners as $earner)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $earner->full_name }}</td>
                            <td>{{ ucfirst($earner->source_type) }}</td>
                            <td>{{ $earner->email }}</td>
                            <td>{{ $earner->mobile_number }}</td>
                            <td>{{ $earner->status }}</td>
                            <td class="text-center">
                                <a href="{{ route('sales-marketing.earners.show', $earner) }}"
                                   class="text-blue-600">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-6 text-gray-400">
                                No earners yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- ================= ADD MODAL ================= --}}
    <div x-show="openAdd" x-cloak
        class="fixed inset-0 bg-black/40 flex items-center justify-center">

        <div @click.outside="openAdd=false"
            class="bg-white w-full max-w-2xl rounded-2xl shadow-xl">

            <form method="POST" action="{{ route('sales-marketing.earners.store') }}">
                @csrf

                <div class="p-4 border-b font-semibold">
                    Add Commission Earner
                </div>

                <div class="p-6 grid grid-cols-2 gap-4">

                    {{-- SOURCE --}}
                    <div>
                        <label>Source Type</label>
                        <select name="source_type" x-model="form.source_type"
                            class="w-full border rounded-xl">
                            <option value="manual">Manual</option>
                            <option value="contact">Contact</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status" class="w-full border rounded-xl">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- CONTACT --}}
                    <template x-if="form.source_type === 'contact'">
                        <div class="col-span-2">
                            <label>Select Contact</label>
                            <select name="source_id" class="w-full border rounded-xl">
                                <option value="">Select</option>
                                @foreach(\App\Models\Contact::all() as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->first_name }} {{ $c->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </template>

                    {{-- EMPLOYEE --}}
                    <template x-if="form.source_type === 'employee'">
                        <div class="col-span-2">
                            <label>Select Employee</label>
                            <select name="source_id" class="w-full border rounded-xl">
                                <option value="">Select</option>
                                @foreach(\App\Models\User::all() as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </template>

                    {{-- MANUAL --}}
                    <template x-if="form.source_type === 'manual'">
                        <div class="col-span-2">
                            <label>Full Name</label>
                            <input type="text" name="full_name"
                                class="w-full border rounded-xl">
                        </div>
                    </template>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email"
                            class="w-full border rounded-xl">
                    </div>

                    <div>
                        <label>Mobile</label>
                        <input type="text" name="mobile_number"
                            class="w-full border rounded-xl">
                    </div>

                    <div>
                        <label>Bank</label>
                        <input type="text" name="bank_name"
                            class="w-full border rounded-xl">
                    </div>

                    <div>
                        <label>Account #</label>
                        <input type="text" name="account_number"
                            class="w-full border rounded-xl">
                    </div>

                </div>

                <div class="p-4 border-t flex justify-end gap-2">
                    <button type="button" @click="openAdd=false">Cancel</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-xl">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function earnersPage() {
    return {
        openAdd: false,
        form: {
            source_type: 'manual'
        }
    }
}
</script>
@endpush