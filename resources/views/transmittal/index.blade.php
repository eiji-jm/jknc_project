@extends('layouts.app')

@section('content')
<div
    id="transmittal-page"
    class="w-full h-full px-6 py-5"
    x-data="{
        showSlideOver: false,

        previewRef: 'AUTO-INCREMENT',
        previewDate: '{{ now()->format('Y-m-d') }}',

        transmittalMode: 'SEND',

        partyName: '',
        officeName: '',
        previewAddress: '452 D.M. Cortes St Mandaue, Central Visayas',

        deliveryType: '',
        byPersonWho: '',
        registeredMailProvider: '',
        electronicMethod: '',

        recipientEmail: '',

        actionDelivery: false,
        actionPickUp: false,
        actionDropOff: false,
        actionEmail: false,

        previewPreparedBy: 'ANGELIE AVENIDO',
        previewApprovedBy: 'MA. LOURDES MATA',
        previewApprovedPosition: 'Branch OIC',
        previewCustodian: 'ANGELIE AVENIDO',
        previewDeliveredBy: 'Carmela Ortiz',
        previewReceivedBy: '',
        previewReceivedAt: '',

        previewItems: [
            { no: 1, particular: '', unique_id: '', qty: '', description: '', remarks: '' },
        ],

        selectedRecord: null,
        previewVisible: false,
        previewModalOpen: false,

        closeAddSectionAlpine() {
            this.showSlideOver = false;
            closeAddSection();
        },

        modeDescription() {
            return this.transmittalMode === 'SEND'
                ? 'Outgoing transmittal'
                : 'Incoming transmittal';
        },

        computedFrom() {
            return this.transmittalMode === 'SEND'
                ? (this.officeName || '')
                : (this.partyName || '');
        },

        computedTo() {
            return this.transmittalMode === 'SEND'
                ? (this.partyName || '')
                : (this.officeName || '');
        },

        partyLabel() {
            return this.transmittalMode === 'SEND' ? 'To' : 'From';
        },

        partyPlaceholder() {
            return this.transmittalMode === 'SEND'
                ? 'Enter external receiver / destination'
                : 'Enter external sender / source';
        },

        officeLabel() {
            return this.transmittalMode === 'SEND' ? 'From (Office)' : 'To (Office)';
        },

        officePlaceholder() {
            return this.transmittalMode === 'SEND'
                ? 'Enter office sending the transmittal'
                : 'Enter office receiving the transmittal';
        },

        selectedActions() {
            const actions = [];
            if (this.actionDelivery) actions.push('Delivery');
            if (this.actionPickUp) actions.push('Pick Up');
            if (this.actionDropOff) actions.push('Drop Off');
            if (this.actionEmail) actions.push('Email');
            return actions.length ? actions.join(', ') : '—';
        },

        deliverySummary() {
            if (this.deliveryType === 'By Person') {
                return this.byPersonWho ? `By Person - ${this.byPersonWho}` : 'By Person';
            }

            if (this.deliveryType === 'Registered Mail') {
                return this.registeredMailProvider ? `Registered Mail - ${this.registeredMailProvider}` : 'Registered Mail';
            }

            if (this.deliveryType === 'Electronic') {
                return this.electronicMethod ? `Electronic - ${this.electronicMethod}` : 'Electronic';
            }

            return '—';
        }
    }"
