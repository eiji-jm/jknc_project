@extends('layouts.app')

@section('content')
<div class="w-full h-full px-6 py-5" x-data="{ showCreateUser: false }">
<<<<<<< HEAD
=======
    @php
        $authUser = auth()->user();
    @endphp
>>>>>>> origin/Admin/Townhall/Corporate

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

<<<<<<< HEAD
=======
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

>>>>>>> origin/Admin/Townhall/Corporate
    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CREATE USER SLIDE OVER --}}
    <div x-show="showCreateUser" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div
                x-show="showCreateUser"
                @click="showCreateUser = false"
                class="absolute inset-0 bg-black/40"
            ></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="showCreateUser"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-xl bg-white shadow-2xl h-full flex flex-col"
                >
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Create User</h2>
                        <button
                            type="button"
                            @click="showCreateUser = false"
                            class="text-gray-400 hover:text-gray-600 text-lg"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Full Name</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter full name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter email"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Role</label>
                            <select
                                name="role"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="">Select role</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>Employee</option>
<<<<<<< HEAD
=======
                                <option value="Client" {{ old('role') == 'Client' ? 'selected' : '' }}>Client</option>
>>>>>>> origin/Admin/Townhall/Corporate
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Password</label>
                            <input
                                type="password"
                                name="password"
                                placeholder="Enter password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                placeholder="Confirm password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>

                        <div class="pt-4 border-t border-gray-200 flex gap-3">
                            <button
                                type="button"
                                @click="showCreateUser = false"
                                class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2.5 text-sm font-medium hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="flex-1 bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-700 transition"
                            >
                                Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- PAGE WRAPPER --}}
    <div class="bg-white border border-gray-200 rounded-xl min-h-[calc(100vh-7rem)] flex flex-col">
<<<<<<< HEAD

        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Users</h1>
                <p class="text-sm text-gray-500 mt-1">Manage login credentials and roles</p>
=======
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-[30px] font-semibold text-gray-800 leading-none">Users</h1>
                <p class="text-sm text-gray-500 mt-1">Manage login credentials, permissions, and roles</p>
>>>>>>> origin/Admin/Townhall/Corporate
            </div>

            <button
                @click="showCreateUser = true"
                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                + Create User
            </button>
        </div>

        <div class="px-5 py-5 flex-1 flex flex-col">
            <div class="border border-gray-200 rounded-xl overflow-hidden flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">ID</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Name</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Email</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Role</th>
<<<<<<< HEAD
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Created At</th>
=======
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Permissions</th>
                            <th class="px-4 py-3 border-r border-gray-200 font-semibold">Created At</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
>>>>>>> origin/Admin/Townhall/Corporate
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($users as $user)
<<<<<<< HEAD
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->id }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->name }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->email }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">
                                    @php
                                        $roleClasses = $user->role === 'Admin'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $roleClasses }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $user->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
=======
                            <tr class="border-t border-gray-200 hover:bg-gray-50 align-top">
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->id }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->name }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->email }}</td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    @if($authUser->canManageRoles() && !$user->isSuperAdmin())
                                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-2">
                                            @csrf

                                            <select
                                                name="role"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                            >
                                                <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="Employee" {{ $user->role === 'Employee' ? 'selected' : '' }}>Employee</option>
                                                <option value="Client" {{ $user->role === 'Client' ? 'selected' : '' }}>Client</option>
                                            </select>

                                            @if($authUser->isSuperAdmin())
                                                <div class="space-y-1 text-xs">
                                                    <label class="flex items-center gap-2">
                                                        <input
                                                            type="checkbox"
                                                            name="can_edit_user_roles"
                                                            value="1"
                                                            {{ $user->can_edit_user_roles ? 'checked' : '' }}
                                                        >
                                                        <span>Can edit user roles</span>
                                                    </label>

                                                    <label class="flex items-center gap-2">
                                                        <input
                                                            type="checkbox"
                                                            name="can_delete_users"
                                                            value="1"
                                                            {{ $user->can_delete_users ? 'checked' : '' }}
                                                        >
                                                        <span>Can delete users</span>
                                                    </label>
                                                </div>
                                            @endif

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition"
                                            >
                                                Update
                                            </button>
                                        </form>
                                    @else
                                        @php
                                            $roleClasses = $user->role === 'Admin'
                                                ? 'bg-blue-50 text-blue-700'
                                                : ($user->role === 'Superadmin'
                                                    ? 'bg-purple-50 text-purple-700'
                                                    : 'bg-gray-100 text-gray-700');
                                        @endphp

                                        <span class="px-2 py-1 text-xs rounded-full font-medium {{ $roleClasses }}">
                                            {{ $user->role }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200 text-xs">
                                    <div class="space-y-1">
                                        <div>
                                            <span class="font-medium text-gray-600">Edit Roles:</span>
                                            <span class="{{ $user->can_edit_user_roles ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ $user->can_edit_user_roles ? 'Yes' : 'No' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-medium text-gray-600">Delete Users:</span>
                                            <span class="{{ $user->can_delete_users ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ $user->can_delete_users ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 border-r border-gray-200">
                                    {{ $user->created_at?->format('Y-m-d') }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        @if($authUser->canDeleteUsers() && $authUser->id !== $user->id && !$user->isSuperAdmin())
                                            <form
                                                action="{{ route('admin.users.destroy', $user->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Delete this user?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                {{ $authUser->id === $user->id ? 'Current user' : 'No action' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
>>>>>>> origin/Admin/Townhall/Corporate
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500 px-1">
                <div>
                    Total Users <span class="text-gray-800 font-semibold">{{ $users->total() }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <span>{{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }}</span>
                </div>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
