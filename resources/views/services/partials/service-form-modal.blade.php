@php
    $fieldPrefix = $fieldPrefix ?? 'service';
    $modalId = $modalId ?? $fieldPrefix . 'Modal';
    $formId = $formId ?? $fieldPrefix . 'Form';
    $methodId = $methodId ?? $fieldPrefix . 'FormMethod';
    $titleId = $titleId ?? $fieldPrefix . 'ModalTitle';
    $submitId = $submitId ?? $fieldPrefix . 'FormSubmit';
    $companyFieldId = $companyFieldId ?? $fieldPrefix . 'FormCompany';
    $nameFieldId = $nameFieldId ?? $fieldPrefix . 'FormName';
    $categoryFieldId = $categoryFieldId ?? $fieldPrefix . 'FormCategory';
    $assignedStaffFieldId = $assignedStaffFieldId ?? $fieldPrefix . 'FormAssignedStaff';
    $statusFieldId = $statusFieldId ?? $fieldPrefix . 'FormStatus';
    $frequencyFieldId = $frequencyFieldId ?? $fieldPrefix . 'FormFrequency';
    $startDateFieldId = $startDateFieldId ?? $fieldPrefix . 'FormStartDate';
    $endDateFieldId = $endDateFieldId ?? $fieldPrefix . 'FormEndDate';
    $priorityFieldId = $priorityFieldId ?? $fieldPrefix . 'FormPriority';
    $packageFieldId = $packageFieldId ?? $fieldPrefix . 'FormPackage';
    $levelFieldId = $levelFieldId ?? $fieldPrefix . 'FormLevel';
    $descriptionFieldId = $descriptionFieldId ?? $fieldPrefix . 'FormDescription';
@endphp

<x-slide-over :id="$modalId" width="sm:max-w-[680px] lg:max-w-[820px]">
    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="{{ $titleId }}" class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                @if (! empty($subtitle))
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" data-close-service-modal class="h-9 w-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <form id="{{ $formId }}" method="POST" action="{{ $action }}" class="flex min-h-0 flex-1 flex-col">
        @csrf
        <input type="hidden" id="{{ $methodId }}" name="_method" value="POST">

        <div class="flex-1 overflow-y-auto px-4 py-4 sm:px-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @if ($companyLocked ?? false)
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Company</label>
                            <input type="text" value="{{ $lockedCompany->company_name }}" class="h-9 w-full rounded border border-gray-200 bg-gray-50 px-4 text-sm text-gray-600 outline-none" readonly>
                        </div>
                    @else
                        <div class="md:col-span-2">
                            <label for="{{ $companyFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Company <span class="text-red-500">*</span></label>
                            <select id="{{ $companyFieldId }}" name="company_id" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                                <option value="">Select company</option>
                                @foreach ($companies as $modalCompany)
                                    <option value="{{ $modalCompany->id }}">{{ $modalCompany->company_name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label for="{{ $nameFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Service Name <span class="text-red-500">*</span></label>
                        <input id="{{ $nameFieldId }}" name="name" type="text" value="{{ old('name') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $categoryFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <input id="{{ $categoryFieldId }}" name="category" type="text" value="{{ old('category') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $assignedStaffFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Assigned Staff <span class="text-red-500">*</span></label>
                        <input id="{{ $assignedStaffFieldId }}" name="assigned_staff" type="text" value="{{ old('assigned_staff') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        @error('assigned_staff')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $statusFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select id="{{ $statusFieldId }}" name="status" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}" @selected(old('status', 'Active') === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $frequencyFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Frequency <span class="text-red-500">*</span></label>
                        <select id="{{ $frequencyFieldId }}" name="frequency" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                            @foreach ($frequencyOptions as $frequencyOption)
                                <option value="{{ $frequencyOption }}" @selected(old('frequency', 'Monthly') === $frequencyOption)>{{ $frequencyOption }}</option>
                            @endforeach
                        </select>
                        @error('frequency')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $startDateFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                        <input id="{{ $startDateFieldId }}" name="start_date" type="date" value="{{ old('start_date') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $endDateFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">End Date</label>
                        <input id="{{ $endDateFieldId }}" name="end_date" type="date" value="{{ old('end_date') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $priorityFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Priority</label>
                        <select id="{{ $priorityFieldId }}" name="priority" class="h-9 w-full rounded border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            @foreach (['Low', 'Normal', 'High', 'Critical'] as $priorityOption)
                                <option value="{{ $priorityOption }}" @selected(old('priority', 'Normal') === $priorityOption)>{{ $priorityOption }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $packageFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Service Package</label>
                        <input id="{{ $packageFieldId }}" name="service_package" type="text" value="{{ old('service_package') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        @error('service_package')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $levelFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Service Level</label>
                        <input id="{{ $levelFieldId }}" name="service_level" type="text" value="{{ old('service_level') }}" class="h-9 w-full rounded border border-gray-200 px-4 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        @error('service_level')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="{{ $descriptionFieldId }}" class="mb-1 block text-sm font-medium text-gray-700">Description / Notes</label>
                        <textarea id="{{ $descriptionFieldId }}" name="description" rows="4" class="w-full rounded border border-gray-200 px-4 py-3 text-sm text-gray-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
            </div>
        </div>

        <div class="border-t border-gray-100 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-close-service-modal class="h-9 min-w-[100px] rounded-full border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="{{ $submitId }}" class="h-9 min-w-[100px] rounded-full bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </form>
</x-slide-over>
