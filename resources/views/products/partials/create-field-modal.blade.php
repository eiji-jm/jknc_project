<div id="createFieldModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl border border-gray-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Create Field</h2>
                <p class="text-sm text-gray-500">Field Type: <span id="createFieldTypeLabel" class="font-medium text-gray-700">Picklist</span></p>
            </div>
            <button id="closeCreateFieldModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form method="POST" action="{{ route('products.custom-fields.store') }}" class="space-y-4 p-6">
            @csrf
            <input id="createFieldTypeInput" type="hidden" name="field_type" value="{{ old('field_type', 'picklist') }}">

            <div class="grid grid-cols-[140px_1fr] items-center gap-3">
                <label for="field_name" class="text-right text-sm font-medium text-gray-700">Field Name</label>
                <input id="field_name" name="field_name" value="{{ old('field_name') }}" required class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div id="picklistOptionsSection" class="space-y-2">
                <div class="grid grid-cols-[140px_1fr] items-start gap-3">
                    <label class="pt-2 text-right text-sm font-medium text-gray-700">Options</label>
                    <div>
                        <div id="picklistOptionsContainer" class="space-y-2">
                            @php
                                $oldOptions = old('options', ['']);
                                if (! is_array($oldOptions) || count($oldOptions) === 0) {
                                    $oldOptions = [''];
                                }
                            @endphp
                            @foreach ($oldOptions as $value)
                                <div class="flex items-center gap-2">
                                    <input name="options[]" value="{{ $value }}" placeholder="Option value" class="h-10 flex-1 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <button type="button" class="remove-picklist-option h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button id="addPicklistOption" type="button" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">
                            + Add Option
                        </button>
                    </div>
                </div>
            </div>

            <div id="defaultValueSection" class="grid grid-cols-[140px_1fr] items-center gap-3">
                <label for="default_value" class="text-right text-sm font-medium text-gray-700">Default Value</label>
                <input id="default_value" name="default_value" value="{{ old('default_value') }}" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div id="lookupSection" class="hidden grid-cols-[140px_1fr] items-center gap-3">
                <label for="lookup_module" class="text-right text-sm font-medium text-gray-700">Lookup Module</label>
                <select id="lookup_module" name="lookup_module" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Select module</option>
                    <option value="deals" {{ old('lookup_module') === 'deals' ? 'selected' : '' }}>Deals</option>
                    <option value="company" {{ old('lookup_module') === 'company' ? 'selected' : '' }}>Company</option>
                    <option value="contacts" {{ old('lookup_module') === 'contacts' ? 'selected' : '' }}>Contacts</option>
                </select>
            </div>

            <div class="grid grid-cols-[140px_1fr] items-center gap-3">
                <span></span>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="required" value="1" {{ old('required') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Required
                </label>
            </div>

            @if ($errors->has('field_name') || $errors->has('field_type') || $errors->has('default_value') || $errors->has('lookup_module') || $errors->has('options'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <button id="cancelCreateFieldModal" type="button" class="h-10 rounded-lg border border-gray-300 px-4 text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
