<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        if (!in_array($authUser->role, ['SuperAdmin', 'Admin'])) {
            abort(403, 'Unauthorized');
        }

        if (!$authUser->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $users = User::where('role', '!=', 'SuperAdmin')
            ->latest()
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();

        if (!in_array($authUser->role, ['SuperAdmin', 'Admin'])) {
            abort(403, 'Unauthorized');
        }

        if (!$authUser->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:Admin,Employee'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }
}
