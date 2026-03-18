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
            <h1 class="text-[30px] font-semibold text-gray-800 leading-none">User Permissions</h1>
            <p class="text-sm text-gray-500 mt-1">Assign specific permissions per employee</p>
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
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white text-gray-700">
                        @forelse($users as $user)
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->id }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->name }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->email }}</td>
                                <td class="px-4 py-3 border-r border-gray-200">{{ $user->role }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a
                                        href="{{ route('admin.user-permissions.edit', $user->id) }}"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
                                    >
                                        Manage Permissions
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
