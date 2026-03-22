@php
    $fieldPrefix = $fieldPrefix ?? 'service';
    $modalId = $modalId ?? $fieldPrefix . 'Modal';
    $formId = $formId ?? $fieldPrefix . 'Form';
    $methodId = $methodId ?? $fieldPrefix . 'FormMethod';
    $titleId = $titleId ?? $fieldPrefix . 'ModalTitle';
    $submitId = $submitId ?? $fieldPrefix . 'FormSubmit';
@endphp

<x-slide-over :id="$modalId" width="sm:max-w-[680px] lg:max-w-[720px]">
    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="{{ $titleId }}" class="text-2xl font-semibold text-gray-900">{{ $title }}</h2>
                @if (! empty($subtitle))
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" data-close-service-modal class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <form
        id="{{ $formId }}"
        method="POST"
        action="{{ $action }}"
        class="flex min-h-0 flex-1 flex-col"
        x-data="serviceFormState({
            serviceAreaOptions: @js($serviceAreaOptions->values()),
            engagementOptions: @js($engagementOptions->values()),
            initialServiceArea: @js(array_values((array) old('service_area', []))),
            initialEngagement: @js(array_values((array) old('engagement_structure', []))),
            initialFrequency: @js(old('frequency', ''))
        })"
        x-init="initialize()"
    >
        @csrf
        <input type="hidden" id="{{ $methodId }}" name="_method" value="POST">

        <div class="min-h-0 flex-1 space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="space-y-6">
                <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Service Intake</p>
                        <p class="text-xs text-gray-400">Configure the catalog record before routing, pricing, and recurring rules.</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 sm:items-end">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Status</label>
                            <select id="{{ $fieldPrefix }}FormStatus" name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Assigned Unit</label>
                            <select id="{{ $fieldPrefix }}FormAssignedUnit" name="assigned_unit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select unit</option>
                                @foreach ($assignedUnitOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if (! ($companyLocked ?? false))
                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Company Link</h3>
                        <p class="mb-4 text-xs text-gray-500">Link this service to a company record when applicable.</p>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Company</label>
                            <select id="{{ $fieldPrefix }}FormCompany" name="company_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">No linked company</option>
                                @foreach ($companies as $companyOption)
                                    <option value="{{ $companyOption->id }}">{{ $companyOption->company_name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>
                @else
                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Company Link</h3>
                        <p class="mb-4 text-xs text-gray-500">This service will be linked to the current company record.</p>
                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Linked Company</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $lockedCompany->company_name }}</p>
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Basic Information</h3>
                    <p class="mb-4 text-xs text-gray-500">Define the core name, summary, and delivery output of this service.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Name <span class="text-red-500">*</span></label>
                            <input id="{{ $fieldPrefix }}FormServiceName" name="service_name" type="text" value="{{ old('service_name') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            @error('service_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Description <span class="text-red-500">*</span></label>
                            <textarea id="{{ $fieldPrefix }}FormServiceDescription" name="service_description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>{{ old('service_description') }}</textarea>
                            @error('service_description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Activity / Output <span class="text-red-500">*</span></label>
                            <textarea id="{{ $fieldPrefix }}FormServiceOutput" name="service_activity_output" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>{{ old('service_activity_output') }}</textarea>
                            @error('service_activity_output')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Service Classification</h3>
                    <p class="mb-4 text-xs text-gray-500">Categorize the service area and service family before pricing and routing.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Service Area <span class="text-red-500">*</span></label>
                            <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_220px] sm:items-start">
                                <div class="rounded-2xl border border-gray-200 bg-white p-3">
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <template x-for="option in serviceAreaOptions" :key="option">
                                            <button
                                                type="button"
                                                @click="toggleServiceArea(option)"
                                                class="flex items-center gap-3 rounded-lg border px-3 py-2.5 text-left text-sm font-medium transition"
                                                :class="selectedServiceAreas.includes(option) ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50/40'"
                                            >
                                                <span class="flex h-4 w-4 items-center justify-center rounded border text-[10px]" :class="selectedServiceAreas.includes(option) ? 'border-blue-500 bg-blue-600 text-white' : 'border-gray-300 bg-white text-transparent'">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                                <span x-text="option"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="sm:pt-1">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Other Service Area</label>
                                    <input
                                        id="{{ $fieldPrefix }}FormServiceAreaOther"
                                        name="service_area_other"
                                        type="text"
                                        value="{{ old('service_area_other') }}"
                                        :disabled="!showOtherServiceArea"
                                        :class="showOtherServiceArea ? 'bg-white text-gray-900' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        placeholder="Specify other service area"
                                    >
                                    @error('service_area_other')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <template x-for="area in selectedServiceAreas" :key="'hidden-'+area">
                            <input type="hidden" name="service_area[]" :value="area">
                        </template>
                        <p class="text-xs text-gray-500">Select one or more service areas.</p>
                        @error('service_area')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Category</label>
                            <select id="{{ $fieldPrefix }}FormCategory" name="category" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select category</option>
                                @foreach ($categories as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Engagement & Structure</h3>
                    <p class="mb-4 text-xs text-gray-500">Define how the service is delivered and whether it should recur.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Engagement Structure <span class="text-red-500">*</span></label>
                            <div class="rounded-2xl border border-gray-200 bg-white p-3">
                                <div class="grid gap-2">
                                    <template x-for="option in engagementOptions" :key="option">
                                        <button
                                            type="button"
                                            @click="toggleEngagement(option)"
                                            class="flex items-center gap-3 rounded-lg border px-3 py-2.5 text-left text-sm font-medium transition"
                                            :class="selectedEngagements.includes(option) ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50/40'"
                                        >
                                            <span class="flex h-4 w-4 items-center justify-center rounded border text-[10px]" :class="selectedEngagements.includes(option) ? 'border-blue-500 bg-blue-600 text-white' : 'border-gray-300 bg-white text-transparent'">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span x-text="option"></span>
                                        </button>
                                    </template>
                                </div>
                                <template x-for="engagement in selectedEngagements" :key="'engagement-hidden-'+engagement">
                                    <input type="hidden" name="engagement_structure[]" :value="engagement">
                                </template>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Select engagement type(s).</p>
                            @error('engagement_structure')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2 md:grid-cols-1">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Assigned Unit</label>
                                <select id="{{ $fieldPrefix }}FormAssignedUnit" name="assigned_unit" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select unit</option>
                                    @foreach ($assignedUnitOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select id="{{ $fieldPrefix }}FormStatus" name="status" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                    @foreach ($statusOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Is Recurring</label>
                                <div class="flex h-10 items-center rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-700">
                                    <span x-text="isRecurring ? 'Yes' : 'No'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Scheduling</h3>
                    <p class="mb-4 text-xs text-gray-500">Control cadence, reminder timing, and one-time deadlines.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Frequency</label>
                            <select id="{{ $fieldPrefix }}FormFrequency" name="frequency" @change="syncFrequency($event.target.value)" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select frequency</option>
                                @foreach ($frequencyOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('frequency')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Reminder Lead Time</label>
                            <select id="{{ $fieldPrefix }}FormReminder" name="reminder_lead_time" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select lead time</option>
                                @foreach ($reminderLeadTimes as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="showScheduleRule" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Schedule Rule</label>
                            <input id="{{ $fieldPrefix }}FormScheduleRule" name="schedule_rule" type="text" value="{{ old('schedule_rule') }}" placeholder="Every 5th of the month" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('schedule_rule')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-show="showDeadline" x-cloak>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Deadline</label>
                            <input id="{{ $fieldPrefix }}FormDeadline" name="deadline" type="datetime-local" value="{{ old('deadline') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('deadline')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Requirements</h3>
                    <p class="mb-4 text-xs text-gray-500">Group documentary requirements under the right service client category.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Requirement Category</label>
                            <select id="{{ $fieldPrefix }}FormRequirementCategory" name="requirement_category" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select category</option>
                                @foreach ($requirementCategories as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('requirement_category')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Requirements</label>
                            <textarea id="{{ $fieldPrefix }}FormRequirements" name="requirements" rows="5" placeholder="Enter one requirement per line" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('requirements') }}</textarea>
                            <p class="mt-2 text-xs text-gray-500">Each line is stored as a grouped bullet under the selected category.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">Pricing</h3>
                    <p class="mb-4 text-xs text-gray-500">Capture unit economics, caps, and fallback fees for service billing.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Unit <span class="text-red-500">*</span></label>
                            <select id="{{ $fieldPrefix }}FormUnit" name="unit" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                <option value="">Select unit</option>
                                @foreach ($unitOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('unit')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Rate per Unit</label>
                            <input id="{{ $fieldPrefix }}FormRatePerUnit" name="rate_per_unit" type="number" min="0" step="0.01" value="{{ old('rate_per_unit') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('rate_per_unit')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Minimum Units</label>
                            <input id="{{ $fieldPrefix }}FormMinUnits" name="min_units" type="number" min="1" step="1" value="{{ old('min_units') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Maximum Cap</label>
                            <input id="{{ $fieldPrefix }}FormMaxCap" name="max_cap" type="number" min="0" step="0.01" value="{{ old('max_cap') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Price / Fee</label>
                            <input id="{{ $fieldPrefix }}FormPriceFee" name="price_fee" type="number" min="0" step="0.01" value="{{ old('price_fee') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('price_fee')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Cost of Service</label>
                            <input id="{{ $fieldPrefix }}FormCost" name="cost_of_service" type="number" min="0" step="0.01" value="{{ old('cost_of_service') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>
                </section>

                @if ($customFields->count() > 0)
                    <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <h3 class="text-base font-semibold text-gray-900">Custom Fields</h3>
                        <p class="mb-4 text-xs text-gray-500">Capture module-specific data without changing the baseline service schema.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($customFields as $field)
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">{{ $field->field_name }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                                    @if ($field->field_type === 'textarea')
                                        <textarea name="custom_fields[{{ $field->field_key }}]" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('custom_fields.' . $field->field_key, $field->default_value) }}</textarea>
                                    @elseif ($field->field_type === 'picklist')
                                        <select name="custom_fields[{{ $field->field_key }}]" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                            <option value="">Select option</option>
                                            @foreach (($field->options ?? []) as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($field->field_type === 'checkbox')
                                        <label class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700">
                                            <input type="checkbox" name="custom_fields[{{ $field->field_key }}]" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            Enabled
                                        </label>
                                    @elseif ($field->field_type === 'date')
                                        <input type="date" name="custom_fields[{{ $field->field_key }}]" value="{{ old('custom_fields.' . $field->field_key, $field->default_value) }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    @else
                                        <input type="{{ in_array($field->field_type, ['number', 'currency'], true) ? 'number' : 'text' }}" @if(in_array($field->field_type, ['number', 'currency'], true)) step="0.01" min="0" @endif name="custom_fields[{{ $field->field_key }}]" value="{{ old('custom_fields.' . $field->field_key, $field->default_value) }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                    <h3 class="text-base font-semibold text-gray-900">System Info</h3>
                    <p class="mb-4 text-xs text-gray-500">Track review and approval metadata without leaving the service record.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Created By</label>
                            <input id="{{ $fieldPrefix }}FormCreatedByLabel" type="text" readonly class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-600">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Updated At</label>
                            <input id="{{ $fieldPrefix }}FormUpdatedAtLabel" type="text" readonly class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-600">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Reviewed By</label>
                            <select id="{{ $fieldPrefix }}FormReviewedBy" name="reviewed_by" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select reviewer</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Reviewed At</label>
                            <input id="{{ $fieldPrefix }}FormReviewedAt" name="reviewed_at" type="datetime-local" value="{{ old('reviewed_at') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Approved By</label>
                            <select id="{{ $fieldPrefix }}FormApprovedBy" name="approved_by" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Select approver</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Approved At</label>
                            <input id="{{ $fieldPrefix }}FormApprovedAt" name="approved_at" type="datetime-local" value="{{ old('approved_at') }}" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="mt-auto border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-service-modal class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" id="{{ $submitId }}" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">Save Service</button>
            </div>
        </div>
    </form>
</x-slide-over>

<script>
    function serviceFormState(config) {
        return {
            serviceAreaOptions: config.serviceAreaOptions || [],
            engagementOptions: config.engagementOptions || [],
            selectedServiceAreas: config.initialServiceArea || [],
            selectedEngagements: config.initialEngagement || [],
            showOtherServiceArea: false,
            showScheduleRule: false,
            showDeadline: false,
            isRecurring: false,
            initialize() {
                this.showOtherServiceArea = this.selectedServiceAreas.includes('Others');
                this.isRecurring = this.selectedEngagements.includes('Regular (Retainer)') || this.selectedEngagements.includes('Hybrid');
                this.showScheduleRule = this.isRecurring;
                this.syncFrequency(config.initialFrequency || '');
            },
            toggleServiceArea(option) {
                if (this.selectedServiceAreas.includes(option)) {
                    this.selectedServiceAreas = this.selectedServiceAreas.filter((item) => item !== option);
                } else {
                    this.selectedServiceAreas = [...this.selectedServiceAreas, option];
                }
                this.showOtherServiceArea = this.selectedServiceAreas.includes('Others');
            },
            toggleEngagement(option) {
                if (this.selectedEngagements.includes(option)) {
                    this.selectedEngagements = this.selectedEngagements.filter((item) => item !== option);
                } else {
                    this.selectedEngagements = [...this.selectedEngagements, option];
                }
                this.isRecurring = this.selectedEngagements.includes('Regular (Retainer)') || this.selectedEngagements.includes('Hybrid');
                this.showScheduleRule = this.isRecurring;
            },
            syncFrequency(value) {
                this.showDeadline = value === 'One-time';
            },
        };
    }
</script>
