@extends('layouts.app')

@section('content')
<div
    class="w-full h-full px-6 py-5"
    x-data="{
        showSlideOver: false,
        showViewer: false,
        selectedCommunication: null
    }"
>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- VIEWER MODAL --}}
    <div x-show="showViewer" x-cloak class="fixed inset-0 z-[60] overflow-hidden">
        <div class="absolute inset-0 bg-black/40" @click="showViewer = false"></div>

        <div class="absolute inset-0 flex items-center justify-center p-6">
            <div
                x-show="showViewer"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-5xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-200 flex flex-col"
            >
                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Communication Details</h2>
                        <p class="text-sm text-gray-500" x-text="selectedCommunication?.ref_no || ''"></p>
                    </div>

                    <button
                        type="button"
                        @click="showViewer = false"
                        class="text-gray-400 hover:text-gray-600 text-lg"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Date</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.communication_date || '—'"></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">From</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.from_name || '—'"></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">To / For</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.to_for || '—'"></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Department / Stakeholder</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.department_stakeholder || '—'"></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</p>
                            <p class="text-sm">
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-medium"
                                    :class="{
                                        'bg-yellow-50 text-yellow-700': (selectedCommunication?.status ?? 'Open') === 'Open',
                                        'bg-green-50 text-green-700': selectedCommunication?.status === 'Completed',
                                        'bg-red-50 text-red-700': selectedCommunication?.status === 'Overdue'
                                    }"
                                    x-text="selectedCommunication?.status || '—'"
                                ></span>
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Subject</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.subject || '—'"></p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Body</p>
                        <div class="prose prose-sm max-w-none text-gray-800" x-html="selectedCommunication?.message || '<p>—</p>'"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">CC</p>
                            <p class="text-sm text-gray-800" x-text="selectedCommunication?.cc || '—'"></p>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Attachments</p>

                            <template x-if="selectedCommunication?.attachment_url">
                                <a
                                    :href="selectedCommunication.attachment_url"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline"
                                >
                                    <i class="fas fa-paperclip"></i>
                                    <span>View Attachment</span>
                                </a>
                            </template>

                            <template x-if="!selectedCommunication?.attachment_url">
                                <p class="text-sm text-gray-800">—</p>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button
                        type="button"
                        @click="showViewer = false"
                        class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SLIDE OVER --}}
    <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">

            <div
                x-show="showSlideOver"
                @click="showSlideOver = false"
                class="absolute inset-0 bg-black/40"
            ></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-3xl bg-white shadow-2xl h-full flex flex-col"
                >
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Add Communication</h2>

                        <button
                            type="button"
                            @click="showSlideOver = false"
                            class="text-gray-400 hover:text-gray-600 text-lg"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="townhall-form" action="{{ route('townhall.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        @csrf

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Ref #</label>
                                <input
                                    type="text"
                                    value="AUTO-INCREMENT"
                                    readonly
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
                                <input
                                    type="date"
                                    name="communication_date"
                                    value="{{ old('communication_date') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
                                <input
                                    type="text"
                                    name="from_name"
                                    value="{{ old('from_name') }}"
                                    placeholder="Enter sender"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Department / Stakeholder</label>
                                <input
                                    type="text"
                                    name="department_stakeholder"
                                    value="{{ old('department_stakeholder') }}"
                                    placeholder="Enter department or stakeholder"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">To / For</label>
                                <input
                                    type="text"
                                    name="to_for"
                                    value="{{ old('to_for') }}"
                                    placeholder="Enter recipient"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                                <select
                                    name="status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                                    <option value="">Select status</option>
                                    <option value="Open" {{ old('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Overdue" {{ old('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
                            <input
                                type="text"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="Enter subject"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Body</label>

                            <div id="editor">{!! old('message') !!}</div>
                            <input type="hidden" name="message" id="message">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">CC</label>
                                <input
                                    type="text"
                                    name="cc"
                                    value="{{ old('cc') }}"
                                    placeholder="Enter CC recipients"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Additional</label>
                                <input
                                    type="text"
                                    name="additional"
                                    value="{{ old('additional') }}"
                                    placeholder="Optional"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Attachment</label>
                                <input
                                    type="file"
                                    name="attachment"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                                >
                                <p class="mt-1 text-xs text-gray-400">
                                    Allowed: JPG, JPEG, PNG, GIF, WEBP, PDF, DOC, DOCX
                                </p>
                            </div>

                        <div class="px-0 py-4 border-t border-gray-200 flex items-center gap-3">
                            <button
                                type="button"
                                @click="showSlideOver = false"
                                class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        <div class="px-5 py-4 flex items-center justify-between">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Town Hall</h1>

            <div class="flex items-center gap-2">
                <button class="w-8 h-8 rounded-md border border-gray-200 text-gray-400 hover:bg-gray-50 flex items-center justify-center">
                    <i class="fas fa-bars text-xs"></i>
                </button>

                <button class="w-8 h-8 rounded-md border border-gray-200 text-gray-400 hover:bg-gray-50 flex items-center justify-center">
                    <i class="far fa-rectangle-list text-xs"></i>
                </button>

                <button
                    @click="showSlideOver = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-full transition"
                >
                    <i class="fas fa-plus mr-1"></i> Add Communication
                </button>

                <button class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
            </div>
        </div>

        <div class="px-5 pb-4 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-md overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Date</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Department/Stakeholder</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">From</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Subject</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">For/To</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Attachment</th>
                            <th class="px-3 py-3 font-semibold w-10"></th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($communications as $communication)
                            @php
                                $viewerData = [
                                    'ref_no' => $communication->ref_no,
                                    'communication_date' => $communication->communication_date,
                                    'department_stakeholder' => $communication->department_stakeholder,
                                    'from_name' => $communication->from_name,
                                    'to_for' => $communication->to_for,
                                    'status' => $communication->status,
                                    'subject' => $communication->subject,
                                    'message' => $communication->message,
                                    'cc' => $communication->cc,
                                    'attachment_url' => $communication->attachment ? asset('storage/' . $communication->attachment) : null,
                                ];
                            @endphp

                            <tr
                                class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer transition"
                                @click='selectedCommunication = @json($viewerData); showViewer = true'
                            >
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->ref_no }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->communication_date }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->department_stakeholder }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->from_name }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->subject }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->to_for }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">
                                    @php
                                        $status = $communication->status ?? 'Open';
                                        $statusClasses = match($status) {
                                            'Completed' => 'bg-green-50 text-green-700',
                                            'Overdue' => 'bg-red-50 text-red-700',
                                            default => 'bg-yellow-50 text-yellow-700',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $statusClasses }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 border-r border-gray-200">
                                    @if($communication->attachment)
                                        <a
                                            href="{{ asset('storage/' . $communication->attachment) }}"
                                            target="_blank"
                                            class="text-blue-600 hover:underline"
                                            @click.stop
                                        >
                                            View
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center text-gray-400">
                                    <button
                                        type="button"
                                        class="hover:text-gray-600"
                                        @click.stop='selectedCommunication = @json($viewerData); showViewer = true'
                                    >
                                        …
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-8 text-center text-gray-500">
                                    No Town Hall communications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2 flex items-center justify-between text-[10px] text-gray-500 px-1">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1">
                        Total Task
                        <span class="text-black font-medium">{{ $communications->total() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Open Task
                        <span class="text-yellow-500 font-medium">{{ $communications->where('status', 'Open')->count() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Completed
                        <span class="text-green-500 font-medium">{{ $communications->where('status', 'Completed')->count() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Overdue
                        <span class="text-red-500 font-medium">{{ $communications->where('status', 'Overdue')->count() }}</span>
                    </span>
                </div>

                <div class="flex items-center gap-5">
                    <span class="flex items-center gap-1">
                        Records per page
                        <select class="bg-transparent text-gray-600 outline-none">
                            <option>10</option>
                        </select>
                    </span>

                    <span>
                        {{ $communications->firstItem() ?? 0 }} to {{ $communications->lastItem() ?? 0 }}
                    </span>
                </div>
            </div>

            @if(method_exists($communications, 'links'))
                <div class="mt-3">
                    {{ $communications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editorEl = document.getElementById('editor');
    const hiddenInput = document.getElementById('message');
    const form = document.getElementById('townhall-form');

    if (editorEl && hiddenInput && form) {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write the formal communication here...',
            modules: {
                toolbar: [
                    [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        const oldMessage = {!! json_encode(old('message')) !!};

        if (oldMessage) {
            quill.root.innerHTML = oldMessage;
        } else {
            quill.root.innerHTML = `<p></p>`;
        }

        form.addEventListener('submit', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
    }
});
</script>
@endpush
