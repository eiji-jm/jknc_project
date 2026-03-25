@extends('layouts.app')

@section('content')
<div id="townhall-page" class="w-full h-full px-6 py-5" x-data="{
    showSlideOver: false,
    previewRef: 'AUTO-INCREMENT',
    previewDate: '',
    previewFrom: '{{ Auth::user()->name }}',
    previewDepartment: '',
    previewRecipientLabel: 'To',
    previewTo: '',
    previewPriority: 'Low',
    previewSubject: '',
    previewBody: '<p style=&quot;color:#9ca3af;&quot;>Write the formal communication here...</p>',
    previewCc: '',
    previewAdditional: '',
    previewExpiry: ''
}">

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

    @if(Auth::user()->hasPermission('create_townhall'))
    <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-black/40" @click="showSlideOver = false"></div>

        <div class="absolute inset-0 flex">
            {{-- LEFT PREVIEW PANEL --}}
            <div
                x-show="showSlideOver"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="w-[70%] h-full bg-[#f5f6f8] overflow-y-auto p-6 border-r border-gray-200"
            >
                <div class="max-w-[850px] mx-auto mb-4 flex justify-end sticky top-0 z-10">
                    <button
                        type="button"
                        id="download-preview-pdf"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow transition"
                    >
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </button>
                </div>

                <div class="max-w-[850px] mx-auto">
                    <div id="memo-preview-pdf" class="memo-preview bg-white border border-gray-300 shadow min-h-[1100px] px-[72px] py-[72px]">

                        {{-- LETTERHEAD --}}
                        <div class="flex items-start justify-between border-b border-gray-300 pb-6 mb-8">
                            <div>
                                <h1 class="text-[22px] font-bold tracking-wide text-gray-900">JOHN KELLY &amp; COMPANY</h1>
                                <p class="text-[12px] text-gray-500 mt-1">Corporate Memo Preview</p>
                            </div>

                            <div class="text-right text-[12px] text-gray-600 leading-5">
                                <p>Ref No: <span class="font-semibold" x-text="previewRef"></span></p>
                                <p>Date: <span class="font-semibold" x-text="previewDate || '________________'"></span></p>
                            </div>
                        </div>

                        {{-- MEMO TITLE --}}
                        <div class="text-center mb-8">
                            <h2 class="text-[20px] font-bold tracking-[0.18em] text-gray-900">MEMORANDUM</h2>
                        </div>

                        {{-- META --}}
                        <div class="space-y-3 text-[14px] text-gray-800 mb-10">
                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide" x-text="previewRecipientLabel"></p>
                                <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewTo || '______________________________'"></p>
                            </div>

                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide">From</p>
                                <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewFrom || '______________________________'"></p>
                            </div>

                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide">Department</p>
                                <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewDepartment || '______________________________'"></p>
                            </div>

                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide">Priority</p>
                                <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewPriority || 'Low'"></p>
                            </div>

                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide">Subject</p>
                                <p class="border-b border-dotted border-gray-300 pb-1 font-semibold" x-text="previewSubject || '______________________________'"></p>
                            </div>

                            <div class="grid grid-cols-[120px_1fr] gap-3">
                                <p class="font-semibold uppercase tracking-wide">Expiry</p>
                                <p class="border-b border-dotted border-gray-300 pb-1" x-text="previewExpiry || '______________________________'"></p>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="text-[15px] leading-8 text-gray-900 min-h-[420px]">
                            <div class="prose prose-sm max-w-none [&_p]:my-4 [&_p]:leading-8 [&_ul]:my-4 [&_ol]:my-4" x-html="previewBody"></div>
                        </div>

                        {{-- SIGNATURE AREA --}}
                        <div class="mt-16 space-y-10 text-[14px] text-gray-800">
                            <div>
                                <p>Respectfully,</p>
                                <div class="mt-12 border-b border-gray-400 w-[260px]"></div>
                                <p class="mt-2 font-semibold" x-text="previewFrom || '________________'"></p>
                            </div>

                            <div class="pt-6 border-t border-gray-200 space-y-2">
                                <p><span class="font-semibold">CC:</span> <span x-text="previewCc || '______________________________'"></span></p>
                                <p><span class="font-semibold">Additional:</span> <span x-text="previewAdditional || '______________________________'"></span></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RIGHT FORM PANEL --}}
            <div
                x-show="showSlideOver"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-[30%] h-full bg-white shadow-2xl flex flex-col"
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
                                x-model="previewDate"
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
                                value="{{ Auth::user()->name }}"
                                x-model="previewFrom"
                                readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                            >
                            <p class="mt-1 text-xs text-gray-400">Automatically set based on signed-in user</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Department / Stakeholder</label>
                            <input
                                type="text"
                                name="department_stakeholder"
                                x-model="previewDepartment"
                                value="{{ old('department_stakeholder') }}"
                                placeholder="Enter department or stakeholder"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Recipient Label and Value</label>

                        <div class="grid grid-cols-[120px_1fr] gap-3">
                            <select
                                x-model="previewRecipientLabel"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            >
                                <option value="To">To</option>
                                <option value="For">For</option>
                            </select>

                            <input
                                type="text"
                                name="to_for"
                                x-model="previewTo"
                                value="{{ old('to_for') }}"
                                placeholder="Enter recipient"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            >
                        </div>

                        <input type="hidden" name="recipient_label" :value="previewRecipientLabel">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Priority</label>
                        <select
                            name="priority"
                            x-model="previewPriority"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                            <option value="Low">Low</option>
                            <option value="High">High</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
                        <input
                            type="text"
                            name="subject"
                            x-model="previewSubject"
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
                                x-model="previewCc"
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
                                x-model="previewAdditional"
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

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Expiry Date & Time</label>
                        <input
                            type="datetime-local"
                            name="expires_at"
                            x-model="previewExpiry"
                            value="{{ old('expires_at') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                        >
                        <p class="mt-1 text-xs text-gray-400">
                            Communication will automatically archive after this date and time
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
    @endif

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

                @if(Auth::user()->hasPermission('create_townhall'))
                    <button
                        @click="showSlideOver = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-full transition"
                    >
                        <i class="fas fa-plus mr-1"></i> Add Communication
                    </button>
                @endif

                <button class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
            </div>
        </div>

        <div class="px-5 pb-4 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-md overflow-hidden flex-1 overflow-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Date</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Expiry</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Department/Stakeholder</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">From</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Subject</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">For/To</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Priority</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Approval</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Attachment</th>
                            <th class="px-3 py-3 font-semibold w-10"></th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($communications as $communication)
                            <tr
                                class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer transition"
                                onclick="window.location='{{ route('townhall.show', $communication->id) }}'"
                            >
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->ref_no }}</td>

                                <td class="px-3 py-3 border-r border-gray-200">
                                    {{ $communication->communication_date
                                        ? \Carbon\Carbon::parse($communication->communication_date)->format('M d, Y')
                                        : '—' }}
                                </td>

                                <td class="px-3 py-3 border-r border-gray-200">
                                    @if($communication->expires_at)
                                        <div>{{ \Carbon\Carbon::parse($communication->expires_at)->format('M d, Y') }}</div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ \Carbon\Carbon::parse($communication->expires_at)->format('h:i A') }}
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->department_stakeholder }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->from_name }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">{{ $communication->subject }}</td>
                                <td class="px-3 py-3 border-r border-gray-200">
                                    {{ ($communication->recipient_label ?? 'To') . ': ' . ($communication->to_for ?? '') }}
                                </td>

                                <td class="px-3 py-3 border-r border-gray-200">
                                    @php
                                        $priority = $communication->priority ?? 'Low';
                                        $classes = $priority === 'High'
                                            ? 'bg-red-50 text-red-700'
                                            : 'bg-green-50 text-green-700';
                                    @endphp

                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $classes }}">
                                        {{ $priority }}
                                    </span>
                                </td>

                                <td class="px-3 py-3 border-r border-gray-200">
                                    @if($communication->is_archived)
                                        <span class="px-2 py-1 text-xs rounded-full font-medium bg-gray-200 text-gray-700">
                                            Expired
                                        </span>
                                    @else
                                        @php
                                            $approval = $communication->approval_status ?? 'Pending';
                                            $approvalClasses = match($approval) {
                                                'Approved' => 'bg-green-50 text-green-700',
                                                'Rejected' => 'bg-red-50 text-red-700',
                                                'Needs Revision' => 'bg-blue-50 text-blue-700',
                                                default => 'bg-yellow-50 text-yellow-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full font-medium {{ $approvalClasses }}">
                                            {{ $approval }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 py-3 border-r border-gray-200">
                                    @if($communication->attachment)
                                        <a
                                            href="{{ asset('storage/' . $communication->attachment) }}"
                                            target="_blank"
                                            class="text-blue-600 hover:underline"
                                            onclick="event.stopPropagation()"
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
                                        onclick="event.stopPropagation(); window.location='{{ route('townhall.show', $communication->id) }}'"
                                    >
                                        …
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-3 py-8 text-center text-gray-500">
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
                        Pending
                        <span class="text-yellow-600 font-medium">{{ $communications->where('approval_status', 'Pending')->count() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Approved
                        <span class="text-green-600 font-medium">{{ $communications->where('approval_status', 'Approved')->count() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Needs Revision
                        <span class="text-blue-600 font-medium">{{ $communications->where('approval_status', 'Needs Revision')->count() }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        Rejected
                        <span class="text-red-600 font-medium">{{ $communications->where('approval_status', 'Rejected')->count() }}</span>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
        const rootEl = document.getElementById('townhall-page');
        const alpineData = rootEl ? Alpine.$data(rootEl) : null;

        if (oldMessage) {
            quill.root.innerHTML = oldMessage;
            hiddenInput.value = oldMessage;

            if (alpineData) {
                alpineData.previewBody = oldMessage;
            }
        } else {
            const defaultHtml = '<p style="color:#9ca3af;">Write the formal communication here...</p>';
            quill.root.innerHTML = '';
            hiddenInput.value = '';

            if (alpineData) {
                alpineData.previewBody = defaultHtml;
            }
        }

        quill.on('text-change', function () {
            const html = quill.root.innerHTML;
            hiddenInput.value = html;

            if (alpineData) {
                alpineData.previewBody = quill.getText().trim()
                    ? html
                    : '<p style="color:#9ca3af;">Write the formal communication here...</p>';
            }
        });

        form.addEventListener('submit', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
    }

    const downloadBtn = document.getElementById('download-preview-pdf');

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            const element = document.getElementById('memo-preview-pdf');
            if (!element) return;

            const subject = document.querySelector('input[name="subject"]')?.value?.trim() || 'townhall-memo';
            const safeFileName = subject
                .replace(/[\\/:*?"<>|]+/g, '')
                .replace(/\s+/g, '-')
                .toLowerCase();

            html2pdf().set({
                margin: [0.3, 0.3, 0.3, 0.3],
                filename: `${safeFileName}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(element).save();
        });
    }
});
</script>
@endpush
