<div id="createFieldDropdownMenu" class="fixed z-[90] hidden max-h-72 w-52 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
    @foreach ($fieldTypes as $type)
        <button
            type="button"
            class="create-field-type-option flex w-full items-center gap-3 px-3 py-2 text-left text-sm text-gray-700 hover:bg-blue-50"
            data-field-type="{{ $type['value'] }}"
            data-field-label="{{ $type['label'] }}"
        >
            <i class="fas {{ $type['icon'] }} w-4 text-center text-gray-500"></i>
            <span>{{ $type['label'] }}</span>
        </button>
    @endforeach
</div>
