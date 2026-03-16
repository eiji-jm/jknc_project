<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $permissions = RolePermission::orderBy('role')->get();

        return view('admin.role-permissions', compact('permissions'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $permission = RolePermission::findOrFail($id);

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

        return redirect()->route('admin.role-permissions')->with('success', 'Role permissions updated successfully.');
    }
}
