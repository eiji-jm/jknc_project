<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        if (!$authUser || (!$authUser->isSuperAdmin() && !$authUser->isAdmin())) {
            abort(403, 'Unauthorized.');
        }

        $users = User::where('role', '!=', 'Superadmin')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser || (!$authUser->isSuperAdmin() && !$authUser->isAdmin())) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['Admin', 'Employee'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'can_edit_user_roles' => false,
            'can_delete_users' => false,
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $user = User::findOrFail($id);

        if (!$authUser || !$authUser->canManageRoles()) {
            abort(403, 'You do not have permission to edit user roles.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Superadmin role cannot be modified here.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Employee'])],
            'can_edit_user_roles' => ['nullable', 'boolean'],
            'can_delete_users' => ['nullable', 'boolean'],
        ]);

        $user->role = $validated['role'];

        if ($authUser->isSuperAdmin()) {
            $user->can_edit_user_roles = $request->boolean('can_edit_user_roles');
            $user->can_delete_users = $request->boolean('can_delete_users');
        }

        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        $user = User::findOrFail($id);

        if (!$authUser || !$authUser->canDeleteUsers()) {
            abort(403, 'You do not have permission to delete users.');
        }

        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Superadmin cannot be deleted here.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}
