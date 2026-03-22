@extends('layouts.app')

@section('content')
@php
    $today = now()->toDateString();
    $currentUser = auth()->user()?->name ?? '';
    $defaultNoticeBody = <<<'HTML'
<p><strong>Agenda:</strong></p>
<ol>
    <li>Invocation</li>
    <li>Call to Order</li>
    <li>Proof of Notice</li>
    <li>Determination of Quorum</li>
    <li>Reading and Approval of the Previous Minutes</li>
    <li>Matters for Discussion and Approval</li>
    <li>Other Business</li>
    <li>Adjournment</li>
</ol>
<p>In the instance that the meeting shall be conducted in-person, the minutes of the meeting shall be properly documented and securely stored as part of the official corporate records in accordance with applicable corporate governance requirements.</p>
<p>Directors or stockholders intending to participate via video conferencing are requested to inform the Presiding Officer and the Corporate Secretary in advance so the quorum and participation records may be properly documented.</p>
HTML;
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" x-data="noticeComposer()" @keydown.escape.window="showAddPanel = false">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <div class="text-lg font-semibold">Notices of Meeting</div>
            <div class="flex-1"></div>
            <button type="button" @click="openPanel()" class="h-9 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add Notice
            </button>
        </div>

        <div class="p-4">
            <div class="overflow-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notice #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Meeting</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Schedule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Body / File</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Linked Records</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-900">
                        @forelse ($notices as $notice)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('notices.preview', $notice) }}'">
                                <td class="px-4 py-3 font-medium">{{ $notice->notice_number ?: 'Draft Notice' }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ $notice->governing_body }}</div>
                                    <div class="text-xs text-gray-500">{{ $notice->type_of_meeting }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ optional($notice->date_of_meeting)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $notice->time_started }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $notice->location }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $notice->document_path ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $notice->document_path ? 'Uploaded PDF' : 'Built in editor' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    Minutes: {{ $notice->minutes->count() }}<br>
                                    Resolutions: {{ $notice->resolutions->count() }}<br>
                                    Sec. Certs: {{ $notice->secretaryCertificates->count() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No notices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-cloak>
        <div x-show="showAddPanel" class="fixed inset-0 bg-black/40 z-40" @click="showAddPanel = false"></div>
        <div x-show="showAddPanel"
            class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div>
                    <div class="text-lg font-semibold">Add Notice</div>
                    <div class="text-xs text-gray-500">Upload the original PDF or compose the notice body here.</div>
                </div>
                <div class="flex-1"></div>
                <button class="text-gray-500 hover:text-gray-700" @click="showAddPanel = false" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('notices.store') }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-6" @submit="syncBody()">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-600">Notice #</label>
                        <input type="text" name="notice_number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="NOTICE-2026-001">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Notice</label>
                        <input type="date" name="date_of_notice" value="{{ $today }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Governing Body</label>
                        <select name="governing_body" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="Stockholders">Stockholders</option>
                            <option value="Board of Directors">Board of Directors</option>
                            <option value="Joint Stockholders and Board of Directors">Joint Stockholders and Board of Directors</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Type of Meeting</label>
                        <select name="type_of_meeting" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting Date</label>
                        <input type="date" name="date_of_meeting" value="{{ $today }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Time</label>
                        <input type="time" name="time_started" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                        <div>
                            <label class="text-xs text-gray-600">Meeting Location</label>
                            <p class="mt-1 text-xs text-gray-500">Fill in the compliant venue details below. These will be combined into the saved location field.</p>
                        </div>
                        <input type="hidden" name="location" x-ref="locationField">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600">1. Venue Name</label>
                                <input type="text" x-model="locationParts.venue" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="ABC Building">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">2. Room / Floor</label>
                                <input type="text" x-model="locationParts.room" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="3rd Floor, Conference Room A">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">3. Street Address</label>
                                <input type="text" x-model="locationParts.street" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="123 Cardinal Rosales Ave.">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">4. City / Municipality</label>
                                <input type="text" x-model="locationParts.city" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Cebu City">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">5. Province</label>
                                <input type="text" x-model="locationParts.province" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Cebu">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">6. Country</label>
                                <input type="text" x-model="locationParts.country" @input="syncLocation()" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Philippines">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Saved Location Preview</label>
                            <div class="mt-1 rounded-md border border-dashed border-gray-300 bg-white px-3 py-2 text-sm text-gray-700" x-text="locationPreview || 'Location will be generated from the fields above.'"></div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Meeting #</label>
                        <input type="text" name="meeting_no" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="25th Annual Meeting">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Chairman</label>
                        <input type="text" name="chairman" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Chairman">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Secretary</label>
                        <input type="text" name="secretary" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Corporate Secretary">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Uploaded By</label>
                        <input type="text" name="uploaded_by" value="{{ $currentUser }}" data-default-field="current_user" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Uploader">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Date Updated</label>
                        <input type="date" name="date_updated" value="{{ $today }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Upload Notice (PDF)</label>
                        <input type="file" name="document_path" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700" @change="bodyMode = 'upload'">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Body Source</label>
                        <select name="body_mode" x-model="bodyMode" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <option value="builder">Create in slider</option>
                            <option value="upload">Use uploaded PDF</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center gap-2 bg-gray-50">
                        <span class="text-sm font-semibold text-gray-900">Notice Body Builder</span>
                        <div class="flex-1"></div>
                        <select x-model="fontName" @change="exec('fontName', fontName)" class="rounded-lg border border-gray-300 px-2 py-1 text-xs">
                            <option value="Arial">Sans Serif</option>
                            <option value="Times New Roman">Serif</option>
                            <option value="Georgia">Georgia</option>
                        </select>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs" @click="exec('bold')">B</button>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs italic" @click="exec('italic')">I</button>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs underline" @click="exec('underline')">U</button>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs" @click="exec('insertUnorderedList')">List</button>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs" @click="exec('justifyLeft')">Left</button>
                        <button type="button" class="px-2 py-1 rounded border border-gray-300 text-xs" @click="exec('createLink', prompt('Enter link URL'))">Link</button>
                    </div>
                    <div x-ref="editor" contenteditable="true" class="min-h-[280px] p-4 text-sm outline-none bg-white" @input="bodyMode = 'builder'">
                        {!! $defaultNoticeBody !!}
                    </div>
                    <textarea x-ref="bodyField" name="body_html" class="hidden"></textarea>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 -mx-6 -mb-6">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 text-sm font-medium rounded-lg" @click="showAddPanel = false" type="button">
                        Cancel
                    </button>
                    <div class="flex-1"></div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg" type="submit">
                        Save Notice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function noticeComposer() {
        return {
            showAddPanel: false,
            bodyMode: 'builder',
            fontName: 'Arial',
            locationParts: {
                venue: '',
                room: '',
                street: '',
                city: '',
                province: '',
                country: 'Philippines',
            },
            locationPreview: '',
            defaultBodyHtml: @js($defaultNoticeBody),
            openPanel() {
                this.showAddPanel = true;
                this.$nextTick(() => {
                    if (this.$refs.editor) {
                        this.$refs.editor.innerHTML = this.defaultBodyHtml;
                    }
                    this.syncBody();
                    this.syncLocation();
                });
            },
            exec(command, value = null) {
                if (value === null || value === '') {
                    document.execCommand(command, false);
                } else {
                    document.execCommand(command, false, value);
                }
                this.syncBody();
                this.$refs.editor.focus();
            },
            syncBody() {
                if (this.$refs.bodyField && this.$refs.editor) {
                    this.$refs.bodyField.value = this.$refs.editor.innerHTML;
                }
            },
            syncLocation() {
                const parts = [
                    this.locationParts.venue,
                    this.locationParts.room,
                    this.locationParts.street,
                    this.locationParts.city,
                    this.locationParts.province,
                    this.locationParts.country || 'Philippines',
                ].map((value) => (value || '').trim()).filter(Boolean);

                this.locationPreview = parts.join(', ');

                if (this.$refs.locationField) {
                    this.$refs.locationField.value = this.locationPreview;
                }
            },
        };
    }
</script>
@endsection
