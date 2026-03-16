<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserPermissionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $users = User::orderBy('name')->paginate(10);

        return view('admin.user-permissions', compact('users'));
    }

    public function edit($id)
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);

        $permission = UserPermission::firstOrCreate(
            ['user_id' => $user->id],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,
                'approve_townhall' => false,
                'create_townhall' => false,
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

        $permission = UserPermission::firstOrCreate(['user_id' => $user->id]);

        $permission->update([
            'manage_users' => $request->has('manage_users'),
            'access_admin_dashboard' => $request->has('access_admin_dashboard'),
            'approve_townhall' => $request->has('approve_townhall'),
            'create_townhall' => $request->has('create_townhall'),
            'access_townhall' => $request->has('access_townhall'),
            'access_corporate' => $request->has('access_corporate'),
            'access_activities' => $request->has('access_activities'),
            'access_contacts' => $request->has('access_contacts'),
            'access_company' => $request->has('access_company'),
        ]);

        return redirect()->route('admin.user-permissions')->with('success', 'User permissions updated successfully.');
    }
}
