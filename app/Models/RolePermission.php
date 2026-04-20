<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role',
        'permission',

        'manage_users',
        'access_admin_dashboard',

        'create_townhall',
        'approve_townhall',

        'create_corporate',
        'approve_corporate',

        'access_townhall',
        'access_corporate',
        'access_activities',
        'access_contacts',
        'access_company',

        'access_transmittal',
        'access_deals',
        'access_services',
        'access_project',
        'access_regular',
        'access_product',
        'access_policies',

        'create_sales_marketing',
        'approve_sales_marketing',
        'access_sales_marketing',
    ];
}