>
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-200">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Transmittal</h1>

            <button
                type="button"
                @click="showSlideOver = true"
                onclick="openAddSection()"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-full transition"
            >
                <i class="fas fa-plus mr-1"></i> Add
            </button>
        </div>

        <div class="px-5 pt-4 bg-white border-b border-gray-100">
            <div class="flex gap-8 text-[15px] text-gray-700 overflow-x-auto">
                <button onclick="applyWorkflowFilter('uploaded')" id="tab-uploaded" class="pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900">
                    Uploaded
                </button>
                <button onclick="applyWorkflowFilter('submitted')" id="tab-submitted" class="pb-3 whitespace-nowrap">
                    Submitted
                </button>
                <button onclick="applyWorkflowFilter('accepted')" id="tab-accepted" class="pb-3 whitespace-nowrap">
                    Accepted
                </button>
                <button onclick="applyWorkflowFilter('reverted')" id="tab-reverted" class="pb-3 whitespace-nowrap">
                    Reverted
                </button>
                <button onclick="applyWorkflowFilter('archived')" id="tab-archived" class="pb-3 whitespace-nowrap">
                    Archived
                </button>
            </div>

            <div id="statusMessage" class="mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md">
                These records are uploaded and ready for submission.
            </div>
        </div>

        <div id="tableSection" class="px-5 pt-4 pb-5">
            <div class="border border-gray-200 rounded-md overflow-hidden overflow-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Date</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Mode</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">From</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">To</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Delivery Type</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Actions</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Workflow Status</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Approval Status</th>
                            <th class="px-3 py-3 font-semibold">Template</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white">
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-gray-500">No transmittal records found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FULL SCREEN PREVIEW MODAL -->
        <div x-show="previewModalOpen" x-cloak class="fixed inset-0 z-[70]">
            <div class="absolute inset-0 bg-black/50" @click="previewModalOpen = false"></div>

            <div class="absolute inset-0 flex items-center justify-center p-6">
                <div
                    x-show="previewModalOpen"
                    x-transition:enter="transform transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transform transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-[1500px] h-[92vh] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
                >
                    <div class="h-full grid grid-cols-1 xl:grid-cols-[1fr_340px]">
                        <!-- LEFT -->
                        <div class="flex flex-col min-w-0 bg-[#f3f4f6]">
                            <div class="px-5 py-4 border-b border-gray-200 bg-white flex items-center justify-between">
                                <div class="min-w-0">
                                    <h2 class="text-lg font-semibold text-gray-900">Transmittal Preview</h2>
                                    <p class="text-sm text-gray-500 truncate" x-text="selectedRecord?.transmittal_no || ''"></p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        id="download-preview-pdf-main"
                                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition"
                                    >
                                        <i class="fas fa-file-pdf"></i>
                                        Download PDF
                                    </button>

                                    <button
                                        type="button"
                                        @click="previewModalOpen = false"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        Close
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 overflow-auto p-6">
                                <div class="mx-auto w-fit">
                                    <div id="transmittal-preview-pdf-main" class="transmittal-doc-page bg-white border border-gray-300 shadow-lg">
                                        <div class="tm-title">
                                            <div class="tm-title-main">Transmittal Form</div>
                                        </div>

                                        <div class="tm-top-block">
                                            <div class="tm-top-row tm-top-row-first">
                                                <div class="tm-label">Ref No</div>
                                                <div class="tm-line" x-text="selectedRecord?.transmittal_no || previewRef"></div>

                                                <div class="tm-label tm-date-label">Date</div>
                                                <div class="tm-line tm-date" x-text="previewDate"></div>
                                            </div>

                                            <div class="tm-top-row">
                                                <div class="tm-label">Mode</div>
                                                <div class="tm-line" x-text="transmittalMode"></div>
                                            </div>

                                            <div class="tm-top-row">
                                                <div class="tm-label">From</div>
                                                <div class="tm-line tm-address" x-text="computedFrom() || ' '"></div>
                                            </div>

                                            <div class="tm-top-row">
                                                <div class="tm-label">To</div>
                                                <div class="tm-line tm-address" x-text="computedTo() || ' '"></div>
                                            </div>

                                            <div class="tm-top-row">
                                                <div class="tm-label">Address</div>
                                                <div class="tm-line tm-address" x-text="previewAddress || ' '"></div>
                                            </div>
                                        </div>

                                        <div class="tm-meta-grid">
                                            <div>
                                                <span class="tm-meta-label">Delivery Type:</span>
                                                <span x-text="deliverySummary()"></span>
                                            </div>
                                            <div>
                                                <span class="tm-meta-label">Actions:</span>
                                                <span x-text="selectedActions()"></span>
                                            </div>
                                            <div>
                                                <span class="tm-meta-label">Recipient Email:</span>
                                                <span x-text="recipientEmail || '—'"></span>
                                            </div>
                                            <div>
                                                <span class="tm-meta-label">Electronic Method:</span>
                                                <span x-text="electronicMethod || '—'"></span>
                                            </div>
                                        </div>

                                        <div class="tm-section-title">List of Items</div>

                                        <table class="tm-table">
                                            <thead>
                                                <tr>
                                                    <th class="tm-col-no">No</th>
                                                    <th class="tm-col-particular">Particular</th>
                                                    <th class="tm-col-uid">Unique ID</th>
                                                    <th class="tm-col-qty">Qty.</th>
                                                    <th class="tm-col-description">Description</th>
                                                    <th class="tm-col-remarks">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(item, index) in previewItems" :key="index">
                                                    <tr>
                                                        <td x-text="item.no"></td>
                                                        <td x-text="item.particular || ''"></td>
                                                        <td x-text="item.unique_id || ''"></td>
                                                        <td x-text="item.qty || ''"></td>
                                                        <td x-text="item.description || ''"></td>
                                                        <td x-text="item.remarks || ''"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>

                                        <div class="tm-footer-code">
                                            <div>JKNC-TF-GS-V.1-2025</div>
                                            <div>Page ___ of ___</div>
                                        </div>

                                        <div class="tm-signatures">
                                            <div class="tm-sign-col">
                                                <div class="tm-sign-label">Prepared by:</div>
                                                <div class="tm-sign-line" x-text="previewPreparedBy || ' '"></div>

                                                <div class="tm-sign-label tm-sign-gap">Approved by:</div>
                                                <div class="tm-sign-line" x-text="previewApprovedBy || ' '"></div>
                                                <div class="tm-sign-sub" x-text="previewApprovedPosition || ''"></div>

                                                <div class="tm-sign-gap-sm"></div>
                                                <div class="tm-sign-line" x-text="previewCustodian || ' '"></div>
                                                <div class="tm-sign-sub">Document Custodian</div>
                                            </div>

                                            <div class="tm-sign-col">
                                                <div class="tm-sign-label">Delivered by:</div>
                                                <div class="tm-sign-line" x-text="previewDeliveredBy || ' '"></div>

                                                <div class="tm-sign-label tm-sign-gap">Received by:</div>
                                                <div class="tm-sign-line" x-text="previewReceivedBy || ' '"></div>

                                                <div class="tm-sign-label tm-sign-gap">Date and Time:</div>
                                                <div class="tm-sign-line" x-text="previewReceivedAt || ' '"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT -->
                        <div class="border-l border-gray-200 bg-white overflow-y-auto">
                            <div class="p-5 border-b border-gray-200">
                                <h3 class="text-base font-semibold text-gray-900">Transmittal Information</h3>
                            </div>

                            <div class="p-5 space-y-4 text-sm">
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Ref No</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.transmittal_no || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Date</span>
                                    <span class="font-medium text-gray-900" x-text="selectedRecord?.date || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Mode</span>
                                    <span class="font-medium text-gray-900" x-text="selectedRecord?.mode || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">From</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.from_value || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">To</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.to_value || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Address</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.address || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Delivery Type</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.delivery_type || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Actions</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.actions || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Recipient Email</span>
                                    <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.recipient_email || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Workflow</span>
                                    <span class="font-medium text-gray-900" x-text="selectedRecord?.workflow_status || 'N/A'"></span>
                                </div>
                                <div class="grid grid-cols-[110px_1fr] gap-3">
                                    <span class="text-gray-500">Approval</span>
                                    <span class="font-medium text-gray-900" x-text="selectedRecord?.approval_status || 'N/A'"></span>
                                </div>
                            </div>

                            <div class="px-5 pb-5">
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Receipt Information</h4>

                                    <div class="space-y-3 text-sm">
                                        <div class="grid grid-cols-[110px_1fr] gap-3">
                                            <span class="text-gray-500">Receipt No</span>
                                            <span class="font-medium text-gray-900 break-words" x-text="selectedRecord?.receipt_no || 'Not yet generated'"></span>
                                        </div>

                                        <a x-show="selectedRecord?.receipt_url"
                                           :href="selectedRecord?.receipt_url"
                                           target="_blank"
                                           class="inline-flex w-full items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                                            View Receipt
                                        </a>

                                        <a x-show="selectedRecord?.receipt_url"
                                           :href="selectedRecord?.receipt_url"
                                           target="_blank"
                                           class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                            Print Receipt
                                        </a>

                                        <div x-show="!selectedRecord?.receipt_url" class="text-sm text-gray-500">
                                            Receipt will appear here once the transmittal is approved.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD / EDIT SLIDE OVER -->
        <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/40" @click="closeAddSectionAlpine()"></div>

            <div class="absolute inset-0 flex">
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="w-[70%] h-full bg-[#f3f4f6] overflow-y-auto p-6 border-r border-gray-200"
                >
                    <div class="max-w-[930px] mx-auto mb-4 flex justify-end sticky top-0 z-10">
                        <button
                            type="button"
                            id="download-preview-pdf"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow transition"
                        >
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </button>
                    </div>

                    <div class="max-w-[930px] mx-auto flex justify-center">
                        <div id="transmittal-preview-pdf" class="transmittal-doc-page bg-white border border-gray-300 shadow">
                            <div class="tm-title">
                                <div class="tm-title-main">Transmittal Form</div>
                            </div>

                            <div class="tm-top-block">
                                <div class="tm-top-row tm-top-row-first">
                                    <div class="tm-label">Ref No</div>
                                    <div class="tm-line" x-text="previewRef"></div>

                                    <div class="tm-label tm-date-label">Date</div>
                                    <div class="tm-line tm-date" x-text="previewDate"></div>
                                </div>

                                <div class="tm-top-row">
                                    <div class="tm-label">Mode</div>
                                    <div class="tm-line" x-text="transmittalMode"></div>
                                </div>

                                <div class="tm-top-row">
                                    <div class="tm-label">From</div>
                                    <div class="tm-line tm-address" x-text="computedFrom() || ' '"></div>
                                </div>

                                <div class="tm-top-row">
                                    <div class="tm-label">To</div>
                                    <div class="tm-line tm-address" x-text="computedTo() || ' '"></div>
                                </div>

                                <div class="tm-top-row">
                                    <div class="tm-label">Address</div>
                                    <div class="tm-line tm-address" x-text="previewAddress"></div>
                                </div>
                            </div>

                            <div class="tm-meta-grid">
                                <div>
                                    <span class="tm-meta-label">Delivery Type:</span>
                                    <span x-text="deliverySummary()"></span>
                                </div>
                                <div>
                                    <span class="tm-meta-label">Actions:</span>
                                    <span x-text="selectedActions()"></span>
                                </div>
                                <div>
                                    <span class="tm-meta-label">Recipient Email:</span>
                                    <span x-text="recipientEmail || '—'"></span>
                                </div>
                                <div>
                                    <span class="tm-meta-label">Electronic Method:</span>
                                    <span x-text="electronicMethod || '—'"></span>
                                </div>
                            </div>

                            <div class="tm-section-title">List of Items</div>

                            <table class="tm-table">
                                <thead>
                                    <tr>
                                        <th class="tm-col-no">No</th>
                                        <th class="tm-col-particular">Particular</th>
                                        <th class="tm-col-uid">Unique ID</th>
                                        <th class="tm-col-qty">Qty.</th>
                                        <th class="tm-col-description">Description</th>
                                        <th class="tm-col-remarks">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in previewItems" :key="index">
                                        <tr>
                                            <td x-text="item.no"></td>
                                            <td x-text="item.particular || ''"></td>
                                            <td x-text="item.unique_id || ''"></td>
                                            <td x-text="item.qty || ''"></td>
                                            <td x-text="item.description || ''"></td>
                                            <td x-text="item.remarks || ''"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            <div class="tm-footer-code">
                                <div>JKNC-TF-GS-V.1-2025</div>
                                <div>Page ___ of ___</div>
                            </div>

                            <div class="tm-signatures">
                                <div class="tm-sign-col">
                                    <div class="tm-sign-label">Prepared by:</div>
                                    <div class="tm-sign-line" x-text="previewPreparedBy || ' '"></div>

                                    <div class="tm-sign-label tm-sign-gap">Approved by:</div>
                                    <div class="tm-sign-line" x-text="previewApprovedBy || ' '"></div>
                                    <div class="tm-sign-sub" x-text="previewApprovedPosition || ''"></div>

                                    <div class="tm-sign-gap-sm"></div>
                                    <div class="tm-sign-line" x-text="previewCustodian || ' '"></div>
                                    <div class="tm-sign-sub">Document Custodian</div>
                                </div>

                                <div class="tm-sign-col">
                                    <div class="tm-sign-label">Delivered by:</div>
                                    <div class="tm-sign-line" x-text="previewDeliveredBy || ' '"></div>

                                    <div class="tm-sign-label tm-sign-gap">Received by:</div>
                                    <div class="tm-sign-line" x-text="previewReceivedBy || ' '"></div>

                                    <div class="tm-sign-label tm-sign-gap">Date and Time:</div>
                                    <div class="tm-sign-line" x-text="previewReceivedAt || ' '"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <h2 class="text-lg font-semibold text-gray-800">Add Transmittal</h2>
                        <button type="button" @click="closeAddSectionAlpine()" class="text-gray-400 hover:text-gray-600 text-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Ref #</label>
                            <input type="text" value="AUTO-INCREMENT" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600">
                            <p class="mt-1 text-xs text-gray-400">Date is automatically set to today when saved.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
                            <input type="date" x-model="previewDate" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Mode</label>
                            <select x-model="transmittalMode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="SEND">SEND</option>
                                <option value="RECEIVE">RECEIVE</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400" x-text="modeDescription()"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1" x-text="partyLabel()"></label>
                            <input type="text" x-model="partyName" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" :placeholder="partyPlaceholder()">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1" x-text="officeLabel()"></label>
                            <input type="text" x-model="officeName" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" :placeholder="officePlaceholder()">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Address</label>
                            <textarea x-model="previewAddress" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Enter address"></textarea>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Delivery Type</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Type</label>
                                    <select x-model="deliveryType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                        <option value="">Select delivery type</option>
                                        <option value="By Person">By Person</option>
                                        <option value="Registered Mail">Registered Mail</option>
                                        <option value="Electronic">Electronic</option>
                                    </select>
                                </div>

                                <div x-show="deliveryType === 'By Person'">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Who?</label>
                                    <input type="text" x-model="byPersonWho" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div x-show="deliveryType === 'Registered Mail'">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Courier / Provider</label>
                                    <select x-model="registeredMailProvider" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                                        <option value="">Select provider</option>
                                        <option value="LBC">LBC</option>
                                        <option value="J&T">J&T</option>
                                        <option value="Postal">Postal</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <div x-show="deliveryType === 'Electronic'">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Electronic Method</label>
                                    <select x-model="electronicMethod" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                                        <option value="">Select method</option>
                                        <option value="Email">Email</option>
                                    </select>
                                </div>

                                <div x-show="deliveryType === 'Electronic' && electronicMethod === 'Email'">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Recipient Email</label>
                                    <input type="email" x-model="recipientEmail" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Enter email address">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Actions</h3>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="actionDelivery" class="rounded border-gray-300">
                                    <span>Delivery</span>
                                </label>

                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="actionPickUp" class="rounded border-gray-300">
                                    <span>Pick Up</span>
                                </label>

                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="actionDropOff" class="rounded border-gray-300">
                                    <span>Drop Off</span>
                                </label>

                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="actionEmail" class="rounded border-gray-300">
                                    <span>Email</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-gray-500">List of Items</label>
                                <button type="button" onclick="addTransmittalRow()" class="text-xs bg-blue-600 text-white px-2.5 py-1.5 rounded-md hover:bg-blue-700">
                                    <i class="fas fa-plus mr-1"></i> Add Row
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in previewItems" :key="index">
                                    <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-semibold text-gray-600">Item <span x-text="index + 1"></span></p>
                                            <button type="button" @click="removeTransmittalRow(index)" class="text-red-500 hover:text-red-700 text-xs">
                                                Remove
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="col-span-2">
                                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Particular</label>
                                                <input type="text" x-model="item.particular" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Unique ID</label>
                                                <input type="text" x-model="item.unique_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Qty.</label>
                                                <input type="number" x-model="item.qty" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>

                                            <div class="col-span-2">
                                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Description</label>
                                                <input type="text" x-model="item.description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>

                                            <div class="col-span-2">
                                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Remarks</label>
                                                <input type="text" x-model="item.remarks" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Signatories</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Prepared by</label>
                                    <input type="text" x-model="previewPreparedBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Approved by</label>
                                    <input type="text" x-model="previewApprovedBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Position</label>
                                    <input type="text" x-model="previewApprovedPosition" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Document Custodian</label>
                                    <input type="text" x-model="previewCustodian" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Delivered by</label>
                                    <input type="text" x-model="previewDeliveredBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Received by</label>
                                    <input type="text" x-model="previewReceivedBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Date and Time</label>
                                    <input type="datetime-local" x-model="previewReceivedAt" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
                        <button type="button" @click="closeAddSectionAlpine()" class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition">
                            Cancel
                        </button>

                        <button type="button" onclick="saveTransmittal()" class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    .transmittal-doc-page {
        width: 210mm;
        min-height: 297mm;
        padding: 16mm 16mm 18mm 16mm;
        box-sizing: border-box;
        background: #fff;
        color: #111827;
        font-family: Arial, sans-serif;
    }

    .tm-title {
        text-align: center;
        margin-bottom: 20px;
    }

    .tm-title-main {
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
    }

    .tm-top-block {
        font-size: 12px;
        margin-bottom: 16px;
    }

    .tm-top-row {
        display: grid;
        grid-template-columns: 70px 1fr;
        gap: 10px;
        align-items: end;
        margin-bottom: 6px;
    }

    .tm-top-row-first {
        grid-template-columns: 70px 1fr 42px 130px;
    }

    .tm-label {
        font-weight: 600;
        line-height: 1.2;
    }

    .tm-date-label {
        text-align: right;
    }

    .tm-line {
        border-bottom: 1px solid #9ca3af;
        min-height: 18px;
        line-height: 1.2;
        padding-bottom: 2px;
        word-break: break-word;
    }

    .tm-address {
        white-space: normal;
    }

    .tm-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 20px;
        font-size: 11px;
        margin-bottom: 16px;
    }

    .tm-meta-label {
        font-weight: 700;
    }

    .tm-section-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .tm-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 10.5px;
        margin-bottom: 20px;
    }

    .tm-table th,
    .tm-table td {
        border: 1px solid #9ca3af;
        padding: 5px 6px;
        vertical-align: top;
        text-align: left;
        min-height: 26px;
        word-break: break-word;
    }

    .tm-table th {
        font-weight: 600;
        background: #fff;
    }

    .tm-col-no { width: 36px; }
    .tm-col-particular { width: 110px; }
    .tm-col-uid { width: 105px; }
    .tm-col-qty { width: 40px; }
    .tm-col-description { width: auto; }
    .tm-col-remarks { width: 95px; }

    .tm-footer-code {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        margin-bottom: 28px;
    }

    .tm-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 36px;
        font-size: 12px;
    }

    .tm-sign-col {
        display: flex;
        flex-direction: column;
    }

    .tm-sign-label {
        margin-bottom: 24px;
    }

    .tm-sign-gap {
        margin-top: 28px;
    }

    .tm-sign-gap-sm {
        margin-top: 22px;
    }

    .tm-sign-line {
        border-bottom: 1px solid #9ca3af;
        min-height: 18px;
        padding-bottom: 2px;
        line-height: 1.2;
        font-weight: 600;
        word-break: break-word;
    }

    .tm-sign-sub {
        margin-top: 4px;
        line-height: 1.2;
    }

    @media (max-width: 1280px) {
        .transmittal-doc-page {
            transform: scale(0.88);
            transform-origin: top center;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
let currentWorkflowFilter = 'uploaded';
let transmittalRows = [];

function getAlpineData() {
    const root = document.getElementById('transmittal-page');
    return root ? Alpine.$data(root) : null;
}

function updateStatusMessage() {
    const messageBox = document.getElementById('statusMessage');

    if (currentWorkflowFilter === 'uploaded') {
        messageBox.className = 'mt-3 mb-4 border border-blue-200 bg-blue-50 text-blue-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are uploaded and ready for submission.';
    } else if (currentWorkflowFilter === 'submitted') {
        messageBox.className = 'mt-3 mb-4 border border-yellow-200 bg-yellow-50 text-yellow-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are already submitted and waiting for admin approval.';
    } else if (currentWorkflowFilter === 'accepted') {
        messageBox.className = 'mt-3 mb-4 border border-green-200 bg-green-50 text-green-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were already accepted.';
    } else if (currentWorkflowFilter === 'reverted') {
        messageBox.className = 'mt-3 mb-4 border border-red-200 bg-red-50 text-red-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records were reverted and can be corrected then resubmitted.';
    } else {
        messageBox.className = 'mt-3 mb-4 border border-gray-200 bg-gray-50 text-gray-700 text-[14px] px-4 py-3 rounded-md';
        messageBox.textContent = 'These records are archived.';
    }
}

function setActiveTab() {
    ['uploaded', 'submitted', 'accepted', 'reverted', 'archived'].forEach(tab => {
        const el = document.getElementById(`tab-${tab}`);
        if (!el) return;

        if (tab === currentWorkflowFilter) {
            el.className = 'pb-3 whitespace-nowrap border-b-2 border-blue-600 font-medium text-gray-900';
        } else {
            el.className = 'pb-3 whitespace-nowrap text-gray-700';
        }
    });
}

function applyWorkflowFilter(filterValue) {
    currentWorkflowFilter = filterValue;
    renderTable();
}

function openAddSection() {
    const alpineData = getAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = true;
    }
}

function closeAddSection() {
    const alpineData = getAlpineData();
    if (alpineData) {
        alpineData.showSlideOver = false;
    }
}

function addTransmittalRow() {
    const alpineData = getAlpineData();
    if (!alpineData) return;

    alpineData.previewItems.push({
        no: alpineData.previewItems.length + 1,
        particular: '',
        unique_id: '',
        qty: '',
        description: '',
        remarks: ''
    });
}

function fillPreviewFromRow(row, openModal = true) {
    const alpineData = getAlpineData();
    if (!alpineData || !row) return;

    alpineData.previewRef = row.transmittal_no || 'AUTO-INCREMENT';
    alpineData.previewDate = row.date || '';
    alpineData.transmittalMode = row.mode || 'SEND';

    alpineData.partyName = row.party_name || '';
    alpineData.officeName = row.office_name || '';
    alpineData.previewAddress = row.address || '';

    if (row.delivery_type?.startsWith('By Person')) {
        alpineData.deliveryType = 'By Person';
    } else if (row.delivery_type?.startsWith('Registered Mail')) {
        alpineData.deliveryType = 'Registered Mail';
    } else if (row.delivery_type?.startsWith('Electronic')) {
        alpineData.deliveryType = 'Electronic';
    } else {
        alpineData.deliveryType = '';
    }

    alpineData.byPersonWho = row.by_person_who || '';
    alpineData.registeredMailProvider = row.registered_mail_provider || '';
    alpineData.electronicMethod = row.electronic_method || '';
    alpineData.recipientEmail = row.recipient_email || '';

    alpineData.actionDelivery = !!row.action_delivery;
    alpineData.actionPickUp = !!row.action_pick_up;
    alpineData.actionDropOff = !!row.action_drop_off;
    alpineData.actionEmail = !!row.action_email;

    alpineData.previewPreparedBy = row.prepared_by_name || '';
    alpineData.previewApprovedBy = row.approved_by_name || '';
    alpineData.previewApprovedPosition = row.approved_position || '';
    alpineData.previewCustodian = row.document_custodian || '';
    alpineData.previewDeliveredBy = row.delivered_by || '';
    alpineData.previewReceivedBy = row.received_by || '';
    alpineData.previewReceivedAt = row.received_at || '';

    alpineData.previewItems = (row.items && row.items.length)
        ? row.items.map((item, idx) => ({
            no: item.no ?? (idx + 1),
            particular: item.particular ?? '',
            unique_id: item.unique_id ?? '',
            qty: item.qty ?? '',
            description: item.description ?? '',
            remarks: item.remarks ?? '',
        }))
        : [{ no: 1, particular: '', unique_id: '', qty: '', description: '', remarks: '' }];

    alpineData.selectedRecord = row;
    alpineData.previewVisible = false;
    alpineData.previewModalOpen = openModal;
}

function previewTransmittal(index) {
    const row = transmittalRows[index];
    if (!row) return;
    fillPreviewFromRow(row, true);
}

async function fetchTransmittals() {
    const params = new URLSearchParams();
    params.append('workflow_status', currentWorkflowFilter);

    const res = await fetch(`/transmittal/data?${params.toString()}`, {
        headers: {
            'Accept': 'application/json'
        }
    });

    return await res.json();
}

async function renderTable() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const data = await fetchTransmittals();
    transmittalRows = data || [];

    updateStatusMessage();
    setActiveTab();

    if (!transmittalRows.length) {
        tableBody.innerHTML = `<tr><td colspan="10" class="px-3 py-8 text-center text-gray-500">No transmittal records found.</td></tr>`;
        const alpineData = getAlpineData();
        if (alpineData) {
            alpineData.previewVisible = false;
            alpineData.previewModalOpen = false;
            alpineData.selectedRecord = null;
        }
        return;
    }

    transmittalRows.forEach((item, index) => {
        tableBody.innerHTML += `
            <tr class="border-t border-gray-200 hover:bg-gray-50">
                <td class="px-3 py-3 border-r border-gray-200">${item.transmittal_no ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.date ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.mode ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.from_value ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.to_value ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.delivery_type ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.actions ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.workflow_status ?? ''}</td>
                <td class="px-3 py-3 border-r border-gray-200">${item.approval_status ?? ''}</td>
                <td class="px-3 py-3">
                    <div class="flex flex-col gap-1">
                        <button type="button" onclick="previewTransmittal(${index})" class="text-gray-700 hover:underline text-left">
                            View
                        </button>

                        ${item.can_submit
                            ? `<button type="button" onclick="submitTransmittal(${item.id})" class="text-blue-600 hover:underline text-left">Submit</button>`
                            : ''}

                        ${item.receipt_url
                            ? `<a href="${item.receipt_url}" target="_blank" class="text-green-600 hover:underline text-left">Receipt</a>`
                            : ''}
                    </div>
                </td>
            </tr>
        `;
    });
}

async function saveTransmittal() {
    const alpineData = getAlpineData();
    if (!alpineData) return;

    const payload = {
        transmittal_date: alpineData.previewDate,
        mode: alpineData.transmittalMode,
        party_name: alpineData.partyName,
        office_name: alpineData.officeName,
        address: alpineData.previewAddress,

        delivery_type: alpineData.deliveryType,
        by_person_who: alpineData.byPersonWho,
        registered_mail_provider: alpineData.registeredMailProvider,
        electronic_method: alpineData.electronicMethod,
        recipient_email: alpineData.recipientEmail,

        action_delivery: alpineData.actionDelivery,
        action_pick_up: alpineData.actionPickUp,
        action_drop_off: alpineData.actionDropOff,
        action_email: alpineData.actionEmail,

        prepared_by_name: alpineData.previewPreparedBy,
        approved_by_name: alpineData.previewApprovedBy,
        approved_position: alpineData.previewApprovedPosition,
        document_custodian: alpineData.previewCustodian,
        delivered_by: alpineData.previewDeliveredBy,
        received_by: alpineData.previewReceivedBy,
        received_at: alpineData.previewReceivedAt ? alpineData.previewReceivedAt.replace('T', ' ') : null,

        items: alpineData.previewItems
    };

    try {
        const res = await fetch('/transmittal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Failed to save transmittal.');
            return;
        }

        alert(data.message || 'Transmittal saved successfully.');
        currentWorkflowFilter = 'uploaded';
        closeAddSection();
        await renderTable();
    } catch (error) {
        alert('Something went wrong while saving the transmittal.');
    }
}

