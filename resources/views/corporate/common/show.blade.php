@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <a href="{{ $backRoute }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-lg font-semibold">{{ $title }}</div>
            <div class="flex-1"></div>
            @if (!empty($editRoute))
                <a href="{{ $editRoute }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Edit
                </a>
            @endif
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($fields as $field)
                @php
                    $fieldName = $field['name'];
                    $fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
                    $value = data_get($item, $fieldName);
                    $isFile = ($field['type'] ?? '') === 'file';
                @endphp
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                    <div class="text-xs text-gray-500">{{ $fieldLabel }}</div>
                    <div class="text-sm font-medium text-gray-900 mt-1">
                        @if ($isFile)
                            @if ($value)
                                <span class="text-gray-700">File uploaded</span>
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        @else
                            {{ $value ?? '—' }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
