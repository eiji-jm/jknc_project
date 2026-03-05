@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-4 py-4 border-b border-gray-100">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">COMPANY</h1>
            <p class="mt-1 text-sm text-gray-500">Manage companies and deal relationships</p>

            @if (session('success'))
                <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <div class="relative w-full sm:w-[320px]">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        placeholder="Search Company..."
                        class="w-full h-9 rounded-full border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                    >
                </div>

                <button
                    type="button"
                    class="h-9 min-w-[100px] px-4 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium flex items-center justify-center gap-2"
                >
                    <i class="fas fa-filter text-xs"></i>
                    <span>All</span>
                </button>

                <button
                    type="button"
                    id="openAddCompanyModal"
                    class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2"
                >
                    <span class="text-base leading-none">+</span>
                    <span>Add Company</span>
                </button>
            </div>
        </div>

        <div class="p-4 bg-gray-50">
            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Company Name</th>
                            <th class="px-4 py-3 text-left font-medium">Phone</th>
                            <th class="px-4 py-3 text-left font-medium">Website</th>
                            <th class="px-4 py-3 text-left font-medium">Company Owner</th>
                            <th class="px-4 py-3 text-left font-medium">Actions</th>
                            <th class="px-4 py-3 text-right font-medium">+ Create Field</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                        @forelse ($companies as $company)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-[10px] font-bold leading-tight">
                                            JK<br>&amp;C
                                        </div>
                                        <span class="font-medium text-gray-700">{{ $company->company_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $company->phone ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block max-w-[220px] truncate align-middle">{{ $company->website ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $company->owner_name ?: 'Owner 1' }}</td>
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('company.show', $company->id) }}"
                                        class="inline-flex h-9 px-4 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium items-center"
                                    >
                                        View
                                    </a>
                                </td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                    No companies yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('company.partials.modal-add-company')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addCompanyModal');
        const openButton = document.getElementById('openAddCompanyModal');
        const closeButtons = document.querySelectorAll('[data-close-company-modal]');

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        openButton.addEventListener('click', openModal);

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        @if ($errors->any())
            openModal();
        @endif
    });
</script>
@endsection
