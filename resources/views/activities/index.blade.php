@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 mt-4" 
     x-data="{ 
        showTaskModal: false,
        activeTab: 'task',
        selectAll: false,
        selectedTasks: [],
        tasks: ['1', '2'],
        filterDropdownOpen: false,
        selectedTaskDetails: null,
        meetingsSubTab: 'all',
        meetingFilters: {
            videoRecording: false,
            audioRecording: false,
            transcription: false,
            minutes: false
        },
        showMeetingModal: false,
        selectedMeetingDetails: null,
        meetingDetailsTab: 'information',
        showVideoPlayer: false
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
        <div x-show="activeTab !== 'meetings'" class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <!-- Left Toolbar -->
            <div class="flex items-center gap-3">
                <div class="relative" x-data="{ taskFilterOpen: false }" @click.away="taskFilterOpen = false">
                    <button @click="taskFilterOpen = !taskFilterOpen" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded transition-colors" :class="taskFilterOpen ? 'bg-gray-100' : ''">
                        <i class="fas fa-filter text-sm"></i>
                    </button>
                    
                    <!-- Filter Task Dropdown -->
                    <div x-show="taskFilterOpen" 
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 top-full mt-2 w-64 rounded-xl shadow-lg bg-white ring-1 ring-blue-400 z-50 border border-blue-100">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-100 flex items-center gap-2">
                                <i class="fas fa-bars text-gray-500"></i>
                                <span class="text-sm font-semibold text-gray-800">Filter Task</span>
                            </div>
                            
                            <div class="px-4 py-2">
                                <button class="w-full flex items-center justify-between text-left text-sm text-gray-700 hover:bg-gray-50 p-1 rounded">
                                    Choose a Property
                                    <i class="fas fa-caret-down text-gray-400"></i>
                                </button>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto" role="menu" aria-orientation="vertical">
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Touched Records</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Untouched Records</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Record Actions</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Related Record Actions</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Closed Time</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Created By</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Created Time</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Description</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Due Date</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Modified By</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Modified Time</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Priority</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Related To</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Status</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Tag</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Task Name</span>
                                </label>
                                <label class="flex items-center px-4 py-1.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-3">
                                    <span class="text-sm text-gray-700">Task Owner</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative" x-data="{ 
                        allTaskDropdownOpen: false, 
                        allTaskTab: 'all_views' 
                    }" 
                    @click.away="allTaskDropdownOpen = false">
                    
                    <button @click="allTaskDropdownOpen = !allTaskDropdownOpen" class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        <span x-text="activeTab === 'events' ? 'All Events' : (activeTab === 'call' ? 'All Calls' : 'All Task')">All Task</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="allTaskDropdownOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- All Task Dropdown Panel -->
                    <div x-show="allTaskDropdownOpen" 
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-64 rounded-xl shadow-xl bg-white ring-1 ring-blue-500 z-50 border border-blue-400 overflow-hidden">
                        
                        <div class="flex flex-col h-full max-h-[400px]">
                            <!-- Header Tabs -->
                            <div class="p-2 flex gap-1 bg-white">
                                <button @click="allTaskTab = 'all_views'" 
                                        :class="allTaskTab === 'all_views' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                        class="flex-1 py-1.5 rounded-lg text-sm font-semibold transition-colors">
                                    All Views
                                </button>
                                <button @click="allTaskTab = 'favorites'" 
                                        :class="allTaskTab === 'favorites' ? 'bg-[#8FA8CB] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                        class="flex-1 py-1.5 rounded-lg text-sm font-semibold transition-colors">
                                    Favorites
                                </button>
                            </div>
                            
                            <!-- Search -->
                            <div class="px-3 pb-2 pt-1">
                                <div class="relative">
                                    <input type="text" placeholder="Search" class="w-full pl-3 pr-8 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                </div>
                            </div>
                            
                            <!-- Content Area -->
                            <div class="flex-1 overflow-y-auto pb-2">
                                <!-- All Views Content -->
                                <div x-show="allTaskTab === 'all_views'">
                                    <div class="px-4 py-1.5 text-xs font-bold text-gray-800">Public Views</div>
                                    <div class="flex flex-col role="menu" aria-orientation="vertical"">
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">All Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Closed Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">My Open Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Next 7 Days + Over Due Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Open Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Overdue Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Today + Ovrdue Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Today's Task</a>
                                        <a href="#" @click.prevent="allTaskDropdownOpen = false" class="block px-8 py-2 text-sm text-gray-700 hover:bg-gray-100">Tomorrow's Task</a>
                                    </div>
                                </div>
                                
                                <!-- Favorites Content -->
                                <div x-show="allTaskTab === 'favorites'" style="display: none;">
                                    <div class="py-6 px-4 text-center text-sm text-gray-500">
                                        No favorite views yet.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Create Task Footer -->
                            <div class="p-2 bg-white">
                                <button class="w-full flex items-center justify-center gap-2 py-2 bg-[#8FA8CB] text-white rounded-lg text-sm font-medium hover:bg-[#7a93b5] transition-colors">
                                    <i class="fas fa-plus text-xs"></i>
                                    Create Task
                                </button>
                            </div>
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
                <button @click="activeTab === 'call' ? null : showTaskModal = true" class="flex items-center gap-2 px-4 py-1.5 bg-[#1E293B] text-white rounded-full text-sm font-medium hover:bg-slate-700 transition shadow-sm ml-2">
                    <i class="fas fa-plus text-xs"></i>
                    <span x-text="activeTab === 'events' ? 'Event' : (activeTab === 'call' ? 'Call' : 'Task')">Task</span>
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
        <div x-show="activeTab !== 'meetings'" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead x-show="activeTab === 'task'">
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
                <thead x-show="activeTab === 'events'" style="display: none;">
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-600 w-10 text-center">
                             <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition-colors">
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Title <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            From <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            To <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Related to <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Host</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider relative group cursor-pointer">
                            <div class="flex items-center gap-2">
                                <i class="far fa-plus-square text-gray-400 hover:text-gray-600 text-sm cursor-pointer"></i> 
                                Create Field
                            </div>
                            <!-- Settings Icon floating right -->
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-cog text-gray-400 hover:text-gray-600 cursor-pointer text-sm"></i>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-show="activeTab === 'task'">
                    <!-- Row 1 -->
                    <tr class="group transition-colors" :class="selectedTasks.includes('1') ? 'bg-blue-50/50' : 'hover:bg-gray-50'">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" value="1" x-model="selectedTasks" @change="selectAll = (selectedTasks.length === tasks.length)" class="rounded text-blue-600 border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <span>Online Meeting</span>
                                <button @click="selectedTaskDetails = 'Online Meeting'" class="text-gray-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" title="View Task">
                                    <i class="far fa-eye text-sm"></i>
                                </button>
                            </div>
                        </td>
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
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <span>Proposal</span>
                                <button @click="selectedTaskDetails = 'Proposal'" class="text-gray-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" title="View Task">
                                    <i class="far fa-eye text-sm"></i>
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 24, 2026</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                    </tr>

                    <!-- Empty filler rows for consistent height mapping to design -->
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                </tbody>
                
                <!-- Events Tab Body -->
                <tbody class="divide-y divide-gray-100" x-show="activeTab === 'events'" style="display: none;">
                    <tr class="group hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" class="rounded border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2 pl-2">
                                <span>Online Meeting</span>
                                <button @click="selectedTaskDetails = 'Online Meeting'" class="text-gray-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" title="View Event">
                                    <i class="far fa-eye text-sm"></i>
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 24, 2026</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 25, 2026</td>
                        <td class="py-3 px-4 text-sm text-gray-600 text-center">B.I.R</td>
                        <td class="py-3 px-4">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-[#D1F1DE] text-[#0A5632] rounded text-xs font-medium border border-[#BCE8CD]">
                                <i class="fas fa-user text-[10px] text-[#0A5632]"></i> John...
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center justify-center w-8 h-6 bg-blue-100 rounded text-blue-600 hover:bg-blue-200 transition cursor-pointer">
                                <i class="fas fa-pencil-alt text-[10px]"></i>
                            </div>
                        </td>
                    </tr>
                    <tr class="group hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" class="rounded border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2 pl-6">
                                <span>Proposal</span>
                                <button @click="selectedTaskDetails = 'Proposal'" class="text-gray-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" title="View Event">
                                    <i class="far fa-eye text-sm"></i>
                                </button>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">Feb. 24, 2026</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                    </tr>
                    <!-- Empty filler rows -->
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="7"></td></tr>
                </tbody>

                <!-- Call Tab Header -->
                <thead x-show="activeTab === 'call'" style="display: none;">
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-600 w-10 text-center">
                             <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition-colors">
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            To/From <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Call Type <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Call Start Time <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Call Duration <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider group cursor-pointer">
                            Related to <i class="fas fa-sort text-blue-400 text-[10px] ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Call Owner</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-700 uppercase tracking-wider relative group cursor-pointer">
                            <div class="flex items-center gap-2">
                                <i class="far fa-plus-square text-gray-400 hover:text-gray-600 text-sm cursor-pointer"></i> 
                                Create Field
                            </div>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-cog text-gray-400 hover:text-gray-600 cursor-pointer text-sm"></i>
                            </div>
                        </th>
                    </tr>
                </thead>

                <!-- Call Tab Body -->
                <tbody class="divide-y divide-gray-100" x-show="activeTab === 'call'" style="display: none;">
                    <tr class="group hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" class="rounded border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            <span class="line-through text-gray-400">Person 1</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">Inbound</td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <div>Feb 25, 2026</div>
                            <div class="text-xs text-gray-400">04:00 PM</div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">50 secs.</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Sample Company</td>
                        <td class="py-3 px-4">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#D1F1DE] text-[#0A5632] rounded-full text-xs font-medium border border-[#BCE8CD]">
                                <i class="fas fa-user-circle"></i>
                                John Kelly @gmail.com
                            </div>
                        </td>
                        <td class="py-3 px-4"></td>
                    </tr>
                    <tr class="group hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-center">
                            <input type="checkbox" class="rounded border-gray-300 transition-colors">
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">Person 2</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Outbound</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4"></td>
                    </tr>
                    <!-- Empty filler rows -->
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="8"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="8"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="8"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="8"></td></tr>
                    <tr class="h-[48px] border-b border-gray-50"><td colspan="8"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- Meetings Tab Content -->
        <div x-show="activeTab === 'meetings'" style="display: none;">
            
            <!-- Meetings Sub-Tabs & Schedule Button -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-1 bg-gray-100 rounded-full p-1">
                    <button @click="meetingsSubTab = 'all'" 
                            :class="meetingsSubTab === 'all' ? 'bg-[#1E293B] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-1.5 rounded-full text-sm font-semibold transition-colors">
                        All Meetings
                    </button>
                    <button @click="meetingsSubTab = 'upcoming'" 
                            :class="meetingsSubTab === 'upcoming' ? 'bg-[#1E293B] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-1.5 rounded-full text-sm font-semibold transition-colors">
                        Upcoming
                    </button>
                    <button @click="meetingsSubTab = 'completed'" 
                            :class="meetingsSubTab === 'completed' ? 'bg-[#1E293B] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-1.5 rounded-full text-sm font-semibold transition-colors">
                        Completed
                    </button>
                </div>
                <button @click="showMeetingModal = true" class="flex items-center gap-2 px-5 py-2 bg-[#1E293B] text-white rounded-full text-sm font-semibold hover:bg-slate-700 transition shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Schedule a Meeting
                </button>
            </div>

            <!-- Meetings Body: Sidebar + Cards -->
            <div class="flex min-h-[420px]">
                
                <!-- Filter Sidebar -->
                <div class="w-52 border-r border-gray-100 p-4 shrink-0">
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-sliders-h text-gray-400"></i>
                        Filter
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="meetingFilters.videoRecording" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Video Recording</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="meetingFilters.audioRecording" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Audio Recording</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="meetingFilters.transcription" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Transcription</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="meetingFilters.minutes" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Minutes</span>
                        </label>
                    </div>
                </div>

                <!-- Meeting Cards Area -->
                <div class="flex-1 p-5 space-y-4 overflow-y-auto">
                    
                    <!-- Meeting Card 1: Proposal -->
                    <div x-show="meetingsSubTab === 'all' || meetingsSubTab === 'upcoming'" class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h4 class="text-base font-bold text-gray-900">Proposal - Sample Company</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">upcoming</span>
                            </div>
                            <button @click="selectedMeetingDetails = { title: 'Proposal - Sample Company', owner: 'John Kelly', date: 'February 26, 2026', time: '10:00 AM', duration: '--', location: '--', attendees: 5, status: 'upcoming', description: '--', hasVideo: true, hasAudio: true, hasTranscript: true, hasMinutes: true }" class="px-4 py-1.5 bg-[#1E293B] text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">View Details</button>
                        </div>
                        <div class="flex items-center gap-5 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-calendar text-gray-400"></i>
                                February 26, 2026
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-clock text-gray-400"></i>
                                10:00 AM
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users text-gray-400"></i>
                                5 Attendees
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Video Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Audio Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="fas fa-file-alt text-gray-400 text-[10px]"></i>
                                Transcript
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="far fa-file text-gray-400 text-[10px]"></i>
                                Minutes
                            </span>
                        </div>
                    </div>

                    <!-- Meeting Card 2: Client Presentation -->
                    <div x-show="meetingsSubTab === 'all' || meetingsSubTab === 'completed'" class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h4 class="text-base font-bold text-gray-900">Client Presentation - Sample Company</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">completed</span>
                            </div>
                            <button @click="selectedMeetingDetails = { title: 'Client Presentation - Sample C...', owner: 'John Kelly', date: 'February 26, 2026', time: '10:00 PM', duration: '--', location: 'California, USA', attendees: 10, status: 'completed', description: '--', hasVideo: true, hasAudio: true, hasTranscript: true, hasMinutes: true }" class="px-4 py-1.5 bg-[#1E293B] text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">View Details</button>
                        </div>
                        <div class="flex items-center gap-5 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-calendar text-gray-400"></i>
                                February 26, 2026
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-clock text-gray-400"></i>
                                10:00 AM
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users text-gray-400"></i>
                                10 Attendees
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Video Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Audio Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="fas fa-file-alt text-gray-400 text-[10px]"></i>
                                Transcript
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="far fa-file text-gray-400 text-[10px]"></i>
                                Minutes
                            </span>
                        </div>
                    </div>

                    <!-- Meeting Card 3: Product Road Map Review -->
                    <div x-show="meetingsSubTab === 'all' || meetingsSubTab === 'completed'" class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h4 class="text-base font-bold text-gray-900">Product Road Map Review - Sample Company</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">completed</span>
                            </div>
                            <button @click="selectedMeetingDetails = { title: 'Product Road Map Review', owner: 'John Kelly', date: 'February 26, 2026', time: '10:00 AM', duration: '1 hour', location: '--', attendees: 2, status: 'completed', description: '--', hasVideo: false, hasAudio: true, hasTranscript: true, hasMinutes: true }" class="px-4 py-1.5 bg-[#1E293B] text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">View Details</button>
                        </div>
                        <div class="flex items-center gap-5 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-calendar text-gray-400"></i>
                                February 26, 2026
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-clock text-gray-400"></i>
                                10:00 AM
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users text-gray-400"></i>
                                2 Attendees
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Audio Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="fas fa-file-alt text-gray-400 text-[10px]"></i>
                                Transcript
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="far fa-file text-gray-400 text-[10px]"></i>
                                Minutes
                            </span>
                        </div>
                    </div>

                    <!-- Meeting Card 4: Weekly Team Sync -->
                    <div x-show="meetingsSubTab === 'all' || meetingsSubTab === 'upcoming'" class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h4 class="text-base font-bold text-gray-900">Weekly Team Sync</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">upcoming</span>
                            </div>
                            <button @click="selectedMeetingDetails = { title: 'Weekly Team Sync', owner: 'John Kelly', date: 'February 26, 2026', time: '10:00 AM', duration: '30 mins', location: '--', attendees: 2, status: 'upcoming', description: '--', hasVideo: true, hasAudio: false, hasTranscript: true, hasMinutes: true }" class="px-4 py-1.5 bg-[#1E293B] text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">View Details</button>
                        </div>
                        <div class="flex items-center gap-5 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-calendar text-gray-400"></i>
                                February 26, 2026
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-clock text-gray-400"></i>
                                10:00 AM
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users text-gray-400"></i>
                                2 Attendees
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Video Recording
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="fas fa-file-alt text-gray-400 text-[10px]"></i>
                                Transcript
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                <i class="far fa-file text-gray-400 text-[10px]"></i>
                                Minutes
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer / Pagination Area -->
        <div class="px-6 py-3 bg-white border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3 text-xs font-semibold text-gray-500" x-show="activeTab === 'task'">
                <span class="text-gray-600">Total task 21</span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-blue-600">Open Task 11</span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-green-500">Completed 10</span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-red-500">Overdue 1</span>
            </div>

            <div class="flex items-center gap-3 text-xs font-semibold text-gray-500" x-show="activeTab === 'events'" style="display: none;">
                <span class="text-gray-600">Total Event <span class="text-xs">1</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Upcoming Events <span class="text-yellow-500">1</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Closed Events <span class="text-green-500">1</span></span>
            </div>

            <div class="flex items-center gap-3 text-xs font-semibold text-gray-500" x-show="activeTab === 'meetings'" style="display: none;">
                <span class="text-gray-700">Total Meetings <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-800 text-white text-[10px] font-bold ml-0.5">4</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Upcoming <span class="text-emerald-500 font-bold">2</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Completed <span class="text-purple-500 font-bold">2</span></span>
            </div>

            <div class="flex items-center gap-3 text-xs font-semibold text-gray-500" x-show="activeTab === 'call'" style="display: none;">
                <span class="text-gray-700">Total Calls <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-800 text-white text-[10px] font-bold ml-0.5">1</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Scheduled <span class="text-blue-500 font-bold">0</span></span>
                <div class="w-px h-3 bg-gray-300"></div>
                <span class="text-gray-600">Overdue <span class="text-red-500 font-bold">0</span></span>
            </div>
            
            <div x-show="activeTab !== 'meetings'" class="flex items-center gap-4 text-[11px] text-blue-600 font-semibold tracking-wide bg-blue-50/50 px-3 py-1.5 rounded-md">
                <div class="flex items-center gap-2 text-blue-600 cursor-pointer">
                    Records per page 
                    <button class="focus:outline-none flex items-center gap-1">
                        50 <i class="fas fa-chevron-down text-[9px] opacity-80"></i>
                    </button>
                </div>
                <div class="w-px h-3 bg-blue-200"></div>
                <span>1 - 21 of 21</span>
                <div class="flex items-center gap-2 text-gray-400">
                    <button class="hover:text-blue-600 transition disabled:opacity-50"><i class="fas fa-chevron-left text-[9px]"></i></button>
                    <button class="hover:text-blue-600 transition disabled:opacity-50"><i class="fas fa-chevron-right text-[9px]"></i></button>
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
        <!-- Modal Panel -->
        <div class="relative bg-[#f0f2f5] rounded-[20px] shadow-2xl w-full max-w-[678px] transform transition-all border-4 border-white/50 flex flex-col"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Header -->
            <div class="px-6 py-4 flex items-center justify-center relative shrink-0 border-b border-gray-200/50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Create Task</h3>
            </div>
            
            <!-- Body -->
            <div class="bg-[#1c2941] rounded-[20px] mx-5 mt-4 mb-5 flex flex-col shadow-inner overflow-hidden">
                
                <div class="px-8 py-5 space-y-4 text-xs/5">
                    
                    <!-- Extra Task Info Banner -->
                    <div class="flex items-center text-white pb-1">
                        <span class="w-32 font-semibold text-base tracking-wide">Task Information</span>
                        <div class="flex items-center justify-end gap-2 flex-1">
                            <span class="text-gray-200 text-xs">Owner</span>
                            <button class="bg-white text-gray-800 px-3 py-1.5 rounded-full text-[11px] font-semibold flex items-center gap-1.5 shadow-sm transition hover:bg-gray-100">
                                <i class="fas fa-user-circle text-gray-500 text-sm"></i>
                                John Kelly@gmail.com
                                <i class="fas fa-caret-down text-[10px] ml-0.5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Fields -->
                    <div class="flex items-center text-white">
                        <label class="w-32 text-gray-200">Task Name</label>
                        <input type="text" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                    </div>

                    <div class="flex text-white">
                        <label class="w-32 pt-2 text-gray-200">Due Date</label>
                        <div class="flex-1 space-y-2">
                            <input type="text" placeholder="MM/DD/YY" class="w-full bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                            <div class="flex flex-col gap-1.5 ml-1">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" class="rounded-sm w-3.5 h-3.5 border-gray-400 text-[#1c2941] focus:ring-0 checked:bg-white checked:border-white transition-colors cursor-pointer">
                                    <span class="text-xs text-gray-200 group-hover:text-white transition">Repeat</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" class="rounded-sm w-3.5 h-3.5 border-gray-400 text-[#1c2941] focus:ring-0 checked:bg-white checked:border-white transition-colors cursor-pointer">
                                    <span class="text-xs text-gray-200 group-hover:text-white transition">Reminder</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center text-white pt-1">
                        <label class="w-32 text-gray-200">Related To</label>
                        <input type="text" placeholder="Search Contacts/Companies/Products" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm placeholder-gray-400" />
                    </div>

                    <div class="flex text-white pt-1">
                        <label class="w-32 pt-2 text-gray-200">Description</label>
                        <div class="flex-1 space-y-3">
                            <textarea rows="3" class="w-full bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm resize-none text-sm"></textarea>
                            <div class="flex flex-col gap-1.5 ml-1 pb-2">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" class="rounded-sm w-3.5 h-3.5 border-gray-400 text-[#1c2941] focus:ring-0 checked:bg-white checked:border-white transition-colors cursor-pointer">
                                    <span class="text-xs text-gray-200 group-hover:text-white transition">Mark as High Priority</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" class="rounded-sm w-3.5 h-3.5 border-gray-400 text-[#1c2941] focus:ring-0 checked:bg-white checked:border-white transition-colors cursor-pointer">
                                    <span class="text-xs text-gray-200 group-hover:text-white transition">Mark as Completed</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-8 pb-5 flex items-center justify-center gap-3 shrink-0">
                <button @click="showTaskModal = false" class="px-8 py-2 rounded-full border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-bold text-sm transition shadow-sm min-w-[100px]">
                    Cancel
                </button>
                <button @click="showTaskModal = false" class="px-8 py-2 rounded-full bg-[#1c2941] text-white hover:bg-[#151f33] font-bold text-sm transition shadow-md min-w-[100px]">
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Schedule New Meeting Modal -->
    <div x-show="showMeetingModal" 
         style="display: none;" 
         class="fixed inset-0 z-[100] flex items-center justify-center transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showMeetingModal = false"></div>
        
        <!-- Modal Panel -->
        <div class="relative bg-[#f0f2f5] rounded-[20px] shadow-2xl w-full max-w-[678px] transform transition-all border-4 border-white/50 flex flex-col"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Header -->
            <div class="px-6 py-4 flex items-center justify-center relative shrink-0 border-b border-gray-200/50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Schedule New Meeting</h3>
            </div>
            
            <!-- Body -->
            <div class="bg-[#1c2941] rounded-[20px] mx-5 mt-4 mb-5 flex flex-col shadow-inner overflow-hidden">
                
                <div class="px-8 py-5 space-y-4 text-xs/5">
                    
                    <!-- Meeting Info Banner -->
                    <div class="flex items-center text-white pb-1">
                        <span class="w-36 font-semibold text-base tracking-wide">Meeting Information</span>
                        <div class="flex items-center justify-end gap-2 flex-1">
                            <span class="text-gray-200 text-xs">Owner</span>
                            <button class="bg-white text-gray-800 px-3 py-1.5 rounded-full text-[11px] font-semibold flex items-center gap-1.5 shadow-sm transition hover:bg-gray-100">
                                <i class="fas fa-user-circle text-gray-500 text-sm"></i>
                                John Kelly@gmail.com
                                <i class="fas fa-caret-down text-[10px] ml-0.5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Meeting Title -->
                    <div class="flex items-center text-white">
                        <label class="w-36 text-gray-200">Meeting Title</label>
                        <input type="text" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                    </div>

                    <!-- Date & Time -->
                    <div class="flex items-center text-white">
                        <label class="w-36 text-gray-200">Date & Time</label>
                        <div class="flex items-center gap-3 flex-1">
                            <input type="date" class="bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                            <input type="time" class="bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="flex items-center text-white">
                        <label class="w-36 text-gray-200">Duration</label>
                        <input type="text" placeholder="e.g. 30 mins" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm placeholder-gray-400" />
                    </div>

                    <!-- Meeting Link / Location -->
                    <div class="flex items-center text-white">
                        <label class="w-36 text-gray-200">Meeting Link / Location</label>
                        <input type="text" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                    </div>

                    <!-- Attendees -->
                    <div class="flex items-center text-white">
                        <label class="w-36 text-gray-200">Attendees</label>
                        <input type="text" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm" />
                    </div>

                    <!-- Description / Agenda -->
                    <div class="flex text-white">
                        <label class="w-36 pt-2 text-gray-200">Description / Agenda</label>
                        <textarea rows="3" class="flex-1 bg-white text-gray-900 px-3 py-2 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 shadow-sm resize-none text-sm"></textarea>
                    </div>

                    <!-- Recording & Documentation Option -->
                    <div class="pt-2">
                        <h4 class="text-white font-semibold text-sm mb-3">Recording & Documentation Option</h4>
                        <div class="flex flex-col gap-2 ml-1">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" class="rounded-sm w-4 h-4 border-gray-400 text-blue-500 focus:ring-0 bg-transparent transition-colors cursor-pointer">
                                <span class="text-sm text-gray-200 group-hover:text-white transition">Record Video</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" class="rounded-sm w-4 h-4 border-gray-400 text-blue-500 focus:ring-0 bg-transparent transition-colors cursor-pointer">
                                <span class="text-sm text-gray-200 group-hover:text-white transition">Record Audio</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" class="rounded-sm w-4 h-4 border-gray-400 text-blue-500 focus:ring-0 bg-transparent transition-colors cursor-pointer">
                                <span class="text-sm text-gray-200 group-hover:text-white transition">Generate transcript automatically</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" class="rounded-sm w-4 h-4 border-gray-400 text-blue-500 focus:ring-0 bg-transparent transition-colors cursor-pointer">
                                <span class="text-sm text-gray-200 group-hover:text-white transition">Generate meeting minutes automatically</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-8 pb-5 flex items-center justify-center gap-3 shrink-0">
                <button @click="showMeetingModal = false" class="px-8 py-2 rounded-full border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-bold text-sm transition shadow-sm min-w-[100px]">
                    Cancel
                </button>
                <button @click="showMeetingModal = false" class="px-8 py-2 rounded-full bg-[#1c2941] text-white hover:bg-[#151f33] font-bold text-sm transition shadow-md min-w-[100px]">
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Task Details Slide-over Panel -->
    <div x-show="selectedTaskDetails"
         x-data="{ taskDetailsTab: 'information' }"
         style="display: none;"
         class="fixed bottom-6 right-6 z-50 w-full max-w-[400px] bg-white rounded-xl shadow-2xl border border-blue-400 overflow-hidden flex flex-col transform transition-all"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8">
         
        <div class="bg-[#1c2941] text-white px-6 py-4 flex items-start justify-between">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2 group cursor-pointer w-fit">
                    <span x-text="selectedTaskDetails"></span>
                    <i class="fas fa-pencil-alt text-xs text-gray-400 opacity-0 group-hover:opacity-100 hover:text-white transition"></i>
                </h3>
                <div class="flex items-center gap-2 text-sm text-gray-300 mt-1 group cursor-pointer w-fit">
                    <i class="fas fa-user-circle"></i>
                    <span>John Kelly</span>
                    <i class="fas fa-pencil-alt text-[10px] opacity-0 group-hover:opacity-100 hover:text-white transition"></i>
                </div>
            </div>
            
            <div class="flex items-center gap-3 text-gray-300">
                <button class="hover:text-white transition"><i class="fas fa-external-link-alt"></i></button>
                <button class="hover:text-white transition"><i class="fas fa-ellipsis-v"></i></button>
                <button @click="selectedTaskDetails = null" class="hover:text-white ml-2 transition">
                    <i class="far fa-times-circle text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Panel Body -->
        <div class="flex flex-col flex-1 bg-white">
            
            <!-- Tabs -->
            <div class="flex px-6 pt-2 border-b border-gray-100">
                <button @click="taskDetailsTab = 'information'" 
                        :class="taskDetailsTab === 'information' ? 'bg-[#8FA8CB] text-white' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-6 py-2 rounded-t-lg text-sm font-semibold transition">
                    Information
                </button>
                <button @click="taskDetailsTab = 'notes'" 
                        :class="taskDetailsTab === 'notes' ? 'bg-[#8FA8CB] text-white font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium'"
                        class="px-6 py-2 rounded-t-lg text-sm transition">
                    Notes
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                
                <!-- Information Tab Content -->
                <div x-show="taskDetailsTab === 'information'">
                    <h4 class="font-bold text-gray-900 text-sm mb-4">Task Details</h4>
                
                <div class="space-y-4">
                    <!-- Detail Row -->
                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Due Date</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>Yesterday, 04:00 PM to 05:00 PM</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>
                    
                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Priority</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>15 minutes before start time</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Status</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>Online</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Reminder</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>On due date, 10:00 AM</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Description</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition min-h-[20px]">
                            <span></span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Related To</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>Sample Company</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>

                    <div class="flex group">
                        <div class="w-32 text-sm text-gray-500">Last Modified</div>
                        <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                            <span>John Kelly on Yesterday, 2:51 PM</span>
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Tags</h4>
                    <span class="inline-flex items-center justify-center bg-[#8FA8CB] text-white rounded-full px-3 py-1 text-xs font-semibold shadow-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Tag
                    </span>
                </div>
                </div>

                <!-- Notes Tab Content -->
                <div x-show="taskDetailsTab === 'notes'" style="display: none;">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-4">
                        <textarea class="w-full bg-transparent resize-none outline-none text-sm text-gray-900 placeholder-gray-400" rows="3" placeholder="Add a note..."></textarea>
                        <div class="flex justify-end mt-2">
                            <button class="px-4 py-1.5 bg-[#1c2941] text-white text-xs font-semibold rounded-lg hover:bg-[#151f33] transition">Save Note</button>
                        </div>
                    </div>
                    
                    <!-- Example Note -->
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#8FA8CB] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                JK
                            </div>
                            <div class="flex-1 text-sm bg-white p-3 rounded-lg border border-gray-100 shadow-sm relative group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-gray-900">John Kelly</span>
                                    <div class="flex items-center gap-2">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 hover:text-blue-600 cursor-pointer"></i>
                                            <i class="far fa-trash-alt text-[10px] text-gray-400 hover:text-red-500 cursor-pointer"></i>
                                        </div>
                                        <span class="text-xs text-gray-500">Just now</span>
                                    </div>
                                </div>
                                <p class="text-gray-700">Follow up on the proposal details and confirm the meeting time for next week.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Meeting Details Slide-over Panel -->
    <div x-show="selectedMeetingDetails"
         style="display: none;"
         class="fixed bottom-6 right-6 z-50 w-full max-w-[420px] bg-white rounded-xl shadow-2xl border border-blue-400 overflow-hidden flex flex-col transform transition-all"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8">
         
        <!-- Header -->
        <div class="bg-[#1c2941] text-white px-6 py-4 flex items-start justify-between">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2 group cursor-pointer w-fit">
                    <span x-text="selectedMeetingDetails?.title"></span>
                    <i class="fas fa-pencil-alt text-xs text-gray-400 opacity-0 group-hover:opacity-100 hover:text-white transition"></i>
                </h3>
                <div class="flex items-center gap-2 text-sm text-gray-300 mt-1 group cursor-pointer w-fit">
                    <i class="fas fa-user-circle"></i>
                    <span x-text="selectedMeetingDetails?.owner"></span>
                    <i class="fas fa-pencil-alt text-[10px] opacity-0 group-hover:opacity-100 hover:text-white transition"></i>
                </div>
            </div>
            
            <div class="flex items-center gap-3 text-gray-300">
                <button class="hover:text-white transition"><i class="fas fa-external-link-alt"></i></button>
                <button class="hover:text-white transition"><i class="fas fa-ellipsis-v"></i></button>
                <button @click="selectedMeetingDetails = null" class="hover:text-white ml-2 transition">
                    <i class="far fa-times-circle text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Panel Body -->
        <div class="flex flex-col flex-1 bg-white">
            
            <!-- Tabs -->
            <div class="flex px-6 pt-2 border-b border-gray-100">
                <button @click="meetingDetailsTab = 'information'" 
                        :class="meetingDetailsTab === 'information' ? 'bg-[#8FA8CB] text-white' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-6 py-2 rounded-t-lg text-sm font-semibold transition">
                    Information
                </button>
                <button @click="meetingDetailsTab = 'notes'" 
                        :class="meetingDetailsTab === 'notes' ? 'bg-[#8FA8CB] text-white font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium'"
                        class="px-6 py-2 rounded-t-lg text-sm transition">
                    Notes
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                
                <!-- Information Tab -->
                <div x-show="meetingDetailsTab === 'information'">
                    <h4 class="font-bold text-gray-900 text-sm mb-4">Basic Info</h4>
                
                    <div class="space-y-4">
                        <!-- Date & Time -->
                        <div class="flex group">
                            <div class="w-36 text-sm text-gray-500">Date & Time</div>
                            <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                                <span>
                                    <span x-text="selectedMeetingDetails?.date"></span>
                                    <span class="ml-2" x-text="selectedMeetingDetails?.time"></span>
                                </span>
                                <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                            </div>
                        </div>
                        
                        <!-- Duration -->
                        <div class="flex group">
                            <div class="w-36 text-sm text-gray-500">Duration</div>
                            <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                                <span x-text="selectedMeetingDetails?.duration"></span>
                                <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                            </div>
                        </div>

                        <!-- Meeting Link / Location -->
                        <div class="flex group">
                            <div class="w-36 text-sm text-gray-500">Meeting Link / Location</div>
                            <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                                <span x-text="selectedMeetingDetails?.location"></span>
                                <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                            </div>
                        </div>

                        <!-- Attendees -->
                        <div class="flex group">
                            <div class="w-36 text-sm text-gray-500">Attendees</div>
                            <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                                <span x-text="selectedMeetingDetails?.attendees"></span>
                                <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="flex group">
                            <div class="w-36 text-sm text-gray-500">Status</div>
                            <div class="flex-1 text-sm text-gray-900 flex justify-between items-center group-hover:bg-gray-50 rounded px-1 -mx-1 transition">
                                <span x-text="selectedMeetingDetails?.status"></span>
                                <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 cursor-pointer"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <h4 class="font-bold text-gray-900 text-sm mt-6 mb-2">Description</h4>
                    <p class="text-sm text-gray-500" x-text="selectedMeetingDetails?.description"></p>

                    <!-- Download Options -->
                    <h4 class="font-bold text-gray-900 text-sm mt-6 mb-3">Download Options</h4>
                    <div class="space-y-3">
                        <!-- Video -->
                        <div class="flex items-center" x-show="selectedMeetingDetails?.hasVideo">
                            <div class="w-24 text-sm text-gray-500">Video</div>
                            <div class="flex items-center gap-2">
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1c2941] text-white rounded-lg text-xs font-semibold hover:bg-[#151f33] transition shadow-sm">
                                    <i class="fas fa-download text-[10px]"></i>
                                    Download
                                </button>
                                <button @click="showVideoPlayer = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#8FA8CB] text-white rounded-lg text-xs font-semibold hover:bg-[#7a93b5] transition shadow-sm">
                                    Open
                                </button>
                            </div>
                        </div>
                        
                        <!-- Audio -->
                        <div class="flex items-center" x-show="selectedMeetingDetails?.hasAudio">
                            <div class="w-24 text-sm text-gray-500">Audio</div>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1c2941] text-white rounded-lg text-xs font-semibold hover:bg-[#151f33] transition shadow-sm">
                                <i class="fas fa-download text-[10px]"></i>
                                Download
                            </button>
                        </div>

                        <!-- Transcript -->
                        <div class="flex items-center" x-show="selectedMeetingDetails?.hasTranscript">
                            <div class="w-24 text-sm text-gray-500">Transcript</div>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1c2941] text-white rounded-lg text-xs font-semibold hover:bg-[#151f33] transition shadow-sm">
                                <i class="fas fa-download text-[10px]"></i>
                                Download
                            </button>
                        </div>

                        <!-- Minutes -->
                        <div class="flex items-center" x-show="selectedMeetingDetails?.hasMinutes">
                            <div class="w-24 text-sm text-gray-500">Minutes</div>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1c2941] text-white rounded-lg text-xs font-semibold hover:bg-[#151f33] transition shadow-sm">
                                <i class="fas fa-download text-[10px]"></i>
                                Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notes Tab -->
                <div x-show="meetingDetailsTab === 'notes'" style="display: none;">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-4">
                        <textarea class="w-full bg-transparent resize-none outline-none text-sm text-gray-900 placeholder-gray-400" rows="3" placeholder="Add a note..."></textarea>
                        <div class="flex justify-end mt-2">
                            <button class="px-4 py-1.5 bg-[#1c2941] text-white text-xs font-semibold rounded-lg hover:bg-[#151f33] transition">Save Note</button>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#8FA8CB] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                JK
                            </div>
                            <div class="flex-1 text-sm bg-white p-3 rounded-lg border border-gray-100 shadow-sm relative group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-gray-900">John Kelly</span>
                                    <div class="flex items-center gap-2">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 hover:text-blue-600 cursor-pointer"></i>
                                            <i class="far fa-trash-alt text-[10px] text-gray-400 hover:text-red-500 cursor-pointer"></i>
                                        </div>
                                        <span class="text-xs text-gray-500">Just now</span>
                                    </div>
                                </div>
                                <p class="text-gray-700">Meeting notes will appear here.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Video Player Modal -->
    <div x-show="showVideoPlayer" 
         style="display: none;" 
         class="fixed inset-0 z-[110] flex items-center justify-center transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showVideoPlayer = false"></div>
        
        <!-- Modal Panel -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[520px] transform transition-all overflow-hidden"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
             
            <!-- Header -->
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">VIDEO RECORDED</h3>
                <div class="flex items-center gap-3 text-gray-500">
                    <button class="hover:text-gray-800 transition"><i class="fas fa-download"></i></button>
                    <button class="hover:text-gray-800 transition"><i class="fas fa-external-link-alt"></i></button>
                    <button @click="showVideoPlayer = false" class="hover:text-gray-800 transition">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Video Area -->
            <div class="bg-gray-50 mx-5 mt-4 mb-4 rounded-xl flex items-center justify-center" style="height: 280px;">
                <div class="text-center text-gray-300">
                    <i class="far fa-file-video text-8xl"></i>
                </div>
            </div>

            <!-- Playback Controls -->
            <div class="mx-5 mb-5 bg-[#1c2941] rounded-full px-4 py-2.5 flex items-center gap-3">
                <button class="text-white hover:text-gray-300 transition">
                    <i class="fas fa-play text-sm"></i>
                </button>
                <div class="flex-1 h-1 bg-gray-600 rounded-full relative">
                    <div class="absolute left-0 top-0 h-full w-0 bg-white rounded-full"></div>
                </div>
                <span class="text-white text-xs font-medium tracking-wide">00:00</span>
            </div>
        </div>
    </div>

</div>
@endsection
