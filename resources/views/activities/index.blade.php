@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" 
     x-data="{ 
        showTaskModal: false,
        activeTab: 'task',
        selectAll: false,
        selectedTasks: [],
        tasks: ['1', '2'],
        filterDropdownOpen: false
     }">

    <!-- Header Tabs -->
    <div class="flex items-center justify-center gap-12 py-3 mb-4">
        <button @click="activeTab = 'task'" 
                :class="activeTab === 'task' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-8 py-1.5 rounded-full font-semibold text-sm transition-colors">
            Task
        </button>
        <button @click="activeTab = 'events'" 
                :class="activeTab === 'events' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-8 py-1.5 rounded-full font-semibold text-sm transition-colors">
            Events
        </button>
        <button @click="activeTab = 'call'" 
                :class="activeTab === 'call' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-8 py-1.5 rounded-full font-semibold text-sm transition-colors">
            Call
        </button>
        <button @click="activeTab = 'meetings'" 
                :class="activeTab === 'meetings' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-8 py-1.5 rounded-full font-semibold text-sm transition-colors">
            Meetings
        </button>
    </div>

    <!-- Main Card Container -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
        
        <!-- Toolbar -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <!-- Left Toolbar -->
            <div class="flex items-center gap-3">
                <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded transition-colors">
                    <i class="fas fa-bars text-sm"></i>
                </button>
                <div class="relative" @click.away="filterDropdownOpen = false">
                    <button @click="filterDropdownOpen = !filterDropdownOpen" class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        All Task
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="filterDropdownOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <!-- Dropdown Panel -->
                    <div x-show="filterDropdownOpen" 
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 border border-gray-100">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            <a href="#" @click.prevent="filterDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">All Tasks</a>
                            <a href="#" @click.prevent="filterDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">My Tasks</a>
                            <a href="#" @click.prevent="filterDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">Team Tasks</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Toolbar -->
            <div class="flex items-center gap-3">
                <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded">
                    <i class="fas fa-th-large text-sm"></i>
                </button>
                <div class="h-5 w-px bg-gray-300 mx-1"></div>
                <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded">
                     <i class="fas fa-list text-sm"></i>
                </button>
                
                <!-- Primary Action Button -->
                <button @click="showTaskModal = true" class="flex items-center gap-2 px-4 py-1.5 bg-[#1E293B] text-white rounded-full text-sm font-medium hover:bg-slate-700 transition shadow-sm ml-2">
                    <i class="fas fa-plus text-xs"></i>
                    Task
                    <i class="fas fa-chevron-down text-[10px] ml-1 opacity-80"></i>
                </button>
                
                <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded ml-2">
                    <i class="fas fa-cog text-sm"></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-600 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="selectedTasks = selectAll ? [...tasks] : []" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition-colors">
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Task Name</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Due Date</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Priority</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Related to</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Task Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-show="activeTab === 'task'">
                    <!-- Row 1 -->
                    <tr class="group transition-colors" :class="selectedTasks.includes('1') ? 'bg-blue-50/50' : 'hover:bg-gray-50'">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" value="1" x-model="selectedTasks" @change="selectAll = (selectedTasks.length === tasks.length)" class="rounded text-blue-600 border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">Online Meeting</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 24, 2026</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#D1F1DE] text-[#0A5632] rounded-full text-xs font-medium border border-[#BCE8CD]">
                                <i class="fas fa-user-circle"></i>
                                John Kelly@gmail.com
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr class="group transition-colors" :class="selectedTasks.includes('2') ? 'bg-blue-50/50' : 'hover:bg-gray-50'">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" value="2" x-model="selectedTasks" @change="selectAll = (selectedTasks.length === tasks.length)" class="rounded text-blue-600 border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">Proposal</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 24, 2026</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                    </tr>

                    <!-- Empty filler rows for consistent height mapping to design -->
                    <tr class="h-[48px] border-b border-gray-50" x-show="activeTab === 'task'"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50" x-show="activeTab === 'task'"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50" x-show="activeTab === 'task'"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50" x-show="activeTab === 'task'"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50" x-show="activeTab === 'task'"><td colspan="7"></td></tr>
                    
                    <!-- Other Tabs Empty States -->
                    <tr x-show="activeTab !== 'task'" style="display: none;">
                        <td colspan="7" class="py-12 text-center text-gray-500 text-sm">
                            <i class="far fa-folder-open text-4xl text-gray-300 mb-3 block"></i>
                            No items found for <span x-text="activeTab" class="capitalize font-semibold"></span>.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination Area -->
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-4 text-xs text-gray-500 font-medium">
                <span class="flex items-center gap-1 text-gray-700">Total task <span class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-800">21</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="flex items-center gap-1">Open Task <span class="text-gray-700">11</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="flex items-center gap-1">Completed <span class="text-green-600">10</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="flex items-center gap-1">Overdue <span class="text-red-500 font-bold">1</span></span>
            </div>
            
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    Records per page
                    <select class="border border-gray-300 rounded text-gray-700 text-xs py-0.5 px-1 bg-white">
                        <option>50</option>
                    </select>
                </div>
                <span>1 - 21 of 21</span>
                <div class="flex items-center gap-1">
                    <button class="w-6 h-6 flex items-center justify-center rounded hover:bg-gray-200 text-gray-400 cursor-not-allowed"><i class="fas fa-chevron-left text-[10px]"></i></button>
                    <button class="w-6 h-6 flex items-center justify-center rounded hover:bg-gray-200 text-gray-400 cursor-not-allowed"><i class="fas fa-chevron-right text-[10px]"></i></button>
                </div>
            </div>
        </div>

    </div>

    <!-- Create Task Modal -->
    <div x-show="showTaskModal" 
         style="display: none;" 
         class="fixed inset-0 z-[100] flex items-center justify-center transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showTaskModal = false"></div>
        
        <!-- Modal Panel -->
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-[500px] overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Header -->
            <div class="bg-white px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Create Task</h3>
                <button @click="showTaskModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="bg-[#1c2941] px-8 py-8 space-y-5 text-sm/5">
                
                <!-- Extra Task Info Banner -->
                <div class="flex items-center text-white pb-2">
                    <span class="w-28 font-medium">Task Information</span>
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-gray-300 text-sm">Owner</span>
                        <button class="bg-white/10 hover:bg-white/20 border border-white/10 text-white px-3 py-1.5 rounded-full text-xs font-medium flex items-center gap-2 transition">
                            <i class="fas fa-user-circle text-gray-300 text-sm"></i>
                            John Kelly@gmail.com
                            <i class="fas fa-chevron-down text-[10px] ml-1 opacity-70"></i>
                        </button>
                    </div>
                </div>

                <!-- Fields -->
                <div class="flex items-center text-white">
                    <label class="w-28 text-gray-200">Task Name</label>
                    <input type="text" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-400 shadow-inner" />
                </div>

                <div class="flex text-white">
                    <label class="w-28 pt-2.5 text-gray-200">Due Date</label>
                    <div class="flex-1 space-y-2.5">
                        <div class="relative w-full max-w-[180px]">
                            <input type="text" placeholder="MM/DD/YYYY" class="w-full bg-white text-gray-900 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-400 shadow-inner text-sm" />
                        </div>
                        <div class="flex flex-col gap-1.5 ml-1">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded w-4 h-4 border-gray-300 text-blue-600 bg-white/20 border-0 ring-offset-[#1c2941] focus:ring-white">
                                <span class="text-sm text-gray-300 group-hover:text-white transition">Repeat</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded w-4 h-4 border-gray-300 text-blue-600 bg-white/20 border-0 ring-offset-[#1c2941] focus:ring-white">
                                <span class="text-sm text-gray-300 group-hover:text-white transition">Reminder</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center text-white">
                    <label class="w-28 text-gray-200">Related To</label>
                    <div class="relative flex-1 text-gray-900">
                        <input type="text" placeholder="Search Contacts/Companies/Products" class="w-full bg-white px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-400 shadow-inner" />
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                </div>

                <div class="flex text-white">
                    <label class="w-28 pt-2.5 text-gray-200">Description</label>
                    <div class="flex-1 space-y-3">
                        <textarea rows="3" class="w-full bg-white text-gray-900 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-400 shadow-inner resize-none"></textarea>
                        <div class="flex flex-col gap-1.5 ml-1 pb-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded w-4 h-4 border-gray-300 text-blue-600 bg-white/20 border-0 ring-offset-[#1c2941] focus:ring-white">
                                <span class="text-sm text-gray-300 group-hover:text-white transition">Mark as High Priority</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded w-4 h-4 border-gray-300 text-blue-600 bg-white/20 border-0 ring-offset-[#1c2941] focus:ring-white">
                                <span class="text-sm text-gray-300 group-hover:text-white transition">Mark as Completed</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                <button @click="showTaskModal = false" class="px-6 py-2 rounded-full border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium text-sm transition shadow-sm">
                    Cancel
                </button>
                <button @click="showTaskModal = false" class="px-8 py-2 rounded-full bg-[#1c2941] text-white hover:bg-[#151f33] font-medium text-sm transition shadow-sm">
                    Save
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
