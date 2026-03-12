@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4 pb-8">
    <section class="bg-gray-50 min-h-[760px]">
        <div class="rounded-md border border-gray-200 bg-white overflow-hidden shadow-sm">
            <div class="border-b border-gray-100 px-4 py-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900">SERVICES</h2>
                        <p class="mt-1 text-sm text-gray-500">Manage service engagements across all companies.</p>
                    </div>

                    <button type="button" id="openGlobalServiceModalCreate" class="h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2">
                        <span class="text-base leading-none">+</span>
                        <span>Add Service</span>
                    </button>
                </div>

                @if (session('services_success'))
                    <div class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                        {{ session('services_success') }}
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-3">
                    <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Services</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['active'] }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Upcoming Renewals</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['upcoming'] }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Completed Services</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['completed'] }}</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('services.index') }}" class="mt-4 grid grid-cols-1 gap-2 lg:grid-cols-12">
                    <div class="relative lg:col-span-4">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search services, companies, staff..." class="w-full h-10 rounded border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div class="lg:col-span-2">
                        <select name="status" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="all">Status: All</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <select name="staff" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="all">Assigned Staff: All</option>
                            @foreach ($staffOptions as $staffOption)
                                <option value="{{ $staffOption }}" @selected($filters['staff'] === $staffOption)>{{ $staffOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <select name="category" class="w-full h-10 rounded border border-gray-200 bg-white px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="all">Category: All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button class="h-10 flex-1 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Apply</button>
                        @if ($filters['search'] !== '' || $filters['status'] !== 'all' || $filters['staff'] !== 'all' || $filters['category'] !== 'all')
                            <a href="{{ route('services.index') }}" class="h-10 flex-1 rounded border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="p-4">
                <div class="border border-gray-200 rounded-md bg-white overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Service Name</th>
                                    <th class="px-4 py-3 text-left font-medium">Company</th>
                                    <th class="px-4 py-3 text-left font-medium">Category</th>
                                    <th class="px-4 py-3 text-left font-medium">Assigned Staff</th>
                                    <th class="px-4 py-3 text-left font-medium">Status</th>
                                    <th class="px-4 py-3 text-left font-medium">Frequency</th>
                                    <th class="px-4 py-3 text-left font-medium">Start Date</th>
                                    <th class="px-4 py-3 text-left font-medium">End Date</th>
                                    <th class="px-4 py-3 text-left font-medium">Last Updated</th>
                                    <th class="px-4 py-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                                @forelse ($services as $service)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $service['name'] }}</div>
                                            @if ($service['description'])
                                                <div class="mt-1 text-xs text-gray-500">{{ $service['description'] }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('company.services.index', $service['company_id']) }}" class="font-medium text-blue-700 hover:text-blue-800">
                                                {{ $service['company_name'] }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">{{ $service['category'] }}</td>
                                        <td class="px-4 py-3">{{ $service['assigned_staff'] }}</td>
                                        <td class="px-4 py-3">
                                            @php($statusClasses = match($service['status']) {
                                                'Active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                'Pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                                'Completed' => 'border-blue-200 bg-blue-50 text-blue-700',
                                                'On Hold' => 'border-violet-200 bg-violet-50 text-violet-700',
                                                default => 'border-red-200 bg-red-50 text-red-700',
                                            })
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">{{ $service['status'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">{{ $service['frequency'] }}</td>
                                        <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($service['start_date'])->format('M d, Y') }}</td>
                                        <td class="px-4 py-3">{{ $service['end_date'] ? \Illuminate\Support\Carbon::parse($service['end_date'])->format('M d, Y') : '-' }}</td>
                                        <td class="px-4 py-3">{{ $service['updated_at'] }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('services.show', $service['id']) }}" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                    View
                                                </a>
                                                <button type="button" class="inline-flex h-8 items-center rounded-full border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50" data-global-service-edit='@json($service)'>
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('services.destroy', $service['id']) }}" onsubmit="return confirm('Delete this service engagement?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex h-8 items-center rounded-full border border-red-200 px-3 text-xs font-medium text-red-600 hover:bg-red-50">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-12">
                                            <div class="flex flex-col items-center justify-center text-center">
                                                <div class="h-12 w-12 rounded-full bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                                                    <i class="fas fa-briefcase"></i>
                                                </div>
                                                <h3 class="mt-4 text-base font-semibold text-gray-900">No service engagements found yet.</h3>
                                                <p class="mt-1 max-w-md text-sm text-gray-500">Create the first service record and link it to a company to start tracking ownership and delivery dates.</p>
                                                <button type="button" id="openFirstGlobalServiceModal" class="mt-4 h-9 rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700 inline-flex items-center gap-2">
                                                    <span class="text-base leading-none">+</span>
                                                    <span>Add Service</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 px-4 py-3 flex flex-wrap items-center justify-end gap-3 text-sm text-gray-500">
                <span>{{ $services->count() }} {{ \Illuminate\Support\Str::plural('service', $services->count()) }}</span>
            </div>
        </div>
    </section>
</div>

@include('services.partials.service-form-modal', [
    'fieldPrefix' => 'globalService',
    'modalId' => 'globalServiceModal',
    'title' => 'Add Service',
    'subtitle' => 'Create a service engagement and link it to a company.',
    'action' => route('services.store'),
    'companies' => $companies,
    'statusOptions' => $statusOptions,
    'frequencyOptions' => $frequencyOptions,
    'companyLocked' => false,
])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('globalServiceModal');
        const openButtons = [document.getElementById('openGlobalServiceModalCreate'), document.getElementById('openFirstGlobalServiceModal')].filter(Boolean);
        const closeButtons = modal.querySelectorAll('[data-close-service-modal]');
        const editButtons = document.querySelectorAll('[data-global-service-edit]');
        const form = document.getElementById('globalServiceForm');
        const methodInput = document.getElementById('globalServiceFormMethod');
        const title = document.getElementById('globalServiceModalTitle');
        const submit = document.getElementById('globalServiceFormSubmit');
        const updateUrlTemplate = @json(route('services.update', '__SERVICE__'));

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const resetForm = () => {
            form.reset();
            form.action = @json(route('services.store'));
            methodInput.value = 'POST';
            title.textContent = 'Add Service';
            submit.textContent = 'Save';
            document.getElementById('globalServiceFormStatus').value = 'Active';
            document.getElementById('globalServiceFormFrequency').value = 'Monthly';
            document.getElementById('globalServiceFormPriority').value = 'Normal';
        };

        const fillForm = (service) => {
            document.getElementById('globalServiceFormCompany').value = service.company_id ?? '';
            document.getElementById('globalServiceFormName').value = service.name ?? '';
            document.getElementById('globalServiceFormCategory').value = service.category ?? '';
            document.getElementById('globalServiceFormAssignedStaff').value = service.assigned_staff ?? '';
            document.getElementById('globalServiceFormStatus').value = service.status ?? 'Active';
            document.getElementById('globalServiceFormFrequency').value = service.frequency ?? 'Monthly';
            document.getElementById('globalServiceFormStartDate').value = service.start_date ?? '';
            document.getElementById('globalServiceFormEndDate').value = service.end_date ?? '';
            document.getElementById('globalServiceFormPriority').value = service.priority ?? 'Normal';
            document.getElementById('globalServiceFormPackage').value = service.service_package ?? '';
            document.getElementById('globalServiceFormLevel').value = service.service_level ?? '';
            document.getElementById('globalServiceFormDescription').value = service.description ?? '';
        };

        openButtons.forEach((button) => button.addEventListener('click', function () {
            resetForm();
            openModal();
        }));

        closeButtons.forEach((button) => button.addEventListener('click', closeModal));

        editButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const service = JSON.parse(this.dataset.globalServiceEdit);
                resetForm();
                form.action = updateUrlTemplate.replace('__SERVICE__', service.id);
                methodInput.value = 'PUT';
                title.textContent = 'Edit Service';
                submit.textContent = 'Update';
                fillForm(service);
                openModal();
            });
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

        @if ($errors->has('company_id') || $errors->has('name') || $errors->has('category') || $errors->has('assigned_staff') || $errors->has('status') || $errors->has('frequency') || $errors->has('start_date') || $errors->has('end_date') || $errors->has('description') || $errors->has('priority') || $errors->has('service_package') || $errors->has('service_level'))
            resetForm();
            openModal();
        @endif
    });
</script>
@endsection
