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

                'create_townhall' => true,
                'approve_townhall' => true,

                'create_corporate' => true,
                'approve_corporate' => true,

                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => true,
                'access_contacts' => true,
                'access_company' => true,

                'access_transmittal' => true,
                'access_deals' => true,
                'access_services' => true,
                'access_project' => true,
                'access_regular' => true,
                'access_product' => true,
                'access_policies' => true,

                'create_sales_marketing' => true,
                'approve_sales_marketing' => true,
                'access_sales_marketing' => true,
            ]
        );

        RolePermission::updateOrCreate(
            ['role' => 'Admin'],
            [
                'manage_users' => true,
                'access_admin_dashboard' => true,

                'create_townhall' => true,
                'approve_townhall' => true,

                'create_corporate' => true,
                'approve_corporate' => true,

                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => true,
                'access_contacts' => true,
                'access_company' => true,

                'access_transmittal' => true,
                'access_deals' => true,
                'access_services' => true,
                'access_project' => true,
                'access_regular' => true,
                'access_product' => true,
                'access_policies' => true,

                'create_sales_marketing' => true,
                'approve_sales_marketing' => true,
                'access_sales_marketing' => true,
            ]
        );

        RolePermission::updateOrCreate(
            ['role' => 'Employee'],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,

                'create_townhall' => false,
                'approve_townhall' => false,

                'create_corporate' => false,
                'approve_corporate' => false,

                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => false,
                'access_contacts' => false,
                'access_company' => false,

                'access_transmittal' => false,
                'access_deals' => false,
                'access_services' => false,
                'access_project' => false,
                'access_regular' => false,
                'access_product' => false,
                'access_policies' => false,

                'create_sales_marketing' => true,
                'approve_sales_marketing' => false,
                'access_sales_marketing' => true,
            ]
        );

        RolePermission::updateOrCreate(
            ['role' => 'Client'],
            [
                'manage_users' => false,
                'access_admin_dashboard' => false,

                'create_townhall' => false,
                'approve_townhall' => false,

                'create_corporate' => false,
                'approve_corporate' => false,

                'access_townhall' => true,
                'access_corporate' => true,
                'access_activities' => false,
                'access_contacts' => false,
                'access_company' => false,

                'access_transmittal' => false,
                'access_deals' => false,
                'access_services' => false,
                'access_project' => false,
                'access_regular' => false,
                'access_product' => false,
                'access_policies' => false,

                'create_sales_marketing' => false,
                'approve_sales_marketing' => false,
                'access_sales_marketing' => false,
            ]
        );
    }
}