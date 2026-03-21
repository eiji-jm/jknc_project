@extends('layouts.app')

@section('content')
<div x-data="{ 
    showSlideOver: false,
    currentType: 'Letters',
    selectedItem: null,

    emptyForm: {
        client: '',
        tin: '',
        subject: '',
        from: '',
        to: '',
        department: '',
        details: '',
        date: '',
        time: '',
        deadline: '',
        sent_via: 'Email'
    },

    form: {
        client: '',
        tin: '',
        subject: '',
        from: '',
        to: '',
        department: '',
        details: '',
        date: '',
        time: '',
        deadline: '',
        sent_via: 'Email'
    },

    correspondenceData: {
        'Letters': [],
        'Demand Letter': [],
        'Request Letter': [],
        'Follow Up Letter': [],
        'Memo': [],
        'Notice': []
    },

    resetForm() {
        this.form = JSON.parse(JSON.stringify(this.emptyForm));
    },

    generateCurrentDateTime() {
        const now = new Date();

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        this.form.date = `${year}-${month}-${day}`;
        this.form.time = `${hours}:${minutes}`;
    },

    openAddModal() {
        this.selectedItem = null;
        this.resetForm();
        this.generateCurrentDateTime();
        this.showSlideOver = true;
    },

    openViewModal(item) {
        this.selectedItem = item;
        this.showSlideOver = true;
    },

    closeSlideOver() {
        this.showSlideOver = false;
        this.selectedItem = null;
        this.resetForm();
    },

    async fetchData(type) {
        const res = await fetch(`/correspondence/${encodeURIComponent(type)}`);
        this.correspondenceData[type] = await res.json();
    },

    async addEntry() {
        if (!this.form.client || !this.form.subject || !this.form.from || !this.form.to) {
            alert('Please fill required fields');
            return;
        }

        const res = await fetch('/correspondence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                type: this.currentType,
                client: this.form.client,
                tin: this.form.tin,
                subject: this.form.subject,
                from: this.form.from,
                to: this.form.to,
                department: this.form.department,
                details: this.form.details,
                date: this.form.date,
                time: this.form.time,
                deadline: this.form.deadline,
                sent_via: this.form.sent_via
            })
        });

        const data = await res.json();

        if (!res.ok) {
            console.error(data);
            alert('Failed to save correspondence.');
            return;
        }

        if (!this.correspondenceData[this.currentType]) {
            this.correspondenceData[this.currentType] = [];
        }

        this.correspondenceData[this.currentType].unshift(data);

        this.selectedItem = null;
        this.resetForm();
        this.showSlideOver = false;
    }
}" x-init="fetchData(currentType)" class="w-full px-6 mt-4">

    <div class="bg-white rounded-xl border">
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

            <button @click="openAddModal()" class="bg-blue-600 text-white px-5 py-2 rounded text-sm">
                + Add
            </button>
        </div>

        <div class="p-4 overflow-auto">
            <table class="w-full text-xs border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border p-2">Date Uploaded</th>
                        <th class="border p-2">Uploaded By</th>
                        <th class="border p-2">Client</th>
                        <th class="border p-2">TIN</th>
                        <th class="border p-2">Type</th>
                        <th class="border p-2">Date Sent</th>
                        <th class="border p-2">Time Sent</th>
                        <th class="border p-2">Department</th>
                        <th class="border p-2">From</th>
                        <th class="border p-2">For/To</th>
                        <th class="border p-2">Subject</th>
                        <th class="border p-2">Respond Before</th>
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
                            <td class="border p-2" x-text="item.sent_via"></td>
                            <td class="border p-2" x-text="item.status"></td>
                            <td class="border p-2">
                                <button
                                    @click="openViewModal(item)"
                                    class="text-blue-600 font-semibold underline">
                                    View
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="(correspondenceData[currentType] || []).length === 0">
                        <tr>
                            <td colspan="15" class="border p-4 text-center text-gray-500">
                                No correspondence records found.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showSlideOver" class="fixed inset-0 z-50 flex" x-cloak>
        <div class="absolute inset-0 bg-black/35" @click="closeSlideOver()"></div>

        <div class="relative flex-1 h-full overflow-auto">
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
                                    <span class="font-normal" x-text="selectedItem.date || ''"></span>
                                </p>

                                <div class="pt-2">
                                    <p class="font-bold">TO:</p>
                                    <p x-text="selectedItem.to || ''"></p>
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
                                    <span class="font-semibold" x-text="selectedItem.details || 'the matter stated above'"></span>.
                                </p>

                                <p>
                                    It has been recorded under the
                                    <span class="font-semibold" x-text="selectedItem.department || 'concerned'"></span>
                                    department.
                                </p>

                                <p x-show="selectedItem.deadline">
                                    Please respond before
                                    <span class="font-semibold" x-text="selectedItem.deadline"></span>.
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
                        No preview yet. Fill out the form to create a new correspondence.
                    </div>
                </template>
            </div>
        </div>

        <div class="relative ml-auto w-full max-w-md h-full bg-white shadow-2xl overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-2xl font-bold" x-text="selectedItem ? 'View Correspondence' : 'Add Correspondence'"></h2>
                <button @click="closeSlideOver()" class="text-2xl leading-none text-gray-500 hover:text-black">
                    &times;
                </button>
            </div>

            <div class="p-6 space-y-3">
                <input type="text" placeholder="Client" x-model="form.client" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                <input type="text" placeholder="TIN" x-model="form.tin" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                <input type="text" placeholder="Subject" x-model="form.subject" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                <input type="text" placeholder="From" x-model="form.from" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                <input type="text" placeholder="To" x-model="form.to" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                <input type="text" placeholder="Department" x-model="form.department" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">

                <textarea
                    placeholder="This correspondence is issued regarding..."
                    x-model="form.details"
                    class="w-full border px-3 py-3 rounded-md min-h-[120px]"
                    :disabled="selectedItem"></textarea>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Sent</label>
                    <input type="date" x-model="form.date" class="w-full border px-3 py-3 rounded-md bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Sent</label>
                    <input type="time" x-model="form.time" class="w-full border px-3 py-3 rounded-md bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Please Respond Before</label>
                    <input type="date" x-model="form.deadline" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                </div>

                <select x-model="form.sent_via" class="w-full border px-3 py-3 rounded-md" :disabled="selectedItem">
                    <option>Email</option>
                    <option>LBC</option>
                    <option>Internal</option>
                </select>
            </div>

            <div class="p-6 border-t flex justify-end gap-3">
                <button @click="closeSlideOver()" class="px-5 py-2 border rounded-md hover:bg-gray-100">
                    {{ __('Close') }}
                </button>

                <template x-if="!selectedItem">
                    <button @click="addEntry()" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection