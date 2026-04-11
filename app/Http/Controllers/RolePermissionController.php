<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RolePermissionController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasPermission('manage_users')) {
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

        RolePermission::firstOrCreate(
            ['role' => 'Client'],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,
                'approve_townhall' => false,
                'create_townhall' => false,
                'access_townhall' => true,
                'access_corporate' => false,
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
                WHEN 'Client' THEN 4
                ELSE 5
            END
        ")->get();

        return view('admin.role-permissions', compact('permissions'));
    }

    public function update(Request $request, $id)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasPermission('manage_users')) {
            abort(403, 'Unauthorized');
        }

        $permission = RolePermission::findOrFail($id);

        if ($permission->role === 'SuperAdmin') {
            abort(403, 'SuperAdmin role permissions cannot be modified.');
        }

        $updates = [];

        foreach ([
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
        ] as $column) {
            if (Schema::hasColumn('role_permissions', $column)) {
                $updates[$column] = $request->has($column);
            }
        }

        $permission->update($updates);

        return redirect()->route('admin.role-permissions')
            ->with('success', 'Role permissions updated successfully.');
    }
}
