@extends('layouts.app')

@section('content')
<div x-data="{ 
        showSlideOver: false,
        currentType: 'PNL',

        form: {
            client: '',
            tin: '',
            date: ''
        },

        accountingData: {
            'PNL': [
                { date: '2024-04-22', user: 'Jasper Bulac', client: 'Benthel', tin: '123-456-756', status: 'Completed' }
            ],
            'Balance Sheet': [],
            'Cash Flow': [],
            'Income Statement': [],
            'AFS': []
        },

        addEntry() {
            if(!this.form.client || !this.form.tin || !this.form.date){
                alert('Fill all fields');
                return;
            }

            this.accountingData[this.currentType].push({
                date: this.form.date,
                user: 'You',
                client: this.form.client,
                tin: this.form.tin,
                status: 'Open'
            });

            this.form.client = '';
            this.form.tin = '';
            this.form.date = '';

            this.showSlideOver = false;
        }
    }" 
    class="w-full px-6 mt-4">

    {{-- SLIDE OVER --}}
    <div x-show="showSlideOver" class="fixed inset-0 z-50" x-cloak>
        <div class="absolute inset-0 bg-black/50" @click="showSlideOver=false"></div>

        <div class="absolute right-0 top-0 h-full w-full max-w-sm bg-white shadow-xl flex flex-col">
            <div class="p-6 border-b flex justify-between">
                <h2 class="font-bold">Add Entry</h2>
                <button @click="showSlideOver=false">✕</button>
            </div>

            <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                <input x-model="form.client" placeholder="Client" class="w-full border p-2 rounded">
                <input x-model="form.tin" placeholder="TIN" class="w-full border p-2 rounded">
                <input type="date" x-model="form.date" class="w-full border p-2 rounded">
            </div>

            <div class="p-6 border-t flex gap-2">
                <button @click="showSlideOver=false" class="flex-1 border rounded py-2">Cancel</button>
                <button @click="addEntry()" class="flex-1 bg-blue-600 text-white rounded py-2">Save</button>
            </div>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="bg-white rounded-xl border">

        {{-- TOP BAR --}}
        <div class="flex justify-between items-center p-4 border-b">

            {{-- DROPDOWN --}}
            <div class="relative">
                <button @click="$refs.menu.classList.toggle('hidden')" 
                        class="px-4 py-2 bg-gray-100 rounded">
                    <span x-text="currentType"></span> ▾
                </button>

                <div x-ref="menu" class="hidden absolute mt-2 w-48 bg-white border rounded shadow z-50">
                    <template x-for="type in Object.keys(accountingData)">
                        <div @click="currentType = type; $refs.menu.classList.add('hidden')" 
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                             x-text="type">
                        </div>
                    </template>
                </div>
            </div>

            {{-- ADD --}}
            <button @click="showSlideOver = true"
                class="bg-blue-600 text-white px-5 py-2 rounded">
                + Add
            </button>
        </div>

        {{-- TABLE --}}
        <div class="p-4">
            <div class="border rounded overflow-hidden">
                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">Uploader</th>
                            <th class="p-3 text-left">Client</th>
                            <th class="p-3 text-left">TIN</th>
                            <th class="p-3 text-right">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="item in accountingData[currentType]" :key="item.date">
                            <tr class="border-t">
                                <td class="p-3" x-text="item.date"></td>
                                <td class="p-3" x-text="item.user"></td>
                                <td class="p-3" x-text="item.client"></td>
                                <td class="p-3" x-text="item.tin"></td>
                                <td class="p-3 text-right"
                                    :class="{
                                        'text-green-600': item.status==='Completed',
                                        'text-yellow-500': item.status==='Open',
                                        'text-red-500': item.status==='Overdue'
                                    }"
                                    x-text="item.status">
                                </td>
                            </tr>
                        </template>

                        <template x-if="accountingData[currentType].length === 0">
                            <tr>
                                <td colspan="5" class="text-center p-6 text-gray-400">
                                    No data found
                                </td>
                            </tr>
                        </template>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection