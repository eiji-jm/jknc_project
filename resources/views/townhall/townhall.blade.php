@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5" x-data="{ showSlideOver: false }">

    {{-- SLIDE OVER --}}
    <div x-show="showSlideOver" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">

            {{-- BACKDROP --}}
            <div
                x-show="showSlideOver"
                @click="showSlideOver = false"
                class="absolute inset-0 bg-black/40"
            ></div>

            {{-- PANEL --}}
            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="showSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-2xl bg-white shadow-2xl h-full flex flex-col"
                >
                    {{-- HEADER --}}
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

                    {{-- FORM BODY --}}
                    <form class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                        {{-- REF + DATE --}}
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
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        {{-- FROM + DEPARTMENT/STAKEHOLDER --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
                                <input
                                    type="text"
                                    placeholder="Enter sender"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Department / Stakeholder</label>
                                <input
                                    type="text"
                                    placeholder="Enter department or stakeholder"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        {{-- TO/FOR + STATUS --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">To / For</label>
                                <input
                                    type="text"
                                    placeholder="Enter recipient"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                                <select
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                                    <option value="">Select status</option>
                                    <option>Open</option>
                                    <option>Completed</option>
                                    <option>Overdue</option>
                                </select>
                            </div>
                        </div>

                        {{-- SUBJECT --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
                            <input
                                type="text"
                                placeholder="Enter subject"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            >
                        </div>

                        {{-- MESSAGE BODY --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Message</label>
                            <textarea
                                rows="10"
                                placeholder="Write communication message..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                            ></textarea>
                        </div>

                        {{-- CC + ADDITIONAL --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">CC</label>
                                <input
                                    type="text"
                                    placeholder="Enter CC recipients"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Additional</label>
                                <input
                                    type="text"
                                    placeholder="Optional"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        {{-- ATTACHMENT --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Attachment</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center text-sm text-gray-500">
                                Drag & Drop file here or
                                <span class="text-blue-600 underline cursor-pointer">Browse</span>
                            </div>
                        </div>
                    </form>

                    {{-- FOOTER --}}
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
                        <button
                            type="button"
                            @click="showSlideOver = false"
                            class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">

        {{-- TOP ACTION BAR --}}
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

        {{-- TABLE --}}
        <div class="px-5 pb-4 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-md overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Ref#</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Date</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Time</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Department/Stakeholder</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">From</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Subject</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">For/To</th>
                            <th class="px-3 py-3 border-r border-gray-200 font-semibold">Status</th>
                            <th class="px-3 py-3 font-semibold w-10"></th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        <tr class="border-t border-gray-200">
                            <td class="px-3 py-3 border-r border-gray-200">0001</td>
                            <td class="px-3 py-3 border-r border-gray-200">04/22/2024</td>
                            <td class="px-3 py-3 border-r border-gray-200">10:00 AM</td>
                            <td class="px-3 py-3 border-r border-gray-200">Accounting</td>
                            <td class="px-3 py-3 border-r border-gray-200">Admin</td>
                            <td class="px-3 py-3 border-r border-gray-200">Sample Subject</td>
                            <td class="px-3 py-3 border-r border-gray-200">All Staff</td>
                            <td class="px-3 py-3 border-r border-gray-200">Open</td>
                            <td class="px-3 py-3 text-center text-gray-400">…</td>
                        </tr>

                        @for ($i = 0; $i < 8; $i++)
                            <tr class="border-t border-gray-200 h-12">
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="mt-2 flex items-center justify-between text-[10px] text-gray-500 px-1">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1">Total Task <span class="text-black font-medium">1</span></span>
                    <span class="flex items-center gap-1">Open Task <span class="text-yellow-500 font-medium">1</span></span>
                    <span class="flex items-center gap-1">Completed <span class="text-green-500 font-medium">1</span></span>
                    <span class="flex items-center gap-1">Overdue <span class="text-red-500 font-medium">1</span></span>
                </div>

                <div class="flex items-center gap-5">
                    <span class="flex items-center gap-1">
                        Records per page
                        <select class="bg-transparent text-gray-600 outline-none">
                            <option>10</option>
                        </select>
                    </span>

                    <span>1 to 1</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
