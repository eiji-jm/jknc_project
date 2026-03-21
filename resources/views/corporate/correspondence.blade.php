@extends('layouts.app')

@section('content')
<div x-data="{ 
    showSlideOver: false,
    currentType: 'Letters',
    selectedItem: null,

    form: {
        client: '', tin: '', subject: '', from: '', to: '',
        department: '', date: '', time: '',
        deadline: '', period: '', response_date:'', sent_via:'Email'
    },

    correspondenceData: {
        'Letters': [],
        'Demand Letter': [],
        'Request Letter': [],
        'Follow Up Letter': [],
        'Memo': [],
        'Notice': []
    },

    async fetchData(type) {
        const res = await fetch(`/correspondence/${encodeURIComponent(type)}`);
        this.correspondenceData[type] = await res.json();
    },

    async addEntry() {
        if (!this.form.client || !this.form.subject) {
            alert('Please fill required fields');
            return;
        }

        const res = await fetch('/correspondence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type: this.currentType,
                client: this.form.client,
                tin: this.form.tin,
                subject: this.form.subject,
                from: this.form.from,
                to: this.form.to,
                department: this.form.department,
                date: this.form.date,
                time: this.form.time,
                deadline: this.form.deadline,
                period: this.form.period,
                response_date: this.form.response_date,
                sent_via: this.form.sent_via
            })
        });

        const data = await res.json();

        if (!this.correspondenceData[this.currentType]) {
            this.correspondenceData[this.currentType] = [];
        }

        this.correspondenceData[this.currentType].unshift(data);
        this.selectedItem = data;

        this.form = {
            client: '', tin: '', subject: '', from: '', to: '',
            department: '', date: '', time: '',
            deadline: '', period: '', response_date:'', sent_via:'Email'
        };

        this.showSlideOver = false;
    }
}" x-init="fetchData(currentType)" class="w-full px-6 mt-4">

    <div class="bg-white rounded-xl border">
        {{-- TOP BAR --}}
        <div class="flex justify-between p-4 border-b">
            <div class="relative">
                <button @click="$refs.menu.classList.toggle('hidden')" class="px-4 py-2 bg-gray-100 rounded text-sm">
                    <span x-text="currentType"></span> ▾
                </button>

                <div x-ref="menu" class="hidden absolute mt-2 bg-white border rounded shadow w-56 z-10">
                    <template x-for="type in Object.keys(correspondenceData)" :key="type">
                        <div
                            @click="currentType = type; $refs.menu.classList.add('hidden'); fetchData(type)"
                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                            x-text="type">
                        </div>
                    </template>
                </div>
            </div>

            <button @click="showSlideOver = true" class="bg-blue-600 text-white px-5 py-2 rounded text-sm">
                + Add
            </button>
        </div>

        {{-- TABLE --}}
        <div class="p-4 overflow-auto">
            <table class="w-full text-xs border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border p-2">Date Uploaded</th>
                        <th class="border p-2">Uploaded By</th>
                        <th class="border p-2">Client</th>
                        <th class="border p-2">TIN</th>
                        <th class="border p-2">Type</th>
                        <th class="border p-2">Date</th>
                        <th class="border p-2">Time</th>
                        <th class="border p-2">Department</th>
                        <th class="border p-2">From</th>
                        <th class="border p-2">For/To</th>
                        <th class="border p-2">Subject</th>
                        <th class="border p-2">Deadline</th>
                        <th class="border p-2">Period</th>
                        <th class="border p-2">Response Date</th>
                        <th class="border p-2">Sent Via</th>
                        <th class="border p-2">Status</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in correspondenceData[currentType] || []" :key="item.id">
                        <tr>
                            <td class="border p-2" x-text="item.uploaded_date"></td>
                            <td class="border p-2" x-text="item.user"></td>
                            <td class="border p-2" x-text="item.client"></td>
                            <td class="border p-2" x-text="item.tin"></td>
                            <td class="border p-2" x-text="item.type"></td>
                            <td class="border p-2" x-text="item.date"></td>
                            <td class="border p-2" x-text="item.time"></td>
                            <td class="border p-2" x-text="item.department"></td>
                            <td class="border p-2" x-text="item.from"></td>
                            <td class="border p-2" x-text="item.to"></td>
                            <td class="border p-2" x-text="item.subject"></td>
                            <td class="border p-2" x-text="item.deadline"></td>
                            <td class="border p-2" x-text="item.period"></td>
                            <td class="border p-2" x-text="item.response_date"></td>
                            <td class="border p-2" x-text="item.sent_via"></td>
                            <td class="border p-2" x-text="item.status"></td>
                            <td class="border p-2">
                                <button
                                    @click="selectedItem = item; showSlideOver = true"
                                    class="text-blue-600 font-semibold underline">
                                    View
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- OVERLAY: PREVIEW LEFT + SLIDER RIGHT --}}
    <div x-show="showSlideOver" class="fixed inset-0 z-50 flex" x-cloak>
        {{-- dark background --}}
        <div class="absolute inset-0 bg-black/35" @click="showSlideOver = false"></div>

        {{-- preview area --}}
        <div class="relative flex-1 h-full overflow-auto pr-[0rem]">
            <div class="min-h-full flex items-start justify-center p-8">
                <template x-if="selectedItem">
                    <div class="bg-[#f3f4f6] w-full min-h-full flex items-start justify-center p-6">
                        <div class="bg-white shadow-2xl w-[210mm] min-h-[297mm] p-12 text-gray-800">
                            <div class="text-center mb-10">
                                <h1 class="text-2xl font-serif font-bold uppercase tracking-widest text-blue-900" x-text="selectedItem.type || 'DOCUMENT'"></h1>
                                <hr class="border-t-2 border-blue-900 mt-2">
                            </div>

                            <div class="space-y-6 text-[15px] font-serif leading-7">
                                <p class="font-bold">
                                    Date:
                                    <span class="font-normal" x-text="selectedItem.date || selectedItem.uploaded_date || ''"></span>
                                </p>

                                <div class="pt-2">
                                    <p class="font-bold">TO:</p>
                                    <p x-text="selectedItem.to || selectedItem.client || ''"></p>
                                    <p x-show="selectedItem.client" x-text="selectedItem.client"></p>
                                    <p x-show="selectedItem.tin" x-text="'TIN: ' + selectedItem.tin"></p>
                                </div>

                                <div class="pt-2">
                                    <p class="font-bold">FROM:</p>
                                    <p x-text="selectedItem.from || selectedItem.user || ''"></p>
                                </div>

                                <div class="pt-2">
                                    <p class="font-bold underline uppercase" x-text="'SUBJECT: ' + (selectedItem.subject || '')"></p>
                                </div>

                                <p class="pt-2">Dear Sir/Madam,</p>

                                <p>
                                    This correspondence is issued regarding
                                    <span class="font-semibold" x-text="selectedItem.subject || 'the matter stated above'"></span>.
                                    It has been recorded under the
                                    <span class="font-semibold" x-text="selectedItem.department || 'concerned'"></span>
                                    department.
                                </p>

                                <p x-show="selectedItem.deadline">
                                    Please be advised that your response is expected on or before
                                    <span class="font-semibold" x-text="selectedItem.deadline"></span>.
                                </p>

                                <p x-show="selectedItem.period">
                                    The allowable response period is
                                    <span class="font-semibold" x-text="selectedItem.period"></span>.
                                </p>

                                <p x-show="selectedItem.response_date">
                                    Recorded response date:
                                    <span class="font-semibold" x-text="selectedItem.response_date"></span>.
                                </p>

                                <p>
                                    This correspondence was sent via
                                    <span class="font-semibold" x-text="selectedItem.sent_via || ''"></span>.
                                </p>

                                <p>Thank you for your immediate attention to this matter.</p>

                                <div class="pt-14">
                                    <p>Sincerely,</p>
                                    <br><br>
                                    <p class="font-bold border-t border-black w-56 pt-1" x-text="selectedItem.user || 'Authorized Representative'"></p>
                                    <p class="text-sm italic">Authorized Representative</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!selectedItem">
                    <div class="w-full h-full flex items-center justify-center text-gray-500 italic">
                        Select a correspondence entry to preview here.
                    </div>
                </template>
            </div>
        </div>

        {{-- right slide-over panel --}}
        <div class="relative ml-auto w-full max-w-md h-full bg-white shadow-2xl overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-2xl font-bold">Add Correspondence</h2>
                <button @click="showSlideOver = false" class="text-2xl leading-none text-gray-500 hover:text-black">
                    &times;
                </button>
            </div>

            <div class="p-6 space-y-3">
                <input type="text" placeholder="Client" x-model="form.client" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="TIN" x-model="form.tin" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="Subject" x-model="form.subject" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="From" x-model="form.from" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="To" x-model="form.to" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="Department" x-model="form.department" class="w-full border px-3 py-3 rounded-md">
                <input type="date" x-model="form.date" class="w-full border px-3 py-3 rounded-md">
                <input type="time" x-model="form.time" class="w-full border px-3 py-3 rounded-md">
                <input type="date" x-model="form.deadline" class="w-full border px-3 py-3 rounded-md">
                <input type="text" placeholder="Period" x-model="form.period" class="w-full border px-3 py-3 rounded-md">
                <input type="date" x-model="form.response_date" class="w-full border px-3 py-3 rounded-md">

                <select x-model="form.sent_via" class="w-full border px-3 py-3 rounded-md">
                    <option>Email</option>
                    <option>LBC</option>
                    <option>Internal</option>
                </select>
            </div>

            <div class="p-6 border-t flex justify-end gap-3">
                <button @click="showSlideOver = false" class="px-5 py-2 border rounded-md hover:bg-gray-100">
                    Cancel
                </button>
                <button @click="addEntry()" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection