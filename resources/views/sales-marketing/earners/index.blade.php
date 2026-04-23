@extends('layouts.app')

@section('title', 'Sales & Marketing | Commission Earners')

@section('content')
<div class="flex-1 overflow-y-auto p-6" x-data="earnersPage()">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Commission Earners</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Master list of all Sales & Marketing commission earners.
                    </p>
                </div>

                @if(auth()->user()->hasPermission('create_sales_marketing'))
                    <button
                        type="button"
                        @click="openAdd = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition"
                    >
                        <i class="fas fa-plus"></i>
                        Add Earner
                    </button>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Master List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-6 py-3 font-semibold">Full Name</th>
                            <th class="px-6 py-3 font-semibold">Source</th>
                            <th class="px-6 py-3 font-semibold">Email</th>
                            <th class="px-6 py-3 font-semibold">Mobile Number</th>
                            <th class="px-6 py-3 font-semibold">Bank Name</th>
                            <th class="px-6 py-3 font-semibold">Account Number</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($earners as $earner)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ $earner->full_name }}</td>
                                <td class="px-6 py-4 text-gray-700 capitalize">{{ $earner->source_type }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $earner->email ?: '—' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $earner->mobile_number ?: '—' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $earner->bank_name ?: '—' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $earner->account_number ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $earner->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $earner->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('sales-marketing.earners.show', $earner) }}"
                                           class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition"
                                           title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(auth()->user()->hasPermission('create_sales_marketing'))
                                            <button
                                                type="button"
                                                @click='setEdit(@json($earner))'
                                                class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 transition"
                                                title="Edit"
                                            >
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            <button
                                                type="button"
                                                @click='setDelete(@json($earner))'
                                                class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition"
                                                title="Delete"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                                    No earners yet.
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
        <div @click.outside="openAdd = false" class="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-gray-200">
            <form action="{{ route('sales-marketing.earners.store') }}" method="POST">
                @csrf

                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Add Commission Earner</h2>
                    <button type="button" @click="openAdd = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source Type</label>
                        <select name="source_type" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="manual">Manual</option>
                            <option value="contact">Contact</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <input type="text" name="mobile_number" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" name="account_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="account_number" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                        <input type="text" name="tin" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display:none;">
        <div @click.outside="openEdit = false" class="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-gray-200">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')

                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Edit Commission Earner</h2>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source Type</label>
                        <select name="source_type" x-model="form.source_type" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="manual">Manual</option>
                            <option value="contact">Contact</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-model="form.status" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" x-model="form.full_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="form.email" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <input type="text" name="mobile_number" x-model="form.mobile_number" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" x-model="form.bank_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" name="account_name" x-model="form.account_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="account_number" x-model="form.account_number" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                        <input type="text" name="tin" x-model="form.tin" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openEdit = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div x-show="openDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display:none;">
        <div @click.outside="openDelete = false" class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-200">
            <form :action="deleteAction" method="POST">
                @csrf
                @method('DELETE')

                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Delete Earner</h2>
                    <p class="text-sm text-gray-500 mt-2">
                        Are you sure you want to delete
                        <span class="font-semibold text-gray-800" x-text="form.full_name"></span>?
                    </p>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openDelete = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                        Delete
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
            openEdit: false,
            openDelete: false,
            editAction: '#',
            deleteAction: '#',
            form: {
                id: '',
                source_type: 'manual',
                full_name: '',
                email: '',
                mobile_number: '',
                bank_name: '',
                account_name: '',
                account_number: '',
                tin: '',
                status: 'Active',
            },

            setEdit(earner) {
                this.form = {
                    id: earner.id ?? '',
                    source_type: earner.source_type ?? 'manual',
                    full_name: earner.full_name ?? '',
                    email: earner.email ?? '',
                    mobile_number: earner.mobile_number ?? '',
                    bank_name: earner.bank_name ?? '',
                    account_name: earner.account_name ?? '',
                    account_number: earner.account_number ?? '',
                    tin: earner.tin ?? '',
                    status: earner.status ?? 'Active',
                };

                this.editAction = `/sales-marketing/earners/${earner.id}`;
                this.openEdit = true;
            },

            setDelete(earner) {
                this.form = {
                    id: earner.id ?? '',
                    source_type: earner.source_type ?? 'manual',
                    full_name: earner.full_name ?? '',
                    email: earner.email ?? '',
                    mobile_number: earner.mobile_number ?? '',
                    bank_name: earner.bank_name ?? '',
                    account_name: earner.account_name ?? '',
                    account_number: earner.account_number ?? '',
                    tin: earner.tin ?? '',
                    status: earner.status ?? 'Active',
                };

                this.deleteAction = `/sales-marketing/earners/${earner.id}`;
                this.openDelete = true;
            }
        }
    }
</script>
@endpush