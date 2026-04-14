<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminUserPermissionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $users = User::where('role', '!=', 'SuperAdmin')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.user-permissions', compact('users'));
    }

    public function edit($id)
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);

        if ($user->role === 'SuperAdmin') {
            abort(403, 'SuperAdmin permissions cannot be modified.');
        }

        $permission = UserPermission::firstOrNew(
            ['user_id' => $user->id],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,
                'approve_townhall' => false,
                'create_townhall' => false,
                'create_corporate' => false,
                'approve_corporate' => false,
                'access_townhall' => false,
                'access_corporate' => false,
                'access_activities' => false,
                'access_contacts' => false,
                'access_company' => false,
            ]
        );

        return view('admin.edit-user-permissions', compact('user', 'permission'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);

        if ($user->role === 'SuperAdmin') {
            abort(403, 'SuperAdmin permissions cannot be modified.');
        }

        if ($user->id === Auth::id()) {
            if (!$request->has('manage_users') || !$request->has('access_admin_dashboard')) {
                return back()->with('error', 'You cannot remove your own critical admin permissions.');
            }
        }

        $permission = UserPermission::firstOrCreate(['user_id' => $user->id]);

        $updates = [];

        foreach (
            [
                'manage_users',
                'access_admin_dashboard',
                'approve_townhall',
                'create_townhall',
                'create_corporate',
                'approve_corporate',
                'access_townhall',
                'access_corporate',
                'access_activities',
                'access_contacts',
                'access_company',
            ] as $column
        ) {
            if (Schema::hasColumn('user_permissions', $column)) {
                $updates[$column] = $request->has($column);
            }
        }

        $permission->update($updates);

        return redirect()->route('admin.user-permissions')->with('success', 'User permissions updated successfully.');
    }
}
