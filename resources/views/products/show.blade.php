@extends('layouts.app')

@section('content')
@php
    $priceLabel = isset($product['unit_price']) && $product['unit_price'] !== null
        ? 'P'.number_format((float) $product['unit_price'], 2).' / Unit'
        : null;
@endphp

<div class="px-6 py-6 lg:px-8">
    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('products.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-blue-600">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Back to Products</span>
            </a>
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ $product['product_name'] }}
                @if ($priceLabel)
                    <span class="text-2xl font-semibold text-gray-700"> &middot; {{ $priceLabel }}</span>
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">{{ $product['product_owner'] ?? 'John Admin' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="h-9 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">
                Edit Product
            </button>
            <button type="button" class="h-9 w-9 rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[280px_1fr]">
        <aside class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-2xl font-semibold text-gray-900">Basic Info</h2>
            <dl class="space-y-3 text-sm">
                <div class="grid grid-cols-[120px_1fr] gap-2">
                    <dt class="text-gray-500">Product Code</dt>
                    <dd class="text-gray-800">{{ $product['product_code'] ?? '-' }}</dd>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-2">
                    <dt class="text-gray-500">Product Category</dt>
                    <dd class="text-gray-800">{{ $product['product_category'] ?? '-' }}</dd>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-2">
                    <dt class="text-gray-500">Product Active</dt>
                    <dd class="text-gray-800">{{ $product['product_active'] ?? 'Inactive' }}</dd>
                </div>
            </dl>

            <div class="mt-6">
                <h3 class="mb-2 text-lg font-semibold text-gray-900">Description</h3>
                <p class="text-sm text-gray-600">{{ $product['description'] ?: '-' }}</p>
            </div>

            <div class="mt-6 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                {{ $lastModifiedLabel }}
            </div>
        </aside>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 pt-4">
                <nav class="flex items-center gap-5 text-sm">
                    @foreach ($tabs as $tabKey => $tabLabel)
                        <a
                            href="{{ route('products.show', ['id' => $product['product_id'], 'tab' => $tabKey]) }}"
                            class="border-b-2 pb-3 {{ $tab === $tabKey ? 'border-blue-600 text-blue-700 font-medium' : 'border-transparent text-gray-600 hover:text-gray-800' }}"
                        >
                            {{ $tabLabel }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="p-5">
                @if ($tab === 'timeline')
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">History</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach ($timeline as $item)
                            <article class="flex gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <span class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                    <i class="fas {{ $item['icon'] }} text-xs"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $item['title'] }} by {{ $item['user_name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $item['description'] }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $item['created_at'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($tab === 'pipelines')
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Team Pipeline: <span class="font-medium text-gray-800">Sales Pipeline</span></p>
                        <button type="button" class="h-9 rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm font-medium text-blue-700 hover:bg-blue-100">
                            + Deal
                        </button>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Deal Name</th>
                                    <th class="px-3 py-2 text-left">Amount</th>
                                    <th class="px-3 py-2 text-left">Stage</th>
                                    <th class="px-3 py-2 text-left">Closing Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($pipelines as $pipeline)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ $pipeline['deal_name'] }}</td>
                                        <td class="px-3 py-2 text-gray-600">P{{ number_format((float) $pipeline['amount'], 2) }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $pipeline['stage'] }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $pipeline['closing_date'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-10 text-center text-sm text-gray-500">This record doesn't have any deals.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif ($tab === 'files')
                    <h3 class="mb-4 text-xl font-semibold text-gray-900">Files</h3>
                    <div class="rounded-lg border border-dashed border-gray-300 px-5 py-6 text-center text-sm text-gray-600">
                        <p>
                            Drag a file here or
                            <button type="button" class="font-medium text-blue-600 hover:text-blue-700">Browse</button>
                            for a file to upload
                        </p>
                    </div>

                    @if (count($files) > 0)
                        <div class="mt-4 space-y-2">
                            @foreach ($files as $file)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                    <span class="text-gray-700">{{ $file['name'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $file['size'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-16 text-center text-sm text-gray-500">This record doesn't have any files.</p>
                    @endif
                @elseif ($tab === 'tasks')
                    <div class="mb-4">
                        <select class="h-9 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option>All Tasks</option>
                        </select>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Task Name</th>
                                    <th class="px-3 py-2 text-left">Due Date</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Task Owner</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($tasks as $task)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ $task['task_name'] }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $task['due_date'] }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $task['status'] }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $task['owner_name'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-10 text-center text-sm text-gray-500">This record doesn't have any tasks.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

