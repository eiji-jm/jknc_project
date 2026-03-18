@extends('layouts.app')

@section('content')
<div x-data="{ 
    showSlideOver: false,
    showPdfModal: false,
    currentType: 'Letters',
    selectedItem: null, // Track which row was clicked

    form: {
        client: '', tin: '', subject: '', from: '', to: '',
        department: '', date: '', time: '',
        deadline: '', period: '', response_date: '', sent_via: 'Email'
    },

    correspondenceData: {
        'Letters': [
            { uploaded_date:'2024-03-01', user:'Admin_Sarah', client:'TechFlow Inc.', tin:'123-456', type:'Letters', date:'2024-03-01', time:'09:30', department:'BIR', from:'BIR Office', to:'TechFlow Inc.', subject:'Submission of Documents', deadline:'2024-03-10', period:'7 days', response_date:'2024-03-08', sent_via:'Email', status:'Completed' },
            { uploaded_date:'2024-03-05', user:'Admin_Mark', client:'Green Cafe', tin:'222-333', type:'Letters', date:'2024-03-05', time:'14:00', department:'LGU', from:'City Hall', to:'Green Cafe', subject:'Business Permit Reminder', deadline:'2024-03-12', period:'7 days', response_date:'', sent_via:'LBC', status:'Open' },
            { uploaded_date:'2024-03-07', user:'User_Anna', client:'Bright Path', tin:'555-111', type:'Letters', date:'2024-03-07', time:'11:20', department:'BIR', from:'Revenue Office', to:'Bright Path', subject:'Tax Filing Notice', deadline:'2024-03-15', period:'8 days', response_date:'', sent_via:'Email', status:'Open' }
        ],
        'Demand Letter': [
            { uploaded_date:'2024-02-10', user:'User_John', client:'Blue Logistics', tin:'444-555', type:'Demand Letter', date:'2024-02-10', time:'10:15', department:'Legal', from:'Law Office', to:'Blue Logistics', subject:'Outstanding Balance Notice', deadline:'2024-02-20', period:'10 days', response_date:'', sent_via:'LBC', status:'Overdue' },
            { uploaded_date:'2024-02-12', user:'Admin_Sarah', client:'Ocean View', tin:'998-776', type:'Demand Letter', date:'2024-02-12', time:'13:40', department:'Legal', from:'Legal Dept', to:'Ocean View', subject:'Payment Demand', deadline:'2024-02-22', period:'10 days', response_date:'', sent_via:'Email', status:'Overdue' },
            { uploaded_date:'2024-02-15', user:'Admin_Mark', client:'Old Mill Co.', tin:'777-888', type:'Demand Letter', date:'2024-02-15', time:'09:00', department:'Legal', from:'Law Firm', to:'Old Mill Co.', subject:'Final Demand Notice', deadline:'2024-02-25', period:'10 days', response_date:'', sent_via:'LBC', status:'Overdue' }
        ],
        'Request Letter': [
            { uploaded_date:'2024-01-20', user:'Admin_Sarah', client:'Apex Hardware', tin:'666-777', type:'Request Letter', date:'2024-01-20', time:'11:45', department:'Accounting', from:'Apex Hardware', to:'Accounting Dept', subject:'Request for Financial Statement', deadline:'2024-01-25', period:'5 days', response_date:'2024-01-24', sent_via:'Email', status:'Completed' },
            { uploaded_date:'2024-01-22', user:'User_Anna', client:'Quick Bite', tin:'222-333', type:'Request Letter', date:'2024-01-22', time:'10:00', department:'Accounting', from:'Quick Bite', to:'Finance', subject:'Request for Audit Report', deadline:'2024-01-27', period:'5 days', response_date:'2024-01-26', sent_via:'Email', status:'Completed' },
            { uploaded_date:'2024-01-25', user:'Admin_Mark', client:'Zenith Solar', tin:'123-456', type:'Request Letter', date:'2024-01-25', time:'15:10', department:'Accounting', from:'Zenith Solar', to:'Accounting Dept', subject:'Request for Billing Summary', deadline:'2024-01-30', period:'5 days', response_date:'', sent_via:'Email', status:'Open' }
        ],
        'Follow Up Letter': [
            { uploaded_date:'2024-03-12', user:'User_Anna', client:'Quick Bite', tin:'888-999', type:'Follow Up Letter', date:'2024-03-12', time:'15:20', department:'Accounting', from:'Accounting Dept', to:'Quick Bite', subject:'Follow-up on Pending Docs', deadline:'2024-03-18', period:'6 days', response_date:'', sent_via:'Email', status:'Open' },
            { uploaded_date:'2024-03-13', user:'Admin_Sarah', client:'TechFlow', tin:'111-222', type:'Follow Up Letter', date:'2024-03-13', time:'09:10', department:'BIR', from:'BIR', to:'TechFlow', subject:'Follow-up Tax Filing', deadline:'2024-03-19', period:'6 days', response_date:'', sent_via:'Email', status:'Open' },
            { uploaded_date:'2024-03-14', user:'Admin_Mark', client:'Green Cafe', tin:'333-444', type:'Follow Up Letter', date:'2024-03-14', time:'13:50', department:'LGU', from:'City Hall', to:'Green Cafe', subject:'Permit Follow-up', deadline:'2024-03-20', period:'6 days', response_date:'', sent_via:'LBC', status:'Open' }
        ],
        'Memo': [
            { uploaded_date:'2024-02-28', user:'Admin_Mark', client:'Internal', tin:'N/A', type:'Memo', date:'2024-02-28', time:'08:00', department:'Management', from:'Management', to:'All Staff', subject:'New Compliance Guidelines', deadline:'', period:'', response_date:'', sent_via:'Internal', status:'Completed' },
            { uploaded_date:'2024-03-02', user:'Admin_Sarah', client:'Internal', tin:'N/A', type:'Memo', date:'2024-03-02', time:'10:30', department:'HR', from:'HR Dept', to:'All Employees', subject:'Attendance Policy Update', deadline:'', period:'', response_date:'', sent_via:'Internal', status:'Completed' },
            { uploaded_date:'2024-03-04', user:'Admin_Mark', client:'Internal', tin:'N/A', type:'Memo', date:'2024-03-04', time:'09:00', department:'IT', from:'IT Dept', to:'All Staff', subject:'System Maintenance Notice', deadline:'', period:'', response_date:'', sent_via:'Internal', status:'Completed' }
        ],
        'Notice': [
            { uploaded_date:'2024-03-15', user:'Admin_Sarah', client:'Zenith Solar', tin:'101-202', type:'Notice', date:'2024-03-15', time:'13:10', department:'BIR', from:'BIR', to:'Zenith Solar', subject:'Tax Compliance Notice', deadline:'2024-03-25', period:'10 days', response_date:'', sent_via:'LBC', status:'Open' },
            { uploaded_date:'2024-03-16', user:'Admin_Mark', client:'Apex Hardware', tin:'303-404', type:'Notice', date:'2024-03-16', time:'11:00', department:'LGU', from:'City Hall', to:'Apex Hardware', subject:'Inspection Notice', deadline:'2024-03-26', period:'10 days', response_date:'', sent_via:'Email', status:'Open' },
            { uploaded_date:'2024-03-17', user:'User_Anna', client:'Blue Logistics', tin:'505-606', type:'Notice', date:'2024-03-17', time:'15:45', department:'BIR', from:'BIR', to:'Blue Logistics', subject:'Audit Notice', deadline:'2024-03-27', period:'10 days', response_date:'', sent_via:'LBC', status:'Open' }
        ]
    },

    addEntry() {
        if(!this.form.client || !this.form.subject){
            alert('Please fill required fields');
            return;
        }
        this.correspondenceData[this.currentType].push({
            uploaded_date: new Date().toISOString().split('T')[0],
            user: 'You',
            client: this.form.client,
            tin: this.form.tin,
            type: this.currentType,
            date: this.form.date,
            time: this.form.time,
            department: this.form.department,
            from: this.form.from,
            to: this.form.to,
            subject: this.form.subject,
            deadline: this.form.deadline,
            period: this.form.period,
            response_date: this.form.response_date,
            sent_via: this.form.sent_via,
            status: 'Open'
        });
        this.form = { client:'', tin:'', subject:'', from:'', to:'', department:'', date:'', time:'', deadline:'', period:'', response_date:'', sent_via:'Email' };
        this.showSlideOver = false;
    }
}" class="w-full px-6 mt-4">

    <div class="bg-white rounded-xl border">
        {{-- TOP BAR --}}
        <div class="flex justify-between p-4 border-b">
            <div class="relative">
                <button @click="$refs.menu.classList.toggle('hidden')" class="px-4 py-2 bg-gray-100 rounded text-sm">
                    <span x-text="currentType"></span> ▾
                </button>
                <div x-ref="menu" class="hidden absolute mt-2 bg-white border rounded shadow w-56 z-10">
                    <template x-for="type in Object.keys(correspondenceData)">
                        <div @click="currentType = type; $refs.menu.classList.add('hidden')" 
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                             x-text="type"></div>
                    </template>
                </div>
            </div>
            <button @click="showSlideOver=true" class="bg-blue-600 text-white px-5 py-2 rounded text-sm">
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
                    <template x-for="item in correspondenceData[currentType]" :key="item.subject + item.date">
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
                                {{-- Trigger the Modal & Save the Item Data --}}
                                <button @click="selectedItem = item; showPdfModal = true" class="text-blue-600 font-semibold underline">View</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- DYNAMIC HTML LETTER MODAL --}}
    <div x-show="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-60" x-cloak>
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[95vh] flex flex-col overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                <h3 class="font-bold text-lg" x-text="'Document Preview: ' + (selectedItem ? selectedItem.type : '')"></h3>
                <button @click="showPdfModal = false" class="text-gray-500 hover:text-black text-2xl">&times;</button>
            </div>
            
            {{-- This replaces the broken iframe with a styled letter --}}
            <div class="flex-grow overflow-auto p-12 bg-gray-200">
                <div class="bg-white shadow-2xl mx-auto p-16 w-[210mm] min-h-[297mm] text-gray-800" id="letter-content">
                    
                    {{-- Letter Header --}}
                    <div class="text-center mb-10">
                        <h1 class="text-2xl font-serif font-bold uppercase tracking-widest text-blue-900" x-text="selectedItem ? selectedItem.type : 'DOCUMENT'"></h1>
                        <hr class="border-t-2 border-blue-900 mt-2">
                    </div>

                    {{-- Letter Body --}}
                    <div class="space-y-6 text-base font-serif leading-relaxed">
                        <p class="font-bold">Date: <span x-text="selectedItem ? selectedItem.date : ''"></span></p>
                        
                        <div class="pt-4">
                            <p class="font-bold">TO:</p>
                            <p x-text="selectedItem ? selectedItem.client : ''"></p>
                            <p x-text="'TIN: ' + (selectedItem ? selectedItem.tin : '')"></p>
                        </div>

                        <div class="pt-4">
                            <p class="font-bold underline uppercase" x-text="'SUBJECT: ' + (selectedItem ? selectedItem.subject : '')"></p>
                        </div>

                        <p class="pt-4">Dear Sir/Madam,</p>

                        <p>This document serves as a formal notification regarding the matter stated in the subject line. Please be advised that your response is required by <span class="font-bold" x-text="selectedItem ? selectedItem.deadline : ''"></span>.</p>
                        
                        <p>We have recorded this entry under the <span x-text="selectedItem ? selectedItem.department : ''"></span> department. Failure to comply within the given period of <span x-text="selectedItem ? selectedItem.period : ''"></span> may result in further action.</p>

                        <p>Thank you for your immediate attention to this matter.</p>
                        
                        <div class="pt-12">
                            <p>Sincerely,</p>
                            <br><br>
                            <p class="font-bold border-t border-black w-48 pt-1" x-text="selectedItem ? selectedItem.user : 'Administrator'"></p>
                            <p class="text-sm italic">Authorized Representative</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer with Print Button --}}
            <div class="p-4 border-t bg-gray-50 text-right">
                <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-black transition">Print Document</button>
                <button @click="showPdfModal = false" class="ml-2 border border-gray-300 px-6 py-2 rounded hover:bg-gray-100 transition">Close</button>
            </div>
        </div>
    </div>

</div>
@endsection