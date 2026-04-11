@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">
        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Role Permissions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage access rights per role</p>
        </div>

        <div class="p-5 space-y-5">
            @foreach($permissions as $permission)
                @php
                    $isProtected = $permission->role === 'SuperAdmin';
                @endphp

                <form action="{{ route('admin.role-permissions.update', $permission->id) }}" method="POST" class="border border-gray-200 rounded-xl p-5">
                    @csrf

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                {{ $permission->role }}

                                @if($isProtected)
                                    <span class="px-2 py-1 text-[10px] rounded-full bg-gray-100 text-gray-600 font-medium">
                                        Protected
                                    </span>
                                @endif
                            </h2>

                            @if($isProtected)
                                <p class="text-xs text-gray-500 mt-1">
                                    SuperAdmin has full access and cannot be modified.
                                </p>
                            @endif
                        </div>

                        @if($isProtected)
                            <button
                                type="button"
                                disabled
                                class="px-4 py-2 text-sm font-medium bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed"
                            >
                                Protected
                            </button>
                        @else
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Save Changes
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 text-sm">
                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="manage_users" {{ $permission->manage_users ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Manage Users</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="access_admin_dashboard" {{ $permission->access_admin_dashboard ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Access Admin Dashboard</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="approve_townhall" {{ $permission->approve_townhall ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Approve Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="create_townhall" {{ $permission->create_townhall ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Create Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3">
                            <input type="checkbox" name="create_corporate" {{ $permission->create_corporate ? 'checked' : '' }}>
                            <span>Create Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3">
                            <input type="checkbox" name="approve_corporate" {{ $permission->approve_corporate ? 'checked' : '' }}>
                            <span>Approve Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3">
                            <input type="checkbox" name="access_townhall" {{ $permission->access_townhall ? 'checked' : '' }}>
                            <span>Access Town Hall</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="access_corporate" {{ $permission->access_corporate ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Access Corporate</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="access_activities" {{ $permission->access_activities ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Access Activities</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="access_contacts" {{ $permission->access_contacts ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Access Contacts</span>
                        </label>

                        <label class="flex items-center gap-3 border rounded-lg p-3 {{ $isProtected ? 'bg-gray-50' : '' }}">
                            <input type="checkbox" name="access_company" {{ $permission->access_company ? 'checked' : '' }} {{ $isProtected ? 'disabled' : '' }}>
                            <span>Access Company</span>
                        </label>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