async function submitTransmittal(id) {
    try {
        const res = await fetch(`/transmittal/${id}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Unable to submit transmittal.');
            return;
        }

        alert(data.message || 'Submitted successfully.');
        currentWorkflowFilter = 'submitted';
        await renderTable();
    } catch (error) {
        alert('Something went wrong while submitting.');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const alpineData = getAlpineData();

    if (alpineData) {
        alpineData.removeTransmittalRow = function(index) {
            if (this.previewItems.length > 1) {
                this.previewItems.splice(index, 1);
                this.previewItems.forEach((item, i) => item.no = i + 1);
            }
        };
    }

    const downloadBtn = document.getElementById('download-preview-pdf');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            const element = document.getElementById('transmittal-preview-pdf');
            if (!element) return;

            html2pdf().set({
                margin: [0, 0, 0, 0],
                filename: 'transmittal-form.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(element).save();
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'download-preview-pdf-main') {
            const element = document.getElementById('transmittal-preview-pdf-main');
            if (!element) return;

            html2pdf().set({
                margin: [0, 0, 0, 0],
                filename: 'transmittal-form.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(element).save();
        }
    });

    document.addEventListener('keydown', (e) => {
        const alpineData = getAlpineData();
        if (!alpineData) return;

        if (e.key === 'Escape') {
            if (alpineData.showSlideOver) {
                closeAddSection();
            } else if (alpineData.previewModalOpen) {
                alpineData.previewModalOpen = false;
            }
        }
    });

    renderTable();
});
</script>
@endpush