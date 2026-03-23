<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        RolePermission::updateOrCreate(
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

        RolePermission::updateOrCreate(
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

        RolePermission::updateOrCreate(
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
    }
}
