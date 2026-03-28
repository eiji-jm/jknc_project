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

        RolePermission::firstOrCreate(
            ['role' => 'SuperAdmin'],
            [
                'manage_users' => true,
                'access_admin_dashboard' => true,
                'approve_townhall' => true,
                'create_townhall' => true,
                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => true,
                'access_contacts' => true,
                'access_company' => true,
            ]
        );

        RolePermission::firstOrCreate(
            ['role' => 'Admin'],
            [
                'manage_users' => true,
                'access_admin_dashboard' => true,
                'approve_townhall' => true,
                'create_townhall' => true,
                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => true,
                'access_contacts' => true,
                'access_company' => true,
            ]
        );

        RolePermission::firstOrCreate(
            ['role' => 'Employee'],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,
                'approve_townhall' => false,
                'create_townhall' => false,
                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => false,
                'access_contacts' => false,
                'access_company' => false,
            ]
        );

        $permissions = RolePermission::orderByRaw("
            CASE role
                WHEN 'SuperAdmin' THEN 1
                WHEN 'Admin' THEN 2
                WHEN 'Employee' THEN 3
                ELSE 4
            END
        ")->get();

        return view('admin.role-permissions', compact('permissions'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $permission = RolePermission::findOrFail($id);

        if ($permission->role === 'SuperAdmin') {
            abort(403, 'SuperAdmin role permissions cannot be modified.');
        }

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

        return redirect()
            ->route('admin.role-permissions')
            ->with('success', 'Role permissions updated successfully.');
    }
}
